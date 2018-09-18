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
        $data = 'a:106:{i:0;s:17:"sckm_ad_positions";i:1;s:8:"sckm_ads";i:2;s:11:"sckm_albums";i:3;s:12:"sckm_answers";i:4;s:13:"sckm_articles";i:5;s:15:"sckm_attributes";i:6;s:11:"sckm_awards";i:7;s:11:"sckm_brands";i:8;s:13:"sckm_captchas";i:9;s:10:"sckm_carts";i:10;s:15:"sckm_categories";i:11;s:18:"sckm_class_members";i:12;s:23:"sckm_class_members_copy";i:13;s:12:"sckm_collets";i:14;s:13:"sckm_comments";i:15;s:18:"sckm_course_orders";i:16;s:12:"sckm_courses";i:17;s:14:"sckm_downloads";i:18;s:15:"sckm_exam_games";i:19;s:15:"sckm_exam_infos";i:20;s:16:"sckm_exam_orders";i:21;s:16:"sckm_exam_scores";i:22;s:10:"sckm_exams";i:23;s:19:"sckm_extend_classes";i:24;s:12:"sckm_extends";i:25;s:14:"sckm_feedbacks";i:26;s:15:"sckm_game_infos";i:27;s:10:"sckm_games";i:28;s:12:"sckm_gamexes";i:29;s:17:"sckm_group_groups";i:30;s:11:"sckm_groups";i:31;s:10:"sckm_infos";i:32;s:9:"sckm_jobs";i:33;s:14:"sckm_languages";i:34;s:10:"sckm_links";i:35;s:9:"sckm_maps";i:36;s:23:"sckm_marathon_addresses";i:37;s:21:"sckm_marathon_centers";i:38;s:21:"sckm_marathon_members";i:39;s:22:"sckm_marathon_subjects";i:40;s:14:"sckm_marathons";i:41;s:16:"sckm_match_games";i:42;s:17:"sckm_match_orders";i:43;s:24:"sckm_match_person_scores";i:44;s:20:"sckm_match_questions";i:45;s:21:"sckm_match_responders";i:46;s:17:"sckm_match_rounds";i:47;s:17:"sckm_match_scores";i:48;s:17:"sckm_match_videos";i:49;s:12:"sckm_matches";i:50;s:21:"sckm_member_addresses";i:51;s:18:"sckm_member_orders";i:52;s:12:"sckm_members";i:53;s:10:"sckm_menus";i:54;s:11:"sckm_mfiles";i:55;s:16:"sckm_money_datas";i:56;s:15:"sckm_money_logs";i:57;s:18:"sckm_money_records";i:58;s:11:"sckm_monies";i:59;s:16:"sckm_naobi_datas";i:60;s:12:"sckm_options";i:61;s:20:"sckm_order_addresses";i:62;s:15:"sckm_order_logs";i:63;s:19:"sckm_order_products";i:64;s:11:"sckm_orders";i:65;s:13:"sckm_partners";i:66;s:11:"sckm_people";i:67;s:13:"sckm_pictures";i:68;s:18:"sckm_product_attrs";i:69;s:18:"sckm_product_copys";i:70;s:21:"sckm_product_pictures";i:71;s:18:"sckm_product_specs";i:72;s:17:"sckm_product_tags";i:73;s:18:"sckm_product_types";i:74;s:13:"sckm_products";i:75;s:13:"sckm_projects";i:76;s:13:"sckm_qr_codes";i:77;s:19:"sckm_question_banks";i:78;s:14:"sckm_questions";i:79;s:12:"sckm_replies";i:80;s:19:"sckm_reply_pictures";i:81;s:17:"sckm_search_types";i:82;s:13:"sckm_searches";i:83;s:13:"sckm_settings";i:84;s:18:"sckm_subject_banks";i:85;s:13:"sckm_subjects";i:86;s:20:"sckm_system_messages";i:87;s:18:"sckm_system_values";i:88;s:23:"sckm_tag_config_sources";i:89;s:16:"sckm_tag_configs";i:90;s:26:"sckm_tag_source_config_use";i:91;s:16:"sckm_tag_sources";i:92;s:9:"sckm_tags";i:93;s:17:"sckm_team_members";i:94;s:10:"sckm_teams";i:95;s:20:"sckm_template_blocks";i:96;s:14:"sckm_templates";i:97;s:19:"sckm_topic_pictures";i:98;s:11:"sckm_topics";i:99;s:13:"sckm_trushbin";i:100;s:12:"sckm_uploads";i:101;s:18:"sckm_user_profiles";i:102;s:15:"sckm_userrights";i:103;s:10:"sckm_users";i:104;s:13:"sckm_vfolders";i:105;s:11:"sckm_videos";}';
        var_dump(unserialize($data));
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