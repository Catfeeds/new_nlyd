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
    private $coachRole;
    public function __construct()
    {
        //add_action( 'init', array($this,'add_wp_roles'));
        $ip = $_SERVER['REMOTE_ADDR'];
        if($ip == '127.0.0.1'){
            $this->coachRole  = 'editor';
        }else{
            $this->coachRole  = 'coach';
        }

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
        global $wpdb;
        $page = ($page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1) < 1 ? 1 : $page;
        $searchStr = isset($_GET['search']) ? trim($_GET['search']) : '';
        $serachWhere = '';
        $join = '';
        if($searchStr != ''){
            $join = " LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=a.coach_id AND um.meta_key='user_real_name' 
                LEFT JOIN {$wpdb->usermeta} AS um2 ON um2.user_id=a.coach_id AND um2.meta_key='user_ID' ";
            $serachWhere = " AND (b.user_mobile LIKE '%{$searchStr}%' OR um2.meta_value LIKE '%{$searchStr}%' OR um.meta_value LIKE '%{$searchStr}%')" ;
        }

        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $sql = "SELECT SQL_CALC_FOUND_ROWS b.user_login,a.id,b.ID AS coach_id,a.read,a.memory,a.compute,b.user_mobile,um_id.meta_value AS userID
                    FROM {$wpdb->prefix}users b
                    LEFT JOIN {$wpdb->usermeta} AS uml ON uml.user_id = b.ID AND uml.meta_key='{$wpdb->prefix}capabilities'
                    LEFT JOIN {$wpdb->prefix}coach_skill a  ON a.coach_id = b.ID 
                    LEFT JOIN {$wpdb->usermeta} AS um_id ON um_id.user_id = a.coach_id AND um_id.meta_key='user_ID' 
                    {$join} 
                    WHERE uml.meta_value LIKE '%{$this->coachRole}%' AND b.ID !='' {$serachWhere} 
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
//        leo_dump($rows);die;
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
                        <input type="text" id="searchs" name="s" placeholder="姓名/ID/手机" value="<?=$searchStr?>">
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
                <h2 class="screen-reader-text">用户列表</h2>
            <table class="wp-list-table widefat fixed striped users">
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
                    $organizeClass = new Organize();
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
                         //课程数量
                        $course_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}course WHERE coach_id='{$row['coach_id']}'");
                        //所属机构
                        $zone_meta = $wpdb->get_results("SELECT zm.zone_city,zm.zone_match_type,zm.type_id,zm.zone_name,zm.zone_number,zjc.zone_id FROM {$wpdb->prefix}zone_join_coach AS zjc
                                     LEFT JOIN {$wpdb->prefix}zone_meta AS zm ON zm.user_id=zjc.zone_id
                                     WHERE zjc.coach_id='{$row['coach_id']}'", ARRAY_A);
                        $zone_name_arr = [];
                        foreach ($zone_meta as $zmv){
                            if($zmv['zone_id'] < 1) {
                                $zone_name_arr[] = '平台';
                                continue;
                            }
                            $type_alias = $wpdb->get_var("SELECT zone_type_alias FROM {$wpdb->prefix}zone_type WHERE id={$zmv['type_id']}");
                            $zone_name_arr[] = $organizeClass->echoZoneName($type_alias,$zmv['zone_city'],$zmv['zone_name'],$zmv['zone_match_type'],$zmv['zone_number'],'get','#c47c27');
                        }
                        $zone_name = join('<br />', $zone_name_arr);
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
                            <td class="image column-image" data-colname="教练照片" id="cardImg-<?=$row['coach_id']?>">
                                <img src="<?=isset($usermeta['user_images_color'])?unserialize($usermeta['user_images_color'][0])[0]:''?>" style="height: 60px;" alt="">
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
                                <a href="<?=admin_url('admin.php?page=course&coach_id='.$row['coach_id'])?>"><?=$course_num?></a>
                            </td>
                            <td class="outfit column-outfit" data-colname="所属机构">

                                <?=$zone_name?>
                            </td>
                            <td class="option column-option" data-colname="操作">
                                <a style="color: #00aff9" href="<?php echo '?page=teacher-datum&id='.$row['coach_id'] ?>">编辑</a>
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

            <script type="text/javascript">
                jQuery(document).ready(function($) {

                    layui.use('layer', function(){
                        var layer = layui.layer;
                        var _title = '';
                        <?php
                        foreach ($rows as $row){
                        $real_name = get_user_meta($row['ID'], 'user_real_name', true);
                        $real_name = isset($real_name['real_name']) ? $real_name['real_name'] :'';
                        ?>
                        _title = '<?=$real_name?>'
                        layer.photos({//图片预览
                            photos: '#cardImg-<?=$row['coach_id']?>',
                            move : false,
                            title : _title,
                            anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                        })
                        <?php } ?>
                    });

                })

            </script>
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
        $coach_id = $_GET['id'];
        if(is_post()){
            $is_assign = $wpdb->get_var("SELECT is_assign FROM {$wpdb->prefix}coach_skill WHERE coach_id='{$coach_id}'");
            $zone_user_id = isset($_POST['zone_user_id']) ? intval($_POST['zone_user_id']) : 0;
            $new_coach_id = isset($_POST['new_coach_id']) ? intval($_POST['new_coach_id']) : 0;
            $coach_detail = isset($_POST['coach_detail']) ? trim($_POST['coach_detail']) : '';
            $read_level = isset($_POST['read_level']) ? trim($_POST['read_level']) : '';
            $memory_level = isset($_POST['memory_level']) ? trim($_POST['memory_level']) : '';
            $compute_level = isset($_POST['compute_level']) ? trim($_POST['compute_level']) : '';
//            $zone_user_id = isset($_POST['zone_user_id']) ? intval($_POST['zone_user_id']) : 0;
//            if($zone_user_id < 1) $err_msg .= '请选择所属主体机构';
            if($err_msg == ''){
                $wpdb->query('START TRANSACTION');
                //移动学员
                if($new_coach_id > 0 && $new_coach_id != $coach_id && $is_assign != '1'){
                    $move_bool = $wpdb->update($wpdb->prefix.'my_coach', ['coach_id' => $new_coach_id], ['coach_id' => $coach_id]);
                    if(!$move_bool) {
                        $var = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}my_coach WHERE coach_id='{$coach_id}'");
                        if($var) $err_msg.= '<br />.移动学员失败';
                    }
                }
                if($err_msg == '' && $is_assign != '1'){
                    //是否已有所属机构
                    $zone_coach = $wpdb->get_row("SELECT id,zone_id FROM {$wpdb->prefix}zone_join_coach WHERE coach_id='{$coach_id}' AND zone_id='{$zone_user_id}'",ARRAY_A);
                    $zone_coach_id = $zone_coach['id'];
                    $zone_id = $zone_coach['zone_id'];
                    if($zone_coach_id){
                        if($zone_user_id != $zone_id){
                            //是否有学员
                            $coach_student_val = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}my_coach WHERE coach_id='{$coach_id}'");
                            if($coach_student_val){
                                $err_msg = '当前教练存在学员,请先解除教学关系或移动到其它教练再更换所属机构';
                            }
                        }
                        if($err_msg == '') {
                            $zone_bool = $wpdb->update("{$wpdb->prefix}zone_join_coach", ['zone_id' => $zone_user_id], ['id' => $zone_coach_id]) || $zone_user_id == $zone_id;
                        }else{
                            $zone_bool = false;
                        }
                    }else{
                        $zone_bool = $wpdb->insert("{$wpdb->prefix}zone_join_coach",['zone_id' => $zone_user_id,'coach_id' => $coach_id]);
                    }
                    if(!$zone_bool) $err_msg .= '<br />更新所属机构失败!';
                }
                if($err_msg == ''){
                    //教学类别
                    $reading_value = 0;
                    $memory_value = 0;
                    $arithmetic_value = 0;
                    if(isset($_POST['categorys'])){
                        foreach ($_POST['categorys'] as $v){
                            $category_v = explode('_',$v);
                            switch ($category_v[1]){
                                case 'reading':
                                    $reading_value = $category_v[0];
                                    break;
                                case 'memory':
                                    $memory_value = $category_v[0];
                                    break;
                                case 'arithmetic':
                                    $arithmetic_value = $category_v[0];
                                    break;
                            }
                        }
                    }
                    //解除取消的类别下的学员
                    $categoryArr = getCategory();
//                $coach_id = $wpdb->get_var("SELECT coach_id FROM {$wpdb->prefix}coach_skill WHERE coach_id='{$coach_id}'");
                    $newCategoryArr = array_reduce($categoryArr,function(&$newArray,$v){
                        $newArray[$v['alis']] = $v;
                        return $newArray;
                    });
                    foreach (['reading'=>$reading_value,'memory'=>$memory_value,'arithmetic'=>$arithmetic_value] AS $cateK => $cateV){
                        if($cateV < 1){
                            $bool = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}my_coach WHERE coach_id='{$coach_id}' AND category_id='{$newCategoryArr[$cateK]['ID']}' AND apply_status=2");
                            if($bool){
                                $cate_name = $cateK.'_value';
                                $bool = $wpdb->update($wpdb->prefix.'my_coach',['apply_status'=>3],['coach_id'=>$coach_id,'category_id'=>$newCategoryArr[$cateK]['ID'],'apply_status'=>2]);
                                if(!$bool) {
//                            $$cate_name = $newCategoryArr[$cateK]['ID'];
                                    $err_msg .= '<br> 取消'.$newCategoryArr[$cateK]['post_title'].'教学资格失败!';
                                }
                            }
                            //删除申请
                            $wpdb->delete($wpdb->prefix.'my_coach',['coach_id'=>$coach_id,'category_id'=>$newCategoryArr[$cateK]['ID'],'apply_status'=>1]);
                        }
                    }
                    if($err_msg == ''){
                        $coach_skill_bool = $wpdb->update($wpdb->prefix.'coach_skill',[
                            'read'=>$reading_value,
                            'memory'=>$memory_value,
                            'compute'=>$arithmetic_value,
                            'coach_detail'=>$coach_detail,
                            'read_level'=>$read_level,
                            'memory_level'=>$memory_level,
                            'compute_level'=>$compute_level,
                        ],['coach_id'=>$coach_id]);
                        if(!$coach_skill_bool) {
                            $coach_skill_row = $wpdb->get_row("SELECT `read`,`memory`,`compute` FROM {$wpdb->prefix}coach_skill WHERE coach_id='{$coach_id}'");
                            if($coach_skill_row->read != $reading_value || $coach_skill_row->memory != $memory_value || $coach_skill_row->compute != $arithmetic_value) $err_msg .= '更新教学类别失败!';
                        }
                    }
                }



                if($err_msg == ''){
                    $wpdb->query('COMMIT');
                    $suc_msg = '更新成功';
                }else{
                    $wpdb->query('ROLLBACK');
                    $err_msg .= '<br />更新失败';
                }
            }
        }

        $sql = "SELECT b.user_mobile,b.ID AS user_id,a.read,a.memory,a.compute,zm.zone_name,zjc.zone_id,a.is_assign,a.coach_detail,a.read_level,a.memory_level,a.compute_level
                    FROM {$wpdb->users} AS  b  
                    LEFT JOIN {$wpdb->prefix}coach_skill AS  a ON a.coach_id = b.ID 
                    LEFT JOIN {$wpdb->prefix}zone_join_coach AS zjc ON zjc.coach_id = a.coach_id 
                    LEFT JOIN {$wpdb->prefix}zone_meta AS zm ON zjc.zone_id = zm.user_id 
                    WHERE a.coach_id={$coach_id}";
        $row = $wpdb->get_row($sql, ARRAY_A);
