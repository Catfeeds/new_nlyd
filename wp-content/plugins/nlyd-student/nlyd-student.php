<?php
/*
Plugin Name: Application Student 项目学生端
Plugin URI: http://localhost/wordpress/
Description: 学生端功能集合
Version: 1.0
Author: leo
Author URI: --
*/


//判断插件是否启用
if(!class_exists('StudentController')){

    class StudentController{

        public $model = 'student';
        public $project = 'nlyd-student';

        public $wait_match = array();
        public function __construct()
        {

            //项目默认路径
            define( 'leo_student_path', PLUGINS_PATH.$this->project.'/' );
            define( 'leo_student_url', plugins_url($this->project ) );
            define( 'leo_student_version','2.0' );

            define( 'student_css_url', leo_student_url.'/Public/css/' );
            define( 'student_js_url', leo_student_url.'/Public/js/' );

            if (session_id() === '') {session_start();}

            //引入相关操作文件
            $this->main();

            //配置自己的重写规则
            add_action( 'init', array($this,'custom_rewrite_basic'),10,0);

            //配置自己的重写参数
            add_action( 'init', array($this,'custom_rewrite_tag'),10,0);

            //配置自己的重写模版
            add_action('template_redirect', array($this,'custom_rewrite_template'));
        }


        private function main(){

            /*//引入配置文件
            if(is_file(leo_student_path.'conf/config.php')){

                $config = include(leo_student_path.'conf/config.php');

                //判断是否需要页面生成
                if(!empty($config['page'])){

                    $this->ashu_add_page($config['page']);

                }
            }*/

            //引入学生端公用css/js
            add_action('wp_enqueue_scripts', array($this,'scripts_default'));

            //引入ajax操作文件
            include_once(leo_student_path.'Controller/class-student-ajax.php');
        }



        /**
         * 添加URL重定向规则
         */
        public function custom_rewrite_basic(){

            //add_rewrite_rule('(.?.+?)\/(.*)$','index.php?pagename=$matches[1]&action=$matches[2]','top');
            //add_rewrite_rule('(.?.+?)\/account(/(.*))?/?$','index.php?pagename=student/account&tag=$matches[3]','top');
            add_rewrite_rule('(.?.+?)\/(.?.+?)(\/.*)?$','index.php?pagename=$matches[1]&action=$matches[2]&tag=$matches[3]','top');
        }

        /**
         * 添加URL重定向参数
         */
        function custom_rewrite_tag() {

            add_rewrite_tag('%pagename%', '([^&]+)');
            add_rewrite_tag('%action%', '([^&]+)');
            add_rewrite_tag('%tag%', '([^&]+)');

        }



        /**
         *添加URL重定向页面模版
         */
        public function custom_rewrite_template(){

            global $wp_query;
            $query = $wp_query->query;
            //var_dump($query);
            if(empty($query['pagename']) ){
                return;
            }
            $action = empty($query['action']) ? 'index' : $query['action'];

            //定义model
            define('MODEL',$this->model);

            //定义控制器
            define('CONTROLLER',$query['pagename']);
            //$GLOBALS['controller'] = $query['pagename'];

            //定义方法
            //$GLOBALS['action'] = $query['action'];
            define('ACTION',$action);

            //项目默认路径
            define( 'student_view_path', leo_student_path.'View/'.CONTROLLER.'/' );
            define( 'leo_student_left_path', leo_student_path.'View/' );
            define( 'student_controller_path', leo_student_path.'Controller/' );


            if(!empty($query['tag'])){
                parseUrl(trim($query['tag'],'/'));
            }

            //引入模板
            $this->load_rewrite_template();

        }

        /**
         * 引入url重定向模板
         */
        public function load_rewrite_template(){

            global $wpdb,$current_user;

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
                    $this->wait_match['match_url'] = home_url('matchs/matching/match_id/'.$row['match_id']);
                    $this->wait_match['waiting_url'] = home_url('matchs/matchWaitting/match_id='.$row['match_id']);
                    $this->wait_match['match_id'] = $row['match_id'];
                }
            }
            $class_path = leo_student_path.'Controller/class-'.MODEL.'-'.CONTROLLER.'.php';
            //var_dump($class_path);
            include_once (leo_student_path.'Controller/class-student-home.php');
            if(is_file($class_path)){
                include_once ($class_path);
                $class = ucfirst(MODEL).'_'.ucfirst(CONTROLLER);
                //var_dump($class);die;
                if(class_exists($class)){
                    //var_dump(ACTION);
                    new $class(ACTION);
                }
            }
            return;
        }

        /**
         * 默认公用js/css引入
         */
        public function scripts_default(){

            if(is_home()){
                wp_register_style( 'my-student-home', student_css_url.'home-student.css' );
                wp_enqueue_style( 'my-student-home' );
                wp_register_script( 'student-swiper',student_js_url.'swiper/swiper-4.3.3.min.js',array('jquery'), leo_student_version  );
                wp_enqueue_script( 'student-swiper' );
                wp_register_style( 'my-student-swiper', student_css_url.'swiper/swiper-4.3.3.min.css',array('my-student') );
                wp_enqueue_style( 'my-student-swiper' );
                wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
                wp_enqueue_style( 'my-student-userCenter' );
            }

            wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-cookie' );
            //引入layui
            wp_register_script( 'student-layui',student_js_url.'layui/layui.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-layui' );
            // 表单验证语言包
            wp_register_script( 'student-languages',student_js_url.'validator/verify-ZH-CN.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-languages' );
            // //引入表单验证
            // wp_register_script( 'student-validator',student_js_url.'validator/validform.min.js',array('jquery'), leo_student_version  );
            // wp_enqueue_script( 'student-validator' );
            //序列化form表单Json对象
            wp_register_script( 'student-serialize-object',student_js_url.'jquery.serialize-object.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-serialize-object' );

            wp_register_script( 'student-common',student_js_url.'studentCommon.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-common' );

            wp_register_style( 'my-student', student_css_url.'index.css',array('style'));
            wp_enqueue_style( 'my-student' );


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
new StudentController();