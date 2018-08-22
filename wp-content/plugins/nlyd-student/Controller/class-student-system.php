<?php

/**
 * 首页-体系标准
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16
 * Time: 14:38
 */
class Student_System
{
    private $action;
    public $ajaxControll;
    public function __construct($action)
    {

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        
//        if(isset($_GET['action'])){
//            $function = $_GET['action'];
//        }else {
//            $function = $_GET['action'] = 'index';
//        }

        $this->ajaxControll = new Student_Ajax();
        $this->action = $action;
        //添加短标签
        add_shortcode('system-home',array($this,$action));
    }



    /**
     * 体系标准
     */
    public function index(){    
        $view = student_view_path.CONTROLLER.'/system.php';
        load_view_template($view);
    }
        /**
     * 体系标准
     */
     public function system(){
        $view = student_view_path.CONTROLLER.'/system.php';
        load_view_template($view);

    }
    /**
     * 课程体系
     */
     public function systemCourse(){
        $view = student_view_path.CONTROLLER.'/system-course.php';
        load_view_template($view);

    }
    /**
     * 师资体系
     */
     public function systemTeacher(){
        $view = student_view_path.CONTROLLER.'/system-teacher.php';
        load_view_template($view);

    }
    /**
     * 赛事体系
     */
     public function systemMatch(){
        $view = student_view_path.CONTROLLER.'/system-match.php';
        load_view_template($view);

    }
    /**
     * 测评体系
     */
     public function systemTest(){
        $view = student_view_path.CONTROLLER.'/system-test.php';
        load_view_template($view);

    }

    /**
     * 合作联系
     */
    public function concatUs(){
        $view = student_view_path.CONTROLLER.'/concatUs.php';
        load_view_template($view);
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-cookie' );
        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
        
        wp_register_style( 'my-student-system', student_css_url.'system/system.css' );
        wp_enqueue_style( 'my-student-system' );

        if($this->action == 'concatUs'){
            wp_register_style( 'my-student-concatUs', student_css_url.'concatUs/concatUs.css' );
            wp_enqueue_style( 'my-student-concatUs' );
        }
    }
}