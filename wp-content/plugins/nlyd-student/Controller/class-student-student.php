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

        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view);
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