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

        global $wpdb;
        $wpdb->query('START TRANSACTION');
        //查询原数据
        $zone_meta = $wpdb->get_results("SELECT user_id,type_id FROM {$wpdb->prefix}zone_meta WHERE user_id IN({$user_id}) AND user_status=-1",ARRAY_A);
        if(!$zone_meta) wp_send_json_error(['info' => '未找到申请记录!']);
        if($type == 'agree'){//同意申请
            $user_status = 1;
            foreach ($zone_meta as $zmv){
                //主体类型
                $orgType = $wpdb->get_var("SELECT zone_type_alias FROM {$wpdb->prefix}zone_type WHERE id='{$zmv['type_id']}'");
                $spread_set = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}spread_set WHERE spread_type='{$orgType}' AND spread_status=1", ARRAY_A);
                if($spread_set){
                    //添加上级收益
                    //获取一级上级
                    $referee_id1 = $wpdb->get_var("SELECT referee_id FROM {$wpdb->users} WHERE ID='{$zmv['user_id']}'");
                    $referee_id2 = 0;
                    if($referee_id1 > 0) $referee_id2 = $wpdb->get_var("SELECT referee_id FROM {$wpdb->users} WHERE ID='{$referee_id1}'");
                    //添加分成记录
                    $insertData3 = [
                        'income_type' => 'subject',
                        'user_id' => $zmv['user_id'],
                        'referee_id' => $referee_id1,
                        'referee_income' => $spread_set['direct_superior'],
                        'indirect_referee_id' => $referee_id2 > 0 ? $referee_id2 : 0,
                        'indirect_referee_income' => $referee_id2 > 0 ? $spread_set['indirect_superior'] : 0,
                        'income_status' => 2,
                        'created_time' => get_time('mysql'),
                    ];
                    $bool = $wpdb->insert($wpdb->prefix.'user_income_logs',$insertData3);
                    if(!$bool) {
                        $wpdb->query('ROLLBACK');
                        wp_send_json_error(['info' => '添加分成记录失败!']);
                    }
                    $user_income_logs_id = $wpdb->insert_id;

                    if($referee_id1 > 0){
                        //添加一级上级收益流水
                        $insertData1 = [
                            'user_id' => $referee_id1,
                            'user_type' => 2,
                            'match_id' => $user_income_logs_id,
                            'income_type' => 'subject',
                            'user_income' => $spread_set['direct_superior'],
                            'created_time' => get_time('mysql'),
                        ];
                        $bool = $wpdb->insert($wpdb->prefix.'user_stream_logs',$insertData1);
                        if(!$bool) {
                            $wpdb->query('ROLLBACK');
                            wp_send_json_error(['info' => '添加直接上级收益失败!']);
                        }

                        //获取二级上级
                        if($referee_id2 > 0){
                            //添加二级上级收益流水
                            $insertData2 = [
                                'user_id' => $referee_id2,
                                'user_type' => 2,
                                'match_id' => $user_income_logs_id,
                                'income_type' => 'subject',
                                'user_income' => $spread_set['indirect_superior'],
                                'created_time' => get_time('mysql'),
                            ];
                            $bool = $wpdb->insert($wpdb->prefix.'user_stream_logs',$insertData2);
                            if(!$bool) {
                                $wpdb->query('ROLLBACK');
                                wp_send_json_error(['info' => '添加间接上级收益失败!']);
                            }
                        }
                    }
                }
            }


        }elseif ($type == 'refuse'){//拒绝申请
            $user_status = -2;
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '参数错误!']);
        }

        //审核时间
        $apply_date = get_time('mysql');
        $bool = $wpdb->query("UPDATE `{$wpdb->prefix}zone_meta` SET `user_status` = '{$user_status}',`audit_time` = '{$apply_date}' WHERE user_id IN({$user_id}) AND user_status=-1");
        if($bool) {
            $wpdb->query('COMMIT');
            wp_send_json_success(['info' => '操作成功!']);
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '操作失败!']);
        }

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
        $sql = "DELETE FROM {$wpdb->prefix}spread_set WHERE id='{$id}'";
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
        $powerList = $wpdb->get_results("SELECT role_id FROM {$wpdb->prefix}zone_join_role WHERE zone_type_id='{$type_id}'", ARRAY_A);
        $powerList = array_reduce($powerList, function($ids, $value){
            return array_merge($ids, array_values($value));
        }, array());
        if($powerList) wp_send_json_success(['data'=>$powerList]);
        else wp_send_json_error(['info' => '获取权限失败!']);

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

    /**
     * 修改提现记录发放状态
     */
    public function updateExtractStatus(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
        if($id < 1 || $status < 1) wp_send_json_error(['info' => '参数错误!']);
        global $wpdb,$current_user;
        $bool = $wpdb->update($wpdb->prefix.'user_extract_logs',['censor_user_id'=>$current_user->ID,'extract_status'=>$status,'censor_time'=>get_time('mysql')],['id'=>$id]);
        if($bool) wp_send_json_success(['info' => '操作成功!']);
        else wp_send_json_error(['info' => '操作失败!']);
    }

    /**
     * 修改用户单收益记录确认状态
     */
    public function updateIncomeLogsStatus(){
        $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
        $id = isset($_POST['id']) ? trim($_POST['id']) : '';
        if($status !== 2 || $id == '') wp_send_json_error(['info' => '参数错误!']);

        global $wpdb;
        //获取数据
        $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}user_income_logs WHERE income_type NOT IN ('match','grading') AND id IN({$id}) AND income_status=1", ARRAY_A);
        if(!$rows) wp_send_json_error(['info' => '无待确认数据!']);
        $wpdb->query('START TRANSACTION');
        $sql = "UPDATE {$wpdb->prefix}user_income_logs SET income_status='{$status}' WHERE income_type NOT IN ('match','grading') AND id IN({$id}) AND income_status=1";
        $bool = $wpdb->query($sql);
