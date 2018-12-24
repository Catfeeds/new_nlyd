<?php
class Student_Payment {

    public $payClass;
    public function __construct($action = '')
    {

        if($action == 'success') $action = 'zfb_returnUrl';
        if($action != 'zfb_returnUrl'){
            if(isset($_GET['type'])){
                $type = $_GET['type'];
            }else {
                $type = $action;
            }


            $interface_config = get_option('interface_config');
            if($type == 'wxpay' || $type == 'wxpay/' || $type == 'wx_notifyUrl' || $type == 'wx_notifyUrl/' || $type == 'wx_jsApiPay' || $type == 'wx_jsApiPay/'){
                require_once leo_student_path.'library/Vendor/Wxpay/wxpay.php';
                //TODO 脑力运动
//                $interface_config['wx']['api'] = 'wxaee5846345bca60d';
//                $interface_config['wx']['merchant'] = '1494311232';
//                $interface_config['wx']['secret_key'] = '0395894147b41053e3694f742f6aebce';

                //TODO 脑力运动测试账号
                $interface_config['wx']['api'] = 'wxb575928422b38270';
                $interface_config['wx']['merchant'] = '1514508211';
//                $interface_config['wx']['secret_key'] = '1f55ec97e01f249b4ac57b7c99777173';//H5支付使用
                $interface_config['wx']['secret_key'] = 'NSvMySxKLODh4TkQhAu4j5CTqF2TkpqV';//H5支付使用
                if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false  OR (isset($_GET['jspai']) && $_GET['jspai'] = 'y')) $interface_config['wx']['secret_key'] = 'NSvMySxKLODh4TkQhAu4j5CTqF2TkpqV';//JSAPI支付使用



                //TODO 测试
//                $interface_config['wx']['api'] = 'wx4b9c68ca93325828';
//                $interface_config['wx']['merchant'] = '1495185102';
//                $interface_config['wx']['secret_key'] = 'qweQ61234Hjkasdiosd73Odcdsar72pz';

                //var_dump($interface_config['wx']);exit;
                $this->payClass = new wxpay($interface_config['wx']['api'], $interface_config['wx']['merchant'], $interface_config['wx']['secret_key']);
                $arr2 = [
                    'wx_downloadBill',
                    'wx_downloadBill/',
                    'wx_notifyUrl',
                    'wx_notifyUrl/',
                ];
                if(in_array($type,$arr2)){
                    $this->$type();
                    exit;
                }elseif (in_array($action,$arr2)){
                    $this->$action();
                    exit;
                }

            }else{
                require_once leo_student_path.'library/Vendor/Alipay/alipay.php';
                $this->payClass =  new alipay('2017123001371219',
                    'MIIEpAIBAAKCAQEAuFAjw6VL6CGjp7zxAJzcLSPXxYVHfILRukhTL6Z1ZyRBLlmTW+yFiq96+pNKSbWzmniNcsPqA4xRD0amORUiXxcfY/rlSBSxn/aIHPfLZDFhhGNxuHS1BeXQjJpzOgKzrlWZwrYvVU21Qw1Z74Jdk8TlZySeDgypBNfIKHRpni2AVo+8tT0DvvqsHqilvo8AGJI1U/pDl3TjJSAY6sp0Z/YK/rhPqlgUyzu/nlt9uRLExG4fa224EvKv+Qn/ZdTmzdzvfcxrDfo9iAXJPh1CxT8holbC8TLq+Ff5Ddh7yj/4NaF438uqSy0MwYpYW4nEDkQvvhQLC7uhRsRSpHl3TQIDAQABAoIBAQCjClkoty7Xb/Jp7gwuo5Ns5tj3I/fhn4NQyquzagdOrtZt3tUoqqhSzvn1cJd1bqMq0NsnG0EF1HjcD3343sYh4b1l3so1ogCiZR1wqo4j2j7OMn2lUq/TQMDjr7igJ0W0wIocoLZsOipO3x+ga+zFS5Y2UED0YqSc4RhxGNFZFpwEZUd34Ga1OSjKjehIEQzMnrUTNC6EwzNpkLn/oNTExKeD+uz9e9O9MMcXThaTyWxWYismxJDouVep/dEgGslZTOUxrD0iVSUERzsbWqsHFnc9bhxM3qUKzwZ3Dvpy2Uq+psM80Um1dcE6wL+XdLGi3KyayF+zUSUw9EoNg4vhAoGBAOaWEUaAVGMq0hZ6N1Cx5AC1MAh5Jc14DERDp4MgwQ347fUEZ0rhJu26czP4I67ztRuVj7LzsFSshXigHTAVKctUwH18HORxnhrVQDVAYB4ai3fskP/tdpdiVjIvxycWBtqq39MQPnxlFL7c2leoAYFDcIGy5y+elMessTVhgIn5AoGBAMygfJKGWu3OBR3LBnpuBK8gkKdDNMo7SqUjl+U5boIQwXmlJoVMbWtLdXpMjszplavDRkKQ5w2hOhndnWCEdBRVED8f6iWmQ2+n5z4pgn2AYxA7t4a5n+yVlAvTMaTRLK/DpwQl/Cf0yM/Ame5JS2WYdnaxgdDg1r6IQk5zW8z1AoGAVxZ2j9oIBSw3DKY8Hg4RvvKvoYOf82pTt7SVn8DPKSfLN67iFDXVLhQtToN5dqo0zKZAD6ZaAqDmCBjw7SgREOqBiONHRkBjJl9EUNhvdO8xnamLWh2lnKdXRr0kym5XSF8hCeYos3K50xw2msSpTNjbtSCMkD+kkYV3qGGa2oECgYAumCClkLhly/K4TQGloSWp5wVpQNFld0jQ/6DXzlMOhNg5ZdS2p6eGtgEDHympGUs+eFGoWKx0GxFK0H7EeoSgGJqBdTfw6MIUS6xJKFSRVUm5aY+putzil1DFvIpiWEsPnsKKHEglpQSQ4e9rJf9oG+Zlspe3w2rCqe5HRNdTfQKBgQDQI1BrIyEColUlIK7I0yd6z5Kzk7U57D7YYwg6vVlhKwJp0mEyeq/5o5S5xZMHw1Eyz9U0nyeHtxZYbEjWgnQWpoEIhQeG3TzuZmKCZ2UvM4iqK57pm9c5ebX0sOCfYo0VKmJ4n/5vDGN1x0iglAELKRlLDFcxnHMKNLhXC3sd2A==',
                    'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAk5Ic/oM4MnQFRtGKVvc57Erl/ownJP+dL3swjrAZPWCE0hxy3mkpxfFvogwjHkGVY+eTDfiMRwnoPyppubMDPSc/ZGtz/XvRvAmJ1BGBhLeOHz46xicqS+QK+sdxWUODBwySnepVrDnSi7cuH0/yyzF6tzyzQsFzYAEZJkv0uPx8/0I3WVwlPRK/T29Iid9SFV9nma2awNkwcjO7G6sMf1SIRXbouNVKsyXPcbjoZZEo/PzHFQ2/Rp7TPQTr+j52qgdvIGtBS+sE4TFSYt7Q/IF8fceejjQTYQzpTWDgO6bkR6Ra5tm37lQzPi4tdk+b7tCZ2bRj7IVAi3OvBEqa9QIDAQAB'
                );
            }
        }
//        echo $action;
//        die;
        $arr3 = [
            'zfb_pay',
            'zfb_returnUrl',
            'zfb_notifyUrl',
        ];

