<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/22
 * Time: 21:29
 */
//设置时区
//date_default_timezone_set('Asia/Shanghai');


/**
 * 计算年龄
 */
if(!function_exists('birthday')){

    function birthday($id){

        if(empty($id)) return '';
        $birthday=strtotime(substr($id,6,8));

        $new_time = time();
        $nowYear = (int) date("Y",$new_time);
        $nowMonth = (int) date('md',$new_time);
        // $nowDay = date('d',time());

        $birthYear = (int) date("Y",$birthday);
        $birthMonth = (int) date("md",$birthday);
        //$birthDay = date("d",$birthday);
        //var_dump($nowMonth);
        $age = $nowYear - $birthYear ;
        if($age < 1){
            return -1;
        }
        if($age  > 150){
            return -2;
        }
        if($birthMonth < $nowMonth)//生日的 小于 现在的日月  就加一岁
        {
            $age +=1;
        }

        return $age;

    }
}

/**
 * 计算比赛结束时间
 * @param $match_id
 */
if(!function_exists('get_match_end_time')){

    function get_match_end_time($match_id){
        global $wpdb;

        //获取比赛信息
        $sql = "select match_id,match_start_time,match_more,match_use_time,match_project_interval,match_subject_interval from {$wpdb->prefix}match_meta where match_id = {$match_id}";

        $match = $wpdb->get_row($sql,ARRAY_A);
        if(empty($match)){
            return -1; //error 比赛信息错误
        }
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
            return -2; //error 比赛项目未绑定
        }
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
                $project_use_time = $row['project_use_time'] > 0 ? $row['project_use_time'] : $match['match_use_time'];
            }
            $match_more = $row['match_more'] > 0 ? $row['match_more'] : $match['match_more'];
            $project_time_interval = $row['project_time_interval'] > 0 ? $row['project_time_interval'] : $match['match_subject_interval'];
            //项目间隔时间
            $project_interval = count($rows) - 1 == $k ? 0 :$match['match_project_interval'];


            if(strtotime($row['project_start_time']) > 0){
                $end_time = strtotime($row['project_start_time']) + ($project_use_time*$match_more + ($match_more-1)*$project_time_interval+$project_interval)*60;
                $rows[$k]['project_end_time'] = $row['project_end_time'] = date_i18n('Y-m-d H:i:s',$end_time);

            }else{

                $project_end_time = !empty($rows[$k-1]['project_end_time']) ? strtotime($rows[$k-1]['project_end_time']) + $match['match_project_interval']*60 : strtotime($match['match_start_time']);
                $end_time = $project_end_time + ($project_use_time*$match_more + ($match_more-1)*$project_time_interval)*60;
                $rows[$k]['project_end_time'] = $row['project_end_time'] = date_i18n('Y-m-d H:i:s',$end_time);
                $rows[$k]['project_start_time'] = $row['project_start_time'] = date_i18n('Y-m-d H:i:s',$project_end_time);
            }
            //leo_dump($rows[$k]['project_start_time'].'-----'.$rows[$k]['project_end_time']);

        }

        return $rows;
    }
}


//获取当前时间
if(!function_exists('get_time')){

    function get_time($type='timestamp'){

        if($type == 'mysql'){
            return current_time('mysql');
        }else{
            return strtotime(current_time('mysql'));
        }
    }
}

/**
 * 年齡組別
 */
function get_age_group(){
    return array('1'=>"儿童组",'2'=>"少年组",'3'=>"成年组",'4'=>"老年组");
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
                $group = '老年组';
                break;
            case $age > 17:
                $group = '成年组';
                break;
            case $age > 12:
                $group = '少年组';
                break;
            default:
                $group = '儿童组';
                break;
        }
        return $group;
    }
}

/*
 * 文章速读选项
 * $index 选项索引
 */
function get_select($index){
    $arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R');
    return $arr[$index];
}

/**
 * 扑克接力扑克生成
 */
function poker_create($order=true){
    $kinds=array("spade","heart","diamond","club");//kind数组盛放的是花型
    $nums=array("A", "2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K" );//52张牌对应的数字
    $poker = array();
    foreach ($kinds as $val){
        foreach ($nums as $v){
            $poker[] = $val.'-'.$v;
        }
    }

    if(!$order){
        return $poker;
    }
    shuffle($poker);
    shuffle($poker);
    return $poker;
}


/**
 * 比赛轮数简单转换
 * @param $num
 * @return mixed
 */
