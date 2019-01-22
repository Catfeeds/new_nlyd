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

        $this->ajaxControll = new Student_Ajax();

        //添加短标签
        add_shortcode('directory-home',array($this,$action));
    }

    /**
     * PC名录
     */
    public function pc_index(){
        $dtype = 'reading';

//        $view = student_view_path.CONTROLLER.'/directory.php';
//        load_view_template($view);
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
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
        wp_register_style( 'my-student-home', student_css_url.'home-student.css' );
        wp_enqueue_style( 'my-student-home' );
        wp_register_style( 'my-student-directory', student_css_url.'directory/directory.css' ,array('my-student'));
        wp_enqueue_style( 'my-student-directory' );
    }
}