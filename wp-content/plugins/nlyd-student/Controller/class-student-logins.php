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
    
    public $action;
    public function __construct($action)
    {

        if(is_user_logged_in()) wp_redirect(home_url('account'));

        if($this->is_weixin() && !isset($_GET['access']) && !isset($_GET['login_type']) && $_GET['login_type'] != 'out'){

            wp_redirect(home_url('weixin/webLogin'));
            exit;
        }
        $this->action = $action;
        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        //添加短标签
        add_shortcode('student-login',array($this,$action));
    }

    /**
     * 判断是否是微信浏览器
     */
    public function is_weixin(){
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }

    public function index(){
        $view = student_view_path.CONTROLLER.'/login.php';
        load_view_template($view);
    }
    public function bindPhone(){
        $data = [
            'uid' => $_GET['uid'],
            'access' => $_GET['access'],
            'open' => $_GET['oid'],
        ];
        $view = student_view_path.CONTROLLER.'/bindPhone.php';
        load_view_template($view,$data);
    }
    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-cookie' );
        wp_register_style( 'my-student-login', student_css_url.'user.css',array('my-student') );
        wp_enqueue_style( 'my-student-login' );
        if($this->action=='' || $this->action=='index'){
            wp_register_script( 'student-user',student_js_url.'logins/user.js',array('jquery'), leo_student_version  ,true);
            wp_enqueue_script( 'student-user' );
        }
        if($this->action=='bindPhone'){
            wp_register_script( 'student-bindPhone',student_js_url.'logins/bindPhone.js',array('jquery'), leo_student_version  ,true);
            wp_enqueue_script( 'student-bindPhone' );
        }
    }
}