function chinanum($num){
    $china = array(
        1=>'一',
        2=>'二',
        3=>'三',
        4=>'四',
        5=>'五',
        6=>'六',
        7=>'七',
        8=>'八',
        9=>'九',
        10=>'十',
        11=>'十一',
        12=>'十二',
        13=>'十三',
        14=>'十四',
        15=>'十五',
        16=>'十六',
        17=>'十七',
        18=>'十八',
        19=>'十九',
        20=>'二十',
    );
    return $china[$num];
}


/**
 * 生成对应长度的比赛题
 * @param $length
 * @return array
 */
function rang_str_arr($length){

    $arr = array();
    for($i=0;$i<$length;++$i){
        $arr[] = rand(0,9);
    }
    if($length < 20){
        return $arr;
    }
    if(is_array($arr)) {
        foreach($arr as $k=>$v) {
            if(isset($arr[$k]) && isset($arr[$k+1]) && ($arr[$k]==$arr[$k+1])) {
                array_push($arr,$arr[$k+1]);
                unset($arr[$k+1]);
            }

        }

    }
    $newarr = array();
    if(is_array($arr)) {
        foreach($arr as $v) {
            $newarr[] = $v;
        }
    }
    return $newarr;
}

/**
 * 自定义var_dump输出展示
 */
function leo_dump($data){
    echo '<pre/>';
    print_r($data);
}


/**
 * 生成缴费流水记录号
 */
function createNumber($user_id,$order_id){

    return date('ymd').substr($user_id,-1).rand(1000,9999).$order_id*3;
}


/**
 * 视图模板载入
 * $path 文件路径
 * $dara 附带参数 数组形式传参
 */
if(!function_exists('load_view_template')){
    function load_view_template($path,$data=''){
        if(is_file($path)){
            if(!empty($data)) extract($data);
            include_once ($path);
        }else{
            //404
            return;
        }
    }
}

if(!function_exists('is_mobile')){
    /*判断手持设备*/
    function is_mobile( $a=false ) {
        global $is_mobile;
        if($is_mobile){
            return $is_mobile;
        }
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
        $is_mobile = false;
        foreach ($mobile_agents as $device) {
            if (stristr($user_agent, $device)) {
                if($a==true){

                    $is_mobile = $device;
                }else{

                    $is_mobile = true;
                }
                break;
            }
        }
        return $is_mobile;
    }

}

if(!function_exists('is_weixin')){
    function is_weixin(){
        return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger');
    }
}

if(!function_exists('is_qq')){
    function is_qq(){
        return strpos($_SERVER['HTTP_USER_AGENT'], 'QQ/');
    }
}
// 解析路由地址
// 参数1/值1/参数2/值2...
function parseUrl($url) {
    $var  =  array();
    $path = explode('/',$url);

    if(isset($path)) {
        for($i=0;$i<sizeof($path);$i=$i+2){
            $var[$path[$i]] = (isset( $path[$i+1])) ? $path[$i+1] : '';
        }
    }
    $_GET   =   array_merge($var, $_GET);
    //var_dump($_GET);
}

/**
 * URL组装 支持不同URL模式
 * @param string $url URL表达式，格式：'[模块/控制器/操作#锚点@域名]?参数1=值1&参数2=值2...'
 * @param string|array $vars 传入的参数，支持数组和字符串
 * @param string|boolean $suffix 伪静态后缀，默认为true表示获取配置值
 * @param boolean $domain 是否显示域名
 * @return string
 */
