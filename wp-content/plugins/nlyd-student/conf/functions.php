<?php
/**
 * 年齡組別
 */
function get_age_group(){
    return array('1'=>__('儿童组', 'nlyd-student'),'2'=>__('少年组', 'nlyd-student'),'3'=>__('成年组', 'nlyd-student'),'4'=>__('老年组', 'nlyd-student'));
}

/**
 * @param $age 年龄
 * 根据年龄获取组别名称
 */
if(!function_exists('getAgeGroupNameByAge')) {
    function getAgeGroupNameByAge($age)
    {
        switch ($age) {
            case $age > 59:
                $group = __('老年组', 'nlyd-student');
                break;
            case $age > 17:
                $group = __('成年组', 'nlyd-student');
                break;
            case $age > 12:
                $group = __('少年组', 'nlyd-student');
                break;
            default:
                $group = __('儿童组', 'nlyd-student');
                break;
        }
        return $group;
    }
}

/**
 * 收入来源默认数组
 */
function income_stream_array(){
    $default = array(
        'open_match'=>array('title'=>'开设比赛'),
        'open_grading'=>array('title'=>'开设考级'),
        'open_course'=>array('title'=>'课程渠道'),
        'recommend_match'=>array('title'=>'推荐比赛'),
        'recommend_grading'=>array('title'=>'推荐考级'),
        'director_match'=>array('title'=>'参赛机构'),
        'director_grading'=>array('title'=>'考级负责人'),
        'recommend_match_zone'=>array('title'=>'推荐赛区'),
        'recommend_trains_zone'=>array('title'=>'推荐训练中心'),
        'recommend_test_zone'=>array('title'=>'推荐测评中心'),
        'recommend_course'=>array('title'=>'推荐购课'),
        'recommend_qualified_coach'=>array('title'=>'学员分享-教练'),
        'recommend_qualified_zone'=>array('title'=>'学员分享-机构'),
        'grading_qualified'=>array('title'=>'考级达标'),
        'share_qualified'=>array('title'=>'乐学乐分享活动达标'),
        'cause_manager'=>array('title'=>'事业管理员'),
        'cause_minister'=>array('title'=>'事业部长'),
        'extract'=>array('title'=>'提现'),
    );
}

// 获取ip
function GetIp(){
    $realip = '';
    $unknown = 'unknown';
    if (isset($_SERVER)){
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach($arr as $ip){
                $ip = trim($ip);
                if ($ip != 'unknown'){
                    $realip = $ip;
                    break;
                }
            }
        }else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)){
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)){
            $realip = $_SERVER['REMOTE_ADDR'];
        }else{
            $realip = $unknown;
        }
    }else{
        if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        }else if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)){
            $realip = getenv("HTTP_CLIENT_IP");
        }else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)){
            $realip = getenv("REMOTE_ADDR");
        }else{
            $realip = $unknown;
        }
    }
    $realip = preg_match("/[\d\.]{7,15}/", $realip, $matches) ? $matches[0] : $unknown;
    return $realip;
}

//淘宝接口：根据ip获取所在城市名称
function get_area($ip = ''){
    if($ip == ''){
        $ip = GetIp();
    }
    $url = "http://ip.taobao.com/service/getIpInfo.php?ip={$ip}";
    $ret = https_request($url);
    $arr = json_decode($ret,true);
    return $arr;
}

//新浪接口：根据ip获取所在城市名称
function GetIpLookup($ip = ''){
    if(empty($ip)){
        $ip = GetIp();
    }
    $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);//新浪的开放API
    if(empty($res)){ return false; }
    $jsonMatches = array();
    preg_match('#\{.+?\}#', $res, $jsonMatches);
    if(!isset($jsonMatches[0])){ return false; }
    $json = json_decode($jsonMatches[0], true);
    if(isset($json['ret']) && $json['ret'] == 1){
        $json['ip'] = $ip;
        unset($json['ret']);
    }else{
        return false;
    }
    return $json;
}



