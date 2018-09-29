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
        //$this->autoTimer();
    }


    public function autoTimer(){
        if($this->setting['default_timer'] == 1){

            /*wp_clear_scheduled_hook('my_minutely_event');
            if (!wp_next_scheduled('my_minutely_event')) {
                wp_schedule_event( time(), 'minutely', 'my_minutely_event' );
                //var_dump(date_i18n('Y-m-d H:i:s',wp_next_scheduled('my_minutely_event')));
            }*/
            $timestamp = !wp_next_scheduled('my_minutely_event') ? time() : wp_next_scheduled('my_minutely_event');
            wp_schedule_event( $timestamp, 'minutely', 'my_minutely_event' );

            //var_dump('当前时间:'.date_i18n('Y-m-d H:i:s',time()));
            //var_dump('下一次执行时间:'.date_i18n('Y-m-d H:i:s',wp_next_scheduled('my_minutely_event')));
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
        $map[] = ' c.meta_value="ON" ';
        $where = join(' and ',$map);
        $filed = 'a.post_title,b.id,b.match_id,b.match_use_time,b.match_more,b.match_project_interval,b.match_subject_interval,b.match_start_time,b.entry_start_time,b.entry_end_time,c.meta_value as match_switch';
        $sql = "SELECT $filed FROM {$wpdb->prefix}posts a 
                LEFT JOIN {$wpdb->prefix}match_meta b ON a.ID = b.match_id
                LEFT JOIN {$wpdb->prefix}postmeta c ON a.ID = c.post_id and meta_key = 'default_match_switch'
                WHERE {$where}
                ORDER BY b.match_start_time DESC
                ";
        //var_dump($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($rows)){
            //var_dump($rows);
            $new_time = get_time('mysql');
            foreach ($rows as $v){

                if($v['match_switch'] == 'ON'){

                    $rows = get_match_end_time($v['match_id']);
                    $start_time = reset($rows)['project_start_time'];
                    $end_time = end($rows)['project_end_time'];


                    if($new_time < $v['entry_end_time']){     //报名中
                        $save = array('match_status'=>1);
                    }else if($v['entry_end_time'] < $new_time && $new_time < $start_time){       //等待开赛
                        $save = array('match_status'=>-2);
                    }else if($start_time < $new_time && $new_time < $end_time){       //比赛中
                        $save = array('match_status'=>2);
                    }else if($end_time < $new_time){
                        $save = array('match_status'=>-3);
                    }
                    //var_dump($v['match_id']);
                    //var_dump($save);
                    $a = $wpdb->update($wpdb->prefix.'match_meta',$save,array('id'=>$v['id'],'match_id'=>$v['match_id']));
                    //var_dump($a);
                }
            }
        }
        //测试时放出
        //$type = !empty(ACTION) ? ACTION : '';
        //$myfile = file_put_contents(wp_upload_dir()['basedir'].'/timer-log/timer-log.txt', get_time('mysql')."  This is a timer ".$type." \r\n", FILE_APPEND);

    }

    public function wpjam_more_reccurences() {

        return array(
            'minutely' => array('interval' => 60, 'display' => 'Once Weekly'),
        );
    }

}