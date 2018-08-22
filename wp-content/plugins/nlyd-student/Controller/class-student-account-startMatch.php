<?php

/**
 * 学生-开始比赛
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Account_startMatch extends Student_Home
{
    public function __construct($shortCode)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        $function = 'index';
        if(isset($_GET['action']))$function = $_GET['action'];

        //添加短标签
        add_shortcode('student-account',array($this,$function));
    }

    /**
     * 列表
     */
    public function index(){



        $view = student_view_path.CONTROLLER.'/match.php';
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
        wp_register_style( 'my-student-match', student_css_url.'match.css',array('my-student') );
        wp_enqueue_style( 'my-student-match' );
    }
}