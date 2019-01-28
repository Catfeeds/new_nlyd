<?php
/**
 * 速读名录
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/25
 * Time: 14:44
 */
class Student_Directory_D
{

    public $template_dir;
    public function __construct()
    {
        $this->template_dir = student_view_path.'directory/';
        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));
        //添加短标签
        add_shortcode('directory_d-home',array($this,'index'));
    }

    public function index(){
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
            //死数据
            $moreJson = student_view_path.'directory/static/gradingMoreData.json';
            if(file_exists($moreJson)){
                $moreData = json_decode(file_get_contents($moreJson), true);
            }else{
                $moreData = [];
            }
            $cateArr = getCategory();
            $cateId = 0;
            foreach ($cateArr as $a){
                if($a['alis'] == 'reading') {
                    $cateId = $a['ID'];
                    break;
                }
            }
            foreach ($moreData as $mdv){
                if($cateId != $mdv['category_id']) continue;
                $rows[] = [
                    'read' => $mdv['level'],
                    'real_name' => $mdv['real_name'],
                    'real_age' => $mdv['age'],
                    'user_sex' => $mdv['sex'],
                    'user_nationality' => $mdv['user_nationality'],
                ];
            }
            return ['rows' => $rows];
        });
    }

    /**
     * $cate_alias memory(记) reading(读) arithmetic(算)
     * 静态页生成
     */
    public function makStaticHtml($file_name,$view_name, $cate_alias, $conf_name,$getDataCollback){
        $dir = $this->template_dir.'static';
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
            load_view_template($this->template_dir.$view_name, $data);
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
        wp_register_style( 'my-student-directory_pc', student_css_url.'directory/directory_pc.css',array('my-student') );
        wp_enqueue_style( 'my-student-directory_pc' );
    }
}