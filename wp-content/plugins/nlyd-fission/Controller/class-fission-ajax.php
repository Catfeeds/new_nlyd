<?php
/**
 * Ajax操作类
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/6/22
 * Time: 14:10
 */
use library\AliSms;
use library\TwentyFour;
class Fission_Ajax
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

    /**
     * 搜索用户
     */
    public function get_base_user_list(){
        $searchStr = isset($_GET['term']) ? trim($_GET['term']) : '';
        if($searchStr != ''){
            global $wpdb;
            $type = isset($_GET['type']) ? trim($_GET['type']) : '';
            $zoneJoin = '';
            $zoneWhere = '';
            if($type == 'base'){
                $zoneJoin = "LEFT JOIN `{$wpdb->prefix}zone_meta` AS zm ON zm.user_id=u.ID ";
                $zoneWhere = " AND ISNULL(zm.id)";
            }
            $rows = $wpdb->get_results("SELECT u.user_login,u.user_mobile,um.meta_value AS user_real_name,u.ID FROM `{$wpdb->users}` AS u 
                    LEFT JOIN `{$wpdb->usermeta}` AS um ON um.user_id=u.ID AND um.meta_key='user_real_name' 
                    {$zoneJoin}
                    WHERE (um.meta_value LIKE '%{$searchStr}%' OR u.user_mobile LIKE '%{$searchStr}%') {$zoneWhere}", ARRAY_A);
            $data = [];
            foreach ($rows as $row){
                $add = ['id' => $row['ID']];
                $mobile = $row['user_mobile'] ? '  ('.$row['user_mobile'].')' : '';
                if(empty($rows['user_real_name']) && isset(unserialize($row['user_real_name'])['real_name'])){
                    $add['text'] = unserialize($row['user_real_name'])['real_name'].$mobile;
                }else{
                    $add['text'] = $row['user_login'].$mobile;
                }
                $data[] = $add;
            }
            wp_send_json_success($data);
        }

    }

    /**
     * 删除主体成员
     */
    public function deleteOrganizeMember(){
        $member_id = isset($_POST['member_id']) ? intval($_POST['member_id']) : 0;
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        if($member_id < 1 || $user_id < 1) wp_send_json_error(['info' => '参数错误!']);
        global $wpdb;
        $bool = $wpdb->delete($wpdb->prefix.'zone_join_coach',['coach_id'=>$member_id,'zone_id'=>$user_id]);
        if($bool) wp_send_json_success(['info' => '删除成功!']);
        else wp_send_json_error(['info' => '删除失败!']);
    }

    /**
     * 通过/拒绝主体账号申请
     */
    public function editOrganizeApply(){
        $user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
        if($user_id == '') wp_send_json_error(['info' => '参数错误!']);
        $type = isset($_POST['request_type']) ? trim($_POST['request_type']) : '';
        if($type == 'agree'){//同意申请
            $user_status = 1;
        }elseif ($type == 'refuse'){//拒绝申请
            $user_status = -2;
        }else{
            wp_send_json_error(['info' => '参数错误!']);
        }
        //查询原数据
        global $wpdb;
        $zone_meta_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}zone_meta WHERE user_id IN({$user_id}) AND user_status=-1");
        if(!$zone_meta_id || $zone_meta_id == '') wp_send_json_error(['info' => '未找到申请记录!']);
        //审核时间
        $apply_date = get_time('mysql');
        $bool = $wpdb->query("UPDATE `{$wpdb->prefix}zone_meta` SET `user_status` = '{$user_status}',`audit_time` = '{$apply_date}' WHERE user_id IN({$user_id}) AND user_status=-1");
        if($bool) wp_send_json_success(['info' => '操作成功!']);
        else wp_send_json_error(['info' => '操作失败!']);
    }
    /**
     * 冻结/解冻主体账号
     */
    public function editOrganizeAble(){
        $user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
        if($user_id == '') wp_send_json_error(['info' => '参数错误!']);
        $type = isset($_POST['request_type']) ? trim($_POST['request_type']) : '';
        if($type == 'frozen'){//冻结
            $is_able = 2;
        }elseif ($type == 'thaw'){//解冻
            $is_able = 1;
        }else{
            wp_send_json_error(['info' => '参数错误!']);
        }
        //查询原数据
        global $wpdb;
        $zone_meta_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}zone_meta WHERE user_id IN({$user_id}) AND user_status=1");
        if(!$zone_meta_id || $zone_meta_id == '') wp_send_json_error(['info' => '未找到可操作主体!']);
        $bool = $wpdb->query("UPDATE `{$wpdb->prefix}zone_meta` SET `is_able` = '{$is_able}' WHERE user_id IN({$user_id}) AND user_status=1");
        if($bool) wp_send_json_success(['info' => '操作成功!']);
        else wp_send_json_error(['info' => '操作失败!']);
    }

    /**
     * 删除分成设置
     */
    public function delSpreadSet(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if($id < 1) wp_send_json_error(['info' => '参数错误!']);
        global $wpdb;
        $sql = "DELETE FROM {$wpdb->prefix}spread_set WHERE id='{$id}' OR parent_id='{$id}'";
        $bool = $wpdb->query($sql);
        if($bool) wp_send_json_success(['info'=>'删除成功!']);
        else wp_send_json_error(['info' => '删除失败!']);
    }

    /**
     * 获取机构权限
     */
    public function getPowerListByType(){
        $type_id = isset($_POST['val']) ? intval($_POST['val']) : 0;
        if($type_id < 1) wp_send_json_error(['info' => '参数错误!']);
        global $wpdb;
        $powerList = $wpdb->get_results("SELECT ztr.* FROM {$wpdb->prefix}zone_join_role AS zjr 
                         LEFT JOIN {$wpdb->prefix}zone_type_role AS ztr ON ztr.id=zjr.zone_type_id 
                        WHERE zjr.zone_type_id='{$type_id}'", ARRAY_A);
        wp_send_json_success(['data'=>$powerList]);
    }

    /**
     * 搜索主体列表
     */
    public function get_base_zone_list(){
        $searchStr = isset($_GET['term']) ? trim($_GET['term']) : '';
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';
        $rows = [];
        if($searchStr != ''){
            global $wpdb;
            $rows = $wpdb->get_results("SELECT id,zone_name AS text FROM {$wpdb->prefix}zone_meta WHERE zone_name LIKE '%{$searchStr}%'");
        }
        if($type == 'all_base'){
            $rows[] = ['id' => 0, 'text' => '平台'];
        }else{
            $rows[] = ['id' => 0, 'text' => '无上级'];
        }
        wp_send_json_success($rows);
    }

    /**
     * 搜索教练列表
     */
    public function get_base_coach_list(){
        $searchStr = isset($_GET['term']) ? trim($_GET['term']) : '';
        if($searchStr != ''){
            global $wpdb;
            $type = isset($_GET['type']) ? trim($_GET['type']) : '';
            $rows = $wpdb->get_results("SELECT cs.coach_id AS id,um.meta_value AS user_real_name FROM {$wpdb->prefix}coach_skill AS cs 
                    LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=cs.coach_id AND um.meta_key='user_real_name' 
                    WHERE um.meta_value LIKE '%{$searchStr}%'", ARRAY_A);
            foreach ($rows as &$row){
                if(!empty($row['user_real_name'])){
                    $row['text'] = unserialize($row['user_real_name'])['real_name'];
                }else{
                    unset($row);
                }
            }
            wp_send_json_success($rows);
        }
    }

    /**
     * 启用/禁用课程
     */
    public function ableCourse(){
        $id = isset($_POST['id']) ? trim($_POST['id']) : '';
        $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
        if($id == '' || ($status !== 1 && $status !== 2)){
            wp_send_json_error(['info' => '参数错误']);
        }
        global $wpdb;
        $sql = "UPDATE {$wpdb->prefix}course SET is_enable='{$status}' WHERE id IN({$id})";
        $bool = $wpdb->query($sql);
        if($bool) wp_send_json_success(['info' => '操作成功']);
        else wp_send_json_error(['info' => '操作失败!']);
    }
}

new Fission_Ajax();