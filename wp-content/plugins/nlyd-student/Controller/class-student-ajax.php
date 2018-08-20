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

        if(empty($_POST['my_answer'])){
            global $current_user;
            $redis = new Redis();
            $redis->connect('127.0.0.1',6379,1);
            $redis->auth('leo626');
            $default_count_down = $redis->get('end_time'.$current_user->ID)-time()-2;
            $redis->setex('end_time'.$current_user->ID,$default_count_down,time()+$default_count_down);
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

                if(!preg_match("/[\+\-\*\/\.]{2}|[^\+\-\*\/\(\)\d\.]+/i", $my_answer)){

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
        }else{
            $where = " where x.match_id = {$_POST['match_id']} ";
            //判断是否存在分类id
            if(!empty($_POST['category_id'])){
                //获取当前分类下项目
                $sql_ = "select ID from {$wpdb->prefix}posts where post_parent = {$_POST['category_id']}";
                $category = $wpdb->get_results($sql_,ARRAY_A);
                if(!empty($category)){
                    $project_id = arr2str(array_column($category,'ID'));
                    $where .= " and x.project_id in ({$project_id}) ";
                }
            }
            if(!empty($_POST['project_id'])){
                $where .= " and x.project_id = {$_POST['project_id']} ";
            }

            if(!empty($_POST['age_group'])){
                $age = $_POST['age_group'];

                switch ($age){
                    case $age == 1:
                        $where .= " and y.meta_value < 13 ";
                        break;
                    case $age == 2:
                        $where .= " and (y.meta_value > 12 and y.meta_value < 20) ";
                        break;
                    case $age == 3:
                        $where .= " and (y.meta_value > 19 and y.meta_value < 60) ";
                        break;
                    default:
                        $where .= " and y.meta_value > 59 ";
                        break;
                }
            }

            if(!empty($_POST['match_more'])){
                $where .= " and x.match_more = {$_POST['match_more']} ";
            }

            //$sql = "select a.user_id,SUM(a.score) my_score from (select user_id,project_id,match_more,surplus_time,MAX(my_score) score from {$wpdb->prefix}match_questions {$where} GROUP BY user_id,project_id) a GROUP BY user_id order by my_score desc limit {$start},{$pageSize} ";

            $sql = " select a.user_id,SUM(a.score) my_score,a.user_age 
                    from 
                    ( select x.user_id,x.project_id,x.match_more,x.surplus_time,MAX(my_score) score,y.meta_value user_age 
                      from {$wpdb->prefix}match_questions x   
                        LEFT JOIN {$wpdb->prefix}usermeta y on x.user_id = y.user_id and y.meta_key = 'user_age' 
                        {$where} GROUP BY x.user_id,x.project_id
                    ) a 
                    GROUP BY a.user_id order by my_score desc limit 0,10 ";

            //print_r($sql);
            $rows = $wpdb->get_results($sql,ARRAY_A);
        }
        $maxPage = ceil( (count($rows)/$pageSize) );
        if($_POST['page'] > $maxPage && count($rows) != 0) wp_send_json_error(array('info'=>'已经到底了'));
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
                    switch ($age){
                        case $age > 59:
                            $group = '老年组';
                            break;
                        case $age > 18:
                            $group = '成人组';
                            break;
                        case $age > 13:
                            $group = '少年组';
                            break;
                        default:
                            $group = '儿童组';
                            break;
                    }

                }else{
                    $group = '-';
                }
                if(!empty($user_info['user_address'])){
                    $user_address = unserialize($user_info['user_address']);
                    $city = $user_address['city'];
                }else{
                    $city = '-';
                }

                $list[$k]['ID'] = $user_info['user_ID'];
                $list[$k]['city'] = $city;
                //$list[$k]['score'] = $val['my_score'];
                $list[$k]['group'] = $group;
                $list[$k]['score'] = $val['my_score'] > 0 ? $val['my_score'] : 0;
                $list[$k]['ranking'] = $k+1;

                if($val['user_id'] == $current_user->ID){
                    $my_ranking = $list[$k];
                }
            }
        }

        wp_send_json_success(array('info'=>$list,'my_ranking'=>$my_ranking));

    }

    /**
     * 答案提交
     */
    public function answer_submit(){

        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_answer_submit_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        if(empty($_POST['match_more'])) $_POST['match_more'] = 1;
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
                    $twentyfour = new TwentyFour();
                    foreach ($match_questions as $val){
                        $results = $twentyfour->calculate($val);
                        //print_r($results);
                        $questions_answer[] = !empty($results) ? $results[0] : '本题无解';
                    }
                    $isRight = array_column($data_arr,'isRight');
                    //print_r($questions_answer);
                    //die;
                    $count_value = array_count_values($isRight);
                    $len = $count_value['true'];
                    $my_score = $len * 10;

                }else{

                    $len = count($match_questions);
                    $error_len = count(array_diff_assoc($questions_answer,$my_answer));
                    $my_score = ($len-$error_len)*10;
                }


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
                wp_set_object_terms( $post_id, array('test-question') ,'question_genre');

                break;
            default:
                wp_send_json_error(array('info'=>'未知错误'));
                break;
        }
        $update_arr['answer_status'] = 1;
        $update_arr['my_answer'] = json_encode($my_answer);
        $update_arr['surplus_time'] = $_POST['surplus_time'];
        $update_arr['my_score'] = $my_score;
        /*print_r($update_arr);
        die;*/
        $result = $wpdb->update($wpdb->prefix.'match_questions',$update_arr,array('user_id'=>$current_user->ID,'match_id'=>$_POST['match_id'],'project_id'=>$_POST['project_id'],'match_more'=>$_POST['match_more']));
        if($result){
            wp_send_json_success(array('info'=>'提交完成','url'=>home_url('matchs/'.$_POST['match_action'].'/match_id/'.$_POST['match_id'].'/project_id/'.$_POST['project_id'].'/match_more/'.$_POST['match_more'])));
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
        if($result){
            $url = home_url('matchs/'.$_POST['match_action'].'/match_id/'.$_POST['match_id'].'/project_id/'.$_POST['project_id'].'/match_more/'.$_POST['match_more']);
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
                 left join {$wpdb->prefix}order b on a.match_id = b.match_id and b.user_id = {$current_user->ID} and b.pay_status = 2 
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
        if(empty(get_user_meta($current_user->ID,'user_real_name'))){
            wp_send_json_error(array('info'=>'请先实名认证'));
        }

        if(count($_POST['project_id']) != count($_POST['major_coach'])) wp_send_json_error(array('info'=>'主训教练未设置齐全'));
        if(empty($_POST['team_id'])) wp_send_json_error(array('info'=>'所属战队不能为空'));
        if(empty($_POST['fullname'])) wp_send_json_error(array('info'=>'收件人姓名不能为空'));
        if(empty($_POST['telephone'])) wp_send_json_error(array('info'=>'联系电话不能为空'));
        if(empty($_POST['address'])) wp_send_json_error(array('info'=>'收货地址不能为空'));

        $row = $wpdb->get_row("select id from {$wpdb->prefix}order where user_id = {$current_user->ID} and match_id = {$_POST['match_id']}");
        if(!empty($row)) wp_send_json_error(array('info'=>'你已报名该比赛','url'=>home_url('matchs/info&match_id='.$_POST['match_id'])));
        $data = array(
            'user_id'=>$current_user->ID,
            'match_id'=>$_POST['match_id'],
//            'cost'=>intval($_POST['cost']),
            'cost'=> $_POST['cost'],
            'fullname'=>$_POST['fullname'],
            'telephone'=>$_POST['telephone'],
            'address'=>$_POST['address'],
            'order_type'=>1,
            'pay_status'=>1,
            'created_time'=>date('Y-m-d H:i:s',time()),
        );
        //print_r($data);die;
        //开启事务
        $wpdb->startTrans();
        $a = $wpdb->insert($wpdb->prefix.'order',$data);

        //生成流水号
        $serialnumber = createNumber($current_user->ID,$wpdb->insert_id);
        $b = $wpdb->update($wpdb->prefix.'order',array('serialnumber'=>$serialnumber),array('id'=>$wpdb->insert_id));

        if($b && $a ){

            $wpdb->commit();
            wp_send_json_success(array('info' => '请选择支付方式','serialnumber'=>$serialnumber));
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
        $pageSize = 15;
        $start = ($page-1)*$pageSize;

        $sql = "select SQL_CALC_FOUND_ROWS a.user_id,a.apply_status,
                IFNULL (b.read, '-') as `read`,
                IFNULL(b.memory,'-') as memory, 
                IFNULL(b.compute,'-') as `compute`, 
                IFNULL(b.mental,'-') as `mental` 
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
                $sql1 = "select meta_key,meta_value from {$wpdb->prefix}usermeta where meta_key in('user_head','user_ID','nickname') and user_id = {$v['user_id']}";
                $user = $wpdb->get_results($sql1,ARRAY_A);
                $user_info = array_column($user,'meta_value','meta_key');
                $rows[$k]['user_ID'] = $user_info['user_ID'];
                $rows[$k]['nickname'] = $user_info['nickname'];
                $rows[$k]['user_head'] = !empty($user_info['user_head']) ? $user_info['user_head'] : student_css_url.'image/nlyd.png';
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
        //判断是否有战队
        $sql = "select id,team_id,user_id,status from {$wpdb->prefix}match_team where user_id = {$current_user->ID} ";

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
                wp_send_json_error(array('info'=>$info));
            }
            $id = $wpdb->get_var("select id from {$wpdb->prefix}match_team where team_id = {$_POST['team_id']} and user_id = {$current_user->ID} and user_type = 1");

            if(empty($id)){
                $result = $wpdb->insert($wpdb->prefix.'match_team',array('team_id'=>$_POST['team_id'],'user_id'=>$current_user->ID,'user_type'=>1,'status'=>1,'created_time'=>date('Y-m-d H:i:s',time())));
            }else{
                $result = $wpdb->update($wpdb->prefix.'match_team',array('status'=>1,'created_time'=>date('Y-m-d H:i:s',time())),array('id'=>$id,'team_id'=>$_POST['team_id'],'user_id'=>$current_user->ID));
            }

        }else{
            $sql .= " and team_id = {$_POST['team_id']} and status = 2 ";
            //print_r($sql);die;
            $row = $wpdb->get_row($sql);
            if(empty($row)) wp_send_json_error(array('info'=>'你还没有加入任何战队'));

            $result = $wpdb->update($wpdb->prefix.'match_team',array('status'=>-1),array('user_id'=>$current_user->ID,'team_id'=>$_POST['team_id']));
        }

        if($result){

            /***短信通知战队负责人****/

            /***********end************/

            wp_send_json_success(array('info'=>'操作成功,等待战队受理'));
        }else{
            wp_send_json_error(array('info'=>'操作失败'));
        }
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
        $pageSize = 15;
        $start = ($page-1)*$pageSize;

        $sql = "select SQL_CALC_FOUND_ROWS a.user_id,a.user_type,
                a.status,IFNULL(b.read,'-') as `read`,
                IFNULL(b.memory,'-') as memory,
                IFNULL(b.compute,'-') as compute,
                IFNULL(b.mental,'-') as mental 
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
                $sql1 = "select meta_key,meta_value from {$wpdb->prefix}usermeta where meta_key in('user_head','user_ID','nickname') and user_id = {$v['user_id']}";
                $user = $wpdb->get_results($sql1,ARRAY_A);
                $user_info = array_column($user,'meta_value','meta_key');
                $rows[$k]['user_ID'] = $user_info['user_ID'];
                $rows[$k]['nickname'] = $user_info['nickname'];
                $rows[$k]['user_head'] = !empty($user_info['user_head']) ? $user_info['user_head'] : student_css_url.'image/nlyd.png';
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
        $pageSize = 15;
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
            $a = $wpdb->update($wpdb->prefix.'my_address',array('is_default'=>''),array('user_id'=>$current_user->ID));
        }

        if(empty($_POST['id'])){

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

        if($a && $result){
            $wpdb->commit();
            $url = home_url('account/address');
            if(empty($_POST['match_id'])) $url .= '/match_id/'.$_POST['match_id'];
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
        $pageSize = 15;
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
            $wap[] = " a.user_id = {$_POST['user_id']} ";
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

                if(!empty($my_coach)){
                    if($my_coach['apply_status'] == 2){
                        $rows[$k]['my_coach'] = 'y';
                        $rows[$k]['my_major_coach'] = $my_coach['major'] == 1 ? 'y' : 'n';
                    }
                }
                $rows[$k]['category_id'] = $category_id;
                $rows[$k]['apply_status'] = $my_coach['apply_status'];
                $rows[$k]['coach_url'] = home_url('/teams/coachDetail/coach_id/'.$val['coach_id']);
            }

        }
        //print_r($rows);
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

        if(empty($_POST['category_id']) || empty($_POST['coach_id'])) wp_send_json_error(array('info'=>'参数错误'));

        global $wpdb,$current_user;
        //查询以前是否进行过申请
        $id = $wpdb->get_var("select id from {$wpdb->prefix}my_coach where user_id = {$current_user->ID} and category_id = {$_POST['category_id']} and coach_id = {$_POST['coach_id']}");

        if(empty($id)){
            $data = array('category_id'=>$_POST['category_id'],'coach_id'=>$_POST['coach_id'],'user_id'=>$current_user->ID,'apply_status'=>1);
            $result = $wpdb->insert($wpdb->prefix.'my_coach',$data);
        }else{
            $result = $wpdb->update($wpdb->prefix.'my_coach',array('apply_status'=>1,'major'=>''),array('id'=>$id,'category_id'=>$_POST['category_id'],'user_id'=>$current_user->ID));
        }

        if($result){

            /***********发送短信通知教练*************/


            /******************end*******************/

            wp_send_json_success(array('info'=>'申请成功,请等待教练同意'));
        }else{
            wp_send_json_error(array('info'=>'申请失败'));
        }

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

        //获取教练信息
        $row = $wpdb->get_row("select id,user_id,category_id,apply_status,major from {$wpdb->prefix}my_coach where coach_id = {$_POST['coach_id']} and user_id = $current_user->ID and category_id = {$_POST['category_id']} ",ARRAY_A);

        if(empty($row)) wp_send_json_error(array('info'=>'数据错误'));
        if($row['apply_status'] != 2) wp_send_json_error(array('该教练还不是你的教练'));
        $major = $row['major'] != 1 ? 1 : '';
        
        $a = $wpdb->update($wpdb->prefix.'my_coach',array('major'=>''),array('category_id'=>$_POST['category_id'],'user_id'=>$current_user->ID));
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
        global $wpdb;

        $page = isset($_POST['page'])?$_POST['page']:1;
        $pageSize = 15;
        $start = ($page-1)*$pageSize;

        $sql2 = "select SQL_CALC_FOUND_ROWS user_id,created_time from {$wpdb->prefix}order where match_id = {$_POST['match_id']} limit {$start},{$pageSize}";
        $orders = $wpdb->get_results($sql2,ARRAY_A);
        //print_r($orders);
        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>'已经到底了'));
        if(empty($orders)) wp_send_json_error(array('info'=>'暂无选手报名'));
        foreach ($orders as $k => $v){
            $user = get_user_meta($v['user_id']);
            //print_r($user);
            $orders[$k]['nickname'] = $user['nickname'][0];
            $orders[$k]['user_gender'] = $user['user_gender'][0] ? $user['user_gender'][0] : '--' ;
            $orders[$k]['user_head'] = isset($user['user_head']) ? $user['user_head'][0] : student_css_url.'image/nlyd.png';
            if(!empty($user['user_real_name'])){
                $user_real = unserialize($user['user_real_name'][0]);
                $orders[$k]['real_age'] = $user_real['real_age'];

            }else{
                $orders[$k]['real_age'] = '--';
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
        $pageSize = 15;
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
                  where user_id = {$current_user->ID} and b.match_status in(2,1,-2) 
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
                $sql_ = "select count(id) total from {$wpdb->prefix}order where match_id = {$val['ID']} ";
                $row = $wpdb->get_row($sql_,ARRAY_A);
                $rows[$k]['entry_total'] = !empty($row['total']) ? $row['total'] : 0;
                //前端需要的数组
                $rows[$k]['match_start_time_arr'] = str2arr(time_format(strtotime($val['match_start_time']),'Y-m-d-H-i-s'),'-');
                $rows[$k]['entry_end_time_arr'] = str2arr(time_format(strtotime($val['entry_end_time']),'Y-m-d-H-i-s'),'-');
                //两个链接
                if($val['match_status'] == 2){
                    //比赛中
                    $url = home_url('/matchs/matching/match_id/'.$val['ID']);
                    $button_title = '进入比赛';
                }else if ($val['match_status'] == 1){
                    //报名中
                    $url = home_url('matchs/confirm/match_id/'.$val['ID']);
                    $button_title = '参赛报名';
                }
                else if ($val['match_status'] == -1){
                    //未开始
                    $url = '';
                }else if($val['match_status'] == -3){
                    //已结束
                    $url = home_url('matchs/matching/match_id/'.$val['ID']);
                }else{
                    //等待开赛
                    $url = '';
                }
                $rows[$k]['button_title'] = $button_title;
                $rows[$k]['right_url'] = $url;
                $rows[$k]['left_url'] = home_url('matchs/info/match_id/'.$val['ID']);
                $rows[$k]['new_time'] = str2arr(time_format(time(),'Y-m-d-H-i-s'),'-');
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
        }else{
            $map[] = " b.match_status != -3 ";    //近期
            $match_type = 'recent';
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
        $pageSize = 15;
        $start = ($page-1)*$pageSize;

        $where = join(' and ',$map);

        $sql = "select SQL_CALC_FOUND_ROWS a.ID,a.post_title,a.post_content,b.match_start_time,
                b.match_address,b.match_cost,b.entry_end_time,b.match_status ,c.user_id,
                (case b.match_status 
                when -3 then '已结束' 
                when -2 then '等待开赛' 
                when -1 then '未开始' 
                when 1 then '报名中' 
                when 2 then '比赛中' 
                end) match_status_cn
                from {$wpdb->prefix}posts a
                left join {$wpdb->prefix}match_meta b on a.ID = b.match_id
                left join {$wpdb->prefix}order c on a.ID = c.match_id and c.user_id = {$current_user->ID}
                where {$where} order by b.match_status desc,b.match_start_time asc limit $start,$pageSize;
                ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);

        $total = $wpdb->get_row('select FOUND_ROWS() total',ARRAY_A);
        $maxPage = ceil( ($total['total']/$pageSize) );
        if($_POST['page'] > $maxPage && $total['total'] != 0) wp_send_json_error(array('info'=>'已经到底了'));
        //print_r($rows);
        if(empty($rows)) wp_send_json_error(array('info'=>'暂无比赛'));
        foreach ($rows as $k => $val){
            //获取报名人数
            $sql_ = "select count(id) total from {$wpdb->prefix}order where match_id = {$val['ID']} ";
            $row = $wpdb->get_row($sql_,ARRAY_A);
            $rows[$k]['entry_total'] = !empty($row['total']) ? $row['total'] : 0;
            //前端需要的数组
            $rows[$k]['match_start_time_arr'] = str2arr(time_format(strtotime($val['match_start_time']),'Y-m-d-H-i-s'),'-');
            $rows[$k]['entry_end_time_arr'] = str2arr(time_format(strtotime($val['entry_end_time']),'Y-m-d-H-i-s'),'-');
            //两个链接
            if($val['match_status'] == 2){
                //比赛中
                $url = home_url('matchs/matching/match_id/'.$val['ID']);
                $button_title = '进入比赛';
            }else if ($val['match_status'] == 1){
                //报名中
                $url = home_url('matchs/confirm/match_id/'.$val['ID']);
                $button_title = '参赛报名';
            }
            else if ($val['match_status'] == -1){
                //未开始
                $url = '';
            }else if($val['match_status'] == -3){
                //已结束
                $url = '';
            }else{
                //等待开赛
                $url = home_url('matchs/matchWaitting/match_id/'.$val['ID']);
                $button_title = '等待开赛';
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
                    update_user_meta($current_user->ID,'nickname',$_POST['meta_val']);

                    break;
                default:

                    //wp_send_json_error(array('info'=>'非法操作'));
                    break;
            }

            $resul = $wpdb->update($wpdb->users,array($_POST['meta_key']=>$_POST['meta_val']),array('ID'=>$current_user->ID));

        }else{

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
                    if(empty($_POST['meta_val']['real_name'])) wp_send_json_error(array('info'=>'真实姓名不能为空'));
                    if(empty($_POST['meta_val']['real_ID'])) wp_send_json_error(array('info'=>'证件号不能玩为空'));
                    if(!reg_match($_POST['meta_val']['real_ID'],$_POST['meta_val']['real_type'])) wp_send_json_error(array('info'=>'证件号格式不正确'));
                    if(!empty($_POST['user_gender'])){
                        update_user_meta($current_user->ID,'user_gender',$_POST['user_gender']) && $user_gender_update = true;
                        unset($_POST['user_gender']);
                    }
                    if(!empty($_POST['meta_val']['real_age'])){
                        update_user_meta($current_user->ID,'user_age',$_POST['meta_val']['user_age']) && $user_age_update = true;
                    }
//                    $_POST['user_address'] = array(
//                        'province'=>'四川省',
//                        'city'=>'成都市',
//                        'area'=>'高新区',
//                    );
                    if(!empty($_POST['user_address'])){
                        update_user_meta($current_user->ID,'user_address',$_POST['user_address']) && $user_address_update = true;
                        unset($_POST['user_address']);
                    }

                    break;
                case 'user_sign':
                    if(mb_strlen($_POST['meta_val'],'utf-8') > 40) wp_send_json_error(array('info'=>'昵称不能超过40个字符'));
                    break;
                default:

                    //$resul = update_user_meta($current_user->ID,$_POST['meta_key'],$_POST['meta_val']);
                    break;
            }

            $resul = update_user_meta($current_user->ID,$_POST['meta_key'],$_POST['meta_val']) || isset($user_gender_update) ? true : false || isset($user_address_update) ? true : false || isset($user_age_update) ? true : false;

        }
        if($resul){

            $url = !empty($_POST['match_id']) ? home_url('/matchs/confirm/match_id/'.$_POST['match_id']) : home_url('account/info');
            wp_send_json_success(array('info'=>'保存成功','url'=>$url));
        }else{
            wp_send_json_success(array('info'=>'设置失败'));
        }
    }


    /**
     * 通过身份证自动计算年龄
     */
    public function reckon_age(){
        if(empty($_POST['real_ID'])) wp_send_json_error(array('info'=>'证件号不能玩为空'));
        if(!reg_match($_POST['real_ID'],'sf')) wp_send_json_error(array('info'=>'证件号格式不正确'));
        $sub_str = substr($_POST['real_ID'],6,4);
        $now = date("Y",time());
        $age = $now-$sub_str;
        $age = $age >0 ? $age : 1;
        if($age > 150){
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

            wp_send_json_success(array('info'=>'重置成功','url'=>home_url('/login')));
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
                if(time() > $sms['time']){
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
        if($template != 17 && $template != 19){
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
                'time' => time()+300,
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
                if(time() > $smtp['time']){
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
        if($template != 17 && $template != 19){
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
                'time' => time()+300,
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

            do_action( 'wp_login', $user->user_login, $user );

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

                    wp_send_json_success( array('info'=>'登录成功','url'=>home_url('student/account')));
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

            wp_send_json_success(array('info'=>'注册成功','url'=>home_url('student/account')));
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
            wp_send_json_success(array('info'=>'重置成功','url'=>home_url('/login')));
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
            M('Picture')->create_time=time();
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
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        $_SESSION['login_time'] = time()+15;
        //update_user_meta($user_id,'last_login_time',time());
    }

    /**
     * 用户退出
     */
    public function user_logout(){

        wp_logout();
        unset($_SESSION['login_time']);
        wp_send_json_success(array('info'=>'退出成功','url'=>home_url('student/login')));
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
            case 2://待发货
                $payStatusWhere = '1=1';
                break;
            case 3://待收货
                $payStatusWhere = '1=1';
                break;
            default:
                wp_send_json_error(array('info' => '参数错误'));
        }
        $page < 1 && $page = 1;
        $pageSize = 15;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results('SELECT 
        id,
        serialnumber,
        match_id,
        IFNULL(fullname, "-") AS fullname,
        telephone,
        IFNULL(address, "-") AS address,
        CASE order_type 
        WHEN 1 THEN "报名订单" 
        END AS order_type,
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
        WHEN 2 THEN "支付完成" 
        END AS pay_status,
        created_time
        FROM '.$wpdb->prefix.'order WHERE user_id='.$current_user->ID.' 
        AND '.$payStatusWhere.' 
        LIMIT '.$start.','.$pageSize, ARRAY_A);
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

                //请求数据
                //1.统一下单方法
                $params['notify_url'] = home_url('payment/wxpay/'); //商品描述
                $params['body'] = '脑力运动'; //商品描述
                $params['serialnumber'] = $order['serialnumber']; // TODO 商户自定义的订单号
                $params['price'] = $order['cost']; //订单金额 只能为整数 单位为分
                $params['attach'] = 'serialnumber='.$order['serialnumber']; //附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
                $wxpay = new Student_Payment('wxpay');
                $result = $wxpay::$payClass->h5UnifiedOrder($params);


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
        $date = date('Y m');
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
            'created_time' => date('Y-m-d H:i:s')
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
        $pageSize = 15;
        $which_cat = get_category_by_slug('news');
        $recentPosts = new WP_Query();
        $cat_query = $recentPosts->query('showposts='.$pageSize.'&cat='.$which_cat->cat_ID.'&paged='.$page);
        if($cat_query)
            wp_send_json_success(array('info' => $cat_query));
        else
            wp_send_json_error(array('info' => '获取失败'));
    }


    /**
     * 战绩排名
     */



    /****************************************************以下为后台Ajax功能方法***********************************************************************/

//    /**
//     * 申请教练审核
//     */
//    public function coachApplyStatus(){
//        global $wpdb;
//        $id = intval($_GET['id']);
//        $status = intval($_GET['status']);
//        if($status != -1  && $status != 2){
//            wp_send_json_error(array('info' => '操作失败,状态参数异常'));
//        }
//        $bool = $wpdb->update($wpdb->prefix.'my_coach', array(
//            'apply_status' => $status
//        ), array(
//            'id' => $id
//        ));
//
//        if($bool) wp_send_json_success(array('info'=>'操作成功'));
//        else wp_send_json_error(array('info' => '操作失败'));
//    }
//
//    /**
//     * 申请加入战队审核
//     */
//    public function matchTeamApplyStatus(){
//        global $wpdb;
//        $id = intval($_GET['id']);
//        $status = intval($_GET['status']);
//        if($status != -3 && $status != -2 && $status != 2) wp_send_json_error(array('info' => '操作失败,状态参数异常'));
//        $bool = $wpdb->update($wpdb->prefix.'match_team', array(
//            'status' => $status
//        ), array(
//            'id' => $id
//        ));
//        if($bool) wp_send_json_success(array('info'=>'操作成功'));
//        else wp_send_json_error(array('info' => '操作失败'));
//    }
//
//    /**
//     * 支付查询订单
//     */
//    public function queryPayOrder(){
//        $id = intval($_POST['id']);
//        global $wpdb;
//        $order = $wpdb->get_row('SELECT pay_lowdown,serialnumber,pay_status,pay_type FROM '.$wpdb->prefix.'order WHERE id='.$id, ARRAY_A);
//        if(!$order) wp_send_json_error(array('info' => '未找到订单'));
//        if($order && $order['pay_status'] == 2){
//            switch ($order['pay_type']){
//                case 'wx':
//                    //微信
//                    require_once 'class-student-account-wxpay.php';
//                    $payClass = new Student_Account_Wxpay();
//                    $param = [
//                        'transaction_id' => unserialize($order['pay_lowdown'])['transaction_id'], //微信订单号(二选一)
//                        'order_no' => $order['serialnumber']// 商户订单号 (二选一)
//                    ];
//                    $result = Student_Account_Wxpay::$payClass->orderQuery($param); //return array
//                    break;
//                case 'zfb':
//                    require_once 'class-student-account-alipay.php';
//                    $param = [
//                        'out_trade_no' => $order['serialnumber'],
//                        'trade_no' => unserialize($order['pay_lowdown'])['trade_no'],
//                    ];
//                    $studetPay = new Student_Account_Alipay();
//                    $result = $studetPay::$payClass->queryOrder($param);
//                    break;
//                case 'ylk':
//
//                    break;
//                default:
//                    wp_send_json_error(array('info' => '订单无支付方式'));
//            }
//        }else{
//            wp_send_json_error(array('info' => '订单支付状态异常'));
//        }
//
//        if($result){
//            if($result['status'] == true){
//                //TODO
//                wp_send_json_success(array('info'=>$result['data']));
//            }else{
//                wp_send_json_error(array('info' => $result['data']));
//            }
//
//        }else{
//            wp_send_json_error(array('info' => '查询失败'));
//        }
//    }
//
//    /**
//     * 支付申请退款
//     */
//    public function refundPay(){
//        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_refund_code_nonce') ) {
//            wp_send_json_error(array('info'=>'非法操作'));
//        }
//        $id = intval($_POST['id']);
//        global $wpdb;
//        $refundFee = $_POST['refund_fee'];
//        $order = $wpdb->get_row('SELECT serialnumber,pay_lowdown,cost,pay_type FROM '.$wpdb->prefix.'order WHERE id='.$id.' AND pay_status=2', ARRAY_A);
//
//        if(!$order) wp_send_json_error(array('info' => '未找到订单,请刷新重试'));
//
//        if($refundFee > $order['cost']) wp_send_json_error(array('info' => '退款金额不能超过订单金额'));
//
//        $wpdb->startTrans();
//        $refund_no = $order['serialnumber'].date('dHis').rand(000,999);
//        //创建退款单
//        if(!$wpdb->insert($wpdb->prefix.'order_refund',['order_id' => $order['id'], 'refund_no' => $refund_no, 'created_time' => date('Y-m-d H:i:s')])){
//            $wpdb->rollback();
//            wp_send_json_error(array('info'=>'生成退款单失败'));
//        }
//        $orderRefundId = $wpdb->insert_id;
//        switch ($order['pay_type']){
//            case 'wx':
//                require_once 'class-student-account-wxpay.php';
//                $param['transaction_id'] = unserialize($order['pay_lowdown'])['transaction_id']; //微信订单号
//                $param['out_trade_no'] = $order['serialnumber'];
//                $param['out_refund_no'] = $refund_no; //TODO 商户的微信退款单号
//                $param['refund_fee'] = $refundFee;
//                $param['price'] = $order['cost'];
//                $payClass = new Student_Account_Wxpay();
//                $result = $payClass::$payClass->refund($param);
//                break;
//            case 'zgb':
//                require_once 'class-student-account-wxpay.php';
//                $param = [
//                    'out_trade_no' => $order['serialnumber'],     //商户订单号，和支付宝交易号二选一
//                    'trade_no' => unserialize($order['pay_lowdown'])['trade_no'],    //支付宝交易号，和商户订单号二选一
//                    'refund_amount' => $refundFee,       //退款金额，不能大于订单总金额
//                    'refund_reason' => '商家退款',     //退款的原因说明
//                    'out_request_no' => $refund_no,      //标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传。
//                ];
//                $payClass = new Student_Account_Alipay();
//                $result = $payClass::$payClass->refund($param);
//                break;
//            case 'ylk':
//
//                break;
//            default:
//                wp_send_json_error(array('info' => '此订单无支付方式'));
//        }
//
//        if($result){
//            if($result['status'] == true){
//                //TODO
//                $wpdb->update($wpdb->prefix.'order_refund', ['refund_lowdown' => serialize($result['data'])], ['id' => $orderRefundId]);
//                $wpdb->commit();
//                wp_send_json_success(array('info'=> '申请退款成功'));
//            }else{
//                $wpdb->rollback();
//                wp_send_json_error(array('info' => $result['data']));
//            }
//
//        }else{
//            $wpdb->rollback();
//            wp_send_json_error(array('info' => '查询失败'));
//        }
//    }
//
//
//    /**
//     * 支付查询退款
//     */
//    public function wxRefundQuery(){
//        $id = intval($_POST['id']);
//        global $wpdb;
//        $order = $wpdb->get_row('SELECT o.serialnumber,o.pay_lowdown,o.cost,o.pay_type,r.refund_no,r.refund_lowdown FROM '
//                                .$wpdb->prefix.'order_refund AS r
//                                LEFT JOIN '.$wpdb->prefix.'order AS o ON o,id=r.order_id
//                                WHERE r.id='.$id, ARRAY_A);
//
//        if(!$order) wp_send_json_error(array('info' => '未找到订单,请刷新重试'));
//        switch ($order['pay_type']){
//            case 'wx':
//                require_once 'class-student-account-wxpay.php';
//                $param = [
//                    'transaction_id' => unserialize($order['pay_lowdown'])['transaction_id'], //微信订单号 (四选一)
//                    'out_trade_no' => $order['serialnumber'], //商户订单号 (四选一)
//                    'out_refund_no' => $order['refund_no'], //商户退款单号 (四选一)
//                    'refund_id' => unserialize($order['refund_lowdown'])['refund_id'], //微信退款单号 (四选一)
//                ];
//                $payClass = new Student_Account_Wxpay();
//                $res = $payClass::$payClass->refundQuery($param);
//                break;
//            case 'zfb':
//                require_once 'class-student-account-alipay.php';
//                $param = [
//                    'out_trade_no' => $order['serialnumber'],        //商户订单号，和支付宝交易号二选一
//                    'trade_no' =>  unserialize($order['pay_lowdown'])['trade_no'],        //支付宝交易号，和商户订单号二选一
//                    'out_request_no' => unserialize($order['refund_lowdown'])['out_request_no'],        //请求退款接口时，传入的退款请求号，如果在退款请求时未传入，则该值为创建交易时的外部交易号
//                ];
//                $payClass = new Student_Account_Alipay();
//                $res = $payClass::$payClass->refund($param);
//                break;
//            case 'ylk':
//
//                break;
//            default:
//                wp_send_json_error(array('info' => '此订单无支付方式'));
//        }
//
//        if($res['status']){
//            wp_send_json_success(array('info'=>$res['data']));
//        }else{
//            wp_send_json_error(array('info' => $res['data']));
//        }
//    }
//
//    /**
//     * 支付关闭订单
//     */
//    public function closePayOrder(){
//        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_closepay_code_nonce') ) {
//            wp_send_json_error(array('info'=>'非法操作'));
//        }
//        $id = intval($_POST['id']);
//        global $wpdb;
//        $order = $wpdb->get_row('SELECT serialnumber,pay_lowdown,cost,pay_type FROM '.$wpdb->prefix.'order WHERE id='.$id, ARRAY_A);
//        if(!$order) wp_send_json_error(array('info' => '未找到订单,请刷新重试'));
//
//        switch ($order['pay_type']){
//            case 'wx':
//                //微信
//                require_once 'class-student-account-wxpay.php';
//                $payClass = new Student_Account_Wxpay();
//                $result = $payClass::$payClass->closeOrder($order['serialnumber']); //return array
//                break;
//            case 'zfb':
//                require_once 'class-student-account-alipay.php';
//                $param = [
//                    'out_trade_no' => $order['serialnumber'],//商户订单号，和支付宝交易号二选一
//                    'trade_no' => unserialize($order['pay_lowdown'])['trade_no'],        //支付宝交易号，和商户订单号二选一
//                ];
//                $payClass = new Student_Account_Alipay();
//                $result = $payClass::$payClass->closeOrder($param);
//                break;
//            case 'yld':
//
//                break;
//            default:
//                wp_send_json_error(array('info' => '此订单无支付方式'));
//        }
//
//        if($result['status']){
//            //TODO 关闭成功
//            wp_send_json_success(array('info' => '支付订单已关闭'));
//        }else{
//            wp_send_json_error(array('info' => $result['data']));
//        }
//    }
}

new Student_Ajax();