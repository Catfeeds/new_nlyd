<?php

/**
 * 监赛官页面
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Supervisor extends Student_Home
{
    public function __construct($action)
    {

        parent::__construct();

        global $current_user;

        if(!in_array($current_user->roles[0],array('supervisor','administrator'))){
            $this->get_404('权限不够');
            return;
        }

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        //添加短标签
        add_shortcode('supervisor-home',array($this,$action));
    }

    /**
     * 首页
     */
    public function index(){

        $data = array();
        global $wpdb,$current_user;

        if(isset($_GET['id'])){

            //获取当前时段所有比赛
            $sql_ = "select a.match_id id,b.post_title value from {$wpdb->prefix}match_meta_new a left join {$wpdb->prefix}posts b on a.match_id = b.ID order by id desc limit 0,15";
            //print_r($sql_);
            $rows = $wpdb->get_results($sql_,ARRAY_A);
            if(empty($rows)){
                $this->get_404(array('message'=>'当前暂无比赛','match_url'=>home_url('matchs/info/')));
                return;
            }
            $data['match_list'] = json_encode($rows);

            $sql = "select a.id,a.student_name,a.seat_number,a.evidence,a.`describe`,b.post_title match_title 
                    from {$wpdb->prefix}prison_match_log a
                    left join {$wpdb->prefix}posts b on a.match_id = b.ID
                    where a.id={$_GET['id']} and a.supervisor_id = {$current_user->ID} ";
            //print_r($sql);
            $row = $wpdb->get_row($sql,ARRAY_A);

            if(!empty($row)){
                if(!empty($row['evidence'])){
                    $row['evidence'] = json_decode($row['evidence'],true);
                }
                //print_r($row);
                $data['list'] = $row;
            }

        }else{

            //获取当前时段所有比赛
            $sql_ = "select a.match_id id,b.post_title value from {$wpdb->prefix}match_meta_new a left join {$wpdb->prefix}posts b on a.match_id = b.ID where a.match_status = 2";
            //print_r($sql_);
            $rows = $wpdb->get_results($sql_,ARRAY_A);
            if(empty($rows)){
                $this->get_404(array('message'=>'当前暂无比赛','match_url'=>home_url('matchs/info/')));
                return;
            }
            $data['match_list'] = json_encode($rows);
        }



        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view,$data);

    }

    /**
     * 监赛提交记录
     */
    public function logs(){

        global $wpdb,$current_user;
        $sql = "select a.id,a.created_time,a.student_name,a.match_more,a.seat_number,a.describe,b.post_title match_title,c.post_title project_title
                from {$wpdb->prefix}prison_match_log a
                 left join {$wpdb->prefix}posts b on a.match_id = b.ID
                 left join {$wpdb->prefix}posts c on a.project_id = c.ID
                 where supervisor_id = {$current_user->ID}
                 order by a.id desc 
                 ";
        //where supervisor_id = {$current_user->ID}
        //print_r($sql);
        $data['lists'] = $wpdb->get_results($sql,ARRAY_A);
        //print_r($data['lists']);
        $view = student_view_path.CONTROLLER.'/logs.php';
        load_view_template($view,$data);
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );

        if(ACTION == 'logs'){
            wp_register_style( 'my-student-address', student_css_url.'address.css',array('my-student') );
            wp_enqueue_style( 'my-student-address' );
        }
        if(ACTION == 'index'){
            wp_register_script( 'student-mobileSelect',student_js_url.'Mobile/mobileSelect.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-mobileSelect' );
            wp_localize_script('student-mobileSelect','_mobileSelect',[
                'sure'=>__('确认','nlyd-student'),
                'cancel'=>__('取消','nlyd-student')
            ]);
            wp_register_style( 'my-student-mobileSelect', student_css_url.'Mobile/mobileSelect.css',array('my-student') );
            wp_enqueue_style( 'my-student-mobileSelect' );
        }
    }
}