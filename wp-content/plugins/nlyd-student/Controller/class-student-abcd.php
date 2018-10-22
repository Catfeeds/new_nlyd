<?php
use library\TwentyFour;
/**
 * 学生-往期/近期比赛
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Abcd extends Student_Home
{


    public $redis;

    /**
     * @var 比赛id
     */
    public $match_id;

    /**
     * @var 进行中的比赛项目
     */
    public $current_project;

    /**
     * @var 下一轮的比赛项目
     */
    public $next_project;

    /**
     * @var 最后一轮
     */
    public $end_project = '';

    /**
     * @var 第一轮
     */
    public $start_project = '';

    /**
     * @var 比赛别名
     */
    public $project_alias = '';


    public function __construct($action)
    {

        parent::__construct();

        $this->match_id = $_GET['match_id'];
        $this->project_alias = $_GET['project_alias'];
        if(!empty($_GET['project_id'])){
            $this->project_alias = get_post_meta($_GET['project_id'],'project_alias')[0];
        }

        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1',6379,1);
        $this->redis->auth('leo626');

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        $this->ajaxControll = new Student_Ajax();


        //添加短标签
        add_shortcode('abcd-home',array($this,$action));
    }

    public function test(){
        $a = new TwentyFour();
        var_dump($a->calculate(array(5,5,5,9)));die;
    }

    /**
     * 列表
     */
    public function index(){

        global $wpdb;

        $sql = "select a.id,a.match_id,a.match_status,a.match_start_time,a.match_end_time,a.entry_end_time,c.meta_value match_switch
                from {$wpdb->prefix}match_meta_new a  
                LEFT JOIN {$wpdb->prefix}postmeta c ON a.match_id = c.post_id and meta_key = 'default_match_switch'
                ";
        $rows = $wpdb->get_results($sql,ARRAY_A);

        if(!empty($rows)){
            $new_time = get_time('mysql');
            foreach ($rows as $v){

                if($v['match_switch'] == 'ON') {
                    if($new_time < $v['entry_end_time']){
                        //报名中
                        $save['match_status'] = 1;
                    }
                    elseif ($v['entry_end_time'] <= $new_time && $new_time < $v['match_start_time']){
                        //等待开赛
                        $save['match_status'] = -2;
                    }
                    elseif ($v['match_start_time'] <= $new_time && $new_time < $v['match_end_time']){
                        //进行中
                        $save['match_status'] = 2;
                    }else{
                        //已结束
                        $save['match_status'] = -3;
                    }
                }
                $a = $wpdb->update($wpdb->prefix.'match_meta_new',$save,array('id'=>$v['id'],'match_id'=>$v['match_id']));
            }
        }

        $row = $wpdb->get_row('SELECT ID FROM '.$wpdb->prefix.'posts WHERE post_status="publish" AND post_type="match"');

        $view = student_view_path.CONTROLLER.'/matchList.php';
        load_view_template($view,array('row' => $row));
    }

    /**
     * 获取比赛详情
     */
    public function get_match_info($match_id){

        global $wpdb,$current_user;

        if(isset($_GET['test'])){
            $this->redis->delete('match_content'.$match_id);
        }

        //获取比赛详情
        $sql = "select a.ID,a.post_title,a.post_content,b.match_start_time,b.match_use_time,b.match_more,b.match_project_interval,b.match_subject_interval,b.match_address,b.match_cost,
                    b.match_address,b.entry_end_time,b.match_category_order,b.str_bit,b.match_status,c.user_id,c.pay_status,
                    case b.match_status 
                        when -3 then '已结束' 
                        when -2 then '等待开赛' 
                        when -1 then '未开始' 
                        when 1 then '报名中' 
                        when 2 then '比赛中' 
                        end match_status_cn
                    from {$wpdb->prefix}posts a 
                    left join {$wpdb->prefix}match_meta b on a.ID = b.match_id
                    left join {$wpdb->prefix}order c on a.ID = c.match_id and (c.pay_status=2 or c.pay_status=3 or c.pay_status=4) 
                    where a.ID = {$match_id}
                    ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);

        if(empty($rows)){
            //$this->get_404('数据错误');
            return $rows;
        }
        $entry_user_id = array_column($rows,'user_id');
        $match = $rows[0];

        if(in_array($current_user->ID,$entry_user_id)) $match['is_me'] = 'y';
        if(!empty($match['entry_end_time'])) $match['entry_end_time_arr'] = str2arr(get_time('mysql'),'-');
        return $match;
    }


    /**
     * 比赛详情页
     */
    public function info(){

        if(!isset($_GET['match_id'])) {
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }
        global $wpdb,$current_user;

        //获取比赛详情
        $match = $this->get_match_meta($_GET['match_id']);
        if(empty($match)){
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }

        //print_r($match);

        //根据时间修改比赛状态
        if(strtotime($match['entry_end_time']) <= get_time() && get_time() < strtotime($match['match_start_time'])){
            $a = $wpdb->update($wpdb->prefix.'match_meta_new',array('match_status'=>-2),array('match_id'=>$this->match_id));
            $match['match_status'] = -2;
            $match['match_status_cn'] = __('等待开赛', 'nlyd-student');
        }

        if($match['match_status'] == 1){

            //获取比赛项目
            $project = $this->get_match_project($_GET['match_id'],$match['match_project_id']);
            //print_r($project);

            //获取所有报名选手总数
            $sql2 = "select count(a.id) order_total
                  from {$wpdb->prefix}order a
                  right join {$wpdb->prefix}users b on a.user_id = b.ID
                  where a.match_id = {$_GET['match_id']} and (a.pay_status=2 or a.pay_status=3 or a.pay_status=4)
                  order by a.id desc ";
            //print_r($sql2);
            $order_total = (int) $wpdb->get_var($sql2);

        }
        else{

            //判断是否存在缓存文件
            $language = get_user_meta($current_user->ID,'locale')[0];
            $path = leo_student_public_view.'cache/match_info/';
            is_dir($path) OR mkdir($path, 0777, true);   // 如果文件夹不存在，将以递归方式创建该文件夹

            $file_path = $path.$_GET['match_id'].'_'.$language.'.php';

            if(!file_exists($file_path)){

                //获取所有报名选手列表
                $sql2 = "select a.id,a.user_id,a.created_time
                      from {$wpdb->prefix}order a
                      right join {$wpdb->prefix}users b on a.user_id = b.ID
                      where a.match_id = {$_GET['match_id']} and (a.pay_status=2 or a.pay_status=3 or a.pay_status=4)
                      order by a.id desc ";
                $orders = $wpdb->get_results($sql2,ARRAY_A);

                if(!empty($orders)){

                    foreach ($orders as $k => $v){
                        $user = get_user_meta($v['user_id']);
                        $orders[$k]['user_gender'] = $user['user_gender'][0] ? $user['user_gender'][0] : '--' ;
                        $orders[$k]['user_head'] = isset($user['user_head']) ? $user['user_head'][0] : student_css_url.'image/nlyd.png';
                        if(!empty($user['user_real_name'])){
                            $user_real = unserialize($user['user_real_name'][0]);
                            $orders[$k]['real_age'] = $user_real['real_age'];
                            $orders[$k]['nickname'] = $user_real['real_name'];
                        }else{
                            $orders[$k]['real_age'] = '--';
                            $orders[$k]['nickname'] = $user['nickname'][0];
                        }

                        $user_nationality_pic = $user['user_nationality_pic'][0] ? $user['user_nationality_pic'][0] : 'cn' ;
                        $orders[$k]['nationality'] = $user_nationality_pic;
                        $nationality_short = $user['nationality_short'][0] ? $user['nationality_short'][0] : '' ;
                        $orders[$k]['nationality_short'] = $nationality_short;
                    }
                    //print_r($orders);die;
                }
            }

        }


        //判断选手是否报名
        $sql3 = "select id from {$wpdb->prefix}order where user_id = {$current_user->ID} and match_id = {$_GET['match_id']} ";
        $order_id = $wpdb->get_var($sql3);

        if(!empty($order_id)) $match['is_me'] = 'y';

        if($match['is_me'] == 'y' && $match['match_status'] == -2){
            //print_r($this->project_order_array);
            $match['down_time'] = strtotime($match['match_start_time'])-get_time();
            $match['match_url'] = home_url('matchs/matchWaitting/match_id/'.$this->match_id);
            //var_dump($data['match_url']);
        }
        $data = array('match'=>$match,'match_project'=>$project,'total'=>$order_total > 0 ? $order_total : 0,'orders'=>$orders,'language'=>empty($language) ? 'zh_CN' : $language);
        //print_r($data);
        $view = student_view_path.CONTROLLER.'/matchDetail.php';
        load_view_template($view,$data);
    }

    /**
     * 查看比赛规则
     */
    public function matchRule(){

        if(!isset($_GET['project_id'])){
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }
//        $row = get_post($_GET['project_id']);
        $project_alias = get_post_meta($_GET['project_id'],'project_alias', true);
        $data = array(
//            'post_content'=>$row->post_content,
            'project_alias'=>$project_alias,
        );



        $view = student_view_path.CONTROLLER.'/match-Rule.php';
        load_view_template($view,$data);
    }
    /**
     * 比赛等待倒计时页面
     */
    public function matchWaitting(){

        global $wpdb,$current_user;

        if(!isset($_GET['match_id'])){
            //获取用户即将开赛的比赛信息
            $sql = "select a.match_id,a.match_start_time from {$wpdb->prefix}match_meta_new a 
                    left join {$wpdb->prefix}order b on a.match_id = b.match_id
                    WHERE a.match_status = -2 AND a.match_start_time > NOW() AND b.user_id = {$current_user->ID} AND pay_status in(2,3,4) 
                    ORDER BY match_start_time asc limit 1
                    ";
            //print_r($sql);
            $row = $wpdb->get_row($sql,ARRAY_A);
            //var_dump($row);
            if(empty($row)){
                $this->get_404(__('最近暂无比赛', 'nlyd-student'));
                return;
            }
            $this->match_id = $row['match_id'];
        }

        $project_more = $this->get_match_project_more($this->match_id);

        if($project_more == -1){
            $this->get_404(__('未设置比赛项的轮数', 'nlyd-student'));
            return;
        }
        if($project_more['match_status'] == -3 || $this->end_project == 'y'){
            $this->get_404(array('message'=>__('比赛已结束', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/match_id/'.$project_more['match_id'])));
            return;
        }

        if($project_more['match_status'] == 1){
            $this->get_404(__('比赛报名中,暂未开赛', 'nlyd-student'));
            return;
        }

        $project_more_id = !empty($this->current_project) ? $this->current_project['id'] : $project_more['id'];

        //获取本轮比赛答案是否提交
        $sql = "select my_answer,answer_status from {$wpdb->prefix}match_questions where match_id = {$this->match_id} and project_id = {$this->current_project['project_id']} and match_more = {$this->current_project['match_more']}";
        $row = $wpdb->get_row($sql,ARRAY_A);
        $data['answer_status'] = !empty($row['answer_status']) ? $row['answer_status'] : '';

        $data['count_down'] = strtotime($project_more['start_time'])-get_time();
        $data['match_title'] = $project_more['match_title'];
        $data['project_title'] = $project_more['project_title'];
        $data['next_more_num'] = $project_more['more'];
        $data['current_project'] = $this->current_project;
        $data['end_project'] = $this->end_project;
        $data['start_project'] = $this->start_project;

        $data['match_url'] = home_url(CONTROLLER.'/initialMatch/match_id/'.$this->match_id.'/project_alias/'.$project_more['project_alias'].'/project_more_id/'.$project_more_id);

        $buffer_time = get_time()-strtotime($project_more['start_time']);
        if(1 <= $buffer_time && $buffer_time <= 59 ){

            $data['buffer_time'] = true;
            $data['buffer_url'] = $data['match_url'];
        }

        /*print_r($this->current_project);
        print_r($project_more);*/



        $view = student_view_path.CONTROLLER.'/match-waitting.php';
        load_view_template($view,$data);
    }

    /**
     * 设置下一项比赛项目
     */
    public function set_next_match_project(){

    }

    /*
     * 比赛项目初始页
     */
    public function initialMatch(){

        if(empty($_GET['match_id']) || empty($_GET['project_more_id'])){
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }

        global $wpdb,$current_user;

        $row = $this->get_match_order($current_user->ID,$_GET['match_id']);
        //print_r($row);
        if(empty($row)){

            $this->get_404(__('你未报名', 'nlyd-student'));
            return;
        }else{
            if(!in_array($row->pay_status,array(2,3,4))){
                $this->get_404(__('订单未付款', 'nlyd-student'));
                return;
            }
        }

        //获取比赛项数据
        $project_more = $this->get_match_more($_GET['match_id'],$_GET['project_more_id']);
        if(empty($project_more)){

            $this->get_404(__('数据信息错误', 'nlyd-student'));
            return;
        }

        if(get_time() < strtotime($project_more['start_time'])-2){
            $this->get_404(array('message'=>__('等待开赛', 'nlyd-student'),'waiting_url'=>home_url(CONTROLLER.'/matchWaitting/match_id/'.$_GET['match_id'])));
            return;
        }
        //print_r($project_more);

        if($this->project_alias == 'wzsd'){

            //判断语言
            $language = get_user_meta($current_user->ID,'locale')[0];
            $locale = $language == 'zh_CN' || empty($language) ? 'cn' : 'en';
            //获取文章速读考题
            $posts = get_posts(array(
                    'numberposts' => 1, //输出的文章数量
                    'post_type' => 'question',  //自定义文章类型名称
                    'orderby'=>'post_date', //post_date rand
                    'tax_query'=>array(
                        array(
                            'taxonomy'=>'question_genre', //自定义分类法名称
                            'field'  =>  'slug',
                            'terms'=>$locale.'-match-question' //id为64的分类。也可是多个分类array(12,64)
                        )
                    ),
                )
            );
            $question = $posts[0];
            //print_r($question);die;
            if(empty($question)){
                $error_data = array(
                    'status'=>-1,
                    'message'=>__('暂无比赛题目,联系管理员录题', 'nlyd-student'),
                    'match_url'=>home_url('matchs/matchWaitting/match_id/'.$this->match_id.'/wait/1/'),
                );
                $this->get_404($error_data);
                return;
            }
            $post_id = $question->ID;
            //获取比赛题目
            $sql1 = "select a.ID,a.post_title,b.problem_select,problem_answer
                        from {$wpdb->prefix}posts a 
                        left join {$wpdb->prefix}problem_meta b on a.ID = b.problem_id
                        where a.post_parent = {$post_id} order by b.id asc
                        ";

            $rows = $wpdb->get_results($sql1,ARRAY_A);
            $questions_answer = array();
            $match_questions = array();
            if(!empty($rows)){
                $answer_total = 1;  //默认答案个数
                foreach ($rows as $k => $val){
                    //$val['problem_answer'] = 1;
                    $key = &$val['ID'];
                    $questions_answer[$key]['problem_select'][] = $val['problem_select'];
                    $questions_answer[$key]['problem_answer'][] = $val['problem_answer'];
                    //if($val['problem_answer'] == 1) $answer_total += 1;
                }
                $match_questions = array_unique(array_column($rows,'post_title','ID'));
            }
        }
        elseif (in_array($this->project_alias,array('kysm','zxss'))){

            $match_default = get_option('match_project_use');
            if($this->project_alias == 'kysm'){

                $data['child_count_down'] = !empty($match_default['project_default']['kysm']['flicker']) ? $match_default['project_default']['kysm']['flicker'] : 5;
            }else {
                $zxss_default = $match_default['project_use']['zxss'];

                if(empty($zxss_default)){
                    $zxss_default = array(
                        'even_add'=>180,
                        'add_and_subtract'=>180,
                        'wax_and_wane'=>180,
                    );
                }else{
                    $zxss_default['even_add'] *= 60;
                    $zxss_default['add_and_subtract'] *= 60;
                    $zxss_default['wax_and_wane'] *= 60;
                }

                $data['child_count_down'] = $zxss_default;

            }
        }


        //unset($_SESSION['count_down']);

        if(empty($_SESSION['count_down'])){
            $_SESSION['count_down'] = array(
                'match_title'=>$project_more['match_title'],
                'project_title'=>$project_more['project_title'],
                'count_down_time'=>get_time()+ $project_more['use_time']*60,
            );
        }
        else{

            //var_dump($_SESSION['count_down']);

            if($_SESSION['count_down']['match_title'] != $project_more['match_title'] || $_SESSION['count_down']['project_title'] != $project_more['project_title'] || get_time() > $_SESSION['count_down']['count_down_time']){
                $_SESSION['count_down'] = array(
                    'match_title'=>$project_more['match_title'],
                    'project_title'=>$project_more['project_title'],
                    'count_down_time'=>get_time()+ $project_more['use_time']*60,
                );
            }
        }

        $data['count_down'] = $_SESSION['count_down']['count_down_time']-get_time();
        $data['match_title'] = $project_more['match_title'];
        $data['project_title'] = $project_more['project_title'];
        $data['match_more_cn'] = chinanum($project_more['more']);
        $data['project_alias'] = $this->project_alias = $project_more['project_alias'];
        $data['project_id'] = $project_more['project_id'];
        $data['match_more'] = $project_more['more'];
        $data['redirect_url'] = home_url(CONTROLLER.'/answerMatch/match_id/'.$project_more['match_id'].'/project_more_id/'.$project_more['id'].'/project_alias/'.$project_more['project_alias']);

        if(!empty($post_id)){
            $data['questions'] = $question;
            $data['post_id'] = $post_id;
            $data['redirect_url'] .= '/post_id/'.$post_id;
            $data['questions_answer'] = $questions_answer;
            $data['match_questions'] = $match_questions;
        }

        //print_r($data);
        $view = student_view_path.CONTROLLER.'/match-initial.php';
        load_view_template($view,$data);

    }

    public function set_child_count_down(){
        if(in_array($this->project_alias,array('zxss','kysm'))){

            $child_count_down = get_post_meta($this->project_id,'child_count_down')[0];

            if($this->project_alias == 'zxss'){

                if($this->project_key_array[$this->project_id]['child_count_down'] > 0){
                    $child_count_down['even_add'] = $this->project_count_down * 60;
                    $child_count_down['add_and_subtract'] = $this->project_count_down * 60;
                    $child_count_down['wax_and_wane'] = $this->project_count_down * 60;
                }elseif (!empty($child_count_down) && !empty($child_count_down['even_add']) && !empty($child_count_down['add_and_subtract']) && !empty($child_count_down['wax_and_wane'])){

                    $child_count_down['even_add'] *= 60;
                    $child_count_down['add_and_subtract'] *= 60;
                    $child_count_down['wax_and_wane'] *= 60;
                }else{

                    $child_count_down['even_add'] = 180;
                    $child_count_down['add_and_subtract'] = 180;
                    $child_count_down['wax_and_wane'] = 180;
                }

                $this->default_count_down = $child_count_down['even_add']+$child_count_down['add_and_subtract']+$child_count_down['wax_and_wane'];

                global $current_user;
                $new_time = get_time();
                $first_child = $child_count_down['even_add'];
                $two_child = $first_child+$child_count_down['add_and_subtract'];
                $three_child = $two_child+$child_count_down['wax_and_wane'];
                if($_GET['test'] == 1){
                    $this->redis->del('even_add'.$current_user->ID.'_'.$this->current_more);
                    $this->redis->del('add_and_subtract'.$current_user->ID.'_'.$this->current_more);
                    $this->redis->del('wax_and_wane'.$current_user->ID.'_'.$this->current_more);
                }
                if(empty($this->redis->get('even_add'.$current_user->ID.'_'.$this->current_more)) && empty($this->redis->get('add_and_subtract'.$current_user->ID.'_'.$this->current_more)) && empty($this->redis->get('wax_and_wane'.$current_user->ID.'_'.$this->current_more)) ){
                    $this->redis->setex('even_add'.$current_user->ID.'_'.$this->current_more,$first_child,$first_child+$new_time);
                    $first = true;
                }
                if($first){
                    $this->redis->setex('add_and_subtract'.$current_user->ID.'_'.$this->current_more,$two_child,$two_child+$new_time);
                    $two = true;
                }
                if($two){
                    $this->redis->setex('wax_and_wane'.$current_user->ID.'_'.$this->current_more,$three_child,$three_child+$new_time);
                }
                /*var_dump($this->redis->get('even_add'.$current_user->ID.'_'.$this->current_more));
                var_dump($this->redis->get('add_and_subtract'.$current_user->ID.'_'.$this->current_more));
                var_dump($this->redis->get('wax_and_wane'.$current_user->ID.'_'.$this->current_more));*/

                //leo_dump($this->default_count_down);die;
            }else if($this->project_alias == 'kysm'){
                //print_r($this->project_alias);
                if($this->project_key_array[$this->project_id]['child_count_down'] > 0){
                    $child_count_down = $this->project_key_array[$this->project_id]['child_count_down'];
                }elseif (!empty($child_count_down) && !empty($child_count_down['even_add']) && !empty($child_count_down['add_and_subtract']) && !empty($child_count_down['wax_and_wane'])){
                    $child_count_down = $child_count_down;
                }else{
                    $child_count_down = 5;
                }

            }
            //var_dump($child_count_down);
            $this->child_count_down = $child_count_down;
        }
    }

    /*
     * 比赛项目记忆完成答题页
     */
    public function answerMatch(){

        if(empty($_GET['match_id']) || empty($_GET['project_more_id'])){
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }

        global $wpdb,$current_user;

        $row = $this->get_match_order($current_user->ID,$_GET['match_id']);
        //print_r($row);
        if(empty($row)){

            $this->get_404(__('你未报名', 'nlyd-student'));
            return;
        }else{
            if(!in_array($row->pay_status,array(2,3,4))){
                $this->get_404(__('订单未付款', 'nlyd-student'));
                return;
            }
        }

        //获取比赛项数据
        $project_more = $this->get_match_more($_GET['match_id'],$_GET['project_more_id']);
        if(empty($project_more)){

            $this->get_404(__('数据信息错误', 'nlyd-student'));
            return;
        }

        switch ($_GET['project_alias']){
            case 'wzsd':
                if(empty($_GET['post_id'])){

                    $this->get_404('参数错误');
                    return;
                }
                /*if(!empty($_COOKIE['train_match'])){

                    $match_array = json_decode(stripslashes($_COOKIE['train_match']),true);
                    $questions_answer = $match_array['questions_answer'];
                    $match_questions = $match_array['match_questions'];
                    //print_r($match_array);
                }else{*/

                //获取比赛题目
                $sql1 = "select a.ID,a.post_title,b.problem_select,problem_answer
                        from {$wpdb->prefix}posts a 
                        left join {$wpdb->prefix}problem_meta b on a.ID = b.problem_id
                        where a.post_parent = {$_GET['post_id']} order by b.id asc
                        ";

                $rows = $wpdb->get_results($sql1,ARRAY_A);
                $questions_answer = array();
                $match_questions = array();
                if(!empty($rows)){
                    $answer_total = 1;  //默认答案个数
                    foreach ($rows as $k => $val){
                        //$val['problem_answer'] = 1;
                        $key = &$val['ID'];
                        $questions_answer[$key]['problem_select'][] = $val['problem_select'];
                        $questions_answer[$key]['problem_answer'][] = $val['problem_answer'];
                        //if($val['problem_answer'] == 1) $answer_total += 1;
                    }
                    $match_questions = array_unique(array_column($rows,'post_title','ID'));
                }
                //}
                $data['questions_answer'] = $questions_answer;
                $data['match_questions'] = $match_questions;
                $data['count_down'] = $_GET['surplus_time']-1;
                //print_r($questions_answer);
                //print_r($match_questions);
                break;
            case 'pkjl':
                $path = leo_student_public_view.'cache/match_info/pkjl.php';
                if(file_exists($path)){
                    $kinds=array(
                        "spade"=>array(
                            'content'=>[],
                            'color'=>636,
                        ),
                        "heart"=>array(
                            'content'=>[],
                            'color'=>638,
                        ),
                        "club"=>array(
                            'content'=>[],
                            'color'=>635,
                        ),
                        "diamond"=>array(
                            'content'=>[],
                            'color'=>634,
                        ),
                    );
                    //kind数组盛放的是花型
                    $nums=array("A", "2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K" );//52张牌对应的数字

                    foreach ($kinds as $k => $val){
                        foreach ($nums as $v){
                            $kinds[$k]['content'][] = $v;
                        }
                    }
                    $data['list'] = $kinds;
                }
                break;
            default:
                break;

        }

        $data['count_down'] = $_SESSION['count_down']['count_down_time']-get_time();
        $data['match_title'] = $project_more['match_title'];
        $data['project_title'] = $project_more['project_title'];
        $data['match_more_cn'] = chinanum($project_more['more']);
        $data['match_more'] = $project_more['more'];
        $data['project_alias'] = $this->project_alias = $project_more['project_alias'];
        $data['project_id'] = $project_more['project_id'];


        $data['project_alias'] = $this->project_alias;
        //print_r($data);die;
        $view = student_view_path.CONTROLLER.'/match-answer.php';
        load_view_template($view,$data);
    }

    /*
     * 比赛项目答题结果页
     */
    public function answerLog(){

        if(empty($_GET['match_id']) || empty($_GET['log_id']) || empty($_GET['project_alias']) || empty($_GET['project_more_id']) ){

            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }

        global $wpdb,$current_user;

        //清空倒计时
        unset($_SESSION['count_down']);


        $order = $this->get_match_order($current_user->ID,$_GET['match_id']);
        if(empty($order)){
            $this->get_404(__('你未报名', 'nlyd-student'));
            return;
        }else{

            if(!in_array($order->pay_status,array(2,3,4))){
                $this->get_404(__('订单未付款', 'nlyd-student'));
                return;
            }
        }
        //获取答题记录
        $row = $this->get_match_questions($_GET['match_id'],$_GET['log_id']);

        if(empty($row)){
            $this->get_404(__('数据为空,请确认是否参加本轮答题', 'nlyd-student'));
            return;
        }

        //获取当前轮项目
        $match_more = $this->get_match_more($_GET['match_id'],$_GET['project_more_id']);
        if(empty($match_more)){
            $this->get_404(__('数据错误', 'nlyd-student'));
            return;
        }

        $match_questions = json_decode($row['match_questions'],true);
        $questions_answer = json_decode($row['questions_answer'],true);
        $my_answer = !empty($row['my_answer']) ? json_decode($row['my_answer'],true) : array();

        if($row['project_alias'] == 'wzsd'){
            if(empty($questions_answer)){
                $len = 0;
            }else{

                $len = count($questions_answer);
            }
            $success_len = 0;
            if(!empty($questions_answer)){
                foreach ($questions_answer as $k=>$val){
                    $arr = array();
                    $answerArr = array();
                    foreach ($val['problem_answer'] as $key => $v){
                        if($v == 1){
                            $arr[] = $key;
                            $answerArr[] = $key;
                        }
                    }
                    $questions_answer[$k]['problem_answer'] = $answerArr;
                    if(isset($my_answer[$k])){
                        if(arr2str($arr) == arr2str($my_answer[$k])) ++$success_len;
                    }
                }
            }

        }
        elseif ($row['project_alias'] == 'nxss'){

            $answer = $questions_answer;
            $answer_array = $answer['result'];
            $questions_answer = $answer['examples'];
            /*print_r($answer_array);
            print_r($questions_answer);die;*/

            $count_value = array_count_values($answer_array);
            $success_len = !empty($count_value['true']) ? $count_value['true'] : 0;

            $len = count($questions_answer);

        }
        else{

            if(!empty($questions_answer)){
                $len = count($questions_answer);
                if(!empty($my_answer)){

                    $error_arr = array_diff_assoc($questions_answer,$my_answer);
                    $error_len = count($error_arr);
                    $success_len = $len - $error_len;
                }else{
                    $success_len = 0;
                }
            }else{
                $my_answer = array();
                $error_arr = array();
                $success_len = 0;
                $len = 0;
            }
        }

        //print_r($match_more);

        $ranking = '';
        if(strtotime($match_more['end_time']) <= get_time()){
            //获取本轮排名
            $sql = "select user_id from {$wpdb->prefix}match_questions where match_id = {$match_more['match_id']} and project_id = {$match_more['project_id']} and match_more = {$match_more['more']} group by my_score desc,surplus_time desc";
            $rows = $wpdb->get_results($sql,ARRAY_A);

            $ranking = array_search($current_user->ID,array_column($rows,'user_id'))+1;
        }

        //请求接下来的比赛项目
        $next_project_more = $this->get_match_project_more($_GET['match_id']);

        /*print_r($next_project_more);
        print_r($match_more);*/

        if(!empty($next_project_more) && empty($this->end_project)){

            if($next_project_more['project_alias'] != $match_more['project_alias']){
                $next_project = 'y';
            }else{
                $next_project = 'n';
            }

            $next_project_url = home_url(CONTROLLER.'/initialMatch/match_id/'.$next_project_more['match_id'].'/project_alias/'.$next_project_more['project_alias'].'/project_more_id/'.$next_project_more['id']);
        }
        if($this->end_project == 'y'){

            $next_project = '';
            $next_project_url = home_url(CONTROLLER.'/record/match_id/'.$match_more['match_id']);
        }

        $data = array(
            'next_count_down'=> !empty($next_project) ? strtotime($next_project_more['start_time'])-get_time() : 0,
            'next_project'=>$next_project,
            'project_alias'=>$row['project_alias'],
            'str_len'=>$len,
            'match_more_cn'=>chinanum($match_more['more']),
            'match_more'=>$this->match_more,
            'success_length'=>$success_len,
            'use_time'=>$match_more['use_time']*60-$row['surplus_time'],
            'surplus_time'=>$row['surplus_time'],
            'accuracy'=>$success_len > 0 ? round($success_len/$len,2)*100 : 0,
            'ranking'=>$ranking,
            'match_questions'=>$match_questions,
            'questions_answer'=>$questions_answer,
            'my_answer'=>$my_answer,
            'answer_array'=>$answer_array,
            'my_score'=>$row['my_score'],
            'project_title'=>$match_more['project_title'],
            'match_title'=>$this->match_title,
            'error_arr'=>!empty($error_arr) ? array_keys($error_arr) : array(),
            'next_project_url'=>$next_project_url,
            'wait_url' =>home_url('matchs/matchWaitting/match_id/'.$match_more['match_id']),
            'record_url'=>home_url('matchs/record/match_id/'.$match_more['match_id'].'/last/answerLog'),
            'match_row'=>$row
        ,
        );
        //print_r($data);
        $view = student_view_path.CONTROLLER.'/match-answer-log.php';
        load_view_template($view,$data);
    }


    /*
     * 查看本轮答题记录
     */
    public function checkAnswerLog(){

        if(empty($_GET['match_id']) || empty($_GET['project_id']) || empty($_GET['match_more'])){
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }
        global $wpdb,$current_user;

        $row = $this->get_project_all_more($_GET['match_id'],$_GET['project_id'],$_GET['match_more']);
        //print_r($row);
        if(empty($row)){
            $this->get_404(__('数据为空,请确认是否参加本轮答题', 'nlyd-student'));
            return;
        }else{
            if($row['answer_status'] != 1){
                $this->get_404(__('操作错误,你未进行答题', 'nlyd-student'));
                return;
            }
        }

        $match_questions = json_decode($row['match_questions'],true);
        $questions_answer = json_decode($row['questions_answer'],true);
        $my_answer = !empty($row['my_answer']) ? json_decode($row['my_answer'],true) : array();

        //获取比赛类型别名
        $project_alias = get_post_meta($_GET['project_id'],'project_alias')[0];
        //print_r($row);

        if(in_array($project_alias,array('wzsd'))){
            if(empty($questions_answer)){
                $len = 0;
            }else{

                $len = count($questions_answer);
            }
            $success_len = 0;
            if(!empty($questions_answer)){
                foreach ($questions_answer as $k=>$val){
                    $arr = array();
                    $answerArr = array();
                    foreach ($val['problem_answer'] as $key => $v){
                        if($v == 1){
                            $arr[] = $key;
                            $answerArr[] = $key;
                        }
                    }
                    $questions_answer[$k]['problem_answer'] = $answerArr;
                    if(isset($my_answer[$k])){
                        if(arr2str($arr) == arr2str($my_answer[$k])) ++$success_len;
                    }
                }
            }

        }
        elseif ($project_alias == 'nxss'){

            $answer = $questions_answer;
            $answer_array = $answer['result'];
            $questions_answer = $answer['examples'];
            /*print_r($answer_array);
            print_r($questions_answer);*/
            //die;

            $count_value = array_count_values($answer_array);
            $success_len = !empty($count_value['true']) ? $count_value['true'] : 0;

            $len = count($questions_answer);

            /*if(!empty($match_questions)){
                $twentyfour = new TwentyFour();
                foreach ($match_questions as $val){
                    $results = $twentyfour->calculate($val);
                    //print_r($results);
                    $arr[] = !empty($results) ? $results[0] : 'unsolvable';
                }
                $questions_answer = $arr;
            }*/
        }
        else{

            if(!empty($questions_answer)){
                $len = count($questions_answer);
                $error_arr = array_diff_assoc($questions_answer,$my_answer);
                $error_len = count($error_arr);
                $success_len = $len - $error_len;
            }else{
                $my_answer = array();
                $error_arr = array();
                $success_len = 0;
                $len = 0;
            }

        }
        //获取本轮排名
        $sql = "select user_id from {$wpdb->prefix}match_questions where match_id = {$_GET['match_id']} and project_id = {$_GET['project_id']} and match_more = {$_GET['match_more']} group by my_score desc,surplus_time desc";
        $rows = $wpdb->get_results($sql,ARRAY_A);

        $ranking = array_search($current_user->ID,array_column($rows,'user_id'))+1;

        $data = array(
            'project_alias'=>$this->project_alias,
            'str_len'=>$len,
            'match_more_cn'=>chinanum($_GET['match_more']),
            'success_length'=>$success_len,
            'use_time'=>$row['use_time']*60-$row['surplus_time'],
            'surplus_time'=>$row['surplus_time'],
            'accuracy'=>$success_len > 0 ? round($success_len/$len,2)*100 : 0,
            'ranking'=>$ranking,
            'match_questions'=>$match_questions,
            'questions_answer'=>$questions_answer,
            'my_answer'=>$my_answer,
            'answer_array'=>$answer_array,
            'my_score'=>$row['my_score'],
            'project_title'=>$row['project_title'],
            'match_title'=>$row['match_title'],
            'error_arr'=>!empty($error_arr) ? array_keys($error_arr) : array(),
            'record_url'=>home_url('matchs/record/type/project/match_id/'.$row['match_id'].'/project_id/'.$row['project_id'].'/match_more/'.$row['more']),
        );
        if($_GET['type'] == 'select'){

            $data['record_url'] = home_url(CONTROLLER.'/record/match_id/'.$_GET['match_id']);
        }

        //print_r($data);
        $view = student_view_path.CONTROLLER.'/match-answer-log.php';

        load_view_template($view,$data);
    }



    /**
     * 信息确认页
     */
    public function confirm(){

        if(!isset($_GET['match_id'])) {
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }
        //获取比赛详情
        $match = $this->get_match_meta($_GET['match_id']);

        if(empty($match)) {
            $this->get_404(__('比赛信息错误', 'nlyd-student'));
            return;
        }

        //获取比赛项目
        $project = $this->get_match_project($_GET['match_id'],$match['match_project_id']);

        //print_r($match);





        //update_option('match_project_default',true);
        //$project = $this->get_match_project($_GET['match_id'],$match_project_default);
        //print_r($project);
        //获取选手信息
        global $current_user,$user_info,$wpdb;
        $player['real_name'] = isset($user_info['user_real_name']['real_name']) ? $user_info['user_real_name']['real_name'] : '';
        //获取比赛战队
        $sql= "select a.team_id,b.post_title,b.post_content from {$wpdb->prefix}match_team a left join {$wpdb->prefix}posts b on a.team_id = b.ID where a.user_id = {$current_user->ID} and a.status = 2";
        $row = $wpdb->get_row($sql);
        $player['user_team'] = $row->post_title;
        $player['team_id'] = $row->team_id;
        $player['user_ID'] = $user_info['user_ID'];
        //print_r($player);
        //获取邮寄地址
        $where[] = " user_id = {$current_user->ID} ";
        if(isset($_GET['address_id'])){
            $where[] = " id = {$_GET['address_id']} ";
        }else{
            $where[] = " is_default = 1 ";
        }
        $where = join(' and ',$where);
        $sql1 = "select fullname,telephone,concat_ws('',country,province,city,area,address) user_address from {$wpdb->prefix}my_address where {$where}";
        $address = $wpdb->get_row($sql1,ARRAY_A);
        //查询是否已经支付/已经存在订单
        $order = $wpdb->get_row('SELECT id,pay_status FROM '.$wpdb->prefix.'order WHERE user_id='.$current_user->ID.' AND match_id='.$_GET['match_id'], ARRAY_A);
        if($order){
            if($order['pay_status'] == 2  || $order['pay_status'] == 3  || $order['pay_status'] == 4){
                //已支付或待收货或已完成
                $orderStatus['status'] = 2;
            }elseif ($order['pay_status'] == 1){
                //未支付
                $orderStatus['status'] = 0;
            }
            $orderStatus['order_id'] = $order['id'];
        }else{
            $orderStatus['status'] = 0;
            $orderStatus['order_id'] = 0;
        }
        //        print_r($order);
        $data = array('match'=>$match,'match_project'=>$project,'player'=>$player,'address'=>$address, 'orderStatus' => $orderStatus);

        $view = student_view_path.CONTROLLER.'/confirm.php';
        load_view_template($view,$data);
    }
     /**
     * 支付错误信息展示
     */
     public function payError(){
        $view = student_view_path.CONTROLLER.'/payError.php';
        load_view_template($view);
    }

    /**
     * 战绩排名
     */
    public function record(){

        if(empty($_GET['match_id'])){
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }
        global $wpdb,$current_user;

        //获取所有参赛项目
        $result = $this->get_match_all_project($_GET['match_id']);
        if(empty($result)){
            $this->get_404(__('比赛信息错误', 'nlyd-student'));
            return;
        }else{
            $default_category = $result['projects'];
            $match_status = $result['match_status'];
        }
        //print_r($result);

        //判断是否报名该比赛
        $order = $this->get_match_order($current_user->ID,$_GET['match_id']);

        if($match_status != -3){
            if(!in_array($order->pay_status,array(2,3,4))){

                $this->get_404(__('您未报名该比赛,须比赛结束方可查看成绩', 'nlyd-student'));
                return;

            }
        }

        //获取当前项目排名
        $page = ($page = isset($_GET['page']) ? intval($_GET['page']) : 1) < 1 ? 1 : $page;
        $pageSize = 50;
        $start = ($page-1) * $pageSize;

        if($_GET['type'] == 'project'){

            if(empty($_GET['project_id']) || empty($_GET['match_more'])){

                $this->get_404(__('参数错误', 'nlyd-student'));
                return;
            }
            $sql = "select user_id,my_score from {$wpdb->prefix}match_questions where match_id = {$_GET['match_id']} and project_id = {$_GET['project_id']} and match_more = {$_GET['match_more']} order by my_score desc,surplus_time desc limit {$start},{$pageSize} ";
            //print_r($sql);
            $rows = $wpdb->get_results($sql,ARRAY_A);

            $title = $this->match_title;
        }
        else{

            $first = $default_category[0];
            //print_r($first);
            $where = " WHERE a.match_id = {$_GET['match_id']} AND a.pay_status = 4 and a.order_type = 1 ";

            $sql = "SELECT SQL_CALC_FOUND_ROWS x.user_id,SUM(x.my_score) my_score ,SUM(x.surplus_time) surplus_time 
                    FROM(
                        SELECT a.user_id,a.match_id,c.project_id,MAX(c.my_score) my_score , MAX(c.surplus_time) surplus_time 
                        FROM `{$wpdb->prefix}order` a 
                        LEFT JOIN {$wpdb->prefix}match_questions c ON a.user_id = c.user_id  and c.match_id = {$_GET['match_id']} and project_id = {$first['ID']}
                        {$where}
                        GROUP BY user_id,project_id
                    ) x
                    GROUP BY user_id
                    ORDER BY my_score DESC,surplus_time DESC
                    limit 0,50
                    ";
            //print_r($sql);
            /*if($current_user->ID == 66){
                print_r($sql);
            }*/
            $rows = $wpdb->get_results($sql,ARRAY_A);
            //var_dump($rows);
            $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
            //var_dump($total);
            $title = $this->match['post_title'];
            //获取比赛类别
            $match_category = $this->ajaxControll->get_coach_category(false);

        }
        $list = array();
        if(!empty($rows)){
            foreach ($rows as $k => $val){
                $sql1 = " select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id = {$val['user_id']} and meta_key in('user_address','user_ID','user_real_name') ";
                $info = $wpdb->get_results($sql1,ARRAY_A);

                if(!empty($info)){
                    $user_info = array_column($info,'meta_value','meta_key');
                    $user_real_name = !empty($user_info['user_real_name']) ? unserialize($user_info['user_real_name']) : '';

                    $list[$k]['user_name'] = !empty($user_real_name['real_name']) ? $user_real_name['real_name'] : '-';
                    if(!empty($user_real_name['real_age'])){
                        $age = $user_real_name['real_age'];
                        $group = getAgeGroupNameByAge($age);
                    }else{
                        $group = '-';
                    }
                    if(!empty($user_info['user_address'])){
                        $user_address = unserialize($user_info['user_address']);
                        $city = $user_address['city'] == '市辖区' ? $user_address['city'] : $user_address['province'];
                    }else{
                        $city = '-';
                    }
                    $list[$k]['ID'] = $user_info['user_ID'];
                    $list[$k]['city'] = $city;
                    $list[$k]['score'] = $val['my_score'] > 0 ? $val['my_score'] : 0;
                    $list[$k]['group'] = $group;
                    if($val['my_score'] == $rows[$k-1]['my_score'] && $val['surplus_time'] == $rows[$k-1]['surplus_time']){
                        $list[$k]['ranking'] = $list[$k-1]['ranking'];
                    }else{

                        $list[$k]['ranking'] = $k+1;
                    }

                    if($val['user_id'] == $current_user->ID){
                        $my_ranking = $list[$k];
                    }
                }
            }
        }

        $data = array('pay_status'=>$order->pay_status,'list'=>$list,'my_ranking'=>$my_ranking,'match_title'=>$title,'match_category'=>$match_category,'count'=>$total['total'] > 10 ? $total['total'] : 0,'default_category'=>$default_category);
        //print_r($data);
        $view = student_view_path.CONTROLLER.'/record.php';
        load_view_template($view,$data);
    }
    /**
     * 单项成绩排名
     */
    public function singleRecord (){
        if(empty($_GET['match_id']) || empty($_GET['project_id'])){
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }

        //获取当前项目所有轮数
        $rows = $this->get_project_all_more($_GET['match_id'],$_GET['project_id']);
        //print_r($rows);

        //print_r($this->match_alias);
        //var_dump($this->project_key_array[$_GET['project_id']]);
        //判断是否存在我的本轮答题
        global $wpdb,$current_user;
        $sql = "select user_id from {$wpdb->prefix}match_questions where user_id = {$current_user->ID} and match_id = {$_GET['match_id']} and  project_id = {$_GET['project_id']}";
        $user_id = $wpdb->get_results($sql);

        $data = array(
            'my_log'=>!empty($user_id) ? true : false,
            'project_title'=>$rows[0]['project_title'],
            'match_title'=>$rows[0]['match_title'],
            'lists'=>$rows,
            'answer_url'=>home_url('/matchs/checkAnswerLog/match_id/'.$_GET['match_id'].'/project_id/'.$_GET['project_id'].'/type/select'),
        );

        //print_r($data);
        $view = student_view_path.CONTROLLER.'/singleRecord.php';
        load_view_template($view,$data);
    }

    /**
     * 判断进入哪一个比赛项目
     */
    public function matching(){

        //获取比赛信息
        $match = $this->get_match_info($_GET['match_id']);
        if(empty($match)){
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }
        if(!empty($match['match_category_order'])) $match['match_category_order'] = unserialize($match['match_category_order']);
        //print_r($match);
        //判断排序 进入相关页面
        //wp_redirect(home_url('account/matchList/?action=numberBattleReady/match_id/'.$_GET['match_id']));

    }



    /**
     * 获取项目初始配置
     * @param $project_id 比赛项目id
     */
    public function get_project_default($project_id){
        global $wpdb,$current_user;
        $sql = "select a.*,b.post_title 
                    from {$wpdb->prefix}match_project a 
                    left join {$wpdb->prefix}posts b on a.match_id = b.ID 
                    where a.match_id = {$project_id}  ";
        $match_project = $wpdb->get_row($sql,ARRAY_A);
        //leo_dump($sql);
    }


    /**
     * 设置比赛初始配置
     * @param $match_project 当前比赛项目配置
     */
    public function setting_default_config($match_project){

        //每个项目比赛轮数
        $this->default_match_more = $match_project['match_more'] > 0 ? $match_project['match_more'] : $this->match_more;

        //每个项目初始倒计时
        $this->default_count_down = $match_project['project_use_time'] > 0 ? $match_project['project_use_time'] * 60 : $this->match_count_down * 60;

        //每个项目轮数间隔
        $this->default_more_interval = $match_project['project_time_interval'] > 0 ? $match_project['project_time_interval'] * 60: $this->match_subject_interval * 60;

        //当前比赛进行的轮数
        $this->current_more = !empty($_GET['match_more']) ? $_GET['match_more'] : 1;


        //初始字符长度
        if(in_array($this->project_alias,array('szzb','kysm'))){

            $str_length = get_post_meta($match_project['default_str_length'],'default_str_length')[0];

            if($match_project['str_bit'] > 0){
                $default_str_length = $match_project['str_bit'];
            }elseif (!empty($str_length)){
                $default_str_length = $str_length;
            }

            if($this->project_alias == 'szzb'){
                $this->project_str_len = $default_str_length > 0 ? $default_str_length : 100;
            }elseif ($this->project_alias == 'kysm'){
                $this->project_str_len = $default_str_length > 0 ? $default_str_length : 6;
            }
        }

        //print_r($this->current_project);
        $this->project_start_time = $this->current_project['project_start_time'];

        $interval = $this->match_more == $this->current_more ? $this->match_project_interval *60 : $this->default_more_interval;
        //var_dump($this->match_project_interval);
        //var_dump($this->default_more_interval);
        $this->project_end_time = $this->current_project['project_end_time'];
        //print_r(date_i18n('Y-m-d H:i:s',$this->current_project['project_end_time']));
    }


    //判断是否报名
    public function get_match_order($user_id,$match_id){
        global $wpdb;
        $sql = "select id,pay_status from {$wpdb->prefix}order where user_id = {$user_id} and match_id = {$match_id} ";
        //print_r($sql);
        return $wpdb->get_row($sql);
    }


    /**
     * 获取比赛相关信息
     */
    public function get_match_content($match_id){

        $this->match = $this->get_match_info($match_id);
        if(empty($this->match)){
            $this->get_404(array('message'=>__('比賽信息错误', 'nlyd-student'),'match_url'=>home_url('/matchs/')));
            die;
        }
        //print_r($this->match);die;
        $this->match_id = $this->match['ID'];
        $this->match_title = $this->match['post_title'];
        $this->match_start_time = $this->match['match_start_time'];
        $this->match_more = $this->match['match_more'];
        $this->match_count_down = $this->match['match_use_time'];
        $this->match_project_interval = $this->match['match_project_interval'];
        $this->match_subject_interval = $this->match['match_subject_interval'];

        global $wpdb;
        //对比赛项目进行排序
        $sql1 = "SELECT b.post_title,c.meta_value as project_alias,a.post_id match_id,a.match_project_id,a.project_use_time,a.match_more,a.project_start_time,a.project_time_interval,a.str_bit,a.child_count_down
                         FROM {$wpdb->prefix}match_project a
                         LEFT JOIN {$wpdb->prefix}posts b ON a.match_project_id = b.ID
                         LEFT JOIN {$wpdb->prefix}postmeta c ON a.match_project_id = c.post_id AND meta_key = 'project_alias'
                         WHERE a.post_id = {$match_id} ORDER BY a.project_start_time ASC , a.id ASC 
                         ";
        //print_r($sql1);
        $rows = $wpdb->get_results($sql1,ARRAY_A);

        if(empty($rows)){
            $this->get_404(array('message'=>__('该比赛未绑定比赛项', 'nlyd-student'),'match_url'=>home_url('/matchs/info/match_id/'.$match_id)));
            die;
        }
        //print_r($this->match_start_time);
        //计算每个项目结束时间
        foreach ($rows as $k => $row){

            if($row['project_alias'] == 'zxss'){

                $child_count_down = get_post_meta($row['match_project_id'],'child_count_down')[0];
                if($row['child_count_down'] > 0){
                    $child_count_down['even_add'] = $row['child_count_down'];
                    $child_count_down['add_and_subtract'] = $row['child_count_down'];
                    $child_count_down['wax_and_wane'] = $row['child_count_down'];
                }elseif (!empty($child_count_down) && !empty($child_count_down['even_add']) && !empty($child_count_down['add_and_subtract']) && !empty($child_count_down['wax_and_wane'])){
                    //var_dump($child_count_down);
                    $child_count_down['even_add'] *= 1;
                    $child_count_down['add_and_subtract'] *= 1;
                    $child_count_down['wax_and_wane'] *= 1;
                }else{

                    $child_count_down['even_add'] = 3;
                    $child_count_down['add_and_subtract'] = 3;
                    $child_count_down['wax_and_wane'] = 3;
                }
                $project_use_time = $child_count_down['even_add']+$child_count_down['add_and_subtract']+$child_count_down['wax_and_wane'];
                //print_r($project_use_time);die;
            }else{
                $project_use_time = $row['project_use_time'] > 0 ? $row['project_use_time'] : $this->match_count_down;
            }
            $match_more = $row['match_more'] > 0 ? $row['match_more'] : $this->match_more;
            $project_time_interval = $row['project_time_interval'] > 0 ? $row['project_time_interval'] : $this->match_subject_interval;
            //项目间隔时间
            $project_interval = count($rows) - 1 == $k ? 0 :$this->match_project_interval;


            if(strtotime($row['project_start_time']) > 0){
                $end_time = strtotime($row['project_start_time']) + ($project_use_time*$match_more + ($match_more-1)*$project_time_interval+$project_interval)*60;
                $rows[$k]['project_end_time'] = $row['project_end_time'] = date_i18n('Y-m-d H:i:s',$end_time);

            }else{

                $project_end_time = !empty($rows[$k-1]['project_end_time']) ? strtotime($rows[$k-1]['project_end_time']) + $this->match_project_interval*60 : strtotime($this->match_start_time);
                $end_time = $project_end_time + ($project_use_time*$match_more + ($match_more-1)*$project_time_interval)*60;
                $rows[$k]['project_end_time'] = $row['project_end_time'] = date_i18n('Y-m-d H:i:s',$end_time);
                $rows[$k]['project_start_time'] = $row['project_start_time'] = date_i18n('Y-m-d H:i:s',$project_end_time);
            }
            $this->project_key_array[$row['match_project_id']] = $row;

            //leo_dump($rows[$k]['project_start_time'].'-----'.$rows[$k]['project_end_time']);

        }
        //print_r($rows);die;
        $this->project_order_array = $rows;
        $this->match_project_total = count($rows);
        $this->project_id_array = array_column($rows,'match_project_id');

        if(in_array(ACTION,array('matchWaitting','initialMatch','answerMatch','answerLog')) ){

            $start = reset($rows);
            $end = end($rows);
            //print_r($end);
            if(strtotime($start['project_start_time']) > get_time()){

                if($row['project_alias'] == 'zxss'){

                    $child_count_down = get_post_meta($row['match_project_id'],'child_count_down')[0];
                    //var_dump($child_count_down);
                    if($row['child_count_down'] > 0){
                        $even_add = $row['child_count_down'];
                        $add_and_subtract = $row['child_count_down'];
                        $wax_and_wane = $row['child_count_down'];
                    }elseif (!empty($child_count_down) && !empty($child_count_down['even_add']) && !empty($child_count_down['add_and_subtract']) && !empty($child_count_down['wax_and_wane'])){

                        $even_add = $child_count_down['even_add'];
                        $add_and_subtract = $child_count_down['add_and_subtract'];
                        $wax_and_wane = $child_count_down['wax_and_wane'];
                    }else{

                        $even_add = 3;
                        $add_and_subtract = 3;
                        $wax_and_wane = 3;
                    }
                    $match_use_time = $even_add+$add_and_subtract+$wax_and_wane;
                }


                $current_project = array(
                    'match_type'=>'first',
                    'project_title'=>$start['post_title'],
                    'project_id'=>$start['match_project_id'],
                    'match_use_time'=>$match_use_time * 60,
                    'match_more'=>1,
                    'project_num'=>array_search($start['match_project_id'],$this->project_id_array)+1,
                    'project_end_time'=>strtotime($start['project_start_time']),
                    'project_end_time_format'=>$start['project_start_time'],
                );
            }
            else if (strtotime($end['project_end_time']) <= get_time()){
                if(ACTION != 'answerLog'){

                    //修改比赛状态
                    $wpdb->update($wpdb->prefix.'match_meta',array('match_status'=>-3),array('match_id'=>$this->match_id));

                    $error_data = array(
                        'status'=>-1,
                        'message'=>__('比赛结束', 'nlyd-student'),
                        'match_url'=>home_url('/matchs/info/match_id/'.$this->match_id),
                    );
                    $this->get_404($error_data);
                    die;
                }
            }
            else{

                //修改比赛状态
                $wpdb->update($wpdb->prefix.'match_meta',array('match_status'=>2),array('match_id'=>$this->match_id));

                //判断当前时间应该进行哪个项目

                foreach ($rows as $key => $value){

                    //var_dump($value);

                    $next_start_time = strtotime($value['project_start_time']);
                    $next_end_time = !empty($rows[$key+1]) ? strtotime($rows[$key+1]['project_start_time']) : strtotime($value['project_end_time']);
                    //print_r($value);
                    //leo_dump($value['project_start_time'].'-----'.date_i18n('Y-m-d H:i:s',$next_end_time).'********'.date_i18n('Y-m-d H:i:s',get_time()));

                    if($next_start_time <= get_time() && get_time() < $next_end_time){

                        /*$this->current_project['project_start_time'] = date_i18n('Y-m-d H:i:s',$next_start_time);
                        $this->current_project['project_end_time'] = date_i18n('Y-m-d H:i:s',$next_end_time);*/

                        /*
                         * 计算出每一轮的初始
                         */
                        //轮数
                        $project_match_more = $value['match_more'] > 0 ? $value['match_more'] : $this->match_more;
                        //用时
                        $match_use_time = $value['project_use_time'] > 0 ? $value['project_use_time'] : $this->match_count_down;
                        //轮数间隔
                        $more_interval = $value['project_time_interval'] > 0 ? $value['project_time_interval'] : $this->match_subject_interval;

                        //轮数
                        $more_num = 0;
                        //当前比赛项目信息
                        $current_project = array();
                        //下一轮比赛项目信息
                        $next_project = array();

                        for ($i=1;$i<=$project_match_more;++$i){

                            if($value['project_alias'] == 'zxss'){

                                $child_count_down = get_post_meta($value['match_project_id'],'child_count_down')[0];
                                //var_dump($child_count_down);
                                if($value['child_count_down'] > 0){
                                    $even_add = $value['child_count_down'];
                                    $add_and_subtract = $value['child_count_down'];
                                    $wax_and_wane = $value['child_count_down'];
                                }elseif (!empty($child_count_down) && !empty($child_count_down['even_add']) && !empty($child_count_down['add_and_subtract']) && !empty($child_count_down['wax_and_wane'])){

                                    $even_add = $child_count_down['even_add'];
                                    $add_and_subtract = $child_count_down['add_and_subtract'];
                                    $wax_and_wane = $child_count_down['wax_and_wane'];
                                }else{

                                    $even_add = 3;
                                    $add_and_subtract = 3;
                                    $wax_and_wane = 3;
                                }
                                $match_use_time = $even_add+$add_and_subtract+$wax_and_wane;
                                //print_r($zxss_use_time);
                                $project_more_start_time = $next_start_time + ($i-1) * ($match_use_time + $more_interval) * 60;

                                //leo_dump(date_i18n('Y-m-d H:i:s',$project_more_start_time).'*********');

                            }else{

                                $project_more_start_time = $next_start_time + ($i-1) * ($match_use_time + $more_interval) * 60;
                                //leo_dump(date_i18n('Y-m-d H:i:s',$project_more_start_time).'*********');
                            }
                            $interval = $i < $project_match_more ? $more_interval : $this->match_project_interval ;
                            //var_dump($match_use_time);
                            $project_more_end_time = $project_more_start_time + ($match_use_time + $interval) * 60;
                            if($interval == 0){
                                $project_more_end_time = $next_end_time;
                            }
                            //leo_dump(date_i18n('Y-m-d H:i:s',$project_more_end_time));

                            if($project_more_start_time <= get_time() && get_time() < $project_more_end_time){
                                //print_r($value);
                                $current_project = array(
                                    'project_title'=>$value['post_title'],
                                    'project_id'=>$value['match_project_id'],
                                    'match_use_time'=>$match_use_time * 60,
                                    'match_more'=>$i,
                                    'project_num'=>array_search($value['match_project_id'],$this->project_id_array)+1,
                                    'project_start_time'=>$project_more_start_time,
                                    'project_start_time_format'=>date_i18n('Y-m-d H:i:s',$project_more_start_time),
                                    'project_end_time'=>$project_more_end_time , //!empty($rows[$key+1]) ? $project_more_end_time : $project_more_start_time + $match_use_time*60,
                                    'project_end_time_format'=>date_i18n('Y-m-d H:i:s',$project_more_end_time),//!empty($rows[$key+1]) ? date_i18n('Y-m-d H:i:s',$project_more_end_time) : $value['project_end_time'],
                                );
                                //print_r($current_project);

                                if($i == $project_match_more){

                                    if(!empty($rows[$key+1])){
                                        $next_project = array(
                                            'project_title'=>$rows[$key+1]['post_title'],
                                            'project_id'=>$rows[$key+1]['match_project_id'],
                                            'match_more'=>1,
                                            'project_num'=>array_search($rows[$key+1]['match_project_id'],$this->project_id_array)+1,
                                            'project_start_time'=>strtotime($rows[$key+1]['project_start_time']),
                                            'project_start_time_format'=>$rows[$key+1]['project_start_time'],
                                        );
                                    }else{
                                        $current_project['time_type'] = 'end';
                                        $current_project['project_end_time'] = $project_more_start_time + $match_use_time*60;
                                        $current_project['project_end_time_format'] = $value['project_end_time'];
                                    }
                                }
                                break;
                            }

                        }

                        break;
                    }
                }
                /*print_r($current_project);
                print_r($next_project);
                die;*/
            }

            $this->next_project = $next_project;
            $this->current_project = $current_project;
        }
    }


    /*
     * 根据比赛别名获取链接方法
     */
    public function get_match_action($alias){
        switch ($alias){

            case 'szzb':    //数字争霸
                $action = 'numberBattleReady';
                break;
            case 'pkjl':    //扑克接力
                $action = 'pokerRelayReady';
                break;
            case 'zxss':    //正向速算
                $action = 'fastCalculation';
                break;
            case 'nxss':    //逆向速算
                $action = 'fastReverse';
                break;
            case 'wzsd':     //文章速读
                $action = 'readingReady';
                break;
            case 'kysm':    //快眼扫描
                $action = 'fastScan';
                break;
            default:
                $action = 'numberBattleReady';
                break;
        }
        return $action;
    }


    /**
     * 获取比赛meta信息
     * @param $match_id 比赛id
     * @param $find 需要获取的字段
     */
    public function get_match_meta($match_id,$find='a.*,b.post_title'){

        global $wpdb;
        $sql = " select {$find},
                   DATE_FORMAT(a.match_start_time,'%Y-%m-%d %H:%i') match_start_time, 
                   DATE_FORMAT(a.match_end_time,'%Y-%m-%d %H:%i') match_end_time, 
                   DATE_FORMAT(a.entry_end_time,'%Y-%m-%d %H:%i') entry_end_time
                  from {$wpdb->prefix}match_meta_new a 
                  left join {$wpdb->prefix}posts b on a.match_id = b.ID
                  where match_id = {$match_id} ";
        $match = $wpdb->get_row($sql,ARRAY_A);

        return $match;

    }

    /**
     *  获取比赛项目
     * @param $match_id 比赛id
     * @param $match_project_id 比赛项目id集合
     * @return array
     */
    public function get_match_project($match_id,$match_project_id){
        global $wpdb,$current_user;
        $sql = "select a.ID,a.post_title,a.post_parent,c.post_title parent_title
                from {$wpdb->prefix}posts a
                left join {$wpdb->prefix}posts c on a.post_parent = c.ID
                where a.ID in($match_project_id)";
        $projects = $wpdb->get_results($sql,ARRAY_A);
        //print_r($projects);


        $project = array();
        if(!empty($projects)){
            foreach ($projects as $val){
                $k = &$val['post_parent'];
                $project[$k]['parent_title'] = $val['parent_title'];
                //获取主训教练

                if(empty($project[$k]['major_coach']) && empty($project[$k]['coach_id'])){

                    $sql = "select a.coach_id,b.display_name 
                            from {$wpdb->prefix}my_coach a 
                            left join {$wpdb->prefix}users b on a.coach_id = b.ID
                            where user_id = {$current_user->ID} and category_id = $k and major = 1
                            ";
                    $row = $wpdb->get_row($sql);
                    //print_r($row);
                    if(!empty($row)){
                        $project[$k]['major_coach'] = preg_replace('/, /','',$row->display_name);
                        $project[$k]['coach_id'] = $row->coach_id;
                    }
                }
                $project_id = isset($val['match_project_id']) ? $val['match_project_id'] : $val['ID'];
                $val['rule_url'] = home_url('matchs/matchRule/match_id/'.$match_id.'/project_id/'.$project_id);
                $project[$k]['project'][] = $val;

            }
        }
        //print_r($project);
        return $project;
    }

    /**
     * 获取比赛的项目所有轮数信息
     * @param $match_id 比赛id
     */
    public function get_match_project_more($match_id){
        global $wpdb;
        $sql = "select a.*,b.post_title match_title,c.post_title project_title,d.match_status
                from {$wpdb->prefix}match_project_more a
                left join {$wpdb->prefix}posts b on b.ID = a.match_id
                left join {$wpdb->prefix}posts c on c.ID = a.project_id
                left join {$wpdb->prefix}match_meta_new d on d.match_id = a.match_id
                where a.match_id = {$match_id} order by a.start_time asc ";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(empty($rows)){
            return -1; //未设置比赛项的轮数
        }else{
            $new_time = get_time('mysql');
            $num = count($rows);

            foreach ($rows as $k => $v){
                //获取别名

                $rows[$k]['project_alias'] = $v['project_alias'] = get_post_meta($v['project_id'],'project_alias')[0];

                if($v['start_time'] <= $new_time && $new_time <= $v['end_time']){
                    $this->current_project = $v;
                    if($k == $num-1){
                        return $v;
                    }
                }

                if($new_time <= $v['start_time'] ){
                    if($new_time < $rows[0]['start_time']){
                        $this->start_project = 'y';
                    }
                    //print_r($v);
                    //$this->next_project = $rows[$k-1];
                    return $v;

                }elseif ($rows[$num-1]['end_time'] < $new_time){

                    $this->end_project = 'y';
                    return $rows[$num-1];
                }
            }
        }
    }

    /**
     * 获取指定轮数的数据
     * @param $match_id 比赛id
     * @param $match_project_id
     */
    public function get_match_more($match_id,$match_project_id){
        global $wpdb;
        $sql = "select a.*,b.post_title match_title,c.post_title project_title,d.meta_value project_alias
                from {$wpdb->prefix}match_project_more a
                left join {$wpdb->prefix}posts b on b.ID = a.match_id
                left join {$wpdb->prefix}posts c on c.ID = a.project_id
                left join {$wpdb->prefix}postmeta d on a.project_id = d.post_id and meta_key = 'project_alias'
                where a.match_id = {$match_id} and a.id = {$match_project_id}  ";

        $row = $wpdb->get_row($sql,ARRAY_A);
        //print_r($sql);die;
        return $row;
    }

    /**
     * 获取比赛所有项目信息
     * @param $match_id 比赛id
     */
    public function get_match_all_project($match_id){
        global $wpdb;

        $sql = " select match_project_id,match_status from {$wpdb->prefix}match_meta_new where match_id = {$match_id} ";
        $row = $wpdb->get_row($sql,ARRAY_A);
        //print_r($match_project_id);die;
        if(!empty($row)){

            $sql1 = "select a.ID,a.post_title,a.post_parent,c.post_title parent_title
                from {$wpdb->prefix}posts a
                left join {$wpdb->prefix}posts c on a.post_parent = c.ID
                where a.ID in({$row['match_project_id']})";
            $projects = $wpdb->get_results($sql1,ARRAY_A);
            if(!empty($projects)){
                return array('projects'=>$projects,'match_status'=>$row['match_status']);
            }
        }else{
            return '';
        }
        //print_r($projects);
    }

    /**
     * 获取当前比赛项目所有的轮数
     * @param $match_id 比赛id
     * @param $project_id   项目id
     * @param $match_more   项目轮数
     */
    public function get_project_all_more($match_id,$project_id,$match_more=''){

        global $wpdb,$current_user;

        if(!empty($match_more)){
            $str = " and a.more = {$match_more} ";
        }else{

            $str = '';
        }

        $sql = "select a.match_id,a.project_id,a.more,b.user_id,a.use_time,b.match_questions,b.questions_answer,b.my_answer,b.my_score,b.surplus_time,b.answer_status,c.post_title match_title,d.post_title project_title 
                from {$wpdb->prefix}match_project_more a 
                left join {$wpdb->prefix}match_questions b on a.match_id = b.match_id and a.project_id = b.project_id and a.more = b.match_more
                left join {$wpdb->prefix}posts c on a.match_id = c.ID 
                left join {$wpdb->prefix}posts d on a.project_id = d.ID 
                where a.match_id = {$match_id} and a.project_id = {$project_id} {$str}
                ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($match_more)){
            return $rows[0];
        }
        return $rows;
    }


    /**
     * 获取答题记录
     * @param $match_id 比赛id
     * @param $log_id   答题记录
     * @return array|null|object|void
     */
    public function get_match_questions($match_id,$log_id){

        global $wpdb,$current_user;
        $sql = "select a.answer_status,a.submit_type,a.leave_page_time,a.created_microtime,a.match_questions,a.questions_answer,a.my_answer,a.surplus_time,if(a.my_score>0,a.my_score,0) as my_score,b.post_title,c.meta_value project_alias
                    from {$wpdb->prefix}match_questions a 
                    left join {$wpdb->prefix}posts b on a.project_id = b.ID
                    LEFT JOIN {$wpdb->prefix}postmeta c ON a.project_id = c.post_id AND meta_key = 'project_alias'
                    where a.user_id = {$current_user->ID} and a.match_id = {$match_id} and a.id = {$log_id} 
                    ";
        //print_r($sql);
        $row = $wpdb->get_row($sql,ARRAY_A);

        return $row;
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
        wp_register_script( 'student-leavePage',student_js_url.'matchs/leavePage.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-leavePage' );
        wp_localize_script('student-leavePage','_leavePage',[
            'submit'=>__('离开考试页面,自动提交本轮答题','nlyd-student'),
        ]);
        if(ACTION=='info'){//比赛详情页
            wp_register_style( 'my-student-matchDetail', student_css_url.'matchDetail.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchDetail' );

        }


        //比赛初始页面
        if(ACTION == 'initialMatch'){

            // wp_register_script( 'student-mTouch',student_js_url.'Mobile/mTouch.js',array('jquery'), leo_student_version  );
            // wp_enqueue_script( 'student-mTouch' );
            wp_register_style( 'my-public', student_css_url.'matchs/matching-public.css',array('my-student') );
            wp_enqueue_style( 'my-public' );
            if($this->project_alias=='nxss'){//逆向速算初始页
                wp_register_script( 'student-check24_answer',student_js_url.'matchs/check24_answer.js',array('jquery'), leo_student_version  );
                wp_enqueue_script( 'student-check24_answer' );
                wp_register_style( 'my-student-fastReverse', student_css_url.'matching-fastReverse.css',array('my-student') );
                wp_enqueue_style( 'my-student-fastReverse' );

            }

            if($this->project_alias=='zxss'){//正向速算初始页
                wp_register_style( 'my-student-fastCalculation', student_css_url.'matching-fastCalculation.css',array('my-student') );
                wp_enqueue_style( 'my-student-fastCalculation' );

            }

            if($this->project_alias=='wzsd'){//文章速读初始页
                wp_register_style( 'my-student-matchDetail', student_css_url.'ready-reading.css',array('my-student') );
                wp_enqueue_style( 'my-student-matchDetail' );

            }

            if($this->project_alias=='kysm'){//快眼扫描比赛页
                wp_register_style( 'my-student-fastScan', student_css_url.'matching-fastScan.css',array('my-student') );
                wp_enqueue_style( 'my-student-fastScan' );

            }

            if($this->project_alias=='szzb'){//进入数字争霸准备页面
                wp_register_style( 'my-student-numberBattleReady', student_css_url.'ready-numberBattle.css',array('my-student') );
                wp_enqueue_style( 'my-student-numberBattleReady' );
            }

            if($this->project_alias=='pkjl'){//进入扑克接力准备页面
                wp_register_style( 'my-student-pokerRelayReady', student_css_url.'ready-pokerRelay.css',array('my-student') );
                wp_enqueue_style( 'my-student-pokerRelayReady' );
            }
        }

        //比赛记忆后答题页面
        if(ACTION == 'answerMatch'){
            wp_register_style( 'my-public', student_css_url.'matchs/matching-public.css',array('my-student') );
            wp_enqueue_style( 'my-public' );
            if($this->project_alias=='wzsd'){//文章速读
                wp_register_style( 'my-student-matchDetail', student_css_url.'matching-reading.css',array('my-student') );
                wp_enqueue_style( 'my-student-matchDetail' );
            }

            if($this->project_alias=='szzb'){//数字争霸
                wp_register_style( 'my-student-matching', student_css_url.'matching-numberBattle.css',array('my-student') );
                wp_enqueue_style( 'my-student-matching' );
            }

            if($this->project_alias=='pkjl'){//扑克接力
                wp_register_style( 'my-student-pokerRelay', student_css_url.'matching-pokerRelay.css',array('my-student') );
                wp_enqueue_style( 'my-student-pokerRelay' );
            }
        }

        //答案记录页面
        if(in_array(ACTION,array('answerLog','checkAnswerLog'))){
            if($this->project_alias=='nxss'){//逆向速算成绩页
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }

            if($this->project_alias=='zxss'){//正向速算成绩页
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }

            if($this->project_alias=='wzsd'){//文章速读成绩页
                wp_register_style( 'my-student-matchDetail', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-matchDetail' );

            }

            if($this->project_alias=='kysm'){//快眼扫描成绩页
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }

            if($this->project_alias=='szzb'){//数字争霸本轮答题记录
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }
            if($this->project_alias=='pkjl'){//扑克接力本轮答题记录
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }
        }

        if(ACTION=='confirm'){//信息确认页
            wp_register_style( 'my-student-confirm', student_css_url.'confirm.css',array('my-student') );
            wp_enqueue_style( 'my-student-confirm' );
        }
        if(ACTION=='record'){//战绩排名页
            wp_register_script( 'student-share',student_js_url.'share/NativeShare.js', leo_student_version  );
            wp_enqueue_script( 'student-share' );

            wp_register_style( 'my-student-record', student_css_url.'record.css',array('my-student') );
            wp_enqueue_style( 'my-student-record' );

        }
        if(ACTION=='singleRecord'){//单项比赛成绩排名页
            wp_register_script( 'student-share',student_js_url.'share/NativeShare.js', leo_student_version  );
            wp_enqueue_script( 'student-share' );
            wp_register_style( 'my-student-singleRecord', student_css_url.'singleRecord.css',array('my-student') );
            wp_enqueue_style( 'my-student-singleRecord' );
        }

        if(ACTION=='index'){//比赛列表页
            wp_register_style( 'my-student-matchList', student_css_url.'matchList.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchList' );
        }
        if(ACTION=='matchRule'){//比赛规则
            wp_register_style( 'my-student-matchRule', student_css_url.'match-Rule.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchRule' );
        }
        if(ACTION=='matchWaitting'){//比赛等待倒计时页面
            wp_register_style( 'my-student-matchWaitting', student_css_url.'match-waitting.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchWaitting' );
        }
        if(ACTION=='startMatch'){//开始比赛
            wp_register_style( 'my-student-match', student_css_url.'match.css',array('my-student') );
            wp_enqueue_style( 'my-student-match' );
        }
    }
}