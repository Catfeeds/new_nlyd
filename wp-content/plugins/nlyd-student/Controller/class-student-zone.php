<?php

/**
 * 学生-分支机构
 * Created by PhpStorm.
 * User: zoneistrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Zone extends Student_Home
{
    public function __construct($action)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        //添加短标签
        add_shortcode('zone-home',array($this,$action));
    }


    /**
     * 机构主页
     */
    public function index(){
        global $wpdb,$user_info;

        $row = $this->get_zone_row();

        //获取用户今日收益
        $sql = "select sum(user_income) stream from {$wpdb->prefix}user_stream_logs where user_id = {$user_info['user_id']} and date_format(created_time,'%Y-%m-%d') = CURDATE() ";
        $data['stream'] = $wpdb->get_var($sql);
        //print_r($row);
        if($row['user_status'] == 1){
            $day = date_i18n('Y年m月d日',strtotime('+1 year',$row['audit_time']));

        }
        if(empty($row['legal_person'])){
            //获取所有的机构名
            $rows = $wpdb->get_results("select * from {$wpdb->prefix}zone_type where zone_type_status = 1",ARRAY_A);
            $data['list'] = $rows;
        }else{
            //获取机构权限
            if(empty($row['role_id'])){
                $sql = "select a.role_id,b.role_name,role_action,b.role_back from {$wpdb->prefix}zone_join_role a 
                    left join {$wpdb->prefix}zone_type_role b on a.role_id = b.id
                    where a.zone_type_id = {$row['type_id']} 
                    ";
            }else{
                $sql = "select * from {$wpdb->prefix}zone_type_role where id in ({$row['role_id']})";
            }

            $role_list = $wpdb->get_results($sql,ARRAY_A);
            $data['role_list'] = $role_list;
            $data['row'] = $row;
        }

        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view,$data);

    }
    /**
     * 申请项目介绍页
     */
     public function introduce(){
        $view = student_view_path.CONTROLLER.'/introduce.php';
        load_view_template($view);
    }
    /**
     * 收益管理
     */
     public function profit(){

        global $wpdb,$user_info;
         //获取用户今日收益
         $sql1 = "select sum(user_income) stream from {$wpdb->prefix}user_stream_logs where user_id = {$user_info['user_id']} and date_format(created_time,'%Y-%m-%d') = CURDATE() ";
         $data['stream'] = $wpdb->get_var($sql1);

         //获取用户今日收益
         $sql2 = "select sum(user_income) stream_total from {$wpdb->prefix}user_stream_logs where user_id = {$user_info['user_id']} and user_income > 0 ";
         $data['stream_total'] = $wpdb->get_var($sql2);

         //获取可提现金额
         $balance = $this->get_stream_total();
         $data['balance'] = $balance > 0 ? $balance : number_format(0,2);

        $view = student_view_path.CONTROLLER.'/profit.php';
        load_view_template($view,$data);
    }
    /**
     * 提现页面
     */
     public function getCash(){

        //获取机构信息
        $data['zone'] = $this->get_zone_meta();
        $data['bank_card_num'] = substr_replace($data['zone']['bank_card_num'],'********',4,8);
        //获取能提现的最大金额
        $balance = $this->get_stream_total();
        $data['balance'] = $balance > 0 ? $balance : number_format(0,2);

        $view = student_view_path.CONTROLLER.'/profit-getCash.php';
        load_view_template($view,$data);
    }
    /**
     * 提现成功页面
     */
     public function getCashSuccess(){

        $view = student_view_path.CONTROLLER.'/profit-getCash-success.php';
        load_view_template($view);
    }
    /**
     * 收益详情页面
     */
     public function profitDetail(){

         global $wpdb,$current_user;

         $zone = $this->get_zone_meta();

         if(!empty($zone)){
             $sql = " select *,
                case income_type
                when 'match' then '比赛收益'
                when 'grading' then '考级收益'
                when 'extract' then '比赛提现'
                end income_type_title 
                from {$wpdb->prefix}income_logs 
                where id = {$_GET['id']} and (referee_id = {$current_user->ID} or indirect_referee_id = $current_user->ID) ";
         }else{

             $sql = "select *,date_format(created_time,'%Y/%m/%d %H:%i') created_time,
                if(income_type = 'extract','bg_reduce', 'bg_add') as income_type_class,
                case income_type
                when 'match' then '比赛收益'
                when 'grading' then '考级收益'
                when 'extract' then '比赛提现'
                end income_type_title
                from {$wpdb->prefix}user_stream_logs where id = {$_GET['id']} and user_id = {$current_user->ID} ";
         }

         $row = $wpdb->get_row($sql,ARRAY_A);
         if(empty($row)){
             $this->get_404(array('message'=>'数据错误','return_url'=>home_url('/zone/profit/')));
             return;
         }
         if(!empty($zone)){
             if($row['referee_id'] == $current_user->ID){
                 $row['user_income'] = $row['referee_income'];
                 $row['profit_lv'] = '1级收益';
                 $referee_name = get_user_meta($row['user_id'],'user_real_name')[0];
                 $row['channel'] = $referee_name['real_name'];
             }
             elseif ($row['indirect_referee_id'] == $current_user->ID){
                 $row['user_income'] = $row['indirect_referee_income'];
                 $row['profit_lv'] = '2级收益';
                 $indirect_referee = get_user_meta($row['indirect_referee_id'],'user_real_name')[0];
                 $row['channel'] = $indirect_referee['real_name'];
             }

         }
         if(in_array($row['income_type'],array('match','grading'))){
             switch ($row['income_type']){
                 case 'match':
                     $table = $wpdb->prefix.'match_meta_new';
                     $where = "a.match_id = {$row['match_id']}";
                     $t = 'match_id';
                     $scene = "match_scene";
                     break;
                 case 'grading':
                     $table = $wpdb->prefix.'grading_meta';
                     $where = "a.grading_id = {$row['match_id']}";
                     $t = 'grading_id';
                     $scene = "scene";
                     break;
             }
             $sql1 = "select b.post_title as match_title,c.role_name
                      from {$table} a
                      left join {$wpdb->prefix}posts b on a.{$t} = b.ID
                      left join {$wpdb->prefix}zone_match_role c on a.{$scene} = c.id
                      where {$where}
                      ";
             //print_r($sql1);
             $match = $wpdb->get_row($sql1,ARRAY_A);
             $data['match'] = $match;
             //print_r($match);
         }
         print_r($row);
         $data['row'] = $row;
         $view = student_view_path.CONTROLLER.'/profit-detail.php';
         load_view_template($view,$data);
    }
    /**
     * 提现详情页面
     */
     public function getCashDetail(){


         $zone = $this->get_zone_meta();

         print_r($zone);

        $view = student_view_path.CONTROLLER.'/profit-getCash-detail.php';
        load_view_template($view);
    }
    /**
     * 比赛管理列表
     */
     public function match(){
         global $wpdb,$current_user;
         //获取用户发布比赛的权限
         $match_role_id = $wpdb->get_var("select match_role_id from {$wpdb->prefix}zone_meta where user_id = {$current_user->ID}");
         if(empty($match_role_id)){
             $this->get_404(array('message'=>__('你未拥有该权限,请联系管理员授权', 'nlyd-student'),'return_url'=>home_url('/zone/')));
             return;
         }

        $view = student_view_path.CONTROLLER.'/match-list.php';
        load_view_template($view);
    }
    /**
     * 发布比赛
     */
     public function matchBuild(){
         global $wpdb,$current_user;
         //获取用户发布比赛的权限
         $match_role_id = $wpdb->get_var("select match_role_id from {$wpdb->prefix}zone_meta where user_id = {$current_user->ID}");
         if(empty($match_role_id)){
             $this->get_404(array('message'=>__('你未拥有该权限,请联系管理员授权', 'nlyd-student'),'return_url'=>home_url('/zone/')));
             return;
         }
         $match_role = $wpdb->get_results("select *,role_name as value from {$wpdb->prefix}zone_match_role where id in($match_role_id) and status = 1",ARRAY_A);
         //print_r($match_role);
         //获取比赛类型
         $match_genre = $wpdb->get_results("select a.ID as id,a.post_title as value,b.meta_value from {$wpdb->prefix}posts a 
                                  left join {$wpdb->prefix}postmeta b on a.ID = b.post_id and b.meta_key='project_alias'
                                  where a.post_type = 'genre' and a.post_status = 'publish' and b.meta_value in('mental_world_cup','digital_brain_king','counting_brain_marathon')");
         $data['scene_list'] = !empty($match_role) ? json_encode($match_role) : '';
         $data['match_genre'] = !empty($match_genre) ? json_encode($match_genre) : '';

         if(isset($_GET['match_id'])){
             //获取比赛信息
             $sql = "select a.post_title ,c.role_name as scene_title,d.post_title as genre_title, b.* from {$wpdb->prefix}posts a 
                      left join {$wpdb->prefix}match_meta_new b on a.ID = b.match_id 
                      left join {$wpdb->prefix}zone_match_role c on b.match_scene = c.id 
                      left join {$wpdb->prefix}posts d on b.match_genre = d.ID 
                      where a.ID = {$_GET['match_id']}
                      ";
             $match = $wpdb->get_row($sql,ARRAY_A);
             if(!empty($match['match_start_time'])){
                 $match['data_time'] = preg_replace('/\s|:/','-',$match['match_start_time']);
             }
             $data['match'] = $match;
             //print_r($match);
         }

         $view = student_view_path.CONTROLLER.'/match-build.php';
         load_view_template($view,$data);
    }
    /**
     * 比赛时间管理
     */
     public function matchTime(){
         global $wpdb,$current_user;
         $sql = "select a.id,a.project_id,b.post_title, date_format(a.start_time,'%Y/%m/%d %H:%i') start_time,date_format(a.end_time,'%Y/%m/%d %H:%i') end_time,a.use_time,a.more
                  from {$wpdb->prefix}match_project_more a 
                  left join {$wpdb->prefix}posts b on a.project_id = b.ID
                  where match_id = {$_GET['match_id']} and created_id = {$current_user->ID} ";
         $rows = $wpdb->get_results($sql,ARRAY_A);
         if(empty($rows)){
             $this->get_404(_('未查询到比赛项目信息'));
             return;
         }
         //print_r($rows);
         $list = array();
         foreach ($rows as $val ){
             $k = &$val['project_id'];
             $title = &$val['post_title'];
             $list[$k]['title'] = $title;
             $list[$k]['use_time'] = $val['use_time'];
             $list[$k]['child'][] = $val;
         }
         //print_r($list);
         $data['list'] = $list;
         //print_r($rows);
         $view = student_view_path.CONTROLLER.'/match-time.php';
         load_view_template($view,$data);
    }
    /**
     * 比赛发布成功
     */
     public function buildSuccess(){
        $view = student_view_path.CONTROLLER.'/match-buildSuccess.php';
        load_view_template($view);
    }
    /**
     * 考级管理列表
     */
     public function kaojiList(){
        $view = student_view_path.CONTROLLER.'/kaoji-list.php';
        load_view_template($view);
    }
    /**
     * 发布考级
     */
     public function kaojiBuild(){
        $view = student_view_path.CONTROLLER.'/kaoji-build.php';
        load_view_template($view);
    }
    /**
     * 考级发布成功
     */
     public function kaojiBuildSuccess(){
        $view = student_view_path.CONTROLLER.'/kaoji-buildSuccess.php';
        load_view_template($view);
    }

    /**
     * 课程管理列表
     */
     public function course(){
        $view = student_view_path.CONTROLLER.'/course-list.php';
        load_view_template($view);
    }
    /**
     * 发布课程
     */
     public function courseBuild(){
        $view = student_view_path.CONTROLLER.'/course-build.php';
        load_view_template($view);
    }
    /**
     * 课程发布成功
     */
     public function courseBuildSuccess(){
        $view = student_view_path.CONTROLLER.'/course-buildSuccess.php';
        load_view_template($view);
    }
    /**
     * 课程学员
     */
     public function courseStudent(){
        $view = student_view_path.CONTROLLER.'/course-studentList.php';
        load_view_template($view);
    }
    /**
     * 分中心学员管理
     */
     public function student(){
        $view = student_view_path.CONTROLLER.'/student-list.php';
        load_view_template($view);
    }
    /**
     * 课程学员
     */
     public function studentDetail(){
        $view = student_view_path.CONTROLLER.'/student-detail.php';
        load_view_template($view);
    }

    /**
     * 教练管理
     */
     public function coach(){
        $view = student_view_path.CONTROLLER.'/coach-list.php';
        load_view_template($view);
    }
    /**
     * 添加教练
     */
     public function coachAdd(){
        $view = student_view_path.CONTROLLER.'/coach-add.php';
        load_view_template($view);
    }
    /**
     * 教练详情
     */
     public function coachDetail(){
        $view = student_view_path.CONTROLLER.'/coach-detail.php';
        load_view_template($view);
    }
    /*
     *机构主体信息页面
     */
    public function account(){
        global $user_info;
        $row = $this->get_zone_row();

        if(empty($row)){
            $this->get_404(array('message'=>'数据错误'));
            return;
        }
        $data['user_real_name'] = $user_info['user_real_name'];
        $data['row'] = $row;
        $view = student_view_path.CONTROLLER.'/account.php';
        load_view_template($view,$data);
    }

    /**
     * 获取机构信息
     */
    public function get_zone_row(){
        global $wpdb,$user_info;
        $sql = "select a.*,b.zone_type_name,b.zone_type_alias,c.user_mobile from {$wpdb->prefix}zone_meta a 
                left join {$wpdb->prefix}zone_type b on a.type_id = b.id 
                left join {$wpdb->prefix}users c on a.user_id = c.ID 
                where a.user_id = '{$user_info['user_id']}' ";
        //print_r($sql);
        $row = $wpdb->get_row($sql,ARRAY_A);
        $row['user_head'] = $user_info['user_head'];
        $row['user_real_name'] = $user_info['user_real_name']['real_name'];
        $row['user_ID'] = $user_info['user_ID'];

        //获取推荐人
        $referee_user_real_name = get_user_meta($user_info['referee_id'],'user_real_name')[0];
        $row['referee_name'] = $referee_user_real_name['real_name'];

        return $row;
    }

    /**
     * 分支机构申请页面
     */
    public function apply(){

        global $wpdb,$current_user,$user_info;
        $row = $this->get_zone_row();
        if(!empty($row)){
            //获取主席
            $row['chairman_name'] = !empty($row['chairman_id']) ? get_user_meta($row['chairman_id'],'user_real_name')[0]['real_name'] : '';
            //获取秘书长
            $row['secretary_name'] = !empty($row['secretary_id']) ? get_user_meta($row['secretary_id'],'user_real_name')[0]['real_name'] : '';

            $data['row'] = $row;
        }else{
            //分中心编号
            $total = $wpdb->get_var("select max(id) total from {$wpdb->prefix}zone_meta ");
            if($total < 8){
                $data['zone_num'] = 8;
            }else{
                $data['zone_num'] = $total+1;
            }
        }
        //获取所有机构
        //$data['list'] = $wpdb->get_results("select id,zone_type_name,zone_type_alias from {$wpdb->prefix}zone_type where zone_type_status = 1 order by zone_sort asc",ARRAY_A);

        //获取事业管理员
        $user_real_name = get_user_meta($current_user->data->referee_id,'user_real_name')[0];
        if(!empty($user_real_name)){
            $data['referee_name'] = $user_real_name['real_name'];
        }
        //print_r($user_info);
        //分中心负责人
        if(!empty($user_info['user_real_name'])){
            $data['director'] = $user_info['user_real_name']['real_name'];
            $data['contact'] = $user_info['contact'];
            $data['user_ID_Card'] = $user_info['user_ID_Card'];
        }


        $view = student_view_path.CONTROLLER.'/apply.php';
        load_view_template($view,$data);
    }
   /**
     * 分支机构申请页面成功后提示页面
     */
     public function applySuccess(){
        $view = student_view_path.CONTROLLER.'/apply-success.php';
        load_view_template($view);

    }

    /**
     * 获取可提现金额
     */
    public function get_stream_total(){
        global $wpdb,$current_user;
        $sql3 = "select sum(user_income) stream_total from {$wpdb->prefix}user_stream_logs where user_id = {$current_user->ID} ";
        return $wpdb->get_var($sql3);
    }

    /**
     * 获取机构信息
     */
    public function get_zone_meta(){
        global $wpdb,$current_user;
        $row = $wpdb->get_row("select * from {$wpdb->prefix}zone_meta where user_id = {$current_user->ID}",ARRAY_A);
        return $row;
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_script( 'student-mobileSelect',student_js_url.'Mobile/mobileSelect.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-mobileSelect' );
        wp_localize_script('student-mobileSelect','_mobileSelect',[
            'sure'=>__('确认','nlyd-student'),
            'cancel'=>__('取消','nlyd-student')
        ]);
        wp_register_style( 'my-student-mobileSelect', student_css_url.'Mobile/mobileSelect.css',array('my-student') );
        wp_enqueue_style( 'my-student-mobileSelect' );
        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
        // if(ACTION == 'index'){
        // }

        if(ACTION == 'apply' || ACTION == 'courseBuild'){
            wp_register_script( 'zone_select2_js',match_js_url.'select2/dist/js/select2.js',array('jquery'), leo_match_version  );
            wp_enqueue_script( 'zone_select2_js' );
            wp_register_script( 'zone_select2_i18n_js',match_js_url.'select2/dist/js/i18n/zh-CN.js',array('jquery'), leo_match_version  );
            wp_enqueue_script( 'zone_select2_i18n_js' );
            wp_register_style( 'zone_select2_css',match_js_url.'select2/dist/css/select2.css','', leo_match_version  );
            wp_enqueue_style( 'zone_select2_css' );
        }
        wp_register_style( 'my-student-zone', student_css_url.'zone/zone.css',array('my-student') );
        wp_enqueue_style( 'my-student-zone' );
    }

}