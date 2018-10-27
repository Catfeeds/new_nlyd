<?php
/*
Plugin Name: Application Interface 项目接口
Plugin URI: http://localhost/wordpress/
Description: 接口设置
Version: 1.0
Author: leo
Author URI: --
*/


if(!class_exists('myInterface')){
    class myInterface{


        public function __construct()
        {
            $this->main();
        }

        public function main(){

            require_once( leo_user_interface_path . 'Controller/class-default-ajax.php' );

            add_action('admin_menu',array($this,'add_submenu'));

            add_action('admin_enqueue_scripts', array($this,'scripts_default'));

            add_action( 'wp_ajax_saveInterface',array($this,'saveInterface'));
            add_action( 'wp_ajax_saveLogo',array($this,'saveLogo'));
            add_action( 'wp_ajax_saveBanner',array($this,'saveBanner'));
        }

        public function add_submenu(){

            add_submenu_page( 'edit.php?post_type=match', '时长配置', '时长配置', 'manage_options', 'match_default', array($this,'setting_match_default') );
            add_submenu_page( 'options-general.php', '接口设置', '接口设置', 'manage_options', 'interface', array($this,'my_submenu_page_display') );
            add_submenu_page( 'options-general.php', 'logo设置', 'logo设置', 'manage_options', 'logo', array($this,'my_submenu_page_logo') );
            add_submenu_page( 'options-general.php', 'banner设置', 'banner设置', 'manage_options', 'banner', array($this,'my_submenu_page_banner') );
            add_submenu_page( 'options-general.php', '默认配置', '默认配置', 'manage_options', 'default_setting', array($this,'my_submenu_page_setting') );
            add_submenu_page( 'options-general.php', '清除历史', '清除历史', 'administrator', 'clear_history', array($this,'my_clear_history') );

        }


        /**
         * 比赛时长默认配置
         */
        public function setting_match_default(){

            $match_project = get_option('match_project_use')['project_use'];
            $project_default = get_option('match_project_use')['project_default'];
            //print_r($match_project);
            $args = array(
                'post_type' => array('project'),
                'post_status' => array('publish'),
                'order' => 'ASC',
                'orderby' => 'menu_order',
            );
            $the_query = new WP_Query( $args );
            $lists = $the_query->posts;
            require_once( leo_user_interface_path . 'view/match_default.php' );
        }


        /**
         * 清除历史
         */
        public function my_clear_history(){
            require_once( leo_user_interface_path . 'view/clear_history.php' );
        }

        /**
         * 项目所有默认设置
         */
        public function my_submenu_page_setting(){
            require_once( leo_user_interface_path . 'view/setting.php' );
        }


        /**
         * 首页banner设置
         */
        public function my_submenu_page_banner(){
            require_once( leo_user_interface_path . 'view/banner.php' );
        }

        /**
         * logo设置
         */
        public function my_submenu_page_logo(){
            $logo_url = get_option('logo_url');
            require_once( leo_user_interface_path . 'view/logo.php' );
        }

        /**
         * 页面以及数据处理
         */
        public function my_submenu_page_display(){

            $interface_config = get_option('interface_config');

            $config = $this->getConfig();

            require_once( leo_user_interface_path . 'view/view.php' );
        }

        public function getConfig(){

            return array(
                            'smtp'=>array(
                                'title'=>'邮件',
                                'tag'=>array(
                                    array(
                                        'title'=>'SMTP服务器',
                                        'name'=>'host',
                                        'placeholder'=>'邮件发送所需SMTP服务器',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'SMTP服务器端口',
                                        'name'=>'port',
                                        'placeholder'=>'邮件发送所需SMTP服务器端口',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'SMTP服务器用户名',
                                        'name'=>'user_name',
                                        'placeholder'=>'邮件发送所需SMTP服务器帐号',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'SMTP服务器密码',
                                        'name'=>'user_pass',
                                        'placeholder'=>'邮件发送所需SMTP服务器密码',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'SMTP服务授权码',
                                        'name'=>'user_warrant',
                                        'placeholder'=>'邮件发送所需SMTP服务授权码',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'发件人email',
                                        'name'=>'from_email',
                                        'placeholder'=>'一般设置为服务器用户名',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'发件人名称',
                                        'name'=>'from_name',
                                        'placeholder'=>'邮件发送所需发件人名称',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'回复人email',
                                        'name'=>'reply_email',
                                        'placeholder'=>'留空则为发件人email',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'回复人名称',
                                        'name'=>'reply_name',
                                        'placeholder'=>'留空则为发件人名称',
                                        'type'=>'input'
                                    ),
                                )
                            ),
                            'sms'=>array(
                                'title'=>'短信',
                                'tag'=>array(
                                    array(
                                        'title'=>'短信网关',
                                        'name'=>'host',
                                        'placeholder'=>'发送短信所需网关url接口',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'短信KeyId',
                                        'name'=>'key',
                                        'placeholder'=>'发送短信所需API_KEY',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'短信KeySecret',
                                        'name'=>'secret',
                                        'placeholder'=>'发送短信所需API_Secret',
                                        'type'=>'input'
                                    ),
                                )
                            ),
                            'zfb'=>array(
                                'title'=>'支付宝',
                                'tag'=>array(
                                    array(
                                        'title'=>'支付宝网关',
                                        'name'=>'host',
                                        'placeholder'=>'支付宝调用所需url接口',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'支付宝APP_ID',
                                        'name'=>'api',
                                        'placeholder'=>'支付宝调用所需APP_ID',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'支付宝支付密钥',
                                        'name'=>'secret_key',
                                        'placeholder'=>'支付宝调用所需微信支付密钥',
                                        'type'=>'input'
                                    ),
                                )
                            ),
                            'wx'=>array(
                                'title'=>'微信',
                                'tag'=>array(
                                    array(
                                        'title'=>'微信网关',
                                        'name'=>'host',
                                        'placeholder'=>'微信调用所需url接口',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'微信APP_ID',
                                        'name'=>'api',
                                        'placeholder'=>'微信调用所需APP_ID',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'微信AppSecret',
                                        'name'=>'secret',
                                        'placeholder'=>'AppSecret(应用密钥)',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'微信支付商户号',
                                        'name'=>'merchant',
                                        'placeholder'=>'微信调用所需微信支付商户号',
                                        'type'=>'input'
                                    ),
                                    array(
                                        'title'=>'微信支付密钥',
                                        'name'=>'secret_key',
                                        'placeholder'=>'微信调用所需微信支付密钥',
                                        'type'=>'input'
                                    ),

                                )
                            ),
                            'login'=>array(
                                'title'=>'第三方登录',
                                'tag'=>array(
                                    array(
                                        'title'=>'微信登录',
                                        'name'=>'wx_login',
                                        'placeholder'=>'控制微信登录开启/关闭',
                                        'type'=>'checkbox'
                                    ),
                                    array(
                                        'title'=>'QQ登录',
                                        'name'=>'qq_login',
                                        'placeholder'=>'控制QQ登录开启/关闭',
                                        'type'=>'checkbox'
                                    ),
                                    array(
                                        'title'=>'其他登录',
                                        'name'=>'other_login',
                                        'placeholder'=>'控制其他第三方登录开启/关闭',
                                        'type'=>'checkbox'
                                    ),
                                )
                            ),
                        );
        }

        /**
         * 引入js/css
         */
        public function scripts_default(){

            $screen       = get_current_screen();
            $screen_id    = $screen ? $screen->id : '';

            if ( in_array( $screen_id, array('settings_page_interface') ) ) {

                //js
                wp_register_script( 'interface-js',plugins_url('/js/interface.js', __FILE__),array(), leo_user_interface_version  );
                wp_enqueue_script( 'interface-js' );

                //css
                wp_register_style( 'interface-css',plugins_url('/css/interface.css', __FILE__),array(), leo_user_interface_version  );
                wp_enqueue_style( 'interface-css' );

            }

        }

        /**
         * Ajax提交
         */
        public function saveInterface(){

            if(empty($_POST['action'])) wp_send_json_error('请求方法错误');

            if(update_option( 'interface_config', $_POST['interface'] )){

                wp_send_json_success('保存成功');

            }else{

                wp_send_json_error('保存失败');
            }
        }

        public function saveLogo(){
            if(empty($_POST['action'])) wp_send_json_error('请求方法错误');
            if(update_option( 'logo_url', $_POST['logo_url'] ) || update_option( 'match_project_default', $_POST['match_project_default'] )){

                wp_send_json_success('保存成功');

            }else{

                wp_send_json_error('保存失败');
            }
        }

        public function saveBanner(){
            if(empty($_POST['action'])) wp_send_json_error('请求方法错误');
            foreach ($_POST['index_banner_url'] as $k => $v){
                    if($v == false) unset($_POST['index_banner_url'][$k]);
            }
            if(update_option( 'index_banner_url', $_POST['index_banner_url'] )){

                wp_send_json_success('保存成功');

            }else{

                wp_send_json_error('保存失败');
            }
        }
    }


}
define( 'leo_user_interface_path', plugin_dir_path( __FILE__ ) );
define( 'leo_user_interface_version','v2.1.0.7' );//样式版本

new myInterface();