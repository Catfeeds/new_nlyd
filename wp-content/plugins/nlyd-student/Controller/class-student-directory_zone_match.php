<?php
/**
 * 首页-赛区名录
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/25
 * Time: 14:43
 */

class Student_Directory_Zone_Match
{

    public $template_dir;
    public function __construct()
    {
        $this->template_dir = student_view_path.'directory/';
        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));
        //添加短标签
        add_shortcode('directory_zone_match-home',array($this,'index'));
    }

    public function index(){
        $dir = $this->template_dir.'static';
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
        $view = $this->template_dir.'/directory-zone_pc.php';
        $type_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}zone_type WHERE zone_type_alias='match'");
        $current_time = get_time('mysql');
        $last_time = $wpdb->get_var("SELECT MAX(audit_time) FROM {$wpdb->prefix}zone_meta WHERE type_id='{$type_id}' AND user_id>0 AND user_status=1 AND is_able=1 AND (term_time>'{$current_time}' OR term_time='')");

        if($conf['zoneMatchTime'] == $last_time && $conf['zoneMatchTermTime'] > $current_time && file_exists($file_name)){
            $html = file_get_contents($file_name);
            load_view_template($view, ['html' => $html]);
            exit;
        }else{
            $res = $wpdb->get_results("SELECT zm.zone_number,zm.bank_card_name,zm.chairman_id,zm.secretary_id,zm.zone_city,zm.zone_name,zt.zone_type_alias,zm.zone_match_type,zm.zone_number 
                   FROM {$wpdb->prefix}zone_meta AS zm 
                   LEFT JOIN {$wpdb->prefix}zone_type AS zt ON zt.id=zm.type_id 
                   WHERE zm.type_id='{$type_id}' AND zm.user_id>0 AND zm.user_status=1 AND zm.is_able=1 AND (zm.term_time>'{$current_time}' OR zm.term_time='')", ARRAY_A);
            $rows = [];
            $organizeClass = new Organize();
            foreach ($res as $re){
                $re['zone_title_name'] = $organizeClass->echoZoneName($re['zone_type_alias'], $re['zone_city'], $re['zone_name'], $re['zone_match_type'],$re['zone_number'], 'get', '#ffb536');
                $re['chairman_name'] = get_user_meta($re['chairman_id'], 'user_real_name', true)['real_name'];
                $re['secretary_name'] = get_user_meta($re['secretary_id'], 'user_real_name', true)['real_name'];
                $rows[] = $re;
            }
            $tmpFile = fopen($file_name, 'a+');
            fwrite($tmpFile, '<div class="layui-fluid nl-content">
                <div class="layui-row">
                <div class="layui-row mt_10">
                <div class="layui-row dir_nav_content active">');
            foreach ($rows as $row){

                $str = '<div class="dir_zone_table">
                    <div class="dir_table_name">'.$row['zone_title_name'].'</div>
                    <div class="dir_tab_wap">
                        <div class="dir_tab_row">
                            <div class="dir_tab_label">'.__('编 号', 'nlyd-student').'：</div>
                            <div class="dir_tab_info">'.$row['zone_number'].'</div>
                        </div>
                        <div class="dir_tab_row">
                            <div class="dir_tab_label">'.__('承办单位', 'nlyd-student').'：</div>
                            <div class="dir_tab_info">'.$row['bank_card_name'].'</div>
                        </div>
                        <div class="dir_tab_row">
                            <div class="dir_tab_label">'.__('主 席', 'nlyd-student').'：</div>
                            <div class="dir_tab_info">'.$row['chairman_name'].'</div>
                        </div>
                        <div class="dir_tab_row">
                            <div class="dir_tab_label">'.__('秘书长', 'nlyd-student').'：</div>
                            <div class="dir_tab_info">'.$row['secretary_name'].'</div>
                        </div>
                    </div>
                </div>';

                fwrite($tmpFile, $str);
                }

            fwrite($tmpFile, ' </div>
                </div>
                </div>
                </div>');
            fclose($tmpFile);
//            leo_dump($rows);die;
            ob_start();//启动ob缓存
            ob_clean();
            $html = file_get_contents($file_name);
            load_view_template($view, ['html' => $html]);

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
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_style( 'my-student-directory_pc', student_css_url.'directory/directory_pc.css',array('my-student') );
        wp_enqueue_style( 'my-student-directory_pc' );
    }
}