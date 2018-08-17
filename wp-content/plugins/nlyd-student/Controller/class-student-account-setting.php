<?php

/**
 * 学生-安全设置
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Account_Setting extends Student_Home
{


    public function __construct($shortCode)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        if(isset($_GET['action'])){
            $function = $_GET['action'];
        }else {
            $function = $_GET['action'] = 'index';
        };
        //添加短标签
        add_shortcode('student-account',array($this,$function));
    }

    public function index(){

        $view = student_view_path.'setting.php';
        load_view_template($view);
    }
    /**
    *
    *用户协议
    *
    **/
    public function userAgreement(){
        $view = student_view_path.'userAgreement.php';
        load_view_template($view);
    }
    /**
    *
    *隐私协议
    *
    **/
    public function privacyAgreement(){
        $view = student_view_path.'privacyAgreement.php';
        load_view_template($view);
    }
    /**
    *
    *建议反馈
    *
    **/
    public function suggest(){
        $view = student_view_path.'suggest.php';
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
        if($_GET['action']=='index'){//设置
            wp_register_style( 'my-student-setting', student_css_url.'setting.css',array('my-student') );
            wp_enqueue_style( 'my-student-setting' );
        }
        if($_GET['action']=='userAgreement' || $_GET['action']=='privacyAgreement'){//协议
            wp_register_style( 'my-student-agreement', student_css_url.'agreement.css',array('my-student') );
            wp_enqueue_style( 'my-student-agreement' );
        }
        if($_GET['action']=='suggest'){//协议
            wp_register_style( 'my-student-suggest', student_css_url.'suggest.css',array('my-student') );
            wp_enqueue_style( 'my-student-suggest' );
        }
    }
}