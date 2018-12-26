<?php
/**
 * 检查目录是否存在,不存在增加
 */
if(!function_exists('reg_match')){
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
        }elseif (strlen($str) == 18){
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

if(!function_exists('get_time')){
    function get_time($type='timestamp'){

        if($type == 'mysql'){
            return current_time('mysql');
        }else{
            return strtotime(current_time('mysql'));
        }
    }
}

if(!function_exists('get_match_end_time')){
    function get_match_end_time($match_id){
        global $wpdb;

        //获取比赛信息
        $sql = "select match_project_id from {$wpdb->prefix}match_meta_new where match_id = {$match_id}";

        $match_project_id = $wpdb->get_var($sql);
        if(empty($match_project_id)){
            return -1; //error 比赛信息错误
        }
        $sql = "SELECT p.post_title,pm.meta_value as project_alias,p.ID AS match_project_id,p.post_parent FROM {$wpdb->posts} AS p 
            LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID=pm.post_id AND pm.meta_key='project_alias' 
            WHERE p.ID IN ({$match_project_id})";
        $projectArr = $wpdb->get_results($sql, ARRAY_A);

        if(empty($projectArr)){
            return -2; //error 比赛项目未绑定
        }

        foreach ($projectArr as &$v){
            $more_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}match_project_more WHERE match_id='{$match_id}' AND project_id='{$v['match_project_id']}' AND status IN(1,2)");
            if($more_id) $v['is_end'] = false;
            else $v['is_end'] = true;
        }

        return $projectArr;
    }
}

