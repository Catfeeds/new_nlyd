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
//            define( 'leo_match_version','V2.1.7.4' );//样式版本

            define( 'fission_css_url', fission_url.'/Public/css/' );
            define( 'fission_js_url', fission_url.'/Public/js/' );
            define( 'fission_view_path', fission_path.'View/' );
            define( 'fission_controller_path', fission_path.'Controller/' );
            $this->main();
        }
        private function main(){
            add_action('admin_menu',array($this,'add_submenu'));

            //引入主体组织文件
            require_once fission_path.'Controller/class-organize.php';

            //引入收益文件
            require_once fission_path.'Controller/class-spread.php';

            //引入课程文件
            require_once fission_path.'Controller/class-course.php';

            //引入数据统计文件
            require_once fission_path.'Controller/class-statistics.php';

            //引入aiax操作文件
            require_once fission_path.'Controller/class-fission-ajax.php';
        }

        public function add_submenu(){
            if ( current_user_can( 'administrator' ) && !current_user_can( 'fission' ) ) {
                global $wp_roles;
                $role = 'fission';//权限名
                $wp_roles->add_cap('administrator', $role);
            }

            add_menu_page('机构管理', '机构管理', 'fission', 'fission',array(new Organize(true),'organizeList'),'dashicons-businessman',100);
        }
        public function fissionIndex(){

        }
    }
    new FissionController();
}