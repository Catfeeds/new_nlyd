<?php
/**
 * 定时器
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/21
 * Time: 14:09
 */

namespace library;


class Timer
{
    public $setting;
    public function __construct()
    {
        $this->setting = get_option('default_setting');
        add_filter('cron_schedules', array($this,'wpjam_more_reccurences'));
        $this->wpjam_more_reccurences();

        $this->autoTimer();
    }


    public function autoTimer(){
        if($this->setting['default_timer'] == 1){

            if (!wp_next_scheduled('my_minutely_event')) {
                wp_schedule_event( time(), 'minutely', 'my_minutely_event' );
                //var_dump(date('Y-m-d H:i:s',wp_next_scheduled('my_minutely_event')));
            }
            //var_dump(date('Y-m-d H:i:s',wp_next_scheduled('my_minutely_event')));
            add_action( 'my_minutely_event', array($this,'wpjam_daily_function'));

        }else{

            if (wp_next_scheduled('my_minutely_event')) {
                wp_clear_scheduled_hook('my_minutely_event');
            }
        }
    }

    public function wpjam_daily_function() {
        global $wpdb;
        $map = array();
        $map[] = ' a.post_type = "match" ';
        $map[] = ' a.post_status = "publish" ';
        $where = join(' and ',$map);
        $filed = 'a.post_title,b.id,b.match_id,b.match_use_time,b.match_more,b.match_project_interval,b.match_subject_interval,b.match_start_time,b.entry_start_time,b.entry_end_time';
        $sql = "SELECT $filed FROM {$wpdb->prefix}posts a 
                LEFT JOIN {$wpdb->prefix}match_meta b ON a.ID = b.match_id
                WHERE {$where}
                ";

        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($rows)){
            //var_dump($rows);
            $new_time = date('Y-m-d H:i:s',time());
            foreach ($rows as $v){

                //获取开赛的比赛项目
                $sql_ = "select project_use_time,match_more,project_time_interval,project_start_time from {$wpdb->prefix}match_project where post_id = {$v['match_id']}  order by project_start_time desc ";
                $results = $wpdb->get_results($sql_,ARRAY_A);

                //计算结束时间
                if(!empty($results)){
                    $match_use_time = 0;
                    //var_dump($results);
                    foreach ($results as $val){

                        $project_use_time = $val['project_use_time'] > 0 ? $val['project_use_time'] : $v['match_use_time'];
                        $match_more = $val['match_more'] > 0 ? $val['match_more'] : $v['match_more'];
                        $project_time_interval = $val['project_time_interval'] > 0 ? $val['project_time_interval'] : $v['match_subject_interval'];

                        $match_use_time += $project_use_time*$match_more + ($match_more-1)*$project_time_interval + $v['match_project_interval'];
                        //var_dump($match_end_time);
                    }
                    $match_end_time = strtotime($v['match_start_time']) + ($match_use_time-$v['match_project_interval'])*60;

                    if(strtotime($results[0]['project_start_time']) > 1){
                        $fixed = $results[0];
                        $project_use_time = !empty($fixed['project_use_time']) ? $fixed['project_use_time'] : $v['match_use_time'];
                        $match_more = !empty($val['match_more']) ? $val['match_more'] : $v['match_more'];
                        $project_time_interval = !empty($val['project_time_interval']) ? $val['project_time_interval'] : $v['match_subject_interval'];

                        $fixed_use_time = $project_use_time*$match_more + ($match_more-1)*$project_time_interval;
                        $match_end_time = strtotime($fixed['project_start_time']) + $fixed_use_time*60;
                    }
                    $end_time = date('Y-m-d H:i:s',$match_end_time);
                    //var_dump($end_time);
                }else{

                    $use_time = $v['match_use_time']*$v['match_more']*6 + 5*$v['match_project_interval'] + (($v['match_more']-1)*$v['match_subject_interval'])*6;
                    $match_end_time = strtotime($v['entry_start_time'])+$use_time*60;
                    $end_time = date('Y-m-d H:i:s',$match_end_time);
                    //var_dump($end_time);
                }
                /*$end_time = date('Y-m-d H:i:s',$match_end_time);
                var_dump($end_time);*/

                if($new_time < $v['entry_start_time']){     //未开始
                    $save = array('match_status'=>-1);
                }else if($v['entry_start_time'] < $new_time && $new_time < $v['entry_end_time']){     //报名中
                    $save = array('match_status'=>1);
                }else if($v['entry_end_time'] < $new_time && $new_time < $v['match_start_time']){       //等待开赛
                    $save = array('match_status'=>-2);
                }else if($v['match_start_time'] < $new_time && $new_time < $end_time){       //比赛中
                    $save = array('match_status'=>2);
                }else if($end_time < $new_time){
                    $save = array('match_status'=>-3);
                }
                $a = $wpdb->update($wpdb->prefix.'match_meta',$save,array('id'=>$v['id'],'match_id'=>$v['match_id']));
                //var_dump($a);
            }
        }
        $myfile = file_put_contents(leo_user_interface_path.'log/timer-log.txt', date('Y-m-d H:i:s',time())."  This is a timer. \r\n", FILE_APPEND);
        //$myfile = file_put_contents(leo_user_interface_path.'log/sql-log.txt', date('Y-m-d H:i:s',time())."  $sql \r\n", FILE_APPEND);
    }

    public function wpjam_more_reccurences() {

        return array(
            'minutely' => array('interval' => 60, 'display' => 'Once Weekly'),
        );
    }

}
new Timer();