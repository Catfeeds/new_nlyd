<?php

/**
 * 课程
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16
 * Time: 14:38
 */

class Student_Courses
{

    public $ajaxControll;
    public function __construct($action)
    {

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        //添加短标签
        add_shortcode('course-home',array($this,$action));
    }

    
    /**
     * 课程首页
     */
     public function index(){

         //$ip='14.106.228.94';
         $ip = GetIp();
         if($ip != '127.0.0.1'){
             $location = convertip($ip);
         }
         //print_r($location);
         if(preg_match('/省|市|区|县/',$location)){
             $city = $location;
         }else{
             $city = '请选择';
         }

         $view = student_view_path.CONTROLLER.'/course-center.php';
         load_view_template($view,array('city'=>$city));
    }

    /**
     * 训练中心课程展示
     */
     public function cenerCourse(){
        global $wpdb;
         //获取机构名字
         if(isset($_GET['id'])){
             $zone_nem = $wpdb->get_var("select zone_name from {$wpdb->prefix}zone_meta where user_id = {$_GET['id']} ");
            $data['zone_name'] = $zone_nem;
         }
         //判断课程状态
         global $wpdb,$current_user;
         $sql = "select a.id,count(c.id) entry_total,a.open_quota,a.is_enable,course_start_time,unix_timestamp(course_start_time) start_time,course_end_time,unix_timestamp(course_end_time) end_time
                from {$wpdb->prefix}course a 
                left join {$wpdb->prefix}order c on a.id = c.match_id and c.pay_status in (2,3,4)
                where a.zone_id = {$_GET['id']}
                group by a.id
                order by a.course_start_time desc ,a.is_enable desc";
         $rows =  $wpdb->get_results($sql,ARRAY_A);
         //print_r($sql);
         if(!empty($rows)){
             $time = get_time('mysql');
             $entry_is_true = 0;
             $match_is_true = 0;
             foreach ($rows as $k => $v){
                 if($v['is_enable'] != -4){
                     $is_enable = '';
                     if($v['start_time'] > 0){
                         if($time < $v['course_start_time']){
                             $is_enable = 1; //报名中
                             $entry_is_true += 1;
                         }
                         elseif ( $v['course_start_time'] <= $time && $time <= $v['course_end_time']){
                             $is_enable = 2; //授课中
                             $match_is_true += 1;
                         }
                         else{

                             $is_enable = -3;    //已结课
                         }
                         //print_r($v['id'].'===='.$is_enable);
                     }else{
                         if($v['entry_total'] < $v['open_quota']){
                             $is_enable = 1;
                             $entry_is_true += 1;
                         }
                         elseif ($v['entry_total'] >= $v['open_quota']){
                             $is_enable = -2;
                             $match_is_true += 1;
                         }
                     }
                     //print_r($is_enable);
                     if(!empty($is_enable)){
                         $a = $wpdb->update($wpdb->prefix.'course',array('is_enable'=>$is_enable),array('id'=>$v['id']));
                     }
                 }
             }
             if($entry_is_true>0){
                 $data['anchor'] = 1;
             }elseif ($match_is_true>0){
                 $data['anchor'] = 2;
             }else{
                 $data['anchor'] = 3;
             }
             $data['match_is_true'] = $match_is_true;
             $data['entry_is_true'] = $entry_is_true;
         }

        $view = student_view_path.CONTROLLER.'/course-center-list.php';
        load_view_template($view,$data);
    }