        if($action == 'wx_js_pay'){
            $this->$action();
        }
        if(in_array($action,$arr3)){
            //添加短标签
            add_shortcode('payment-home',array($this,$action));
        }elseif(in_array($type,$arr3)){

            //添加短标签
            add_shortcode('payment-home',array($this,$type));
//            return $this;
        }

    }

    /***************************************微信start*****************************************/

    /**
     * 微信支付参数
     */
    public function getWxParam($order,$is_jsapi = false){
        if($is_jsapi == true){
            $params['notify_url'] = home_url('payment/wx_notifyUrl/type/wxpay/jspai/y'); //商品描述
        }else{
            $params['notify_url'] = home_url('payment/wx_notifyUrl/type/wxpay'); //商品描述
        }
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
        $date = $_GET['date']  = '20180730'; //TODO

        $res = $this->payClass->downloadBill($date);
        if($res != ''){
            $arr = explode("\r\n", $res);
            $filename = 'wxBill_';
            $filename .= $date."_";
            $filename .= get_time().".xls";
            header('Content-Type:application/x-msexecl;name="'.$filename.'"');
            header('Content-Disposition:inline;filename="'.$filename.'"');
            require_once PLUGINS_PATH.'nlyd-student/library/Vendor/PHPExcel/Classes/PHPExcel.php';
            require_once PLUGINS_PATH.'nlyd-student/library/Vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';
            $objPHPExcel = new \PHPExcel();
            $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('left');
            foreach ($arr as $k => $row){
                $coll = 'A';
                foreach (explode(',', $row) as $v2){
//                    $html .= '<td style="text-align: center; vnd.ms-excel.numberformat:@">'.$v2.'</td>';
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coll.($k+1),' '.$v2);
                    ++$coll;
                }
            }
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');

            exit;
        }

    }

    /**
     * 微信公众号支付页面
     */
    public function wx_js_pay(){
        global $wpdb,$current_user;
        if($current_user->weChat_openid){
            $open_id = $current_user->weChat_openid;
        }else{
            require_once 'class-student-weixin.php';
            $wexinClass = new Student_Weixin('jsPayGetOpenId');
            $open_id = $wexinClass->getWebCode(true);

        }
        if(!isset($_GET['match_id']) && !isset($_GET['grad_id'])){
            $view = student_view_path.CONTROLLER.'/wxJsApiPay.php';
            load_view_template($view,array('param'=>['status' => false, 'data' => '订单信息不匹配,请联系客服'],'match_id' => 0));
            exit;
        }
        if($open_id == false){
            $result = ['status' => false, 'data' => '获取用户信息失败'];
        }else{

            $result = $this->wx_jsApiPay($open_id);
        }
        $match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;
        if($match_id < 1) $match_id =  isset($_GET['grad_id']) ? intval($_GET['grad_id']) : 0;
        //查询订单类型
        $order_type = $wpdb->get_var("SELECT order_type FROM {$wpdb->prefix}order WHERE match_id='{$match_id}'");
//        add_shortcode('payment-home',array($this,'wx_js_pay'));
        $view = student_view_path.CONTROLLER.'/wxJsApiPay.php';
        load_view_template($view,array('param'=>$result,'match_id' => $match_id,'order_type'=>$order_type));
    }

    /**
     * 微信公众号支付
     */
    public function wx_jsApiPay($open_id){
        $id = intval($_GET['id']);
        global $wpdb,$current_user;
        $order = $wpdb->get_row(
            'SELECT serialnumber,match_id,user_id,fullname,telephone,address,pay_type,cost,pay_status,created_time FROM '
//            .$wpdb->prefix.'order WHERE id='.$id.' AND user_id='.$current_user->ID.' AND pay_status=1', ARRAY_A);
            .$wpdb->prefix.'order WHERE id='.$id.' AND pay_status=1', ARRAY_A);

        if(!$order) return ['status' => false, 'data' => '订单信息缺失,请联系客服'];
        $param = $this->getWxParam($order, true);
        $param['open_id'] = $open_id;
//        if($current_user->weChat_openid){
//            $param['open_id'] = $open_id;
//        }else{
//            //TODO 获取openid
//            $param['open_id'] = false;
//        }
        //请求数据
        //1.统一下单方法
        $result = $this->payClass->jsApiPay($param);

        if($result != false){
            return $result;
        }else{
            //发起支付失败
            return ['status' => false, 'data' => '发起支付失败'];
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
        $result = $this->payClass->h5UnifiedOrder($this->getWxParam($order));
//        var_dump($result);return;
        if($result != false){
            if($result['status']){

                echo '<script>window.location.href="'.$result['data'].'&redirect_url='.urlencode(home_url('payment/success/type/wxpay/serialnumber/'.$order['serialnumber'])).'"</script>';
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
//        file_put_contents('wxNofity.txt', date_i18n('Y-m-d H:i:s', current_time('timestamp')).'执行微信支付回调');
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
        $queryRes = $this->payClass->notify(file_get_contents("php://input"), function ($out_trade_no){
            //获取订单信息回调,用于验证签名
            global $wpdb;
            $order = $wpdb->get_row(
                'SELECT serialnumber,match_id,user_id,fullname,sub_centres_id,telephone,order_type,address,pay_type,cost,pay_status,created_time FROM '
                .$wpdb->prefix.'order WHERE serialnumber='.$out_trade_no.' AND pay_status=1', ARRAY_A);
            if($order){

                $param = $this->getWxParam($order);
                $param['order_type'] = $order['order_type'];
                return $param;
            }else{
                return false;
            }
        });

        if($queryRes && $queryRes['data']['trade_state'] == 'SUCCESS'){

            // TODO 处理业务逻辑
            $serialnumber = $queryRes['notifyData']['out_trade_no'];
//            file_put_contents('aax.txt',json_encode($queryRes['order']));
            $pay_status = 2;
            switch ($queryRes['order']['order_type']){
                case 1://比赛订单
                    $pay_status = 4;
                    break;
                case 2://考级订单
                    $pay_status = 4;
                    break;
                case 3://s商品订单
                    $pay_status = 2;
                    break;
            }
            $updateData = [
                'pay_status' => $pay_status,
                'pay_type' => 'wx',
                'pay_lowdown' => serialize($queryRes['notifyData'])
            ];
            $bool = $wpdb->update($wpdb->prefix.'order', $updateData, array('serialnumber' => $serialnumber));
            if($bool){
                /*****************收益分配start*******************/
                $order = $wpdb->get_row('SELECT match_id,user_id,sub_centres_id,order_type FROM '.$wpdb->prefix.'order WHERE serialnumber='.$serialnumber, ARRAY_A);
                //获取当前比赛场景
                if($order['order_type'] == 1){
                    $income_type = 'match_id';
                    $table = $wpdb->prefix.'match_meta_new';
                    $join = "match_scene";
                    $field = 'a.match_id,a.match_scene,a.created_id,';
                }
                elseif ($order['order_type'] == 2){
                    $income_type = 'grading_id';
                    $table = $wpdb->prefix.'grading_meta';
                    $join = "scene";
                    $field = 'a.grading_id,a.scene,a.created_person,';
                }
                $sql = "select {$field} b.role_name,
                            b.role_type,b.role_alias,b.is_profit,b.status
                            from {$table} a 
                            left join {$wpdb->prefix}zone_match_role b on a.{$join} = b.id
                            where a.{$income_type} = {$order['match_id']} and b.is_profit = 1 and b.status = 1";
                $row = $wpdb->get_row($sql,ARRAY_A);
                if(!empty($row)){
                    $set_sql = "select * from {$wpdb->prefix}spread_set where spread_type = '{$row['role_alias']}' ";
                    $setting = $wpdb->get_row($set_sql,ARRAY_A);
                    if(!empty($setting)){
                        $id = $wpdb->get_var("select id from {$wpdb->prefix}user_income_logs where user_id = {$order['user_id']} and match_id = {$order['match_id']}");

                        if(empty($id)){

                            //准备数据
                            //获取直接/间接收益人
                            $sql = "select a.ID user_id,a.referee_id,b.referee_id as indirect_referee_id from {$wpdb->prefix}users a left join {$wpdb->prefix}users b on a.referee_id = b.ID where a.ID = {$order['user_id']}";
                            $user = $wpdb->get_row($sql,ARRAY_A);

                            //获取比赛/考级相关信息
                            if($order['order_type'] == 1){
                                $income_type = 'match';
                                //准备对应的数据
                                $money1 = $setting['direct_superior'];     //比赛直接推广人 direct_superior
                                $money2 = $setting['indirect_superior'];    //比赛间接推广人  indirect_superior
                                $money3 = $setting['mechanism'];   //参赛机构        mechanism
                                $money4 = $setting['sub_center'];   //办赛机构         sub_center

                                $created_id = $wpdb->get_var("select created_id from {$wpdb->prefix}match_meta_new where match_id = {$order['match_id']}");
                                //print_r($created_id);
                                $insert = array(
                                    'income_type'=>$income_type,
                                    'match_id'=>$order['match_id'],
                                    'user_id'=>$order['user_id'],
                                    'referee_id'=>$user['referee_id'] > 0 ? $user['referee_id'] : '',  //直接人
                                    'referee_income'=>$user['referee_id'] > 0 ? $money1 : '',  //直接人收益
                                    'indirect_referee_id'=>$user['indirect_referee_id'] > 0 ? $user['indirect_referee_id'] : '',    //间接人
                                    'indirect_referee_income'=>$user['indirect_referee_id'] > 0 ? $money2 : '',  //间接人收益
                                    'person_liable_id'=>$order['sub_centres_id'] > 0 ? $order['sub_centres_id'] : '',   //参赛机构
                                    'person_liable_income'=>$order['sub_centres_id'] > 0 ? $money3 : '',  //参赛机构收益
                                    'sponsor_id'=>$created_id > 0 ? $created_id : '',  //办赛机构
                                    'sponsor_income'=>$created_id > 0 ? $money4 : '',  //办赛机构收益
                                );
                                //print_r($insert);die;
                            }
                            elseif ($order['order_type'] == 2){
                                $income_type = 'grading';
                                //准备对应的数据
                                $money1 = $setting['direct_superior'];     //比赛直接推广人
                                $money2 = $setting['indirect_superior'];    //比赛间接推广人
                                $money3 = $setting['coach'];        //责任教练
                                $money4 = $setting['sub_center'];   //办赛机构

                                $grading = $wpdb->get_row("select person_liable,created_person from {$wpdb->prefix}grading_meta where grading_id = {$order['match_id']}",ARRAY_A);

                                $insert = array(
                                    'income_type'=>$income_type,
                                    'match_id'=>$order['match_id'],
                                    'user_id'=>$order['user_id'],
                                    'referee_id'=>$user['referee_id'] > 0 ? $user['referee_id'] : '',  //直接人
                                    'referee_income'=>$user['referee_id'] > 0 ? $money1 : '',  //直接人收益
                                    'indirect_referee_id'=>$user['indirect_referee_id'] > 0 ? $user['indirect_referee_id'] : '',    //间接人
                                    'indirect_referee_income'=>$user['indirect_referee_id'] > 0 ? $money2 : '',  //间接人收益
                                    'person_liable_id'=>$grading['person_liable'] > 0 ? $grading['person_liable'] : '',   //责任教练
                                    'person_liable_income'=>$grading['person_liable'] > 0 ? $money3 : '',  //参赛机构收益
                                    'sponsor_id'=>$grading['created_person'] > 0 ? $grading['created_person'] : '',  //办赛机构
                                    'sponsor_income'=>$grading['created_person'] > 0 ? $money4 : '',  //办赛机构收益
                                );
                            }
                            $insert['created_time'] = get_time('mysql');

                            $wpdb->query('START TRANSACTION');
                            $a = $wpdb->insert($wpdb->prefix.'user_income_logs',$insert);

                            $stream_id = $wpdb->get_var("select id from {$wpdb->prefix}user_stream_logs where user_id = {$insert['sponsor_id']} and user_type = 1 and match_id = {$order['match_id']}");
                            //var_dump($stream_id);
                            if(empty($stream_id)){
                                $b = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('user_id'=>$insert['sponsor_id'],'user_type'=>1,'income_type'=>$income_type,'match_id'=>$order['match_id'],'created_time'=>get_time('mysql')));
                            }
                            else{
                                $b = true;
                            }
                            //print_r($a .'************'. $b);die;
                            if($a && $b){
                                $wpdb->query('COMMIT');
                            }else{
                                $wpdb->query('ROLLBACK');
                            }

                        }

                    }
                }


                /*****************收益分配end*******************/
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
        $queryRes = $this->payClass->orderQuery($param); //return array
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
        var_dump($this->payClass->refund($param));
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
        $res = $this->payClass->refundQuery($param);
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
            'SELECT serialnumber,match_id,order_type,sub_centres_id,user_id,fullname,telephone,address,pay_type,cost,pay_status,created_time FROM '
            .$wpdb->prefix.'order WHERE id='.$id.' AND user_id='.$current_user->ID.' AND pay_status=1', ARRAY_A);
        if(!$order){
//            $this->get_404('参数错误');
            return;
        }
        //请求参数
        $param = [
            'notify_url'    => home_url('payment/zfb_notifyUrl/type/alipay'),
            'return_url'    => home_url('payment/zfb_returnUrl/type/alipay/serialnumber/'.$order['serialnumber']),
            'out_trade_no'  => $order['serialnumber'],
            'subject'       => '脑力中国',
//            'total_amount'  =>  0.01,
            'total_amount'  => $order['cost'],
            'body'  => '', //商品描述,可空
        ];

        /*****************收益分配start*******************/
        //获取当前比赛场景
        /*if($order['order_type'] == 1){
            $income_type = 'match_id';
            $table = $wpdb->prefix.'match_meta_new';
            $field = 'a.match_id,a.match_scene,a.created_id,';
            $join = "match_scene";
        }
        elseif ($order['order_type'] == 2){
            $income_type = 'grading_id';
            $table = $wpdb->prefix.'grading_meta';
            $field = 'a.grading_id,a.scene,b.role_name,a.created_person,';
            $join = "scene";
            //left join {$wpdb->prefix}zone_meta c on a.created_person = c.id
        }
        $sql = "select {$field}
                b.role_name,b.role_type,b.role_alias,b.is_profit,b.status
                from {$table} a 
                left join {$wpdb->prefix}zone_match_role b on a.{$join} = b.id
                where a.{$income_type} = {$order['match_id']} and b.is_profit = 1 and b.status = 1";
        //print_r($sql);die;
        $row = $wpdb->get_row($sql,ARRAY_A);
        if(!empty($row)){
            $set_sql = "select * from {$wpdb->prefix}spread_set where spread_type = '{$row['role_alias']}' ";
            $setting = $wpdb->get_row($set_sql,ARRAY_A);

            if(!empty($setting)){
                $id = $wpdb->get_var("select id from {$wpdb->prefix}user_income_logs where user_id = {$order['user_id']} and match_id = {$order['match_id']}");

                if(empty($id)){

                    //准备数据
                    //获取直接/间接收益人
                    $sql = "select a.ID user_id,a.referee_id,b.referee_id as indirect_referee_id from {$wpdb->prefix}users a left join {$wpdb->prefix}users b on a.referee_id = b.ID where a.ID = {$order['user_id']}";
                    $user = $wpdb->get_row($sql,ARRAY_A);

                    //获取比赛/考级相关信息
                    if($order['order_type'] == 1){
                        $income_type = 'match';
                        //准备对应的数据
                        $money1 = $setting['direct_superior'];     //比赛直接推广人 direct_superior
                        $money2 = $setting['indirect_superior'];    //比赛间接推广人  indirect_superior
                        $money3 = $setting['mechanism'];   //参赛机构        mechanism
                        $money4 = $setting['sub_center'];   //办赛机构         sub_center

                        $created_id = $wpdb->get_var("select created_id from {$wpdb->prefix}match_meta_new where match_id = {$order['match_id']}");
                        //print_r($created_id);
                        $insert = array(
                            'income_type'=>$income_type,
                            'match_id'=>$order['match_id'],
                            'user_id'=>$order['user_id'],
                            'referee_id'=>$user['referee_id'] > 0 ? $user['referee_id'] : '',  //直接人
                            'referee_income'=>$user['referee_id'] > 0 ? $money1 : '',  //直接人收益
                            'indirect_referee_id'=>$user['indirect_referee_id'] > 0 ? $user['indirect_referee_id'] : '',    //间接人
                            'indirect_referee_income'=>$user['indirect_referee_id'] > 0 ? $money2 : '',  //间接人收益
                            'person_liable_id'=>$order['sub_centres_id'] > 0 ? $order['sub_centres_id'] : '',   //参赛机构
                            'person_liable_income'=>$order['sub_centres_id'] > 0 ? $money3 : '',  //参赛机构收益
                            'sponsor_id'=>$created_id > 0 ? $created_id : '',  //办赛机构
                            'sponsor_income'=>$created_id > 0 ? $money4 : '',  //办赛机构收益
                        );
                        //print_r($insert);die;
                    }
                    elseif ($order['order_type'] == 2){
                        $income_type = 'grading';
                        //准备对应的数据
                        $money1 = $setting['direct_superior'];     //比赛直接推广人
                        $money2 = $setting['indirect_superior'];    //比赛间接推广人
                        $money3 = $setting['coach'];        //责任教练
                        $money4 = $setting['sub_center'];   //办赛机构

                        $grading = $wpdb->get_row("select person_liable,created_person from {$wpdb->prefix}grading_meta where grading_id = {$order['match_id']}",ARRAY_A);

                        $insert = array(
                            'income_type'=>$income_type,
                            'match_id'=>$order['match_id'],
                            'user_id'=>$order['user_id'],
                            'referee_id'=>$user['referee_id'] > 0 ? $user['referee_id'] : '',  //直接人
                            'referee_income'=>$user['referee_id'] > 0 ? $money1 : '',  //直接人收益
                            'indirect_referee_id'=>$user['indirect_referee_id'] > 0 ? $user['indirect_referee_id'] : '',    //间接人
                            'indirect_referee_income'=>$user['indirect_referee_id'] > 0 ? $money2 : '',  //间接人收益
                            'person_liable_id'=>$grading['person_liable'] > 0 ? $grading['person_liable'] : '',   //责任教练
                            'person_liable_income'=>$grading['person_liable'] > 0 ? $money3 : '',  //参赛机构收益
                            'sponsor_id'=>$grading['created_person'] > 0 ? $grading['created_person'] : '',  //办赛机构
                            'sponsor_income'=>$grading['created_person'] > 0 ? $money4 : '',  //办赛机构收益
                        );
                    }
                    $insert['created_time'] = get_time('mysql');

                    $wpdb->query('START TRANSACTION');
                    $a = $wpdb->insert($wpdb->prefix.'user_income_logs',$insert);

                    $stream_id = $wpdb->get_var("select id from {$wpdb->prefix}user_stream_logs where user_id = {$insert['sponsor_id']} and user_type = 1 and match_id = {$order['match_id']}");
                    //var_dump($stream_id);
                    if(empty($stream_id)){
                        $b = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('user_id'=>$insert['sponsor_id'],'user_type'=>1,'income_type'=>$income_type,'match_id'=>$order['match_id'],'created_time'=>get_time('mysql')));
                    }
                    else{
                        $b = true;
                    }
                    //print_r($a .'************'. $b);die;
                    if($a && $b){
                        $wpdb->query('COMMIT');
                    }else{
                        $wpdb->query('ROLLBACK');
                    }

                }
            }
        }*/

        /*****************收益分配end*******************/
        $this->payClass->pay($param);
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
        $this->payClass ->notify($data, function ($data){
            global $wpdb;
            $order = $wpdb->get_row('SELECT id,order_type,match_id,user_id,sub_centres_id,pay_status,cost,order_type FROM '.$wpdb->prefix.'order WHERE serialnumber='.$data['out_trade_no'], ARRAY_A);
            //file_put_contents('aaa.txt', json_decode($order, JSON_UNESCAPED_UNICODE));
            if($order && $order['pay_status'] == 1 && $order['cost'] == $data['total_amount']){
                //TODO 更新订单支付状态
                switch ($order['order_type']){
                    case 1://比赛订单
                        $pay_status = 4;
                        break;
                    case 2://考级订单
                        $pay_status = 4;
                        break;
                    case 3://商品订单
                        $pay_status = 2;
                        break;
                }
                $updateData = [
                    'pay_status' => $pay_status,
                    'pay_type' => 'zfb',
                    'pay_lowdown' => serialize($data)
                ];
                $result = $wpdb->update($wpdb->prefix.'order', $updateData, array('id' => $order['id']));
                if($result){
                    /*****************收益分配start*******************/

                    //获取当前比赛场景
                    if($order['order_type'] == 1){
                        $income_type = 'match_id';
                        $table = $wpdb->prefix.'match_meta_new';
                        $join = "match_scene";
                        $field = 'a.match_id,a.match_scene,a.created_id,';
                    }
                    elseif ($order['order_type'] == 2){
                        $income_type = 'grading_id';
                        $table = $wpdb->prefix.'grading_meta';
                        $join = "scene";
                        $field = 'a.grading_id,a.scene,a.created_person,';
                    }
                    $sql = "select {$field} b.role_name,
                            b.role_type,b.role_alias,b.is_profit,b.status
                            from {$table} a 
                            left join {$wpdb->prefix}zone_match_role b on a.{$join} = b.id
                            where a.{$income_type} = {$order['match_id']} and b.is_profit = 1 and b.status = 1";
                    $row = $wpdb->get_row($sql,ARRAY_A);
                    if(!empty($row)){
                        $set_sql = "select * from {$wpdb->prefix}spread_set where spread_type = '{$row['role_alias']}' ";
                        $setting = $wpdb->get_row($set_sql,ARRAY_A);
                        if(!empty($setting)){
                            $id = $wpdb->get_var("select id from {$wpdb->prefix}user_income_logs where user_id = {$order['user_id']} and match_id = {$order['match_id']}");

                            if(empty($id)){

                                //准备数据
                                //获取直接/间接收益人
                                $sql = "select a.ID user_id,a.referee_id,b.referee_id as indirect_referee_id from {$wpdb->prefix}users a left join {$wpdb->prefix}users b on a.referee_id = b.ID where a.ID = {$order['user_id']}";
                                $user = $wpdb->get_row($sql,ARRAY_A);

                                //获取比赛/考级相关信息
                                if($order['order_type'] == 1){
                                    $income_type = 'match';
                                    //准备对应的数据
                                    $money1 = $setting['direct_superior'];     //比赛直接推广人 direct_superior
                                    $money2 = $setting['indirect_superior'];    //比赛间接推广人  indirect_superior
                                    $money3 = $setting['mechanism'];   //参赛机构        mechanism
                                    $money4 = $setting['sub_center'];   //办赛机构         sub_center

                                    $created_id = $wpdb->get_var("select created_id from {$wpdb->prefix}match_meta_new where match_id = {$order['match_id']}");
                                    //print_r($created_id);
                                    $insert = array(
                                        'income_type'=>$income_type,
                                        'match_id'=>$order['match_id'],
                                        'user_id'=>$order['user_id'],
                                        'referee_id'=>$user['referee_id'] > 0 ? $user['referee_id'] : '',  //直接人
                                        'referee_income'=>$user['referee_id'] > 0 ? $money1 : '',  //直接人收益
                                        'indirect_referee_id'=>$user['indirect_referee_id'] > 0 ? $user['indirect_referee_id'] : '',    //间接人
                                        'indirect_referee_income'=>$user['indirect_referee_id'] > 0 ? $money2 : '',  //间接人收益
                                        'person_liable_id'=>$order['sub_centres_id'] > 0 ? $order['sub_centres_id'] : '',   //参赛机构
                                        'person_liable_income'=>$order['sub_centres_id'] > 0 ? $money3 : '',  //参赛机构收益
                                        'sponsor_id'=>$created_id > 0 ? $created_id : '',  //办赛机构
                                        'sponsor_income'=>$created_id > 0 ? $money4 : '',  //办赛机构收益
                                    );
                                    //print_r($insert);die;
                                }
                                elseif ($order['order_type'] == 2){
                                    $income_type = 'grading';
                                    //准备对应的数据
                                    $money1 = $setting['direct_superior'];     //比赛直接推广人
                                    $money2 = $setting['indirect_superior'];    //比赛间接推广人
                                    $money3 = $setting['coach'];        //责任教练
                                    $money4 = $setting['sub_center'];   //办赛机构

                                    $grading = $wpdb->get_row("select person_liable,created_person from {$wpdb->prefix}grading_meta where grading_id = {$order['match_id']}",ARRAY_A);

                                    $insert = array(
                                        'income_type'=>$income_type,
                                        'match_id'=>$order['match_id'],
                                        'user_id'=>$order['user_id'],
                                        'referee_id'=>$user['referee_id'] > 0 ? $user['referee_id'] : '',  //直接人
                                        'referee_income'=>$user['referee_id'] > 0 ? $money1 : '',  //直接人收益
                                        'indirect_referee_id'=>$user['indirect_referee_id'] > 0 ? $user['indirect_referee_id'] : '',    //间接人
                                        'indirect_referee_income'=>$user['indirect_referee_id'] > 0 ? $money2 : '',  //间接人收益
                                        'person_liable_id'=>$grading['person_liable'] > 0 ? $grading['person_liable'] : '',   //责任教练
                                        'person_liable_income'=>$grading['person_liable'] > 0 ? $money3 : '',  //参赛机构收益
                                        'sponsor_id'=>$grading['created_person'] > 0 ? $grading['created_person'] : '',  //办赛机构
                                        'sponsor_income'=>$grading['created_person'] > 0 ? $money4 : '',  //办赛机构收益
                                    );
                                }
                                $insert['created_time'] = get_time('mysql');

                                $wpdb->query('START TRANSACTION');
                                $a = $wpdb->insert($wpdb->prefix.'user_income_logs',$insert);

                                $stream_id = $wpdb->get_var("select id from {$wpdb->prefix}user_stream_logs where user_id = {$insert['sponsor_id']} and user_type = 1 and match_id = {$order['match_id']}");
                                //var_dump($stream_id);
                                if(empty($stream_id)){
                                    $b = $wpdb->insert($wpdb->prefix.'user_stream_logs',array('user_id'=>$insert['sponsor_id'],'user_type'=>1,'income_type'=>$income_type,'match_id'=>$order['match_id'],'created_time'=>get_time('mysql')));
                                }
                                else{
                                    $b = true;
                                }
                                //print_r($a .'************'. $b);die;
                                if($a && $b){
                                    $wpdb->query('COMMIT');
                                }else{
                                    $wpdb->query('ROLLBACK');
                                }

                            }

                        }
                    }

                    /*****************收益分配end*******************/
                    return $result;
                }
            }else{
                return false;
            }
        });
    }

    /**
     * 支付宝同步回调
     */
    public function zfb_returnUrl(){
        file_put_contents('aaa.txt', '执行支付宝回调');
        global $wpdb,$current_user;
        $row = $wpdb->get_row("select id,match_id,order_type from {$wpdb->prefix}order where serialnumber = {$_GET['serialnumber']} and user_id = {$current_user->ID}");
        wp_register_style( 'userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'userCenter' );
        wp_register_style( 'paySuccess', student_css_url.'paySuccess.css',array('my-student') );
        wp_enqueue_style( 'paySuccess' );
        if(empty($row)){
            $view = leo_student_public_view.'my-404.php';

            $data['message'] = '未找到数据';
            load_view_template($view,$data);
            return;
        }
        // TODO 查询比赛详情和订单详情

        //获取比赛名字
        $match_title = $wpdb->get_var("select post_title as match_title from {$wpdb->prefix}posts where ID = {$row->match_id}");
        $view = student_view_path.CONTROLLER.'/paySuccess.php';
//        load_view_template($view);
        load_view_template($view,array('row'=>$row,'match_title'=>$match_title));

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
//            $this->payClass->queryOrder($param);
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
//            $this->payClass->refund($param);
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
        $this->payClass->queryRefund($param);
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
        $this->payClass->closeOrder($param);
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
        $res = $this->payClass->downloadBill($param);
        if($res['status'] == true){
            header('Location: '.$res['data']);
        }else{
            echo $res['data'];
        }
    }

    /***************************************支付宝end*****************************************/
}