<?php
/**
 * 学生-往期/近期比赛
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
use library\WeChatShare;
class Student_Signs
{

    public $appid = 'wxb575928422b38270';
    public $appsecret = '1f55ec97e01f249b4ac57b7c99777173';

    public function __construct($action)
    {
        if(isset($_GET['match_id'])){

            //确认比赛信息
            global $wpdb;
            $sql = "select match_status from {$wpdb->prefix}match_meta_new where match_id = {$_GET['match_id']} ";
            $match_status = $wpdb->get_var($sql);
            if(empty($match_status) || $match_status == -3){
                echo '<h3>签到结束</h3>';
                die;
            }
        }

        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false && ACTION == 'index'){

            $this->index();
            die;
        }else{
            $action = $action != 'success' ? 'signs' : $action;
            add_action('wp_enqueue_scripts', array($this,'scripts_default'),10,7);
            add_shortcode('student-signs',array($this,$action));
        }
    }

    /**
    签到操作
     */
    public function index(){

        if(isset($_SESSION['user_openid'])) unset($_SESSION['user_openid']);

        $this->getWebCode();
    }


    /**
     * 签到成功页面
     */
    public function success(){

        if(is_user_logged_in()){
            global $wpdb,$current_user;

            //获取比赛信息
            $row = $wpdb->get_row("select * from {$wpdb->prefix}match_meta_new where match_id = {$_GET['id']} and match_status = -2",ARRAY_A);
            if(empty($row)){
                $this->get_404(array('message'=>'签到结束','match_url'=>home_url('matchs/info/match_id/'.$_GET['id'])));
                return;
            }

            $rows = $wpdb->get_results("SELECT * FROM {$wpdb->usermeta} WHERE user_id = {$current_user->ID} and meta_key in('user_nationality','user_nationality_pic','user_nationality_short','user_birthday','nickname','user_head','user_address','user_real_name','real_ID','user_ID_Card','user_ID','user_gender') ",ARRAY_A);
            $user_info = array_column($rows,'meta_value','meta_key');
            $user_address = isset($user_info['user_address']) ? unserialize($user_info['user_address']) : '';
            $user_real_name = isset($user_info['user_real_name']) ? unserialize($user_info['user_real_name']) : '';

            $real_ID = isset($user_real_name['real_ID']) ? hideStar($user_real_name['real_ID']) : '';
            $real_name = isset($user_real_name['real_name']) ? $user_real_name['real_name'] : '';
            $user_gender = $user_info['user_gender'];
            $user_birthday = !empty($user_info['user_birthday']) ? $user_info['user_birthday'] : substr($user_real_name['real_ID'],6,8);
            $age_type = getAgeGroupNameByAge($user_real_name['real_age']);
            //var_dump($user_info);
            $match = get_post($_GET['id']);

            $match_title = $match->post_title;

            //获取所有报名信息
            $sql = "select user_id from {$wpdb->prefix}order where match_id = {$_GET['id']} and pay_status in(2,3,4) order by id asc";
            $results = $wpdb->get_results($sql,ARRAY_A);
            $index = array_search($current_user->ID,array_column($results,'user_id')) + 1;
            //print_r($index);
            //print_r(array_column($results,'user_id'));
        }
        $data = array(
                'index'=>$index,
                'match_title'=>$match_title,
                'match_content'=>$match->post_content,
                'real_ID'=>$real_ID,
                'real_name'=>$real_name,
                'user_gender'=>$user_gender,
                'user_birthday'=>$user_birthday,
                'user_nationality'=>$user_info['user_nationality'],
                'user_nationality_pic'=>$user_info['user_nationality_pic'],
                'user_nationality_short'=>$user_info['user_nationality_short'],
                'age_type'=>$age_type,
                'address'=>$user_address['province'].$user_address['city'].$user_address['area']
            );
        $view = student_view_path.CONTROLLER.'/success.php';
        load_view_template($view,$data);
    }


    /*二维码页面展示*/
    public function signs(){ ?>

        <script type="text/javascript">
            jQuery(function($){
                $.alerts("<?=__('请使用微信扫码进行签到', 'nlyd-student')?>");
            })
            //alert('请使用微信扫码进行签到');
        </script>
        <?php
        /*global $wpdb;
        //获取所有报名信息
        $sql = "select user_id from {$wpdb->prefix}order where match_id = {$_GET['match_id']} and pay_status in(2,3,4) order by id asc";
        $results = $wpdb->get_results($sql,ARRAY_A);
        $index = array_search(3,array_column($results,'user_id')) + 1;

        $b = $wpdb->insert(
            $wpdb->prefix.'match_sign',
            array(
                'user_id'=>3,
                'match_id'=>$_GET['match_id'],
                'seat_number'=>$index,
                'created_time' => get_time('mysql')
            )
        );
        var_dump($b);*/
        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view);
    }

    public function get_404($tag){
        $view = leo_student_public_view.'my-404.php';
        if(!is_array($tag)){
            $data['message'] = $tag;
        }else{
            $data = $tag;
        }
        load_view_template($view,$data);
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

            //var_dump($url);die;
//            wp_redirect($url);
            Header('Location:'.$url);
            exit;
        }else{
            return $this->getWebAccessToken();
        }
        exit;
    }

    /**
     * 微信网页授权获取access_token
     */
    public function getWebAccessToken()
    {
        $code = $_GET['code'];

        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
        $url .= 'appid='.$this->appid;
        $url .= '&secret='.$this->appsecret;
        $url .= '&code='.$code;
        $url .= '&grant_type=authorization_code';
        $data = request_get($url);

        if (isset($data['errcode'])) {

            echo '<h1>错误：</h1>'.$data['errcode'];
            echo '<br/><h2>错误信息：</h2>'.$data['errmsg'];
            die;
        }

        $access_token = $data['access_token'];
        $openid = $data['openid'];

        $_SESSION['user_openid'] == $openid;
        //是否存在用户
        global $wpdb;
        $users = $wpdb->get_row('SELECT ID,user_mobile,user_email FROM '.$wpdb->users.' WHERE weChat_openid="'.$data['openid'].'" AND weChat_openid != ""');
        $users_id = $users->ID == true ? $users->ID : 0;

        if(empty($users) || (!$users->user_mobile && !$users->user_email)){
            $users_id = $users->ID == true ? $users->ID : 0;
            $url = home_url('logins/bindPhone/uid/'.$users_id.'/access/'.$access_token.'/oid/'.$openid.'/loginType/sign/id/'.$_GET['match_id']);
            //var_dump($url);die;
            ?>
            <script type="text/javascript">
                alert("<?=__('你是新用户或未进行手机绑定\\n即将跳转到绑定页', 'nlyd-student')?>");
                setTimeout(function(){
                    window.location.href='<?=$url?>';
                    return false;
                },50)
            </script>

            <?php
            exit;
        }

        update_user_meta($users->ID,'user_session_id',session_id());
        wp_set_current_user($users->ID);
        wp_set_auth_cookie($users->ID);

        //获取报名列表
        $sql = "select id from {$wpdb->prefix}order where match_id = {$_GET['match_id']} and user_id = $users->ID and pay_status in (2,3,4) order by id desc ";
        $order_id = $wpdb->get_var($sql);
        if(empty($order_id)){ ?>
            <script type="text/javascript">
                //$.alerts('即将跳转到实名认证页');
                alert('<?=__('未检测到报名信息\\n请联系管理员核实', 'nlyd-student')?>');
                setTimeout(function(){
                    window.location.href='<?=home_url("matchs/info/match_id/".$_GET['match_id'])?>';
                    return false;
                },50)
            </script>

            <?php
            exit;
        }else{

            //获取所有报名信息
            $sql = "select user_id from {$wpdb->prefix}order where match_id = {$_GET['match_id']} and pay_status in(2,3,4) order by id asc";
            $results = $wpdb->get_results($sql,ARRAY_A);
            $index = array_search($users->ID,array_column($results,'user_id')) + 1;

            //判断是否实名认证
            $user_real_name = get_user_meta($users_id,'user_real_name');
            if(empty($user_real_name) || empty($user_real_name['real_name'])){ ?>
                <script type="text/javascript">
                    //$.alerts('即将跳转到实名认证页');
                    window.location.href='<?=home_url('/account/info/id/'.$_GET['match_id'])?>';
                </script>
            <?php
                die;
            }

            $b = $wpdb->insert(
                $wpdb->prefix.'match_sign',
                array(
                    'user_id'=>$users->ID,
                    'match_id'=>$_GET['match_id'],
                    'seat_number'=>$index,
                    'created_time' => get_time('mysql')
                )
            );
            if($b){
                ?>

                <script type="text/javascript">
                    //$.alerts('即将跳转到实名认证页');
                    window.location.href='<?=home_url('signs/success/id/'.$_GET['match_id'])?>';
                    /*alert('=__('签到成功', 'nlyd-student')');
                    setTimeout(function(){
                        window.location.href='home_url('signs/success/id/'.$_GET['match_id'])';
                        return false;
                    },500)*/
                </script>
                <?php
                exit;
            }else{ ?>
                <script type="text/javascript">
                    //$.alerts('即将跳转到实名认证页');
                    alert('<?=__('签到失败,比赛订单创建失败\\n请联系管理员', 'nlyd-student')?>');
                </script>
                <?php exit;
            }
        }
    }

    /**
     * 网页授权登录根据access_token和openid获取用户信息
     */
    public function getUserInfo($access_token,$openid,$type = true,$user_id = 0,$mobile='',$emailOrMobile = '',$bindType = ''){
        $get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

        $res = request_get($get_user_info_url);

        var_dump($res);die;
        //返回的是一个数组，里面存放用户的信息

        if (isset($res['errcode'])) {
            if(is_ajax()){
                wp_send_json_error(['info' => '获取微用户信息失败,请退出后重试']);
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
     * 微信定位
     */
    public function weChatLocation(){

        $jssdk = new WeChatShare();
        $signPackage = $jssdk->GetSignPackage();

        $view = student_view_path.CONTROLLER.'/location.php';
        load_view_template($view,array('jdk',$signPackage));
    }


    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        if(ACTION == 'success'){
            wp_register_style( 'my-signs-success', student_css_url.'signs/success.css',array('my-student') );
            wp_enqueue_style( 'my-signs-success' );
        }
        if(ACTION == 'weChatLocation'){

            wp_register_script( 'weChat-js-sdk', 'http://res.wx.qq.com/open/js/jweixin-1.0.0.js',array('jquery'),leo_student_version);
            wp_enqueue_script( 'weChat-js-sdk' );
        }

    }
}