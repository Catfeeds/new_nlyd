<?php

/**
 * 首页-名录
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16
 * Time: 14:38
 */
class Student_Directory
{

    public $ajaxControll;
    public function __construct($action)
    {
        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        
        if(isset($_GET['action'])){
            $function = $_GET['action'];
        }else {
            $function = $_GET['action'] = 'index';
        }

        $this->ajaxControll = new Student_Ajax();

        //添加短标签
        add_shortcode('directory-home',array($this,$action));
    }

    /**
     * 名录
     */
    public function index(){
        $view = student_view_path.CONTROLLER.'/directory.php';
        load_view_template($view);
    }
    /**
     * 2019赛区名录
     */
    public function directoryZone_pc(){
        $dir = student_view_path.CONTROLLER.'/static';
        if(!is_dir($dir)){
            mkdir($dir);
        }
        $json_file = $dir.'/directory.json';
        $file_name = $dir.'/zone_match.html';
        if(file_exists($json_file)){
            $conf = json_decode(file_get_contents($json_file), true);
        }else{
            $conf['zoneMatchTime'] = '';
            $conf['zoneMatchTermTime'] = '';
        }
        global $wpdb;
        $type_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}zone_type WHERE zone_type_alias='match'");
        $current_time = get_time('mysql');
        $last_time = $wpdb->get_var("SELECT MAX(audit_time) FROM {$wpdb->prefix}zone_meta WHERE type_id='{$type_id}' AND user_id>0 AND user_status=1 AND is_able=1 AND (term_time>'{$current_time}' OR term_time='')");

