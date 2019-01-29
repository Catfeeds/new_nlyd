<?php

/**
 * 学生端公用父类
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Student
{
    public function __construct($action)
    {

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        //添加短标签
        add_shortcode('student-index',array($this,$action));
    }

    public function index(){
        global $wpdb;
        //获取推荐课程
        $sql = "select a.id,a.course_title,c.zone_city
                from {$wpdb->prefix}course a 
                left join {$wpdb->prefix}course_type b on a.course_type = b.id
                left join {$wpdb->prefix}zone_meta c on a.zone_id = c.user_id
                where a.is_enable = 1 
                order by a.id desc 
                limit 4
               ";
        print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($rows)){
            foreach ($rows as $k => $val){

                if(!empty($val['zone_city'])){
                    //获取城市
                    $city_arr = str2arr($val['zone_city'],'-');
                    if(!empty($city_arr[2])){
                        $city = rtrim($city_arr[1],'市').preg_replace('/区|县/','',$city_arr[2]);
                    }elseif ($city_arr[1] != '市辖区'){
                        $city = rtrim($city_arr[1],'市');
                    }else{
                        $city = rtrim($city_arr[0],'市');
                    }
                    $rows[$k]['zone_city'] = $city;
                }
            }
            $data['course_list'] = $rows;
        }

        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view,$data);
    }

    public function scripts_default(){

        wp_register_style( 'my-student-home', student_css_url.'home-student.css' );
        wp_enqueue_style( 'my-student-home' );
        wp_register_script( 'student-swiper',student_js_url.'swiper/swiper-4.3.3.min.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-swiper' );
        wp_register_style( 'my-student-swiper', student_css_url.'swiper/swiper-4.3.3.min.css',array('my-student') );
        wp_enqueue_style( 'my-student-swiper' );
        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
    }

}