//        leo_dump($row);die;
        if(!$row) {
            //查询是否有user数据
            $var = $wpdb->get_var("SELECT user_id FROM {$wpdb->usermeta} WHERE user_id='{$coach_id}' AND meta_key='{$wpdb->prefix}capabilities' AND meta_value LIKE '%{$this->coachRole}%'");
            //查询是否有coach_skill数据
            $covar = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}coach WHERE coach_id='{$coach_id}'");
            if($var && !$covar ){
                //添加教练技能表记录
                $bool = $wpdb->insert($wpdb->prefix.'coach_skill', ['coach_id' => $coach_id]);
                if(!$bool) exit('未找到用户数据,插入教练技能失败!');
                $row = $wpdb->get_row($sql, ARRAY_A);
            }else{
                exit('未找到用户数据!');
            }
        }
        $postsRows = getCategory();
        $usermeta = get_user_meta($row['user_id']);
        $user_real_name = isset($usermeta['user_real_name']) ? unserialize($usermeta['user_real_name'][0]) : [];
//        leo_dump($postsRows);
        //学员数量
        $studentNumArr = $this->getStudentNum($row['user_id']);
        //课程数量
        $course_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}course WHERE coach_id='{$row['user_id']}'");

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
<!--                            <tr class="user-last-name-wrap">-->
<!--                                <th><label for="surname">教练照片</label></th>-->
<!--                                <td>-->
<!--                                    <img src="" alt="">-->
<!--                                    <input type="file">-->
<!--                                </td>-->
<!--                            </tr>-->
                            <tr class="user-last-name-wrap">
                                <th><label for="">教学类别</label></th>
                                <td>
                                    <?php foreach ($postsRows as $prv){ ?>
                                        <label for="category_<?=$prv['ID']?>"><?=$prv['post_title']?></label>
                                        <input name="categorys[]" type="checkbox" <?=in_array($prv['ID'],[$row['read'],$row['memory'],$row['compute']])?'checked="checked"':''?> id="category_<?=$prv['ID']?>" value="<?=$prv['ID'].'_'.$prv['alis']?>">
                                    <?php } ?>
                                    <span style="color: #c4330a;font-weight: bold">取消时请慎重, 一旦取消,将会清除此类别已有学员关系,无法恢复</span>
                                </td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="memory_level">记忆类教练职称</label></th>
                                <td><input type="text" name="memory_level" id="memory_level" value="<?=isset($row['memory_level']) ? $row['memory_level']: ''?>" class="regular-text"></td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="read_level">速读类教练职称</label></th>
                                <td><input type="text" name="read_level" id="read_level" value="<?=isset($row['read_level']) ? $row['read_level']: ''?>" class="regular-text"></td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="compute_level">心算类教练职称</label></th>
                                <td><input type="text" name="compute_level" id="compute_level" value="<?=isset($row['compute_level']) ? $row['compute_level']: ''?>" class="regular-text"></td>
                            </tr>
