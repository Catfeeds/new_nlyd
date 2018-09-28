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

        if(empty($_POST['numbers'])) wp_send_json_error(array('info'=>'参数不能为空'));
        if(!is_array($_POST['numbers'])) wp_send_json_error(array('info'=>'参数必须是数组'));
        if(empty($_POST['match_more']) || empty($_POST['project_alias'])) wp_send_json_error(array('info'=>'参数错误'));

        if(empty($_POST['my_answer'])){
            global $current_user;
            $redis = new Redis();
            $redis->connect('127.0.0.1',6379,1);
            $redis->auth('leo626');
            $new_time = get_time();
            $default_count_down = $redis->get('count_down'.$current_user->ID.$_POST['project_alias'].$_POST['match_more'])-1;
            $redis->setex('count_down'.$current_user->ID.$_POST['project_alias'].$_POST['match_more'],$default_count_down-$new_time,$default_count_down);
            wp_send_json_error(array('info'=>$default_count_down-$new_time));

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

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_ranking_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }

        //获取当前项目排名
        global $wpdb,$current_user;

        $page = ($page = intval($_POST['page'])) < 1 ? 1 : $page;
        $pageSize = 10;
        $start = ($page-1) * $pageSize;

        if($_POST['type'] == 'project'){

            if(empty($_GET['project_id']) || empty($_GET['match_more'])){
                wp_send_json_error(array('info'=>'参数错误'));
            }
            $sql = "select user_id,my_score from {$wpdb->prefix}match_questions where match_id = {$_GET['match_id']} and project_id = {$_GET['project_id']} and match_more = {$_GET['match_more']} order by my_score desc,surplus_time desc limit {$start},{$pageSize} ";
            //print_r($sql);
            $rows = $wpdb->get_results($sql,ARRAY_A);
        }
        else{

            $where = " WHERE a.match_id = {$_POST['match_id']} AND a.pay_status = 4 and a.order_type = 1 ";
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
            }

            if(!empty($_POST['match_more'])){
                $left_where .= " and c.match_more = {$_POST['match_more']} ";
            }

            $sql3 = "SELECT x.user_id,SUM(x.my_score) my_score ,SUM(x.surplus_time) surplus_time 
                        FROM(
                            SELECT a.user_id,a.match_id,c.project_id,if(MAX(c.my_score) > 0 ,MAX(c.my_score),0) my_score , if(MAX(c.surplus_time) ,MAX(c.surplus_time) ,0) surplus_time 
                            FROM `{$wpdb->prefix}order` a 
                            LEFT JOIN {$wpdb->prefix}match_questions c ON a.user_id = c.user_id  and c.match_id = {$_POST['match_id']} {$left_where}
                            #where a.match_id = 56329
                            {$where}
                            GROUP BY user_id,project_id
                        ) x
                        left join `{$wpdb->prefix}usermeta` y on x.user_id = y.user_id and y.meta_key='user_age'
                        {$age_where}
                        GROUP BY user_id
                        ORDER BY my_score DESC,surplus_time DESC,x.user_id DESC";
            
            /*if($current_user->ID == 63){
                print_r($sql);
            }*/
            $rows = $wpdb->get_results($sql3,ARRAY_A);
            //print_r($rows);
            
        }
        $total = count($rows);
        $remainder = $total%$pageSize;
        $maxPage = ceil($total/$pageSize);

        if($_POST['page'] > $maxPage && $total != 0) wp_send_json_error(array('info'=>'已经到底了'));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>'暂无列表信息'));

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
                    $city = $user_address['city'] == '市辖区' ? $user_address['city'] : $user_address['province'];
                }else{
                    $city = '-';
                }

                $list[$k]['ID'] = $user_info['user_ID'];
                $list[$k]['city'] = $city;
                //$list[$k]['score'] = $val['my_score'];
                $list[$k]['group'] = $group;
                $list[$k]['score'] = $val['my_score'] > 0 ? $val['my_score'] : 0;
                $list[$k]['surplus_time'] = $val['surplus_time'] > 0 ? $val['surplus_time'] : 0;
                $list[$k]['ranking'] = $start+$k+1;

                $my_score = $val['my_score'] > 0 ? $val['my_score'] : 0;
                $surplus_time = $val['surplus_time'] > 0 ? $val['surplus_time'] : 0;
                if($k != 0){
                    if(($my_score == $rows[$k-1]['my_score'] && $surplus_time == $rows[$k-1]['surplus_time']) || ($my_score == 0 &&  $rows[$k-1]['my_score'] == 0)){
                        $list[$k]['ranking'] = $list[$k-1]['ranking'];
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

                if($val['user_id'] == $current_user->ID){
                    $my_ranking = $list[$k];
                }
            }
        }
        if($maxPage == $_POST['page']){
            $pageSize = $remainder;
        }  
            
        $list2 = array_slice($list,$start,$pageSize);

        wp_send_json_success(array('info'=>$list2,'my_ranking'=>$my_ranking));

    }

    /**
     * 答案提交
     */
    public function answer_submit(){

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_answer_submit_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        ini_set('post_max_size','10M');

        if(empty($_POST['match_more'])) $_POST['match_more'] = 1;

        // var_dump($_POST['match_id']);
        // var_dump($_POST['project_id']);
        // var_dump($_POST['match_more']);
        // var_dump($_POST['match_action']);
        // var_dump($_POST['surplus_time']);
        // print_r($_POST);
        if(empty($_POST['match_id']) || empty($_POST['project_id']) || empty($_POST['match_more']) || empty($_POST['match_action']) || !isset($_POST['surplus_time'])) wp_send_json_error(array('info'=>'参数错误'));
        if(!empty($_POST['my_answer'])){
            if(!is_array($_POST['my_answer'])) wp_send_json_error(array('info'=>'答案请以数组格式提交'));
            $my_answer = $_POST['my_answer'];
        }else{
            $my_answer = array();
        }

        global $wpdb,$current_user;
        $sql = "select answer_status,questions_answer
                from {$wpdb->prefix}match_questions
                where user_id = {$current_user->ID} and match_id = {$_POST['match_id']} and project_id = {$_POST['project_id']} and match_more = {$_POST['match_more']}
                ";
        $row = $wpdb->get_row($sql,ARRAY_A);
        //print_r($sql);
        if(empty($row)) wp_send_json_error(array('info'=>'数据错误'));
        if($row['answer_status'] == 1) wp_send_json_error(array('info'=>'答案已提交'));

        //计算成绩
        //print_r($_POST['match_action']);die;
        $update_arr = array();
        switch ($_POST['match_action']){
            case 'subjectNumberBattle':    //数字争霸
                $questions_answer = json_decode($row['questions_answer']);
                $len = count($questions_answer);
                //print_r($questions_answer);

                $error_len = count(array_diff_assoc($questions_answer,$my_answer));
                $my_score = ($len-$error_len)*12;

                if ($error_len == 0){
                    $my_score += $_POST['surplus_time'] * 1;
                }
                break;
            case 'subjectPokerRelay':    //扑克接力
                $questions_answer = json_decode($row['questions_answer']);
                $len = count($questions_answer);
                //print_r($questions_answer);

                $error_len = count(array_diff_assoc($questions_answer,$my_answer));
                $my_score = ($len-$error_len)*18;

                if ($error_len == 0){
                    $my_score += $_POST['surplus_time'] * 1;
                }
                break;
            case 'subjectfastScan':    //快眼扫描
            case 'subjectFastCalculation':    //正向速算
            case 'subjectFastReverse': //逆向速算

                $data_arr = $_POST['my_answer'];

                if(!empty($data_arr)){
                    $match_questions = array_column($data_arr,'question');
                    $questions_answer = array_column($data_arr,'rights');
                    $my_answer = array_column($data_arr,'yours');
                }
                if($_POST['match_action'] == 'subjectFastReverse'){
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
                    $error_len = count(array_diff_assoc($questions_answer,$my_answer));
                    $my_score = ($len-$error_len)*10;
                }

                //print_r($questions_answer);die;
                $update_arr['match_questions'] = json_encode($match_questions);
                $update_arr['questions_answer'] = json_encode($questions_answer);
                break;
            case 'subjectReading': //文章速读
                $questions_answer = json_decode($row['questions_answer'],true);
                $len = count($questions_answer);
                $success_len = 0;

                foreach ($questions_answer as $k=>$val){
                    $arr = array();
                    foreach ($val['problem_answer'] as $key => $v){
                        if($v == 1){
                            $arr[] = $key;
                        }
                    }

                    if(isset($my_answer[$k])){
                        if(arr2str($arr) == arr2str($my_answer[$k])) ++$success_len;
                    }
                }
                $my_score = $success_len * 23;
                if ($success_len == $len){
                    $my_score += $_POST['surplus_time'] * 1;
                }
                $redis = new Redis();
                $redis->connect('127.0.0.1',6379,1);
                $redis->auth('leo626');

                if(!empty($redis->get('wzsd_question'.$current_user->ID))){
                    $result = json_decode($redis->get('wzsd_question'.$current_user->ID),true);
                    $post_id = $result->ID;
                    $redis->del('wzsd_question'.$current_user->ID);
                }else{

                    $result = json_decode($row['questions_answer'],true);
                    $key = array_keys($result);
                    $sql = "select post_parent from {$wpdb->prefix}posts where ID = {$key[0]}";
                    $post_id = $wpdb->get_var($sql);

                }

                //修改其分类
                //$a = wp_set_object_terms( $post_id, array('test-question') ,'question_genre');
                //var_dump($a);die;
                break;
            default:
                wp_send_json_error(array('info'=>'未知错误'));
                break;
        }
        $update_arr['answer_status'] = 1;
        $update_arr['my_answer'] = json_encode($my_answer);
        $update_arr['surplus_time'] = $_POST['surplus_time'];
        $update_arr['my_score'] = $my_score;
        $update_arr['submit_type'] = isset($_POST['submit_type']) ? $_POST['submit_type'] : 1;
        $update_arr['leave_page_time'] = isset($_POST['leave_page_time']) ? json_encode($_POST['leave_page_time']) : '';
        /*print_r($update_arr);
        die;*/
        $result = $wpdb->update($wpdb->prefix.'match_questions',$update_arr,array('user_id'=>$current_user->ID,'match_id'=>$_POST['match_id'],'project_id'=>$_POST['project_id'],'match_more'=>$_POST['match_more']));
        if($result){
            //wp_send_json_success(array('info'=>'提交完成','url'=>home_url('matchs/'.$_POST['match_action'].'/match_id/'.$_POST['match_id'].'/project_id/'.$_POST['project_id'].'/match_more/'.$_POST['match_more'])));
            wp_send_json_success(array('info'=>'提交完成','url'=>home_url('matchs/answerLog/match_id/'.$_POST['match_id'].'/project_id/'.$_POST['project_id'].'/match_more/'.$_POST['match_more'])));
        }else {
            wp_send_json_error(array('info' => '提交失败'));
        }
    }

    /**
     * 记忆完成提交
     */
    public function memory_complete(){

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_memory_complete_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        if(empty($_POST['match_id']) || empty($_POST['project_id']) || empty($_POST['match_more'])) wp_send_json_error(array('info'=>'参数错误'));

        global $wpdb,$current_user;
        $result = $wpdb->update($wpdb->prefix.'match_questions',array('answer_status'=>-1),array('user_id'=>$current_user->ID,'match_id'=>$_POST['match_id'],'project_id'=>$_POST['project_id'],'match_more'=>$_POST['match_more']));

        $answer_status = $wpdb->get_var("select answer_status from {$wpdb->prefix}match_questions where user_id = {$current_user->ID} and match_id = {$_POST['match_id']} and project_id = {$_POST['project_id']} and match_more = {$_POST['match_more']}");

        if($result || $answer_status == -1){
            $url = home_url('matchs/answerMatch/match_id/'.$_POST['match_id'].'/project_id/'.$_POST['project_id'].'/match_more/'.$_POST['match_more']);
            if(isset($_POST['questions_id']) && !empty($_POST['questions_id'])){
                $url .= '&questions_id='.$_POST['questions_id'];
            }
            wp_send_json_success(array('info'=>'即将跳转','url'=>$url));
        }else{
            wp_send_json_error(array('info'=>'记忆失败'));
        }
    }

    /**
     * 获取最新比赛倒计时
     */
    public function get_count_down(){

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_count_down_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        global $wpdb,$current_user;
        //获取最新比赛倒计时
        $sql1 = "select a.match_id,a.match_start_time,b.user_id from {$wpdb->prefix}match_meta a
                 left join {$wpdb->prefix}order b on a.match_id = b.match_id and b.user_id = {$current_user->ID} and b.pay_status in(2,3,4) 
                 where match_status = -2 order by match_start_time asc ";
        //print_r($sql1);
        $row = $wpdb->get_row($sql1);
        if(!empty($row)){
            $row = time_format(strtotime($row->match_start_time),'Y-m-d H:i:s');

            wp_send_json_success(array('info'=>$row));
        }

        wp_send_json_error(array('info'=>'最近暂无比赛'));
    }

    /**
     * 报名支付
     */
    public function entry_pay(){

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_go_pay_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        if(empty($_POST['match_id']) || empty($_POST['project_id'])  || !isset($_POST['cost'])) wp_send_json_error(array('info'=>'参数错误'));

        global $wpdb,$current_user;
        if($current_user->ID < 1 || !$current_user->ID){
            wp_send_json_error(array('info'=>'请登录'));
        }

        if(empty(get_user_meta($current_user->ID,'user_real_name'))){
            wp_send_json_error(array('info'=>'请先实名认证'));
        }

        //if(count($_POST['project_id']) != count($_POST['major_coach'])) wp_send_json_error(array('info'=>'主训教练未设置齐全'));
        //if(empty($_POST['team_id'])) wp_send_json_error(array('info'=>'所属战队不能为空'));
        //if(empty($_POST['fullname'])) wp_send_json_error(array('info'=>'收件人姓名不能为空'));
        //if(empty($_POST['telephone'])) wp_send_json_error(array('info'=>'联系电话不能为空'));
        //if(empty($_POST['address'])) wp_send_json_error(array('info'=>'收货地址不能为空'));

        $sql = "select match_id,match_status,match_max_number from {$wpdb->prefix}match_meta where match_id = {$_POST['match_id']} ";
        $match_meta = $wpdb->get_row($sql,ARRAY_A);
        if(empty($match_meta)) wp_send_json_error(array('info'=>'比赛信息错误'));
        if($match_meta['match_status'] != 1) wp_send_json_error(array('info'=>'当前比赛已禁止报名'));
        $total = $wpdb->get_var("select count(id) total from {$wpdb->prefix}order where match_id = {$_POST['match_id']} ");
        if($match_meta['match_max_number'] > 0){

            if(!empty($total)){
                if($total >= $match_meta['match_max_number']) wp_send_json_error(array('info'=>'已达到最大报名数,请联系管理员'));
            }
        }

        $row = $wpdb->get_row("select id,pay_status from {$wpdb->prefix}order where user_id = {$current_user->ID} and match_id = {$_POST['match_id']}");

        if(!empty($row)) {
            if($row->pay_status == 2 || $row->pay_status==3 || $row->pay_status==4){
                wp_send_json_error(array('info'=>'你已报名该比赛','url'=>home_url('matchs/info/match_id/'.$_POST['match_id'])));
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
            'order_type'=>1,
            'pay_status'=>1,
            'created_time'=>get_time('mysql'),
        );
        //TODO 测试时 订单价格为0
//        $_POST['cost'] = 0;
        //如果报名金额为0, 直接支付成功状态
        if($_POST['cost'] == 0 || $_POST['cost'] < 0.01){
            $data['pay_status'] = 4;
        }

        //print_r($data);die;
        //开启事务
        $wpdb->startTrans();
        $a = $wpdb->insert($wpdb->prefix.'order',$data);

        //生成流水号
        $serialnumber = createNumber($current_user->ID,$wpdb->insert_id);
        $b = $wpdb->update($wpdb->prefix.'order',array('serialnumber'=>$serialnumber),array('id'=>$wpdb->insert_id));


        if($b && $a ){
            $wpdb->commit();
            if($data['pay_status'] == 2 || $data['pay_status'] == 4){
                wp_send_json_success(array('info' => '报名成功','serialnumber'=>$serialnumber, 'is_pay' => 0, 'url' => home_url('payment/success/serialnumber/'.$serialnumber)));
            }
            wp_send_json_success(array('info' => '请选择支付方式','serialnumber'=>$serialnumber,'is_pay' => 1));
        }else{

            $wpdb->rollback();
            wp_send_json_error(array('info'=>'提交失败'));
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
        $pageSize = 10;
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
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>'已经到底了'));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>'暂无列表信息'));

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
                $rows[$k]['mental'] = '待定';
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

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_set_team_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        //$_POST['team_id'] = 407;
        if(empty($_POST['team_id'])) wp_send_json_error(array('info'=>'参数错误'));
        global $wpdb,$current_user;
        //判断是否登录
        if($current_user->ID < 1) wp_send_json_error(array('info'=>'未登录'));
        //判断是否有战队
        $sql = "select id,team_id,user_id,status from {$wpdb->prefix}match_team where user_id = {$current_user->ID} ";
        //开启事务,发送短信失败回滚
        $wpdb->startTrans();
        if($_POST['handle'] == 'join'){ //加入战队
            $sql .= " and status > -2 ";
            $row = $wpdb->get_row($sql);
            if(!empty($row)){
                switch ($row->status){
                    case -1:
                        $info = '离队申请正在审核,暂时不能申请加入战队';
                        break;
                    case 1:
                        $info = '入队申请正在审核,暂时不能申请加入战队';
                        break;
                    case 2:
                        $info = '已有战队,暂时不能申请加入战队,请先申请离队';
                        break;
                }
                $wpdb->rollback();
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
            $sql .= " and team_id = {$_POST['team_id']} and status = 2 ";
            //print_r($sql);die;
            $row = $wpdb->get_row($sql);
            if(empty($row)){
                $wpdb->rollback();
                wp_send_json_error(array('info'=>'你还没有加入任何战队'));
            }

            $result = $wpdb->update($wpdb->prefix.'match_team',array('status'=>-1),array('user_id'=>$current_user->ID,'team_id'=>$_POST['team_id']));
            $msgTemplate = 12;
        }
        if($result){
            $wpdb->commit();
            /***短信通知战队负责人****/
            $director = $wpdb->get_row('SELECT u.user_mobile,u.display_name,u.ID AS uid FROM '.$wpdb->prefix.'team_meta AS tm 
            LEFT JOIN '.$wpdb->users.' AS u ON u.ID=tm.team_director WHERE tm.team_id='.$_POST['team_id'], ARRAY_A);
            $userID = get_user_meta($current_user->ID, 'user_ID')[0];
            $ali = new AliSms();
//            print_r($director);die;
            $result = $ali->sendSms($director['user_mobile'], $msgTemplate, array('teams'=>str_replace(', ', '', $director['display_name']), 'user_id' => $userID));
            /***********end************/
            wp_send_json_success(array('info'=>'操作成功,等待战队受理'));
        }
        $wpdb->rollback();
        wp_send_json_error(array('info'=>'操作失败'));

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
        $pageSize = 10;
        $start = ($page-1)*$pageSize;

        $sql = "select SQL_CALC_FOUND_ROWS a.user_id,a.user_type,
                a.status,IFNULL(b.read,'-') as `read`,
                IFNULL(b.memory,'-') as memory,
                IFNULL(b.compute,'-') as compute
                from {$wpdb->prefix}match_team a
                left join {$wpdb->prefix}user_skill_rank b on a.user_id = b.user_id 
                where a.user_type = {$type} and a.status = 2 and a.team_id = {$team_id} 
                limit {$start},{$pageSize}
                ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );

        if($_POST['page'] > $maxPage && $total['total'] != 0 ) wp_send_json_error(array('info'=>'已经到底了'));
        if(empty($rows)) wp_send_json_error(array('info'=>'暂无列表信息'));

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
                $rows[$k]['mental'] = '待定';
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

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_team_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        global $wpdb,$current_user;
        $map = array();
        $map[] = " a.post_status = 'publish' ";
        $map[] = " a.post_type = 'team' ";
        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 10;
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
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>'已经到底了'));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>'暂无战队'));
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

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_choose_address_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        $rows = $this->get_address(false);

        wp_send_json_success(array('info'=>home_url('matchs/confirm&match_id='.$_POST['match_id'].'&address_id='.$_POST['id'])));
    }


    /**
     * 设置默认地址
     */
    public function set_default_address(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_set_default_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }

        if(empty($_POST['id'])) wp_send_json_error(array('info'=>'参数错误'));

        $this->get_address(false);

        global $wpdb,$current_user;
        $wpdb->startTrans();
        $a = $wpdb->update($wpdb->prefix.'my_address',array('is_default'=>''),array('user_id'=>$current_user->ID));

        $b = $wpdb->update($wpdb->prefix.'my_address',array('is_default'=>1),array('id'=>$_POST['id'],'user_id'=>$current_user->ID));
        if($a && $b){
            //提交事务
            $wpdb->commit();
            wp_send_json_success(array('info'=>'设置成功'));
        }else{
            //事务回滚
            $wpdb->rollback();
            wp_send_json_error(array('info'=>'设置失败'));
        }
    }

    /**
     * 获取单条地址信息
     */
    public function get_address($json=true,$id=''){

        if($json){

            if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_address_code_nonce') ) {
                wp_send_json_error(array('info'=>'非法操作'));
            }
        }

        if(empty($_POST['id'])) wp_send_json_error(array('info'=>'参数错误'));

        global $wpdb,$current_user;
        $sql = "select id,fullname,telephone,country,province,city,area,address,is_default from {$wpdb->prefix}my_address where id = {$_POST['id']} and user_id = {$current_user->ID} ";

        $row = $wpdb->get_row($sql,ARRAY_A);
        if(empty($row)) wp_send_json_error(array('info'=>'数据错误'));

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
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_remove_address_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        if(empty($_POST['id'])) wp_send_json_error(array('info'=>'参数错误'));
        global $wpdb,$current_user;
        $result = $wpdb->delete($wpdb->prefix.'my_address',array('id'=>$_POST['id'],'user_id'=>$current_user->ID));
        if($result){
            wp_send_json_success(array('info'=>'删除成功'));
        }else{
            wp_send_json_error(array('info'=>'删除失败'));
        }
    }


    /**
     * 新增/修改地址
     */
    public function save_address(){

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_save_address_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }

        global $wpdb,$current_user;

        if(mb_strlen($_POST['fullname']) < 1 || mb_strlen($_POST['fullname']) > 100) wp_send_json_error(array('收件人长度为1-100个字符'));
        if(reg_match('m',$_POST['telephone'])) wp_send_json_error(array('手机格式不正确'));

        if(empty($_POST['province']) || empty($_POST['city']) || empty($_POST['area']) || empty($_POST['address'])) wp_send_json_error(array('info'=>'请确认地址信息的完整性'));

        $_POST['user_id'] = $current_user->ID;

        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}my_address WHERE user_id = {$current_user->ID}");
        if(empty($_POST['id'])){
            if($total == 10) wp_send_json_error(array('info'=>'最多只能添加10个收货地址'));
        }
        $wpdb->startTrans();

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
            $wpdb->commit();
            $url = home_url('account/address');
            if(!empty($match_id)) $url .= '/match_id/'.$match_id;
            $data['info'] = '保存成功';
            $data['url'] = $url;
            wp_send_json_success($data);
        }else{
            $wpdb->rollback();
            wp_send_json_error(array('info'=>'保存失败'));
        }

    }

    /**
     * 获取教练列表
     */
    public function get_coach_lists($category_id='',$user_id='',$json=true){

        global $wpdb,$current_user;

        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 10;
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
        //$user_id = 3;
        if(!empty($_POST['user_id'])){
            $user_id = $_POST['user_id'];
            $wap[] = " a.user_id = {$user_id} ";
            $wap[] = " a.apply_status = 2 ";
        }
        if(!empty($wap)){
            $where = join(' and ',$wap);
        }

        if(!empty($_POST['user_id'])){

            $sql = " select SQL_CALC_FOUND_ROWS a.id,a.user_id,a.coach_id,b.display_name,c.read,c.memory,c.compute
                from {$wpdb->prefix}my_coach a 
                left join {$wpdb->prefix}users b on a.coach_id = b.ID
                left join {$wpdb->prefix}coach_skill c on a.coach_id = c.coach_id
                where {$where} order by a.major desc limit $start,$pageSize
                ";
        }else{

            if(empty($category_id)){
                $category_id = $category[0]['ID'];
            }
            $where = "a.read = {$category_id} or a.memory = {$category_id} or a.compute = {$category_id}";
            $sql = "select SQL_CALC_FOUND_ROWS b.display_name,a.coach_id,a.read,a.memory,a.compute
                    from {$wpdb->prefix}coach_skill a 
                    left join {$wpdb->prefix}users b on a.coach_id = b.ID  
                    where {$where} 
                    limit $start,$pageSize
                    ";
        }
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($rows);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>'已经到底了'));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>'暂无列表信息'));

        if(!empty($rows)){
            foreach ($rows as $k=>$val){
                //获取教练信息
                $sql1 = "select meta_key,meta_value from {$wpdb->prefix}usermeta where meta_key in('user_ID','user_head','user_gender','user_coach_level') and user_id = {$val['coach_id']} ";
                $meta = $wpdb->get_results($sql1,ARRAY_A);
                //print_r($sql1);
                //print_r($meta);
                if(!empty($meta)){
                    $user_meta = array_column($meta,'meta_value','meta_key');
                    //print_r($user_meta);
                }
                $rows[$k]['display_name'] = preg_replace('/, /','',$val['display_name']);
                $rows[$k]['user_gender'] = !empty($user_meta['user_gender']) ? $user_meta['user_gender'] : '-';
                $rows[$k]['user_ID'] = !empty($user_meta['user_ID']) ? $user_meta['user_ID'] : '-';
                $rows[$k]['user_head'] = !empty($user_meta['user_head']) ? $user_meta['user_head'] : student_css_url.'image/nlyd.png';
                $rows[$k]['user_coach_level'] = !empty($user_meta['user_coach_level']) ? $user_meta['user_coach_level'] : '高级教练';

                //判断是否为我的教练/主训
                $sql2 = "select * from {$wpdb->prefix}my_coach where user_id = {$current_user->ID} and coach_id = {$val['coach_id']} and category_id = {$category_id} and apply_status != -1";
                //print_r($sql2);
                $my_coach = $wpdb->get_row($sql2,ARRAY_A);
                // print_r($my_coach);

                $rows[$k]['my_coach'] = 'n';
                $rows[$k]['my_major_coach'] = 'n';

                $rows[$k]['category_id'] = $category_id;
                $rows[$k]['apply_status'] = $my_coach['apply_status'];
                $rows[$k]['coach_url'] = home_url('/teams/coachDetail/coach_id/'.$val['coach_id']);

                if(!empty($my_coach)){
                    if($my_coach['apply_status'] == 2){
                        $rows[$k]['my_coach'] = 'y';
                        $rows[$k]['my_major_coach'] = $my_coach['major'] == 1 ? 'y' : 'n';
                    }
                }
                //每种分类对应的状态
                $categoryArr = ['read', 'memory', 'compute'];
                foreach ($categoryArr as $cateK => $cate){
//                    $readApply = $wpdb->get_row('SELECT mc.apply_status,p.post_title,mc.major FROM '.$wpdb->prefix.'my_coach AS mc LEFT JOIN '.$wpdb->posts.' AS p ON p.ID=mc.category_id WHERE mc.category_id='.$rows[$k][$cate].' AND mc.user_id='.$current_user->ID.' AND coach_id='.$val['coach_id']);
//                      $readApply = $wpdb->get_row('SELECT post_title FROM '.$wpdb->prefix.'posts WHERE ID='.$val[$cate]);
                    switch ($cate){
                        case 'read':
                            $post_title = '速读类';
                            break;
                        case 'memory':
                            $post_title = '速记类';
                            break;
                        case 'compute':
                            $post_title = '速算类';
                            break;
                    }
                    $rows[$k]['category'][$cateK]['name'] = $cate;
                    $rows[$k]['category'][$cateK]['post_title'] = $post_title;
                    $rows[$k]['category'][$cateK]['category_id'] = $rows[$k][$cate];
                    $rows[$k]['category'][$cateK]['is_current'] = 'false';//此教练是否在当前分类
                    $rows[$k]['category'][$cateK]['is_apply'] = 'false'; //是否申请中
                    $rows[$k]['category'][$cateK]['is_my_coach'] = 'false'; //是否已通过
                    $rows[$k]['category'][$cateK]['is_my_major'] = 'false'; //是否是主训
                    $rows[$k]['category'][$cateK]['is_relieve'] = 'false'; //是否已解除
                    $rows[$k]['category'][$cateK]['is_refuse'] = 'false';//是否已拒绝
                    if($rows[$k][$cate] != 0 && $rows[$k][$cate] != null){
                        $rows[$k]['category'][$cateK]['is_current'] = 'true';//此教练是否在当前分类
                        $coachStudent = $wpdb->get_row('SELECT apply_status,major FROM '.$wpdb->prefix.'my_coach WHERE category_id='.$rows[$k][$cate].' AND user_id='.$current_user->ID.' AND coach_id='.$val['coach_id']);
                        if($coachStudent){
                            switch ($coachStudent->apply_status){
                                case 1://申请中
                                    $rows[$k]['category'][$cateK]['is_apply'] = 'true';
                                    break;
                                case 2://已通过
                                    $rows[$k]['category'][$cateK]['is_my_coach'] = 'true';
                                    $rows[$k]['category'][$cateK]['is_my_major'] = $coachStudent->major == 1 ? 'true' : 'false';
                                    break;
                                case 3://已解除
                                    $rows[$k]['category'][$cateK]['is_relieve'] = 'true';
                                    break;
                                case -1://已拒绝
                                    $rows[$k]['category'][$cateK]['is_refuse'] = 'true';
                                    break;
                            }
                        }
                    }
                }
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
        $sql = "select ID,post_title from {$wpdb->prefix}posts where post_type = 'match-category' and post_status = 'publish' order by menu_order asc  ";

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

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_set_coach_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        global $wpdb,$current_user;
        //判断是否登录
        if($current_user->ID < 1) wp_send_json_error(array('info'=>'未登录'));

        if(empty($_POST['category_id']) || empty($_POST['coach_id'])) wp_send_json_error(array('info'=>'参数错误'));
        //不允许申请自己为教练
        if($_POST['coach_id'] == $current_user->ID) wp_send_json_error(array('info'=>'不能申请自己为教练'));
        //是否同时设置为主训教练
        $major = intval($_POST['major']) == 1 ? 1 : 0;
//        var_dump($_POST['category_id']);die;
        //查询以前是否进行过申请
        $id = $wpdb->get_var("select id from {$wpdb->prefix}my_coach where user_id = {$current_user->ID} and category_id = {$_POST['category_id']} and coach_id = {$_POST['coach_id']}");
        //开启事务,发送短信失败回滚


        if(empty($id)){
            $data = array('category_id'=>$_POST['category_id'],'coach_id'=>$_POST['coach_id'],'user_id'=>$current_user->ID,'apply_status'=>1, 'major' => $major);
            $result = $wpdb->insert($wpdb->prefix.'my_coach',$data);
        }else{
            $result = $wpdb->update($wpdb->prefix.'my_coach',array('apply_status'=>1,'major'=>$major),array('id'=>$id,'category_id'=>$_POST['category_id'],'user_id'=>$current_user->ID));
        }



        if($result){
            /***********发送短信通知教练*************/
            //获取教练信息
            $coach = $wpdb->get_row('SELECT user_mobile,display_name,ID AS uid FROM '.$wpdb->users.' WHERE ID='.$_POST['coach_id'], ARRAY_A);
            $post_title = $wpdb->get_var('SELECT post_title FROM '.$wpdb->posts.' WHERE ID='.$_POST['category_id']);
            $userID = get_user_meta($current_user->ID, '', true)['user_ID'][0];
            $ali = new AliSms();
            $result = $ali->sendSms($coach['user_mobile'], 13, array('coach'=>str_replace(', ', '', $coach['display_name']), 'user' => $userID ,'cate' => $post_title));
            /******************end*******************/
            wp_send_json_success(array('info'=>'申请成功,请等待教练同意'));
        }
        wp_send_json_error(array('info'=>'申请失败'));
    }


    /**
     *设置/取消主训教练
     */
    public function set_major_coach(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_set_major_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        if( empty($_POST['coach_id']) ||  empty($_POST['category_id'])) wp_send_json_error(array('info'=>'参数错误'));

        global $wpdb,$current_user;
        //判断是否登录
        if($current_user->ID < 1) wp_send_json_error(array('info'=>'未登录'));

        //获取教练信息
        $row = $wpdb->get_row("select id,user_id,category_id,apply_status,major from {$wpdb->prefix}my_coach where coach_id = {$_POST['coach_id']} and user_id = $current_user->ID and category_id = {$_POST['category_id']} and apply_status=2",ARRAY_A);

        if(empty($row)) wp_send_json_error(array('info'=>'数据错误'));
        if($row['apply_status'] != 2) wp_send_json_error(array('该教练还不是你的教练'));
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
            }else{
                $url = '';
            }
            wp_send_json_success(array('info'=>'操作成功','url'=>$url));
        }else{

            wp_send_json_error(array('info'=>'操作失败'));
        }
    }



    /**
     * 报名参赛
     */
    public function entry_match(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_entry_match_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        if(empty($_POST['match_id'])) wp_send_json_error(array('info'=>'参数错误'));

        global $wpdb,$current_user;
        $sql = "select id from {$wpdb->prefix}order where user_id = {$current_user} and match_id = {$_POST['match_id']}";
        $row = $wpdb->get_row($sql);
        if(!empty($row)) wp_send_json_error(array('info'=>'你已报名该比赛,禁止重复报名'));
        wp_send_json_success(array('info'=>home_url('matchs/confirm/match_id/'.$_POST['match_id'])));
    }


    /**
     * 比赛详情页报名选手列表获取
     */
    public function get_entry_list(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_entry_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }

        //获取报名选手列表
        global $wpdb,$current_user;

        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 10;
        $start = ($page-1)*$pageSize;

        $sql2 = "select SQL_CALC_FOUND_ROWS a.id,a.user_id,a.created_time 
                  from {$wpdb->prefix}order a
                  right join {$wpdb->prefix}users b on a.user_id = b.ID
                  where a.match_id = {$_POST['match_id']} and (a.pay_status=2 or a.pay_status=3 or a.pay_status=4)
                  order by a.id desc limit {$start},{$pageSize} ";
        $orders = $wpdb->get_results($sql2,ARRAY_A);
        /*if($current_user->ID == 66){
            print_r($sql2);
             print_r($orders);
        }*/
        //print_r($orders);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>'已经到底了'));
        if(empty($orders)) wp_send_json_error(array('info'=>'暂无选手报名'));
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
        $pageSize = 10;
        $start = ($page-1)*$pageSize;

        $sql_ = "select SQL_CALC_FOUND_ROWS a.ID,a.post_title,a.post_content,b.match_start_time,
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
                  left join {$wpdb->prefix}match_meta b on c.match_id = b.match_id 
                  where user_id = {$current_user->ID} and (pay_status=2 or pay_status=3 or pay_status=4) 
                  order by b.match_status desc limit $start,$pageSize
                  ";
        //print_r($sql_);
        $rows = $wpdb->get_results( $sql_,ARRAY_A);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>'已经到底了'));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>'暂无比赛'));

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
                //两个链接
                if($val['match_status'] == 2){
                    //比赛中
                    $url = home_url('/matchs/matchWaitting/match_id/'.$val['ID']);
                    $button_title = '进入比赛';
                }else if ($val['match_status'] == 1){
                    //报名中
                    $url = '';
                    $button_title = '已报名参赛';
                }
                else if ($val['match_status'] == -1){
                    //未开始
                    $url = '';
                }else if($val['match_status'] == -3){
                    //已结束
                    $button_title = '查看战绩';
                    $url = home_url('matchs/record/match_id/'.$val['ID']);
                }else{
                    //等待开赛
                    $url = home_url('matchs/matchWaitting/match_id/'.$val['ID']);
                    $button_title = '等待开赛';
                }
                $rows[$k]['button_title'] = $button_title;
                $rows[$k]['right_url'] = $url;
                $rows[$k]['left_url'] = home_url('matchs/info/match_id/'.$val['ID']);
                $rows[$k]['new_time'] = str2arr(get_time('mysql'),'-');
            }
        }

        wp_send_json_success(array('info'=>$rows));
    }

    /**
     * 获取比赛列表
     */
    public function get_match_list(){

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_match_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
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
        $sql1 = "select match_start_time from {$wpdb->prefix}match_meta where match_status = -2 order by match_start_time desc ";

        $row = $wpdb->get_row($sql1);
        if(!empty($row)){
            $start_time = $row->match_start_time;
            $new_match['new_match_arr'] = str2arr(time_format(strtotime($start_time),'Y-m-d-H-i-s'),'-');
            $new_match['match_type'] = $match_type;
        }

        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 10;
        $start = ($page-1)*$pageSize;

        $where = join(' and ',$map);

        $sql = "select SQL_CALC_FOUND_ROWS a.ID,a.post_title,a.post_content,b.match_start_time,
                if(b.match_address = '','--',b.match_address) match_address,
                b.match_cost,b.entry_end_time,b.match_status ,c.user_id
                from {$wpdb->prefix}posts a
                left join {$wpdb->prefix}match_meta b on a.ID = b.match_id
                left join {$wpdb->prefix}order c on a.ID = c.match_id and c.user_id = {$current_user->ID} and (c.pay_status=2 or c.pay_status=3 or c.pay_status=4) 
                where {$where} order by {$order} limit $start,$pageSize;
                ";
        //print_r($sql);
        /*if($current_user->ID == 66){
                print_r($sql);
        }*/
        
        $rows = $wpdb->get_results($sql,ARRAY_A);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>'已经到底了'));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>'暂无比赛'));
        foreach ($rows as $k => $val){

            //修改比赛状态
            $match = get_match_end_time($val['ID']);
            $end_time = end($match)['project_end_time'];
            /*if($current_user->ID == 66){
                print_r($val);
                var_dump($end_time);
                echo "<hr/>";
            }*/
            if(strtotime($val['entry_end_time']) < get_time() && get_time() < strtotime($val['match_start_time'])){
                $val['match_status'] = $match_status = -2;  //等待开赛
            }elseif (get_time() < strtotime($val['entry_end_time'])){
                $val['match_status'] = $match_status = 1;      //报名中
            }elseif (get_time() > strtotime($end_time)){
                $val['match_status'] = $match_status = -3;  //已结束
            }else{
                $val['match_status'] = $match_status = 2;   //比赛中
            }

            
            $a = $wpdb->update($wpdb->prefix.'match_meta',array('match_status'=>$match_status),array('match_id'=>$val['ID']));

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
                $button_title = '进入比赛';
                $rows[$k]['match_status_cn'] = '比赛中';
            }
            else if ($val['match_status'] == 1){
                //报名中
                $url = home_url('matchs/confirm/match_id/'.$val['ID']);
                $button_title = '参赛报名';
                $rows[$k]['match_status_cn'] = '报名中';
            }
            else if ($val['match_status'] == -1){
                //未开始
                $url = '';
                $rows[$k]['match_status_cn'] = '未开始';
            }
            else if($val['match_status'] == -3){
                //已结束
                $url = '';
                $rows[$k]['match_status_cn'] = '已结束';
            }
            else{
                //等待开赛
                $url = home_url('matchs/matchWaitting/match_id/'.$val['ID']);
                $button_title = '等待开赛';
                $rows[$k]['match_status_cn'] = '等待开赛';
            }
            $rows[$k]['button_title'] = $button_title;
            $rows[$k]['right_url'] = $url;
            $rows[$k]['left_url'] = home_url('matchs/info/match_id/'.$val['ID']);

            if($_POST['match_type'] =='history'){
                $button_title = '查看排名';
                $rows[$k]['right_url'] = home_url('matchs/record/match_id/'.$val['ID']);
            }
        }

        wp_send_json_success(array('info'=>$rows));
    }

    /**
     * 设置账户资料
     */
    public function student_saveInfo(){

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_saveInfo_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }

        if(empty($_POST['meta_key'])) wp_send_json_error(array('info'=>'meta_key不能为空'));
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
                            wp_send_json_error(array('info'=>'该手机号已存在'));
                        }
                    }

                    break;
                case 'user_email':

                    //邮箱验证
                    $this->get_smtp_code($_POST['meta_val'],15,true,$_POST['verify_code']);
                    if($user){
                        if($current_user->data->ID != $user['ID']){
                            wp_send_json_error(array('info'=>'该邮箱号已存在'));
                        }
                    }
                    break;
                case 'user_pass':

                    if(empty($_POST['password'])) wp_send_json_error(array('info'=>'新密码不能为空'));
                    if(!empty($_POST['password']) && $_POST['confirm_password'] !== $_POST['password']) wp_send_json_error(array('info'=>'两次密码不一致'));
                    if(!empty($_POST['old_pass'])){

                        $check = wp_check_password($_POST['old_pass'],$current_user->data->user_pass);
                        if(!$check) wp_send_json_error(array('info'=>'密码错误'));

                        $check_ = wp_check_password($_POST['password'],$current_user->data->user_pass);
                        if($check_) wp_send_json_error(array('info'=>'新密码不能和老密码一致'));

                    }
                    if(!preg_match('/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{6,16}$/',$_POST['password'])){
                        wp_send_json_error(array('info'=>'6-16位字母+数字组合'));
                    }

                    $_POST['meta_val'] = wp_hash_password( $_POST['password'] );

                    break;
                case 'user_nicename':

                    if(mb_strlen($_POST['meta_val'],'utf-8') > 24) wp_send_json_error(array('info'=>'昵称不能超过24个字符'));
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
                        wp_send_json_error(array('info'=>'上传失败'));
                    }
                    break;
                case 'user_real_name':

                    //验证格式
                    if(empty($_POST['meta_val']['real_type'])) wp_send_json_error(array('info'=>'请选择证件类型'));
                    //if(empty($_POST['meta_val']['real_name'])) wp_send_json_error(array('info'=>'真实姓名不能为空'));
                    //if(empty($_POST['meta_val']['real_ID'])) wp_send_json_error(array('info'=>'证件号不能玩为空'));
                    if(!reg_match($_POST['meta_val']['real_ID'],$_POST['meta_val']['real_type'])) wp_send_json_error(array('info'=>'证件号格式不正确'));
                    if(!preg_match("/^[\x{4e00}-\x{9fa5}]+[·•]?[\x{4e00}-\x{9fa5}]+$/u", $_POST['meta_val']['real_name'])) wp_send_json_error(array('info'=>'名字格式不正确,请输入你的中文名'));

                    //判断是否报名
                    if(isset($_POST['type']) && $_POST['type'] == 'sign'){

                        $sql = "SELECT a.id,a.zhifu FROM sckm_match_orders a LEFT JOIN sckm_members  b ON a.member_id = b.id WHERE a.match_id = 234 and a.status = 1 and b.truename = '{$_POST['meta_val']['real_name']}' ";

                        $row = $wpdb->get_row($sql,ARRAY_A);
                         //var_dump($row);die;
                        if(empty($row)) wp_send_json_error(array('info'=>'该用户未成功匹配参数资格<br/>请确认该选手实名信息'));
                        if($row){

                            $order_id = $wpdb->get_var("select id order_id from {$wpdb->prefix}order where match_id = 56522 and user_id = {$current_user->ID} ");
                            if(empty($order_id)){
                                
                                $wpdb->startTrans();
                                //在平台创建订单
                                $orde_data = array(
                                            'user_id' => $current_user->ID,
                                            'match_id' => 56522, //等待比赛创建后修改match_id
                                            'order_type' =>1,
                                            'pay_type' => 'wx',
                                            'cost' => 0,
                                            'pay_status' => 4,
                                            'created_time' => get_time('mysql')
                                        );
                                $a = $wpdb->insert($wpdb->prefix.'order',$orde_data);
                                $orders_id = $wpdb->insert_id;
                                $b = $wpdb->insert(
                                                $wpdb->prefix.'match_sign',
                                                array(
                                                    'user_id'=>$current_user->ID,
                                                    'match_id'=>56522,
                                                    'created_time' => get_time('mysql')
                                                )
                                        );
                                $c = $wpdb->update($wpdb->prefix.'order', ['serialnumber' => createNumber($current_user->ID,$orders_id)], ['id' => $orders_id]);
                                //wp_send_json_error(array('info'=>$orders_id.'====='.$a.'----'.$b.'==='.$c));
                                
                                //var_dump($a.'----'.$b);die;
                                /*if($a && $b){

                                    $wpdb->commit();
                                    wp_send_json_success(array('info'=>'签到成功','url'=>home_url('matchs')));
                                }else{

                                    $wpdb->rollback();
                                    wp_send_json_error(array('info'=>'签到失败,比赛订单创建失败<br/>请联系管理员'));
                                }*/
                            }else{
                                wp_send_json_success(array('info'=>'签到成功','url'=>home_url('signs/success/')));
                            }
                        }
                    }

                    if(!empty($_POST['user_gender'])){
                        update_user_meta($current_user->ID,'user_gender',$_POST['user_gender']) && $user_gender_update = true;
                        unset($_POST['user_gender']);
                    }
                    if(!empty($_POST['meta_val']['real_age'])){
                        update_user_meta($current_user->ID,'user_age',$_POST['meta_val']['real_age']) && $user_age_update = true;
                    }
                    //var_dump($_POST['meta_val']);die;