    /**
     * 课程详情
     */
     public function courseDetail(){

         global $wpdb,$current_user;
         $sql = "select a.id,a.course_title,a.coach_id,
                if(unix_timestamp(a.course_start_time)>0,date_format(a.course_start_time,'%Y-%m-%d %H:%i'),'待定') course_start_time,
                 a.open_quota,a.course_details,if(a.address != '',a.address,'-') address,a.const,b.zone_name,b.zone_number,b.zone_city,c.zone_type_name,
                 c.zone_type_alias,if(b.zone_match_type=1,'战队精英赛','城市赛') as match_type,d.post_title category_title
                 from {$wpdb->prefix}course a 
                 left join {$wpdb->prefix}zone_meta b on a.zone_id = b.user_id 
                 left join {$wpdb->prefix}zone_type c on b.type_id = c.id 
                 left join {$wpdb->prefix}posts d on a.course_category_id = d.ID 
                 where a.id = {$_GET['id']}
                 ";
         //print_r($sql);
         $row = $wpdb->get_row($sql,ARRAY_A);
         if(!empty($row)){
             $city_arr = str2arr($row['zone_city'],'-');
             if(!empty($city_arr[2])){
                 $city = rtrim($city_arr[1],'市').preg_replace('/区|县/','',$city_arr[2]);
             }elseif ($city_arr[1] != '市辖区'){
                 $city = rtrim($city_arr[1],'市');
             }else{
                 $city = rtrim($city_arr[0],'市');
             }
             $city = !empty($city) ? $city : '';
             $row['city'] = $city;
             if($row['zone_type_alias'] == 'match'){
                 $zone_title = $row['zone_name'].$city.$row['match_type'].'组委会';
             }
             else{
                 $zone_title = $row['zone_name'].$row['zone_type_name']."({$row['zone_number']})";
             }
             $row['zone_title'] = $zone_title;
         }
         if(!empty($row['coach_id'])){
             $user_name = get_user_meta($row['coach_id'],'user_real_name')[0]['real_name'];
             $row['user_name'] = $user_name;
         }
         //print_r($row);
         //获取报名人数
         $row['order_total'] = $wpdb->get_var("select count(*) total from {$wpdb->prefix}order where match_id = {$_GET['id']} and order_type = 3 and pay_status in(2,3,4)");
         //判断是否报名
         if($current_user->ID){
             $row['is_entered'] = $wpdb->get_var("select id from {$wpdb->prefix}order where user_id = {$current_user->ID} and match_id = {$_GET['id']} and order_type = 3 and pay_status in(2,3,4) ");
         }
         if($row['open_quota'] > 0 ){
             if($row['open_quota'] <= $row['order_total'] ){
                 $row['is_full'] = 'y';
             }
         }
         //print_r($row);
         $view = student_view_path.CONTROLLER.'/course-detail.php';
         load_view_template($view,$row);
    }
    /**
     * 课程报名
     */
     public function courseSign(){
         global $wpdb,$current_user;
         $sql = "select b.zone_city,a.course_title,a.const,a.coach_id,a.is_enable,a.address,if(unix_timestamp(a.course_start_time) > 1,a.course_start_time,'-') start_time,d.post_title category_title
                  from {$wpdb->prefix}course a 
                  left join {$wpdb->prefix}zone_meta b on a.zone_id = b.user_id
                  left join {$wpdb->prefix}posts d on a.course_category_id = d.ID 
                  where a.id = {$_GET['id']}
                  ";

         $row = $wpdb->get_row($sql,ARRAY_A);
         if(!empty($row)){
             $city_arr = str2arr($row['zone_city'],'-');
             if(!empty($city_arr[2])){
                 $city = rtrim($city_arr[1],'市').preg_replace('/区|县/','',$city_arr[2]);
             }elseif ($city_arr[1] != '市辖区'){
                 $city = rtrim($city_arr[1],'市');
             }else{
                 $city = rtrim($city_arr[0],'市');
             }
             $row['city'] = $city;
         }

         if(!empty($row['coach_id'])){
             $user_name = get_user_meta($row['coach_id'],'user_real_name')[0]['real_name'];
             $row['coach_name'] = $user_name;
         }

         if($current_user->ID){
             $user_name = get_user_meta($current_user->ID,'user_real_name')[0]['real_name'];
             $row['user_name'] = $user_name;
             $row['user_mobile'] = $current_user->data->user_mobile;
             $row['user_ID'] = $current_user->ID+10000000;
         }
        //print_r($row);
        $view = student_view_path.CONTROLLER.'/course-sign.php';
        load_view_template($view,$row);
    }
    /**
     * 课程报名成功
     */
     public function courseSignSuccess(){
        $view = student_view_path.CONTROLLER.'/course-signSuccess.php';
        load_view_template($view);
    }
    /**
     * 课程报名成功
     */
     public function courseEnd(){
         global $wpdb,$current_user;
         $sql = "select a.id,a.course_title,a.coach_id,a.course_start_time,a.open_quota,a.course_details,b.zone_name,b.zone_city,c.zone_type_name,
                 c.zone_type_alias,if(b.zone_match_type=1,'战队精英赛','城市赛') as match_type
                 from {$wpdb->prefix}course a 
                 left join {$wpdb->prefix}zone_meta b on a.zone_id = b.user_id 
                 left join {$wpdb->prefix}zone_type c on b.type_id = c.id 
                 where a.id = {$_GET['id']}
                 ";
         //print_r($sql);
         $row = $wpdb->get_row($sql,ARRAY_A);
         if(!empty($row)){
             $city_arr = str2arr($row['zone_city'],'-');
             if(!empty($city_arr[2])){
                 $city = rtrim($city_arr[1],'市').preg_replace('/区|县/','',$city_arr[2]);
             }elseif ($city_arr[1] != '市辖区'){
                 $city = rtrim($city_arr[1],'市');
             }else{
                 $city = rtrim($city_arr[0],'市');
             }
             $city = !empty($city) ? $city : '';
             $row['city'] = $city;
             if($row['zone_type_alias'] == 'match'){
                 $zone_title = $row['zone_name'].$city.$row['match_type'].'组委会';
             }
             else{
                 $title = 'IISC';
                 if($row['zone_type_alias'] == 'test'){
                     $title .= '水平';
                 }

                 $zone_title = $title.$city.$row['zone_type_name'];
             }
             $row['zone_title'] = $zone_title;
         }

        $view = student_view_path.CONTROLLER.'/course-end.php';
        load_view_template($view,$row);
    }
    /**
     * 课程学员查看
     */
     public function courseStudent(){
         global $wpdb,$current_user;
         $title = $wpdb->get_var("select course_title from {$wpdb->prefix}course where id = {$_GET['id']}");
         $view = student_view_path.CONTROLLER.'/course-student.php';
         load_view_template($view,array('course_title'=>$title));
    }
    /**
     * 商品详情
     */
     public function shopsDetail(){
        $view = student_view_path.CONTROLLER.'/shops-detail.php';
        load_view_template($view);
    }
    /**
     * 购物车
     */
     public function shopsCar(){
        $view = student_view_path.CONTROLLER.'/shops-car.php';
        load_view_template($view);
    }
    /**
     * 商品结算
     */
     public function shopsSettlement(){
        $view = student_view_path.CONTROLLER.'/shops-settlement.php';
        load_view_template($view);
    }
    /**
     * 支付成功
     */
     public function shopsPaySuccess(){
        $view = student_view_path.CONTROLLER.'/shops-paySuccess.php';
        load_view_template($view);
    }
    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );

        wp_register_script( 'student-mobileSelect',student_js_url.'Mobile/mobileSelect.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-mobileSelect' );
        wp_localize_script('student-mobileSelect','_mobileSelect',[
            'sure'=>__('确认','nlyd-student'),
            'cancel'=>__('取消','nlyd-student')
        ]);
        wp_register_style( 'my-student-mobileSelect', student_css_url.'Mobile/mobileSelect.css',array('my-student') );
        wp_enqueue_style( 'my-student-mobileSelect' );
        if (ACTION == 'index') {
            wp_register_style( 'my-student-home', student_css_url.'home-student.css' );
            wp_enqueue_style( 'my-student-home' );
        }
        if(ACTION == 'index' || ACTION == 'shopsDetail'){
            wp_register_script( 'student-swiper',student_js_url.'swiper/swiper-4.3.3.min.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-swiper' );
            wp_register_style( 'my-student-swiper', student_css_url.'swiper/swiper-4.3.3.min.css',array('my-student') );
            wp_enqueue_style( 'my-student-swiper' );
        }
        if (ACTION == 'shopsDetail' || ACTION == 'shopsCar' || ACTION == 'shopsSettlement' || ACTION == 'shopsPaySuccess') {
            wp_register_style( 'my-student-shop', student_css_url.'shops/shop.css',array('my-student') );
            wp_enqueue_style( 'my-student-shop' );
        }else{
            wp_register_style( 'my-student-course', student_css_url.'course/course.css' );
            wp_enqueue_style( 'my-student-course' );  
        }
 
    

    }
}