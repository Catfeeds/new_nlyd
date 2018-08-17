<?php

/**
 * 学生-教练
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16
 * Time: 14:38
 */
class Student_Coach
{

    public $ajaxControll;
    public function __construct($action)
    {

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        $this->ajaxControll = new Student_Ajax();

        //添加短标签
        add_shortcode('coach-home',array($this,$action));
    }



    /**
     * 列表
     */
    public function index(){
        global $wpdb;
        // //获取教练分类
        $category = $this->ajaxControll->get_coach_category(false);
        // //获取我的教练列表
        $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : $category[0]['ID'];
        // $coach_lists = $this->ajaxControll->get_coach_lists($category_id,'',false);
        $where = "`read`={$category_id} or memory={$category_id} or compute={$category_id}";
        $sql = "select count(id) as len from {$wpdb->prefix}coach_skill where {$where}";
        $count = $wpdb->get_row($sql);
        $data = array('category'=>$category,'coachCount' => $count->len);
        $view = student_view_path.'coachList.php';
        load_view_template($view,$data);
    }

    /**
     * 教练详情页
     */
    public function coachDetail(){

        if(empty($_GET['coach_id'])){
            $this->get_404('参数错误');
            return;
        }
        global $wpdb,$current_user;
        //获取教练详情
        $sql = "SELECT * FROM {$wpdb->usermeta} WHERE user_id = {$_GET['coach_id']} and meta_key in('user_head','user_coach_level','user_real_name','user_gender','user_ID') ";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        $user_info = array_column($rows,'meta_value','meta_key');

        if(isset($user_info['user_real_name'])){
            $user_real_name = unserialize($user_info['user_real_name']);
            $user_info['real_name'] = $user_real_name['real_name'];
        }else{
            $user = get_userdata( $_GET['coach_id'] );
            if(!empty($user->data->display_name)){
                $user_info['real_name'] = preg_replace('/, /','',$user->data->display_name);
            }
        }
        $user_info['user_head'] = !empty($user_info['user_head']) ? $user_info['user_head'] : student_css_url.'image/nlyd.png';
        $user_info['user_coach_level'] = !empty($user_meta['user_coach_level']) ? $user_meta['user_coach_level'] : '高级教练';

        //获取教练技能
        $sql = "select b.post_title from {$wpdb->prefix}coach_skill a  
            right join {$wpdb->prefix}posts b on a.read = b.ID or a.memory = b.ID or a.compute = b.ID
            where a.coach_id = {$_GET['coach_id']}
            ";
        $rows = $wpdb->get_results($sql,ARRAY_A);

        //获取学员与主训
        $sql1 = "select sum(student_count) student_count,sum(major_count) major_count from(
                select count(DISTINCT user_id) student_count,0 major_count from wp_my_coach where coach_id = {$_GET['coach_id']}  and apply_status = 2
                UNION ALL 
                select 0 student_count,count(id) major_count from wp_my_coach where coach_id = {$_GET['coach_id']} and apply_status = 2 and major= 1
            ) a
            ";
        $content = $wpdb->get_row($sql1,ARRAY_A);
        //print_r($content);
        /*//获取学员列表
        $_POST['coach_id'] = $_GET['coach_id'];
        $rows_ = $this->ajaxControll->get_cocah_member(false);*/
        //print_r($rows_);
        //判断是否为我的教练
        $sql2 = "select id from {$wpdb->prefix}my_coach where user_id = {$current_user->ID} and coach_id = {$_GET['coach_id']}";
        $id = $wpdb->get_var($sql2);
        //print_r($id);
        $view = student_view_path.'coachDetail.php';
        load_view_template($view,array('user_info'=>$user_info,'skill'=>$rows,'content'=>$content,'my_coach_id'=>$id));
    }
    /**
     * 我的教练
     */
    public function myCoach(){

        //获取教练分类
        $category = $this->ajaxControll->get_coach_category(false);

        //获取我的教练列表
        $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : $category[0]['ID'];
        global $current_user,$wpdb;

        $where = 'category_id = '.$category_id.' and user_id='.$current_user->ID.' and apply_status=2';


        $sql = " select count(id) as len
                from {$wpdb->prefix}my_coach
                where {$where}";

        /*$coach_lists = $this->ajaxControll->get_coach_lists($category_id,$current_user->ID,false);
        //print_r($coach_lists);
        $data = array('category'=>$category,'lists'=>$coach_lists);*/
        $count = $wpdb->get_row($sql);
        $view = student_view_path.'coachList.php';
        load_view_template($view,array('category'=>$category,'user_id'=>$current_user->ID, 'coachCount' => $count->len));
    }
    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );

        if(ACTION=='coachDetail'){//教练详情页
            wp_register_style( 'my-student-coachDetail', student_css_url.'coachDetail.css',array('my-student') );
            wp_enqueue_style( 'my-student-coachDetail' );

        }
        if(ACTION=='index'){//教练列表页
            wp_register_style( 'my-student-coachList', student_css_url.'coachList.css',array('my-student') );
            wp_enqueue_style( 'my-student-coachList' );
        }

        if(in_array(ACTION,array('index','coachDetail'))){
            wp_register_script( 'student-swiper',student_js_url.'swiper/swiper-4.3.3.min.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-swiper' );
            wp_register_style( 'my-student-swiper', student_css_url.'swiper/swiper-4.3.3.min.css',array('my-student') );
            wp_enqueue_style( 'my-student-swiper' );
        }

    }
}