//        echo $wpdb->last_query;
        if(!$bool) {
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '修改失败!']);
        }
        //添加收益记录
        foreach ($rows as $row){
            $insertData3 = [
                'income_type' => $row['income_type'],
                'match_id' => $row['id'],
                'created_time' => get_time('mysql'),
            ];
            //直接上级
            if($row['referee_id'] > 0 && $row['referee_income'] != null && $row['referee_income'] != 0){
                $insertData3['user_income'] = $row['referee_income'];
                $insertData3['user_id'] = $row['referee_id'];
                $insertData3['user_type'] = 2;
                $stream_logs_bool = $wpdb->insert($wpdb->prefix.'user_stream_logs',$insertData3);
                if(!$stream_logs_bool){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => '直接上级收益添加失败!']);
                }
            }
            //间接上级
            if($row['indirect_referee_id'] > 0 && $row['indirect_referee_income'] != null && $row['indirect_referee_income'] != 0){
                $insertData3['user_income'] = $row['indirect_referee_income'];
                $insertData3['user_id'] = $row['indirect_referee_id'];
                $insertData3['user_type'] = 2;
                $stream_logs_bool = $wpdb->insert($wpdb->prefix.'user_stream_logs',$insertData3);
                if(!$stream_logs_bool){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => '间接上级收益添加失败!']);
                }
            }
            //负责人收益
            if($row['person_liable_id'] > 0 && $row['person_liable_income'] != null && $row['person_liable_income'] != 0){
                $insertData3['user_income'] = $row['person_liable_income'];
                $insertData3['user_id'] = $row['person_liable_id'];
                $insertData3['user_type'] = 2;
                $stream_logs_bool = $wpdb->insert($wpdb->prefix.'user_stream_logs',$insertData3);
                if(!$stream_logs_bool){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => '负责人收益添加失败!']);
                }
            }
            //主办方收益
            if($row['sponsor_id'] > 0 && $row['sponsor_income'] != null && $row['sponsor_income'] != 0){
                $insertData3['user_income'] = $row['sponsor_income'];
                $insertData3['user_id'] = $row['sponsor_id'];
                $insertData3['user_type'] = 1;
                $stream_logs_bool = $wpdb->insert($wpdb->prefix.'user_stream_logs',$insertData3);
                if(!$stream_logs_bool){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => '主办方收益添加失败!']);
                }
            }
            //事业员收益
            if($row['manager_id'] > 0 && $row['manager_income'] != null && $row['manager_income'] != 0){
                $insertData3['user_income'] = $row['manager_income'];
                $insertData3['user_id'] = $row['manager_id'];
                $insertData3['user_type'] = 2;
                $stream_logs_bool = $wpdb->insert($wpdb->prefix.'user_stream_logs',$insertData3);
                if(!$stream_logs_bool){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => '事业员收益添加失败!']);
                }
            }
        }
        $wpdb->query('COMMIT');
        wp_send_json_success(['info' => '修改成功!']);
    }

    /**
     * 修改赛事考级收益记录确认状态
     */
    public function updateMatchIncomeLogsStatus(){
        $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
        $match_id = isset($_POST['id']) ? trim($_POST['id']) : '';
        if($status !== 2 || $match_id == '') wp_send_json_error(['info' => '参数错误!']);

        global $wpdb;
        //获取数据
        $rows = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}user_income_logs WHERE income_type IN ('match','grading') AND match_id IN({$match_id}) AND income_status=1 LIMIT 1", ARRAY_A);
        if(!$rows) wp_send_json_error(['info' => '无待确认数据!']);
        $sql = "UPDATE {$wpdb->prefix}user_income_logs SET income_status=2 WHERE income_type IN ('match','grading') AND match_id IN({$match_id}) AND income_status=1";
        $bool = $wpdb->query($sql);
        if($bool) wp_send_json_success(['info' => '确认成功']);
        else wp_send_json_error(['info' => '确认失败']);
    }

    /**
     * 删除主体类型
     */
    public function delZoneType(){
        $ids = isset($_POST['id']) ? trim($_POST['id']) : '';
        if($ids == '') wp_send_json_error(['info' => '参数错误!']);
        //查询需要修改的数据
        global $wpdb;
        $rows = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}zone_type WHERE id IN({$ids})", ARRAY_A);
        if(!$rows) wp_send_json_error(['info' => '无可删除数据']);
        //是否有主体,有主体的类型不可删除
        $organize = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}zone_meta WHERE type_id IN({$ids})");
        if($organize) wp_send_json_error(['info' => '当前选择的类型中已有主体存在! 不可删除']);

        //删除
        $bool = $wpdb->query("DELETE FROM {$wpdb->prefix}zone_type WHERE id IN({$ids})");
        if($bool) wp_send_json_success(['info' => '删除成功!']);
        else wp_send_json_error(['info' => '删除失败!']);
    }
}

new Fission_Ajax();