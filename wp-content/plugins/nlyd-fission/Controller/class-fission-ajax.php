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
     * 删除机构成员
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
     * 通过/拒绝机构账号申请
     */
    public function editOrganizeApply(){
        die;
        $id = isset($_POST['id']) ? trim($_POST['id']) : '';
        if($id == '') wp_send_json_error(['info' => '参数错误!']);
        $type = isset($_POST['request_type']) ? trim($_POST['request_type']) : '';

        global $wpdb;
        $wpdb->query('START TRANSACTION');
        //查询原数据
        $zone_meta = $wpdb->get_results("SELECT user_id,type_id,id,apply_id FROM {$wpdb->prefix}zone_meta WHERE id IN({$id}) AND user_status=-1",ARRAY_A);
        if(!$zone_meta) wp_send_json_error(['info' => '未找到申请记录!']);
        if($type == 'agree'){//同意申请
            $user_status = 1;
            $sendMsgArr = [];
            foreach ($zone_meta as $zmv){
                //创建新账号
                $user_email = $zmv['apply_id'].rand(000,999).date('is', get_time()).'@gjnlyd.com';
                $user_password = '123456';
                $user_id = wp_create_user($user_email,$user_password,$user_email);
                if(!$user_id) wp_send_json_error(['info' => '创建账号失败!']);
                //更新新账号推荐人和推荐时间
                $apply_user = $wpdb->get_row("SELECT referee_id,user_mobile FROM {$wpdb->users} WHERE ID='{$zmv['apply_id']}'", ARRAY_A);
                $referee_id = $apply_user['referee_id'];
                if($referee_id > 0){
                    if(!$wpdb->update($wpdb->users,['referee_id' => $referee_id,'referee_time'=>get_time('mysql')],['ID' => $user_id])) wp_send_json_error(['info' => '更新机构推荐人失败!']);
                }
                //跟新机构所有者
                if(!$wpdb->update($wpdb->prefix.'zone_meta',['user_id' => $user_id],['id'=>$zmv['id']])) wp_send_json_error(['info' => '更新机构所有者id失败!']);
//                $wpdb->update($wpdb->users,['referee_id' => ])
                //添加机构管理员
                if(!$wpdb->insert($wpdb->prefix.'zone_manager',['zone_id' => $zmv['id'], 'user_id' => $zmv['apply_id']])) wp_send_json_error(['info' => '添加管理员失败!']);
                //机构类型
                $orgType = $wpdb->get_row("SELECT zone_type_alias,zone_type_name FROM {$wpdb->prefix}zone_type WHERE id='{$zmv['type_id']}'", ARRAY_A);
                $spread_set = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}spread_set WHERE spread_type='{$orgType['zone_type_alias']}' AND spread_status=1", ARRAY_A);
                if($spread_set){
                    //添加上级收益
                    //获取一级上级
                    $referee_id1 = $wpdb->get_var("SELECT referee_id FROM {$wpdb->users} WHERE ID='{$user_id}'");
                    $referee_id2 = 0;
                    if($referee_id1 > 0) $referee_id2 = $wpdb->get_var("SELECT referee_id FROM {$wpdb->users} WHERE ID='{$referee_id1}'");
                    //添加分成记录
                    $insertData3 = [
                        'income_type' => 'subject',
                        'user_id' => $zmv['apply_id'],
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
//                            'user_type' => 2,
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
//                                'user_type' => 2,
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
                //发送短信通知申请人并给出机构的账号密码
                $sendMsgArr[] = ['user_mobile'=>$apply_user['user_mobile'],'type_name'=>$orgType['zone_type_name'],'user_login'=>$user_email,'password'=>$user_password];
            }
        }elseif ($type == 'refuse'){//拒绝申请
            $user_status = -2;
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '参数错误!']);
        }

        //审核时间
        $apply_date = get_time('mysql');
        $bool = $wpdb->query("UPDATE `{$wpdb->prefix}zone_meta` SET `user_status` = '{$user_status}',`audit_time` = '{$apply_date}' WHERE id IN({$id}) AND user_status=-1");
        if($bool) {
            $wpdb->query('COMMIT');
            if(isset($sendMsgArr) && $sendMsgArr != []){
                $ali = new AliSms();
                foreach ($sendMsgArr as $smav){
                    $ali->sendSms($smav['user_mobile'], 6, array('type_name'=>$smav['type_name'], 'user_login' => $smav['user_login'], 'password' => $smav['password']));
                }
            }
            wp_send_json_success(['info' => '操作成功!']);
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '操作失败!']);
        }

    }
    /**
     * 冻结/解冻机构账号
     */
    public function editOrganizeAble(){
        $id = isset($_POST['id']) ? trim($_POST['id']) : '';
        if($id == '') wp_send_json_error(['info' => '参数错误!']);
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
        $zone_meta_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}zone_meta WHERE id IN({$id}) AND user_status=1");
        if(!$zone_meta_id || $zone_meta_id == '') wp_send_json_error(['info' => '未找到可操作机构!']);
        $bool = $wpdb->query("UPDATE `{$wpdb->prefix}zone_meta` SET `is_able` = '{$is_able}' WHERE id IN({$id}) AND user_status=1");
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
     * 搜索机构列表
     */
    public function get_base_zone_list(){
        $searchStr = isset($_GET['term']) ? trim($_GET['term']) : '';
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';
        $rows = [];
        if($searchStr != ''){
            global $wpdb;
            $rows = $wpdb->get_results("SELECT zm.user_id AS id,zm.zone_name AS text FROM {$wpdb->prefix}zone_meta AS zm
                    LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=zm.chairman_id AND um.meta_key='user_real_name'
                    WHERE (zm.zone_name LIKE '%{$searchStr}%' OR um.meta_value LIKE '%{$searchStr}%') AND zm.user_id!='' AND zm.user_status=1");
        }
        if($type == 'all_base'){
            $rows[] = ['id' => 0, 'text' => '平台'];
        }elseif($type == 'parent'){
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
        $wpdb->query('START TRANSACTION');
        if($status === 2){
            //查询当前提现记录
            $user_extract_logs = $wpdb->get_row("SELECT extract_id,extract_amount,extract_type,stream_log_id FROM {$wpdb->prefix}user_extract_logs WHERE id='{$id}'", ARRAY_A);
            //判断金额是否足够
            $money = $wpdb->get_var("SELECT SUM(user_income) FROM {$wpdb->prefix}user_stream_logs WHERE user_id='{$user_extract_logs['extract_id']}' AND id != '{$user_extract_logs['stream_log_id']}'");
            if($money < $user_extract_logs['extract_amount']) wp_send_json_error(['info' => '用户余额不足!']);
            //通过,增加收益记录
            //查询机构
//            $type_id = $wpdb->get_var("SELECT type_id FROM {$wpdb->prefix}zone_meta WHERE user_id='{$user_extract_logs['extract_id']}'");
//            $insertData = [
//                'user_id'=> $user_extract_logs['extract_id'],
//                'user_type'=> $type_id,
//                'income_type'=> 'extract',
//                'user_income'=> -1*$user_extract_logs['extract_amount'],
//                'extract_type'=> $user_extract_logs['extract_type'],
//                'created_time' => get_time('mysql')
//            ];
            $bool = $wpdb->update($wpdb->prefix.'user_stream_logs',['extract_type' => $user_extract_logs['extract_type']], ['id' => $user_extract_logs['stream_log_id']]);
//            leo_dump($wpdb->last_query);die;
            if(!$bool){
                $wpdb->query('ROLLBACK');
                wp_send_json_error(['info' => '添加流水记录失败!']);
            }
        }
        $bool = $wpdb->update($wpdb->prefix.'user_extract_logs',['censor_user_id'=>$current_user->ID,'extract_status'=>$status,'censor_time'=>get_time('mysql')],['id'=>$id]);
        if($bool) {
            $wpdb->query('COMMIT');
            wp_send_json_success(['info' => '操作成功!']);
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '操作失败!']);
        }
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
        $rows = $wpdb->get_results("SELECT match_id FROM {$wpdb->prefix}user_income_logs WHERE income_type NOT IN ('match','grading') AND id IN({$id}) AND income_status=1", ARRAY_A);
        if(!$rows) wp_send_json_error(['info' => '无待确认数据!']);
        $wpdb->query('START TRANSACTION');
        $sql = "UPDATE {$wpdb->prefix}user_income_logs SET income_status='{$status}' WHERE income_type NOT IN ('match','grading') AND id IN({$id}) AND income_status=1";
        $bool = $wpdb->query($sql);
//        echo $wpdb->last_query;
        if(!$bool) {
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '修改失败!']);
        }
        //修改收益记录状态
        $match_id = [];
        foreach ($rows as $row){
            if($row['match_id'] > 0) $match_id[] = $row['match_id'];
        }
        $sql = "UPDATE {$wpdb->prefix}user_stream_logs SET `income_status`=2 WHERE match_id IN({$match_id}) AND income_type NOT IN ('recommend_trains_zone','recommend_test_zone','recommend_match_zone')";
        $user_stream_logs_bool = $wpdb->query($sql);
        if(!$user_stream_logs_bool){
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '修改失败!']);
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
        $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}user_income_logs WHERE income_type IN ('match','grading') AND match_id IN({$match_id}) AND income_status=1", ARRAY_A);
        if(!$rows) wp_send_json_error(['info' => '无待确认数据!']);
        $sql = "UPDATE {$wpdb->prefix}user_income_logs SET income_status=2 WHERE income_type IN ('match','grading') AND match_id IN({$match_id}) AND income_status=1";
        $wpdb->query('START TRANSACTION');
        $bool = $wpdb->query($sql);
        if(!$bool){
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '确认失败']);
        }
        //添加收益流水
