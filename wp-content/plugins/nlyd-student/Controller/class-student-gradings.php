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
     * 考级报名页
     */
    public function confirm(){

        global $wpdb,$current_user;
        $match = $this->get_grading($_GET['grad_id']);
        if(empty($match)){
            $this->get_404(array('message'=>'数据错误','return_url'=>home_url('grading')));
            return;
        }
        //print_r($match);
        $data['match'] = $match;

        //主训教练
        $coach_id = $wpdb->get_var("select coach_id from {$wpdb->prefix}my_coach where user_id = {$current_user->ID} and category_id = {$match['category_id']} and major = 1");
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
        $order = $wpdb->get_row("select memory_lv,pay_status from {$wpdb->prefix}order where match_id = {$match['grading_id']}",ARRAY_A);
        $data['memory_lv'] = !empty($order['memory_lv']) ? $order['memory_lv'] : 1;
        //print_r($order);
        $view = student_view_path.CONTROLLER.'/confirm.php';
        load_view_template($view,$data);
    }


    /**
     * 考级等待页
     */
    public function matchWaitting(){

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
        if($row['project_alias'] == 'memory'){ //如果是记忆 获取报名记忆等级
            $row['memory_lv'] = $wpdb->get_var("select memory_lv from {$wpdb->prefix}order where match_id = {$row['grading_id']} and user_id = {$current_user->ID}");
        }
        $row['count_down'] = strtotime($row['start_time']) - get_time();
        $row['redirect_url'] = home_url('gradings/initialMatch/grad_id/'.$row['grading_id'].'/grad_type/'.$row['project_alias']);
        if($row['memory_lv'] > 0){
            $row['redirect_url'] .= '/type/sz/memory_lv/'.$row['memory_lv'];
            $_SESSION['memory_lv'] = $row['memory_lv'];
        }
        //print_r($row);
        $view = student_view_path.CONTROLLER.'/match-waitting.php';
        load_view_template($view,$row);
    }


    /**
     * 比赛初始页
     */
    public function initialMatch(){

        //获取数据
        $row = $this->get_grading($_GET['grad_id']);
        if(empty($row)){
            $this->get_404(array('message'=>__('暂无考级', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
            return;
        }
        //print_r($row);
        if($row['status'] == -3){
            $this->get_404(array('message'=>__('考级已结束', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
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
        }

        $memory_type = $project[$_GET['type']];
        if(empty($memory_type)){
            $this->get_404(array('message'=>__('未找到此类型考级题目', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
            return;
        }
        print_r($memory_type);
        $row['memory_type'] = $memory_type;
        $row['type_title'] = $this->get_memory_type_title($_GET['type']);

        $view = student_view_path.CONTROLLER.'/match-initial.php';
        load_view_template($view,$row);
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

                $sql1 = "select * from {$wpdb->prefix}match_questions where match_id = {$match_data['match_id']} and project_id = {$match_data['project_id']} and match_more = {$match_data['match_more']} and user_id = {$current_user->ID}";
                $row = $wpdb->get_row($sql1,ARRAY_A);
                //print_r($row);
                if(empty($row)){

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
                    $_GET['log_id'] = $row['id'];
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

        //请求接下来的比赛项目
        //获取当前项目
        $project = $this->get_grading_parameter($order->memory_lv);
        $keys = array_keys($project);
        $index = array_search($row['questions_type'],$keys)+1;
        $next_index = $keys[$index];
        $next_project = $project[$next_index];

        //如果为当前考级最后一项就计算考核结果
        if(empty($next_project)){
            //http://127.0.0.1/nlyd/gradings/initialMatch/grad_id/882/grad_type/memory/type/sz/memory_lv/1/
            $next_project_url = home_url();
        }else{
            $next_project['title'] = $this->get_memory_type_title($next_index);
            $next_project_url = home_url('gradings/initialMatch/grad_id/'.$_GET['grad_id'].'/grad_type/'.$_GET['grad_type'].'/type/'.$next_index);
            if($order->memory_lv > 0 ){
                $next_project_url .= '/memory_lv/'.$order->memory_lv;
            }
        }
        //print_r($next_project);
        $data = array(
            'next_project'=>$next_project,
            'next_count_down'=>300,
            'str_len'=>$len,
            'match_more'=>$this->match_more,
            'success_length'=>$success_len,
            'accuracy'=>$row['correct_rate'] > 0 ? $row['correct_rate']*100 : 0,
            'match_questions'=>$match_questions,
            'questions_answer'=>$questions_answer,
            'my_answer'=>$my_answer,
            'answer_array'=>$answer_array,
            'error_arr'=>!empty($error_arr) ? array_keys($error_arr) : array(),
            'next_project_url'=>$next_project_url,
            'match_row'=>$row,
        );
        //print_r($data);
        $view = student_view_path.CONTROLLER.'/match-answer-log.php';
        load_view_template($view,$data);
    }



    public function grading_voice(){//人脉信息记忆页
        $view = student_view_path.CONTROLLER.'/grading-voice.php';
        load_view_template($view);
    }
    public function grading_rmxx(){//人脉信息记忆页
        $view = student_view_path.CONTROLLER.'/grading-rmxx.php';
        load_view_template($view);
    }
    public function grading_zwcy(){//中文词语记忆页
        $view = student_view_path.CONTROLLER.'/grading-zwcy.php';
        load_view_template($view);
    }
    public function grading_szzb(){//数字英文字母记忆页
        $view = student_view_path.CONTROLLER.'/grading-szzb.php';
        load_view_template($view);
    }   
    public function matching_PI(){//圆周率默写
        $view = student_view_path.CONTROLLER.'/matching-PI.php';
        load_view_template($view);
    }
    public function matching_silent(){//国学经典默写
        $view = student_view_path.CONTROLLER.'/matching-silent.php';
        load_view_template($view);
    }

    public function matching_wzsd(){//文章速读比赛页
        $view = student_view_path.CONTROLLER.'/matching-reading.php';
        load_view_template($view);
    }
    public function ready_wzsd(){//文章速读准备页
        $view = student_view_path.CONTROLLER.'/matching-ready.php';
        load_view_template($view);
    }
    public function matching_zxss(){//正向速算比赛页
        $view = student_view_path.CONTROLLER.'/matching-fastCalculation.php';
        load_view_template($view);
    }
    public function matching_nxss(){//逆向速算比赛页
        $view = student_view_path.CONTROLLER.'/matching-fastReverse.php';
        load_view_template($view);
    }
    public function record(){//考级成绩
        $view = student_view_path.CONTROLLER.'/record.php';
        load_view_template($view);
    }
    public function matchRule(){//规则
        $view = student_view_path.CONTROLLER.'/match-Rule.php';
        load_view_template($view);
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
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>3),
                );
                break;
            case 4:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>80,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>60,'answer_time'=>1800),
                    'zm'=>array('memory_time'=>300,'length'=>40,'answer_time'=>900),
                    'tl'=>array('memory_time'=>40,'length'=>40,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6),
                );
                break;
            case 5:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>120,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>80,'answer_time'=>1800),
                    'zm'=>array('memory_time'=>300,'length'=>50,'answer_time'=>900),
                    'tl'=>array('memory_time'=>45,'length'=>45,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6),
                );
                break;
            case 6:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>160,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>100,'answer_time'=>1800),
                    'zm'=>array('memory_time'=>300,'length'=>60,'answer_time'=>900),
                    'tl'=>array('memory_time'=>50,'length'=>50,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6),
                );
                break;
            case 7:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>200,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>120,'answer_time'=>1800),
                    'tl'=>array('memory_time'=>60,'length'=>60,'answer_time'=>900),
                    'rm'=>array('memory_time'=>600,'length'=>5,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6),
                );
                break;
            case 8:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>240,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>140,'answer_time'=>1800),
                    'tl'=>array('memory_time'=>70,'length'=>70,'answer_time'=>900),
                    'rm'=>array('memory_time'=>600,'length'=>6,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6),
                );
                break;
            case 9:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>280,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>160,'answer_time'=>1800),
                    'tl'=>array('memory_time'=>80,'length'=>80,'answer_time'=>900),
                    'rm'=>array('memory_time'=>600,'length'=>8,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6),
                );
                break;
            case 10:
                $project = array(
                    'sz'=>array('memory_time'=>300,'length'=>320,'answer_time'=>900),
                    'cy'=>array('memory_time'=>900,'length'=>180,'answer_time'=>1800),
                    'tl'=>array('memory_time'=>100,'length'=>100,'answer_time'=>900),
                    'rm'=>array('memory_time'=>600,'length'=>10,'answer_time'=>900),
                    'wz'=>array('memory_time'=>1800,'length'=>100,'num'=>6),
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
                DATE_FORMAT(a.end_time,'%Y-%m-%d %H:%i') end_time,
                case a.status
                when -2 then '等待开赛'
                when 1 then '报名中'
                when 2 then '进行中'
                else '已结束'
                end status_cn,
                case e.meta_value
                when 'memory' then '记忆'
                when 'arithmetic' then '速算'
                when 'reading' then '速读'
                end project_alias_cn,
                e.meta_value project_alias
                from wp_grading_meta a 
                left join wp_posts b on a.grading_id = b.ID 
                left join wp_posts c on a.category_id = c.ID 
                left join wp_order d on a.grading_id = d.match_id
                left join wp_postmeta e ON a.category_id = e.post_id AND meta_key = 'project_alias'
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
     * @param $match_id 比赛id
     * @param $log_id   答题记录
     * @return array|null|object|void
     */
    public function get_grading_questions($match_id,$log_id){

        global $wpdb,$current_user;
        $sql = "select grading_type,questions_type,submit_type,leave_page_time,grading_questions,questions_answer,my_answer,correct_rate,is_true,
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
        wp_register_style( 'my-public', student_css_url.'matchs/matching-public.css',array('my-student') );
        wp_enqueue_style( 'my-public' );
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

            if(in_array($_GET['type'],array('sz','cy','yzl'))){//中文词语记忆

                wp_register_style( 'my-student-matching-numberBattle', student_css_url.'matching-numberBattle.css',array('my-student') );
                wp_enqueue_style( 'my-student-matching-numberBattle' );
            }

            if($_GET['type'] == 'sz' ){//进入数字争霸准备页面
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
        }

        if(ACTION == 'answerLog'){//
            wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
            wp_enqueue_style( 'my-student-subject' );

        }


        if(ACTION == 'grading_zwcy' || ACTION == 'matching_PI'){//中文词语记忆
            wp_register_style( 'my-student-matching-numberBattle', student_css_url.'matching-numberBattle.css',array('my-student') );
            wp_enqueue_style( 'my-student-matching-numberBattle' );
        }

        if(ACTION == 'grading_voice'){
            wp_register_style( 'my-student-matching-numberBattle', student_css_url.'matching-numberBattle.css',array('my-student') );
            wp_enqueue_style( 'my-student-matching-numberBattle' );
        }
        if(ACTION == 'grading_rmxx'){//人脉信息记忆
            wp_register_style( 'my-student-matching-card', student_css_url.'grading/card.css',array('my-student') );
            wp_enqueue_style( 'my-student-matching-card' );
        }
        if(ACTION == 'matching_silent'){//国学经典默写
            wp_register_style( 'my-student-matching-silent', student_css_url.'grading/silent.css',array('my-student') );
            wp_enqueue_style( 'my-student-matching-silent' );
        }

        if(ACTION == 'matching_wzsd'){//文章速读比赛页
            wp_register_style( 'my-student-reading', student_css_url.'matching-reading.css',array('my-student') );
            wp_enqueue_style( 'my-student-reading' );
        }
        if(ACTION == 'ready_wzsd'){//文章速读准备页
            wp_register_style( 'my-student-ready-reading', student_css_url.'ready-reading.css',array('my-student') );
            wp_enqueue_style( 'my-student-ready-reading' );
        }
        if(ACTION == 'matching_zxss' ){//正向速算比赛页
            wp_register_style( 'my-student-fastCalculation', student_css_url.'matching-fastCalculation.css',array('my-student') );
            wp_enqueue_style( 'my-student-fastCalculation' );
        }

        if(ACTION == 'matching_nxss'){//逆向速算比赛页
            wp_register_script( 'student-check24_answer',student_js_url.'matchs/check24_answer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-check24_answer' );
            wp_register_style( 'my-student-fastReverse', student_css_url.'matching-fastReverse.css',array('my-student') );
            wp_enqueue_style( 'my-student-fastReverse' );
            wp_register_style( 'my-student-matching-fastReverse', student_css_url.'matching-fastReverse.css',array('my-student') );
            wp_enqueue_style( 'my-student-matching-fastReverse' );
        }
    }
}