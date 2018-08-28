<?php
class Match_student {
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_order_menu_page') );
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));

    }

    public function register_order_menu_page(){
        add_menu_page('报名学员', '报名学员', 'administrator', 'match_student',array($this,'studentLists'),'dashicons-businessman',99);
        add_submenu_page('match_student','个人成绩','个人成绩','administrator','match_student-score',array($this,'studentScore'));
//        add_submenu_page('order','申请退款','申请退款','administrator','order-refund',array($this,'refund'));
//        add_submenu_page('order','发货','发货','administrator','order-send',array($this,'sendGoods'));
//        add_submenu_page('order','我的课程','我的课程','administrator','teacher-course',array($this,'course'));
    }


    public function studentLists(){
        $match = get_post($_GET['match_id']);
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS u.ID,u.user_login,u.display_name,u.user_mobile,u.user_email,o.created_time,o.address,o.telephone FROM '.$wpdb->prefix.'order AS o 
        LEFT JOIN '.$wpdb->users.' AS u ON u.ID=o.user_id 
        WHERE o.order_type=1 AND o.pay_status!=-2 AND o.match_id='.$match->ID.' LIMIT '.$start.','.$pageSize, ARRAY_A);

        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page
        ));

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?=$match->post_title?>-报名学员</h1>

            <a href="http://127.0.0.1/nlyd/wp-admin/user-new.php" class="page-title-action">添加报名学员</a>

            <hr class="wp-header-end">

<!--            <h2 class="screen-reader-text">过滤用户列表</h2>-->
<!--            <ul class="subsubsub">-->
<!--                <li class="all"><a href="users.php" class="current" aria-current="page">全部<span class="count">（28）</span></a> |</li>-->
<!--                <li class="administrator"><a href="users.php?role=administrator">管理员<span class="count">（1）</span></a> |</li>-->
<!--                <li class="editor"><a href="users.php?role=editor">教练<span class="count">（20）</span></a> |</li>-->
<!--                <li class="subscriber"><a href="users.php?role=subscriber">学生<span class="count">（7）</span></a></li>-->
<!--            </ul>-->
            <form method="get">

<!--                <p class="search-box">-->
<!--                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>-->
<!--                    <input type="search" id="user-search-input" name="s" value="">-->
<!--                    <input type="submit" id="search-submit" class="button" value="搜索用户">-->
<!--                </p>-->

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="9783a8b758"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">	<div class="tablenav top">
                    <a href="?page=download&action=matchStudent&match_id=<?=$match->ID?>"><div class="button" >导出成员</div></a>
<!--                    <div class="alignleft actions bulkactions">-->
<!--                        <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label><select name="action" id="bulk-action-selector-top">-->
<!--                            <option value="-1">批量操作</option>-->
<!--                            <option value="delete">删除</option>-->
<!--                        </select>-->
<!--                        <input type="submit" id="doaction" class="button action" value="应用">-->
<!--                    </div>-->
<!--                    <div class="alignleft actions">-->
<!--                        <label class="screen-reader-text" for="new_role">将角色变更为…</label>-->
<!--                        <select name="new_role" id="new_role">-->
<!--                            <option value="">将角色变更为…</option>-->
<!---->
<!--                            <option value="subscriber">学生</option>-->
<!--                            <option value="contributor">投稿者</option>-->
<!--                            <option value="author">作者</option>-->
<!--                            <option value="editor">教练</option>-->
<!--                            <option value="administrator">管理员</option>		</select>-->
<!--                        <input type="submit" name="changeit" id="changeit" class="button" value="更改">-->
<!--                    </div>-->
<!--                    <h2 class="screen-reader-text">用户列表导航</h2>-->
                    <div class="tablenav-pages"><span class="displaying-num"><?=$count['count']?>个项目</span>
                        <?=$pageHtml?>
                    </div>

                    <br class="clear">
                </div>
                <h2 class="screen-reader-text">用户列表</h2><table class="wp-list-table widefat fixed striped users">
                    <thead>

                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="username" class="manage-column column-username column-primary sortable desc">
                            <a href="javascript:;"><span>用户名</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="ID" class="manage-column column-ID">ID</th>
                        <th scope="col" id="name" class="manage-column column-name">姓名</th>
                        <th scope="col" id="sex" class="manage-column column-sex">性别</th>
                        <th scope="col" id="birthday" class="manage-column column-birthday">出⽣⽇期</th>
                        <th scope="col" id="age_group" class="manage-column column-age_group">年龄组别</th>
                        <th scope="col" id="address" class="manage-column column-address">所在地区</th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">电话</th>
                        <th scope="col" id="email" class="manage-column column-email">电子邮件</th>
                        <th scope="col" id="entry_time" class="manage-column column-entry_time">报名时间</th>
                        <th scope="col" id="score" class="manage-column column-score">个⼈⽐赛成绩</th>
