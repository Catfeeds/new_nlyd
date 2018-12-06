<?php
//组织主体控制器
class Organize{
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_order_menu_page') );
//        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
    }
    private function register_order_menu_page(){

    }
}