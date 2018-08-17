<?php

/**
 * 学生首页
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Index
{
    public function __construct($shortCode)
    {

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));
        if(isset($_GET['action'])){
            $function = $_GET['action'];
        }else {
            $function = $_GET['action'] = 'index';
        }
        //添加短标签
        add_shortcode($shortCode,array($this,'index'));
    }

    public function index(){
        $view = student_view_path.'index.php';
        load_view_template($view);

    }
    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

        wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-cookie' );
        wp_register_style( 'my-student-index', student_css_url.'index.css' );
        wp_enqueue_style( 'my-student-index' );

 
    }
}