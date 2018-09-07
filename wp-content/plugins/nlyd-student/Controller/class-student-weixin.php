<?php
class Student_Weixin
{
    public $appid = 'wxb70e5bd97a67bb51';
    public $appsecret = '9c846264ee2169c34882e71ccc0f5935';
    public function __construct($action)
    {
        if($action) $this->$action();
        exit;
        //添加短标签
//        add_shortcode('student-weixin',array($this,$action));
    }

    public function verification(){


        die;
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
            if($this->getWebCode(true)){
                wp_redirect(home_url('account'));
            }
        }
    }

    /**\
     * 微信网页授权获取code
     */
    public function getWebCode(){

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
            header('Location:'.$url);
            exit;
        }else{
            return $this->getWebAccessToken(true);
        }
    }

    /**
     * 微信网页授权获取access_token
     */
    function getWebAccessToken($type)
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
            echo '<h1>错误：</h1>'.$data['errcode'];
            echo '<br/><h2>错误信息：</h2>'.$data['errmsg'];
            exit;
        }


        $access_token = $data['access_token'];
        $openid = $data['openid'];
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
        if (isset($res->errcode)) {
            echo '<h1>错误：</h1>'.$res->errcode;
            echo '<br/><h2>错误信息：</h2>'.$res->errmsg;
            exit;
        }
        //打印用户信息
//        session('lfs_WeChat',$res);
        // var_dump($res);die;


        if (!$this->indexs($res,$type)) {
            echo '<h1>错误：</h1>存入用户信息失败';
//            echo '<br/><h2>错误信息：</h2>'.$res->errmsg;
            exit;
        }
        return true;
        //return $res;
        /*echo '<pre>';
        print_r($res);
        echo '</pre>';*/

    }

    /**
     * 微信授权登录页
    */
    public function indexs($res = [],$type=''){
        $openid = $res['openid'];
        if($res == [] && !is_post()) return false;

        /*判断是否为平台用户*/
        global $wpdb;
        $users = $wpdb->get_row('SELECT ID,user_mobile FROM '.$wpdb->users.' WHERE weChat_openid="'.$res['openid'].'" AND weChat_openid != ""');
        if(empty($users) || !$users->user_mobile){
            $users_id = $users->user_mobile == true ? $users->ID : 0;
            //TODO 绑定手机
            $auth = array(
                'user_nicename'        => $res['nickname'],
                'weChat_openid'        => $res['openid'],
                'weChat_union_id'        => $res['unionid'],
                'user_registered'        => get_time('mysql'),
            );
            //TODO 显示绑定手机页面

            exit;
        }else{
            $user_id = $users->ID;
        }

        if(is_post()){
            $user_id = $_POST['user_id'];
            if($_POST['user_id'] < 1){
                /*保存用户信息*/
                $wpdb->startTrans();
                $user_id = wp_create_user($_POST['weChat_openid'],$_POST['weChat_openid'].'_'.get_time());
                $bool = $wpdb->update($wpdb->users,$_POST['auth'],['ID' => $user_id]);
                if(!$bool) {
                    $wpdb->rollback();
                    echo '<h1>错误：</h1>存入用户信息失败';
                    exit;
                }
                if($this->insertUsermeta($_POST['res'],$user_id)){
                    $wpdb->commit();
                }else{
                    $wpdb->rollback();
                    echo '<h1>错误：</h1>存入用户信息失败';
                    exit;
                }
            }else{
                $bool = $wpdb->update($wpdb->users,['user_mobile' => $_POST['mobile']],['ID' => $user_id]);
                if(!get_user_meta($user_id)) $this->insertUsermeta($_POST['res'],$user_id);
                if(!$bool) {
                    echo '<h1>错误：</h1>存入用户信息失败';
                    exit;
                }
            }

        }



        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        return true;
    }

    /**
     * usermeta数据
     */
    public function insertUsermeta($res,$user_id){
        global $wpdb;
        $user_address = ['country' => $res['country'], 'province' => $res['province'], 'city' => $res['city']];
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
        $sql = 'INSERT INTO '.$wpdb->usermeta.' (user_id,meta_key,meta_value) VALUES
            ('.$user_id.',"user_gender", "'.$sex.'"),
            ('.$user_id.',"user_address",\''.serialize($user_address).'\'),
            ('.$user_id.',"nickname","'.$res['nickname'].'"),
            ('.$user_id.',"user_head","'.$res['headimgurl'].'")';
        echo $sql;
        return $wpdb->query($sql);
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