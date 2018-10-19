<?php
class Brainpower
{
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_order_menu_page') );
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));

    }

    public function register_order_menu_page(){

        if ( current_user_can( 'administrator' ) && !current_user_can( 'brainpower' ) ) {
            global $wp_roles;

            $role = 'brainpower';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'brainpower_join_directory';//权限名
            $wp_roles->add_cap('administrator', $role);

        }

        add_menu_page('脑力健将', '脑力健将', 'brainpower', 'brainpower',array($this,'index'),'dashicons-businessman',99);
        add_submenu_page('brainpower','加入名录','加入名录','brainpower_join_directory','brainpower-join_directory',array($this,'joinDirectory'));
//        add_submenu_page('order','申请退款','申请退款','administrator','order-refund',array($this,'refund'));
//        add_submenu_page('order','我的学员','我的学员','administrator','teacher-student',array($this,'student'));
//        add_submenu_page('order','我的课程','我的课程','administrator','teacher-course',array($this,'course'));
    }
    public function index(){
        die;
    }

    /**
     * 获取正确率
     */
    public function getCorrect($child_ID,$match_id,$user_ID,$title){
        global $wpdb;
        $av = $wpdb->get_row('SELECT id,questions_answer,my_answer,my_score FROM '.$wpdb->prefix.'match_questions WHERE project_id IN('.$child_ID.') AND match_id='.$match_id.' AND user_id='.$user_ID.' 
                            AND my_score=(SELECT MAX(my_score) FROM '.$wpdb->prefix.'match_questions WHERE project_id IN('.$child_ID.') AND match_id='.$match_id.' AND user_id='.$user_ID.')', ARRAY_A);
        $correct = 0;
        $av['my_answer'] = $my_answer = json_decode($av['my_answer'], true);
        $av['questions_answer'] = $questions_answer = json_decode($av['questions_answer'], true);
        if(!$my_answer) return 0;
        $abc = 0;
        $bcd = count($questions_answer);
        foreach ($my_answer as $k => $avv){
            if(is_array($avv) && isset($questions_answer[$k]['problem_answer'])){
                //速度类选项题
                foreach ($questions_answer[$k]['problem_answer'] as $pak => $pav){
                    if($pav == true && $avv[$pak] == true){
                        ++$abc;
                    }
                }
            }else{
                if($avv == 'unsolvable' || (preg_match('/[\+\*\/\-\×\÷]/', $avv) && preg_match('/逆向/', $title))){
                    //逆向速算,总分除十=正确题目数
                    $abc += $av['my_score'] / 10;
                }elseif($avv == $questions_answer[$k]){
                    ++$abc;
                }
            }
        }
        $correct += $abc/$bcd;
        return $correct;
    }

    /**
     * 加入名录
     */
    public function joinDirectory(){
        global $wpdb;
        $match_id = intval($_GET['match_id']);
        if($match_id < 1) exit('参数错误');
        $match = $wpdb->get_row('SELECT match_status,match_more,match_id FROM '.$wpdb->prefix.'match_meta WHERE match_id='.$match_id, ARRAY_A);
        //TODO 判断比赛是否结束

        if(!$match || $match['match_status'] != -3){
            exit('<h3>当前比赛未结束!</h3>');
        }



        //查询比赛小项目
        $projectArr = get_match_end_time($match_id);

        //获取比赛大类
        $match_student = new Match_student();
        $categoryArr = $match_student->getCategoryArr($projectArr);

        //查询前十
        foreach ($categoryArr as &$cate){
            $cate['data'] = $match_student->getCategoryRankingData($match,join(',', $cate['id']),0,'0,10');
        }


        $msg = '';
        if(is_post()){
            $range = isset($_POST['range']) ? intval($_POST['range']) : 0;
            if($range != 1 && $range !=2 ) exit('<h3>参数错误</h3>');
            //清除当前比赛已有脑力健将
//            $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}directories WHERE `match` LIKE '%({$match_id})%'", ARRAY_A);
//            foreach ($rows as $row2){
//                if($row2['level'] == false || $row2['level'] <= 1){
//                    $wpdb->delete($wpdb->prefix.'directories', ['id' => $row2['id']]);
//                }else{
//                    $level = $row2['level']-1;
//                    $matchArr = explode(',', $row2['match']);
//                    foreach ($matchArr as $k => $v){
//                        if($v == '('.$match_id.')'){
//                            unset($matchArr[$k]);
//                            break;
//                        }
//                    }
//                    $matchStr = join(',', $matchArr);
//                    $wpdb->update($wpdb->prefix.'directories', ['level' => $level]);
//                }
//            }

            foreach ($categoryArr as $cav1){
                foreach ($cav1['data'] as $caDataV){
                    $row = $wpdb->get_row('SELECT id,`level`,`match` FROM '.$wpdb->prefix.'directories WHERE `category_name`="'.$cav1['alias'].'" AND `user_id`='.$caDataV['user_id'].' AND `range`='.$range, ARRAY_A);

                    if($row){
                        if(!in_array('('.$match_id.')', explode(',', $row['match']))){
                            $level = $row['level']+1;
                            $match = $row['match'].',('.$match_id.')';
                            $sql = "UPDATE {$wpdb->prefix}directories SET `level`={$level},`match`='{$match}' WHERE id={$row['id']}";
                            $bool = $wpdb->query($sql);
                        }
                    }else{
                        $sql = "INSERT INTO {$wpdb->prefix}directories (`user_id`,`category_name`,`level`,`match`,`range`,`type_name`) 
                                VALUES ('{$caDataV['user_id']}','{$cav1['alias']}',1,'({$match_id})',{$range},'{$cav1['name']}类脑力健将')";
                        $bool = $wpdb->query($sql);
                    }
                    if($bool){
                        $msg = '生成成功';
                    }
                }
            }
            if($msg == '') $msg = '生成失败';
        }
//        leo_dump($categoryArr);

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?=get_post($match_id)->post_title?>-脑力健将名录</h1>

            <form method="post" action="" onsubmit="" id="_F">

                <p class="search-box">
                    <!--                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>-->
                    <!--                    <input type="search" id="user-search-input" name="s" value="">-->
                    <!--                    <input type="submit" id="search-submit" class="button" value="搜索用户">-->
                </p>

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="437465374e"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
                <div class="tablenav top">


                    <!--                        <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label><select name="action" id="bulk-action-selector-top">-->
                    <!--                            <option value="-1">批量操作</option>-->
                    <!--                            <option value="delete">删除</option>-->
                    <!--                        </select>-->
                    <style>
                        #dra_set_name,#dra_set_btn{
                            padding-bottom: 0.5em;
                            padding-top: 0.5em;
                        }
                        .red_border {
                            border: 1px solid red;
                        }
                    </style>
                    <div id="dra_set_name" class="">
                        <span style="font-weight: bold">名录类型:</span>
                        <input type="radio" name="range" value="1">中国
                        <input type="radio" name="range" value="2">国际

                    </div>

                    <div id="dra_set_btn">

                            <input type="button" id="generate_btn" class="button action" style="font-weight: bold" value="生成名录">

                        <div style="display: inline-block; padding-left: 3em; font-weight: bold;line-height: 28px;"><?=$msg?></div>
                    </div>
                    <script type="text/javascript">

                        jQuery(document).ready(function($) {
                            $('#generate_btn').on('click', function () {
                                var vals = $('input[name="range"]:checked').val();
                                if(vals == undefined || vals == false){
                                    $('#dra_set_name').addClass('red_border')
                                    return false;
                                }
                                if(confirm('是否确认生成名录?生成后无法重新生成')){
                                    $(this).off('click');
                                    document.getElementById('_F').submit();
                                }
                            });

                        })

                    </script>


                    <br class="clear">
                </div>
            </form>
            <hr class="wp-header-end">


            <?php if(!is_mobile()){
                echo '<br class="clear"> <br class="clear">';
            } ?>
<!---->
<!--            <h2 class="screen-reader-text">过滤用户列表</h2>-->
<!--            <ul class="subsubsub">-->
<!--                <li class="all"><a href="users.php" class="current" aria-current="page">全部<span class="count">（10）</span></a> |</li>-->
<!--                <li class="administrator"><a href="users.php?role=administrator">管理员<span class="count">（1）</span></a> |</li>-->
<!--                <li class="editor"><a href="users.php?role=editor">教练<span class="count">（6）</span></a> |</li>-->
<!--                <li class="subscriber"><a href="users.php?role=subscriber">学生<span class="count">（3）</span></a></li>-->
<!--            </ul>-->

                <h2 class="screen-reader-text">用户列表</h2><table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <th scope="col" id="real_name" class="manage-column column-real_name column-primary">
                            <span>姓名</span><span class="sorting-indicator"></span>
                        </th>
                        <th scope="col" id="cates" class="manage-column column-cates">类别</th>
                        <th scope="col" id="userID" class="manage-column column-userID">选手ID</th>
                        <th scope="col" id="card_num" class="manage-column column-card_num">证件号码</th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">电话号码</th>
                        <th scope="col" id="email" class="manage-column column-email">邮箱</th>
                    </tr>

                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">
                    <?php foreach ($categoryArr as $cav){ ?>


                        <?php foreach ($cav['data'] as $data){
                            ?>
                            <tr class="data-list">
                                <td class="real_name column-real_name has-row-actions column-primary line-c" style="vertical-align: center" data-colname="姓名">
                                    <strong><?=$data['real_name']?></strong>
                                    <br>
                                    <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                                </td>
                                <td class="cates column-cates line-c" data-colname="类别"><?=$cav['name']?>类</td>
                                <td class="userID column-userID line-c" data-colname="选手ID"><?=$data['userID']?></td>
                                <td class="card_num column-card_num line-c" data-colname="证件号码"><?=$data['card']?></td>
                                <td class="mobile column-mobile line-c" data-colname="电话号码"><?=$data['user_mobile']?></td>
                                <td class="email column-email line-c" data-colname="邮箱"><?=$data['user_email']?></td>

                            </tr>
                        <?php } ?>
                    <?php } ?>

                  </tbody>


                    <tfoot>
                    <tr>
                        <th scope="col" class="manage-column column-real_name column-primary">
                            <span>姓名</span><span class="sorting-indicator"></span>
                        </th>
                        <th scope="col" class="manage-column column-cates">类别</th>
                        <th scope="col" class="manage-column column-userID">选手ID</th>
                        <th scope="col" class="manage-column column-card_num">证件号码</th>
                        <th scope="col" class="manage-column column-mobile">电话号码</th>
                        <th scope="col" class="manage-column column-email">邮箱</th>
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">


                    <br class="clear">
                </div>


            <br class="clear">
        </div>
        <?php
    }


    /**
     * 引入当前页面css/js
     */
    public function register_scripts(){
        switch ($_GET['page']){
            case 'feedback':
                wp_register_script('feedback-js',match_js_url.'feedback.js');
                wp_enqueue_script( 'feedback-js' );
//                wp_register_style('list-css',match_css_url.'order-lists.css');
//                wp_enqueue_style( 'list-css' );
                break;
            case 'order-refund':
                wp_register_style('datum-css',match_css_url.'teacher-datum.css');
                wp_enqueue_style( 'datum-css' );
                wp_register_script('list-js',match_js_url.'order-lists.js');
                wp_enqueue_script( 'list-js' );
                break;
        }
        echo "<script>var ajax_url='".admin_url('admin-ajax.php' )."';</script>";
    }
}
new Brainpower();