//                    $_POST['user_address'] = array(
//                        'province'=>'四川省',
//                        'city'=>'成都市',
//                        'area'=>'高新区',
//                    );
                    if(!empty($_POST['user_address'])){
                        update_user_meta($current_user->ID,'user_address',$_POST['user_address']) && $user_address_update = true;
                        unset($_POST['user_address']);
                    }


                    if(!empty($_FILES['images'])){
                        //var_dump($_FILES['images']);
                        $upload_dir = wp_upload_dir();
                        $dir = '/user/'.$current_user->ID.'/';
                        $imagePathArr = [];
                        $num = 0;
                        foreach ($_FILES['images']['tmp_name'] as $upd){
                            $file = $this->saveIosFile($upd,$upload_dir['basedir'].$dir);
                            if($file){
                                $_POST['user_ID_Card'][] = $upload_dir['baseurl'].$dir.$file;
                                ++$num;
                            }
                        }
                    }

                    update_user_meta($current_user->ID,'user_ID_Card',$_POST['user_ID_Card']);
                    $user_ID_Card_update = true;

                    break;
                case 'user_sign':
                    if(mb_strlen($_POST['meta_val'],'utf-8') > 40) wp_send_json_error(array('info'=>'昵称不能超过40个字符'));
                    break;
                default:

                    //$resul = update_user_meta($current_user->ID,$_POST['meta_key'],$_POST['meta_val']);
                    break;
            }

            $resul = update_user_meta($current_user->ID,$_POST['meta_key'],$_POST['meta_val']) || isset($user_gender_update) ? true : false || isset($user_address_update) ? true : false || isset($user_age_update) ? true : false || isset($user_ID_Card_update) ? true : false;

        }
        if($resul){

            if(isset($_POST['type']) && $_POST['type'] == 'sign'){

                if($a && $b && $c){

                    $wpdb->commit();
                    wp_send_json_success(array('info'=>'签到成功','url'=>home_url('signs/success/')));
                }else{

                    $wpdb->rollback();
                    wp_send_json_error(array('info'=>'签到失败,比赛订单创建失败<br/>请联系管理员'));
                }
            }

            $url = !empty($_POST['match_id']) ? home_url('/matchs/confirm/match_id/'.$_POST['match_id']) : home_url('account/info');
            $success['info'] = '保存成功';
            $success['url'] = $url;
            wp_send_json_success($success);
        }else{
            wp_send_json_success(array('info'=>'设置失败'));
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
                        wp_send_json_error(array('info'=>'新密码两次输入不一致'));
                    }

                    if(wp_check_password($_POST['confirm_pass'],$current_user->user_pass)){
                        wp_send_json_error(array('info'=>'新旧密码不能一致'));
                    }

                    $new_pass = wp_hash_password( $_POST['confirm_pass'] );

                    $result = $wpdb->update($wpdb->prefix.'users',array('user_pass'=>$new_pass),array('ID'=>$current_user->ID));

                }else{
                    wp_send_json_error(array('info'=>'老密码不正确'));
                }
                break;
            case 'mobile':

                if(!reg_match($_POST['user_mobile'],'m')) wp_send_json_error(array('info'=>'手机格式有误'));

                if($_POST['step'] == 'one'){
                    $this->get_sms_code($_POST['user_mobile'],21,true,$_POST['verify_code']);
                    unset($_SESSION['sms']);
                    wp_send_json_success(array('info'=>'验证成功','url'=>home_url('/safety/safetySetting/type/mobile/confirm/1')));
                }else{

                    $this->get_sms_code($_POST['user_mobile'],16,true,$_POST['verify_code']);
                    $user  = get_user_by( 'mobile', $_POST['user_mobile'] );
                    if(!empty($user)) wp_send_json_error(array('info'=>'该手机号已被占用'));
                    $result = $wpdb->update($wpdb->prefix.'users',array('user_mobile'=>$_POST['user_mobile']),array('ID'=>$current_user->ID));
                }

                break;
            case 'email':
                if(!reg_match($_POST['user_email'],'e')) wp_send_json_error(array('info'=>'邮箱格式有误'));
                $this->get_smtp_code($_POST['user_email'],16,true,$_POST['verify_code']);
                $user  = get_user_by( 'email', $_POST['user_email'] );
                if(!empty($user)) wp_send_json_error(array('info'=>'该邮箱号已被占用'));
                $result = $wpdb->update($wpdb->prefix.'users',array('user_email'=>$_POST['user_email']),array('ID'=>$current_user->ID));
                break;
            case 'weChat':
                $result = update_user_meta($current_user->ID,'user_weChat',$_POST['user_weChat']);
                break;
            case 'qq':
                $result = update_user_meta($current_user->ID,'user_qq',$_POST['user_qq']);
                break;
            default:
                wp_send_json_error(array('info'=>'未知的操作请求'));
                break;
        }

        if($result){
            wp_send_json_success(array('info'=>'更新成功','url'=>home_url('account/secure/')));
        }else{
            wp_send_json_error(array('info'=>'更新失败'));
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
                wp_send_json_error(array('info'=>'未知的操作请求'));
                break;
        }
        if($result){
            wp_send_json_success(array('info'=>'解绑成功','url'=>home_url('account/secure/')));
        }else{
            wp_send_json_error(array('info'=>'解绑失败'));
        }
    }

    /**
     * 通过身份证自动计算年龄
     */
    public function reckon_age(){
        if(empty($_POST['real_ID'])) wp_send_json_error(array('info'=>'证件号不能玩为空'));
        if(!reg_match($_POST['real_ID'],'sf')) wp_send_json_error(array('info'=>'证件号格式不正确'));
        /*$sub_str = substr($_POST['real_ID'],6,4);
        $now = date_i18n("Y",get_time());
        $age = $now-$sub_str;
        $age = $age >0 ? $age : 1;*/
        $age = birthday($_POST['real_ID']);
        
        if($age == -1){
            wp_send_json_error(array('info'=>'年龄不能低于1岁,请确认身份证信息'));
        }
        if($age == -2){
            wp_send_json_error(array('info'=>'年龄超过150岁,请确认身份证信息'));
        }
        wp_send_json_success(array('info'=>$age));
    }

    /**
     * 修改/设置密码
     */
    public function student_savePass(){

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_savePass_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }

        if(empty($_POST['password'])) wp_send_json_error(array('info'=>'新密码不能为空'));
        if(!empty($_POST['password']) && $_POST['confirm_password'] !== $_POST['password']) wp_send_json_error(array('info'=>'两次密码不一致'));
        if(!empty($_POST['old_pass'])){
            global $current_user;
            $check = wp_check_password($_POST['old_pass'],$current_user->data->user_pass);
            if(!$check) wp_send_json_error(array('info'=>'密码错误'));

            $check_ = wp_check_password($_POST['password'],$current_user->data->user_pass);
            if($check_) wp_send_json_error(array('info'=>'新密码不能和老密码一致'));

        }
        if(!preg_match('/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{6,16}$/',$_POST['password'])){
            wp_send_json_error(array('info'=>'6-16位字母+数字组合'));
        }

        global $wpdb;
        $result = $wpdb->update($wpdb->users,array('user_pass'=>wp_hash_password( $_POST['password'] )),array('ID'=>$current_user->ID));
        if($result){

            wp_send_json_success(array('info'=>'重置成功','url'=>home_url('/logins')));
        }else{
            wp_send_json_error(array('info'=>'重置失败'));
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
        if(empty($mobile)) wp_send_json_error(array('info'=>'手机号不能为空'));
        if(!reg_match($mobile,'m')) wp_send_json_error(array('info'=>'手机格式有误'));

        if($session_verify){

            if(isset($_SESSION['sms']) && !empty($_SESSION['sms'])){
                $sms = $_SESSION['sms'];
                if($sms['mobile'] != $mobile) wp_send_json_error(array('info'=>'当前手机号与获取验证手机号不一致'));
                if($sms['template'] != md5($template)){
                    unset($_SESSION['sms']);
                    wp_send_json_error(array('info'=>'请先获取验证码'));
                }
                if(get_time() > $sms['time']){
                    unset($_SESSION['sms']);
                    wp_send_json_error(array('info'=>'验证码已过期,请重新获取'));
                }
                if(!empty($send_code)){

                    if(md5($send_code) != $sms['code']) wp_send_json_error(array('info'=>'验证码错误'));
                }else{
                    wp_send_json_error(array('info'=>'验证码不能为空'));
                }
            }else{
                wp_send_json_error(array('info'=>'请先获取验证码'));
            }

            return ;
        }

        //如果不是注册操作,判断是否为平台用户
        if(!in_array($template,array(16,17,19,21))){
            $user  = get_user_by( 'mobile', $mobile );

            if(empty($user)) wp_send_json_error(array('info'=>'您不是平台用户,请先进行注册'));
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
            wp_send_json_success(array('info'=>'获取成功'));
        }else{
            wp_send_json_error(array('info'=>'获取失败,请稍后重试'));
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
        if(empty($email)) wp_send_json_error(array('info'=>'邮箱号不能为空'));
        if(!reg_match($email,'e')) wp_send_json_error(array('info'=>'邮箱格式有误'));

        if($session_verify){

            if(isset($_SESSION['smtp']) && !empty($_SESSION['smtp'])){
                $smtp = $_SESSION['smtp'];
                if($smtp['email'] != $email) wp_send_json_error(array('info'=>'当前邮箱号与获取验证邮箱号不一致'));
                if($smtp['template'] != md5($template)){
                    unset($_SESSION['smtp']);
                    wp_send_json_error(array('info'=>'请先获取验证码'));
                }
                if(get_time() > $smtp['time']){
                    unset($_SESSION['smtp']);
                    wp_send_json_error(array('info'=>'验证码已过期,请重新获取'));
                }
                if(!empty($send_code)){

                    if(md5($send_code) != $smtp['code']) wp_send_json_error(array('info'=>'验证码错误'));
                }else{
                    wp_send_json_error(array('info'=>'验证码不能为空'));
                }
            }else{
                wp_send_json_error(array('info'=>'请先获取验证码'));
            }

            return ;
        }

        //如果不是注册操作,判断是否为平台用户
        if(!in_array($template,array(16,17,19,21))){
            $user  = get_user_by( 'email', $email );
            if(empty($user)) wp_send_json_error(array('info'=>'您不是平台用户,请先进行注册'));
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
            wp_send_json_success(array('info'=>'获取成功'));
        }else{
            wp_send_json_error(array('info'=>'获取失败,请稍后重试'));
        }
    }

    /**
     * 学生登录
     */
    public function student_login(){

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_login_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }

        switch ($_POST['login_type']){
            case 'mobile':

                //短信验证
                $this->get_sms_code($_POST['user_login'],19,true,$_POST['password']);

                break;
            case 'pass';

                if(empty($_POST['user_login'])) wp_send_json_error(array('info'=>'用户名不能为空'));
                if(empty($_POST['password'])) wp_send_json_error(array('info'=>'密码不能为空'));

                break;
        }
        global $wpdb;
        $sql = "SELECT * FROM $wpdb->users WHERE user_login = '{$_POST['user_login']}' or user_email = '{$_POST['user_login']}' or user_mobile = '{$_POST['user_login']}'";
        $user = $wpdb->get_row($sql);

        //判断用户是否存在
        if($user){
            if($_POST['login_type'] == 'pass'){

                $check = wp_check_password($_POST['password'],$user->user_pass);
                if(!$check) wp_send_json_error(array('info'=>'密码错误'));
            }

            $this->setUserCookie($user->ID);

            //do_action( 'wp_login', $user->user_login, $user );

            wp_send_json_success( array('info'=>'登录成功','url'=>home_url('/account/')));

        }else{

            //注册用户
            if($_POST['login_type'] == 'mobile'){

                if(!reg_match($_POST['user_login'],'m')) wp_send_json_error(array('info'=>'手机格式不正确'));
                $result = wp_create_user($_POST['user_login'],$_POST['password'],'',$_POST['user_login']);
                //print_r($result);die;
                if($result){
                    unset($_SESSION['sms']);

                    $this->setUserCookie($result);

                    wp_send_json_success( array('info'=>'登录成功','url'=>home_url('account')));
                }else{
                    wp_send_json_error(array('info'=>'登录失败'));
                }
            }else{

                wp_send_json_error(array('info'=>'不存在此用户,请先注册'));
            }

        }
    }

    /**
     *学生注册
     */
    public function student_register(){

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_register_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }

        if(empty($_POST['user_login'])) wp_send_json_error(array('info'=>'账号不能为空'));
        if(empty($_POST['verify_code'])) wp_send_json_error(array('info'=>'验证码不能为空'));
        if(empty($_POST['password'])) wp_send_json_error(array('info'=>'密码不能为空'));

        global $wpdb;
        $sql = "SELECT * FROM $wpdb->users WHERE user_login = '{$_POST['user_login']}' or user_email = '{$_POST['user_login']}' or user_mobile = '{$_POST['user_login']}'";
        $user = $wpdb->get_row($sql);
        if($user) wp_send_json_error(array('info'=>'此用户已存在'));

        if(reg_match($_POST['user_login'],'m')){

            $this->get_sms_code($_POST['user_login'],17,true,$_POST['verify_code']);
            $result = wp_create_user($_POST['user_login'],$_POST['password'],'',$_POST['user_login']);
        }elseif (reg_match($_POST['user_login'],'e')){

            $this->get_smtp_code($_POST['user_login'],17,true,$_POST['verify_code']);
            $result = wp_create_user($_POST['user_login'],$_POST['password'],$_POST['user_login']);
        }

        if($result){
            unset($_SESSION['sms']);
            unset($_SESSION['smtp']);

            $this->setUserCookie($result);

            wp_send_json_success(array('info'=>'注册成功','url'=>home_url('account')));
        }else{
            wp_send_json_error(array('info'=>'注册失败'));
        }

    }

    /**
     * 密码重置
     */
    public function student_reset(){

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_reset_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }

        if(empty($_POST['user_login'])) wp_send_json_error(array('info'=>'账号不能为空'));

        global $wpdb;
        $sql = "SELECT * FROM $wpdb->users WHERE user_login = '{$_POST['user_login']}' or user_email = '{$_POST['user_login']}' or user_mobile = '{$_POST['user_login']}'";
        $user = $wpdb->get_row($sql);

        if(!$user) wp_send_json_error(array('info'=>'不存在此用户,请先注册'));

        if(empty($_POST['password'])) wp_send_json_error('密码不能为空');
        if(!empty($_POST['password']) && $_POST['confirm_password'] !== $_POST['password']) wp_send_json_error(array('info'=>'两次密码不一致'));

        if(reg_match($_POST['user_login'],'m')){

            $this->get_sms_code($_POST['user_login'],16,true,$_POST['verify_code']);
        }elseif (reg_match($_POST['user_login'],'e')){

            $this->get_smtp_code($_POST['user_login'],16,true,$_POST['verify_code']);
        }

        $result = $wpdb->update($wpdb->users,array('user_pass'=>wp_hash_password( $_POST['password'] )),array('ID'=>$user->ID));
        if($result){
            unset($_SESSION['sms']);
            unset($_SESSION['smtp']);
            wp_send_json_success(array('info'=>'重置成功','url'=>home_url('/logins')));
        }else{
            wp_send_json_error(array('info'=>'重置失败'));
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

        if(empty($filecontent)) wp_send_json_error(array('info'=>'数据错误'));
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

    /**
     * 保存用户cookie信息
     * @param $user_id
     */
    public function setUserCookie($user_id){
        update_user_meta($user_id,'user_session_id',session_id());
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        $_SESSION['login_time'] = get_time()+15;
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
        wp_send_json_success(array('info'=>'退出成功','url'=>home_url('logins/index/login_type/out')));
    }

    /**
     * 消息列表
     */
    public function getMessagesLists(){
        if(is_ajax()){
            global $wpdb,$current_user;
            if(!$current_user->ID) wp_send_json_error(array('info' => '您还没有登录'));
            $page = ($page = intval($_POST['page'])) < 1 ? 1 : $page;
            $pageSize = 10;
            $start = ($page-1) * $pageSize;
            $sql = 'SELECT SQL_CALC_FOUND_ROWS id,`type`,title,read_status,title,content,message_time from '
                .$wpdb->prefix.'messages WHERE '
                .'status=1 AND user_id=' .$current_user->ID
                .' ORDER BY read_status ASC'
                .' LIMIT '.$start.', '.$pageSize;
            $result = $wpdb->get_results($sql);

            $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
            if(ceil($total['total'] / $pageSize) < $page && $total['total'] != 0) wp_send_json_error(array('info' => '没有更多了'));
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
            wp_send_json_error(array('info'=>'未找到数据'));
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
            case 5://订单失效
                $payStatusWhere = 'pay_status=5';
                break;
            case -1://待退款
                $payStatusWhere = 'pay_status=-1';
                break;
            case -2://已退款
                $payStatusWhere = 'pay_status=-2';
                break;
            default:
                wp_send_json_error(array('info' => '参数错误'));
        }
        $page < 1 && $page = 1;
        $pageSize = 10;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results('SELECT 
        id,
        serialnumber,
        pay_status,
        match_id,
        IFNULL(fullname, "-") AS fullname,
        telephone,
        IFNULL(address, "-") AS address,
        CASE order_type 
        WHEN 1 THEN "报名订单" 
        WHEN 2 THEN "商品订单" 
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
        WHEN 2 THEN "已支付" 
        WHEN 3 THEN "待收货" 
        WHEN 4 THEN "已完成" 
        WHEN 5 THEN "已失效" 
        END AS pay_status_title,
        created_time
        FROM '.$wpdb->prefix.'order WHERE user_id='.$current_user->ID.' 
        AND '.$payStatusWhere.' 
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
                case 2://商品订单
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
        wp_send_json_error(array('info' => '无订单'));
    }
    /**
     * 获取订单详情
     */
    public function getOrderDetials(){
        $id = intval($_POST['id']);
        if($id < 1) wp_send_json_error(array('info' => '参数错误'));
        global $wpdb,$current_user;
        require_once 'class-student-account-order.php';
        $row = $wpdb->get_row('SELECT '.self::selectField().' FROM '.$wpdb->prefix.'order WHERE user_id='.$current_user->ID.' AND id='.$id, ARRAY_A);
        if($row) wp_send_json_success(array('info' => $row));
        wp_send_json_error(array('info' => '未找到订单'));
    }


    /**
     * 支付
     */
    public function pay(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_ranking_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        global $wpdb,$current_user;
        if($current_user->ID < 1 || !$current_user->ID){
            wp_send_json_error(['info' => '您暂未登录', 'url' => home_url('logins')]);
        }

        $otderSn = trim($_POST['serialnumber']);
        $payType = $_POST['pay_type'];

        //查询配置
//        $interface_config = get_option('interface_config');
        $order = $wpdb->get_row(
            'SELECT id,serialnumber,match_id,user_id,fullname,telephone,address,pay_type,cost,pay_status,created_time FROM '
            .$wpdb->prefix.'order WHERE serialnumber='.$otderSn.' AND user_id='.$current_user->ID, ARRAY_A);
        if(!$order)  wp_send_json_error(array('info'=>'订单不存在'));
        if($order['pay_status'] != 1)  wp_send_json_error(array('info'=>'此订单不是待支付订单'));
        require_once 'class-student-payment.php';
        switch ($payType){
            case 'wxh5pay':
                //TODO 微信支付暂未开放
//                wp_send_json_error(array('info'=>'微信支付暂未开放'));
                //请求数据
                //1.统一下单方法
                $params['notify_url'] = home_url('payment/wxpay/type/wx_notifyUrl'); //商品描述
                $params['body'] = '脑力运动'; //商品描述
                $params['serialnumber'] = $order['serialnumber']; // TODO 商户自定义的订单号
                $params['price'] = $order['cost']; //订单金额 只能为整数 单位为分
                $params['attach'] = 'serialnumber='.$order['serialnumber']; //附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
                $wxpay = new Student_Payment('wxpay');
                //判断是否是微信浏览器
                if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
                    //jsapi支付需要一个单独的页面获取openid
                    $result = ['status' => true, 'data' => home_url('payment/wx_js_pay/type/wxpay/id/'.$order['id'].'/match_id/'.$_POST['match_id'])];

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
            wp_send_json_error(array('info'=>'发起支付失败'));
        }
    }

    /**
     * 意见反馈
     */
    public function feedback(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_ranking_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        global $wpdb, $current_user;
        $contact = $_POST['contact'];//联系方式
        $content = $_POST['content'];//内容41
        $date = date_i18n('Y m', get_time());
        $upload_dir = wp_upload_dir();
        $dateArr = explode(' ',$date);
        $dir = '/'.$dateArr[0].'/'.$dateArr[1].'/';
        $num = 0;
        $imagePathArr = [];
        foreach ($_FILES['images']['tmp_name'] as $upd){
            $file = $this->saveIosFile($upd,$upload_dir['basedir'].$dir);
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
            wp_send_json_success(array('info' => '提交完成, '.$num.'张图片上传成功'));
        }else{
            foreach ($imagePathArr as $v){
                $filePa = explode('uploads',$v);
                if(is_file($upload_dir['basedir'].$filePa[1])) unlink($upload_dir['basedir'].$filePa[1]);

            }
            wp_send_json_error(array('info' => '提交失败'));
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
            wp_send_json_error(array('info' => '获取失败'));
    }

    /**
     * 获取logo
     */
    public function getLogo(){
        $logo = get_option('logo_url');
        if($logo)
            wp_send_json_success(array('info' => $logo));
        else
            wp_send_json_error(array('info' => '获取失败'));
    }

    /**
     * 新闻咨询列表
     */
    public function getNewsLists(){
        global $wpdb;
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $page < 1 && $page = 1;
        $pageSize = 10;
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
            wp_send_json_error(array('info' => '获取失败'));
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
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results('SELECT 
        id,goods_title,goods_intro,images,brain,stock,sales,price 
        FROM '.$wpdb->prefix.'goods WHERE shelf=1 AND stock>0 '.$searchWhere.' LIMIT '.$start.','.$pageSize, ARRAY_A);
        foreach ($rows as &$row){
            $row['images'] = unserialize($row['images']);
        }
        if($rows) wp_send_json_success(['info' => $rows]);
        else wp_send_json_error(['info' => '没有商品']);
    }

    /**
     * 加入购物车
     */
    public function joinCart(){
        global $wpdb,$current_user;
        $goodsId = intval($_POST['goods_id']);
        $goodsNum = intval($_POST['num']);
        if($goodsId < 1) wp_send_json_error(['info' => '非法参数']);
        //检查商品库存
        $goods = $wpdb->get_row('SELECT stock FROM '.$wpdb->prefix.'goods WHERE id='.$goodsId);
        if(!$goods) wp_send_json_error(['info' => '未找到商品']);
        if($goods['stock'] < $goodsNum) wp_send_json_error(['info' => '商品库存不足']);
        //该商品是否已存在购物车
        $row = $wpdb->get_row('SELECT id,goods_num FROM '.$wpdb->prefix.'order_goods WHERE user_id='.$current_user->ID.' AND goods_id='.$goodsId.' AND order_id=0', ARRAY_A);
        if($row){
            $bool = $wpdb->update($wpdb->prefix.'order_goods',['goods_num' => $goodsNum+$row['goods_num']], ['id' => $row['id']]);
        }else{
            $bool = $wpdb->insert($wpdb->prefix.'order_goods', ['goods_num' => $goodsNum,'goods_id' => $goodsId, 'user_id' => $current_user->ID]);
        }
        if($bool) wp_send_json_success(['info' => '加入购物车成功']);
        else wp_send_json_error(['info' => '操作失败']);
    }

    /**
     * 提交订单
     */
    public function subGoodsOrder(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_sub_order_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
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
            wp_send_json_error(['info' => '请选择商品']);
        }
        $address = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'my_address 
        WHERE user_id='.$user_id.' AND id='.$address_id, ARRAY_A);
        if(!$address) wp_send_json_error(['info' => '出错了, 找不到收货地址']);
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
            $wpdb->startTrans();
            foreach ($orderGoodsRows as $orderGoodsRow){
                $goods = $wpdb->get_row('SELECT id,goods_title,shelf,price,stock FROM '.$wpdb->prefix.'goods WHERE id='.$orderGoodsRow['goods_id'], ARRAY_A);
                //不存在商品
                if(!$goods){
                    $wpdb->rollback();
                    wp_send_json_error(['info' => '出错了, 找不到商品']);
                }
                //已下架商品
                if($goods['shelf'] == 2) {
                    $wpdb->rollback();
                    wp_send_json_error(['info' => $goods['goods_title'].'-已下架']);
                }
                //库存不足
                if($goods['stock'] < $orderGoodsRow['goods_num']){
                    $wpdb->rollback();
                    wp_send_json_error(['info' => $goods['goods_title'].'-库存不足']);
                }
                //减少商品库存
                if(!$wpdb->update($wpdb->prefix.'goods', ['stock' => $goods['stock'] - $orderGoodsRow['goods_num']], ['id' => $goods['id']])){
                    $wpdb->rollback();
                    wp_send_json_error(['info' => '出错了, 库存更新失败']);
                }
                //更新order_goods 支付价格和支付脑币
                if(!$wpdb->update($wpdb->prefix.'order_goods', ['pay_price' => $goods['price'], 'pay_brain' => $goods['brain']], ['id' => $goods['id']])){
                    $wpdb->rollback();
                    wp_send_json_error(['info' => '出错了, 支付价格更新失败']);
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
                'order_type' => 2,
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
                        $wpdb->commit();
                        wp_send_json_success(['info' => $insertId]);
                    }else{
                        //修改order_goods的状态失败
                        $wpdb->rollback();
                        wp_send_json_error(['info' => '提交订单失败']);
                    }
                }else{
                    //修改订单号失败
                    $wpdb->rollback();
                    wp_send_json_error(['info' => '提交订单失败']);
                }
            }else{
                //插入订单数据失败
                $wpdb->rollback();
                wp_send_json_error(['info' => '提交订单失败']);
            }
        }else{
            wp_send_json_error(['info' => '系统繁忙,请稍后再试']);
        }
    }

    /**
     * 确认收货
     */
    public function collectGoods(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_collect_goods_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        global $wpdb,$current_user;
        $id = intval($_POST['id']);
        $bool = $wpdb->update($wpdb->prefix.'order', ['pay_status' => 4], ['id' => $id, 'pay_stats' => 3, 'user_id' => $current_user->ID]);
        if($bool) wp_send_json_success(['info' => '订单已确认收货']);
        else  wp_send_json_error(['info' => '操作失败,请稍后再试']);
    }

    /**
     * 取消订单
     * 可取消状态 :  未支付
     */
    public function cancelOrder(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_cancel_goods_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        $serialnumber = trim($_POST['serialnumber']);
        global $wpdb;
        if($wpdb->query('UPDATE '.$wpdb->prefix.'order SET pay_status=5 WHERE pay_status=1 AND serialnumber='.$serialnumber))
            wp_send_json_success(['info' => '订单已取消']);
        else
            wp_send_json_error(['info' => '操作失败,请稍后再试']);
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
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_replace_major_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        if(empty($_POST['coach_id']) ||  empty($_POST['category_id'])) wp_send_json_error(array('info'=>'参数错误'));
        global $wpdb,$current_user;
        //判断是否登录
        if($current_user->ID < 1) wp_send_json_error(array('info'=>'未登录'));

        //判断当前是否是已申请的教练
        $row = $wpdb->get_row("select id,user_id,category_id,apply_status,major from {$wpdb->prefix}my_coach where coach_id = {$_POST['coach_id']} and user_id = $current_user->ID and category_id = {$_POST['category_id']} and apply_status=2",ARRAY_A);
        if(empty($row)) wp_send_json_error(array('info'=>'数据错误'));
        if($row['apply_status'] != 2){
            $post_title = get_post($row['category_id'])->post_title;
            wp_send_json_error(array('该教练还不是你的'.$post_title.'教练'));
        }

        //开启事务
        $wpdb->startTrans();
        //取消原主训教练
        $cancelRes = $wpdb->query('UPDATE '.$wpdb->prefix.'my_coach SET major=0 WHERE category_id='.$_POST['category_id'].' AND user_id='.$current_user->ID);
        if(!$cancelRes) {
            $wpdb->rollback();
            wp_send_json_error(array('info'=>'更换主训教练失败'));
        }
        //设着当前教练为主训
        $currentRes = $wpdb->query('UPDATE '.$wpdb->prefix.'my_coach SET major=1 WHERE id='.$row['id']);
        if($currentRes){
            $wpdb->commit();
            wp_send_json_success(['info' => '主训教练更换成功']);

        }else{
            $wpdb->rollback();
            wp_send_json_error(array('info'=>'更换主训教练失败'));
        }
    }

    /**
     * 学生解除教练关系
     */
    public function relieveMyCoach(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_relieve_coach_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        if(empty($_POST['coach_id']) ||  empty($_POST['category_id'])) wp_send_json_error(array('info'=>'参数错误'));
        global $wpdb,$current_user;
        //判断是否登录
        if($current_user->ID < 1) wp_send_json_error(array('info'=>'未登录'));

        //判断当前是否是已申请的教练
        $row = $wpdb->get_row("select mc.id,mc.apply_status,mc.major,u.user_mobile,p.post_title,display_name,u.ID as uid from {$wpdb->prefix}my_coach as mc 
        left join {$wpdb->users} as u on u.ID=mc.coach_id 
        left join {$wpdb->posts} as p on mc.category_id=p.ID 
        where mc.coach_id = {$_POST['coach_id']} and mc.user_id = $current_user->ID and mc.category_id = {$_POST['category_id']} and mc.apply_status=2",ARRAY_A);
        if(empty($row)) wp_send_json_error(array('info'=>'数据错误'));
        if($row['apply_status'] != 2) wp_send_json_error(array('该教练还不是你的教练'));
        $userID = get_user_meta($current_user->ID, '', true)['user_ID'][0];
        //改变状态
        $update = $wpdb->query('UPDATE '.$wpdb->prefix.'my_coach SET apply_status=3 WHERE id='.$row['id']);
        if($update){
            //TODO 发送短信通知教练 ===================================
            $ali = new AliSms();
            $result = $ali->sendSms($row['user_mobile'], 14, array('coach'=>str_replace(', ', '', $row['display_name']), 'user_id' => $userID ,'cate' => $row['post_title']), '国际脑力运动');
            wp_send_json_success(['info' => '解除教学关系成功']);
        }
        wp_send_json_error(array('info'=>'解除教学关系失败'));
    }

    /**
     * 判断当前类别当前用户是是否存在主训教练
     */
    public function searchCurrentCoach(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_current_coach_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        $category_id = intval($_POST['category_id']);
        global $wpdb,$current_user;
        if($category_id < 1){
            wp_send_json_error(array('info'=>'参数错误'));
        }
        $id = $wpdb->get_var('SELECT id FROM '.$wpdb->prefix.'my_coach WHERE category_id='.$category_id.' AND user_id='.$current_user->ID.' AND  major=1 AND (apply_status=1 or apply_status=2)');
        //var_dump($id);die;
        if(!$id){
            wp_send_json_success(array('info'=>'当前无主训教练'));
        }else{
            wp_send_json_error(array('info'=>'当前已存在主训教练'));
        }
    }

    /**
     * 微信授权登录绑定手机或邮箱
     */
    public function wxWebLoginBindMobile(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_current_wx_web_login_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }

        $bindType = $_POST['type'];
        if($bindType != 'code' && $bindType != 'username') wp_send_json_error(array('info'=>'参数错误'));

        if(preg_match('/^1[3456789][0-9]{9}$/',$_POST['mobile'])) {
            $type = 'mobile';
        }elseif (is_email($_POST['mobile'])){
            $type = 'email';
        }else{
            wp_send_json_error(array('info'=>'手机或邮箱格式不正确'));
        }



        global $wpdb;


        if($bindType == 'code'){
            $user = $wpdb->get_row('SELECT ID,user_pass FROM '.$wpdb->users.' WHERE (user_mobile="'.$_POST['mobile'].'" OR user_login="'.$_POST['mobile'].'" OR user_email="'.$_POST['mobile'].'") AND weChat_openid!=""');
            //验证码绑定
            if($type == 'mobile'){
                //判断当前手机是否已经存在
                if($user)  wp_send_json_error(array('info'=>'当前手机号码已绑定其它微信'));
                if($bindType == 'code') $this->get_sms_code($_POST['mobile'],17,true,$_POST['send_code']);
            }else{
                if($user)  wp_send_json_error(array('info'=>'当前邮箱已绑定其它微信'));
                if($bindType == 'code') $this->get_smtp_code($_POST['mobile'],17,true,$_POST['send_code']);
            }
            $user_id = $_POST['user_id'];
        }else{
            $user = $wpdb->get_row('SELECT ID,user_pass,weChat_openid FROM '.$wpdb->users.' WHERE (user_mobile="'.$_POST['mobile'].'" OR user_login="'.$_POST['mobile'].'" OR user_email="'.$_POST['mobile'].'")');
            //账号绑定
            //判断用户是否存在
            if($user){
                $check = wp_check_password($_POST['password'],$user->user_pass);
                if(!$check) wp_send_json_error(array('info'=>'密码错误'));
            }else{
                wp_send_json_error(array('info'=>'该用户不存在'));
            }

        //if($user->weChat_openid) wp_send_json_error(array('info'=>'该用户已绑定其它微信'));
            $user_id = $user->ID;
            update_user_meta($user_id,'user_session_id',session_id());
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            //wp_send_json_success(['info' => '登录成功', 'url' => home_url('account')]);
            if(isset($_POST['loginType']) && $_POST['loginType'] == 'sign'){
                wp_send_json_success(array('info'=>'账户绑定完成,即将跳转', 'url' => home_url('account/certification/type/sign')));
            }else{

                wp_send_json_success(array('info'=>'绑定成功', 'url' => home_url('account')));
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
                wp_send_json_success(array('info'=>'账户绑定完成,即将跳转', 'url' => home_url('account/certification/type/sign')));
            }else{

                wp_send_json_success(array('info'=>'绑定成功', 'url' => home_url('account')));
            }
            //wp_send_json_success(array('info'=>'绑定成功', 'url' => home_url('account')));
        }else{
            wp_send_json_error(array('info'=>'绑定失败'));
        }
    }
    /**
     * 名录列表
     */
    public function getDirectories(){
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $type = intval($_POST['type']);
        if($type < 1)  wp_send_json_error(array('info'=>'参数错误'));
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        global $wpdb;
//        $res = $wpdb->get_results('SELECT d.user_id,d.level,d.category_name,mc.coach_id,d.certificate,
//        CASE d.range
//        WHEN 1 THEN "中国"
//        WHEN 2 THEN "国际"
//        ELSE "未知"
//        END AS ranges
//        FROM '.$wpdb->prefix.'directories AS d
//        LEFT JOIN '.$wpdb->prefix.'my_coach AS mc ON mc.user_id=d.user_id
//        WHERE d.type='.$type.' AND d.is_show=1 ORDER BY d.id DESC LIMIT '.$start.','.$pageSize);
        $res = $wpdb->get_results('SELECT d.user_id,d.level,d.category_name,d.certificate,
        CASE d.range 
        WHEN 1 THEN "中国" 
        WHEN 2 THEN "国际" 
        ELSE "未知" 
        END AS ranges  
        FROM '.$wpdb->prefix.'directories AS d 
        WHERE d.type='.$type.' AND d.is_show=1 ORDER BY d.id DESC LIMIT '.$start.','.$pageSize);

        foreach ($res as &$v){
            $usermeta = get_user_meta($v->user_id,'', true);

            $coachmeta = get_user_meta($v->coach_id,'user_real_name')[0];
            $user_real_name = unserialize($usermeta['user_real_name'][0]);
            if(!$user_real_name){
                $user_real_name['real_name'] = $usermeta['last_name'][0].$usermeta['first_name'][0];
            }
            $v->header_img = $usermeta['user_head'][0];
            $v->userID = $usermeta['user_ID'][0];
            $v->real_name = $user_real_name['real_name'];
            $v->sex = $usermeta['user_gender'][0];
            $v->coach_name = unserialize($coachmeta)['real_name'];

        }

        if($res)
            wp_send_json_success(array('info'=>$res));
        else
            wp_send_json_error(array('info'=>'没有数据'));
    }

    /**
     * 根据搜索条件获取战队列表
     */
    public function getTeamsBySearch(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_team_search_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        global $wpdb,$current_user;
        $search = trim($_POST['search']);
        $map = array();
        $map[] = " a.post_status = 'publish' ";
        $map[] = " a.post_type = 'team' ";
        $map[] = " a.post_title LIKE '%$search%' ";
        //判断是否有分页
        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 10;
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
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>'已经到底了'));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>'暂无战队'));
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
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_get_team_ranking_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        global $wpdb,$current_user;
        $match_id = intval($_POST['match_id']);
        if($match_id < 1) wp_send_json_error(['info' => '比赛参数错误']);
        $match = $wpdb->get_row('SELECT match_status,match_more,match_id FROM '.$wpdb->prefix.'match_meta WHERE match_id='.$match_id, ARRAY_A);
        if(!$match || $match['match_status'] != -3) wp_send_json_error(['info' => '当前比赛未结束']);


        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        if($page < 1) $page = 1;
        $pageSize = 10;
        $start = ($page-1)*$pageSize;

        //战队排名
        //获取参加比赛的成员
        $sql = "SELECT p.post_title,p.ID,o.user_id FROM `{$wpdb->prefix}order` AS o 
                    LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON o.user_id=mt.user_id AND mt.status=2 
                    LEFT JOIN `{$wpdb->posts}` AS p ON p.ID=mt.team_id 
                    WHERE o.match_id={$match['match_id']} AND o.pay_status IN(2,3,4) AND mt.team_id!='' 
                    LIMIT {$start},{$pageSize}";
        $result = $wpdb->get_results($sql, ARRAY_A);
        if(!$result) wp_send_json_error(['info' => '没有数据']);
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
            $sql = "SELECT SUM(my_score) AS my_score,SUM(surplus_time) AS surplus_time FROM 
                          (SELECT MAX(my_score) AS my_score,MAX(surplus_time) AS surplus_time FROM `{$wpdb->prefix}match_questions` AS mq 
                          LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON mt.user_id=mq.user_id AND mt.status=2 AND mt.team_id={$tuV2['team_id']}
                          WHERE mq.match_id={$match['match_id']} AND mt.team_id={$tuV2['team_id']} AND mq.user_id IN({$tuV2['user_ids']}) 
                          GROUP BY mq.project_id,mq.user_id) AS child  
                          ORDER BY my_score DESC limit 0,5
                       ";
            $row = $wpdb->get_row($sql,ARRAY_A);
            $tuV2['my_team'] = in_array($current_user->ID,explode(',',$tuV2['user_ids'])) ? 'y' : 'n';
            $tuV2['my_score'] = $row['my_score'] > 0 ? $row['my_score'] : 0;
            $tuV2['surplus_time'] = $row['surplus_time'] > 0 ? $row['surplus_time'] : 0;
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
        if($page > 1){
            $ranking = intval($_POST['ranking']);
            if($ranking < 1) wp_send_json_error(['info' => '排名参数错误!']);
            //查询上一页最后一名
            $start -= 1;
            $sql = "SELECT p.post_title,p.ID,o.user_id FROM `{$wpdb->prefix}order` AS o 
                    LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON o.user_id=mt.user_id AND mt.status=2 
                    LEFT JOIN `{$wpdb->posts}` AS p ON p.ID=mt.team_id 
                    WHERE o.match_id={$match['match_id']} AND o.pay_status IN(2,3,4) AND mt.team_id!='' 
                    LIMIT {$start},1";
            $rowPrev = $wpdb->get_row($sql, ARRAY_A);
            $sql = "SELECT SUM(my_score) AS my_score,SUM(surplus_time) AS surplus_time FROM 
                          (SELECT MAX(my_score) AS my_score,MAX(surplus_time) AS surplus_time FROM `{$wpdb->prefix}match_questions` AS mq 
                          LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON mt.user_id=mq.user_id AND mt.status=2 AND mt.team_id={$rowPrev['team_id']}
                          WHERE mq.match_id={$match['match_id']} AND mt.team_id={$rowPrev['team_id']} AND mq.user_id IN({$rowPrev['user_ids']}) 
                          GROUP BY mq.project_id,mq.user_id) AS child  
                          ORDER BY my_score DESC limit 0,5
                       ";
            $row = $wpdb->get_row($sql,ARRAY_A);
            $rowPrev['my_score'] = $row['my_score'];
            $rowPrev['surplus_time'] = $row['surplus_time'];

            if(!($row['my_score'] == $totalRanking[0]['my_score'] && $row['surplus_time']<$totalRanking[0]['surplus_time'])){
                ++$ranking;
            }
        }else{
            $ranking = $page;
        }
        foreach ($totalRanking as $k => $v){
            $totalRanking[$k]['ranking'] = $ranking;
            if(!(isset($totalRanking[$k+1]) && $totalRanking[$k+1]['my_score'] == $totalRanking[$k]['my_score'] && $totalRanking[$k+1]['surplus_time'] == $totalRanking[$k]['surplus_time'])){
                ++$ranking;
            }
        }
        wp_send_json_success(['info' => $totalRanking]);
    }
}

new Student_Ajax();