<?php

/**
 * 学生端公用父类
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Student
{
    public function __construct($action)
    {

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        //添加短标签
        add_shortcode('student-index',array($this,$action));
    }

    public function index(){
        global $wpdb;
        //获取推荐课程
        $course_sql = "select a.id,a.course_title,c.zone_city,c.zone_number,c.user_id
                from {$wpdb->prefix}course a 
                left join {$wpdb->prefix}course_type b on a.course_type = b.id
                left join {$wpdb->prefix}zone_meta c on a.zone_id = c.user_id
                where a.is_enable = 1 and c.user_id is not null
                order by a.id desc 
                limit 4
               ";

        $course_rows = $wpdb->get_results($course_sql,ARRAY_A);
        if(!empty($course_rows)){
            foreach ($course_rows as $k => $val){

                if(!empty($val['zone_city'])){
                    //获取城市
                    $city_arr = str2arr($val['zone_city'],'-');
                    if(!empty($city_arr[2])){
                        $city = rtrim($city_arr[1],'市').preg_replace('/区|县/','',$city_arr[2]);
                    }elseif ($city_arr[1] != '市辖区'){
                        $city = rtrim($city_arr[1],'市');
                    }else{
                        $city = rtrim($city_arr[0],'市');
                    }
                    $course_rows[$k]['zone_city'] = $city;
                }
            }
            $data['course_list'] = $course_rows;
        }

        //获取教练
        $coach_sql = "select a.id,a.coach_id,b.meta_value from {$wpdb->prefix}coach_skill a 
                      left join {$wpdb->prefix}usermeta b on a.coach_id = b.user_id and meta_key = 'user_real_name' 
                      where b.meta_value is not null
                      order by a.id asc limit 5 ";
        $coach_rows = $wpdb->get_results($coach_sql,ARRAY_A);
        if(!empty($coach_rows)){
            foreach ($coach_rows as $key => $v){
                $real_name = unserialize($v['meta_value']);
                $coach_rows[$key]['coach_name'] = $real_name['real_name'];
                $work_photo = get_user_meta($v['coach_id'],'user_images_color')[0];
                $coach_rows[$key]['work_photo'] = !empty($work_photo[0]) ? $work_photo[0] : student_css_url.'image/nlyd.png' ;
            }

            $data['coach_list'] = $coach_rows;
        }

        //获取赛事回顾
        $cat=get_category_by_slug('games-or-news');
        if($cat->cat_ID > 0){
            $args = array(
                'numberposts'     => 10,
                'offset'          => 0,
                'category'        =>$cat->cat_ID ,
                'orderby'         => 'post_date',
                'order'           => 'DESC',
                'post_type'       => 'post',
                'post_status'     => 'publish'
            );
            $posts_array = get_posts( $args );
            if(!empty($posts_array)){
                $data['post_list'] = $posts_array;
            }
        }

        //推荐资讯
        $term_id = $wpdb->get_results("select term_id from {$wpdb->prefix}terms where slug in ('intelligence-science','default-classification','industry-information','events') ",ARRAY_A);

        if(!empty($term_id)){
            $post_id = array_column($term_id,'term_id');

            $news_args = array(
                'numberposts'     => 1,
                'offset'          => 0,
                'category'        =>$post_id,
                'orderby'         => 'rand',
                'order'           => 'DESC',
                'post_type'       => 'post',
                'post_status'     => 'publish'
            );
            $posts_news = get_posts( $news_args );
            if(!empty($posts_array)){
                $data['news'] = $posts_news[0];
            }
        }

        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view,$data);
    }

    public function scripts_default(){

        wp_register_style( 'my-student-home', student_css_url.'home-student.css' );
        wp_enqueue_style( 'my-student-home' );
        wp_register_script( 'student-swiper',student_js_url.'swiper/swiper-4.3.3.min.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-swiper' );
        wp_register_style( 'my-student-swiper', student_css_url.'swiper/swiper-4.3.3.min.css',array('my-student') );
        wp_enqueue_style( 'my-student-swiper' );
        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
    }

}