//        $sql2 = "INSERT INTO `{$wpdb->prefix}user_stream_logs` (`user_id`,`income_type`,`match_id`,`user_income`,`created_time`,`income_status`) VALUES";
//        $insertDataArr = [];
//        $created_time = get_time('mysql');
//        foreach ($rows as $row){

//            //直接上街
//            if($row['referee_id'] && $row['referee_income'] != '0' && $row['referee_income'] != NULL){
//                $income_type = $row['income_type'] == 'match' ? 'recommend_match' : 'recommend_grading';
//                $insertDataArr[] = "('{$row['referee_id']}','{$income_type}','{$row['match_id']}','{$row['referee_income']}','{$created_time}',2)";
//            }
//            //间接上级
//            if($row['indirect_referee_id'] && $row['indirect_referee_income'] != '0' && $row['indirect_referee_income'] != NULL){
//                $income_type = $row['income_type'] == 'match' ? 'recommend_match' : 'recommend_grading';
//                $insertDataArr[] = "('{$row['indirect_referee_id']}','{$income_type}','{$row['match_id']}','{$row['indirect_referee_income']}','{$created_time}',2)";
//            }
//            //负责人
//            if($row['person_liable_id'] && $row['person_liable_income'] != '0' && $row['person_liable_income'] != NULL){
//                $income_type = $row['income_type'] == 'match' ? 'director_match' : 'director_grading';
//                $insertDataArr[] = "('{$row['person_liable_id']}','{$income_type}','{$row['match_id']}','{$row['person_liable_income']}','{$created_time}',2)";
//            }
//            //主办方
//            if($row['sponsor_id'] && $row['sponsor_income'] != '0' && $row['sponsor_income'] != NULL){
//                $income_type = $row['income_type'] == 'match' ? 'open_match' : 'open_grading';
//                $insertDataArr[] = "('{$row['sponsor_id']}','{$income_type}','{$row['match_id']}','{$row['sponsor_income']}','{$created_time}',2)";
//            }
//            //事业员
//            if($row['manager_id'] && $row['manager_income'] != '0' && $row['manager_income'] != NULL){
//                $insertDataArr[] = "('{$row['manager_id']}','cause_manager','{$row['match_id']}','{$row['manager_income']}','{$created_time}',2)";
//            }
//            //事业管理员
//            if($row['indirect_manager_id'] && $row['indirect_manager_income'] != '0' && $row['indirect_manager_income'] != NULL){
//                $insertDataArr[] = "('{$row['indirect_manager_id']}','cause_minister','{$row['match_id']}','{$row['indirect_manager_income']}','{$created_time}',2)";
//            }
//        }
//        $sql2 .= join(',', $insertDataArr);
//        $insertBool = $wpdb->query($sql2);
        //修改收益流水状态
        $sql = "UPDATE {$wpdb->prefix}user_stream_logs SET `income_status`=2 WHERE match_id IN({$match_id}) AND income_type NOT IN ('recommend_trains_zone','recommend_test_zone','recommend_match_zone')";
        $user_stream_logs_bool = $wpdb->query($sql);
        if($user_stream_logs_bool){
            $wpdb->query('COMMIT');
            wp_send_json_success(['info' => '确认成功']);
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '确认失败']);
        }
    }

    /**
     * 删除机构类型
     */
    public function delZoneType(){
        $ids = isset($_POST['id']) ? trim($_POST['id']) : '';
        if($ids == '') wp_send_json_error(['info' => '参数错误!']);
        //查询需要修改的数据
        global $wpdb;
        $rows = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}zone_type WHERE id IN({$ids})", ARRAY_A);
        if(!$rows) wp_send_json_error(['info' => '无可删除数据']);
        //是否有机构,有机构的类型不可删除
        $organize = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}zone_meta WHERE type_id IN({$ids})");
        if($organize) wp_send_json_error(['info' => '当前选择的类型中已有机构存在! 不可删除']);

        //删除
        $bool = $wpdb->query("DELETE FROM {$wpdb->prefix}zone_type WHERE id IN({$ids})");
        if($bool) wp_send_json_success(['info' => '删除成功!']);
        else wp_send_json_error(['info' => '删除失败!']);
    }

    /**
     * 添加机构管理员
     */
    public function addZoneAdmin(){
        $zid = isset($_POST['zid']) ? intval($_POST['zid']) : 0;
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
        if($zid < 1 || $uid < 1) wp_send_json_error(['info' => '参数错误!']);
        global $wpdb;
        $bool = $wpdb->insert($wpdb->prefix.'zone_manager', ['zone_id' => $zid, 'user_id' => $uid]);
        if($bool) wp_send_json_success(['info' => '添加成功!']);
        else wp_send_json_error(['info' => '添加失败!']);
    }

    /**
     * 删除机构管理员
     */
    public function removeZoneAdmin(){
        $zid = isset($_POST['zid']) ? intval($_POST['zid']) : 0;
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
        if($zid < 1 || $uid < 1) wp_send_json_error(['info' => '参数错误!']);
        global $wpdb;
        $bool = $wpdb->delete($wpdb->prefix.'zone_manager', ['zone_id' => $zid, 'user_id' => $uid]);
        if($bool) wp_send_json_success(['info' => '删除成功!']);
        else wp_send_json_error(['info' => '删除失败!']);
    }

    /**
     * 删除课程类型
     */
    public function delCourseType(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $id < 1 && wp_send_json_error(['info' => '参数错误!']);
        global  $wpdb;
        //是否存在课程
        $var = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}course WHERE course_type='{$id}'");
        if($var) wp_send_json_error(['info' => '当前课程类型已存在课程,无法删除!']);
        $bool = $wpdb->delete($wpdb->prefix.'course_type', ['id' => $id]);
        if($bool) wp_send_json_success(['info' => '删除成功!']);
        else wp_send_json_error(['info' => '删除失败!']);
    }

    /**
     * 获取无机构的教练
     */
    public function get_not_zone_coach(){
        global $wpdb;
        $s = isset($_GET['term']) ? trim($_GET['term']) : '';
        if($s != ''){
            $rows = $wpdb->get_results("SELECT cs.coach_id AS id,um.meta_value FROM {$wpdb->prefix}coach_skill AS cs 
                    LEFT JOIN {$wpdb->prefix}zone_join_coach AS zjc ON zjc.coach_id=cs.coach_id
                    LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=cs.coach_id AND um.meta_key='user_real_name'
                    WHERE um.meta_value LIKE '%{$s}%' AND um.user_id>0 AND zjc.id IS NULL");
            foreach ($rows as &$row){
                $row->text = unserialize($row->meta_value)['real_name'];
            }
            wp_send_json_success($rows);
        }
    }

    /**
     * 添加机构教练
     */
    public function addZoneCoach(){
        $zone_id = isset($_POST['zid']) ? intval($_POST['zid']) : 0;
        $coach_id = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
        if($zone_id < 1 || $coach_id < 1) wp_send_json_error(['info' => '参数错误!']);
        global $wpdb;
        //该教练是否已经存在机构
        $val = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}zone_join_coach WHERE coach_id='{$coach_id}'");
        if($val) wp_send_json_error(['info' => '当前教练已有任职机构']);
        $bool = $wpdb->insert($wpdb->prefix.'zone_join_coach', ['zone_id' => $zone_id, 'coach_id' => $coach_id]);
        if($bool) wp_send_json_success(['info' => '添加成功!']);
        else wp_send_json_error(['info' => '添加失败!']);
    }
}

new Fission_Ajax();