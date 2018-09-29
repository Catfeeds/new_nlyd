<?php
use library\Timer;
use library\AliSms;
use library\TwentyFour;
/**
 * Redis测试
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Timer
{
    public function __construct($action)
    {


        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        //添加短标签
        add_shortcode('timer-index',array($this,$action));
    }

     public function test(){

     	$redis = new Redis();
        $redis->connect('127.0.0.1',6379,1);
        $redis->auth('leo626');
     	global $wpdb;	

        if(empty($redis->get('order'))){
        	
	        $sql = 'SELECT a.member_id,d.openid,d.mem_mobile,d.`truename`,b.team_id,c.title FROM sckm_match_orders a 
					LEFT JOIN sckm_team_members b ON a.member_id = b.member_id 
					LEFT JOIN sckm_teams c on b.team_id = c.id
					LEFT JOIN sckm_members d ON a.member_id = d.id
					WHERE a.match_id = 234 and a.`status` = 1';	
			$data = $wpdb->get_results($sql,ARRAY_A);
	        $redis->setex('order',600,json_encode($data));
        }else{
        	$data = json_decode($redis->get('order'),true);
        }

        $teams = array();
        $old_user = array();
        $new_user = array();

        foreach ($data as $k => $v) {

        	$sql = "SELECT ID FROM zlin_posts WHERE post_title = '{$v['title']}' ";
        	//print_r($sql);
        	$new_team_id = $wpdb->get_var($sql);
        	$data[$k]['new_team_id'] = $new_team_id;

        	$sql1 = "SELECT user_id FROM `zlin_usermeta` WHERE meta_key = 'user_real_name' AND meta_value LIKE '%{$v['truename']}%' ";
        	$new_uesr_id = $wpdb->get_var($sql1);
        	$data[$k]['new_uesr_id'] = $new_uesr_id;

        	if(empty($new_uesr_id)){

        		/*$user[$k]['member_id'] = $v['member_id'];
        		$user[$k]['name'] = $v['truename'];*/
        		$user[] = $v['member_id'];
        	} else{

				$new_user[] = $new_uesr_id;

				/*$sql2 = "SELECT id FROM  zlin_match_team WHERE user_id = {$new_uesr_id}";
	        	$num = 0;
	        	if(!$wpdb->get_var($sql2)){

	        		$insert = array(
	        				'team_id'=>$new_team_id,
	        				'user_id'=>$new_uesr_id,
	        				'user_type'=>1,
	        				'status'=>2,
	        				'created_time'=>get_time('mysql')
	        			);
	        		$a = $wpdb->insert('zlin_match_team',$insert);
	        		if($a){
	        			$num ++;
	        		}
	        	}*/
        	}
        	//print_r($user_id.'<br/>');
        	//var_dump($sql);
        	//var_dump($row);
        }

        var_dump(count($user));
        print_r(arr2str($user));
        var_dump(count($new_user));
		//$teams[] = $row;
        //var_dump($teams);

        //var_dump($data);
    }

    /**
     * 比赛状态自动化
     */
    public function index(){

        /*var_dump('leo-timer');
        var_dump(file_exists('/data/log.txt'));
        $myfile = file_put_contents('/data/log.txt', get_time('mysql')."  This is a timer. \r\n", FILE_APPEND);
        var_dump($myfile);die;*/

        $switch = get_option('default_setting')['default_timer'];
        if($switch == 1){
            $timer = new Timer();
            $timer->wpjam_daily_function();
        }
        $view = student_view_path.'/public/timer.php';
        load_view_template($view);
    }

    public function redis(){
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379,1);
        $redis->auth('leo626');
        //echo "Server is running: " . $redis->ping();

        //进队列
        /*$redis->lpush('list', 'html');
        $redis->lpush('list', 'css');
        $redis->lpush('list', 'php');*/

        echo "数据进队列完成\n";

        //可查看队列
        //获取列表中所有的值
        $list = $redis->lrange('list', 0, -1);
        print_r($list);echo '<br>';

        //从右侧加入一个
        /*$redis->rpush('list', 'mysql');
        $list = $redis->lrange('list', 0, -1);
        print_r($list);echo '<br>';*/

        //从左侧弹出一个
        /*$redis->rpop('list');
        $list = $redis->lrange('list', 0, -1);
        print_r($list);echo '<br>';*/

        /*//出队列
        $redis->lpop('queue_name');
        //查看队列
        $res = $redis->lrange('queue_name',0,-1);
        print_r($res);*/
    }

    public function send_sms(){
        //$ali = new AliSms();
        //$result = $ali->sendSms('15882484638,13666723810,18368349933,15067331988,13957339686,13004198439,15372368001,13700617776', 99, '');
        //var_dump($result);
        $view = student_view_path.'/public/timer.php';
        load_view_template($view);
    }

    public function TwentyFour(){
        $b = 0;
        $str = '$b = (8*9)/(10-7);';
        eval($str);
        var_dump($b);

        $twentyfour = new TwentyFour();
        $data = array(7,8,9,10);
        $results = $twentyfour->calculate($data);
        var_dump($results);
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
    }
}