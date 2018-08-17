<?php

/**
 * 学生-课程
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16
 * Time: 14:38
 */
class Student_Account_Course extends Student_Home
{

    public $ajaxControll;
    public function __construct($shortCode)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        
        if(isset($_GET['action'])){
            $function = $_GET['action'];
        }else {
            $function = $_GET['action'] = 'index';
        }

        $this->ajaxControll = new Student_Ajax();

        //添加短标签
        add_shortcode('student-account',array($this,$function));
    }



    /**
     * 列表
     */
    public function index(){

        // //获取教练分类
         $category = $this->ajaxControll->get_coach_category(false);

        // //获取我的教练列表
        // $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : $category[0]->ID;
        // global $current_user;
        // $coach_lists = $this->ajaxControll->get_coach_lists($category_id,'',false);

         $data = array('category'=>$category);
        
        $view = student_view_path.'course.php';
        load_view_template($view,$data);
    }
    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_script( 'student-Hammer',student_js_url.'Mobile/Hammer.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-Hammer' );
        wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-cookie' );
    }
}