<!--                        <th scope="col" id="record" class="manage-column column-record">答题记录</th>-->

                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">

                        <?php foreach ($rows as $row){
                            $usermeta = get_user_meta($row['ID'], '', true);
                            var_dump($usermeta);
                         ?>
                            <tr id="user-<?=$row['ID']?>">
                                <th scope="row" class="check-column">
                                    <label class="screen-reader-text" for="">选择</label>
                                    <input type="checkbox" name="users[]" id="" class="subscriber" value="">
                                </th>
                                <td class="username column-username has-row-actions column-primary" data-colname="用户名">
                                    <img alt="" src="<?=$usermeta['user_head'][0]?>" class="avatar avatar-32 photo" height="32" width="32">
                                    <strong><?=$row['user_login']?></strong>
<!--                                    <br>-->
<!--                                    <div class="row-actions">-->
<!--                                        <span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/user-edit.php?user_id=13&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">编辑</a> | </span>-->
<!--                                        <span class="delete"><a class="submitdelete" href="users.php?action=delete&amp;user=13&amp;_wpnonce=9783a8b758">删除</a> | </span>-->
<!--                                    </div>-->
<!--                                    <button type="button" class="toggle-row">-->
<!--                                        <span class="screen-reader-text">显示详情</span>-->
<!--                                    </button>-->
                                </td>
                                <td class="role column-ID" data-colname="ID"><?=$usermeta['user_ID'][0]?></td>

                                <td class="name column-name" data-colname="姓名"><span aria-hidden="true"><?=unserialize($usermeta['user_real_name'][0])['real_name']?></span><span class="screen-reader-text">未知</span></td>

                                <td class="name column-sex" data-colname="性别"><span aria-hidden="true"><?=$usermeta['user_gender'][0]?></span><span class="screen-reader-text">未知</span></td>
                                <td class="role column-birthday" data-colname="出生日期"><?=$usermeta['user_birthday'][0]?></td>
                                <td class="role column-age_group" data-colname="年龄组别"><?=getAgeGroupNameByAge(unserialize($usermeta['user_real_name'][0])['real_age'])?></td>
                                <td class="role column-address" data-colname="所在地区"><?=unserialize($usermeta['user_address'][0])['province'].unserialize($usermeta['user_address'][0])['city']?></td>
                                <td class="email column-mobile" data-colname="手机"><a href="tel:dddddddddddddd@aa.aa"><?=$row['telephone']?></a></td>
                                <td class="email column-email" data-colname="电子邮件"><a href="mailto:dddddddddddddd@aa.aa"><?=$row['user_email']?></a></td>
                                <td class="role column-entry_time" data-colname="报名时间"><?=$row['created_time']?></td>
                                <td class="role column-score" data-colname="个人比赛成绩"><a href="admin.php?page=match_student-score&match_id=<?=$_GET['match_id']?>&student_id=<?=$row['ID']?>">个人比赛成绩</a></td>
<!--                                <td class="role column-record" data-colname="答题记录">答题记录</td>-->
                            </tr>
                        <?php } ?>

                    </tbody>

                    <tfoot>
                    <tr>


                        <td class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-username column-primary sortable desc">
                            <a href="javascript:;"><span>用户名</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" class="manage-column column-ID">ID</th>
                        <th scope="col" class="manage-column column-name">姓名</th>
                        <th scope="col" class="manage-column column-sex">性别</th>
                        <th scope="col" class="manage-column column-birthday">出⽣⽇期</th>
                        <th scope="col" class="manage-column column-age_group">年龄组别</th>
                        <th scope="col" class="manage-column column-address">所在地区</th>
                        <th scope="col" class="manage-column column-mobile">电话</th>
                        <th scope="col" class="manage-column column-email">电子邮件</th>
                        <th scope="col" class="manage-column column-entry_time">报名时间</th>
                        <th scope="col" class="manage-column column-score">个人比赛成绩</th>
<!--                        <th scope="col" class="manage-column column-record">答题记录</th>-->
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">

