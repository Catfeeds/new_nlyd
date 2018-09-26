<?php
class Match_student {
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_order_menu_page') );
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
    }

    public function register_order_menu_page(){

        if ( current_user_can( 'administrator' ) && !current_user_can( 'team' ) ) {
            global $wp_roles;

            $role = 'match_student';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'match_student_score';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'match_student_ranking';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'match_student_add_student';//权限名
            $wp_roles->add_cap('administrator', $role);

        }

        add_menu_page('报名学员', '报名学员', 'match_student', 'match_student',array($this,'studentLists'),'dashicons-businessman',99);
        add_submenu_page('match_student','个人成绩','个人成绩','match_student_score','match_student-score',array($this,'studentScore'));
        add_submenu_page('match_student','比赛排名','比赛排名','match_student_ranking','match_student-ranking',array($this,'matchRanking'));
        add_submenu_page('match_student','新增报名学员','新增报名学员','match_student_add_student','match_student-add_student',array($this,'addStudent'));
//        add_submenu_page('match_student','脑力健将','脑力健将','administrator','match_student-brainpower',array($this,'brainpower'));
    }


    public function studentLists(){
        $match = get_post($_GET['match_id']);
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS u.ID,u.user_login,u.display_name,u.user_mobile,u.user_email,o.created_time,o.address,o.telephone,u.user_mobile FROM '.$wpdb->prefix.'order AS o 
        LEFT JOIN '.$wpdb->users.' AS u ON u.ID=o.user_id 
        WHERE o.order_type=1 AND (o.pay_status=2 OR o.pay_status=3 OR o.pay_status=4) AND o.match_id='.$match->ID.' LIMIT '.$start.','.$pageSize, ARRAY_A);

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
            <h1 class="wp-heading-inline"><?=$match->post_title?>-比赛成绩</h1>

            <a href="admin.php?page=match_student-add_student&match_id=<?=$match->ID?>" class="page-title-action">添加报名学员</a>

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
                        <th scope="col" id="birthday" class="manage-column column-birthday">年龄</th>
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
//                            var_dump($usermeta);
                        ?>
                        <tr id="user-<?=$row['ID']?>">
                            <th scope="row" class="check-column">
                                <label class="screen-reader-text" for="">选择</label>
                                <input type="checkbox" name="users[]" id="" class="subscriber" value="">
                            </th>
                            <td class="username column-username has-row-actions column-primary" data-colname="用户名">
                                <img alt="" src="<?=$usermeta['user_head'][0]?>" class="avatar avatar-32 photo" height="32" width="32">
                                <strong><?=$row['user_login']?></strong>
                                <div class="row-actions">
                                    <!--                                    <span class="edit"><a href="https://ydbeta.gjnlyd.com/wp-admin/user-edit.php?user_id=311&amp;wp_http_referer=%2Fwp-admin%2Fusers.php">编辑</a> | </span>-->
                                    <!--                                    <span class="delete"><a class="submitdelete" href="users.php?action=delete&amp;user=311&amp;_wpnonce=0046431749">删除</a> | </span>-->
                                    <span class="view"><a href="<?=admin_url('admin.php?page=match_student-score&match_id=' . $match->ID . '&student_id='.$row['ID'])?>" aria-label="">答题记录</a></span>
                                </div>
                                <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button></td>
                            </td>
                            <td class="role column-ID" data-colname="ID"><?=$usermeta['user_ID'][0]?></td>

                            <td class="name column-name" data-colname="姓名"><span aria-hidden="true"><?=unserialize($usermeta['user_real_name'][0])['real_name']?></span><span class="screen-reader-text">未知</span></td>

                            <td class="name column-sex" data-colname="性别"><span aria-hidden="true"><?=$usermeta['user_gender'][0]?></span><span class="screen-reader-text">未知</span></td>
                            <td class="role column-birthday" data-colname="出生日期"><?=unserialize($usermeta['user_real_name'][0])['real_age']?></td>
                            <td class="role column-age_group" data-colname="年龄组别"><?=getAgeGroupNameByAge(unserialize($usermeta['user_real_name'][0])['real_age'])?></td>
                            <td class="role column-address" data-colname="所在地区"><?=unserialize($usermeta['user_address'][0])['province'].unserialize($usermeta['user_address'][0])['city']?></td>
                            <td class="email column-mobile" data-colname="手机"><a href="tel:dddddddddddddd@aa.aa"><?=$row['telephone'] ? $row['telephone'] : $row['user_mobile']?></a></td>
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
                        <th scope="col" class="manage-column column-birthday">年龄</th>
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
//        $user = get_user_by('ID',$user_id);
        $usermeta = get_user_meta($user_id, '', true);
        //获取当前比赛所有项目
        $projectArr = get_match_end_time($match_id);
//        var_dump($projectArr);

        //当前选择项目, 没有,默认第一个
        $proId = isset($_GET['proId']) ? $_GET['proId'] : $projectArr[0]['match_project_id'];

        //获取当期项目总轮数
        $match_moreAll = $wpdb->get_var('SELECT MAX(match_more) AS match_more FROM '.$wpdb->prefix.'match_questions WHERE match_id='.$match_id.' AND user_id='.$user_id.' AND project_id='.$proId.' GROUP BY project_id,user_id');

        //获取当前项目当前轮数, 没有默认第一轮
        $current_match_more = isset($_GET['more']) ? $_GET['more'] : 1;

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
            <h1 class="wp-heading-inline"><?=$match->post_title?>-<?=unserialize($usermeta['user_real_name'][0])['real_name']?>-答题记录</h1>

            <!--            <a href="http://127.0.0.1/nlyd/wp-admin/user-new.php" class="page-title-action">添加用户</a>-->

            <hr class="wp-header-end">

            <h2 class="screen-reader-text"></h2>
            <form method="get">

                <p class="search-box">

                </p>

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="5ce30f05fd"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">	<div class="tablenav top">

                    <div class="alignleft actions bulkactions">
                        <?php foreach ($projectArr as $pav){ ?>
                            <span><a href="<?=admin_url('admin.php?page=match_student-score&match_id='.$match_id.'&student_id='.$user_id.'&proId='.$pav['match_project_id'])?>"><?=$pav['post_title']?></a></span>
                        <?php } ?>

<!--                        <span><a href="">逆向速算</a></span>-->
<!--                        <span><a href="">逆向速算</a></span>-->
<!--                        <span><a href="">逆向速算</a></span>-->
                    </div>

                    <br class="clear">
                    <div class="alignleft actions">
                        <?php for($i = 1; $i <= $match_moreAll; ++$i){ ?>
                            <span><a href="<?=admin_url('admin.php?page=match_student-score&match_id='.$match_id.'&student_id='.$user_id.'&proId='.$proId.'&more='.$i)?>">第<?=$i?>轮</a></span>
                        <?php } ?>
<!--                        <span><a href="">第一轮</a></span>-->
<!--                        <span><a href="">第二轮</a></span>-->
<!--                        <span><a href="">第三轮</a></span>-->
<!--                        <span><a href="">第四轮</a></span>-->
                    </div>
                </div>
                <h2 class="screen-reader-text">答题记录</h2>
                <table class="wp-list-table widefat fixed striped users">
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
     * 获取正确率
     */
    public function getCorrect($user_id,$project_id,$match_id){
        global $wpdb;
        $av = $wpdb->get_row('SELECT questions_answer,my_answer,my_score FROM '.$wpdb->prefix.'match_questions 
                    WHERE my_score=(SELECT MAX(my_score) FROM '.$wpdb->prefix.'match_questions WHERE match_id='.$match_id.' AND user_id='.$user_id.' AND project_id='.$project_id.') 
                    AND user_id='.$user_id.' AND project_id='.$project_id.' AND match_id='.$match_id, ARRAY_A);


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
                $title = get_post($project_id)->post_title;
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
     * 比赛排名
     */
    public function matchRanking(){
        global $wpdb;

        //首先获取当前比赛
        $post = get_post(intval($_GET['match_id']));
        $match = $wpdb->get_row('SELECT match_status,match_more FROM '.$wpdb->prefix.'match_meta WHERE match_id='.$post->ID, ARRAY_A);

        //TODO 判断比赛是否结束
        if(!$match || $match['match_status'] != -3){
            echo '<br /><h2 style="color: #a80000">比赛未结束!</h2>';
            return;
        }

        //查询比赛小项目

//        $projectArr = $wpdb->get_results('SELECT ID,post_title FROM '.$wpdb->posts.' WHERE post_type="project" AND post_status="publish"', ARRAY_A);
        $projectArr = get_match_end_time($post->ID);

        $categoryArr = []; //分类选项卡数组
        foreach ($projectArr as $pak => $pav) {
            //获取类别和项目选项卡
            if (in_array($pav['project_alias'], ['pkjl', 'szzb'])) {
                $categoryArr['sjl']['post_title'] = '速记类';
                if(!isset($categoryArr['sjl']['ids'])) $categoryArr['sjl']['ids'] = '';
                $categoryArr['sjl']['ids'] .= $pav['match_project_id'].',';
            } elseif (in_array($pav['project_alias'], ['kysm', 'wzsd'])) {
                $categoryArr['sdl']['post_title'] = '速读类';
                if(!isset($categoryArr['sdl']['ids'])) $categoryArr['sdl']['ids'] = '';
                $categoryArr['sdl']['ids'] .= $pav['match_project_id'].',';
            } elseif (in_array($pav['project_alias'], ['zxss', 'nxss'])) {
                $categoryArr['ssl']['post_title'] = '速算类';
                if(!isset($categoryArr['ssl']['ids'])) $categoryArr['ssl']['ids'] = '';
                $categoryArr['ssl']['ids'] .= $pav['match_project_id'].',';;
            }
        }
        $where = '';
        $score = 'my_score';
        $selectArr = []; //循环查询分数数组
        $op1 = true;
        $op2 = false;
        $op3 = false;
        $selectType = 1;

        $downloadParam = '';
        $ageWhere = '';
//       选项卡条件
        if(isset($_GET['op1'])){
            switch ($_GET['op1']){
                case 1://总排名
                    $selectArr = $projectArr;
                    $downloadParam .= '&op1=1';
                    break;
                case 2: //分类排名
                    $downloadParam .= '&op1=2';
                    $op2 = true;
                    $selectType = 2;
                    $score = 'MAX(my_score) AS my_score';
                    if(isset($_GET['op2'])){
                        $selectArr = [$categoryArr[$_GET['op2']]];
                        $where = ' AND project_id IN('.substr($categoryArr[$_GET['op2']]['ids'],0,strlen($categoryArr['sdl']['ids'])-1).') GROUP BY project_id';
                    }else{
                        //默认第一个分类
                        $selectArr = [$categoryArr[key($categoryArr)]];
                        $where = ' AND project_id IN('.substr($categoryArr[key($categoryArr)]['ids'],0,strlen($categoryArr['sdl']['ids'])-1).') GROUP BY project_id';
                        $_GET['op2'] = key($categoryArr);
                    }
                    $downloadParam .= '&op2='.$_GET['op2'];
                    break;
                case 3: //单项排名
                    $downloadParam .= '&op1=3';
                    $op3 = true;
                    $selectType = 3;
                    $score = 'MAX(my_score) AS my_score';
                    if(isset($_GET['op4'])){
                        switch ($_GET['op4']){
                            case 4://儿童组
                                $ageWhere = ' AND  um.meta_value<13';
                                break;
                            case 3://少年组
                                $ageWhere = ' AND um.meta_value>12 AND um.meta_value<18';
                                break;
                            case 2://成年组
                                $ageWhere = ' AND um.meta_value>17 AND um.meta_value<60';
                                break;
                            case 1://老年组
                                $ageWhere = ' AND um.meta_value>59';
                                break;
                            default://全部
                        }
                    }else{
                        $_GET['op4'] = 0;
                    }

                    if(isset($_GET['op3'])){
                        $where = ' AND project_id ='.$_GET['op3'].' GROUP BY match_id';
                        foreach ($projectArr as $pak => $pav) {
                            if($pav['match_project_id'] == $_GET['op3']){
                                $post_title = $pav['post_title'];
                                break;
                            }
                        }

                    }else{
                        //默认地一个项目
                        $where = ' AND project_id ='.$projectArr[0]['match_project_id'].' GROUP BY match_id';
                        $post_title = $projectArr[0]['post_title'];
                        $_GET['op3'] = $projectArr[0]['match_project_id'];
                    }
                    $selectArr = [['post_title' => $post_title]];
                    $downloadParam .= '&op3='.$_GET['op3'].'&op4='.$_GET['op4'];;
                    break;
                default:
                    $selectArr = $projectArr;
            }
        }else{
            $selectArr = $projectArr;
        }
//        var_dump($projectWhere);
//        //是否选择组别分类
//        $group = 0;
//        if(is_post()){
//            $group = intval($_POST['age_group']);
//        }



        //查询每个参赛学员的总分排名
        //分页
        $_GET['cpage'] = isset($_GET['cpage']) ? $_GET['cpage'] : 1;
        $page = intval($_GET['cpage']) < 1 ? 1 : intval($_GET['cpage']);
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        //TODO 分页排序需要处理, 暂时关闭分页   LIMIT '.$start.','.$pageSize
        $totalRanking = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS o.telephone,u.user_email,o.user_id,mq.project_id,u.user_mobile,o.created_time,um.meta_value 
            FROM '.$wpdb->prefix.'order AS o 
            LEFT JOIN '.$wpdb->users.' AS u ON u.ID=o.user_id 
            LEFT JOIN '.$wpdb->usermeta.' AS um ON um.user_id=u.ID AND um.meta_key="user_age" 
            LEFT JOIN '.$wpdb->prefix.'match_questions AS mq ON mq.user_id=u.ID 
            WHERE o.match_id='.$post->ID.' AND o.pay_status IN(2,3,4) AND u.ID != ""'.$ageWhere.' GROUP BY o.user_id ORDER BY u.ID ASC', ARRAY_A);
//
//        var_dump($wpdb->last_query);
//        die;
        $count  = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page
        ));

//        echo '<pre />';
//        var_dump($selectArr);
        //剩余时间 | 正确率
        //获取比赛轮数

        //查询每个学员每个小项目每一轮的分数
        $whereT = 0;

        foreach ($totalRanking as &$trv){
            $trv['my_score'] = 0;
            $trv['surplus_time'] = 0;
            $trv['projectScore'] = [];
            foreach ($selectArr as  $pak => $pav) {
                $trv['projectScore'][$pak] = 0;
                if($where == '' || $whereT == 1) {
                    $where = ' AND project_id='.$pav['match_project_id'];
                    $whereT = 1;
                }
                if($selectType == 2){
                    $trv['projectScore'][$pak] = 0;
                }else{
                    $trv['projectScore'][$pak] = '';
                }
//                $trv['projectScore'][$pak] = '';
                $res = $wpdb->get_results('SELECT '.$score.',match_more,surplus_time,project_id FROM '.$wpdb->prefix.'match_questions AS mq 
                WHERE match_id='.$post->ID.' AND user_id='.$trv['user_id'].$where, ARRAY_A);


//            var_dump($where);
                $moreArr = [];
                $scoreArr = [];
                $surplus_timeArr = [];
                if($selectType == 1){
                    for($mi = 1; $mi <= $match['match_more']; ++$mi){
                        $moreArr[$mi] = '0/';
                    }
                }

                foreach ($res as $rv){
                    if($selectType == 2){
                        $moreArr[] = ($rv['my_score'] > 0 ? $rv['my_score'] : 0);
                    }else{
                        $moreArr[$rv['match_more']] = ($rv['my_score'] > 0 ? $rv['my_score'] : '0').'/';
                    }

                    $scoreArr[] = $rv['my_score'];
                    $surplus_timeArr[] = $rv['surplus_time'];
                }

                $trv['my_score'] += ($scoreArr != [] ? max($scoreArr) : 0);
                $trv['surplus_time'] += ($surplus_timeArr != [] ? max($surplus_timeArr) : 0);

                foreach ($moreArr as $mav){
                    if($selectType == 2){
                        $trv['projectScore'][$pak] += $mav;
                    }else{
                        $trv['projectScore'][$pak] .= $mav;
                    }
                }
                if($selectType == 2) $trv['projectScore'][$pak] .= '/';

                if(!$trv['projectScore'][$pak]) $trv['projectScore'][$pak] = '0/';
//                var_dump($trv['projectScore'][$pak]);
                $trv['projectScore'][$pak] = substr($trv['projectScore'][$pak], 0, strlen($trv['projectScore'][$pak])-1);
//                print_r($res);
            }
            $usermeta = get_user_meta($trv['user_id'], '', true);
            $user_real_name = unserialize($usermeta['user_real_name'][0]);
            $age = $user_real_name['real_age'];
            $user_real_name = $user_real_name['real_name'];
            $trv['age'] = $age;
            $trv['ageGroup'] = getAgeGroupNameByAge($age);
            $trv['userID'] = $usermeta['user_ID'][0];
            $trv['real_name'] = $user_real_name;
            $trv['sex'] = $usermeta['user_gender'][0];
            $trv['birthday'] = isset($usermeta['user_birthday']) ? $usermeta['user_birthday'][0] : '';
            $trv['address'] = unserialize($usermeta['user_address'][0])['province'].unserialize($usermeta['user_address'][0])['city'];
        }
        $rankingType = 1;
        for($i = 0; $i < count($totalRanking); ++$i){
            if(isset($totalRanking[$i+1])){
//                var_dump(2222222);
                for ($j = $i+1; $j < count($totalRanking); ++$j){
                    if($totalRanking[$i]['my_score'] == $totalRanking[$j]['my_score']){
//                        if($totalRanking[$i]['my_score'] < 1){
//                            $rankingAuto = false;
//                        }else
                        if($totalRanking[$j]['surplus_time'] > $totalRanking[$i]['surplus_time']){

                            $a = $totalRanking[$j];
                            $totalRanking[$j] = $totalRanking[$i];
                            $totalRanking[$i] = $a;
                        }elseif ($totalRanking[$j]['surplus_time'] == $totalRanking[$i]['surplus_time']){
                            //TODO 正确率, 获取分数最高一轮的正确率
//                    $iCorce = $this->getCorrect($totalRanking[$i]['user_id'],$totalRanking[$i]['project_id'],$post->ID);
//                    $jCorce = $this->getCorrect($totalRanking[$j]['user_id'],$totalRanking[$j]['project_id'],$post->ID);
//                    if($iCorce < $jCorce){
//                        $a = $totalRanking[$j];
//                        $totalRanking[$j] = $totalRanking[$i];
//                        $totalRanking[$i] = $a;
//                    }
                        }
                    }elseif ($totalRanking[$j]['my_score'] > $totalRanking[$i]['my_score']){
                        $a = $totalRanking[$j];
                        $totalRanking[$j] = $totalRanking[$i];
                        $totalRanking[$i] = $a;
                    }
                }
            }



        }
        $ranking = 1;
        foreach ($totalRanking as $k => $v){

            $totalRanking[$k]['ranking'] = $ranking;
            if(isset($totalRanking[$k+1]) && $totalRanking[$k+1]['my_score'] == $totalRanking[$k]['my_score'] && $totalRanking[$k+1]['surplus_time'] == $totalRanking[$k]['surplus_time']){

            }else{
                ++$ranking;
            }


        }
//        var_dump($categoryArr);
        ?>

        <div class="wrap">
            <h1 class="wp-heading-inline"><?=$post->post_title?>-比赛排名</h1>

            <!--            <a href="http://127.0.0.1/nlyd/wp-admin/user-new.php" class="page-title-action">添加用户</a>-->

            <hr class="wp-header-end">


            <form method="post" action="">
                <p class="search-box">
                    <!--                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>-->
                    <!--                    <input type="search" id="user-search-input" name="s" value="">-->
                    <!--                    <input type="submit" id="search-submit" class="button" value="搜索用户">-->
                </p>

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="8e15b92f19"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">	<div class="tablenav top">

                    <style type="text/css">
                        #oprion-box a{
                            color: #282828;
                            text-decoration: none;
                        }
                        #oprion-box > div{
                            padding-top: 0.5em;
                        }
                        #oprion-box > div > span{
                            display: inline-block;
                            width: 5em;
                            height: 1.5em;
                            border-radius: 0.2em;
                            text-align: center;
                            line-height: 1.5em;
                            cursor: pointer;
                        }
                        #option1 > span{
                            background-color: rgba(151, 151, 151, 0.48);
                            font-weight: bold;
                        }
                        #oprion-box .active{
                            background-color: #45B29D;
                        }
                        #oprion-box .active a{

                            color: #ffffff;
                        }
                    </style>
                    <div id="oprion-box">
                        <div id="option1">
                            <span class="<?php if(isset($_GET['op1']) && $_GET['op1'] == 1 || (!isset($_GET['op1']))){ ?>active<?php } ?>"><a href="<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=1')?>">总排名</a></span>
                            <span class="<?php if(isset($_GET['op1']) && $_GET['op1'] == 2){ ?>active<?php } ?>"><a href="<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=2')?>">分类排名</a></span>
                            <span class="<?php if(isset($_GET['op1']) && $_GET['op1'] == 3){ ?>active<?php } ?>"><a href="<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=3')?>">单项排名</a></span>
                        </div>
                        <?php if($op2) { ?>
                            <div id="option2">
                                <?php foreach ($categoryArr as $cgk => $cgv){ ?>
                                    <span class="<?php if(isset($_GET['op2']) && $_GET['op2'] == $cgk){ ?>active<?php } ?>"><a href="<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=2&op2='.$cgk)?>"><?=$cgv['post_title']?></a></span>
                                <?php } ?>

                            </div>
                        <?php } ?>
                        <?php if($op3){ ?>
                            <div id="option3">

                                <?php foreach ($projectArr as $pav2) { ?>
                                    <span class="<?php if(isset($_GET['op3']) && $_GET['op3'] == $pav2['match_project_id']){ ?>active<?php } ?>">
                                        <a href="<?= admin_url('admin.php?page=match_student-ranking&match_id=' . $post->ID . '&op1=3&op3=' . $pav2['match_project_id']) ?>"><?= $pav2['post_title'] ?></a>
                                    </span>
                                <?php } ?>
                                <select name="" id="option4" onchange="window.location.href='<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=3&op3='.$_GET['op3'].'&op4=')?>'+this.value">
                                    <option <?php if(isset($_GET['op4']) && $_GET['op4'] == '0'){ ?>selected="selected"<?php } ?> value="0">全部</option>
                                    <option <?php if(isset($_GET['op4']) && $_GET['op4'] == '4'){ ?>selected="selected"<?php } ?> value="4">儿童组</option>
                                    <option <?php if(isset($_GET['op4']) && $_GET['op4'] == '3'){ ?>selected="selected"<?php } ?> value="3">少年组</option>
                                    <option <?php if(isset($_GET['op4']) && $_GET['op4'] == '2'){ ?>selected="selected"<?php } ?> value="2">成年组</option>
                                    <option <?php if(isset($_GET['op4']) && $_GET['op4'] == '1'){ ?>selected="selected"<?php } ?> value="1">老年组</option>
                                </select>
                            </div>
                        <?php } ?>




                    </div>

                    <div class="alignleft actions">
                    </div>
                    <div class="alignleft actions bulkactions">

                        <a href="admin.php?page=download&action=match_ranking&match_id=<?=$post->ID.$downloadParam?>" class="button">导出排名</a>
                    </div>
                    <div class="tablenav-pages one-page">

                    </div>
                    <br class="clear">

                </div>    <br class="clear">
                <h2 class="screen-reader-text">用户列表</h2>
                <table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                            <input id="cb-select-all-1" type="checkbox">
                        </td>

                        <th scope="col" id="ID" class="manage-column column-ID">学员ID</th>
                        <th scope="col" id="real_name" class="manage-column column-real_name">姓名</th>
                        <th scope="col" id="sex" class="manage-column column-sex">性别</th>
                        <th scope="col" id="birthday" class="manage-column column-birthday">年龄</th>
                        <th scope="col" id="age" class="manage-column column-age">年龄组别</th>
                        <th scope="col" id="address" class="manage-column column-address">所在地区</th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                        <th scope="col" id="email" class="manage-column column-email">邮箱</th>
                        <th scope="col" id="created_time" class="manage-column column-created_time">报名时间</th>
                        <th scope="col" id="ranking" class="manage-column column-ranking">名次</th>
                        <?php if($selectType == 1){ ?>
                            <th scope="col" id="total_score" class="manage-column column-total_score">总得分</th>
                        <?php } ?>
                        <?php foreach ($selectArr as $titleV){ ?>

                            <th scope="col" id="" class="manage-column column-"><?=$titleV['post_title']?>得分</th>
                        <?php } ?>
                        <!--                        <th scope="col" id="" class="manage-column column-">扑克接力得分</th>-->
                        <!--                        <th scope="col" id="" class="manage-column column-">快眼扫描得分</th>-->
                        <!--                        <th scope="col" id="" class="manage-column column-">文章速读得分</th>-->
                        <!--                        <th scope="col" id="" class="manage-column column-">正向运算得分</th>-->
                        <!--                        <th scope="col" id="" class="manage-column column-">逆向运算得分</th>-->
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">


                    <?php foreach ($totalRanking as $raV){

                        ?>
                        <tr id="user-13">
                            <th scope="row" class="check-column">
                                <label class="screen-reader-text" for="user_13"></label>
                                <input type="checkbox" name="users[]" id="" class="subscriber" value="">
                            </th>

                            <td class="name column-ID" data-colname="学员ID"><span aria-hidden="true"><?=$raV['userID']?></span><span class="screen-reader-text">-</span></td>
                            <td class="name column-real_name" data-colname="姓名"><span aria-hidden="true"><?=$raV['real_name']?></span><span class="screen-reader-text"></span></td>
                            <td class="name column-sex" data-colname="性别"><span aria-hidden="true"><?=$raV['sex']?></span><span class="screen-reader-text">-</span></td>
                            <td class="name column-birthday" data-colname="出生日期"><span aria-hidden="true"><?=$raV['age']?></span><span class="screen-reader-text">-</span></td>
                            <td class="name column-age" data-colname="年龄组别"><span aria-hidden="true"><?=$raV['ageGroup']?></span><span class="screen-reader-text">-</span></td>
                            <td class="name column-address" data-colname="所在地区"><span aria-hidden="true"><?=$raV['address']?></span><span class="screen-reader-text">-</span></td>
                            <td class="name column-mobile" data-colname="手机"><span aria-hidden="true"><?=$raV['telephone'] ? $raV['telephone'] : $raV['user_mobile']?></span><span class="screen-reader-text">-</span></td>
                            <td class="name column-email" data-colname="邮箱"><span aria-hidden="true"><?=$raV['user_email']?></span><span class="screen-reader-text">-</span></td>
                            <td class="name column-created_time" data-colname="报名时间"><span aria-hidden="true"><?=$raV['created_time']?></span><span class="screen-reader-text">-</span></td>
                            <td class="name column-ranking" data-colname="名次"><span aria-hidden="true"><?=$raV['ranking']?></span><span class="screen-reader-text">-</span></td>
                            <?php if($selectType == 1){ ?>
                                <td class="name column-total_score" data-colname="总得分"><span aria-hidden="true"><?=$raV['my_score']?></span><span class="screen-reader-text">-</span></td>
                            <?php } ?>
                            <?php foreach ($raV['projectScore'] as $ravV){ ?>
                                <td class="name column-total_score" data-colname=""><span aria-hidden="true"><?=$ravV?></span><span class="screen-reader-text">-</span></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    </tbody>

                    <tfoot>


                    <tr>
                        <td class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-ID">学员ID</th>
                        <th scope="col" class="manage-column column-real_name">姓名</th>
                        <th scope="col" class="manage-column column-sex">性别</th>
                        <th scope="col" class="manage-column column-birthday">年龄</th>
                        <th scope="col" class="manage-column column-age">年龄组别</th>
                        <th scope="col" class="manage-column column-address">所在地区</th>
                        <th scope="col" class="manage-column column-mobile">手机</th>
                        <th scope="col" class="manage-column column-email">邮箱</th>
                        <th scope="col" class="manage-column column-created_time">报名时间</th>
                        <th scope="col" class="manage-column column-ranking">名次</th>
                        <?php if($selectType == 1){ ?>
                            <th scope="col" class="manage-column column-total_score">总得分</th>
                        <?php } ?>
                        <?php foreach ($selectArr as $titleV){ ?>
                            <th scope="col"class="manage-column column-"><?=$titleV['post_title']?>得分</th>
                        <?php } ?>
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">

                    <div class="alignleft actions bulkactions">

                    </div>
                    <div class="alignleft actions">
                    </div>
                    <div class="tablenav-pages one-page">

                    </div>
                    <br class="clear">
                </div>
            </form>

            <br class="clear">
        </div>
        <?php
    }

    /**
     * 新增报名学员
     */
    public function addStudent(){
        $match_id = intval($_GET['match_id']);
        $post = get_post($match_id);
        $page = ($page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1) < 1 ? 1 : $page;
        $pageSize = 1;
        $start = ($page - 1)*$pageSize;
        $searchCode = '';
        $rows = [];

        if(is_post() || isset($_GET['searchCode'])){
            $searchCode = isset($_GET['searchCode']) ? $_GET['searchCode'] : $_POST['searchCode'];
            global $wpdb;
            $rows = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS o.id AS oid,u.ID AS uid,u.user_login,u.user_mobile,u.user_email,um.meta_value AS user_ID,um2.meta_value AS user_level,um3.meta_value AS user_real_name FROM '.$wpdb->users.' AS u 
            LEFT JOIN '.$wpdb->usermeta.' AS um ON u.ID=um.user_id AND um.meta_key="user_ID" 
            LEFT JOIN '.$wpdb->usermeta.' AS um2 ON u.ID=um2.user_id AND um2.meta_key="'.$wpdb->prefix.'user_level" 
            LEFT JOIN '.$wpdb->usermeta.' AS um3 ON u.ID=um3.user_id AND um3.meta_key="user_real_name" 
            LEFT JOIN '.$wpdb->prefix.'order AS o ON u.ID=o.user_id AND o.match_id='.$match_id.' 
            WHERE um2.meta_value=0 AND o.id is NULL 
            AND (u.user_login LIKE "%'.$searchCode.'%" 
            OR u.user_mobile LIKE "%'.$searchCode.'%" 
            OR u.user_email LIKE "%'.$searchCode.'%" 
            OR um.meta_value LIKE "%'.$searchCode.'%") LIMIT '.$start.','.$pageSize, ARRAY_A);


            $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
            $pageAll = ceil($count['count']/$pageSize);
            $pageHtml = paginate_links( array(
                'base' => add_query_arg( 'cpage', '%#%' ),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $pageAll,
                'current' => $page,
                'add_fragment' => '&searchCode='.$searchCode,
            ));
        }
        ?>
        <div class="wrap">
            <h1 id="add-new-user"><?=$post->post_title?>-添加报名学员</h1>

            <div id="ajax-response"></div>

            <!--            <p>新建用户，并将用户加入此站点。</p>-->
            <form method="post" action="?page=match_student-add_student&match_id=<?=$match_id?>" name="createuser" id="createuser" class="validate" novalidate="novalidate">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="ce6b58ac15"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php">
                <input type="hidden" id="getWpnonce" name="_wpnonce" value="<?=wp_create_nonce('student_join_match_code_nonce');?>" />
                <table class="form-table">
                    <tbody>


                    <tr class="form-field">
                        <th scope="row"><label for="url">输入学员信息</label></th>
                        <td>
                            <input name="searchCode" type="text" id="url" class="code" value="<?=$searchCode?>" placeholder="用户名/手机/邮箱/学员ID">
                            <button type="submit" class="button">搜索学员</button>
                        </td>
                    </tr>


                    </tbody>
                </table>
            </form>




            <div class="tablenav top">

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <span class="pagination-links">
                        <?=$pageHtml?>
                    </span>
                </div>
                <br class="clear">
            </div>
            <table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                        <input id="cb-select-all-1" type="checkbox">
                    </td>
                    <th scope="col" id="pruser_loginoject" class="manage-column column-user_login">用户名</th>
                    <th scope="col" id="user_ID" class="manage-column column-user_ID">学员ID</th>
                    <th scope="col" id="real_name" class="manage-column column-real_name">姓名</th>
                    <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                    <th scope="col" id="email" class="manage-column column-email">电子邮件</th>
                    <th scope="col" id="addS" class="manage-column column-addS">添加到比赛学员</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">
                <?php foreach ($rows as $row) {
                    ?>

                    <tr>
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="user_13">选择</label>

                            <input type="checkbox" name="ids[]" class="subscriber" value="">
                        </th>
                        <td class="role column-user_login" data-colname="用户名"><?=$row['user_login']?></td>

                        <td class="role column-user_ID" data-colname="学员ID"><?=$row['user_ID']?></td>
                        <td class="role column-real_name" data-colname="姓名"><?=unserialize($row['user_real_name'])['real_name']?></td>
                        <td class="role column-mobile" data-colname="手机"><?=$row['user_mobile']?></td>
                        <td class="role column-email" data-colname="电子邮件"><?=$row['user_email']?></td>
                        <td class="role column-addS" data-colname="添加到比赛学员">
                            <a href="javascript:;" class="joinMatch" data-id="<?=$post->ID?>" data-uid="<?=$row['uid']?>">加入比赛</a>
                        </td>
                    </tr>
                <?php } ?>


                </tbody>

                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                        <input id="cb-select-all-2" type="checkbox">
                    </td>
                    <th scope="col" class="manage-column column-user_login">用户名</th>
                    <th scope="col" class="manage-column column-user_ID">学员ID</th>
                    <th scope="col" class="manage-column column-real_name">姓名</th>
                    <th scope="col" class="manage-column column-mobile">手机</th>
                    <th scope="col" class="manage-column column-email">电子邮件</th>
                    <th scope="col" class="manage-column column-addS">添加到比赛学员</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <span class="pagination-links">
                        <?=$pageHtml?>
                    </span>
                </div>
                <br class="clear">
            </div>
        </div>
        <?php
    }

    /**
     * 引入当前页面css/js
     */
    public function register_scripts(){
        switch ($_GET['page']){
            case 'match_student-add_student' or 'match_student-ranking':
                wp_register_script('list-js',match_js_url.'match_student-lists.js');
                wp_enqueue_script( 'list-js' );
//                wp_register_style('list-css',match_css_url.'order-lists.css');
//                wp_enqueue_style( 'list-css' );
                break;

        }
        echo "<script>var ajax_url='".admin_url('admin-ajax.php' )."';</script>";
    }
}

new Match_student();