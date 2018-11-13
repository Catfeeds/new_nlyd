<?php
class Grading
{
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_order_menu_page') );
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
    }

    public function register_order_menu_page(){

        if ( current_user_can( 'administrator' ) && !current_user_can( 'edit.php?post_type=grading' ) ) {
            global $wp_roles;

            $role = 'grading_students';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'add_grading_students';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'add_grading_studentScore';//权限名
            $wp_roles->add_cap('administrator', $role);
        }

        add_submenu_page('edit.php?post_type=grading','考级选手','考级选手','grading_students','grading-students',array($this,'gradingStudents'));
        add_submenu_page('edit.php?post_type=grading','添加选手','添加选手','add_grading_students','add-grading-students',array($this,'addGradingStudents'));
        add_submenu_page('edit.php?post_type=grading','答题记录','答题记录','add_grading_studentScore','add-grading-studentScore',array($this,'gradingStudentScore'));
    }

    /**
     * 考级选手页面
     */
    public function gradingStudents(){
        $gradingId = isset($_GET['grading_id']) ? intval($_GET['grading_id']) : 0;
        $gradingId < 1 && exit('参数错误');
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $searchJoin = '';
        $searchWhere = '';
        global $wpdb;
        if($searchStr!=''){
            $searchJoin = "LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=o.user_id AND um.meta_key='user_real_name' 
                           LEFT JOIN {$wpdb->usermeta} AS um2 on um2.user_id=o.user_id AND um2.meta_key='user_ID'";
            $searchWhere = "AND (um.meta_value LIKE '%{$searchStr}%' OR u.user_mobile LIKE '%{$searchStr}%' OR um2.meta_value LIKE '%{$searchStr}%')";
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS u.user_login,u.user_mobile,u.user_email,o.user_id,o.created_time FROM `{$wpdb->prefix}order` AS o 
        LEFT JOIN `{$wpdb->users}` AS u ON u.ID=o.user_id AND u.ID!='' 
        {$searchJoin} 
        WHERE o.match_id='{$gradingId}' AND o.order_type=2 AND o.pay_status IN(2,3,4) 
        {$searchWhere} 
        LIMIT {$start},{$pageSize}";
        $rows  = $wpdb->get_results($sql, ARRAY_A);
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
            <h1 class="wp-heading-inline">考级选手</h1>

            <a href="<?=admin_url('edit.php?post_type=grading&page=add-grading-students&grading_id='.$gradingId)?>" class="page-title-action">添加选手</a>

            <hr class="wp-header-end">

                <p class="search-box">
                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                    <input type="search" id="search_val" name="s" placeholder="姓名/手机/ID" value="<?=$searchStr?>">
                    <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('edit.php?post_type=grading&page=grading-students&grading_id='.$gradingId.'&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
                </p>

                <div class="tablenav top">



                    <div class="tablenav-pages">
                        <span class="displaying-num"><?=$count['count']?>个项目</span>
                        <?=$pageHtml?>
                    </div>
                    <br class="clear">
                </div>
                <h2 class="screen-reader-text">考级选手</h2>
            <table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <th scope="col" id="real_name" class="manage-column column-real_name column-primary sortable">
                            <a href="javascript:;">
                                <span>姓名</span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" id="ID" class="manage-column column-ID">ID</th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                        <th scope="col" id="email" class="manage-column column-email">邮箱</th>
                        <th scope="col" id="is_adopt" class="manage-column column-is_adopt">是否通过</th>
                        <th scope="col" id="adopt_level" class="manage-column column-adopt_level">通过级别</th>
                        <th scope="col" id="created_time" class="manage-column column-created_time">报名时间</th>
                    </tr>
                     </thead>

                    <tbody id="the-list" data-wp-lists="list:user">

                    <?php foreach ($rows as $row){
                        $usermeta = get_user_meta($row['user_id']);
                        $use_real_name = isset($usermeta['user_real_name']) ? unserialize($usermeta['user_real_name'][0]) : [];
                        ?>
                    <tr id="user-<?=$row['user_id']?>"><td class="real_name column-real_name has-row-actions column-primary" data-colname="姓名">
                            <img alt="" src="<?=isset($usermeta['user_head']) ? $usermeta['user_head'][0] : ''?>" class="avatar avatar-32 photo" height="32" width="32">
                            <strong><?=isset($use_real_name['real_name']) ? $use_real_name['real_name'] : ''?></strong>
                            <br>
                            <div class="row-actions">
                                <span class="edit"><a href="<?=admin_url('edit.php?post_type=grading&page=add-grading-studentScore&grading_id='.$gradingId.'&user_id='.$row['user_id'])?>">答题记录</a>  </span>
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="ID column-ID" data-colname="ID"><?=isset($usermeta['user_ID']) ? $usermeta['user_ID'][0] : ''?></td>
                        <td class="mobile column-mobile" data-colname="手机"><?=$row['user_mobile']?></td>
                        <td class="email column-email" data-colname="邮箱"><?=$row['user_email']?></td>
                        <td class="is_adopt column-is_adopt" data-colname="是否通过"><?='是否通过'?></td>
                        <td class="adopt_level column-adopt_level" data-colname="通过级别"><?='2'?></td>
                        <td class="created_time column-created_time" data-colname="报名时间"><?=$row['created_time']?></td>
                    </tr>
                    <?php } ?>
                    <tfoot>
                    <tr>
                        <th scope="col" class="manage-column column-real_name column-primary sortable">
                            <a href="javascript:;">
                                <span>姓名</span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" class="manage-column column-ID">ID</th>
                        <th scope="col" class="manage-column column-mobile">手机</th>
                        <th scope="col" class="manage-column column-email">邮箱</th>
                        <th scope="col" class="manage-column column-is_adopt">是否通过</th>
                        <th scope="col" class="manage-column column-adopt_level">通过级别</th>
                        <th scope="col" class="manage-column column-created_time">报名时间</th>
                    </tr>
                     </tfoot>

                </table>
                <div class="tablenav bottom">


                    <div class="tablenav-pages">
                        <span class="displaying-num"><?=$count['count']?>个项目</span>
                        <?=$pageHtml?>
                    </div>
                    <br class="clear">
                </div>


            <br class="clear">
        </div>
        <?php
    }

    /**
     * 添加考级选手
     */
    public function addGradingStudents(){
        $gradingId = isset($_GET['grading_id']) ? intval($_GET['grading_id']) : 0;
        $gradingId < 1 && exit('参数错误');


        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';

        if($searchStr != ''){
            $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
            $page < 1 && $page = 1;
            $pageSize = 20;
            $start = ($page-1)*$pageSize;
            global $wpdb;
            $sql = "SELECT SQL_CALC_FOUND_ROWS u.user_mobile,u.ID AS user_id,um.meta_value AS user_real_name,um2.meta_value AS userID FROM {$wpdb->users} AS u  
            LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=u.ID AND um.meta_key='user_real_name' 
            LEFT JOIN {$wpdb->usermeta} AS um2 ON um2.user_id=u.ID AND um2.meta_key='user_ID' 
            LEFT JOIN {$wpdb->prefix}order AS o ON o.user_id=u.ID AND o.match_id='{$gradingId}' AND o.order_type=2 AND o.pay_status IN(2,3,4)  
            WHERE o.id IS NULL AND (um.meta_value LIKE '%{$searchStr}%' OR um2.meta_value LIKE '%{$searchStr}%' OR u.user_mobile LIKE '%{$searchStr}%') 
            LIMIT {$start},{$pageSize}";
            $rows = $wpdb->get_results($sql, ARRAY_A);
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
        }else{
            $rows = '';
            $pageHtml = '';
        }
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">添加选手</h1>


            <hr class="wp-header-end">

            <p class="search-box">
                <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                <input type="search" id="search_val" name="s" placeholder="姓名/手机/ID" value="<?=$searchStr?>">
                <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('edit.php?post_type=grading&page=add-grading-students&grading_id='.$gradingId.'&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
            </p>

            <div class="tablenav top">



                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">添加选手</h2>
            <table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <th scope="col" id="real_name" class="manage-column column-real_name column-primary sortable">
                        <a href="javascript:;">
                            <span>姓名</span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" id="ID" class="manage-column column-ID">ID</th>
                    <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                    <th scope="col" id="joinGrading" class="manage-column column-joinGrading">加入考级</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">
                <?php if($rows == ''){ ?>
                    <tr>
                        <td COLSPAN="4" style="text-align: left;font-weight: bold">无数据........</td>
                    </tr>
                <?php }else{ ?>
                    <?php foreach ($rows as $row){
                        $usermeta = get_user_meta($row['user_id']);
                        $use_real_name = isset($usermeta['user_real_name']) ? unserialize($usermeta['user_real_name'][0]) : [];
                        ?>
                        <tr id="user-<?=$row['user_id']?>" data-id="<?=$row['user_id']?>">
                            <td class="real_name column-real_name has-row-actions column-primary" data-colname="姓名">
                                <img alt="" src="<?=isset($usermeta['user_head']) ? $usermeta['user_head'][0] : ''?>" class="avatar avatar-32 photo" height="32" width="32">
                                <strong><?=isset($use_real_name['real_name']) ? $use_real_name['real_name'] : ''?></strong>
                                <br>
                                <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                            </td>
                            <td class="ID column-ID" data-colname="ID"><?=isset($usermeta['user_ID']) ? $usermeta['user_ID'][0] : ''?></td>
                            <td class="mobile column-mobile" data-colname="手机"><?=$row['user_mobile']?></td>
                            <td class="joinGrading column-joinGrading" data-colname="加入考级"><a href="javascript:;" class="joinGradingMember" style="color: #02892e">加入考级</a></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                <tfoot>
                <tr>
                    <th scope="col" class="manage-column column-real_name column-primary sortable">
                        <a href="javascript:;">
                            <span>姓名</span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" class="manage-column column-ID">ID</th>
                    <th scope="col" class="manage-column column-mobile">手机</th>
                    <th scope="col" class="manage-column column-joinGrading">加入考级</th>
                </tr>
                </tfoot>
            </table>
            <div class="tablenav bottom">


                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
            <script>
                jQuery(document).ready(function($) {
                    $('#the-list').find('.joinGradingMember').on('click', function () {
                        var grading_id = '<?=$gradingId?>';
                        var user_id = $(this).closest('tr').attr('data-id');
                        if(user_id < 1 || grading_id < 1 || user_id == undefined || user_id == null) return false;
                        $.ajax({
                            url : ajaxurl,
                            data : {'action':'joinGradingMember','grading_id':grading_id,'user_id':user_id},
                            dataType : 'json',
                            type : 'post',
                            success : function (response) {
                                alert(response.data.info);
                                if(response['success'] == true){
                                    window.location.reload();
                                }
                            },error : function () {
                                alert('请求失败!');
                            }
                        });
                    });
                })
            </script>

            <br class="clear">
        </div>
        <?php
    }

    /**
     * 考级答题记录
     */
    public function gradingStudentScore(){

        $gradingId = intval($_GET['grading_id']);
        $user_id = intval($_GET['user_id']);
        global $wpdb;

        $grading = get_post($gradingId);
//        $user = get_user_by('ID',$user_id);
        $usermeta = get_user_meta($user_id, '', true);

        //当前考级外键
        $gradingMeta = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}grading_meta WHERE grading_id='{$gradingId}'", ARRAY_A);
        if(!$gradingMeta) exit('未查询到考级信息!');

        $proId = $gradingMeta['category_id'];

        //获取当期项目总轮数
        $gradingQuestion = $wpdb->get_row('SELECT * AS match_more FROM '.$wpdb->prefix.'grading_questions WHERE grading_id='.$gradingId.' AND user_id='.$user_id, ARRAY_A);

        //项目
        $sql = "SELECT p.post_title,pm.meta_value as project_alias,p.ID AS match_project_id FROM {$wpdb->posts} AS p 
            LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID=pm.post_id AND pm.meta_key='project_alias' 
            WHERE p.ID='{$gradingMeta['category_id']}'";
        $category = $wpdb->get_row($sql, ARRAY_A);

        $sql2 = "SELECT p.post_title,pm.meta_value as project_alias,p.ID AS match_project_id FROM {$wpdb->posts} AS p 
            LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID=pm.post_id AND pm.meta_key='project_alias' 
            WHERE p.post_parent='{$category['match_project_id']}'";
        $project = $wpdb->get_results($sql2, ARRAY_A);

        $projectArr = [];
        foreach ($project as $pv){
            $projectArr[] = $pv['project_alias'];
        }
        //生成答题记录数组
        $match_questions = json_decode($gradingQuestion['match_questions'],true);
        $questions_answer = json_decode($gradingQuestion['questions_answer'],true);
        $my_answer = !empty($gradingQuestion['my_answer']) ? json_decode($gradingQuestion['my_answer'],true) : array();
        $categoryName = $category['post_title'];
        if($gradingQuestion['grading_type'] == 'arithmetic'){
            //心算
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
        elseif ($gradingQuestion['grading_type'] == 'reading'){
            //速读
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
        elseif ($gradingQuestion['grading_type'] == 'memory'){
            //记忆
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
//            'project_alias'=>$currentProject['project_alias'],
            'str_len'=>$len,
            'match_more_cn'=>chinanum($_GET['match_more']),
            'success_length'=>$success_len,
//            'use_time'=>$currentProject['child_count_down']-$row['surplus_time'],
//            'surplus_time'=>$row['surplus_time'],
            'accuracy'=>$success_len > 0 ? round($success_len/$len,2)*100 : 0,
            'ranking'=>$ranking,
            'match_questions'=>$match_questions,
            'questions_answer'=>$questions_answer,
            'my_answer'=>$my_answer,
            'answer_array'=>$answer_array,
            'my_score'=>$gradingQuestion['my_score'],
//            'project_title'=>$currentProject['post_title'],
            'match_title'=>$grading->post_title,
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
            <h1 class="wp-heading-inline"><?=$grading->post_title?>-<?=$categoryName?>-<?=unserialize($usermeta['user_real_name'][0])['real_name']?>-答题记录</h1>

            <!--            <a href="http://127.0.0.1/nlyd/wp-admin/user-new.php" class="page-title-action">添加用户</a>-->

            <hr class="wp-header-end">

            <h2 class="screen-reader-text"></h2>
            <form method="get">

                <p class="search-box">

                </p>

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="5ce30f05fd"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
                <div class="top">

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


                    <br class="clear">

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
                        <th scope="col" id="match_questions" class="manage-column column-match_questions column-primary">比赛考题</th>
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
                                <td class="role column-match_questions column-primary" data-colname="比赛考题">
                                    <?=$match_question?>
                                    <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                                </td>

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
                        <th scope="col" class="manage-column column-match_questions column-primary">比赛考题</th>
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
     * 引入当前页面css/js
     */
    public function register_scripts(){
        if(!isset($_GET['page'])){
            wp_register_script('list-js', match_js_url . 'grading.js');
            wp_enqueue_script('list-js');
        }else{

        }

    }
}

new Grading();