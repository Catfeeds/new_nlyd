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
        //add_action( 'init', array($this,'add_wp_roles'));

        add_action( 'admin_menu', array($this,'register_teacher_menu_page') );
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));

    }

    public function register_teacher_menu_page(){

        if ( current_user_can( 'administrator' ) && !current_user_can( 'teacher' ) ) {
            global $wp_roles;

            $role = 'teacher';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'teacher_add';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'teacher_datum';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'teacher_student';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'teacher_course';//权限名
            $wp_roles->add_cap('administrator', $role);

        }

        add_menu_page('教练', '教练', 'teacher', 'teacher',array($this,'teacher'),'dashicons-businessman',99);
        add_submenu_page('teacher','新增教练','新增教练','teacher_add','teacher-add',array($this,'newTeacher'));
        add_submenu_page('teacher','教练资料','教练资料','teacher_datum','teacher-datum',array($this,'datum'));
        add_submenu_page('teacher','我的学生','我的学生','teacher_student','teacher-student',array($this,'student'));
        add_submenu_page('teacher','我的课程','我的课程','teacher_course','teacher-course',array($this,'course'));
    }

    /**
     * 教练列表
     */
    public function teacher(){
        
        $page = ($page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1) < 1 ? 1 : $page;
        $searchStr = isset($_GET['search']) ? trim($_GET['search']) : '';
        $serachWhere = '';
        $join = '';
        global $wpdb;
        if($searchStr != ''){
            $join = " LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=a.coach_id AND um.meta_key='user_real_name'";
            $serachWhere = " AND (b.user_mobile LIKE '%{$searchStr}%' OR b.user_email LIKE '%{$searchStr}%' OR um.meta_value LIKE '%{$searchStr}%')" ;
        }

        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $sql = "SELECT SQL_CALC_FOUND_ROWS b.user_login,b.user_email,a.id,a.coach_id,a.read,a.memory,a.compute,b.user_mobile,um_id.meta_value AS userID 
                    FROM {$wpdb->prefix}coach_skill a 
                    LEFT JOIN {$wpdb->prefix}users b ON a.coach_id = b.ID 
                    LEFT JOIN {$wpdb->usermeta} AS um_id ON um_id.user_id = a.coach_id AND um_id.meta_key='user_ID' 
                    {$join} 
                    WHERE a.coach_id > 0 AND b.ID !='' {$serachWhere} 
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

            <a href="<?=admin_url('admin.php?page=teacher-add')?>" class="page-title-action">添加教练</a>

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="31db78f456"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php?paged=1">
                <div class="tablenav top">

                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                        <select name="action" id="bulk-action-selector-top">
                            <option value="-1">批量操作</option>
                            <option value="delete">解除教练资格</option>
                        </select>
                        <input type="submit" id="doaction" class="button action" value="应用">
                    </div>
                    <p class="search-box">
                        <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                        <input type="text" id="searchs" name="s" placeholder="姓名/手机/邮箱" value="<?=$searchStr?>">
                        <input type="button" id="search-button" onclick="window.location.href='<?=admin_url('admin.php?page=teacher&search=')?>'+document.getElementById('searchs').value" class="button" value="搜索用户">
                        <select name="" id="">
                            <option value="">机构名称</option>
                        </select>
                    </p>
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
                        <th scope="col" id="name" class="manage-column column-name column-primary">姓名</th>
                        <th scope="col" id="sex" class="manage-column column-sex">性别</th>
                        <th scope="col" id="age" class="manage-column column-age">年龄</th>
                        <th scope="col" id="student" class="manage-column column-mobile">手机</th>
                        <th scope="col" id="ID" class="manage-column column-ID">教练ID</th>
                        <th scope="col" id="image" class="manage-column column-image">教练照片</th>
                        <th scope="col" id="category" class="manage-column column-category">教学类别 </th>
                        <th scope="col" id="student_num" class="manage-column column-student_num">学员数量 </th>
                        <th scope="col" id="course_num" class="manage-column column-course_num">课程数量 </th>
                        <th scope="col" id="outfit" class="manage-column column-outfit">所属机构 </th>
                        <th scope="col" id="option" class="manage-column column-option">操作 </th>
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">
                    <?php
                    foreach ($rows as $row){
                        //有多少学员
                        $studentNumArr = $this->getStudentNum($row['coach_id']);
                        //教练信息
                        $usermeta = get_user_meta($row['coach_id']);
                        $user_real_name = isset($usermeta['user_real_name'][0]) ? unserialize($usermeta['user_real_name'][0]) : [];
//                        leo_dump($usermeta);
//                        die;
                        //有多少类别
                        $categoryArr = [];
                         if($row['read']) $categoryArr[]='速读';
                         if($row['memory']) $categoryArr[]='记忆';
                         if($row['compute']) $categoryArr[]='心算';
                        ?>
                        <tr>
                            <th scope="row" class="check-column">
                                <label class="screen-reader-text" for="teacher_<?=$row['coach_id']?>"></label>
                                <input type="checkbox" name="users[]" id="teacher_<?=$row['coach_id']?>" class="subscriber" value="<?=$row['coach_id']?>">
                            </th>

                            <td class="name column-name column-primary" data-colname="姓名">
                                <span aria-hidden="true"><?=isset($user_real_name['real_name']) ? $user_real_name['real_name'] : '-'?></span>
                                <button type="button" class="toggle-row">
                                    <span class="screen-reader-text">显示详情</span>
                                </button>
                            </td>
                            <td class="sex column-sex" data-colname="性别">
                                <span aria-hidden="true"><?=isset($usermeta['user_gender']) ? $usermeta['user_gender'][0] : '-'?></span>
                            </td>
                            <td class="age column-age" data-colname="年龄">
                                <span aria-hidden="true"><?=isset($user_real_name['real_age']) ? $user_real_name['real_age'] : '-'?></span>
                            </td>
                            <td class="name column-mobile" data-colname="手机">
                                <span aria-hidden="true"><?=$row['user_mobile']?></span>
                                <span class="screen-reader-text">-</span>
                            </td>
                            <td class="ID column-ID" data-colname="ID">
                                <span aria-hidden="true"><?=$row['userID']?></span>
                                <span class="screen-reader-text">未知</span>
                            </td>
                            <td class="image column-image" data-colname="教练照片">
                                <img src="<?=isset($usermeta['user_head'])?$usermeta['user_head'][0]:''?>" style="height: 60px;" alt="">
                            </td>
                            <td class="category column-category" data-colname="教学类别">
                                <?=join('/',$categoryArr)?>
                            </td>
                            <td class="student_num column-student_num" data-colname="学员数量">
                                <a href="<?php echo '?page=teacher-student&id='.$row['coach_id']?><?=$studentNumArr['apply']>0?'&type=1':''?>" aria-label="">
                                    <span style="color: #00aff9"><?=$studentNumArr['member']?></span>
                                    <?php if($studentNumArr['apply']>0) echo '<span style="color: #c42e00">+'.$studentNumArr['apply'].'</span>'; ?>
                                </a>
                            </td>
                            <td class="course_num column-course_num" data-colname="课程数量">
                                待定
                            </td>
                            <td class="outfit column-outfit" data-colname="所属机构">
                                待定
                            </td>
                            <td class="option column-option" data-colname="操作">
                                <a style="color: #00aff9" href="<?php echo '?page=teacher-datum&id='.$row['id'] ?>">编辑</a>
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
                        <th scope="col" class="manage-column column-datum column-primary">姓名 </th>
                        <th scope="col" class="manage-column column-sex"> 性别</th>
                        <th scope="col" class="manage-column column-age"> 年龄</th>
                        <th scope="col" class="manage-column column-mobile">手机</th>
                        <th scope="col" class="manage-column column-ID"> 教练ID</th>
                        <th scope="col" class="manage-column column-image"> 教练照片</th>
                        <th scope="col" class="manage-column column-category">教学类别</th>
                        <th scope="col" class="manage-column column-student_num">学员数量 </th>
                        <th scope="col" class="manage-column column-course_num">课程数量 </th>
                        <th scope="col" class="manage-column column-outfit">所属机构 </th>
                        <th scope="col" class="manage-column column-option">操作 </th>
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
        global $wpdb;
        $id = $_GET['id'];
        if(is_post()){
            //教学类别
            $read_value = 0;
            $memory_value = 0;
            $compute_value = 0;
            if(isset($_POST['categorys'])){
                foreach ($_POST['categorys'] as $v){
                    $category_v = explode('_',$v);
                    switch ($category_v[1]){
                        case 'reading':
                            $read_value = $category_v[0];
                            break;
                        case 'memory':
                            $memory_value = $category_v[0];
                            break;
                        case 'arithmetic':
                            $compute_value = $category_v[0];
                            break;
                    }
                }
            }

            $bool = $wpdb->update($wpdb->prefix.'coach_skill',['read'=>$read_value,'memory'=>$memory_value,'compute'=>$compute_value],['id'=>$id]);
            if($bool){
                $suc_msg = '更新成功';
            }else{
                $err_msg = '更新失败';
            }

        }

        $sql = "SELECT b.user_mobile,b.ID AS user_id,a.read,a.memory,a.compute 
                    FROM {$wpdb->users} AS  b  
                    LEFT JOIN {$wpdb->prefix}coach_skill AS  a ON a.coach_id = b.ID 
                    WHERE a.id={$id}";
        $row = $wpdb->get_row($sql, ARRAY_A);
        if(!$row) exit('未找到用户数据!');
        $sql = "select p.ID,p.post_title,pm.meta_value as alis from {$wpdb->prefix}posts as p 
                left join {$wpdb->postmeta} as pm on pm.post_id=p.ID and pm.meta_key='project_alias' 
                where post_type = 'match-category' and post_status = 'publish' and post_title not like '%自测%' order by menu_order asc";
        $postsRows = $wpdb->get_results($sql,ARRAY_A);
        $usermeta = get_user_meta($row['user_id']);
        $user_real_name = isset($usermeta['user_real_name']) ? unserialize($usermeta['user_real_name'][0]) : [];
//        leo_dump($postsRows);
        //学员数量
        $studentNumArr = $this->getStudentNum($row['user_id']);
        ?>
        <div id="wpbody" role="main">

            <div id="wpbody-content" aria-label="主内容" tabindex="0">

                <div class="wrap" id="profile-page">
                    <h1 class="wp-heading-inline">教练详情</h1>

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
                        <div style="line-height: 30px;height: 30px;">
                            <span style="font-size: 26px;">教练详情</span>
                            &ensp;&ensp;&ensp;&ensp;
                            <span><a href="<?=admin_url('users.php?page=users-info&ID='.$row['user_id'])?>" style="color: #00afc4; text-decoration: none">编辑更多账户资料</a></span>
                        </div>

                        <table class="form-table">
                            <tbody><tr class="user-user-login-wrap">
                                <th><label for="user_login">教练姓名</label></th>
                                <td><?=isset($user_real_name['real_name']) ? $user_real_name['real_name']: ''?></td>
                            </tr>

                            <tr class="user-first-name-wrap">
                                <th><label for="dis_name">教练性别</label></th>
                                <td><?=isset($usermeta['user_gender']) ? $usermeta['user_gender'][0]: ''?></td>
                            </tr>

                            <tr class="user-last-name-wrap">
                                <th><label for="surname">教练年龄</label></th>
                                <td><?=isset($user_real_name['real_age']) ? $user_real_name['real_age']: ''?></td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="surname">手机号码</label></th>
                                <td><?=isset($row['user_mobile']) ? $row['user_mobile']: ''?></td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="surname">账户ID</label></th>
                                <td><?=isset($usermeta['user_ID']) ? $usermeta['user_ID'][0]: ''?></td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="surname">教练照片</label></th>
                                <td>
                                    <img src="" alt="">
                                    <input type="file">
                                </td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="">教学类别</label></th>
                                <td>
                                    <?php foreach ($postsRows as $prv){ ?>
                                        <label for="category_<?=$prv['ID']?>"><?=$prv['post_title']?></label>
                                        <input name="categorys[]" type="checkbox" <?=in_array($prv['ID'],[$row['read'],$row['memory'],$row['compute']])?'checked="checked"':''?> id="category_<?=$prv['ID']?>" value="<?=$prv['ID'].'_'.$prv['alis']?>">
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="surname">教练职称</label></th>
                                <td><input type="text" name="surname" id="surname" value="<?=explode(', ',$row['display_name'])[0]?>" class="regular-text"></td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="surname">教练证书</label></th>
                                <td><input type="text" name="surname" id="surname" value="<?=explode(', ',$row['display_name'])[0]?>" class="regular-text"></td>
                            </tr>

                            <tr class="user-last-name-wrap">
                                <th><label for="surname">教练简介</label></th>
                                <td>
                                    <textarea name="" id="" cols="30" rows="10"></textarea>
                                </td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="surname">学员数量</label></th>
                                <td>
                                    <span style="color: #007fc4"><?=$studentNumArr['member']?>位</span>
                                    &ensp;&ensp;
                                    <span style="color: #C4003D">(<?=$studentNumArr['apply']?>个新申请用户)</span>
                                    &ensp;&ensp;&ensp;&ensp;
                                    <a style="color: #c4071c;text-decoration: none;font-weight: 600" href="<?php echo '?page=teacher-student&id='.$row['user_id']?>?>">(点击进入学员列表)</a>
                                </td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="surname">相关课程</label></th>
                                <td><input type="text" name="surname" id="surname" value="<?=explode(', ',$row['display_name'])[0]?>" class="regular-text"></td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="surname">所属机构</label></th>
                                <td>
                                    <select name="" id=""></select>
                                </td>
                            </tr>

                            </tbody>
                        </table>
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
        global $current_user,$wpdb;

        $page = ($page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1) < 1 ? 1 : $page;
        $type = isset($_GET['type']) ? intval($_GET['type']) : 2;
        $coach_id = isset($_GET['id']) ? intval($_GET['id']) : $current_user->ID;
        $sql = "select ID,post_title from {$wpdb->prefix}posts where post_type = 'match-category' and post_status = 'publish' and post_title not like '%自测%' order by menu_order asc  ";
        $postsRows = $wpdb->get_results($sql,ARRAY_A);

        $catArr = [];
        //类别
        $compute = isset($_GET['compute']) ? intval($_GET['compute']) : 0;
        $memory = isset($_GET['memory']) ? intval($_GET['memory']) : 0;
        $read = isset($_GET['read']) ? intval($_GET['read']) : 0;
        if($compute > 0) $catArr[] = $compute;
        if($memory > 0) $catArr[] = $memory;
        if($read > 0) $catArr[] = $read;
        if(empty($catArr)){
            foreach ($postsRows as $pRow){
                if(preg_match('/算/', $pRow['post_title']) || preg_match('/记/', $pRow['post_title']) || preg_match('/读/', $pRow['post_title'])){
                    $catArr[] = $pRow['ID'];
                }
            }
        }
        $cateWhere = ' AND co.category_id IN(';
        foreach ($catArr as $cate){
            $cateWhere .= $cate.',';
        }
        $cateWhere = substr($cateWhere, 0, strlen($cateWhere)-1);
        $cateWhere .= ')';
        //类别end
        if($type == 0) $typeWhere = '';
        else $typeWhere = ' AND co.apply_status='.$type;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';

        $searchWhere = '';
        $searchJoin = '';
        if($searchStr != ''){
            $searchWhere = " AND (u.user_mobile LIKE '%{$searchStr}%' OR um.meta_value LIKE '%{$searchStr}%' OR um2.meta_value LIKE '%{$searchStr}%')";
            $searchJoin = " LEFT JOIN {$wpdb->usermeta} AS um2 ON um2.user_id=co.user_id AND um2.meta_key='user_real_name'
                            LEFT JOIN '.$wpdb->usermeta.' AS um ON um.user_id=co.user_id AND um.meta_key='user_ID' ";
        }
        $studentNumArr = $this->getStudentNum($coach_id);
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $sql = 'SELECT SQL_CALC_FOUND_ROWS u.user_mobile,co.apply_status,u.ID AS user_id,
                GROUP_CONCAT(p.ID,",",p.post_title separator "/") AS category,
                GROUP_CONCAT(p.post_title separator "/") AS category_name,
                CASE co.apply_status 
                WHEN -1 THEN "<span style=\'color:#a00\'>已拒绝</span>" 
                WHEN 3 THEN "<span style=\'color:#a00\'>已解除</span>" 
                WHEN 1 THEN "<span style=\'color:#2aa52e\'>申请中</span>" 
                WHEN 2 THEN "<span style=\'color:#0073aa\'>已通过</span>" 
                END AS apply_name 
                FROM '.$wpdb->prefix.'my_coach co LEFT JOIN '.$wpdb->users.' u ON u.ID=co.user_id 
                LEFT JOIN '.$wpdb->posts.' AS p ON p.ID=co.category_id 
                '.$searchJoin.' 
                WHERE co.coach_id='.$coach_id.' AND u.ID>0 '.$typeWhere.$cateWhere.$searchWhere.' 
                GROUP BY co.user_id 
                ORDER BY co.apply_status ASC 
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
        $real_name = isset(get_user_meta($coach_id, 'user_real_name',true)['real_name']) ? get_user_meta($coach_id, 'user_real_name',true)['real_name'].'-' : '';
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?=$real_name?>学生</h1>
            <ul id="tab">
                <li class="<?php if($type == 2) echo 'active'?>" onclick="window.location.href='<?='?page=teacher-student&type=2'.'&id='.$coach_id?>'">已通过(<?=$studentNumArr['member']?>)</li>
                <li class="<?php if($type == 1) echo 'active'?>" onclick="window.location.href='<?='?page=teacher-student&type=1'.'&id='.$coach_id?>'">申请中(<?=$studentNumArr['apply']?>)</li>
                <li class="<?php if($type == -1) echo 'active'?>" onclick="window.location.href='<?='?page=teacher-student&type=-1'.'&id='.$coach_id?>'">已拒绝(<?=$studentNumArr['refuse']?>)</li>

                <li class="<?php if($type == 3) echo 'active'?>" onclick="window.location.href='<?='?page=teacher-student&type=3'.'&id='.$coach_id?>'">已解除(<?=$studentNumArr['relieve']?>)</li>
            </ul>
            <br class="clear">
            <br class="clear">
            <div>
                <?php foreach ($postsRows as $prow){?>

                    <lable for="du"><?=$prow['post_title']?></lable>

                    <?php if(preg_match('/算/', $prow['post_title'])){ ?>
                        <input id="compute" type="checkbox" <?php if(in_array($prow['ID'], $catArr)) echo 'checked="checked"'; ?> name="compute" value="<?=$prow['ID']?>">&nbsp;
                    <?php }elseif(preg_match('/记/', $prow['post_title'])){ ?>
                        <input id="memory" type="checkbox" <?php if(in_array($prow['ID'], $catArr)) echo 'checked="checked"'; ?> name="memory" value="<?=$prow['ID']?>">&nbsp;
                    <?php }elseif(preg_match('/读/', $prow['post_title'])){ ?>
                        <input id="read" type="checkbox" <?php if(in_array($prow['ID'], $catArr)) echo 'checked="checked"'; ?> name="read" value="<?=$prow['ID']?>">&nbsp;
                    <?php } ?>


                <?php } ?>
                <button type="button" class="button" onclick="window.location.href='<?='?page=teacher-student&type='.$type.'&id='.$coach_id?>'+typeFunc()">确定</button>
                <script type="text/javascript">
                    function typeFunc() {
                        var compute = document.getElementById('compute').checked ? document.getElementById('compute').value : 0;
                        var memory = document.getElementById('memory').checked ? document.getElementById('memory').value : 0;
                        var read = document.getElementById('read').checked ? document.getElementById('read').value : 0;
                        var str = '&compute='+compute+'&memory='+memory+'&read='+read;
                        return str;
                    }
                </script>
            </div>
            <p class="search-box">
                <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                <input type="text" id="searchs" name="s" placeholder="姓名/ID/手机" value="">
                <input type="button" id="search-button" onclick="window.location.href='<?=admin_url('admin.php?page=teacher-student&id='.$coach_id.'&s=')?>'+document.getElementById('searchs').value" class="button" value="搜索用户">
            </p>
            <style type="text/css">
                .option-child{
                    display: none;
                }
            </style>
            <form method="get" onsubmit="return false;" data-cid="<?=$coach_id?>">



                <input type="hidden" id="_wpnonce" name="_wpnonce" value="31db78f456"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">	<div class="tablenav top">

                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                        <select name="action" id="bulk-action-selector-top" class="all_select">
                            <option value="">批量操作</option>
                            <option value="2">通过申请</option>
                            <option value="-1">拒绝申请</option>
                            <option value="3">解除教学</option>
                        </select>
                        <?php foreach ($postsRows as $prv){ ?>
                            <label for="all_category_<?=$prv['ID']?>"><?=$prv['post_title']?></label><input id="all_category_<?=$prv['ID']?>" type="checkbox" name="all_category[]" value="<?=$prv['ID']?>">
                        <?php } ?>
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

                        <th scope="col" id="name" class="manage-column column-name column-primary">
                            姓名
                        </th>
                        <th scope="col" id="sex" class="manage-column column-sex">性别</th>
                        <th scope="col" id="age" class="manage-column column-age">年龄</th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                        <th scope="col" id="ID" class="manage-column column-ID">ID</th>
                        <th scope="col" id="type" class="manage-column column-type">类别</th>
                        <th scope="col" id="role" class="manage-column column-role">申请状态</th>
                        <th scope="col" id="option" class="manage-column column-option">操作</th>

                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">
                         <?php
                         foreach ($rows as $row){
                             $usermeta = get_user_meta($row['user_id']);
                             $user_real_name = isset($usermeta['user_real_name'][0]) ? unserialize($usermeta['user_real_name'][0]) : [];
                         ?>
                             <tr data-uid="<?=$row['user_id']?>">
                                 <th scope="row" class="check-column check" >
                                     <label class="screen-reader-text">选择</label>
                                     <input type="checkbox" name="users[]" class="subscriber" value="5">
                                 </th>
                                 <td class="name column-name column-primary" data-colname="姓名">

                                     <span aria-hidden="true"><?=isset($user_real_name['real_name']) ? $user_real_name['real_name'] : '-'?></span>
                                     <br>
                                     <div class="apply_option">
                                         <div class="row-actions">

                                         </div>
                                         <button type="button" class="toggle-row">
                                             <span class="screen-reader-text">显示详情</span>
                                         </button>
                                 </td>
                                 <td class="mobile column-sex" data-colname="性别"><?=isset($usermeta['user_gender'][0]) ? $usermeta['user_gender'][0] : '-'?></td>
                                 <td class="mobile column-age" data-colname="年龄"><?=isset($user_real_name['real_age']) ? $user_real_name['real_age'] : '-'?></td>

                                 <td class="email column-mobile" data-colname="手机"><a href="tel:<?=$row['user_mobile']?>"><?=$row['user_mobile']?></a></td>
                                 <td class="ID column-ID" data-colname="ID"><span aria-hidden="true"><?=isset($usermeta['user_ID']) ? $usermeta['user_ID'][0]:'-'?></span><span class="screen-reader-text">未知</span></td>
                                 <td class="email column-type" data-colname="类别"><?=$row['category_name']?></td>
                                 <td class="role column-role" data-colname="申请状态"><?=$row['apply_name']?></td>
                                 <td class="option column-option" data-colname="操作">
                                     <?php if($row['apply_status'] == 1){ ?>
                                         <span class="edit"><a href="javascript:;" class="agree"> 通过审核</a> | </span>
                                         <span class="delete"><a class="submitdelete refuse" href="javascript:;">拒绝申请</a>  </span>
                                     <?php }elseif ($row['apply_status'] == 2){?>
                                         <span class="delete"><a class="submitdelete relieve" href="javascript:;">解除</a>  </span>
                                     <?php }?>
                                     <div class="option-child">
                                         <?php
                                            $category_arr = explode('/',$row['category']);
                                            foreach ($category_arr as $cak => $cav){
                                                $cav = explode(',',$cav);
                                                echo '<input type="checkbox" id="ca_'.$cav[0].'" value="'.$cav[0].'" ><label for="ca_'.$cav[0].'">'.$cav[1].'</label>';
                                            }
                                         ?>
                                         <button type="button" class="button confirm-option">确定</button>
                                         <button type="button" class="button cancel-option">取消</button>
                                     </div>
                                 </td>

                             </tr>
                         <?php } ?>


                    </tbody>
                    <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-name column-primary">
                            姓名
                        </th>
                        <th scope="col"  class="manage-column column-sex">性别</th>
                        <th scope="col"  class="manage-column column-age">年龄</th>
                        <th scope="col"  class="manage-column column-mobile">手机</th>
                        <th scope="col"  class="manage-column column-ID">ID</th>
                        <th scope="col" class="manage-column column-type">类别</th>
                        <th scope="col" class="manage-column column-role">申请状态</th>
                        <th scope="col" class="manage-column column-option">操作</th>
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">

                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                        <select name="action2" id="bulk-action-selector-bottom" class="all_select">
                            <option value="">批量操作</option>
                            <option value="2">通过申请</option>
                            <option value="-1">拒绝申请</option>
                            <option value="3">解除教学</option>
                        </select>
                        <?php foreach ($postsRows as $prv){ ?>
                            <label for="all_category_<?=$prv['ID']?>"><?=$prv['post_title']?></label><input id="all_category_<?=$prv['ID']?>" type="checkbox" name="all_category[]" value="<?=$prv['ID']?>">
                        <?php } ?>
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
     * 新增教练
     */
    public function newTeacher(){
        global $wpdb;
        if(is_post()){
            //教练类别
            $read = isset($_POST['read']) ? intval($_POST['read']) : 0;
            $memory = isset($_POST['memory']) ? intval($_POST['memory']) : 0;
            $compute = isset($_POST['compute']) ? intval($_POST['compute']) : 0;
            $errStr = '';
            if(empty($_POST['pass1']) || $_POST['pass1'] != $_POST['pass2']) $errStr = '两次输入的密码不一样';
            if(!preg_match('/1[345678][0-9]{9}/', $_POST['user_mobile'])) $errStr = '手机格式错误';
            if($errStr == '') {
                $wpdb->startTrans();
                $insertData = [
                    'user_login' => $_POST['user_login'],
                    'user_pass' => $_POST['pass1'],
                    'user_email' => $_POST['email'],
                    'user_mobile' => $_POST['user_mobile'],
                    'display_name' => $_POST['last_name'].', '.$_POST['first_name'],
                    'role' => $_POST['role']
                ];
                $userId = wp_insert_user($insertData);
                if(is_object($userId)){
                    $wpdb->rollback();
                    foreach ($userId->errors as $err){
                        foreach ($err as $er){
                            $errStr .= $er.'<br />';
                        }
                    }
                }else{
                    //修改usermeta表姓氏和名字
                    update_user_meta($userId, 'last_name', $_POST['last_name']);
                    update_user_meta($userId, 'first_name', $_POST['first_name']);
                    //添加教练技能
                    $skillData = [
                        'coach_id' => $userId,
                        'read' => $read,
                        'memory' => $memory,
                        'compute' => $compute,
                    ];
                    $skillRes = $wpdb->insert($wpdb->prefix.'coach_skill', $skillData);
                    if(!$skillRes){
                        $wpdb->rollback();
                        $errStr = '<strong>添加失败</strong>';
                    }
                }
                if($errStr == '') {
                    $wpdb->commit();
                    echo '<script type="text/javascript">window.location.href="'.admin_url('admin.php?page=teacher').'"</script>';
                    exit;
                }
            }


        }
        $sql = "select ID,post_title from {$wpdb->prefix}posts where post_type = 'match-category' and post_status = 'publish' order by menu_order asc  ";
        $postsRows = $wpdb->get_results($sql,ARRAY_A);
        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加教练</h1>


            <div id="ajax-response"></div>

            <p>新建教练，并将教练加入此站点。</p>
            <div style="color: #A90000;">
                <?=isset($errStr) ? $errStr : '';?>
            </div>
            <form method="post" name="createuser" id="createuser" class="validate" novalidate="novalidate">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="8e776847cc"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php"><table class="form-table">
                    <tbody>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="user_login">用户名 <span class="description">（必填）</span></label></th>
                        <td><input placeholder="不可使用中文" name="user_login" type="text" id="user_login" value="<?=isset($_POST['user_login']) ? $_POST['user_login'] : ''?>" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="60"></td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="user_mobile">手机号码 <span class="description">（必填）</span></label></th>
                        <td><input name="user_mobile" type="text" id="user_mobile" value="<?=isset($_POST['user_mobile']) ? $_POST['user_mobile'] : ''?>"></td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="email">电子邮件 <span class="description">（必填）</span></label></th>
                        <td><input name="email" type="email" id="email" value="<?=isset($_POST['email']) ? $_POST['email'] : ''?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="first_name">名字 </label></th>
                        <td><input name="first_name" type="text" id="first_name" value="<?=isset($_POST['first_name']) ? $_POST['first_name'] : ''?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="last_name">姓氏 </label></th>
                        <td><input name="last_name" type="text" id="last_name" value="<?=isset($_POST['last_name']) ? $_POST['last_name'] : ''?>"></td>
                    </tr>
<!--                    <tr class="form-field">-->
<!--                        <th scope="row"><label for="url">站点</label></th>-->
<!--                        <td><input name="url" type="url" id="url" class="code" value=""></td>-->
<!--                    </tr>-->
                    <tr class="form-field form-required user-pass1-wrap">
                        <th scope="row">
                            <label for="pass1-text">
                                密码				<span class="description hide-if-js">（必填）</span>
                            </label>
                        </th>
                        <td>
                            <input class="hidden" value=" "><!-- #24364 workaround -->
<!--                            <button type="button" class="button wp-generate-pw hide-if-no-js">显示密码</button>-->

                            <div class="wp-pwd">
								<span class="password-input-wrapper show-password">

				</span>
                                <input type="text" name="pass1" id="pass1" value="<?=isset($_POST['pass1']) ? $_POST['pass1'] : ''?>" class="regular-text strong" autocomplete="off" data-reveal="1" data-pw="#8LefUAX7w^Q!)9HJFy7muCG" aria-describedby="pass-strength-result">
<!--                                <input type="text" id="pass1-text" name="pass1-text" autocomplete="off" class="regular-text strong" disabled="">-->
                            </div>
                        </td>
                    </tr>
                    <tr class="form-field form-required user-pass2-wrap">
                        <th scope="row"><label for="pass2">重复密码 <span class="description">（必填）</span></label></th>
                        <td>
                            <input name="pass2" type="text" id="pass2" value="<?=isset($_POST['pass2']) ? $_POST['pass2'] : ''?>" autocomplete="off">
                        </td>
                    </tr>
<!---->
<!--                    <tr>-->
<!--                        <th scope="row">发送用户通知</th>-->
<!--                        <td>-->
<!--                            <input type="checkbox" name="send_user_notification" id="send_user_notification" value="1" checked="checked">-->
<!--                            <label for="send_user_notification">向新用户发送有关账户详情的电子邮件。</label>-->
<!--                        </td>-->
<!--                    </tr>-->
<!--                    <tr class="form-field">-->
<!--                        <th scope="row"><label for="role">角色</label></th>-->
<!--                        <td>-->
<!--                            <select name="role" id="role">-->
<!--                                <option selected="selected" value="subscriber">学生</option>-->
<!--                                <option value="contributor">投稿者</option>-->
<!--                                <option value="author">作者</option>-->
<!--                                <option value="editor">教练</option>-->
<!--                                <option value="administrator">管理员</option>-->
<!--                            </select>-->
<!--                        </td>-->
<!--                    </tr>-->
                    <tr class="coach-category">
                       <th>
                           教练技能
                       </th>
                        <td>
                            <?php foreach ($postsRows as $prow){?>

                                <lable for="du"><?=$prow['post_title']?></lable>

                                <?php if(preg_match('/算/', $prow['post_title'])){ ?>
                                    <input id="compute" type="checkbox" <?=(isset($_POST['compute'])&&$_POST['compute'] == $prow['ID'])?'checked="checked"':''?> name="compute" value="<?=$prow['ID']?>">
                                <?php }elseif(preg_match('/记/', $prow['post_title'])){ ?>
                                    <input id="memory" type="checkbox" <?=(isset($_POST['memory'])&&$_POST['memory'] == $prow['ID'])?'checked="checked"':''?> name="memory" value="<?=$prow['ID']?>">
                                <?php }elseif(preg_match('/读/', $prow['post_title'])){ ?>
                                    <input id="read" type="checkbox" <?=(isset($_POST['read'])&&$_POST['read'] == $prow['ID'])?'checked="checked"':''?> name="read" value="<?=$prow['ID']?>">
                                <?php } ?>


                            <?php } ?>
                        </td>
                    </tr>
                    <input type="hidden" name="role" value="editor" />
                    </tbody>
                </table>


                <p class="submit"><input type="submit" name="createuser" id="createusersub" class="button button-primary" value="添加用户"></p>
            </form>
        </div>
        <?php

    }

    /**
     * 教练的课程
     */
    public function course(){

    }

    /**
     * 获取学员的数量
     */
    public function getStudentNum($coach_id){
        global $wpdb;
        //通过数量
        $member_num = $wpdb->get_results("SELECT id FROM `{$wpdb->prefix}my_coach` WHERE apply_status=2 AND coach_id='{$coach_id}' GROUP BY user_id");
        //申请数量
        $apply_num = $wpdb->get_results("SELECT id FROM `{$wpdb->prefix}my_coach` WHERE apply_status=1 AND coach_id='{$coach_id}' GROUP BY user_id");
        //拒绝数量
        $refuse_num = $wpdb->get_results("SELECT id FROM `{$wpdb->prefix}my_coach` WHERE apply_status=-1 AND coach_id='{$coach_id}' GROUP BY user_id");
        //解除数量
        $relieve_num = $wpdb->get_results("SELECT id FROM `{$wpdb->prefix}my_coach` WHERE apply_status=3 AND coach_id='{$coach_id}' GROUP BY user_id");
        return ['member'=>count($member_num),'apply'=>count($apply_num),'refuse'=>count($refuse_num),'relieve'=>count($relieve_num)];
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
