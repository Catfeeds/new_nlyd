<?php

/**
 * 学生-考级中心首页
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Grade extends Student_Home
{
    private $ajaxControll;
    public function __construct($action)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        $this->ajaxControll = new Student_Ajax();

        //添加短标签
        add_shortcode('grade-home',array($this,$action));
    }


    /**
     * 考级列表页
     */
    public function index(){

        global $wpdb;
        $sql = "select a.id,a.grading_id,a.status,a.start_time,a.end_time,a.entry_end_time,c.meta_value match_switch
                from {$wpdb->prefix}grading_meta a  
                LEFT JOIN {$wpdb->prefix}postmeta c ON a.grading_id = c.post_id and meta_key = 'default_match_switch'
                ";
        $rows = $wpdb->get_results($sql,ARRAY_A);

        if(!empty($rows)){
            $new_time = get_time('mysql');
            foreach ($rows as $v){

                if($v['match_switch'] == 'ON') {
                    if($new_time < $v['entry_end_time']){
                        //报名中
                        $save['status'] = 1;
                    }
                    elseif ($v['entry_end_time'] <= $new_time && $new_time < $v['start_time']){
                        //等待开赛
                        $save['status'] = -2;
                    }
                    elseif ($v['start_time'] <= $new_time && $new_time < $v['end_time']){
                        //进行中
                        $save['status'] = 2;
                    }else{
                        //已结束
                        $save['status'] = -3;
                    }
                }
                $a = $wpdb->update($wpdb->prefix.'grading_meta',$save,array('id'=>$v['id'],'grading_id'=>$v['grading_id']));
            }
        }

        //获取最近一场考级
        $start_time = $wpdb->get_var("select start_time from {$wpdb->prefix}grading_meta where status = -2 order by start_time asc ");

        if(!empty($start_time)){
            $data['new_grading_time'] = strtotime($start_time)-get_time();
        }

        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view,$data);
    }

    /**
     * 考级详情页
     */
    public function info(){

        global $wpdb,$current_user;
        $match = $this->get_grading($_GET['grad_id']);
        if(empty($match)){
            $this->get_404(array('message'=>'数据错误','return_url'=>home_url('grading')));
            return;
        }
        //print_r($match);
        $data['match'] = $match;

        //获取报名人数
        $total = $wpdb->get_var("select count(*) total from {$wpdb->prefix}order where match_id = {$match['grading_id']} and order_type = 2 and pay_status in (2,3,4)");
        $data['total'] = $total > 0 ? $total : 0;

        //获取订单
        $data['memory_lv'] = $wpdb->get_var("select memory_lv from {$wpdb->prefix}order where match_id = {$match['grading_id']} and user_id = {$current_user->ID}");

        $view = student_view_path.CONTROLLER.'/matchDetail.php';
        load_view_template($view,$data);
    }




    /**
     * 考级测试等待页
     */
    public function matchWaitting(){


        echo '准备页面';die;


        //获取数据
        $row = $this->get_grading($_GET['grad_id']);

        if(empty($row)){
            $this->get_404(array('message'=>__('暂无考级', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
            return;
        }
        if($row['status'] == -3){
            $this->get_404(array('message'=>__('考级已结束', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
            return;
        }
        //print_r($row);
        global $wpdb,$current_user;

        if($row['user_id'] != $current_user->ID){
            $this->get_404(array('message'=>__('未查询到报名信息', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
            return;
        }

        if($row['project_alias'] == 'memory'){ //如果是记忆 获取报名记忆等级
            $row['memory_lv'] = $wpdb->get_var("select memory_lv from {$wpdb->prefix}order where match_id = {$row['grading_id']} and user_id = {$current_user->ID}");
        }
        $row['count_down'] = strtotime($row['start_time']) - get_time();

        $row['redirect_url'] = home_url('gradings/initialMatch/grad_id/'.$row['grading_id'].'/grad_type/'.$row['project_alias']);

        //获取已经参与的比赛项
        $sql = "select questions_type,post_more from {$wpdb->prefix}grading_questions where grading_id = {$row['grading_id']} and user_id = {$current_user->ID}";
        $rows = $wpdb->get_results($sql,ARRAY_A);

        if($row['memory_lv'] > 0){

            if($row['count_down'] < 0 ){


                $questions_type = array_column($rows,'questions_type');

                $keys = array_keys($this->get_grading_parameter($row['memory_lv']));

                $diff = array_diff_assoc($keys,$questions_type);
                /*print_r($questions_type);
                print_r($keys);
                print_r($diff);*/
                $next_key = reset($diff);
                if(!empty($next_key)){
                    $row['redirect_url'] .= '/type/'.$next_key.'/memory_lv/'.$row['memory_lv'];
                }else{
                    $this->get_404(array('message'=>__('你已完成全部作答', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
                    return;
                }
            }else{

                $row['redirect_url'] .= '/type/sz/memory_lv/'.$row['memory_lv'];
            }
            $_SESSION['memory_lv'] = $row['memory_lv'];
        }
        elseif ($row['meta_value'] == 'reading'){
            $post_more = array_column($rows,'post_more');
            $diff = array_diff_assoc(array(1,2,3),$post_more);
            $next_key = reset($diff);
            if(!empty($next_key)){
                $row['redirect_url'] .= '/more/'.$next_key;
            }else{
                $this->get_404(array('message'=>__('你已完成全部作答', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
                return;
            }
        }
        elseif ($row['meta_value'] == 'arithmetic'){
            $questions_type = array_column($rows,'questions_type');
            //print_r($questions_type);die;
            $diff = array_diff_assoc(array('nxys','zxys'),$questions_type);
            //print_r($diff);
            $next_key = reset($diff);
            if(!empty($next_key)){
                $row['redirect_url'] .= '/type/'.$next_key;
            }else{
                $this->get_404(array('message'=>__('你已完成全部作答', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
                return;
            }
        }


        $view = student_view_path.CONTROLLER.'/match-waitting.php';
        load_view_template($view,$row);
    }


    /**
     * 比赛初始页
     */
    public function initial(){

        global $wpdb,$current_user;

        if(empty($_GET['history_id']) || empty($_GET['genre_id']) || empty($_GET['grad_type'])){
           $this->get_404(array('message'=>__('参数错误', 'nlyd-student'),'match_url'=>home_url('/trains/')));
           return;
        }

        if($_GET['grad_type'] == 'memory'){
            $memory_lv = isset($_GET['memory_lv']) ? $_GET['memory_lv'] : $_SESSION['memory_lv'];
            $type = isset($_GET['type']) ? $_GET['type'] : 'sz';
            $project = $this->get_grading_parameter($memory_lv);
            if(empty($project)){
                $this->get_404(array('message'=>__('请确认考级等级', 'nlyd-student'),'match_url'=>home_url('/trains/')));
                return;
            }
            $row['project'] = $project;
            $memory_type = $project[$type];
            if(empty($memory_type)){
                $this->get_404(array('message'=>__('未找到此类型考级题目', 'nlyd-student'),'match_url'=>home_url('/trains/')));
                return;
            }
            //print_r($memory_type);
            $row['memory_type'] = $memory_type;
        }
        elseif ($_GET['grad_type'] == 'reading'){

            if(!isset($_SESSION['match_post_id'])){
                //获取已比赛文章
                $sql1 = "select post_id from {$wpdb->prefix}user_post_use where user_id = {$current_user->ID} and type = 2";
                //print_r($sql1);
                $post_str = $wpdb->get_var($sql1);
                if(!empty($post_str)){
                    $where = " and b.object_id not in({$post_str}) ";
                }
                //判断语言
                $language = get_user_meta($current_user->ID,'locale')[0];
                $locale = $language == 'zh_CN' || empty($language) ? 'cn' : 'en';
                //获取文章速读考题
                $sql = "select b.object_id,b.term_taxonomy_id from {$wpdb->prefix}terms a 
                        left join {$wpdb->prefix}term_relationships b on a.term_id = b.term_taxonomy_id 
                        left join {$wpdb->prefix}posts c on b.object_id = c.ID
                        where a.slug = '{$locale}-test-question' and c.post_status = 'publish' {$where} ";
                //print_r($sql);

                $rows = $wpdb->get_results($sql,ARRAY_A);

                if(empty($rows)){
                    $this->get_404(array('message'=>__('题库暂未更新，联系管理员录题', 'nlyd-student'),'match_url'=>home_url('/trains/')));
                    return;
                }
                $result = array_column($rows,'object_id');
                //print_r($rows);
                $post_id = $result[array_rand($result)];

                //print_r($post_id);

                $_SESSION['match_post_id'] = $post_id;

            }
            else{
                $post_id = $_SESSION['match_post_id'];
            }


            //获取文章
            $question = get_post($post_id );
            //print_r($question);

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
                foreach ($rows as $k => $val){
                    //$val['problem_answer'] = 1;
                    $key = &$val['ID'];
                    $questions_answer[$key]['problem_select'][] = $val['problem_select'];
                    $questions_answer[$key]['problem_answer'][] = $val['problem_answer'];
                    //if($val['problem_answer'] == 1) $answer_total += 1;
                }
                $match_questions = array_unique(array_column($rows,'post_title','ID'));
            }
            $row['questions'] = $question;
            $row['post_id'] = $post_id;
            $row['questions_answer'] = $questions_answer;
            $row['match_questions'] = $match_questions;
            $row['redirect_url'] = home_url(CONTROLLER.'/answerMatch/genre_id/'.$_GET['genre_id'].'/grad_type/'.$_GET['grad_type'].'/post_id/'.$post_id.'/history_id/'.$_GET['history_id']);
            //print_r($row);
        }
        //print_r($num);die;
        $row['history_id'] = $_GET['history_id'];
        $row['project_alias'] = $_GET['grad_type'];
        $row['type_title'] = $this->get_memory_type_title($_GET['type']);

        $view = student_view_path.CONTROLLER.'/match-initial.php';
        load_view_template($view,$row);
    }

    /*
     * 比赛项目记忆完成答题页
     */
    public function answerMatch(){

        unset($_SESSION['match_post_id']);

        if(empty($_GET['post_id'])){
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }

        global $wpdb,$current_user;

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
        //print_r($data);

        $view = student_view_path.CONTROLLER.'/matching-reading.php';
        load_view_template($view,$data);
    }


    /*
     * 比赛项目答题结果页
     */
    public function answerLog(){

        global $wpdb,$current_user;

        if(isset($_GET['log_id'])){

            if(empty($_GET['history_id']) || empty($_GET['log_id']) || empty($_GET['grad_type']) || empty($_GET['type']) ){

                $this->get_404(__('参数错误', 'nlyd-student'));
                return;
            }
        }
        else{

            if(isset($_SESSION['match_data'])){

                $match_data = $_SESSION['match_data'];
                //print_r($match_data);

                $sql1 = "select * from {$wpdb->prefix}match_questions where match_id = {$match_data['match_id']} and project_id = {$match_data['project_id']} and match_more = {$match_data['match_more']} and user_id = {$current_user->ID}";
                $row_1 = $wpdb->get_row($sql1,ARRAY_A);
                //print_r($row);
                if(empty($row_1)){

                    //计算成绩
                    switch ($match_data['project_alias']){
                        case 'szzb':
                        case 'pkjl':

                            if(!empty($match_data['my_answer'])){

                                $len = count($match_data['questions_answer']);

                                $error_len = count(array_diff_assoc($match_data['questions_answer'],$match_data['my_answer']));

                                $score = $match_data['project_type'] == 'szzb' ? 12 : 18;

                                $my_score = ($len-$error_len)*$score;

                                if ($error_len == 0 && !empty($match_data['my_answer'])){
                                    $my_score += $match_data['surplus_time'] * 1;
                                }
                            }else{
                                $my_score = 0;
                            }

                            break;
                        case 'kysm':
                        case 'zxss':
                        case 'nxss':


                            $data_arr = $match_data['my_answer'];
                            //print_r($data_arr);die;
                            if(!empty($data_arr)){
                                $match_questions = array_column($data_arr,'question');
                                $questions_answer = array_column($data_arr,'rights');
                                $match_data['my_answer'] = array_column($data_arr,'yours');
                            }

                            if($_POST['project_alias'] == 'nxss'){
                                $isRight = array_column($data_arr,'isRight');

                                $success_len = 0;
                                if(!empty($isRight)){
                                    $count_value = array_count_values($isRight);
                                    $success_len += $count_value['true'];
                                }
                                $answer['examples'] = $questions_answer;
                                $answer['result'] = $isRight;
                                $questions_answer = $answer;

                                $my_score = $success_len * 10;

                            }else{

                                $len = count($match_questions);
                                $error_len = count(array_diff_assoc($questions_answer,$match_data['my_answer']));
                                $my_score = ($len-$error_len)*10;
                            }


                            $match_data['match_questions'] = $match_questions;
                            $match_data['questions_answer'] = $questions_answer;

                            break;
                        case 'wzsd':
                            //print_r($_POST);die;
                            if(empty($match_data['post_id'])){
                                $this->get_404('文章id不存在');
                            }
                            //print_r($_POST);die;
                            $questions_answer = $match_data['questions_answer'];
                            $len = count($questions_answer);
                            $success_len = 0;

                            foreach ($questions_answer as $k=>$val){
                                $arr = array();
                                foreach ($val['problem_answer'] as $key => $v){
                                    if($v == 1){
                                        $arr[] = $key;
                                    }
                                }

                                if(isset($match_data['my_answer'][$k])){
                                    if(arr2str($arr) == arr2str($match_data['my_answer'][$k])) ++$success_len;
                                }
                            }
                            $my_score = $success_len * 23;
                            if ($success_len == $len){
                                $my_score += $match_data['surplus_time'] * 1;
                            }

                            break;
                        default:
                            break;
                    }

                    $insert = array(
                        'user_id'=>$current_user->ID,
                        'match_id'=>$match_data['match_id'],
                        'project_id'=>$match_data['project_id'],
                        'match_more'=>$match_data['match_more'],
                        'match_questions'=>json_encode($match_data['match_questions']),
                        'questions_answer'=>json_encode($match_data['questions_answer']),
                        'my_answer'=>json_encode($match_data['my_answer']),
                        'surplus_time'=>$match_data['surplus_time'],
                        'my_score'=>$my_score,
                        'answer_status'=>1,
                        'submit_type'=>isset($match_data['submit_type']) ? $match_data['submit_type'] : 1,
                        'leave_page_time'=>isset($match_data['leave_page_time']) ? json_encode($match_data['leave_page_time']) : '',
                        'created_time'=>get_time('mysql'),
                        'created_microtime'=>str2arr(microtime(),' ')[0],
                    );

                    /*print_r($insert);
                    die;*/

                    $result = $wpdb->insert($wpdb->prefix.'match_questions',$insert);
                    if($result){
                        $_GET['log_id'] = $wpdb->insert_id;

                        if(!empty($match_data['post_id']) && $match_data['project_alias'] == 'wzsd'){

                            $sql1 = "select id from {$wpdb->prefix}user_post_use where user_id = {$current_user->ID} and type = 1 ";
                            $use_id = $wpdb->get_row($sql1,ARRAY_A);
                            if($use_id){
                                $sql2 = "UPDATE {$wpdb->prefix}user_post_use SET post_id = if(post_id = '',{$match_data['post_id']},CONCAT_WS(',',post_id,{$match_data['post_id']})) WHERE user_id = {$current_user->ID} and type = 1";
                                $a = $wpdb->query($sql2);
                            }else{

                                $a = $wpdb->insert($wpdb->prefix.'user_post_use',array('user_id'=>$current_user->ID,'post_id'=>$match_data['post_id'],'type'=>1));
                            }

                        }
                    }
                }else{
                    $_GET['log_id'] = $row_1['id'];
                }

            }

        }

        //清空倒计时
        unset($_SESSION['count_down']);

        if(isset($_GET['log_id'])){

            //获取答题记录
            $row = $this->get_grading_questions($_GET['history_id'],$_GET['log_id']);
            //print_r($row);
            if(empty($row)){
                $this->get_404(__('数据为空,请确认是否参加本轮答题', 'nlyd-student'));
                return;
            }
        }

        $grading_questions = json_decode($row['grading_questions'],true);
        $questions_answer = json_decode($row['questions_answer'],true);
        $my_answer = !empty($row['my_answer']) ? json_decode($row['my_answer'],true) : array();

        if($row['questions_type'] == 'wz'){
            $len = 0;
            $success_len = 0;
            if(!empty($my_answer)){
                //print_r($questions_answer);
                //print_r($my_answer);

                foreach ($my_answer as $k => $v){
                    $total = count($v);
                    $len += count($v);
                    //print_r($len.'--');
                    $error_arr=array_diff_assoc($v,$questions_answer[$k]);
                    if(empty($error_arr)){
                        $error_len = 0;
                    }else{
                        $error_len = count($error_arr);
                    }

                    $success_len += $total-$error_len;
                }
            }
        }
        elseif($row['questions_type'] == 'rm'){
            if(!empty($my_answer)){
                $success_len = 0;
                $len = count($my_answer);
                foreach ($my_answer as $k => $v){
                    if($v['name'] == $questions_answer[$k]['name'] && $v['phone'] == $questions_answer[$k]['phone']){
                        $success_len += 1;
                    }
                }
            }

        }
        elseif ($row['questions_type'] == 'reading'){
            if(empty($questions_answer)){
                $len = 0;
            }else{

                $len = count($questions_answer);
            }
            $success_len = 0;
            if(!empty($questions_answer)){
                //print_r($grading_questions);
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
        elseif ($row['questions_type'] == 'nxys'){
            $answer = $questions_answer;
            $answer_array = $answer['result'];
            //print_r($answer);
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
        if($_GET['grad_type'] == 'memory'){

            //请求接下来的比赛项目
            //获取当前项目
            $project = $this->get_grading_parameter($_GET['memory_lv']);
            $keys = array_keys($project);
            $index = array_search($row['questions_type'],$keys)+1;
            $next_index = $keys[$index];
            $next_project = $project[$next_index];
        }
        elseif ($_GET['grad_type'] == 'arithmetic'){
         if($row['questions_type'] == 'zxys'){
             $row['questions_type_cn'] = '正向运算';
             $next_project = $next_index = 'nxys';

         }else{
             $row['questions_type_cn'] = '逆向运算';
         }
        }else{
            $row['grad_type'] = '文章速读';
            $next_index = 'reading';
        }

        //如果为当前考级最后一项就计算考核结果

        if(empty($next_project)){
            //获取所有项目
            $sql_ = "select id,user_id,grade_log_id,grading_type,questions_type,grading_questions,questions_answer,my_answer,correct_rate,my_score,post_str_length,use_time
                      from {$wpdb->prefix}user_grade_logs 
                      where user_id = {$current_user->ID} and grade_log_id = {$_GET['history_id']} and grading_type = '{$_GET['grad_type']}' 
                      ";
            $rows = $wpdb->get_results($sql_,ARRAY_A);
            //print_r($sql_);

            if(!empty($rows)){
                //获取用户技能 (考级测试)
                $rank_row = $wpdb->get_row("select id,`read`,memory,compute from {$wpdb->prefix}user_skill_rank where user_id = {$current_user->ID} and skill_type = 2",ARRAY_A);
                $update1 = array();
                if($_GET['memory_lv'] > 0){

                    $correct_rate = array();
                    foreach ($rows as $v){
                        if($v['questions_type'] != 'wz'){
                            $correct_rate[] = $v['correct_rate'];
                        }else{
                            $gxArr = $v;
                        }
                    }
                    $result = array_sum($correct_rate);
                    if($result >= count($correct_rate) ){
                        $grading_result = 1;
                    }
                    //print_r($correct_rate);
                    if($_GET['memory_lv'] > 2){

                        $gx_questions_answer = json_decode($gxArr['questions_answer'],true);
                        $gx_my_answer = json_decode($gxArr['my_answer'],true);
                        //print_r($gx_questions_answer);
                        //print_r($gx_my_answer);
                        if(!empty($gx_my_answer)){
                            foreach ($gx_my_answer as $x => $y){
                                $length = count($gx_questions_answer[$x]);
                                //print_r($length);
                                $error_arr = array_diff_assoc($gx_questions_answer[$x],$y);
                                $error_len += count($error_arr);

                            }
                            if($error_len > 10){
                                $grading_result = 2;
                            }
                        }

                    }
                    $lv = $_GET['memory_lv'];
                    if($lv > $rank_row['memory']){
                        $update1 = array('memory'=>$lv);
                    }
                    $insert1 = array('user_id'=>$current_user->ID,'memory'=>$lv,'skill_type'=>2);
                }
                elseif($_GET['grad_type']== 'reading'){

                    //print_r($rows);die;
                    $row_ = $rows[0];
                    $grading_result = 2;
                    if($row['correct_rate'] >= 0.7){
                        $rate = $row_['post_str_length']/($row_['use_time']/60);
                        $lv = floor($rate/1000);
                        if($lv > 0){
                            $grading_result = 1;
                            if($lv > $rank_row['read']){
                                $update1 = array('read'=>$lv);
                            }
                            $insert1 = array('user_id'=>$current_user->ID,'read'=>$lv,'skill_type'=>2);
                        }
                    }
                    //print_r($lv);
                }
                elseif($_GET['grad_type']== 'arithmetic'){
                    //print_r($rows);
                    $grading_result = 2;
                    $my_score = array_sum(array_column($rows,'my_score'));
                    $lv = floor($my_score/200);

                    if($lv > 0){
                        $grading_result = 1;
                        if($lv > $rank_row['compute']){
                            $update1 = array('compute'=>$lv);
                        }
                        $insert1 = array('user_id'=>$current_user->ID,'compute'=>$lv,'skill_type'=>2);
                    }
                }

                //$next_project_url = home_url('grade/record/genre_id/'.$_GET['genre_id'].'/grad_type/'.$_GET['grad_type']);
            }

            //更新考级记录数据
            $update = array(
                'grade_result'=>$grading_result,
                'grade_lv'=>$lv > 0 ? $lv : '',
                'created_time'=>get_time('mysql')

            );

            $wpdb->startTrans();
            $a = $wpdb->update($wpdb->prefix.'user_grade_log_history',$update,array('id'=>$_GET['history_id'],'user_id'=>$current_user->ID));

            if(empty($rank_row) && $grading_result == 1){
                $b =  $wpdb->insert($wpdb->prefix.'user_skill_rank',$insert1);
            }else{
                if(!empty($update1)){
                    $b = $wpdb->update($wpdb->prefix.'user_skill_rank',$update1,array('user_id'=>$current_user->ID,'id'=>$rank_row['id'],'skill_type'=>2));
                }else{
                    $b = 1;
                }
            }
            //var_dump($a .'----'. $b);die;
            if($a && $b ){
                $wpdb->commit();
            }else{
                $wpdb->rollback();
            }
            //print_r($a);
            //新建训练记录
            $insert = array(
                'user_id'=>$current_user->ID,
                'genre_id'=>$_GET['genre_id'],
                'grade_type'=>$_GET['grad_type'],
                'created_time'=>get_time('mysql'),
            );
            //print_r($insert);die;
            $c = $wpdb->insert($wpdb->prefix.'user_grade_log_history',$insert);
            //var_dump($a);
            if($c){
                $history_id = $wpdb->insert_id;
                //print_r($log_id);
            }
        }
        else{
            if($_GET['grad_type'] == 'memory'){

                $next_project['title'] = $this->get_memory_type_title($next_index);
            }
            $next_project_url = home_url('grade/initial/genre_id/'.$_GET['genre_id'].'/grad_type/'.$_GET['grad_type'].'/type/'.$next_index.'/history_id/'.$_GET['history_id']);
            if($_GET['memory_lv'] > 0 ){
                $next_project_url .= '/memory_lv/'.$_GET['memory_lv'];
            }
        }

        //print_r($next_project);
        $data = array(
            'next_project'=>$next_project,
            'next_count_down'=>300,
            'str_len'=>$len,
            'success_length'=>$success_len,
            'accuracy'=>$row['correct_rate'] > 0 ? $row['correct_rate']*100 : 0,
            'grading_questions'=>$grading_questions,
            'questions_answer'=>$questions_answer,
            'my_answer'=>$my_answer,
            'answer_array'=>$answer_array,
            'error_arr'=>!empty($error_arr) ? array_keys($error_arr) : array(),
            'next_project_url'=>$next_project_url,
            'match_row'=>$row,
            'grading_result'=>$grading_result,
            'reading_rate'=> $_GET['grad_type'] == 'reading' ? floor($row['post_str_length']/($row['use_time']/60)) : '',
            'grade_lv'=>$lv,
            'recur_url'=>home_url('grade/initial/genre_id/'.$_GET['genre_id'].'/grad_type/'.$_GET['grad_type'].'/history_id/'.$history_id),
            'revert_url'=>home_url('/trains/lists/id/'.$_GET['genre_id'].'/'),

        );
        //print_r($row);
        if($_GET['grad_type'] == 'memory'){
            $data['recur_url'] .= '/type/sz/memory_lv/'.$_GET['memory_lv'];
        }
        //print_r($row);
        if($grading_result == 1){
            $grade_result = $lv.'级'.'已达标';
        }else{
            $grade_result = '未达标';
            if($row['grading_type'] == 'memory'){
                //var_dump($lv);
                $grade_result = $lv.$grade_result;
            }
        }
        $data['grade_result'] = $row['grading_type_cn'].$grade_result;
        //print_r($data['grade_result']);
        $view = student_view_path.CONTROLLER.'/match-answer-log.php';
        load_view_template($view,$data);
        exit;
    }

    /**
     * 考级成绩页
     */
    public function record(){//考级成绩

        global $wpdb,$current_user;
        $sql = "select a.id,a.user_id,b.post_title,a.memory_lv,c.grading_result,if(c.grading_result = 1,'已达标','未达标') result_cn,c.id grading_log_id
                from {$wpdb->prefix}order a 
                LEFT JOIN {$wpdb->prefix}posts b on a.match_id = b.ID
                LEFT JOIN {$wpdb->prefix}grading_logs c on a.match_id = c.grading_id and a.user_id = c.user_id
                where a.match_id = {$_GET['grad_id']} AND a.pay_status in (2,3,4) and b.post_status = 'publish'
                order by c.grading_result asc
                ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($rows);
        if(empty($rows)){
            $this->get_404(array('message'=>__('未查询到考级记录', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['match_id'])));
            return;
        }
        if(!empty($rows)){
            $row = array();
            foreach ($rows as $k => $val){

                $info = $wpdb->get_results("select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id = {$val['user_id']} and meta_key in('user_real_name','user_ID')",ARRAY_A);
                $user_info = array_column($info,'meta_value','meta_key');
                $user_real_name = unserialize($user_info['user_real_name']);
                $rows[$k]['real_name'] = $user_real_name['real_name'];
                $rows[$k]['user_ID'] = $user_info['user_ID'];
                if($val['grading_result'] == 1){
                    $result = $wpdb->get_row("select user_id,`read`,memory,compute from {$wpdb->prefix}user_skill_rank  where user_id = {$val['user_id']}",ARRAY_A);
                    //print_r($result);
                    switch ($_GET['grad_type']){
                        case 'memory':
                            $grading_lv = $result['memory'];
                            break;
                        case 'reading':
                            $grading_lv = $result['read'];
                            break;
                        case 'arithmetic':
                            $grading_lv = $result['compute'];
                            break;
                    }
                }
                else{
                    $grading_lv = $_GET['grad_type'] == 'memory' ? $val['memory_lv'] : '';
                }
                $rows[$k]['result_cn'] = !empty($grading_lv) ? $grading_lv.'级'.$val['result_cn'] : $val['result_cn'];

                if($val['user_id'] == $current_user->ID){
                    //print_r($val);
                    $row = $rows[$k];
                }
            }
        }
        //print_r($row);

        $data = array(
            'row'=>$row,
            'rows'=>$rows,
        );

        $view = student_view_path.CONTROLLER.'/record.php';
        load_view_template($view,$data);
    }

    /**
     * 我的答题记录
     */
    public function myAnswerLog(){

        global $wpdb,$current_user;
        if(!isset($_GET['questions_type'])){
            $str = "order by a.id asc limit 1";
        }else{
            $where = " and a.questions_type = '{$_GET['questions_type']}' ";
        }
        $sql = "select a.user_id,a.grade_log_id,a.grading_type,a.questions_type,a.grading_questions,a.questions_answer,a.my_answer,
                a.my_score,a.correct_rate,b.grade_lv,post_str_length,use_time,
                    case a.grading_type
                    when 'reading' then '速读'
                    when 'memory' then '记忆'
                    when 'arithmetic' then '心算'
                    end grading_type_cn,
                    case a.questions_type
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
                    end questions_type_cn 
                from {$wpdb->prefix}user_grade_logs a 
                left join {$wpdb->prefix}user_grade_log_history b on a.grade_log_id = b.id
                where a.user_id = {$current_user->ID} and a.grade_log_id = {$_GET['log_id']} {$where}
                {$str}
                ";
        $row  = $wpdb->get_row($sql,ARRAY_A);
        //print_r($sql);
        if(empty($row)){
            $this->get_404(__('未获取到答题记录', 'nlyd-student'));
            return;
        }

        $grading_questions = json_decode($row['grading_questions'],true);
        $questions_answer = json_decode($row['questions_answer'],true);
        $my_answer = !empty($row['my_answer']) ? json_decode($row['my_answer'],true) : array();
        //print_r($row);

        if($row['questions_type'] == 'wz'){
            $len = 0;
            $success_len = 0;
            if(!empty($my_answer)){
                //print_r($questions_answer);
                //print_r($my_answer);

                foreach ($my_answer as $k => $v){
                    $total = count($v);
                    $len += count($v);
                    //print_r($len.'--');
                    $error_arr=array_diff_assoc($v,$questions_answer[$k]);
                    //print_r($result);
                    //var_dump($result);
                    if(empty($error_arr)){
                        $error_len = 0;
                    }else{
                        $error_len = count($error_arr);
                    }

                    $success_len += $total-$error_len;
                }
            }
        }
        elseif ($row['questions_type'] == 'reading'){
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
        elseif ($row['questions_type'] == 'nxys'){
            $answer = $questions_answer;
            $answer_array = $answer['result'];
            $questions_answer = $answer['examples'];
            /*print_r($answer_array);
            print_r($questions_answer);die;*/

            $count_value = array_count_values($answer_array);
            $success_len = !empty($count_value['true']) ? $count_value['true'] : 0;

            $len = count($questions_answer);
        }
        elseif($row['questions_type'] == 'rm'){
            if(!empty($my_answer)){
                $success_len = 0;
                $len = count($my_answer);
                foreach ($my_answer as $k => $v){
                    if($v['name'] == $questions_answer[$k]['name'] && $v['phone'] == $questions_answer[$k]['phone']){
                        $success_len += 1;
                    }
                }
            }

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
        //print_r($next_project);

        if($row['grade_lv'] > 0 ){
            $project = $this->get_grading_parameter($row['grade_lv']);
            $keys = array_keys($project);
            $index = array_search($row['questions_type'],$keys);
            //print_r($keys);

            $next_index = $keys[$index+1];
            $prev_index = $keys[$index-1];
            $next = !empty($next_index) ? home_url('grade/myAnswerLog/grad_type/'.$_GET['grad_type'].'/log_id/'.$_GET['log_id'].'/questions_type/'.$next_index) : '';
            $prev = !empty($prev_index) ? home_url('grade/myAnswerLog/grad_type/'.$_GET['grad_type'].'/log_id/'.$_GET['log_id'].'/questions_type/'.$prev_index) : '';

            /*print_r($next_index);
            print_r($prev_index);*/
        }elseif ($row['grading_type'] == 'arithmetic'){

            $next = $row['questions_type']=='zxys' ? home_url('grade/myAnswerLog/grad_type/'.$_GET['grad_type'].'/log_id/'.$_GET['log_id'].'/questions_type/nxys') : '';
            $prev = $row['questions_type']=='nxys' ? home_url('grade/myAnswerLog/grad_type/'.$_GET['grad_type'].'/log_id/'.$_GET['log_id'].'/questions_type/zxys') : '';
        }

        $data = array(
            'next_count_down'=>300,
            'str_len'=>$len,
            'success_length'=>$success_len,
            'accuracy'=>$row['correct_rate'] > 0 ? $row['correct_rate']*100 : 0,
            'questions_answer'=>$questions_answer,
            'grading_questions'=>$grading_questions,
            'my_answer'=>$my_answer,
            'error_arr'=>!empty($error_arr) ? array_keys($error_arr) : array(),
            'reading_rate'=> $_GET['grad_type'] == 'reading' ? floor($row['post_str_length']/($row['use_time']/60)) : '',
            'match_row'=>$row,
            'next'=>$next,
            'prev'=>$prev,
        );
        //print_r($data);
        $view = student_view_path.CONTROLLER.'/match-answer-log.php';
        load_view_template($view,$data);

    }


    /**
     * 根据记忆等级获取参数
     * @param $memory_lv 记忆等级
     * @return array
     */
    public function get_grading_parameter($memory_lv){
        switch ($memory_lv){
            case 1:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>30,'answer_time'=>900),    //数字 记忆时间 个数  答题时间
                    'cy'=>array('memory_time'=>900,'length'=>30,'answer_time'=>1800),    //词语 记忆时间 个数  答题时间
                    'yzl'=>array('memory_time'=>900,'length'=>100),  //圆周率长度
                );
                break;
            case 2:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>40,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>40,'answer_time'=>1800),
                    'yzl'=>array('memory_time'=>900,'length'=>200),
                );
                break;
            case 3:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>60,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>50,'answer_time'=>1800),
                    'zm'=>array('memory_time'=>300,'length'=>30,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>3,'lv'=>3),
                );
                break;
            case 4:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>80,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>60,'answer_time'=>1800),
                    'zm'=>array('memory_time'=>300,'length'=>40,'answer_time'=>900),
                    'tl'=>array('memory_time'=>40,'length'=>40,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6,'lv'=>4),
                );
                break;
            case 5:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>120,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>80,'answer_time'=>1800),
                    'zm'=>array('memory_time'=>300,'length'=>50,'answer_time'=>900),
                    'tl'=>array('memory_time'=>45,'length'=>45,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6,'lv'=>5),
                );
                break;
            case 6:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>160,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>100,'answer_time'=>1800),
                    'zm'=>array('memory_time'=>300,'length'=>60,'answer_time'=>900),
                    'tl'=>array('memory_time'=>50,'length'=>50,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6,'lv'=>6),
                );
                break;
            case 7:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>200,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>120,'answer_time'=>1800),
                    'tl'=>array('memory_time'=>60,'length'=>60,'answer_time'=>900),
                    'rm'=>array('memory_time'=>600,'length'=>5,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6,'lv'=>7),
                );
                break;
            case 8:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>240,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>140,'answer_time'=>1800),
                    'tl'=>array('memory_time'=>70,'length'=>70,'answer_time'=>900),
                    'rm'=>array('memory_time'=>600,'length'=>6,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6,'lv'=>8),
                );
                break;
            case 9:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>280,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>160,'answer_time'=>1800),
                    'tl'=>array('memory_time'=>80,'length'=>80,'answer_time'=>900),
                    'rm'=>array('memory_time'=>600,'length'=>8,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6,'lv'=>9),
                );
                break;
            case 10:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>320,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>180,'answer_time'=>1800),
                    'tl'=>array('memory_time'=>100,'length'=>100,'answer_time'=>900),
                    'rm'=>array('memory_time'=>600,'length'=>10,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6,'lv'=>10),
                );
                break;
            default:
                $project = '';
                break;
        }
        return $project;
    }

    /**
     * 根据考题标题
     * @param $type
     */
    public function get_memory_type_title($type){
        switch ($type){
            case 'sz':
                $title = '随机数字';
                break;
            case 'cy':
                $title = '随机词汇';
                break;
            case 'zm':
                $title = '随机字母';
                break;
            case 'yzl':
                $title = '圆周率';
                break;
                break;
            case 'tl':
                $title = '听记数字';
                break;
            case 'rm':
                $title = '人脉信息';
                break;
            case 'wz':
                $title = '国学经典';
                break;
            default:
                $title = '';
                break;
        }
        return $title;
    }

    /**
     * 获取考级信息
     * $grad_id 考试比赛id
     */
    public function get_grading($grad_id){
        global $wpdb;
        $sql = "select a.*,b.post_title grading_title,b.post_content,
                c.post_title grading_type,if(d.id>0,'y','') is_me,
                DATE_FORMAT(a.start_time,'%Y-%m-%d %H:%i') start_time,
                DATE_FORMAT(a.end_time,'%Y-%m-%d %H:%i') end_time,d.id order_id,d.user_id,
                case a.status
                when -2 then '等待开赛'
                when 1 then '报名中'
                when 2 then '进行中'
                else '已结束'
                end status_cn,
                meta_value,
                case e.meta_value
                when 'memory' then '记忆'
                when 'arithmetic' then '速算'
                when 'reading' then '速读'
                end project_alias_cn,
                e.meta_value project_alias
                from {$wpdb->prefix}grading_meta a 
                left join {$wpdb->prefix}posts b on a.grading_id = b.ID 
                left join {$wpdb->prefix}posts c on a.category_id = c.ID 
                left join {$wpdb->prefix}order d on a.grading_id = d.match_id
                left join {$wpdb->prefix}postmeta e ON a.category_id = e.post_id AND meta_key = 'project_alias'
                where a.grading_id = {$grad_id}
                ";
        //print_r($sql);
        $row = $wpdb->get_row($sql,ARRAY_A);
        return $row;
    }


    /**
     * 判断是否报名
     * @param $user_id  用户id
     * @param $match_id 考级id
     * @return array|null|object|void
     */
    public function get_match_order($user_id,$match_id){
        global $wpdb;
        $sql = "select id,pay_status,memory_lv from {$wpdb->prefix}order where user_id = {$user_id} and match_id = {$match_id} ";
        //print_r($sql);
        return $wpdb->get_row($sql);
    }


    /**
     * 获取答题记录
     * @param $history_id 训练id
     * @param $log_id   答题记录
     * @return array|null|object|void
     */
    public function get_grading_questions($history_id,$log_id){

        global $wpdb,$current_user;
        $sql = "select grading_type,questions_type,grading_questions,questions_answer,my_answer,my_score,correct_rate,post_str_length,use_time,
                    case grading_type
                    when 'reading' then '速读'
                    when 'memory' then '记忆'
                    when 'arithmetic' then '心算'
                    end grading_type_cn,
                    case questions_type
                    when 'sz' then '随机数字'
                    when 'cy' then '随机词汇'
                    when 'zm' then '随机字母'
                    when 'yzl' then '圆周率'
                    when 'tl' then '听记数字'
                    when 'rm' then '人脉信息'
                    when 'wz' then '国学经典'
                    when 'reading' then '文章速读'
                    when 'zxys' then '正向运算'
                    when 'nxys' then '逆向运算'
                    end questions_type_cn
                    from {$wpdb->prefix}user_grade_logs
                    where user_id = {$current_user->ID} and grade_log_id = {$history_id} and id = {$log_id} 
                    ";
        //print_r($sql);
        $row = $wpdb->get_row($sql,ARRAY_A);

        return $row;
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_script( 'student-grade_leavePage',student_js_url.'matchs/grade_leavePage.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-grade_leavePage' );
        wp_localize_script('student-grade_leavePage','_leavePage',[
            'submit'=>__('离开考级页面,自动提交答题','nlyd-student'),
        ]);
        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
        // wp_register_style( 'my-public', student_css_url.'matchs/matching-public.css',array('my-student') );
        // wp_enqueue_style( 'my-public' );
        if(ACTION == 'index'){//考级列表
            wp_register_style( 'my-student-matchList', student_css_url.'matchList.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchList' );
        }
        if(ACTION == 'info'){//考级详情
            wp_register_style( 'my-student-matchDetail', student_css_url.'matchDetail.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchDetail' );
        }
        if(ACTION=='matchWaitting'){//考级等待倒计时页面
            wp_register_style( 'my-student-matchWaitting', student_css_url.'match-waitting.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchWaitting' );
        }

        if(ACTION == 'initial'){//
            wp_register_style( 'my-public', student_css_url.'matchs/matching-public.css',array('my-student') );
            wp_enqueue_style( 'my-public' );
            if($_GET['grad_type'] == 'arithmetic'){
                if($_GET['type'] == 'nxys'){
                    wp_register_script( 'student-check24_answer',student_js_url.'matchs/check24_answer.js',array('jquery'), leo_student_version  );
                    wp_enqueue_script( 'student-check24_answer' );
                    wp_register_style( 'my-student-fastReverse', student_css_url.'matching-fastReverse.css',array('my-student') );
                    wp_enqueue_style( 'my-student-fastReverse' );
                    wp_register_style( 'my-student-matching-fastReverse', student_css_url.'matching-fastReverse.css',array('my-student') );
                    wp_enqueue_style( 'my-student-matching-fastReverse' );
                }else{
                    wp_register_style( 'my-student-fastCalculation', student_css_url.'matching-fastCalculation.css',array('my-student') );
                    wp_enqueue_style( 'my-student-fastCalculation' );
                }
            }
            if($_GET['grad_type'] == 'reading'){
                wp_register_style( 'my-student-ready-reading', student_css_url.'ready-reading.css',array('my-student') );
                wp_enqueue_style( 'my-student-ready-reading' );
            }

            if(in_array($_GET['type'],array('sz','zm','cy','yzl'))){//中文词语记忆

                wp_register_style( 'my-student-matching-numberBattle', student_css_url.'matching-numberBattle.css',array('my-student') );
                wp_enqueue_style( 'my-student-matching-numberBattle' );
            }

            if(in_array($_GET['type'],array('sz','zm'))){//进入数字争霸准备页面
                wp_register_style( 'my-student-numberBattleReady', student_css_url.'ready-numberBattle.css',array('my-student') );
                wp_enqueue_style( 'my-student-numberBattleReady' );
            }
            if($_GET['type'] == 'tl' ) {//进入听力
                wp_register_style('my-student-matching-numberBattle', student_css_url . 'matching-numberBattle.css', array('my-student'));
                wp_enqueue_style('my-student-matching-numberBattle');
            }
            if($_GET['type'] == 'rm' ) {//人脉信息
                wp_register_style( 'my-student-matching-card', student_css_url.'grading/card.css',array('my-student') );
                wp_enqueue_style( 'my-student-matching-card' );
            }

            if($_GET['type'] == 'wz'){//国学经典默写
                wp_register_style( 'my-student-matching-silent', student_css_url.'grading/silent.css',array('my-student') );
                wp_enqueue_style( 'my-student-matching-silent' );
            }
        }

        if(ACTION == 'answerMatch'){
            wp_register_style( 'my-public', student_css_url.'matchs/matching-public.css',array('my-student') );
                wp_enqueue_style( 'my-public' );
            if($_GET['grad_type'] == 'reading'){
                wp_register_style( 'my-student-reading', student_css_url.'matching-reading.css',array('my-student') );
                wp_enqueue_style( 'my-student-reading' );
            }
        }

        if(in_array(ACTION,array('answerLog','myAnswerLog')) ){//

            wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
            wp_enqueue_style( 'my-student-subject' );

        }



        if(ACTION == 'matchRule' ){//考级规则
            wp_register_style( 'my-student-matchRule', student_css_url.'match-Rule.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchRule' );
        }


    }
}