//POST请求函数
function https_request($url,$data = null){
    $curl = curl_init();

    curl_setopt($curl,CURLOPT_URL,$url);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);

    if(!empty($data)){//如果有数据传入数据
        curl_setopt($curl,CURLOPT_POST,1);//CURLOPT_POST 模拟post请求
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);//传入数据
    }

    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    $output = curl_exec($curl);
    curl_close($curl);

    return $output;
}

function convertip($ip) {
    //IP数据文件路径
    $dat_path = leo_student_path.'conf/qqwry.dat';

    //检查IP地址
    if(!preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) {
        return 'IP Address Error';
    }

    //打开IP数据文件
    if(!$fd = @fopen($dat_path, 'rb')){
        return 'IP date file not exists or access denied';
    }

    //分解IP进行运算，得出整形数
    $ip = explode('.', $ip);
    $ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

    //获取IP数据索引开始和结束位置
    $DataBegin = fread($fd, 4);
    $DataEnd = fread($fd, 4);
    $ipbegin = implode('', unpack('L', $DataBegin));
    if($ipbegin < 0) $ipbegin += pow(2, 32);
    $ipend = implode('', unpack('L', $DataEnd));
    if($ipend < 0) $ipend += pow(2, 32);
    $ipAllNum = ($ipend - $ipbegin) / 7 + 1;

    $BeginNum = 0;
    $EndNum = $ipAllNum;

    //使用二分查找法从索引记录中搜索匹配的IP记录
    while($ip1num>$ipNum || $ip2num<$ipNum) {
        $Middle= intval(($EndNum + $BeginNum) / 2);

        //偏移指针到索引位置读取4个字节
        fseek($fd, $ipbegin + 7 * $Middle);
        $ipData1 = fread($fd, 4);
        if(strlen($ipData1) < 4) {
            fclose($fd);
            return 'System Error';
        }
        //提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
        $ip1num = implode('', unpack('L', $ipData1));
        if($ip1num < 0) $ip1num += pow(2, 32);

        //提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
        if($ip1num > $ipNum) {
            $EndNum = $Middle;
            continue;
        }

        //取完上一个索引后取下一个索引
        $DataSeek = fread($fd, 3);
        if(strlen($DataSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
        fseek($fd, $DataSeek);
        $ipData2 = fread($fd, 4);
        if(strlen($ipData2) < 4) {
            fclose($fd);
            return 'System Error';
        }
        $ip2num = implode('', unpack('L', $ipData2));
        if($ip2num < 0) $ip2num += pow(2, 32);

        //没找到提示未知
        if($ip2num < $ipNum) {
            if($Middle == $BeginNum) {
                fclose($fd);
                return 'Unknown';
            }
            $BeginNum = $Middle;
        }
    }

    //下面的代码读晕了，没读明白，有兴趣的慢慢读
    $ipFlag = fread($fd, 1);
    if($ipFlag == chr(1)) {
        $ipSeek = fread($fd, 3);
        if(strlen($ipSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
        fseek($fd, $ipSeek);
        $ipFlag = fread($fd, 1);
    }

    if($ipFlag == chr(2)) {
        $AddrSeek = fread($fd, 3);
        if(strlen($AddrSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $ipFlag = fread($fd, 1);
        if($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if(strlen($AddrSeek2) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }

        while(($char = fread($fd, 1)) != chr(0))
            $ipAddr2 .= $char;

        $AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
        fseek($fd, $AddrSeek);

        while(($char = fread($fd, 1)) != chr(0))
            $ipAddr1 .= $char;
    } else {
        fseek($fd, -1, SEEK_CUR);
        while(($char = fread($fd, 1)) != chr(0))
            $ipAddr1 .= $char;

        $ipFlag = fread($fd, 1);
        if($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if(strlen($AddrSeek2) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }
        while(($char = fread($fd, 1)) != chr(0)){
            $ipAddr2 .= $char;
        }
    }
    fclose($fd);

    //最后做相应的替换操作后返回结果
    if(preg_match('/http/i', $ipAddr2)) {
        $ipAddr2 = '';
    }

    //$ipaddr = "$ipAddr1 $ipAddr2";
    $ipaddr = "$ipAddr1";
    $ipaddr = preg_replace('/CZ88.Net/is', '', $ipaddr);
    $ipaddr = preg_replace('/^s*/is', '', $ipaddr);
    $ipaddr = preg_replace('/s*$/is', '', $ipaddr);
    if(preg_match('/http/i', $ipaddr) || $ipaddr == '') {
        $ipaddr = 'Unknown';
    }
    $ipaddr = iconv("GB2312","UTF-8//IGNORE",$ipaddr);
    //print_r($ipaddr);
    if(preg_match('/省/',$ipaddr)){
        $ipaddr = preg_replace('/省/','省-',$ipaddr);
    }
    if(preg_match('/市市/',$ipaddr)){
        $ipaddr = preg_replace('/市市/','市-市',$ipaddr);
    }
    elseif (preg_match('/市/',$ipaddr)){
        $ipaddr = preg_replace('/市/','市-',$ipaddr);
    }
    if(preg_match('/区/',$ipaddr)){
        $ipaddr = preg_replace('/区/','区-',$ipaddr);
    }

    $location = rtrim($ipaddr,'-');

    return $location;
}

/**
 * 用户收益处理
 * $order 用户订单
 */
function set_user_income($order){
    global $wpdb;
    /*****************收益分配start*******************/

    //获取当前比赛场景
    if($order['order_type'] == 3){  //课程
        $sql = "select  a.zone_id,a.course_category_id,a.coach_id,b.type_alias from {$wpdb->prefix}course a
                    left join {$wpdb->prefix}course_type b on a.course_type = b.id
                    where a.id = {$order['match_id']} ";
        //print_r($sql);
    }
    else{

        //获取当前比赛场景
        if($order['order_type'] == 1){  //比赛
            $income_type = 'match_id';
            $table = $wpdb->prefix.'match_meta_new';
            $join = "match_scene";
            $field = 'a.match_id,a.match_scene,a.created_id,';
        }
        elseif ($order['order_type'] == 2){ //考级
            $income_type = 'grading_id';
            $table = $wpdb->prefix.'grading_meta';
            $join = "scene";
            $field = 'a.grading_id,a.scene,a.created_person,';
        }
        $sql = "select {$field} b.role_name,
                            b.role_type,b.role_alias,b.is_profit,b.status
                            from {$table} a
                            left join {$wpdb->prefix}zone_match_role b on a.{$join} = b.id
                            where a.{$income_type} = {$order['match_id']} and b.is_profit = 1 and b.status = 1";
    }
    $row = $wpdb->get_row($sql,ARRAY_A);
    //print_r($row);die;
    if(!empty($row)){
        switch ($order['order_type']){
            case 1:
            case 2:
                $zone_user_id = $row['created_id'] ? $row['created_id'] : $row['created_person'];
                //print_r($row);die;
                //获取机构赛区类型
                $zone_meta = $wpdb->get_row("select b.zone_type_alias,a.zone_match_type,a.is_double from {$wpdb->prefix}zone_meta a
                                                    left join {$wpdb->prefix}zone_type b on a.type_id = b.id
                                                    where user_id = {$zone_user_id}",ARRAY_A);
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
                $set_sql = "select * from {$wpdb->prefix}spread_set where spread_type = '{$zone_meta['zone_type_alias']}' and  match_grading = {$order['order_type']} and match_type = {$match_type}";
                break;
            case 3:
                $set_sql = "select * from {$wpdb->prefix}spread_set where spread_type = '{$row['type_alias']}' ";
                break;
            default:

                break;
        }
        //print_r($set_sql);die;
        $setting = $wpdb->get_row($set_sql,ARRAY_A);
        //print_r($setting);die;
        if(!empty($setting)){

            //获取直接/间接收益人
            $sql = "select a.ID user_id,a.referee_id,b.referee_id as indirect_referee_id from {$wpdb->prefix}users a left join {$wpdb->prefix}users b on a.referee_id = b.ID where a.ID = {$order['user_id']}";
            $user = $wpdb->get_row($sql,ARRAY_A);

            //获取比赛/考级相关信息
            if($order['order_type'] == 1){
                $income_type = 'match';
                //准备对应的数据
                $money1 = $setting['direct_superior'];     //比赛直接推广人 direct_superior
                $money2 = $setting['indirect_superior'];    //比赛间接推广人  indirect_superior
                $money3 = $setting['mechanism'];   //参赛机构        mechanism
                $money4 = $setting['sub_center'];   //办赛机构         sub_center
                $created_id = $wpdb->get_var("select created_id from {$wpdb->prefix}match_meta_new where match_id = {$order['match_id']}");
                $person_liable_id = $order['sub_centres_id'];
                $sponsor_id = $created_id;

            }
            elseif ($order['order_type'] == 2){
                $income_type = 'grading';
                //准备对应的数据
                $money1 = $setting['direct_superior'];     //比赛直接推广人
                $money2 = $setting['indirect_superior'];    //比赛间接推广人
                $money3 = $setting['coach'];        //责任教练
                $money4 = $setting['sub_center'];   //办赛机构

                $grading = $wpdb->get_row("select person_liable,created_person from {$wpdb->prefix}grading_meta where grading_id = {$order['match_id']}",ARRAY_A);
                $person_liable_id = $grading['person_liable'];
                $sponsor_id = $grading['created_person'];

            }
            elseif ($order['order_type'] == 3){

                //只有基础课才有收益分成
                if($row['type_alias'] == 'basis-course'){

                    $income_type = 'course';
                    //准备对应的数据
                    $money1 = $setting['direct_superior'];     //比赛直接推广人
                    $money2 = $setting['indirect_superior'];    //比赛间接推广人
                    $money3 = $setting['coach'];        //教练
                    $money4 = $setting['sub_center'];   //发布机构
                    $money5 = $setting['general_manager'];   //发布机构
                }
                //设置教练
                $coach_sql = "select id from {$wpdb->prefix}my_coach where user_id = {$order['user_id']} and category_id = {$row['course_category_id']} ";
                $coach_id = $wpdb->get_var($coach_sql);
                if($coach_id){
                    $wpdb->update($wpdb->prefix.'my_coach',array('coach_id'=>$row['coach_id'],'apply_status'=>2),array('id'=>$coach_id));
                }else{
                    $wpdb->insert($wpdb->prefix.'my_coach',array('user_id'=>$order['user_id'],'category_id'=>$row['course_category_id'],'coach_id'=>$row['coach_id'],'apply_status'=>2));
                }

                //print_r($coach_id);die;
            }
            //print_r($insert);die;
            $insert['created_time'] = get_time('mysql');

            $wpdb->query('START TRANSACTION');

            if(($user['referee_id'] > 0 || $row['zone_id'] > 0) && $money1 > 0){
                $referee_type = 'recommend_'.$income_type;
                if($user['referee_id'] > 0){
                    $referee_id = $user['referee_id'];
                }else{
                    $referee_id = $row['zone_id'];
                }

                $a = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('provide_id'=>$order['user_id'],'match_id'=>$order['match_id'],'user_id'=>$referee_id,'user_income'=>$money1,'income_rank'=>1,'income_type'=>$referee_type,'created_time'=>get_time('mysql')));

            }else{
                $a = true;
            }

            if($user['indirect_referee_id'] > 0 && $money2 > 0){
                $indirect_referee_type = 'recommend_'.$income_type;
                $b = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('provide_id'=>$order['user_id'],'match_id'=>$order['match_id'],'user_id'=>$user['indirect_referee_id'],'user_income'=>$money2,'income_rank'=>2,'income_type'=>$indirect_referee_type,'created_time'=>get_time('mysql')));

            }else{
                $b = true;
            }
            if($person_liable_id > 0 && $money3 > 0){
                $person_liable_type = 'director_'.$income_type;
                $c = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('provide_id'=>$order['user_id'],'match_id'=>$order['match_id'],'user_id'=>$person_liable_id,'user_income'=>$money3,'income_rank'=>1,'income_type'=>$person_liable_type,'created_time'=>get_time('mysql')));
            }else{
                $c = true;
            }

            if($sponsor_id > 0 && $money4 > 0){
                $sponsor_type = 'open_'.$income_type;
                $d = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('provide_id'=>$order['user_id'],'match_id'=>$order['match_id'],'user_id'=>$sponsor_id,'user_income'=>$money4,'income_rank'=>1,'income_type'=>$sponsor_type,'match_id'=>$order['match_id'],'created_time'=>get_time('mysql')));
            }else{
                $d = true;
            }

            if($order['order_type'] == 3 && !empty($user['referee_id']) && $row['type_alias'] == 'basis-course'){
                //判断直接推荐人推荐了几人购课
                $total = $wpdb->get_var("select count(*) from {$wpdb->prefix}user_stream_logs where income_type = 'recommend_course' and provide_id = {$order['user_id']} and user_id = {$user['referee_id']}");
                //print_r($total);die;

                //获取推荐人教练
                //$coach_id = $row['coach_id'];
                $coach_id = $wpdb->get_var("select coach_id from {$wpdb->prefix}my_coach where user_id = {$user['referee_id']} and category_id = {$row['course_category_id']} and apply_status =2 ");
                //print_r($coach_id);die;
                //教练收益
                if($total < 4 && $coach_id > 0){
                    $e = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('provide_id'=>$order['user_id'],'user_id'=>$coach_id,'match_id'=>$order['match_id'],'user_income'=>$money3,'income_rank'=>1,'income_type'=>'recommend_qualified_coach','created_time'=>get_time('mysql')));

                }else{
                    $e = true;
                }

                //print_r($total_);die;
                if($total < 4 ){
                    $f = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('provide_id'=>$order['user_id'],'match_id'=>$order['match_id'],'user_id'=>$row['zone_id'],'user_income'=>$money4,'income_rank'=>1,'income_type'=>'recommend_qualified_zone','created_time'=>get_time('mysql')));
                }else{
                    $f = true;
                }

                if($total >= 3){
                    //判断是否已经拿回学费补贴
                    $income_id = $wpdb->get_var("select id from {$wpdb->prefix}user_stream_logs where income_type = 'share_qualified' and user_id = {$order['user_id']} ");
                    if(empty($income_id)){
                        //获取用户考级等级
                        $skill_id = $wpdb->get_var("select id from {$wpdb->prefix}user_skill_rank where user_id = {$order['user_id']} and memory>= 2");

                        if($skill_id > 0){
                            $direct_superior = $wpdb->get_var("select direct_superior from {$wpdb->prefix}spread_set where spread_type = 'share_qualified' ");
                            if($direct_superior > 0 ){
                                $user_income = $direct_superior;
                            }elseif ($order['cost'] > 0){
                                $user_income = $order->cost;
                            }
                            if($user_income > 0){
                                $h = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('user_id'=>$user['referee_id'],'match_id'=>$order['match_id'],'income_type'=>'share_qualified','user_income'=>$user_income,'income_rank'=>1,'created_time'=>get_time('mysql')));
                            }
                        }
                    }
                }
                //print_r($coach_id);die;
            }else{
                $e = true;
                $f = true;
                $h = true;
            }

            if($order['order_type'] ==3){
                $center_manager_type = 'recommend_course';
                //获取总经理
                $center_manager_id = $wpdb->get_var("select center_manager_id from {$wpdb->prefix}zone_meta where user_id = {$row['zone_id']} ");
                if($center_manager_id > 0 && $money5 > 0){
                    $g = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('provide_id'=>$order['user_id'],'match_id'=>$order['match_id'],'user_id'=>$center_manager_id,'user_income'=>$money5,'income_type'=>$center_manager_type,'created_time'=>get_time('mysql')));
                }else{
                    $g = true;
                }

            }else{
                $g = true;
            }

            //print_r($a .'&&' .$b .'&&'. $c .'&&'. $d .'&&'. $e.'&&'. $f.'&&'. $g .'&&'.$h);die;
            if( $a && $b && $c && $d && $e && $f && $g && $h){
                $wpdb->query('COMMIT');
            }else{
                $wpdb->query('ROLLBACK');
            }
        }
    }

    /*****************收益分配end*******************/
}