<?php

/**
 * 学生-教辅商城
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Shop
{

    private $action;
    public function __construct($action)
    {

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

//        if(isset($_GET['action'])){
//            $function = $_GET['action'];
//        }else {
//            $function = $_GET['action'] = 'index';
//        };
        //添加短标签
        $this->action = $action;
        add_shortcode('shop-home',array($this,$action));
    }

    public function index(){
        $view = student_view_path.'shop.php';
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
        if($this->action=='index'){//设置
            wp_register_script( 'student-swiper',student_js_url.'swiper/swiper-4.3.3.min.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-swiper' );
            wp_register_style( 'my-student-swiper', student_css_url.'swiper/swiper-4.3.3.min.css',array('my-student') );
            wp_enqueue_style( 'my-student-swiper' );
            wp_register_style( 'my-student-shop', student_css_url.'shop.css',array('my-student') );
            wp_enqueue_style( 'my-student-shop' );
        }
    }
}