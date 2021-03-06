<?php

/**
 * 学生-考级中心首页
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Gradings extends Student_Home
{
    private $ajaxControll;
    public function __construct($action)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        $this->ajaxControll = new Student_Ajax();

        //添加短标签
        add_shortcode('grading-home',array($this,$action));
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

        if($entry_is_true>0){
            $anchor = 1;
        }elseif ($match_is_true>0){
            $anchor = 2;
        }else{
            $anchor = 3;
        }

        //获取最近一场考级
        $start_time = $wpdb->get_var("select start_time from {$wpdb->prefix}grading_meta where status = -2 order by start_time asc ");

        if(!empty($start_time)){
            $data['new_grading_time'] = strtotime($start_time)-get_time();
        }
        $data['entry_is_true'] = $entry_is_true;
        $data['match_is_true'] = $match_is_true;
        $data['anchor'] = $anchor;

        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view,$data);
    }

    /**
     * 考级详情页
     */
    public function info(){

        global $wpdb,$current_user;
        $match = $this->get_grading($_GET['grad_id'],$current_user->ID);
        if(empty($match)){
            $this->get_404(array('message'=>'数据错误','return_url'=>home_url('grading')));
            return;
        }

        //根据时间修改比赛状态
        if(strtotime($match['entry_end_time']) <= get_time() && get_time() < strtotime($match['start_time'])){
            $a = $wpdb->update($wpdb->prefix.'grading_meta',array('status'=>-2),array('id'=>$match['id'],'grading_id'=>$match['grading_id']));
            $match['status'] = -2;
            $match['match_status_cn'] = __('等待考级', 'nlyd-student');
        }
        if(strtotime($match['start_time']) <= get_time() && get_time() < strtotime($match['end_time'])){
            $a = $wpdb->update($wpdb->prefix.'grading_meta',array('status'=>2),array('id'=>$match['id'],'grading_id'=>$match['grading_id']));
            $match['status'] = 2;
            $match['match_status_cn'] = __('考级中', 'nlyd-student');
        }
        $match['down_time'] = strtotime($match['start_time'])-get_time();
        $match['match_url'] = home_url('/gradings/matchWaitting/grad_id/'.$_GET['grad_id']);
        $data['match'] = $match;
        //print_r($match);
        //获取报名人数
        $total = $wpdb->get_var("select count(*) total from {$wpdb->prefix}order where match_id = {$match['grading_id']} and order_type = 2 and pay_status in (2,3,4)");
        $data['total'] = $total > 0 ? $total : 0;

        //获取订单
        $data['memory_lv'] = $wpdb->get_var("select memory_lv from {$wpdb->prefix}order where match_id = {$match['grading_id']} and user_id = {$current_user->ID} ");

        $view = student_view_path.CONTROLLER.'/matchDetail.php';
        load_view_template($view,$data);
    }


    /**
     * 考级报名页
     */
    public function confirm(){

        global $wpdb,$current_user;
        $match = $this->get_grading($_GET['grad_id'],$current_user->ID);
        //print_r($match);
        if(empty($match)){
            $this->get_404(array('message'=>'数据错误','return_url'=>home_url('grading')));
            return;
        }
        //print_r($match);
        $data['match'] = $match;

        //主训教练
        $coach_id = $wpdb->get_var("select coach_id from {$wpdb->prefix}my_coach where user_id = {$current_user->ID} and category_id = {$match['category_id']}");
        if($coach_id > 0){
            $data['coach_real_name'] = get_user_meta($coach_id,'user_real_name')[0];
        }else{
            $data['coach_real_name'] = '';
        }
        //实名认证
        $data['user_real_name'] = get_user_meta($current_user->ID,'user_real_name')[0];

        //战队
        $data['team_title'] = $wpdb->get_var("select b.post_title team_title 
                                            from {$wpdb->prefix}match_team a  
                                            left join {$wpdb->prefix}posts b on a.team_id = b.ID
                                            where user_id = {$current_user->ID} and status = 2 and user_type = 1");
        //选手ID
        $data['user_ID'] = get_user_meta($current_user->ID,'user_ID')[0];

        //获取当前比赛是否报名
        $order = $wpdb->get_row("select memory_lv,pay_status from {$wpdb->prefix}order where match_id = {$match['grading_id']} and user_id = {$current_user->ID} ",ARRAY_A);
        $data['memory_lv'] = !empty($order['memory_lv']) ? $order['memory_lv'] : 0;
        //print_r($order);
        $view = student_view_path.CONTROLLER.'/confirm.php';
        load_view_template($view,$data);
    }


    /**
     * 考级等待页
     */
    public function matchWaitting(){

        global $wpdb,$current_user;

        //获取数据
        $row = $this->get_grading($_GET['grad_id'],$current_user->ID);

        if(empty($row)){
            $this->get_404(array('message'=>__('暂无考级', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
            return;
        }
        if($row['status'] == -3){
            $this->get_404(array('message'=>__('考级已结束', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
            return;
        }
        //print_r($row);

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
                    $this->get_404(array('message'=>__('你已完成全部作答', 'nlyd-student'),'return_log_url'=>home_url(CONTROLLER.'/myAnswerLog/grad_id/'.$_GET['grad_id'])));
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
                $this->get_404(array('message'=>__('你已完成全部作答', 'nlyd-student'),'return_log_url'=>home_url(CONTROLLER.'/myAnswerLog/grad_id/'.$_GET['grad_id'])));
                return;
            }
        }
        elseif ($row['meta_value'] == 'arithmetic'){
            $questions_type = array_column($rows,'questions_type');
            //print_r($questions_type);die;
            $diff = array_diff_assoc(array('zxys','nxys'),$questions_type);
            //print_r($diff);
            $next_key = reset($diff);
            if(!empty($next_key)){
                $row['redirect_url'] .= '/type/'.$next_key;
            }else{
                $this->get_404(array('message'=>__('你已完成全部作答', 'nlyd-student'),'return_log_url'=>home_url(CONTROLLER.'/myAnswerLog/grad_id/'.$_GET['grad_id'])));
                return;
            }
        }


        $view = student_view_path.CONTROLLER.'/match-waitting.php';
        load_view_template($view,$row);
    }


    /**
     * 比赛初始页
     */
    public function initialMatch(){

        global $wpdb,$current_user;
        //var_dump($current_user->ID);
        //获取数据
        $row = $this->get_grading($_GET['grad_id'],$current_user->ID);
        if(empty($row)){
            $this->get_404(array('message'=>__('暂无考级', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
            return;
        }
        //print_r($row);
        if($row['status'] == -3){
            $this->get_404(array('message'=>__('考级已结束', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
            return;
        }

        if($row['user_id'] != $current_user->ID){
            $this->get_404(array('message'=>__('未查询到报名信息', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
            return;
        }

        if(!in_array($row['pay_status'],array(2,3,4))){
            $this->get_404(__('订单未付款', 'nlyd-student'));
            return;
        }

        //获取答题记录
        if(isset($_GET['more'])){
            $where = " and post_more = {$_GET['more']} ";
        }
        $sql = "select id from {$wpdb->prefix}grading_questions 
                where grading_id = {$_GET['grad_id']} and user_id = {$current_user->ID} and grading_type = '{$_GET['grad_type']}' and questions_type = '{$_GET['type']}' {$where} 
                ";
        $id = $wpdb->get_var($sql);
        //print_r($id);
        if(!empty($id)){
            $this->get_404(array('message'=>__('本项目答案已提交', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
            return;
        }

        if($_GET['grad_type'] == 'memory'){
            $memory_lv = isset($_GET['memory_lv']) ? $_GET['memory_lv'] : $_SESSION['memory_lv'];
            $project = $this->get_grading_parameter($memory_lv);
            if(empty($project)){
                $this->get_404(array('message'=>__('请确认考级等级', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
                return;
            }
            $row['project'] = $project;
            $memory_type = $project[$_GET['type']];
            if(empty($memory_type)){
                $this->get_404(array('message'=>__('未找到此类型考级题目', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
                return;
            }
            //print_r($memory_type);
            $row['memory_type'] = $memory_type;
        }
        elseif ($_GET['grad_type'] == 'reading'){

            if(!isset($_SESSION['grading_post_id'])){
                //获取已比赛文章
                $sql1 = "select post_id from {$wpdb->prefix}user_post_use where user_id = {$current_user->ID} and type = 1";
                //print_r($sql1);
                $post_str = $wpdb->get_var($sql1);
                if(!empty($post_str)){
                    $where_ = " and b.object_id not in({$post_str}) ";
                }
                //判断语言
                $language = get_user_meta($current_user->ID,'locale')[0];
                $locale = $language == 'zh_CN' || empty($language) ? 'cn' : 'en';
                //获取文章速读考题
                $sql = "select b.object_id,b.term_taxonomy_id from {$wpdb->prefix}terms a 
                        left join {$wpdb->prefix}term_relationships b on a.term_id = b.term_taxonomy_id 
                        left join {$wpdb->prefix}posts c on b.object_id = c.ID
                        where a.slug = '{$locale}-match-question' and c.post_status = 'publish' {$where_} ";
                //print_r($sql);

                $rows = $wpdb->get_results($sql,ARRAY_A);

                if(empty($rows)){
                    $this->get_404(array('message'=>__('题库暂未更新，联系管理员录题', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
                    return;
                }
                $result = array_column($rows,'object_id');
                //print_r($rows);
                $post_id = $result[array_rand($result)];

                //print_r($post_id);

                $_SESSION['grading_post_id'] = $post_id;

            }else{
                $post_id = $_SESSION['grading_post_id'];
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
            $row['redirect_url'] = home_url(CONTROLLER.'/answerMatch/grad_id/'.$_GET['grad_id'].'/grad_type/'.$_GET['grad_type'].'/post_id/'.$post_id);
            //print_r($row);
        }
        $id = $wpdb->get_var("select id from {$wpdb->prefix}grading_logs where user_id = {$current_user->ID} and grading_id = {$_GET['grad_id']} ");
        //var_dump($id);die;
        if(empty($id)){
            $insert = array(
                'user_id'=>$current_user->ID,
                'grading_id'=>$_GET['grad_id'],
                'grading_result'=>2,
                'grading_lv'=> $memory_lv > 0 ? $memory_lv : '',
                'created_time'=>get_time('mysql'),
            );
            $a = $wpdb->insert($wpdb->prefix.'grading_logs',$insert);
        }

        $row['type_title'] = $this->get_memory_type_title($_GET['type']);

        $view = student_view_path.CONTROLLER.'/match-initial.php';
        load_view_template($view,$row);
    }

    /*
     * 比赛项目记忆完成答题页
     */
    public function answerMatch(){

        unset($_SESSION['grading_post_id']);

        if(empty($_GET['grad_id']) || empty($_GET['post_id'])){
            $this->get_404(__('参数错误', 'nlyd-student'));
            return;
        }

        global $wpdb,$current_user;

        $row = $this->get_match_order($current_user->ID,$_GET['grad_id']);
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

        $grad = $this->get_grading($_GET['grad_id'],$current_user->ID);
        $data['grading_title'] = $grad['grading_title'];

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

            if(empty($_GET['grad_id']) || empty($_GET['log_id']) || empty($_GET['grad_type']) || empty($_GET['type']) ){

                $this->get_404(__('参数错误', 'nlyd-student'));
                return;
            }
        }
        else{

            if(isset($_SESSION['match_data'])){

                $match_data = $_SESSION['match_data'];
                //print_r($match_data);

                $sql1 = "select * from {$wpdb->prefix}grading_questions 
                          where grading_id = {$match_data['grading_id']} 
                          and grading_type = '{$match_data['grading_type']}' 
                          and questions_type = '{$match_data['questions_type']}' 
                          and user_id = {$current_user->ID} ";
                //print_r($sql1);
                $row_1 = $wpdb->get_row($sql1,ARRAY_A);
                //print_r($row_1);
                if(empty($row_1)){

                    //数据处理
                    $correct_rate = 0;  //准确率
                    switch ($match_data['grading_type']){
                        case 'memory':
                            switch ($match_data['questions_type']){
                                case 'sz':
                                case 'cy':
                                case 'yzl':
                                case 'zm':
                                case 'tl':
                                    if(!empty($match_data['my_answer'])){

                                        $len = count($match_data['questions_answer']);
                                        $error_len = count(array_diff_assoc($match_data['questions_answer'],$match_data['my_answer']));
                                        $correct_rate = ($len-$error_len)/$len;
                                    }
                                    break;
                                case 'rm':
                                    if(!empty($match_data['my_answer'])){

                                        $len = count($match_data['questions_answer']);
                                        $my_answer = $match_data['my_answer'];
                                        $questions_answer = $match_data['questions_answer'];
                                        $success_len = 0;
                                        foreach ($my_answer as $k => $v){
                                            if( ($my_answer[$k]['name'] == $questions_answer[$k]['name']) && ($my_answer[$k]['phone'] == $questions_answer[$k]['phone']) && ($my_answer[$k]['picture'] == $questions_answer[$k]['picture'])){
                                                $success_len += 1;
                                            }
                                        }
                                        $correct_rate = $success_len/$len;
                                    }
                                    break;
                                case 'wz':

                                    $questions_answer = $match_data['questions_answer'];
                                    $len = 0;
                                    $error_len = 0;
                                    foreach ($questions_answer as $k =>$v){
                                        $len += count($v['rights']);
                                        $error_len += count(array_diff_assoc($v['rights'],$v['yours']));
                                    }
                                    $correct_rate = ($len-$error_len)/$len;
                                    //print_r($correct_rate);die;
                                    $match_data['grading_questions'] = array_column($questions_answer,'question');
                                    $match_data['questions_answer'] = array_column($questions_answer,'rights');
                                    $match_data['my_answer'] = array_column($questions_answer,'yours');
                                    break;
                            }
                            break;
                        case 'reading':
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
                            $correct_rate = $success_len/$len;
                            //print_r($success_len);die;
                            break;
                        case 'arithmetic':

                            $data_arr = $match_data['my_answer'];
                            //print_r($data_arr);die;
                            if(!empty($data_arr)){
                                $match_questions = array_column($data_arr,'question');
                                $questions_answer = array_column($data_arr,'rights');
                                $_POST['my_answer'] = array_column($data_arr,'yours');
                                $len = count($match_questions);
                            }

                            if($_POST['questions_type'] == 'nxys'){
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

                                $error_len = count(array_diff_assoc($questions_answer,$_POST['my_answer']));
                                $success_len = $len-$error_len;
                                $my_score = $success_len*10;
                            }
                            $correct_rate = $success_len/$len;
                            //print_r($my_score);die;
                            $match_data['grading_questions'] = $match_questions;
                            $match_data['questions_answer'] = $questions_answer;
                            //var_dump($_POST);die;
                            break;
                    }

                    $insert = array(
                        'user_id'=>$current_user->ID,
                        'grading_id'=>$_POST['grading_id'],
                        'grading_type'=>$_POST['grading_type'],
                        'questions_type'=>$_POST['questions_type'],
                        'grading_questions'=>json_encode($_POST['grading_questions']),
                        'questions_answer'=>json_encode($_POST['questions_answer']),
                        'my_answer'=>json_encode($_POST['my_answer']),
                        'correct_rate'=>$correct_rate,
                        'my_score'=>$my_score,
                        'submit_type'=>isset($_POST['submit_type']) ? $_POST['submit_type'] : 1,
                        'leave_page_time'=>isset($_POST['leave_page_time']) ? json_encode($_POST['leave_page_time']) : '',
                        'created_time'=>get_time('mysql'),
                        'use_time'=>isset($_POST['usetime']) ? $_POST['usetime'] : '',
                        'post_id'=>isset($_POST['post_id']) ? $_POST['post_id'] : '',
                        'post_str_length'=>isset($_POST['length']) ? $_POST['length'] : '',
                        'is_true'=>!empty($prison_log_id) ? 2 : 1,
                        'post_more'=>!empty($_POST['more']) ? $_POST['more'] : '',
                    );
                    //print_r($insert);die;
                    $result = $wpdb->insert($wpdb->prefix.'grading_questions',$insert);

                    /*print_r($insert);
                    die;*/
                    //print_r($result);die;
                    if($result){
                        $_GET['log_id'] = $wpdb->insert_id;

                        if(!empty($match_data['post_id']) && $match_data['grading_type'] == 'reading'){

                            $sql1 = "select id from {$wpdb->prefix}user_post_use where user_id = {$current_user->ID} and type = 1 ";
                            $use_id = $wpdb->get_row($sql1,ARRAY_A);
                            if($use_id){
                                $sql2 = "UPDATE {$wpdb->prefix}user_post_use SET post_id = if(post_id = '',{$match_data['post_id']},CONCAT_WS(',',post_id,{$match_data['post_id']})) WHERE user_id = {$current_user->ID} and type = 2";
                                $a = $wpdb->query($sql2);
                            }else{

                                $a = $wpdb->insert($wpdb->prefix.'user_post_use',array('user_id'=>$current_user->ID,'post_id'=>$match_data['post_id'],'type'=>2));
                            }

                        }
                    }
                }

                else{
                    $_GET['log_id'] = $row_1['id'];
                }

            }

        }

        //清空倒计时
        unset($_SESSION['count_down']);

        $order = $this->get_match_order($current_user->ID,$_GET['grad_id']);
        if(empty($order)){
            $this->get_404(__('你未报名', 'nlyd-student'));
            return;
        }else{

            if(!in_array($order->pay_status,array(2,3,4))){
                $this->get_404(__('订单未付款', 'nlyd-student'));
                return;
            }
        }

        if(isset($_GET['log_id'])){

            //获取答题记录
            $row = $this->get_grading_questions($_GET['grad_id'],$_GET['log_id']);
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
            $project = $this->get_grading_parameter($order->memory_lv);
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
            $next_more = $row['post_more']+1;

            $next_project = $next_more <= 3 ? 'reading' : '';
            $next_index = 'reading';
        }

        //如果为当前考级最后一项就计算考核结果
        //获取考级场景
        $role_alias = $wpdb->get_var("select b.role_alias from {$wpdb->prefix}grading_meta a 
                                        left join {$wpdb->prefix}zone_match_role b on a.scene = b.id
                                        where grading_id = {$_GET['grad_id']}");
        if(empty($next_project) && $role_alias == 'official-grading'){
            //获取所有项目
            $sql_ = "select id,user_id,grading_id,grading_type,questions_type,grading_questions,questions_answer,my_answer,correct_rate,my_score,post_str_length,use_time
                      from {$wpdb->prefix}grading_questions 
                      where user_id = {$current_user->ID} and grading_id = {$_GET['grad_id']} and grading_type = '{$_GET['grad_type']}' 
                      ";
            $rows = $wpdb->get_results($sql_,ARRAY_A);
            //print_r($rows);
            if(!empty($rows)){
                //获取用户技能
                $rank_row = $wpdb->get_row("select id,`read`,memory,compute from {$wpdb->prefix}user_skill_rank where user_id = {$current_user->ID} and skill_type = 1",ARRAY_A);
                $update = array();
                if($order->memory_lv > 0){

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
                    //print_r($grading_result);
                    if($order->memory_lv > 2){

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
                            //print_r($error_len);
                            if($error_len > 10){
                                $grading_result = 2;
                            }
                        }

                    }
                    //print_r($grading_result);
                    $lv = $order->memory_lv;
                    if($lv > $rank_row['memory'] && $grading_result == 1){
                        $update = array('memory'=>$order->memory_lv);
                    }
                    //print_r($update);die;
                    $insert1 = array('user_id'=>$current_user->ID,'memory'=>$order->memory_lv,'skill_type'=>1);
                }
                elseif($_GET['grad_type']== 'reading'){
                    $arr = array();
                    foreach ($rows as $k =>$v){
                        $rows[$k]['rate'] = $rate1 = $v['post_str_length']/($v['use_time']/60);
                        $arr[] = $rate1;
                    }
                    array_multisort($arr, SORT_ASC, $rows);
                    array_shift($rows);
                    array_pop($rows);
                    $row_ = $rows[0];

                    $grading_result = 2;
                    if($row_['correct_rate'] >= 0.7){
                        $rate = $row_['post_str_length']/($row_['use_time']/60);
                        $lv = floor($rate/1000);
                        if($lv > 0){
                            $grading_result = 1;
                            if($lv > $rank_row['read']){
                                $update = array('read'=>$lv);
                            }
                            $insert1 = array('user_id'=>$current_user->ID,'read'=>$lv,'skill_type'=>1);
                        }

                    }
                    //print_r($row);
                }
                elseif($_GET['grad_type']== 'arithmetic'){
                    $grading_result = 2;
                    $my_score = array_sum(array_column($rows,'my_score'));
                    if($my_score > 400){
                        $lv = 1;
                        $lv = floor(($my_score-400)/200+$lv);
                    }
                    if($lv > 0){
                        $grading_result = 1;
                        if($lv > $rank_row['compute']){
                            $update = array('compute'=>$lv);
                        }
                        $insert1 = array('user_id'=>$current_user->ID,'compute'=>$lv,'skill_type'=>1);
                    }
                }

                $wpdb->query('START TRANSACTION');

                $id = $wpdb->get_var("select id from {$wpdb->prefix}grading_logs where user_id = {$current_user->ID} and grading_id = {$_GET['grad_id']} ");

                //获取本次考级类别的教练
                $coach_sql = "select b.coach_id from {$wpdb->prefix}grading_meta a 
                          left join {$wpdb->prefix}my_coach b on a.category_id = b.category_id 
                          where a.grading_id = {$_GET['grad_id']} and b.user_id = {$current_user->ID} and b.apply_status = 2
                          ";
                $coach_id = $wpdb->get_var($coach_sql);
                if(empty($id)){
                    $insert = array(
                        'user_id'=>$current_user->ID,
                        'grading_id'=>$_GET['grad_id'],
                        'grading_result'=>$grading_result,
                        'grading_lv'=> $lv > 0 ? $lv : '',
                        'grading_coach_id'=> $coach_id > 0 ? $coach_id : '',
                        'created_time'=>get_time('mysql'),
                    );
                    $a = $wpdb->insert($wpdb->prefix.'grading_logs',$insert);
                    //var_dump($a);
                }
                else{
                    if($grading_result == 1){
                        $a = $wpdb->update($wpdb->prefix.'grading_logs',array('grading_coach_id'=>$coach_id,'grading_result'=>1,'grading_lv'=>$lv),array('id'=>$id));
                    }else{
                        $a = true;
                    }
                }
                if(empty($rank_row)){

                    if($grading_result == 1){
                        $b =  $wpdb->insert($wpdb->prefix.'user_skill_rank',$insert1);
                    }else{

                        $b = true;
                    }
                }else{

                    if(!empty($update)){
                        $b = $wpdb->update($wpdb->prefix.'user_skill_rank',$update,array('user_id'=>$current_user->ID,'id'=>$rank_row['id'],'skill_type'=>1));
                    }else{
                        $b = true;
                    }
                }
                //var_dump($a.'---'.$b);die;
                if($a && $b){
                    $wpdb->query('COMMIT');
                }else{
                    $wpdb->query('ROLLBACK');
                }

                $next_project_url = home_url('gradings/record/grad_id/'.$_GET['grad_id'].'/grad_type/'.$_GET['grad_type']);
            }

        }
        else{
            if($_GET['grad_type'] == 'memory'){

                $next_project['title'] = $this->get_memory_type_title($next_index);
            }
            $next_project_url = home_url('gradings/initialMatch/grad_id/'.$_GET['grad_id'].'/grad_type/'.$_GET['grad_type'].'/type/'.$next_index);
            if($order->memory_lv > 0 ){
                $next_project_url .= '/memory_lv/'.$order->memory_lv;
            }
            elseif ($_GET['grad_type'] == 'reading'){
                $next_project_url .= '/more/'.$next_more;
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
            'reading_rate'=> $_GET['grad_type'] == 'reading' ? floor($row['post_str_length']/($row['use_time']/60)) : '',
            'error_arr'=>!empty($error_arr) ? array_keys($error_arr) : array(),
            'next_project_url'=>$next_project_url,
            'match_row'=>$row,
        );
        if($grading_result == 1){
            $grade_result = $lv.'级'.'已达标';

            if($lv >= 2 && $_GET['grad_type']== 'memory' && $role_alias == 'official-grading'){

                //判断分享是否达标
                $share_total = $wpdb->get_var("select count(*) from {$wpdb->prefix}user_stream_logs where income_type = 'recommend_course' and user_id = {$current_user->ID}");

                if($share_total >= 3){
                    //判断是否已经拿回学费补贴
                    $income_id = $wpdb->get_var("select id from {$wpdb->prefix}user_stream_logs where income_type = 'share_qualified' and user_id = {$current_user->ID} ");
                    if(empty($income_id)){
                        $direct_superior = $wpdb->get_var("select direct_superior from {$wpdb->prefix}spread_set where spread_type = 'share_qualified' ");
                        if($direct_superior > 0 ){
                            $user_income = $direct_superior;
                        }elseif ($order->cost > 0){
                            $user_income = $order->cost;
                        }
                        if($user_income > 0){
                            $x = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('user_id'=>$current_user->ID,'match_id'=>$_GET['grad_id'],'income_type'=>'share_qualified','user_income'=>$user_income,'income_rank'=>1,'created_time'=>get_time('mysql')));
                        }
                    }
                }

                //获取收益配置
                $set_sql = "select * from {$wpdb->prefix}spread_set where spread_type = 'course_grading' ";
                $setting = $wpdb->get_row($set_sql,ARRAY_A);
                if(!empty($setting)){
                    //准备对应的数据
                    $money1 = $setting['first_cause'];     //事业员
                    $money2 = $setting['second_cause'];    //事业部长
                    $money3 = $setting['coach'];        //教练
                    $money4 = $setting['sub_center'];   //办赛机构
                    $money5 = $setting['general_manager'];    //总经理

                    //查询当前发布机构
                    $zone_sql = "select b.user_id zone_id,b.center_manager_id,b.referee_id,c.referee_id as indirect_referee_id from {$wpdb->prefix}grading_meta a 
                          left join {$wpdb->prefix}zone_meta b on a.created_person = b.user_id 
                          left join {$wpdb->prefix}users c on b.referee_id = c.ID 
                          where a.grading_id = {$_GET['grad_id']} ";
                    $referee_ = $wpdb->get_row($zone_sql,ARRAY_A);

                    $wpdb->query('START TRANSACTION');

                    $a = $b = $c = $d = $e = true;
                    if($referee_['referee_id'] > 0 && $money1 > 0){
                        $referee_income_id = $wpdb->get_var("select id from {$wpdb->prefix}user_stream_logs where 'provide_id' = {$current_user->ID} and user_id = {$referee_['referee_id']} and income_type = 'grading_qualified' ");
                        if(empty($referee_income_id)){
                            $a = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('provide_id'=>$current_user->ID,'match_id'=>$_GET['grad_id'],'user_id'=>$referee_['referee_id'],'user_income'=>$money1,'income_type'=>'grading_qualified','created_time'=>get_time('mysql')));
                        }
                    }
                    if($referee_['indirect_referee_id'] > 0 && $money2 > 0){
                        $referee_income_id = $wpdb->get_var("select id from {$wpdb->prefix}user_stream_logs where 'provide_id' = {$current_user->ID} and user_id = {$referee_['indirect_referee_id']} and income_type = 'grading_qualified' ");
                        if(empty($referee_income_id)){
                            $b = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('provide_id'=>$current_user->ID,'match_id'=>$_GET['grad_id'],'user_id'=>$referee_['indirect_referee_id'],'user_income'=>$money2,'income_type'=>'grading_qualified','created_time'=>get_time('mysql')));
                        }
                    }
                    if($coach_id > 0 && $money3 > 0){
                        $referee_income_id = $wpdb->get_var("select id from {$wpdb->prefix}user_stream_logs where 'provide_id' = {$current_user->ID} and user_id = {$coach_id} and income_type = 'grading_qualified' ");
                        if(empty($referee_income_id)){
                            $c = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('provide_id'=>$current_user->ID,'match_id'=>$_GET['grad_id'],'user_id'=>$coach_id,'user_income'=>$money3,'income_type'=>'grading_qualified','created_time'=>get_time('mysql')));
                        }
                    }
                    if($referee_['zone_id'] > 0 && $money4 > 0){
                        $referee_income_id = $wpdb->get_var("select id from {$wpdb->prefix}user_stream_logs where 'provide_id' = {$current_user->ID} and user_id = {$referee_['zone_id']} and income_type = 'grading_qualified' ");
                        if(empty($referee_income_id)){
                            $d = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('provide_id'=>$current_user->ID,'match_id'=>$_GET['grad_id'],'user_id'=>$referee_['zone_id'],'user_income'=>$money4,'income_type'=>'grading_qualified','created_time'=>get_time('mysql')));
                        }
                    }
                    if($referee_['center_manager_id'] > 0 && $money5 > 0){
                        $referee_income_id = $wpdb->get_var("select id from {$wpdb->prefix}user_stream_logs where 'provide_id' = {$current_user->ID} and user_id = {$referee_['center_manager_id']} and income_type = 'grading_qualified' ");
                        if(empty($referee_income_id)){
                            $e = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('provide_id'=>$current_user->ID,'match_id'=>$_GET['grad_id'],'user_id'=>$referee_['center_manager_id'],'user_income'=>$money4,'income_type'=>'grading_qualified','created_time'=>get_time('mysql')));
                        }
                    }

                    //print_r($a .'&&' .$b .'&&'. $c .'&&'. $d .'&&'. $e);die;
                    if( $a && $b && $c && $d && $e ){
                        $wpdb->query('COMMIT');
                    }else{
                        $wpdb->query('ROLLBACK');
                    }
                }
            }

        }else{
            $grade_result = '未达标';
            if($row['grading_type'] == 'memory'){
                //var_dump($lv);
                $grade_result = $lv.$grade_result;
            }
        }
        $data['grade_result'] = $row['grading_type_cn'].$grade_result;

        $view = student_view_path.CONTROLLER.'/match-answer-log.php';
        load_view_template($view,$data);
    }

    /**
     * 考级成绩页
     */
    public function record(){//考级成绩

        global $wpdb,$current_user;
        $sql = "select a.id,a.user_id,b.post_title,a.memory_lv,c.grading_result,if(c.grading_result = 1,'已达标','未达标') result_cn,c.id grading_log_id,c.grading_lv
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
                $rows[$k]['result_cn'] = !empty($val['grading_lv']) ? $val['grading_lv'].'级'.$val['result_cn'] : $val['result_cn'];

                if($val['user_id'] == $current_user->ID){
                    //print_r($val);
                    $row = $rows[$k];
                }
            }
        }

        //获取是否存在答题记录
        $log_id = $wpdb->get_var("select id from {$wpdb->prefix}grading_questions where user_id = {$current_user->ID} and grading_id = {$_GET['grad_id']}");
        $data = array(
            'log_id'=>$log_id,
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
            $str = "order by id asc limit 1";
        }else{
            $where = " and questions_type = '{$_GET['questions_type']}' ";
        }
        $post_more = isset($_GET['post_more']) ? $_GET['post_more'] : 1;
        if($_GET['questions_type'] == 'reading'){
            $where .= " and post_more = {$post_more} ";
        }
        $sql = "select user_id,grading_id,grading_type,questions_type,grading_questions,questions_answer,my_answer,my_score,correct_rate,post_str_length,use_time,
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
                    when 'zxys' then '正向速算'
                    when 'nxys' then '逆向速算'
                    end questions_type_cn 
                from {$wpdb->prefix}grading_questions where user_id = {$current_user->ID} and grading_id = {$_GET['grad_id']} {$where}
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
                    if(empty($result)){
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

        $order = $this->get_match_order($current_user->ID,$_GET['grad_id']);
        if($order->memory_lv > 0 ){
            $project = $this->get_grading_parameter($order->memory_lv);
            $keys = array_keys($project);
            $index = array_search($row['questions_type'],$keys);
            //print_r($keys);

            $next_index = $keys[$index+1];
            $prev_index = $keys[$index-1];
            $next = !empty($next_index) ? home_url('gradings/myAnswerLog/grad_id/'.$_GET['grad_id'].'/grad_type/memory/questions_type/'.$next_index) : '';
            $prev = !empty($prev_index) ? home_url('gradings/myAnswerLog/grad_id/'.$_GET['grad_id'].'/grad_type/memory/questions_type/'.$prev_index) : '';

            /*print_r($next_index);
            print_r($prev_index);*/
        }elseif ($row['grading_type'] == 'arithmetic'){

            $next = $row['questions_type']=='zxys' ? home_url('gradings/myAnswerLog/grad_id/'.$_GET['grad_id'].'/grad_type/arithmetic/questions_type/nxys') : '';
            $prev = $row['questions_type']=='nxys' ? home_url('gradings/myAnswerLog/grad_id/'.$_GET['grad_id'].'/grad_type/arithmetic/questions_type/zxys') : '';
        }elseif ($row['grading_type'] == 'reading'){
            //print_r($post_more);die;
            $next_more = $post_more+1; //$post_more < 4 ? $post_more+1 : '';
            $prev_more = $post_more-1; //$post_more < 4 ? $post_more-1 : '';

            $next = $post_more < 3 ? home_url('gradings/myAnswerLog/grad_id/'.$_GET['grad_id'].'/grad_type/reading/questions_type/reading/post_more/'.$next_more) : '';
            $prev = $post_more > 1 ? home_url('gradings/myAnswerLog/grad_id/'.$_GET['grad_id'].'/grad_type/reading/questions_type/reading/post_more/'.$prev_more) : '';
        }

        $data = array(
            'next_count_down'=>300,
            'str_len'=>$len,
            'success_length'=>$success_len,
            'accuracy'=>$row['correct_rate'] > 0 ? $row['correct_rate']*100 : 0,
            'questions_answer'=>$questions_answer,
            'grading_questions'=>$grading_questions,
            'my_answer'=>$my_answer,
            'answer_array'=>$answer_array,
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

    // public function matching_wzsd(){//文章速读比赛页
    //     $view = student_view_path.CONTROLLER.'/matching-reading.php';
    //     load_view_template($view);
    // }
    // public function ready_wzsd(){//文章速读准备页
    //     $view = student_view_path.CONTROLLER.'/matching-ready.php';
    //     load_view_template($view);
    // }
    // public function matching_zxss(){//正向速算比赛页
    //     $view = student_view_path.CONTROLLER.'/matching-fastCalculation.php';
    //     load_view_template($view);
    // }
    // public function matching_nxss(){//逆向速算比赛页
    //     $view = student_view_path.CONTROLLER.'/matching-fastReverse.php';
    //     load_view_template($view);
    // }

    public function matchRule(){//规则
        switch ($_GET['type']){
            case 'memory':
                $title = '记忆';
                break;
            case 'reading':
                $title = '速读';
                break;
            case 'arithmetic':
                $title = '心算';
                break;
            default:
                $title = '';
                break;
        }
        $view = student_view_path.CONTROLLER.'/match-Rule.php';
        load_view_template($view,array('title'=>$title));
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
    public function get_grading($grad_id,$user_id){
        global $wpdb;
        $sql = "select a.*,b.post_title grading_title,b.post_content,
                c.post_title grading_type,if(d.id>0,'y','') is_me,d.pay_status,
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
                left join {$wpdb->prefix}order d on a.grading_id = d.match_id AND d.user_id = {$user_id}
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
        $sql = "select id,pay_status,memory_lv,cost from {$wpdb->prefix}order where user_id = {$user_id} and match_id = {$match_id} ";
        //print_r($sql);
        return $wpdb->get_row($sql);
    }


    /**
     * 获取答题记录
     * @param $match_id 比赛id
     * @param $log_id   答题记录
     * @return array|null|object|void
     */
    public function get_grading_questions($match_id,$log_id){

        global $wpdb,$current_user;
        $sql = "select grading_type,questions_type,submit_type,leave_page_time,grading_questions,questions_answer,my_answer,my_score,
                    correct_rate,is_true,post_str_length,use_time,post_more,
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
                    end questions_type_cn
                    from {$wpdb->prefix}grading_questions
                    where user_id = {$current_user->ID} and grading_id = {$match_id} and id = {$log_id} 
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
        if(ACTION=='confirm'){//信息确认页
            wp_register_script( 'student-mobileSelect',student_js_url.'Mobile/mobileSelect.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-mobileSelect' );
            wp_localize_script('student-mobileSelect','_mobileSelect',[
                'sure'=>__('确认','nlyd-student'),
                'cancel'=>__('取消','nlyd-student')
            ]);
            wp_register_style( 'my-student-mobileSelect', student_css_url.'Mobile/mobileSelect.css',array('my-student') );
            wp_enqueue_style( 'my-student-mobileSelect' );
            wp_register_style( 'my-student-confirm', student_css_url.'confirm.css',array('my-student') );
            wp_enqueue_style( 'my-student-confirm' );
        }
        if(ACTION=='matchWaitting'){//考级等待倒计时页面
            wp_register_style( 'my-student-matchWaitting', student_css_url.'match-waitting.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchWaitting' );
        }

        if(ACTION == 'initialMatch'){//
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