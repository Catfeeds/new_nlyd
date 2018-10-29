<?php

/**
 * 监赛官页面
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Supervisor extends Student_Home
{
    public function __construct($action)
    {

        parent::__construct();

        global $current_user;

        if(!in_array($current_user->roles[0],array('supervisor','administrator'))){
            $this->get_404('权限不够');
            return;
        }

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        //添加短标签
        add_shortcode('supervisor-home',array($this,$action));
    }

    /**
     * 首页
     */
    public function index(){

        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view);

    }

    /**
     * 监赛提交记录
     */
    public function logs(){

        global $wpdb,$current_user;
        $sql = "select * from {$wpdb->prefix}prison_match_log where supervisor_id = {$current_user->ID}";
        $rows = $wpdb->get_results($sql,ARRAY_A);
print_r($rows);
        $view = student_view_path.CONTROLLER.'/logs.php';
        load_view_template($view,$rows);
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){


        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
    }
}