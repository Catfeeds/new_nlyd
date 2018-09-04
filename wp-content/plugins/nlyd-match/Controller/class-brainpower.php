<?php
class Brainpower
{
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_order_menu_page') );
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));

    }

    public function register_order_menu_page(){

        add_menu_page('脑力健将', '脑力健将', 'administrator', 'brainpower',array($this,'index'),'dashicons-businessman',99);
        add_submenu_page('brainpower','加入名录','加入名录','administrator','brainpower-join_directory',array($this,'joinDirectory'));
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
    public function getCorrect($answer,$title){
        $correct = 0;
        foreach ($answer as &$av){
            $av['my_answer'] = $my_answer = json_decode($av['my_answer'], true);
            $av['questions_answer'] = $questions_answer = json_decode($av['questions_answer'], true);
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
        }
        return $correct;
    }

    /**
     * 加入名录
     */
    public function joinDirectory(){
//        global $wpdb;
//        $match_id = intval($_GET['match_id']);
//        $project_id_arr = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'match_questions WHERE match_id='.$match_id, ARRAY_A);
//        for ($i = 2; $i < 15; ++$i){
//            foreach ($project_id_arr as $v){
//                unset($v['id']);
//                $v['user_id'] = $i;
//                $wpdb->insert($wpdb->prefix.'match_questions', $v);
//            }
//        }

        global $wpdb;
        $match_id = intval($_GET['match_id']);
        //查询大类以及附属小类
        $cateArr = $wpdb->get_results('SELECT p1.post_title AS parent_title,p1.ID AS parent_ID,GROUP_CONCAT(p2.ID) AS child_ID,GROUP_CONCAT(p2.post_title) AS child_title FROM '.$wpdb->posts.' AS p1 
        LEFT JOIN '.$wpdb->posts.' AS p2 ON p2.post_parent=p1.ID AND p2.post_status="publish" AND p2.post_type="project" 
        WHERE p1.post_status="publish" AND p1.post_type="match-category" GROUP BY parent_ID', ARRAY_A);

        echo '<pre />';
        //1.根据比赛id查询比赛每一项目得前十五名,当分数相同时重新排名,然后去除后5名
        //1.1 查询比赛类别, 用于分组
        foreach ($cateArr as $cak => $cav){
            //每个大类的学员总分数
            $res = $wpdb->get_results('SELECT u.ID AS user_ID,SUM(mq.my_score) AS my_score,u.user_login,u.display_name,u.user_mobile,SUM(mq.surplus_time) AS surplus_time,mc.coach_id FROM '.$wpdb->prefix.'match_questions AS mq 
            LEFT JOIN '.$wpdb->users.' AS u ON u.ID=mq.user_id 
            LEFT JOIN '.$wpdb->prefix.'my_coach AS mc ON mc.user_id=u.ID AND mc.major=1 
            WHERE mq.project_id IN('.$cav['child_ID'].') AND mq.match_id='.$match_id.' GROUP BY user_ID ORDER BY my_score DESC LIMIT 0,15', ARRAY_A);
            //排序
            for ($i = 0; $i < count($res); ++$i){
                for ($j = $i+1; $j <= count($res); ++$j){
                    if($res[$i]['my_score'] == $res[$j]['my_score']){
                        //分数相同,算时间
                        if($res[$i]['surplus_time'] < $res[$j]['surplus_time']){
                            $a = $res[$i];
                            $res[$i] = $res[$j];
                            $res[$j] = $a;
                        }elseif ($res[$i]['surplus_time'] == $res[$j]['surplus_time']){
                            //时间相同,算正确率
                            //查询答案
                            $answer = $wpdb->get_results('SELECT questions_answer,my_answer,my_score FROM '.$wpdb->prefix.'match_questions WHERE project_id IN('.$cav['child_ID'].') AND match_id='.$match_id.' AND user_id='.$res[$i]['user_ID'], ARRAY_A);
                            $answer2 = $wpdb->get_results('SELECT questions_answer,my_answer,my_score FROM '.$wpdb->prefix.'match_questions WHERE project_id IN('.$cav['child_ID'].') AND match_id='.$match_id.' AND user_id='.$res[$j]['user_ID'], ARRAY_A);
                            $correct1 = $this->getCorrect($answer,$cav['child_title']);
                            $correct2 = $this->getCorrect($answer2,$cav['child_title']);
                            $res[$i]['correct'] = $correct1;
                            $res[$j]['correct'] = $correct2;
//                            $res[$j]['answer'] = $answer2;
                            if($correct2 > $correct1){
                                $a = $res[$i];
                                $res[$i] = $res[$j];
                                $res[$j] = $a;
                            }
                            //正确率相同,看脸
                        }
                    }
                }

            }
            $cateArr[$cak]['data'] = array_slice($res, 0, 10);//截取前十
        }

//        echo '<pre />';
//        print_r($cateArr);
//        die;


        //3.插入数据sql生成
        $sql = 'INSERT INTO '.$wpdb->prefix.'brainpower (user_id,category_id,major_coach_id,`level`,`match`,`range`) VALUES ';
        $insertValue = '';
        foreach ($cateArr as $pgv){
            $match = serialize(['1' => ['match_id' => $match_id, 'match_level' => 1]]);
            foreach ($pgv['data'] as $sv){
                //2.查询这前十名是否已是当前类别当前赛事脑力健将, 如果是并且需要修改级别则修改级别
                $oldId = $wpdb->get_row('SELECT id,`level`,`match` FROM '.$wpdb->prefix.'brainpower WHERE category_id='.$pgv['parent_ID'].' AND user_id='.$sv['user_ID']);
                if($oldId){
                    $match = unserialize($oldId->match);
                    $match[$oldId->level+1] = ['match_id' => $match_id, 'match_level' => $oldId->level+1];
                    $wpdb->update($wpdb->prefix.'brainpower', ['level' => $oldId->level+1, 'match' => serialize($match), 'major_coach_id' => $sv['coach_id']], ['id' => $oldId->id]);
                }else{
                    $insertValue .= "('{$sv['user_ID']}','{$pgv['parent_ID']}','{$sv['coach_id']}','1','{$match}','1'),";
                }
            }
        }
        if($insertValue == ''){

        }else{
            $sql .= $insertValue;
            $sql = substr($sql,0,strlen($sql)-1);
            //4.开始插入数据
            $res = $wpdb->query($sql);
        }

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">用户</h1>

            <a href="http://127.0.0.1/nlyd/wp-admin/user-new.php" class="page-title-action">添加用户</a>

            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤用户列表</h2><ul class="subsubsub">
                <li class="all"><a href="users.php" class="current" aria-current="page">全部<span class="count">（10）</span></a> |</li>
                <li class="administrator"><a href="users.php?role=administrator">管理员<span class="count">（1）</span></a> |</li>
                <li class="editor"><a href="users.php?role=editor">教练<span class="count">（6）</span></a> |</li>
                <li class="subscriber"><a href="users.php?role=subscriber">学生<span class="count">（3）</span></a></li>
            </ul>
            <form method="get">

                <p class="search-box">
                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                    <input type="search" id="user-search-input" name="s" value="">
                    <input type="submit" id="search-submit" class="button" value="搜索用户"></p>

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="437465374e"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">	<div class="tablenav top">

                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label><select name="action" id="bulk-action-selector-top">
                            <option value="-1">批量操作</option>
                            <option value="delete">删除</option>
                        </select>
                        <input type="submit" id="doaction" class="button action" value="应用">
                    </div>
                    <div class="alignleft actions">
                        <label class="screen-reader-text" for="new_role">将角色变更为…</label>
                        <select name="new_role" id="new_role">
                            <option value="">将角色变更为…</option>

                            <option value="subscriber">学生</option>
                            <option value="contributor">投稿者</option>
                            <option value="author">作者</option>
                            <option value="editor">教练</option>
                            <option value="administrator">管理员</option>		</select>
                        <input type="submit" name="changeit" id="changeit" class="button" value="更改">		</div>
                    <div class="tablenav-pages one-page"><span class="displaying-num">11个项目</span>
                        <span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
<span class="paging-input">第<label for="current-page-selector" class="screen-reader-text">当前页</label><input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging"><span class="tablenav-paging-text">页，共<span class="total-pages">1</span>页</span></span>
<span class="tablenav-pages-navspan" aria-hidden="true">›</span>
<span class="tablenav-pages-navspan" aria-hidden="true">»</span></span></div>
                    <br class="clear">
                </div>
                <h2 class="screen-reader-text">用户列表</h2><table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="username" class="manage-column column-username column-primary sortable desc">
                            <a href="http://127.0.0.1/nlyd/wp-admin/users.php?orderby=login&amp;order=asc"><span>用户名</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="name" class="manage-column column-name">姓名</th>
                        <th scope="col" id="email" class="manage-column column-email sortable desc">
                            <a href="http://127.0.0.1/nlyd/wp-admin/users.php?orderby=email&amp;order=asc"><span>电子邮件</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="role" class="manage-column column-role">角色</th>
                        <th scope="col" id="posts" class="manage-column column-posts num">文章</th>
                        <th scope="col" id="mycred_default" class="manage-column column-mycred_default sortable desc">
                            <a href="http://127.0.0.1/nlyd/wp-admin/users.php?orderby=mycred_default&amp;order=asc"><span>积分</span><span class="sorting-indicator"></span></a>
                        </th>
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">

                    <tr id="user-12">
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="user_12">选择1111111111</label>
                            <input type="checkbox" name="users[]" id="user_12" class="editor" value="12">
                        </th>
                        <td class="username column-username has-row-actions column-primary" data-colname="用户名">
                            <img alt="" src="http://2.gravatar.com/avatar/b697aceb6d93a06b47ed9eabdd504985?s=32&amp;d=mm&amp;r=g" srcset="http://2.gravatar.com/avatar/b697aceb6d93a06b47ed9eabdd504985?s=64&amp;d=mm&amp;r=g 2x" class="avatar avatar-32 photo" height="32" width="32">
                            <strong>
                                <a href="http://127.0.0.1/nlyd/wp-admin/user-edit.php?user_id=12&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">1111111111</a>
                            </strong><br>
                            <div class="row-actions">
                                <span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/user-edit.php?user_id=12&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">编辑</a> | </span>
                                <span class="delete"><a class="submitdelete" href="users.php?action=delete&amp;user=12&amp;_wpnonce=437465374e">删除</a> | </span>
                                <span class="view"><a href="http://127.0.0.1/nlyd/author/1111111111/" aria-label="阅读66, dd的文章">查看</a></span>
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="name column-name" data-colname="姓名">dd 66</td>
                        <td class="email column-email" data-colname="电子邮件">
                            <a href="mailto:456789@qq.coms">456789@qq.coms</a>
                        </td>
                        <td class="role column-role" data-colname="角色">教练</td>
                        <td class="posts column-posts num" data-colname="文章">0</td>
                        <td class="mycred_default column-mycred_default" data-colname="积分">
                            <div id="mycred-user-12-balance-mycred_default"> <span>10</span> </div>
                            <div id="mycred-user-12-balance-mycred_default"><small style="display:block;"><strong>Total</strong>: <span>10</span></small></div>
                            <div class="row-actions">
                                <span class="history"><a href="http://127.0.0.1/nlyd/wp-admin/admin.php?page=mycred&amp;user=12">历史记录</a> | </span>
                                <span class="adjust">
                                    <a href="javascript:void(0)" class="mycred-open-points-editor" data-userid="12" data-current="10" data-total="10" data-type="mycred_default" data-username="66, dd" data-zero="0">调整</a>
                                </span>
                            </div>
                        </td>
                    </tr>
                  </tbody>


                    <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-username column-primary sortable desc">
                            <a href="http://127.0.0.1/nlyd/wp-admin/users.php?orderby=login&amp;order=asc"><span>用户名</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" class="manage-column column-name">姓名</th>
                        <th scope="col" class="manage-column column-email sortable desc">
                            <a href="http://127.0.0.1/nlyd/wp-admin/users.php?orderby=email&amp;order=asc"><span>电子邮件</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" class="manage-column column-role">角色</th>
                        <th scope="col" class="manage-column column-posts num">文章</th>
                        <th scope="col" class="manage-column column-mycred_default sortable desc">
                            <a href="http://127.0.0.1/nlyd/wp-admin/users.php?orderby=mycred_default&amp;order=asc"><span>积分</span><span class="sorting-indicator"></span></a>
                        </th>
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">

                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label><select name="action2" id="bulk-action-selector-bottom">
                            <option value="-1">批量操作</option>
                            <option value="delete">删除</option>
                        </select>
                        <input type="submit" id="doaction2" class="button action" value="应用">
                    </div>
                    <div class="alignleft actions">
                        <label class="screen-reader-text" for="new_role2">将角色变更为…</label>
                        <select name="new_role2" id="new_role2">
                            <option value="">将角色变更为…</option>

                            <option value="subscriber">学生</option>
                            <option value="contributor">投稿者</option>
                            <option value="author">作者</option>
                            <option value="editor">教练</option>
                            <option value="administrator">管理员</option>		</select>
                        <input type="submit" name="changeit2" id="changeit2" class="button" value="更改">		</div>
                    <div class="tablenav-pages one-page"><span class="displaying-num">11个项目</span>
                        <span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
<span class="screen-reader-text">当前页</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">第1页，共<span class="total-pages">1</span>页</span></span>
<span class="tablenav-pages-navspan" aria-hidden="true">›</span>
<span class="tablenav-pages-navspan" aria-hidden="true">»</span></span></div>
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