<?php
/*
Plugin Name: Application Home 项目前端
Plugin URI: http://localhost/wordpress/
Description: 前端功能集合
Version: 1.0
Author: leo
Author URI: --
*/


//判断插件是否启用
if(!class_exists('HomeController')){

    class HomeController{

        public function __construct()
        {

            define( 'leo_home_path', plugin_dir_path( __FILE__ ) );
            define( 'leo_home_url', plugins_url('',__FILE__ ) );
            define( 'leo_home_version','1.0' );//样式版本

            define( 'home_css_url', leo_home_url.'/Public/css/' );
            define( 'home_js_url', leo_home_url.'/Public/js/' );
            define( 'home_view_path', leo_home_path.'View/' );
            define( 'home_controller_path', leo_home_path.'Controller/' );

            if (session_id() === '') {session_start();}

            //引入相关操作文件
            $this->main();


            //配置自己的重写规则
            //add_action( 'init', array($this,'custom_rewrite_basic'),10,0);

            //配置自己的重写参数
            //add_action( 'init', array($this,'custom_rewrite_tag'),10,0);

            //配置自己的重写模版
            //add_action('template_redirect', array($this,'custom_rewrite_template'));
        }


        private function main(){

            //引入ajax操作文件
            //include_once(leo_home_path.'Controller/class-home-ajax.php');
        }



        /**
         * 添加URL重定向规则
         */
        public function custom_rewrite_basic(){

            add_rewrite_rule('home\/(.*)$','index.php?pagename=home&action=$matches[2]','top');
        }

        /**
         * 添加URL重定向参数
         */
        function custom_rewrite_tag() {

            //add_rewrite_tag('%action%', '([^&]+)');
            add_rewrite_tag('%tag%', '([^&]+)');

        }



        /**
         *添加URL重定向页面模版
         */
        public function custom_rewrite_template(){


            //引入学生端公用css/js
            add_action('wp_enqueue_scripts', array($this,'scripts_default'));

            //引入模板
            $this->load_rewrite_template();

        }

        /**
         * 引入url重定向模板
         */
        public function load_rewrite_template(){
            global $wp_query,$wpdb,$current_user;

            if($current_user->ID){
                //获取用户即将开赛的比赛信息
                $sql = "select a.match_id,a.match_start_time from {$wpdb->prefix}match_meta a 
                left join {$wpdb->prefix}order b on a.match_id = b.match_id
                WHERE a.match_status = -2 AND a.match_start_time > NOW() AND b.user_id = {$current_user->ID} AND pay_status = 2 
                ORDER BY match_start_time asc limit 1
                ";
                $row = $wpdb->get_row($sql,ARRAY_A);

                if(!empty($row)){
                    $this->wait_match['match_start_time'] = strtotime($row['match_start_time'])-time();
                    $this->wait_match['match_url'] = home_url('home/account/matchList?action=matching&match_id='.$row['match_id']);
                    $this->wait_match['waiting_url'] = home_url('home/account/matchList?action=matchWaitting&match_id='.$row['match_id']);
                    $this->wait_match['match_id'] = $row['match_id'];
                }
            }

            if(empty($wp_query->query['pagename'])) return;

            $path_info = str2arr($wp_query->query['pagename'],'/');

            if(count($path_info) == 1){
                $path_info[1] =  'index';
            }
            if(isset($wp_query->query['tag']) && !empty($wp_query->query['tag'])){
                $path_info[2] = $wp_query->query['tag'];
                $action = '_'.ucfirst($path_info[2]);
            }else{
                $action = '';
            }
            $shortCode = join('-',$path_info);

            $class_path = leo_home_path.'/Controller/class-'.$shortCode.'.php';

            if(is_file($class_path)){

                include_once (leo_home_path.'/Controller/class-home-home.php');
                
                include_once ($class_path);
                $class = ucfirst($path_info[0]).'_'.ucfirst($path_info[1]).$action;
                //print_r($class);
                if(class_exists($class)){
                    new $class($shortCode);
                }
            }
            return;
            /*else{

                //跳转到404
                status_header(400);
                include_once (get_404_template());
            }*/

        }

        /**
         * 默认公用js/css引入
         */
        public function scripts_default(){
            /*if(is_home()){
                wp_register_style( 'my-home-home', home_css_url.'home-home.css' );
                wp_enqueue_style( 'my-home-home' );
                wp_register_script( 'home-swiper',home_js_url.'swiper/swiper-4.3.3.min.js',array('jquery'), leo_home_version  );
                wp_enqueue_script( 'home-swiper' );
                wp_register_style( 'my-home-swiper', home_css_url.'swiper/swiper-4.3.3.min.css',array('my-home') );
                wp_enqueue_style( 'my-home-swiper' );
                wp_register_style( 'my-home-userCenter', home_css_url.'userCenter.css',array('my-home') );
                wp_enqueue_style( 'my-home-userCenter' );
            }
            

            wp_register_script( 'home-cookie',home_js_url.'cookie.url.config.js',array('jquery'), leo_home_version  );
            wp_enqueue_script( 'home-cookie' );
            //引入layui
            wp_register_script( 'home-layui',home_js_url.'layui/layui.js',array('jquery'), leo_home_version  );
            wp_enqueue_script( 'home-layui' );
            // 表单验证语言包
            wp_register_script( 'home-languages',home_js_url.'validator/verify-ZH-CN.js',array('jquery'), leo_home_version  );
            wp_enqueue_script( 'home-languages' );
            // //引入表单验证
            // wp_register_script( 'home-validator',home_js_url.'validator/validform.min.js',array('jquery'), leo_home_version  );
            // wp_enqueue_script( 'home-validator' );
            //序列化form表单Json对象
            wp_register_script( 'home-serialize-object',home_js_url.'jquery.serialize-object.js',array('jquery'), leo_home_version  );
            wp_enqueue_script( 'home-serialize-object' );

            wp_register_script( 'home-common',home_js_url.'homeCommon.js',array('jquery'), leo_home_version  );
            wp_enqueue_script( 'home-common' );

            wp_register_style( 'my-home', home_css_url.'index.css',array('style'));
            wp_enqueue_style( 'my-home' );*/


            ?>
            <script>window.admin_ajax  = '<?= admin_url('admin-ajax.php' );?>';</script>
            <script>window.plugins_url  = '<?= plugins_url('',dirname(__FILE__));?>';</script>
            <script>window.home_url  = '<?= home_url();?>';</script>
            <script>window.wait_match  = '<?= !empty($this->wait_match) ? json_encode($this->wait_match) : '';?>';</script>
            <?php
        }




        /**
         * 页面添加
         * @param $data 页面设置参数
         * @param int $post_parent  父级ID
         */
        public function ashu_add_page($data,$post_parent=0){

            $allPages = get_pages();//获取所有页面
            $allPostName = array();

            if(!empty($allPages)){
                foreach ($allPages as $val){
                    $allPostName[] = $val->post_name;
                }
            }
            if(!in_array($data['post_name'],$allPostName)){

                $new_page_id = wp_insert_post(
                    array(
                        'post_title' => $data['post_title'],
                        'post_type'     => 'page',
                        'post_name'  => $data['post_name'],
                        'post_content' => $data['post_content'],
                        'post_status' => 'publish',
                        'post_author' => 1,
                        'post_parent' => $post_parent
                    )
                );

                if($new_page_id ){
                    if(!empty($data['child-page'])){
                        foreach ($data['child-page'] as $child ){
                            $this->ashu_add_page($child,$new_page_id);
                        }
                    }
                }
            }

        }

    }
}
new HomeController();