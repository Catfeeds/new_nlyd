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
        $searchStr = isset($_GET['search']) ? trim($_GET['search']) : '';
        global $wpdb;
        $searchWhere = '';
        $joinSql = '';
        if($searchStr != ''){
            $searchWhere = ' AND (u.user_mobile LIKE "%'.$searchStr.'%" OR u.user_email LIKE "%'.$searchStr.'%" OR um.meta_value LIKE "%'.$searchStr.'%")';
            $joinSql = ' LEFT JOIN '.$wpdb->usermeta.' AS um ON um.user_id=u.ID AND um.meta_key="user_ID"';
        }

        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS u.ID,u.user_login,u.display_name,u.user_mobile,u.user_email,o.created_time,o.address,o.telephone,u.user_mobile FROM '.$wpdb->prefix.'order AS o 
        LEFT JOIN '.$wpdb->users.' AS u ON u.ID=o.user_id 
        '.$joinSql.'
        WHERE o.order_type=1 AND o.pay_status IN(2,3,4) '.$searchWhere.' AND o.match_id='.$match->ID.' AND u.ID!="" ORDER BY o.created_time DESC LIMIT '.$start.','.$pageSize, ARRAY_A);


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
            <h1 class="wp-heading-inline"><?=$match->post_title?>-报名选手</h1>

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

                <p class="search-box">
                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                    <input type="search" id="search_val" name="search_val" placeholder="手机/邮箱/ID" value="<?=$searchStr?>">
                    <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('edit.php?page=match_student&match_id='.$match->ID.'&search=')?>'+document.getElementById('search_val').value" value="搜索用户">
                </p>

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
//
//        die;
        //当前选择项目, 没有,默认第一个
        if(isset($_GET['proId'])){
            foreach ($projectArr as $pav){
                if($pav['match_project_id'] == $_GET['proId']){
                    $currentProject = $pav;
                    break;
                }
            }
        }else{
            $currentProject = $projectArr[0];
        }
        if(!isset($currentProject)){
            echo '比赛项目参数错误';
            exit;
        }

        $proId = $currentProject['match_project_id'];

        //获取当期项目总轮数
        $match_moreAll = $wpdb->get_var('SELECT MAX(match_more) AS match_more FROM '.$wpdb->prefix.'match_questions WHERE match_id='.$match_id.' AND user_id='.$user_id.' AND project_id='.$proId.' GROUP BY project_id,user_id');

        //获取当前项目当前轮数, 没有默认第一轮
        $current_match_more = isset($_GET['more']) ? $_GET['more'] : 1;

        //查询成绩
        $row = $wpdb->get_row('SELECT match_questions,questions_answer,my_answer,surplus_time,my_score,created_time,
              CASE answer_status 
              WHEN -1 THEN "记忆完成" 
              WHEN 1 THEN "提交" 
              END AS answer_status 
              FROM '.$wpdb->prefix.'match_questions  
              WHERE match_id='.$match_id.' AND project_id='.$proId.' AND user_id='.$user_id.' AND match_more='.$current_match_more, ARRAY_A);
        $is_view = true;
        if(!$row){
            echo '本轮比赛无记录';
            $is_view = false;
        }elseif ($row['answer_status'] != 1){
            echo '本轮成绩未提交';
            $is_view = false;
        }

        //生成答题记录数组
        $match_questions = json_decode($row['match_questions'],true);
        $questions_answer = json_decode($row['questions_answer'],true);
        $my_answer = !empty($row['my_answer']) ? json_decode($row['my_answer'],true) : array();

        if(in_array($currentProject['project_alias'],array('wzsd'))){
            if(empty($questions_answer)){
                $len = 0;
            }else{

                $len = count($questions_answer);
            }
            $success_len = 0;
            if(!empty($questions_answer)){
                foreach ($questions_answer as $k=>$val){
                    $arr = array();
                    $answerArr = array();
                    foreach ($val['problem_answer'] as $key => $v){
                        if($v == 1){
                            $arr[] = $key;
                            $answerArr[] = $key;
                        }
                    }
                    $questions_answer[$k]['problem_answer'] = $answerArr;
                    if(isset($my_answer[$k])){
                        if(arr2str($arr) == arr2str($my_answer[$k])) ++$success_len;
                    }
                }
            }

        }
        elseif ($currentProject['project_alias'] == 'nxss'){

            $answer = $questions_answer;
            $answer_array = $answer['result'];
            $questions_answer = $answer['examples'];
            /*print_r($answer_array);
            print_r($questions_answer);*/
            //die;

            $count_value = array_count_values($answer_array);
            $success_len = !empty($count_value['true']) ? $count_value['true'] : 0;

            $len = count($questions_answer);

            /*if(!empty($match_questions)){
                $twentyfour = new TwentyFour();
                foreach ($match_questions as $val){
                    $results = $twentyfour->calculate($val);
                    //print_r($results);
                    $arr[] = !empty($results) ? $results[0] : 'unsolvable';
                }
                $questions_answer = $arr;
            }*/
        }
        else{

            if(!empty($questions_answer)){
                $len = count($questions_answer);
                $error_arr = array_diff_assoc($questions_answer,$my_answer);
                $error_len = count($error_arr);
                $success_len = $len - $error_len;
            }else{
                $my_answer = array();
                $error_arr = array();
                $success_len = 0;
                $len = 0;
            }
        }

        //获取本轮排名
        $sql = "select user_id from {$wpdb->prefix}match_questions where match_id = {$match_id} and project_id = {$proId} and match_more = {$current_match_more} group by my_score desc,surplus_time desc";
        $rows = $wpdb->get_results($sql,ARRAY_A);


        $ranking = array_search($user_id,array_column($rows,'user_id'))+1;
        $data = array(
            'project_alias'=>$currentProject['project_alias'],
            'str_len'=>$len,
            'match_more_cn'=>chinanum($_GET['match_more']),
            'success_length'=>$success_len,
            'use_time'=>$currentProject['child_count_down']-$row['surplus_time'],
            'surplus_time'=>$row['surplus_time'],
            'accuracy'=>$success_len > 0 ? round($success_len/$len,2)*100 : 0,
            'ranking'=>$ranking,
            'match_questions'=>$match_questions,
            'questions_answer'=>$questions_answer,
            'my_answer'=>$my_answer,
            'answer_array'=>$answer_array,
            'my_score'=>$row['my_score'],
            'project_title'=>$currentProject['post_title'],
            'match_title'=>$match->post_title,
            'error_arr'=>!empty($error_arr) ? array_keys($error_arr) : array(),
//            'record_url'=>home_url('matchs/record/type/project/match_id/'.$this->match_id.'/project_id/'.$this->project_id.'/match_more/'.$this->current_more),
        );



//        $rows = $wpdb->get_results('SELECT mq.match_more,mq.match_questions,mq.questions_answer,mq.my_answer,mq.surplus_time,mq.my_score,p.post_title AS project_title,mq.created_time,
//              CASE mq.answer_status
//              WHEN -1 THEN "记忆完成"
//              WHEN 1 THEN "提交"
//              END AS answer_status
//              FROM '.$wpdb->prefix.'match_questions AS mq
//              LEFT JOIN '.$wpdb->posts.' AS p ON p.ID=mq.project_id
//              WHERE mq.user_id='.$user_id.' AND mq.match_id='.$match_id.' ORDER BY mq.match_more ASC', ARRAY_A);


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

                    <style type="text/css">
                        #op1 a, #op2 a{
                            color: #282828;
                            text-decoration: none;

                        }
                        #op1, #op2{
                            padding-top: 0.5em;
                        }
                        #op1 > span,#op2 > span{
                            display: inline-block;
                            width: 5em;
                            height: 1.5em;
                            border-radius: 0.2em;
                            text-align: center;
                            line-height: 1.5em;
                            cursor: pointer;
                        }
                        #op1 > span{
                            background-color: rgba(151, 151, 151, 0.48);
                            font-weight: bold;
                        }
                        #op1 .active,#op2 .active{
                            background-color: #45B29D;
                        }
                        #op1 .active a,#op2 .active a{

                            color: #ffffff;
                        }
                    </style>
                    <div class="alignleft actions bulkactions" id="op1">
                        <?php foreach ($projectArr as $pav){ ?>
                            <span class="<?=$proId==$pav['match_project_id'] ? 'active' : ''; ?>"><a href="<?=admin_url('admin.php?page=match_student-score&match_id='.$match_id.'&student_id='.$user_id.'&proId='.$pav['match_project_id'])?>"><?=$pav['post_title']?></a></span>
                        <?php } ?>

                        <!--                        <span><a href="">逆向速算</a></span>-->
                        <!--                        <span><a href="">逆向速算</a></span>-->
                        <!--                        <span><a href="">逆向速算</a></span>-->
                    </div>

                    <br class="clear">
                    <div class="alignleft actions" id="op2">
                        <?php for($i = 1; $i <= $match_moreAll; ++$i){ ?>
                            <span class="<?=$current_match_more==$i ? 'active' : ''; ?>"><a href="<?=admin_url('admin.php?page=match_student-score&match_id='.$match_id.'&student_id='.$user_id.'&proId='.$proId.'&more='.$i)?>">第<?=$i?>轮</a></span>
                        <?php } ?>
                        <!--                        <span><a href="">第一轮</a></span>-->
                        <!--                        <span><a href="">第二轮</a></span>-->
                        <!--                        <span><a href="">第三轮</a></span>-->
                        <!--                        <span><a href="">第四轮</a></span>-->
                    </div>
                </div>
                <h2 class="screen-reader-text">答题记录</h2>
                <br class="clear">
                <div><span>成绩:</span> <span> <?=$data['my_score']?></span></div>
                <div><span>本轮排名:</span> <span> <?=$data['ranking']?></span></div>
                <!--                <div><span>使用时间:</span> <span> --><?//=$data['use_time']?><!--</span></div>-->
                <div><span>剩余时间:</span> <span> <?=$data['surplus_time']?></span></div>
                <table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="match_questions" class="manage-column column-match_questions">比赛考题</th>
                        <th scope="col" id="questions_answer" class="manage-column column-questions_answer">考题答案</th>
                        <th scope="col" id="my_answer" class="manage-column column-my_answer">我的答案</th>
                        <th scope="col" id="is_correct" class="manage-column column-is_correct">是否正确</th>
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">
                    <!--                        </tr>-->
                    <?php
                    //                                            echo '<pre />';
                    //                                            var_dump($data);
                    //                                            die;
                    ////
                    if($data['match_questions'])
                        foreach ($data['match_questions'] as $k => $match_question){
                            if(in_array($data['project_alias'], ['nxss', 'kysm'])){
                                if(is_array($match_question)){
//
                                    foreach ($match_questions as $mqv){
                                        $str = '';
                                        if(is_array($match_question)){
                                            foreach ($mqv as $mqv2){
                                                $str .= $mqv2.' , ';
                                            }
                                        }
                                    }
                                }
                                $match_question = substr($str,0,strlen($str)-2);
                            }

                            ?>
                            <tr>

                                <th scope="row" class="check-column">
                                </th>
                                <td class="role column-match_questions" data-colname="比赛考题"><?=$match_question?></td>

                                <?php if($data['project_alias'] == 'wzsd'){ ?>

                                    <?php
                                    $str = '';
                                    $correct = null;
                                    foreach ($data['questions_answer'][$k]['problem_select'] as $qk => $questions_answer){
                                        if(in_array($qk,$data['questions_answer'][$k]['problem_answer'])){
                                            $str .= '<span style="color: #6d82cc">' .($qk+1).'.'.$questions_answer.'</span> <br /> ';
                                            $correct = $qk;
                                        }else{
                                            $str .= ($qk+1).'.'.$questions_answer.' <br /> ';
                                        }
                                    }
                                    $data['my_answer'][$k][0];

//                                        if($str != '') $str = substr($str, 0, strlen($str)-1);
                                    ?>
                                    <td class="role column-questions_answer" data-colname="考题答案"><?=$str?></td>
                                    <td class="role column-my_answer" data-colname="我的答案">
                                        <?=$data['my_answer'][$k][0] != '' ? $data['my_answer'][$k][0]+1 : '未作答'?>
                                    </td>
                                <?php }else{ ?>
                                    <td class="role column-questions_answer" data-colname="考题答案"><?=$data['questions_answer'][$k]?></td>
                                    <td class="role column-my_answer" data-colname="我的答案">
                                        <?=$data['my_answer'][$k] != '' ? $data['my_answer'][$k] : '未作答'?>
                                    </td>
                                <?php } ?>

                                <td class="role column-is_correct" data-colname="是否正确">
                                    <?php if($data['answer_array'] != null){ ?>
                                        <span style="color:<?=$data['answer_array'][$k] == 'true' ? '#0073aa' : '#ca4a1f'?>">
                                            <?=$data['answer_array'][$k] == 'true' ? '正确' : '错误'?>

                                        </span>
                                    <?php }else{ ?>
                                        <?php if($data['project_alias'] == 'wzsd'){ ?>
                                            <span style="color:<?=(string)$correct === (string)$data['my_answer'][$k][0] ? '#0073aa' : '#ca4a1f'?>">
                                            <?=(string)$data['my_answer'][$k][0] === (string)(string)$correct ? '正确' : '错误'?>
                                            </span>
                                        <?php }else{ ?>
                                            <span style="color:<?=(string)$data['my_answer'][$k] === (string)$data['questions_answer'][$k] ? '#0073aa' : '#ca4a1f'?>">
                                            <?=(string)$data['my_answer'][$k] === (string)$data['questions_answer'][$k] ? '正确' : '错误'?>
                                            </span>
                                        <?php } ?>
                                    <?php } ?>


                                </td>
                            </tr>
                            <?php
                            $match_questions_str = '';

                        }


                    ?>

                    </tbody>

                    <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-match_questions">比赛考题</th>
                        <th scope="col" class="manage-column column-questions_answer">考题答案</th>
                        <th scope="col" class="manage-column column-my_answer">我的答案</th>
                        <th scope="col" class="manage-column column-is_correct">是否正确</th>
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
        $match = $wpdb->get_row('SELECT match_status,match_more,match_id FROM '.$wpdb->prefix.'match_meta WHERE match_id='.$post->ID, ARRAY_A);

        //TODO 判断比赛是否结束
        $matchEnd = true;
        if(!$match || $match['match_status'] != -3){
            $matchEnd = false;
        }
        $rankingView = ['status' => true];

        //查询比赛小项目
        $projectArr = get_match_end_time($post->ID);
        $categoryArr = []; //分类选项卡数组
        $currentDateTime = get_time('mysql');
        //选项卡查询
        $op1 = isset($_GET['op1']) ? $_GET['op1'] : 3; //一级选项卡, 默认单项排名
        $op2 = isset($_GET['op2']) ? $_GET['op2'] : 'sdl'; //二级选项卡, 默认第一个分类
        $op3 = isset($_GET['op3']) ? $_GET['op3'] : $projectArr[0]['match_project_id']; //三级选项卡, 默认第一个项目
        $op4 = isset($_GET['op4']) ? $_GET['op4'] : 0; //四级选项卡, 默认全部年龄
        $op5 = isset($_GET['op5']) ? $_GET['op5'] : 1; //五级选项卡(总排名和战队排名), 默认总排名
        $downloadParam = "&op1={$op1}&op2={$op2}&op3={$op3}&op4={$op4}&op5={$op5}";