<!--                            <tr class="user-last-name-wrap">-->
<!--                                <th><label for="surname">教练证书</label></th>-->
<!--                                <td><input type="text" name="surname" id="surname" value="--><?//=explode(', ',$row['display_name'])[0]?><!--" class="regular-text"></td>-->
<!--                            </tr>-->
<!---->
<!--                            <tr class="user-last-name-wrap">-->
<!--                                <th><label for="surname">教练简介</label></th>-->
<!--                                <td>-->
<!--                                    <textarea name="" id="" cols="30" rows="10"></textarea>-->
<!--                                </td>-->
<!--                            </tr>-->
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
                                <td><?=$course_num?>个      <a style="color: #c4071c;text-decoration: none;font-weight: 600" href="<?=admin_url('admin.php?page=course&coach_id='.$row['user_id'])?>">(点击进入课程列表)</a></td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="coach_detail">教练简介</label></th>
                                <td> <?php wp_editor( isset($row['coach_detail']) ? $row['coach_detail'] : '', 'coach_detail', $settings = array() ); ?></td>
                            </tr>
                            <?php if($row['is_assign'] != '1'){ ?>
                                <tr class="user-last-name-wrap">
                                    <th><label for="zone_user_id">所属机构</label></th>
                                    <td>
                                        <select class="js-data-select-ajax" name="zone_user_id" style="width: 50%" data-action="get_base_zone_list" data-type="all_base">
                                            <?php if($row['zone_id'] > 0){
                                                ?>
                                                <option value="<?=$row['zone_id']?>" selected="selected">
                                                    <?=$row['zone_name']?>
                                                </option>
                                                <?php
                                            }else{
                                                ?>
                                                <option value="0" selected="selected">
                                                    平台
                                                </option>
                                                <?php
                                            } ?>

                                        </select>
                                    </td>
                                </tr>
                                <tr class="user-last-name-wrap">
                                    <th><label for="zone_user_id">移动学员</label></th>
                                    <td>
                                        <select class="js-data-select-ajax" name="new_coach_id" style="width: 50%" data-action="get_zone_coach" data-type="<?=$row['zone_id']?>">

                                        </select>
                                    </td>
                                </tr>
                            <?php } ?>


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
//        $sql = "select ID,post_title from {$wpdb->prefix}posts where post_type = 'match-category' and post_status = 'publish' and post_title not like '%自测%' and post_title not like '%训练%' order by menu_order asc  ";
//        $postsRows = $wpdb->get_results($sql,ARRAY_A);
        $postsRows = getCategory();

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
                            <label for="all_category_1_<?=$prv['ID']?>"><?=$prv['post_title']?></label><input id="all_category_1_<?=$prv['ID']?>" type="checkbox" name="all_category[]" value="<?=$prv['ID']?>">
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
                                        $checkboxType = $row['apply_status'] == 1 ? 'checked="checked"' : '';
                                        $category_arr = explode('/',$row['category']);
                                        foreach ($category_arr as $cak => $cav){
                                            $cav = explode(',',$cav);
                                            echo '<input type="checkbox" id="ca_'.$cav[0].'" '.$checkboxType.' value="'.$cav[0].'" ><label for="ca_'.$cav[0].'">'.$cav[1].'</label>';
                                        }
                                     ?>
                                         <button type="button" class="button confirm-option"></button>
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
                            <label for="all_category_2_<?=$prv['ID']?>"><?=$prv['post_title']?></label><input id="all_category_2_<?=$prv['ID']?>" type="checkbox" name="all_category[]" value="<?=$prv['ID']?>">
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
        $err_msg = '';
        $suc_msg = '';
        global $wpdb;
        if(is_post()){

            $user_login = isset($_POST['user_login']) ? trim($_POST['user_login']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $real_name = isset($_POST['real_name']) ? trim($_POST['real_name']) : '';
            $real_age = isset($_POST['real_age']) ? intval($_POST['real_age']) : 0;
            $user_mobile = isset($_POST['user_mobile']) ? trim($_POST['user_mobile']) : '';
            $user_gender = isset($_POST['user_gender']) ? trim($_POST['user_gender']) : '';
            $card_type = isset($_POST['card_type']) ? trim($_POST['card_type']) : '';
            $card_num = isset($_POST['card_num']) ? trim($_POST['card_num']) : '';
            $categorys = isset($_POST['categorys']) ? $_POST['categorys'] : '';
            $zone_user_id = isset($_POST['zone_user_id']) ? intval($_POST['zone_user_id']) : 0;

            //判断账号格式
            if(!preg_match('/^1[3456789][0-9]{9}$/',$user_login) && !preg_match('/^[a-z0-9A-Z]+[- | a-z0-9A-Z . _]+@([a-z0-9A-Z]+(-[a-z0-9A-Z]+)?\\.)+[a-z]{2,}$/',$user_login)) $err_msg = '账号格式不正确';
            if(strlen($password) < 6) $err_msg .= '<br />密码长度不够';
            if($real_name == '') $err_msg .= '<br />请填写教练姓名';
            if($real_age < 1) $err_msg .= '<br />请填写教练年龄';
            if(!preg_match('/^1[3456789][0-9]{9}$/',$user_mobile)) $err_msg .= '<br />手机格式不正确';
            if($user_gender == '') $err_msg .= '<br />请填写教练性别';
            if($card_type == '') $err_msg .= '<br />请填写选择证件类型';
            if($card_num == '') $err_msg .= '<br />请填证件号码';
            $wpdb->query('START TRANSACTION');
            //添加user先
            $user_id = wp_create_user($user_login, $password, '');
            if($user_id < 1) $err_msg = '创建用户失败!';
            if($err_msg == ''){
                $upBool = $wpdb->update($wpdb->users,['user_mobile' => $user_login], ['ID' => $user_id]);
                if(!$upBool) $err_msg = '更新手机号码失败!';
            }
            //添加user_meta了
            if($err_msg == ''){
                $user_real_name = [
                  'real_name' => $real_name,
                  'real_age' => $real_age,
                  'real_type' => $card_type,
                  'real_ID' => $card_num,
                ];
                update_user_meta($user_id, 'user_real_name',$user_real_name);
                update_user_meta($user_id, 'user_ID', 10000000+$user_id);
                update_user_meta($user_id, 'user_gender', $user_gender);
            }

            //现在整教练技能
           if($err_msg == ''){
               $reading_value = 0;
               $memory_value = 0;
               $arithmetic_value = 0;
               if(is_array($categorys)){
                   foreach ($categorys as $v){
                       $category_v = explode('_',$v);
                       switch ($category_v[1]){
                           case 'reading':
                               $reading_value = $category_v[0];
                               break;
                           case 'memory':
                               $memory_value = $category_v[0];
                               break;
                           case 'arithmetic':
                               $arithmetic_value = $category_v[0];
                               break;
                       }
                   }
               }
               $coach_skill_bool = $wpdb->insert($wpdb->prefix.'coach_skill',['read'=>$reading_value,'memory'=>$memory_value,'compute'=>$arithmetic_value,'coach_id'=>$user_id]);
               if(!$coach_skill_bool) $err_msg = '添加教练技能失败';
           }

           //还有所属机构哟
            $zone_bool = $wpdb->insert("{$wpdb->prefix}zone_join_coach",['zone_id' => $zone_user_id,'coach_id' => $user_id]);
            if(!$zone_bool) $err_msg = '添加所属机构失败';
            if($err_msg == ''){
                $wpdb->query('COMMIT');
                $suc_msg = '添加成功!';
            }else{
                $wpdb->query('ROLLBACK');
            }
        }
        $postsRows = getCategory();
        ?>
        <div id="wpbody" role="main">

            <div id="wpbody-content" aria-label="主内容" tabindex="0">

                <div class="wrap" id="profile-page">
                    <h1 class="wp-heading-inline">添加教练</h1>

                    <form id="" action="" method="post" novalidate="novalidate">
                        <input type="hidden" id="_wpnonce" name="_wpnonce" value="5fcd054cd3"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-edit.php?user_id=5&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">	<input type="hidden" name="wp_http_referer" value="/nlyd/wp-admin/users.php">
                        <p>
                            <input type="hidden" name="from" value="profile">
                            <input type="hidden" name="checkuser_id" value="1">
                        </p>
                        <table class="form-table">

                        </table>
                        <div id="err-box" style="color: #c42f18"><?=$err_msg?></div>
                        <div id="suc-box" style="color: #0ec431"><?=$suc_msg?></div>


                        <table class="form-table">
                            <tbody>
                            <tr class="user-user-login-wrap">
                                <th><label for="user_login">账号</label></th>
                                <td><input type="text" name="user_login" value="" placeholder="手机或邮箱格式"></td>
                            </tr>
                            <tr class="user-user-login-wrap">
                                <th><label for="password">密码</label></th>
                                <td><input type="text" name="password" value=""></td>
                            </tr>
                            <tr class="user-user-login-wrap">
                                <th><label for="real_name">教练姓名</label></th>
                                <td><input type="text" name="real_name" value=""></td>
                            </tr>

                            <tr class="user-first-name-wrap">
                                <th><label for="dis_name">教练性别</label></th>
                                <td>
                                    <label for="sex1"><input type="radio" name="user_gender" value="男" id="sex1">男</label>
                                    <label for="sex2"><input type="radio" name="user_gender" value="女" id="sex2">女</label>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="card_type">认证证件</label></th>
                                <td>
                                    <select name="card_type" id="card_type">
                                        <option value="sf">身份证</option>
                                        <option value="jg">军官证</option>
                                        <option value="hz">护照</option>
                                        <option value="tb">台胞证</option>
                                        <option value="ga">港澳证</option>
                                    </select>
                                </td>
                            </tr
                            <tr>
                                <th><label for="card_num">证件号码</label></th>
                                <td><input type="text" name="card_num" id="card_num" value="" class="regular-text"></td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="real_age">教练年龄</label></th>
                                <td><input type="text" name="real_age" id="real_age" value=""></td>
                            </tr>
                            <tr class="user-last-name-wrap">
                                <th><label for="user_mobile">手机号码</label></th>
                                <td><input type="text" name="user_mobile" value=""></td>
                            </tr>

<!--                            <tr class="user-last-name-wrap">-->
<!--                                <th><label for="surname">教练照片</label></th>-->
<!--                                <td>-->
<!--                                    <img src="" alt="">-->
<!--                                    <input type="file">-->
<!--                                </td>-->
<!--                            </tr>-->
                            <tr class="user-last-name-wrap">
                                <th><label for="">教学类别</label></th>
                                <td>
                                    <?php foreach ($postsRows as $prv){ ?>
                                        <label for="category_<?=$prv['ID']?>"><?=$prv['post_title']?></label>
                                        <input name="categorys[]" type="checkbox" id="category_<?=$prv['ID']?>" value="<?=$prv['ID'].'_'.$prv['alis']?>">
                                    <?php } ?>
                                </td>
                            </tr>
<!--                            <tr class="user-last-name-wrap">-->
<!--                                <th><label for="surname">教练职称</label></th>-->
<!--                                <td><input type="text" name="surname" id="surname" value="" class="regular-text"></td>-->
<!--                            </tr>-->
<!--                            <tr class="user-last-name-wrap">-->
<!--                                <th><label for="surname">教练证书</label></th>-->
<!--                                <td><input type="text" name="surname" id="surname" value="" class="regular-text"></td>-->
<!--                            </tr>-->
<!---->
<!--                            <tr class="user-last-name-wrap">-->
<!--                                <th><label for="surname">教练简介</label></th>-->
<!--                                <td>-->
<!--                                    <textarea name="" id="" cols="30" rows="10"></textarea>-->
<!--                                </td>-->
<!--                            </tr>-->

                            <tr class="user-last-name-wrap">
                                <th><label for="zone_user_id">所属机构</label></th>
                                <td>
                                    <select class="js-data-select-ajax" name="zone_user_id" style="width: 50%" data-action="get_base_zone_list" data-type="all_base">

                                    </select>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="添加教练"></p>
                    </form>
                </div>
                <script type="text/javascript">
                    if (window.location.hash == '#password') {
                        document.getElementById('pass1').focus();
                    }
                    jQuery(document).ready(function($) {
                        $('#card_num').on('change',function () {
                            var age = GetAge($(this).val());
                            if(age > 0) $("#real_age").val(age);
                        });
                        function GetAge(identityCard) {
                            var len = (identityCard + "").length;
                            if (len == 0) {
                                return 0;
                            } else {
                                if ((len != 15) && (len != 18))//身份证号码只能为15位或18位其它不合法
                                {
                                    return 0;
                                }
                            }
                            var strBirthday = "";
                            if (len == 18)//处理18位的身份证号码从号码中得到生日和性别代码
                            {
                                strBirthday = identityCard.substr(6, 4) + "/" + identityCard.substr(10, 2) + "/" + identityCard.substr(12, 2);
                            }
                            if (len == 15) {
                                strBirthday = "19" + identityCard.substr(6, 2) + "/" + identityCard.substr(8, 2) + "/" + identityCard.substr(10, 2);
                            }
                            //时间字符串里，必须是“/”
                            var birthDate = new Date(strBirthday);
                            var nowDateTime = new Date();
                            var age = nowDateTime.getFullYear() - birthDate.getFullYear();
                            //再考虑月、天的因素;.getMonth()获取的是从0开始的，这里进行比较，不需要加1
                            if (nowDateTime.getMonth() < birthDate.getMonth() || (nowDateTime.getMonth() == birthDate.getMonth() && nowDateTime.getDate() < birthDate.getDate())) {
                                age--;
                            }
                            return age;
                        }
                    })

                </script>

                <div class="clear"></div></div><!-- wpbody-content -->
            <div class="clear">

            </div>
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
                wp_register_script('layui-js',match_js_url.'layui/layui.js');
                wp_enqueue_script( 'layui-js' );
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
