<?php

/**
 * 学生登录页
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Logins
{
    
    public $action;
    public function __construct($action)
    {

        //引入当前页面css/js
        if($_SESSION['user_openid'] == false){
            if(is_user_logged_in()){
                //添加推广人
                if($_GET['referee_id'] > 0){
                    global $current_user,$wpdb;
                    //获取我的推广人上级
                    $referee_id = $wpdb->get_var("select referee_id from {$wpdb->prefix}users where ID = {$_POST['referee_id']}");
                    if(empty($current_user->data->referee_id) && $_POST['referee_id'] != $current_user->ID && $referee_id != $current_user->ID){
                        //添加推广人
                        $a = $wpdb->update($wpdb->prefix.'users',array('referee_id'=>$_GET['referee_id'],'referee_time'=>date_i18n('Y-m-d',get_time())),array('ID'=>$current_user->ID));
                        //var_dump($a);die;
                    }
                }

                wp_redirect(home_url('account'));
            }


            if(is_weixin() && !isset($_GET['access']) && !isset($_GET['login_type']) && $_GET['login_type'] != 'out' && ($_SERVER['SERVER_NAME'] == 'ydbeta.gjnlyd.com')){
                if($_GET['referee_id']){
                    $_SESSION['referee_id_wx'] = $_GET['referee_id'];
                    wp_redirect(home_url('weixin/webLogin/referee_id/'.$_GET['referee_id']));
                }else{
                    wp_redirect(home_url('weixin/webLogin'));
                }
                exit;
            }
            $this->action = $action;
        }
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        //添加短标签
        add_shortcode('student-login',array($this,$action));
    }

    /**
     * 判断是否是微信浏览器
     */
    /*public function is_weixin(){
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }*/

    public function index(){
        $setting = get_option('default_setting');
        $view = student_view_path.CONTROLLER.'/login.php';
        load_view_template($view,$setting);
    }
    public function bindPhone(){
        if(empty($_GET['access']) || empty($_GET['oid'])){
            echo __('参数错误', 'nlyd-student');
            exit;
        }
        $data = [
            'uid' => $_GET['uid'],
            'access' => $_GET['access'],
            'open' => $_GET['oid'],
        ];
        $view = student_view_path.CONTROLLER.'/bindPhone.php';
        load_view_template($view,$data);
    }
    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_style( 'my-student-login', student_css_url.'user.css',array('my-student') );
        wp_enqueue_style( 'my-student-login' );
        if($this->action=='' || $this->action=='index'){
            wp_register_script( 'student-user',student_js_url.'logins/user.js',array('jquery'), leo_student_version  ,true);
            wp_enqueue_script( 'student-user' );
            wp_localize_script('student-user','_user',[
                'get'=>__('获取验证码','nlyd-student'),
                'resend'=>__('重新发送','nlyd-student'),
                'fast'=>__('手机快速登录','nlyd-student'),
                'new'=>__('重置密码','nlyd-student'),
                'agree'=>__('我同意以上协议','nlyd-student'),
            ]);
        }
        if($this->action=='bindPhone'){
            wp_register_script( 'student-bindPhone',student_js_url.'logins/bindPhone.js',array('jquery'), leo_student_version  ,true);
            wp_enqueue_script( 'student-bindPhone' );
            wp_localize_script('student-bindPhone','_bindPhone',[
                'get'=>__('获取验证码','nlyd-student'),
                'resend'=>__('重新发送','nlyd-student'),
            ]);
        }
    }
}