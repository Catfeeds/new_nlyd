<?php

/**
 * 首页-体系标准
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16
 * Time: 14:38
 */
class Student_System
{
    private $action;
    public $ajaxControll;
    public function __construct($action)
    {
        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));
        $this->ajaxControll = new Student_Ajax();
        $this->action = $action;
        //添加短标签
        add_shortcode('system-home',array($this,$action));
    }



    /**
     * 体系标准
     */
    public function index(){    
        $view = student_view_path.CONTROLLER.'/system.php';
        load_view_template($view);
    }
        /**
     * 体系标准
     */
     public function system(){
        $view = student_view_path.CONTROLLER.'/system.php';
        load_view_template($view);

    }
    /**
     * 课程体系
     */
     public function systemCourse(){
        $view = student_view_path.CONTROLLER.'/system-course.php';
        load_view_template($view);

    }
    /**
     * 师资体系
     */
     public function systemTeacher(){
        $view = student_view_path.CONTROLLER.'/system-teacher.php';
        load_view_template($view);

    }
    /**
     * 赛事体系
     */
     public function systemMatch(){
        $view = student_view_path.CONTROLLER.'/system-match.php';
        load_view_template($view);

    }
    /**
     * 测评体系
     */
     public function systemTest(){
        $view = student_view_path.CONTROLLER.'/system-test.php';
        load_view_template($view);

    }
    /**
     * 合作联系
     */
    public function concatUs(){
        global $wpdb,$current_user;
        //获取所有机构列表
        $rows = $wpdb->get_results("select * from {$wpdb->prefix}zone_type where zone_type_status = 1 order by zone_sort asc",ARRAY_A);
        if(!empty($rows)){
            foreach ($rows as $k => $v){
                //获取是否有
                $result = $wpdb->get_results("select id,user_status from {$wpdb->prefix}zone_meta where apply_id = {$current_user->ID} and type_id = {$v['id']} and user_status in (-1,-2) ",ARRAY_A);
                if(!empty($result)){
                    foreach ($result as $x){
                        if($x['user_status'] == -1){
                            $rows[$k]['user_status'] = $x['user_status'];
                            $rows[$k]['zone_id'] = $x['id'];
                            break;
                        }
                        if($x['user_status'] == -2){
                            $rows[$k]['user_status'] = $x['user_status'];
                            $rows[$k]['zone_id'] = $x['id'];
                            continue;
                        }
                    }
                }
                //print_r($result);
            }
        }
        $row['list'] = $rows;

        $view = student_view_path.CONTROLLER.'/concatUS.php';
        load_view_template($view,$row);
    }
    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
        
        wp_register_style( 'my-student-system', student_css_url.'system/system.css' );
        wp_enqueue_style( 'my-student-system' );

        if($this->action == 'concatUs'){
            wp_register_style( 'my-student-concatUS', student_css_url.'concatUS/concatUS.css' );
            wp_enqueue_style( 'my-student-concatUS' );
        }
    }
}