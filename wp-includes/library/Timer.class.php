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
        $where = join(' and ',$map);
        $filed = 'a.post_title,b.id,b.match_id,b.match_use_time,b.match_more,b.match_project_interval,b.match_subject_interval,b.match_start_time,b.entry_start_time,b.entry_end_time,c.meta_value as match_switch';
        $sql = "SELECT $filed FROM {$wpdb->prefix}posts a 
                LEFT JOIN {$wpdb->prefix}match_meta b ON a.ID = b.match_id
                LEFT JOIN {$wpdb->prefix}postmeta c ON a.ID = c.post_id and meta_key = 'default_match_switch'
                WHERE {$where}
                ";
        //var_dump($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($rows)){
            //var_dump($rows);
            $new_time = get_time('mysql');
            foreach ($rows as $v){

                if($v['match_switch'] == 'ON'){

                    //获取开赛的比赛项目
                    $sql_ = "select a.match_project_id,a.project_use_time,a.match_more,a.project_time_interval,a.project_start_time,a.child_count_down,b.meta_value as project_alias
                         from {$wpdb->prefix}match_project a 
                         left join {$wpdb->prefix}postmeta b on a.match_project_id = b.post_id AND meta_key = 'project_alias'
                         where a.post_id = {$v['match_id']}  order by a.project_start_time desc ";
                    $results = $wpdb->get_results($sql_,ARRAY_A);
                    //var_dump($sql_);
                    //计算结束时间
                    if(!empty($results)){
                        $match_use_time = 0;
                        //var_dump($results);
                        foreach ($results as $val){
                            if($val['project_alias'] == 'zxss'){

                                $child_count_down = get_post_meta($val['match_project_id'],'child_count_down')[0];
                                if($val['child_count_down'] > 0){
                                    $child_count_down['even_add'] = $val['child_count_down'];
                                    $child_count_down['add_and_subtract'] = $val['child_count_down'];
                                    $child_count_down['wax_and_wane'] = $val['child_count_down'];
                                }elseif (!empty($child_count_down)){

                                    $child_count_down['even_add'] *= 1;
                                    $child_count_down['add_and_subtract'] *= 1;
                                    $child_count_down['wax_and_wane'] *= 1;
                                }else{

                                    $child_count_down['even_add'] = 3;
                                    $child_count_down['add_and_subtract'] = 3;
                                    $child_count_down['wax_and_wane'] = 3;
                                }
                                $project_use_time = $child_count_down['even_add']+$child_count_down['add_and_subtract']+$child_count_down['wax_and_wane'];
                                //print_r($project_use_time);
                            }else{
                                $project_use_time = $val['project_use_time'] > 0 ? $val['project_use_time'] : $v['match_use_time'];
                            }

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