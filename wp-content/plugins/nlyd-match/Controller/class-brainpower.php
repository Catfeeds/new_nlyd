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
        $is_view_btn = true;
        global $wpdb;
        $match_id = intval($_GET['match_id']);
        if($match_id < 1) exit('参数错误');
        $match = $wpdb->get_row('SELECT match_status,match_more,match_id FROM '.$wpdb->prefix.'match_meta WHERE match_id='.$match_id, ARRAY_A);
        //TODO 判断比赛是否结束

        if(!$match || $match['match_status'] != -3){
            exit('<h3>当前比赛未结束!</h3>');
        }
        //查询是否有名录
        $res = $wpdb->get_row('SELECT id FROM '.$wpdb->prefix.'directories WHERE `match` LIKE "%('.$match_id.')%"', ARRAY_A);
        if($res) {
            $is_view_btn = false;
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

//        leo_dump($categoryArr);
        $msg = '';

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?=get_post($match_id)->post_title?>-脑力健将名录</h1>


            <hr class="wp-header-end">
<!---->
<!--            <h2 class="screen-reader-text">过滤用户列表</h2>-->
<!--            <ul class="subsubsub">-->
<!--                <li class="all"><a href="users.php" class="current" aria-current="page">全部<span class="count">（10）</span></a> |</li>-->
<!--                <li class="administrator"><a href="users.php?role=administrator">管理员<span class="count">（1）</span></a> |</li>-->
<!--                <li class="editor"><a href="users.php?role=editor">教练<span class="count">（6）</span></a> |</li>-->
<!--                <li class="subscriber"><a href="users.php?role=subscriber">学生<span class="count">（3）</span></a></li>-->
<!--            </ul>-->
            <form method="post" action="" onsubmit="" id="_F">

                <p class="search-box">
<!--                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>-->
<!--                    <input type="search" id="user-search-input" name="s" value="">-->
<!--                    <input type="submit" id="search-submit" class="button" value="搜索用户">-->
                </p>

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="437465374e"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
                <div class="tablenav top">

                    <div class="alignleft actions bulkactions">
<!--                        <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label><select name="action" id="bulk-action-selector-top">-->
<!--                            <option value="-1">批量操作</option>-->
<!--                            <option value="delete">删除</option>-->
<!--                        </select>-->
                        <?php if($is_view_btn){?>
                            <input type="button" onclick="confirmSub();" class="button action" style="font-weight: bold" value="生成名录">
                        <?php } ?>
                        <div style="display: inline-block; padding-left: 3em; font-weight: bold;line-height: 28px;"><?=$msg?></div>
                        <script type="text/javascript">
                            function confirmSub() {
                                if(confirm('是否确认生成名录?')){
                                    document.getElementById('_F').submit();
                                }
                            }
                        </script>
                    </div>
                    <div class="alignleft actions">

                    </div>

                    <br class="clear">
                </div>
                <h2 class="screen-reader-text">用户列表</h2><table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="username" class="manage-column column-username column-primary">
                            姓名
                        </th>
                        <th scope="col" id="cate" class="manage-column column-cate">类别</th>
                        <th scope="col" id="name" class="manage-column column-real_name">选手ID</th>
<!--                        <th scope="col" id="role" class="manage-column column-score">分数</th>-->
                        <th scope="col" id="role" class="manage-column column-mobile">手机</th>
                        <th scope="col" id="email" class="manage-column column-email">邮箱</th>
                        </th>
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">
                    <?php foreach ($categoryArr as $cav){ ?>


                        <?php foreach ($cav['data'] as $cavD){
                            ?>

                            <tr id="user-12">
                                <th scope="row" class="check-column">
                                    <label class="screen-reader-text" for=""></label>
                                    <input type="checkbox" name="users[]" id="" class="editor" value="">
                                </th>
                                <td class="username column-username has-row-actions column-primary" data-colname="用户名">
                                    <img alt="" src="http://2.gravatar.com/avatar/b697aceb6d93a06b47ed9eabdd504985?s=32&amp;d=mm&amp;r=g" srcset="<?=get_user_meta($cavD['user_ID'], 'user_head')[0]?>" class="avatar avatar-32 photo" height="32" width="32">
                                    <strong>
                                        <?=$cavD['user_login']?>
                                    </strong>
                                </td>
                                <td class="role column-real_name" data-colname="姓名"><?=str_replace(', ','',$cavD['display_name'])?></td>
                                <td class="role column-score" data-colname="分数"><?=$cavD['my_score']?></td>
                                <td class="role column-mobile" data-colname="手机"><?=$cavD['user_mobile']?></td>
                                <td class="email column-email" data-colname="电子邮件">
                                    <?=$cavD['user_email']?>
                                </td>

                            </tr>
                        <?php } ?>
                    <?php } ?>

                  </tbody>


                    <tfoot>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label
                            <input type="checkbox">
                        </td>
                        <th scope="col"  class="manage-column column-username column-primary">
                            姓名
                        </th>
                        <th scope="col" class="manage-column column-cate">类别</th>
                        <th scope="col" class="manage-column column-real_name">选手ID</th>
                        <!--                        <th scope="col" id="role" class="manage-column column-score">分数</th>-->
                        <th scope="col" class="manage-column column-mobile">手机</th>
                        <th scope="col" class="manage-column column-email">邮箱</th>
                        </th>
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">


                    <br class="clear">
                </div>
            </form>

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