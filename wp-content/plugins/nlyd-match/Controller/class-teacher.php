<?php

/**
 * 后台教练
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Teacher
{

    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_teacher_menu_page') );
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));

    }

    public function register_teacher_menu_page(){

        add_menu_page('教练', '教练', 'administrator', 'teacher',array($this,'teacher'),'dashicons-businessman',99);
        add_submenu_page('teacher','新增教练','新增教练','administrator','user-new.php','');
        add_submenu_page('teacher','个人资料','个人资料','administrator','teacher-datum',array($this,'datum'));
        add_submenu_page('teacher','我的学员','我的学员','administrator','teacher-student',array($this,'student'));
        add_submenu_page('teacher','我的课程','我的课程','administrator','teacher-course',array($this,'course'));
    }

    /**
     * 教练列表
     */
    public function teacher(){
        
        $page = ($page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1) < 1 ? 1 : $page;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        global $wpdb;
        $sql = "SELECT SQL_CALC_FOUND_ROWS b.display_name,b.user_login,b.user_email,a.id,a.coach_id,a.read,a.memory,a.compute
                    FROM {$wpdb->prefix}users b  
                    LEFT JOIN {$wpdb->prefix}coach_skill a ON a.coach_id = b.ID 
                    WHERE a.coach_id > 0 
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
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">教练</h1>

<!--            <a href="http://127.0.0.1/nlyd/wp-admin/user-new.php" class="page-title-action">添加用户</a>-->


<!--                <p class="search-box">-->
<!--                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>-->
<!--                    <input type="search" id="user-search-input" name="s" value="">-->
<!--                    <input type="submit" id="search-submit" class="button" value="搜索用户">-->
<!--                </p>-->

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="31db78f456"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php?paged=1">
                <div class="tablenav top">

                    <div class="alignleft actions bulkactions">
<!--                        <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label><select name="action" id="bulk-action-selector-top">-->
<!--                            <option value="-1">批量操作</option>-->
<!--                            <option value="delete">删除</option>-->
<!--                        </select>-->
<!--                        <input type="submit" id="doaction" class="button action" value="应用">-->
                    </div>
                    <div class="tablenav-pages one-page">
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
                        <th scope="col" id="name" class="manage-column column-name">姓名</th>
                        <th scope="col" id="datum" class="manage-column column-name">教练资料</th>
                        <th scope="col" id="student" class="manage-column column-name">查看学员</th>
                        <th scope="col" id="email" class="manage-column column-email sortable desc">
                            <a href="javascript:;"><span>电子邮件</span><span class="sorting-indicator"></span></a>
                        </th>
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">
                    <?php foreach ($rows as $row){ ?>
                        <tr id="user-3">
                            <th scope="row" class="check-column">
                                <label class="screen-reader-text" for="user_3">选择15982345102</label>
                                <input type="checkbox" name="users[]" id="user_3" class="subscriber" value="3">
                            </th>
                            <td class="username column-username has-row-actions column-primary" data-colname="用户名">
                                <strong><a href="javascript:;"><?=$row['user_login']?></a></strong><br>
<!--                                <div class="row-actions">-->
<!--                                    <span class="edit"><a href="--><?php //echo '?page=teacher-datum&id='.$row['id'] ?><!--">教练资料</a> | </span>-->
<!--                                    <span class="view"><a href="--><?php //echo '?page=teacher-student&id='.$row['coach_id'] ?><!--" aria-label="">查看学员</a></span>-->
<!--                                </div>-->
<!--                                <button type="button" class="toggle-row">-->
<!--                                    <span class="screen-reader-text">显示详情</span>-->
<!--                                </button>-->
                            </td>
                            <td class="name column-name" data-colname="姓名">
                                <span aria-hidden="true"><?=str_replace(', ', '', $row['display_name'])?></span>
                                <span class="screen-reader-text">未知</span>
                            </td>
                            <td class="name column-name" data-colname="教练资料">
                                <span aria-hidden="true"><a href="<?php echo '?page=teacher-datum&id='.$row['id'] ?>">教练资料</a></span>
                                <span class="screen-reader-text">-</span>
                            </td>
                            <td class="name column-name" data-colname="查看学员">
                                <span aria-hidden="true"><a href="<?php echo '?page=teacher-student&id='.$row['coach_id'] ?>" aria-label="">查看学员</a></span>
                                <span class="screen-reader-text">-</span>
                            </td>
                            <td class="email column-email" data-colname="电子邮件"><a href="mailto:<?=$row['user_email']?>"><?=$row['user_email']?></a></td>

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
                        <th scope="col" class="manage-column column-datum column-primary">
                            姓名
                        </th>
                        <th scope="col" class="manage-column column-student column-primary">
                            教练资料
                        </th>
                        <th scope="col" class="manage-column column-name">查看学员</th>
                        <th scope="col" class="manage-column column-email sortable desc">
                            <a href="javascript:;"><span>电子邮件</span><span class="sorting-indicator"></span></a>
                        </th>
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">

                    <div class="alignleft actions bulkactions">
<!--                        <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label><select name="action2" id="bulk-action-selector-bottom">-->
<!--                            <option value="-1">批量操作</option>-->
<!--                            <option value="delete">删除</option>-->
<!--                        </select>-->
<!--                        <input type="submit" id="doaction2" class="button action" value="应用">-->
                    </div>
                    <div class="tablenav-pages one-page">
                        <?=$pageHtml?>
                    </div>
                </div>

        </div>







    <?php
//        load_view_template(match_view_path.'teacher.php', array('rows' => $rows));
    }


    /**
     * 教练资料
     */
    public function datum(){
        $err_msg = '';
        $suc_msg = '';

        if(is_post()){
            if(!preg_match('/1[345678][0-9]{9}/', $_POST['mobile'])) $err_msg = '手机格式错误';
            if(!preg_match('/[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}/',$_POST['email'])){
                $err_msg = $err_msg != '' ? $err_msg.', 邮箱格式错误' : '邮箱格式错误';
            }
            global $wpdb;
            if($err_msg == ''){
                //教练资料
                $bool = $wpdb->update($wpdb->users,
                    ['display_name' => $_POST['surname'].', '.$_POST['dis_name'], 'user_mobile' => $_POST['mobile'], 'user_email' => $_POST['email']],
                    ['id' => $_POST['user_id']]);
                //教练技能
                $read = isset($_POST['read']) ? intval($_POST['read']) : 0;
                $memory = isset($_POST['memory']) ? intval($_POST['memory']) : 0;
                $compute = isset($_POST['compute']) ? intval($_POST['compute']) : 0;
                $bool_skill = $wpdb->update($wpdb->prefix.'coach_skill',['read' => $read, 'memory' => $memory, 'compute' => $compute], ['id' => intval($_POST['sk_id'])]);

                if($bool || $bool_skill) $suc_msg = '编辑成功';
                else $err_msg = '编辑失败';
            }

        }
        $id = $_GET['id'];
        global $wpdb;
        $sql = "SELECT SQL_CALC_FOUND_ROWS b.display_name,b.user_mobile,b.user_login,b.user_email,a.id,a.coach_id,a.read,b.id as user_id,a.memory,a.compute
                    FROM {$wpdb->prefix}users b  
                    LEFT JOIN {$wpdb->prefix}coach_skill a ON a.coach_id = b.ID 
                    WHERE a.id={$id}";
        $row = $wpdb->get_row($sql, ARRAY_A);
        $sql = "select ID,post_title from {$wpdb->prefix}posts where post_type = 'match-category' and post_status = 'publish' order by menu_order asc  ";
        $postsRows = $wpdb->get_results($sql,ARRAY_A);


        ?>
        <div id="wpbody" role="main">

            <div id="wpbody-content" aria-label="主内容" tabindex="0">

                <div class="wrap" id="profile-page">
                    <h1 class="wp-heading-inline">教练资料13982242710</h1>



                    <form id="" action="" method="post" novalidate="novalidate">
                        <input type="hidden" id="_wpnonce" name="_wpnonce" value="5fcd054cd3"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-edit.php?user_id=5&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">	<input type="hidden" name="wp_http_referer" value="/nlyd/wp-admin/users.php">
                        <p>
                            <input type="hidden" name="from" value="profile">
                            <input type="hidden" name="checkuser_id" value="1">
                        </p>



                        <table class="form-table">

                        </table>
                        <div id="err-box"><?=$err_msg?></div>
                        <div id="suc-box"><?=$suc_msg?></div>
                        <h2>姓名</h2>

                        <table class="form-table">
                            <tbody><tr class="user-user-login-wrap">
                                <th><label for="user_login">用户名</label></th>
                                <td><input type="text" name="user_login" id="user_login" value="<?=$row['user_login']?>" disabled="disabled" class="regular-text"> <span class="description">用户名不可更改。</span></td>
                            </tr>


                            <tr class="user-first-name-wrap">
                                <th><label for="dis_name">名字</label></th>
                                <td><input type="text" name="dis_name" id="dis_name" value="<?=explode(', ',$row['display_name'])[1]?>" class="regular-text"></td>
                            </tr>

                            <tr class="user-last-name-wrap">
                                <th><label for="surname">姓氏</label></th>
                                <td><input type="text" name="surname" id="surname" value="<?=explode(', ',$row['display_name'])[0]?>" class="regular-text"></td>
                            </tr>

                            </tbody>
                        </table>

                        <h2>联系信息</h2>

                        <table class="form-table">
                            <tbody><tr class="user-email-wrap">
                                <th><label for="email">电子邮件 <span class="description">（必填）</span></label></th>
                                <td><input type="email" name="email" id="email" value="<?=$row['user_email']?>" class="regular-text ltr">
                                </td>
                            </tr>

                            <tr class="user-url-wrap">
                                <th><label for="mobile">手机</label></th>
                                <td><input type="text" name="mobile" id="mobile" value="<?=$row['user_mobile']?>" class="regular-text code"></td>
                            </tr>

                            </tbody>
                        </table>


                        <?php if(is_array($postsRows)){ ?>
                            <h2>技能</h2>
                            <table class="form-table">
                                <tbody>
                                <tr class="user-url-wrap">
                                    <th><label for="skill">技能</label></th>
                                    <td>
                                        <?php foreach ($postsRows as $prow){?>

                                            <lable for="du"><?=$prow['post_title']?></lable>

                                            <?php if(preg_match('/算/', $prow['post_title'])){ ?>
                                                <input id="du" type="checkbox" <?php if($row['compute'] == $prow['ID']){ ?> checked="checked"<?php } ?> name="compute" value="<?=$prow['ID']?>">
                                            <?php }elseif(preg_match('/记/', $prow['post_title'])){ ?>
                                                <input id="du" type="checkbox" <?php if($row['memory'] == $prow['ID']){ ?> checked="checked"<?php } ?> name="memory" value="<?=$prow['ID']?>">
                                            <?php }elseif(preg_match('/读/', $prow['post_title'])){ ?>
                                                <input id="du" type="checkbox" <?php if($row['read'] == $prow['ID']){ ?> checked="checked"<?php } ?> name="read" value="<?=$prow['ID']?>">
                                            <?php } ?>


                                        <?php } ?>
                                        <input type="hidden" name="sk_id" value="<?=$id?>">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        <?php }?>



                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="user_id" value="<?=$row['user_id']?>">

                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="更新教练"></p>
                    </form>
                </div>
                <script type="text/javascript">
                    if (window.location.hash == '#password') {
                        document.getElementById('pass1').focus();
                    }
                </script>

                <div class="clear"></div></div><!-- wpbody-content -->
            <div class="clear"></div></div>

        <?php
    }

    /**
     * 教练的学员
     */
    public function student(){

        $page = ($page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1) < 1 ? 1 : $page;
        $type = isset($_GET['type']) ? intval($_GET['type']) : 0;
        if($type == 0)
            $typeWhere = '';
        else
            $typeWhere = 'AND co.apply_status='.$type;

        global $current_user,$wpdb;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $coach_id = isset($_GET['id']) ? intval($_GET['id']) : $current_user->ID;
        $sql = 'SELECT SQL_CALC_FOUND_ROWS u.display_name,u.user_login,u.user_email,u.user_mobile,co.id,co.apply_status,
                CASE co.apply_status 
                WHEN -1 THEN "<span style=\'color:#a00\'>已拒绝</span>" 
                WHEN 1 THEN "<span style=\'color:#2aa52e\'>申请中</span>" 
                WHEN 2 THEN "<span style=\'color:#0073aa\'>已通过</span>" 
                END AS apply_name 
                FROM '.$wpdb->prefix.'my_coach co LEFT JOIN '.$wpdb->users.' u ON u.ID=co.user_id WHERE co.coach_id='.$coach_id.' AND u.ID>0 '.$typeWhere.'  
                LIMIT '.$start.','.$pageSize;
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
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">学员</h1>
            <ul id="tab">
                <li class="<?php if($type == 0) echo 'active'?>" onclick="window.location.href='<?='?page=teacher-student&type=0'.'&id='.$coach_id?>'">所有</li>
                <li class="<?php if($type == 2) echo 'active'?>" onclick="window.location.href='<?='?page=teacher-student&type=2'.'&id='.$coach_id?>'">已通过</li>
                <li class="<?php if($type == 1) echo 'active'?>" onclick="window.location.href='<?='?page=teacher-student&type=1'.'&id='.$coach_id?>'">申请中</li>
            </ul>
            <form method="get" onsubmit="return false;">



                <input type="hidden" id="_wpnonce" name="_wpnonce" value="31db78f456"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">	<div class="tablenav top">

                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label><select name="action" id="bulk-action-selector-top">
                            <option value="">批量操作</option>
                            <option value="2">通过申请</option>
                            <option value="-1">拒绝申请</option>
                        </select>
                        <input type="button" id="doaction" class="button action batch-btn" value="应用">
                    </div>

                    <div class="tablenav-pages one-page"></div>
                    <div class="tablenav-pages one-page">
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
                        <th scope="col" id="name" class="manage-column column-name">姓名</th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                        <th scope="col" id="email" class="manage-column column-email sortable desc">
                            <a href="javascript:;"><span>电子邮件</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="role" class="manage-column column-role">申请状态</th>

                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">
                         <?php foreach ($rows as $row){ ?>
                             <tr id="user-5" data-id="<?=$row['id']?>">
                                 <th scope="row" class="check-column check" >
                                     <label class="screen-reader-text">选择13982242710</label>
                                     <input type="checkbox" name="users[]" id="user_5" class="subscriber" value="5">
                                 </th>
                                 <td class="username column-username has-row-actions column-primary" data-colname="用户名">
                                     <strong><a href="javascript:;">   <?=$row['user_login']?></a></strong><br>
                                     <div class="apply_option">
                                     <div class="row-actions">

                                         <?php if($row['apply_status'] == 1){ ?>


                                         <span class="edit"><a href="javascript:;" class="agree"> 通过审核</a> | </span>
                                         <span class="delete"><a class="submitdelete refuse" href="javascript:;">拒绝申请</a>  </span>

                                         <?php }?>
                                     </div>
                                 </td>
                                 <td class="name column-name" data-colname="姓名"><span aria-hidden="true"><?=str_replace(', ', '', $row['display_name'])?></span><span class="screen-reader-text">未知</span></td>
                                 <td class="email column-mobile" data-colname="手机"><a href="tel:<?=$row['user_mobile']?>"><?=$row['user_mobile']?></a></td>
                                 <td class="email column-email" data-colname="电子邮件"><a href="mailto:456789@qq.com"><?=$row['user_email']?></a></td>
                                 <td class="role column-role" data-colname="申请状态"><?=$row['apply_name']?></td>

                             </tr>
                         <?php } ?>


                    </tbody>
                    <tfoot>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="username" class="manage-column column-username column-primary sortable desc">
                            <a href="javascript:;"><span>用户名</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="name" class="manage-column column-name">姓名</th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                        <th scope="col" id="email" class="manage-column column-email sortable desc">
                            <a href="javascript:;"><span>电子邮件</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="role" class="manage-column column-role">申请状态</th>
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">

                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label><select name="action2" id="bulk-action-selector-bottom">
                            <option value="">批量操作</option>
                            <option value="2">通过申请</option>
                            <option value="-1">拒绝申请</option>
                        </select>
                        <input type="button" id="doaction3" class="button action batch-btn" value="应用">
                    </div>
                    <div class="tablenav-pages one-page">
                        <?=$pageHtml?>
                    </div>

                    </div>

                    <br class="clear">
                </div>
            </form>

            <br class="clear">

        </div>
        <?php
    }


    /**
     * 教练的课程
     */
    public function course(){

    }

    /**
     * 引入当前页面css/js
     */
    public function register_scripts(){
        switch ($_GET['page']){
            case 'teacher':
                wp_register_script('list-js',match_js_url.'teacher-list.js');
                wp_enqueue_script( 'list-js' );
                wp_register_style('list-css',match_css_url.'teacher-list.css');
                wp_enqueue_style( 'list-css' );
                break;
            case 'teacher-datum':
                wp_register_style('datum-css',match_css_url.'teacher-datum.css');
                wp_enqueue_style( 'datum-css' );
                wp_register_script('datum-js',match_js_url.'teacher-datum.js');
                wp_enqueue_script( 'datum-js' );
                break;
            case 'teacher-student':
                wp_register_style('list-css',match_css_url.'teacher-list.css');
                wp_enqueue_style( 'list-css' );
                wp_register_style('student-css',match_css_url.'teacher-student.css');
                wp_enqueue_style( 'student-css' );
                wp_register_script('student-js',match_js_url.'teacher-student.js');
                wp_enqueue_script( 'student-js' );
                break;
        }
        echo "<script>var ajax_url='".admin_url('admin-ajax.php' )."';</script>";
    }


}
new Teacher();

