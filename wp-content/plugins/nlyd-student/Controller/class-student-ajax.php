<?php
/**
 * Ajax操作类
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/6/22
 * Time: 14:10
 */
use library\AliSms;
use library\TwentyFour;
class Student_Ajax
{

    function __construct() {

        //如果有提交并且提交的内容中有规定的提交数据
        $data = $_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST : $_GET;
        if(empty($data['action']) ) return;
        $action = $data['action'];

        //判斷方法是否存在
        if(!method_exists($this,$action)) return;

        add_action( 'wp_ajax_'.$action,array($this, $action) );
        add_action( 'wp_ajax_nopriv_'.$action,  array($this,$action) );
    }


    /**
     *获取24点结果
     */
    public function get_24_result(){

        if(empty($_POST['numbers'])) wp_send_json_error(array('info'=>__('参数不能为空', 'nlyd-student')));
        if(!is_array($_POST['numbers'])) wp_send_json_error(array('info'=>__('参数必须是数组', 'nlyd-student')));
        if(empty($_POST['match_more']) || empty($_POST['project_alias'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));

        if(empty($_POST['my_answer'])){

            if(isset($_SESSION['count_down']['count_down_time'])){
                $default_count_down = $_SESSION['count_down']['count_down_time']-get_time() - 2;

                $_SESSION['count_down']['count_down_time'] = get_time() + $default_count_down;
            }

            wp_send_json_error(array('info'=>$default_count_down));

        }else{

            $twentyfour = new TwentyFour();
            //$_POST['numbers'] = array(5,5,5,9);
            if($_POST['my_answer'] == 'unsolvable'){

                $results = $twentyfour->calculate($_POST['numbers']);

                if(empty($results)){
                    wp_send_json_success(array('info'=>true));
                }else{
                    wp_send_json_error(array('info'=>false));
                }

            }else{
                //$_POST['my_answer'] = '(5+5)+5+9';
                $my_answer = str_replace('×','*',$_POST['my_answer']);
                $my_answer = str_replace('÷','/',$my_answer);
                //var_dump(preg_match('/^((\d++(\.\d+)?|\((?1)\))((\+|\/|\*|-)(\d++(\.\d+)?|(?1)))*)$/', $my_answer));die;
                if(preg_match('/^((\d++(\.\d+)?|\((?1)\))((\+|\/|\*|-)(\d++(\.\d+)?|(?1)))*)$/', $my_answer)){

                    $l_cont = substr_count($my_answer,"(");
                    $r_cont = substr_count($my_answer,")");

                    if(($l_cont != 0) || ($r_cont != 0)){
                        if((substr_count($my_answer,"(") != substr_count($my_answer,")")) ){
                            wp_send_json_success(array('info'=>false));
                        }
                    }

                    $b = 0;
                    $str = '$b = '.$my_answer.';';
                    eval($str);
                    //var_dump($b);
                    if($b == 24){
                        wp_send_json_success(array('info'=>true));
                    }else{

                        wp_send_json_success(array('info'=>false));
                    }

                }

                wp_send_json_error(array('info'=>false));

            }
        }
    }


    /**
     * 成绩排名查看
     */
    public function get_score_ranking(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_ranking_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        //获取当前项目排名
        global $wpdb,$current_user;

        $page = ($page = intval($_POST['page'])) < 1 ? 1 : $page;
        $pageSize = 50;
        $start = ($page-1) * $pageSize;

        if($_POST['type'] == 'project'){

            if(empty($_GET['project_id']) || empty($_GET['match_more'])){
                wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));
            }
            $sql = "select user_id,my_score from {$wpdb->prefix}match_questions where match_id = {$_GET['match_id']} and project_id = {$_GET['project_id']} and match_more = {$_GET['match_more']} order by my_score desc,surplus_time desc limit {$start},{$pageSize} ";
            //print_r($sql);
            $rows = $wpdb->get_results($sql,ARRAY_A);
        }
        else{

            $where = " WHERE a.match_id = {$_POST['match_id']} AND (a.pay_status = 4 or a.pay_status = 2) and a.order_type = 1 ";
            $age_where = '';
            $left_where = '';
            //判断是否存在分类id
            if(!empty($_POST['category_id'])){
                //获取当前分类下项目
                $sql_ = "select ID from {$wpdb->prefix}posts where post_parent = {$_POST['category_id']}";
                $category = $wpdb->get_results($sql_,ARRAY_A);
                if(!empty($category)){
                    $project_id = arr2str(array_column($category,'ID'));
                    $left_where .= " and c.project_id in ({$project_id}) ";
                }
            }
            if(!empty($_POST['project_id'])){
                $left_where .= " and c.project_id = {$_POST['project_id']} ";
            }

            if(!empty($_POST['age_group'])){
                $age = $_POST['age_group'];
                $age_where = " WHERE ";
                switch ($age){
                    case $age == 1:
                        $age_where .= "  y.meta_value < 13 ";
                        break;
                    case $age == 2:
                        $age_where .= "  (y.meta_value > 12 and y.meta_value < 18) ";
                        break;
                    case $age == 3:
                        $age_where .= "  (y.meta_value > 17 and y.meta_value < 60) ";
                        break;
                    default:
                        $age_where .= "  y.meta_value > 59 ";
                        break;
                }
                $age_left = " left join `{$wpdb->prefix}usermeta` y on x.user_id = y.user_id and y.meta_key='user_age' ";
            }

            if(!empty($_POST['match_more'])){
                $left_where .= " and c.match_more = {$_POST['match_more']} ";
            }

            $sql3 = "SELECT x.user_id,SUM(x.my_score) my_score ,SUM(x.surplus_time) surplus_time ,x.created_microtime
                        FROM(
                            SELECT a.user_id,a.match_id,if(MAX(c.created_microtime) > 0, MAX(c.created_microtime) ,0) created_microtime ,c.project_id,if(MAX(c.my_score) > 0 ,MAX(c.my_score),0) my_score , if(MAX(c.surplus_time) ,MAX(c.surplus_time) ,0) surplus_time 
                            FROM `{$wpdb->prefix}order` a 
                            LEFT JOIN {$wpdb->prefix}match_questions c ON a.user_id = c.user_id  and c.match_id = {$_POST['match_id']} and c.is_true = 1  {$left_where}
                            {$where}
                            GROUP BY user_id,project_id
                        ) x
                        {$age_left}
                        {$age_where}
                        GROUP BY user_id
                        ORDER BY my_score DESC,surplus_time DESC,x.created_microtime ASC";
            //print_r($sql3);
            /*if($current_user->ID == 63){
                print_r($sql);
            }*/
            $rows = $wpdb->get_results($sql3,ARRAY_A);
            //print_r($rows);

        }
        $total = count($rows);
        $remainder = $total%$pageSize;
        $maxPage = ceil($total/$pageSize);

        if($_POST['page'] > $maxPage && $total != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无列表信息', 'nlyd-student')));

        $list = array();
        foreach ($rows as $k => $val){
            $sql1 = " select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id = {$val['user_id']} and meta_key in('user_address','user_ID','user_real_name','user_age') ";
            $info = $wpdb->get_results($sql1,ARRAY_A);

            if(!empty($info)){
                $user_info = array_column($info,'meta_value','meta_key');
                $user_real_name = !empty($user_info['user_real_name']) ? unserialize($user_info['user_real_name']) : '';

                $list[$k]['user_name'] = !empty($user_real_name['real_name']) ? $user_real_name['real_name'] : '-';
                if(!empty($user_info['user_age'])){
                    $age = $user_info['user_age'];
                    $group = getAgeGroupNameByAge($age);

                }else{
                    $group = '-';
                }
                if(!empty($user_info['user_address'])){
                    $user_address = unserialize($user_info['user_address']);
                    $city = $user_address['city'] == '市辖区' ? $user_address['province'] : $user_address['city'];
                }else{
                    $city = '-';
                }

                $list[$k]['ID'] = $user_info['user_ID'];
                $list[$k]['city'] = $city;
                //$list[$k]['score'] = $val['my_score'];
                $list[$k]['group'] = $group;
                $list[$k]['score'] = $val['my_score'] > 0 ? $val['my_score'] : 0;
                $list[$k]['surplus_time'] = $val['surplus_time'] > 0 ? $val['surplus_time'] : 0;
                $list[$k]['ranking'] = $k+1;
                ///////
                $my_score = $val['my_score'] > 0 ? $val['my_score'] : 0;
                $surplus_time = $val['surplus_time'] > 0 ? $val['surplus_time'] : 0;
                if($val['my_score']==0){

                    if($k != 0){
                        if(($my_score == $rows[$k-1]['my_score'] && $surplus_time == $rows[$k-1]['surplus_time']) || ($my_score == 0 &&  $rows[$k-1]['my_score'] == 0)){
                            $list[$k]['ranking'] = !empty($list[$k-1]['ranking']) ? $list[$k-1]['ranking'] : $list[$k-2]['ranking'];
                        }
                    }
                    if( !empty($_POST['lastItem']) ){
                        $last = $_POST['lastItem'];
                        if($my_score == 0 && $my_score == $last['score']){
                            $list[$k]['ranking'] = $last['ranking'];
                        }else if($my_score == $last['score'] && $surplus_time == $last['surplus_time']){
                            $list[$k]['ranking'] = $last['ranking'];
                        }
                    }

                }
                if($val['user_id'] == $current_user->ID){
                    $my_ranking = $list[$k];
                }
            }
        }
        if($maxPage == $_POST['page']){
            if($total < $pageSize){
                $pageSize = $total;
            }else{
                
                $pageSize = $remainder < 1 ? $pageSize : $remainder;
            }
               // $pageSize = $remainder;
        }  
            
        $list2 = array_slice($list,$start,$pageSize);

        wp_send_json_success(array('info'=>$list2,'my_ranking'=>$my_ranking));

    }

