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
        $row = get_post($id);

        //将全局当前文章改为$row
        global $post,$wpdb;
        $post = $row;
        $cateToken = 0;
        foreach (get_the_category() as $category){
            //判断是否是新闻咨询
            if($category->category_nicename == 'news'){
                $cateToken = 1;
                break;
            }
        }
        if($cateToken === 0) return false;

        //查询浏览量
        $readRes = $wpdb->get_row('SELECT meta_id,meta_value FROM '.$wpdb->postmeta.' WHERE post_id='.$id.' AND meta_key="read_num"', ARRAY_A);
        if(!$readRes){
            if(!($metaId = $wpdb->insert($wpdb->postmeta,['post_id' => $id, 'meta_key' => 'read_num', 'meta_value' => 1]))){
                return false;
            }
            $readNum = 1;
        }else{
            $readNum = $readRes['meta_value'];
            $metaId = $readRes['meta_id'];
        }
        $data = [
            'row' => $post,
            'next' => get_next_post(true),
            'prev' => get_previous_post(true),
            'readNum' => $readNum
        ];
        //浏览量+1
        $wpdb->update($wpdb->postmeta, ['meta_value' => ++$readNum], ['meta_id' => $metaId]);
        load_view_template($view, $data);
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