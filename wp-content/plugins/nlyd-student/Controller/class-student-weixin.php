<?php
class Student_Weixin
{

//    public $appid = 'wxb70e5bd97a67bb51';
//    public $appsecret = '9c846264ee2169c34882e71ccc0f5935';
    //脑力运动测试号
    public $appid = 'wxb575928422b38270';
    public $appsecret = '1f55ec97e01f249b4ac57b7c99777173';

    public function __construct($action = '')
    {

        if($action != 'jsPayGetOpenId'){
            if($action) $this->$action();
            if($action != ''){
                exit;
            }else{
                return;
            }
        }
        //添加短标签
//        add_shortcode('student-weixin',array($this,$action));
    }

    public function verification(){
//        $echoStr = $_GET["echostr"];
//        echo $echoStr;
//
//        die;
    }


    /**
     * 微信网页授权登录
     */
    public function webLogin(){


//            wp_logout();

//        $this->getWebCode(true);
        if(is_user_logged_in()){
            global $current_user;
            wp_redirect(home_url('account'));
        }else{
            $this->getWebCode();
        }
    }

    /**\
     * 微信网页授权获取code
     */
    public function getWebCode($is_pay = false){
        if(empty($_GET['code'])){
            $redirect_url = home_url().$_SERVER["REQUEST_URI"];
            //$redirect_uri = 'http://.miss4ever.com'.$_SERVER['REQUEST_URI'];
            //$response_type = 'wixin_return_code';
            //$scope = 'snsapi_userinfo';
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
            $url .= 'appid='.$this->appid;
            $url .= '&redirect_uri='.$redirect_url;
            $url .= '&response_type=code';
            //这是微信公众号内获取
            $url .= '&scope=snsapi_userinfo';
            //这是微信开放平台获取
//            $url .= '&scope=snsapi_login';
            $url .= '&#wechat_redirect';
//            var_dump($url);return;

//            var_dump($url);
//            wp_redirect($url);
            Header('Location:'.$url);
            exit;
        }else{
            return $this->getWebAccessToken($is_pay);
        }
        exit;
    }

    /**
     * 微信网页授权获取access_token
     */
    function getWebAccessToken($is_pay = false)
    {
        $code = $_GET['code'];
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
        $url .= 'appid='.$this->appid;
        $url .= '&secret='.$this->appsecret;
        $url .= '&code='.$code;
        $url .= '&grant_type=authorization_code';

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $data = curl_exec($ch);
        $data = json_decode($data,true);
        curl_close($ch);
        if (isset($data['errcode'])) {
            if(is_ajax()){
                wp_send_json_error(['info' => __('获取微用户授权失败.请退出后重试', 'nlyd-student')]);
            }else{
                if($is_pay == true){
                    return false;
                }else{
                    echo '<h1>错误：</h1>'.$data['errcode'];
                    echo '<br/><h2>错误信息：</h2>'.$data['errmsg'];
                    exit;
                }
            }
        }

        $access_token = $data['access_token'];
        $openid = $data['openid'];
        if($is_pay == true){
            return $openid;
        }

        //是否存在用户
        global $wpdb;
        $users = $wpdb->get_row('SELECT ID,user_mobile,user_email FROM '.$wpdb->users.' WHERE weChat_openid="'.$data['openid'].'" AND weChat_openid != ""');
        if(empty($users) || (!$users->user_mobile && !$users->user_email)){
            //TODO 显示绑定手机页面
            $users_id = $users->ID == true ? $users->ID : 0;
            wp_redirect(home_url('logins/bindPhone/uid/'.$users_id.'/access/'.$access_token.'/oid/'.$openid));
            exit;
        }
        $this->getUserInfo($access_token,$openid, false,$users->ID);
        exit;
    }

    /**
     * 网页授权登录根据access_token和openid获取用户信息
     */
    public function getUserInfo($access_token,$openid,$type = true,$user_id = 0,$mobile='',$emailOrMobile = '',$bindType = ''){
        $get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$get_user_info_url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        //返回的是一个数组，里面存放用户的信息
        $res = json_decode($res,true);
        if (isset($res['errcode'])) {
            if(is_ajax()){
                wp_send_json_error(['info' => __('获取微用户信息失败,请退出后重试', 'nlyd-student')]);
            }else{
                echo '<h1>错误：</h1>'.$res['errcode'];
                echo '<br/><h2>错误信息：</h2>'.$res['errmsg'];
                exit;
            }
        }
        //保存用户信息
        $res['mobile'] = $mobile;
        return $this->save_user($res,$type,$user_id,$emailOrMobile,$bindType);
        exit;
    }

