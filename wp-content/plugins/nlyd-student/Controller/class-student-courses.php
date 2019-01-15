<?php

/**
 * 首页-名录
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16
 * Time: 14:38
 */
class Student_Directory
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
     * 名录
     */
    public function index(){
        $view = student_view_path.CONTROLLER.'/directory.php';
        load_view_template($view);
    }
    /**
     * 脑力健将名录
     */
     public function directoryPlayer(){
        $view = student_view_path.CONTROLLER.'/directory-player.php';
        load_view_template($view);

    }
        /**
     * 记忆水平认证名录
     */
     public function directoryRemember(){
        $view = student_view_path.CONTROLLER.'/directory-remember.php';
        load_view_template($view);

    }
        /**
     * 速读水平认证名录
     */
     public function directoryRead(){
        $view = student_view_path.CONTROLLER.'/directory-read.php';
        load_view_template($view);

    }
        /**
     * 心算水平认证名录
     */
     public function directoryCalculation(){
        $view = student_view_path.CONTROLLER.'/directory-calculation.php';
        load_view_template($view);

    }
    /**
     * 课程首页
     */
     public function course(){
        $view = student_view_path.CONTROLLER.'/course-center.php';
        load_view_template($view);
    }
    /**
     * 训练中心课程展示
     */
     public function cenerCourse(){
        $view = student_view_path.CONTROLLER.'/course-center-list.php';
        load_view_template($view);
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
        $view = student_view_path.CONTROLLER.'/course-sign.php';
        load_view_template($view);
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
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
        wp_register_style( 'my-student-home', student_css_url.'home-student.css' );
        wp_enqueue_style( 'my-student-home' );
        wp_register_style( 'my-student-directory', student_css_url.'directory/directory.css' );
        wp_enqueue_style( 'my-student-directory' );
      
        if(ACTION == 'course'){
            wp_register_script( 'student-swiper',student_js_url.'swiper/swiper-4.3.3.min.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-swiper' );
            wp_register_style( 'my-student-swiper', student_css_url.'swiper/swiper-4.3.3.min.css',array('my-student') );
            wp_enqueue_style( 'my-student-swiper' );
        }
        wp_register_style( 'my-student-course', student_css_url.'course/course.css' );
        wp_enqueue_style( 'my-student-course' );
    }
}