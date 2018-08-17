<?php

/**
 * 学生登录页
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Logins
{
    public function __construct($action)
    {

        if(is_user_logged_in()) wp_redirect(home_url('account'));

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));
var_dump(111);
        //添加短标签
        add_shortcode('student-login',array($this,$action));
    }

    public function index(){

        $view = student_view_path.'login.php';
        load_view_template($view);
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-cookie' );

        wp_register_style( 'my-student-login', student_css_url.'user.css',array('my-student') );
        wp_enqueue_style( 'my-student-login' );
    }
}