        if($conf['zoneMatchTime'] == $last_time && $conf['zoneMatchTermTime'] > $current_time && file_exists($file_name)){
            echo file_get_contents($file_name);
            exit;
        }else{
            $res = $wpdb->get_results("SELECT zm.zone_number,zm.bank_card_name,zm.chairman_id,zm.secretary_id,zm.zone_city,zm.zone_name,zt.zone_type_alias,zm.zone_match_type 
                   FROM {$wpdb->prefix}zone_meta AS zm 
                   LEFT JOIN {$wpdb->prefix}zone_type AS zt ON zt.id=zm.type_id 
                   WHERE zm.type_id='{$type_id}' AND zm.user_id>0 AND zm.user_status=1 AND zm.is_able=1 AND (zm.term_time>'{$current_time}' OR zm.term_time='')", ARRAY_A);
            $rows = [];
            $organizeClass = new Organize();
            foreach ($res as $re){
                $re['zone_title_name'] = $organizeClass->echoZoneName($re['zone_type_alias'], $re['zone_city'], $re['zone_name'], $re['zone_match_type'], 'get', '#ffb536');
                $re['chairman_name'] = get_user_meta($re['chairman_id'], 'user_real_name', true)['real_name'];
                $re['secretary_name'] = get_user_meta($re['secretary_id'], 'user_real_name', true)['real_name'];
                $rows[] = $re;
            }
//            leo_dump($rows);die;
            ob_start();//启动ob缓存
            ob_clean();
            $view = student_view_path.CONTROLLER.'/directory-zone_pc.php';
            load_view_template($view, ['rows' => $rows]);

            $ob_str=ob_get_contents();
            if($rows) {
                file_put_contents($file_name,$ob_str);
                $conf['zoneMatchTime'] = $last_time;
                $conf['zoneMatchTermTime'] = get_time()+3600*24*30;
            }
            file_put_contents($json_file, json_encode($conf));
        }
    }
    /**
     * 脑力健将名录
     */
     public function directoryPlayer(){
         if(is_mobile()){
             $view = student_view_path.CONTROLLER.'/directory-player.php';
             load_view_template($view);
         }else{
             $level = isset($_GET['level']) ? intval($_GET['level']) : 1;
//             $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
//             $page < 1 && $page = 1;
//             $pageSize = 50;
//             $start = ($page-1)*$pageSize;

             $where = " AND d.level='{$level}'";
             $cateArr = getCategory();
             $cateArr = array_column($cateArr,NULL,'ID');

             global $wpdb;
             $level_max = $wpdb->get_var("SELECT MAX(`level`) FROM {$wpdb->prefix}directories");
             $res = $wpdb->get_results("SELECT d.user_id,d.level,d.certificate,p.post_title,d.range,d.category_id,
                    CASE d.range 
                    WHEN 1 THEN '中国' 
                    WHEN 2 THEN '国际' 
                    ELSE '未知' 
                    END AS ranges  
                    FROM {$wpdb->prefix}directories AS d 
                    LEFT JOIN {$wpdb->posts} AS p ON p.ID=d.category_id 
                    WHERE d.is_show=1 AND (d.range=1 or d.range=2) {$where}
                    ORDER BY d.id ASC
                     ", ARRAY_A);
             $rows = [];
             foreach ($res as &$v){
                 $usermeta = get_user_meta($v['user_id'],'', true);
                 $user_real_name = unserialize($usermeta['user_real_name'][0]);
                 if(!$user_real_name){
                     $user_real_name['real_name'] = $usermeta['last_name'][0].$usermeta['first_name'][0];
                 }
                 $v['header_img'] = $usermeta['user_head'][0];
                 $v['userID'] = $usermeta['user_ID'][0];
                 $v['real_name'] = $user_real_name['real_name'];
                 $v['sex'] = $usermeta['user_gender'][0];
                 $v['age'] = $user_real_name['real_age'];
                 $v['user_nationality'] = $usermeta['user_nationality_pic'][0];

                 if(isset($rows[$v['range']])){
                     if(isset($rows[$v['range']][$v['category_id']])){
                         $rows[$v['range']][$v['category_id']][] = $v;
                     }else{
                         $rows[$v['range']][$v['category_id']] = [0 => $v];
                     }
                 }else{
                     $rows[$v['range']] = [$v['category_id'] => [0 => $v]];
                 }
             }
             $view = student_view_path.CONTROLLER.'/directory_pc.php';
             load_view_template($view,['rows'=>$rows, 'max_level' => $level_max, 'current_level' => $level, 'cateArr' => $cateArr]);
         }
    }
        /**
     * 记忆水平认证名录
     */
    public function directoryRemember(){
        if(is_mobile()){
            $view = student_view_path.CONTROLLER.'/directory-remember.php';
            load_view_template($view);
        }else{
            $this->makStaticHtml('remember_static.html', 'directory-remember_pc.php', 'memory', 'directoryRememberTime', function (){
                global $wpdb;
                $res = $wpdb->get_results("SELECT user_id,`memory` FROM {$wpdb->prefix}user_skill_rank WHERE skill_type=1 AND `memory`>0", ARRAY_A);
                $rows = [];
                foreach ($res as $k => $row){
                    $user_meta = get_user_meta($row['user_id']);
                    $row['userID'] = isset($user_meta['user_ID']) ? $user_meta['user_ID'][0] : '';
                    $row['real_name'] = isset($user_meta['user_real_name']) ? (isset(unserialize($user_meta['user_real_name'][0])['real_name'])?unserialize($user_meta['user_real_name'][0])['real_name']:'') : '';
                    $row['real_age'] = isset($user_meta['user_real_name']) ? (isset(unserialize($user_meta['user_real_name'][0])['real_age'])?unserialize($user_meta['user_real_name'][0])['real_age']:'') : '';
                    $row['user_sex'] = isset($user_meta['user_gender']) ? $user_meta['user_gender'][0] : '';
                    $row['user_nationality'] = $user_meta['user_nationality_pic'][0];
                    if($row['real_name'] == '') continue;
                    if(isset($rows[$row['memory']])){
                        $rows[intval($row['memory'])][] = $row;
                    }else{
                        $rows[intval($row['memory'])] = [0 => $row];
                    }
                }
                ksort($rows);
                return ['rows' => $rows];
            });
        }
    }
        /**
     * 速读水平认证名录
     */
    public function directoryRead(){
        if(is_mobile()){
            $view = student_view_path.CONTROLLER.'/directory-read.php';
            load_view_template($view);
        }else{
            $this->makStaticHtml('read_static.html', 'directory-read_pc.php', 'reading', 'readRememberTime', function (){
                global $wpdb;
                $res = $wpdb->get_results("SELECT user_id,`read` FROM {$wpdb->prefix}user_skill_rank WHERE skill_type=1 AND `read`>0", ARRAY_A);
                $rows = [];
                foreach ($res as $k => $row){
                    $user_meta = get_user_meta($row['user_id']);
                    $row['userID'] = isset($user_meta['user_ID']) ? $user_meta['user_ID'][0] : '';
                    $row['real_name'] = isset($user_meta['user_real_name']) ? (isset(unserialize($user_meta['user_real_name'][0])['real_name'])?unserialize($user_meta['user_real_name'][0])['real_name']:'') : '';
                    $row['real_age'] = isset($user_meta['user_real_name']) ? (isset(unserialize($user_meta['user_real_name'][0])['real_age'])?unserialize($user_meta['user_real_name'][0])['real_age']:'') : '';
                    $row['user_sex'] = isset($user_meta['user_gender']) ? $user_meta['user_gender'][0] : '';
                    $row['user_nationality'] = $user_meta['user_nationality_pic'][0];
                    if($row['real_name'] == '') continue;
                    $rows[] = $row;
                }
                return ['rows' => $rows];
            });
        }
    }
        /**
     * 心算水平认证名录
     */
    public function directoryCalculation(){
         if(is_mobile()){
             $view = student_view_path.CONTROLLER.'/directory-calculation.php';
             load_view_template($view);
         }else{
             $this->makStaticHtml('calculation_static.html', 'directory-calculation_pc.php', 'arithmetic', 'calculationRememberTime', function (){
                 global $wpdb;
                 $res = $wpdb->get_results("SELECT user_id,`compute` FROM {$wpdb->prefix}user_skill_rank WHERE skill_type=1 AND `compute`>0", ARRAY_A);
                 $rows = [];
                 foreach ($res as $k => $row){
                     $user_meta = get_user_meta($row['user_id']);
                     $row['userID'] = isset($user_meta['user_ID']) ? $user_meta['user_ID'][0] : '';
                     $row['real_name'] = isset($user_meta['user_real_name']) ? (isset(unserialize($user_meta['user_real_name'][0])['real_name'])?unserialize($user_meta['user_real_name'][0])['real_name']:'') : '';
                     $row['real_age'] = isset($user_meta['user_real_name']) ? (isset(unserialize($user_meta['user_real_name'][0])['real_age'])?unserialize($user_meta['user_real_name'][0])['real_age']:'') : '';
                     $row['user_sex'] = isset($user_meta['user_gender']) ? $user_meta['user_gender'][0] : '';
                     $row['user_nationality'] = $user_meta['user_nationality_pic'][0];
                     if($row['real_name'] == '') continue;
                     $rows[] = $row;
                 }
                 return ['rows' => $rows];
             });
         }
    }

    /**
     * $cate_alias memory(记) reading(读) arithmetic(算)
     * 静态页生成
     */
    public function makStaticHtml($file_name,$view_name, $cate_alias, $conf_name,$getDataCollback){
        $dir = student_view_path.CONTROLLER.'/static';
        if(!is_dir($dir)){
            mkdir($dir);
        }
        $file_name = $dir.'/'.$file_name;
        // && filemtime($file_name)+30>=time()
        $json_file = $dir.'/directory.json';
        global $wpdb;
        $cate_id = 0;
        $categoryArr = getCategory();
        foreach ($categoryArr as $cv){
            if($cv['alis'] == $cate_alias){
                $cate_id = $cv['ID'];
                break;
            }
        }
        //查询最后一次过级时间
        $last_time = $wpdb->get_var("SELECT MAX(gl.created_time) FROM {$wpdb->prefix}grading_logs AS gl
                         LEFT JOIN {$wpdb->prefix}grading_meta AS gm ON gm.grading_id=gl.grading_id 
                         WHERE gl.grading_result=1 AND gm.category_id='{$cate_id}'");
        if(file_exists($json_file)){
            $conf = json_decode(file_get_contents($json_file), true);
        }else{
            $conf[$conf_name] = '';
        }
        if($conf[$conf_name] == $last_time && file_exists($file_name)){
            echo file_get_contents($file_name);
            exit;
        }else{
            $data = $getDataCollback();
            ob_start();//启动ob缓存
            ob_clean();
            load_view_template(student_view_path.CONTROLLER.'/'.$view_name, $data);
            $ob_str=ob_get_contents();
            if($data) {
                file_put_contents($file_name,$ob_str);
                $conf[$conf_name] = $last_time;
            }else{
                $conf[$conf_name] = '';
            }
            file_put_contents($json_file, json_encode($conf));
        }
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        if ((ACTION == 'directoryZone_pc' || ACTION == 'directoryPlayer' || ACTION == 'directoryRead' || ACTION == 'directoryRemember' || ACTION == 'directoryCalculation') && !is_mobile()) {
            wp_register_style( 'my-student-directory_pc', student_css_url.'directory/directory_pc.css',array('my-student') );
            wp_enqueue_style( 'my-student-directory_pc' );
        }else{
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-home', student_css_url.'home-student.css' );
            wp_enqueue_style( 'my-student-home' );
            wp_register_style( 'my-student-directory', student_css_url.'directory/directory.css' ,array('my-student'));
            wp_enqueue_style( 'my-student-directory' ); 
        }
    }
}