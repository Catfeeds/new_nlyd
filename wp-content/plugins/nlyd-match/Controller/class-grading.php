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

        $g_type = isset($_GET['g_type']) ? trim($_GET['g_type']) : $gradingArr[0]['questions_type'];

        //获取答案
        $gradingQuestion = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'grading_questions WHERE grading_id='.$gradingId.' AND user_id='.$user_id.' AND questions_type="'.$g_type.'"', ARRAY_A);





        //项目处理
        $grading_questions = json_decode($gradingQuestion['grading_questions'],true);
        $questions_answer = json_decode($gradingQuestion['questions_answer'],true);
        $my_answer = json_decode($gradingQuestion['my_answer'],true);


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
                       $p_name = $this->getProject($gav['questions_type']);
                       ?>
                    <li class="<?=$gav['questions_type']?>">
                        <a href="<?=admin_url('edit.php?post_type=grading&page=add-grading-studentScore&grading_id='.$gradingId.'&user_id='.$user_id.'&g_type='.$gav['questions_type'])?>" <?=$g_type==$gav['questions_type']?'class="current"':''?> aria-current="page"><?=$p_name?>
                            <span class="count"></span>
                        </a>
                        <?=($k+1)<$counts?'|':''?>
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
                                        <?=$my_answer[$k]?$my_answer[$k]:'-'?>
                                    </span>
                                </td>



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
     * 项目类型处理
     */
    public function getProject($types){
        $name = '';
        switch ($types){
            case 'sz':
                $name = '数字';
                break;
            case 'cy':
                $name = '词语';
                break;
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