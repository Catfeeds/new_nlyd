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
                    ORDER BY d.id DESC
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
        $view = student_view_path.CONTROLLER.'/directory-remember.php';
        load_view_template($view);

    }
        /**
     * 速读水平认证名录
     */
     public function directoryRead(){
        $view = student_view_path.CONTROLLER.'/directory-read.php';
        load_view_template($view);

    }
        /**
     * 心算水平认证名录
     */
     public function directoryCalculation(){
        $view = student_view_path.CONTROLLER.'/directory-calculation.php';
        load_view_template($view);

    }

    /**
     * 名录脑力健将pc
     */
     public function directory_pc(){
        $view = student_view_path.CONTROLLER.'/directory_pc.php';
        load_view_template($view);
    }
    /**
     * 名录速读pc
     */
     public function directoryRead_pc(){
        $view = student_view_path.CONTROLLER.'/directory-read_pc.php';
        load_view_template($view);
    }
    /**
     * 名录记忆pc
     */
     public function directoryRemember_pc(){
        $view = student_view_path.CONTROLLER.'/directory-remember_pc.php';
        load_view_template($view);
    }
    /**
     * 名录心算pc
     */
     public function directoryCalculation_pc(){
        $view = student_view_path.CONTROLLER.'/directory-calculation_pc.php';
        load_view_template($view);
    }
    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        if (ACTION == 'directoryPlayer' || ACTION == 'directoryRead_pc' || ACTION == 'directoryRemember_pc' || ACTION == 'directoryCalculation_pc') {
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