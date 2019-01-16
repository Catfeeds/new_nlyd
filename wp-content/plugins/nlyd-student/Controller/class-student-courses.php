<?php

/**
 * 首页-名录
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

        
        if(isset($_GET['action'])){
            $function = $_GET['action'];
        }else {
            $function = $_GET['action'] = 'index';
        }

        //添加短标签
        add_shortcode('course-home',array($this,$action));
    }



    
    /**
     * 课程首页
     */
     public function index(){
        $view = student_view_path.CONTROLLER.'/course-center.php';
        load_view_template($view);
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

        $view = student_view_path.CONTROLLER.'/course-center-list.php';
        load_view_template($view,$data);
    }
    /**
     * 课程详情
     */
     public function courseDetail(){
        $view = student_view_path.CONTROLLER.'/course-detail.php';
        load_view_template($view);
    }
    /**
     * 课程报名
     */
     public function courseSign(){
         global $wpdb,$current_user;
         $sql = "select b.zone_city,a.course_title,a.const,a.is_enable,a.address
                  from {$wpdb->prefix}course a 
                  left join {$wpdb->prefix}zone_meta b on a.zone_id = b.user_id
                  where a.id = {$_GET['id']}
                  ";

         $row = $wpdb->get_row($sql,ARRAY_A);
         if(!empty($row)){
             $city_arr = str2arr($row['zone_city'],'-');
             if(!empty($city_arr[2])){
                 $city = rtrim($city_arr[1],'市').rtrim($city_arr[2],'区');
             }elseif ($city_arr[1] != '市辖区'){
                 $city = rtrim($city_arr[1],'市');
             }else{
                 $city = rtrim($city_arr[0],'市');
             }
             $row['city'] = $city;
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
        $view = student_view_path.CONTROLLER.'/course-end.php';
        load_view_template($view);
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
        wp_register_style( 'my-student-home', student_css_url.'home-student.css' );
        wp_enqueue_style( 'my-student-home' );
        wp_register_script( 'student-mobileSelect',student_js_url.'Mobile/mobileSelect.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-mobileSelect' );
        wp_localize_script('student-mobileSelect','_mobileSelect',[
            'sure'=>__('确认','nlyd-student'),
            'cancel'=>__('取消','nlyd-student')
        ]);
        wp_register_style( 'my-student-mobileSelect', student_css_url.'Mobile/mobileSelect.css',array('my-student') );
        wp_enqueue_style( 'my-student-mobileSelect' );
      
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