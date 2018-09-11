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
        wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-cookie' );

        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
    }
}