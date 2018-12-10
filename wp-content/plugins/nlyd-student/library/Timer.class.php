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
        $filed = 'a.post_title,b.id,b.match_id,match_scene,b.match_start_time,b.match_end_time,b.entry_end_time,c.meta_value as match_switch';
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
            //获取训练题库
            $cats = $wpdb->get_results("select term_id,slug from {$wpdb->prefix}terms where slug in('en-test-question','cn-test-question')" ,ARRAY_A);
            $test = array_column($cats,'slug','term_id');

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

                        //将比赛题库清空
                        /*$sql = "select a.post_id,c.slug
                                from {$wpdb->prefix}match_questions a 
                                left join {$wpdb->prefix}term_relationships b on a.post_id = b.object_id
                                left join {$wpdb->prefix}terms c on b.term_taxonomy_id = c.term_id
                                where a.match_id = {$v['match_id']} AND a.post_id != ''
                                ";
                        $results = $wpdb->get_results($sql,ARRAY_A);
                        //print_r($results);
                        if(!empty($results)){
                            foreach ($results as $x){
                                if($x['slug'] == 'cn-match-question'){
                                    $cat_id['term_taxonomy_id'] = array_search('cn-test-question',$test);
                                }elseif ($x['slug'] == 'en-match-question'){
                                    $cat_id['term_taxonomy_id'] = array_search('en-test-question',$test);
                                }
                                //print_r($cat_id);
                                $b = $wpdb->update($wpdb->prefix.'term_relationships',$cat_id,array('object_id'=>$x['post_id']));
                                /*die;
                                print_r($b);
                            }
                        }*/
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
        $filed = 'a.post_title,b.id,b.grading_id,scene,b.start_time,b.end_time,b.entry_end_time,person_liable,person_liable,created_person,c.meta_value as match_switch';
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
                        if($v['scene'] == 1){

                            //获取本次考级所有考级学员以及2级推广人
                            $sql = "select a.user_id,a.pay_type,a.match_id,b.referee_id,c.referee_id as indirect_referee_id 
                                    from {$wpdb->prefix}order a 
                                    left join {$wpdb->prefix}users b on a.user_id = b.ID
                                    left join {$wpdb->prefix}users c on b.referee_id = c.ID
                                    where a.match_id = {$v['grading_id']} and a.pay_type = 2 and pay_status in (2,3,4) ";
                            $rows_ = $wpdb->get_results($sql,ARRAY_A);

                            if(!empty($rows_)){
                                $str = '';
                                foreach ($rows_ as $i) {
                                    //考级负责人
                                    if($v['person_liable'] > 1){    //如果有负责人
                                        $person_liable_id = $v['person_liable'];
                                    }
                                    else{  //否则发布人就是负责人
                                        $person_liable_id = $v['created_person'];
                                    }
                                    //考级中心
                                    $sponsor_id = $v['created_person'];
                                    //考级中心上级
                                    if(!empty($created_person)){
                                        $manager_id = $wpdb->get_var("select referee_id from {$wpdb->prefix}users where ID = {$created_person}");
                                    }
                                    else{
                                        $manager_id = '';
                                    }

                                    //准备对应的数据
                                    $money1 = 5;    //考级直接推广人
                                    $money2 = 2.5;    //考级间接推广人
                                    $money3 = 3;    //考级负责人
                                    $money4 = 40;    //考级中心
                                    $money5 = 20;    //考级中心上级

                                    "( '{$i['pay_type']}', '{$i['match_id']}', '{$i['user_id']}', '{$i['referee_id']}', '{$money1}', '{$i['indirect_referee_id']}', '$money2', '{$person_liable_id}', '{$money3}', '{$sponsor_id}', '{$money4}', '{$manager_id}', '{$money5}', NOW())";
                                }

                                $sql = "INSERT INTO `{$wpdb->prefix}income_logs` 
                                    ( `pay_type`, `match_id`, `user_id`, `referee_id`, `referee_income`, `indirect_referee_id`, `indirect_referee_income`, `person_liable_id`, `person_liable_income`, `sponsor_id`, `sponsor_income`, `manager_id`, `manager_income`, `created_time`) VALUES";
                            }

                            //if($v['grading_id'] == 897){
                                //print_r($v);
                                print_r($rows_);
                                /*if($v['person_liable'] > 1){    //如果有负责人
                                    $person_liable = $v['person_liable'];
                                }
                                else{  //否则发布人就是负责人
                                    $person_liable = $v['created_person'];
                                }
                                //考级中心
                                $created_person = $v['created_person'];

                                //获取考级中心上级
                                if(!empty($created_person)){
                                    $manager_id = $wpdb->get_var("select referee_id from {$wpdb->prefix}users where ID = {$created_person}");
                                }
                                else{
                                    $manager_id = '';
                                }
                                print_r($manager_id);*/

                                //准备对应的数据
                                $money1 = 5;    //考级直接推广人
                                $money2 = 2.5;    //考级间接推广人
                                $money3 = 3;    //考级负责人
                                $money4 = 40;    //考级中心
                                $money5 = 20;    //考级中心上级


                            //}



                            //数据插入
                        }
                    }

                    //var_dump($v['match_id']);
                    //var_dump($save);
                    //改变比赛状态
                    $a = $wpdb->update($wpdb->prefix.'grading_meta',$save,array('id'=>$v['id'],'grading_id'=>$v['grading_id']));
                    //var_dump($a);
                    //改变比赛项目状态
                    /*$sql1 = "select id,match_id,start_time,end_time from {$wpdb->prefix}match_project_more where match_id = {$v['match_id']} ";
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
                    }*/
                    //print_r($project_rows);
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