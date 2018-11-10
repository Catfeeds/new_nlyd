<?php
class Grading
{
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_order_menu_page') );
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));

    }

    public function register_order_menu_page(){

//        if ( current_user_can( 'administrator' ) && !current_user_can( 'edit.php?post_type=grading' ) ) {
//            global $wp_roles;
//
//            $role = 'feedback';//权限名
//            $wp_roles->add_cap('administrator', $role);
//
//            $role = 'feedback_intro';//权限名
//            $wp_roles->add_cap('administrator', $role);
//
//        }

//        add_submenu_page('edit.php?post_type=grading','查看详情','查看详情','feedback_intro','feedback-intro',array($this,'intro'));
    }
    /**
     * 引入当前页面css/js
     */
    public function register_scripts(){

    }
}