//        leo_dump($op2);
        $data = [];
        if($op1 == 1){
            if($matchEnd == false){
                $rankingView = ['status' => false, 'msg' => '当前比赛未结束!'];
            }else{
                $data = $this->getAllRankingData($match,$projectArr,$op5);
            }
        }elseif ($op1 == 2){
            //获取当前分类的id字符串
            $project_id_array = [];//项目id数组
            $project_alias_arr = [];// 分类下的项目数组
            switch ($op2){
                case 'sdl':
                    $project_alias_arr = ['wzsd','kysm'];
                    break;
                case 'ssl':
                    $project_alias_arr = ['zxss','nxss'];
                    break;
                case 'sjl':
                    $project_alias_arr = ['szzb','pkjl'];
                    break;
                default:
                    exit('参数错误');
            }
            foreach ($projectArr as $pavGetIds){
                if(in_array($pavGetIds['project_alias'],$project_alias_arr )){
                    if($currentDateTime < $pavGetIds['project_end_time']){
                        $rankingView = ['status' => false, 'msg' => '当前分类未结束!'];
                        break;
                    }
                    $project_id_array[] = $pavGetIds['match_project_id'];
                };
            }
            if($rankingView['status'] == true){
                $data = $this->getCategoryRankingData($match,join(',',$project_id_array),$op4);
            }
        }elseif ($op1 == 3){
            foreach ($projectArr as $pavGetIds){
                if($pavGetIds['match_project_id'] == $op3 && $currentDateTime < $pavGetIds['project_end_time']){
                    $rankingView = ['status' => false, 'msg' => '当前项目未结束!'];
                    break;
                }
            }
            if($rankingView['status'] == true) $data = $this->getCategoryRankingData($match,$op3,$op4);
        }else{
            exit('参数错误!');
        }

        //显示html