    /**
     * 答案提交
     */
    public function answer_submit(){


        unset($_SESSION['count_down']);
        unset($_SESSION['match_post_id']);

        if(empty($_POST['match_id']) || empty($_POST['project_id']) || empty($_POST['match_more']) ) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));

        $_SESSION['match_data'] = $_POST;

        ini_set('post_max_size','20M');

        global $wpdb,$current_user;
        $sql = "select id,answer_status,questions_answer
                from {$wpdb->prefix}match_questions
                where user_id = {$current_user->ID} and match_id = {$_POST['match_id']} and project_id = {$_POST['project_id']} and match_more = {$_POST['match_more']}
                ";
        $row = $wpdb->get_row($sql,ARRAY_A);
        //print_r($sql);
        if($row['answer_status'] == 1) wp_send_json_success(array('info'=>__('答案已提交', 'nlyd-student'),'url'=>home_url('matchs/answerLog/match_id/'.$_POST['match_id'].'/log_id/'.$row['id'].'/project_alias/'.$_POST['project_alias'].'/project_more_id/'.$_POST['project_more_id'])));

        //计算成绩

        switch ($_POST['project_alias']){
            case 'szzb':
            case 'pkjl':
                //print_r($_POST);die;
                if(!empty($_POST['my_answer'])){

                    $len = count($_POST['questions_answer']);

                    $error_len = count(array_diff_assoc($_POST['questions_answer'],$_POST['my_answer']));

                    $score = $_POST['project_alias'] == 'szzb' ? 12 : 18;

                    $my_score = ($len-$error_len)*$score;

                    if ($error_len == 0 && !empty($_POST['my_answer'])){
                        $my_score += $_POST['surplus_time'] * 1;
                    }
                }else{
                    $my_score = 0;
                }

                break;
            case 'kysm':
            case 'zxss':
            case 'nxss':


                $data_arr = $_POST['my_answer'];
                //print_r($data_arr);die;
                if(!empty($data_arr)){
                    $match_questions = array_column($data_arr,'question');
                    $questions_answer = array_column($data_arr,'rights');
                    $_POST['my_answer'] = array_column($data_arr,'yours');
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
                    $error_len = count(array_diff_assoc($questions_answer,$_POST['my_answer']));
                    $my_score = ($len-$error_len)*10;
                }


                $_POST['match_questions'] = $match_questions;
                $_POST['questions_answer'] = $questions_answer;

                break;
            case 'wzsd':
                //print_r($_POST);die;
                if(empty($_POST['post_id'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));
                //print_r($_POST);die;
                $questions_answer = $_POST['questions_answer'];
                $len = count($questions_answer);
                $success_len = 0;

                foreach ($questions_answer as $k=>$val){
                    $arr = array();
                    foreach ($val['problem_answer'] as $key => $v){
                        if($v == 1){
                            $arr[] = $key;
                        }
                    }

                    if(isset($_POST['my_answer'][$k])){
                        if(arr2str($arr) == arr2str($_POST['my_answer'][$k])) ++$success_len;
                    }
                }
                $my_score = $success_len * 23;
                if ($success_len/$len >= 0.8){
                    $my_score += $_POST['surplus_time'] * 1;
                }

                break;
            default:
                break;
        }

        //判断当前答题是否有效
        $prison_log_id = $wpdb->get_var("select id from {$wpdb->prefix}prison_match_log where user_id = {$current_user->ID} and match_id = {$_POST['match_id']} and project_id = {$_POST['project_id']} and match_more = {$_POST['match_more']} ");

        $insert = array(
            'user_id'=>$current_user->ID,
            'match_id'=>$_POST['match_id'],
            'project_id'=>$_POST['project_id'],
            'match_more'=>$_POST['match_more'],
            'match_questions'=>json_encode($_POST['match_questions']),
            'questions_answer'=>json_encode($_POST['questions_answer']),
            'my_answer'=>json_encode($_POST['my_answer']),
            'surplus_time'=>$_POST['surplus_time'],
            'my_score'=>$my_score,
            'answer_status'=>1,
            'submit_type'=>isset($_POST['submit_type']) ? $_POST['submit_type'] : 1,
            'leave_page_time'=>isset($_POST['leave_page_time']) ? json_encode($_POST['leave_page_time']) : '',
            'created_time'=>get_time('mysql'),
            'created_microtime'=>str2arr(microtime(),' ')[0],
            'post_id'=>isset($_POST['post_id']) ? $_POST['post_id'] : '',
            'is_true'=>!empty($prison_log_id) ? 2 : 1,
        );

         /*print_r($insert);
         die;*/
        $result = $wpdb->insert($wpdb->prefix.'match_questions',$insert);

        if($result){
            $log_id = $wpdb->insert_id;
            if(!empty($_POST['post_id']) && $_POST['project_alias'] == 'wzsd'){

                /*//获取该文章原始分类
                $sql = " select b.slug from {$wpdb->prefix}term_relationships a left join {$wpdb->prefix}terms b on a.term_taxonomy_id = b.term_id where a.object_id = {$_POST['post_id']}";
                $slug = $wpdb->get_var($sql);
                if($slug == 'en-match-question'){
                    $type = 'cn-test-question';
                }else{
                    $type = 'en-test-question';
                }
                //修改其分类
                $a = wp_set_object_terms( $_POST['post_id'], array($type) ,'question_genre');
                //var_dump($a);die;*/

                $sql1 = "select id from {$wpdb->prefix}user_post_use where user_id = {$current_user->ID} and type = 1 ";
                $use_id = $wpdb->get_row($sql1,ARRAY_A);
                if($use_id){
                    $sql2 = "UPDATE {$wpdb->prefix}user_post_use SET post_id = if(post_id = '',{$_POST['post_id']},CONCAT_WS(',',post_id,{$_POST['post_id']})) WHERE user_id = {$current_user->ID} and type = 1";
                    $a = $wpdb->query($sql2);
                }else{

                    $a = $wpdb->insert($wpdb->prefix.'user_post_use',array('user_id'=>$current_user->ID,'post_id'=>$_POST['post_id'],'type'=>1));
                }

            }

            wp_send_json_success(array('info'=>__('提交完成', 'nlyd-student'),'url'=>home_url('matchs/answerLog/match_id/'.$_POST['match_id'].'/log_id/'.$log_id.'/project_alias/'.$_POST['project_alias'].'/project_more_id/'.$_POST['project_more_id'])));
        }else {
            wp_send_json_error(array('info' => __('提交失败', 'nlyd-student')));
        }
    }

    /**
     * 记忆完成提交
     */
    public function memory_complete(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_memory_complete_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        if(empty($_POST['match_id']) || empty($_POST['project_id']) || empty($_POST['match_more'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));

        global $wpdb,$current_user;
        $arr = array('answer_status'=>-1);
        if( $_POST['type']=='pkjl' || $_POST['type']=='szzb'){
            if(empty($_POST['match_questions'])) wp_send_json_error(array('info'=>__('题目记忆失败,请联系管理员', 'nlyd-student')));
            $arr['match_questions'] = json_encode($_POST['match_questions']);
            $arr['questions_answer'] = json_encode($_POST['match_questions']);
        }
        //var_dump($_POST);die;
        $result = $wpdb->update($wpdb->prefix.'match_questions',$arr,array('user_id'=>$current_user->ID,'match_id'=>$_POST['match_id'],'project_id'=>$_POST['project_id'],'match_more'=>$_POST['match_more']));

        $answer_status = $wpdb->get_var("select answer_status from {$wpdb->prefix}match_questions where user_id = {$current_user->ID} and match_id = {$_POST['match_id']} and project_id = {$_POST['project_id']} and match_more = {$_POST['match_more']}");

        if($result || $answer_status == -1){
            $url = home_url('matchs/answerMatch/match_id/'.$_POST['match_id'].'/project_id/'.$_POST['project_id'].'/match_more/'.$_POST['match_more']);
            if(isset($_POST['questions_id']) && !empty($_POST['questions_id'])){
                $url .= '&questions_id='.$_POST['questions_id'];
            }
            wp_send_json_success(array('info'=>__('即将跳转', 'nlyd-student'),'url'=>$url));
        }else{
            wp_send_json_error(array('info'=>__('记忆失败', 'nlyd-student')));
        }
    }


    /**
     * 报名支付
     */
    public function entry_pay(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_go_pay_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        if(empty($_POST['match_id']) || !isset($_POST['cost'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));

        global $wpdb,$current_user;
        if($current_user->ID < 1 || !$current_user->ID){
            wp_send_json_error(array('info'=>__('请先登录', 'nlyd-student'),'url'=>home_url('/logins/')));
        }

        if(empty(get_user_meta($current_user->ID,'user_real_name'))){
            wp_send_json_error(array('info'=>__('请先实名认证', 'nlyd-student'),'url'=>home_url('/account/info/')));
        }

        //if(count($_POST['project_id']) != count($_POST['major_coach'])) wp_send_json_error(array('info'=>'主训教练未设置齐全'));
        //if(empty($_POST['team_id'])) wp_send_json_error(array('info'=>'所属战队不能为空'));
        //if(empty($_POST['fullname'])) wp_send_json_error(array('info'=>'收件人姓名不能为空'));
        //if(empty($_POST['telephone'])) wp_send_json_error(array('info'=>'联系电话不能为空'));
        //if(empty($_POST['address'])) wp_send_json_error(array('info'=>'收货地址不能为空'));

        if($_POST['order_type'] == 1){  //比赛

            $title = '比赛';
            $info_url = home_url('matchs/info/match_id/'.$_POST['match_id']);
            $sql = "select match_id,match_scene,match_status,match_max_number from {$wpdb->prefix}match_meta_new where match_id = {$_POST['match_id']} ";
            $match_meta = $wpdb->get_row($sql,ARRAY_A);
            if(empty($match_meta)) wp_send_json_error(array('info'=>__('比赛信息错误', 'nlyd-student')));
            if($match_meta['match_status'] != 1) wp_send_json_error(array('info'=>__('当前比赛已禁止报名', 'nlyd-student')));
            $total = $wpdb->get_var("select count(id) total from {$wpdb->prefix}order where match_id = {$_POST['match_id']} ");
            if($match_meta['match_max_number'] > 0){

                if(!empty($total)){
                    if($total >= $match_meta['match_max_number']) wp_send_json_error(array('info'=>__('已达到最大报名数,请联系管理员', 'nlyd-student')));
                }
            }
        }
        elseif($_POST['order_type'] == 2){  //考级

            $title = '考级';
            $info_url = home_url('gradings/info/grad_id/'.$_POST['match_id']);
            $sql = "select grading_id,scene,status from {$wpdb->prefix}grading_meta where grading_id = {$_POST['match_id']} ";
            $match_meta = $wpdb->get_row($sql,ARRAY_A);
            if(empty($match_meta)) wp_send_json_error(array('info'=>__('考级信息错误', 'nlyd-student')));
            if($match_meta['status'] != 1) wp_send_json_error(array('info'=>__('当前考级已禁止报名', 'nlyd-student')));

            if(isset($_POST['memory_lv'])){
                if($_POST['memory_lv'] < 1){
                    wp_send_json_error(array('info'=>__('请选择考级等级', 'nlyd-student')));
                }
            }

        }
        elseif($_POST['order_type'] == 3){   //课程

            $title = '课程';
            $info_url = home_url('courses/courseDetail/id/'.$_POST['match_id']);
            $sql = "select id,is_enable from {$wpdb->prefix}course where id = {$_POST['match_id']} ";
            $match_meta = $wpdb->get_row($sql,ARRAY_A);
            if(empty($match_meta)) wp_send_json_error(array('info'=>__('课程信息错误', 'nlyd-student')));
            if($match_meta['is_enable'] != 1) wp_send_json_error(array('info'=>__('当前课程已禁止报名', 'nlyd-student')));
            //获取当前类课程是否有教练
            /*$sql_ = "select b.coach_id from {$wpdb->prefix}course a left join {$wpdb->prefix}my_coach b on a.course_category_id = b.category_id where a.id = {$_POST['match_id']} and b.user_id = {$current_user->ID} and b.apply_status = 2";
            $coach_id = $wpdb->get_var($sql_);
            if(empty($coach_id)) wp_send_json_error(array('info'=>__('未绑定该课程分类教练,请核实','nlyd-student')));*/
            //print_r($coach_id);die;
        }


        $row = $wpdb->get_row("select id,pay_status from {$wpdb->prefix}order where user_id = {$current_user->ID} and match_id = {$_POST['match_id']}");

        if(!empty($row)) {
            if($row->pay_status == 2 || $row->pay_status==3 || $row->pay_status==4){

                wp_send_json_error(array('info'=>__('你已报名该'.$title, 'nlyd-student'),'url'=>$info_url));
            }else{
                //如果是未支付订单删除订单重新下单
                $wpdb->delete($wpdb->prefix.'order', ['id' => $row->id]);
            }
        }

        $data = array(
            'user_id'=>$current_user->ID,
            'match_id'=>$_POST['match_id'],
//            'cost'=>intval($_POST['cost']),
            'cost'=> $_POST['cost'],
            'fullname'=>!empty($_POST['fullname']) ? $_POST['fullname'] : '' ,
            'telephone'=>!empty($_POST['telephone']) ? $_POST['telephone'] : '',
            'address'=>!empty($_POST['address']) ? $_POST['address'] : '',
            'order_type'=>isset($_POST['order_type']) ? $_POST['order_type'] : 1,
            'sub_centres_id'=>isset($_POST['sub_centres_id']) ? $_POST['sub_centres_id'] : '',
            'pay_status'=>1,
            'created_time'=>get_time('mysql'),
        );
        if($_POST['order_type'] == 2){
            $data['memory_lv'] = !empty($_POST['memory_lv']) ? $_POST['memory_lv'] : '';
        }
        //print_r($data);die;
        //TODO 测试时 订单价格为0
//        $_POST['cost'] = 0;
        //如果报名金额为0, 直接支付成功状态
        if($_POST['cost'] == 0 || $_POST['cost'] < 0.01){
            $data['pay_status'] = 4;
        }

        //开启事务
        $wpdb->query('START TRANSACTION');
        $a = $wpdb->insert($wpdb->prefix.'order',$data);

        //生成流水号
        $serialnumber = createNumber($current_user->ID,$wpdb->insert_id);
        $b = $wpdb->update($wpdb->prefix.'order',array('serialnumber'=>$serialnumber),array('id'=>$wpdb->insert_id));
        //print_r($a.'---'.$b);die;
        if($b && $a ){
            $wpdb->query('COMMIT');

            if($_POST['cost'] == 0 || $_POST['cost'] < 0.01){
                if($_POST['order_type'] == 2 || $_POST['order_type'] == 1){
                    $type_id = $_POST['order_type'] == 2 ? $match_meta['scene'] : $match_meta['match_scene'];
                    $role_alias = $wpdb->get_var("select role_alias from {$wpdb->prefix}zone_match_role where id = {$type_id}");
                    if(in_array($role_alias,array('official-grading','official-match'))){
                        /*****************收益分配start*******************/
                        $order = $wpdb->get_row('SELECT match_id,user_id,sub_centres_id,order_type FROM '.$wpdb->prefix.'order WHERE serialnumber='.$serialnumber, ARRAY_A);
                        set_user_income($order);
                        /*****************收益分配end*******************/
                    }
                }
            }
            
            if($data['pay_status'] == 2 || $data['pay_status'] == 4){
                wp_send_json_success(array('info' => __('报名成功', 'nlyd-student'),'serialnumber'=>$serialnumber, 'is_pay' => 0, 'url' => home_url('payment/success/serialnumber/'.$serialnumber)));
            }
            wp_send_json_success(array('info' => __('请选择支付方式', 'nlyd-student'),'serialnumber'=>$serialnumber,'is_pay' => 1));
        }else{

            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>__('提交失败', 'nlyd-student')));
        }
    }


    /**
     * 获取当前教练学员
     */
    public function get_cocah_member($json=true){
        global $wpdb;

        $coach_id = $_POST['coach_id'];
        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        $sql = "select SQL_CALC_FOUND_ROWS a.user_id,a.apply_status,
                IFNULL (b.read, '-') as `read`,
                IFNULL(b.memory,'-') as memory, 
                IFNULL(b.compute,'-') as `compute`
                from {$wpdb->prefix}my_coach a
                left join {$wpdb->prefix}user_skill_rank b on a.user_id = b.user_id 
                where a.apply_status = 2 and a.coach_id = {$coach_id} GROUP BY a.user_id 
                limit {$start},{$pageSize}
                ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无列表信息', 'nlyd-student')));

        if(!empty($rows)){
            foreach ($rows as $k => $v){
                $sql1 = "select meta_key,meta_value from {$wpdb->prefix}usermeta where meta_key in('user_head','user_ID','nickname','user_real_name') and user_id = {$v['user_id']}";
                $user = $wpdb->get_results($sql1,ARRAY_A);
                $user_info = array_column($user,'meta_value','meta_key');

                if(!empty($user_info['user_real_name'])){
                    $user_real = unserialize($user_info['user_real_name']);
                    $rows[$k]['real_age'] = $user_real['real_age'];
                    $rows[$k]['nickname'] = $user_real['real_name'];
                }else{
                    $rows[$k]['nickname'] = $user_info['nickname'];
                }
                $rows[$k]['user_ID'] = $user_info['user_ID'];

                $rows[$k]['user_head'] = !empty($user_info['user_head']) ? $user_info['user_head'] : student_css_url.'image/nlyd.png';
                $rows[$k]['mental'] = __('待定', 'nlyd-student');
            }
        }
        if(is_ajax()){
            wp_send_json_success(array('list'=>$rows));
        }else{
            return $rows;
        }
    }

    /**
     * 加入/退出战队
     */
    public function set_team(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_set_team_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        //$_POST['team_id'] = 407;
        if(empty($_POST['team_id'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));
        global $wpdb,$current_user;
        //判断是否登录
        if($current_user->ID < 1) wp_send_json_error(array('info'=>__('未登录', 'nlyd-student')));
        //判断是否有战队
        $sql = "select id,team_id,user_id,status from {$wpdb->prefix}match_team where user_id = {$current_user->ID} ";
        //开启事务,发送短信失败回滚
        $wpdb->query('START TRANSACTION');
        if($_POST['handle'] == 'join'){ //加入战队
            $applyTypeName = __('加入战队', 'nlyd-student');
            $sql .= " and status > -2 ";
            $row = $wpdb->get_row($sql);
            if(!empty($row)){
                switch ($row->status){
                    case -1:
                        $info = __('离队申请正在审核,暂时不能申请加入战队', 'nlyd-student');
                        break;
                    case 1:
                        $info = __('入队申请正在审核,暂时不能申请加入战队', 'nlyd-student');
                        break;
                    case 2:
                        $info = __('已有战队,暂时不能申请加入战队,请先申请离队', 'nlyd-student');
                        break;
                }
                $wpdb->query('ROLLBACK');
                wp_send_json_error(array('info'=>$info));
            }
            $id = $wpdb->get_var("select id from {$wpdb->prefix}match_team where team_id = {$_POST['team_id']} and user_id = {$current_user->ID} and user_type = 1");

            if(empty($id)){
                $result = $wpdb->insert($wpdb->prefix.'match_team',array('team_id'=>$_POST['team_id'],'user_id'=>$current_user->ID,'user_type'=>1,'status'=>1,'created_time'=>get_time('mysql')));
            }else{
                $result = $wpdb->update($wpdb->prefix.'match_team',array('status'=>1,'created_time'=>get_time('mysql')),array('id'=>$id,'team_id'=>$_POST['team_id'],'user_id'=>$current_user->ID));
            }
            $msgTemplate = 11;
        }else{
            $applyTypeName = __('退出战队', 'nlyd-student');
            $sql .= " and team_id = {$_POST['team_id']} and status = 2 ";
            //print_r($sql);die;
            $row = $wpdb->get_row($sql);
            if(empty($row)){
                $wpdb->query('ROLLBACK');
                wp_send_json_error(array('info'=>__('你还没有加入任何战队', 'nlyd-student')));
            }

            $result = $wpdb->update($wpdb->prefix.'match_team',array('status'=>-1),array('user_id'=>$current_user->ID,'team_id'=>$_POST['team_id']));
            $msgTemplate = 12;
        }
        if($result){
            $wpdb->query('COMMIT');
            /***短信通知战队负责人****/
            $director = $wpdb->get_row('SELECT u.user_mobile,u.display_name,u.ID,u.user_email AS uid FROM '.$wpdb->prefix.'team_meta AS tm 
            LEFT JOIN '.$wpdb->users.' AS u ON u.ID=tm.team_director WHERE tm.team_id='.$_POST['team_id'], ARRAY_A);
            if($director){
                $userContact = getMobileOrEmailAndRealname($director['ID'], $director['user_mobile'], $director['user_email']);
                if($userContact){
                    $userID = get_user_meta($current_user->ID, 'user_ID')[0];
                    if($userContact['type'] == 'mobile'){
                        $ali = new AliSms();
                        $result = $ali->sendSms($userContact['contact'], $msgTemplate, array('teams'=>$userContact['real_name'], 'user_id' => $userID));
                    }else{
                        $result = send_mail($userContact['contact'], 14, ['teams' => $userContact['real_name'], 'userID' => $userID, 'applyType' => $applyTypeName]);
                    }
                }
            }
//            $userID = get_user_meta($current_user->ID, 'user_ID')[0];
//            $ali = new AliSms();
////            print_r($director);die;
//            $result = $ali->sendSms($director['user_mobile'], $msgTemplate, array('teams'=>str_replace(', ', '', $director['display_name']), 'user_id' => $userID));
            /***********end************/
            wp_send_json_success(array('info'=>__('操作成功,等待战队受理', 'nlyd-student')));
        }
        $wpdb->query('ROLLBACK');
        wp_send_json_error(array('info'=>__('操作失败', 'nlyd-student')));

    }

    /**
     * 获取当前战队队员/教练
     */
    public function get_team_member($json=true){

        global $wpdb;

        $type = isset($_POST['type']) ? $_POST['type'] : 1;
        $team_id = $_POST['team_id'];
        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        $sql = "select SQL_CALC_FOUND_ROWS a.user_id,a.user_type,
                a.status,IFNULL(b.read,'-') as `read`,
                IFNULL(b.memory,'-') as memory,
                IFNULL(b.compute,'-') as compute
                from {$wpdb->prefix}match_team a
                left join {$wpdb->prefix}user_skill_rank b on a.user_id = b.user_id and b.skill_type=1
                where a.user_type = {$type} and a.status = 2 and a.team_id = {$team_id} 
                limit {$start},{$pageSize}
                ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );

        if($_POST['page'] > $maxPage && $total['total'] != 0 ) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无列表信息', 'nlyd-student')));

        if(!empty($rows)){
            foreach ($rows as $k => $v){
                $sql1 = "select meta_key,meta_value from {$wpdb->prefix}usermeta where meta_key in('user_head','user_ID','nickname','user_real_name') and user_id = {$v['user_id']}";
                $user = $wpdb->get_results($sql1,ARRAY_A);
                $user_info = array_column($user,'meta_value','meta_key');
                $rows[$k]['user_ID'] = $user_info['user_ID'];
                if(!empty($user_info['user_real_name'])){
                    $user_real = unserialize($user_info['user_real_name']);
//                    print_r($user_real);
                    $rows[$k]['nickname'] = $user_real['real_name'];
                }else{
                    $rows[$k]['nickname'] = $user_info['nickname'];
                } 
                
                $rows[$k]['user_head'] = !empty($user_info['user_head']) ? $user_info['user_head'] : student_css_url.'image/nlyd.png';
                $rows[$k]['mental'] = __('待定', 'nlyd-student');
            }
        }

        if($json=true){
            wp_send_json_success(array('info'=>$rows));
        }else{
            return $rows;
        }
    }


    /**
     * 获取所有战队列表
     */
    public function get_team_lists(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_team_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        global $wpdb,$current_user;
        $map = array();
        $map[] = " a.post_status = 'publish' ";
        $map[] = " a.post_type = 'team' ";
        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        $where = join(' and ',$map);
        $sql = "select SQL_CALC_FOUND_ROWS a.ID,a.post_title,b.user_id,b.status,
                if(c.team_world != '',c.team_world,'--') as team_world,
                if(c.team_director != '',c.team_director,'--') as team_director,
                if(c.team_slogan != '',c.team_slogan,'--') as team_slogan,
                if(c.team_leader != '',c.team_leader,'--') as team_leader
                from {$wpdb->prefix}posts a  
                left join {$wpdb->prefix}match_team b on a.ID = b.team_id and b.user_type = 1 and b.user_id = {$current_user->ID}
                left join {$wpdb->prefix}team_meta c on a.ID = c.team_id 
                where {$where} order by b.status desc limit $start,$pageSize";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        // print_r($sql);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无战队', 'nlyd-student')));
        foreach ($rows as $k => $val ){

            //获取领队
            if(is_numeric($val['team_director'])){
                $user = get_userdata( $val['team_director']);
                if(!empty($user->data->display_name)){
                    $rows[$k]['team_director'] = preg_replace('/, /','',$user->data->display_name);
                }
            }
            //获取战队成员总数
            $total = $wpdb->get_var("select count(*) from {$wpdb->prefix}match_team where team_id = {$val['ID']} and status = 2 and user_type = 1");
            $rows[$k]['team_total'] = $total;

            $rows[$k]['team_url'] = home_url('/teams/teamDetail/team_id/'.$val['ID']);
        }
        //print_r($rows);
        wp_send_json_success(array('info'=>$rows));

    }


    /**
     * 报名邮寄地址选择
     */
    public function choose_address(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_choose_address_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        $rows = $this->get_address(false);

        wp_send_json_success(array('info'=>home_url('matchs/confirm&match_id='.$_POST['match_id'].'&address_id='.$_POST['id'])));
    }


    /**
     * 设置默认地址
     */
    public function set_default_address(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_set_default_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        if(empty($_POST['id'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));

        $this->get_address(false);

        global $wpdb,$current_user;
        $wpdb->query('START TRANSACTION');
        $a = $wpdb->update($wpdb->prefix.'my_address',array('is_default'=>''),array('user_id'=>$current_user->ID));

        $b = $wpdb->update($wpdb->prefix.'my_address',array('is_default'=>1),array('id'=>$_POST['id'],'user_id'=>$current_user->ID));
        if($a && $b){
            //提交事务
            $wpdb->query('COMMIT');
            wp_send_json_success(array('info'=>__('设置成功', 'nlyd-student')));
        }else{
            //事务回滚
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>__('设置失败', 'nlyd-student')));
        }
    }

    /**
     * 获取单条地址信息
     */
    public function get_address($json=true,$id=''){

        if($json){

            /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_address_code_nonce') ) {
                wp_send_json_error(array('info'=>'非法操作'));
            }*/
        }

        if(empty($_POST['id'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));

        global $wpdb,$current_user;
        $sql = "select id,fullname,telephone,country,province,city,area,address,is_default from {$wpdb->prefix}my_address where id = {$_POST['id']} and user_id = {$current_user->ID} ";

        $row = $wpdb->get_row($sql,ARRAY_A);
        if(empty($row)) wp_send_json_error(array('info'=>__('数据错误', 'nlyd-student')));

        if($json){
            wp_send_json_success(array('info'=>$row));
        }else{
            return $row;
        }
    }

    /**
     * 删除地址
     */
    public function remove_address(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_remove_address_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        if(empty($_POST['id'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));
        global $wpdb,$current_user;
        $result = $wpdb->delete($wpdb->prefix.'my_address',array('id'=>$_POST['id'],'user_id'=>$current_user->ID));
        if($result){
            wp_send_json_success(array('info'=>__('删除成功', 'nlyd-student')));
        }else{
            wp_send_json_error(array('info'=>__('删除失败', 'nlyd-student')));
        }
    }


    /**
     * 新增/修改地址
     */
    public function save_address(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_save_address_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        global $wpdb,$current_user;

        if(mb_strlen($_POST['fullname']) < 1 || mb_strlen($_POST['fullname']) > 100) wp_send_json_error(array(__('收件人长度为1-100个字符', 'nlyd-student')));
        if(reg_match('m',$_POST['telephone'])) wp_send_json_error(array('info'=>__('手机格式不正确', 'nlyd-student')));

        if(empty($_POST['province']) || empty($_POST['city']) || empty($_POST['area']) || empty($_POST['address'])) wp_send_json_error(array('info'=>__('请确认地址信息的完整性', 'nlyd-student')));

        $_POST['user_id'] = $current_user->ID;

        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}my_address WHERE user_id = {$current_user->ID}");
        if(empty($_POST['id'])){
            if($total == 10) wp_send_json_error(array('info'=>__('最多只能添加10个收货地址', 'nlyd-student')));
        }
        $wpdb->query('START TRANSACTION');

        $a = 1;
        if(isset($_POST['is_default']) && $total > 0){
            $sql = "select id from {$wpdb->prefix}my_address where user_id = {$current_user->ID} and is_default = 1";
            if(!empty($wpdb->get_var($sql))){
                $a = $wpdb->update($wpdb->prefix.'my_address',array('is_default'=>''),array('user_id'=>$current_user->ID));
            }
        }
        $match_id = $_POST['match_id'];
        unset($_POST['match_id']);
        if(empty($_POST['id']) || $_POST['id'] < 1){

            unset($_POST['action']);
            unset($_POST['_wpnonce']);
            //print_r($_POST);

            $result = $wpdb->insert($wpdb->prefix.'my_address',$_POST);
        }else{

            $result = $wpdb->update($wpdb->prefix.'my_address',[
                'fullname' => trim($_POST['fullname']),
                'telephone' => $_POST['telephone'],
                'country' => $_POST['country'],
                'province' => $_POST['province'],
                'city' => $_POST['city'],
                'area' => $_POST['area'],
                'address' => $_POST['address'],
                'is_default' => isset($_POST['is_default']) ? 1 : 0,
            ],array('id'=>$_POST['id'],'user_id'=>$current_user->ID));
        }
        //var_dump($a.'---'.$result);die;
        if($a && $result){
            $wpdb->query('COMMIT');
            $url = home_url('account/address');
            if(!empty($match_id)) $url .= '/match_id/'.$match_id;
            $data['info'] = __('保存成功', 'nlyd-student');
            $data['url'] = $url;
            wp_send_json_success($data);
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>__('保存失败', 'nlyd-student')));
        }

    }

    /**
     * 获取教练列表
     */
    public function get_coach_lists($category_id='',$user_id='',$json=true){

        global $wpdb,$current_user;

        $page = isset($_POST['page'])?$_POST['page']:1;
        $searchStr = isset($_POST['s'])?trim($_POST['s']):'';
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        if(isset($_POST['category_id'])) $category_id = $_POST['category_id'];
        $wap = array();
        if(!empty($category_id)){
            $wap[] = " a.category_id = {$category_id} ";
        }else{
            $category = $this->get_coach_category(false);
            $wap[] = " a.category_id = {$category[0]['ID']} ";
            $category_id = $category[0]['ID'];
        }
        $cateArr = getCategory();
        $cateArr = array_column($cateArr, NULL, 'ID');
        switch ($cateArr[$category_id]['alis']){
            case 'memory':
                $coach_level = 'memory_level';
                break;
            case 'reading':
                $coach_level = 'read_level';
                break;
            case 'arithmetic':
                $coach_level = 'compute_level';
                break;
            default :
                wp_send_json_error(array('info'=>__('暂无列表信息', 'nlyd-student')));
        }
        $searchJoin = '';
        $searchWhere = '';
        if($searchStr != ''){
            $searchJoin = " LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=a.coach_id AND um.meta_key='user_real_name'";
            $searchWhere = " AND um.meta_value LIKE '%{$searchStr}%'";
        }

        if(empty($category_id)){
            $category_id = $category[0]['ID'];
        }
        $where = "(a.read = {$category_id} or a.memory = {$category_id} or a.compute = {$category_id})";
        $sql = "select SQL_CALC_FOUND_ROWS b.display_name,a.coach_id,a.read,a.memory,a.compute,a.{$coach_level} AS level_name
                from {$wpdb->prefix}coach_skill a 
                left join {$wpdb->prefix}users b on a.coach_id = b.ID  
                {$searchJoin}
                where {$where} 
                {$searchWhere}
                limit $start,$pageSize
                ";

        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($rows);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无列表信息', 'nlyd-student')));

        if(!empty($rows)){
            foreach ($rows as $k=>$val){
                //获取教练信息
                $sql1 = "select meta_key,meta_value from {$wpdb->prefix}usermeta where meta_key in('user_ID','user_images_color','user_gender','user_coach_level','user_real_name') and user_id = {$val['coach_id']} ";
                $meta = $wpdb->get_results($sql1,ARRAY_A);
                //print_r($sql1);
                //print_r($meta);
                if(!empty($meta)){
                    $user_meta = array_column($meta,'meta_value','meta_key');
                    //print_r($user_meta);
                }
                $rows[$k]['display_name'] = !empty($user_meta['user_real_name']) ? unserialize($user_meta['user_real_name'])['real_name'] : '';
                $rows[$k]['user_gender'] = !empty($user_meta['user_gender']) ? $user_meta['user_gender'] : '-';
                $rows[$k]['user_ID'] = !empty($user_meta['user_ID']) ? $user_meta['user_ID'] : '-';
                $rows[$k]['user_head'] = !empty($user_meta['user_images_color']) ? unserialize($user_meta['user_images_color'])[0] : student_css_url.'image/nlyd.png';
                $rows[$k]['user_coach_level'] = '教练';
//                $rows[$k]['user_coach_level'] = !empty($val['level_name']) ? $val['level_name'] : '高级教练';

                //判断是否为我的教练/主训
                $sql2 = "select apply_status from {$wpdb->prefix}my_coach where user_id = {$current_user->ID} and coach_id = {$val['coach_id']} and category_id = {$category_id}";
                //print_r($sql2);
                $my_coach = $wpdb->get_row($sql2,ARRAY_A);
                // print_r($my_coach);

                $rows[$k]['my_coach'] = 'n';
//                $rows[$k]['my_major_coach'] = 'n';

                $rows[$k]['category_id'] = $category_id;
//                $rows[$k]['apply_status'] = $my_coach['apply_status'];
                $rows[$k]['coach_url'] = home_url('/teams/coachDetail/coach_id/'.$val['coach_id']);
                if(!empty($my_coach)){
                    if($my_coach['apply_status'] == 2){
                        $rows[$k]['my_coach'] = 'y';
//                        $rows[$k]['my_major_coach'] = $my_coach['major'] == 1 ? 'y' : 'n';
                    }
                    $rows[$k]['apply_status'] = $my_coach['apply_status'];
                }else{
                    $rows[$k]['apply_status'] = 0;
                }
                //每种分类对应的状态
//                $categoryArr = ['read', 'memory', 'compute'];
//                foreach ($categoryArr as $cateK => $cate){
////                    $readApply = $wpdb->get_row('SELECT mc.apply_status,p.post_title,mc.major FROM '.$wpdb->prefix.'my_coach AS mc LEFT JOIN '.$wpdb->posts.' AS p ON p.ID=mc.category_id WHERE mc.category_id='.$rows[$k][$cate].' AND mc.user_id='.$current_user->ID.' AND coach_id='.$val['coach_id']);
////                      $readApply = $wpdb->get_row('SELECT post_title FROM '.$wpdb->prefix.'posts WHERE ID='.$val[$cate]);
//                    switch ($cate){
//                        case 'read':
//                            $post_title = __('速读类', 'nlyd-student');
//                            break;
//                        case 'memory':
//                            $post_title = __('记忆类', 'nlyd-student');
//                            break;
//                        case 'compute':
//                            $post_title = __('心算类', 'nlyd-student');
//                            break;
//                    }
//                    $rows[$k]['category'][$cateK]['name'] = $cate;
//                    $rows[$k]['category'][$cateK]['post_title'] = $post_title;
//                    $rows[$k]['category'][$cateK]['category_id'] = $rows[$k][$cate];
//                    $rows[$k]['category'][$cateK]['is_current'] = 'false';//此教练是否在当前分类
//                    $rows[$k]['category'][$cateK]['is_apply'] = 'false'; //是否申请中
//                    $rows[$k]['category'][$cateK]['is_my_coach'] = 'false'; //是否已通过
////                    $rows[$k]['category'][$cateK]['is_my_major'] = 'false'; //是否是主训
//                    $rows[$k]['category'][$cateK]['is_relieve'] = 'false'; //是否已解除
//                    $rows[$k]['category'][$cateK]['is_refuse'] = 'false';//是否已拒绝
//                    if($rows[$k][$cate] != 0 && $rows[$k][$cate] != null){
//                        $rows[$k]['category'][$cateK]['is_current'] = 'true';//此教练是否在当前分类
//                        $coachStudent = $wpdb->get_row('SELECT apply_status,major FROM '.$wpdb->prefix.'my_coach WHERE category_id='.$rows[$k][$cate].' AND user_id='.$current_user->ID.' AND coach_id='.$val['coach_id']);
//                        if($coachStudent){
//                            switch ($coachStudent->apply_status){
//                                case 1://申请中
//                                    $rows[$k]['category'][$cateK]['is_apply'] = 'true';
//                                    break;
//                                case 2://已通过
//                                    $rows[$k]['category'][$cateK]['is_my_coach'] = 'true';
////                                    $rows[$k]['category'][$cateK]['is_my_major'] = $coachStudent->major == 1 ? 'true' : 'false';
//                                    break;
//                                case 3://已解除
//                                    $rows[$k]['category'][$cateK]['is_relieve'] = 'true';
//                                    break;
//                                case -1://已拒绝
//                                    $rows[$k]['category'][$cateK]['is_refuse'] = 'true';
//                                    break;
//                            }
//                        }
//                    }
//                }
            }
        }
//        echo '<pre />';
//        print_r($rows);die;
        if($json){
            wp_send_json_success(array('info'=>$rows));
        }else{

            return $rows;
        }
    }

    /**
     * 获取教练类别
     */
    public function get_coach_category($json=true){

        global $wpdb;
        $post_id = $wpdb->get_var("select post_id from {$wpdb->prefix}postmeta where meta_key = 'project_alias' and meta_value = 'mental_world_cup'");

        $sql = "select ID,post_title 
                from {$wpdb->prefix}posts 
                where post_parent = {$post_id} and post_status = 'publish'
                order by menu_order asc
                " ;

        $rows = $wpdb->get_results($sql,ARRAY_A);
        if($json){
            wp_send_json_success(array('info'=>$rows));
        }else{

            return $rows;
        }
    }

    /**
     * 申请当我教练
     */
    public function set_coach(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_set_coach_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        global $wpdb,$current_user;
        //判断是否登录
        if($current_user->ID < 1) wp_send_json_error(array('info'=>__('未登录', 'nlyd-student')));

        if(empty($_POST['category_id']) || empty($_POST['coach_id'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));
        //不允许申请自己为教练
        if($_POST['coach_id'] == $current_user->ID) wp_send_json_error(array('info'=>__('不能申请自己为教练', 'nlyd-student')));
        //是否同时设置为主训教练
//        $major = intval($_POST['major']) == 1 ? 1 : 0;
//        var_dump($_POST['category_id']);die;
        //查询以前是否进行过申请
        $row = $wpdb->get_row("select * from {$wpdb->prefix}my_coach where user_id = {$current_user->ID} and category_id = {$_POST['category_id']} ",ARRAY_A);
        if(!empty($row)){
            if($row['apply_status'] == 1) wp_send_json_error(array('info'=>__('此类下已有申请,等待审核', 'nlyd-student')));
            if($row['apply_status'] == 2) wp_send_json_error(array('info'=>__('此类下已有教练,请先解除', 'nlyd-student')));
            $result = $wpdb->update($wpdb->prefix.'my_coach',array('apply_status'=>1),array('id'=>$row['id']));
        }else{
            $data = array('category_id'=>$_POST['category_id'],'coach_id'=>$_POST['coach_id'],'user_id'=>$current_user->ID,'apply_status'=>1);
            $result = $wpdb->insert($wpdb->prefix.'my_coach',$data);
        }




        if($result){
            wp_send_json_success(array('info'=>__('申请成功,请等待教练同意', 'nlyd-student')));
            /***********发送短信或邮件通知教练*************/
            //获取教练信息
            $coach = $wpdb->get_row('SELECT user_mobile,display_name,ID AS uid,user_email FROM '.$wpdb->users.' WHERE ID='.$_POST['coach_id'], ARRAY_A);

            if($coach){
                $userContact = getMobileOrEmailAndRealname($coach['uid'], $coach['user_mobile'], $coach['user_email']);
                if($userContact){
                    $post_title = $wpdb->get_var('SELECT post_title FROM '.$wpdb->posts.' WHERE ID='.$_POST['category_id']);
                    $userID = get_user_meta($current_user->ID, 'user_ID')[0];
                    if($userContact['type'] == 'mobile'){
                        $ali = new AliSms();
                        $result = $ali->sendSms($userContact['contact'], 13, array('coach'=>$userContact['real_name'], 'user' => $userID ,'cate' => $post_title));
                    }else{
                        $result = send_mail($userContact['contact'], 13, ['coach' => $userContact['real_name'], 'userID' => $userID, 'cate' => $post_title]);
                    }
                }
            }

//            $post_title = $wpdb->get_var('SELECT post_title FROM '.$wpdb->posts.' WHERE ID='.$_POST['category_id']);
//            $userID = get_user_meta($current_user->ID, '', true)['user_ID'][0];
//            $ali = new AliSms();
//            $result = $ali->sendSms($coach['user_mobile'], 13, array('coach'=>str_replace(', ', '', $coach['display_name']), 'user' => $userID ,'cate' => $post_title));
            /******************end*******************/
        }
        wp_send_json_error(array('info'=>__('申请失败', 'nlyd-student')));
    }


    /**
     *设置/取消主训教练
     */
    public function set_major_coach(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_set_major_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/
        if( empty($_POST['coach_id']) ||  empty($_POST['category_id'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));

        global $wpdb,$current_user;
        //判断是否登录
        if($current_user->ID < 1) wp_send_json_error(array('info'=>__('未登录', 'nlyd-student')));

        //获取教练信息
        $row = $wpdb->get_row("select id,user_id,category_id,apply_status,major from {$wpdb->prefix}my_coach where coach_id = {$_POST['coach_id']} and user_id = $current_user->ID and category_id = {$_POST['category_id']} and apply_status=2",ARRAY_A);

        if(empty($row)) wp_send_json_error(array('info'=>__('数据错误', 'nlyd-student')));
        if($row['apply_status'] != 2) wp_send_json_error(array('info'=>__('该教练还不是你的教练', 'nlyd-student')));
        $major = $row['major'] != 1 ? 1 : '';


        //判断是否已存在其它主训教练或正在申请的教练是主训教练,如果有, 更换主训教练
        if($major == 1){
            if($wpdb->get_row('SELECT id,apply_status FROM '.$wpdb->prefix.'my_coach WHERE user_id='.$current_user->ID.' AND category_id='.$_POST['category_id'].' AND major=1 AND apply_status=2')){
                //已有主训教练
                wp_send_json_error(['info' => 100]);
            }
        }
        if($major == 1){
            $a = $wpdb->update($wpdb->prefix.'my_coach',array('major'=>0),array('category_id'=>$_POST['category_id'],'user_id'=>$current_user->ID));
        }
        $b = $wpdb->update($wpdb->prefix.'my_coach',array('major'=>$major),array('id'=>$row['id'],'user_id'=>$current_user->ID));
        if($b){

            if(!empty($_POST['match_id'])){
                $url = home_url('matchs/confirm/match_id/'.$_POST['match_id']);
            }elseif (!empty($_POST['grad_id'])){
                $url = home_url('gradings/confirm/grad_id/'.$_POST['grad_id']);
            }
            else{
                $url = '';
            }
            wp_send_json_success(array('info'=>__('操作成功', 'nlyd-student'),'url'=>$url));
        }else{

            wp_send_json_error(array('info'=>__('操作失败', 'nlyd-student')));
        }
    }



    /**
     * 报名参赛
     */
    public function entry_match(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_entry_match_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        if(empty($_POST['match_id'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));

        global $wpdb,$current_user;
        $sql = "select id from {$wpdb->prefix}order where user_id = {$current_user} and match_id = {$_POST['match_id']}";
        $row = $wpdb->get_row($sql);
        if(!empty($row)) wp_send_json_error(array('info'=>__('你已报名该比赛,禁止重复报名', 'nlyd-student')));
        wp_send_json_success(array('info'=>home_url('matchs/confirm/match_id/'.$_POST['match_id'])));
    }


    /**
     * 比赛详情页报名选手列表获取
     */
    public function get_entry_list(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_entry_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        //获取报名选手列表
        global $wpdb,$current_user;

        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        $order_type = isset($_POST['order_type']) ? $_POST['order_type'] : 1;
        $sql2 = "select SQL_CALC_FOUND_ROWS a.id,a.user_id,a.created_time 
                  from {$wpdb->prefix}order a
                  right join {$wpdb->prefix}users b on a.user_id = b.ID
                  where a.match_id = {$_POST['match_id']} and (a.pay_status=2 or a.pay_status=3 or a.pay_status=4) and order_type = {$order_type}
                  order by a.id desc limit {$start},{$pageSize} ";
        $orders = $wpdb->get_results($sql2,ARRAY_A);
        /*if($current_user->ID == 66){
            print_r($sql2);
             print_r($orders);
        }*/
        //print_r($orders);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        if(empty($orders)) wp_send_json_error(array('info'=>__('暂无选手报名', 'nlyd-student')));
        foreach ($orders as $k => $v){
            $user = get_user_meta($v['user_id']);
            $orders[$k]['user_gender'] = $user['user_gender'][0] ? __($user['user_gender'][0], 'nlyd-student') : '--' ;
            $orders[$k]['user_head'] = isset($user['user_head']) ? $user['user_head'][0] : student_css_url.'image/nlyd.png';
            if(!empty($user['user_real_name'])){
                $user_real = unserialize($user['user_real_name'][0]);
                $orders[$k]['real_age'] = $user_real['real_age'];
                $orders[$k]['nickname'] = $user_real['real_name'];
            }else{
                $orders[$k]['real_age'] = '--';
                $orders[$k]['nickname'] = $user['nickname'][0];
            }
            $orders[$k]['created_time'] = date_i18n('Ymd',strtotime($v['created_time']));

            $user_nationality_pic = $user['user_nationality_pic'][0] ? $user['user_nationality_pic'][0] : 'cn' ;
            $str = file_get_contents(leo_student_path."conf/nationality_array.json");
            $contents = json_decode($str,true);
            $orders[$k]['nationality_short'] = $contents[$user_nationality_pic]['short'];
            $orders[$k]['nationality'] = $user_nationality_pic;

        }

        //print_r($orders);
        wp_send_json_success(array('info'=>$orders));
    }

    /**
     * 我的比赛
     */
    public function get_my_match_list(){

        global $wpdb,$current_user;

        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        if($_POST['type'] == 2){
            $sql_ = "select SQL_CALC_FOUND_ROWS a.ID,a.post_title,a.post_content,b.start_time as match_start_time,b.grading_notice_url as match_notice_url,
                    b.address as match_address,b.cost as match_cost,b.start_time,b.entry_end_time,b.status as match_status,c.user_id,
                    case b.status 
                    when -3 then '已结束' 
                    when -2 then '等待开赛' 
                    when -1 then '未开始' 
                    when 1 then '报名中' 
                    when 2 then '比赛中' 
                    end match_status_cn
                  from {$wpdb->prefix}order c 
                  left join {$wpdb->prefix}posts a on c.match_id = a.ID 
                  left join {$wpdb->prefix}grading_meta b on c.match_id = b.grading_id 
                  where user_id = {$current_user->ID} and (pay_status=2 or pay_status=3 or pay_status=4) and c.order_type = 2 and b.start_time != '' and a.ID > 0
                  order by b.status desc,b.start_time desc limit $start,$pageSize
                  ";
        }else{

            $sql_ = "select SQL_CALC_FOUND_ROWS a.ID,a.post_title,a.post_content,b.match_start_time,b.match_notice_url,
                    b.match_address,b.match_cost,b.entry_end_time,b.match_status,c.user_id,
                    case b.match_status 
                    when -3 then '已结束' 
                    when -2 then '等待开赛' 
                    when -1 then '未开始' 
                    when 1 then '报名中' 
                    when 2 then '比赛中' 
                    end match_status_cn
                  from {$wpdb->prefix}order c 
                  left join {$wpdb->prefix}posts a on c.match_id = a.ID 
                  left join {$wpdb->prefix}match_meta_new b on c.match_id = b.match_id 
                  where user_id = {$current_user->ID} and (pay_status=2 or pay_status=3 or pay_status=4) and c.order_type = 1 and b.match_start_time != '' and a.ID > 0
                  order by b.match_status desc limit $start,$pageSize
                  ";
        }

        //print_r($sql_);
        $rows = $wpdb->get_results( $sql_,ARRAY_A);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无比赛', 'nlyd-student')));

        if(!empty($rows)){
            foreach ($rows as $k => $val){
                //获取报名人数
                $sql_ = "select count(a.id) total 
                      from {$wpdb->prefix}order a 
                      right join {$wpdb->prefix}users b on a.user_id = b.ID
                      where match_id = {$val['ID']} and pay_status in(2,3,4) ";
                $row = $wpdb->get_row($sql_,ARRAY_A);
                $rows[$k]['entry_total'] = !empty($row['total']) ? $row['total'] : 0;
                //前端需要的数组
                $rows[$k]['match_start_time_arr'] = str2arr(time_format(strtotime($val['match_start_time']),'Y-m-d-H-i-s'),'-');
                $rows[$k]['entry_end_time_arr'] = str2arr(time_format(strtotime($val['entry_end_time']),'Y-m-d-H-i-s'),'-');
                $rows[$k]['match_status_cn'] = __($rows[$k]['match_status_cn'], 'nlyd-student');//翻译
                //两个链接
                if($val['match_status'] == 2){
                    //比赛中
                    $url = $_POST['type'] == 2 ? home_url('/gradings/matchWaitting/grad_id/'.$val['ID']) : home_url('/matchs/matchWaitting/match_id/'.$val['ID']);
                    $title = $_POST['type'] == 2 ? '考级' : '比赛';
                    $button_title = __('进入'.$title, 'nlyd-student');
                }else if ($val['match_status'] == 1){
                    //报名中
                    $url = '';
                    $button_title = __('已报名参赛', 'nlyd-student');
                }
                else if ($val['match_status'] == -1){
                    //未开始
                    $url = '';
                }else if($val['match_status'] == -3){
                    //已结束
                    $url = $_POST['type'] == 2 ? home_url('/gradings/record/grad_id/'.$val['ID']) : home_url('matchs/record/match_id/'.$val['ID']);
                    $button_title = __('查看战绩', 'nlyd-student');
                }else{
                    //等待开赛
                    $url = $_POST['type'] == 2 ? home_url('/gradings/matchWaitting/grad_id/'.$val['ID']) : home_url('matchs/matchWaitting/match_id/'.$val['ID']);
                    $title = $_POST['type'] == 2 ? '考级' : '比赛';
                    $button_title = __('等待'.$title, 'nlyd-student');
                }
                $rows[$k]['button_title'] = $button_title;
                $rows[$k]['right_url'] = $url;
                $rows[$k]['left_url'] = $_POST['type'] == 2 ? home_url('/gradings/info/grad_id/'.$val['ID']) : home_url('matchs/info/match_id/'.$val['ID']);
                $rows[$k]['new_time'] = str2arr(get_time('mysql'),'-');
            }
        }

        wp_send_json_success(array('info'=>$rows));
    }

    /**
     * 获取比赛列表
     */
    public function get_match_list(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_match_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        global $wpdb,$current_user;
        $map = array();
        $map[] = " a.post_status = 'publish' ";
        $map[] = " a.post_type = 'match' ";

        //判断往期/近期
        if( isset($_POST['match_type']) && $_POST['match_type'] =='history' ){
            $map[] = " b.match_status = -3 ";     //历史
            $match_type = 'history';
            $order = ' b.match_start_time desc ';

        }elseif (isset($_POST['match_type']) && $_POST['match_type'] =='signUp'){
            $map[] = " b.match_status = 1 ";     //报名中
            $match_type = 'signUp';
            $order = ' b.entry_end_time asc ';
        }
        else{
            $map[] = " (b.match_status = -2  or b.match_status = 2) ";    //比赛
            $match_type = 'recent';
            $order = ' b.match_start_time asc ';
        }

        //获取最新比赛倒计时
        $sql1 = "select match_start_time from {$wpdb->prefix}match_meta_new where match_status = -2 order by match_start_time desc ";

        $row = $wpdb->get_row($sql1);
        if(!empty($row)){
            $start_time = $row->match_start_time;
            $new_match['new_match_arr'] = str2arr(time_format(strtotime($start_time),'Y-m-d-H-i-s'),'-');
            $new_match['match_type'] = $match_type;
        }

        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        $where = join(' and ',$map);

        $sql = "select SQL_CALC_FOUND_ROWS a.ID,a.post_title,
                a.post_content,b.match_notice_url,b.created_id,
                DATE_FORMAT(b.match_start_time,'%Y-%m-%d %H:%i') match_start_time,
                if(b.match_address = '','--',b.match_address) match_address,
                if(d.role_name = '','正式比赛',d.role_name) role_name,
                b.match_cost,b.entry_end_time,b.match_status ,c.user_id
                from {$wpdb->prefix}posts a
                left join {$wpdb->prefix}match_meta_new b on a.ID = b.match_id
                left join {$wpdb->prefix}order c on a.ID = c.match_id and c.user_id = {$current_user->ID} and (c.pay_status=2 or c.pay_status=3 or c.pay_status=4) 
                left join {$wpdb->prefix}zone_match_role d on b.match_scene = d.id
                where {$where} order by {$order} limit $start,$pageSize;
                ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无比赛', 'nlyd-student')));
        foreach ($rows as $k => $val){
            //print_r($val);
            //获取办赛机构
            $zone_meta = $wpdb->get_row("select id,if(zone_match_type=1,'战队精英赛','城市赛') as match_type,zone_city,zone_name from {$wpdb->prefix}zone_meta where user_id = {$val['created_id']}",ARRAY_A);
            if(!empty($zone_meta)){
                $meta = $zone_meta['zone_city'].$zone_meta['match_type'].'组委会';
                //print_r($meta);
            }
            $rows[$k]['zone'] = empty($zone_meta) ? '' : $meta;
            //获取参赛须知
            $rows[$k]['match_notice_url'] = !empty($val['match_notice_url']) ? $val['match_notice_url'] : '';

            //获取报名人数
            $sql_ = "select count(a.id) total 
                      from {$wpdb->prefix}order a 
                      right join {$wpdb->prefix}users b on a.user_id = b.ID
                      where match_id = {$val['ID']} and pay_status in(2,3,4) and order_type=1";

            //print_r($sql_);
            $row = $wpdb->get_row($sql_,ARRAY_A);
            $rows[$k]['entry_total'] = !empty($row['total']) ? $row['total'] : 0;
            //前端需要的数组
            $rows[$k]['match_start_time_arr'] = str2arr(time_format(strtotime($val['match_start_time']),'Y-m-d-H-i-s'),'-');
            $rows[$k]['entry_end_time_arr'] = str2arr(time_format(strtotime($val['entry_end_time']),'Y-m-d-H-i-s'),'-');
            //两个链接
            if($val['match_status'] == 2){
                //比赛中
                $url = home_url('matchs/matchWaitting/match_id/'.$val['ID']);
                $button_title = __('进入比赛', 'nlyd-student');
                $rows[$k]['match_status_cn'] = __('比赛中', 'nlyd-student');
            }
            else if ($val['match_status'] == 1){
                //报名中
                $url = home_url('matchs/confirm/match_id/'.$val['ID']);
                $button_title = __('参赛报名', 'nlyd-student');
                $rows[$k]['match_status_cn'] = __('报名中', 'nlyd-student');
            }
            else if ($val['match_status'] == -1){
                //未开始
                $url = '';
                $rows[$k]['match_status_cn'] = __('未开始', 'nlyd-student');
            }
            else if($val['match_status'] == -3){
                //已结束
                $url = '';
                $rows[$k]['match_status_cn'] = __('已结束', 'nlyd-student');
            }
            else{
                //等待开赛
                $url = home_url('matchs/matchWaitting/match_id/'.$val['ID']);
                $button_title = __('等待开赛', 'nlyd-student');
                $rows[$k]['match_status_cn'] = __('等待开赛', 'nlyd-student');
            }
            $rows[$k]['button_title'] = $button_title;
            $rows[$k]['right_url'] = $url;
            $rows[$k]['left_url'] = home_url('matchs/info/match_id/'.$val['ID']);

            if($_POST['match_type'] =='history'){
                $button_title = __('查看排名', 'nlyd-student');
                $rows[$k]['right_url'] = home_url('matchs/record/match_id/'.$val['ID']);
            }
        }

        wp_send_json_success(array('info'=>$rows));
    }

    /**
     * 设置账户资料
     */
    public function student_saveInfo(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_saveInfo_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        if(empty($_POST['meta_key'])) wp_send_json_error(array('info'=>__('meta_key不能为空', 'nlyd-student')));
        //if(empty($_POST['meta_val'])) wp_send_json_error(array('info'=>'值不能为空'));

        global $current_user,$wpdb;

        if(in_array($_POST['meta_key'],array('user_pass','user_mobile','user_email','user_nicename'))){
            if($_POST['meta_key'] == 'user_mobile' ||  $_POST['meta_key'] == 'user_email'){

                $sql = "SELECT * FROM $wpdb->users WHERE user_login = '{$_POST['user_login']}' or user_email = '{$_POST['user_login']}' or user_mobile = '{$_POST['user_login']}'";
                $user = $wpdb->get_row($sql,ARRAY_A);
            }


            switch ($_POST['meta_key']){
                case 'user_mobile':

                    //手机验证
                    $this->get_sms_code($_POST['meta_val'],15,true,$_POST['verify_code']);
                    if($user){
                        if($current_user->data->ID != $user['ID']){
                            wp_send_json_error(array('info'=>__('该手机号已存在', 'nlyd-student')));
                        }
                    }

                    break;
                case 'user_email':

                    //邮箱验证
                    $this->get_smtp_code($_POST['meta_val'],15,true,$_POST['verify_code']);
                    if($user){
                        if($current_user->data->ID != $user['ID']){
                            wp_send_json_error(array('info'=>__('该邮箱号已存在', 'nlyd-student')));
                        }
                    }
                    break;
                case 'user_pass':

                    if(empty($_POST['password'])) wp_send_json_error(array('info'=>__('新密码不能为空', 'nlyd-student')));
                    if(!empty($_POST['password']) && $_POST['confirm_password'] !== $_POST['password']) wp_send_json_error(array('info'=>__('两次密码不一致', 'nlyd-student')));
                    if(!empty($_POST['old_pass'])){

                        $check = wp_check_password($_POST['old_pass'],$current_user->data->user_pass);
                        if(!$check) wp_send_json_error(array('info'=>__('密码错误', 'nlyd-student')));

                        $check_ = wp_check_password($_POST['password'],$current_user->data->user_pass);
                        if($check_) wp_send_json_error(array('info'=>__('新密码不能和老密码一致', 'nlyd-student')));

                    }
                    if(!preg_match('/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{6,16}$/',$_POST['password'])){
                        wp_send_json_error(array('info'=>__('6-16位字母+数字组合', 'nlyd-student')));
                    }

                    $_POST['meta_val'] = wp_hash_password( $_POST['password'] );

                    break;
                case 'user_nicename':

                    if(mb_strlen($_POST['meta_val'],'utf-8') > 24) wp_send_json_error(array('info'=>__('昵称不能超过24个字符', 'nlyd-student')));
                    update_user_meta($current_user->ID,'nickname',$_POST['meta_val']) ;
                    $user_nicename_update = true;
                    break;
                default:

                    //wp_send_json_error(array('info'=>'非法操作'));
                    break;
            }

            $resul = $wpdb->update($wpdb->users,array($_POST['meta_key']=>$_POST['meta_val']),array('ID'=>$current_user->ID)) || $user_nicename_update;

        }
        else{

            switch ($_POST['meta_key']){
                case 'user_head':

                    //print_r($_FILES['meta_val']['tmp_name']);die;
                    $upload_dir = wp_upload_dir();
                    $dir = '/user/'.$current_user->ID.'/';
                    $file = $this->saveIosFile($_FILES['meta_val']['tmp_name'],$upload_dir['basedir'].$dir);

                    if($file){
                        $_POST['meta_val'] = $upload_dir['baseurl'].$dir.$file;
                        $success['head_url'] = $upload_dir['baseurl'].$dir.$file;
                    }else{
                        wp_send_json_error(array('info'=>__('上传失败', 'nlyd-student')));
                    }
                    break;
                case 'user_real_name':

                    //验证格式
                    if(empty($_POST['nationality']) || empty($_POST['nationality_pic'])) wp_send_json_error(array('info'=>__('国籍必选', 'nlyd-student')));
                    if(empty($_POST['meta_val']['real_name'])) wp_send_json_error(array('info'=>__('真实姓名不能为空', 'nlyd-student')));
                    if(empty($_POST['meta_val']['real_ID'])) wp_send_json_error(array('info'=>__('证件号不能为空', 'nlyd-student')));
                    $user_id = $wpdb->get_var("select user_id from {$wpdb->prefix}usermeta where meta_value like '%{$_POST['meta_val']['real_ID']}%'");
                    if($user_id > 0 && $user_id != $current_user->ID){
                        wp_send_json_error(array('info'=>__('该证件号已被使用', 'nlyd-student')));
                    }

                    update_user_meta($current_user->ID,'user_nationality',$_POST['nationality']);
                    update_user_meta($current_user->ID,'user_nationality_pic',$_POST['nationality_pic']);
                    update_user_meta($current_user->ID,'user_nationality_short',$_POST['nationality_short']);

                    if($_POST['nationality'] != '中华人民共和国'){
                        if(empty($_POST['birthday']))wp_send_json_error(array('info'=>__('生日必选', 'nlyd-student')));
                        update_user_meta($current_user->ID,'user_birthday',$_POST['birthday']);

                        $birthday_year= (int) substr($_POST['birthday'],0,4);
                        $new_year = (int) date_i18n('Y',get_time());
                        $age = $new_year - $birthday_year;

                        $_POST['meta_val']['real_age'] = $age;
                    }

                    if(empty($_POST['meta_val']['real_type'])) wp_send_json_error(array('info'=>__('请选择证件类型', 'nlyd-student')));
                    //if(empty($_POST['meta_val']['real_name'])) wp_send_json_error(array('info'=>__('真实姓名不能为空', 'nlyd-student')));
                    //if(empty($_POST['meta_val']['real_ID'])) wp_send_json_error(array('info'=>__('证件号不能玩为空', 'nlyd-student')));
                    if(!reg_match($_POST['meta_val']['real_ID'],$_POST['meta_val']['real_type'])) wp_send_json_error(array('info'=>__('证件号格式不正确', 'nlyd-student')));
                    //if(!preg_match("/^[\x{4e00}-\x{9fa5}]+[·•]?[\x{4e00}-\x{9fa5}]+$/u", $_POST['meta_val']['real_name'])) wp_send_json_error(array('info'=>'名字格式不正确,请输入你的中文名'));

                    //判断是否报名
                    if(isset($_POST['type']) && $_POST['type'] == 'sign'){

                        $b = $wpdb->insert(
                            $wpdb->prefix.'match_sign',
                            array(
                                'user_id'=>$current_user->ID,
                                'match_id'=>$_POST['sign_match'],
                                'seat_number'=>$_POST['order_index'],
                                'created_time' => get_time('mysql')
                            )
                        );
                    }

                    if(!empty($_POST['user_gender'])){
                        update_user_meta($current_user->ID,'user_gender',$_POST['user_gender']) && $user_gender_update = true;
                        unset($_POST['user_gender']);
                    }
                    if(!empty($_POST['meta_val']['real_age'])){
                        update_user_meta($current_user->ID,'user_age',$_POST['meta_val']['real_age']) && $user_age_update = true;
                    }
                    //var_dump($_POST['meta_val']);die;
//                    $_POST['user_address'] = array(nationality
//                        'province'=>'四川省',
//                        'city'=>'成都市',
//                        'area'=>'高新区',
//                    );
                    if(!empty($_POST['user_address'])){
                        update_user_meta($current_user->ID,'user_address',$_POST['user_address']) && $user_address_update = true;
                        unset($_POST['user_address']);
                    }

                    //print_r($_POST);die;

                    //寸照
                    if(!empty($_POST['images_color'])){

                        $upload_dir = wp_upload_dir();
                        $dir = '/color/'.$current_user->ID.'/';
                        $num = 0;
                        foreach ($_POST['images_color'] as $va){
                            $file = $this->base64file($va,$upload_dir['basedir'].$dir);
                            if($file){
                                $_POST['user_images_color'][] = $upload_dir['baseurl'].$dir.$file;
                                ++$num;
                            }
                        }
                    }
                    update_user_meta($current_user->ID,'user_images_color',$_POST['user_images_color']);

                    if(!empty($_POST['images'])){
                        //var_dump($_FILES['images']);
                        $upload_dir = wp_upload_dir();
                        $dir = '/user/'.$current_user->ID.'/';
                        $imagePathArr = [];
                        $num = 0;
                        foreach ($_POST['images'] as $upd){
                            $file = $this->base64file($upd,$upload_dir['basedir'].$dir);
                            if($file){
                                $_POST['user_ID_Card'][] = $upload_dir['baseurl'].$dir.$file;
                                ++$num;
                            }
                        }
                        //print_r($_POST['user_ID_Card']);die;
                    }
                    update_user_meta($current_user->ID,'user_ID_Card',$_POST['user_ID_Card']);

                    $user_ID_Card_update = true;

                    break;
                case 'user_sign':
                    if(mb_strlen($_POST['meta_val'],'utf-8') > 40) wp_send_json_error(array('info'=>__('昵称不能超过40个字符', 'nlyd-student')));
                    break;
                default:

                    //$resul = update_user_meta($current_user->ID,$_POST['meta_key'],$_POST['meta_val']);
                    break;
            }

            $resul = update_user_meta($current_user->ID,$_POST['meta_key'],$_POST['meta_val']) || isset($user_gender_update) ? true : false || isset($user_address_update) ? true : false || isset($user_age_update) ? true : false || isset($user_ID_Card_update) ? true : false;

        }
        if($resul){

            if(isset($_POST['type']) && $_POST['type'] == 'sign'){

                if( $b){

                    wp_send_json_success(array('info'=>__('签到成功', 'nlyd-student'),'url'=>home_url('signs/success/id/'.$_POST['sign_match'])));
                }else{

                    wp_send_json_error(array('info'=>__('签到失败', 'nlyd-student').'<br/>'.__('请联系管理员', 'nlyd-student')));
                }
            }

            $url = !empty($_POST['match_id']) ? home_url('/matchs/confirm/match_id/'.$_POST['match_id']) : home_url('account/info');
            $url = !empty($_POST['grad_id']) ? home_url('/gradings/confirm/grad_id/'.$_POST['grad_id']) : home_url('account/info');
            $success['info'] = __('保存成功', 'nlyd-student');
            $success['url'] = $url;
            wp_send_json_success($success);
        }else{
            wp_send_json_success(array('info'=>__('设置失败', 'nlyd-student')));
        }
    }


    /**
     * 安全中心修改
     */
    public function secure_save(){

        global $wpdb,$current_user;

        switch ($_POST['save_type']){
            case 'pass':
                if(wp_check_password($_POST['old_pass'],$current_user->user_pass)){

                    if($_POST['new_pass'] != $_POST['confirm_pass'] ){
                        wp_send_json_error(array('info'=>__('新密码两次输入不一致', 'nlyd-student')));
                    }

                    if(wp_check_password($_POST['confirm_pass'],$current_user->user_pass)){
                        wp_send_json_error(array('info'=>__('新旧密码不能一致', 'nlyd-student')));
                    }

                    $new_pass = wp_hash_password( $_POST['confirm_pass'] );

                    $result = $wpdb->update($wpdb->prefix.'users',array('user_pass'=>$new_pass),array('ID'=>$current_user->ID));

                }else{
                    wp_send_json_error(array('info'=>__('老密码不正确', 'nlyd-student')));
                }
                break;
            case 'mobile':

                if(!reg_match($_POST['user_mobile'],'m')) wp_send_json_error(array('info'=>__('手机格式有误', 'nlyd-student')));

                if($_POST['step'] == 'one'){
                    $this->get_sms_code($_POST['user_mobile'],21,true,$_POST['verify_code']);
                    unset($_SESSION['sms']);
                    wp_send_json_success(array('info'=>__('验证成功', 'nlyd-student'),'url'=>home_url('/safety/safetySetting/type/mobile/confirm/1')));
                }else{

                    $this->get_sms_code($_POST['user_mobile'],16,true,$_POST['verify_code']);
                    $user  = get_user_by( 'mobile', $_POST['user_mobile'] );
                    if(!empty($user)) wp_send_json_error(array('info'=>__('该手机号已被占用', 'nlyd-student')));
                    $result = $wpdb->update($wpdb->prefix.'users',array('user_mobile'=>$_POST['user_mobile']),array('ID'=>$current_user->ID));
                }

                break;
            case 'email':
                if(!reg_match($_POST['user_email'],'e')) wp_send_json_error(array('info'=>__('邮箱格式有误', 'nlyd-student')));
                $this->get_smtp_code($_POST['user_email'],16,true,$_POST['verify_code']);
                $user  = get_user_by( 'email', $_POST['user_email'] );
                if(!empty($user)) wp_send_json_error(array('info'=>__('该邮箱号已被占用', 'nlyd-student')));
                $result = $wpdb->update($wpdb->prefix.'users',array('user_email'=>$_POST['user_email']),array('ID'=>$current_user->ID));
                break;
            case 'weChat':
                $result = update_user_meta($current_user->ID,'user_weChat',$_POST['user_weChat']);
                break;
            case 'qq':
                $result = update_user_meta($current_user->ID,'user_qq',$_POST['user_qq']);
                break;
            default:
                wp_send_json_error(array('info'=>__('未知的操作请求', 'nlyd-student')));
                break;
        }

        if($result){
            wp_send_json_success(array('info'=>__('更新成功', 'nlyd-student'),'url'=>home_url('account/secure/')));
        }else{
            wp_send_json_error(array('info'=>__('更新失败', 'nlyd-student')));
        }
    }


    /**
     * 解绑微信/QQ
     */
    public function untie(){
        global $wpdb,$current_user;
        switch ($_POST['type']){
            case 'weChat':
                $result = $wpdb->update($wpdb->prefix.'users',array('weChat_openid'=>''),array('ID'=>$current_user->ID));
                break;
            case 'qq':
                $result = $wpdb->update($wpdb->prefix.'users',array('qq_union_id'=>''),array('ID'=>$current_user->ID));
                break;
            default:
                wp_send_json_error(array('info'=>__('未知的操作请求', 'nlyd-student')));
                break;
        }
        if($result){
            wp_send_json_success(array('info'=>__('解绑成功', 'nlyd-student'),'url'=>home_url('account/secure/')));
        }else{
            wp_send_json_error(array('info'=>__('解绑失败', 'nlyd-student')));
        }
    }

    /**
     * 通过身份证自动计算年龄
     */
    public function reckon_age(){
        
        if(empty($_POST['real_ID'])) wp_send_json_error(array('info'=>__('证件号不能为空', 'nlyd-student')));
        if(strlen($_POST['real_ID']) == 18){
            if(!reg_match($_POST['real_ID'],'sf')) wp_send_json_error(array('info'=>__('证件号格式不正确', 'nlyd-student')));
            /*$sub_str = substr($_POST['real_ID'],6,4);
            $now = date_i18n("Y",get_time());
            $age = $now-$sub_str;
            $age = $age >0 ? $age : 1;*/
            $age = birthday($_POST['real_ID']);
            
            if($age == -1){
                wp_send_json_error(array('info'=>__('年龄不能低于1岁,请确认身份证信息', 'nlyd-student')));
            }
            if($age == -2){
                wp_send_json_error(array('info'=>__('年龄超过150岁,请确认身份证信息', 'nlyd-student')));
            }
            wp_send_json_success(array('info'=>$age));
        }else{
            wp_send_json_success(array('info'=>1));
        }
        
    }

    /**
     * 修改/设置密码
     */
    public function student_savePass(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_savePass_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        if(empty($_POST['password'])) wp_send_json_error(array('info'=>__('新密码不能为空', 'nlyd-student')));
        if(!empty($_POST['password']) && $_POST['confirm_password'] !== $_POST['password']) wp_send_json_error(array('info'=>__('两次密码不一致', 'nlyd-student')));
        if(!empty($_POST['old_pass'])){
            global $current_user;
            $check = wp_check_password($_POST['old_pass'],$current_user->data->user_pass);
            if(!$check) wp_send_json_error(array('info'=>__('密码错误', 'nlyd-student')));

            $check_ = wp_check_password($_POST['password'],$current_user->data->user_pass);
            if($check_) wp_send_json_error(array('info'=>__('新密码不能和老密码一致', 'nlyd-student')));

        }
        if(!preg_match('/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{6,16}$/',$_POST['password'])){
            wp_send_json_error(array('info'=>__('6-16位字母+数字组合', 'nlyd-student')));
        }

        global $wpdb;
        $result = $wpdb->update($wpdb->users,array('user_pass'=>wp_hash_password( $_POST['password'] )),array('ID'=>$current_user->ID));
        if($result){

            wp_send_json_success(array('info'=>__('重置成功', 'nlyd-student'),'url'=>home_url('/logins')));
        }else{
            wp_send_json_error(array('info'=>__('重置失败', 'nlyd-student')));
        }

    }

    /**
     * 获取短信验证码
     * 单对象发送验证码
     * @param $mobile 接收手机号
     * @param $template 短信模版
     * @param $session_verify 是否session验证
     */
    public function get_sms_code($mobile='',$template='',$session_verify = false,$send_code =''){

        if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($mobile)){
            $mobile = $_POST['mobile'] ? $_POST['mobile'] : $_POST['user_login'];
            $template = $_POST['template'];
        }
        if(empty($mobile)) wp_send_json_error(array('info'=>__('手机号不能为空', 'nlyd-student')));
        if(!reg_match($mobile,'m')) wp_send_json_error(array('info'=>__('手机格式有误', 'nlyd-student')));

        if($session_verify){

            if(isset($_SESSION['sms']) && !empty($_SESSION['sms'])){
                $sms = $_SESSION['sms'];
                if($sms['mobile'] != $mobile) wp_send_json_error(array('info'=>__('当前手机号与获取验证手机号不一致', 'nlyd-student')));
                if($sms['template'] != md5($template)){
                    unset($_SESSION['sms']);
                    wp_send_json_error(array('info'=>__('请先获取验证码', 'nlyd-student')));
                }
                if(get_time() > $sms['time']){
                    unset($_SESSION['sms']);
                    wp_send_json_error(array('info'=>__('验证码已过期,请重新获取', 'nlyd-student')));
                }
                if(!empty($send_code)){

                    if(md5($send_code) != $sms['code']) wp_send_json_error(array('info'=>__('验证码错误', 'nlyd-student')));
                }else{
                    wp_send_json_error(array('info'=>__('验证码不能为空', 'nlyd-student')));
                }
            }else{
                wp_send_json_error(array('info'=>__('请先获取验证码', 'nlyd-student')));
            }

            return ;
        }

        //如果不是注册操作,判断是否为平台用户
        if(!in_array($template,array(16,17,19,21))){
            $user  = get_user_by( 'mobile', $mobile );

            if(empty($user)) wp_send_json_error(array('info'=>__('您不是平台用户,请先进行注册', 'nlyd-student')));
        }

        $code = rand(1000, 9999);

        $ali = new AliSms();
        $result = $ali->sendSms($mobile,$template,array('code'=>$code));
        if($result){

            $_SESSION['sms'] = array(
                'mobile' => $mobile,
                'code' => md5($code),
                'template' => md5($template),
                'time' => get_time()+300,
            );
            wp_send_json_success(array('info'=>__('获取成功', 'nlyd-student')));
        }else{
            wp_send_json_error(array('info'=>__('获取失败,请稍后重试', 'nlyd-student')));
        }
    }

    /**
     * 获取邮件验证码
     * 单对象发送验证码
     * @param $emali 接收邮箱号
     * @param $template 邮箱模版
     * @param $session_verify 是否session验证
     */
    public function get_smtp_code($email='',$template='',$session_verify = false,$send_code =''){

        if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($email) ){
            $email = $_POST['email'] ? $_POST['email'] : $_POST['user_login'];
            $template = $_POST['template'];
        }
        if(empty($email)) wp_send_json_error(array('info'=>__('邮箱号不能为空', 'nlyd-student')));
        if(!reg_match($email,'e')) wp_send_json_error(array('info'=>__('邮箱格式有误', 'nlyd-student')));

        if($session_verify){

            if(isset($_SESSION['smtp']) && !empty($_SESSION['smtp'])){
                $smtp = $_SESSION['smtp'];
                if($smtp['email'] != $email) wp_send_json_error(array('info'=>__('当前邮箱号与获取验证邮箱号不一致', 'nlyd-student')));
                if($smtp['template'] != md5($template)){
                    unset($_SESSION['smtp']);
                    wp_send_json_error(array('info'=>__('请先获取验证码', 'nlyd-student')));
                }
                if(get_time() > $smtp['time']){
                    unset($_SESSION['smtp']);
                    wp_send_json_error(array('info'=>__('验证码已过期,请重新获取', 'nlyd-student')));
                }
                if(!empty($send_code)){

                    if(md5($send_code) != $smtp['code']) wp_send_json_error(array('info'=>__('验证码错误', 'nlyd-student')));
                }else{
                    wp_send_json_error(array('info'=>__('验证码不能为空', 'nlyd-student')));
                }
            }else{
                wp_send_json_error(array('info'=>__('请先获取验证码', 'nlyd-student')));
            }

            return ;
        }

        //如果不是注册操作,判断是否为平台用户
        if(!in_array($template,array(16,17,19,21))){
            $user  = get_user_by( 'email', $email );
            if(empty($user)) wp_send_json_error(array('info'=>__('您不是平台用户,请先进行注册', 'nlyd-student')));
        }

        $code = rand(1000, 9999);

        $result = send_mail($email,$template,array('code'=>$code));
        if($result){

            $_SESSION['smtp'] = array(
                'email' => $email,
                'code' => md5($code),
                'template' => md5($template),
                'time' => get_time()+300,
            );
            wp_send_json_success(array('info'=>__('获取成功', 'nlyd-student')));
        }else{
            wp_send_json_error(array('info'=>__('获取失败,请稍后重试', 'nlyd-student')));
        }
    }

    /**
     * 学生登录
     */
    public function student_login(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_login_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        switch ($_POST['login_type']){
            case 'mobile':

                //短信验证
               // $this->get_sms_code($_POST['user_login'],19,true,$_POST['password']);

                break;
            case 'pass';

                if(empty($_POST['user_login'])) wp_send_json_error(array('info'=>__('用户名不能为空', 'nlyd-student')));
                if(empty($_POST['password'])) wp_send_json_error(array('info'=>__('密码不能为空', 'nlyd-student')));

                break;
        }
        global $wpdb;
        $sql = "SELECT * FROM $wpdb->users WHERE user_login = '{$_POST['user_login']}' or user_email = '{$_POST['user_login']}' or user_mobile = '{$_POST['user_login']}'";
        $user = $wpdb->get_row($sql);

        //判断用户是否存在
        if($user){
            if($_POST['login_type'] == 'pass'){

                $check = wp_check_password($_POST['password'],$user->user_pass);
                if(!$check) wp_send_json_error(array('info'=>__('密码错误', 'nlyd-student')));
            }

            $this->setUserCookie($user->ID);

            //do_action( 'wp_login', $user->user_login, $user );

            //判断是否为机构
            $zone_id = $wpdb->get_var("select id from {$wpdb->prefix}zone_meta where user_id = {$user->ID} ");
            if($zone_id){
                $url = home_url('/zone/');
            }else{
                $url = home_url('account');
            }

            if(isset($_SESSION['redirect_url'])){
                $url = $_SESSION['redirect_url'];
                unset($_SESSION['redirect_url']);
            }


            //添加推广人
            if($_POST['referee_id'] > 0){
                //获取我的推广人上级
                $referee_id = $wpdb->get_var("select referee_id from {$wpdb->prefix}users where ID = {$_POST['referee_id']}");

                if(empty($user->referee_id) && $_POST['referee_id'] != $user->ID && $referee_id != $user->ID){

                    $a = $wpdb->update($wpdb->prefix.'users',array('referee_id'=>$_POST['referee_id'],'referee_time'=>date_i18n('Y-m-d',get_time())),array('ID'=>$user->ID));
                    if($a){
                        $url = home_url('/zone/indexUser/');
                        wp_send_json_success( array('info'=>__('绑定成功', 'nlyd-student'),'url'=>$url));
                    }else{
                        wp_send_json_success( array('info'=>__('绑定失败', 'nlyd-student'),'url'=>$url));
                    }
                }
            }

            wp_send_json_success( array('info'=>__('登录成功', 'nlyd-student'),'url'=>$url));

        }else{

            //注册用户
            if($_POST['login_type'] == 'mobile'){

                if(!reg_match($_POST['user_login'],'m')) wp_send_json_error(array('info'=>__('手机格式不正确', 'nlyd-student')));
                $result = wp_create_user($_POST['user_login'],$_POST['password']);
                //print_r($result);die;
                if($result){
                    //$meta['user_ID'] = 10000000+$user_id;
                    $wpdb->update($wpdb->prefix.'users',array('user_mobile'=>$_POST['user_login']),array('ID'=>$result));
                    update_user_meta($result,'user_ID',10000000+$result);
                    unset($_SESSION['sms']);
                    $this->setUserCookie($result);

                    if(isset($_SESSION['redirect_url'])){
                        $url = $_SESSION['redirect_url'];
                        unset($_SESSION['redirect_url']);
                    }else{
                        $url = home_url('account');
                    }

                    //添加推广人
                    if($_POST['referee_id'] > 0){
                        //获取我的推广人上级
                        $referee_id = $wpdb->get_var("select referee_id from {$wpdb->prefix}users where ID = {$_POST['referee_id']}");
                        if(empty($user->referee_id) && $_POST['referee_id'] != $result && $referee_id != $result){

                            $a=$wpdb->update($wpdb->prefix.'users',array('referee_id'=>$_POST['referee_id'],'referee_time'=>date_i18n('Y-m-d',get_time())),array('ID'=>$result));
                            //var_dump($a);die;
                            if($a){
                                $url = home_url('/zone/indexUser/');
                                wp_send_json_success( array('info'=>__('绑定成功', 'nlyd-student'),'url'=>$url));
                            }else{
                                wp_send_json_success( array('info'=>__('绑定失败', 'nlyd-student'),'url'=>$url));
                            }
                        }
                    }

                    wp_send_json_success( array('info'=>__('登录成功', 'nlyd-student'),'url'=>$url));
                }else{
                    wp_send_json_error(array('info'=>__('登录失败', 'nlyd-student')));
                }
            }
            else{

                wp_send_json_error(array('info'=>__('不存在此用户,请先注册', 'nlyd-student')));
            }

        }
    }

    /**
     *学生注册
     */
    public function student_register(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_register_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        if(empty($_POST['user_login'])) wp_send_json_error(array('info'=>__('账号不能为空', 'nlyd-student')));
        if(empty($_POST['verify_code'])) wp_send_json_error(array('info'=>__('验证码不能为空', 'nlyd-student')));
        if(empty($_POST['password'])) wp_send_json_error(array('info'=>__('密码不能为空', 'nlyd-student')));

        global $wpdb;
        $sql = "SELECT * FROM $wpdb->users WHERE user_login = '{$_POST['user_login']}' or user_email = '{$_POST['user_login']}' or user_mobile = '{$_POST['user_login']}'";
        $user = $wpdb->get_row($sql);
        if($user) wp_send_json_error(array('info'=>__('此用户已存在', 'nlyd-student')));

        if(reg_match($_POST['user_login'],'m')){

            $this->get_sms_code($_POST['user_login'],17,true,$_POST['verify_code']);
            $result = wp_create_user($_POST['user_login'],$_POST['password']);
        }elseif (reg_match($_POST['user_login'],'e')){

            $this->get_smtp_code($_POST['user_login'],17,true,$_POST['verify_code']);
            $result = wp_create_user($_POST['user_login'],$_POST['password'],$_POST['user_login']);
        }

        if($result){
            if(reg_match($_POST['user_login'],'m')){

                $wpdb->update($wpdb->prefix.'users',array('user_mobile'=>$_POST['user_login']),array('ID'=>$result));
            }
            update_user_meta($result,'user_ID',10000000+$result);

            unset($_SESSION['sms']);
            unset($_SESSION['smtp']);
            $this->setUserCookie($result);

            $url = home_url('account');

            //添加推广人
            if($_POST['referee_id'] > 0){
                $a = $wpdb->update($wpdb->prefix.'users',array('referee_id'=>$_POST['referee_id'],'referee_time'=>date_i18n('Y-m-d',get_time())),array('ID'=>$result));
                if($a){
                    $url = home_url('/zone/indexUser/');
                    wp_send_json_success( array('info'=>__('绑定成功', 'nlyd-student'),'url'=>$url));
                }else{
                    wp_send_json_success( array('info'=>__('绑定失败', 'nlyd-student'),'url'=>$url));
                }
            }

            wp_send_json_success(array('info'=>__('注册成功', 'nlyd-student'),'url'=>$url));
        }else{
            wp_send_json_error(array('info'=>__('注册失败', 'nlyd-student')));
        }

    }

    /**
     * 密码重置
     */
    public function student_reset(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_reset_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        if(empty($_POST['user_login'])) wp_send_json_error(array('info'=>__('账号不能为空', 'nlyd-student')));

        global $wpdb;
        $sql = "SELECT * FROM $wpdb->users WHERE user_login = '{$_POST['user_login']}' or user_email = '{$_POST['user_login']}' or user_mobile = '{$_POST['user_login']}'";
        $user = $wpdb->get_row($sql);

        if(!$user) wp_send_json_error(array('info'=>__('不存在此用户,请先注册', 'nlyd-student')));

        if(empty($_POST['password'])) wp_send_json_error(__('密码不能为空', 'nlyd-student'));
        if(!empty($_POST['password']) && $_POST['confirm_password'] !== $_POST['password']) wp_send_json_error(array('info'=>__('两次密码不一致', 'nlyd-student')));

        if(reg_match($_POST['user_login'],'m')){

            $this->get_sms_code($_POST['user_login'],16,true,$_POST['verify_code']);
        }elseif (reg_match($_POST['user_login'],'e')){

            $this->get_smtp_code($_POST['user_login'],16,true,$_POST['verify_code']);
        }

        $result = $wpdb->update($wpdb->users,array('user_pass'=>wp_hash_password( $_POST['password'] )),array('ID'=>$user->ID));
        if($result){
            unset($_SESSION['sms']);
            unset($_SESSION['smtp']);
            wp_send_json_success(array('info'=>__('重置成功', 'nlyd-student'),'url'=>home_url('/logins')));
        }else{
            wp_send_json_error(array('info'=>__('重置失败', 'nlyd-student')));
        }

    }


    /**
     * 非ios关联上传
     * 微信 $img = $WeChat->downloadWeixinFile($val);
     *@param  $filecontent 文件流
     *@param  $path 		文件保存目录
     *
     */
    public function saveFile($filecontent,$path){

        $filename = date('YmdHis').'_'.rand(1000,9999).'.jpg';          //定义图片名字及格式
        $upload_dir = __ROOT_PHATH__."/Uploads/".$path;//保存路径，以时间作目录分层
        $file_dir = "/Uploads/".$path;
        if(!file_exists($upload_dir)){
            mkdir($upload_dir,0755,true);
        }
        $savepath = $upload_dir.'/'.$filename;
        if(file_put_contents($savepath, $filecontent)){//写入文件流生成文件

            //将图片保存到数据库
            M('Picture')->path = $file_dir.'/'.$filename;
            M('Picture')->status=1;
            M('Picture')->create_time=get_time();
            $result=M('Picture')->add();
            if($result){
                return $result;
            }else{
                return false;
            }

            //return $file_dir.'/'.$filename;//返回文件路径
        }
        return false;
    }

    /**
     * ios关联上传
     * 一般文件流上传
     *@param  $filecontent 文件流
     *@param  $path         文件保存目录
     *
     */
    public function saveIosFile($filecontent,$upload_dir){

        if(empty($filecontent)) wp_send_json_error(array('info'=>__('数据错误', 'nlyd-student')));
        //$base64 = htmlspecialchars($filecontent);
        //$fileName = iconv ( "UTF-8", "GB2312", $filecontent );

        $filename = date('YmdHis').'_'.rand(1000,9999).'.jpg';          //定义图片名字及格式

        if(!file_exists($upload_dir)){
            mkdir($upload_dir,0755,true);
        }
        $savepath = $upload_dir.'/'.$filename;
        // $this->apiReturn(4001,$savepath);
        if (move_uploaded_file($filecontent, $savepath)) {
            return $filename;
        }else{
            return false;
        }
    }

    /*
     * base64文件流上传
     */
    public function base64file($filecontent,$upload_dir){
        if(empty($filecontent)) wp_send_json_error(array('info'=>__('数据错误', 'nlyd-student')));
        $len = strpos($filecontent, ';');//寻找位置
        $img_type = substr($filecontent, 5, $len - 5);//删除后面
        $arr = str2arr($img_type,'/');
        if($arr[1] == 'jpeg' || $arr[1] == 'jpg'){
            $ext = '.jpg';
        }elseif ($arr[1] == 'png'){
            $ext = '.png';
        }else{
            wp_send_json_error(array('info'=>__('只支持jpg,jpeg,png格式文件上传')));
        }

        if(!file_exists($upload_dir)){
            mkdir($upload_dir,0755,true);
        }

        $base_img = str_replace("data:image/{$arr[1]};base64,", '', $filecontent);
        $filename = date('YmdHis').'_'.rand(1000,9999).$ext;          //定义图片名字及格式
        $savepath = $upload_dir.$filename;
        //print_r($savepath);die;
        $a = file_put_contents($savepath, base64_decode($base_img));
        //print_r($a);die;
        if ($a) {
            return $filename;
        }else{
            return false;
        }
    }

    /**
     * 保存用户cookie信息
     * @param $user_id
     */
    public function setUserCookie($user_id){
        update_user_meta($user_id,'user_session_id',session_id());
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        $_SESSION['login_time'] = get_time()+15;
        if(get_user_meta($user_id, 'locale', true)) setcookie('user_language', get_user_meta($user_id, 'locale', true), time()+3600*24,'/');
        //update_user_meta($user_id,'last_login_time',get_time());
    }

    /**
     * 用户退出 
     */
    public function user_logout(){

        wp_logout();
        unset($_SESSION['login_time']);
        unset($_SESSION['user_info']);
        unset($_SESSION['user_openid']);
        wp_send_json_success(array('info'=>__('退出成功', 'nlyd-student'),'url'=>home_url('logins/index/login_type/out')));
    }

    /**
     * 消息列表
     */
    public function getMessagesLists(){
        if(is_ajax()){
            global $wpdb,$current_user;
            if(!$current_user->ID) wp_send_json_error(array('info' => __('您还没有登录', 'nlyd-student')));
            $page = ($page = intval($_POST['page'])) < 1 ? 1 : $page;
            $pageSize = 50;
            $start = ($page-1) * $pageSize;
            $sql = 'SELECT SQL_CALC_FOUND_ROWS id,`type`,title,read_status,title,content,message_time from '
                .$wpdb->prefix.'messages WHERE '
                .'status=1 AND user_id=' .$current_user->ID
                .' ORDER BY read_status ASC'
                .' LIMIT '.$start.', '.$pageSize;
            $result = $wpdb->get_results($sql);

            $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
            if(ceil($total['total'] / $pageSize) < $page && $total['total'] != 0) wp_send_json_error(array('info' => __('没有更多了', 'nlyd-student')));
            wp_send_json_success(array('info'=>$result));
        }
    }

    /**
     * 消息详情
     */
    public function getMessagesDetails(){
        global $wpdb,$current_user;
        $messagesId = intval($_POST['messages_id']);
        $row = $wpdb->get_row('SELECT title,content,message_time FROM '.$wpdb->prefix.'messages '.'WHERE'
            .' id='.$messagesId.' AND user_id='.$current_user->ID.' AND status=1');
        if($row){
            $wpdb->update($wpdb->prefix.'messages', array(
                'read_status' => 2
            ),array(
                'id' => $messagesId
            ));
            wp_send_json_success(array('info'=>$row));
        } else{
            wp_send_json_error(array('info'=>__('未找到数据', 'nlyd-student')));
        }
    }

    /**
     * 获取我的订单列表
     */
    public function getOrderList(){
        global $wpdb,$current_user;
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;

        if(!isset($_POST['pay_status'])) $_POST['pay_status'] = 10;
        switch ($_POST['pay_status']){
            case 10: //全部订单
                $payStatusWhere = '1=1';
                break;
            case 1://待支付
                $payStatusWhere = 'pay_status=1';
                break;
            case 2://待发货 支付完成
                $payStatusWhere = 'pay_status=2';
                break;
            case 3://待收货
                $payStatusWhere = 'pay_status=3';
                break;
            case 4://订单完成 (已收货)
                $payStatusWhere = 'pay_status=4';
                break;
//            case 5://订单失效
//                $payStatusWhere = 'pay_status=5';
                break;
            case -1://待退款
                $payStatusWhere = 'pay_status=-1';
                break;
            case -2://已退款
                $payStatusWhere = 'pay_status=-2';
                break;
            default:
                wp_send_json_error(array('info' => __('参数错误', 'nlyd-student')));
        }
        $page < 1 && $page = 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results('SELECT 
        id AS order_id,
        serialnumber,
        pay_status,
        match_id,
        IFNULL(fullname, "-") AS fullname,
        telephone,
        IFNULL(address, "-") AS address,
        CASE order_type 
        WHEN 1 THEN "报名订单" 
        WHEN 2 THEN "考级订单" 
        WHEN 3 THEN "商品订单" 
        END AS order_type_title,
        IFNULL(express_number, "-") AS express_number,
        IFNULL(express_company, "-") AS express_company,
        CASE pay_type 
        WHEN "zfb" THEN "支付宝" 
        WHEN "wx" THEN "微信" 
        WHEN "ylk" THEN "银联卡" 
        ELSE "-" 
        END AS pay_type,
        cost,
        CASE pay_status
        WHEN -2 THEN "已退款" 
        WHEN -1 THEN "待退款" 
        WHEN 1 THEN "待支付" 
        WHEN 2 THEN "待发货" 
        WHEN 3 THEN "待收货" 
        WHEN 4 THEN "已完成" 
        WHEN 5 THEN "已失效" 
        END AS pay_status_title,
        created_time
        FROM '.$wpdb->prefix.'order WHERE user_id='.$current_user->ID.' 
        AND '.$payStatusWhere.' AND pay_status!=5 
        LIMIT '.$start.','.$pageSize, ARRAY_A);

        //查询商品或比赛
        foreach ($rows as $k => $order){
            switch ($order['order_type']){
                case 1://报名订单
                    $posts = $wpdb->get_row('SELECT post_title FROM '.$wpdb->prefix.'posts WHERE ID='.$order['match_id']);
                    $goodsData = [
                        [
                            'goods_title' => $posts->post_title,
                            'goods_num' => 1,
                            'price' => $order['cost'],
                            'pay_price' => $order['cost'],
                            'pay_brain' => 0,
                        ]
                    ];
                    $allPrice = $order['cost'];
                    break;
                case 2://考级订单
                    $posts = $wpdb->get_row('SELECT post_title FROM '.$wpdb->prefix.'posts WHERE ID='.$order['match_id']);
                    $goodsData = [
                        [
                            'goods_title' => $posts->post_title,
                            'goods_num' => 1,
                            'price' => $order['cost'],
                            'pay_price' => $order['cost'],
                            'pay_brain' => 0,
                        ]
                    ];
                    $allPrice = $order['cost'];
                    break;
                case 3://商品订单
                    $goodsRows = $wpdb->get_results('SELECT od.goods_num,od.pay_price,od.pay_brain,g.goods_title FROM 
                    '.$wpdb->prefix.'order_goods AS od 
                    LEFT JOIN '.$wpdb->prefix.'goods AS g ON od.goods_id=g.id');
                    $goodsData = [];
                    $allPrice = 0;
                    foreach ($goodsRows as $goodsRow){
                        $goodsData[] = [
                            'goods_title' => $goodsRow->goods_title,
                            'goods_num' => $goodsRow->goods_num,
                            'price' => ($goodsRow->pay_price+$goodsRow->pay_brain) * $goodsRow->goods_num,
                            'pay_price' => $goodsRow->pay_price * $goodsRow->goods_num,
                            'pay_brain' => $goodsRow->pay_brain * $goodsRow->goods_num,
                        ];
                        $allPrice += $goodsRow->pay_price * $goodsRow->goods_num;
                    }
                    break;
            }
            $order['goodsList'] = $goodsData;
            $order['allPrice'] = $allPrice;
            $order['addGoodsNum'] = count($goodsData);
            $rows[$k] = $order;

        }
        if($rows) wp_send_json_success(array('info' => $rows));
        wp_send_json_error(array('info' => __('无订单', 'nlyd-student')));
    }
    /**
     * 获取订单详情
     */
    public function getOrderDetials(){
        $id = intval($_POST['id']);
        if($id < 1) wp_send_json_error(array('info' => __('参数错误', 'nlyd-student')));
        global $wpdb,$current_user;
        require_once 'class-student-account-order.php';
        $row = $wpdb->get_row('SELECT '.self::selectField().' FROM '.$wpdb->prefix.'order WHERE user_id='.$current_user->ID.' AND id='.$id, ARRAY_A);
        if($row) wp_send_json_success(array('info' => $row));
        wp_send_json_error(array('info' => __('未找到订单', 'nlyd-student')));
    }


    /**
     * 支付
     */
    public function pay(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_ranking_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        global $wpdb,$current_user;
        if($current_user->ID < 1 || !$current_user->ID){
            wp_send_json_error(['info' => __('您暂未登录', 'nlyd-student'), 'url' => home_url('logins')]);
        }

        $otderSn = trim($_POST['serialnumber']);
        $payType = $_POST['pay_type'];

        //查询配置
//        $interface_config = get_option('interface_config');
        $order = $wpdb->get_row(
            'SELECT id,serialnumber,match_id,user_id,fullname,telephone,address,pay_type,cost,pay_status,created_time,order_type FROM '
            .$wpdb->prefix.'order WHERE serialnumber='.$otderSn.' AND user_id='.$current_user->ID, ARRAY_A);
        if(!$order)  wp_send_json_error(array('info'=>__('订单不存在', 'nlyd-student')));
        if($order['pay_status'] != 1)  wp_send_json_error(array('info'=>__('此订单不是待支付订单', 'nlyd-student')));
        require_once 'class-student-payment.php';
        switch ($payType){
            case 'wxh5pay':
                //TODO 微信支付暂未开放
//                wp_send_json_error(array('info'=>'微信支付暂未开放'));
                //请求数据
                //1.统一下单方法
                $params['notify_url'] = home_url('payment/wxpay/type/wx_notifyUrl'); //商品描述
                $params['body'] = __('国际脑力运动', 'nlyd-student'); //商品描述
                $params['serialnumber'] = $order['serialnumber']; // TODO 商户自定义的订单号
                $params['price'] = $order['cost']; //订单金额 只能为整数 单位为分
                $params['attach'] = 'serialnumber='.$order['serialnumber']; //附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
                $wxpay = new Student_Payment('wxpay');
                //判断是否是微信浏览器
                if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
                    //jsapi支付需要一个单独的页面获取openid
                    $url = 'payment/wx_js_pay/type/wxpay/id/'.$order['id'].'/match_id/'.$_POST['match_id'];
                    if($order['order_type'] == '2'){
                        $url .= '/grad_id/'.$_POST['match_id'];
                    }elseif ($order['order_type'] == '3'){
                        $url .= '/course_id/'.$_POST['match_id'].'/center_id/'.$_POST['center_id'];
                    }
                    $url = home_url($url);
                    $result = ['status' => true, 'data' => $url];

//                    $params['notify_url'] = home_url('payment/wxpay/type/wx_notifyUrl/jspai/y'); //商品描述
//                    $params['open_id'] =$current_user->weChat_openid;
//                    $result = $wxpay->payClass->jsApiPay($params);
//                    if($result['status'] != false){
//                        wp_send_json_success(array('params' => $result['data'], 'info' => NULL));
//                    }else{
//                        wp_send_json_error(array('info'=>$result['data']));
//                    }
//                    $result = ['status' => true, 'data' => home_url('payment/wxpay/type/wx_jsApiPay/id/').$order['id'].'.html'];
                }else{
                    $result = $wxpay->payClass->h5UnifiedOrder($params);
                }

                break;
            case 'alipay':

                //支付宝需要跳转到自己的方法
                $result = ['status' => true, 'data' => home_url('payment/zfb_pay/type/alipay/id/'.$order['id'])];
                break;
        }

        if($result != false){
            if($result['status']){
                wp_send_json_success(array('info' => $result['data']));
            }else{
                wp_send_json_error(array('info'=>$result['data']));
            }
        }else{
            //发起支付失败
            wp_send_json_error(array('info'=>__('发起支付失败', 'nlyd-student')));
        }
    }

    /**
     * 意见反馈
     */
    public function feedback(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_ranking_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        global $wpdb, $current_user;
        $contact = $_POST['contact'];//联系方式
        $content = $_POST['content'];//内容41
        $date = date_i18n('Y m', get_time());
        $upload_dir = wp_upload_dir();
        $dateArr = explode(' ',$date);
        $dir = '/'.$dateArr[0].'/'.$dateArr[1].'/';
        $num = 0;
        $imagePathArr = [];
        foreach ($_POST['images'] as $upd){
            $file = $this->base64file($upd,$upload_dir['basedir'].$dir);
            if($file){
                $imagePathArr[] = $upload_dir['baseurl'].$dir.$file;
                ++$num;
            }
        }
        $data = [
            'content' => $content,
            'images' => serialize($imagePathArr),
            'contact' => $contact,
            'created_time' => current_time('mysql')
        ];

        if($wpdb->insert($wpdb->prefix.'feedback', $data)){
            wp_send_json_success(array('info' => sprintf(__('提交完成, %s张图片上传成功', 'nlyd-student'), $num)));
        }else{
            foreach ($imagePathArr as $v){
                $filePa = explode('uploads',$v);
                if(is_file($upload_dir['basedir'].$filePa[1])) unlink($upload_dir['basedir'].$filePa[1]);

            }
            wp_send_json_error(array('info' => __('提交失败', 'nlyd-student')));
        }
    }

    /**
     * 获取banner
     */
    public function getBanner(){
        $banners = get_option('index_banner_url');
        if($banners)
            wp_send_json_success(array('info' => $banners));
        else
            wp_send_json_error(array('info' => __('获取失败', 'nlyd-student')));
    }

    /**
     * 获取logo
     */
    public function getLogo(){
        $logo = get_option('logo_url');
        if($logo)
            wp_send_json_success(array('info' => $logo));
        else
            wp_send_json_error(array('info' => __('获取失败', 'nlyd-student')));
    }

    /**
     * 新闻咨询列表
     */
    public function getNewsLists(){
        global $wpdb;
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $page < 1 && $page = 1;
        $pageSize = 50;
        $which_cat = get_category_by_slug('news');
        $recentPosts = new WP_Query();
        $cat_query = $recentPosts->query('showposts='.$pageSize.'&cat='.$which_cat->cat_ID.'&paged='.$page);

        if($cat_query){
            //内容截取和图片
            foreach ($cat_query as $v){
                $v->image = wp_get_attachment_image_src(get_post_thumbnail_id($v->ID), 'thumbnail')[0];
                $v->post_content = msubstr(strip_tags($v->post_content),0,35);
            }
            wp_send_json_success(array('info' => $cat_query));
        }else{
            wp_send_json_error(array('info' => __('获取失败', 'nlyd-student')));
        }

    }

    /**
     * 商品列表
     */
    public function getGoodsLists(){
        global $wpdb;
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        //搜索商品
        $searchWhere = isset($_POST['search_str']) ? 'AND goods_title LIKE "%'.trim($_POST['search_str']).'%"' : '';
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results('SELECT 
        id,goods_title,goods_intro,images,brain,stock,sales,price 
        FROM '.$wpdb->prefix.'goods WHERE shelf=1 AND stock>0 '.$searchWhere.' LIMIT '.$start.','.$pageSize, ARRAY_A);
        foreach ($rows as &$row){
            $row['images'] = unserialize($row['images']);
        }
        if($rows) wp_send_json_success(['info' => $rows]);
        else wp_send_json_error(['info' => __('没有商品', 'nlyd-student')]);
    }

    /**
     * 加入购物车
     */
    public function joinCart(){
        global $wpdb,$current_user;
        $goodsId = intval($_POST['goods_id']);
        $goodsNum = intval($_POST['num']);
        if($goodsId < 1) wp_send_json_error(['info' => __('参数错误', 'nlyd-student')]);
        //检查商品库存
        $goods = $wpdb->get_row('SELECT stock FROM '.$wpdb->prefix.'goods WHERE id='.$goodsId);
        if(!$goods) wp_send_json_error(['info' => __('未找到商品', 'nlyd-student')]);
        if($goods['stock'] < $goodsNum) wp_send_json_error(['info' => __('商品库存不足', 'nlyd-student')]);
        //该商品是否已存在购物车
        $row = $wpdb->get_row('SELECT id,goods_num FROM '.$wpdb->prefix.'order_goods WHERE user_id='.$current_user->ID.' AND goods_id='.$goodsId.' AND order_id=0', ARRAY_A);
        if($row){
            $bool = $wpdb->update($wpdb->prefix.'order_goods',['goods_num' => $goodsNum+$row['goods_num']], ['id' => $row['id']]);
        }else{
            $bool = $wpdb->insert($wpdb->prefix.'order_goods', ['goods_num' => $goodsNum,'goods_id' => $goodsId, 'user_id' => $current_user->ID]);
        }
        if($bool) wp_send_json_success(['info' => __('加入购物车成功', 'nlyd-student')]);
        else wp_send_json_error(['info' => __('操作失败', 'nlyd-student')]);
    }

    /**
     * 提交订单
     */
    public function subGoodsOrder(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_sub_order_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        global $wpdb,$current_user;
        $user_id = $current_user->ID;
        $address_id = intval($_POST['address']);
        $cartIdArr = $_POST['carts'];
        $orderGoodsIdStr = '(';
        if(is_array($cartIdArr) && !empty($cartIdArr)){
            foreach ($cartIdArr as $cartId){
                $orderGoodsIdStr .= $cartId.',';
            }
            $orderGoodsIdStr = substr($orderGoodsIdStr, 0, strlen($orderGoodsIdStr)-1).')';
        }else{
            wp_send_json_error(['info' => __('请选择商品', 'nlyd-student')]);
        }
        $address = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'my_address 
        WHERE user_id='.$user_id.' AND id='.$address_id, ARRAY_A);
        if(!$address) wp_send_json_error(['info' => __('出错了, 找不到收货地址', 'nlyd-student')]);
        //文件锁
        if(!is_file('flock.txt')) file_put_contents('flock.txt', 1);
        $fp = fopen('flock.txt', 'a+');
        if(flock($fp, LOCK_EX)){
            //查询购物车
            $orderGoodsRows = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'order_goods WHERE user_id='.$user_id.' AND order_id=0 AND id IN'.$orderGoodsIdStr, ARRAY_A);
            //计算支付价格
            $allPrice = 0;//支付金额
            $allBrain = 0;//脑币
//            $orderGoodsIdStr = '(';// (1,2,3) 后面修改order_goods的order_id使用
            $wpdb->query('START TRANSACTION');
            foreach ($orderGoodsRows as $orderGoodsRow){
                $goods = $wpdb->get_row('SELECT id,goods_title,shelf,price,stock FROM '.$wpdb->prefix.'goods WHERE id='.$orderGoodsRow['goods_id'], ARRAY_A);
                //不存在商品
                if(!$goods){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => __('出错了, 找不到商品', 'nlyd-student')]);
                }
                //已下架商品
                if($goods['shelf'] == 2) {
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => sprintf(__('%s-已下架', 'nlyd-student'), $goods['goods_title'])]);
                }
                //库存不足
                if($goods['stock'] < $orderGoodsRow['goods_num']){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => sprintf(__('%s-库存不足', 'nlyd-student'), $goods['goods_title'])]);
                }
                //减少商品库存
                if(!$wpdb->update($wpdb->prefix.'goods', ['stock' => $goods['stock'] - $orderGoodsRow['goods_num']], ['id' => $goods['id']])){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => __('出错了, 库存更新失败', 'nlyd-student')]);
                }
                //更新order_goods 支付价格和支付脑币
                if(!$wpdb->update($wpdb->prefix.'order_goods', ['pay_price' => $goods['price'], 'pay_brain' => $goods['brain']], ['id' => $goods['id']])){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => __('出错了, 支付价格更新失败', 'nlyd-student')]);
                }
                $allPrice += $goods['price'] * $orderGoodsRow['goods_num'];
                $allBrain += $goods['brain'] * $orderGoodsRow['goods_num'];
