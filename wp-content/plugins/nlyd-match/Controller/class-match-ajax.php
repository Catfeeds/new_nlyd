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
            $statusName = '通过审核';
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
        $user = $wpdb->get_results('SELECT u.ID,u.user_mobile,u.display_name,m.category_id,mu.display_name AS coach_name FROM '.$wpdb->prefix.'my_coach AS m 
            LEFT JOIN '.$wpdb->users.' AS u ON m.user_id=u.ID 
            LEFT JOIN '.$wpdb->users.' AS mu ON m.coach_id=mu.ID 
            WHERE m.'.$idWhere.' AND m.`apply_status`=1',ARRAY_A);

        $sql = 'UPDATE '.$wpdb->prefix.'my_coach SET `apply_status`='.$status.' WHERE '.$idWhere.' AND `apply_status`=1';
        if($status != -1  && $status != 2){
            wp_send_json_error(array('info' => '操作失败,状态参数异常'));
        }
        $bool = $wpdb->query($sql);
//        $bool = true;
        if($bool) {
            //TODO 发送通知
            $ali = new AliSms();
            $sendErr = "\n";
            $categoryArr = []; //post数据数组. 避免重复查询
            foreach ($user as $v){
                //如果display_name没有或者user_mobile没有就去寻找默认收货地址中的电话号码和收件人
                if(!$v['display_name'] || !$v['user_mobile']){
                    $address = $wpdb->get_row('SELECT telephone,fullname FROM '.$wpdb->prefix.'my_address WHERE is_default=1 AND user_id='.$v['ID'], ARRAY_A);
                    $real_name = $address['fullname'];
                    $mobile = $address['telephone'];
                }else{
                    $real_name = explode(', ', $v['display_name'])[0].explode(', ', $v['display_name'])[1];
                    $mobile = $v['user_mobile'];
                }
                //查询类别名称
                if(isset($categoryArr[$v['category_id']])){
                    $post_title = $categoryArr[$v['category_id']];
                }else{
                    $post_title = get_post($v['category_id'])->post_title;
                    $categoryArr[$v['category_id']] = $post_title;
                }
                $result = $ali->sendSms($mobile, 10, array('user'=>$real_name,'cate'=>$post_title, 'coach' => explode(', ', $v['coach_name'])[0].explode(', ', $v['coach_name'])[1], 'type' => $statusName));
                if(!$result){
                    $sendErr .= $real_name.': '.$mobile.'短信发送失败'."\n";
                }
            }

            wp_send_json_success(array('info'=>'操作成功'.$sendErr));
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
            $statusName = '退出';
            if($type == 1)
                $teamStatus = -3;
            else
                $teamStatus = 2;
        }elseif($status == 1){
            //入队
            $statusName = '加入';
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

        $users = $wpdb->get_results('SELECT u.user_mobile,u.ID,u.display_name,m.team_id FROM '.$wpdb->prefix.'match_team AS m 
            LEFT JOIN '.$wpdb->users.' AS u ON u.ID=m.user_id 
            WHERE m.id IN('.$id.') AND m.status='.$status, ARRAY_A);

        $sql = 'UPDATE '.$wpdb->prefix.'match_team SET status='.$teamStatus.' WHERE status='.$status.' AND id IN('.$id.')';
        //战队名称
        $post_title = get_post($users[0]['team_id'])->post_title;
        $bool = $wpdb->query($sql);
        if($bool){
            //TODO 发送通知
            $ali = new AliSms();

            $sendErr = "\n";
            foreach ($users as $v){
                if(!$v['display_name'] || !$v['user_mobile']){
                    $address = $wpdb->get_row('SELECT telephone,fullname FROM '.$wpdb->prefix.'my_address WHERE is_default=1 AND user_id='.$v['ID'], ARRAY_A);
                    $real_name = $address['fullname'];
                    $mobile = $address['telephone'];
                }else{
                    $real_name = explode(', ', $v['display_name'])[0].explode(', ', $v['display_name'])[1];
                    $mobile = $v['user_mobile'];
                }
                $result = $ali->sendSms($mobile, 9, array('user'=>$real_name, 'applytype' => $statusName, 'team' => $post_title, 'type' => $typeName));
                if(!$result) $sendErr .= $real_name.': '.$mobile."发送短信失败\n";
            }
            wp_send_json_success(array('info'=>'操作成功'.$sendErr));
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
        $order = $wpdb->get_row('SELECT id,serialnumber,pay_lowdown,cost,pay_type,funllname,telephone FROM '.$wpdb->prefix.'order WHERE serialnumber='.$serial.' AND pay_status=-1', ARRAY_A);

        if(!$order) wp_send_json_error(array('info' => '未找到订单或此订单不是待退款订单'));

        if($refundFee > $order['cost']) wp_send_json_error(array('info' => '退款金额不能超过订单金额'));

        $wpdb->startTrans();
        $refund_no = $order['serialnumber'].date_i18n('dHis',current_time('timestamp')).rand(000,999);
//        var_dump(['order_id' => $order['id'], 'refund_no' => $refund_no, 'created_time' => date('Y-m-d H:i:s')]);die;
        //更新订单状态
        if(!$wpdb->update($wpdb->prefix.'order', ['pay_status' => -2], ['id' => $order['id'], 'pay_status' => -1])){
            $wpdb->rollback();
            wp_send_json_error(array('info'=>'更新订单状态失败'));
        }

        //创建退款单
        if(!$wpdb->insert($wpdb->prefix.'order_refund',['order_id' => $order['id'], 'refund_no' => $refund_no, 'refund_cost' => $refundFee,  'created_time' => date_i18n('Y-m-d H:i:s', current_time('timestamp'))])){
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
                //保存第三方信息
                $wpdb->update($wpdb->prefix.'order_refund', ['refund_lowdown' => serialize($result['data'])], ['id' => $orderRefundId]);
                //发送短信
                $ali = new AliSms();

                $result = $ali->sendSms($order['telephone'], 8, array('user'=> $order['funllname'], 'order' => $order['serialnumber'], 'cost' => $refundFee));
                if($result){
                    $sendMsg = ', 已发送短信通知';
                }else{
                    $sendMsg = ', 短信发送失败';
                }
                $wpdb->commit();
                wp_send_json_success(array('info'=> '申请退款成功'.$sendMsg));
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

        $select = "SELECT a.user_id as id ,b.team_id,c.display_name as text FROM {$wpdb->prefix}usermeta a ";

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

    /**
     * 教练解除教学关系
     */
    public function relieveMyStudent(){
        $id = intval($_POST['id']);
        if($id < 1) wp_send_json_error(['info' => '参数错误']);
        //查询是否是战队成员
        global $wpdb;
        $res = $wpdb->get_row('SELECT id,user_id,category_id,coach_id FROM '.$wpdb->prefix.'my_coach WHERE id='.$id.' AND apply_status=2', ARRAY_A);
        //不存在或已解除
        if(!$res) wp_send_json_error(['info' => '该学员不存在或已解除']);

        //获取用户信息
        $user = $wpdb->get_row('SELECT ID,user_mobile,display_name FROM '.$wpdb->users.' WHERE ID='.$res['user_id'], ARRAY_A);

        //获取教练名字
        $coach = $wpdb->get_row('SELECT ID,display_name FROM '.$wpdb->users.' WHERE ID='.$res['coach_id'], ARRAY_A);

        if(!$user) wp_send_json_error(['info' => '该学员不存在']);

        //学员名字和手机
        if(!$user['display_name'] || !$user['user_mobile']){
            $address = $wpdb->get_row('SELECT telephone,fullname FROM '.$wpdb->prefix.'my_address WHERE is_default=1 AND user_id='.$user['ID'], ARRAY_A);
            $real_name = $address['fullname'];
            $mobile = $address['telephone'];
        }else{
            $real_name = explode(', ', $user['display_name'])[0].explode(', ', $user['display_name'])[1];
            $mobile = $user['user_mobile'];
        }


        //教练名字
        $coach_real_name = explode(', ', $user['display_name'])[0].explode(', ', $user['display_name'])[1];

        //开始解除
        $wpdb->startTrans();
        $bool = $wpdb->update($wpdb->prefix.'my_coach', ['apply_status' => 3], ['id' => $id]);
        if($bool){
            //TODO 发送短信通知学员
            //类别名称
            $post_title = get_post($res['category_id'])->post_title;

            $ali = new AliSms();
            $result = $ali->sendSms($mobile, 7, array('user'=> $real_name, 'cate' => $post_title, 'coach' => $coach_real_name));
            if($result){
                $wpdb->commit();
                wp_send_json_success(['info' => '已解除教学关系']);
            }else{
                $wpdb->rollback();
                wp_send_json_error(['info' => '解除失败, 短信发送失败']);
            }

        }else{
            $wpdb->rollback();
            wp_send_json_error(['info' => '解除失败']);
        }
    }

    /**
     * 踢出战队
     */
    public function expelTeam(){
        $id = intval($_POST['id']);
        if($id < 1) wp_send_json_error(['info' => '参数错误']);
        global $wpdb;
        $bool = $wpdb->update($wpdb->prefix.'match_team', ['status' => -3], ['id' => $id]);
        if($bool){
            wp_send_json_success(['info' => '操作成功']);
        }else{
            wp_send_json_error(['info' => '操作失败']);
        }
    }

    /**
     * 关闭比赛
     */
    public function closeMatch(){
        $id = intval($_POST['id']);
        if($id < 1) wp_send_json_error(['info' => '参数错误']);
        //移入回收站
        if(wp_trash_post($id)){
            wp_send_json_success(['info' => '关闭成功']);
        }else{
            wp_send_json_error(['info' => '关闭失败']);
        }
    }

    /**
     * 删除比赛
     */
    public function delMatch(){
        $id = intval($_POST['id']);
        if($id < 1) wp_send_json_error(['info' => '参数错误']);
        //判断是否是已关闭的比赛(回收站中的)
        $post = get_post($id);
        if($post->post_status == 'trash'){
            //删除post
            if(wp_delete_post($id)){
                wp_send_json_success(['info' => '比赛已删除']);
            }else{
                wp_send_json_error(['info' => '删除失败']);
            }
        }else{
            wp_send_json_error(['info' => '请先关闭比赛']);
        }
    }

    /**
     * 添加比赛报名学员
     */
    public function joinMatch(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_join_match_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        $match_id = intval($_POST['mid']);
        $user_id = intval($_POST['uid']);
        if($match_id < 1 || $user_id < 1) wp_send_json_error(array('info'=>'非法操作'));
        global $wpdb;
        //判断是否已报名该比赛
        $orderRes = $wpdb->get_var('SELECT id FROM '.$wpdb->prefix.'order WHERE user_id='.$user_id.' AND match_id='.$match_id.' AND order_type=1');
        if($orderRes) wp_send_json_error(array('info'=>'此学员已报名该比赛'));
        //判断是否有主训和默认收货地址
        //主训
        $sql = "select ID,post_title from {$wpdb->prefix}posts where post_type = 'match-category' and post_status = 'publish' order by menu_order asc  ";
        $postsRows = $wpdb->get_results($sql,ARRAY_A);
        foreach ($postsRows as $prow){
            //每个类别得主训
            $res = $wpdb->get_row('SELECT id FROM '.$wpdb->prefix.'my_coach WHERE apply_status=2 AND major=1 AND category_id='.$prow['ID']);
            if(!$res){
                wp_send_json_error(array('info'=>'当前学员未设置'.$prow['post_title'].'主训教练!'));
                return;
            }
        }
        //默认收货地址
        $addressRes = $wpdb->get_row('SELECT fullname,telephone,country,province,city,area,address FROM '.$wpdb->prefix.'my_address WHERE user_id='.$user_id.' AND is_default=1', ARRAY_A);
        if(!$addressRes){
            wp_send_json_error(array('info'=>'当前学员未设置默认收货地址!'));
            return;
        }
        $cost = $wpdb->get_var('SELECT match_cost FROM '.$wpdb->prefix.'match_meta WHERE match_id='.$match_id);
        //新增订单
        $orderInsertData = [
            'user_id' => $user_id,
            'match_id'=>$match_id,
            'cost'=> $cost,
            'fullname'=>$addressRes['fullname'],
            'telephone'=>$addressRes['telephone'],
            'address'=>$addressRes['country'].$addressRes['province'].$addressRes['city'].$addressRes['area'].$addressRes['address'],
            'order_type'=>1,
            'pay_status'=>2,
            'created_time'=>get_time('mysql'),
        ];


        //开启事务
        $wpdb->startTrans();
        $insertRes = $wpdb->insert($wpdb->prefix.'order',$orderInsertData);

        if(!$insertRes){
            $wpdb->rollback();
            wp_send_json_error(['info' => '操作失败']);
            return;
        }
        //生成流水号
        $serialnumber = createNumber($user_id,$wpdb->insert_id);
        $updateRes = $wpdb->update($wpdb->prefix.'order',array('serialnumber'=>$serialnumber),array('id'=>$wpdb->insert_id));
        if($updateRes){
            $wpdb->commit();
            wp_send_json_success(['info' => '操作成功']);
        }else{
            $wpdb->rollback();
            wp_send_json_error(['info' => '操作失败']);
        }
    }
}

new Match_Ajax();