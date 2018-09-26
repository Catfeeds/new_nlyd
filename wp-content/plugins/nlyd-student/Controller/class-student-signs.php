<?php
/**
 * 学生-往期/近期比赛
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */

class Student_Signs
{

    public $appid = 'wxb575928422b38270';
    public $appsecret = '1f55ec97e01f249b4ac57b7c99777173';
    
    public function __construct($action)
    {

        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false){
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

        $view = student_view_path.CONTROLLER.'/success.php';
        load_view_template($view);
    }


    /*二维码页面展示*/
    public function signs(){ ?>

    <script type="text/javascript">
        jQuery(function($){
            $.alerts('请使用微信扫码进行签到');
        })
            //alert('请使用微信扫码进行签到');
    </script>
    <?php
        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view);
    }


    public function hint(){

        $this->get_404('请使用');
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

        if(empty($users) || (!$users->user_mobile && !$users->user_email)){
            $users_id = $users->ID == true ? $users->ID : 0;
            $url = home_url('logins/bindPhone/uid/'.$users_id.'/access/'.$access_token.'/oid/'.$openid.'/loginType/sign');
        ?>
            <script type="text/javascript">
                alert("你是新用户或未进行手机绑定\n即将跳转到绑定页");
                setTimeout(function(){
                    window.location.href='<?=$url?>';
                    return false;
                },1500)
            </script>

        <?php
            exit;
        }

        update_user_meta($users->ID,'user_session_id',session_id());
        wp_set_current_user($users->ID);
        wp_set_auth_cookie($users->ID);

        $meta = get_user_meta($users->ID,'user_real_name')[0];
        //var_dump($users->ID);die;
        if(empty($meta['real_name'])){
            $url = home_url('account/certification/type/sign');
            //add_shortcode('student-signs',array($this,'signs'));
        ?>
            <script type="text/javascript">
            	//$.alerts('即将跳转到实名认证页');
                alert('你未进行实名认证\n即将跳转到实名认证页');
                setTimeout(function(){
                    window.location.href='<?=$url?>';
                    return false;
                },1500)
            </script>
        <?php }
        else{

        	$sql = "SELECT a.id,a.zhifu FROM sckm_match_orders a LEFT JOIN sckm_members  b ON a.member_id = b.id WHERE a.match_id = 234 and a.status = 1 and b.truename = '{$meta['real_name']}' ";
            $row = $wpdb->get_row($sql,ARRAY_A);
         	//var_dump($row);die;
            if(empty($row)){
				$url = home_url('account/certification/type/sign');
			?>
				<script type="text/javascript">
            	//$.alerts('即将跳转到实名认证页');
                alert('该用户未在老平台进行比赛报名\n请确认该姓名的真实性');
                setTimeout(function(){
                    window.location.href='<?=$url?>';
                    return false;
                },1500)
            </script>
            <?php }else{
            	$order_id = $wpdb->get_var("select id order_id from {$wpdb->prefix}order where match_id = 56522 and user_id = {$users->ID} ");
            	//var_dump($order_id);die;
            	if(!empty($order_id)){
            		$b = $wpdb->insert(
                                        $wpdb->prefix.'match_sign',
                                        array(
                                            'user_id'=>$users->ID,
                                            'match_id'=>56522,
                                            'created_time' => get_time('mysql')
                                        )
                                );
            		//var_dump(is_user_logged_in());die;
            		?>
					<script type="text/javascript">
		            	//$.alerts('即将跳转到实名认证页');
		                alert('签到成功');
		                setTimeout(function(){
		                    window.location.href='<?=home_url('matchs')?>';
		                    return false;
		                },1500)
            		</script>
            	<?php
            	}else{

						$wpdb->startTrans();
                        //在平台创建订单
                        $orde_data = array(
                                    'user_id' => $users->ID,
                                    'match_id' => 56522, //等待比赛创建后修改match_id
                                    'order_type' =>1,
                                    'pay_type' => 'wx',
                                    'cost' => 0,
                                    'pay_status' => 4,
                                    'created_time' => get_time('mysql')
                                );
                        $a = $wpdb->insert($wpdb->prefix.'order',$orde_data);
                        $orders_id_ = $wpdb->insert_id;
                        $b = $wpdb->insert(
                                        $wpdb->prefix.'match_sign',
                                        array(
                                            'user_id'=>$users->ID,
                                            'match_id'=>56522,
                                            'created_time' => get_time('mysql')
                                        )
                                );
                        $c = $wpdb->update($wpdb->prefix.'order', ['serialnumber' => createNumber($users->ID,$orders_id_)], ['id' => $orders_id_]);

                        if($a && $b && $c){
                        	$wpdb->commit();
                    	?>
                    	<script type="text/javascript">
			            	//$.alerts('即将跳转到实名认证页');
			                alert('签到成功');
			                setTimeout(function(){
			                    window.location.href='<?=home_url('matchs')?>';
			                    return false;
			                },1500)
	            		</script>
                    	<?php
                        }else{
                        	$wpdb->rollback();
                    	?>
                    	<script type="text/javascript">
			            	//$.alerts('即将跳转到实名认证页');
			                alert('签到失败,比赛订单创建失败\n请联系管理员');
	            		</script>
                        <?php }
            	}
            }
        }
        var_dump($meta);die;

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
     * 默认公用js/css引入
     */
    public function scripts_default(){ 
        if(ACTION == 'success'){
            wp_register_style( 'my-signs-success', student_css_url.'signs/success.css',array('my-student') );
            wp_enqueue_style( 'my-signs-success' );
        }
        //wp_register_script( 'my-student-sign', student_js_url.'student-sign.js',array('jquery'),leo_student_version);
        //wp_enqueue_script( 'my-student-sign' );           

    }
}