function aaa(){
    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

        /*********************导入处理******************************/
        $cwd = getcwd();
        chdir( DIR_SYSTEM.'PHPExcel' );
        require_once( 'Classes/PHPExcel.php' );
        chdir( $cwd );
        /* $cwd = getcwd();
         chdir( DIR_SYSTEM.'PHPExcel' );
         require_once( 'Classes/PHPExcel.php' );
         chdir( $cwd );*/

        // parse uploaded spreadsheet file
        $inputFileType = PHPExcel_IOFactory::identify($_FILES['file']['tmp_name']);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(true);

        //接收存在缓存中的excel表格
        $reader = $objReader->load($_FILES['file']['tmp_name']);
        $sheet = $reader->getSheet(0);
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        // $highestColumn = $sheet->getHighestColumn(); // 取得总列数
        $pageSize = 1000;
        $totalPage = floor(($highestRow-1 + $pageSize -1) / $pageSize);
        /******************************************************/

        switch ($this->request->post['type']){
            case 'excel':
                $result = $this->excelImport();   //模版导入
                break;
            case 'product_order':
                $result = $this->producOrderImport();  //商品订单导入
                break;
            case 'tablet_order':
                $result = $this->tabletOrderImport($reader,$highestRow,$totalPage,$pageSize);  //牌位订单导入
                break;
            case 'tablet_type':
                $result = $this->tabletTypeImport($reader,$highestRow,$totalPage,$pageSize);   //牌位类型导入
                break;
            default:
                $this->error['warning'] = '参数错误';
                break;

        }

    }
}