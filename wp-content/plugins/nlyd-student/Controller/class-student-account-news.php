<?php

/**
 * 首页-最新资讯(新闻)
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16
 * Time: 14:38
 */
class Student_Account_News extends Student_Home
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
     * 新闻列表
     */
    public function index(){    
        $view = student_view_path.'news/news-list.php';
//        global $wpdb;
        $which_cat = get_category_by_slug('news');
        $recentPosts = new WP_Query();
        $cat_query = $recentPosts->query('showposts=10&cat='.$which_cat->cat_ID.'&paged=1');
//
//        var_dump($cat_query);
        load_view_template($view, ['rows' => $cat_query]);
    }
    /**
     * 新闻详情
     */
     public function newsDetail(){    
        $view = student_view_path.'news/news-detail.php';
        $id = intval($_GET['id']);
        $row = get_posts($id);
//        var_dump($row[0]);
//         var_dump(get_previous_post());
//         var_dump(get_next_post()->ID);
        load_view_template($view, ['row' => $row[0]]);
    }
    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-cookie' );
        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
        if($_GET['action']=='index'){//新闻列表
            wp_register_style( 'my-student-news-list', student_css_url.'news/news-list.css' );
            wp_enqueue_style( 'my-student-news-list' );
        }
        if($_GET['action']=='newsDetail'){//新闻详情
            wp_register_style( 'my-student-news-detail', student_css_url.'news/news-detail.css' );
            wp_enqueue_style( 'my-student-news-detail' );
        }
    }
}