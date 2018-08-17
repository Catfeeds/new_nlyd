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
class Match_Ajax
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
     * 获取所有题目
     */
    public function get_question_list(){
        global $wpdb;
        $map = array();
        $map[] = ' post_status = "publish" ';
        $map[] = ' post_type = "question" ';
        if(!empty($_GET['term'])){
            $map[] = " post_title like '%{$_GET['term']}%' ";
        }
        if(!empty($_GET['question_id'])){
            $map[] = " ID = {$_GET['question_id']} ";
        }
        $where = join(' and ',$map);
        $sql = "select ID as id,post_title as text from {$wpdb->prefix}posts where {$where} limit 0,10";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        wp_send_json_success($rows);
    }


    /****************************************************以下为后台Ajax功能方法***********************************************************************/

    /**
     * 申请教练审核
     */
    public function coachApplyStatus(){
        global $wpdb;
        $status = intval($_POST['status']);
        if($status == 2){
            $statusName = '通过';
        }elseif ($status == -1){
            $statusName = '被拒绝';
        }
        if(is_array($_POST['id'])){
            $id = '';
            foreach ($_POST['id'] as $v){
                $id.= $v.',';
            }
            $id = substr($id,0,strlen($id)-1);
            $idWhere = 'id IN('.$id.')';
        }else{
            $id = intval($_POST['id']);
            $idWhere = 'id='.$id;
        }
        $user = $wpdb->get_results('SELECT u.user_mobile FROM '.$wpdb->prefix.'my_coach AS m 
            LEFT JOIN '.$wpdb->users.' AS u ON m.user_id=u.ID 
            WHERE m.'.$idWhere.' AND m.`apply_status`=1',ARRAY_A);

        $sql = 'UPDATE '.$wpdb->prefix.'my_coach SET `apply_status`='.$status.' WHERE '.$idWhere.' AND `apply_status`=1';
        if($status != -1  && $status != 2){
            wp_send_json_error(array('info' => '操作失败,状态参数异常'));
        }
        $bool = $wpdb->query($sql);
        if($bool) {
            //TODO 发送通知
            $ali = new AliSms();
            $mobileStr = '';
            foreach ($user as $v){
                $mobileStr .= $v['user_mobile'].',';
            }
            $mobileStr = substr($mobileStr,0,strlen($mobileStr)-1);
            $result = $ali->sendSms($mobileStr, 19, array('code'=>'您的设置教练申请已'.$statusName));
            $sendType = $result ? '成功' : '失败';
            wp_send_json_success(array('info'=>'操作成功, 发送短信通知'.$sendType));
        } else{
            wp_send_json_error(array('info' => '操作失败'));
        }
    }

    /**
     * 申请加入战队审核
     */
    public function matchTeamApplyStatus(){
        global $wpdb;
        $status = intval($_POST['status']); // -1 退队; 1 入队
        $type = intval($_POST['type']); //1 同意; 2 拒绝
        if(($type != 1 && $type != 2) || ($status != -1 && $status != 1)) wp_send_json_error(array('info' => '参数错误'));

        if($status == -1){
            //退队
            $statusName = '退队';
            if($type == 1)
                $teamStatus = -3;
            else
                $teamStatus = 2;
        }elseif($status == 1){
            //入队
            $statusName = '入队';
            if($type == 1)
                $teamStatus = 2;
            else
                $teamStatus = -2;
        }
        $typeName = $type == 1 ? '同意' : '被拒绝';


        if($teamStatus != -3 && $teamStatus != -2 && $teamStatus != 2) wp_send_json_error(array('info' => '操作失败,状态参数异常'));
        if(is_array($_POST['id'])){
            $id = '';
            foreach ($_POST['id'] as $v){
                $id.= $v.',';
            }
            $id = substr($id,0,strlen($id)-1);
        }else{
            $id = intval($_POST['id']);
        }

        $users = $wpdb->get_results('SELECT user_mobile FROM '.$wpdb->prefix.'match_team AS m 
            LEFT JOIN '.$wpdb->users.' AS u ON u.ID=m.user_id 
            WHERE m.id IN('.$id.') AND m.status='.$status, ARRAY_A);

        $sql = 'UPDATE '.$wpdb->prefix.'match_team SET status='.$teamStatus.' WHERE status='.$status.' AND id IN('.$id.')';

        $bool = $wpdb->query($sql);
        if($bool){
            //TODO 发送通知
            $ali = new AliSms();
            $mobileStr = '';
            foreach ($users as $v){
                $mobileStr .= $v['user_mobile'].',';
            }
            $mobileStr = substr($mobileStr,0,strlen($mobileStr)-1);
            $result = $ali->sendSms($mobileStr, 19, array('code'=>'您的'.$statusName.'申请已'.$typeName));
            $senfStatusName = $result ? '成功' : '失败';
            wp_send_json_success(array('info'=>'操作成功, 发送短信通知'.$senfStatusName));
        }else{
            wp_send_json_error(array('info' => '操作失败'));
        }

    }

    /**
     * 支付查询订单
     */
    public function queryPayOrder(){
        $id = intval($_POST['id']);
        global $wpdb;
        $order = $wpdb->get_row('SELECT pay_lowdown,serialnumber,pay_status,pay_type FROM '.$wpdb->prefix.'order WHERE id='.$id, ARRAY_A);
        if(!$order) wp_send_json_error(array('info' => '未找到订单'));
        if($order && $order['pay_status'] == 2){
            require_once WP_CONTENT_DIR.'/plugins/nlyd-student/Controller/class-student-payment.php';
            switch ($order['pay_type']){
                case 'wx':
                    //微信
                    $payClass = new Student_Payment('wxpay');
                    $param = [
                        'transaction_id' => unserialize($order['pay_lowdown'])['transaction_id'], //微信订单号(二选一)
                        'order_no' => $order['serialnumber']// 商户订单号 (二选一)
                    ];
                    $result = $payClass::$payClass->orderQuery($param); //return array
                    break;
                case 'zfb':
                    $param = [
                        'out_trade_no' => $order['serialnumber'],
                        'trade_no' => unserialize($order['pay_lowdown'])['trade_no'],
                    ];
                    $payClass = new Student_Payment('alipay');
                    $result = $payClass::$payClass->queryOrder($param);
                    break;
                case 'ylk':

                    break;
                default:
                    wp_send_json_error(array('info' => '订单无支付方式'));
            }
        }else{
            wp_send_json_error(array('info' => '订单支付状态异常'));
        }

        if($result){
            if($result['status'] == true){
                //TODO
                wp_send_json_success(array('info'=>$result['data']));
            }else{
                wp_send_json_error(array('info' => $result['data']));
            }

        }else{
            wp_send_json_error(array('info' => '查询失败'));
        }
    }

    /**
     * 支付申请退款
     */
    public function refundPay(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_refund_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        $serial = intval($_POST['serial']);
        global $wpdb;
        $refundFee = $_POST['refund_fee'];
        $order = $wpdb->get_row('SELECT id,serialnumber,pay_lowdown,cost,pay_type FROM '.$wpdb->prefix.'order WHERE serialnumber='.$serial.' AND pay_status=-1', ARRAY_A);

        if(!$order) wp_send_json_error(array('info' => '未找到订单或此订单不是待退款订单'));

        if($refundFee > $order['cost']) wp_send_json_error(array('info' => '退款金额不能超过订单金额'));

        $wpdb->startTrans();
        $refund_no = $order['serialnumber'].date('dHis').rand(000,999);
//        var_dump(['order_id' => $order['id'], 'refund_no' => $refund_no, 'created_time' => date('Y-m-d H:i:s')]);die;
        //更新订单状态
        if(!$wpdb->update($wpdb->prefix.'order', ['pay_status' => -2], ['id' => $order['id'], 'pay_status' => -1])){
            $wpdb->rollback();
            wp_send_json_error(array('info'=>'更新订单状态失败'));
        }

        //创建退款单
        if(!$wpdb->insert($wpdb->prefix.'order_refund',['order_id' => $order['id'], 'refund_no' => $refund_no, 'refund_cost' => $refundFee,  'created_time' => date('Y-m-d H:i:s')])){
            $wpdb->rollback();
            wp_send_json_error(array('info'=>'生成退款单失败'));
        }

        $orderRefundId = $wpdb->insert_id;
        require_once WP_CONTENT_DIR.'/plugins/nlyd-student/Controller/class-student-payment.php';
        switch ($order['pay_type']){
            case 'wx':
                $param['transaction_id'] = unserialize($order['pay_lowdown'])['transaction_id']; //微信订单号
                $param['out_trade_no'] = $order['serialnumber'];
                $param['out_refund_no'] = $refund_no; //TODO 商户的微信退款单号
                $param['refund_fee'] = $refundFee;
                $param['price'] = $order['cost'];
                $payClass = new Student_Payment('wxpay');
                $result = $payClass::$payClass->refund($param);
                break;
            case 'zfb':
                $param = [
                    'out_trade_no' => $order['serialnumber'],     //商户订单号，和支付宝交易号二选一
                    'trade_no' => unserialize($order['pay_lowdown'])['trade_no'],    //支付宝交易号，和商户订单号二选一
                    'refund_amount' => $refundFee,       //退款金额，不能大于订单总金额
                    'refund_reason' => '商家退款',     //退款的原因说明
                    'out_request_no' => $refund_no,      //标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传。
                ];
                $payClass = new Student_Payment('alipay');

                $result = $payClass::$payClass->refund($param);
                break;
            case 'ylk':

                break;
            default:
                $wpdb->rollback();
                wp_send_json_error(array('info' => '此订单无支付方式'));
        }

        if($result){
            if($result['status'] == true){
                //TODO
                $wpdb->update($wpdb->prefix.'order_refund', ['refund_lowdown' => serialize($result['data'])], ['id' => $orderRefundId]);
                $wpdb->commit();
                wp_send_json_success(array('info'=> '申请退款成功'));
            }else{
                $wpdb->rollback();
                wp_send_json_error(array('info' => $result['data']));
            }

        }else{
            $wpdb->rollback();
            wp_send_json_error(array('info' => '查询失败'));
        }
    }

    /**
     * 不退款,顶单状态修改为支付完成
     */
    public function noRefund(){
        $id = intval($_POST['id']);
        global $wpdb;
        if($wpdb->update($wpdb->prefix.'order', ['pay_status' => 2], ['id' => $id, 'pay_status' => -1])){
            //TODO 发送通知
//            $ali = new AliSms();
//            $row = $wpdb->get_row('SELECT serialnumber,telephone FROM '.$wpdb->prefix.'order WHERE id='.$id, ARRAY_A);
//            $result = $ali->sendSms($row['telephone'], 19, array('code'=>'您的订单号'.$row['serialnumber'].'申请退款已被拒绝'));

            wp_send_json_success(array('info'=> '操作成功,已修改支付状态'));
        }else{
            wp_send_json_error(array('info' => '操作失败'));
        }
    }

    /**
     * 支付查询退款
     */
    public function wxRefundQuery(){
        $id = intval($_POST['id']);
        global $wpdb;
        $order = $wpdb->get_row('SELECT o.serialnumber,o.pay_lowdown,o.cost,o.pay_type,r.refund_no,r.refund_lowdown FROM '
                                .$wpdb->prefix.'order_refund AS r 
                                LEFT JOIN '.$wpdb->prefix.'order AS o ON o,id=r.order_id   
                                WHERE r.id='.$id, ARRAY_A);

        if(!$order) wp_send_json_error(array('info' => '未找到订单,请刷新重试'));
        require_once WP_CONTENT_DIR.'/plugins/nlyd-student/Controller/class-student-payment.php';
        switch ($order['pay_type']){
            case 'wx':

                $param = [
                    'transaction_id' => unserialize($order['pay_lowdown'])['transaction_id'], //微信订单号 (四选一)
                    'out_trade_no' => $order['serialnumber'], //商户订单号 (四选一)
                    'out_refund_no' => $order['refund_no'], //商户退款单号 (四选一)
                    'refund_id' => unserialize($order['refund_lowdown'])['refund_id'], //微信退款单号 (四选一)
                ];
                $payClass = new Student_Payment('wxpay');
                $res = $payClass::$payClass->refundQuery($param);
                break;
            case 'zfb':
                $param = [
                    'out_trade_no' => $order['serialnumber'],        //商户订单号，和支付宝交易号二选一
                    'trade_no' =>  unserialize($order['pay_lowdown'])['trade_no'],        //支付宝交易号，和商户订单号二选一
                    'out_request_no' => unserialize($order['refund_lowdown'])['out_request_no'],        //请求退款接口时，传入的退款请求号，如果在退款请求时未传入，则该值为创建交易时的外部交易号
                ];
                $payClass = new Student_Payment('alipay');
                $res = $payClass::$payClass->refund($param);
                break;
            case 'ylk':

                break;
            default:
                wp_send_json_error(array('info' => '此订单无支付方式'));
        }

        if($res['status']){
            wp_send_json_success(array('info'=>$res['data']));
        }else{
            wp_send_json_error(array('info' => $res['data']));
        }
    }

    /**
     * 支付关闭订单
     */
    public function closePayOrder(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_closepay_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        $id = intval($_POST['id']);
        global $wpdb;
        $order = $wpdb->get_row('SELECT serialnumber,pay_lowdown,cost,pay_type FROM '.$wpdb->prefix.'order WHERE id='.$id, ARRAY_A);
        if(!$order) wp_send_json_error(array('info' => '未找到订单,请刷新重试'));
        require_once WP_CONTENT_DIR.'/plugins/nlyd-student/Controller/class-student-payment.php';
        switch ($order['pay_type']){
            case 'wx':
                //微信
                $payClass = new Student_Payment('wxpay');
                $result = $payClass::$payClass->closeOrder($order['serialnumber']); //return array
                break;
            case 'zfb':
                $param = [
                    'out_trade_no' => $order['serialnumber'],//商户订单号，和支付宝交易号二选一
                    'trade_no' => unserialize($order['pay_lowdown'])['trade_no'],        //支付宝交易号，和商户订单号二选一
                ];
                $payClass = new Student_Payment('alipay');
                $result = $payClass::$payClass->closeOrder($param);
                break;
            case 'yld':

                break;
            default:
                wp_send_json_error(array('info' => '此订单无支付方式'));
        }

        if($result['status']){
            //TODO 关闭成功
            wp_send_json_success(array('info' => '支付订单已关闭'));

        }else{
            wp_send_json_error(array('info' => $result['data']));

        }
    }

    /**
     *根据查询条件匹配战队成员列表 学生或者教练
     */
    public function getMemberByWhere(){
        $type = $_GET['team_type'];
        $team_id = $_GET['team_id'];

        switch ($type){
            case 'team_leader': //队长
                $user_role = 0;
                $user_type = 1;
                break;
            case 'team_director':   //负责人
                $user_role = 7;
                $user_type = 2;
                break;
        }
        global $wpdb;

        $select = "SELECT a.user_id as id ,b.team_id,c.display_name as text FROM wp_usermeta a ";

        $left_join = "left join {$wpdb->prefix}match_team b on a.user_id = b.user_id
                      left join {$wpdb->prefix}users c on a.user_id = c.ID";

        $where = " where a.meta_key = 'wp_user_level' AND a.meta_value = {$user_role} ";
        $where_ = " AND b.status = 2 AND b.user_type = {$user_type} AND b.team_id = {$team_id} ";
        if(!empty($_GET['term'])){
            $where .= "  and (c.user_login like '%{$_GET['term']}%' or c.user_nicename like '%{$_GET['term']}%' or c.user_email like '%{$_GET['term']}%' or c.display_name like '%{$_GET['term']}%') ";
        }
        $limit = " limit 0,10 ";

        $sql = $select.$left_join.$where.$where_.$limit;
        $rows = $wpdb->get_results($sql,ARRAY_A);

        if(empty($rows)){
            $select = "SELECT a.user_id as id ,c.display_name as text FROM wp_usermeta a ";
            $left_join = " left join {$wpdb->prefix}users c  on a.user_id = c.ID ";

            $sql = $select.$left_join.$where.$limit;
            $rows = $wpdb->get_results($sql,ARRAY_A);

        }
        if(!empty($rows)){
            foreach ($rows as $k => $val ){
                $user = get_user_meta($val['id'],'user_real_name');
                if(!empty($user[0])){
                    $rows[$k]['text'] = $user[0]['real_name'];
                }else{
                    $rows[$k]['text'] = preg_replace('/, /','',$val['text']);
                }
            }
        }
        wp_send_json_success($rows);
    }

    /**
     * 设置战队队长或战队负责人
     */
    public function setMatchTeamLeaderOrDirector(){
        $team_id = intval($_POST['team_id']);
        $match_team_id = intval($_POST['match_team_id']);
        $type = $_POST['type'];
        switch ($type){
            case 'leader':
                $field = 'team_leader';
                break;
            case 'director':
                $field = 'team_director';
                break;
            default:
                wp_send_json_error(['info' => '参数错误!']);
        }
        if($team_id < 1 || $match_team_id < 1) wp_send_json_error(['info' => '参数错误!']);
        global $wpdb;
        $bool = $wpdb->update($wpdb->prefix.'team_meta', [$field => $match_team_id], ['team_id' => $team_id]);
        if($bool)
            wp_send_json_success(['info' => '设置成功']);
        else
            wp_send_json_error(['info' => '设置失败']);
    }

    /**
     * 删除一条意见反馈
     */
    public function remFeedback(){
        $id = intval($_POST['id']);
        if($id < 1) wp_send_json_error(['info' => '参数错误']);
        global $wpdb;
        $row = $wpdb->get_row('SELECT images FROM '.$wpdb->prefix.'feedback'.' WHERE id='.$id, ARRAY_A);
        if(!$row) wp_send_json_error(['info' => '没有找到此条意见反馈']);
        $bool = $wpdb->delete($wpdb->prefix.'feedback', ['id' => $id]);
        if($bool){
            //删除图片
            foreach (unserialize($row['images']) as $v){
                $filePa = explode('uploads',$v);
                if(is_file(wp_upload_dir()['basedir'].$filePa[1])) unlink(wp_upload_dir()['basedir'].$filePa[1]);
            }
            wp_send_json_success(['info' => '删除成功']);
        }else{
            wp_send_json_error(['info' => '删除失败']);
        }
    }
}

new Match_Ajax();