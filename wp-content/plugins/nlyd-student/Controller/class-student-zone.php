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
     * 机构主页
     */
    public function index(){
        $row = $this->get_zone_row();
        if($row['user_status'] == 1){
            $day = date_i18n('Y年m月d日',strtotime('+1 year',$row['audit_time']));

        }
        print_r($row);
        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view,$data);

    }


    /*
     *机构主体信息页面
     */
    public function account(){
        global $user_info;
        $row = $this->get_zone_row();

        if(empty($row)){
            $this->get_404(array('message'=>'数据错误'));
            return;
        }
        $data['user_real_name'] = $user_info['user_real_name'];
        $data['row'] = $row;
        $view = student_view_path.CONTROLLER.'/account.php';
        load_view_template($view,$data);
    }

    /**
     * 获取机构信息
     */
    public function get_zone_row(){
        global $wpdb,$user_info;
        $sql = "select a.*,b.zone_type_name,c.user_mobile from {$wpdb->prefix}zone_meta a 
                left join {$wpdb->prefix}zone_type b on a.type_id = b.id 
                left join {$wpdb->prefix}users c on a.user_id = c.ID 
                where a.user_id = '{$user_info['user_id']}' ";
        //print_r($sql);
        $row = $wpdb->get_row($sql,ARRAY_A);
        return $row;
    }

    /**
     * 分支机构申请页面
     */
    public function apply(){

        global $wpdb,$current_user,$user_info;

        //获取所有机构
        $data['list'] = $wpdb->get_results("select id,zone_type_name,zone_type_alias from {$wpdb->prefix}zone_type where zone_type_status = 1 order by zone_sort asc",ARRAY_A);

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