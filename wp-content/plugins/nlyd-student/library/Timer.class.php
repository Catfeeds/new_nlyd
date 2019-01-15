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

    //比赛自动更改状态
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
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($rows)){
            //var_dump($rows);

            $new_time = get_time('mysql');
            foreach ($rows as $v){
                if($v['match_status'] != -4){

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
                            //获取当前考级是否已经进行了收益分配
                            /*$sql1 = "select a.match_id,a.match_scene,c.user_id,c.user_income
                                from {$wpdb->prefix}match_meta_new a 
                                left join {$wpdb->prefix}user_stream_logs c on a.match_id = c.match_id 
                                where c.match_id = {$v['match_id']} AND c.user_income is null";
                            $row_ = $wpdb->get_row($sql1,ARRAY_A);
                            //print_r($sql1);
                            if(empty($row_['user_income'])){
                                //获取本次考级所有考级学员以及2级推广人
                                $sql2 = "select a.user_id,a.order_type,a.match_id,a.sub_centres_id,b.referee_id,c.referee_id as indirect_referee_id 
                                        from {$wpdb->prefix}order a 
                                        left join {$wpdb->prefix}users b on a.user_id = b.ID
                                        left join {$wpdb->prefix}users c on b.referee_id = c.ID
                                        where a.match_id = {$v['match_id']} and a.order_type = 1 and pay_status in (2,3,4) ";
                                $rows_ = $wpdb->get_results($sql2,ARRAY_A);
                                //print_r($sql2);
                                if(!empty($rows_)){
                                    //获取当前考级收益列表
                                    $results = $wpdb->get_results("select * from {$wpdb->prefix}user_income_logs where match_id = {$v['match_id']} and income_type = 'match' ",ARRAY_A);

                                    if(!empty($results)){
                                        $str = '';
                                        $user_stream = array();  //用户收益数组
                                        $money4 = 0;
                                        $money1 = 5;    //考级直接推广人
                                        $money2 = 5;    //考级间接推广人
                                        $money3 = 20;    //考级负责人
                                        $money4 = 20;    //考级中心
                                        foreach ($results as $i) {
                                            if( $i['income_status'] == 2 ){

                                                //处理每个考试用户流水

                                                //直接收益
                                                if(!empty($i['referee_id']) && $i['referee_income']>0 ){

                                                    $key1 = $i['referee_id'];
                                                    $money1 = $i['referee_income'];
                                                    if(isset($user_stream[$key1])){
                                                        //referee_income
                                                        $user_stream[$key1] += $money1;
                                                    }else{
                                                        $user_stream[$key1] = $money1;
                                                    }
                                                }

                                                //间接收益
                                                if(!empty($i['indirect_referee_id']) && $i['indirect_referee_income']>0 ){

                                                    $key2 = $i['indirect_referee_id'];
                                                    $money2 = $i['indirect_referee_income'];
                                                    if(isset($user_stream[$key2])){
                                                        //referee_income
                                                        $user_stream[$key2] += $money2;
                                                    }else{
                                                        $user_stream[$key2] = $money2;
                                                    }
                                                }

                                                //责任教练/参赛机构
                                                if(!empty($i['person_liable_id']) && $i['person_liable_income']>0 ) {

                                                    $key3 = $i['person_liable_id'];
                                                    $money3 = $i['person_liable_income'];
                                                    if(isset($user_stream[$key3])){
                                                        //referee_income
                                                        $user_stream[$key3] += $money3;
                                                    }else{
                                                        $user_stream[$key3] = $money3;
                                                    }
                                                }

                                                //比赛中心
                                                if(!empty($i['sponsor_id']) && $i['sponsor_income']>0 ) {

                                                    $money4 += $i['sponsor_income'];
                                                }
                                            }
                                        }
                                        //var_dump($results[0]['income_status']);die;
                                        if($results[0]['income_status'] == 2){

                                            $wpdb->query('START TRANSACTION');
                                            if(!empty($user_stream)){
                                                $str1 = '';
                                                foreach ($user_stream as $k => $t){
                                                    $str1 .= "({$k},'match',{$v['match_id']}, {$t}, NOW()),";
                                                }
                                                $sql_ = "INSERT INTO `{$wpdb->prefix}user_stream_logs` ( `user_id`, `income_type`, `match_id`, `user_income`, `created_time`) VALUES ".rtrim($str1, ',');
                                                //print_r($sql_);die;
                                                $x = $wpdb->query($sql_);
                                            }else{
                                                $x = 1;
                                            }

                                            $y = $wpdb->update($wpdb->prefix.'user_stream_logs',array('user_income'=>$money4),array('user_id'=>$v['created_id'],'match_id'=>$v['match_id'],'income_type'=>'undertake'));

                                            //print_r($x .'&&'. $y);die;
                                            if($x && $y){
                                                $wpdb->query('COMMIT');
                                            }else{
                                                $wpdb->query('ROLLBACK');
                                            }
                                        }


                                    }
                                }
                            }*/
                        }
                        //var_dump($v['match_id']);
                        //var_dump($save);
                        //改变比赛状态

                        $a = $wpdb->update($wpdb->prefix.'match_meta_new',$save,array('id'=>$v['id'],'match_id'=>$v['match_id']));

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
                if($v['status'] != -4){

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
                            /*
                            //获取当前考级是否已经进行了收益分配
                            $sql1 = "select a.grading_id,a.scene,c.user_id,c.user_income
                                from {$wpdb->prefix}grading_meta a 
                                left join {$wpdb->prefix}user_stream_logs c on a.grading_id = c.match_id
                                where c.match_id = {$v['grading_id']} AND c.user_income is null";
                            $row_ = $wpdb->get_row($sql1,ARRAY_A);
                            //print_r($sql1);

                            if(empty($row_['user_income'])){

                                //获取本次考级所有考级学员以及2级推广人
                                $sql2 = "select a.user_id,a.order_type,a.match_id,b.referee_id,c.referee_id as indirect_referee_id 
                                    from {$wpdb->prefix}order a 
                                    left join {$wpdb->prefix}users b on a.user_id = b.ID
                                    left join {$wpdb->prefix}users c on b.referee_id = c.ID
                                    where a.match_id = {$v['grading_id']} and a.order_type = 2 and pay_status in (2,3,4) ";
                                $rows_ = $wpdb->get_results($sql2,ARRAY_A);
                                //print_r($rows_);die;
                                if(!empty($rows_)){
                                    //获取当前考级收益列表
                                    $results = $wpdb->get_results("select * from {$wpdb->prefix}user_income_logs where match_id = {$v['grading_id']} and income_type = 'grading' ",ARRAY_A);

                                    if(!empty($results)){
                                        //print_r($results);die;
                                        $str = '';
                                        $user_stream = array();  //用户收益数组
                                        $money4 = 0;
                                        $money1 = 5;    //考级直接推广人
                                        $money2 = 5;    //考级间接推广人
                                        $money3 = 20;    //考级负责人
                                        $money4 = 20;    //考级中心
                                        foreach ($results as $i) {
                                            if( $i['income_status'] == 2 ){

                                                //处理每个考试用户流水

                                                //直接收益
                                                if(!empty($i['referee_id']) && $i['referee_income']>0 ){

                                                    $key1 = $i['referee_id'];
                                                    $money1 = $i['referee_income'];
                                                    if(isset($user_stream[$key1])){
                                                        //referee_income
                                                        $user_stream[$key1] += $money1;
                                                    }else{
                                                        $user_stream[$key1] = $money1;
                                                    }
                                                }

                                                //间接收益
                                                if(!empty($i['indirect_referee_id']) && $i['indirect_referee_income']>0 ){

                                                    $key2 = $i['indirect_referee_id'];
                                                    $money2 = $i['indirect_referee_income'];
                                                    if(isset($user_stream[$key2])){
                                                        //referee_income
                                                        $user_stream[$key2] += $money2;
                                                    }else{
                                                        $user_stream[$key2] = $money2;
                                                    }
                                                }

                                                //责任教练/参赛机构
                                                if(!empty($i['person_liable_id']) && $i['person_liable_income']>0 ) {

                                                    $key3 = $i['person_liable_id'];
                                                    $money3 = $i['person_liable_income'];
                                                    if(isset($user_stream[$key3])){
                                                        //referee_income
                                                        $user_stream[$key3] += $money3;
                                                    }else{
                                                        $user_stream[$key3] = $money3;
                                                    }
                                                }

                                                //比赛中心
                                                if(!empty($i['sponsor_id']) && $i['sponsor_income']>0 ) {
                                                    $money4 += $i['sponsor_income'];
                                                }
                                            }
                                        }
                                        if($results[0]['income_status'] == 2){
                                            $wpdb->query('START TRANSACTION');
                                            if(!empty($user_stream)){
                                                $str1 = '';
                                                foreach ($user_stream as $k => $t){
                                                    $str1 .= "({$k},'grading',{$v['grading_id']}, {$t}, NOW()),";
                                                }
                                                $sql_ = "INSERT INTO `{$wpdb->prefix}user_stream_logs` ( `user_id`, `income_type`, `match_id`, `user_income`, `created_time`) VALUES ".rtrim($str1, ',');
                                                //print_r($sql_);die;
                                                $wpdb->query('START TRANSACTION');
                                                $x = $wpdb->query($sql_);
                                            }else{
                                                $x = 1;
                                            }
                                            $y = $wpdb->update($wpdb->prefix.'user_stream_logs',array('user_income'=>$money4),array('user_id'=>$v['created_person'],'match_id'=>$v['grading_id'],'income_type'=>'undertake'));

                                            //print_r($x .'&&'. $y);die;
                                            if($x && $y){
                                                $wpdb->query('COMMIT');
                                            }else{
                                                $wpdb->query('ROLLBACK');
                                            }
                                        }
                                    }
                                }
                            }*/
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
    }


    public function wpjam_more_reccurences() {

        return array(
            'minutely' => array('interval' => 60, 'display' => 'Once Weekly'),
        );
    }

}