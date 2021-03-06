<?php

/**
 * 学生-分支机构
 * Created by PhpStorm.
 * User: zoneistrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Zone
{
    public function __construct($action)
    {
        //316751759@gjnlyd.com http://127.0.0.1/nlyd/zone/grading/

        if(!is_user_logged_in()){

            $_SESSION['redirect_url'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }
        //判断是否是管理员操作面板和是否登录
        if(!is_user_logged_in()){
            wp_redirect(home_url('logins'));
        }

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        //添加短标签
        add_shortcode('zone-home',array($this,$action));
    }

    public function get_404($tag){
        $view = leo_student_public_view.'my-404.php';
        if(!is_array($tag)){
            $data['message'] = $tag;
        }else{
            $data = $tag;
        }
        load_view_template($view,$data);
    }

    /**
     * 机构主页
     */
    public function index(){
        global $wpdb,$current_user;

        $row = $this->get_zone_row();

        //获取用户今日收益
        $sql = "select sum(user_income) stream from {$wpdb->prefix}user_stream_logs where user_id = {$current_user->ID} and date_format(created_time,'%Y-%m-%d') = CURDATE() and user_income > 0";
        $data['stream'] = $wpdb->get_var($sql);
        //print_r($row);
        if($row['user_status'] == 1){
            $day = date_i18n('Y年m月d日',strtotime('+1 year',$row['audit_time']));

        }
        if(empty($row['legal_person'])){
            //获取所有的机构名
            //$rows = $wpdb->get_results("select * from {$wpdb->prefix}zone_type where zone_type_status = 1",ARRAY_A);
            //$data['list'] = $rows;
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
        }

        $data['row'] = $row;

        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view,$data);

    }
    /**
     *个人用户控制台
     */
    public function indexUser(){
        global $wpdb,$current_user;
        //获取推荐ID/推荐时间/今日收益
        $sql = "select a.referee_id,a.referee_time,date_format(b.created_time,'%Y-%m-%d') date_time ,sum(b.user_income) total_income
                  from {$wpdb->prefix}users a
                  left join {$wpdb->prefix}user_stream_logs b on a.ID = b.user_id and date_format(b.created_time,'%Y-%m-%d') = curdate() and b.user_income > 0
                  where a.ID = {$current_user->ID}
                  group by date_time
                  ";

        $row = $wpdb->get_row($sql,ARRAY_A);

        //获取用户推广码
        $ajax = new Student_Ajax();
        $row['referee_code'] = $ajax->qrcode('user');

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
        //print_r($rows);
        /*$sql_ = "select a.*,b.id zone_id,b.apply_id,b.user_status
                 from {$wpdb->prefix}zone_type a
                 left join {$wpdb->prefix}zone_meta b on a.id = b.type_id  and apply_id = {$current_user->ID}
                 where zone_type_status = 1";
       // print_r($sql_);
        $rows = $wpdb->get_results($sql_,ARRAY_A);*/
        $row['list'] = $rows;

        $view = student_view_path.CONTROLLER.'/index-user.php';
        load_view_template($view,$row);
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

        global $wpdb,$current_user;
        //获取用户今日收益
        $sql1 = "select sum(user_income) stream from {$wpdb->prefix}user_stream_logs where user_id = {$current_user->ID} and date_format(created_time,'%Y-%m-%d') = CURDATE() and user_income > 0  ";
        $data['stream'] = $wpdb->get_var($sql1);

        //获取用户累计收益
        $sql2 = "select sum(user_income) stream_total from {$wpdb->prefix}user_stream_logs where user_id = {$current_user->ID} and user_income > 0 ";
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
        if(!empty($data['zone'])){
            $data['user_cheques_bank'] = empty($data['zone']['opening_bank']) ? '' : '（'.$data['zone']['opening_bank'].' 尾号'.substr($data['zone']['bank_card_num'],-4,4).'）';
        }else{
            global $wpdb,$current_user;
            //获取相关收款设置
            $sql = "select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id = {$current_user->ID} and meta_key in ('aliPay_coin_code','user_coin_code','user_cheques_bank') ";
            $rows = $wpdb->get_results($sql,ARRAY_A);
            if(!empty($rows)){
                $meta = array_column($rows,'meta_value','meta_key');
                $data['aliPay_coin_code'] = empty($meta['aliPay_coin_code']) ? '' : unserialize($meta['aliPay_coin_code'])[0];
                $data['user_coin_code'] = empty($meta['user_coin_code']) ? '' : unserialize($meta['user_coin_code'])[0];
                $user_cheques_bank = empty($meta['aliPay_coin_code']) ? '' : unserialize($meta['user_cheques_bank']);
                $data['user_cheques_bank'] = empty($user_cheques_bank) ? '' : '（'.$user_cheques_bank['open_bank'].' 尾号'.substr($user_cheques_bank['open_card_num'],-4,4).'）';
            }
        }

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
        $row = $wpdb->get_row("select match_id,income_type,user_type,user_income,created_time,
        						if(income_status=2,'已到账','待到账') income_status_cn,provide_id,
					        	case income_type
				                    when 'open_match' then '开设比赛'
				                    when 'open_grading' then '开设考级'
				                    when 'open_course' then '课程渠道'
				                    when 'recommend_match' then '推荐比赛'
				                    when 'recommend_grading' then '推荐考级'
				                    when 'director_match' then '参赛机构'
				                    when 'director_grading' then '考级负责人'
				                    when 'recommend_match_zone' then '推荐赛区'
				                    when 'recommend_trains_zone' then '推荐训练中心'
				                    when 'recommend_test_zone' then '推荐测评中心'
				                    when 'recommend_course' then '推荐购课'
				                    when 'recommend_qualified_coach' then '学员分享'
				                    when 'recommend_qualified_zone' then '学员分享'
				                    when 'grading_qualified' then '考级达标'
				                    when 'cause_manager' then '事业管理员'
				                    when 'cause_minister' then '事业部长'
				                    when 'extract' then '提现'
				                    else '数据测试'
				                  end income_type_title
                              from {$wpdb->prefix}user_stream_logs 
                              where id = {$_GET['id']} and user_id = {$current_user->ID} ",ARRAY_A);

        //print_r($row);die;

        if(empty($row)){
            $this->get_404(array('message'=>__('数据信息错误', 'nlyd-student'),'return_url'=>home_url('/zone/profit/')));
            return;
        }
        if(!empty($row['provide_id'])){
            $row['channel_ID'] = $row['provide_id']+10000000;
        }
        if(in_array($row['income_type'],array('open_match','open_grading','recommend_match','recommend_grading'))){
            $row['post_title'] = $wpdb->get_var("select post_title from {$wpdb->prefix}posts where ID = {$row['match_id']} ");
        }

        $view = student_view_path.CONTROLLER.'/profit-detail.php';
        load_view_template($view,$row);
    }
    /**
     * 提现详情页面
     */
    public function getCashDetail(){

        global $wpdb,$current_user;
        //获取提现信息
        $sql = "select *,
                  case extract_type
                  when 'bank' then '提现至对公账户'
                  when 'user_bank' then '提现至银行卡'
                  when 'weChat' then '提现至微信'
                  when 'aliPay' then '提现至支付宝'
                  else '提现至钱包'
                  end extract_type_cn,
                  case extract_status
                  when 2 then '已到账'
                  when 3 then '提现失败'
                  else '提现中'
                  end extract_status_cn
                  from {$wpdb->prefix}user_extract_logs where stream_log_id = {$_GET['id']} and extract_id = $current_user->ID";
        //print_r($sql);
        $row = $wpdb->get_row($sql,ARRAY_A);
        if(empty($row)){
            $this->get_404(array('message'=>__('数据信息错误', 'nlyd-student'),'return_url'=>home_url('/zone/profit/')));
            return;
        }
        //print_r($row);

        $view = student_view_path.CONTROLLER.'/profit-getCash-detail.php';
        load_view_template($view,$row);
    }
    /**
     * 比赛管理列表
     */
    public function match(){
        global $wpdb,$current_user;
        //获取用户发布比赛的权限
        $match_role_id = $wpdb->get_var("select match_role_id from {$wpdb->prefix}zone_meta where user_id = {$current_user->ID}");

        //print_r("select match_role_id from {$wpdb->prefix}zone_meta where user_id = {$current_user->ID}");
        if(empty($match_role_id)){
            $this->get_404(array('message'=>__('你未拥有该权限,请联系管理员授权', 'nlyd-student'),'return_url'=>home_url('/zone/')));
            return;
        }

        $sql = "select a.id,a.match_id,a.match_status,a.match_start_time,a.match_end_time,a.entry_end_time,c.meta_value match_switch
                from {$wpdb->prefix}match_meta_new a  
                LEFT JOIN {$wpdb->prefix}postmeta c ON a.match_id = c.post_id and meta_key = 'default_match_switch'
                where a.created_id = {$current_user->ID}
                ";
        $rows = $wpdb->get_results($sql,ARRAY_A);

        if(!empty($rows)){
            $new_time = get_time('mysql');
            $entry_is_true = 0;
            $match_is_true = 0;
            foreach ($rows as $v){
                if($v['match_status'] != -4){

                    if($v['match_switch'] == 'ON') {
                        if($new_time < $v['entry_end_time']){
                            //报名中
                            $save['match_status'] = 1;
                            $entry_is_true += 1;

                        }
                        elseif ($v['entry_end_time'] <= $new_time && $new_time < $v['match_start_time']){
                            //等待开赛
                            $save['match_status'] = -2;
                            $match_is_true += 1;

                        }
                        elseif ($v['match_start_time'] <= $new_time && $new_time < $v['match_end_time']){
                            //进行中
                            $save['match_status'] = 2;
                            $match_is_true += 1;

                        }else{
                            //已结束
                            $save['match_status'] = -3;

                        }
                    }
                    $a = $wpdb->update($wpdb->prefix.'match_meta_new',$save,array('id'=>$v['id'],'match_id'=>$v['match_id']));
                }
            }
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
        $match_role = $wpdb->get_results("select *,role_name as value from {$wpdb->prefix}zone_match_role where id in($match_role_id) and status = 1",ARRAY_A);
        if(empty($match_role_id) || empty($match_role)){
            $this->get_404(array('message'=>__('你未拥有该权限,请联系管理员授权', 'nlyd-student'),'return_url'=>home_url('/zone/')));
            return;
        }
        $match_role_list = array();
        foreach ($match_role as $v){
            if($v['role_type'] == 'match'){
                $match_role_list[] = $v;
            }
        }
        //print_r($match_role_list);
        $data['scene_list'] = !empty($match_role_list) ? json_encode($match_role_list) : '';
        //print_r($match_role);
        //print_r($match_role);
        //获取比赛类型
        $match_genre = $wpdb->get_results("select a.ID as id,a.post_title as value,b.meta_value from {$wpdb->prefix}posts a 
                                  left join {$wpdb->prefix}postmeta b on a.ID = b.post_id and b.meta_key='project_alias'
                                  where a.post_type = 'genre' and a.post_status = 'publish' and b.meta_value in('mental_world_cup','digital_brain_king','counting_brain_marathon')");

        $data['match_genre'] = !empty($match_genre) ? json_encode($match_genre) : '';

        /*//获取默认比赛费用
        $set_sql = "select pay_amount match_cost from {$wpdb->prefix}spread_set where spread_type = 'official-match' ";
        $match_cost = $wpdb->get_var($set_sql);
        $data['match_cost'] = !empty($match_cost)? $match_cost :number_format(0);*/

        if(isset($_GET['match_id'])){
            //获取比赛信息
            $sql = "select a.post_title ,c.role_name as scene_title,c.role_alias,d.post_title as genre_title, b.* from {$wpdb->prefix}posts a 
                      left join {$wpdb->prefix}match_meta_new b on a.ID = b.match_id 
                      left join {$wpdb->prefix}zone_match_role c on b.match_scene = c.id 
                      left join {$wpdb->prefix}posts d on b.match_genre = d.ID 
                      where a.ID = {$_GET['match_id']}
                      ";
            $match = $wpdb->get_row($sql,ARRAY_A);
            //print_r($match);
            if(!empty($match['match_start_time'])){
                $match['data_time'] = preg_replace('/\s|:/','-',$match['match_start_time']);
            }
            if(!empty($match['entry_end_time'])){
                $match['data_entry_end_time'] = preg_replace('/\s|:/','-',$match['entry_end_time']);
            }
            if($match['match_status'] == 2 && $match['role_alias'] == 'simulate-match'){
                $match['allow_cancel'] = 'y';
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

        //获取所有项目
        $sql = "select ID,post_title from {$wpdb->prefix}posts where post_type = 'project' and post_status = 'publish' order by menu_order asc ";
        $match_project = $wpdb->get_results($sql,ARRAY_A);
        $default_project = array_column($match_project,'post_title','ID');
        $list = array();
        $match_project_use = get_option('match_project_use')['project_use'];
        foreach ($default_project as $k => $y){
            $project_alias = get_post_meta($k,'project_alias')[0];
            $use_time = $project_alias == 'zxss' ? array_sum(array_values($match_project_use[$project_alias])) : $match_project_use[$project_alias];
            $list[$k]['title'] = $y;
            $list[$k]['use_time'] = $use_time;
        }

        $sql = "select a.id,a.project_id,b.post_title, date_format(a.start_time,'%Y/%m/%d %H:%i') start_time,date_format(a.end_time,'%Y/%m/%d %H:%i') end_time,a.use_time,a.more
                  from {$wpdb->prefix}match_project_more a 
                  left join {$wpdb->prefix}posts b on a.project_id = b.ID
                  where match_id = {$_GET['match_id']}  
                  order by start_time asc 
                   ";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(empty($rows)){
            $this->get_404(_('未查询到比赛项目信息'));
            return;
        }
        foreach ($rows as $val ){
            $k = &$val['project_id'];
            $list[$k]['child'][] = $val;
        }
        //print_r($list);
        $data['list'] = $list;
        //print_r($rows);
        $view = student_view_path.CONTROLLER.'/match-time.php';
        load_view_template($view,$data);
    }
    /**
     * 比赛学员报名/签到情况
     */
    public function matchSignDetail(){
        global $wpdb,$current_user;

        //获取比赛学员签到情况
        $sql = "select count(*) total from {$wpdb->prefix}order a 
                left join {$wpdb->prefix}match_meta_new b on a.match_id = b.match_id
                where a.match_id = {$_GET['match_id']} and a.pay_status in (2,3,4) and b.created_id = {$current_user->ID}";
        $order_total = $wpdb->get_var($sql);
        $data['order_total'] = $order_total > 0 ? $order_total : 0;

        $sql_ = "select count(distinct a.user_id) total from {$wpdb->prefix}match_sign a 
                left join {$wpdb->prefix}match_meta_new b on a.match_id = b.match_id
                where a.match_id = {$_GET['match_id']} and b.created_id = {$current_user->ID}
                group by a.match_id
                ";
        $sign_total = $wpdb->get_var($sql_);
        $data['sign_total'] = $sign_total > 0 ? $sign_total : 0;
        //print_r($data);

        //获取比赛名字
        $post = get_post($_GET['match_id']);
        $data['post_title'] = $post->post_title;

        $view = student_view_path.CONTROLLER.'/match-signDetail.php';
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
    public function grading(){

        global $wpdb,$current_user;
        $sql = "select a.id,a.grading_id,a.status,a.start_time,a.end_time,a.entry_end_time,c.meta_value match_switch
                from {$wpdb->prefix}grading_meta a  
                LEFT JOIN {$wpdb->prefix}postmeta c ON a.grading_id = c.post_id and meta_key = 'default_match_switch'
                where a.created_person = {$current_user->ID}
                ";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($sql);
        if(!empty($rows)){
            $new_time = get_time('mysql');
            $entry_is_true = 0;
            $match_is_true = 0;
            foreach ($rows as $v){
                if($v['status'] != -4){

                    if($v['match_switch'] == 'ON') {
                        if($new_time < $v['entry_end_time']){
                            //报名中
                            $save['status'] = 1;
                            $entry_is_true += 1;
                        }
                        elseif ($v['entry_end_time'] <= $new_time && $new_time < $v['start_time']){
                            //等待开赛
                            $save['status'] = -2;
                            $match_is_true += 1;
                        }
                        elseif ($v['start_time'] <= $new_time && $new_time < $v['end_time']){
                            //进行中
                            $save['status'] = 2;
                            $match_is_true += 1;
                        }else{
                            //已结束
                            $save['status'] = -3;
                        }
                    }
                    $a = $wpdb->update($wpdb->prefix.'grading_meta',$save,array('id'=>$v['id'],'grading_id'=>$v['grading_id']));
                }
            }
        }

        $view = student_view_path.CONTROLLER.'/kaoji-list.php';
        load_view_template($view);
    }
    /**
     * 发布考级
     */
    public function kaojiBuild(){

        global $wpdb,$current_user;
        //获取用户发布比赛的权限
        $match_role_id = $wpdb->get_var("select match_role_id from {$wpdb->prefix}zone_meta where user_id = {$current_user->ID}");
        $match_role = $wpdb->get_results("select *,role_name as value from {$wpdb->prefix}zone_match_role where id in($match_role_id) and status = 1",ARRAY_A);
        if(empty($match_role_id) || empty($match_role)){
            $this->get_404(array('message'=>__('你未拥有该权限,请联系管理员授权', 'nlyd-student'),'return_url'=>home_url('/zone/')));
            return;
        }
        $match_role_list = array();
        foreach ($match_role as $v){
            if($v['role_type'] == 'grading'){
                $match_role_list[] = $v;
            }
        }
        //print_r($match_role_list);
        //考级场景
        $data['scene_list'] = !empty($match_role_list) ? json_encode($match_role_list) : '';
        //考级类别
        $post_id = $wpdb->get_var("select post_id from {$wpdb->prefix}postmeta where meta_key = 'project_alias' and meta_value = 'mental_world_cup'");
        $sql = "select ID id ,post_title value 
                from {$wpdb->prefix}posts 
                where post_parent = {$post_id} and post_status = 'publish'
                " ;
        $rows = $wpdb->get_results($sql,ARRAY_A);
        $data['category_list'] = !empty($rows) ? json_encode($rows) : '';

        if(isset($_GET['grading_id'])){
            //获取考级信息
            $sql = "select a.post_title ,c.role_name as scene_title,c.role_alias,d.post_title as genre_title, b.* from {$wpdb->prefix}posts a 
                      left join {$wpdb->prefix}grading_meta b on a.ID = b.grading_id 
                      left join {$wpdb->prefix}zone_match_role c on b.scene = c.id 
                      left join {$wpdb->prefix}posts d on b.category_id = d.ID 
                      where a.ID = {$_GET['grading_id']}
                      ";
            $match = $wpdb->get_row($sql,ARRAY_A);
            //print_r($match);
            if(!empty($match['start_time'])){
                $match['data_start_time'] = preg_replace('/\s|:/','-',$match['start_time']);
            }
            if(!empty($match['end_time'])){
                $match['data_end_time'] = preg_replace('/\s|:/','-',$match['end_time']);
            }
            if(!empty($match['entry_end_time'])){
                $match['data_entry_end_time'] = preg_replace('/\s|:/','-',$match['entry_end_time']);
            }
            if($match['person_liable'] > 0){
                $match['person_liable_name'] = get_user_meta($match['person_liable'],'user_real_name')[0]['real_name'];
            }

            //print_r($match);
            if($match['status'] == 2 && $match['role_alias'] == 'simulate-grading'){
                $match['allow_cancel'] = 'y';
            }
            $data['match'] = $match;
            //print_r($match);
        }

        $view = student_view_path.CONTROLLER.'/kaoji-build.php';
        load_view_template($view,$data);
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

        //判断课程状态
        global $wpdb,$current_user;
        $sql = "select a.id,count(c.id) entry_total,a.open_quota,a.is_enable,course_start_time,unix_timestamp(course_start_time) start_time,course_end_time,unix_timestamp(course_end_time) end_time
                from {$wpdb->prefix}course a 
                left join {$wpdb->prefix}order c on a.id = c.match_id and c.pay_status in (2,3,4)
                where a.zone_id = {$current_user->ID}
                group by a.id
                order by a.course_start_time desc ,a.is_enable desc";
        $rows =  $wpdb->get_results($sql,ARRAY_A);
        //print_r($rows);
        if(!empty($rows)){
            $time = get_time('mysql');
            foreach ($rows as $k => $v){
                if($v['is_enable'] != -4){
                    $is_enable = '';
                    if($v['course_start_time'] > 0){
                        if($time < $v['course_start_time']){
                            $is_enable = 1; //报名中
                        }
                        elseif ( $v['course_start_time'] <= $time && $time <= $v['course_end_time']){
                            $is_enable = 2; //授课中
                        }
                        else{

                            $is_enable = -3;    //已结课
                        }
                    }else{
                        if($v['entry_total'] < $v['open_quota']){
                            $is_enable = 1;
                        }
                        elseif ($v['entry_total'] >= $v['open_quota']){
                            $is_enable = -2;
                        }
                    }
                    //print_r($is_enable);
                    if(!empty($is_enable)){
                        $a = $wpdb->update($wpdb->prefix.'course',array('is_enable'=>$is_enable),array('id'=>$v['id']));
                    }
                }
            }
        }
        $view = student_view_path.CONTROLLER.'/course-list.php';
        load_view_template($view);
    }
    /**
     * 发布课程
     */
    public function courseBuild(){

        //获取课程类型
        global $wpdb,$current_user;
        $course_type = $wpdb->get_results("select id,type_name value,type_alias role_alias from {$wpdb->prefix}course_type ",ARRAY_A);
        $data['course_type'] = !empty($course_type) ? json_encode($course_type) : '';

        //获取教学类型
        $post_id = $wpdb->get_var("select post_id from {$wpdb->prefix}postmeta where meta_key = 'project_alias' and meta_value = 'mental_world_cup'");
        $sql = "select ID id,post_title value 
                from {$wpdb->prefix}posts 
                where post_parent = {$post_id} and post_status = 'publish'
                " ;
        $category_type = $wpdb->get_results($sql,ARRAY_A);
        //print_r($category_type);
        $data['category_type'] = !empty($category_type) ? json_encode($category_type) : '';

        if(isset($_GET['id'])){
            $sql_ = "select a.*,b.type_name,c.post_title category_type,
                    if(unix_timestamp(course_start_time)>0,date_format(course_start_time,'%Y-%m-%d %H:%i'),'') start_time,
                    if(unix_timestamp(course_end_time)>0,date_format(course_end_time,'%Y-%m-%d %H:%i'),'') end_time
                    from {$wpdb->prefix}course a 
                    left join {$wpdb->prefix}course_type b on a.course_type = b.id
                    left join {$wpdb->prefix}posts c on a.course_category_id = c.ID
                    where a.id = {$_GET['id']} and zone_id = {$current_user->ID}";
            $row = $wpdb->get_row($sql_,ARRAY_A);
            if(empty($row)){
                $this->get_404($this->get_404(array('message'=>__('数据错误', 'nlyd-student'),'return_url'=>home_url('/zone/course/'))));
                return;
            }
            if($row['start_time'] > 0){
                $row['data_start_time'] = preg_replace('/\s|:/','-',$row['start_time']);
            }
            if($row['end_time'] > 0 ){
                $row['data_end_time'] = preg_replace('/\s|:/','-',$row['end_time']);
            }
            //获取授课教练
            $row['coach_name'] = get_user_meta($row['coach_id'],'user_real_name')[0]['real_name'];
            $data['course'] = $row;
        }

        $view = student_view_path.CONTROLLER.'/course-build.php';
        load_view_template($view,$data);
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
        global $wpdb,$current_user;
        $course_title = $wpdb->get_var("select course_title from {$wpdb->prefix}course where id = {$_GET['id']} and zone_id = {$current_user->ID}");
        if(empty($course_title)){
            $this->get_404($this->get_404(array('message'=>__('数据错误', 'nlyd-student'),'return_url'=>home_url('/zone/course/'))));
            return;
        }
        $view = student_view_path.CONTROLLER.'/course-studentList.php';
        load_view_template($view,array('course_title'=>$course_title));
    }

    /**
     * 学员管理
     */
    public function student(){

        $view = student_view_path.CONTROLLER.'/student-by-center.php';
        load_view_template($view);

    }

    /**
     * 分中心学员管理
     */
    public function studentCenter(){
        $view = student_view_path.CONTROLLER.'/student-by-center.php';
        load_view_template($view);
    }
    /**
     * 教练学员管理
     */
    public function studentCoach(){
        $view = student_view_path.CONTROLLER.'/student-by-coach.php';
        load_view_template($view);
    }
    /**
     * 教练学员申请
     */
    public function studentApply(){
        $view = student_view_path.CONTROLLER.'/student-apply-by-coach.php';
        load_view_template($view);
    }
    /**
     * 课程学员
     */
    public function studentDetail(){

        global $wpdb,$current_user;
        //获取教练信息
        $sql = "select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id = {$_GET['student_id']} and meta_key in('user_real_name','user_ID','user_gender','user_head','real_ID','user_ID_Card','coach_brief') 
                  ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(empty($rows)){
            $this->get_404($this->get_404(array('message'=>__('数据错误', 'nlyd-student'),'return_url'=>home_url('/zone/student/'))));
            return;
        }
        //print_r($rows);
        $user_info = array_column($rows,'meta_value','meta_key');

        $student['real_name'] = unserialize($user_info['user_real_name'])['real_name'];
        $student['user_ID'] = !empty($user_info['user_ID']) ? $user_info['user_ID'] : '-';
        $student['user_gender'] = !empty($user_info['user_gender']) ? $user_info['user_gender'] : '-';
        $student['user_age'] = unserialize($user_info['user_real_name'])['real_age'];
        $student['real_ID'] = !empty($user_info['real_ID']) ? hideStar($user_info['real_ID']) : '-';
        $student['user_ID_Card'] = unserialize($user_info['user_ID_Card']);
        $student['coach_brief'] = !empty($user_info['coach_brief']) ? hideStar($user_info['coach_brief']) : '暂无';

        //获取学员教练
        $sql_ = "select a.*,b.meta_value from {$wpdb->prefix}my_coach a 
                  left join {$wpdb->prefix}postmeta b on a.category_id = b.post_id and meta_key = 'project_alias'
                  where a.user_id = {$_GET['student_id']} and a.apply_status = 2 ";
        //print_r($sql_);
        $rows_ = $wpdb->get_results($sql_,ARRAY_A);
        //print_r($rows_);
        if(!empty($rows_)){
            foreach ($rows_ as $val){
                $user_real_name = get_user_meta($val['coach_id'],'user_real_name')[0];
                $coach[$val['meta_value']]  = !empty($user_real_name) ? $user_real_name['real_name'] : '-';
            }
            $student['coach'] = $coach;
        }
        //print_r($coach);
        $row = $wpdb->get_row("select user_mobile,referee_id from {$wpdb->prefix}users where ID = {$_GET['student_id']} ",ARRAY_A);
        $student['user_mobile'] = !empty($row['user_mobile']) ? $row['user_mobile'] : '-';
        $student['referee_id'] = !empty($row['referee_id']) ? $row['referee_id']+10000000 : '-';
        $view = student_view_path.CONTROLLER.'/student-detail.php';
        load_view_template($view,$student);
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
        global $wpdb,$current_user;
        //获取教练信息
        $sql = "select b.meta_key,b.meta_value from {$wpdb->prefix}zone_join_coach a 
                  left join  {$wpdb->prefix}usermeta b on a.coach_id = b.user_id and meta_key in('user_real_name','coach_ID','user_ID','user_gender','user_head','user_images_color','real_ID','user_ID_Card','coach_brief') 
                  where a.coach_id = {$_GET['coach_id']} and zone_id = $current_user->ID";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(empty($rows)){
            $this->get_404($this->get_404(array('message'=>__('数据错误', 'nlyd-student'),'return_url'=>home_url('/zone/coach/'))));
            return;
        }
        $user_info = array_column($rows,'meta_value','meta_key');
        //print_r($user_info);
        $coach_work_photo = !empty($user_info['user_images_color']) ? unserialize($user_info['user_images_color'])[0] : $user_info['user_head'] ;
        $coach['work_photo'] = !empty($coach_work_photo) ? $coach_work_photo : student_css_url.'image/nlyd.png';
        $coach['real_name'] = unserialize($user_info['user_real_name'])['real_name'];
        $coach['coach_ID'] = !empty($user_info['coach_ID']) ? $user_info['coach_ID'] : $user_info['user_ID'];
        $coach['user_gender'] = !empty($user_info['user_gender']) ? $user_info['user_gender'] : '-';
        $coach['real_ID'] = !empty($user_info['user_real_name']) ? hideStar(unserialize($user_info['user_real_name'])['real_ID']) : '-';
        $coach['user_ID_Card'] = unserialize($user_info['user_ID_Card']);
        $coach['coach_brief'] = !empty($user_info['coach_brief']) ? hideStar($user_info['coach_brief']) : '暂无';

        //获取教练技能
        $sql_ = "select a.*,
                  b.user_mobile from {$wpdb->prefix}coach_skill a 
                  left join {$wpdb->prefix}users b on a.coach_id = b.ID
                  where a.coach_id = {$_GET['coach_id']} ";
        $row = $wpdb->get_row($sql_,ARRAY_A);
        $skill = array();
        if(!empty($row)){
            if($row['read'] > 0){
                $skill[] = '速读';
            }
            if ($row['memory'] > 0){
                $skill[] = '记忆';
            }
            if ($row['compute'] > 0){
                $skill[] = '心算';
            }
        }
        $coach['coach_skill'] = !empty($skill) ? arr2str($skill,'/') : '暂无';
        //print_r($row);
        $coach['user_mobile'] = !empty($row['user_mobile']) ? hideStar($row['user_mobile']) : '-';

        //获取教练学员
        $coach['total'] = $wpdb->get_var("select count(*) total from {$wpdb->prefix}my_coach where coach_id = {$_GET['coach_id']} and apply_status = 2");
        //print_r($coach['total']);
        $view = student_view_path.CONTROLLER.'/coach-detail.php';
        load_view_template($view,$coach);
    }


    /**
     * 战队管理
     */
    public function teamDetail(){
        global $wpdb,$current_user;
        $sql = "select a.id,a.team_id,b.post_title,a.team_director,a.team_slogan,a.team_brief from {$wpdb->prefix}team_meta a 
                                      left join {$wpdb->prefix}posts b on a.team_id = b.ID
                                      where team_id = {$_GET['team_id']} ";
        $row = $wpdb->get_row($sql,ARRAY_A);
        if(empty($row)){
            $this->get_404(array('message'=>__('战队信息错误', 'nlyd-student'),'return_url'=>home_url('/zone/')));
            return;
        }
        //获取新的申请人数
        if(!empty($row)){
            $result = $wpdb->get_results("select status,count(*) total from {$wpdb->prefix}match_team where team_id = {$_GET['team_id']} group by status",ARRAY_A);
            if(!empty($result)){
                $row['total'] = array_column($result,'total','status');
                //print_r($row['total']);
            }
        }

        //print_r($row);
        $view = student_view_path.CONTROLLER.'/team.php';
        load_view_template($view,$row);
    }
    /**
     * 填写战队资料
     */
    public function teamBuild(){

        global $wpdb,$current_user;
        if(isset($_GET['team_id'])){

            //获取战队信息
            $sql = "select a.id,b.post_title,a.team_director,a.user_id,a.team_slogan,a.team_brief from {$wpdb->prefix}team_meta a 
                                      left join {$wpdb->prefix}posts b on a.team_id = b.ID
                                      where team_id = {$_GET['team_id']} ";
            $row = $wpdb->get_row($sql,ARRAY_A);
            //print_r($row);
            if(empty($row)){
                $this->get_404(array('message'=>__('战队信息错误', 'nlyd-student'),'return_url'=>home_url('/zone/team/')));
                return;
            }

            if(!empty($row['team_director'])){

                $row['director_name'] = get_user_meta($row['team_director'],'user_real_name')[0]['real_name'];
            }

            if($row['user_id'] == $current_user->ID){
                $row['is_default'] = 'y';
            }
        }
        //print_r($row);
        $view = student_view_path.CONTROLLER.'/team-build.php';
        load_view_template($view,$row);
    }
    /**
     * 添加战队成员
     */
    public function teamAddMember(){
        global $wpdb,$current_user;
        $sql = "select a.id,b.post_title,a.team_director,a.team_slogan,a.team_brief from {$wpdb->prefix}team_meta a 
                                      left join {$wpdb->prefix}posts b on a.team_id = b.ID
                                      where user_id = {$current_user->ID} ";
        $row = $wpdb->get_row($sql,ARRAY_A);
        if(empty($row)){
            $this->get_404(array('message'=>__('请先创建战队', 'nlyd-student'),'return_url'=>home_url('/zone/teamBuild/')));
            return;
        }
        $view = student_view_path.CONTROLLER.'/team-addMember.php';
        load_view_template($view,$row);
    }
    /**
     * 战队列表
     */
    public function team(){
        $view = student_view_path.CONTROLLER.'/team-list.php';
        load_view_template($view);
    }
    /**
     * 战队申请管理
     */
    public function teamApply(){
        $view = student_view_path.CONTROLLER.'/team-apply.php';
        load_view_template($view);
    }
    /**
     * 机构账号密码设置
     */
    public function setting(){

        global $wpdb,$current_user;
        $data['user_login'] = $current_user->data->user_email;
        $sql = "select b.id ,c.user_mobile 
                  from {$wpdb->prefix}zone_meta a 
                  left join {$wpdb->prefix}zone_manager b on a.id = b.zone_id 
                  left join {$wpdb->prefix}users c on b.user_id = c.ID 
                  where a.user_id = {$current_user->ID}";
        $data['list'] = $wpdb->get_results($sql,ARRAY_A);
        //print_r($sql);
        $view = student_view_path.CONTROLLER.'/setting.php';
        load_view_template($view,$data);
    }
    /**
     * 机构账号添加关联账号
     */
    public function settingAdd(){
        $view = student_view_path.CONTROLLER.'/setting-add.php';
        load_view_template($view);
    }
    /**
     * 机构账号密码设置
     */
    public function settingPsw(){
        $view = student_view_path.CONTROLLER.'/setting-psw.php';
        load_view_template($view);
    }
    /**
     * 提现设置
     */
    public function settingCash(){
        global $wpdb,$current_user;
        //获取相关收款设置
        $sql = "select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id = {$current_user->ID} and meta_key in ('aliPay_coin_code','user_coin_code','user_cheques_bank') ";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($rows)){
            $data = array_column($rows,'meta_value','meta_key');
        }
        //print_r($data);
        $view = student_view_path.CONTROLLER.'/setting-cash.php';
        load_view_template($view,$data);
    }

    /**
     * 银行卡提现设置
     */
    public function settingCashCard(){
        global $current_user;
        $bank = get_user_meta($current_user->ID,'user_cheques_bank')[0];
        //print_r($bank);
        $view = student_view_path.CONTROLLER.'/setting-cash-card.php';
        load_view_template($view,$bank);
    }
    /**
     * 微信提现设置
     */
    public function settingCashWechat(){

        global $current_user;
        $img = get_user_meta($current_user->ID,'user_coin_code')[0];

        $view = student_view_path.CONTROLLER.'/setting-cash-wechat.php';
        load_view_template($view,array('img'=>$img[0]));
    }
    /**
     * 支付宝提现设置
     */
    public function settingCashAlipay(){
        global $current_user;
        $img = get_user_meta($current_user->ID,'aliPay_coin_code')[0];

        $view = student_view_path.CONTROLLER.'/setting-cash-alipay.php';
        load_view_template($view,array('img'=>$img[0]));
    }
    /**
     * 推荐管理
     */
    public function recommend(){

        global $wpdb,$current_user;

        //获取我推荐的用户
        $sql = "select a.referee_id,a.ID child_id,b.id zone_id
                 from {$wpdb->prefix}users a 
                 left join {$wpdb->prefix}zone_meta b on a.ID = b.user_id 
                 where a.referee_id = {$current_user->ID} and b.id is null ";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($sql);
        $data['user_total'] = 0;
        if(!empty($rows)){
            /*$parent_total = count($rows);
            $child_total = 0;*/
            $total = count($rows);
            foreach ($rows as $v){
                //print_r($sql_);
                $total += $wpdb->get_var("select count(*)
                                             from {$wpdb->prefix}users a 
                                             left join {$wpdb->prefix}zone_meta b on a.ID = b.user_id 
                                             where a.referee_id = {$v['child_id']} and b.id is null ");
            }
            $data['user_total'] = $total;
            //print_r($total);
        }

        //获取我推荐的机构
        //1级推荐
        $sql_ = "select apply_id from {$wpdb->prefix}zone_meta where referee_id = {$current_user->ID} and user_id > 0";
        //print_r($sql_);
        $rows_ = $wpdb->get_results($sql_,ARRAY_A);
        $data['zone_total'] = 0;
        $total_ = count($rows_);
        //print_r($total_);
        //2级推荐
        $sql__ = "select ID
                 from {$wpdb->prefix}users
                 where referee_id = {$current_user->ID} ";
        $rows__ = $wpdb->get_results($sql__,ARRAY_A);
        if(!empty($rows__)){

            foreach ($rows__ as $v_){
                $total_ += $wpdb->get_var("select count(*)  from {$wpdb->prefix}zone_meta where referee_id = {$v_['ID']} and user_id > 0 ");
            }
            //print_r($total_);
            $data['zone_total'] = $total_;
        }
        //print_r($data);

        $view = student_view_path.CONTROLLER.'/recommend-list.php';
        load_view_template($view,$data);
    }



    /**
     * 推荐管理
     */
    public function data(){
        global $wpdb,$current_user;

        /*if($_SESSION['statistics'] && $_SESSION['statistics']['overdue_time'] > get_time()){
            $data = unserialize($_COOKIE['user_statistics']);
        }else{*/

            //获取开设比赛/考级次数
            $data['match_total'] = $wpdb->get_var("select count(*) total from {$wpdb->prefix}match_meta_new where created_id = {$current_user->ID} and match_scene = 1");
            $data['grading_total'] = $wpdb->get_var("select count(*) total from {$wpdb->prefix}grading_meta where created_person = {$current_user->ID} and b.scene = 1 ");
            //参与比赛/考级人数
            $data['match_order'] = $wpdb->get_var("select count(*) total from {$wpdb->prefix}order a 
                                                         left join {$wpdb->prefix}match_meta_new b on a.match_id = b.match_id
                                                         left join {$wpdb->prefix}zone_match_role c on b.match_scene = c.id
                                                         where a.order_type = 1 and b.created_id = {$current_user->ID} and c.role_alias = 'official-grading' ");
            $data['grading_order'] = $wpdb->get_var("select count(*) total from {$wpdb->prefix}order a 
                                                            left join {$wpdb->prefix}grading_meta b on a.match_id = b.grading_id
                                                            left join {$wpdb->prefix}zone_match_role c on on b.scene = c.id
                                                            where a.order_type = 2 and b.created_person = {$current_user->ID} and c.role_alias = 'official-match'  ");
            //获取考级/比赛收益
            $data['match_income'] = $wpdb->get_var("select sum(user_income) total from {$wpdb->prefix}user_stream_logs where user_id = {$current_user->ID} and (income_type = 'open_match' or income_type = 'recommend_match' or income_type = 'director_match') ");
            $data['grading_income'] = $wpdb->get_var("select sum(user_income) total from {$wpdb->prefix}user_stream_logs where user_id = {$current_user->ID} and (income_type = 'open_grading' or income_type = 'recommend_grading' or income_type = 'director_grading') ");

            //获取累计收益/提现
            $data['user_income'] = $wpdb->get_var("select sum(user_income) from {$wpdb->prefix}user_stream_logs where  user_id = {$current_user->ID} and user_income > 0 ");
            $data['extract_income'] = $wpdb->get_var("select sum(user_income) from {$wpdb->prefix}user_stream_logs where  user_id = {$current_user->ID} and user_income < 0 ");

            //获取开设课程
            $data['course_order'] = $wpdb->get_var("select count(*) from {$wpdb->prefix}course where zone_id = {$current_user->ID} ");

            //获取推荐机构/获取推荐用户
            $sql_ = "select count(*) from {$wpdb->prefix}zone_meta where referee_id = {$current_user->ID} and user_id > 0";
            $data['zone_total'] = $wpdb->get_var($sql_);
            $sql = "select count(*) total
                     from {$wpdb->prefix}users a 
                     left join {$wpdb->prefix}zone_meta b on a.ID = b.user_id 
                     where a.referee_id = {$current_user->ID} and b.id is null ";
            $data['user_total'] = $wpdb->get_var($sql);

            //推荐收益/课程收益
            $data['recommend_income'] = $wpdb->get_var("select sum(user_income) total from {$wpdb->prefix}user_stream_logs where user_id = {$current_user->ID} and (income_type = 'recommend_match_zone' or income_type = 'recommend_trains_zone' or income_type = 'recommend_test_zone') ");
            $data['course_income'] = $wpdb->get_var("select sum(user_income) total from {$wpdb->prefix}user_stream_logs where user_id = {$current_user->ID} and (income_type = 'recommend_course' or income_type = 'recommend_trains_zone' or income_type = 'recommend_test_zone') ");

            //获取机构赛区类型
            $zone_meta = $wpdb->get_row("select zone_match_type,is_double,audit_time,term_time from {$wpdb->prefix}zone_meta where user_id = {$current_user->ID}",ARRAY_A);
            //print_r($zone_meta);
            if($zone_meta['zone_match_type'] == 1){ //战队赛
                $standard = 70;
            }else{  //城市赛
                $standard = $zone_meta['is_double'] == 1 ? 200 : 100;
            }
            //达标人数
            $standard_total = $wpdb->get_var("select count(*) total from {$wpdb->prefix}order a 
                                                    left join {$wpdb->prefix}match_meta_new b on a.match_id = b.match_id
                                                    where a.order_type = 1 and b.created_id = {$current_user->ID} 
                                                    and a.created_time >= {$zone_meta['audit_time']} and a.created_time <= {$zone_meta['term_time']}
                                                    and a.cost > 0
                                                     ");
            $data['is_standard'] = $standard_total > $standard ? 'y' : 'n';

            $_SESSION['user_statistics'] = array('data'=>$data,'overdue_time'=>get_time()+1800);
        //}

        $view = student_view_path.CONTROLLER.'/data-statistics.php';
        load_view_template($view,$data);
    }
    /*
     *机构主体信息页面
     */
    /*public function account(){
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
    }*/

    /**
     * 获取机构信息
     */
    public function get_zone_row($zone_id=''){
        global $wpdb,$current_user;

        if(!empty($zone_id)){
            $where = " a.id = {$zone_id} ";
        }else{
            $where = "a.user_id = '{$current_user->ID}'";
        }
        $sql = "select a.*,
                case a.zone_match_type
                when 1 then '战队精英赛'
                when 2 then '城市赛'
                end zone_match_type_cn,
                b.zone_type_name,b.zone_type_alias,c.user_mobile from {$wpdb->prefix}zone_meta a 
                left join {$wpdb->prefix}zone_type b on a.type_id = b.id 
                left join {$wpdb->prefix}users c on a.user_id = c.ID 
                where {$where} ";
        //print_r($sql);
        $row = $wpdb->get_row($sql,ARRAY_A);
        //print_r($row);
        $row['user_head'] = get_user_meta($current_user->ID,'user_head')[0];
        $row['user_ID'] = get_user_meta($current_user->ID,'user_ID')[0];
        if(!empty($row['id'])){

            $city_arr = str2arr($row['zone_city'],'-');
            if(!empty($city_arr[2])){
                $city = rtrim($city_arr[1],'市').preg_replace('/市|区|县/','',$city_arr[2]);
            }elseif ($city_arr[1] != '市辖区'){
                $city = rtrim($city_arr[1],'市');
            }else{
                $city = rtrim($city_arr[0],'市');
            }
            if(!empty($row['zone_match_type_cn'])){
                $row['zone_title'] = $row['zone_name'].$city.$row['zone_match_type_cn'].'组委会';
            }else{
                $row['zone_title'] = $row['zone_name'].$row['zone_type_name'].' • '.$city;
            }
        }

        //获取负责人/主席/秘书长
        $center_manager = get_user_meta($row['center_manager_id'],'user_real_name')[0];
        $chairman = get_user_meta($row['chairman_id'],'user_real_name')[0];
        $secretary = get_user_meta($row['secretary_id'],'user_real_name')[0];
        $row['center_manager'] = $center_manager['real_name'];
        $row['chairman_name'] = $chairman['real_name'];
        $row['secretary_name'] = $secretary['real_name'];

        //获取推荐人
        $sql = "select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id = {$row['referee_id']} and meta_key in ('user_real_name','user_ID') ";
        //print_r($sql);
        $meta_value = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($meta_value)){
            $meta = array_column($meta_value,'meta_value','meta_key');
            if(!empty($meta['user_real_name'])){
                $referee_user_real_name = unserialize($meta['user_real_name']);
                $row['referee_name'] = $referee_user_real_name['real_name'];
            }
            $row['referee_user_ID'] = $meta['user_ID'];
        }
        //print_r($row);

        return $row;
    }

    /**
     * 分支机构申请页面
     */
    public function apply(){

        global $wpdb,$current_user,$user_info;

        $row = $this->get_zone_row($_GET['zone_id']);
        if($row['zone_match_type'] == 2){
            if($row['is_double'] == 1){
                $row['zone_match_type_cn'] .= '(多)';
            }
            elseif($row['is_double'] == 2){
                $row['zone_match_type_cn'] .= '(单)';
            }
        }
        //print_r($row);
        if(!empty($row)){
            //获取主席
            $row['chairman_phone'] = !empty($row['chairman_id']) ? $wpdb->get_var("select user_mobile from {$wpdb->prefix}users where ID = {$row['chairman_id']} ") : '';
            //获取秘书长
            $row['secretary_phone'] = !empty($row['secretary_id']) ? $wpdb->get_var("select user_mobile from {$wpdb->prefix}users where ID = {$row['secretary_id']} ") : '';

            $data['row'] = $row;
        }

        //获取机构类型
        $data['zone_type_name'] = $wpdb->get_var("select zone_type_name from {$wpdb->prefix}zone_type where id = '{$_GET['type_id']}' ");

        //获取事业管理员
        $user_real_name = get_user_meta($current_user->data->referee_id,'user_real_name')[0];
        if(!empty($user_real_name)){
            $data['referee_name'] = $user_real_name['real_name'];
        }

        //print_r($row);
        $view = student_view_path.CONTROLLER.'/apply.php';
        load_view_template($view,$data);
    }
    /**
     * 分支机构申请页面成功后提示页面
     */
    public function applySuccess(){
        global $wpdb;
        //获取分中心类型
        $data['zone_type_name'] = $wpdb->get_var("select zone_type_name from {$wpdb->prefix}zone_type where id = '{$_GET['type_id']}' ");
        if($data['zone_type_name'] == '赛事'){
            $data['zone_type_name'] = '赛区';
        }
        //print_r($data['zone_type_name']);
        $view = student_view_path.CONTROLLER.'/apply-success.php';
        load_view_template($view,$data);

    }

    /**
     * 获取可提现金额
     */
    public function get_stream_total(){
        global $wpdb,$current_user;
        $where = "user_id = {$current_user->ID} and income_type not in ('open_course','open_grading','cause_manager','cause_minister','extract') and income_status = 1  ";
        $sql3 = "select sum(user_income) stream_total from {$wpdb->prefix}user_stream_logs where {$where} ";
        //print_r($sql3);
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
     * 教练控制台
     */
     public function coachConsole(){
        $view = student_view_path.CONTROLLER.'/coach-console.php';
        load_view_template($view);
    }
    /**
     * 上传教练证书
     */
     public function coachUpload(){
        $view = student_view_path.CONTROLLER.'/coach-upload.php';
        load_view_template($view);
    }
    /**
     * 教练简介
     */
     public function coachIntroduction(){
        $view = student_view_path.CONTROLLER.'/coach-introduction.php';
        load_view_template($view);
    }
    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_enqueue_script('thickbox');
        wp_enqueue_script('my-upload');
        wp_enqueue_style('thickbox');
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
        if(ACTION == 'team' ){
            wp_register_style( 'my-student-teamList', student_css_url.'team.css',array('my-student') );
            wp_enqueue_style( 'my-student-teamList' );
        }
        wp_register_style( 'my-student-zone', student_css_url.'zone/zone.css',array('my-student') );
        wp_enqueue_style( 'my-student-zone' );
    }

}