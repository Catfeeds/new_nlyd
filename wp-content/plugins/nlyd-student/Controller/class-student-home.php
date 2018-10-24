<?php

/**
 * 学生端公用父类
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Home
{
    public function __construct()
    {
//        wp_logout();
//        die;
        if(!is_user_logged_in()){

            $_SESSION['redirect_url'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }

        if(CONTROLLER != 'account'){
            //判断是否是管理员操作面板和是否登录
            if(!is_user_logged_in()){

                if(is_weixin() && !isset($_GET['access']) && !isset($_GET['login_type']) && $_GET['login_type'] != 'out'){

                    wp_redirect(home_url('weixin/webLogin'));
                    exit;
                }

                wp_redirect(home_url('logins'));
            }
        }elseif ((CONTROLLER == 'account' && ACTION != 'index')){

            //判断是否是管理员操作面板和是否登录
            if(!is_user_logged_in()){

                if($this->is_weixin() && !isset($_GET['access']) && !isset($_GET['login_type']) && $_GET['login_type'] != 'out'){

                    wp_redirect(home_url('weixin/webLogin'));
                    exit;
                }

                wp_redirect(home_url('logins'));
            }
        }

        $this->get_user_info();
    }

    //获取用户信息
    public function get_user_info(){

        global $current_user,$wpdb,$user_info;
        /*if(isset($_SESSION['user_info'])){
            $user_info = $_SESSION['user_info'];
            //var_dump($user_info);
        }else{*/

            $rows = $wpdb->get_results("SELECT * FROM {$wpdb->usermeta} WHERE user_id = {$current_user->ID} and meta_key in('nickname','user_head','user_address','user_real_name','real_ID','user_ID_Card','user_ID','user_gender','user_nationality','user_nationality_pic','user_nationality_short','user_birthday','user_coin_code') ",ARRAY_A);
            $user_info = array_column($rows,'meta_value','meta_key');
            //print_r($user_info);
            $user_level = get_the_author_meta('user_level',$current_user->ID);
            //print_r($user_level);
            if($user_level == 0){
                $user_info['user_type'] = __('学 员', 'nlyd-student');
            }elseif ($user_level == 10){
                $user_info['user_type'] = __('管理员', 'nlyd-student');
            }elseif ($user_level == 7){
                $user_info['user_type'] = __('教练', 'nlyd-student');
            }
            $user_info['user_id'] = $current_user->ID;
            $user_info['user_head'] = isset($user_info['user_head']) ? $user_info['user_head'] : student_css_url.'image/nlyd.png';
            $user_info['user_mobile'] = !empty($current_user->user_mobile) ? $current_user->user_mobile : '';
            $user_info['user_email'] = !empty($current_user->user_email) ? $current_user->user_email : '';
            $user_info['user_email'] = !empty($current_user->user_email) ? $current_user->user_email : '';
            $user_info['contact'] = !empty($current_user->user_mobile) ? hideStar($current_user->user_mobile) : hideStar($current_user->user_email);

            $user_info['qq_union_id'] = !empty($current_user->qq_union_id) ? $current_user->qq_union_id : '';
            $user_info['weChat_openid'] = !empty($current_user->weChat_openid) ? $current_user->weChat_openid : '';

            $user_info['user_address'] = isset($user_info['user_address']) ? unserialize($user_info['user_address']) : '';
            $user_info['user_real_name'] = isset($user_info['user_real_name']) ? unserialize($user_info['user_real_name']) : '';
            $user_info['user_ID_Card'] = isset($user_info['user_ID_Card']) ? unserialize($user_info['user_ID_Card']) : '';
            $user_info['real_ID'] = isset($user_info['user_real_name']['real_ID']) ? hideStar($user_info['user_real_name']['real_ID']) : '';
            $user_info['user_coin_code'] = isset($user_info['user_coin_code']) ? unserialize($user_info['user_coin_code']) : '';

            if(!empty($user_info['user_real_name']['real_type'])){
                switch ($user_info['user_real_name']['real_type']){
                    case 'sf':
                        $text = __('身份证', 'nlyd-student');
                        break;
                    case 'jg':
                        $text = __('军官证', 'nlyd-student');
                        break;
                    case 'hz':
                        $text = __('护照', 'nlyd-student');
                        break;
                    case 'tb':
                        $text = __('台胞证', 'nlyd-student');
                        break;
                    case 'ga':
                        $text = __('港澳证', 'nlyd-student');
                        break;
                }
                $user_info['user_real_name']['real_type_c'] = $text;
            }
            //print_r($user_info);
            //$_SESSION['user_info'] = $user_info;
        //}

        /*global $current_user;
        print_r($current_user);*/
    }

    public function get_404($tag){
        $view = leo_student_public_view.'my-404.php';
        if(!is_array($tag)){
            $data['message'] = $tag;
        }else{
            $data = $tag;
        }
        load_view_template($view,$data);
    }

}