<?php
/*
Plugin Name: Application Spread 推广功能
Plugin URI: http://localhost/wordpress/
Description: 后台推广管理
Version: 1.0
Author: lx
Author URI: --
Text Domain: nlyd-spread
*/

if(!class_exists('SpreadController')){

    class SpreadController{

        public $post_type;
        public $match;

        public function __construct($post_type)
        {
            define( 'leo_spread_path', plugin_dir_path( __FILE__ ) );
            define( 'leo_spread_url', plugins_url('',__FILE__ ) );
            define( 'leo_spread_version','V2.1.1.1' );//样式版本

            define( 'spread_css_url', leo_spread_url.'/Public/css/' );
            define( 'spread_js_url', leo_spread_url.'/Public/js/' );
            define( 'spread_view_path', leo_spread_path.'View/' );
            define( 'spread_controller_path', leo_spread_path.'Controller/' );
            $this->post_type = $post_type;
            $this->main();
        }

        private function main(){
            //设置
            require_once spread_controller_path.'class-setting.php';


            require_once spread_controller_path.'class-spread.php';

            require_once spread_controller_path.'class-spread-ajax.php';
        }








    }

    if(isset($_GET['post'])){
        $row = get_post($_GET['post']);
        if(!empty($row)){
            $post_type = $row->post_type;
        }
    }elseif (isset($_GET['post_type'])){
        $post_type = $_GET['post_type'];
    }else{
        $post_type = '';
    }

    new SpreadController($post_type);
}
