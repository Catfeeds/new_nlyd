<?php
class Course{
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_organize_menu_page') );
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
    }

    public function register_organize_menu_page(){
        if ( current_user_can( 'administrator' ) && !current_user_can( 'course' ) ) {
            global $wp_roles;

            $role = 'course';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'course_add_course';//权限名
            $wp_roles->add_cap('administrator', $role);

        }

        add_menu_page('课程管理', '课程管理', 'course', 'course',array($this,'index'),'dashicons-businessman',99);
        add_submenu_page('course','添加课程','添加课程','course_add_course','course-add-course',array($this,'addCourse'));
    }

    /**
     * 课程列表
     */
    public function index(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $type = isset($_GET['stype']) ? intval($_GET['stype']) : 0;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $where = "WHERE 1=1";
        if($type>0){
            $where .= " AND cou.course_type='{$type}'";
        }
        if($searchStr != ''){
            $where .= " AND (cou.course_title LIKE '%{$searchStr}%')";
        }
        $rows = $wpdb->get_results("SELECT cou.course_title,cou.course_img,cou.const,cou.const,cou.is_enable,cou.coach_id,cou.course_start_time,cou.course_end_time,
                cou.created_time,cou.province,cou.city,cou.area,cou.address,cou.open_quota,cou.seize_quota,cou.course_type,cou.zone_id,cou.id,
                zm.zone_name,um.meta_value AS coach_real_name  
                FROM {$wpdb->prefix}course AS cou 
                LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=cou.coach_id AND um.meta_key='user_real_name' 
                LEFT JOIN {$wpdb->prefix}zone_meta AS zm ON zm.user_id=cou.zone_id 
                {$where}
                LIMIT {$start},{$pageSize}",ARRAY_A);
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
        //各种数量
        $numSql = "SELECT count(id) FROM {$wpdb->prefix}course";
        $lxl_num  = $wpdb->get_var($numSql.' WHERE course_type=1');
        $all_num = $wpdb->get_var($numSql);
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">课程列表</h1>

            <a href="<?=admin_url('admin.php?page=course-add-course')?>" class="page-title-action">添加课程</a>

            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤课程列表</h2>

            <br class="clear">
            <ul class="subsubsub">
                <li class="all"><a href="<?=admin_url('admin.php?page=course&stype=0')?>" <?=$type===0?'class="current"':''?> aria-current="page">全部<span class="count">（<?=$all_num?>）</span></a> |</li>
                <li class="all"><a href="<?=admin_url('admin.php?page=course&stype=1')?>" <?=$type===1?'class="current"':''?> aria-current="page">乐学乐分享<span class="count">（<?=$lxl_num?>）</span></a></li>

            </ul>


            <p class="search-box">
                <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                <input type="search" id="search_val" name="search_val" placeholder="课程名称" value="<?=$searchStr?>">
                <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=course&stype='.$type.'&s=')?>'+document.getElementById('search_val').value" value="搜索课程">
            </p>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
                        <option value="1">启用</option>
                        <option value="2">禁用</option>
                    </select>
                    <input type="button" id="doaction" data-type="all_options" class="button action all_options" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">主体列表</h2>
            <table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th scope="col" id="course_title" class="manage-column column-course_title column-primary">课程名称</th>
                    <th scope="col" id="course_img" class="manage-column column-course_img">课程图片</th>
                    <th scope="col" id="coach_id" class="manage-column column-coach_id">授课教练</th>
                    <th scope="col" id="course_start_time" class="manage-column column-course_start_time">开课时间</th>
                    <th scope="col" id="course_end_time" class="manage-column column-course_end_time">结课时间</th>
                    <th scope="col" id="address" class="manage-column column-address">授课地址</th>
                    <th scope="col" id="open_quota" class="manage-column column-open_quota">开放名额</th>
                    <th scope="col" id="seize_quota" class="manage-column column-seize_quota">已抢占名额</th>
                    <th scope="col" id="zone_user_id" class="manage-column column-zone_user_id">所属机构</th>
                    <th scope="col" id="course_type" class="manage-column column-course_type">课程类型</th>
                    <th scope="col" id="is_enable" class="manage-column column-is_enable">状态</th>
                    <th scope="col" id="created_time" class="manage-column column-created_time">创建时间</th>
                    <th scope="col" id="options1" class="manage-column column-options1">操作</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){
                    ?>
                    <tr data-id="<?=$row['id']?>">
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="cb-select-407">选择<?=$row['course_title']?></label>
                            <input id="cb-select-<?=$row['course_title']?>" type="checkbox" class="th-check" name="post[]" value="<?=$row['id']?>">
                            <div class="locked-indicator">
                                <span class="locked-indicator-icon" aria-hidden="true"></span>
                                <span class="screen-reader-text">“<?=$row['course_title']?>”已被锁定</span>
                            </div>
                        </th>
                        <td class="course_title column-course_title has-row-actions column-primary" data-colname="主体名称">
                            <?=$row['course_title']?>
                            <br>
                            <div class="row-actions">
                                <span class="edit"><a href="<?=admin_url('admin.php?page=course-add-course&id='.$row['id'])?>">编辑</a></span>
                                <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                                <!--                               <span class="view"><a href="">资料</a></span>-->
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="course_img column-course_img" data-colname="课程图片" id="course_img_<?=$row['id']?>">
                            <img src="<?=$row['course_img']?>" alt="" style="height: 60px;">
                        </td>
                        <td class="coach_id column-coach_id" data-colname="授课教练">
                            <?=!empty($row['coach_real_name']) ? unserialize($row['coach_real_name'])['real_name'] : ''?>
                        </td>
                        <td class="course_start_time column-course_start_time" data-colname="开课时间"><?=$row['course_start_time']?></td>
                        <td class="course_end_time column-course_end_time" data-colname="结课时间"><?=$row['course_end_time'] == '0000-00-00 00:00:00' ? '待定' : $row['course_end_time']?></td>
                        <td class="address column-address" data-colname="授课地址"><?=$row['province'].$row['city'].$row['area'].$row['address']?></td>
                        <td class="open_quota column-open_quota" data-colname="开放名额"><?=$row['open_quota']?></td>
                        <td class="seize_quota column-seize_quota" data-colname="已抢占名额"><?=$row['seize_quota']?></td>
                        <td class="zone_user_id column-zone_user_id" data-colname="所属机构"><?=empty($row['zone_name']) ? '平台' :$row['zone_name']?></td>
                        <td class="course_type column-course_type" data-colname="课程类型"><?=$row['course_type'] == '1' ? '乐学乐分享' :''?></td>
                        <td class="is_enable column-is_enable" data-colname="状态"><?=$row['is_enable'] == '2' ? '<span style="color: #c42800;">禁用</span>':'正常'?></td>
                        <td class="created_time column-created_time" data-colname="创建时间"><?=$row['created_time']?></td>
                        <td class="options1 column-options1" data-colname="操作">
                            <?=$row['is_enable'] == '2' ? '<a href="javascript:;" class="enable-single">启用</a>':'<a href="javascript:;" class="disable-single">禁用</a>'?>
                        </td>


                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-course_title column-primary">课程名称</th>
                    <th scope="col" class="manage-column column-course_img">课程图片</th>
                    <th scope="col" class="manage-column column-coach_id">授课教练</th>
                    <th scope="col" class="manage-column column-course_start_time">开课时间</th>
                    <th scope="col" class="manage-column column-course_end_time">结课时间</th>
                    <th scope="col" class="manage-column column-address">授课地址</th>
                    <th scope="col" class="manage-column column-open_quota">开放名额</th>
                    <th scope="col" class="manage-column column-seize_quota">已抢占名额</th>
                    <th scope="col" class="manage-column column-zone_user_id">所属机构</th>
                    <th scope="col" class="manage-column column-course_type">课程类型</th>
                    <th scope="col" class="manage-column column-is_enable">状态</th>
                    <th scope="col" class="manage-column column-created_time">创建时间</th>
                    <th scope="col" class="manage-column column-options1">操作</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
                        <option value="1">启用</option>
                        <option value="2">禁用</option>
                    </select>
                    <input type="button" id="doaction2 " class="button action all_options" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>

            <br class="clear">
            <script>
                jQuery(document).ready(function($) {
                    $('.all_options').on('click', function () {
                        postAjax($(this),'all_options');
                    });
                    $('.enable-single').on('click', function () {
                        postAjax($(this),'enable-single');
                    });
                    $('.disable-single').on('click', function () {
                        postAjax($(this),'disable-single');
                    });
                    function postAjax(_this,type) {
                        var status = 0;
                        var _id = '';
                        switch (type){
                            case 'disable-single':
                                status = 2;
                                _id = _this.closest('tr').attr('data-id');
                                break;
                            case 'enable-single':
                                status = 1;
                                _id = _this.closest('tr').attr('data-id');
                                break;
                            case 'all_options':
                                status = _this.prev().val();
                                var idArr = [];
                                $.each($('#the-list').find('.th-check:checked'),function (i,v) {
                                    idArr.push($(v).val());
                                });
                                _id = idArr.join(',');
                                break;
                            default:
                                return;
                        }
                        if((status != '1' && status != '2') || _id == '') return false;
                        var data = {'action': 'ableCourse', 'status': status, 'id': _id}
                        $.ajax({
                            url: ajaxurl,
                            data: data,
                            dataType: 'json',
                            type: 'post',
                            success: function (response) {
                                alert(response.data.info);
                                if (response['success']) {
                                    window.location.reload();
                                }
                            }, error: function () {
                                alert('请求失败!');
                            }
                        });
                    }

                    layui.use('layer', function(){
                        var layer = layui.layer;
                        var _title = '';
                        <?php foreach ($rows as $row){ ?>
                        layer.photos({//图片预览
                            photos: '#course_img_<?=$row['id']?>',
                            move : false,
                            anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                        })
                        <?php } ?>
                    });
                });

            </script>
        </div>
        <?php
    }

    /**
     * 添加/编辑课程
     */
    public function addCourse(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $success_msg = '';
        $error_msg = '';
        $row = [];
        global $wpdb;
        if(is_post()){
            $course_title = isset($_POST['course_title']) ? trim($_POST['course_title']) : '';
            $course_details = isset($_POST['course_details']) ? trim($_POST['course_details']) : '';
            $const = isset($_POST['const']) ? floatval($_POST['const']) : 0;
            $coach_id = isset($_POST['coach_id']) ? intval($_POST['coach_id']) : 0;
            $course_start_time = isset($_POST['course_start_time']) ? trim($_POST['course_start_time']) : '';
            $course_end_time = isset($_POST['course_end_time']) ? trim($_POST['course_end_time']) : '';
            $province = isset($_POST['province']) ? trim($_POST['province']) : '';
            $city = isset($_POST['city']) ? trim($_POST['city']) : '';
            $area = isset($_POST['area']) ? trim($_POST['area']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $open_quota = isset($_POST['open_quota']) ? intval($_POST['open_quota']) : 0;
            $seize_quota = isset($_POST['seize_quota']) ? intval($_POST['seize_quota']) : 0;
            $zone_id = isset($_POST['zone_id']) ? intval($_POST['zone_id']) : 0;
            $course_type = isset($_POST['course_type']) ? intval($_POST['course_type']) : 1;
            $is_enable = isset($_POST['is_enable']) ? intval($_POST['is_enable']) : 2;
            $course_category_id = isset($_POST['course_category_id']) ? intval($_POST['course_category_id']) : 0;
            $duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;
            $admin_mobile = isset($_POST['admin_mobile']) ? trim($_POST['admin_mobile']) : '';
//            if($course_title == '') $error_msg = '请填写课程名称';
//            if($coach_id < 1) $error_msg .= ($error_msg == '' ? '':'<br />').'请选择授课教练';
//            if($course_start_time == '') $error_msg .= ($error_msg == '' ? '':'<br />').'请选择开课时间';
//            if($province == '') $error_msg .= ($error_msg == '' ? '':'<br />').'请选择省份';
//            if($city == '') $error_msg .= ($error_msg == '' ? '':'<br />').'请选择城市';
//            if($area == '') $error_msg .= ($error_msg == '' ? '':'<br />').'请选择区县';
//            if($address == '') $error_msg .= ($error_msg == '' ? '':'<br />').'请填写详细地址';
//            if($open_quota < 0) $error_msg .= ($error_msg == '' ? '':'<br />').'请填写开放名额';
            if($course_end_time == '0100-01-01 00:00:00') $course_end_time = '';
            if($error_msg == ''){
                $insertData = [
                    'course_title' => $course_title,
                    'course_details' => $course_details,
                    'const' => $const,
                    'coach_id' => $coach_id,
                    'course_start_time' => $course_start_time,
                    'course_end_time' => $course_end_time,
                    'province' => $province,
                    'city' => $city,
                    'area' => $area,
                    'address' => $address,
                    'open_quota' => $open_quota,
                    'seize_quota' => $seize_quota,
                    'zone_id' => $zone_id,
                    'course_type' => $course_type,
                    'is_enable' => $is_enable,
                    'course_category_id' => $course_category_id,
                    'duration' => $duration,
                    'admin_mobile' => $admin_mobile,
                ];

                //图片
                if(isset($_FILES['course_img']) && $_FILES['course_img']['size'] > 0){
                    $upload_dir = wp_upload_dir();
                    $dir = '/course/';
                    //print_r($upd);
                    $file = saveIosFile($_FILES['course_img']['tmp_name'],$upload_dir['basedir'].$dir);
                    if($file){
                        $insertData['course_img'] = $upload_dir['baseurl'].$dir.$file;
                        $old_img = $wpdb->get_var("SELECT course_img FROM {$wpdb->prefix}course WHERE id='{$id}'");
                    }
                }
                if($id > 0){
                    $bool = $wpdb->update($wpdb->prefix.'course',$insertData,['id'=>$id]);
                    if($bool && isset($old_img)){
                        //删除原图片
                        $old_img = explode('course/',$old_img);
                        if(isset($old_img[1])){
                            is_file($upload_dir['basedir'].$dir.$old_img[1]) && unlink($upload_dir['basedir'].$dir.$old_img[1]);
                        }
                    }
                }else{
                    $insertData['created_time'] = get_time('mysql');
                    $bool = $wpdb->insert($wpdb->prefix.'course',$insertData);
                }
                if($bool){
                    $success_msg = '操作成功!';
                }else{
                    is_file($upload_dir['basedir'].$dir.$file) && unlink($upload_dir['basedir'].$dir.$file);
                    $error_msg = '操作失败!';
                }
            }

        }
        if($id > 0){
            $row = $wpdb->get_row("SELECT cou.course_title,cou.course_img,cou.const,cou.const,cou.is_enable,cou.coach_id,cou.course_start_time,cou.course_end_time,
                cou.created_time,cou.province,cou.city,cou.area,cou.address,cou.open_quota,cou.seize_quota,cou.course_type,cou.zone_id,cou.course_details,
                zm.zone_name,um.meta_value AS coach_real_name,cou.course_category_id,cou.duration,cou.admin_mobile  
                FROM {$wpdb->prefix}course AS cou 
                LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=cou.coach_id AND um.meta_key='user_real_name' 
                LEFT JOIN {$wpdb->prefix}zone_meta AS zm ON zm.id=cou.zone_id 
                WHERE cou.id='{$id}'", ARRAY_A);
        }
        $categoryArr = getCategory();
//        leo_dump($categoryArr);die;
        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加/编辑课程</h1>

            <div id="ajax-response">
                <span style="color: #2bc422"><?=$success_msg?></span>
                <span style="color: #c44e00"><?=$error_msg?></span>
            </div>

            <form method="post" action="" class="validate" novalidate="novalidate" enctype="multipart/form-data">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="5f6ea9ff44">
                <input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php">
                <table class="form-table">
                    <tbody>
                    <tr class="">
                        <th scope="row"><label for="course_title">课程名称 </label></th>
                        <td>
                            <input type="text" style="padding: 3px 5px;" name="course_title" id="course_title" value="<?=isset($row['course_title']) ? $row['course_title'] : ''?>">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="course_details">课程详情 </label></th>
                        <td>
                            <?php wp_editor( isset($row['course_details']) ? $row['course_details'] : '', 'course_details', $settings = array() ); ?>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="course_img">课程图片 </label></th>
                        <td>
                            <img src="<?=isset($row['course_img']) ? $row['course_img'] :''?>" alt="" style="height: 80px;">
                            <input type="file" name="course_img" id="course_img">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="const">课程价格 </label></th>
                        <td>
                            <input type="text" style="padding: 3px 5px;" name="const" id="const" value="<?=isset($row['const']) ? $row['const'] : 3000?>">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="course_category_id">教学类别 </label></th>
                        <td>
                            <select class="" name="course_category_id"  id="course_category_id">
                                <?php foreach ($categoryArr as $cgav){ ?>
                                    <option value="<?=$cgav['ID']?>" <?=isset($row['course_category_id']) && $cgav['ID'] == $row['course_category_id'] ? 'selected="selected"' : ''?>>
                                        <?=$cgav['post_title']?>
                                    </option>
                                <?php } ?>

                            </select>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="coach_id">授课教练 </label></th>
                        <td>
                            <select class="js-data-select-ajax" name="coach_id" style="width: 50%" data-action="get_base_coach_list" data-type="all">
                                <option value="<?=isset($row['coach_id']) ? $row['coach_id'] : 0?>" selected="selected">
                                    <?=!empty($row['coach_real_name']) ? unserialize($row['coach_real_name'])['real_name'] : ''?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="admin_mobile">管理员电话 </label></th>
                        <td>
                            <input type="text" style="padding: 3px 5px;" value="<?=isset($row['admin_mobile']) ? $row['admin_mobile'] : ''?>" name="admin_mobile" id="admin_mobile" placeholder="管理员电话">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="duration">课程时长(小时) </label></th>
                        <td>
                            <input type="text" style="padding: 3px 5px;" value="<?=isset($row['duration']) ? $row['duration'] : ''?>" name="duration" id="duration" placeholder="课程时长">
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="course_start_time">开课时间 </label></th>
                        <td>
                            <input type="text" style="max-width: 500px;" value="<?=isset($row['course_start_time']) ? $row['course_start_time'] : get_time('mysql')?>" name="course_start_time" class="layui-input date-picker y-m-d-h-m-s" readonly  id="course_start_time" placeholder="开课时间">
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="course_end_time">结课时间 </label></th>
                        <td>
                            <input type="text" style="max-width: 500px;" value="<?=isset($row['course_end_time'])  ? $row['course_end_time'] : get_time('mysql')?>" name="course_end_time" class="layui-input date-picker y-m-d-h-m-s" readonly  id="course_end_time" placeholder="待定">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="address">上课地址 </label></th>
                        <td>
                            <select name="province" id="province">
                                <option value="">请选择</option>
                            </select>
                            <select name="city" id="city">
                                <option value="">请选择</option>
                            </select>
                            <select name="area" id="area">
                                <option value="">请选择</option>
                            </select>
                            <input type="text" style="padding: 3px 5px;" name="address" value="<?=isset($row['address']) ? $row['address'] : ''?>">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="open_quota">开放名额 </label></th>
                        <td>
                            <input type="text" style="padding: 3px 5px;" name="open_quota" id="open_quota" value="<?=isset($row['open_quota']) ? $row['open_quota'] : 0?>">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="seize_quota">已抢占名额 </label></th>
                        <td>
                            <input type="text" style="padding: 3px 5px;" name="seize_quota" id="seize_quota" value="<?=isset($row['seize_quota']) ? $row['seize_quota'] : 0?>">
                        </td>
                    </tr>

                    <tr class="">
                        <th scope="row"><label for="zone_user_id">所属主体机构 </label></th>
                        <td>
                            <select class="js-data-select-ajax" name="zone_id" style="width: 50%" data-action="get_base_zone_list" data-type="all_base">
                                <option value="<?=isset($row['zone_id']) ? $row['zone_id'] : 0?>" selected="selected">
                                    <?=empty($row['zone_name']) ? '平台' :$row['zone_name']?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="course_type">课程类型 </label></th>
                        <td>
                            <label for="course_type_1">乐学乐分享  <input type="radio" checked="checked" id="course_type_1" name="course_type" value="1"></label>

                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="is_enable">状态 </label></th>
                        <td>
                            <label for="is_enable_1">正常  <input type="radio" <?=$row['is_enable'] == '1' || !isset($row['is_enable']) ? 'checked="checked"':''?> id="is_enable_1" name="is_enable" value="1"></label>
                            <label for="is_enable_2">禁用  <input type="radio" <?=$row['is_enable'] == '2' ? 'checked="checked"':''?> id="is_enable_2" name="is_enable" value="2"></label>

                        </td>
                    </tr>


                    </tbody>
                </table>


                <p class="submit"><input type="submit" name="createuser" id="createusersub" class="button button-primary" value="提交"></p>
            </form>
            <?php
            wp_register_script( 'student-languages',student_js_url.'validator/verify-ZH-CN.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-languages' );
            wp_localize_script('student-languages','verify_ZH',[
            ]);
            ?>
            <script>

                jQuery(document).ready(function($) {
                    layui.use(['laydate',], function(){
                        var laydate = layui.laydate;
                        //日期时间选择器
                        $('.date-picker').each(function(){
                            var id=$(this).attr('id');
                            var format='yyyy-MM-dd HH:mm';
                            var type='datetime';
                            if($(this).hasClass('y-m-d')){
                                format='yyyy-MM-dd';
                                type='date'
                            }else if($(this).hasClass('y-m-d-h-m-s')){
                                format='yyyy-MM-dd HH:mm:ss';
                                type='datetime';
                            }


                            laydate.render({
                                elem: '#'+id
                                ,type: type
                                ,format: format
                            });
                        })
                    })
                    initAddress('<?=isset($row['province'])?$row['province']:''?>',
                        '<?=isset($row['city'])?$row['city']:''?>',
                        '<?=isset($row['area'])?$row['area']:''?>');
                    function initAddress(province,city,area){
                        var provicone_html = '<option data-index="" value="">请选择</option>'
                        var city_html = '<option data-index="" value="">请选择</option>';
                        var area_html = '',selectedArea='';
                        $.each($.validationLayui.allArea.area,function(index,value){
                            if(province == value.value){
                                provicone_html += '<option data-index="'+index+'" selected="selected" value="'+value.value+'">'+value.value+'</option>';
                                $.each(value.childs,function (cityIndex,cityValue) {
                                    if(cityValue.value == city){
                                        city_html += '<option data-index="'+index+'_'+cityIndex+'" selected="selected" value="'+cityValue.value+'">'+cityValue.value+'</option>';
                                        $.each(cityValue.childs,function (areaIndex,areaValue) {
                                            selectedArea = areaValue.value == area ?'selected="selected"':'';
                                            area_html += '<option data-index="'+areaIndex+'" '+selectedArea+' value="'+areaValue.value+'">'+areaValue.value+'</option>';
                                        });
                                    }else{
                                        city_html += '<option data-index="'+index+'_'+cityIndex+'"  value="'+cityValue.value+'">'+cityValue.value+'</option>';
                                    }
                                });
                            }else{
                                provicone_html += '<option data-index="'+index+'"  value="'+value.value+'">'+value.value+'</option>';
                            }
                        });
                        $('#province').html(provicone_html);
                        $('#city').html(city_html);
                        $('#area').html(area_html);
                    }

                    $('#province').on('change', function () {
                        changeProvicone($(this));
                    });
                    function changeProvicone(_this){
                        var val = _this.find('option:selected').attr('data-index');
                        if(val > -1){
                            var city_html = '';
                            $.each($.validationLayui.allArea.area[val].childs,function (cindex,cvalue) {
                                city_html += '<option data-index="'+val+'_'+cindex+'" value="'+cvalue.value+'">'+cvalue.value+'</option>';
                            });
                            $('#city').html(city_html);
                            var area_html = '';
                            $.each($.validationLayui.allArea.area[val].childs[0].childs,function (aindex,avalue) {
                                area_html += '<option data-index="'+aindex+'" value="'+avalue.value+'">'+avalue.value+'</option>';
                            });
                            $('#area').html(area_html);
                        }else{
                            $('#city').html('');
                            $('#area').html('');
                        }
                    }
                    $('#city').on('change', function () {
                        changeCity($(this));
                    });
                    function changeCity(_this) {
                        var val = _this.find('option:selected').attr('data-index');
                        val = val.split('_');
                        if(val[0] > -1){
                            var area_html = '';
                            $.each($.validationLayui.allArea.area[val[0]].childs[val[1]].childs,function (aindex,avalue) {
                                area_html += '<option value="'+avalue.value+'">'+avalue.value+'</option>';
                            });
                            $('#area').html(area_html);
                        }else{
                            $('#area').html('');
                        }
                    }

                    $('#zone_type').on('change', function () {
                        var val = $(this).val();
                        $.ajax({
                            url : ajaxurl,
                            data : {'action':'getPowerListByType','val':val},
                            type : 'post',
                            dataType : 'json',
                            success : function (response) {
                                if(response['success']){
                                    var _html = '';
                                    $.each(response.data.data,function (i,v) {
                                        _html += "<label for='power_"+v['role_name']+"'><input id='power_"+v['role_name']+"' type='checkbox' name='power[]' value='"+v['id']+"'>"+v['role_name']+"</label>";
                                    })
                                    $('#power_td').html(_html);
                                }
                            },error : function () {

                            }
                        });
                    });
                });
            </script>
        </div>
        <?php
    }
    public function register_scripts(){

        switch ($_GET['page']){
            case 'course':
                wp_register_script('layui-js',match_js_url.'layui/layui.js');
                wp_enqueue_script( 'layui-js' );
                break;
            case 'course-add-course':
                wp_register_script( 'admin_layui_js',match_js_url.'layui/layui.js',array('jquery'), leo_match_version  );
                wp_enqueue_script( 'admin_layui_js' );
                wp_register_style( 'admin_layui_css',match_css_url.'layui.css','', leo_match_version  );
                wp_enqueue_style( 'admin_layui_css' );
                break;
        }
    }
}
new Course();