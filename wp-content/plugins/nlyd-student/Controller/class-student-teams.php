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
//            $user_info['real_name'] = $rows['last_name'].$rows['first_name'];
        }else{
            $user = get_userdata( $_GET['coach_id'] );
            if(!empty($user->data->display_name)){
                $user_info['real_name'] = preg_replace('/, /','',$user->data->display_name);
            }
        }
        $user_info['user_head'] = !empty($user_info['user_head']) ? $user_info['user_head'] : student_css_url.'image/nlyd.png';
        $user_info['user_coach_level'] = !empty($user_meta['user_coach_level']) ? $user_meta['user_coach_level'] : '高级教练';
        //获取教练技能

        $sql = "select `read`,memory,compute from {$wpdb->prefix}coach_skill where coach_id = {$_GET['coach_id']}";
        $rows = $wpdb->get_row($sql,ARRAY_A);

        $arr = ['read' => '速读类', 'memory' => '速记类', 'compute' => '速算类'];
        $k = 0;
        foreach ($arr as $ak => $ar){
//            $readApply = $wpdb->get_row('SELECT post_title,ID FROM '.$wpdb->prefix.'posts WHERE ID='.$rows[$ak]);
            $rows['category'][$k]['name'] = $ak;
            $rows['category'][$k]['post_title'] = $ar;
            $rows['category'][$k]['category_id'] = $rows[$ak];
            $rows['category'][$k]['is_current'] = false;//此教练是否在当前分类
            $rows['category'][$k]['is_apply'] = false; //是否申请中
            $rows['category'][$k]['is_my_coach'] = false; //是否已通过
            $rows['category'][$k]['is_my_major'] = false; //是否是主训
            $rows['category'][$k]['is_relieve'] = false; //是否已解除
            $rows['category'][$k]['is_refuse'] = false;//是否已拒绝
            if($rows[$ak] != 0 && $rows[$ak] != null){
                $rows['category'][$k]['is_current'] = true;//此教练是否在当前分类
                $coachStudent = $wpdb->get_row('SELECT apply_status,major FROM '.$wpdb->prefix.'my_coach WHERE category_id='.$rows[$ak].' AND user_id='.$current_user->ID.' AND coach_id='.$_GET['coach_id']);
                if($coachStudent){
                    switch ($coachStudent->apply_status){
                        case 1://申请中
                            $rows['category'][$k]['is_apply'] = true;
                            break;
                        case 2://已通过
                            $rows['category'][$k]['is_my_coach'] = true;
                            $rows['category'][$k]['is_my_major'] = $coachStudent->major == 1 ? true : false;
                            break;
                        case 3://已解除
                            $rows['category'][$k]['is_relieve'] = true;
                            break;
                        case -1://已拒绝
                            $rows['category'][$k]['is_refuse'] = true;
                            break;
                    }
                }
            }
            ++$k;
        }
//        die;
        //获取学员与主训
        $sql1 = "select sum(student_count) student_count,sum(major_count) major_count from(
                select count(DISTINCT user_id) student_count,0 major_count from wp_my_coach where coach_id = {$_GET['coach_id']}  and apply_status = 2
                UNION ALL 
                select 0 student_count,count(id) major_count from wp_my_coach where coach_id = {$_GET['coach_id']} and apply_status = 2 and major= 1
            ) a
            ";
        $content = $wpdb->get_row($sql1,ARRAY_A);
        //print_r($content);
        //获取学员列表
        //$_POST['coach_id'] = $_GET['coach_id'];
        //$rows_ = $this->ajaxControll->get_cocah_member(false);
        //print_r($rows_);
        //判断是否为我的教练
        $sql2 = "select id from {$wpdb->prefix}my_coach where user_id = {$current_user->ID} and coach_id = {$_GET['coach_id']} and apply_status != 2 category_id=".$_GET['category_id'];
        $id = $wpdb->get_var($sql2);
        $view = student_view_path.CONTROLLER.'/coachDetail.php';
        load_view_template($view,array('user_infos'=>$user_info,'skill'=>$rows,'content'=>$content,'my_coach_id'=>$id));
    }

    /**
     * 教练列表
     */
    public function coachList(){
        global $wpdb;
        // //获取教练分类
        $category = $this->ajaxControll->get_coach_category(false);
        // //获取我的教练列表
        $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : $category[0]['ID'];
        // $coach_lists = $this->ajaxControll->get_coach_lists($category_id,'',false);
        $where = "`read`={$category_id} or memory={$category_id} or compute={$category_id}";
        $sql = "select count(id) as len from {$wpdb->prefix}coach_skill where {$where}";
        $count = $wpdb->get_row($sql);
        $data = array('category'=>$category,'coachCount' => $count->len,'action'=>'coachList','category_id'=>$category_id);
        $view = student_view_path.CONTROLLER.'/coachList.php';
        load_view_template($view,$data);
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

        $where = 'user_id='.$current_user->ID.' and apply_status=2';

        $sql = " select count(id) as len
                from {$wpdb->prefix}my_coach
                where {$where}";

        $count = $wpdb->get_row($sql);
        // $view = student_view_path.CONTROLLER.'/coachList.php';
        // load_view_template($view,array('category'=>$category,'category_id' => $category_id,'user_id'=>$current_user->ID, 'coachCount' => $count->len,'action'=>'myCoach'));
        $view = student_view_path.CONTROLLER.'/myCoach.php';
        load_view_template($view);
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
        $view = student_view_path.CONTROLLER.'/team.php';
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

        $view = student_view_path.CONTROLLER.'/teamDetail.php';
        load_view_template($view,array('team'=>$team));
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );

        if( ACTION == 'teamDetail'){//比赛详情
            wp_register_style( 'my-student-teamDetail', student_css_url.'teamDetail.css',array('my-student') );
            wp_enqueue_style( 'my-student-teamDetail' );

        }

        if(ACTION=='index'){
            wp_register_style( 'my-student-team', student_css_url.'team.css',array('my-student') );
            wp_enqueue_style( 'my-student-team' );
        }

        if(in_array(ACTION,array('myCoach','coachList'))){//教练列表页
            wp_register_style( 'my-student-coachList', student_css_url.'coach/coachList.css',array('my-student') );
            wp_enqueue_style( 'my-student-coachList' );

        }
        if(ACTION=='coachDetail'){//教练详情页
            wp_register_style( 'my-student-coachDetail', student_css_url.'coach/coachDetail.css',array('my-student') );
            wp_enqueue_style( 'my-student-coachDetail' );

        }
        if(in_array(ACTION,array('index','coachDetail','myCoach','coachList'))){
            wp_register_script( 'student-swiper',student_js_url.'swiper/swiper-4.3.3.min.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-swiper' );
            wp_register_style( 'my-student-swiper', student_css_url.'swiper/swiper-4.3.3.min.css',array('my-student') );
            wp_enqueue_style( 'my-student-swiper' );
        }
    }
}