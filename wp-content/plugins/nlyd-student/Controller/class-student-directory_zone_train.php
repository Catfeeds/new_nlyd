<?php
/**
 * 首页-训练中心名录
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/25
 * Time: 14:43
 */

class Student_Directory_Zone_Train
{

    public $template_dir;
    public function __construct()
    {
        $this->template_dir = student_view_path.'directory/';
        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));
        //添加短标签
        add_shortcode('directory_zone_train-home',array($this,'index'));
    }

    public function index(){
        global $wpdb;
        $view = $this->template_dir.'/directory-zone_pc.php';
        $type_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}zone_type WHERE zone_type_alias='trains'");
        $current_time = get_time('mysql');
        $last_time = $wpdb->get_var("SELECT MAX(audit_time) FROM {$wpdb->prefix}zone_meta WHERE type_id='{$type_id}' AND user_id>0 AND user_status=1 AND is_able=1 AND (term_time>'{$current_time}' OR term_time='')");
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379,1);
        $redis->auth('leo626');
        if(($data = $redis->get('zone_trains_directory'))){
            $data = json_decode($data, true);
            if($data['last_time'] == $last_time){
                $rows = $data['rows'];
            }
        }
        if(empty($rows)){
            $res = $wpdb->get_results("SELECT zm.zone_number,zm.bank_card_name,zm.chairman_id,zm.secretary_id,zm.zone_city,zm.zone_name,zt.zone_type_alias,zm.zone_match_type,zm.zone_number 
                   FROM {$wpdb->prefix}zone_meta AS zm 
                   LEFT JOIN {$wpdb->prefix}zone_type AS zt ON zt.id=zm.type_id 
                   WHERE zm.type_id='{$type_id}' AND zm.user_id>0 AND zm.user_status=1 AND zm.is_able=1 AND (zm.term_time>'{$current_time}' OR zm.term_time='')", ARRAY_A);
            $rows = [];
            $organizeClass = new Organize();
            foreach ($res as $re){
                $re['zone_title_name'] = $organizeClass->echoZoneName($re['zone_type_alias'], $re['zone_city'], $re['zone_name'], $re['zone_match_type'],$re['zone_number'], 'get', '#ffb536');
                $re['chairman_name'] = get_user_meta($re['chairman_id'], 'user_real_name', true)['real_name'];
                $re['secretary_name'] = get_user_meta($re['secretary_id'], 'user_real_name', true)['real_name'];
                $rows[] = $re;
            }
//            leo_dump($rows);die;
            $redis->setex('zone_trains_directory', 3600*24*30,json_encode(['last_time'=>$last_time,'rows'=>$rows]));
        }
        load_view_template($view, ['rows' => $rows]);
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_style( 'my-student-directory_pc', student_css_url.'directory/directory_pc.css',array('my-student') );
        wp_enqueue_style( 'my-student-directory_pc' );
    }
}