if(!function_exists('U')){

    function U($url='',$vars='',$suffix=true,$domain=false) {
        // 解析URL
        $info   =  parse_url($url);
        $url    =  !empty($info['path'])?$info['path']:ACTION_NAME;
        if(isset($info['fragment'])) { // 解析锚点
            $anchor =   $info['fragment'];
            if(false !== strpos($anchor,'?')) { // 解析参数
                list($anchor,$info['query']) = explode('?',$anchor,2);
            }
            if(false !== strpos($anchor,'@')) { // 解析域名
                list($anchor,$host)    =   explode('@',$anchor, 2);
            }
        }elseif(false !== strpos($url,'@')) { // 解析域名
            list($url,$host)    =   explode('@',$info['path'], 2);
        }
        // 解析子域名
        if(isset($host)) {
            $domain = $host.(strpos($host,'.')?'':strstr($_SERVER['HTTP_HOST'],'.'));
        }elseif($domain===true){
            $domain = $_SERVER['HTTP_HOST'];
            /*if(C('APP_SUB_DOMAIN_DEPLOY') ) { // 开启子域名部署
                $domain = $domain=='localhost'?'localhost':'www'.strstr($_SERVER['HTTP_HOST'],'.');
                // '子域名'=>array('模块[/控制器]');
                foreach (C('APP_SUB_DOMAIN_RULES') as $key => $rule) {
                    $rule   =   is_array($rule)?$rule[0]:$rule;
                    if(false === strpos($key,'*') && 0=== strpos($url,$rule)) {
                        $domain = $key.strstr($domain,'.'); // 生成对应子域名
                        $url    =  substr_replace($url,'',0,strlen($rule));
                        break;
                    }
                }
            }*/
        }

        // 解析参数
        if(is_string($vars)) { // aaa=1&bbb=2 转换成数组
            parse_str($vars,$vars);
        }elseif(!is_array($vars)){
            $vars = array();
        }
        if(isset($info['query'])) { // 解析地址里面参数 合并到vars
            parse_str($info['query'],$params);
            $vars = array_merge($params,$vars);
        }

        // URL组装
        $depr       =   '/';    //分隔符
        $urlCase    =   false;  // URL区分大小写
        if($url) {
            if(0=== strpos($url,'/')) {// 定义路由
                $route      =   true;
                $url        =   substr($url,1);
                if('/' != $depr) {
                    $url    =   str_replace('/',$depr,$url);
                }
            }else{
                if('/' != $depr) { // 安全替换
                    $url    =   str_replace('/',$depr,$url);
                }
                // 解析模块、控制器和操作
                $url        =   trim($url,$depr);
                $path       =   explode($depr,$url);
                $var        =   array();
                $varModule      =   'm';
                $varController  =   'c';
                $varAction      =   'a';
                $var[$varAction]       =   !empty($path)?array_pop($path):ACTION_NAME;
                $var[$varController]   =   !empty($path)?array_pop($path):CONTROLLER_NAME;

                if($urlCase) {
                    $var[$varController]   =   parse_name($var[$varController]);
                }
                $module =   '';

                if(!empty($path)) {
                    $var[$varModule]    =   implode($depr,$path);
                }

                if(isset($var[$varModule])){
                    $module =   $var[$varModule];
                    unset($var[$varModule]);
                }

            }
        }

        if(isset($route)) {
            $url    =   ROOT_URL.'/'.rtrim($url,$depr);
        }else{
            $module =   (defined('BIND_MODULE') && BIND_MODULE==$module )? '' : $module;
            $url    =   ROOT_URL.'/'.($module?$module.'/':'').implode($depr,array_reverse($var));
        }
        if($urlCase){
            $url    =   strtolower($url);
        }
        if(!empty($vars)) { // 添加参数
            foreach ($vars as $var => $val){
                if('' !== trim($val))   $url .= $depr . $var . $depr . urlencode($val);
            }
        }
        if($suffix) {
            $suffix   =  $suffix===true?'html':$suffix;
            if($pos = strpos($suffix, '|')){
                $suffix = substr($suffix, 0, $pos);
            }
            if($suffix && '/' != substr($url,-1)){
                $url  .=  '.'.ltrim($suffix,'.');
            }
        }
        if(isset($anchor)){
            $url  .= '#'.$anchor;
        }
        if($domain) {
            $url   =  (is_ssl()?'https://':'http://').$domain.$url;
        }
        return $url;
    }
}

/**
 * 检查目录是否存在,不存在增加
 */
if(!function_exists('mkdirs')){
    function mkdirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
        if (!mkdirs(dirname($dir), $mode)) return FALSE;
        return @mkdir($dir, $mode);
    }
}

/**
 * 自定义正则匹配
 * @param  string $str  需要匹配的内容
 * @param  string $type 需匹配的类型
 */
if(!function_exists('reg_match')){
    function reg_match($str, $type){

        switch ($type) {

            case 'm':   //匹配手机
                $reg = '/^1[345789]\d{9}$/';
                break;
            case 't':   //匹配座机
                $reg = '\d{3}-\d{8}|\d{4}-\d{7}';
                break;
            case 'sf':   //匹配身份证
                $reg = '/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/';
                break;
            case 'e':   //匹配邮箱
                $reg = '/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i';
                break;
            case 'i':   //匹配IP
                $reg = '/^(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?/i';
                break;
            case 'jg':   //军官证
                $reg = '/^[0-9]{8}$/';
                break;
            case 'hz':   //护照
                $reg = '/^[a-zA-Z0-9]{5,17}$/';
                break;
            case 'jz':   //驾照
                $reg = '/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/';
                break;
            case 'tb':   //台胞证
                $reg = '/^([0-9]{8}|[0-9]{10})$/';
                break;
            case 'ga':   //港澳证
                $reg = '/^[a-zA-Z0-9]{6,10}$/';
                break;
            default:
                # code...
                break;
        }
        return preg_match($reg, $str);
    }
}

