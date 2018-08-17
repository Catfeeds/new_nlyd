<?php
class Student_Payment {

    public static $payClass;
    public function __construct($action)
    {

        $interface_config = get_option('interface_config');
        if($action == 'wxpay' ){
            require_once INCLUDES_PATH.'library/Vendor/Wxpay/wxpay.php';
            //TODO 脑力运动
            //$interface_config['wx']['api'] = 'wxaee5846345bca60d';
            //$interface_config['wx']['merchant'] = '1494311232';
            //$interface_config['wx']['secret_key'] = '0395894147b41053e3694f742f6aebce';

            //TODO 测试
            $interface_config['wx']['api'] = 'wx4b9c68ca93325828';
            $interface_config['wx']['merchant'] = '1495185102';
            $interface_config['wx']['secret_key'] = 'qweQ61234Hjkasdiosd73Odcdsar72pz';

            //var_dump($interface_config['wx']);exit;
            self::$payClass = new wxpay($interface_config['wx']['api'], $interface_config['wx']['merchant'], $interface_config['wx']['secret_key']);

            if(isset($_GET['action'])){
                $function = $_GET['action'];
            }else {
                $function = $_GET['action'] = 'notifyUrl';
            }
            if($function == 'downloadBill'){
                $this->downloadBill();
                exit;
            }
        }else{

            require_once INCLUDES_PATH.'library/Vendor/Alipay/alipay.php';
            self::$payClass =  new alipay('2017123001371219',
                'MIIEpAIBAAKCAQEAuFAjw6VL6CGjp7zxAJzcLSPXxYVHfILRukhTL6Z1ZyRBLlmTW+yFiq96+pNKSbWzmniNcsPqA4xRD0amORUiXxcfY/rlSBSxn/aIHPfLZDFhhGNxuHS1BeXQjJpzOgKzrlWZwrYvVU21Qw1Z74Jdk8TlZySeDgypBNfIKHRpni2AVo+8tT0DvvqsHqilvo8AGJI1U/pDl3TjJSAY6sp0Z/YK/rhPqlgUyzu/nlt9uRLExG4fa224EvKv+Qn/ZdTmzdzvfcxrDfo9iAXJPh1CxT8holbC8TLq+Ff5Ddh7yj/4NaF438uqSy0MwYpYW4nEDkQvvhQLC7uhRsRSpHl3TQIDAQABAoIBAQCjClkoty7Xb/Jp7gwuo5Ns5tj3I/fhn4NQyquzagdOrtZt3tUoqqhSzvn1cJd1bqMq0NsnG0EF1HjcD3343sYh4b1l3so1ogCiZR1wqo4j2j7OMn2lUq/TQMDjr7igJ0W0wIocoLZsOipO3x+ga+zFS5Y2UED0YqSc4RhxGNFZFpwEZUd34Ga1OSjKjehIEQzMnrUTNC6EwzNpkLn/oNTExKeD+uz9e9O9MMcXThaTyWxWYismxJDouVep/dEgGslZTOUxrD0iVSUERzsbWqsHFnc9bhxM3qUKzwZ3Dvpy2Uq+psM80Um1dcE6wL+XdLGi3KyayF+zUSUw9EoNg4vhAoGBAOaWEUaAVGMq0hZ6N1Cx5AC1MAh5Jc14DERDp4MgwQ347fUEZ0rhJu26czP4I67ztRuVj7LzsFSshXigHTAVKctUwH18HORxnhrVQDVAYB4ai3fskP/tdpdiVjIvxycWBtqq39MQPnxlFL7c2leoAYFDcIGy5y+elMessTVhgIn5AoGBAMygfJKGWu3OBR3LBnpuBK8gkKdDNMo7SqUjl+U5boIQwXmlJoVMbWtLdXpMjszplavDRkKQ5w2hOhndnWCEdBRVED8f6iWmQ2+n5z4pgn2AYxA7t4a5n+yVlAvTMaTRLK/DpwQl/Cf0yM/Ame5JS2WYdnaxgdDg1r6IQk5zW8z1AoGAVxZ2j9oIBSw3DKY8Hg4RvvKvoYOf82pTt7SVn8DPKSfLN67iFDXVLhQtToN5dqo0zKZAD6ZaAqDmCBjw7SgREOqBiONHRkBjJl9EUNhvdO8xnamLWh2lnKdXRr0kym5XSF8hCeYos3K50xw2msSpTNjbtSCMkD+kkYV3qGGa2oECgYAumCClkLhly/K4TQGloSWp5wVpQNFld0jQ/6DXzlMOhNg5ZdS2p6eGtgEDHympGUs+eFGoWKx0GxFK0H7EeoSgGJqBdTfw6MIUS6xJKFSRVUm5aY+putzil1DFvIpiWEsPnsKKHEglpQSQ4e9rJf9oG+Zlspe3w2rCqe5HRNdTfQKBgQDQI1BrIyEColUlIK7I0yd6z5Kzk7U57D7YYwg6vVlhKwJp0mEyeq/5o5S5xZMHw1Eyz9U0nyeHtxZYbEjWgnQWpoEIhQeG3TzuZmKCZ2UvM4iqK57pm9c5ebX0sOCfYo0VKmJ4n/5vDGN1x0iglAELKRlLDFcxnHMKNLhXC3sd2A==',
                'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAk5Ic/oM4MnQFRtGKVvc57Erl/ownJP+dL3swjrAZPWCE0hxy3mkpxfFvogwjHkGVY+eTDfiMRwnoPyppubMDPSc/ZGtz/XvRvAmJ1BGBhLeOHz46xicqS+QK+sdxWUODBwySnepVrDnSi7cuH0/yyzF6tzyzQsFzYAEZJkv0uPx8/0I3WVwlPRK/T29Iid9SFV9nma2awNkwcjO7G6sMf1SIRXbouNVKsyXPcbjoZZEo/PzHFQ2/Rp7TPQTr+j52qgdvIGtBS+sE4TFSYt7Q/IF8fceejjQTYQzpTWDgO6bkR6Ra5tm37lQzPi4tdk+b7tCZ2bRj7IVAi3OvBEqa9QIDAQAB'
            );

        }

        //添加短标签
        add_shortcode('payment-home',array($this,$action));
    }