//        leo_dump($data);
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?=$post->post_title?>-比赛排名<?=$matchEnd ? '' : '(未结束)'?></h1>

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
                            <span class="<?php if($op1 == 3){ ?>active<?php } ?>"><a href="<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=3')?>">单项排名</a></span>
                            <span class="<?php if($op1 == 2){ ?>active<?php } ?>"><a href="<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=2')?>">分类排名</a></span>
                            <span class="<?php if($op1 == 1){ ?>active<?php } ?>"><a href="<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=1')?>">总排名</a></span>
                        </div>

                        <?php if($op1 == 1){ ?>
                            <div id="option5">
                                <span class="<?php if($op5 == 1){ ?>active<?php } ?>"><a href="<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=1&op5=1')?>">个人排名</a></span>
                                <span class="<?php if($op5 == 2){ ?>active<?php } ?>"><a href="<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=1&op5=2')?>">战队排名</a></span>
                            </div>
                        <?php } ?>
                        <?php if($op1 == 2) { ?>
                            <div id="option2">

                                <span class="<?php if($op2 == 'sdl'){ ?>active<?php } ?>">
                                    <a href="<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=2&op2=sdl')?>">速读类</a>
                                </span>
                                <span class="<?php if($op2 == 'sjl'){ ?>active<?php } ?>">
                                    <a href="<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=2&op2=sjl')?>">速记类</a>
                                </span>
                                <span class="<?php if($op2 == 'ssl'){ ?>active<?php } ?>">
                                    <a href="<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=2&op2=ssl')?>">速算类</a>
                                </span>


                            </div>
                        <?php } ?>
                        <?php if($op1 == 3){ ?>
                            <div id="option3">

                                <?php foreach ($projectArr as $pav2) { ?>
                                    <span class="<?php if($op3 == $pav2['match_project_id']){ ?>active<?php } ?>">
                                        <a href="<?= admin_url('admin.php?page=match_student-ranking&match_id=' . $post->ID . '&op1=3&op3=' . $pav2['match_project_id']) ?>"><?= $pav2['post_title'] ?></a>
                                    </span>
                                <?php } ?>
                                <select name="" id="option4" onchange="window.location.href='<?=admin_url('admin.php?page=match_student-ranking&match_id='.$post->ID.'&op1=3&op3='.$op3.'&op4=')?>'+this.value">
                                    <option <?php if($op4 == '0'){ ?>selected="selected"<?php } ?> value="0">全部</option>
                                    <option <?php if($op4 == '4'){ ?>selected="selected"<?php } ?> value="4">儿童组</option>
                                    <option <?php if($op4 == '3'){ ?>selected="selected"<?php } ?> value="3">少年组</option>
                                    <option <?php if($op4 == '2'){ ?>selected="selected"<?php } ?> value="2">成年组</option>
                                    <option <?php if($op4 == '1'){ ?>selected="selected"<?php } ?> value="1">老年组</option>
                                </select>
                            </div>
                        <?php } ?>


                    </div>

                    <div class="alignleft actions">
                    </div>
                    <div class="alignleft actions bulkactions">
                        <?php if($rankingView['status'] == true){ ?>
                            <a href="admin.php?page=download&action=match_ranking&match_id=<?=$post->ID.$downloadParam?>" class="button">导出排名</a>
                        <?php } ?>
                    </div>
                    <div class="tablenav-pages one-page">

                    </div>
                    <br class="clear">

                </div>    <br class="clear">
                <h2 class="screen-reader-text">排名列表</h2>
                <?php if($rankingView['status'] == true){ ?>
                    <?php if($op1 == 1 && $op5 == 2){ ?>
                        <table class="wp-list-table widefat fixed striped users">
                            <thead>
                            <tr>
                                <td id="cb" class="manage-column column-cb check-column">
                                    <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                                    <input id="cb-select-all-1" type="checkbox">
                                </td>
                                <th scope="col" id="ID" class="manage-column column-username column-primary">

                                    <span>名次</span>
                                </th>
                                <th scope="col" id="real_name" class="manage-column column-real_name">战队</th>
                                <th scope="col" id="sex" class="manage-column column-sex">ID</th>
                                <th scope="col" id="birthday" class="manage-column column-birthday">总成绩</th>
                            </tr>
                            </thead>
                            <tbody id="the-list" data-wp-lists="list:user">
                            <?php foreach ($data as $raV){ ?>
                                <tr id="user-13">
                                    <th scope="row" class="check-column">
                                        <label class="screen-reader-text" for="user_13"></label>
                                        <input type="checkbox" name="users[]" id="" class="subscriber" value="">
                                    </th>
                                    <td class="username column-username has-row-actions column-primary" data-colname="名次">
                                        <strong><?=$raV['ranking']?></strong><br>
                                        <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                                    </td>
                                    <td class="name column-real_name" data-colname="战队"><span aria-hidden="true"><?=$raV['team_name']?></span><span class="screen-reader-text"></span></td>
                                    <td class="name column-sex" data-colname="ID"><span aria-hidden="true"><?=$raV['team_id']?></span><span class="screen-reader-text">-</span></td>
                                    <td class="name column-birthday" data-colname="总成绩"><span aria-hidden="true"><?=$raV['my_score']?></span><span class="screen-reader-text">-</span></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td class="manage-column column-cb check-column">
                                    <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                                    <input id="cb-select-all-2" type="checkbox">
                                </td>

                                <th scope="col" class="manage-column column-ID column-primary">名次</th>
                                <th scope="col" class="manage-column column-real_name">战队</th>
                                <th scope="col" class="manage-column column-sex">ID</th>
                                <th scope="col" class="manage-column column-birthday">总成绩</th>
                            </tr>
                            </tfoot>

                        </table>
                    <?php }else{ ?>
                        <table class="wp-list-table widefat fixed striped users">
                            <thead>
                            <tr>
                                <td id="cb" class="manage-column column-cb check-column">
                                    <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                                    <input id="cb-select-all-1" type="checkbox">
                                </td>

                                <th scope="col" id="ID" class="manage-column column-ID column-primary">学员ID</th>
                                <th scope="col" id="real_name" class="manage-column column-real_name">姓名</th>
                                <th scope="col" id="sex" class="manage-column column-sex">性别</th>
                                <th scope="col" id="birthday" class="manage-column column-birthday">年龄</th>
                                <th scope="col" id="age" class="manage-column column-age">年龄组别</th>
                                <th scope="col" id="address" class="manage-column column-address">所在地区</th>
                                <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                                <th scope="col" id="email" class="manage-column column-email">邮箱</th>
                                <th scope="col" id="created_time" class="manage-column column-created_time">报名时间</th>

                                <th scope="col" id="ranking" class="manage-column column-ranking">名次</th>
                                <th scope="col" id="total_score" class="manage-column column-total_score">得分</th>

                                <?php
                                if(isset($data[0]['projectScore'])) {
                                    foreach ($projectArr as $titleV) {
                                        ?>
                                        <th scope="col" id="" class="manage-column column-"><?= $titleV['post_title'] ?>得分</th>
                                        <?php
                                    }
                                }
                                ?>
                                <!--                        <th scope="col" id="" class="manage-column column-">扑克接力得分</th>-->
                                <!--                        <th scope="col" id="" class="manage-column column-">快眼扫描得分</th>-->
                                <!--                        <th scope="col" id="" class="manage-column column-">文章速读得分</th>-->
                                <!--                        <th scope="col" id="" class="manage-column column-">正向运算得分</th>-->
                                <!--                        <th scope="col" id="" class="manage-column column-">逆向运算得分</th>-->
                            </tr>
                            </thead>

                            <tbody id="the-list" data-wp-lists="list:user">


                            <?php foreach ($data as $raV){

                                ?>
                                <tr id="user-13">
                                    <th scope="row" class="check-column">
                                        <label class="screen-reader-text" for="user_13"></label>
                                        <input type="checkbox" name="users[]" id="" class="subscriber" value="">
                                    </th>

                                    <td class="name column-ID column-primary" data-colname="学员ID">
                                        <span aria-hidden="true"><?=$raV['userID']?></span><span class="screen-reader-text">-</span>

                                        <div class="row-actions">
                                            <!--                                    <span class="edit"><a href="https://ydbeta.gjnlyd.com/wp-admin/user-edit.php?user_id=311&amp;wp_http_referer=%2Fwp-admin%2Fusers.php">编辑</a> | </span>-->
                                            <!--                                    <span class="delete"><a class="submitdelete" href="users.php?action=delete&amp;user=311&amp;_wpnonce=0046431749">删除</a> | </span>-->
                                            <span class="view"><a href="<?=admin_url('admin.php?page=match_student-score&match_id=' . $post->ID . '&student_id='.$raV['user_id'])?>" aria-label="">答题记录</a></span>
                                        </div>

                                        <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                                    </td>
                                    <td class="name column-real_name" data-colname="姓名"><span aria-hidden="true"><?=$raV['real_name']?></span><span class="screen-reader-text"></span></td>
                                    <td class="name column-sex" data-colname="性别"><span aria-hidden="true"><?=$raV['sex']?></span><span class="screen-reader-text">-</span></td>
                                    <td class="name column-birthday" data-colname="出生日期"><span aria-hidden="true"><?=$raV['age']?></span><span class="screen-reader-text">-</span></td>
                                    <td class="name column-age" data-colname="年龄组别"><span aria-hidden="true"><?=$raV['ageGroup']?></span><span class="screen-reader-text">-</span></td>
                                    <td class="name column-address" data-colname="所在地区"><span aria-hidden="true"><?=$raV['address']?></span><span class="screen-reader-text">-</span></td>
                                    <td class="name column-mobile" data-colname="手机"><span aria-hidden="true"><?=$raV['telephone'] ? $raV['telephone'] : $raV['user_mobile']?></span><span class="screen-reader-text">-</span></td>
                                    <td class="name column-email" data-colname="邮箱"><span aria-hidden="true"><?=$raV['user_email']?></span><span class="screen-reader-text">-</span></td>
                                    <td class="name column-created_time" data-colname="报名时间"><span aria-hidden="true"><?=$raV['created_time']?></span><span class="screen-reader-text">-</span></td>

                                    <td class="name column-ranking" data-colname="名次"><span aria-hidden="true"><?=$raV['ranking']?></span><span class="screen-reader-text">-</span></td>
                                    <td class="name column-total_score" data-colname="得分"><span aria-hidden="true"><?=$raV['my_score']?></span><span class="screen-reader-text">-</span></td>

                                    <?php
                                    if(isset($raV['projectScore'])) {
                                        foreach ($raV['projectScore'] as $ravV) {
                                            ?>
                                            <td class="name column-total_score" data-colname=""><span
                                                        aria-hidden="true"><?= $ravV ?></span><span
                                                        class="screen-reader-text">-</span></td>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tr>
                            <?php } ?>
                            </tbody>

                            <tfoot>


                            <tr>
                                <td class="manage-column column-cb check-column">
                                    <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                                    <input id="cb-select-all-2" type="checkbox">
                                </td>
                                <th scope="col" class="manage-column column-ID column-primary">学员ID</th>
                                <th scope="col" class="manage-column column-real_name">姓名</th>
                                <th scope="col" class="manage-column column-sex">性别</th>
                                <th scope="col" class="manage-column column-birthday">年龄</th>
                                <th scope="col" class="manage-column column-age">年龄组别</th>
                                <th scope="col" class="manage-column column-address">所在地区</th>
                                <th scope="col" class="manage-column column-mobile">手机</th>
                                <th scope="col" class="manage-column column-email">邮箱</th>
                                <th scope="col" class="manage-column column-created_time">报名时间</th>

                                <th scope="col" class="manage-column column-ranking">名次</th>
                                <th scope="col" class="manage-column column-total_score">得分</th>



                                <?php
                                if(isset($raV['projectScore'])) {
                                    foreach ($projectArr as $titleV) {
                                        ?>
                                        <th scope="col" class="manage-column column-"><?= $titleV['post_title'] ?>得分</th>
                                        <?php
                                    }
                                }
                                ?>
                            </tr>
                            </tfoot>

                        </table>
                    <?php } ?>
                <?php }else{ ?>
                    <h2><?=$rankingView['msg']?></h2>
                <?php } ?>
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
     * 排名分类和单项数据
     */
    public function getCategoryRankingData($match,$projectIdStr,$ageType){
        global $wpdb;
        //获取每个用户的每个分类的分数和排名
        switch ($ageType){
            case 4://儿童组
                $ageWhere = ' y.meta_value<13';
                break;
            case 3://少年组
                $ageWhere = ' y.meta_value>12 AND y.meta_value<18';
                break;
            case 2://成年组
                $ageWhere = ' y.meta_value>17 AND y.meta_value<60';
                break;
            case 1://老年组
                $ageWhere = ' y.meta_value>59';
                break;
            default://全部
                $ageWhere = ' 1=1';
        }

        $result = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS x.user_id,SUM(x.my_score) my_score ,x.telephone,SUM(x.surplus_time) surplus_time,u.user_login,u.user_mobile,u.user_email,x.created_time,x.project_id,x.created_microtime  
                    FROM(
                        SELECT a.user_id,a.match_id,c.project_id,MAX(c.my_score) my_score ,a.telephone, MAX(c.surplus_time) surplus_time,if(MAX(c.created_microtime) > 0, MAX(c.created_microtime) ,0) created_microtime,a.created_time 
                        FROM `{$wpdb->prefix}order` a 
                        LEFT JOIN {$wpdb->prefix}match_questions c ON a.user_id = c.user_id  and c.match_id = {$match['match_id']} and c.project_id IN({$projectIdStr})                 
                        WHERE a.match_id = {$match['match_id']} AND a.pay_status = 4 and a.order_type = 1 
                        GROUP BY user_id,project_id
                    ) x
                    left join `{$wpdb->prefix}usermeta` y on x.user_id = y.user_id and y.meta_key='user_age' 
                    left join `{$wpdb->users}` u on u.ID=y.user_id 
                    WHERE {$ageWhere}
                    GROUP BY user_id
                    ORDER BY my_score DESC,surplus_time DESC,x.created_microtime ASC ", ARRAY_A);

        $list = array();
        $ranking = 1;
        foreach ($result as $k => $val){
//            $result[$k]['projectScore'] = [$result[$k]['my_score']];//与总排名数据格式一致
            $sql1 = " select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id = {$val['user_id']} and meta_key in('user_address','user_ID','user_real_name','user_age','user_gender','user_birthday') ";
            $info = $wpdb->get_results($sql1,ARRAY_A);

            if(!empty($info)){
                $user_info = array_column($info,'meta_value','meta_key');
                $user_real_name = !empty($user_info['user_real_name']) ? unserialize($user_info['user_real_name']) : '';

                $result[$k]['real_name'] = !empty($user_real_name['real_name']) ? $user_real_name['real_name'] : '-';
                if(!empty($user_info['user_age'])){
                    $age = $user_info['user_age'];
                    $group = getAgeGroupNameByAge($age);

                }else{
                    $group = '-';
                }
                if(!empty($user_info['user_address'])){
                    $user_address = unserialize($user_info['user_address']);
//                    $city = $user_address['city'] == '市辖区' ? $user_address['city'] : $user_address['province'];
                    $city = $user_address['province'].$user_address['city'];
                }else{
                    $city = '-';
                }

                $result[$k]['userID'] = $user_info['user_ID'];
                $result[$k]['address'] = $city;
                //$list[$k]['score'] = $val['my_score'];
                $result[$k]['ageGroup'] = $group;
                $result[$k]['age'] = $age;
                $result[$k]['sex'] = $user_info['user_gender'] ? $user_info['user_gender'] : '-';
                $result[$k]['birthday'] = isset($user_info['user_birthday']) ? $user_info['user_birthday'] : '-';
                $result[$k]['score'] = $val['my_score'] > 0 ? $val['my_score'] : 0;
                $result[$k]['my_score'] = $val['my_score'] > 0 ? $val['my_score'] : 0;
                $result[$k]['ranking'] = $ranking;
                if($val['my_score'] > 0) ++$ranking;

//                if($k != 0){
//                    if(($val['my_score'] == $result[$k-1]['my_score'] && $val['surplus_time'] == $result[$k-1]['surplus_time']) || ($val['my_score']== 0 && $result[$k-1]['my_score']==0)){
//                        $result[$k]['ranking'] = $result[$k-1]['ranking'];
//                    }
//                }
//                if($val['user_id'] == $current_user->ID){
//                    $my_ranking = $list[$k];
//                }
            }
        }
        return $result;
    }

    /**
     * 排名总数据
     */
    public function getAllRankingData($match,$projectArr,$op5){
        global $wpdb;

        if($op5 == 1){
            //个人排名
            //先查询所有成员
            $totalRanking = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS o.telephone,u.user_email,o.user_id,mq.project_id,u.user_mobile,o.created_time,um.meta_value AS user_age 
               FROM '.$wpdb->prefix.'order AS o
               LEFT JOIN '.$wpdb->users.' AS u ON u.ID=o.user_id
               LEFT JOIN '.$wpdb->usermeta.' AS um ON um.user_id=u.ID AND um.meta_key="user_age"
               LEFT JOIN '.$wpdb->prefix.'match_questions AS mq ON mq.user_id=u.ID
               WHERE o.match_id='.$match['match_id'].' AND o.pay_status IN(2,3,4) AND u.ID != "" GROUP BY o.user_id ORDER BY u.ID ASC', ARRAY_A);

            //查询每个成员分数
            foreach ($totalRanking as &$trv){
                $trv['my_score'] = 0;
                $trv['surplus_time'] = 0;
                $trv['created_microtime'] = 0;
                $trv['projectScore'] = []; //项目分数数组
                foreach ($projectArr as $paks => $pavs) {
                    $res = $wpdb->get_results('SELECT my_score,match_more,surplus_time,project_id,created_microtime FROM '.$wpdb->prefix.'match_questions 
                        WHERE match_id='.$match['match_id'].' AND user_id='.$trv['user_id'].' AND project_id='.$pavs['match_project_id'], ARRAY_A);
                    $scoreArr = [];//项目所有分数数组
                    $surplus_timeArr = [];//项目所有剩余时间数组
                    $created_microtimeArrr = [];//项目所提交毫秒数组
                    $moreArr = []; //每一轮分数数组
                    $match_more_all = $pavs['match_more'] > 0 ? $pavs['match_more'] : $match['match_more'];
                    for($mi = 1; $mi <= $match_more_all; ++$mi){
                        $moreArr[$mi] = '0';
                    }
                    foreach ($res as $resV){
                        $surplus_timeArr[] = $resV['surplus_time'];
                        $scoreArr[] = $resV['my_score'];
                        $created_microtimeArrr[] = $resV['created_microtime'];
                        $moreArr[$resV['match_more']] = $resV['my_score'] ? $resV['my_score'] : '0';
                    }
                    $trv['projectScore'][$paks] = join('/', $moreArr);//每个项目分数字符串
                    $trv['my_score'] += $scoreArr == [] ? 0 : max($scoreArr);//每个项目最大分数和
                    $trv['surplus_time'] += $scoreArr == [] ? 0 : max($surplus_timeArr);//每个项目最大剩余时间和
                    $trv['created_microtime'] += $created_microtimeArrr == [] ? 0 : max($created_microtimeArrr);//每个项目提交毫秒时间和
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
                $trv['birthday'] = isset($usermeta['user_birthday']) ? $usermeta['user_birthday'][0] : '-';
                $trv['address'] = unserialize($usermeta['user_address'][0])['province'].unserialize($usermeta['user_address'][0])['city'];
            }

        }else{
            //战队排名

            //获取参加比赛的成员
            $sql = "SELECT p.post_title,p.ID,o.user_id FROM `{$wpdb->prefix}order` AS o 
                    LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON o.user_id=mt.user_id AND mt.status=2 
                    LEFT JOIN `{$wpdb->posts}` AS p ON p.ID=mt.team_id 
                    WHERE o.match_id={$match['match_id']} AND o.pay_status IN(2,3,4) AND mt.team_id!='' AND p.post_title!=''";
            $result = $wpdb->get_results($sql, ARRAY_A);
            //处理每个战队的成员
            $teamsUsers = []; //每个战队的每个成员
            foreach ($result as $resV){
                if(!isset($teamsUsers[$resV['ID']])) {
                    $teamsUsers[$resV['ID']] = [];
                    $teamsUsers[$resV['ID']]['user_ids'] = [];
                    $teamsUsers[$resV['ID']]['team_name'] = $resV['post_title'];
                    $teamsUsers[$resV['ID']]['team_id'] = $resV['ID'];
                }
                $teamsUsers[$resV['ID']]['user_ids'][] = $resV['user_id'];
            }
            foreach ($teamsUsers as &$tuV){
                $tuV['user_ids'] = join(',',$tuV['user_ids']);
            }
            $totalRanking = [];
            foreach ($teamsUsers as $tuV2){
                //每个战队的分数
            
             
             $sql = "SELECT SUM(my_score) AS my_score,SUM(surplus_time) AS surplus_time,SUM(created_microtime) AS created_microtime FROM 
                  (SELECT MAX(my_score) AS my_score,MAX(surplus_time) AS surplus_time,if(MAX(created_microtime) > 0, MAX(created_microtime) ,0) AS created_microtime,mq.user_id FROM `{$wpdb->prefix}match_questions` AS mq 
                  LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON mt.user_id=mq.user_id AND mt.status=2 AND mt.team_id={$tuV2['team_id']}
                  WHERE mq.match_id={$match['match_id']} AND mt.team_id={$tuV2['team_id']} AND mq.user_id IN({$tuV2['user_ids']}) 
                  GROUP BY mq.project_id,mq.user_id) AS child  
                  GROUP BY user_id 
                  ORDER BY my_score DESC limit 0,5
               ";        
                $rows = $wpdb->get_results($sql,ARRAY_A);
                // leo_dump($wpdb->last_query);
				$tuV2['my_score'] = 0;
				$tuV2['surplus_time'] = 0;
				$tuV2['created_microtime'] = 0;
                foreach ($rows as $key => $value) {
                	$tuV2['my_score'] += $value['my_score'];
				$tuV2['surplus_time'] += $value['surplus_time'];
				$tuV2['created_microtime'] += $value['created_microtime'];
                }
                $totalRanking[] = $tuV2;
            }
        }

        //排序
        for($i = 0; $i < count($totalRanking); ++$i){
            if(isset($totalRanking[$i+1])){
                for ($j = $i+1; $j < count($totalRanking); ++$j){
                    if($totalRanking[$i]['my_score'] == $totalRanking[$j]['my_score']){
//                       if($totalRanking[$i]['my_score'] < 1){
//                           $rankingAuto = false;
//                       }else
                        if($totalRanking[$j]['surplus_time'] > $totalRanking[$i]['surplus_time']){

                            $a = $totalRanking[$j];
                            $totalRanking[$j] = $totalRanking[$i];
                            $totalRanking[$i] = $a;
                        }elseif ($totalRanking[$j]['surplus_time'] == $totalRanking[$i]['surplus_time']){
                            if($totalRanking[$j]['created_microtime'] < $totalRanking[$i]['created_microtime']){
                                $a = $totalRanking[$j];
                                $totalRanking[$j] = $totalRanking[$i];
                                $totalRanking[$i] = $a;
                            }
                        }
                    }elseif ($totalRanking[$j]['my_score'] > $totalRanking[$i]['my_score']){
                        $a = $totalRanking[$j];
                        $totalRanking[$j] = $totalRanking[$i];
                        $totalRanking[$i] = $a;
                    }
                }
            }
        }
        //名次
        $ranking = 1;
        foreach ($totalRanking as $k => $v){
            $totalRanking[$k]['ranking'] = $ranking;
            if( $totalRanking[$k]['my_score'] > 0){
                ++$ranking;
            }
        }
        return $totalRanking;
    }

    /*
     * ========================================================================================
     */

    /**
     * 排名分类和单项数据,战队排名算法备份
     */
    public function getCategoryRankingData_back($match,$projectIdStr,$ageType, $is_team = false){
        global $wpdb;
        //获取每个用户的每个分类的分数和排名
        switch ($ageType){
            case 4://儿童组
                $ageWhere = ' y.meta_value<13';
                break;
            case 3://少年组
                $ageWhere = ' y.meta_value>12 AND y.meta_value<18';
                break;
            case 2://成年组
                $ageWhere = ' y.meta_value>17 AND y.meta_value<60';
                break;
            case 1://老年组
                $ageWhere = ' y.meta_value>59';
                break;
            default://全部
                $ageWhere = ' 1=1';
        }
        //如果是战队来获取
        $teamJoin = '';
        $teamWhere = '';
        $teamColumn= '';
        $teamLimit = '';
        if($is_team == true){
            $teamJoin = " LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON mt.user_id=c.user_id 
                        LEFT JOIN `{$wpdb->posts}` AS p ON p.ID=mt.team_id";
            $teamWhere = " AND mt.id!='' AND p.post_title!=''";
            $teamColumn = ',team_id,post_title';
            $teamLimit = "LIMIT 0,3";
        }
        $result = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS x.user_id,SUM(x.my_score) my_score ,x.telephone,SUM(x.surplus_time) surplus_time,u.user_login,u.user_mobile,u.user_email,x.created_time,x.project_id{$teamColumn} 
                    FROM(
                        SELECT a.user_id,a.match_id,c.project_id,MAX(c.my_score) my_score ,a.telephone, MAX(c.surplus_time) surplus_time,a.created_time{$teamColumn} 
                        FROM `{$wpdb->prefix}order` a 
                        LEFT JOIN {$wpdb->prefix}match_questions c ON a.user_id = c.user_id  and c.match_id = {$match['match_id']} and c.project_id IN({$projectIdStr}) 
                        {$teamJoin}                 
                        WHERE a.match_id = {$match['match_id']} AND a.pay_status = 4 and a.order_type = 1 {$teamWhere}
                        GROUP BY user_id,project_id
                    ) x
                    left join `{$wpdb->prefix}usermeta` y on x.user_id = y.user_id and y.meta_key='user_age' 
                    left join `{$wpdb->users}` u on u.ID=y.user_id 
                    WHERE {$ageWhere}
                    GROUP BY user_id
                    ORDER BY my_score DESC,surplus_time DESC {$teamLimit}", ARRAY_A);

        $list = array();
        $start = 0;
        foreach ($result as $k => $val){
//            $result[$k]['projectScore'] = [$result[$k]['my_score']];//与总排名数据格式一致
            $sql1 = " select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id = {$val['user_id']} and meta_key in('user_address','user_ID','user_real_name','user_age','user_gender','user_birthday') ";
            $info = $wpdb->get_results($sql1,ARRAY_A);


            if(!empty($info)){
                if($is_team == false){
                    $user_info = array_column($info,'meta_value','meta_key');
                    $user_real_name = !empty($user_info['user_real_name']) ? unserialize($user_info['user_real_name']) : '';

                    $result[$k]['real_name'] = !empty($user_real_name['real_name']) ? $user_real_name['real_name'] : '-';
                    if(!empty($user_info['user_age'])){
                        $age = $user_info['user_age'];
                        $group = getAgeGroupNameByAge($age);

                    }else{
                        $group = '-';
                    }
                    if(!empty($user_info['user_address'])){
                        $user_address = unserialize($user_info['user_address']);
//                    $city = $user_address['city'] == '市辖区' ? $user_address['city'] : $user_address['province'];
                        $city = $user_address['province'].$user_address['city'];
                    }else{
                        $city = '-';
                    }

                    $result[$k]['userID'] = $user_info['user_ID'];
                    $result[$k]['address'] = $city;
                    //$list[$k]['score'] = $val['my_score'];
                    $result[$k]['ageGroup'] = $group;
                    $result[$k]['age'] = $age;
                    $result[$k]['sex'] = $user_info['user_gender'] ? $user_info['user_gender'] : '-';
                    $result[$k]['birthday'] = isset($user_info['user_birthday']) ? $user_info['user_birthday'] : '-';
                    $result[$k]['score'] = $val['my_score'] > 0 ? $val['my_score'] : 0;
                    $result[$k]['my_score'] = $val['my_score'] > 0 ? $val['my_score'] : 0;
                }
                $result[$k]['ranking'] = $start+$k+1;
                if($k != 0){
                    if(($val['my_score'] == $result[$k-1]['my_score'] && $val['surplus_time'] == $result[$k-1]['surplus_time']) || ($val['my_score']== 0 && $result[$k-1]['my_score']==0)){
                        $result[$k]['ranking'] = $result[$k-1]['ranking'];
                    }
                }
//                if($val['user_id'] == $current_user->ID){
//                    $my_ranking = $list[$k];
//                }
            }
        }
        return $result;
    }
    /**
     * 排名总数据,战队排名算法备份
     */
    public function getAllRankingData_back($match,$projectArr,$op5){
        global $wpdb;

        if($op5 == 1){
            //个人排名
            //先查询所有成员
            $totalRanking = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS o.telephone,u.user_email,o.user_id,mq.project_id,u.user_mobile,o.created_time,um.meta_value AS user_age 
               FROM '.$wpdb->prefix.'order AS o
               LEFT JOIN '.$wpdb->users.' AS u ON u.ID=o.user_id
               LEFT JOIN '.$wpdb->usermeta.' AS um ON um.user_id=u.ID AND um.meta_key="user_age"
               LEFT JOIN '.$wpdb->prefix.'match_questions AS mq ON mq.user_id=u.ID
               WHERE o.match_id='.$match['match_id'].' AND o.pay_status IN(2,3,4) AND u.ID != "" GROUP BY o.user_id ORDER BY u.ID ASC', ARRAY_A);

            //查询每个成员分数
            foreach ($totalRanking as &$trv){
                $trv['my_score'] = 0;
                $trv['surplus_time'] = 0;
                $trv['projectScore'] = []; //项目分数数组
                foreach ($projectArr as $paks => $pavs) {
                    $res = $wpdb->get_results('SELECT my_score,match_more,surplus_time,project_id FROM '.$wpdb->prefix.'match_questions 
                        WHERE match_id='.$match['match_id'].' AND user_id='.$trv['user_id'].' AND project_id='.$pavs['match_project_id'], ARRAY_A);
                    $scoreArr = [];//项目所有分数数组
                    $surplus_timeArr = [];//项目所有剩余时间数组
                    $moreArr = []; //每一轮分数数组
                    $match_more_all = $pavs['match_more'] > 0 ? $pavs['match_more'] : $match['match_more'];
                    for($mi = 1; $mi <= $match_more_all; ++$mi){
                        $moreArr[$mi] = '0';
                    }
                    foreach ($res as $resV){
                        $surplus_timeArr[] = $resV['surplus_time'];
                        $scoreArr[] = $resV['my_score'];
                        $moreArr[$resV['match_more']] = $resV['my_score'] ? $resV['my_score'] : '0';
                    }
                    $trv['projectScore'][$paks] = join('/', $moreArr);//每个项目分数字符串
                    $trv['my_score'] += $scoreArr == [] ? 0 : max($scoreArr);//每个项目最大分数和
                    $trv['surplus_time'] += $scoreArr == [] ? 0 : max($surplus_timeArr);//每个项目最大剩余时间和
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
                $trv['birthday'] = isset($usermeta['user_birthday']) ? $usermeta['user_birthday'][0] : '-';
                $trv['address'] = unserialize($usermeta['user_address'][0])['province'].unserialize($usermeta['user_address'][0])['city'];
            }

        }else{
            //战队排名
            //获取三大类
            $categoryArr = [
                'sdl' => [
                    'ids' => [],
                    'item' => []
                ],
                'ssl' => [
                    'ids' => [],
                    'item' => []
                ],
                'sjl' => [
                    'ids' => [],
                    'item' => []
                ],
            ]; //三大类数组
            foreach ($projectArr as $tpav){
                if(in_array($tpav['project_alias'], ['wzsd','kysm'])){
                    $categoryArr['sdl']['item'][] = $tpav;
                    $categoryArr['sdl']['ids'][] = $tpav['match_project_id'];
                }elseif(in_array($tpav['project_alias'], ['zxss','nxss'])){
                    $categoryArr['ssl']['item'][] = $tpav;
                    $categoryArr['ssl']['ids'][] = $tpav['match_project_id'];
                }elseif(in_array($tpav['project_alias'], ['szzb','pkjl'])){
                    $categoryArr['sjl']['item'][] = $tpav;
                    $categoryArr['sjl']['ids'][] = $tpav['match_project_id'];
                }
            }

            //每一类有战队的排名
            foreach ($categoryArr as  &$tcav){
                $tcav['data'] = $this->getCategoryRankingData($match, join(',', $tcav['ids']), 0, true);
            }
            //每个战队的成员
            $teamUserArr = [];
            foreach ($categoryArr as $tcak2 => $tcav2){
                foreach ($tcav2['data'] as $titemv){
                    if(!isset($teamUserArr[$titemv['team_id']])) $teamUserArr[$titemv['team_id']] = [];
                    if(!isset($teamUserArr[$titemv['team_id']]['user_ids'])) $teamUserArr[$titemv['team_id']]['user_ids'] = [];
                    if(!isset($teamUserArr[$titemv['team_id']]['datas'])) $teamUserArr[$titemv['team_id']]['user_ids'] = [];
                    $titemv['category_str'] = $tcak2;
                    $teamUserArr[$titemv['team_id']]['datas'][] = $titemv;
                    $teamUserArr[$titemv['team_id']]['user_ids'][] = $titemv['user_id'];
                }
            }
            foreach ($teamUserArr as &$tuav){
                $tuav['integral'] = 0;
                foreach ($categoryArr as  $tcav3){
                    foreach ($tcav3['data'] as $titemv2){
                        if(in_array($titemv2['user_id'], $tuav['user_ids'])){
                            $tuav['integral'] += $titemv2['ranking'];
                        }
                    }
                }
            }

            //获取参加比赛的成员
            $sql = "SELECT p.post_title,p.ID,o.user_id FROM `{$wpdb->prefix}order` AS o 
                    LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON o.user_id=mt.user_id AND mt.status=2 
                    LEFT JOIN `{$wpdb->posts}` AS p ON p.ID=mt.team_id 
                    WHERE o.match_id={$match['match_id']} AND o.pay_status IN(2,3,4) AND mt.team_id!=''";
            $result = $wpdb->get_results($sql, ARRAY_A);
            //处理每个战队的成员
            $teamsUsers = []; //每个战队的每个成员
            foreach ($result as $resV){
                if(!isset($teamsUsers[$resV['ID']])) {
                    $teamsUsers[$resV['ID']] = [];
                    $teamsUsers[$resV['ID']]['user_ids'] = [];
                    $teamsUsers[$resV['ID']]['team_name'] = $resV['post_title'];
                    $teamsUsers[$resV['ID']]['team_id'] = $resV['ID'];
                }
                $teamsUsers[$resV['ID']]['user_ids'][] = $resV['user_id'];
            }
            foreach ($teamsUsers as &$tuV){
                $tuV['user_ids'] = join(',',$tuV['user_ids']);
            }
            $totalRanking = [];
            foreach ($teamsUsers as $tuV2){
                //每个战队的分数
                $sql = "SELECT SUM(my_score) AS my_score,SUM(surplus_time) AS surplus_time FROM 
                          (SELECT MAX(my_score) AS my_score,MAX(surplus_time) AS surplus_time FROM `{$wpdb->prefix}match_questions` AS mq 
                          LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON mt.user_id=mq.user_id AND mt.status=2 AND mt.team_id={$tuV2['team_id']}
                          WHERE mq.match_id={$match['match_id']} AND mt.team_id={$tuV2['team_id']} AND mq.user_id IN({$tuV2['user_ids']}) 
                          GROUP BY mq.project_id,mq.user_id) AS child  
                          ORDER BY my_score DESC limit 0,5
                       ";
                $row = $wpdb->get_row($sql,ARRAY_A);
                $tuV2['my_score'] = $row['my_score'] > 0 ? $row['my_score'] : 0;
                $tuV2['surplus_time'] = $row['surplus_time'] > 0 ? $row['surplus_time'] : 0;
                $totalRanking[] = $tuV2;
            }
        }

        //排序
        for($i = 0; $i < count($totalRanking); ++$i){
            if(isset($totalRanking[$i+1])){
                for ($j = $i+1; $j < count($totalRanking); ++$j){
                    if($totalRanking[$i]['my_score'] == $totalRanking[$j]['my_score']){
//                       if($totalRanking[$i]['my_score'] < 1){
//                           $rankingAuto = false;
//                       }else
                        if($totalRanking[$j]['surplus_time'] > $totalRanking[$i]['surplus_time']){

                            $a = $totalRanking[$j];
                            $totalRanking[$j] = $totalRanking[$i];
                            $totalRanking[$i] = $a;
                        }
                    }elseif ($totalRanking[$j]['my_score'] > $totalRanking[$i]['my_score']){
                        $a = $totalRanking[$j];
                        $totalRanking[$j] = $totalRanking[$i];
                        $totalRanking[$i] = $a;
                    }
                }
            }
        }
        //名次
        $ranking = 1;
        foreach ($totalRanking as $k => $v){
            $totalRanking[$k]['ranking'] = $ranking;
            if(!(isset($totalRanking[$k+1]) && $totalRanking[$k+1]['my_score'] == $totalRanking[$k]['my_score'] && $totalRanking[$k+1]['surplus_time'] == $totalRanking[$k]['surplus_time'])){
                ++$ranking;
            }
        }
        return $totalRanking;
    }

    /*
    * =========================================================================================
    */

    /**
     * 新增报名学员
     */
    public function addStudent(){
        $match_id = intval($_GET['match_id']);
        $post = get_post($match_id);
        $page = ($page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1) < 1 ? 1 : $page;
        $pageSize = 10;
        $start = ($page - 1)*$pageSize;
        $searchCode = '';
        $rows = [];

        if(is_post() || isset($_GET['searchCode'])){
            $searchCode = isset($_GET['searchCode']) ? $_GET['searchCode'] : $_POST['searchCode'];
            if($searchCode != ''){
                global $wpdb;
                $rows = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS o.id AS oid,u.ID AS uid,u.user_login,u.user_mobile,u.user_email,um.meta_value AS user_ID,um3.meta_value AS user_real_name FROM '.$wpdb->users.' AS u 
                LEFT JOIN '.$wpdb->usermeta.' AS um ON u.ID=um.user_id AND um.meta_key="user_ID" 
                LEFT JOIN '.$wpdb->usermeta.' AS um3 ON u.ID=um3.user_id AND um3.meta_key="user_real_name" 
                LEFT JOIN '.$wpdb->prefix.'order AS o ON u.ID=o.user_id AND o.match_id='.$match_id.' AND o.pay_status IN(2,3,4) 
                WHERE o.id is NULL 
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