//                $orderGoodsIdStr .= $orderGoodsRow['id'].',';
            }
            $orderGoodsIdStr = substr($orderGoodsIdStr,0,strlen($orderGoodsIdStr)-1);
            $orderGoodsIdStr .= ')';
            //订单数据
            $orderInsertData = [
                'user_id' => $user_id,
                'match_id' => 0,
                'fullname' => $address['fullname'],
                'telephone' => $address['telephone'],
                'address' => $address['country'].$address['province'].$address['city'].$address['area'].$address['address'],
                'order_type' => 3,
                'express_number' => '',
                'express_company' => '',
                'cost' => $allPrice,
                'pay_status' => 1,
                'created_time' => current_time('mysql'),
            ];

            $bool = $wpdb->insert($wpdb->prefix.'order',$orderInsertData);
            if($bool){//新增订单数据
                $insertId = $wpdb->insert_id;
                if($wpdb->update($wpdb->prefix.'order', ['serialnumber' => createNumber($user_id,$wpdb->insert_id)], ['id' => $insertId])){//修改订单号
                    if($wpdb->query('UPDATE '.$wpdb->prefix.'order_goods SET order_id='.$insertId.' WHERE id IN'.$orderGoodsIdStr)){  //修改order_goods的状态
                        $wpdb->query('COMMIT');
                        wp_send_json_success(['info' => $insertId]);
                    }else{
                        //修改order_goods的状态失败
                        $wpdb->query('ROLLBACK');
                        wp_send_json_error(['info' => __('提交订单失败', 'nlyd-student')]);
                    }
                }else{
                    //修改订单号失败
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => __('提交订单失败', 'nlyd-student')]);
                }
            }else{
                //插入订单数据失败
                $wpdb->query('ROLLBACK');
                wp_send_json_error(['info' => __('提交订单失败', 'nlyd-student')]);
            }
        }else{
            wp_send_json_error(['info' => __('系统繁忙,请稍后再试', 'nlyd-student')]);
        }
    }

    /**
     * 确认收货
     */
    public function collectGoods(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_collect_goods_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        global $wpdb,$current_user;
        $id = intval($_POST['id']);
        $bool = $wpdb->update($wpdb->prefix.'order', ['pay_status' => 4], ['id' => $id, 'pay_stats' => 3, 'user_id' => $current_user->ID]);
        if($bool) wp_send_json_success(['info' => __('订单已确认收货', 'nlyd-student')]);
        else  wp_send_json_error(['info' => __('操作失败,请稍后再试', 'nlyd-student')]);
    }

    /**
     * 取消订单
     * 可取消状态 :  未支付
     */
    public function cancelOrder(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_cancel_goods_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        $serialnumber = trim($_POST['serialnumber']);
        global $wpdb;
        if($wpdb->query('UPDATE '.$wpdb->prefix.'order SET pay_status=5 WHERE pay_status=1 AND serialnumber='.$serialnumber))
            wp_send_json_success(['info' => __('订单已取消', 'nlyd-student')]);
        else
            wp_send_json_error(['info' => __('操作失败,请稍后再试', 'nlyd-student')]);
    }

    /**
     * 订单自动确认收货
     */
    public function autoCollectGoods(){
        global $wpdb;
        $contrastTime = get_time()-86400*15;//15天
        $wpdb->query('UPDATE '.$wpdb->prefix.'order'.' SET pay_status=4 WHERE pay_status=3 AND send_goods_time<'.$contrastTime);

    }

    /**
     * 更换我的主训教练
     */
    public function replaceMajorCoach(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_replace_major_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        if(empty($_POST['coach_id']) ||  empty($_POST['category_id'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));
        global $wpdb,$current_user;
        //判断是否登录
        if($current_user->ID < 1) wp_send_json_error(array('info'=>__('未登录', 'nlyd-student')));

        //判断当前是否是已申请的教练
        $row = $wpdb->get_row("select id,user_id,category_id,apply_status,major from {$wpdb->prefix}my_coach where coach_id = {$_POST['coach_id']} and user_id = $current_user->ID and category_id = {$_POST['category_id']} and apply_status=2",ARRAY_A);
        if(empty($row)) wp_send_json_error(array('info'=>__('数据错误', 'nlyd-student')));
        if($row['apply_status'] != 2){
            $post_title = get_post($row['category_id'])->post_title;
            wp_send_json_error(array('info' => sprintf(__('该教练还不是你的%s教练', 'nlyd-student'), $post_title)));
        }

        //开启事务
        $wpdb->query('START TRANSACTION');
        //取消原主训教练
        $cancelRes = $wpdb->query('UPDATE '.$wpdb->prefix.'my_coach SET major=0 WHERE category_id='.$_POST['category_id'].' AND user_id='.$current_user->ID);
        if(!$cancelRes) {
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>__('更换主训教练失败', 'nlyd-student')));
        }
        //设着当前教练为主训
        $currentRes = $wpdb->query('UPDATE '.$wpdb->prefix.'my_coach SET major=1 WHERE id='.$row['id']);
        if($currentRes){
            $wpdb->query('COMMIT');
            wp_send_json_success(['info' => __('主训教练更换成功', 'nlyd-student')]);

        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>__('更换主训教练失败', 'nlyd-student')));
        }
    }

    /**
     * 学生解除教练关系
     */
    public function relieveMyCoach(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_relieve_coach_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/
        $categoryId = isset($_POST['category_id']) ? trim($_POST['category_id']) : '';
        $coach_id = isset($_POST['coach_id']) ? intval($_POST['coach_id']) : '';
        if(empty($coach_id) ||  empty($categoryId)) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));
        global $wpdb,$current_user;
        //判断是否登录
        if($current_user->ID < 1) wp_send_json_error(array('info'=>__('未登录', 'nlyd-student')));

        //判断当前是否是已申请的教练
        $rows = $wpdb->get_results("select mc.id,mc.apply_status,mc.major,u.user_mobile,u.user_email,p.post_title,u.ID as uid,mc.coach_id from {$wpdb->prefix}my_coach as mc 
        left join {$wpdb->users} as u on u.ID=mc.coach_id 
        left join {$wpdb->posts} as p on mc.category_id=p.ID 
        where mc.coach_id = {$coach_id} and mc.user_id = $current_user->ID and mc.category_id IN({$categoryId}) and mc.apply_status=2",ARRAY_A);
//        echo $wpdb->last_query;
//        die;
        if(empty($rows)) wp_send_json_error(array('info'=>__('数据错误', 'nlyd-student')));
//        if($row['apply_status'] != 2) wp_send_json_error(array('info'=>__('该教练还不是你的教练', 'nlyd-student')));

        //改变状态
        $update = $wpdb->query('UPDATE '.$wpdb->prefix.'my_coach SET apply_status=3 WHERE user_id='.$current_user->ID.' AND coach_id='.$coach_id.' AND category_id IN('.$categoryId.')');
        if($update){
            //TODO 发送短信通知教练 ===================================
                $userID = get_user_meta($current_user->ID, '', true)['user_ID'][0];
//                $userContact = getMobileOrEmailAndRealname($row['coach_id'], $row['user_mobile'], $row['user_email']);
               foreach ($rows as $v){
                   $coach_real_name = get_user_meta($v['coach_id'], 'user_real_name',true);
                   $coach_real_name = isset($coach_real_name['real_name']) ? $coach_real_name['real_name'] : $v['user_login'];
                   if($v['user_mobile']){
                       $ali = new AliSms();
                       $result = $ali->sendSms($v['user_mobile'], 14, array('coach'=>$coach_real_name, 'user_id' => $userID ,'cate' => $v['post_title']), '国际脑力运动');
                   }else{
                       $result = send_mail($v['user_email'], 12, ['coach' => $coach_real_name, 'userID' => $userID, 'cate' => $v['post_title']]);
                   }
               }
//            $ali = new AliSms();
//            $result = $ali->sendSms($row['user_mobile'], 14, array('coach'=>str_replace(', ', '', $row['display_name']), 'user_id' => $userID ,'cate' => $row['post_title']), '国际脑力运动');
            wp_send_json_success(['info' => __('解除教学关系成功', 'nlyd-student')]);
        }
        wp_send_json_error(array('info'=>__('解除教学关系失败', 'nlyd-student')));
    }

    /**
     * 判断当前类别当前用户是是否存在主训教练
     */
    public function searchCurrentCoach(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_current_coach_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        $category_id = intval($_POST['category_id']);
        global $wpdb,$current_user;
        if($category_id < 1){
            wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));
        }
        $id = $wpdb->get_var('SELECT id FROM '.$wpdb->prefix.'my_coach WHERE category_id='.$category_id.' AND user_id='.$current_user->ID.' AND  major=1 AND (apply_status=1 or apply_status=2)');
        //var_dump($id);die;
        if(!$id){
            wp_send_json_success(array('info'=>__('当前无主训教练', 'nlyd-student')));
        }else{
            wp_send_json_error(array('info'=>__('当前已存在主训教练', 'nlyd-student')));
        }
    }

    /**
     * 微信授权登录绑定手机或邮箱
     */
    public function wxWebLoginBindMobile(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_current_wx_web_login_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        $bindType = $_POST['type'];
        if($bindType != 'code' && $bindType != 'username') wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));

        if(preg_match('/^1[3456789][0-9]{9}$/',$_POST['mobile'])) {
            $type = 'mobile';
        }elseif (is_email($_POST['mobile'])){
            $type = 'email';
        }else{
            wp_send_json_error(array('info'=>__('手机或邮箱格式不正确', 'nlyd-student')));
        }



        global $wpdb;


        if($bindType == 'code'){
            $user = $wpdb->get_row('SELECT ID,user_pass FROM '.$wpdb->users.' WHERE (user_mobile="'.$_POST['mobile'].'" OR user_login="'.$_POST['mobile'].'" OR user_email="'.$_POST['mobile'].'") AND weChat_openid!=""');
            //验证码绑定
            if($type == 'mobile'){
                //判断当前手机是否已经存在
                if($user)  wp_send_json_error(array('info'=>__('当前手机号码已绑定其它微信', 'nlyd-student')));
                if($bindType == 'code') $this->get_sms_code($_POST['mobile'],17,true,$_POST['send_code']);
            }else{
                if($user)  wp_send_json_error(array('info'=>__('当前邮箱已绑定其它微信', 'nlyd-student')));
                if($bindType == 'code') $this->get_smtp_code($_POST['mobile'],17,true,$_POST['send_code']);
            }
            $user_id = $_POST['user_id'];
        }else{
            $user = $wpdb->get_row('SELECT ID,user_pass,weChat_openid FROM '.$wpdb->users.' WHERE (user_mobile="'.$_POST['mobile'].'" OR user_login="'.$_POST['mobile'].'" OR user_email="'.$_POST['mobile'].'")');
            //账号绑定
            //判断用户是否存在
            if($user){
                $check = wp_check_password($_POST['password'],$user->user_pass);
                if(!$check) wp_send_json_error(array('info'=>__('密码错误', 'nlyd-student')));
            }else{
                wp_send_json_error(array('info'=>__('该用户不存在', 'nlyd-student')));
            }

        //if($user->weChat_openid) wp_send_json_error(array('info'=>'该用户已绑定其它微信'));
            $user_id = $user->ID;
            $url = home_url('account');

            //添加推广人
            if(isset($_SESSION['referee_id_wx']) && !(get_user_by('ID',$user_id)->referee_id) && $user_id != $_SESSION['referee_id_wx'] && get_user_by('ID',$_SESSION['referee_id_wx'])->referee_id != $user_id){
                $bool = $wpdb->update($wpdb->prefix.'users',array('referee_id'=>$_SESSION['referee_id_wx'],'referee_time'=>date_i18n('Y-m-d',get_time())),array('ID'=>$user_id));
                if(!$bool){
                    wp_send_json_error(array('info'=>__('添加推荐人失败!', 'nlyd-student')));
                }
                if(isset($_SESSION['referee_id_wx'])){
                    $url = home_url('/zone/indexUser/');
                }
                unset($_SESSION['referee_id_wx']);
            }
            $this->setUserCookie($user_id);
            //wp_send_json_success(['info' => '登录成功', 'url' => home_url('account')]);
            if(isset($_POST['loginType']) && $_POST['loginType'] == 'sign'){
                wp_send_json_success(array('info'=>__('登录成功,即将跳转', 'nlyd-student'), 'url' => home_url('account/certification/type/sign/sign_match_id/'.$_POST['match_id'])));
            }else{
                wp_send_json_success(array('info'=>__('登录成功', 'nlyd-student'), 'url' => $url));
            }
        }


        $mobile = $_POST['mobile'];
        $access_token = $_POST['access'];
        $open_id = $_POST['open'];
        require_once 'class-student-weixin.php';
        $weiLogin = new Student_Weixin();
        $res = $weiLogin->getUserInfo($access_token, $open_id, true, $user_id, $mobile,$type,$bindType);

        if($res){
            if(isset($_POST['loginType']) && $_POST['loginType'] == 'sign'){
                //获取所有报名信息
                $sql = "select user_id from {$wpdb->prefix}order where match_id = {$_POST['match_id']} and pay_status in(2,3,4) order by id asc";
                $results = $wpdb->get_results($sql,ARRAY_A);
                $index = array_search($user_id,array_column($results,'user_id')) + 1;

                wp_send_json_success(array('info'=>__('账户绑定完成,即将跳转', 'nlyd-student'), 'url' => home_url('/account/info/type/sign/sign_match/'.$_POST['match_id'].'/order_index/'.$index)));
            }else{

                wp_send_json_success(array('info'=>__('绑定成功', 'nlyd-student'), 'url' => home_url('account')));
            }
            //wp_send_json_success(array('info'=>'绑定成功', 'url' => home_url('account')));
        }else{
            wp_send_json_error(array('info'=>__('绑定失败', 'nlyd-student')));
        }
    }
    /**
     * 名录列表
     */
    public function getDirectories(){
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $type = isset($_POST['type']) ? intval($_POST['type']) : 0;
//        if($type < 1)  wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));
        $page < 1 && $page = 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        global $wpdb;
        $res = $wpdb->get_results('SELECT d.user_id,d.level,d.certificate,p.post_title,
        CASE d.range 
        WHEN 1 THEN "中国" 
        WHEN 2 THEN "国际" 
        ELSE "未知" 
        END AS ranges  
        FROM '.$wpdb->prefix.'directories AS d 
        LEFT JOIN '.$wpdb->posts.' AS p ON p.ID=d.category_id 
        WHERE d.is_show=1 ORDER BY d.id ASC LIMIT '.$start.','.$pageSize);
        foreach ($res as &$v){
            $usermeta = get_user_meta($v->user_id,'', true);
            $user_real_name = unserialize($usermeta['user_real_name'][0]);
            if(!$user_real_name){
                $user_real_name['real_name'] = $usermeta['last_name'][0].$usermeta['first_name'][0];
            }
            $v->header_img = $usermeta['user_head'][0];
            $v->userID = $usermeta['user_ID'][0];
            $v->real_name = $user_real_name['real_name'];
            $v->sex = $usermeta['user_gender'][0];

        }

        if($res)
            wp_send_json_success(array('info'=>$res));
        else
            wp_send_json_error(array('info'=>__('没有数据', 'nlyd-student')));
    }

    /**
     * 获取用户考级名录
     */
    public function getUserGradingDirectories(){
        $cate_type = isset($_POST['type_id']) ? intval($_POST['type_id']) : 0;
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        if(!in_array($cate_type,[1,2,3])) wp_send_json_error(['info' => '参数错误!']);
        switch ($cate_type){
            case 1:
                $cate_name = 'read';
                break;
            case 2:
                $cate_name = 'memory';
                break;
            case 3:
                $cate_name = 'compute';
                break;
        }
        $page < 1 && $page = 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        global $wpdb;
        $rows = $wpdb->get_results("SELECT user_id,`{$cate_name}` AS skill_level FROM {$wpdb->prefix}user_skill_rank WHERE skill_type=1 AND `{$cate_name}`>0 LIMIT {$start},{$pageSize}", ARRAY_A);
        if($rows){
            foreach ($rows as $k => &$row){
                $user_meta = get_user_meta($row['user_id']);
                $row['userID'] = isset($user_meta['user_ID']) ? $user_meta['user_ID'][0] : '';
                $row['real_name'] = isset($user_meta['user_real_name']) ? (isset(unserialize($user_meta['user_real_name'][0])['real_name'])?unserialize($user_meta['user_real_name'][0])['real_name']:'') : '';
                $row['user_head'] = isset($user_meta['user_head']) ? $user_meta['user_head'][0] : '';
                $row['user_sex'] = isset($user_meta['user_gender']) ? $user_meta['user_gender'][0] : '';
                if($row['real_name'] == '') unset($row);
            }
            wp_send_json_success(['info'=>'获取成功','data'=>$rows]);
        }else{
            wp_send_json_error(['info'=>'无数据!']);
        }

    }

    /**
     * 根据搜索条件获取战队列表
     */
    public function getTeamsBySearch(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_team_search_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/

        global $wpdb,$current_user;
        $search = trim($_POST['search']);
        $map = array();
        $map[] = " a.post_status = 'publish' ";
        $map[] = " a.post_type = 'team' ";
        $map[] = " a.post_title LIKE '%$search%' ";
        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        $where = join(' and ',$map);
        $sql = "select SQL_CALC_FOUND_ROWS a.ID,a.post_title,b.user_id,b.status,
                if(c.team_world != '',c.team_world,'--') as team_world,
                if(c.team_director != '',c.team_director,'--') as team_director,
                if(c.team_slogan != '',c.team_slogan,'--') as team_slogan,
                if(c.team_leader != '',c.team_leader,'--') as team_leader
                from {$wpdb->prefix}posts a  
                left join {$wpdb->prefix}match_team b on a.ID = b.team_id and b.user_type = 1 and b.user_id = {$current_user->ID}
                left join {$wpdb->prefix}team_meta c on a.ID = c.team_id 
                where {$where} order by b.status desc limit $start,$pageSize";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        // print_r($sql);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无战队', 'nlyd-student')));
        foreach ($rows as $k => $val ){

            //获取领队
            if(is_numeric($val['team_director'])){
                $user = get_userdata( $val['team_director']);
                if(!empty($user->data->display_name)){
                    $rows[$k]['team_director'] = preg_replace('/, /','',$user->data->display_name);
                }
            }
            //获取战队成员总数
            $total = $wpdb->get_var("select count(*) from {$wpdb->prefix}match_team where team_id = {$val['ID']} and status = 2 and user_type = 1");
            $rows[$k]['team_total'] = $total;

            $rows[$k]['team_url'] = home_url('/teams/teamDetail/team_id/'.$val['ID']);
        }
        //print_r($rows);
        wp_send_json_success(array('info'=>$rows));
    }

    /**
     * 战队排名
     */
    public function teamRanking(){

        /*if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_team_ranking_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }*/
        global $wpdb,$current_user;
        $match_id = intval($_POST['match_id']);
        if($match_id < 1) wp_send_json_error(['info' => __('比赛参数错误', 'nlyd-student')]);
        $match = $wpdb->get_row('SELECT match_status,match_id FROM '.$wpdb->prefix.'match_meta_new WHERE match_id='.$match_id, ARRAY_A);
        if(!$match || $match['match_status'] != -3) wp_send_json_error(['info' => __('当前比赛未结束', 'nlyd-student')]);

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        if($page < 1) $page = 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        $page = 1;
        //战队排名
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379,1);
        $redis->auth('leo626');
       // $redis->delete('team_ranking_'.$match_id);

        if(!$data = $redis->get('team_ranking_'.$match_id)){
            //获取参加比赛的成员
            $sql = "SELECT p.post_title,p.ID,o.user_id FROM `{$wpdb->prefix}order` AS o 
                    LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON o.user_id=mt.user_id AND mt.status=2 
                    LEFT JOIN `{$wpdb->posts}` AS p ON p.ID=mt.team_id 
                    WHERE o.match_id={$match['match_id']} AND o.pay_status IN(2,3,4) AND mt.team_id!='' AND p.post_title!=''";
            $result = $wpdb->get_results($sql, ARRAY_A);

            //处理每个战队的成员
            $teamsUsers = []; //每个战队的每个成员
            foreach ($result as $resV){
                if(!isset($teamsUsers[$resV['ID']])) {
                    $teamsUsers[$resV['ID']] = [];
                    $teamsUsers[$resV['ID']]['user_ids'] = [];
                    $teamsUsers[$resV['ID']]['team_name'] = $resV['post_title'];
                    $teamsUsers[$resV['ID']]['team_id'] = $resV['ID'];
                }
                $teamsUsers[$resV['ID']]['user_ids'][] = $resV['user_id'];
            }
            foreach ($teamsUsers as &$tuV){
                $tuV['user_ids'] = join(',',$tuV['user_ids']);
            }

            $totalRanking = [];

            foreach ($teamsUsers as $tuV2){

                //每个战队的分数
                // $sql = "SELECT SUM(my_score) AS my_score,SUM(surplus_time) AS surplus_time,SUM(created_microtime) AS created_microtime FROM 
                //           (SELECT MAX(my_score) AS my_score,MAX(surplus_time) AS surplus_time,if(MAX(created_microtime) > 0, MAX(created_microtime) ,0) AS created_microtime FROM `{$wpdb->prefix}match_questions` AS mq 
                //           LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON mt.user_id=mq.user_id AND mt.status=2 AND mt.team_id={$tuV2['team_id']}
                //           WHERE mq.match_id={$match['match_id']} AND mt.team_id={$tuV2['team_id']} AND mq.user_id IN({$tuV2['user_ids']}) 
                //           GROUP BY mq.project_id,mq.user_id) AS child  
                //           ORDER BY my_score DESC limit 0,5
                //        ";

                //  $row = $wpdb->get_row($sql,ARRAY_A);
                // $tuV2['my_score'] = $row['my_score'] > 0 ? $row['my_score'] : 0;
                // $tuV2['surplus_time'] = $row['surplus_time'] > 0 ? $row['surplus_time'] : 0;
                // $tuV2['created_microtime'] = $row['created_microtime'] > 0 ? $row['created_microtime'] : 0;
                // $totalRanking[] = $tuV2;
                 
                 
             $sql = "SELECT SUM(my_score) AS my_score,SUM(surplus_time) AS surplus_time,SUM(created_microtime) AS created_microtime FROM 
                  (SELECT MAX(my_score) AS my_score,MAX(surplus_time) AS surplus_time,if(MAX(created_microtime) > 0, MAX(created_microtime) ,0) AS created_microtime,mq.user_id FROM `{$wpdb->prefix}match_questions` AS mq 
                  LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON mt.user_id=mq.user_id AND mt.status=2 AND mt.team_id={$tuV2['team_id']}
                  WHERE mq.match_id={$match['match_id']} AND mt.team_id={$tuV2['team_id']} AND mq.user_id IN({$tuV2['user_ids']}) AND mq.is_true = 1 
                  GROUP BY mq.project_id,mq.user_id) AS child  
                  GROUP BY user_id 
                  ORDER BY my_score DESC limit 0,5
               ";      
                   $rows = $wpdb->get_results($sql,ARRAY_A);

                $tuV2['my_score'] = 0;
                $tuV2['surplus_time'] = 0;
                $tuV2['created_microtime'] = 0;
                foreach ($rows as $key => $value) {
                    $tuV2['my_score'] += $value['my_score'];
                    $tuV2['surplus_time'] += $value['surplus_time'];
                    $tuV2['created_microtime'] += $value['created_microtime'];
                }
                $totalRanking[] = $tuV2;
            }
            //排序
            for($i = 0; $i < count($totalRanking); ++$i){
                if(isset($totalRanking[$i+1])){
                    for ($j = $i+1; $j < count($totalRanking); ++$j){
                        if($totalRanking[$i]['my_score'] == $totalRanking[$j]['my_score']){
//                       if($totalRanking[$i]['my_score'] < 1){
//                           $rankingAuto = false;
//                       }else
                            if($totalRanking[$j]['surplus_time'] > $totalRanking[$i]['surplus_time']){

                                $a = $totalRanking[$j];
                                $totalRanking[$j] = $totalRanking[$i];
                                $totalRanking[$i] = $a;
                            }elseif ($totalRanking[$j]['surplus_time'] == $totalRanking[$i]['surplus_time']){
                                if($totalRanking[$j]['created_microtime'] < $totalRanking[$i]['created_microtime']){
                                    $a = $totalRanking[$j];
                                    $totalRanking[$j] = $totalRanking[$i];
                                    $totalRanking[$i] = $a;
                                }
                            }
                        }elseif ($totalRanking[$j]['my_score'] > $totalRanking[$i]['my_score']){
                            $a = $totalRanking[$j];
                            $totalRanking[$j] = $totalRanking[$i];
                            $totalRanking[$i] = $a;
                        }
                    }
                }
            }
            //名次
            $ranking = 1;
            foreach ($totalRanking as $k => $v){
                $totalRanking[$k]['ranking'] = $ranking;
                if( $totalRanking[$k]['my_score'] > 0){
                    ++$ranking;
                }
            }

//            $ranking = 1;
//            foreach ($totalRanking as $k => $v){
//                $totalRanking[$k]['ranking'] = $ranking;
//                if(!(isset($totalRanking[$k+1]) && $totalRanking[$k+1]['my_score'] == $totalRanking[$k]['my_score'] && $totalRanking[$k+1]['surplus_time'] == $totalRanking[$k]['surplus_time'])){
//                    ++$ranking;
//                }
//            }
            $data = $totalRanking;
            $redis->setex('team_ranking_'.$match_id, 3600*24*7,json_encode($data));
        }
        else{
            $data = json_decode($data, true);
        }
        $count = count($data);
        $pageAll = ceil($count/$pageSize);
        if($page > $pageAll) wp_send_json_error(['info' => __('没有数据', 'nlyd-student')]);

        if($pageAll == $page){
            if($count <= $pageSize){
                $pageSize = $count;
            }else{
                $pagesz = $count%$pageSize;
                $pageSize = $pagesz < 1 ? $pageSize : $pagesz;
            }
        }

        $my_team = [];
        foreach ($data AS $tuV3){
            if(in_array($current_user->ID,explode(',',$tuV3['user_ids']))){
                $my_team = $tuV3;
            }
        }
        $data = array_slice($data, $start, $pageSize);

        wp_send_json_success(['info' => $data, 'my_team' => $my_team]);
    }

    /**
     * 设置记忆等级
     */
    /*public function set_memory_grade(){
        if(empty($_POST['genre_id']) || empty($_POST['grad_type']) || empty($_POST['grade'])){
            wp_send_json_error(array('info'=>__('参数错误')));
        }
        global $wpdb,$current_user;
        $insert = array(
            'user_id'=>$current_user->ID,
            'genre_id'=>$_POST['genre_id'],
            'grade_type'=>$_POST['grad_type'],
            'created_time'=>get_time('mysql'),
        );
        print_r($insert);die;
        $c = $wpdb->insert($wpdb->prefix.'user_grade_log_history',$insert);
    }*/

    /**
     * 训练答案提交
     */
    public function trains_submit(){

        unset($_SESSION['trains_post_id']);

        if(empty($_POST['genre_id']) || empty($_POST['project_type'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));
        global $wpdb,$current_user;

        ini_set('post_max_size','20M');

        $_SESSION['train_list'] = $_POST;

        switch ($_POST['project_type']){
            case 'szzb':
            case 'pkjl':

                if(!empty($_POST['my_answer'])){

                    $len = count($_POST['train_questions']);

                    $error_len = count(array_diff_assoc($_POST['train_answer'],$_POST['my_answer']));

                    $score = $_POST['project_type'] == 'szzb' ? 12 : 18;

                    $my_score = ($len-$error_len)*$score;

                    if ($error_len == 0 && !empty($_POST['my_answer'])){
                        $my_score += $_POST['surplus_time'] * 1;
                    }
                }else{
                    $my_score = 0;
                }

            break;
            case 'kysm':
            case 'zxss':
            case 'nxss':

                $data_arr = $_POST['my_answer'];

                if(!empty($data_arr)){
                    $match_questions = array_column($data_arr,'question');
                    $questions_answer = array_column($data_arr,'rights');
                    $_POST['my_answer'] = array_column($data_arr,'yours');
                }
                if($_POST['project_type'] == 'nxss'){
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
                    $error_len = count(array_diff_assoc($questions_answer,$_POST['my_answer']));
                    $my_score = ($len-$error_len)*10;
                }


                $_POST['train_questions'] = $match_questions;
                $_POST['train_answer'] = $questions_answer;

                break;
            case 'wzsd':
                //print_r($_POST);die;
                if(empty($_POST['post_id'])) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));
                //print_r($_POST);die;
                $questions_answer = $_POST['train_answer'];
                $len = count($questions_answer);
                $success_len = 0;

                foreach ($questions_answer as $k=>$val){
                    $arr = array();
                    foreach ($val['problem_answer'] as $key => $v){
                        if($v == 1){
                            $arr[] = $key;
                        }
                    }

                    if(isset($_POST['my_answer'][$k])){
                        if(arr2str($arr) == arr2str($_POST['my_answer'][$k])) ++$success_len;
                    }
                }
                $my_score = $success_len * 23;
                if ($len/$success_len >= 0.8 ){
                    $my_score += $_POST['surplus_time'] * 1;
                }
                break;
            default:
                break;
        }

        $insert = array(
            'user_id'=>$current_user->ID,
            'genre_id'=>$_POST['genre_id'],
            'project_type'=>$_POST['project_type'],
            'train_questions'=>json_encode($_POST['train_questions']),
            'train_answer'=>json_encode($_POST['train_answer']),
            'my_answer'=>json_encode($_POST['my_answer']),
            'surplus_time'=>$_POST['surplus_time'],
            'my_score'=>$my_score,
            'post_id'=>isset($_POST['post_id']) ? $_POST['post_id'] : '',
            'created_time'=>get_time('mysql'),
        );
        //print_r($insert);die;
        $sql = "select id from {$wpdb->prefix}user_train_logs where user_id = {$current_user->ID} order by created_time asc ";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        $total = count($rows);

        if($total > 99){
            $result = $wpdb->update($wpdb->prefix.'user_train_logs',$insert,array('id'=>$rows[0]['id']));
            $id = $rows[0]['id'];
        }else{

            $result = $wpdb->insert($wpdb->prefix.'user_train_logs',$insert);
            $id = $wpdb->insert_id;
        }
        if($result){
            /*if($_POST['project_type'] == 'wzsd'){
                $sql1 = "select id from {$wpdb->prefix}user_post_use where user_id = {$current_user->ID} and type = 2";
                $use_id = $wpdb->get_var($sql1);
                //print_r($use_id);die;
                if($use_id){
                    $sql2 = "UPDATE {$wpdb->prefix}user_post_use SET post_id = if(post_id = '',{$_POST['post_id']},CONCAT_WS(',',post_id,{$_POST['post_id']})) WHERE user_id = {$current_user->ID} and type = 2";
                    //print_r($sql2);
                    $a = $wpdb->query($sql2);
                    //print_r($a);die;
                }else{
                    $wpdb->insert($wpdb->prefix.'user_post_use',array('user_id'=>$current_user->ID,'post_id'=>$_POST['post_id'],'type'=>2));
                }
            }*/
            $match_more = isset($_POST['match_more']) ? $_POST['match_more'] : 1;

            wp_send_json_success(array('info'=>__('提交成功', 'nlyd-student'),'url'=>home_url('trains/logs/id/'.$id.'/type/'.$_POST['project_type'].'/match_more/'.$match_more)));
        }else{
            wp_send_json_error(array('info'=>__('提交失败', 'nlyd-student')));
        }

    }

    /**
     * 用户修改语言
     */
    public function userUpdateLanguage(){
        $lang = trim($_POST['lang']);
        if(!$lang) wp_send_json_error(array('info'=>__('参数错误', 'nlyd-student')));
        global $current_user;

        $cookieBool = setcookie('user_language', $lang, time()+3600*24,'/');
        if(!$cookieBool) wp_send_json_error(array('info'=>__('修改失败', 'nlyd-student')));
//        print_r($_COOKIE);
        $current_user->ID > 0 && update_user_meta($current_user->ID, 'locale', $lang);
        wp_send_json_success(array('info'=>__('修改成功', 'nlyd-student')));
//        if($bool){
//            wp_send_json_success(array('info'=>__('修改成功', 'nlyd-student')));
//        }else{
//            wp_send_json_error(array('info'=>__('修改失败', 'nlyd-student')));
//        }
    }

    /**
     * 比赛奖金明细列表
     */
    public function matchBonusLists(){
        $match_id = isset($_POST['match_id']) ? intval($_POST['match_id']) : 0;
        if($match_id < 1) wp_send_json_error(['info' => __('参数错误', 'nlyd-student'),'is_admin'=>'false','is_data'=>'false','is_user_view'=>'false']);
        //是否是管理员
        global $wpdb,$current_user;
        $is_admin = in_array('administrator', $current_user->roles) ? 'true' : 'false';

        //断比赛是否结束
        $match = $wpdb->get_row('SELECT match_status FROM '.$wpdb->prefix.'match_meta_new WHERE match_id='.$match_id, ARRAY_A);
        if(!$match || $match['match_status'] != -3) wp_send_json_error(['info' => __('当前比赛未结束', 'nlyd-student'),'is_admin'=>$is_admin,'is_data'=>'false','is_user_view'=>'false']);

        if($is_admin == 'false'){
            $is_user_view = $wpdb->get_var("SELECT is_user_view FROM {$wpdb->prefix}match_bonus WHERE match_id={$match_id}");
            $is_user_view == 2 && wp_send_json_error(['info' => __('后台奖金核实中', 'nlyd-student'),'is_admin'=>$is_admin,'is_user_view'=>'false','is_data'=>'false']);
        }

        $page = isset($_POST['page']) ? intval($_POST['page']) : 0;
        $page < 1 && $page = 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        //查询列表
        $result = $wpdb->get_results("SELECT id,userID,is_send,real_name,is_user_view FROM {$wpdb->prefix}match_bonus AS mb 
                  WHERE match_id={$match_id} ORDER BY all_bonus DESC LIMIT {$start},{$pageSize}", ARRAY_A);
        if(!$result) wp_send_json_error(['info' => __('无数据', 'nlyd-student'),'is_admin'=>$is_admin,'is_data'=>'false','is_user_view'=>'false']);
        $start+=1;
        foreach ($result as $k => &$res){
            $res['is_send'] = $res['is_send'] == 2 ? $res['is_send'] = 'y' : 'n';
            $res['num'] = $start;
            $res['url'] = home_url('matchs/bonusDetail/id/'.$res['id']);
            ++$start;
            $is_user_view = $res['is_user_view'] == 1 ? 'true' : 'false';
        }
        wp_send_json_success(['info' => $result,'is_admin'=>$is_admin,'is_data'=>'true','is_user_view'=>$is_user_view]);
    }

    /**
     * 生成奖金明细
     */
    public function createBonus(){
        global $wpdb,$current_user;
        //判断是否是管理员
        if(empty($current_user->roles) || !in_array('administrator', $current_user->roles)) wp_send_json_error(['info' => __('权限不足', 'nlyd-student')]);

        $match_id = isset($_POST['match_id']) ? intval($_POST['match_id']) : 0;
        if($match_id < 1) wp_send_json_error(['info' => __('参数错误', 'nlyd-student')]);


        $match = $wpdb->get_row('SELECT match_status FROM '.$wpdb->prefix.'match_meta_new WHERE match_id='.$match_id, ARRAY_A);

        if(!$match || $match['match_status'] != -3) wp_send_json_error(['info' => __('当前比赛未结束', 'nlyd-student')]);


        //判断是否已经生成
        $row = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}match_bonus WHERE match_id='{$match_id}'", ARRAY_A);
        if($row) wp_send_json_error(['info' => __('当前比赛奖金已生成', 'nlyd-student')]);
        //获取模板
        //查找当前比赛奖金设置
        $bonusTmpId = get_post_meta($match_id,'match_income_detail',true);
        $bonusTmp = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}match_bonus_tmp WHERE id={$bonusTmpId}" ,ARRAY_A);
        if(!$bonusTmp) wp_send_json_error(['info' => __('未找到奖金设置模板', 'nlyd-student')]);

        //去生成
        $matchStudentObj = new Match_student();
        $allDatas = $matchStudentObj->getBonusData($match_id,$bonusTmp);
        if($allDatas['bool']) wp_send_json_success(['info' => __('生成成功', 'nlyd-student')]);
        else wp_send_json_error(['info' => __('生成失败!', 'nlyd-student')]);
    }

    /**
     * 下载奖金明细
     */
    public function downloadBonus(){
        global $current_user;
        //判断是否是管理员
        if(empty($current_user->roles) || !in_array('administrator', $current_user->roles)) wp_send_json_error(['info' => __('权限不足', 'nlyd-student')]);

        $match_id = isset($_POST['match_id']) ? intval($_POST['match_id']) : 0;
        if($match_id < 1) wp_send_json_error(['info' => __('参数错误', 'nlyd-student')]);
        wp_send_json_success(['info' => admin_url('admin.php?page=download&action=match_bonus&match_id='.$match_id)]);
    }

    /**
     * 允许选手查看奖金明细设置
     */
    public function isUserViewBonus(){
        global $wpdb,$current_user;
        //判断是否是管理员
        if(empty($current_user->roles) || !in_array('administrator', $current_user->roles)) wp_send_json_error(['info' => __('权限不足', 'nlyd-student')]);

        $match_id = isset($_POST['match_id']) ? intval($_POST['match_id']) : 0;
        $is_view = intval($_POST['is_view']);
        if($match_id < 1) wp_send_json_error(['info' => __('参数错误', 'nlyd-student')]);
        if($is_view != 1 && $is_view != 2) wp_send_json_error(['info' => __('参数错误', 'nlyd-student')]);

        //判断是否已经生成
        $rows = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}match_bonus WHERE match_id='{$match_id}'", ARRAY_A);
        if(!$rows) wp_send_json_error(['info' => __('当前比赛奖金未生成', 'nlyd-student')]);

        //开始修改
        $updateBool = $wpdb->update($wpdb->prefix.'match_bonus', ['is_user_view' => $is_view], ['match_id' => $match_id]);
        if(!$updateBool) wp_send_json_error(['info' => __('修改失败', 'nlyd-student')]);
        else wp_send_json_success(['info' => __('修改成功', 'nlyd-student')]);
    }

    /**
     * 监赛官上传证据
     *
     */
    public function upload_match_evidence(){
        global $current_user,$wpdb;

        if(empty($_POST['match_id'])) wp_send_json_error(array('info'=>'比赛必填'));
        if(empty($_POST['seat_number'])) wp_send_json_error(array('info'=>'座位号必填'));
        if(isset($_POST['id']) && !empty($_POST['id'])){
            if(empty($_POST['evidence'])) wp_send_json_error(array('info'=>'佐证照不能为空'));
        }
        else{
            if(empty($_FILES['evidence'])) wp_send_json_error(array('info'=>'佐证照不能为空'));

            //获取当前比赛项目
            $sql = "select match_id,project_id,more,start_time,end_time from {$wpdb->prefix}match_project_more where match_id = {$_POST['match_id']}";
            $rows = $wpdb->get_results($sql,ARRAY_A);
            if(!empty($rows)){
                $new_time = get_time('mysql');
                $match = array();
                foreach ($rows as $val){
                    if($val['start_time'] < $new_time && $new_time < $val['end_time']){
                        $match = $val;
                    }
                }
            }
            if(empty($match)) wp_send_json_error(array('info'=>'当前时间段无比赛'));

            //根据签到座位获取用户id
            $sql_ = "select user_id from {$wpdb->prefix}match_sign where match_id = {$_POST['match_id']} and seat_number = {$_POST['seat_number']}";
            $user_id = $wpdb->get_var($sql_);
            $user_real_name = get_user_meta($user_id,'user_real_name')[0];
            $real_name = $user_real_name['real_name'];

            //获取是否已上传监赛
            $log_id = $wpdb->get_var("select id from {$wpdb->prefix}prison_match_log where match_id = {$match['match_id']} and project_id = {$match['project_id']} and match_more = {$match['more']} and user_id = {$user_id} ");

            if(!empty($log_id)) wp_send_json_error(array('info'=>'本轮比赛已提交过监赛记录'));
        }

        if(isset($_FILES['evidence'])){
            $upload_dir = wp_upload_dir();
            $dir = '/evidence/';

            $num = 0;
            foreach ($_FILES['evidence']['tmp_name'] as $upd){
                $file = $this->saveIosFile($upd,$upload_dir['basedir'].$dir);
                if($file){
                    $_POST['evidence'][] = $upload_dir['baseurl'].$dir.$file;
                    ++$num;
                }
            }
        }

        if(isset($_POST['id']) && !empty($_POST['id'])){
            $update = array(
                'match_id'=>$_POST['match_id'],
                'supervisor_id'=>$current_user->ID,
                'student_name'=>$real_name,
                'seat_number'=>$_POST['seat_number'],
                'evidence'=>!empty($_POST['evidence']) ? json_encode($_POST['evidence']) : '',
                'describe'=>!empty($_POST['describe']) ? $_POST['describe'] : '',
            );
            $result = $wpdb->update($wpdb->prefix.'prison_match_log',$update,array('id'=>$_POST['id'],'supervisor_id'=>$current_user->ID));
        }else{
            $insert = array(
                'supervisor_id'=>$current_user->ID,
                'user_id'=>$user_id,
                'match_id'=>$match['match_id'],
                'project_id'=>$match['project_id'],
                'match_more'=>$match['more'],
                'match_id'=>$match['match_id'],
                'student_name'=>$real_name,
                'seat_number'=>$_POST['seat_number'],
                'evidence'=>!empty($_POST['evidence']) ? json_encode($_POST['evidence']) : '',
                'describe'=>!empty($_POST['describe']) ? $_POST['describe'] : '',
                'created_time'=>get_time('mysql'),
            );

            $result = $wpdb->insert($wpdb->prefix.'prison_match_log',$insert);

            $b = $wpdb->update($wpdb->prefix.'match_questions',array('is_true'=>2),array('match_id'=>$match['match_id'],'project_id'=>$match['project_id'],'match_more'=>$match['more'],'user_id'=>$user_id));

        }
        if($result){
            wp_send_json_success(array('info'=>'上传成功','url'=>home_url('supervisor/logs/')));
        }else{
            wp_send_json_error(array('info'=>'上传失败'));
        }
    }


    /**
     * 删除监赛记录
     */
    public function remove_prison_match_log(){
        global $wpdb;

        $row = $wpdb->get_row("select * from {$wpdb->prefix}prison_match_log where id = {$_POST['id']}",ARRAY_A);
        if(empty($row)) wp_send_json_error(array('数据错误'));

        $result = $wpdb->delete($wpdb->prefix.'prison_match_log',array('id'=>$_POST['id']));

        $b = $wpdb->update($wpdb->prefix.'match_questions',array('is_true'=>1),array('match_id'=>$row['match_id'],'project_id'=>$row['project_id'],'match_more'=>$row['match_more'],'user_id'=>$row['user_id']));
        if($result){
            wp_send_json_success(array('info'=>'删除成功'));
        }else{
            wp_send_json_error(array('info'=>'删除失败'));
        }
    }

    /**
     * 获取考级
     */
    public function get_grading_logs(){
        global $wpdb,$current_user;
        $map = array();
        $map[] = " a.post_status = 'publish' ";
        $map[] = " a.post_type = 'grading' ";

        //判断往期/近期
        if( isset($_POST['match_type']) && $_POST['match_type'] =='history' ){
            $map[] = " b.status = -3 ";     //历史
            $order = ' b.start_time desc ';

        }elseif (isset($_POST['match_type']) && $_POST['match_type'] =='signUp'){
            $map[] = " b.status = 1 ";     //报名中
            $order = ' b.entry_end_time asc ';
        }
        else{
            $map[] = " (b.status = -2  or b.status = 2) ";    //比赛
            $order = ' b.start_time asc ';
        }


        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        $where = join(' and ',$map);

        $sql = "select SQL_CALC_FOUND_ROWS a.ID,a.post_title,
                a.post_content,b.grading_notice_url,b.created_person,
                DATE_FORMAT(b.start_time,'%Y-%m-%d %H:%i') start_time,
                if(b.address = '','--',b.address) address,
                b.cost,b.entry_end_time,b.status ,c.user_id
                from {$wpdb->prefix}posts a
                left join {$wpdb->prefix}grading_meta b on a.ID = b.grading_id
                left join {$wpdb->prefix}order c on a.ID = c.match_id and c.user_id = {$current_user->ID} and (c.pay_status=2 or c.pay_status=3 or c.pay_status=4) 
                where {$where} order by {$order} limit $start,$pageSize;
                ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无考级', 'nlyd-student')));
        foreach ($rows as $k => $val){

            //获取办赛机构
            $zone_meta = $wpdb->get_row("select id,if(zone_match_type=1,'战队精英赛','城市赛') as match_type,zone_city,zone_name from {$wpdb->prefix}zone_meta where user_id = {$val['created_person']}",ARRAY_A);
            if(!empty($zone_meta)){
                $meta = $zone_meta['zone_city'].$zone_meta['match_type'].'组委会';
                //print_r($meta);
            }
            $rows[$k]['zone'] = empty($zone_meta) ? '' : $meta;

            //获取参赛须知
            $rows[$k]['match_notice_url'] = !empty($val['match_notice_url']) ? $val['match_notice_url'] : '';

            //获取报名人数
            $sql_ = "select count(a.id) total 
                      from {$wpdb->prefix}order a 
                      right join {$wpdb->prefix}users b on a.user_id = b.ID
                      where match_id = {$val['ID']} and pay_status in(2,3,4) and order_type=2";

            //print_r($sql_);
            $row = $wpdb->get_row($sql_,ARRAY_A);
            $rows[$k]['entry_total'] = !empty($row['total']) ? $row['total'] : 0;
            //两个链接
            if($val['status'] == 2){
                //比赛中
                $url = home_url('gradings/matchWaitting/grad_id/'.$val['ID']);
                $button_title = __('进入考级', 'nlyd-student');
                $rows[$k]['match_status_cn'] = __('考级中', 'nlyd-student');
            }
            else if ($val['status'] == 1){
                //报名中
                $url = home_url('gradings/confirm/grad_id/'.$val['ID']);
                $button_title = __('考级报名', 'nlyd-student');
                $rows[$k]['match_status_cn'] = __('报名中', 'nlyd-student');
            }
            else if ($val['status'] == -1){
                //未开始
                $url = '';
                $rows[$k]['match_status_cn'] = __('未开始', 'nlyd-student');
            }
            else if($val['status'] == -3){
                //已结束
                $url = '';
                $rows[$k]['match_status_cn'] = __('已结束', 'nlyd-student');
            }
            else{
                //等待开赛
                $url = home_url('gradings/matchWaitting/grad_id/'.$val['ID']);
                $button_title = __('等待考级', 'nlyd-student');
                $rows[$k]['match_status_cn'] = __('等待考级', 'nlyd-student');
            }
            $rows[$k]['match_status'] = $val['status'];
            $rows[$k]['button_title'] = $button_title;
            $rows[$k]['right_url'] = $url;
            $rows[$k]['left_url'] = home_url('gradings/info/grad_id/'.$val['ID']);

            if($_POST['match_type'] =='history'){
                $button_title = __('查看排名', 'nlyd-student');
                $rows[$k]['right_url'] = home_url('gradings/record/grad_id/'.$val['ID']);
            }
        }
        //print_r($rows);
        wp_send_json_success(array('info'=>$rows));
    }

    /**
     * 根据座位号获取用户
     */
    public function get_student_name(){
        global $wpdb;
        if(empty($_POST['match_id']) || empty($_POST['seat_number'])) wp_send_json_error(array('info'=>'比赛/座位号不能为空'));
        $sql = "select user_id from {$wpdb->prefix}match_sign where match_id = {$_POST['match_id']} and seat_number = {$_POST['seat_number']}";
        $user_id = $wpdb->get_var($sql);

        if(empty($user_id)) wp_send_json_error(array('info'=>'未检测到当前座位号用户'));
        $user_real_name = get_user_meta($user_id,'user_real_name')[0];
        //print_r($user_real_name);
        if(empty($user_real_name['real_name'])) wp_send_json_error(array('info'=>'真实姓名获取失败'));

        wp_send_json_success(array('info'=>$user_real_name['real_name']));

    }

    /**
     * 更改奖金发放状态
     */
    public function updateSendStatus(){
        global $current_user,$wpdb;
        if(empty($current_user->roles) || !in_array('administrator', $current_user->roles)) wp_send_json_error(['info' => __('权限不足', 'nlyd-student')]);

        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $match_id = isset($_POST['match_id']) ? intval($_POST['match_id']) : 0;
        if($user_id < 1 || $match_id < 1) wp_send_json_error(['info' => '参数错误']);
        //查看当前记录
        $sql1 = "SELECT id,is_send FROM {$wpdb->prefix}match_bonus WHERE match_id='{$match_id}' AND user_id={$user_id}";
        $row = $wpdb->get_row($sql1,ARRAY_A);
        if(!$row) wp_send_json_error(['info' => '未找到数据,请刷新再试!']);
        switch ($row['is_send']){
            case 1:
                $status = 2;
                break;
            case 2:
                $status = 1;
                break;
            default:
                wp_send_json_error(['info' => '未找到数据,请刷新再试!']);
        }
        $send_time = $status === 2 ? get_time('mysql') : '';
        $sql = "UPDATE {$wpdb->prefix}match_bonus SET is_send='{$status}',`send_time`='{$send_time}' WHERE match_id='{$match_id}' AND user_id={$user_id}";
//        $bool = $wpdb->update($wpdb->prefix.'match_bonus', ['is_send' => $status], ['user_id'=>$user_id,'match_id'=>$match_id]);
        $bool = $wpdb->query($sql);
        if($bool) wp_send_json_success(['info'=>'修改成功']);
        else wp_send_json_error(['info' => '修改失败']);
    }

    /**
     * 考级答案提交
     */
    public function grading_answer_submit(){
        unset($_SESSION['count_down']);
        unset($_SESSION['grading_post_id']);
        if($_POST['questions_type'] == 'wz'){
            if(empty($_POST['questions_answer'])) wp_send_json_error(array('数据信息不能为空'));
        }else{

            if(empty($_POST['grading_id']) || empty($_POST['grading_type']) || empty($_POST['questions_type']) || empty($_POST['grading_questions']) || empty($_POST['questions_answer'])){
                wp_send_json_error(array('所提交数据信息不完全'));
            }
        }
        ini_set('post_max_size','20M');
        $_SESSION['match_data'] = $_POST;

        global $wpdb,$current_user;

        //查看答案是否提交
        if($_POST['more']>0){
            $where = " and more = {$_POST['more']} ";
        }
        $sql = "select id,questions_answer
                from {$wpdb->prefix}grading_questions
                where user_id = {$current_user->ID} and grading_id = {$_POST['grading_id']} and grading_type = '{$_POST['grading_type']}' and questions_type = '{$_POST['questions_type']}' {$where}
                ";
        //print_r($sql);die;
        $row = $wpdb->get_row($sql,ARRAY_A);
        //print_r($sql);
        $url = home_url('gradings/answerLog/grad_id/'.$_POST['grading_id'].'/log_id/'.$row['id'].'/grad_type/'.$_POST['grading_type'].'/type/'.$_POST['questions_type']);
        if(!empty($row)) wp_send_json_success(array('info'=>__('答案已提交', 'nlyd-student'),'url'=>$url));

        //数据处理
        $correct_rate = 0;  //准确率
        switch ($_POST['grading_type']){
            case 'memory':
                switch ($_POST['questions_type']){
                    case 'sz':
                    case 'cy':
                    case 'yzl':
                    case 'tl':
                    case 'zm':
                        if(!empty($_POST['my_answer'])){
                            //print_r($_POST['questions_answer']);die;
                            $len = count($_POST['questions_answer']);
                            $error_len = count(array_diff_assoc($_POST['questions_answer'],$_POST['my_answer']));   //$_POST['my_answer']
                            $correct_rate = ($len-$error_len)/$len;
                        }
                        break;
                    case 'rm':
                        if(!empty($_POST['my_answer'])){

                            $len = count($_POST['questions_answer']);
                            $my_answer = $_POST['my_answer'];
                            $questions_answer = $_POST['questions_answer'];
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

                        $questions_answer = $_POST['questions_answer'];
                        $len = 0;
                        $error_len = 0;
                        foreach ($questions_answer as $k =>$v){
                            $len += count($v['rights']);
                            $error_len += count(array_diff_assoc($v['rights'],$v['yours']));
                        }
                        $_POST['grading_questions'] = array_column($questions_answer,'question');
                        $_POST['questions_answer'] = array_column($questions_answer,'rights');
                        $_POST['my_answer'] = array_column($questions_answer,'yours');
                        $correct_rate = ($len-$error_len)/$len;
                        break;
                }
                break;
            case 'reading':

                $questions_answer = $_POST['questions_answer'];
                $len = count($questions_answer);
                $success_len = 0;

                foreach ($questions_answer as $k=>$val){
                    $arr = array();
                    foreach ($val['problem_answer'] as $key => $v){
                        if($v == 1){
                            $arr[] = $key;
                        }
                    }

                    if(isset($_POST['my_answer'][$k])){
                        if(arr2str($arr) == arr2str($_POST['my_answer'][$k])) ++$success_len;
                    }
                }
                $correct_rate = $success_len/$len;
                //print_r($success_len);die;
                break;
            case 'arithmetic':

                $data_arr = $_POST['my_answer'];
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
                //print_r($my_score);die;
                $_POST['grading_questions'] = $match_questions;
                $_POST['questions_answer'] = $questions_answer;
                $correct_rate = $success_len/$len;
                //var_dump($_POST);die;
                break;
        }
        //zlin_user_skill_rank 技能表
        //print_r($correct_rate);die;
        //$correct_rate = 1;
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
        /*print_r($result);
        die;*/
        if($result){

            $log_id = $wpdb->insert_id;

            if(!empty($_POST['post_id']) && $_POST['grading_type'] == 'reading'){

                $sql1 = "select id from {$wpdb->prefix}user_post_use where user_id = {$current_user->ID} and type = 1 ";
                $use_id = $wpdb->get_row($sql1,ARRAY_A);
                if($use_id){
                    $sql2 = "UPDATE {$wpdb->prefix}user_post_use SET post_id = if(post_id = '',{$_POST['post_id']},CONCAT_WS(',',post_id,{$_POST['post_id']})) WHERE user_id = {$current_user->ID} and type = 1";
                    $a = $wpdb->query($sql2);
                }else{

                    $a = $wpdb->insert($wpdb->prefix.'user_post_use',array('user_id'=>$current_user->ID,'post_id'=>$_POST['post_id'],'type'=>1));
                }

            }

            wp_send_json_success(array('info'=>__('提交完成', 'nlyd-student'),'url'=>home_url('gradings/answerLog/grad_id/'.$_POST['grading_id'].'/log_id/'.$log_id.'/grad_type/'.$_POST['grading_type'].'/type/'.$_POST['questions_type'])));
        }
        else{
            wp_send_json_error(array('info' => __('提交失败', 'nlyd-student')));
        }

    }

    /**
     * 考级训练答案提交
     */
    public function grade_answer_submit(){
        unset($_SESSION['count_down']);
        unset($_SESSION['grade_post_id']);
        if($_POST['questions_type'] == 'wz'){
            if(empty($_POST['questions_answer'])) wp_send_json_error(array('数据信息不能为空'));
        }else{

            if(empty($_POST['genre_id']) || empty($_POST['grading_type']) || empty($_POST['questions_type']) || empty($_POST['grading_questions']) || empty($_POST['questions_answer'])){
                wp_send_json_error(array('所提交数据信息不完全'));
            }
        }
        $_SESSION['match_data'] = $_POST;
        ini_set('post_max_size','20M');

        global $wpdb,$current_user;
        $sql1 = "select * from {$wpdb->prefix}user_grade_logs 
                          where grade_log_id = {$_POST['history_id']} 
                          and grading_type = '{$_POST['grading_type']}' 
                          and questions_type = '{$_POST['questions_type']}' 
                          and user_id = {$current_user->ID} ";
        //print_r($sql1);
        $row_1 = $wpdb->get_row($sql1,ARRAY_A);
        $url = home_url('grade/answerLog/grad_id/'.$_POST['grading_id'].'/log_id/'.$row_1['id'].'/grad_type/'.$_POST['grad_type'].'/type/'.$_POST['questions_type']);
        if(!empty($row_1)) wp_send_json_success(array('info'=>__('答案已提交', 'nlyd-student'),'url'=>$url));

        //print_r($_POST);die;
        //数据处理
        $correct_rate = 0;  //准确率
        switch ($_POST['grading_type']){
            case 'memory':
                switch ($_POST['questions_type']){
                    case 'sz':
                    case 'cy':
                    case 'yzl':
                    case 'zm':
                    case 'tl':
                        if(!empty($_POST['my_answer'])){

                            $len = count($_POST['questions_answer']);
                            $error_len = count(array_diff_assoc($_POST['questions_answer'],$_POST['my_answer']));
                            $correct_rate = ($len-$error_len)/$len;
                        }
                        break;
                    case 'rm':
                        if(!empty($_POST['my_answer'])){

                            $len = count($_POST['questions_answer']);
                            $my_answer = $_POST['my_answer'];
                            $questions_answer = $_POST['questions_answer'];
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

                        $questions_answer = $_POST['questions_answer'];
                        $len = 0;
                        $error_len = 0;
                        foreach ($questions_answer as $k =>$v){
                            $len += count($v['rights']);
                            $error_len += count(array_diff_assoc($v['rights'],$v['yours']));
                        }
                        //print_r($correct_rate);die;
                        $_POST['grading_questions'] = array_column($questions_answer,'question');
                        $_POST['questions_answer'] = array_column($questions_answer,'rights');
                        $_POST['my_answer'] = array_column($questions_answer,'yours');
                        $correct_rate = ($len-$error_len)/$len;
                        break;
                }
                break;
            case 'reading':
                //print_r($_POST);die;
                $questions_answer = $_POST['questions_answer'];
                $len = count($questions_answer);
                $success_len = 0;

                foreach ($questions_answer as $k=>$val){
                    $arr = array();
                    foreach ($val['problem_answer'] as $key => $v){
                        if($v == 1){
                            $arr[] = $key;
                        }
                    }

                    if(isset($_POST['my_answer'][$k])){
                        if(arr2str($arr) == arr2str($_POST['my_answer'][$k])) ++$success_len;
                    }
                }
                $correct_rate = $success_len/$len;
                //print_r($success_len);die;
                break;
            case 'arithmetic':

                $data_arr = $_POST['my_answer'];
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
                //print_r($my_score);die;
                $_POST['grading_questions'] = $match_questions;
                $_POST['questions_answer'] = $questions_answer;
                $correct_rate = $success_len/$len;
                //var_dump($_POST);die;
                break;
        }

        $insert = array(
            'user_id'=>$current_user->ID,
            'grade_log_id'=>$_POST['history_id'],
            'grading_type'=>$_POST['grading_type'],
            'questions_type'=>$_POST['questions_type'],
            'grading_questions'=>json_encode($_POST['grading_questions']),
            'questions_answer'=>json_encode($_POST['questions_answer']),
            'my_answer'=>json_encode($_POST['my_answer']),
            'correct_rate'=>$correct_rate,
            'my_score'=>$my_score,
            'created_time'=>get_time('mysql'),
            'use_time'=>isset($_POST['usetime']) ? $_POST['usetime'] : '',
            'post_id'=>isset($_POST['post_id']) ? $_POST['post_id'] : '',
            'post_str_length'=>isset($_POST['length']) ? $_POST['length'] : '',
        );
        //print_r($insert);die;
        $result = $wpdb->insert($wpdb->prefix.'user_grade_logs',$insert);
        /*print_r($result);
        die;*/
        if($result){

            $log_id = $wpdb->insert_id;

            /*if(!empty($_POST['post_id']) && $_POST['grading_type'] == 'reading'){

                $sql1 = "select id from {$wpdb->prefix}user_post_use where user_id = {$current_user->ID} and type = 2 ";
                $use_id = $wpdb->get_row($sql1,ARRAY_A);
                if($use_id){
                    $sql2 = "UPDATE {$wpdb->prefix}user_post_use SET post_id = if(post_id = '',{$_POST['post_id']},CONCAT_WS(',',post_id,{$_POST['post_id']})) WHERE user_id = {$current_user->ID} and type = 2";
                    $a = $wpdb->query($sql2);
                }else{

                    $a = $wpdb->insert($wpdb->prefix.'user_post_use',array('user_id'=>$current_user->ID,'post_id'=>$_POST['post_id'],'type'=>2));
                }

            }*/

            wp_send_json_success(array('info'=>__('提交完成', 'nlyd-student'),'url'=>home_url('grade/answerLog/genre_id/'.$_POST['genre_id'].'/history_id/'.$_POST['history_id'].'/log_id/'.$log_id.'/grad_type/'.$_POST['grading_type'].'/type/'.$_POST['questions_type'].'/memory_lv/'.$_POST['memory_lv'])));
        }
        else{
            wp_send_json_error(array('info' => __('提交失败', 'nlyd-student')));
        }

    }

    /**
     * 用户推广码生成
     *
     */
    public function qrcode($type=''){

        global $current_user;
        $upload_dir = wp_upload_dir();
        $spread_qrcode = get_user_meta($current_user->ID,'referee_qrcode');

        if(!empty($spread_qrcode) && file_exists($upload_dir['basedir'].$spread_qrcode[0])){
            if($type=='user'){
                return $upload_dir['baseurl'].$spread_qrcode[0];
            }
            wp_send_json_success($upload_dir['baseurl'].$spread_qrcode[0]);
        }else{

            include_once leo_student_path."library/Vendor/phpqrcode/phpqrcode.php"; //引入PHP QR库文件
            $value=home_url('/logins/index/referee_id/'.$current_user->ID);
            $dir = '/referee/'.$current_user->ID.'/';
            $path = $upload_dir['basedir'].$dir;
            if(!file_exists($path)){
                mkdir($path,0755,true);
            }
            $filename = date('YmdHis').'_'.rand(1000,9999).'.jpg';          //定义图片名字及格式
            $qrcode_path = $path.$filename;

            $errorCorrectionLevel = "L"; //容错级别
            $matrixPointSize = "6"; //生成图片大小
            QRcode::png($value, $qrcode_path, $errorCorrectionLevel, $matrixPointSize, 2);
            //生成带logo的二维码
            /*$logo = leo_student_path.'Public/css/image/logo1.jpg';
            //svar_dump(file_exists($logo));die;
            //die;
            if (file_exists($logo)) {
                $QR = imagecreatefromstring ( file_get_contents ( $qrcode_path ) );
                $logo = imagecreatefromstring ( file_get_contents ( $logo ) );
                $QR_width = imagesx ( $QR );
                $QR_height = imagesy ( $QR );
                $logo_width = imagesx ( $logo );
                $logo_height = imagesy ( $logo );
                $logo_qr_width = $QR_width / 5;
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                $from_width = ($QR_width - $logo_qr_width) / 2;
                imagecopyresampled ( $QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height );
            }
            imagejpeg ( $QR, $qrcode_path );//带Logo二维码的文件名
            //die;
            $back = leo_student_path.'Public/css/image/test.jpg';
            if (file_exists($back)) {
                $back_ = imagecreatefromstring ( file_get_contents ( $back ) );
                $qrcode = imagecreatefromstring ( file_get_contents ( $qrcode_path ) );
                // $back_width = imagesx ( $back_ );
                //$back_height = imagesy ( $back_ );
                $qrcode_width = imagesx ( $qrcode );
                $qrcode_height = imagesy ( $qrcode );
                $logo_qr_width = $QR_width / 5;
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                $from_width = ($QR_width - $logo_qr_width) / 2;
                imagecopyresampled ( $back_, $qrcode, 95, 195, 0, 0, $qrcode_width, $qrcode_height, $qrcode_width, $qrcode_height );
            }
            imagejpeg ( $back_, $qrcode_path );//带Logo二维码的文件名*/
            update_user_meta($current_user->ID,'referee_qrcode',$dir.$filename);
            if($type=='user'){
                //var_dump($upload_dir['baseurl'].$spread_qrcode[0]);die;
                return $upload_dir['baseurl'].$dir.$filename;
            }
            wp_send_json_success($upload_dir['baseurl'].$dir.$filename);
        }
    }

    /**
     * 生成比赛签到码
     */
    public function match_sign_code(){

        if(empty($_POST['match_id'])) wp_send_json_error(array('info'=>__('id不能为空')));

        $upload_dir = wp_upload_dir();

        $dir = '/sign-code/'.$_POST['match_id'].'/';
        $path = $upload_dir['basedir'].$dir;
        $filename = 'sign-'.$_POST['match_id'].'.jpg';          //定义图片名字及格式

        if(file_exists($path.$filename)){
            wp_send_json_success($upload_dir['baseurl'].$dir.$filename);
        }
        include_once leo_student_path."library/Vendor/phpqrcode/phpqrcode.php"; //引入PHP QR库文件
        $value=home_url('/signs/index/match_id/'.$_POST['match_id']);


        if(!file_exists($path)){
            mkdir($path,0755,true);
        }
        $qrcode_path = $path.$filename;

        $errorCorrectionLevel = "L"; //容错级别
        $matrixPointSize = "6"; //生成图片大小
        QRcode::png($value, $qrcode_path, $errorCorrectionLevel, $matrixPointSize, 2);
        wp_send_json_success($upload_dir['baseurl'].$dir.$filename);
    }

    /**
     * 生成课程码
     */
    public function course_sign_code(){

        if(empty($_POST['course_id'])) wp_send_json_error(array('info'=>__('id不能为空')));
        global $current_user;
        $upload_dir = wp_upload_dir();

        $dir = '/course-code/'.$_POST['course_id'].'/';
        $path = $upload_dir['basedir'].$dir;
        $filename = 'sign-'.$_POST['course_id'].'.jpg';          //定义图片名字及格式

        if(file_exists($path.$filename)){
            wp_send_json_success($upload_dir['baseurl'].$dir.$filename);
        }
        include_once leo_student_path."library/Vendor/phpqrcode/phpqrcode.php"; //引入PHP QR库文件
        $value=home_url('courses/courseDetail/center_id/'.$current_user->ID.'/id/'.$_POST['course_id']);


        if(!file_exists($path)){
            mkdir($path,0755,true);
        }
        $qrcode_path = $path.$filename;

        $errorCorrectionLevel = "L"; //容错级别
        $matrixPointSize = "6"; //生成图片大小
        QRcode::png($value, $qrcode_path, $errorCorrectionLevel, $matrixPointSize, 2);
        wp_send_json_success($upload_dir['baseurl'].$dir.$filename);
    }

    /**
     * 机构申请资料提交
     */
    public function zone_apply_submit(){
        global $wpdb,$current_user;
        ini_set('post_max_size','30M');
        //print_r($_POST);die;
        //$_POST['zone_match_address'] = '江苏省-南京市-建邺区';
        if($_POST['zone_id'] > 0){
            $row = $wpdb->get_row("select id,user_status from {$wpdb->prefix}zone_meta where id = {$_POST['zone_id']}",ARRAY_A);
            if($row['user_status']== -1){
                //wp_send_json_error(array('info'=>'资料审核中,禁止修改'));
            }
            if($row['user_status']== 1){
                wp_send_json_error(array('info'=>'审核已通过,资料禁止修改'));
            }
        }
        if(empty($_POST['type_id']) || empty($_POST['zone_type_alias']) || empty($_POST['zone_address']) || empty($_POST['bank_card_name']) || empty($_POST['legal_person']) ||  empty($_POST['opening_bank']) || empty($_POST['bank_card_num'])){
            wp_send_json_error(array('info'=>'相关资料不能有空值'));
        }
        if($_POST['zone_type_alias'] == 'match'){
            if(empty($_POST['zone_match_type'])) wp_send_json_error(array('info'=>'赛区类型必选'));

            if($_POST['zone_match_type'] == 1){
                $zone_match_type = 1;
            }else{
                $zone_match_type = 2;
            }
            if($_POST['zone_match_type'] == 2){ //单区
                $is_double = 2;
            }
            if($_POST['zone_match_type'] == 3){ //多区
                $is_double = 1;
            }
        }
        //print_r($is_double);
        if(empty($_POST['business_licence_url'])){
            if(empty($_POST['business_licence'])){
                wp_send_json_error(array('info'=>'营业执照必传'));
            }
        }else{
            $business_licence_url = $_POST['business_licence_url'];
        }
        if($_POST['type_id'] == 1 || $_POST['type_id'] == 3 ){

            $manager = $_POST['zone_type_alias'] == 'match' ? '中心负责人' : '总经理';
            if(empty($_POST['center_manager'])) wp_send_json_error(array('info'=>__($manager.'未进行确认')));
            $sql1 = "select a.ID,b.meta_value from {$wpdb->prefix}users a 
                left join {$wpdb->prefix}usermeta b on a.ID = b.user_id and b.meta_key = 'user_real_name'
                where a.ID = '{$_POST['center_manager']}'
                ";
            $center_manager = $wpdb->get_row($sql1,ARRAY_A);
            if(empty($center_manager)) wp_send_json_error(array('info'=>__('该'.$manager.'未注册')));
            $manager_real_name = unserialize($center_manager['meta_value']);
            if(empty($manager_real_name['real_name'])) wp_send_json_error(array('info'=>__('该'.$manager.'未实名认证')));
        }

        if($_POST['type_id'] == 3 && $_POST['zone_type_alias'] == 'match'){    //赛区
            if(empty($_POST['chairman_phone']) || empty($_POST['secretary_phone'])){
                wp_send_json_error(array('info'=>'组委会主席或者秘书长未进行确认'));
            }

            $sql = "select a.ID,b.meta_value from {$wpdb->prefix}users a 
                left join {$wpdb->prefix}usermeta b on a.ID = b.user_id and b.meta_key = 'user_real_name'
                where a.ID = '{$_POST['chairman_phone']}'
                ";
            $chairman = $wpdb->get_row($sql,ARRAY_A);
            if(empty($chairman)) wp_send_json_error(array('info'=>__('该组委会主席未注册')));
            $chairman_real_name = unserialize($chairman['meta_value']);
            if(empty($chairman_real_name['real_name'])) wp_send_json_error(array('info'=>__('该组委会主席未实名认证')));

            $sql_ = "select a.ID,b.meta_value from {$wpdb->prefix}users a 
                left join {$wpdb->prefix}usermeta b on a.ID = b.user_id and b.meta_key = 'user_real_name'
                where a.ID = '{$_POST['secretary_phone']}'
                ";
            $secretary = $wpdb->get_row($sql_,ARRAY_A);
            if(empty($secretary)) wp_send_json_error(array('info'=>__('该秘书长未注册')));
            $secretary_real_name = unserialize($secretary['meta_value']);
            if(empty($secretary_real_name['real_name'])) wp_send_json_error(array('info'=>__('该秘书长未实名认证')));

        }
        //print_r($_POST);die;
        if(!empty($_POST['business_licence'])){

            $upload_dir = wp_upload_dir();
            $dir = '/business_licence/'.$current_user->ID.'/';
            $file = $this->base64file($_POST['business_licence'],$upload_dir['basedir'].$dir);
            if($file){
                $business_licence_url = $upload_dir['baseurl'].$dir.'/'.$file;
            }
        }
        //获取默认权限
        $role = $wpdb->get_results("select role_id from {$wpdb->prefix}zone_join_role where zone_type_id = {$_POST['type_id']} ",ARRAY_A);
        $role_id = arr2str(array_column($role,'role_id'));

        $match_role= $wpdb->get_results("select match_role_id from {$wpdb->prefix}zone_join_match_role where zone_type_id = {$_POST['type_id']} ",ARRAY_A);
        $match_role_id = arr2str(array_column($match_role,'match_role_id'));

        //print_r($_POST);die;
        $data = array(
            'apply_id'=>$current_user->ID,
            'type_id'=>$_POST['type_id'],
            'zone_match_type'=>!empty($zone_match_type) ? $zone_match_type : '',
            'is_double'=>!empty($is_double) ? $is_double : '',
            'zone_address'=>$_POST['zone_address'],
            'business_licence_url'=>$business_licence_url,
            'legal_person'=>$_POST['legal_person'],
            'zone_city'=>$_POST['zone_match_address'],
            'zone_name'=>!empty($_POST['zone_name']) ? $_POST['zone_name']:'',
            'bank_card_name'=>$_POST['bank_card_name'],
            'opening_bank'=>$_POST['opening_bank'],
            'opening_bank_address'=>!empty($_POST['opening_bank_address']) ? $_POST['opening_bank_address'] : '',
            'bank_card_num'=>$_POST['bank_card_num'],
            'chairman_id'=>!empty($chairman) ? $chairman['ID'] : '',
            'secretary_id'=>!empty($secretary) ? $secretary['ID'] : '',
            'center_manager_id'=>!empty($center_manager) ? $center_manager['ID'] : '',
            'referee_id'=>$current_user->data->referee_id,
            'user_status'=>-1,
            'role_id'=>$role_id,
            'match_role_id'=>$match_role_id,
            'created_time'=>get_time('mysql'),
        );

        //print_r($data);die;
        if(empty($row)){
            $result = $wpdb->insert($wpdb->prefix.'zone_meta',$data);
        }else{
            $result = $wpdb->update($wpdb->prefix.'zone_meta',$data,array('id'=>$row['id']));
        }
        if($result){
            wp_send_json_success(array('info'=>'提交成功,等待管理员审核','url'=>home_url('/zone/applySuccess/type_id/'.$_POST['type_id'])));
        }
        else{
            wp_send_json_error(array('info'=>'提交失败,请联系管理员'));
        }

    }

    //根据手机或者真实姓名获取用户
    public function get_manage_user(){
        if(isset($_GET['term'])){
            $map[] = " (a.user_login like '%{$_GET['term']}%') ";
            $map[] = " (a.user_mobile like '%{$_GET['term']}%') ";
            $map[] = " (a.user_email like '%{$_GET['term']}%') ";
            $map[] = " (b.meta_value like '%{$_GET['term']}%') ";
            $where = 'and ('.join( ' or ',$map) .')';
        }

        global $wpdb;
        $sql = "select b.user_id as id, 
                case 
                when a.user_login != '' then a.user_login
                when a.user_mobile != '' then a.user_mobile
                when a.user_email != '' then a.user_email
                else a.user_nicename
                end as text,b.meta_value 
                from {$wpdb->prefix}users a 
                left join {$wpdb->prefix}usermeta b on a.ID = b.user_id and meta_key = 'user_real_name'
                where a.ID > 0 and b.meta_value is not null {$where}
                limit 20
                ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($rows)){

            foreach ($rows as $k => $v){
                if(!empty($v['meta_value'])){
                    $meta = unserialize($v['meta_value']);
                    if(!empty($meta)){
                        $rows[$k]['text'] = $meta['real_name'];
                    }

                }
            }
            wp_send_json_success($rows);
        }else{
            wp_send_json_error('');
        }

    }


    /**
     * 获取用户收益记录
     */
    public function get_user_profit_logs(){
        global $wpdb,$current_user;

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        if($_POST['map'] == 'all'){ //全部
            $where = "user_id = {$current_user->ID}";
        }elseif ($_POST['map'] == 'extract'){ //提现
            $where = " user_id = {$current_user->ID} and user_income < 0 ";
        }else{  //收益
            $where = "user_id = {$current_user->ID} and user_income > 0 ";
        }

        //获取收益列表
        $sql = " select id,date_format(created_time,'%Y/%m/%d %H:%i') created_time,income_type,user_income,match_id,
                  if(income_status <> 2 ,'待到账' ,'已到账') income_status,
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
                  where {$where}
                  order by created_time desc limit $start,$pageSize 
                  ";
        //print_r($sql);die;
        $rows = $wpdb->get_results($sql,ARRAY_A);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无记录', 'nlyd-student')));
        foreach ($rows as $k =>$v){
            if($v['income_type'] == 'undertake'){
                $type = $wpdb->get_var("select case order_type when 1 then '开设比赛' when 2 then '开设考级' end order_type from {$wpdb->prefix}order order_type where match_id = {$v['match_id']}");
                if(!empty($type)){
                    $rows[$k]['income_type_title'] = $type;
                }
            }
        }

        wp_send_json_success(array('info'=>$rows));
    }

    /**
     *机构生成比赛
     */
    public function zone_create_match(){

        if(empty($_POST['post_title']) ||empty($_POST['match_scene']) || empty($_POST['match_genre']) || empty($_POST['match_address']) || empty($_POST['match_start_time']) ){
            wp_send_json_error(array('info'=>'比赛场景/类型/名称/地点/时间为必填项'));
        }
        if($_POST['entry_end_time'] > $_POST['match_start_time'] )wp_send_json_error(array('info'=>'报名结束时间必须大于开始时间'));
        global $wpdb,$current_user;

        //print_r($_POST);die;
        /***********************准备数据**********************************/
        $arr = array(
            'post_title' => $_POST['post_title'],
            'post_type'     => 'match',
            'post_status' => 'publish',
            'post_author' => $current_user->ID,
        );

        $wpdb->query('START TRANSACTION');

        //获取所有比赛项目
        $project_array = $wpdb->get_results("select ID from {$wpdb->prefix}posts where post_type = 'project' and post_status = 'publish' order by menu_order asc",ARRAY_A);
        $project_array = array_column($project_array,'ID');
        //print_r($project_array);
        $project_id = arr2str($project_array);

        if(!empty($_POST['match_id'])){
            $wpdb->update($wpdb->prefix.'posts',$arr,array('ID'=>$_POST['match_id']));
            $new_page_id = $_POST['match_id'];
            //获取比赛开赛时间
            $match_start_time = $wpdb->get_var("select match_start_time from {$wpdb->prefix}match_meta_new where match_id = {$_POST['match_id']} ");
            if($match_start_time != $_POST['match_start_time']){
                $wpdb->delete($wpdb->prefix.'match_project_more',array('match_id'=>$_POST['match_id']));
            }else{
                $b = true;
            }
        }else{
            $new_page_id = wp_insert_post($arr);
        }

        $match_meta = array(
            'match_id'=>$new_page_id,
            'match_scene'=>$_POST['match_scene'],
            'match_genre'=>$_POST['match_genre'],
            'match_address'=>$_POST['match_address'],
            'match_cost'=>$_POST['match_cost'],
            'entry_end_time'=>!empty($_POST['entry_end_time']) ? $_POST['entry_end_time'] : date_i18n('Y-m-d H:i:s',strtotime('-10 minute',strtotime($_POST['match_start_time']))),
            'match_start_time'=>$_POST['match_start_time'],
            'match_project_id'=>$project_id,
        );
        if(!empty($_POST['match_id'])){

            $match_meta['revise_id'] = $current_user->ID;
            $match_meta['revise_time'] = get_time('mysql');
            $a = $wpdb->update($wpdb->prefix.'match_meta_new',$match_meta,array('match_id'=>$_POST['match_id']));
        }else{
            $match_meta['created_id'] = $current_user->ID;
            $match_meta['created_time'] = get_time('mysql');
            $a = $wpdb->insert($wpdb->prefix.'match_meta_new',$match_meta);
        }
        if(!$b){

            //生成比赛项目
            //获取默认每轮间隔时间/每项间隔时间
            $more_interval_time = 3;
            $project_interval_time = 10;

            $match_start_time = strtotime($_POST['match_start_time']);
            $match_project_use = get_option('match_project_use')['project_use'];
            $start_time = $match_start_time;
            $str = '';
            foreach ($project_array as $k => $v){
                $project_alias = get_post_meta($v,'project_alias')[0];
                $use_time = $project_alias == 'zxss' ? array_sum(array_values($match_project_use[$project_alias])) : $match_project_use[$project_alias];
                //print_r($use_time.'--');
                for($i=1;$i<4;++$i){
                    $start = date_i18n('Y-m-d H:i:s',$start_time);

                    $end_time = strtotime("+$use_time minute",$start_time);
                    $end = date_i18n('Y-m-d H:i:s',$end_time);
                    //print_r($start.'****'.$end.'</br>');
                    $str .= "( '{$new_page_id}', '{$v}', '{$i}', '{$start}', '{$end}', '{$use_time}', '-1', '{$current_user->ID}', NULL, NOW(), NULL),";

                    $start_time = strtotime("+{$more_interval_time} minute",$end_time);

                    $wpdb->update($wpdb->prefix.'match_meta_new',array('match_end_time'=>$end),array('match_id'=>$new_page_id));
                }
                $start_time = strtotime("+{$project_interval_time} minute",$end_time);
                //print_r($v);
            }
            $sql = "INSERT INTO `{$wpdb->prefix}match_project_more` ( `match_id`, `project_id`, `more`, `start_time`, `end_time`, `use_time`, `status`, `created_id`, `revise_id`, `created_time`, `revise_time`) VALUES ".rtrim($str,',');
            //print_r($sql);die;
            $b = $wpdb->query($sql);
        }
        //print_r($new_page_id .'&&'. $a .'&&'. $b);
        if($new_page_id && $a && $b){
            //设置比赛开关
            update_post_meta($new_page_id,'default_match_switch','ON');
            $wpdb->query('COMMIT');
            wp_send_json_success(array('info'=>$_POST['match_id'] > 0 ? '比赛编辑成功' : '比赛发布成功','url'=>home_url('/zone/matchTime/match_id/'.$new_page_id)));
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>$_POST['match_id'] > 0 ? '比赛编辑失败' : '比赛发布失败'));
        }
    }


    /**
     * 机构关闭比赛
     */
    public function end_match(){
        global $wpdb,$current_user;
        if(empty($_POST['match_id'])) wp_send_json_error(array('info'=>__('参数必传')));
        $id = $wpdb->get_var("select id from {$wpdb->prefix}match_meta_new where match_id = {$_POST['match_id']} and created_id = {$current_user->ID} ");
        //print_r($id);die;
        if(empty($id)) wp_send_json_error(array('info'=>__('禁止操作非本机构发布的比赛')));
        $update = array(
            'revise_id'=>$current_user->ID,
            'match_status'=>-4,
            'match_end_time'=>date_i18n('Y-m-d H:i:s',strtotime('-5 minute',get_time()))
        );
        $update1 = array(
            'end_time'=>date_i18n('Y-m-d H:i:s',strtotime('-5 minute',get_time())),
            'status'=>-1,
        );
        $wpdb->query('START TRANSACTION');
        $a = $wpdb->update($wpdb->prefix.'match_meta_new',$update,array('id'=>$id));
        $b = $wpdb->update($wpdb->prefix.'match_project_more',$update1,array('match_id'=>$_POST['match_id']));
        //print_r($a .'&&'. $b);die;
        if($a && $b){
            $wpdb->query('COMMIT');
            wp_send_json_success(array('info' => __('提交成功', 'nlyd-student'),'url'=>home_url('/zone/match/')));
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>__('提交失败', 'nlyd-student')));
        }
    }


    /**
     * 机构关闭考级
     */
    public function end_grading(){
        global $wpdb,$current_user;
        if(empty($_POST['grading_id'])) wp_send_json_error(array('info'=>__('参数必传','nlyd-student')));
        $id = $wpdb->get_var("select id from {$wpdb->prefix}grading_meta where grading_id = {$_POST['grading_id']} and created_person = {$current_user->ID} ");
        if(empty($id)) wp_send_json_error(array('info'=>__('禁止操作非本机构发布的考级','nlyd-student')));
        $update = array(
            'revise_id'=>$current_user->ID,
            'status'=>-4,
            'end_time'=>date_i18n('Y-m-d H:i:s',strtotime('-5 minute',get_time()))
        );

        $a = $wpdb->update($wpdb->prefix.'grading_meta',$update,array('id'=>$id));
        if($a){
            wp_send_json_success(array('info' => __('提交成功', 'nlyd-student'),'url'=>home_url('/zone/grading/')));
        }else{
            wp_send_json_error(array('info'=>__('提交失败', 'nlyd-student')));
        }
    }


    /**
     * 机构发布考级
     */
    public function zone_create_grading(){
        if(empty($_POST['post_title']) || empty($_POST['scene']) || empty($_POST['category_id']) || empty($_POST['start_time'])|| empty($_POST['end_time']) ){
            wp_send_json_error(array('info'=>'考级场景/类别/名称/时间为必填项'));
        }
        if($_POST['start_time'] >= $_POST['end_time'] )wp_send_json_error(array('info'=>'结束时间必须大于开始时间'));
        if($_POST['entry_end_time'] >= $_POST['start_time'] )wp_send_json_error(array('info'=>'报名结束时间必须大于开始时间'));
        global $wpdb,$current_user;
        $sql = "select a.ID,b.meta_value from {$wpdb->prefix}users a 
                left join {$wpdb->prefix}usermeta b on a.ID = b.user_id and b.meta_key = 'user_real_name'
                where a.ID = '{$_POST['person_liable']}'
                ";
        $person_liable = $wpdb->get_row($sql,ARRAY_A);
        if(empty($person_liable)) wp_send_json_error(array('info'=>__('该责任人未注册','nlyd-student')));
        $real_name = unserialize($person_liable['meta_value']);
        if(empty($real_name['real_name'])) wp_send_json_error(array('info'=>__('该责任人未实名认证','nlyd-student')));

        /***********************准备数据**********************************/
        $arr = array(
            'post_title' => $_POST['post_title'],
            'post_type'     => 'grading',
            'post_status' => 'publish',
            'post_author' => $current_user->ID,
        );
        $wpdb->query('START TRANSACTION');

        //获取所有比赛项目
        $project_array = $wpdb->get_results("select ID from {$wpdb->prefix}posts where post_type = 'project' and post_status = 'publish' order by menu_order asc",ARRAY_A);
        $project_array = array_column($project_array,'ID');
        //print_r($project_array);
        $project_id = arr2str($project_array);

        if(!empty($_POST['grading_id'])){
            $wpdb->update($wpdb->prefix.'posts',$arr,array('ID'=>$_POST['grading_id']));
            $new_page_id = $_POST['grading_id'];
        }else{
            $new_page_id = wp_insert_post($arr);
        }
        $match_meta = array(
            'grading_id'=>$new_page_id,
            'scene'=>$_POST['scene'],
            'category_id'=>$_POST['category_id'],
            'address'=>$_POST['address'],
            'cost'=>$_POST['cost'],
            'entry_end_time'=>!empty($_POST['entry_end_time']) ? $_POST['entry_end_time'] : date_i18n('Y-m-d H:i:s',strtotime('-10 minute',strtotime($_POST['start_time']))),
            'start_time'=>$_POST['start_time'],
            'end_time'=>$_POST['end_time'],
            'person_liable'=>!empty($person_liable['ID']) ? $person_liable['ID'] : '',
        );
        if(!empty($_POST['grading_id'])){

            $match_meta['revise_id'] = $current_user->ID;
            $match_meta['revise_time'] = get_time('mysql');
            $a = $wpdb->update($wpdb->prefix.'grading_meta',$match_meta,array('grading_id'=>$_POST['grading_id']));
        }else{
            $match_meta['created_person'] = $current_user->ID;
            $match_meta['created_time'] = get_time('mysql');
            $a = $wpdb->insert($wpdb->prefix.'grading_meta',$match_meta);
        }
        //print_r($a .'&&'. $new_page_id);die;
        if($a && $new_page_id){
            //设置比赛开关
            update_post_meta($new_page_id,'default_match_switch','ON');
            $wpdb->query('COMMIT');
            wp_send_json_success(array('info'=>$_POST['grading_id'] > 0 ? '考级编辑成功' : '考级发布成功','url'=>home_url('/zone/grading/')));
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>$_POST['grading_id'] > 0 ? '考级编辑失败' : '考级发布失败'));
        }
    }


    /**
     * 获取比赛费用
     * @return [type] [description]
     */
    public function get_match_cost(){
        global $wpdb,$current_user;
        if(empty($_POST['type'])) wp_send_json_error(array('info'=>__('参数错误','nlyd-student')));

        if (in_array($_POST['type'],array('official-match','official-grading'))){
            if($_POST['type'] == 'official-match'){
                $type = 1;
            }else{
                $type = 2;
            }

            //获取机构赛区类型
            $zone_meta = $wpdb->get_row("select b.zone_type_alias,a.zone_match_type,a.is_double from {$wpdb->prefix}zone_meta a 
                                                left join {$wpdb->prefix}zone_type b on a.type_id = b.id
                                                where user_id = {$current_user->ID}",ARRAY_A);
            if($zone_meta['zone_match_type'] == 1){ //战队赛
                $match_type = 1;
            }
            elseif ($zone_meta['is_double'] == 1){  //多区县
                $match_type = 2;
            }
            elseif ($zone_meta['is_double'] == 2){  //单区县
                $match_type = 3;
            }
            else{
                $match_type = 4;
            }
            $set_sql = "select pay_amount match_cost from {$wpdb->prefix}spread_set where spread_type = '{$zone_meta['zone_type_alias']}' and  match_grading = {$type} and match_type = {$match_type}";
            //print_r($set_sql);

        }elseif (in_array($_POST['type'],array('basis-course','boost-course'))){

            $set_sql = "select pay_amount match_cost from {$wpdb->prefix}spread_set where spread_type = '{$_POST['type']}' ";
        }
        if(in_array($_POST['type'],array('official-match','official-grading','basis-course','boost-course'))){
            $match_cost = $wpdb->get_var($set_sql);
            if($match_cost < 10){
                wp_send_json_error(array('info'=>__('未匹配到费用','nlyd-student')));
            }
        }
        $match_cost = !empty($match_cost)? $match_cost :number_format(0);
        wp_send_json_success($match_cost);

    }

    /**
     *机构比赛/考级 时间修改/编辑
     */
    public function update_match_time(){
        if(empty($_POST['match_id'])) wp_send_json_error(array('info'=>__('比赛id不能为空','nlyd-student')));
        if(empty($_POST['data'])) wp_send_json_error(array('info'=>__('修改数据不能为空','nlyd-student')));
        global $wpdb,$current_user;
        switch ($_POST['match_type']){
            case 'match':
                //print_r($_POST);die;
                //获取报名截止时间
                $entry_end_time = $wpdb->get_var("select entry_end_time from {$wpdb->prefix}match_meta_new where match_id = {$_POST['match_id']} ");

                foreach ($_POST['data'] as $k => $v){
                    if($v['id'] > 0){
                        //print_r($entry_end_time.'===='.$v['start_time']);
                        if( strtotime($entry_end_time) >= strtotime($v['start_time']) ){
                            wp_send_json_error(array('info'=>__('每轮项目开赛时间必须大于报名截止时间','nlyd-student')));
                        }
                        $result = $this->contrast_time($_POST['data'],$v['start_time'],$v['end_time'],$v['id']);
                        if(!empty($result)){
                            wp_send_json_error(array('info'=>__($v['project_title'].'第'.$v['project_more'].'轮与'.$result['project_title'].'第'.$result['project_more'].'轮时间冲突','nlyd-student')));
                        }
                    }
                    else{
                        wp_send_json_error(array('info'=>__($v['project_title'].$v['project_more'].'未检测到项目id','nlyd-student')));
                    }
                    $update = array(
                        'start_time'=>$v['start_time'],
                        'end_time'=>$v['end_time'],
                        'revise_id'=>$current_user->ID,
                        'revise_time'=>get_time('mysql'),
                    );
                    $wpdb->update($wpdb->prefix.'match_project_more',$update,array('id'=>$v['id']));
                }
                if(!empty($_POST['project_id'])){
                    foreach ($_POST['project_id'] as $y ){
                        //重新排序
                        $this->project_sort($_POST['match_id'],$y);
                    }
                }
                break;
            case 'grading':



                break;
            default:
                wp_send_json_error(array('info'=>__('参数错误','nlyd-student')));
                break;
        }
        wp_send_json_success(array('info'=>__('更新成功','nlyd-student')));
    }

    /**
     * 机构比赛轮数新增
     */
    public function add_match_time(){
        global $wpdb,$current_user;
        if(empty($_POST['match_id']) || empty($_POST['project_id']) || empty($_POST['project_more']) || empty($_POST['start_time']) || empty($_POST['end_time']) || empty($_POST['use_time'])){
            wp_send_json_error(array('info'=>__('必传参数不齐全','nlyd-student')));
        }
        switch ($_POST['match_type']) {
            case 'match':

                //获取报名截止时间
                $entry_end_time = $wpdb->get_var("select entry_end_time from {$wpdb->prefix}match_meta_new where match_id = {$_POST['match_id']}");
                //print_r($entry_end_time);die;
                if( strtotime($entry_end_time) >= strtotime($_POST['start_time']) ){
                    wp_send_json_error(array('info'=>__('每轮项目开赛时间必须大于报名截止时间','nlyd-student')));
                }
                //获取已有的比赛列表
                $sql = "select a.*,b.post_title from {$wpdb->prefix}match_project_more a 
                left join {$wpdb->prefix}posts b on a.project_id = b.ID
                where match_id = {$_POST['match_id']} and created_id = $current_user->ID";
                //print_r($sql);die;
                $rows = $wpdb->get_results($sql, ARRAY_A);
                if (!empty($rows)) {

                    $result = $this->contrast_time($rows, $_POST['start_time'], $_POST['end_time']);
                    //print_r($result);
                    if (!empty($result)) {
                        wp_send_json_error(array('info' => _('与' . $result['post_title'] . '第' . $result['more'] . '轮时间冲突','nlyd-student')));
                    }
                    $insert = array(
                        'match_id' => $_POST['match_id'],
                        'project_id' => $_POST['project_id'],
                        'start_time' => $_POST['start_time'],
                        'more' => $_POST['project_more'],
                        'start_time' => $_POST['start_time'],
                        'end_time' => $_POST['end_time'],
                        'use_time' => $_POST['use_time'],
                        'created_id' => $current_user->ID,
                        'created_time' => get_time('mysql'),
                    );
                    //print_r($insert);
                    $res = $wpdb->insert($wpdb->prefix . 'match_project_more', $insert);
                    //对当前项目进行重新排序
                    $this->project_sort($_POST['match_id'],$_POST['project_id']);
                }
                break;
            case 'grading':


                break;
            default:
                wp_send_json_error(array('info' => __('参数错误','nlyd-student')));
                break;
        }
        //print_r($res);die;
        if($res){
            wp_send_json_success(array('info'=>__('新增成功','nlyd-student')));
        }
        wp_send_json_error(array('info'=>__('新增失败','nlyd-student')));
    }

    /**
     * 删除轮数/考级
     */
    public function remove_match_time(){
        global $wpdb,$current_user;
        if(empty($_POST['id'])){
            wp_send_json_error(array('info'=>__('id必传','nlyd-student')));
        }
        switch ($_POST['match_type']) {
            case 'match':
                $row = $wpdb->get_row("select * from {$wpdb->prefix}match_project_more where id={$_POST['id']} ",ARRAY_A);
                //print_r($row);die;

                $res = $wpdb->delete($wpdb->prefix . 'match_project_more', array('id'=>$_POST['id']));

                //对当前项目进行重新排序
                $this->project_sort($row['match_id'],$row['project_id']);
                break;
            case 'grading':


                break;
            default:
                wp_send_json_error(array('info' => _('参数错误')));
                break;
        }
        if($res){
            wp_send_json_success(array('info'=>__('删除成功','nlyd-student')));
        }
        wp_send_json_error(array('info'=>__('删除失败','nlyd-student')));
    }

    /**
    * 轮数重新排序
    */
    public function project_sort($match_id,$project_id){
        global $wpdb;
        $projects = $wpdb->get_results("select * from {$wpdb->prefix}match_project_more where match_id = {$match_id} and project_id = {$project_id} order by start_time asc",ARRAY_A);
        if(!empty($projects)){
            foreach ($projects as $k => $v){
                ///print_r($v);
                $wpdb->update($wpdb->prefix.'match_project_more',array('more'=>$k+1),array('id'=>$v['id']));
            }
        }
        //print_r($projects);die;
    }

    /**
     * 获取机构发布的考级
     */
    public function get_zone_grading_list(){
        global $wpdb,$current_user;

        $map[] = " a.created_person = {$current_user->ID} ";
        if($_POST['grading_type'] == 'history'){
            $map[] = " a.status = -3 ";
        }elseif ($_POST['grading_type'] == 'matching'){
            $map[] = " a.status != -3 ";
        }
        $where = join("and",$map);

        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        $sql = "select a.grading_id,a.scene,b.post_title,d.role_name,a.status,entry_end_time,a.start_time,a.person_liable,a.cost,a.address,count(c.id) entry_total,
                case a.status
                when '-4' then '已取消'
                when '-3' then '已结束'
                when '-2' then '等待考级'
                when '1' then '报名中'
                when '2' then '进行中'
                end grading_status_cn
                from {$wpdb->prefix}grading_meta a 
                left join {$wpdb->prefix}posts b on a.grading_id = b.ID
                left join {$wpdb->prefix}order c on a.grading_id = c.match_id and c.pay_status in (2,3,4)
                left join {$wpdb->prefix}zone_match_role d on a.scene = d.id
                where {$where} 
                group by a.grading_id
                order by a.start_time desc ,a.status desc
                limit $start,$pageSize
               ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无比赛', 'nlyd-student')));
        foreach ($rows as $k => $v){
            $user_real_name = get_user_meta($v['person_liable'],'user_real_name')[0];
            $rows[$k]['person_liable'] = !empty($user_real_name['real_name']) ? $user_real_name['real_name'] : '-';
        }
        wp_send_json_success(array('info'=>$rows));
    }


    /**
     * 获取机构发布的比赛
     */
    public function get_zone_match_list(){
        global $wpdb,$current_user;

        $map[] = " a.created_id = {$current_user->ID} ";
        if($_POST['match_type'] == 'history'){
            $map[] = " a.match_status = -3 ";
        }elseif ($_POST['match_type'] == 'matching'){
            $map[] = " a.match_status != -3 ";
        }
        $where = join("and",$map);

        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        $sql = "select a.match_id,a.match_scene,b.post_title,d.role_name,a.match_status,entry_end_time,a.match_start_time,match_cost,a.match_address,count(c.id) entry_total,
                case a.match_status
                when '-4' then '已取消'
                when '-3' then '已结束'
                when '-2' then '等待开赛'
                when '1' then '报名中'
                when '2' then '进行中'
                end match_status_cn
                from {$wpdb->prefix}match_meta_new a 
                left join {$wpdb->prefix}posts b on a.match_id = b.ID
                left join {$wpdb->prefix}order c on a.match_id = c.match_id and c.pay_status in (2,3,4)
                left join {$wpdb->prefix}zone_match_role d on a.match_scene = d.id
                where {$where} 
                group by a.match_id
                order by a.match_start_time desc ,a.match_status desc
                limit $start,$pageSize
               ";
        $rows = $wpdb->get_results($sql,ARRAY_A);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无比赛', 'nlyd-student')));
        wp_send_json_success(array('info'=>$rows));
    }

    /**
     * 对比比赛时间
     */
    public function contrast_time($data,$start_time,$end_time,$id=''){

        foreach ($data as $k =>$v){

            if($id != $v['id'] ){
                if( ( strtotime($v['start_time']) <= strtotime($start_time) && strtotime($start_time) <= strtotime($v['end_time']) ) || ( strtotime($v['start_time']) <= strtotime($end_time) && strtotime($end_time) <= strtotime($v['end_time']) ) ){
                    return $v;    //返回冲突时间
                }
            }
        }
    }



    /**
     * 用户提现申请
     */
    public function user_extract_apply(){

        global $wpdb,$current_user;

        switch ($_POST['extract_type']){
            case 'weChat':
                //获取收款二维码
                $extract_code_img = get_user_meta($current_user->ID,'user_coin_code')[0];
                if(empty($user_cheques_bank)) wp_send_json_error(array('info'=>__('请先设置微信收款码')));
                break;
            case 'aliPay':
                //获取收款二维码
                $extract_code_img = get_user_meta($current_user->ID,'aliPay_coin_code')[0];
                if(empty($user_cheques_bank)) wp_send_json_error(array('info'=>__('请先设置支付宝收款码')));
                break;
            case 'user_bank':
                //获取收款账户
                $user_cheques_bank = get_user_meta($current_user->ID,'user_cheques_bank')[0];
                if(empty($user_cheques_bank)) wp_send_json_error(array('info'=>__('请先设置银行收款账户')));
                $opening_user = $user_cheques_bank['open_name'];
                $opening_bank = $user_cheques_bank['open_bank'];
                $opening_bank_address = $user_cheques_bank['open_address'];
                $bank_card_num = $user_cheques_bank['open_card_num'];
                //print_r($user_cheques_bank);die;
                break;
            case 'bank':
                //获取账户信息
                $row = $wpdb->get_row("select * from {$wpdb->prefix}zone_meta where user_id = {$current_user->ID}",ARRAY_A);
                if(empty($row)) wp_send_json_error(array(_('账户信息错误')));
                $opening_bank = $row['opening_bank'];
                $opening_bank_address = $row['opening_bank_address'];
                $bank_card_num = $row['bank_card_num'];
                break;
            case 'wallet':
                break;
            default:
                wp_send_json_error(array('info'=>__('提现方式错误','nlyd-student')));
                break;
        }
        if(empty($_POST['num'])) wp_send_json_error(array('info'=>__('请输入提现金额')));

        //获取可提现金额
        $stream_total = $wpdb->get_var("select sum(user_income) stream_total from {$wpdb->prefix}user_stream_logs where user_id = {$current_user->ID} and income_type != 'undertake' ");

        if($stream_total < $_POST['num']){
            wp_send_json_error(array('info'=>__('可提现余额不足,无法提现','nlyd-student')));
        }

        $wpdb->query('START TRANSACTION');
        $insert1 = array(
            'user_id'=>$current_user->ID,
            'income_type'=>'extract',
            'extract_type'=>$_POST['extract_type'],
            'user_income'=>-$_POST['num'],
            'created_time'=>get_time('mysql'),

        );
        $a = $wpdb->insert($wpdb->prefix.'user_stream_logs',$insert1);
        $id = $wpdb->insert_id;
        $insert2 = array(
            'stream_log_id'=>$id,
            'extract_id'=>$current_user->ID,
            'extract_amount'=>$_POST['num'],
            'extract_type'=>$_POST['extract_type'],
            'bank_user'=>!empty($opening_user) ? $opening_user : '' ,
            'bank_name'=>!empty($opening_bank) ? $opening_bank : '' ,
            'bank_address'=>!empty($opening_bank_address) ? $opening_bank_address : '' ,
            'extract_account'=>!empty($bank_card_num) ? $bank_card_num : '' ,
            'extract_code_img'=>!empty($extract_code_img) ? $extract_code_img[0] : '' ,
            'apply_time'=>get_time('mysql') ,

        );

        $b = $wpdb->insert($wpdb->prefix.'user_extract_logs',$insert2);
        //print_r($a.'----'.$b);
        if($a && $b ){
            $wpdb->query('COMMIT');
            wp_send_json_success(array('info'=>__('提交成功','nlyd-student'),'url'=>home_url('/zone/getCashSuccess/')));
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>__('提交失败','nlyd-student')));
        }
    }


    /**
     * 获取我的详情
     */
    public function get_my_profit_detail(){
        global $wpdb,$current_user;
        //$_POST['id'] = 148;
        //获取当前收益内容
        $row = $wpdb->get_row("select match_id,income_type,user_type,user_income,
        						if(income_status=2,'已到账','待到账') income_status_cn,
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
                              where id = {$_POST['id']} and user_id = {$current_user->ID} ",ARRAY_A);

        //print_r($row);die;

        if(empty($row)){
            wp_send_json_error(array('info'=>__('数据错误','nlyd-student')));
        }

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        if(in_array($row['income_type'],array('recommend_match_zone','recommend_trains_zone','recommend_test_zone'))){  //推荐赛区/推荐训练中心/推荐测评中心
            $where = "  a.id = {$row['match_id']} and a.income_type = 'subject' ";
        }
        elseif (in_array($row['income_type'],array('open_match'))){     //开设比赛
            $where = " a.income_type = 'match' and a.match_id = {$row['match_id']} and a.sponsor_id = {$current_user->ID} ";
        }
        elseif (in_array($row['income_type'],array('open_grading'))){   //开设考级
            $where = " a.income_type = 'grading' and a.match_id = {$row['match_id']} and a.sponsor_id = {$current_user->ID} ";
        }
        elseif (in_array($row['income_type'],array('open_course'))){    //课程渠道
            $where = " a.income_type = 'course' and a.match_id = {$row['match_id']} and a.sponsor_id = {$current_user->ID} ";
        }
        elseif (in_array($row['income_type'],array('recommend_match'))){    //推荐比赛
            $where = " a.income_type = 'match' and a.match_id = {$row['match_id']} and (a.referee_id = {$current_user->ID} or a.indirect_referee_id = {$current_user->ID}) ";
        }
        elseif (in_array($row['income_type'],array('recommend_match','recommend_grading'))){    //推荐考级
            $where = " a.income_type = 'grading' and a.match_id = {$row['match_id']} and (a.referee_id = {$current_user->ID} or a.indirect_referee_id = {$current_user->ID}) ";
        }
        elseif (in_array($row['income_type'],array('director_match','director_grading'))){  //参赛机构/考级负责人
            $where = " a.match_id = {$row['match_id']} and a.person_liable_id = {$current_user->ID}  ";
        }
        elseif (in_array($row['income_type'],array('recommend_course'))){    //推荐购课
            $where = " a.income_type = 'course' and a.match_id = {$row['match_id']} and (a.referee_id = {$current_user->ID} or a.indirect_referee_id = {$current_user->ID})   ";
        }
        elseif (in_array($row['income_type'],array('recommend_qualified'))){    //购课补贴
            $where = " a.income_type = 'course' and a.id = {$row['match_id']} and a.person_liable_id = {$current_user->ID}  ";
        }
        elseif (in_array($row['income_type'],array('grading_qualified'))){  //考级达标
            $where = " a.income_type = 'grading' and a.id = {$row['match_id']} and a.referee_id = {$current_user->ID}  ";
        }
        elseif (in_array($row['income_type'],array('cause_manager'))){  //事业管理员
            $where = " a.income_type = 'course' and a.match_id = {$row['match_id']} and a.manager_id = {$current_user->ID}  ";
        }
        elseif (in_array($row['income_type'],array('cause_minister'))){ //事业部长
            $where = " a.match_id = {$row['match_id']} and a.indirect_manager_id = {$current_user->ID}  ";
        }

        //获取对应数据列表
        $sql = "select SQL_CALC_FOUND_ROWS a.*,
                      if(a.income_status=2,'已到账','待到账') income_status_cn
                      from {$wpdb->prefix}user_income_logs a 
                      left join {$wpdb->prefix}posts b on a.match_id = b.ID 
                      where {$where}
                      order by id desc limit $start,$pageSize
                      ";
        //print_r($sql);die;
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($rows);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无记录', 'nlyd-student')));
        //print_r($rows);
        if(!empty($rows)){
            $list = array();
            foreach ($rows as $k => $v){
                $list['user_id'] = $v['user_id'];
                $list['revenue_source'] = $row['income_type_title'];
                if($v['referee_id'] == $current_user->ID){
                    $list['profit_income'] = $v['referee_income'];
                }
                elseif ($v['indirect_referee_id'] == $current_user->ID){
                    $list['profit_income'] = $v['indirect_referee_income'];
                }
                if(in_array($row['income_type'],array('director_grading','director_match','recommend_qualified'))){
                    if ($v['person_liable_id'] == $current_user->ID){
                        $list['profit_income'] = $v['person_liable_income'];
                    }
                }
                if ($v['sponsor_id'] == $current_user->ID){
                    $list['profit_income'] = $v['sponsor_income'];
                }

                $referee_name = get_user_meta($v['user_id'],'user_real_name')[0];
                //var_dump($referee_name);
                $list['channel'] = $referee_name['real_name'];
                $list['channel_ID'] = $v['user_id']+10000000;
                $list['post_title'] = $v['post_title'];
                $list['income_type'] = $v['income_type'];
                $list['income_status_cn'] = $v['income_status_cn'];
                $list['created_time'] = $v['created_time'];
                $lists[] = $list;
            }
            //print_r($list);
        }
        wp_send_json_success(array('info'=>$lists));

    }

    /**
     * 获取我的推荐
     */
    public function get_my_offline(){
        global $wpdb,$current_user;

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;


        //获取我推荐的机构
        if($_POST['map'] == 'zone'){
            $sql = "select SQL_CALC_FOUND_ROWS a.id ,b.zone_type_alias,b.zone_type_name, 
					case a.zone_match_type
	                when 1 then '战队精英赛'
	                when 2 then '城市赛'
	                end zone_match_type_cn,
	                a.zone_city,a.zone_name,a.user_id,apply_id,
	                date_format(a.audit_time,'%Y-%m-%d') audit_time  
	                from {$wpdb->prefix}zone_meta a 
	                left join {$wpdb->prefix}zone_type b on a.type_id = b.id 
	                where a.referee_id = {$current_user->ID} and a.user_id > 0 order by a.id desc limit $start,$pageSize";
            //print_r($sql);die;
        }
        else{   //获取我推荐的用户


            $sql = "select SQL_CALC_FOUND_ROWS a.ID ,a.user_nicename,b.meta_value as user_real_name,referee_time 
                    from {$wpdb->prefix}users a 
                    left join {$wpdb->prefix}usermeta b on a.ID = b.user_id and b.meta_key = 'user_real_name'
                    left join {$wpdb->prefix}zone_meta c on a.ID = c.user_id 
                    where a.referee_id = {$current_user->ID} and c.id is null
                    order by a.ID desc limit $start,$pageSize 
                    ";
        }
        //print_r($sql);

        $rows = $wpdb->get_results($sql,ARRAY_A);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['map'] != 'zone' && empty($rows) ){
            if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        }
        //print_r($rows);
        //if(empty($rows)) wp_send_json_error(array('info'=>__('暂无记录', 'nlyd-student')));

        if($_POST['map'] != 'zone'){
            $list = array();
            foreach ($rows as $k => $v) {
                //print_r($v);
                if(!empty($v['user_real_name'])){
                    $user_real_name = unserialize($v['user_real_name']);
                    $list[$k]['real_age'] = $user_real_name['real_age'];
                    $list[$k]['real_name'] = $user_real_name['real_name'];
                }else{
                    $list[$k]['real_name'] = $v['user_nicename'];
                    $list[$k]['real_age'] = '-';
                }
                $list[$k]['referee_time'] = !empty($v['referee_time']) ? $v['referee_time'] : '-';

                //获取学号/性别/是否购课
                $sql1 = "select meta_key,meta_value from {$wpdb->prefix}usermeta 
                        where user_id = {$v['ID']} and meta_key in ('user_ID','user_gender')
                         ";
                $rows1 = $wpdb->get_results($sql1,ARRAY_A);
                if(!empty($rows1)){
                    $meta_value = array_column($rows1,'meta_value','meta_key');
                }
                $list[$k]['user_ID'] = !empty($meta_value['user_ID']) ? $meta_value['user_ID'] : $v['ID'] + 10000000;
                $list[$k]['user_gender'] = !empty($meta_value['user_gender']) ? $meta_value['user_gender'] : '-';

                $order_id = $wpdb->get_var("select id from {$wpdb->prefix}order b where user_id = {$current_user->ID} and order_type = 3 ");
                $list[$k]['is_shop'] = $order_id > 0 ? 'y' : 'n';
            }

            //获取二级推荐
            $child_sql = "select ID
                 from {$wpdb->prefix}users
                 where referee_id = {$current_user->ID} ";
            //print_r($child_sql);
            $child_user = $wpdb->get_results($child_sql,ARRAY_A);
            if(!empty($child_user)){
                foreach ($child_user as $x => $y) {
                    $sql_ = "select SQL_CALC_FOUND_ROWS a.ID ,a.user_nicename,b.meta_value as user_real_name,referee_time 
			                from {$wpdb->prefix}users a 
			                left join {$wpdb->prefix}usermeta b on a.ID = b.user_id and b.meta_key = 'user_real_name'
			                left join {$wpdb->prefix}zone_meta c on a.ID = c.user_id 
			                where a.referee_id = {$y['ID']} and c.id is null
			                order by a.ID desc 
			                ";
                    //print_r($sql_);
                    $rows1 = $wpdb->get_results($sql_,ARRAY_A);
                    //print_r($sql__);
                    //print_r($rows1);
                    if(!empty($rows1)){
                        foreach ($rows1 as $k1 => $v1) {
                            if(!empty($v1['user_real_name'])){
                                $user_real_name = unserialize($v1['user_real_name']);
                                $rows1[$k1]['real_age'] = $user_real_name['real_age'];
                                $rows1[$k1]['real_name'] = $user_real_name['real_name'];
                            }else{
                                $rows1[$k1]['real_age'] = '-';
                                $rows1[$k1]['real_name'] = $v1['user_nicename'];
                            }
                            $rows1[$k1]['referee_time'] = !empty($v1['referee_time']) ? $v1['referee_time'] : '-';

                            //获取学号/性别/是否购课
                            $sql2 = "select meta_key,meta_value from {$wpdb->prefix}usermeta 
		                            where user_id = {$v1['ID']} and meta_key in ('user_ID','user_gender')
		                             ";
                            $rows2 = $wpdb->get_results($sql2,ARRAY_A);
                            if(!empty($rows1)){
                                $meta_value_ = array_column($rows2,'meta_value','meta_key');
                                $rows1[$k1]['user_ID'] = $meta_value_['user_ID'];
                                $rows1[$k1]['user_gender'] = !empty($meta_value_['user_gender']) ? $meta_value_['user_gender'] : '-';

                            }

                            $order_id = $wpdb->get_var("select id from {$wpdb->prefix}order b where user_id = {$current_user->ID} and order_type = 3 ");
                            $rows1[$k1]['is_shop'] = $order_id > 0 ? 'y' : 'n';
                        }
                    }
                }
            }
            //var_dump($list);
        }
        else{
            foreach ($rows as $k => $v) {
                if(!empty($v['zone_city'])){

                    $city_arr = str2arr($v['zone_city'],'-');
                    if(!empty($city_arr[2])){
                        $city = rtrim($city_arr[1],'市').preg_replace('/区|县/','',$city_arr[2]);
                    }elseif ($city_arr[1] != '市辖区'){
                        $city = rtrim($city_arr[1],'市');
                    }else{
                        $city = rtrim($city_arr[0],'市');
                    }
                }
                //print_r($city);

                //$city = !empty($city) ? '（'.$city.'）' : '';
                $city = !empty($city) ? $city : '';
                if($v['zone_type_alias'] == 'match'){
                    $rows[$k]['zone_name'] = $v['zone_name'].$city.$v['zone_match_type_cn'].'组委会';
                }
                else{
                    $title = 'IISC脑力';
                    if($v['zone_type_alias'] == 'test'){
                        $title .= '水平';
                    }

                    $rows[$k]['zone_name'] = $title.$v['zone_type_name'].' • '.$city;
                }
            }
            //获取二级推荐
            $child_sql = "select ID
                 from {$wpdb->prefix}users
                 where referee_id = {$current_user->ID} ";
            //print_r($child_sql);
            $child_user = $wpdb->get_results($child_sql,ARRAY_A);
            if(!empty($child_user)){
                foreach ($child_user as $x => $y) {
                    $sql1 = "select SQL_CALC_FOUND_ROWS a.id ,b.zone_type_alias,b.zone_type_name, 
								case a.zone_match_type
				                when 1 then '战队精英赛'
				                when 2 then '城市赛'
				                end zone_match_type_cn,
				                a.zone_city,a.zone_name,a.user_id,apply_id,
				                date_format(a.audit_time,'%Y-%m-%d') audit_time  
				                from {$wpdb->prefix}zone_meta a 
				                left join {$wpdb->prefix}zone_type b on a.type_id = b.id 
				                where a.referee_id = {$y['ID']} and a.user_id > 0 order by a.id desc
				                ";
                    $rows1 = $wpdb->get_results($sql1,ARRAY_A);
                    //print_r($sql__);
                    //print_r($rows1);
                    if(!empty($rows1)){
                        foreach ($rows1 as $k1 => $v1) {
                            # code...
                            if(!empty($v1['zone_city'])){

                                $city_arr = str2arr($v1['zone_city'],'-');
                                if(!empty($city_arr[2])){
                                    $city = rtrim($city_arr[1],'市').preg_replace('/区|县/','',$city_arr[2]);
                                }elseif ($city_arr[1] != '市辖区'){
                                    $city = rtrim($city_arr[1],'市');
                                }else{
                                    $city = rtrim($city_arr[0],'市');
                                }
                            }
                            //print_r($city);

                            $city = !empty($city) ? $city : '';
                            if($v1['zone_type_alias'] == 'match'){
                                $rows1[$k1]['zone_name'] = $v1['zone_name'].$city.$v1['zone_match_type_cn'].'组委会';
                            }
                            else{
                                $title = 'IISC脑力';
                                if($v1['zone_type_alias'] == 'test'){
                                    $title .= '水平';
                                }

                                $rows1[$k1]['zone_name'] = $title.$v1['zone_type_name'].' • '.$city;
                            }
                        }
                    }
                }
            }

            $list = $rows;
        }
        wp_send_json_success(array('info'=>$list,'child'=>$rows1));
    }

    /**
     * 机构添加教练
     */
    public function add_zone_coach(){
        if(empty($_POST['coach_id'])){
            wp_send_json_error(array('info'=>__('请选择教练','nlyd-student')));
        }
        global $wpdb,$current_user;

        $sql = "select a.ID,b.meta_value from {$wpdb->prefix}users a 
                left join {$wpdb->prefix}usermeta b on a.ID = b.user_id and b.meta_key = 'user_real_name'
                where a.ID = '{$_POST['coach_id']}'
                ";
        $coach = $wpdb->get_row($sql,ARRAY_A);
        if(empty($coach)) wp_send_json_error(array('info'=>__('该教练未注册','nlyd-student')));
        $real_name = unserialize($coach['meta_value']);
        if(empty($real_name['real_name'])) wp_send_json_error(array('info'=>__('该教练未实名认证','nlyd-student')));

        //判断该教练是否被占用
        $row = $wpdb->get_row("select a.zone_id,b.id,b.is_assign from {$wpdb->prefix}zone_join_coach a 
                                      left join {$wpdb->prefix}coach_skill b on a.coach_id = b.coach_id
                                      where a.coach_id = {$coach['ID']} ",ARRAY_A);

        if(!empty($row['id'])){

            if($row['is_assign'] != 1){
                wp_send_json_error(array('info'=>__('该教练已绑定,请核实信息','nlyd-student')));
            }
            if($row['zone_id'] == $current_user->ID){
                wp_send_json_error(array('info'=>__('该教练已绑定,请核实信息','nlyd-student')));
            }
        }

        $wpdb->query('START TRANSACTION');

        $a = $wpdb->insert($wpdb->prefix.'zone_join_coach',array('zone_id'=>$current_user->ID,'coach_id'=>$coach['ID']));

        if(!$wpdb->get_var("select id from {$wpdb->prefix}coach_skill where coach_id = {$coach['ID']} ")){
            $b = $wpdb->insert($wpdb->prefix.'coach_skill',array('coach_id'=>$coach['ID'],'read'=>'','memory'=>'','compute'=>''));
        }else{
            $b =true;
        }
        //print_r($a.'--'.$b);die;
        if($b && $a){
            $wpdb->query('COMMIT');

            $user = new WP_User( $coach['ID'] );
            if(!$user->has_cap( 'coach' )) $user->add_role( 'coach' );

            wp_send_json_success(array('info'=>__('添加成功','nlyd-student'),'url'=>home_url('/zone/coach/')));
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>__('添加失败','nlyd-student')));
        }
    }

    /**
     * 机构教练列表
     */
    public function zone_coach_list(){
        global $wpdb,$current_user;

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        $sql = "select * from {$wpdb->prefix}zone_join_coach where zone_id = {$current_user->ID} order by id desc limit $start,$pageSize";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无教练', 'nlyd-student')));
        if(!empty($rows)){

            foreach ($rows as $k => $v){
                $rows[$k]['order'] =  $start+$k+1;

                $sql_ = "select meta_key,meta_value from {$wpdb->prefix}usermeta where meta_key in('user_real_name','user_ID','user_gender','user_images_color','user_head') and user_id = {$v['coach_id']}";
                $res = $wpdb->get_results($sql_,ARRAY_A);
                $user_info = array_column($res,'meta_value','meta_key');
                //print_r($user_info);
                //获取工作照
                $coach_work_photo = !empty($user_info['user_images_color']) ? unserialize($user_info['user_images_color'])[0] : $user_info['user_head'] ;
                $rows[$k]['work_photo'] = !empty($coach_work_photo) ? $coach_work_photo : student_css_url.'image/nlyd.png';

                $rows[$k]['user_ID'] = !empty($user_info['user_ID']) ? $user_info['user_ID'] : $v['coach_id']+10000000;
                $rows[$k]['user_gender'] = !empty($user_info['user_gender']) ? $user_info['user_gender'] : '-' ;
                $user_real_name = unserialize($user_info['user_real_name']);
                $rows[$k]['real_name'] = !empty($user_real_name['real_name']) ? $user_real_name['real_name'] : '-' ;
                $rows[$k]['user_age'] = !empty($user_real_name['real_age']) ? $user_real_name['real_age'] : '-' ;
            }
        }
        //print_r($rows);
        wp_send_json_success(array('info'=>$rows));
    }

    /**
     *解除机构教练
     */
    public function zone_coach_relieve(){
        global $wpdb,$current_user;
        if(empty($_POST['coach_id'])){
            wp_send_json_error(array('info'=>__('教练id必传','nlyd-student')));
        }
        $sql = "select a.zone_id,a.coach_id,b.user_id,b.id from {$wpdb->prefix}zone_join_coach a 
                left join {$wpdb->prefix}my_coach b on a.coach_id = b.coach_id
                where a.coach_id = {$_POST['coach_id']} ";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($rows[0]['user_id'])){
            //获取当前机构下所有教练
            $sql_  = "select a.*,meta_key,b.meta_value from {$wpdb->prefix}zone_join_coach a 
                      left join {$wpdb->prefix}usermeta b on a.coach_id = b.user_id and meta_key = 'user_real_name' 
                      where a.zone_id = {$current_user->ID}";
            $rows_ = $wpdb->get_results($sql_,ARRAY_A);
            if(count($rows_) == 1){
                wp_send_json_error(array('info'=>__('该机构仅有一名教练,禁止解除','nlyd-student')));
            }
            $list = array();
            foreach ($rows_ as $k => $v){
                if($v['coach_id'] !== $_POST['coach_id']){
                    $arr['coach_id'] = $v['coach_id'];
                    $arr['real_name'] = unserialize($v['meta_value'])['real_name'];
                    $list[] = $arr;
                }
            }
            //print_r(json_encode($list));die;
            //$_POST['new_coach_id'] = 9;
            if(empty($_POST['new_coach_id'])){
                wp_send_json_success(array('list'=>json_encode($list)));
            }else{
                $x = $wpdb->update($wpdb->prefix.'my_coach',array('coach_id'=>$_POST['new_coach_id']),array('coach_id'=>$_POST['coach_id']));
                //print_r($x);die;
            }
        }
        //print_r($rows);die;
        $result = $wpdb->delete($wpdb->prefix.'zone_join_coach',array('zone_id'=>$current_user->ID,'coach_id'=>$_POST['coach_id']));
        if($result){
            wp_send_json_success(array('info'=>__('解绑成功','nlyd-student')));
        }else{
            wp_send_json_error(array('info'=>__('解绑失败','nlyd-student')));
        }
    }


    /**
     * 战队申请
     */
    public function team_apply(){
        if(empty($_POST['post_title']) || empty($_POST['team_director_phone'])){
            wp_send_json_error(array('info'=>__('战队名字/负责人必填','nlyd-student')));
        }
        //print_r($_POST);die;
        global $wpdb,$current_user;

        //判断战队负责人
        $sql = "select a.ID,b.meta_value from {$wpdb->prefix}users a 
                left join {$wpdb->prefix}usermeta b on a.ID = b.user_id and b.meta_key = 'user_real_name'
                where a.ID = '{$_POST['team_director_phone']}'
                ";
        $team_director = $wpdb->get_row($sql,ARRAY_A);
        if(empty($team_director)) wp_send_json_error(array('info'=>__('该负责人未注册','nlyd-student')));
        $real_name = unserialize($team_director['meta_value']);
        if(empty($real_name['real_name'])) wp_send_json_error(array('info'=>__('该负责人未实名认证','nlyd-student')));

        //开启事务
        $wpdb->query('START TRANSACTION');
        if(!empty($_POST['team_id'])){
            $a = $wpdb->update($wpdb->prefix.'posts',array('post_title'=>$_POST['post_title'],'post_modified'=>get_time('mysql')),array('ID'=>$_POST['team_id']));
            $b = $wpdb->update($wpdb->prefix.'team_meta',
                                    array(
                                        'team_world'=>!empty($_POST['team_world']) ? $_POST['team_world'] : '',
                                        'team_slogan'=>!empty($_POST['team_slogan']) ? $_POST['team_slogan'] : '',
                                        'team_director'=>!empty($team_director['ID']) ? $team_director['ID'] : '',
                                        'max_number'=>!empty($_POST['max_number']) ? $_POST['max_number'] : '',
                                        'team_leader'=>!empty($_POST['team_leader']) ? $_POST['team_leader'] : '',
                                        'team_brief'=>!empty($_POST['team_brief']) ? $_POST['team_brief'] : '',
                                        'created_time'=>get_time('mysql'),
                                    ),
                                    array('team_id'=>$_POST['team_id'])
                            );
        }else{

            $a = wp_insert_post(
                array(
                    'post_title' => $_POST['post_title'],
                    'post_type'     => 'team',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                )
            );
            $parent_id = !empty($_POST['type']) ? $current_user->ID : '';
            $user_id = !empty($_POST['type']) ? '' : $current_user->ID;
            $b = $wpdb->insert($wpdb->prefix.'team_meta',
                array(
                    'user_id'=>$user_id,
                    'team_id'=>$a,
                    'team_world'=>!empty($_POST['team_world']) ? $_POST['team_world'] : '',
                    'team_slogan'=>!empty($_POST['team_slogan']) ? $_POST['team_slogan'] : '',
                    'team_director'=>!empty($team_director['ID']) ? $team_director['ID'] : '',
                    'max_number'=>!empty($_POST['max_number']) ? $_POST['max_number'] : '',
                    'team_leader'=>!empty($_POST['team_leader']) ? $_POST['team_leader'] : '',
                    'team_brief'=>!empty($_POST['team_brief']) ? $_POST['team_brief'] : '',
                    'parent_id'=>!empty($parent_id) ? $parent_id : '',
                    'created_time'=>get_time('mysql'),
                )
            );
        }
        //print_r($a .'&&'. $b);die;
        if($a && $b){
            $wpdb->query('COMMIT');
            wp_send_json_success(array('info' => __('提交成功', 'nlyd-student')));
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>__('提交失败', 'nlyd-student')));
        }
    }

    /**
     * 获取机构战队
     */
    public function get_zone_teams(){
        global $wpdb,$current_user;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        $sql = "select a.team_id,a.parent_id,b.post_title from {$wpdb->prefix}team_meta a 
                left join {$wpdb->prefix}posts b on a.team_id = b.ID
                where a.user_id = {$current_user->ID} or a.parent_id = {$current_user->ID}  
                order by parent_id asc limit $start,$pageSize";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($sql);
        //print_r($sql);die;
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无战队', 'nlyd-student')));
        if(!empty($rows)){
            foreach ($rows as $k => $v){
                //获取申请
                $result = $wpdb->get_var("select count(*) total from {$wpdb->prefix}match_team where team_id = {$v['team_id']} and status in(1,-1)");
                $rows[$k]['apply_total'] = $result;
            }
        }
        //print_r($rows);
        wp_send_json_success(array('info'=>$rows));
    }

    /**
     * 为战队添加成员
     */
    public function add_team_personnel(){
        if(empty($_POST['user_phone']) || empty($_POST['team_id'])){
           wp_send_json_error(array('info'=>__('请选择需要添加的用户','nlyd-student')));
        }
        //查看当前用户是否已有战队
        global $wpdb,$current_user;

        //判断成员
        $sql = "select a.ID,b.meta_value from {$wpdb->prefix}users a 
                left join {$wpdb->prefix}usermeta b on a.ID = b.user_id and b.meta_key = 'user_real_name'
                where a.ID = '{$_POST['user_phone']}'
                ";

        $user = $wpdb->get_row($sql,ARRAY_A);
        if(empty($user)) wp_send_json_error(array('info'=>__('该学员未注册','nlyd-student')));
        $real_name = unserialize($user['meta_value']);
        if(empty($real_name['real_name'])) wp_send_json_error(array('info'=>__('该学员未实名认证','nlyd-student')));


        $result = $wpdb->get_var("select id from {$wpdb->prefix}match_team where user_id = {$user['ID']} and user_type = 1 and status > -2");
        if($result > 0){
            wp_send_json_error(array('info'=>__('该用户已有战队,请核实','nlyd-student')));
        }

        $res = $wpdb->insert($wpdb->prefix.'match_team',array('team_id'=>$_POST['team_id'],'user_id'=>$user['ID'],'user_type'=>1,'status'=>2,'created_time'=>get_time('mysql')));

        if($res){
            wp_send_json_success(array('info'=>__('添加成功','nlyd-student')));
        }else{
            wp_send_json_error(array('info'=>__('添加失败','nlyd-student')));
        }
    }

    /**
     * 战队成员列表
     */
    public function get_team_personnel(){
        /*if(empty($_POST['team_id'])){
            wp_send_json_error(array('info'=>__('战队id必传','nlyd-student')));
        }*/
        global $wpdb,$current_user;

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        $where = " and a.status = 2 ";
        if(!empty($_POST['map'])){
            $where = " and a.status in (-1,1) ";
        }

        $sql = "select a.*,c.user_mobile,
                case status
                when -1 then '退出'
                when 1 then '加入'
                end status_cn
                from {$wpdb->prefix}match_team a 
                left join {$wpdb->prefix}team_meta b on a.team_id = b.team_id
                left join {$wpdb->prefix}users c on a.user_id = c.ID
                where a.team_id = {$_POST['team_id']} {$where} 
                order by id desc limit $start,$pageSize";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无成员', 'nlyd-student')));
        foreach ($rows as $k => $v){
            $rows[$k]['order'] =  $start+$k+1;
            $sql_ = "select meta_key,meta_value from {$wpdb->prefix}usermeta where meta_key in('user_real_name','user_ID','user_gender','coach_work_photo','user_head') and user_id = {$v['user_id']}";
            $res = $wpdb->get_results($sql_,ARRAY_A);
            $user_info = array_column($res,'meta_value','meta_key');
            //print_r($user_info);

            $rows[$k]['user_ID'] = !empty($user_info['user_ID']) ? $user_info['user_ID'] : $v['coach_id']+10000000;
            $rows[$k]['user_gender'] = !empty($user_info['user_gender']) ? $user_info['user_gender'] : '-' ;
            $user_real_name = unserialize($user_info['user_real_name']);
            $rows[$k]['real_name'] = !empty($user_real_name['real_name']) ? $user_real_name['real_name'] : '-' ;
            $rows[$k]['user_age'] = !empty($user_real_name['real_age']) ? $user_real_name['real_age'] : '-' ;
            $rows[$k]['user_mobile'] = !empty($v['user_mobile']) ? $v['user_mobile'] : '-' ;
            if($v['status'] == 1){
                $rows[$k]['agree_status'] = 2;
                $rows[$k]['refuse_status'] = -2;
            }elseif ($v['status'] == -1){
                $rows[$k]['agree_status'] = -2;
                $rows[$k]['refuse_status'] = 2;
            }
        }
        wp_send_json_success(array('info'=>$rows));
    }

    /**
     *战队成员审核操作
     */
    public function team_personnel_operation(){

        if(empty($_POST['status']) || empty($_POST['id'])){
            wp_send_json_error(array('info'=>__('参数不全','nlyd-student')));
        }

        global $wpdb;
        $result = $wpdb->update($wpdb->prefix.'match_team',array('status'=>$_POST['status']),array('id'=>$_POST['id']));
        if($result){
            wp_send_json_success(array('info'=>__('操作成功','nlyd-student')));
        }else{
            wp_send_json_error(array('info'=>__('操作失败','nlyd-student')));
        }
    }


    /**
     * 战队解散
     */
    public function team_disband(){
        if(empty($_POST['type']) || empty($_POST['team_id'])) wp_send_json_error(array('info'=>__('参数不全','nlyd-student')));
        global $wpdb,$current_user;
        //获取默认战队
        $team_id = $wpdb->get_var("select team_id from {$wpdb->prefix}team_meta where user_id = {$current_user->ID}");
        if($team_id == $_POST['team_id']) wp_send_json_error(array('info'=>__('默认战队禁止删除','nlyd-student')));

        $wpdb->query('START TRANSACTION');

        $total = $wpdb->get_var("select count(*) from {$wpdb->prefix} where team_id = {$_POST['team_id']} ");
        if($total > 0){

            if($_POST['type']==2){
                $a = $wpdb->update($wpdb->prefix.'match_team',array('team_id'=>$team_id),array('team_id'=>$_POST['team_id']));
            }else{
                $a = $wpdb->delete($wpdb->prefix.'match_team',array('team_id'=>$_POST['team_id']));
            }
        }else{
            $a = true;
        }
        $b = $wpdb->delete($wpdb->prefix.'team_meta',array('team_id'=>$_POST['team_id']));
        $c = $wpdb->delete($wpdb->prefix.'posts',array('ID'=>$_POST['team_id']));
        //print_r($a .'&&'. $b.'&&'. $c);die;
        if($a && $b && $c){
            $wpdb->query('COMMIT');
            wp_send_json_success(array('info' => __('提交成功', 'nlyd-student'),'url'=>home_url('/zone/team/')));
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>__('提交失败', 'nlyd-student')));
        }
    }

    /**
     * 机构学员列表
     */
    public function zone_student_list(){

        global $wpdb,$current_user;

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        $sql = "select a.zone_id,b.user_id,c.referee_id from {$wpdb->prefix}course a 
                left join {$wpdb->prefix}order b on a.id = b.match_id 
                left join {$wpdb->prefix}users c on b.user_id = c.ID 
                where a.zone_id = {$current_user->ID} and order_type = 3 and pay_status in (2,3,4) 
                and c.ID is not null  GROUP BY user_id
                limit $start,$pageSize
                ";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($sql);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无学员', 'nlyd-student')));
        if(!empty($rows)){

            foreach ($rows as $k => $v){
                $rows[$k]['order'] =  $start+$k+1;

                $sql_ = "select meta_key,meta_value from {$wpdb->prefix}usermeta where meta_key in('user_real_name','user_ID','user_gender','user_images_color','user_head') and user_id = {$v['user_id']}";
                $res = $wpdb->get_results($sql_,ARRAY_A);
                $user_info = array_column($res,'meta_value','meta_key');
                //print_r($user_info);
                //获取工作照
                $coach_work_photo = !empty($user_info['user_images_color']) ? unserialize($user_info['user_images_color'])[0] : $user_info['user_head'] ;
                $rows[$k]['work_photo'] = !empty($coach_work_photo) ? $coach_work_photo : student_css_url.'image/nlyd.png';

                $rows[$k]['user_ID'] = !empty($user_info['user_ID']) ? $user_info['user_ID'] : $v['coach_id']+10000000;
                $rows[$k]['user_gender'] = !empty($user_info['user_gender']) ? $user_info['user_gender'] : '-' ;
                $user_real_name = unserialize($user_info['user_real_name']);
                $rows[$k]['real_name'] = !empty($user_real_name['real_name']) ? $user_real_name['real_name'] : '-' ;
                $rows[$k]['user_age'] = !empty($user_real_name['real_age']) ? $user_real_name['real_age'] : '-' ;
                $rows[$k]['referee_id'] = !empty($v['referee_id'] > 0 ) ? $v['referee_id']+10000000 : '-' ;
            }
        }
        //print_r($rows);
        wp_send_json_success(array('info'=>$rows));
    }


    /**
     * 管理员跳转登录
     */
    public function login_zone(){

        if(empty($_POST['zone_id'])){
            wp_send_json_error(array('info'=>__('机构id必传','nlyd-student')));
        }
        global $wpdb,$current_user;
        $sql_ = "select b.user_id
                from {$wpdb->prefix}zone_manager a 
                left join {$wpdb->prefix}zone_meta b on a.zone_id = b.id 
                where a.user_id = {$current_user->ID} and  b.id = {$_POST['zone_id']}";
        //print_r($sql_);die;
        $zone_user_id = $wpdb->get_var($sql_);
        if(empty($zone_user_id)){
            wp_send_json_error(array('info'=>__('未查询到关联机构','nlyd-student')));
        }else{
            $_SESSION['manager_id'] = $current_user->ID;
            $this->setUserCookie($zone_user_id);
            wp_send_json_success( array('info'=>__('登录成功', 'nlyd-student'),'url'=>home_url('/zone/')));
        }
    }

    /**
     * 切回管理员用户中心
     */
    public function change_user(){
        if($_SESSION['manager_id'] > 0){
            $this->setUserCookie($_SESSION['manager_id']);
            unset($_SESSION['manager_id']);
            wp_send_json_success( array('info'=>__('登录成功', 'nlyd-student'),'url'=>home_url('/account/')));
        }else{
            wp_send_json_error(array('info'=>__('未检测到管理员信息', 'nlyd-student')));
        }
    }

    /**
     * 设置收款
     */
    public function set_receivables(){
        global $current_user;

        switch ($_POST['type']){
            case 'bank':
                    if(empty($_POST['open_name']) || empty($_POST['open_bank']) || empty($_POST['open_address']) || empty($_POST['open_card_num']) ){
                        wp_send_json_error(array('info'=>__('所有参数必填','nlyd-student')));
                    }
                    unset($_POST['type']);
                    unset($_POST['action']);
                $result = update_user_meta($current_user->ID,'user_cheques_bank',$_POST);
                break;
            case 'weChat':
                if(!empty($_POST['images_weChat'])){

                    $upload_dir = wp_upload_dir();
                    $dir = '/QRcode/'.$current_user->ID.'/';
                    $num = 0;
                    foreach ($_POST['images_weChat'] as $va){
                        $file = $this->base64file($va,$upload_dir['basedir'].$dir);

                        if($file){
                            $_POST['user_coin_code'][] = $upload_dir['baseurl'].$dir.$file;
                            ++$num;
                        }
                    }

                    //$result = update_user_meta($current_user->ID,'user_coin_code',$_POST['user_coin_code']);
                }else{
                    //wp_send_json_error(array('info'=>__('请选择收款码','nlyd-student')));
                }
                $result = update_user_meta($current_user->ID,'user_coin_code',$_POST['user_coin_code']);
                break;
            case 'aliPay':
                if(!empty($_POST['images_aliPay'])){
                    $upload_dir = wp_upload_dir();
                    $dir = '/QRcode/'.$current_user->ID.'/';
                    $num = 0;
                    foreach ($_POST['images_aliPay'] as $va){
                        $file = $this->base64file($va,$upload_dir['basedir'].$dir);

                        if($file){
                            $_POST['aliPay_coin_code'][] = $upload_dir['baseurl'].$dir.$file;
                            ++$num;
                        }
                    }
                    //print_r($_POST['user_coin_code']);
                    //$result = update_user_meta($current_user->ID,'aliPay_coin_code',$_POST['aliPay_coin_code']);
                }else{
                    //wp_send_json_error(array('info'=>__('请选择收款码','nlyd-student')));
                }
                $result = update_user_meta($current_user->ID,'aliPay_coin_code',$_POST['aliPay_coin_code']);
                break;
            default:
                wp_send_json_error(array('info'=>__('参数错误','nlyd-student')));
                break;
        }
        if($result){
            wp_send_json_success(array('info'=>__('更新成功','nlyd-student')));
        }else{
            wp_send_json_error(array('info'=>__('更新失败','nlyd-student')));
        }

    }

    /**
     * 通过手机获取用户
     */
    public function get_mobile_user(){

        global $wpdb,$current_user;
        if(reg_match('m',$_POST['mobile'])) wp_send_json_error(array(__('手机格式不正确', 'nlyd-student')));
        $sql = "select a.ID,b.meta_value from {$wpdb->prefix}users a 
                left join {$wpdb->prefix}usermeta b on a.ID = b.user_id and b.meta_key = 'user_real_name'
                where a.user_mobile = '{$_POST['mobile']}'
                ";
        $row = $wpdb->get_row($sql,ARRAY_A);

        if(empty($row)) wp_send_json_error(array('info'=>__('该用户未注册','nlyd-student')));
        $real_name = unserialize($row['meta_value']);
        if(empty($real_name['real_name'])) wp_send_json_error(array('info'=>__('该用户未实名认证','nlyd-student')));
        if($_POST['type'] == 'course'){
            $user = new WP_User( $row['ID'] );
            $id = $wpdb->get_var("select id from {$wpdb->prefix}zone_join_coach where coach_id = {$row['ID']} and zone_id = {$current_user->ID}");
            if(!$user->has_cap( 'coach' ) || empty($id)){
                wp_send_json_error(array('info'=>__('该教练不是该机构教练,请先添加为机构教练','nlyd-student')));
            }
        }

        wp_send_json_success(array('user_id'=>$row['ID'],'user_name'=>$real_name['real_name']));
    }

    /**
     * 获取比赛签到情况
     */
    public function get_sign_list(){
        if(empty($_POST['match_id'])) wp_send_json_error(array('info'=>__('参数错误','nlyd-student')));
        global $wpdb,$current_user;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        $sql = "select a.match_id,a.user_id,if(c.id > 0,c.id,0) sign_id from {$wpdb->prefix}order a 
                left join {$wpdb->prefix}match_meta_new b on a.match_id = b.match_id
                left join {$wpdb->prefix}match_sign c on a.user_id = c.user_id
                where a.match_id = {$_POST['match_id']} and pay_status in (2,3,4) and b.created_id = {$current_user->ID}
                group by a.user_id limit {$start},{$pageSize}
                ";
        //print_r($sql);die;
        $rows = $wpdb->get_results($sql,ARRAY_A);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无教练', 'nlyd-student')));
        if(!empty($rows)){

            foreach ($rows as $k => $v){
                $rows[$k]['order'] =  $start+$k+1;

                $sql_ = "select meta_key,meta_value from {$wpdb->prefix}usermeta where meta_key in('user_real_name','user_ID','user_gender','user_images_color','user_head') and user_id = {$v['user_id']}";
                $res = $wpdb->get_results($sql_,ARRAY_A);
                $user_info = array_column($res,'meta_value','meta_key');
                //print_r($user_info);
                //获取工作照
                $coach_work_photo = !empty($user_info['user_images_color']) ? unserialize($user_info['user_images_color'])[0] : $user_info['user_head'] ;
                $rows[$k]['work_photo'] = !empty($coach_work_photo) ? $coach_work_photo : student_css_url.'image/nlyd.png';

                $rows[$k]['user_gender'] = !empty($user_info['user_gender']) ? $user_info['user_gender'] : '-' ;
                $user_real_name = unserialize($user_info['user_real_name']);
                $rows[$k]['real_name'] = !empty($user_real_name['real_name']) ? $user_real_name['real_name'] : '-' ;
                $rows[$k]['user_age'] = !empty($user_real_name['real_age']) ? $user_real_name['real_age'] : '-' ;
                $rows[$k]['sign_cn'] = !empty($v['sign_id']) ? '已签' : '未签' ;
                $rows[$k]['sign_color'] = !empty($v['sign_id']) ? 'c_green' : 'c_black6' ;
            }
        }
        //print_r($rows);
        wp_send_json_success(array('info'=>$rows));
    }


    /**
     * 机构课程发布
     */
    public function zone_course_created(){
        if(empty($_POST['course_type']) || empty($_POST['course_category_id']) || empty($_POST['course_title']) || empty($_POST['duration']) || empty($_POST['coach_phone']) || empty($_POST['const']) ){
            wp_send_json_error(array('info'=>__('必填项不能为空','nlyd-student')));
        }

        if(!empty($_POST['course_start_time']) && !empty($_POST['course_end_time'])){
            if($_POST['course_start_time'] > $_POST['course_end_time']) wp_send_json_error(array('info'=>__('开课时间不能大于结课时间','nlyd-student')));
        }

        global $wpdb,$current_user;
        if(!empty($_POST['coach_phone'])){

            $sql = "select a.ID,b.meta_value from {$wpdb->prefix}users a 
                left join {$wpdb->prefix}usermeta b on a.ID = b.user_id and b.meta_key = 'user_real_name'
                where a.ID = '{$_POST['coach_phone']}'
                ";
            $coach = $wpdb->get_row($sql,ARRAY_A);
            if(empty($coach)) wp_send_json_error(array('info'=>__('该教练未注册','nlyd-student')));
            $real_name = unserialize($coach['meta_value']);
            if(empty($real_name['real_name'])) wp_send_json_error(array('info'=>__('该教练未实名认证','nlyd-student')));

            //判断教练是否有机构或者教练是否是特派教练
            $user = new WP_User( $coach['ID'] );
            $id = $wpdb->get_var("select id from {$wpdb->prefix}zone_join_coach where coach_id = {$coach['ID']} and zone_id = {$current_user->ID}");
            if(!$user->has_cap( 'coach' ) || empty($id)){
                wp_send_json_error(array('info'=>__('该教练不是该机构教练,请先添加为机构教练','nlyd-student')));
            }
        }
        $data = array(
            'course_title'=>$_POST['course_title'],
            'course_details'=>!empty($_POST['course_details']) ? $_POST['course_details'] : '',
            'const'=>$_POST['const'] > 0 ? $_POST['const'] : 0,
            'is_enable'=>$_POST['is_enable'] > 0 ? $_POST['is_enable'] : 1,
            'coach_id'=>$coach['ID'],
            'course_start_time'=>!empty($_POST['course_start_time']) ? $_POST['course_start_time'] : '',
            'course_end_time'=>!empty($_POST['course_end_time']) ? $_POST['course_end_time'] : '',
            'open_quota'=>$_POST['open_quota'] > 0 ? $_POST['open_quota'] : '',
            'zone_id'=>$current_user->ID,
            'course_type'=>$_POST['course_type'],
            'address'=>!empty($_POST['address']) ? $_POST['address'] : '',
            'duration'=>$_POST['duration'],
            'course_category_id'=>$_POST['course_category_id'],
        );
        if($_POST['id'] > 0){
            $a = $wpdb->update($wpdb->prefix.'course',$data,array('id'=>$_POST['id'],'zone_id'=>$current_user->ID));
        }else{
            $data['created_time'] = get_time('mysql');
            $a = $wpdb->insert($wpdb->prefix.'course',$data);

        }
        //更新教练技能
        $alias = get_post_meta($_POST['course_category_id'],'project_alias')[0];
        if(!empty($alias)){
            switch ($alias){
                case 'memory':
                    $array['memory'] = $_POST['course_category_id'];
                    break;
                case 'reading':
                    $array['read'] = $_POST['course_category_id'];
                    break;
                case 'arithmetic':
                    $array['compute'] = $_POST['course_category_id'];
                    break;
                default:
                    $array = '';
                    break;
            }
            if(!empty($array)){
                $wpdb->update($wpdb->prefix.'coach_skill',$array,array('coach_id'=>$coach['ID']));
            }
        }

        if($a){
            wp_send_json_success(array('info'=>__('操作完成','nlyd-student'),'url'=>home_url('/zone/course/')));
        }else{
            wp_send_json_error(array('info'=>__('操作失败','nlyd-student')));
        }
    }


    /**
     * 获取机构发布的课程
     */
    public function get_zone_course(){
        global $wpdb,$current_user;
        if(isset($_POST['id'])){
            $zone_user_id = $_POST['id'];
        }else{
            $zone_user_id = $current_user->ID;
        }
        $map[] = " a.zone_id = {$zone_user_id} ";
        if($_POST['course_type'] == 'history'){
            $map[] = " a.is_enable < -2 ";
        }elseif ($_POST['course_type'] == 'matching'){
            $map[] = " a.is_enable != -3 ";
        }elseif ($_POST['course_type'] == 1){
            $map[] = " a.is_enable = 1 ";
        }elseif ($_POST['course_type'] == 2){
            $map[] = " a.is_enable in (2,-2) ";
        }elseif ($_POST['course_type'] == -3){
            $map[] = " a.is_enable = -3 ";
        }
        $where = join("and",$map);

        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        $sql = "select a.id,a.course_title,a.const,count(c.id) entry_total,b.type_name,a.open_quota,a.is_enable,a.coach_id,
                if(unix_timestamp(course_start_time)>0,date_format(course_start_time,'%Y-%m-%d %H:%i'),'待定') start_time,
                if(unix_timestamp(course_end_time)>0,date_format(course_end_time,'%Y-%m-%d %H:%i'),'待定') end_time,d.post_title category_title,
                case a.is_enable
                when '-3' then '已结课'
                when '-2' then '等待开课'
                when '1' then '报名中'
                when '2' then '授课中'
                end status_cn
                from {$wpdb->prefix}course a 
                left join {$wpdb->prefix}course_type b on a.course_type = b.id
                left join {$wpdb->prefix}order c on a.id = c.match_id and c.pay_status in (2,3,4)
                left join {$wpdb->prefix}posts d on a.course_category_id = d.ID 
                where {$where} 
                group by a.id
                order by a.course_start_time desc ,a.is_enable desc
                limit $start,$pageSize
               ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无课程', 'nlyd-student')));
        foreach ($rows as $k => $val){

            $user_real_name = get_user_meta($val['coach_id'],'user_real_name')[0];
            $rows[$k]['real_name'] = !empty($user_real_name) ? $user_real_name['real_name'] : '-';
            if(isset($_POST['id'])){
                //获取城市
                $zone_city = $wpdb->get_var("select zone_city from {$wpdb->prefix}zone_meta where user_id = {$zone_user_id} ");
                $city_arr = str2arr($zone_city,'-');
                if(!empty($city_arr[2])){
                    $city = rtrim($city_arr[1],'市').preg_replace('/区|县/','',$city_arr[2]);
                }elseif ($city_arr[1] != '市辖区'){
                    $city = rtrim($city_arr[1],'市');
                }else{
                    $city = rtrim($city_arr[0],'市');
                }
                $rows[$k]['zone_city'] = $city;
                //判断是否购课
                $order_id = $wpdb->get_var("select id from {$wpdb->prefix}order where user_id = {$current_user->ID} and match_id = {$val['id']} and order_type = 3 and pay_status in (2,3,4)");
                $rows[$k]['order_id'] = $order_id;
                if($val['open_quota'] <= $val['entry_total']){
                    $rows[$k]['is_full'] = 'y';
                }
                //判断剩余名额
                if($val['open_quota'] > 0){
                    $rows[$k]['surplus'] = $val['entry_total'] - $val['open_quota'] >= 0 ? 0 :$val['open_quota'] - $val['entry_total'];
                }else{
                    $rows[$k]['surplus'] = '无限制';
                }
            }
        }
        wp_send_json_success(array('info'=>$rows));
    }

    /**
     * 机构结课
     */
    public function zone_close_course(){
        if(empty($_POST['id'])) wp_send_json_error(array('info'=>__('课程id不能为空','nlyd-student')));
        global $wpdb,$current_user;
        $a = $wpdb->update($wpdb->prefix.'course',array('is_enable'=>-3,'course_end_time'=>get_time('mysql')),array('id'=>$_POST['id'],'zone_id'=>$current_user->ID));
        if($a){
            wp_send_json_success(array('info'=>__('关闭成功','nlyd-student'),'url'=>home_url('/zone/course/')));
        }else{
            wp_send_json_error(array('info'=>'关闭失败'));
        }
    }

    /**
     * 获取课程报名学员
     */
    public function get_course_student(){
        if(empty($_POST['id'])) wp_send_json_error(array('info'=>__('课程id不能为空','nlyd-student')));
        global $wpdb,$current_user;

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        $sql = "select a.match_id,a.user_id,c.user_mobile,c.referee_id,b.course_category_id from {$wpdb->prefix}order a 
                left join {$wpdb->prefix}course b on a.match_id = b.id
                left join {$wpdb->prefix}users c on a.user_id = c.ID
                where b.zone_id = {$current_user->ID} and a.match_id = {$_POST['id']} 
                and a.order_type = 3 and pay_status in (2,3,4)
                limit $start,$pageSize";
        $rows = $wpdb->get_results($sql,ARRAY_A);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无学员', 'nlyd-student')));
        if(!empty($rows)){

            foreach ($rows as $k => $v){
                $rows[$k]['order'] =  $start+$k+1;

                $sql_ = "select meta_key,meta_value from {$wpdb->prefix}usermeta where meta_key in('user_real_name','user_ID','user_gender','user_images_color','user_head') and user_id = {$v['user_id']}";
                $res = $wpdb->get_results($sql_,ARRAY_A);
                $user_info = array_column($res,'meta_value','meta_key');
                //print_r($user_info);
                $rows[$k]['user_ID'] = !empty($user_info['user_ID']) ? $user_info['user_ID'] : $v['coach_id']+10000000;
                $rows[$k]['user_gender'] = !empty($user_info['user_gender']) ? $user_info['user_gender'] : '-' ;
                $user_real_name = unserialize($user_info['user_real_name']);
                $rows[$k]['real_name'] = !empty($user_real_name['real_name']) ? $user_real_name['real_name'] : '-' ;
                $rows[$k]['user_age'] = !empty($user_real_name['real_age']) ? $user_real_name['real_age'] : '-' ;
                //$referee_id = $wpdb->get_var("select referee_id from {$wpdb->prefix}users where ID = {$v['user_id']} ");
                $rows[$k]['referee_id'] = !empty($v['referee_id'] > 0 ) ? $v['referee_id']+10000000 : '-' ;
                //获取考级是否达标
                $project_alias = get_post_meta($v['course_category_id'],'project_alias');
                if($project_alias == 'reading'){

                }
                elseif ($project_alias == 'memory'){

                }
                else{
                    //arithmetic
                }
            }
        }
        //print_r($rows);
        wp_send_json_success(array('info'=>$rows));
    }

    /**
     * 获取课程报名学员
     */
    public function get_course_student_list(){
        if(empty($_POST['id'])) wp_send_json_error(array('info'=>__('课程id不能为空','nlyd-student')));
        global $wpdb;

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        $sql = "select a.match_id,a.user_id,b.course_category_id from {$wpdb->prefix}order a 
                left join {$wpdb->prefix}course b on a.match_id = b.id
                left join {$wpdb->prefix}users c on a.user_id = c.ID
                where a.match_id = {$_POST['id']} 
                and a.order_type = 3 and pay_status in (2,3,4)
                limit $start,$pageSize";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($sql);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无学员', 'nlyd-student')));
        if(!empty($rows)){

            foreach ($rows as $k => $v){
                $rows[$k]['order'] =  $start+$k+1;

                $sql_ = "select meta_key,meta_value from {$wpdb->prefix}usermeta where meta_key in('user_real_name','user_ID','user_gender','user_images_color','user_head') and user_id = {$v['user_id']}";
                $res = $wpdb->get_results($sql_,ARRAY_A);
                $user_info = array_column($res,'meta_value','meta_key');
                //print_r($user_info);
                $rows[$k]['user_ID'] = !empty($user_info['user_ID']) ? $user_info['user_ID'] : $v['coach_id']+10000000;
                $rows[$k]['user_gender'] = !empty($user_info['user_gender']) ? $user_info['user_gender'] : '-' ;
                $user_real_name = unserialize($user_info['user_real_name']);
                $rows[$k]['real_name'] = !empty($user_real_name['real_name']) ? $user_real_name['real_name'] : '-' ;
                $rows[$k]['user_age'] = !empty($user_real_name['real_age']) ? $user_real_name['real_age'] : '-' ;
                $referee_id = $wpdb->get_var("select referee_id from {$wpdb->prefix}users where ID = {$v['user_id']} ");
                $rows[$k]['referee_id'] = !empty($referee_id > 0 ) ? $referee_id+10000000 : '-' ;
            }
        }
        //print_r($rows);
        wp_send_json_success(array('info'=>$rows));
    }


    /**
     * 获取首页课程导航下机构列表
     */
    public function get_course_zone(){
        global $wpdb;
        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        //if($_POST['city'])

        if(!empty($_POST['city'])){
            $where = " and a.zone_city like '%{$_POST['city']}%' ";
            $sql= "select a.user_id,a.zone_number,a.zone_name,a.zone_city,a.type_id,c.zone_type_name,
                count(b.id) course_total
                from {$wpdb->prefix}zone_meta a 
                left join {$wpdb->prefix}course b on a.user_id = b.zone_id and b.is_enable = 1
                left join {$wpdb->prefix}zone_type c on a.type_id = c.id
                where a.user_status = 1 and c.zone_type_alias = 'trains' {$where}
                GROUP BY user_id
                order by b.id desc
                limit $start,$pageSize
              ";
            $rows = $wpdb->get_results($sql ,ARRAY_A);
            if(empty($rows)) wp_send_json_error(array('info'=>__('该地区暂无课程', 'nlyd-student')));

        }else{

            $sql= "select a.user_id,a.zone_number,a.zone_name,a.zone_city,a.type_id,c.zone_type_name,
                count(b.id) course_total
                from {$wpdb->prefix}zone_meta a 
                left join {$wpdb->prefix}course b on a.user_id = b.zone_id and b.is_enable = 1
                left join {$wpdb->prefix}zone_type c on a.type_id = c.id
                where a.user_status = 1 and c.zone_type_alias = 'trains' 
                GROUP BY user_id
                order by b.id desc
                limit $start,$pageSize
              ";
            $rows = $wpdb->get_results($sql ,ARRAY_A);
            $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
            $maxPage = ceil( ($total['total']/$pageSize) );
            if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
            if(empty($rows)) wp_send_json_error(array('info'=>__('暂无训练中心', 'nlyd-student')));
        }


        foreach ($rows as $k => $val){
            $city_arr = str2arr($val['zone_city'],'-');
            if(!empty($city_arr[2])){
                $city = rtrim($city_arr[1],'市').preg_replace('/市|区|县/','',$city_arr[2]);
            }elseif ($city_arr[1] != '市辖区'){
                $city = rtrim($city_arr[1],'市');
            }else{
                $city = rtrim($city_arr[0],'市');
            }
            $rows[$k]['content'] = '（NO.'.$val['zone_number'].'.'.$city.'）';
        }
        wp_send_json_success(array('info'=>$rows));
    }

    /**
     * 获取结课成绩
     */
    public function get_class_ending(){

        if(empty($_POST['id'])) wp_send_json_error(array('info'=>__('课程id不能为空','nlyd-student')));
        global $wpdb;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        $sql = "select a.match_id,a.user_id,b.course_category_id,a.cost 
                 from {$wpdb->prefix}order a 
                 left join {$wpdb->prefix}course b on a.match_id = b.id
                 where a.match_id = {$_POST['id']} and a.order_type = 3 and a.pay_status in (2,3,4) ";
        $rows = $wpdb->get_results($sql,ARRAY_A);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无学员', 'nlyd-student')));
        if(!empty($rows)){
            foreach ($rows as $k => $val){
                //判断考级是否达标
                $sql_ = "select compute,memory,`read` from {$wpdb->prefix}user_skill_rank where user_id = {$val['user_id']} and skill_type = 1";
                $row = $wpdb->get_row($sql_,ARRAY_A);
                //print_r($row);die;
                if(!empty($row)){
                    $project_alias = get_post_meta($val['course_category_id'],'project_alias')[0];
                    //print_r($project_alias);
                    switch ($project_alias){
                        case 'arithmetic': //算
                            $rows[$k]['is_skill'] = $row['compute'] > 2 ? 'y' : 'n';
                            break;
                        case 'memory': //记
                            $rows[$k]['is_skill'] = $row['memory'] > 2 ? 'y' : 'n';
                            break;
                        case 'reading': //读
                            $rows[$k]['is_skill'] = $row['read'] > 2 ? 'y' : 'n';
                            break;
                        default:
                            $rows[$k]['is_skill'] = 'n';
                            break;
                    }
                }else{
                    $rows[$k]['is_skill'] = 'n';
                }
                //判断推荐是否达标
                $referee_sql = "select count(*) from {$wpdb->prefix}users a 
                                 left join {$wpdb->prefix}order b on a.ID = b.user_id
                                 where a.referee_id = {$val['user_id']} and b.match_id = {$_POST['id']} and b.order_type = 3 and b.pay_status in (2,3,4) 
                                ";
                //print_r($referee_sql);die;
                $referee_total = $wpdb->get_var($referee_sql);
                //$referee_total = 8;
                $rows[$k]['is_share'] = $referee_total > 3 ? 'y' : 'n';

                //学费补贴
                $rows[$k]['tuition_subsidy'] = $rows[$k]['is_share'] =='y' && $rows[$k]['is_skill'] =='y' ? $val['cost'] : '-';

                if($referee_total > 0){
                    $course_sql = "select c.direct_superior from {$wpdb->prefix}course a 
                                    left join {$wpdb->prefix}course_type b on a.course_type = b.id 
                                    left join {$wpdb->prefix}spread_set c on b.type_alias = c.spread_type 
                                    where a.id = {$val['match_id']}";
                    $direct_superior = $wpdb->get_var($course_sql);
                    //print_r($direct_superior);
                    //print_r($course_sql);die;
                    //推荐奖励
                    $rows[$k]['referee_reward'] = $referee_total > 0 && $direct_superior > 0 ? $referee_total*$direct_superior : '-';
                }

                if($val['user_id'] > 0){
                    $real_name = get_user_meta($val['user_id'],'user_real_name')[0]['real_name'];
                    $rows[$k]['real_name'] = !empty($real_name) ? $real_name : '-';
                }
                $rows[$k]['user_ID'] = $val['user_id'] + 10000000;
            }
        }
        //print_r($rows);
        wp_send_json_success(array('info'=>$rows));
    }

    //获取我的课程
    public function get_my_course(){

        global $wpdb,$current_user;

        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 50;
        $start = ($page-1)*$pageSize;

        $sql = "select a.id order_id,a.user_id,b.id course_id,b.course_title,b.const,b.open_quota,b.is_enable,b.coach_id,b.zone_id,c.type_name,
                if(unix_timestamp(b.course_start_time)>0,date_format(b.course_start_time,'%Y-%m-%d %H:%i'),'待确认') start_time,
                if(unix_timestamp(b.course_end_time)>0,date_format(b.course_end_time,'%Y-%m-%d %H:%i'),'待确认') end_time,
                case b.is_enable
                when '-3' then '已结课'
                when '-2' then '等待开课'
                when '1' then '报名中'
                when '2' then '授课中'
                end status_cn
                from {$wpdb->prefix}order a 
                left join {$wpdb->prefix}course b on a.match_id = b.id
                LEFT JOIN {$wpdb->prefix}course_type c on b.course_type = c.id
                WHERE a.user_id = {$current_user->ID} and a.pay_status in (2,3,4) and b.id is not null
                order by b.is_enable desc
                limit $start,$pageSize
                ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>__('已经到底了', 'nlyd-student')));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>__('暂无课程', 'nlyd-student')));
        foreach ($rows as $k => $val){

            $user_real_name = get_user_meta($val['coach_id'],'user_real_name')[0];
            $rows[$k]['real_name'] = !empty($user_real_name) ? $user_real_name['real_name'] : '-';
            if(isset($_POST['id'])){
                //获取城市
                $zone_city = $wpdb->get_var("select zone_city from {$wpdb->prefix}zone_meta where user_id = {$val['zone_id']} ");
                $city_arr = str2arr($zone_city,'-');
                if(!empty($city_arr[2])){
                    $city = rtrim($city_arr[1],'市').preg_replace('/区|县/','',$city_arr[2]);
                }elseif ($city_arr[1] != '市辖区'){
                    $city = rtrim($city_arr[1],'市');
                }else{
                    $city = rtrim($city_arr[0],'市');
                }
                $rows[$k]['zone_city'] = $city;
            }
        }
        wp_send_json_success(array('info'=>$rows));
    }

    //机构管理员的添加/删除
    public function set_zone_manager(){

        if(empty($_POST['type'])){
            wp_send_json_error(array('info'=>__('参数不全')));
        }
        global $wpdb,$current_user;
        //获取机构id
        $zone_id = $wpdb->get_var("select id from {$wpdb->prefix}zone_meta where user_id = {$current_user->ID}");
        if(empty($zone_id)) wp_send_json_error(array('info'=>__('机构信息错误')));
        if($_POST['type'] == 'set'){

            $sql = "select a.ID,b.meta_value from {$wpdb->prefix}users a 
                left join {$wpdb->prefix}usermeta b on a.ID = b.user_id and b.meta_key = 'user_real_name'
                where a.ID = '{$_POST['user_phone']}'
                ";
            $manager = $wpdb->get_row($sql,ARRAY_A);
            if(empty($manager)) wp_send_json_error(array('info'=>__('该用户未注册','nlyd-student')));
            $real_name = unserialize($manager['meta_value']);
            if(empty($real_name['real_name'])) wp_send_json_error(array('info'=>__('该用户未实名认证','nlyd-student')));
            $manager_id = $wpdb->get_var("select id from {$wpdb->prefix}zone_manager where zone_id = {$zone_id} and user_id = {$manager['ID']}");
            if($manager_id){
                wp_send_json_error(array('info'=>__('该用户已是该机构管理员')));
            }
            $a = $wpdb->insert($wpdb->prefix.'zone_manager',array('zone_id'=>$zone_id,'user_id'=>$manager['ID']));
        }else{
            if(empty($_POST['id'])){
                wp_send_json_error(array('info'=>__('id不能为空')));
            }
            $total = $wpdb->get_var("select count(*) from {$wpdb->prefix}zone_manager where zone_id = {$zone_id} ");

            if($total < 2 ){
                wp_send_json_error(array('info'=>__('禁止删除所有管理员')));
            }
            $a = $wpdb->delete($wpdb->prefix.'zone_manager',array('id'=>$_POST['id'],'zone_id'=>$zone_id));
        }
        if($a){
            wp_send_json_success(array('info'=>__('操作成功'),'url'=>home_url('/zone/setting/')));
        }else{
            wp_send_json_error(array('info'=>__('操作失败')));
        }
    }

    /*
    *比较字符串不同的字符
    *@参数：$str1:第一个字符串，$str2:第二个字符串
    *@返回值：不同字符串的数组，
    */
    public function diffStr($str1,$str2){
        /*$arr1 = str2arr($str1);
        $arr2 = str2arr($str2);*/
        preg_match_all("/./u", $str1, $arr1);
        preg_match_all("/./u", $str2, $arr2);
        /*print_r($arr1);
        print_r($arr2);*/
        $result=array_diff_assoc($arr1[0],$arr2[0]);
        //print_r($result);die;
        return $result;
    }

}

new Student_Ajax();