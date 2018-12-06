<?php
class Course{

    public function __construct()
    {
        //add_action( 'init', array($this,'add_wp_roles'));

        add_action( 'admin_menu', array($this,'register_teacher_menu_page') );
//        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
    }

    public function register_teacher_menu_page(){
        if ( current_user_can( 'administrator' ) && !current_user_can( 'goods' ) ) {
            global $wp_roles;
            $role = 'goods_course';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'goods_add_course';//权限名
            $wp_roles->add_cap('administrator', $role);

        }
        add_submenu_page('goods','课程列表','课程列表','goods_course','spread-course',array($this,'courseList'));
        add_submenu_page('goods','新增课程','新增课程','goods_add_course','spread-add_course',array($this,'addCourse'));
    }

    /**
     * 课程
     */
    public function courseList(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM `{$wpdb->prefix}course` LIMIT {$start},{$pageSize}", ARRAY_A);
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

        <?php
    }

    /**
     * 新增课程
     */
    public function addCourse(){

        if(is_post()){
            $msg = '';
            $success = '';
            $course_title = isset($_POST['course_title']) ? trim($_POST['course_title']) : '';
            $course_details = isset($_POST['course_details']) ? trim($_POST['course_details']) : '';
            $const = isset($_POST['const']) ? trim($_POST['const']) : '';
            $course_start_time = isset($_POST['course_start_time']) ? trim($_POST['course_start_time']) : '';
            $course_end_time = isset($_POST['course_end_time']) ? trim($_POST['course_end_time']) : '';
            $province = isset($_POST['province']) ? trim($_POST['province']) : '';
            $city = isset($_POST['city']) ? trim($_POST['city']) : '';
            $area = isset($_POST['area']) ? trim($_POST['area']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $open_quota = isset($_POST['open_quota']) ? intval($_POST['open_quota']) : 0;
            $teacher_id = isset($_POST['teacher_id']) ? intval($_POST['teacher_id']) : 0;
            $is_enable = isset($_POST['is_enable']) ? intval($_POST['is_enable']) : '';
            $course_type = isset($_POST['course_type']) ? intval($_POST['course_type']) : 1;
            if($course_title == '') $msg = '课程名称必填';
            if($is_enable != 1 && $is_enable !=2 ) $msg .= $msg == '' ? '请选择状态':'<br>请选择状态';
            if($teacher_id < 1) $msg .= ($msg == '' ? '请选择授课教练':'<br>请选择授课教练');
            if($msg == ''){
                global $wpdb;
                $insertData = [
                    'course_title'=>$course_title,
                    'course_details'=>$course_details,
                    'const'=>$const,
                    'is_enable'=>$is_enable,
                    'course_end_time'=>$course_end_time,
                    'course_start_time'=>$course_start_time,
                    'province'=>$province,
                    'city'=>$city,
                    'area'=>$area,
                    'address'=>$address,
                    'teacher_id'=>$teacher_id,
                    'open_quota'=>$open_quota,
                    'course_type'=>$course_type,
                    'created_time'=>get_time('mysql'),
                ];
                $wpdb->startTrans();
                $insertRes = $wpdb->insert($wpdb->prefix.'course', $insertData);
                if($insertRes){
                    $id = $wpdb->insert_id;
                    $upload_dir = wp_upload_dir();
                    $dir = '/course/'.$id.'/';
                    $file = saveIosFile($_FILES['course_img']['tmp_name'],$upload_dir['basedir'].$dir);
                    if($file){
                        $bool = $wpdb->update($wpdb->prefix.'course',['course_img'=>$upload_dir['baseurl'].$dir.$file],['id'=>$id]);
                        if($bool){
                            $success = '添加成功';
                            $wpdb->commit();
                        }else{
                            $msg = '添加失败';
                            if(is_file($upload_dir['basedir'].$dir.$file)) unlink($upload_dir['basedir'].$dir.$file);
                            $wpdb->rollback();
                        }
                    }else{
                        $success = '添加成功';
                        $wpdb->commit();
                    }
                }else{
                    $msg = '添加失败';
                }
            }

        }

        ?>
        <div class="wrap">
            <h1>添加课程</h1>

            <div id="ajax-response"></div>

            <p>新建课程。</p>
            <div style="color: #26a82b;"><?=$success?></div>
            <div style="color: #A90000;"><?=$msg?></div>
            <form method="post" name="createuser" id="createuser" class="validate" novalidate="novalidate" enctype="multipart/form-data">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="8e776847cc"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php"><table class="form-table">
                    <tbody>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="course_title">课程名称 <span class="description">（必填）</span></label></th>
                        <td><input name="course_title" type="text" id="course_title" value="" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="60"></td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="course_details">详细信息 <span class="description"></span></label></th>
                        <td>
                            <?php wp_editor( '', 'course_details', $settings = array() ); ?>
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="const">课程价格 <span class="description"></span></label></th>
                        <td><input name="const" type="text" id="const" value="0.00"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="course_img">图片 </label></th>
                        <td><input name="course_img" type="file" id="course_img" value=""></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="teacher_id">授课教练 </label></th>
                        <td>
                            <select class="js-data-select-ajax" name="teacher_id" style="width: 50%" data-action="search_teacher_list" data-type="teacher"></select>
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="course_start_time">开课时间 <span class="description"></span></label></th>
                        <td><input name="course_start_time" type="datetime-local" id="course_start_time" value=""></td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="course_end_time">结课时间 <span class="description"></span></label></th>
                        <td><input name="course_end_time" type="datetime-local" id="course_end_time" value=""></td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="open_quota">开放名额 <span class="description"></span></label></th>
                        <td><input name="open_quota" type="text" id="open_quota" value=""></td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="province">地区 <span class="description"></span></label></th>
                        <td>
                            <select name="province" id="province"></select>
                            <select name="city" id="city"></select>
                            <select name="area" id="area"></select>


                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="address">详细地址 <span class="description"></span></label></th>
                        <td>  <input type="text" id="address" name="address"></td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="course_type">课程类型 <span class="description"></span></label></th>
                        <td>
                            <select name="course_type" id="course_type">
                                <option value="1">乐学乐课程</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="is_enable">状态 </label></th>
                        <td>
                            <label><input name="is_enable" type="radio" value="1" checked="checked"> 开启</label>&ensp;&ensp;&ensp;
                            <label><input name="is_enable" type="radio" value="2"> 关闭</label>
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
                'required'=>__('必填项不能为空','nlyd-student'),
                'phone'=>__('手机号格式不正确','nlyd-student'),
                'email'=>__('邮箱格式不正确','nlyd-student'),
                'url'=>__('链接格式不正确','nlyd-student'),
                'number'=>__('只能填写数字','nlyd-student'),
                'date'=>__('日期格式不正确','nlyd-student'),
                'identity'=>__('请输入正确的身份证号','nlyd-student'),
                'phoneOrEmail'=>__('手机号或邮箱格式不正确','nlyd-student'),
                'password'=>__('密码必须是6位以上含字母及数字','nlyd-student'),
                'differPass'=>__('两次输入密码不一致','nlyd-student'),
                'chineseName'=>__('姓名格式不正确','nlyd-student'),
                'filterSqlStr'=>__('含有非法字符','nlyd-student'),
            ]);
            ?>
            <script>

