<?php
/*
Plugin Name: Application Fission 裂变中心
Plugin URI: http://localhost/wordpress/
Description: 后台裂变管理
Version: 1.0
Author: lx
Author URI: --
Text Domain: nlyd-fission
*/
if(!class_exists('FissionController')){

    class FissionController{
        public function __construct()
        {
            define( 'fission_path', plugin_dir_path( __FILE__ ) );
            define( 'fission_url', plugins_url('',__FILE__ ) );
//            define( 'leo_match_version','V2.1.3.1' );//样式版本

            define( 'fission_css_url', fission_url.'/Public/css/' );
            define( 'fission_js_url', fission_url.'/Public/js/' );
            define( 'fission_view_path', fission_path.'View/' );
            define( 'fission_controller_path', fission_path.'Controller/' );
            $this->main();
        }
        private function main(){
            add_action('admin_menu',array($this,'add_submenu'));
        }

        public function add_submenu(){

            if ( current_user_can( 'administrator' ) && !current_user_can( 'fission' ) ) {
                global $wp_roles;

                $role = 'fission';//权限名
                $wp_roles->add_cap('administrator', $role);
            }
            add_menu_page('裂变中心', '裂变中心', 'fission', 'fission',array($this,'fissionIndex'),'dashicons-businessman',99);
        }
        private function fissionIndex(){

        }
    }
    new FissionController();
}