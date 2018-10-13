<?php

/**
 * 首页-训练模块
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16
 * Time: 14:38
 */
class Student_Trains extends Student_Home
{
    private $action;
    public $ajaxControll;
    public function __construct($action)
    {

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        parent::__construct();


        //添加短标签
        add_shortcode('train-home',array($this,$action));
    }



    /**
     * 首页
     */
    public function index(){

        //获取所有比赛类型
        $args = array(
            'post_type' => array('genre'),
            'post_status' => array('publish'),
            'order' => 'DESC',
            'orderby' => 'ID',
        );
        $the_query = new WP_Query( $args );

        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view,array('list'=>$the_query->posts));
    }

    public function lists(){
        if(empty($_GET['id'])) $this->get_404('参数错误');

        $args = array(
            'post_type' => array('match-category'),
            'post_status' => array('publish'),
            'post_parent'=>$_GET['id'],
            'order' => 'DESC',
            'orderby' => 'ID',
        );
        $the_query = new WP_Query( $args );
        $ids = arr2str(array_column((array)$the_query->posts,'ID'));
        //print_r($ids);
        global $wpdb;
        $sql = "SELECT ID,post_title FROM {$wpdb->prefix}posts WHERE post_parent in($ids)";
        $rows = $wpdb->get_results($sql);
        if(!empty($rows)){
            $list = $rows;
        }else{
            $list = $the_query->posts;
        }

        $view = student_view_path.CONTROLLER.'/lists.php';
        load_view_template($view,array('list'=>$list));
    }

    /**
     * 答题页面
     */
    public function answer(){


        $view = student_view_path.CONTROLLER.'/answer.php';
        //load_view_template($view,array('list'=>$list));

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