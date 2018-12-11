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
        $filed = 'a.post_title,b.id,b.match_id,b.match_scene,b.match_start_time,b.match_end_time,b.entry_end_time,b.match_cost,b.created_id,c.meta_value as match_switch';
        $sql = "SELECT $filed FROM {$wpdb->prefix}posts a 
                RIGHT JOIN {$wpdb->prefix}match_meta_new b ON a.ID = b.match_id
                LEFT JOIN {$wpdb->prefix}postmeta c ON a.ID = c.post_id and meta_key = 'default_match_switch'
                WHERE {$where}
                ORDER BY b.match_start_time DESC
                ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($rows)){
            //var_dump($rows);

            $new_time = get_time('mysql');
            foreach ($rows as $v){

                if($v['match_switch'] == 'ON'){

                    if($new_time < $v['entry_end_time']){
                        //报名中
                        $save['match_status'] = 1;
                    }
                    elseif ($v['entry_end_time'] <= $new_time && $new_time < $v['match_start_time']){
                        //等待开赛
                        $save['match_status'] = -2;
                    }
                    elseif ($v['match_start_time'] <= $new_time && $new_time < $v['match_end_time']){
                        //进行中
                        $save['match_status'] = 2;
                    }else{
                        //已结束
                        $save['match_status'] = -3;

                        //如果是正式比赛根据规则进行费用发布
                        if($v['match_scene'] == 1 && $v['match_cost'] > 200){

                            //获取当前考级是否已经进行了收益分配
                            $total = $wpdb->get_var("select count(id) total from {$wpdb->prefix}income_logs where match_id = {$v['match_id']} and order_type = 1 ");
                            if($total < 1){
                                //获取本次比赛所有考级学员以及2级推广人
                                $sql = "select a.user_id,a.order_type,a.match_id,a.sub_centres_id,b.referee_id,c.referee_id as indirect_referee_id 
                                    from {$wpdb->prefix}order a 
                                    left join {$wpdb->prefix}users b on a.user_id = b.ID
                                    left join {$wpdb->prefix}users c on b.referee_id = c.ID
                                    where a.match_id = {$v['match_id']} and a.order_type = 1 and pay_status in (2,3,4) ";
                                $rows_ = $wpdb->get_results($sql,ARRAY_A);

                                if(!empty($rows_)){
                                    $str = '';
                                    foreach ($rows_ as $i) {

                                        //比赛中心
                                        $sponsor_id = $v['created_id'];
                                        //参赛机构
                                        $person_liable_id = $v['sub_centres_id'];


                                        //准备对应的数据
                                        $money1 = 5;        //比赛直接推广人
                                        $money2 = 2.5;    //比赛间接推广人
                                        $money3 = 40;    //参赛机构
                                        $money4 = 40;    //比赛中心

                                        $str .= "( '{$i['order_type']}', '{$i['match_id']}', '{$i['user_id']}', '{$i['referee_id']}', '{$money1}', '{$i['indirect_referee_id']}', '$money2', '{$person_liable_id}', '{$money3}', '{$sponsor_id}', '{$money4}', NOW()),";
                                    }

                                    //print_r($str);

                                    $sql = "INSERT INTO `{$wpdb->prefix}income_logs`( `order_type`, `match_id`, `user_id`, `referee_id`, `referee_income`, `indirect_referee_id`, `indirect_referee_income`, `person_liable_id`, `person_liable_income`, `sponsor_id`, `sponsor_income`, `created_time`) VALUES ".rtrim($str, ',');

                                    //print_r($sql);
                                    $result = $wpdb->query($sql);

                                }
                            }
                        }
                    }
                    //var_dump($v['match_id']);
                    //var_dump($save);
                    //改变比赛状态
                    $a = $wpdb->update($wpdb->prefix.'match_meta_new',$save,array('id'=>$v['id'],'match_id'=>$v['match_id']));
                    //var_dump($a);
                    //改变比赛项目状态
                    $sql1 = "select id,match_id,start_time,end_time from {$wpdb->prefix}match_project_more where match_id = {$v['match_id']} ";
                    $project_rows = $wpdb->get_results($sql1,ARRAY_A);
                    if(!empty($project_rows)){
                        foreach ($project_rows as $val){

                            if($new_time < $val['start_time']){
                                //未开始
                                $status['status'] = 1;
                            }elseif ($val['start_time'] <= $new_time && $new_time <= $val['end_time']){
                                //进行中
                                $status['status'] = 2;
                            }else{
                                //进行中
                                $status['status'] = -1;
                            }
                            
                            //改变比赛项目状态
                            $b = $wpdb->update($wpdb->prefix.'match_project_more',$status,array('id'=>$val['id'],'match_id'=>$val['match_id']));
                        }
                    }
                    //print_r($project_rows);
                }
            }
        }
        //测试时放出
        //$type = !empty(ACTION) ? ACTION : '';
        //$myfile = file_put_contents(wp_upload_dir()['basedir'].'/timer-log/timer-log.txt', get_time('mysql')."  This is a timer ".$type." \r\n", FILE_APPEND);

    }


    //考级自动更改状态
    public function wpjam_daily_grading(){

        global $wpdb;
        $map = array();
        $map[] = ' a.post_type = "grading" ';
        $map[] = ' a.post_status = "publish" ';
        $map[] = ' c.meta_value="ON" ';
        $where = join(' and ',$map);
        $filed = 'a.post_title,b.id,b.grading_id,scene,b.start_time,b.end_time,b.entry_end_time,person_liable,b.cost,b.person_liable,b.created_person,c.meta_value as match_switch';
        $sql = "SELECT $filed FROM {$wpdb->prefix}posts a 
                RIGHT JOIN {$wpdb->prefix}grading_meta b ON a.ID = b.grading_id
                LEFT JOIN {$wpdb->prefix}postmeta c ON a.ID = c.post_id and meta_key = 'default_match_switch'
                WHERE {$where}
                ORDER BY b.start_time DESC
                ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($rows)){
            //var_dump($rows);

            $new_time = get_time('mysql');
            foreach ($rows as $v){

                if($v['match_switch'] == 'ON'){

                    if($new_time < $v['entry_end_time']){
                        //报名中
                        $save['status'] = 1;
                    }
                    elseif ($v['entry_end_time'] <= $new_time && $new_time < $v['start_time']){
                        //等待开赛
                        $save['status'] = -2;
                    }
                    elseif ($v['start_time'] <= $new_time && $new_time < $v['end_time']){
                        //进行中
                        $save['status'] = 2;
                    }else{
                        //已结束
                        $save['status'] = -3;

                        //如果是正式考级根据规则进行费用发布
                        if($v['scene'] == 1 && $v['match_cost'] > 300){

                            //获取当前考级是否已经进行了收益分配
                            $total = $wpdb->get_var("select count(id) total from {$wpdb->prefix}income_logs where match_id = {$v['grading_id']} and order_type = 2 ");
                            if($total < 1){
                                //获取本次考级所有考级学员以及2级推广人
                                $sql = "select a.user_id,a.order_type,a.match_id,b.referee_id,c.referee_id as indirect_referee_id 
                                    from {$wpdb->prefix}order a 
                                    left join {$wpdb->prefix}users b on a.user_id = b.ID
                                    left join {$wpdb->prefix}users c on b.referee_id = c.ID
                                    where a.match_id = {$v['grading_id']} and a.order_type = 2 and pay_status in (2,3,4) ";
                                $rows_ = $wpdb->get_results($sql,ARRAY_A);

                                if(!empty($rows_)){
                                    $str = '';
                                    foreach ($rows_ as $i) {
                                        //if($v['grading_id'] == 897){

                                        //考级负责人
                                        if($v['person_liable'] > 1){    //如果有负责人
                                            $person_liable_id = $v['person_liable'];
                                        }
                                        else{  //否则发布人就是负责人
                                            $person_liable_id = $v['created_person'];
                                        }
                                        //考级中心
                                        $sponsor_id = $v['created_person'];


                                        //准备对应的数据
                                        $money1 = 5;    //考级直接推广人
                                        $money2 = 2.5;    //考级间接推广人
                                        $money3 = 3;    //考级负责人
                                        $money4 = 40;    //考级中心

                                        $str .= "( '{$i['order_type']}', '{$i['match_id']}', '{$i['user_id']}', '{$i['referee_id']}', '{$money1}', '{$i['indirect_referee_id']}', '$money2', '{$person_liable_id}', '{$money3}', '{$sponsor_id}', '{$money4}', NOW()),";
                                        //}
                                    }

                                    //print_r($str);

                                    $sql = "INSERT INTO `{$wpdb->prefix}income_logs`( `order_type`, `match_id`, `user_id`, `referee_id`, `referee_income`, `indirect_referee_id`,`indirect_referee_income`, `person_liable_id`, `person_liable_income`, `sponsor_id`,`sponsor_income`, `created_time`) VALUES ".rtrim($str, ',');

                                    $result = $wpdb->query($sql);

                                    //print_r($sql);
                                }
                            }
                        }
                    }

                    //var_dump($v['match_id']);
                    //var_dump($save);
                    //改变考级状态
                    $a = $wpdb->update($wpdb->prefix.'grading_meta',$save,array('id'=>$v['id'],'grading_id'=>$v['grading_id']));
                    //var_dump($a);
                }
            }
        }
    }


    public function wpjam_more_reccurences() {

        return array(
            'minutely' => array('interval' => 60, 'display' => 'Once Weekly'),
        );
    }

}