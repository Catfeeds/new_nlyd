<?php
/**
 * Ajax操作类
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/6/22
 * Time: 14:10
 */
use library\TwentyFour;
class Spread_Ajax
{

    public $redis;
    function __construct() {

        //如果有提交并且提交的内容中有规定的提交数据
        $data = $_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST : $_GET;
        if(empty($data['action']) ) return;
        $action = $data['action'];

        //判斷方法是否存在
        if(!method_exists($this,$action)) return;

        add_action( 'wp_ajax_'.$action,array($this, $action) );
        add_action( 'wp_ajax_nopriv_'.$action,  array($this,$action) );
    }

     public function changeUserSpreadLevel(){
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $type = isset($_POST['type']) ? trim($_POST['type']) : '';
        if($user_id<1 || !isset($_POST['level']) || ($type!='lxl' && $type!='fx')) wp_send_json_error(['info' => '参数错误!']);
        $level = intval($_POST['level']);
        if($level > 2 || $level < 0) wp_send_json_error(['info' => '参数错误!']);
        $res = update_user_meta($user_id,$type.'_level',$level);
        if($res) wp_send_json_success(['info' => '操作成功!']);
        else wp_send_json_error(['info' => '操作失败!']);
    }

    /**
     * 更新添加推广奖金项目设置
     */
    public function updateSpreadMoneySet(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $is_enable = isset($_POST['is_enable']) ? intval($_POST['is_enable']) : 0;
        if($id < 1 && $id != -1 || ($is_enable !=1 && $is_enable != 2)) wp_send_json_error(['info' => '参数错误!']);
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $project = isset($_POST['project']) ? intval($_POST['project']) : '';
        $user_type = isset($_POST['user_type']) ? intval($_POST['user_type']) : '';
        $money = isset($_POST['money']) ? trim($_POST['money']) : '';
        $arr = [
            'money_name' => $name,
            'project_type' => $project,
            'user_type' => $user_type,
            'money' => $money,
            'is_enable' => $is_enable
        ];
        global $wpdb;
        if($id < 1){
            $bool = $wpdb->insert($wpdb->prefix.'spread_money_set',$arr);
        }else{
            $bool = $wpdb->update($wpdb->prefix.'spread_money_set',$arr,['id'=>$id]);
        }
        if($bool) wp_send_json_success(['info' => '操作成功!']);
        else wp_send_json_error(['info' => '操作失败!']);
    }
    /**
     * 删除推广奖金项目设置
     */
    public function deleteSpreadMoneySet(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if($id < 1) wp_send_json_error(['info' => '参数错误!']);
        global $wpdb;
        $bool = $wpdb->delete($wpdb->prefix.'spread_money_set',['id'=>$id]);
        if($bool) wp_send_json_success(['info' => '操作成功!']);
        else wp_send_json_error(['info' => '操作失败!']);
    }

    /**
     * 根据真实姓名搜索教练
     */
    public function search_teacher_list(){
        $searchStr = isset($_GET['term']) ? trim($_GET['term']) : '';
        if($searchStr != ''){
            global $wpdb;
            $rows = $wpdb->get_results("SELECT cs.coach_id AS id,um.meta_value AS user_real_name FROM `{$wpdb->prefix}coach_skill` AS cs 
                    LEFT JOIN `{$wpdb->users}` AS u ON u.ID=cs.coach_id 
                    LEFT JOIN `{$wpdb->usermeta}` AS um ON um.user_id=cs.coach_id AND um.meta_key='user_real_name' 
                    WHERE u.ID!='' AND um.meta_value LIKE '%{$searchStr}%'", ARRAY_A);
            if($rows){
                foreach ($rows as &$row){
                    if($row['user_real_name']){
                        $row['text'] = unserialize($row['user_real_name'])['real_name'];
                        unset($row['user_real_name']);
                    }else{
                        unset($row);
                    }
                }
                wp_send_json_success($rows);
            }
        }
    }

}

new Spread_Ajax();