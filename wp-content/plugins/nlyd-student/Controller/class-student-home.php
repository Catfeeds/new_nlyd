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
        if(CONTROLLER != 'account'){
            //判断是否是管理员操作面板和是否登录
            if(!is_user_logged_in()){

                wp_redirect(home_url('logins'));
            }
        }elseif (CONTROLLER == 'account' && ACTION != 'index'){

            //判断是否是管理员操作面板和是否登录
            if(!is_user_logged_in()){

                wp_redirect(home_url('logins'));
            }
        }

        $this->get_user_info();
    }

    //获取用户信息
    public function get_user_info(){

        global $current_user,$wpdb,$user_info;
        if(isset($_SESSION['user_info'])){
            $user_info = $_SESSION['user_info'];
        }else{

            $rows = $wpdb->get_results("SELECT * FROM {$wpdb->usermeta} WHERE user_id = {$current_user->ID}",ARRAY_A);
            $user_info = array_column($rows,'meta_value','meta_key');
            //print_r($user_info);
            $user_level = get_the_author_meta('user_level',$current_user->ID);
            if($user_level == 0){
                $user_info['user_type'] = '学 员';
            }elseif ($user_level == 10){
                $user_info['user_type'] = '管理员';
            }elseif ($user_level == 7){
                $user_info['user_type'] = '教练';
            }
            $user_info['user_id'] = $current_user->ID;
            $user_info['user_head'] = isset($user_info['user_head']) ? $user_info['user_head'] : student_css_url.'image/nlyd.png';
            $user_info['contact'] = !empty($current_user->user_mobile) ? hideStar($current_user->user_mobile) : hideStar($current_user->user_email);

            $user_info['user_address'] = isset($user_info['user_address']) ? unserialize($user_info['user_address']) : '';
            $user_info['user_real_name'] = isset($user_info['user_real_name']) ? unserialize($user_info['user_real_name']) : '';
            $user_info['user_ID_Card'] = isset($user_info['user_ID_Card']) ? unserialize($user_info['user_ID_Card']) : '';
            $user_info['real_ID'] = isset($user_info['user_real_name']['real_ID']) ? hideStar($user_info['user_real_name']['real_ID']) : '';

            if(!empty($user_info['user_real_name']['real_type'])){
                switch ($user_info['user_real_name']['real_type']){
                    case 'sf':
                        $text = '身份证';
                        break;
                    case 'jg':
                        $text = '军官证';
                        break;
                    case 'hz':
                        $text = '护照';
                        break;
                    case 'tb':
                        $text = '台胞证';
                        break;
                    case 'ga':
                        $text = '港澳证';
                        break;
                }
                $user_info['user_real_name']['real_type_c'] = $text;
            }

            $_SESSION['user_info'] = $user_info;
        }

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