                jQuery(document).ready(function($) {

                    //省市区三级联动
                    // console.log(provicone_html);
                    // $('#whereProvicone').html(where_provicone_html);
                    // $('#receiptProvicone').html(receipt_provicone_html);
                    var province_html = '';
                    var city_html = '';
                    var area_html = '';
                    $.each($.validationLayui.allArea.area,function(index,value){
                        province_html += '<option data-index="'+index+'" value="'+value.value+'">'+value.value+'</option>';
                        if(index == 0){
                          $.each(value.childs,function (cityIndex,cityValue) {
                              city_html += '<option data-index="'+index+'_'+cityIndex+'"  value="'+cityValue.value+'">'+cityValue.value+'</option>';
                              if(cityIndex == 0) {
                                  $.each(cityValue.childs, function (areaIndex, areaValue) {
                                      area_html += '<option data-index="' + areaIndex + '" value="' + areaValue.value + '">' + areaValue.value + '</option>';
                                  });
                              }
                           });
                        }

                    });
                    $('#province').html(province_html);
                    $('#city').html(city_html);
                    $('#area').html(area_html);


                    $('#province').on('change', function () {
                        var val = $(this).find('option:selected').attr('data-index');
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
                    });


                    $('#city').on('change', function () {
                        var val = $(this).find('option:selected').attr('data-index');
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
                    });



                })

            </script>
        </div>
        <?php
    }
}
new Course();