    /***************************************微信start*****************************************/

    /**
     * 微信支付参数
     */
    public function getWxParam($order){
        $params['notify_url'] = home_url('account/student/wxpay/'); //商品描述
        $params['body'] = '脑力中国'; //商品描述
        $params['serialnumber'] = $order['serialnumber']; // TODO 自定义的订单号
//        $params['price'] = 0.01; //订单金额 只能为整数 单位为分
        $params['price'] = $order['cost']; //订单金额 只能为整数 单位为分
        $params['attach'] = 'serialnumber='.$order['serialnumber']; //附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        return $params;
    }

    /**
     * 微信下载对账单
     */
    public function wx_downloadBill(){
        $date = $_GET['date'];//  = '20180730'; //TODO

        $res = self::$payClass->downloadBill($date);
        if($res != ''){
            $arr = explode("\r\n", $res);
            $html = '<table style="color: black;text-align: center" border="1px solid #000000">';
            foreach ($arr as $v){
                $html .= '<tr>';
                foreach (explode(',', $v) as $v2){
                    $html .= '<td style="text-align: center; vnd.ms-excel.numberformat:@">'.$v2.'</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</table>';

            $filename = 'wxBill_';
            $filename .= $date."_";
            $filename .= time().".xls";
            $path = WP_PLUGIN_DIR.'/downloadFile/'.$filename;
            file_put_contents($path,$html);
            $file_temp = fopen ( $path, "r");

            // Begin writing headers
            header ( "Pragma: public" );
            header ( "Expires: 0" );
            header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
            header ( "Cache-Control: public" );
            header ( "Content-Description: File Transfer" );
            // Use the switch-generated Content-Type
            header ( "Content-Type: application/vnd.ms-excel" );
            // Force the download
            $header = "Content-Disposition: attachment; filename=" . $filename . ";";
            header ( $header );
            header ( "Content-Transfer-Encoding: binary" );
            header ( "Content-Length: " . filesize($path) );

            //@readfile ( $file );
            echo fread ($file_temp, filesize ($path) );
            fclose ($file_temp);
            exit;
        }

    }


    /**
     * 微信H5支付
     */
    public function wx_h5Pay(){
        $id = intval($_GET['id']);
        global $wpdb,$current_user;
        $order = $wpdb->get_row(
            'SELECT serialnumber,match_id,user_id,fullname,telephone,address,pay_type,cost,pay_status,created_time FROM '
            .$wpdb->prefix.'order WHERE id='.$id.' AND user_id='.$current_user->ID.' AND pay_status=1', ARRAY_A);
        if(!$order) return false;
        //请求数据
        //1.统一下单方法
        $result = self::$payClass->h5UnifiedOrder($this->getWxParam($order));
//        var_dump($result);return;
        if($result != false){
            if($result['status']){
                echo '<script>window.location.href="'.$result['data'].'"</script>';
            }else{
                echo $result['data'];
            }
        }else{
            //发起支付失败
            return false;
        }
    }




    /**
     * 微信H5支付回调
     */
    public function wx_notifyUrl(){
        //返回结果示例
//        $foo = '<xml>
//  <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
//  <attach><![CDATA[支付测试]]></attach>
//  <bank_type><![CDATA[CFT]]></bank_type>
//  <fee_type><![CDATA[CNY]]></fee_type>
//  <is_subscribe><![CDATA[Y]]></is_subscribe>
//  <mch_id><![CDATA[10000100]]></mch_id>
//  <nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
//  <openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
//  <out_trade_no><![CDATA[1409811653]]></out_trade_no>
//  <result_code><![CDATA[SUCCESS]]></result_code>
//  <return_code><![CDATA[SUCCESS]]></return_code>
//  <sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
//  <sub_mch_id><![CDATA[10000100]]></sub_mch_id>
//  <time_end><![CDATA[20140903131540]]></time_end>
//  <total_fee>1</total_fee>
//<coupon_fee><![CDATA[10]]></coupon_fee>
//<coupon_count><![CDATA[1]]></coupon_count>
//<coupon_type><![CDATA[CASH]]></coupon_type>
//<coupon_id><![CDATA[10000]]></coupon_id>
//<coupon_fee><![CDATA[100]]></coupon_fee>
//  <trade_type><![CDATA[JSAPI]]></trade_type>
//  <transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
//</xml>';
        global $wpdb;
        $queryRes = self::$payClass->notify(file_get_contents("php://input"), function ($out_trade_no){
            //获取订单信息回调,用于验证签名
            global $wpdb;
            $order = $wpdb->get_row(
                'SELECT serialnumber,match_id,user_id,fullname,telephone,address,pay_type,cost,pay_status,created_time FROM '
                .$wpdb->prefix.'order WHERE serialnumber='.$out_trade_no, ARRAY_A);
            if($order){
                return $this->getWxParam($order);
            }else{
                return false;
            }
        });
        if($queryRes && $queryRes['trade_state'] === 'SUCCESS'){
            // TODO 处理业务逻辑
            $serialnumber = $queryRes['out_trade_no'];
            $updateData = [
                'pay_status' => 2,
                'pay_type' => 'wx',
                'pay_lowdown' => serialize($queryRes['notifyData'])
            ];
            $bool = $wpdb->update($wpdb->prefix.'order', $updateData, array('serialnumber' => $serialnumber));
            if($bool){
                echo 'SUCCESS';
            }else{
                echo 'FALL';
            }
        }else{
            echo 'FALL';
        }
    }



    /**
     * 微信支付查询订单
     */
    public function wx_H5QueryOrder($param = []){
//        $param = [
//            'transaction_id' => '', //微信订单号(二选一)
//            'order_no' => ''// 商户订单号 (二选一)
//        ];
        $queryRes = self::$payClass->orderQuery($param); //return array
        if($queryRes){
            if($queryRes['status'] == true){
                //TODO
                return true;
            }else{
                echo $queryRes['data'];
                return false;
            }
        }else{
            return false;
        }
        //示例
//        'Array
//        (
//            [return_code] => SUCCESS
//            [return_msg] => OK
//            [appid] => wx2421b1c4370ec43b
//            [mch_id] => 10000100
//            [device_info] => 1000 //微信支付分配的终端设备号，
//            [nonce_str] => D2O2XMfWpVrTVzew
//            [sign] => E0BAAA96824BFEEFDA2666F8F53ABE7C
//            [result_code] => SUCCESS
//            [openid] => oUpF8uN95-Ptaags6E_roPHg7AG0
//            [is_subscribe] => Y 用户是否关注公众账号，Y-关注，N-未关注，仅在公众账号类型支付有效
//            [trade_type] => MICROPAY 调用接口提交的交易类型，取值如下：JSAPI，NATIVE，APP，MICROPAY，MWEB详细说明见参数规定
//            [bank_type] => CCB_DEBIT 银行类型，采用字符串类型的银行标识
//            [total_fee] => 1 订单总金额，单位为分
//            [fee_type] => CNY
//            [transaction_id] => 1008450740201411110005820873 微信支付订单号
//            [out_trade_no] => 1415757673
//            [attach] => Array 附加数据，原样返回
//                (
//                )
//
//            [time_end] => 20141111170043 支付完成时间
//            [trade_state] => REFUND SUCCESS—支付成功 //REFUND—转入退款 NOTPAY—未支付 CLOSED—已关闭 REVOKED—已撤销（刷卡支付）USERPAYING--用户支付中 PAYERROR--支付失败(其他原因，如银行返回失败)
//            [cash_fee] => 1 现金支付金额订单现金支付金额
//            [trade_state_desc] => 订单发生过退款，退款详情请查询退款单 //对当前查询订单状态的描述和下一步操作的指引
//        )';

    }


    /**
     * 微信支付关闭订单
     * 商户订单支付失败需要生成新单号重新发起支付，要对原订单号调用关单，避免重复支付；
     * 系统下单后，用户支付超时，系统退出不再受理，避免用户继续，请调用关单接口。
     */
    public function wx_CloseOrder(){
        $wxpay = self::getWxPay();

        $order_no = '1415983244'; //商户订单号
        $result = $wxpay->closeOrder($order_no); //return array
        if($result){
            //TODO 关闭成功
        }else{
            return false;
        }

    }

    /**
     * 微信支付申请退款
     */
    public function wx_Refund(){
        global $wpdb;
        $order = $wpdb->get_row(
            'SELECT serialnumber,match_id,user_id,fullname,telephone,address,pay_type,cost,pay_status,created_time FROM '
            .$wpdb->prefix.'order LIMIT 1', ARRAY_A);
        $param = $this->getWxParam($order);
        $param['transaction_id'] = '';
        $param['out_trade_no'] = $param['serialnumber'];
        $param['out_refund_no'] = '3215634354';
        $param['refund_fee'] = 0.01;
        var_dump(self::$payClass->refund($param));
    }

    /**
     * 微信查询退款
     */
    public function wx_RefundQuery(){
        $param = [
            'transaction_id' => '1321321', //微信订单号 (四选一)
            'out_trade_no' => '', //商户订单号 (四选一)
            'out_refund_no' => '', //商户退款单号 (四选一)
            'refund_id' => '', //微信退款单号 (四选一)
        ];
        $res = self::$payClass->refundQuery($param);
        var_dump($res);
    }

    /**
     * 轮询查询订单状态
     */
    public function wx_orderIsSuccess(){
        $count = $_POST['count'];
        $serial_number = $_POST['serial_number'] = '1808011966475';
        global $wpdb,$current_user;
        $orderStatus = $wpdb->get_row('SELECT serialnumber,pay_status,pay_lowdown FROM '.$wpdb->prefix.'order WHERE user_id='.$current_user->ID.' AND serialnumber='.$serial_number, ARRAY_A);
        if($orderStatus['pay_status'] == 2){
            $payIntro = unserialize($orderStatus['pay_lowdown']);
            if($this->wxH5QueryOrder(['transaction_id' => $payIntro['transaction_id'],'order_no' => $orderStatus['serialnumber']])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /***************************************微信end*****************************************/

    /***************************************支付宝start*****************************************/
    /**
     * 支付宝发起支付
     */
    public function zfb_pay(){
        $id = intval($_GET['id']);
        global $wpdb,$current_user;
        $order = $wpdb->get_row(
            'SELECT serialnumber,match_id,user_id,fullname,telephone,address,pay_type,cost,pay_status,created_time FROM '
            .$wpdb->prefix.'order WHERE id='.$id.' AND user_id='.$current_user->ID.' AND pay_status=1', ARRAY_A);
        if(!$order){
//            $this->get_404('参数错误');
            return;
        }
        //请求参数
        $param = [
            'notify_url'    => home_url('student/account/alipay'),
            'return_url'    => home_url('student/account/alipay/?action=returnUrl&serialnumber='.$order['serialnumber']),
            'out_trade_no'  => $order['serialnumber'],
            'subject'       => '脑力中国',
//            'total_amount'  =>  0.01,
            'total_amount'  => $order['cost'],
            'body'  => '', //商品描述,可空
        ];

        self::$payClass->pay($param);
        return;
    }

    /**
     * 支付宝异步回调
     */
    public function zfb_notifyUrl(){
//        $data = [
//            'gmt_create' => '2018-08-11 17:23:14',
//            'charset' => 'UTF-8',
//            'seller_email' => 'gjnlyd5@163.com',
//            'subject' => '脑力运动',
//            'sign' => 'GY09Et4Q36nJP+T7MBLjwBws3S/Ox6Ok2Tmd1QFBJXQlrcDKQmehGoDmPEHDzPYK0Gfuye/EYNY1yklz5NtwQY4I/GArwR6znrvVP2vsrxuvC7IEVQsTVWV9DpMKTYw0rPTQOXMW5MiWG+VNfvfrNvZGkankXRPcstuFV0O4lsngWfLuE1u11b3iR4ntwIKoijNja+lOllwcgOsJFtHvQ/KEk6Ai9yEIeOiLj6TK1Ea8nWYo/sHq2lyG8g6AjjL12qToREUTIjmxjato50T8SHQE4xZMrrKSJQE2pALq703NpkeehxjgXxKGnKaLqcEOgscly8I9lOoN/4Haxa3tGg==',
//            'buyer_id' => '2088502299947481',
//            'invoice_amount' => '0.01',
//            'notify_id' => '1860d391af24e322d8444868d7c9a94jph',
//            'fund_bill_list' => '[{"amount":"0.01","fundChannel":"ALIPAYACCOUNT"}]',
//            'notify_type' => 'trade_status_sync',
//            'trade_status' => 'TRADE_SUCCESS',
//            'receipt_amount' => '0.01',
//            'buyer_pay_amount' => '0.01',
//            'app_id' => '2017123001371219',
//            'sign_type' => 'RSA2',
//            'seller_id' => '2088921458043341',
//            'gmt_payment' => '2018-08-11 17:23:27',
//            'notify_time' => '2018-08-11 17:27:06',
//            'version' => '1.0',
//            'out_trade_no' => '18081195205138',
//            'total_amount' => '0.01',
//            'trade_no' => '2018081121001004480530317218',
//            'auth_app_id' => '2017123001371219',
//            'buyer_logon_id' => '277***@qq.com',
//            'point_amount' => '0.00',
//        ];
//        $_POST = $data;
        if(isset($_POST['sign'])) $_POST['sign'] = stripslashes($_POST['sign']);
        if(isset($_POST['fund_bill_list'])) $_POST['fund_bill_list'] = stripslashes($_POST['fund_bill_list']);

        $data = $_POST;
        self::$payClass ->notify($data, function ($data){
            global $wpdb;
            $order = $wpdb->get_row('SELECT id,pay_status,cost FROM '.$wpdb->prefix.'order WHERE serialnumber='.$data['out_trade_no'], ARRAY_A);
            //file_put_contents('aaa.txt', json_decode($order, JSON_UNESCAPED_UNICODE));
            if($order && $order['pay_status'] == 1 && $order['cost'] == $data['total_amount']){
                //TODO 更新订单支付状态
                $updateData = [
                    'pay_status' => 2,
                    'pay_type' => 'zfb',
                    'pay_lowdown' => serialize($data)
                ];
                return $wpdb->update($wpdb->prefix.'order', $updateData, array('id' => $order['id']));
            }else{
                return false;
            }
        });
    }

    /**
     * 支付宝同步回调
     */
    public function zfb_returnUrl(){
        global $wpdb,$current_user;
        $row = $wpdb->get_row("select id,match_id from {$wpdb->prefix}order where serialnumber = {$_GET['serialnumber']} and user_id = {$current_user->ID}");
        if(empty($row)){
            $this->get_404('参数错误');
            return;

        };
        // TODO 查询比赛详情和订单详情
        $view = student_view_path.'paySuccess.php';
        load_view_template($view,array('row'=>$row));
    }

    /**
     * 查询订单
     */
    public function zfb_queryOrder(){
//        $id = intval($_GET['id']);
//        global $wpdb;
//        $order = $wpdb->get_row('SELECT pay_lowdown,serialnumber,pay_status,pay_type FROM '.$wpdb->prefix.'order WHERE id='.$id, ARRAY_A);
////        if($order){ //TODO 测试条件
//        if($order && $order['pay_status'] == 2 && $order['pay_type'] == 'zfb'){
//            $param = [
//              'out_trade_no' => $order['serialnumber'] = '1533519758980',
//              'trade_no' => unserialize($order['pay_lowdown'])['trade_no'] = '2018080621001004870526732375',
//            ];
////            var_dump($param);return;
//            self::$payClass->queryOrder($param);
//            //TODO
//        }else{
//            return false;
//        }
    }

    /**
     * 申请退款
     */
    public function zfb_refund(){
//        $id = intval($_GET['id']);
//        global $wpdb;
//        $order = $wpdb->get_row('SELECT pay_lowdown,serialnumber,pay_status,pay_type FROM '.$wpdb->prefix.'order WHERE id='.$id, ARRAY_A);
//        if($order){
//            //TODO
//            $param = [
//                'out_trade_no' => '1533519269594',     //商户订单号，和支付宝交易号二选一
//                'trade_no' => '2018080621001004870530336326',    //支付宝交易号，和商户订单号二选一
//                'refund_amount' => '0.01',       //退款金额，不能大于订单总金额
//                'refund_reason' => '测试退款',     //退款的原因说明
//                'out_request_no' => '123456',      //标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传。
//            ];
//            self::$payClass->refund($param);
//        }else{
//
//        }
    }

    /**
     * 查询退款
     */
    public function zfb_queryRefund(){
        $id = intval($_GET['id']);
        global $wpdb;
        $order = $wpdb->get_row('SELECT pay_lowdown,serialnumber,pay_status,pay_type FROM '.$wpdb->prefix.'order WHERE id='.$id, ARRAY_A);
        $param = [
            'out_trade_no' => '1533519269594',        //商户订单号，和支付宝交易号二选一
            'trade_no' => '2018080621001004870530336326',        //支付宝交易号，和商户订单号二选一
            'out_request_no' => '123456',        //请求退款接口时，传入的退款请求号，如果在退款请求时未传入，则该值为创建交易时的外部交易号
        ];
        self::$payClass->queryRefund($param);
    }

    /**
     * 关闭订单
     */
    public function close(){
        $id = intval($_GET['id']);
        $param = [
            'out_trade_no' => '1533295337382',//商户订单号，和支付宝交易号二选一
            'trade_no' => '2018080621001004870530336326',        //支付宝交易号，和商户订单号二选一
        ];
        self::$payClass->closeOrder($param);
    }

    /**
     * 下载对账单
     */
    public function zfb_downloadBill(){
        $date =  $_GET['date'];
        $param = [
            'bill_type' => 'trade',      //trade指商户基于支付宝交易收单的业务账单；signcustomer是指基于商户支付宝余额收入及支出等资金变动的帐务账单；
            'bill_date' => $date,       //账单时间：日账单格式为yyyy-MM-dd，月账单格式为yyyy-MM。
        ];
        $res = self::$payClass->downloadBill($param);
        if($res['status'] == true){
            header('Location: '.$res['data']);
        }else{
            echo $res['data'];
        }
    }

    /***************************************支付宝end*****************************************/
}