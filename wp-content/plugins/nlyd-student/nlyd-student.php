<?php
/*
Plugin Name: Application Student 项目学生端
Plugin URI: http://localhost/wordpress/
Description: 学生端功能集合
Version: 1.0
Author: leo
Author URI: --
Text Domain: nlyd-student
*/

//load_plugin_textdomain( 'nlyd-student', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
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
            define( 'leo_student_version','VV2.1.4.9' );

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

            //用户语言
            add_filter( 'locale', array($this,'admin_in_english_locale') );
        }

        public function admin_in_english_locale($locale){

            if ( !is_admin() ) {
                global $current_user;

                if(!empty($current_user)){
                    return get_user_meta($current_user->ID,'locale',true);
                }else{
                    if($_COOKIE['user_language']){
                        $cookie = $_COOKIE['user_language'];
//                        setcookie('user_language', $cookie, time()+3600*24*30,'/');
                        return $cookie;
                    }else{
                        return 'zh_CN';
                    }
                }

            }
            return $locale;
        }


        private function main(){

            // 屏蔽后台页脚 WordPress 版权及版本号
            function change_footer_admin () {return '';}
            add_filter('admin_footer_text', 'change_footer_admin', 9999);
            function change_footer_version() {return '';}
            add_filter( 'update_footer', 'change_footer_version', 9999);

            //引入配置文件
            register_activation_hook(__FILE__, array($this,'plugin_activation_cretable'));
            //$this->plugin_activation_cretable();

            //引入学生端公用css/js
            add_action('wp_enqueue_scripts', array($this,'scripts_default'),10,2);

            //登录时执行
            add_action('wp_login',array( $this, 'logging_in' ));

            add_action('wp_head',array($this,'is_abnormal_login'));

            //引入ajax操作文件
            include_once(leo_student_path.'Controller/class-student-ajax.php');

            //引入conf下functions
            include_once(leo_student_path.'conf/functions.php');
        }


        public function logging_in($user_login){
            $user = get_user_by( 'login', $user_login );
            update_user_meta($user->ID,'user_session_id',session_id());
        }

        //插件启动时加载
        public function plugin_activation_cretable(){

            //页面配置
            if(is_file(leo_student_path.'conf/config.php')){

                $config = include_once(leo_student_path.'conf/config.php');
                //判断是否需要页面生成
                if(!empty($config['page'])){

                    $this->ashu_add_page($config['page']);

                }
            }

            //数据表配置
            if(is_file(leo_student_path.'conf/create_table.php')){
                include_once(leo_student_path.'conf/create_table.php');
            }

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
            define( 'student_view_path', leo_student_path.'View/' );
            define( 'leo_student_public_view', leo_student_path.'View/public/' );
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
                left join {$wpdb->prefix}posts c on a.match_id = c.ID
                WHERE a.match_status = -2 AND a.match_start_time >= NOW() AND b.user_id = {$current_user->ID} AND pay_status in(2,3,4) 
                ORDER BY match_start_time asc limit 1
                ";
                //print_r($sql);
                $row = $wpdb->get_row($sql,ARRAY_A);

                if(empty($row)){
                    $sql = "select a.match_id,a.match_start_time from {$wpdb->prefix}match_meta_new a 
                    left join {$wpdb->prefix}order b on a.match_id = b.match_id
                    left join {$wpdb->prefix}posts c on a.match_id = c.ID
                    WHERE a.match_status = -2 AND a.match_start_time >= NOW() AND b.user_id = {$current_user->ID} AND pay_status in(2,3,4) 
                    ORDER BY match_start_time asc limit 1";
                    //print_r($sql);
                    $row = $wpdb->get_row($sql,ARRAY_A);
                }

                if(!empty($row)){   
                    $this->wait_match['match_start_time'] = strtotime($row['match_start_time'])-get_time();
                    $this->wait_match['match_url'] = home_url('matchs/matching/match_id/'.$row['match_id']);
                    $this->wait_match['waiting_url'] = home_url('matchs/matchWaitting/match_id/'.$row['match_id']);
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

            if (!is_front_page()) {
                wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
                wp_enqueue_script( 'student-cookie' );
                //序列化form表单Json对象
                wp_register_script( 'student-serialize-object',student_js_url.'jquery.serialize-object.js',array('jquery'), leo_student_version  );
                wp_enqueue_script( 'student-serialize-object' );
                //手势操作
                wp_register_script( 'student-alloy_finger',student_js_url.'Mobile/alloy_finger.js',array('jquery'), leo_student_version  );
                wp_enqueue_script( 'student-alloy_finger' );
                //引入layui
                wp_register_script( 'student-layui-home',student_js_url.'layui/layui.js',array('jquery'), leo_student_version  );
                wp_enqueue_script( 'student-layui-home' );
                wp_localize_script('student-layui-home','layUI',[
                    'complete'=>__('已加载全部数据','nlyd-student'),
                    'more'=>__('加载更多','nlyd-student'),
                ]);
                // 表单验证语言包
                
                wp_register_script( 'student-languages',student_js_url.'validator/verify-ZH-CN.js',array('jquery'), leo_student_version  );
                wp_enqueue_script( 'student-languages' );
                wp_localize_script('student-languages','verify_ZH',[
                    'required'=>__('必填项不能为空','nlyd-student'),
                    'phone'=>__('手机号格式不正确','nlyd-student'),
                    'email'=>__('邮箱格式不正确','nlyd-student'),
                    'url'=>__('链接格式不正确','nlyd-student'),
                    'number'=>__('只能填写数字','nlyd-student'),
                    'date'=>__('日期格式不正确','nlyd-student'),
                    'identity'=>__('请输入正确的身份证号','nlyd-student'),
                    'phoneOrEmail'=>__('手机号或邮箱格式不正确','nlyd-student'),
                    'password'=>__('密码必须是6位以上含字母及数字','nlyd-student'),
                    'differPass'=>__('两次输入密码不一致','nlyd-student'),
                    'chineseName'=>__('姓名格式不正确','nlyd-student'),
                    'filterSqlStr'=>__('含有非法字符','nlyd-student'),
                ]);

                wp_register_script( 'student-common',student_js_url.'studentCommon.js',array('jquery'), leo_student_version  );
                wp_enqueue_script( 'student-common' );
                wp_localize_script('student-common','common',[
                    'disabled'=>__('当前功能暂未开放，敬请期待','nlyd-student'),
                    'share'=>__('请使用浏览器自带分享功能','nlyd-student'),
                    'click'=>__('点击右上角分享给好友或朋友圈','nlyd-student'),
                    'slow'=>__('网络质量差,请重试','nlyd-student'),
                ]);
                //引入layui
                wp_register_style( 'my-layui-css-home', student_css_url.'layui.css',array('style'),leo_student_version);
                wp_enqueue_style( 'my-layui-css-home' );

                wp_register_style( 'my-student', student_css_url.'index.css',array('style'), leo_student_version);
                wp_enqueue_style( 'my-student' );
                // //新闻列表css
                // wp_register_style( 'my-student-news-list', student_css_url.'news/news-list.css',array('my-student') );
                // wp_enqueue_style( 'my-student-news-list' );
                // //新闻详情css
                // wp_register_style( 'my-student-news-detail', student_css_url.'news/news-detail.css',array('my-student') );
                // wp_enqueue_style( 'my-student-news-detail' );
            }


            $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            $type = 'other';
            //分别进行判断
            if(strpos($agent, 'iphone') || strpos($agent, 'ipad'))
            { ?>
                <script>
                    function isWeiXin(){
                        var ua = window.navigator.userAgent.toLowerCase();
                        if(ua.match(/MicroMessenger/i) == 'micromessenger'){
                            return true;
                        }else{
                            return false;
                        }
                    }
                    function isSafari() {
                        var u=navigator.userAgent
                        //Safari                      Chrome          //火狐傲游Chrome    //百度UC.360,sougou,
                        if (/Safari/.test(u) && !/Chrome/.test(u) && !/iOS/.test(u) && !/rowser/.test(u) && !/baidu/.test(u)) {
                            return true;
                        }else{
                            return false;
                        }
                    }
                    if(!isWeiXin()){
                        if(!isSafari()){
                            // alert('<?=__('请使用微信或Safari浏览器打开', 'nlyd-student')?>')
                            // window.history.back(-1);
                        }
                    }
                </script>


            <?php
            }
            elseif(strpos($agent, 'android'))
            { ?>
                <script>
                    function isWeiXin(){
                        var ua = window.navigator.userAgent.toLowerCase();
                        if(ua.match(/MicroMessenger/i) == 'micromessenger'){
                            return true;
                        }else{
                            return false;
                        }
                    }
                    if(!isWeiXin()){
                        if(!window.chrome){
                            // alert('<?=__('请使用微信或谷歌浏览器打开', 'nlyd-student')?>')
                            // window.location.href="https://a.app.qq.com/o/simple.jsp?pkgname=com.android.chrome";
                        }
                    }
                </script>

            <?php } ?>

            <script>window.admin_ajax  = '<?= admin_url('admin-ajax.php' );?>';</script>
            <script>window.plugins_url  = '<?= plugins_url('',dirname(__FILE__));?>';</script>
            <script>window.home_url  = '<?= home_url();?>';</script>
            <script>window.wait_match  = '<?= !empty($this->wait_match) ? json_encode($this->wait_match) : '';?>';</script>
            <?php
        }

        /**
         * 判断是否异地登录
         */
        public function is_abnormal_login(){
            $setting = get_option('default_setting');

            if($setting['default_abnormal_login'] == 1){

                if(is_user_logged_in() && !is_admin()){
                    global $current_user;
                    $session_id = get_user_meta($current_user->ID,'user_session_id')[0];
                    /*var_dump($session_id);
                    var_dump(session_id());*/
                    //die;
                    if($session_id != session_id()){?>
                        <script>
                            jQuery(function($)  {
                                $.alerts('<?=__('账号异地登录,即将退出', 'nlyd-student')?><br/><?=__('请及时修改密码', 'nlyd-student')?>');
                                $.ajax({
                                    type: "POST",
                                    url: window.admin_ajax,
                                    data: {'action':'user_logout','new_date':new Date().getTime()},
                                    dataType:'json',
                                    timeout:3000,
                                    success: function(data, textStatus, jqXHR){
                                        // console.log(data)
                                        if(data.success){
                                            if(data.data.url){
                                                setTimeout(function(){
                                                    window.location.href=data.data.url
                                                },3000)
                                            }
                                        }else{//登陆失败。记录登录时间
                                        }


                                    },
                                    error:function (XMLHttpRequest, textStatus, errorThrown) {

                                        // 通常 textStatus 和 errorThrown 之中
                                        // 只有一个会包含信息
                                        // 调用本次AJAX请求时传递的options参数
                                        // console.log(XMLHttpRequest, textStatus, errorThrown)
                                    }
                                });
                            })

                        </script>
                        <?php
                    }
                }
            }
        }



        /**
         * 页面添加
         * @param $data 页面设置参数
         * @param int $post_parent  父级ID
         */
        public function ashu_add_page($data,$post_parent=0){

            global $wpdb,$current_user;
            foreach ($data as $val ){
                $post_id = $wpdb->get_var("select ID from {$wpdb->prefix}posts where post_name = '{$val['post_name']}' ");
                //var_dump($post_id);
                if(empty($post_id)){
                    $arr = array(
                        'post_title' => $val['post_title'],
                        'post_type'     => 'page',
                        'post_name'  => $val['post_name'],
                        'post_content' => $val['post_content'],
                        'post_status' => 'publish',
                        'post_author' => $current_user->ID,
                    );

                    $new_page_id = wp_insert_post($arr);
                    //var_dump($new_page_id);
                }else{
                    //var_dump($post_id);
                }
            }
            //die;
        }

    }
}
new StudentController();