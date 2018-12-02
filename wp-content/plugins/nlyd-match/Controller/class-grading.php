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

            $role = 'grading_studentScore';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'grading_trainLog';//权限名
            $wp_roles->add_cap('administrator', $role);
        }

        add_submenu_page('edit.php?post_type=grading','考级选手','考级选手','grading_students','grading-students',array($this,'gradingStudents'));
        add_submenu_page('edit.php?post_type=grading','添加选手','添加选手','add_grading_students','add-grading-students',array($this,'addGradingStudents'));
        add_submenu_page('edit.php?post_type=grading','答题记录','答题记录','grading_studentScore','grading-studentScore',array($this,'gradingStudentScore'));
        add_submenu_page('edit.php?post_type=grading','训练记录','训练记录','grading_trainLog','grading-trainLog',array($this,'gradingTrainLog'));
    }

    /**
     * 考级选手页面
     */
    public function gradingStudents(){
        $gradingId = isset($_GET['grading_id']) ? intval($_GET['grading_id']) : 0;
        $gradingId < 1 && exit('参数错误');
        global $wpdb;
        //查询考级类别


        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $searchJoin = '';
        $searchWhere = '';
        if($searchStr!=''){
            $searchJoin = "LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=o.user_id AND um.meta_key='user_real_name' 
                           LEFT JOIN {$wpdb->usermeta} AS um2 on um2.user_id=o.user_id AND um2.meta_key='user_ID'";
            $searchWhere = "AND (um.meta_value LIKE '%{$searchStr}%' OR u.user_mobile LIKE '%{$searchStr}%' OR um2.meta_value LIKE '%{$searchStr}%')";
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS u.user_login,u.user_mobile,u.user_email,o.user_id,o.created_time,gl.grading_result,o.memory_lv,gl.grading_lv,
        CASE gl.grading_result 
        WHEN 1 THEN '<span style=\"color: #20a831\">通过</span>' 
        WHEN 2 THEN '<span style=\"color: #7f0000\">失败</span>' 
        ELSE '-' END AS grading_result_name 
        FROM `{$wpdb->prefix}order` AS o 
        LEFT JOIN `{$wpdb->prefix}grading_logs` AS gl ON gl.user_id=o.user_id AND gl.grading_id=o.match_id 
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
                                <span class=""><a href="<?=admin_url('user-edit.php?user_id='.$row['user_id'])?>" aria-label="">编辑用户</a></span>
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="ID column-ID" data-colname="ID"><?=isset($usermeta['user_ID']) ? $usermeta['user_ID'][0] : ''?></td>
                        <td class="mobile column-mobile" data-colname="手机"><?=$row['user_mobile']?></td>
                        <td class="email column-email" data-colname="邮箱"><?=$row['user_email']?></td>
                        <td class="is_adopt column-is_adopt" data-colname="是否通过"><?=$row['grading_result_name']?></td>
                        <td class="adopt_level column-adopt_level" data-colname="通过级别">
                            <?=$row['grading_result'] == '1' ? $row['grading_lv'] : '未过级'?>
                        </td>
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
            global $wpdb;
            //获取考级类型是否是记忆类
            $cate = $wpdb->get_var("SELECT p.post_title FROM `{$wpdb->prefix}grading_meta` AS gm 
            LEFT JOIN `{$wpdb->posts}` AS p ON p.ID=gm.category_id 
            WHERE gm.grading_id='{$gradingId}'");
            $is_memory = false;
            if(preg_match('/记忆/',$cate) || preg_match('/速记/',$cate)){
                $is_memory = true;
            }


            $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
            $page < 1 && $page = 1;
            $pageSize = 20;
            $start = ($page-1)*$pageSize;
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
                            <td class="joinGrading column-joinGrading" data-colname="加入考级">
                                <?php if($is_memory){ ?>
                                    <input type="text" placeholder="请输入记忆等级">
                                <?php } ?>
                                <a href="javascript:;" class="joinGradingMember" style="color: #02892e">加入考级</a>
                            </td>
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
                        var _data = Object();
                        _data.action = 'joinGradingMember';
                        _data.grading_id = grading_id;
                        _data.user_id = user_id;
                        <?php if($is_memory){ ?>
                        var lv = $(this).prev().val();
                        _data.lv = lv;
                        <?php } ?>
                        if(user_id < 1 || grading_id < 1 || user_id == undefined || user_id == null) return false;
                        $.ajax({
                            url : ajaxurl,
                            data : _data,
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
        $usermeta = get_user_meta($user_id, '', true);

        //当前考级外键
        $gradingMeta = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}grading_meta WHERE grading_id='{$gradingId}'", ARRAY_A);
        if(!$gradingMeta) exit('未查询到考级信息!');

        //项目
        $sql = "SELECT p.post_title,pm.meta_value as project_alias,p.ID AS match_project_id FROM {$wpdb->posts} AS p 
            LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID=pm.post_id AND pm.meta_key='project_alias' 
            WHERE p.ID='{$gradingMeta['category_id']}'";
        $category = $wpdb->get_row($sql, ARRAY_A);

        //获取记录类型
        $gradingArr = $wpdb->get_results('SELECT questions_type FROM '.$wpdb->prefix.'grading_questions WHERE grading_id='.$gradingId.' AND user_id='.$user_id, ARRAY_A);
        if(!$gradingArr) exit('无答题记录');


        $gradingArr = array_reduce($gradingArr, function ($result, $value) {
            return array_merge($result, array_values($value));
        }, array());
        $gradingArr = array_unique($gradingArr);

        $g_type = isset($_GET['g_type']) ? trim($_GET['g_type']) : $gradingArr[0];
        $more = isset($_GET['more']) ? intval($_GET['more']) : 0;

        //获取答案
        $gradingQuestions = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'grading_questions WHERE grading_id='.$gradingId.' AND user_id='.$user_id.' AND questions_type="'.$g_type.'"', ARRAY_A);
//        leo_dump();
//        die;
//        leo_dump($gradingQuestion);
//        die;
        $gradingQuestion = $gradingQuestions[$more];
        $moreArr = [];
        if(count($gradingQuestions) > 1){
            $moreArr = $gradingQuestions;
        }
        $grading_questions = json_decode($gradingQuestion['grading_questions'],true);
        $questions_answer = json_decode($gradingQuestion['questions_answer'],true);
        $my_answer = json_decode($gradingQuestion['my_answer'],true);
        //项目处理
        switch ($g_type){
            case 'rm':
                foreach ($grading_questions as &$gqv){
                    $gqv = join('<br>',$gqv);
                }
                foreach ($questions_answer as &$qav){
                    $qav = join('<br>',$qav);
                }
                foreach ($my_answer as &$mav){
                    $mav = join('<br>',$mav);
                }
                break;
            case 'wz':
                foreach ($grading_questions as &$gqv){
                    $gqv = join('',$gqv);
                }
                foreach ($questions_answer as $qak => &$qav){
                    foreach ($qav as $qak2 => $qav2){
                        if($qav2 == $my_answer[$qak][$qak2]){
                            $my_answer[$qak][$qak2] = '<span class="correct-color">'.$my_answer[$qak][$qak2].'</span>';
                        }else{
                            $my_answer[$qak][$qak2] = $my_answer[$qak][$qak2] == '' ? '&ensp;-&ensp;': $my_answer[$qak][$qak2];
                            $my_answer[$qak][$qak2] = '<span class="error-color">'.$my_answer[$qak][$qak2].'</span>';
                        }

                    }
                    $qav = join('',$qav);
                }

                foreach ($my_answer as &$mav){
                    $mav = join('',$mav);
                }

                break;
            case 'reading'://速读
                foreach ($questions_answer as $qak => &$qav){
                    foreach ($qav['problem_select'] as $qaPeK => &$qaPeV){
                        if($qav['problem_answer'][$qaPeK] == 1){
                            $qaPeV = '<span class="correct-color">'.$qaPeV.'</span>';
                        }
                    }
                    foreach ($my_answer[$qak] as $mak => &$mav){
                        if($qav['problem_answer'][$mav] == 1){
                            $mav = '<span class="correct-color">'.$qav['problem_select'][$mav].'</span>';
                        }else{
                            $mav = $qav['problem_select'][$mav];
                        }
                    }
                    $questions_answer[$qak] = join('<br>',$qav['problem_select']);
                    $my_answer[$qak] = join('<br>',$my_answer[$qak]);
                }
                break;
            case 'nxys':

//                leo_dump($grading_questions);
//                leo_dump($questions_answer);
//                leo_dump($my_answer);
                foreach ($grading_questions as &$gqv){
                    $gqv = join(',',$gqv);
                }
                $arr = [];
                foreach ($questions_answer['examples'] as $qak => $qav){
                    $arr[$qak] = $qav;
                    if($questions_answer['examples']['result'] == true){
                        $my_answer[$qak] = '<span class="correct-color">'.$my_answer[$qak].'</span>';
                    }
                }
                $questions_answer = $arr;
                break;
        }


        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?=$grading->post_title?>-<?=$category['post_title']?>-<?=unserialize($usermeta['user_real_name'][0])['real_name']?>-答题记录</h1>

            <hr class="wp-header-end">

            <h2 class="screen-reader-text"></h2>
        <style type="text/css">

            .error-color {
                color: #a80000;
            }
            .correct-color {
                color: #02892e;
            }
        </style>
            <ul class="subsubsub">
                <?php

                $counts = count($gradingArr);
                foreach ($gradingArr as $k => $gav){

                       $p_name = $this->getProject($gav);
                       ?>
                    <li class="<?=$gav?>">
                        <a href="<?=admin_url('edit.php?post_type=grading&page=add-grading-studentScore&grading_id='.$gradingId.'&user_id='.$user_id.'&g_type='.$gav)?>" <?=$g_type==$gav?'class="current"':''?> aria-current="page"><?=$p_name?>
                            <span class="count"></span>
                        </a>
                        <?=($k+1)<$counts?'|':''?>
                    </li>

                    <?php

                } ?>

<!--                <li class="editor"><a href="users.php?role=editor">人脉<span class="count">（5）</span></a></li>-->
            </ul>
        <br class="clear">
            <ul class="subsubsub">
                <?php

                foreach ($moreArr as $mak => $mav){


                       ?>
                    <li class="<?=$mak?>">
                        <a href="<?=admin_url('edit.php?post_type=grading&page=add-grading-studentScore&grading_id='.$gradingId.'&user_id='.$user_id.'&g_type='.$g_type.'&more='.$mak)?>" <?=$more==$mak?'class="current"':''?> aria-current="page">
                            第<?=$mav['post_more']?>轮
                            <span class="count"></span>
                        </a>
                    </li>

                    <?php

                } ?>

<!--                <li class="editor"><a href="users.php?role=editor">人脉<span class="count">（5）</span></a></li>-->
            </ul>
                <p class="search-box">

                </p>

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="5ce30f05fd"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
                <div class="top">


                    <br class="clear">

                </div>
                <style type="text/css">
                    .intro-box{
                        padding-top: 0.5em;
                    }
                    .intro-box .intro-key{
                        font-weight: bold;
                    }
                </style>
                <?php if($gradingQuestion['correct_rate']){ ?>
                    <div class="intro-box">
                        <span class="intro-key">准确率: </span>
                        <span class="intro-value"><?=$gradingQuestion['correct_rate']*100?>%</span>
                    </div>
                <?php } ?>
                <?php if($gradingQuestion['memory_time']){ ?>
                    <div class="intro-box">
                        <span class="intro-key">记忆耗时: </span>
                        <span class="intro-value"><?=$gradingQuestion['memory_time']?></span>
                    </div>
                <?php } ?>
                <?php if($gradingQuestion['answer_time']){ ?>
                    <div class="intro-box">
                        <span class="intro-key">回答耗时: </span>
                        <span class="intro-value"><?=$gradingQuestion['answer_time']?></span>
                    </div>
                <?php } ?>
                <?php if($gradingQuestion['submit_type']){ ?>
                    <div class="intro-box">
                        <span class="intro-key">提交方式: </span>
                        <span class="intro-value">
                            <?php

                                switch ($gradingQuestion['submit_type']){
                                    case 1:
                                        echo '选手提交';
                                        break;
                                    case 2:
                                        echo '错误达上限提交';
                                        break;
                                    case 3:
                                        echo '时间到达提交';
                                        break;
                                    case 4:
                                        echo '来回切换,系统提交';
                                        break;
                                }
                            ?>
                        </span>
                    </div>
                <?php } ?>
                <?php if($gradingQuestion['leave_page_time']){ ?>
                    <div class="intro-box">
                        <span class="intro-key">每次离开页面的时间: </span>
                        <span class="intro-value"><?=$gradingQuestion['leave_page_time']?></span>
                    </div>
                <?php } ?>
                <?php if($gradingQuestion['created_time']){ ?>
                    <div class="intro-box">
                        <span class="intro-key">提交时间: </span>
                        <span class="intro-value"><?=$gradingQuestion['created_time']?></span>
                    </div>
                <?php } ?>
                <?php if($gradingQuestion['post_id'] > 0){ ?>
                    <div class="intro-box">
                        <span class="intro-key">文章: </span>
                        <span class="intro-value"><?=isset(get_posts(['ID'=>$gradingQuestion['post_id']])[0]) ? get_posts(['ID'=>$gradingQuestion['post_id']])[0]->post_title : ''?></span>
                    </div>
                <?php } ?>

                    <div class="intro-box">
                        <span class="intro-key">成绩性质: </span>
                        <span class="intro-value <?=$gradingQuestion['is_true'] == '1' ? 'correct-color' : 'error-color'?>">
                            <?=$gradingQuestion['is_true'] == '1' ? '真实' : '虚假'?>
                        </span>
                    </div>


                <h2 class="screen-reader-text">答题记录</h2>
                <br class="clear">
<!--                <div><span>剩余时间:</span> <span> --><?//=$data['surplus_time']?><!--</span></div>-->
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
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">
                    <!--                        </tr>-->
                    <?php

                        foreach ($grading_questions as $k => $grading_questions_v){

                            ?>
                            <tr>

                                <th scope="row" class="check-column">
                                </th>
                                <td class="match_questions column-match_questions column-primary" data-colname="比赛考题">
                                    <?=$grading_questions_v?>
                                    <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                                </td>

                                <td class="questions_answer column-questions_answer" data-colname="考题答案">
                                    <?=$questions_answer[$k]?>
                                </td>
                                <td class="my_answer column-my_answer" data-colname="我的答案">
                                    <span class="<?=$questions_answer[$k]==$my_answer[$k]?'correct-color':'error-color'?>">
                                        <?=$my_answer[$k] || $my_answer[$k]=='0'?$my_answer[$k]:'-'?>
                                    </span>
                                </td>
                            </tr>
                            <?php

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
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">

                    <div class="alignleft actions bulkactions">
                    </div>
                    <div class="alignleft actions">


                        <br class="clear">
                    </div>

            <br class="clear">
        </div>
        <?php
    }
    /**
     * 考级训练记录
     */
    public function gradingTrainLog(){
        global $wpdb;
        $categoryArr = getCategory();
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $searchWhere = '';
        $searchJOIN = '';
        if($searchStr != ''){
            $searchJOIN = "LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=utl.user_id AND um.meta_key='user_real_name' 
                           LEFT JOIN {$wpdb->usermeta} AS um2 ON um2.user_id=utl.user_id AND um2.meta_key='user_ID ' ";
            $searchWhere = "WHERE um.meta_value LIKE '%{$searchStr}%' OR um2.meta_value LIKE '%{$searchStr}%' OR u.user_mobile LIKE '%{$searchStr}%' ";
        }
        if($categoryArr == []) exit('未找到类别!');
        $categoryType = isset($_GET['ctype']) ? intval($_GET['ctype']) : $categoryArr[0]['ID'];
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results("SELECT utl.grading_num,utl.questions_type,utl.correct_rate,utl.my_score,utl.use_time,utl.created_time,u.user_mobile,p.post_title AS category_name FROM `{$wpdb->prefix}user_grade_logs` AS utl 
                LEFT JOIN `{$wpdb->users}` AS u ON u.ID=utl.user_id AND u.ID!='' 
                LEFT JOIN `{$wpdb->posts}` AS p ON p.ID=utl.genre_id 
                {$searchJOIN} 
                {$searchWhere}
                ORDER BY utl.created_time DESC LIMIT {$start},{$pageSize}" ,ARRAY_A);
        leo_dump($wpdb->last_query);
        leo_dump($rows);
        die;
        $cateOptions = [];
        foreach ($categoryArr as $cgV){
            $categoryCurrent = $categoryType == $cgV['ID'] ? 'class="current"' : '';
            $cateOptions[] = '<li class="all"><a href="'.admin_url('edit.php?post_type=grading&page=add-grading-trainLog&ctype='.$cgV['ID']).'" '.$categoryCurrent.' aria-current="page">'.$cgV['post_title'].'</a> </li>';
        }

        ?>
        <div class="wrap">
        <h1 class="wp-heading-inline">训练记录</h1>
        <hr class="wp-header-end">
        <h2 class="screen-reader-text">过滤训练记录</h2>
        <ul class="subsubsub">
            <?=join(' | ',$cateOptions)?>
        </ul>
        <p class="search-box">
            <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
            <input type="text" id="searchs" name="s" placeholder="姓名/ID/手机" value="">
            <input type="button" id="search-button" onclick="window.location.href='<?=admin_url('edit.php?post_type=grading&page=add-grading-trainLog&ctype='.$categoryCurrent.'&s=')?>'+document.getElementById('searchs').value" class="button" value="搜索用户">
        </p>
        <input type="hidden" id="_wpnonce" name="_wpnonce" value="cad3ad3c1f"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                <select name="action" id="bulk-action-selector-top">
                    <option value="-1">批量操作</option>
                    <option value="delete">删除</option>
                </select>
                <input type="submit" id="doaction" class="button action" value="应用">
            </div>
            <div class="tablenav-pages one-page">

            </div>
            <br class="clear">
        </div>
        <h2 class="screen-reader-text">训练记录</h2>
        <table class="wp-list-table widefat fixed striped users">
            <thead>
            <tr>
                <th scope="col" id="real_name" class="manage-column column-real_name column-primary">姓名</th>
                <th scope="col" id="ID" class="manage-column column-ID">ID</th>
                <th scope="col" id="sex" class="manage-column column-sex">性别</th>
                <th scope="col" id="age" class="manage-column column-age">年龄</th>
                <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                <th scope="col" id="category" class="manage-column column-category">训练类别</th>
                <th scope="col" id="status" class="manage-column column-status">训练状态</th>
                <th scope="col" id="level" class="manage-column column-level">训练等级</th>
            </tr>
            </thead>

                <tbody id="the-list" data-wp-lists="list:user">
                    <?php
                    foreach ($rows as $row){
                        $usermeta = get_user_meta($row['user_id']);
                        $user_real_name = isset($usermeta['user_real_name']) ? unserialize($usermeta['user_real_name'][0]) : [];
                     ?>
                    <tr data-id="">
                        <td class="username column-username has-row-actions column-primary" data-colname="用户名">
                            <img alt="" src="http://0.gravatar.com/avatar/?s=32&amp;d=mm&amp;r=g" srcset="http://1.gravatar.com/avatar/?s=64&amp;d=mm&amp;r=g 2x" class="avatar avatar-32 photo avatar-default" height="32" width="32">
                            <strong><a href="http://127.0.0.1/nlyd/wp-admin/user-edit.php?user_id=5&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">13982242710</a></strong>
                            <br>
                            <div class="row-actions">
                                <span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/user-edit.php?user_id=5&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">编辑</a> | </span>
                                <span class="delete"><a class="submitdelete" href="users.php?action=delete&amp;user=5&amp;_wpnonce=cad3ad3c1f">删除</a> | </span>
                                <span class="view"><a href="http://127.0.0.1/nlyd/wp-admin/users.php?page=users-info&amp;ID=5">资料</a></span>
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="real_name column-real_name" data-colname="姓名"></td>
                    </tr>
                     <?php
                     }
                     ?>
                </tbody>
            <tfoot>
                <tr>
                    <th scope="col" class="manage-column column-real_name column-primary">姓名</th>
                    <th scope="col" class="manage-column column-ID">ID</th>
                    <th scope="col" class="manage-column column-sex">性别</th>
                    <th scope="col" class="manage-column column-age">年龄</th>
                    <th scope="col" class="manage-column column-mobile">手机</th>
                    <th scope="col" class="manage-column column-category">训练类别</th>
                    <th scope="col" class="manage-column column-status">训练状态</th>
                    <th scope="col" class="manage-column column-level">训练等级</th>
                </tr>
            </tfoot>

        </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
                            <option value="delete">删除</option>
                        </select>
                    <input type="submit" id="doaction2" class="button action" value="应用">
                </div>

            <div class="tablenav-pages one-page">

            </div>
                <br class="clear">
            </div>
        <br class="clear">
        </div>
        <?php
    }

    /**
     * 项目类型处理
     */
    public function getProject($types){
        switch ($types){
            case 'sz':
                $name = '数字';
                break;
            case 'cy':
                $name = '词语';
                break;
            case 'zm':
                $name = '字母';
                break;
            case 'tl':
                $name = '听力';
                break;
            case 'rm':
                $name = '人脉';
                break;
            case 'wz':
                $name = '国学经典';
                break;
            case 'zxys':
                $name = '正向运算';
                break;
            case 'nxys':
                $name = '逆向运算';
                break;
            case 'reading':
                $name = '速读';
                break;
            default:
                $name = $types;
        }
        return $name;
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