<!--                    <div class="alignleft actions bulkactions">-->
<!--                        <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label><select name="action2" id="bulk-action-selector-bottom">-->
<!--                            <option value="-1">批量操作</option>-->
<!--                            <option value="delete">删除</option>-->
<!--                        </select>-->
<!--                        <input type="submit" id="doaction2" class="button action" value="应用">-->
<!--                    </div>-->
<!--                    <div class="alignleft actions">-->
<!--                        <label class="screen-reader-text" for="new_role2">将角色变更为…</label>-->
<!--                        <select name="new_role2" id="new_role2">-->
<!--                            <option value="">将角色变更为…</option>-->
<!---->
<!--                            <option value="subscriber">学生</option>-->
<!--                            <option value="contributor">投稿者</option>-->
<!--                            <option value="author">作者</option>-->
<!--                            <option value="editor">教练</option>-->
<!--                            <option value="administrator">管理员</option>		</select>-->
<!--                        <input type="submit" name="changeit2" id="changeit2" class="button" value="更改">		</div>-->
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?=$count['count']?>个项目</span>
                        <?=$pageHtml?>
                    </div>
                    <br class="clear">
                </div>
            </form>

            <br class="clear">
        </div>
        <?php
    }

    /**
     * 个人比赛成绩
     */
    public function studentScore(){

        $match_id = intval($_GET['match_id']);
        $user_id = intval($_GET['student_id']);
        global $wpdb;
        $match = get_post($match_id);
        $user = get_user_by('ID',$user_id);
        $usermeta = get_user_meta($user_id, '', true);
        //查询成绩
        $rows = $wpdb->get_results('SELECT mq.match_more,mq.match_questions,mq.questions_answer,mq.my_answer,mq.surplus_time,mq.my_score,p.post_title AS project_title,mq.created_time,
              CASE mq.answer_status 
              WHEN -1 THEN "记忆完成" 
              WHEN 1 THEN "提交" 
              END AS answer_status 
              FROM '.$wpdb->prefix.'match_questions AS mq 
              LEFT JOIN '.$wpdb->posts.' AS p ON p.ID=mq.project_id 
              WHERE mq.user_id='.$user_id.' AND mq.match_id='.$match_id.' ORDER BY mq.match_more ASC', ARRAY_A);

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?=$match->post_title?>-<?=unserialize($usermeta['user_real_name'][0])['real_name']?>-比赛成绩</h1>

<!--            <a href="http://127.0.0.1/nlyd/wp-admin/user-new.php" class="page-title-action">添加用户</a>-->

            <hr class="wp-header-end">

            <h2 class="screen-reader-text"></h2>
            <form method="get">

                <p class="search-box">

                </p>

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="5ce30f05fd"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">	<div class="tablenav top">

                    <div class="alignleft actions bulkactions">

                    </div>
                    <div class="alignleft actions">

                    </div>
                    <br class="clear">
                </div>
                <h2 class="screen-reader-text">用户列表</h2><table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="project" class="manage-column column-project">比赛项目</th>
                        <th scope="col" id="match_more" class="manage-column column-match_more">比赛轮数</th>
                        <th scope="col" id="match_questions" class="manage-column column-match_questions">比赛考题</th>
                        <th scope="col" id="questions_answer" class="manage-column column-questions_answer">考题答案</th>
                        <th scope="col" id="my_answer" class="manage-column column-my_answer">我的答案</th>
                        <th scope="col" id="surplus_time" class="manage-column column-surplus_time">剩余时间</th>
                        <th scope="col" id="my_score" class="manage-column column-my_score">我的成绩</th>
                        <th scope="col" id="answer_status" class="manage-column column-answer_status">答题状态</th>
                        <th scope="col" id="created_time" class="manage-column column-created_time">创建时间</th>
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">
                        <?php
                        foreach ($rows as $row) {
//                            var_dump(json_decode($row['match_questions']));
//                            var_dump(json_decode($row['questions_answer']));
//                            var_dump(json_decode($row['my_answer']));
//
//                            die;

                            ?>
                            <tr style="background-color: #b3b3b3;color: #ffffff">
                                <th scope="row" class="check-column">
                                                                      <label class="screen-reader-text" for="user_13">选择</label>

                                    <input type="checkbox" name="ids[]" class="subscriber" value="">
                                </th>
                                <td class="role column-project" data-colname="比赛项目"><?=$row['project_title']?></td>

                                <td class="role column-match_more" data-colname="比赛轮数"><?=$row['match_more']?></td>
                                <td class="role column-match_questions" data-colname="比赛考题">比赛考题</td>
                                <td class="role column-questions_answer" data-colname="考题答案">考题答案</td>
                                <td class="role column-my_answer" data-colname="我的答案">我的答案</td>
                                <td class="role column-surplus_time" data-colname="剩余时间"><?=$row['surplus_time']?></td>
                                <td class="role column-my_score" data-colname="我的成绩"><?=$row['my_score']?></td>
                                <td class="role column-answer_status" data-colname="答题状态"><?=$row['answer_status']?></td>
                                <td class="role column-created_time" data-colname="创建时间"><?=$row['created_time']?></td>
                            </tr>
                            <?php
                            $match_questions_str = '';
                            $questions_answer = json_decode($row['questions_answer'], true);

                            $my_answer = json_decode($row['my_answer'], true);
                            $match_questions = json_decode($row['match_questions'], true);

                            //问题
                            if(empty($match_questions)) continue;
                            foreach ($match_questions as $k => $match_question){
                                if(!is_array($match_question)){
                                    $match_questions_str = $match_question;
                                }else{

                                    foreach ($match_question as $mqv){
                                        $match_questions_str .= $mqv.',';
                                    }
                                }
                                $match_questions_str = substr($match_questions_str, 0, strlen($match_questions_str) -1);

                                //正确答案
                                if(isset($questions_answer[$k]['problem_select']) && is_array($questions_answer[$k]['problem_select'])){
                                    $q_answer = '';
                                    foreach ($questions_answer[$k]['problem_select'] as $qak => $qav){
                                        if($questions_answer[$k]['problem_answer'][$qak] == 1){
                                            //正确答案
                                            $q_answer .= $qav.',';
                                        }
                                    }
                                    $q_answer = substr($q_answer, 0, strlen($q_answer)-1);

                                }else{
                                    $q_answer = $questions_answer[$k];
                                }

                                //我的答案
                                if(is_array($my_answer[$k])){

                                    $m_answer = '';
                                    if(isset($questions_answer[$k]['problem_select'])){
                                        //文章速度
                                        //查询答案
                                        foreach ($my_answer[$k] as $mav){
                                            $m_answer .= $questions_answer[$k]['problem_select'][$mav].',';
                                        }
                                    }else{
                                        foreach ($my_answer[$k] as $mav){
                                            $m_answer .= $mav.',';
                                        }
                                    }



                                    $m_answer = substr($m_answer, 0, strlen($m_answer)-1);
                                }else{
                                    $m_answer = $my_answer[$k];
                                }

                        ?>
                                <tr>

                                    <th scope="row" class="check-column">
                                    </th>
                                        <td class="role column-project" data-colname="比赛项目"></td>

                                        <td class="role column-match_more" data-colname="比赛轮数"></td>
                                        <td class="role column-match_questions" data-colname="比赛考题"><?=$match_questions_str?></td>
                                        <td class="role column-questions_answer" data-colname="考题答案"><?=$q_answer?></td>
                                        <td class="role column-my_answer" data-colname="我的答案">
                                            <span <?=$m_answer!=$q_answer ? 'style="color: #a20000"' : ''?>><?=$m_answer?></span>
                                        </td>
                                        <td class="role column-surplus_time" data-colname="剩余时间"></td>
                                        <td class="role column-my_score" data-colname="我的成绩"></td>
                                        <td class="role column-answer_status" data-colname="答题状态"></td>
                                        <td class="role column-answer_status" data-colname="创建时间"></td>



                                </tr>
                        <?php
                                $match_questions_str = '';

                            }
                        }

                        ?>

                    </tbody>

                    <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-project">比赛项目</th>
                        <th scope="col" class="manage-column column-match_more">比赛轮数</th>
                        <th scope="col" class="manage-column column-match_questions">比赛考题</th>
                        <th scope="col" class="manage-column column-questions_answer">考题答案</th>
                        <th scope="col" class="manage-column column-my_answer">我的答案</th>
                        <th scope="col" class="manage-column column-surplus_time">剩余时间</th>
                        <th scope="col" class="manage-column column-my_score">我的成绩</th>
                        <th scope="col" class="manage-column column-answer_status">答题状态</th>
                        <th scope="col" class="manage-column column-created_time">创建时间</th>
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">

                    <div class="alignleft actions bulkactions">
                    </div>
                    <div class="alignleft actions">


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
            case 'order':
                wp_register_script('list-js',match_js_url.'order-lists.js');
                wp_enqueue_script( 'list-js' );
                wp_register_style('list-css',match_css_url.'order-lists.css');
                wp_enqueue_style( 'list-css' );
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

new Match_student();