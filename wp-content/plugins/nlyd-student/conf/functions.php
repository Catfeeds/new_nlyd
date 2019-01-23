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
        'recommend_qualified'=>array('title'=>'购课补贴'),
        'grading_qualified'=>array('title'=>'考级达标'),
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