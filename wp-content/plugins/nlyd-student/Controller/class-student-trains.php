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
            'post_status' => array('publish','draft'),
            'order' => 'ASC',
            'orderby' => 'menu_order',
        );
        $the_query = new WP_Query( $args );

        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view,array('list'=>$the_query->posts));
    }

    public function lists(){

        if(empty($_GET['id'])){
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }

        //获取当前
        $row = get_post($_GET['id']);
        $project_alias = get_post_meta($_GET['id'],'project_alias')[0];

        $args = array(
            'post_type' => array('match-category'),
            'post_status' => array('publish'),
            'post_parent'=>$_GET['id'],
            'order' => 'ASC',
            'orderby' => 'menu_order',
        );
        $the_query = new WP_Query( $args );
        if(!empty($the_query->posts)){
            $list = array();
            foreach ($the_query->posts as $v){
                $list[$v->ID]['title'] = $v->post_title;
            }
            //print_r($list);
            $ids = arr2str(array_column((array)$the_query->posts,'ID'));

            global $wpdb;
            $sql = "SELECT ID,post_title,post_parent FROM {$wpdb->prefix}posts WHERE post_parent in($ids) ORDER BY menu_order ASC ";
            $rows = $wpdb->get_results($sql);
            if(!empty($rows)){
                foreach ($rows as $val){

                    $val->project_alias = get_post_meta($val->ID,'project_alias')[0];
                    $list[$val->post_parent]['children'][] = $val;
                }
            }
            //print_r($list);
        }

        $view = student_view_path.CONTROLLER.'/lists.php';
        load_view_template($view,array('list'=>$list,'post_title'=>$row->post_title,'project_alias'=>$project_alias,'genre_id'=>$_GET['id']));
    }

    /**
     * 专项训练准备页
     */
    public function ready(){
        if(!in_array($_GET['type'],array('reading','memory','arithmetic'))){

            if(empty($_GET['id']) || empty($_GET['type']) || empty($_GET['genre_id'])){
                $this->get_404(__('参数错误', 'nlyd-student'));
                return;
            }
            $project = get_post($_GET['id']);
        }
        else{
            //新建训练记录
            global $wpdb,$current_user;

            $sql = "select a.id history_id,b.id questions_id from {$wpdb->prefix}user_grade_log_history a  
                    left join {$wpdb->prefix}user_grade_logs b on a.id = b.grade_log_id
                    where a.user_id = {$current_user->ID} and a.grade_type = '{$_GET['type']}'
                    order by a.id desc  limit 1
                    ";
            $row = $wpdb->get_row($sql,ARRAY_A);

            if(empty($row) || !empty($row['questions_id'])){
                $insert = array(
                    'user_id'=>$current_user->ID,
                    'genre_id'=>$_GET['genre_id'],
                    'grade_type'=>$_GET['type'],
                    'created_time'=>get_time('mysql'),
                );
                $a = $wpdb->insert($wpdb->prefix.'user_grade_log_history',$insert);
                //var_dump($a);
                if($a){
                    $history_id = $wpdb->insert_id;
                    //print_r($log_id);
                }else{
                    $this->get_404(__('数据错误,请联系管理员', 'nlyd-student'));
                    return;
                }

            }else{

                $history_id = $row['history_id'];
            }
        }


        $genre = get_post($_GET['genre_id']);

        //print_r($row);
        $view = student_view_path.CONTROLLER.'/ready.php';

        load_view_template($view,array('project_title'=>$project->post_title,'genre_title'=>$genre->post_title,'history_id'=>$history_id));
    }

    /**
     * 初始页面
     */
    public function initial(){

        unset($_SESSION['train_list']);

        if(empty($_GET['type'])){
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }
        global $wpdb,$current_user;
        $post_id = '';
        switch ($_GET['type']){
            case 'wzsd':


                //获取已训练文章
                $sql1 = "select post_id from {$wpdb->prefix}user_post_use where user_id = {$current_user->ID} and type = 2";
                //print_r($sql1);
                $post_str = $wpdb->get_var($sql1);
                if(!empty($post_str)){
                    $where = " and b.object_id not in($post_str) ";
                }
                //判断语言
                $language = get_user_meta($current_user->ID,'locale')[0];
                $locale = $language == 'zh_CN' || empty($language) ? 'cn' : 'en';
                //获取文章速读考题
                $sql = "select b.object_id,b.term_taxonomy_id from {$wpdb->prefix}terms a 
                        left join {$wpdb->prefix}term_relationships b on a.term_id = b.term_taxonomy_id 
                        left join {$wpdb->prefix}posts c on b.object_id = c.ID
                        where a.slug = '{$locale}-test-question' and c.post_status = 'publish' {$where}";
                //print_r($sql);

                $rows = $wpdb->get_results($sql,ARRAY_A);

                if(empty($rows)){
                    $this->get_404(array('message'=>__('恭喜您已训练完题库内所有文章，建议您再利用其他渠道来进行文章速读训练。题库更新后欢迎您回来继续挑战！', 'nlyd-student'),'match_url'=>home_url('trains')));
                    return;
                }
                $result = array_column($rows,'object_id');

                //print_r($result);
                $post_id = $result[array_rand($result)];

                //print_r($result);
                $post_id = $result[array_rand($result)];

                //获取文章
                $content = get_post($post_id );
                //print_r($content);

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

                $count_down = 900;
                break;
            case 'szzb':

                $count_down = 1200;
                break;
            case 'kysm':
                $count_down = 600;
                break;
            case 'pkjl':
                $count_down = 900;
                break;
            case 'zxss':
                $count_down = 540;
                break;
            case 'nxss':
                $count_down = 600;
                break;
            default:
                $this->get_404(__('没有该比赛项目', 'nlyd-student'));
                return;
                break;
        }

        $data = array(
            'content'=>$content,
            'count_down'=>$count_down,
            'url'=>home_url('trains/answer/genre_id/'.$_GET['genre_id'].'/type/'.$_GET['type']),
        );
        if(!empty($post_id)){
            $data['url'] .= '/post_id/'.$post_id;
            $data['questions_answer'] = $questions_answer;
            $data['match_questions'] = $match_questions;
        }

        $data['match_more'] = isset($_GET['match_more']) ? $_GET['match_more'] : 1;

        $data['url'] .= '/match_more/'.$data['match_more'];
        $view = student_view_path.CONTROLLER.'/initial.php';
        load_view_template($view,$data);

    }

    /**
     * 答题页面
     */
    public function answer(){
        global $wpdb;
        //var_dump(json_decode(stripslashes($_COOKIE['questions_answer']),true));

        switch ($_GET['type']){
            case 'wzsd':
                if(empty($_GET['post_id'])){

                    $this->get_404(__('参数错误', 'nlyd-student'));
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
                break;
            default:
                break;
        }
        $data['list'] = $kinds;
        $data['match_more'] = isset($_GET['match_more']) ? $_GET['match_more'] : 1;
        //var_dump($_COOKIE);
        $view = student_view_path.CONTROLLER.'/answer.php';
        load_view_template($view,$data);
    }

    /**
     * 训练答题记录
     */
    public function logs(){

        if(isset($_GET['id'])){
            global $wpdb,$current_user;
            $sql = "select * from {$wpdb->prefix}user_train_logs where user_id = {$current_user->ID} and id = {$_GET['id']}";
            $row = $wpdb->get_row($sql,ARRAY_A);

            if(empty($_GET['id']) || empty($row)){
                $this->get_404(__('参数错误', 'nlyd-student'));
                return;
            }

            $match_questions = !empty($row['train_questions']) ? json_decode($row['train_questions'],true) : array();
            $questions_answer = !empty($row['train_answer']) ? json_decode($row['train_answer'],true) : array();
            $my_answer = !empty($row['my_answer']) ? json_decode($row['my_answer'],true) : array();

            if(in_array($row['project_type'],array('wzsd'))){
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
            elseif ($row['project_type'] == 'nxss'){

                $answer = $questions_answer;

                //print_r($answer);
                $answer_array = $answer['result'];
                $questions_answer = $answer['examples'];
                //print_r($answer_array);
                //print_r($questions_answer);die;

                $count_value = array_count_values($answer_array);
                $success_len = !empty($count_value['true']) ? $count_value['true'] : 0;

                $len = count($answer['result']);

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

        }
        else{
            //unset($_SESSION['train_list']);
            if(isset($_SESSION['train_list'])){

                $train_list = $_SESSION['train_list'];
                //print_r($train_list);
                switch ($train_list['project_type']){
                    case 'szzb':
                    case 'pkjl':

                        if(!empty($train_list['my_answer'])){

                            $len = count($train_list['train_questions']);

                            $error_len = count(array_diff_assoc($train_list['train_answer'],$train_list['my_answer']));

                            $score = $train_list['project_type'] == 'szzb' ? 12 : 18;

                            $my_score = ($len-$error_len)*$score;
                            $success_len = $len-$error_len;
                            if ($error_len == 0 && !empty($train_list['my_answer'])){
                                $my_score += $train_list['surplus_time'] * 1;
                            }
                        }else{
                            $my_score = 0;
                        }
                    $match_questions = $train_list['train_questions'];
                    $questions_answer = $train_list['train_answer'];
                    $my_answer = !empty($train_list['my_answer']) ? $train_list['my_answer'] : array();

                    $error_arr = array_diff_assoc($questions_answer,$my_answer);

                    break;
                    case 'kysm':
                    case 'zxss':
                    case 'nxss':

                        $data_arr = $train_list['my_answer'];

                        if(!empty($data_arr)){
                            $match_questions = array_column($data_arr,'question');
                            $questions_answer = array_column($data_arr,'rights');
                            $my_answer = array_column($data_arr,'yours');
                        }
                        if($train_list['project_type'] == 'nxss'){
                            $isRight = array_column($data_arr,'isRight');

                            $success_len = 0;
                            $len = count($data_arr);

                            if(!empty($isRight)){
                                $count_value = array_count_values($isRight);
                                $success_len += $count_value['true'];
                            }
                            //print_r($train_list['my_answer']);
                            $answer_array = $isRight;

                            $my_score = $success_len * 10;

                        }else{

                            $len = !empty($match_questions) ? count($match_questions) : 0;

                            $error_len = count(array_diff_assoc($questions_answer,$my_answer));
                            $my_score = ($len-$error_len)*10;
                            $success_len = $len-$error_len;
                        }

                        break;
                    case 'wzsd':
                        //print_r($_POST);die;
                        $match_questions = $train_list['train_questions'];
                        $questions_answer = $train_list['train_answer'];
                        $my_answer = $train_list['my_answer'];

                        $len = count($questions_answer);
                        $success_len = 0;

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

                            if(isset($train_list['my_answer'][$k])){
                                if(arr2str($arr) == arr2str($train_list['my_answer'][$k])) ++$success_len;
                            }
                        }
                        $my_score = $success_len * 23;
                        if ($success_len == $len){
                            $my_score += $train_list['surplus_time'] * 1;
                        }
                        break;
                    default:
                        break;
                }
            }
            //var_dump($success_len);
        }

        $this->project_type = $_GET['type'];
        $surplus_time = !empty($row['surplus_time']) ? $row['surplus_time'] :$train_list['surplus_time'];
        $genre_id = !empty($row['genre_id']) ? $row['genre_id'] :$train_list['genre_id'];

        $match_more = $_GET['match_more'] + 1;
        $data = array(
            'type'=>$this->project_type,
            'str_len'=>$len,
            'success_length'=>$success_len,
            'use_time'=>$this->get_count_down($this->project_type)-$surplus_time,
            'surplus_time'=>$surplus_time,
            'accuracy'=>$success_len > 0 ? round($success_len/$len,2)*100 : 0,
            'match_questions'=>$match_questions,
            'questions_answer'=>$questions_answer,
            'my_answer'=>$my_answer,
            'answer_array'=>$answer_array,
            'my_score'=>!empty($row['my_score']) ? $row['my_score'] : $my_score,
            'error_arr'=>!empty($error_arr) ? array_keys($error_arr) : array(),
            'recur_url'=>home_url('/trains/initial/genre_id/'.$genre_id.'/type/'.$this->project_type.'/match_more/'.$match_more), //再来一局
            'revert_url'=>$_SERVER['HTTP_HOST'] == 'ydbeta.gjnlyd.com' ? home_url('/trains/lists/id/52692/') : home_url('trains'),//返回项目列表,
            'match_more'=>isset($_GET['match_more']) ? $_GET['match_more'] : 1,
        );


        $view = student_view_path.CONTROLLER.'/answer-log.php';
        load_view_template($view,$data);
    }


    /**
     * 训练历史记录
     */
    public function history(){

        global $wpdb,$current_user;
        if($_GET['alias'] == 'grading'){
            $sql = "select id,grading_type,questions_type,grading_num,my_score,correct_rate,date_format(created_time,'%Y/%m/%d') time ,created_time,
                case questions_type
                    when 'sz' then '随机数字'
                    when 'cy' then '随机词汇'
                    when 'zm' then '随机字母'
                    when 'yzl' then '圆周率'
                    when 'tl' then '听记数字'
                    when 'rm' then '人脉信息'
                    when 'wz' then '国学经典'
                    when 'reading' then '文章速读'
                    when 'zxys' then '正向速算'
                    when 'nxys' then '逆向速算'
                else '--'
                end project_type_cn
                from {$wpdb->prefix}user_grade_logs 
                where user_id = {$current_user->ID} and  questions_type != '' AND grading_num > 0
                order by created_time desc ";
        }
        elseif($_GET['alias'] == 'mental_world_cup'){

            $sql = "select id,my_score,date_format(created_time,'%Y/%m/%d') time ,created_time,project_type,
                case project_type
                when 'szzb' then '数字争霸' 
                when 'kysm' then '快眼扫描' 
                when 'pkjl' then '扑克接力' 
                when 'wzsd' then '文章速读' 
                when 'zxss' then '正向速算' 
                when 'nxss' then '逆向速算' 
                else '--'
                end project_type_cn
                from {$wpdb->prefix}user_train_logs 
                where user_id = {$current_user->ID} and  project_type != ''
                order by created_time desc limit 0,100";
        }
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($rows);
        if(!empty($rows)){

            if($_GET['alias'] == 'grading'){
                $list = array();
                foreach ($rows as $v){
                    $k = $v['grading_num'];
                    $k1 = $v['grading_type'];
                    $list[$k][$k1][] = $v;
                }
                
            }
            elseif($_GET['alias'] == 'mental_world_cup'){
                $list = array();
                foreach ($rows as $v){
                    $k = $v['time'];
                    $list[$k][] = $v;
                }
            }
        }
        print_r($list);
        //die;
        $data['list'] = $list;

        $view = student_view_path.CONTROLLER.'/history.php';
        load_view_template($view,$data);
    }
    /**
     * 训练类型列表
     */
    public function history_list(){
        global $wpdb,$current_user;
        //获取所有比赛类型
        $sql = "select a.ID id ,a.post_title,b.meta_value alias 
                from {$wpdb->prefix}posts a 
                left join {$wpdb->prefix}postmeta b on a.ID = b.post_id and b.meta_key = 'project_alias'
                where post_type = 'genre' and post_status = 'publish' order by menu_order asc 
                ";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($sql);
        if(!empty($rows)){
            foreach ($rows as $key => $val){
                //获取高亮
                $rows[$key]['highlight'] = get_post_meta($val['id'],'genre_highlight')[0];
                //获取上次训练时间
                $prefix = $wpdb->prefix;
                switch ($val['alias']){
                    case 'grading'; //考级训练
                        $table = $prefix.'user_grade_logs';
                        break;
                    case 'mental_world_cup';    //脑力世界杯训练
                        $table = $prefix.'user_train_logs';
                        break;
                }
                $rows[$key]['last_time'] = $wpdb->get_var("select max(created_time) from {$table} ");
            }
        }
        //print_r($rows);
        $data['list'] = $rows;
        $view = student_view_path.CONTROLLER.'/history-list.php';
        load_view_template($view,$data);
    }
  /**
     * 考级训练记录list
     */
    public function history_grade(){
        $view = student_view_path.CONTROLLER.'/history-grade.php';
        load_view_template($view);
    }
    /**
     * @param $type 训练项目
     * @return int|void 倒计时
     */
    public function get_count_down($type){

        switch ($type){
            case 'wzsd':
                $count_down = 900;
                break;
            case 'szzb':

                $count_down = 1200;
                break;
            case 'kysm':
                $count_down = 900;
                break;
            case 'pkjl':
                $count_down = 900;
                break;
            case 'zxss':
                $count_down = 540;
                break;
            case 'nxss':
                $count_down = 600;
                break;
            default:
                $this->get_404(__('没有该比赛项目', 'nlyd-student'));
                return;
                break;
        }
        return $count_down;
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
        wp_register_script( 'student-leavePage',student_js_url.'matchs/leavePage.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-leavePage' );
        if(in_array(ACTION,array('index','lists','history','history_list','history_grade'))){
            wp_register_style( 'my-trains-index', student_css_url.'trains/index.css',array('my-student') );
            wp_enqueue_style( 'my-trains-index' );
        }
    
        //比赛初始页面
        if(ACTION == 'initial'){

            // wp_register_script( 'student-mTouch',student_js_url.'Mobile/mTouch.js',array('jquery'), leo_student_version  );
            // wp_enqueue_script( 'student-mTouch' );
            wp_register_style( 'my-public', student_css_url.'matchs/matching-public.css',array('my-student') );
            wp_enqueue_style( 'my-public' );
            if($_GET['type']=='nxss'){//逆向速算初始页
                wp_register_script( 'student-check24_answer',student_js_url.'matchs/check24_answer.js',array('jquery'), leo_student_version  );
                wp_enqueue_script( 'student-check24_answer' );
                wp_register_style( 'my-student-fastReverse', student_css_url.'matching-fastReverse.css',array('my-student') );
                wp_enqueue_style( 'my-student-fastReverse' );

            }

            if($_GET['type']=='zxss'){//正向速算初始页
                wp_register_style( 'my-student-fastCalculation', student_css_url.'matching-fastCalculation.css',array('my-student') );
                wp_enqueue_style( 'my-student-fastCalculation' );

            }

            if($_GET['type']=='wzsd'){//文章速读初始页
                wp_register_style( 'my-student-matchDetail', student_css_url.'ready-reading.css',array('my-student') );
                wp_enqueue_style( 'my-student-matchDetail' );

            }


            if($_GET['type']=='szzb'){//进入数字争霸准备页面
                wp_register_style( 'my-student-numberBattleReady', student_css_url.'ready-numberBattle.css',array('my-student') );
                wp_enqueue_style( 'my-student-numberBattleReady' );
            }

            if($_GET['type']=='pkjl'){//进入扑克接力准备页面
                wp_register_style( 'my-student-pokerRelayReady', student_css_url.'ready-pokerRelay.css',array('my-student') );
                wp_enqueue_style( 'my-student-pokerRelayReady' );
            }
        }

        //比赛记忆后答题页面
        if(ACTION == 'answer'){
            wp_register_style( 'my-public', student_css_url.'matchs/matching-public.css',array('my-student') );
            wp_enqueue_style( 'my-public' );
            if($_GET['type']=='wzsd'){//文章速读
                wp_register_style( 'my-student-matchDetail', student_css_url.'matching-reading.css',array('my-student') );
                wp_enqueue_style( 'my-student-matchDetail' );
            }

            if($_GET['type']=='szzb'){//数字争霸
                wp_register_style( 'my-student-matching', student_css_url.'matching-numberBattle.css',array('my-student') );
                wp_enqueue_style( 'my-student-matching' );
            }

            if($_GET['type']=='pkjl'){//扑克接力

                wp_register_style( 'my-student-pokerRelay', student_css_url.'matching-pokerRelay.css',array('my-student') );
                wp_enqueue_style( 'my-student-pokerRelay' );
            }
            if($_GET['type']=='kysm'){//快眼扫描比赛页

                wp_register_style( 'my-student-fastScan', student_css_url.'matching-fastScan.css',array('my-student') );
                wp_enqueue_style( 'my-student-fastScan' );

            }
        }

        //答案记录页面
        if(ACTION=='logs'){
            if($_GET['type']=='nxss'){//逆向速算成绩页
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }

            if($_GET['type']=='zxss'){//正向速算成绩页
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }

            if($_GET['type']=='wzsd'){//文章速读成绩页
                wp_register_style( 'my-student-matchDetail', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-matchDetail' );

            }

            if($_GET['type']=='kysm'){//快眼扫描成绩页
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }

            if($_GET['type']=='szzb'){//数字争霸本轮答题记录

                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }
            if($_GET['type']=='pkjl'){//扑克接力本轮答题记录
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