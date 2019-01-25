<?php

/**
 * 首页-脑力健将名录
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/25
 * Time: 14:45
 */
class Student_Directory_Brain
{

    public $directoryClass;
    public $template_dir;
    public function __construct()
    {
        $this->template_dir = student_view_path.'directory/';
        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));
        //添加短标签
        add_shortcode('directory_brain-home',array($this,'index'));
    }

    /**
     * 脑力健将名录
     */
    public function index(){
//        $level = isset($_GET['level']) ? intval($_GET['level']) : 1;
        $a = explode('/level/',$_SERVER['REQUEST_URI']);
        //
        if(count($a) > 1){
            $level = explode('/',$a[1])[0];
        }else{
            $level = 1;
        }

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
        $view = $this->template_dir.'/directory_pc.php';
        load_view_template($view,['rows'=>$rows, 'max_level' => $level_max, 'current_level' => $level, 'cateArr' => $cateArr]);

    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_style( 'my-student-directory_pc', student_css_url.'directory/directory_pc.css',array('my-student') );
        wp_enqueue_style( 'my-student-directory_pc' );
    }
}