if(!function_exists('getAgeGroupNameByAge')){
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



if(!function_exists('saveIosFile')){
    function saveIosFile($filecontent,$upload_dir){

        if(empty($filecontent)) return false;
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
}


/**
 * 获取类别
 */
if(!function_exists('getCategory')){
    function getCategory($type=0){
        switch ($type){
            case 1://考级自测类别
                $parentAlis = 'grading';
                break;
            default://比赛类别
                $parentAlis = 'mental_world_cup';
        }

        global $wpdb;
        $sql = "select p.ID,p.post_title,pm.meta_value as alis from {$wpdb->prefix}posts as p 
                left join {$wpdb->postmeta} as pm on pm.post_id=p.ID and pm.meta_key='project_alias' 
                left join {$wpdb->posts} AS pp on pp.ID=p.post_parent 
                left join {$wpdb->postmeta} as ppm on ppm.post_id=pp.ID and ppm.meta_key='project_alias'  
                where p.post_type = 'match-category' and p.post_status = 'publish' and ppm.meta_value='{$parentAlis}' order by p.menu_order asc";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(!$rows) $rows = [];
        return $rows;
    }
}

/**
 * 获取收益大类
 */
if(!function_exists('getSpreadCategory')){
    function getSpreadCategory($key=''){

        $arr = array(
                'official-match' => '正式比赛',
                'official-grading' => '正式考级',
                'tram-match' => '战队精英赛',
                'city-match' => '城市精英赛',
                'course' => '购买课程',
                'course_grading' => '课程2级达标',
                'course_recommend' => '课程推荐满3人',
                'stock' => '股权出售',
                'profit_bonus' => '分中心收益分红',
                'apply_center' => '成为分中心',
                'apply_match' => '成为赛区',
                'apply_zone' => '成为机构',
            );
        if(!empty($key)){
            return $arr[$key];
        }else{
            return $arr;
        }

    }
}

/**
 * 添加订单时插入收益数据
 */
if(!function_exists('insertIncomeLogs')){
    function insertIncomeLogs($order){
        global $wpdb;

        $id = $wpdb->get_var("select id from {$wpdb->prefix}user_income_logs where user_id = {$order['user_id']} and match_id = {$order['match_id']}");
        if(!empty($id)) return true;
        //准备数据
        //获取直接/间接收益人
        $sql = "select a.ID user_id,a.referee_id,b.referee_id as indirect_referee_id from {$wpdb->prefix}users a left join {$wpdb->prefix}users b on a.referee_id = b.ID where a.ID = {$order['user_id']}";
        $user = $wpdb->get_row($sql,ARRAY_A);
        //print_r($user);
        //获取比赛/考级相关信息
        if($order['order_type'] == 1){
            $income_type = 'match';
            //准备对应的数据
            $money1 = 0;     //比赛直接推广人
            $money2 = 0;    //比赛间接推广人
            $money3 = 0;   //参赛机构
            $money4 = 0;   //办赛机构
            $money_set = getSpreadOption($income_type);

            if($money_set){
                $money1 = $money_set['direct_superior'];
                $money2 = $money_set['indirect_superior'];
                $money3 = $money_set['mechanism'];
                $money4 = $money_set['sub_center'];
            }
            $created_id = $wpdb->get_var("select created_id from {$wpdb->prefix}match_meta_new where match_id = {$order['match_id']}");
            //print_r($created_id);
            $insert = array(
                'income_type'=>$income_type,
                'match_id'=>$order['match_id'],
                'user_id'=>$order['user_id'],
                'referee_id'=>$user['referee_id'] > 0 ? $user['referee_id'] : '',  //直接人
                'referee_income'=>$user['referee_id'] > 0 ? $money1 : '',  //直接人收益
                'indirect_referee_id'=>$user['indirect_referee_id'] > 0 ? $user['indirect_referee_id'] : '',    //间接人
                'indirect_referee_income'=>$user['indirect_referee_id'] > 0 ? $money2 : '',  //间接人收益
                'person_liable_id'=>$order['sub_centres_id'] > 0 ? $order['sub_centres_id'] : '',   //参赛机构
                'person_liable_income'=>$order['sub_centres_id'] > 0 ? $money3 : '',  //参赛机构收益
                'sponsor_id'=>$created_id > 0 ? $created_id : '',  //办赛机构
                'sponsor_income'=>$created_id > 0 ? $money4 : '',  //办赛机构收益
            );
        }elseif ($order['order_type'] == 2){
            $income_type = 'grading';
            //准备对应的数据
            $money1 = 0;     //比赛直接推广人
            $money2 = 0;    //比赛间接推广人
            $money3 = 0;   //参赛机构
            $money4 = 0;   //办赛机构
            $money_set = getSpreadOption($income_type);
            if($money_set){
                $money1 = $money_set['direct_superior'];
                $money2 = $money_set['indirect_superior'];
                $money3 = $money_set['mechanism'];
                $money4 = $money_set['sub_center'];
            }
            $grading = $wpdb->get_row("select person_liable,created_person from {$wpdb->prefix}grading_meta where grading_id = {$order['match_id']}",ARRAY_A);

            $insert = array(
                'income_type'=>$income_type,
                'match_id'=>$order['match_id'],
                'user_id'=>$order['user_id'],
                'referee_id'=>$user['referee_id'] > 0 ? $user['referee_id'] : '',  //直接人
                'referee_income'=>$user['referee_id'] > 0 ? $money1 : '',  //直接人收益
                'indirect_referee_id'=>$user['indirect_referee_id'] > 0 ? $user['indirect_referee_id'] : '',    //间接人
                'indirect_referee_income'=>$user['indirect_referee_id'] > 0 ? $money2 : '',  //间接人收益
                'person_liable_id'=>$grading['person_liable'] > 0 ? $grading['person_liable'] : '',   //责任教练
                'person_liable_income'=>$grading['person_liable'] > 0 ? $money3 : '',  //参赛机构收益
                'sponsor_id'=>$grading['created_person'] > 0 ? $grading['created_person'] : '',  //办赛机构
                'sponsor_income'=>$grading['created_person'] > 0 ? $money4 : '',  //办赛机构收益
            );
        }
        $insert['created_time'] = get_time('mysql');
        //print_r($id);
        if(empty($id)){
            $res = $wpdb->insert($wpdb->prefix.'user_income_logs',$insert);
            if($res){
                $stream_id = $wpdb->get_var("select id from {$wpdb->prefix}ser_stream_logs where user_id = {$insert['sponsor_id']} and match_id = {$order['match_id']}");
                if(!empty($stream_id)){
                    $bool = $wpdb->insert($wpdb->prefix.'ser_stream_logs',array('user_id'=>$insert['sponsor_id'],'income_type'=>$income_type,'match_id'=>$order['match_id'],'created_time'=>get_time('mysql')));
                    return $bool;
                }else{
                    return true;
                }
            }else{
                return false;
            }
        }
    }
}

/**
 * 获取收益设置
 */
if(!function_exists('getSpreadOption')){
    function getSpreadOption($type){
        global $wpdb;
        $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}spread_set WHERE spread_status=1 AND spread_type='{$type}'", ARRAY_A);
        return $row;
    }
}
