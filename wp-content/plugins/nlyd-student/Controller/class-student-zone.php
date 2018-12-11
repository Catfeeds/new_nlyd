<?php

/**
 * 学生-分支机构
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Zone extends Student_Home
{
    public function __construct($action)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        //添加短标签
        add_shortcode('zone-home',array($this,$action));
    }

    /**
     * 分支机构申请页面
     */
    public function apply(){

        global $wpdb,$current_user,$user_info;
        //获取事业管理员
        $user_real_name = get_user_meta($current_user->data->referee_id,'user_real_name')[0];
        if(!empty($user_real_name)){
            $data['referee_name'] = $user_real_name['real_name'];
        }
        //分中心负责人
        if(!empty($user_info['user_real_name'])){
            $data['director'] = $user_info['real_name'];
        }
        //分中心编号
        $total = $wpdb->get_var("select max(id) total from {$wpdb->prefix}zone_meta ");
        if($total < 8){
            $data['zone_num'] = 8;
        }else{
            $data['zone_num'] = $total+1;
        }

        $view = student_view_path.CONTROLLER.'/apply.php';

        load_view_template($view,$data);
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

    }

}