/**
 * 字符串转换为数组,主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author ijitao
 */
if(!function_exists('str2arr')){
    function str2arr($str, $glue = ','){
        return explode($glue, $str);
    }
}

/**
 * 数组转换为字符串,主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author ijitao
 */
if(!function_exists('arr2str')){
    function arr2str($arr, $glue = ','){
        return implode($glue, $arr);
    }
}

/**
 * 字符串截取,支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
if(!function_exists('msubstr')){
    function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
        if(function_exists("mb_substr")){
            if(mb_strlen($str, $charset) <= $length) return $str;
            $slice = mb_substr($str, $start, $length, $charset);
        }elseif(function_exists('iconv_substr')) {
            if(iconv_substr($str, $charset) <= $length) return $str;
            $slice = iconv_substr($str,$start,$length,$charset);
            if(false === $slice) {
                $slice = '';
            }
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            if(count($match[0]) <= $length) return $str;
            $slice = join("",array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice.'...' : $slice;
    }
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author ijitao
 */
if(!function_exists('time_format')){
    function time_format($time = NULL,$format='Y-m-d H:i'){
        $time = $time === NULL ? NOW_TIME : intval($time);
        return date($format, $time);
    }
}

/**
 * 模拟get进行url请求
 * @param string $url
 */
if(!function_exists('request_get')){
    function request_get($url = '') {
        if (empty($url)) {
            return false;
        }
        $gettUrl = $url;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$gettUrl);
        curl_setopt($ch,CURLOPT_HEADER,0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $data = curl_exec($ch);
        curl_close($ch);
        //返回的是一个数组，里面存放用户的信息
        $data = json_decode($data,true);
        return $data;
    }
}

/**
 * 模拟post进行url请求
 * @param string $url
 * @param string $param
 */
if(!function_exists('request_post')){
    function request_post($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }

        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        $data = json_decode($data,true);
        return $data;
    }
}

/**
 * 自动补0
 */
if(!function_exists('dispRepair')){
    function dispRepair($str,$len,$msg,$type='1') {
        $length = $len - strlen($str);
        if($length<1)return $str;
        if ($type == 1) {
            $str = str_repeat($msg,$length).$str;
        } else {
            $str .= str_repeat($msg,$length);
        }
        return $str;
    }
}

/*隐藏号码*/
if(!function_exists('hidtel')){
    function hidtel($phone){
        $IsWhat = preg_match('/(0[0-9]{2,3}[-]?[2-9][0-9]{6,7}[-]?[0-9]?)/i',$phone); //固定电话
        if($IsWhat == 1){
            return preg_replace('/(0[0-9]{2,3}[-]?[2-9])[0-9]{3,4}([0-9]{3}[-]?[0-9]?)/i','$1****$2',$phone);
        }else{
            return  preg_replace('/(1[3-9]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2',$phone);
        }
    }
}

/*隐藏部分*/
if(!function_exists('hideStar')){
    function hideStar($str) { //用户名、邮箱、手机账号中间字符串以*隐藏
        if (strpos($str, '@')) {
            $email_array = explode("@", $str);
            $prevfix = (strlen($email_array[0]) < 4) ? "" : substr($str, 0, 3); //邮箱前缀
            $count = 0;
            $str = preg_replace('/([\d\w+_-]{0,100})@/', '***@', $str, -1, $count);
            $rs = $prevfix . $str;
        } elseif (strlen($str) == 18){
            $rs = substr($str, 0, 4) . "*************".substr($str, -1);
        }else {
            $pattern = '/(1[34578]{1}[0-9])[0-9]{4}([0-9]{4})/i';
            if (preg_match($pattern, $str)) {
                $rs = preg_replace($pattern, '$1****$2', $str); // substr_replace($name,'****',3,4);
            } else {
                $rs = substr($str, 0, 3) . "***" . substr($str, -1);
            }
        }
        return $rs;
    }
}

/**
 *@todo: 判断是否为post
 */
if(!function_exists('is_post')){
    function is_post()
    {
        return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='POST';
    }
}

/**
 *@todo: 判断是否为get
 */
