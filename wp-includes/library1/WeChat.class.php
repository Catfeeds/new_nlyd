<?php
/**
 *
 */
class WeChat
{

    protected $appid = 'wx0bd09c8eb544aa6e';
    protected $appsecret = 'b120c7e485a08ac4fec5fd6b0eebaa12';
    /**
     * 获取code
     */
    public function actionGetCode($type=true)
    {
        if(empty($_GET['code'])){
            $redirect_uri = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
            //$redirect_uri = 'http://.miss4ever.com'.$_SERVER['REQUEST_URI'];
            //$response_type = 'wixin_return_code';
            //$scope = 'snsapi_userinfo';
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
            $url .= 'appid='.$this->appid;
            $url .= '&redirect_uri='.$redirect_uri;
            $url .= '&response_type=code';
            //这是微信公众号内获取
//            $url .= '&scope=snsapi_userinfo';
            //这是微信开放平台获取
            $url .= '&scope=snsapi_login';
            $url .= '&#wechat_redirect';
            var_dump($url);return;
            header('Location:'.$url);
            exit;
        }else{
            return $this->actionGettoken($type);

        }
    }
    /**
     * 获取access_token
     */
    function actionGettoken($type)
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

        if (isset($data->errcode)) {
            echo '<h1>错误：</h1>'.$data->errcode;
            echo '<br/><h2>错误信息：</h2>'.$data->errmsg;
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
        session('lfs_WeChat',$res);
       // var_dump($res);die;
        if($type){
            $this->indexs($res,$type);
        }
        //return $res;
        /*echo '<pre>';
        print_r($res);
        echo '</pre>';*/

    }
    /*微信授权登录页*/
    public function indexs($res,$type=''){


        $openid = $res['openid'];
        /*判断是否为平台用户*/
        $row = D('User')
                ->where("openid = '{$openid}' and openid !=''")
                ->find();
        if(empty($row)){

            if($type!=='no'){ 
                $url = U("Home/WeChat/index");
                header('Location:'.$url);
                exit;
            }
           
        }else{

            /*保存用户信息*/
            $auth = array(
                'uid'             => $row['id'],
                'nickname'        => get_user_nickname($row['id']),
                'realname'        => $row['realname'],
                'username'        => $row['username'],
                'useremail'       => $row['email'],
                'userphone'       => $row['mobile'],
                'headimgurl'       => $row['headimgurl'],
                //'token'           => $row[4],
                //'last_login_time' => $row['last_login_time'],
            );

            session('user_auth', $auth);
            session('user_auth_sign', data_auth_sign($auth));

            //if(defined('UID')) return true;
            define('UID',is_login());
            //var_dump(UID);die;
            return true;
        }
    }
    /**
     * 微信下载
     * @param $media_id 文件id
     */
    public function downloadWeixinFile($media_id){

        $WeChatShare = new WeChatShare();
        $access_token = $WeChatShare->getAccessToken();
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$media_id}"; //return $url;
        $filebody = file_get_contents($url);//通过接口获取图片流
        //return $filebody;
        /*$ch = curl_init($url);
        curl_setopt($ch,CURLOPT_HEADER, 0);
        curl_setopt($ch,CURLOPT_NOBODY, 0 );
        curl_setopt($ch,CURLOPT_SLL_VERIFYPEER, FALSE );
        curl_setopt($ch,CURLOPT_SLL_VERIFYHOST, FALSE );
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
        curl_close($ch);
        //return $package;die;
        $imageAll = array_merge(array('header'=>$httpinfo),array('body'=>$package));*/
        return $filebody;
    }

}
//return new WX_Oauth();