    /**
     * 微信授权登录页,保存用户信息
     */
    public function save_user($res = [],$type='',$user_id=0,$emailOrMobile='',$bindType=''){
        $me_name = $emailOrMobile == 'mobile' ? __('手机', 'nlyd-student') : __('邮箱', 'nlyd-student');
        global $wpdb;
        $userMetaType = false;
        $wpdb->query('START TRANSACTION');
        if($bindType == 'username'){
            //使用账号绑定微信号
            $auth = array(
                'weChat_openid'        => $res['openid'],
                'weChat_union_id'        => $res['unionid'],
            );
            $bool = $wpdb->update($wpdb->users,$auth,['ID' => $user_id]);
            if(!$bool) return false;
        }else{
            if($type == true){
                //绑定手机后执行
                if($user_id < 1){
                    /*保存用户信息*/
                    //当前手机是否已注册
                    $mobileUser = $wpdb->get_row('SELECT ID,weChat_openid,user_mobile FROM '.$wpdb->users.' WHERE user_mobile="'.$res['mobile'].'" OR user_login="'.$res['mobile'].'" OR user_email="'.$res['mobile'].'"');
                    //TODO 判断当前手机是否已经绑定过微信
                    if($mobileUser->weChat_openid != false){
                        if(is_ajax()){
                            wp_send_json_error(array('info'=>sprintf(__('当前%s已绑定其它微信', 'nlyd-student'), $me_name)));
                            exit;
                        }else{
                            return false;
                        }
                    }

                    $auth = array(
                        'weChat_openid'        => $res['openid'],
                        'weChat_union_id'        => $res['unionid'],
                    );

                    if(!$mobileUser){
                        $userMetaType = true;
                        $auth['user_nicename'] = $res['nickname'];
//                    $auth['display_name'] = $res['mobile'];
                        if($emailOrMobile == 'email'){
                            $user_id = wp_create_user($res['mobile'],123456,$res['mobile']);
                        }else{
                            $user_id = wp_create_user($res['mobile'],123456,'');
                            $wpdb->update($wpdb->users,['user_mobile'=>$res['mobile']],['ID' => $user_id]);
                        }

                    }else{
                        //已存在
                        if(!$mobileUser->user_mobile) $auth['user_'.$emailOrMobile] = $res['mobile'];
                        $user_id = $mobileUser->ID;
                    }
                    $bool = $wpdb->update($wpdb->users,$auth,['ID' => $user_id]);
                    if(!$bool) {
                        $wpdb->query('ROLLBACK');
                        return false;
                    }

                }else{
                    //已存在的微信用户绑定手机
                    $bool = $wpdb->update($wpdb->users,['user_'.$emailOrMobile => $res['mobile']],['ID' => $user_id]);
                    if(!$bool) return false;
                }
            }
        }
        //添加推广人
        if(isset($_SESSION['referee_id_wx']) && !(get_user_by('ID',$user_id)->referee_id) && $user_id != $_SESSION['referee_id_wx'] && get_user_by('ID',$_SESSION['referee_id_wx'])->referee_id != $user_id){
            //获取我的推广人上级
            $referee_id = $wpdb->get_var("select referee_id from {$wpdb->prefix}users where ID = {$_SESSION['referee_id_wx']}");
            if($referee_id != $user_id){
                $bool = $wpdb->update($wpdb->prefix.'users',array('referee_id'=>$_SESSION['referee_id_wx'],'referee_time'=>date_i18n('Y-m-d',get_time())),array('ID'=>$user_id));
            }

            if($bool){
                $wpdb->query('COMMIT');
            }else{
                $wpdb->query('ROLLBACK');
                return false;
            }
        }else{
            $wpdb->query('COMMIT');
        }

        $this->insertUsermeta($res,$user_id,$userMetaType);

        update_user_meta($user_id,'user_session_id',session_id());
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        $_SESSION['login_time'] = get_time()+15;

        if(get_user_meta($user_id, 'locale', true)) setcookie('user_language', get_user_meta($user_id, 'locale', true), time()+3600*24,'/');
        if($type == false) {

            if($_SESSION['user_openid']) wp_redirect(home_url('account/certification/type/sign'));

            if(isset($_SESSION['redirect_url'])){
                $url = $_SESSION['redirect_url'];
                unset($_SESSION['redirect_url']);
            }
            else{
                $url = home_url('account');
            }
            if(isset($_SESSION['referee_id_wx']) && $bool){
                $url = home_url('/zone/indexUser/');
                unset($_SESSION['referee_id_wx']);
            }

            wp_redirect($url);//跳转到用户中心
            exit;
        }
        return true;
    }


    /**
     * usermeta数据
     */
    public function insertUsermeta($res,$user_id,$type = false){
        global $wpdb;
        switch ($res['sex']){
            case 1:
                $sex = '男';
                break;
            case 2:
                $sex = '女';
                break;
            default:
                $sex = '未知';
        }
//        $user_address = ['country' => $res['country'], 'province' => $res['province'], 'city' => $res['city']];
        if($type){
//            update_user_meta($user_id, 'user_gender', $sex);
//            update_user_meta($user_id, 'user_address', $user_address);
            update_user_meta($user_id, 'nickname', $res['nickname']);
            update_user_meta($user_id, 'wechat_nickname', $res['nickname']);
            update_user_meta($user_id, 'user_head', $res['headimgurl']);
            update_user_meta($user_id, 'user_ID', 10000000+$user_id);
        }
//        if($type == true){
//            //更新.
//            $result = true;
//            update_user_meta($user_id, 'user_gender', $sex);
//            update_user_meta($user_id, 'user_address', $user_address);
//            update_user_meta($user_id, 'nickname', $res['nickname']);
//            update_user_meta($user_id, 'user_head', $res['headimgurl']);
//        }else{
//            $sql = 'INSERT INTO '.$wpdb->usermeta.' (user_id,meta_key,meta_value) VALUES
//            ('.$user_id.',"user_gender", "'.$sex.'"),
//            ('.$user_id.',"user_address",\''.serialize($user_address).'\'),
//            ('.$user_id.',"nickname","'.$res['nickname'].'"),
//            ('.$user_id.',"user_head","'.$res['headimgurl'].'")';
//            $result = $wpdb->query($sql);
//        }
        return true;

    }


    /**
     * 获取access_token
     */
    public function getAccessToken() {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
//        $data = json_decode(file_get_contents("access_token.json"));
        $file_path = wp_get_upload_dir()['basedir'].'/access_token.json';

        if(file_exists($file_path)){
            $data = json_decode(file_get_contents($file_path));
            // var_dump($data);
        }else{
            file_put_contents($file_path, '');
            $data = object;
        }
        if ($data->expire_time < time()) {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->appsecret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $fp = fopen($file_path, "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;

    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }
}