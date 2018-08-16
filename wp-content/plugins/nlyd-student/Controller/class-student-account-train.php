<?php

/**
 * 学生-我的训练
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Account_Train extends Student_Home
{
    public function __construct($shortCode)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        //添加短标签
        add_shortcode('student-account',array($this,'index'));
    }

    public function index(){

        $view = student_view_path.'train.php';
        load_view_template($view);
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-cookie' );

        wp_register_style( 'my-student-match', student_css_url.'match.css',array('my-student') );
        wp_enqueue_style( 'my-student-match' );
    }
}