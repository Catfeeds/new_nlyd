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

        $view = student_view_path.CONTROLLER.'/userCenter.php';
        load_view_template($view);
    }

    public function scripts_default(){


    }

}