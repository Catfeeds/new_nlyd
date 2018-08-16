<?php

/**
 * 学生-我的战队
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Teams
{
    public $ajaxControll;
    public function __construct($action)
    {

        if(in_array($action,array('myCoach','teamDetail'))){

            if(!is_admin() && !is_user_logged_in()){

                wp_redirect(home_url('login'));
            }
        }

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        $this->ajaxControll = new Student_Ajax();
        //添加短标签
        add_shortcode('team-home',array($this,$action));
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
     * 战队列表
     */
    public function index(){
        global $wpdb;
        $map = array();
        $map[] = " post_status = 'publish' ";
        $map[] = " post_type = 'team' ";
        //判断是否有分页
        $page = isset($_GET['page'])?$_GET['page']:1;
        $pageSize = 15;
        $start = ($page-1)*$pageSize;
        $where = join(' and ',$map);
        $sql = "select SQL_CALC_FOUND_ROWS ID,post_title from {$wpdb->prefix}posts where {$where} order by ID desc limit $start,$pageSize";
        $rows = $wpdb->get_results($sql,ARRAY_A);

        $row = $wpdb->get_row('SELECT id FROM '.$wpdb->prefix.'posts WHERE post_status="publish" and post_type = "team"');
        //print_r($rows);
        $view = student_view_path.'team.php';
        load_view_template($view,array('lists'=>$rows, 'row' => $row));
    }

    /**
     * 战队详情
     */
    public function teamDetail(){

        global $wpdb,$current_user;



        //获取战队信息
        $sql = "select a.ID,a.post_title,a.post_content,c.user_id,c.status,
                if(b.team_world != '',b.team_world,'--') as team_world,
                if(b.team_director != '',b.team_director,'--') as team_director,
                if(b.team_slogan != '',b.team_slogan,'--') as team_slogan,
                if(b.team_leader != '',b.team_leader,'--') as team_leader
                from {$wpdb->prefix}posts a 
                left join {$wpdb->prefix}team_meta b on a.ID = b.team_id 
                left join {$wpdb->prefix}match_team c on a.ID = c.team_id and user_id = {$current_user->ID} 
                where a.ID = {$_GET['team_id']} 
                ";
        //print_r($sql);
        $team = $wpdb->get_row($sql,ARRAY_A);
        //print_r($team);
        if(empty($team)){
            $this->get_404('参数错误');
            return;
        }
        //获取领队
        if(is_numeric($team['team_director'])){
            $user = get_userdata( $team['team_director']);
            if(!empty($user->data->display_name)){
                $team['team_director'] = preg_replace('/, /','',$user->data->display_name);
            }
        }
        //获取战队成员
        $total = $wpdb->get_var("select count(*) from {$wpdb->prefix}match_team where team_id = {$team['ID']} and status = 2 and user_type = 1");
        $team['team_total'] = $total;

        $view = student_view_path.'teamDetail.php';
        load_view_template($view,array('team'=>$team));
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
        if(ACTION=='index'){
            wp_register_script( 'student-swiper',student_js_url.'swiper/swiper-4.3.3.min.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-swiper' );
            wp_register_style( 'my-student-team', student_css_url.'team.css',array('my-student') );
            wp_enqueue_style( 'my-student-team' );
            wp_register_style( 'my-student-swiper', student_css_url.'swiper/swiper-4.3.3.min.css',array('my-student') );
            wp_enqueue_style( 'my-student-swiper' );
        }
        
        if( ACTION == 'teamDetail'){//比赛详情
            wp_register_style( 'my-student-teamDetail', student_css_url.'teamDetail.css',array('my-student') );
            wp_enqueue_style( 'my-student-teamDetail' );

        }
        
        if(ACTION=='myCoach'){//我的教练
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-coachList', student_css_url.'coachList.css',array('my-student') );
            wp_enqueue_style( 'my-student-coachList' );
        }

        if(ACTION=='coachDetail'){//教练详情页
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-coachDetail', student_css_url.'coachDetail.css',array('my-student') );
            wp_enqueue_style( 'my-student-coachDetail' );

        }
    }
}