if(!function_exists('is_get')){
    function is_get()
    {
        return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='GET';
    }
}

/**
 *@todo: 判断是否为ajax
 */
if(!function_exists('is_ajax')){
    function is_ajax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtoupper($_SERVER['HTTP_X_REQUESTED_WITH'])=='XMLHTTPREQUEST';
    }
}

/**
 * 邮件发送
 * @param $to               收件人邮箱
 * @param string $subject   标题
 * @param $name             发送人名称
 * @param string $body      内容
 * @param null $attachment  附件
 * @return bool|string
 * @throws phpmailerException
 */
 function send_mail($email,$template,$data,$attachment = null){
    $interface_config = get_option('interface_config');
    $config = $interface_config['smtp'];
    if(empty($config)) wp_send_json_error(array('info'=>'邮件接口未配置'));

    if(!is_file(LIBRARY_PATH.'Vendor/PHPMailer/class.phpmailer.php')) wp_send_json_error(array('info'=>'找不到邮件接口文件'));
    include_once (LIBRARY_PATH.'Vendor/PHPMailer/class.phpmailer.php');
    include_once (LIBRARY_PATH.'Vendor/SMTP.php');
    /*ini_set("display_errors","On");
    error_reporting(E_ALL);*/

    $mail = new PHPMailer(); //PHPMailer对象

    $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码

    $mail->IsSMTP(); // 设定使用SMTP服务

    $mail->SMTPDebug = 0; // 关闭SMTP调试功能

    // 1 = errors and messages

    // 2 = messages only
    //print_r($config);die;
    $mail->SMTPAuth = true; // 启用 SMTP 验证功能

    $mail->SMTPSecure = 'ssl'; // 使用安全协议

    $mail->Host = $config['host']; // SMTP 服务器

    $mail->Port = $config['port']; // SMTP服务器的端口号

    $mail->Username = $config['user_name']; // SMTP服务器用户名

    $mail->Password = !empty($config['user_warrant']) ? $config['user_warrant'] : $config['user_pass']; // SMTP服务器密码

    $mail->SetFrom($config['from_email'], $config['from_name']);

    $replyEmail = $config['reply_email']?$config['reply_email']:$config['from_email'];

    $replyName = $config['reply_name']?$config['reply_name']:$config['from_name'];

    $mail->AddReplyTo($replyEmail, $replyName);

    $smtp = new \library\Smtp();
    $result = $smtp->get_smtp_template($data,$template);

    $mail->Subject = $result['title'];

    $mail->AltBody = "为了查看该邮件，请切换到支持 HTML 的邮件客户端";

    $mail->MsgHTML($result['html']);    //发送内容

    $mail->AddAddress($email, $config['from_name']);

    if(is_array($attachment)){ // 添加附件

        foreach ($attachment as $file){

            is_file($file) && $mail->AddAttachment($file);

        }

    }

    return $mail->Send() ? true : $mail->ErrorInfo;

}

add_action("user_register", "set_user_admin_bar_false_by_default", 10, 1);
function set_user_admin_bar_false_by_default($user_id) {
    update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
    update_user_meta( $user_id, 'show_admin_bar_admin', 'false');
}

//文章首行缩进
function Bing_text_indent($text){
    $return = str_replace('<p', '<p style="text-indent:2em;"',$text);
    return $return;
}
add_filter('the_content','Bing_text_indent');

//精简头部信息
remove_action( 'wp_head', 'feed_links', 2 ); //移除feed
remove_action( 'wp_head', 'feed_links_extra', 3 ); //移除feed
remove_action( 'wp_head', 'rsd_link' ); //移除离线编辑器开放接口
remove_action( 'wp_head', 'wlwmanifest_link' );  //移除离线编辑器开放接口
remove_action( 'wp_head', 'index_rel_link' );//去除本页唯一链接信息
remove_action('wp_head', 'parent_post_rel_link', 10, 0 );//清除前后文信息
remove_action('wp_head', 'start_post_rel_link', 10, 0 );//清除前后文信息
remove_action( 'wp_head', 'rel_canonical' );

if(!is_admin()){
    show_admin_bar(false);
}


//引入url重写规则
//require_once(ABSPATH.'wp-includes/library/RewriteRule.class.php');

//引入自动加载
require_once(ABSPATH.'wp-includes/library/Autoloader.class.php');

//引入定时器
require_once(ABSPATH.'wp-includes/library/Timer.class.php');
$timer = new \library\Timer();
$timer->autoTimer();