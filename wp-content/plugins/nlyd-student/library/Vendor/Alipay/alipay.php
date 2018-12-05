<?php
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/service/AlipayTradeService.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/buildermodel/AlipayTradeWapPayContentBuilder.php';


class alipay {

    public static $config;

    public function __construct($appid, $private_key, $public_key)
    {
        self::$config = [
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",  //支付宝网关
            'app_id' => $appid, //应用ID,您的APPID。
            'merchant_private_key' => $private_key,  //商户私钥，您的原始格式RSA私钥
            'charset' => 'UTF-8',   //编码格式
            'sign_type' => 'RSA2' ,   //签名方式
            'alipay_public_key' => $public_key,     //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        ];
    }


    /**
     * 发起支付
     * @param $param 支付参数
     * @throws Exception
     */
    public function pay($param){
        $this->writeLog('', '支付宝支付异步回调');
//        require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/service/AlipayTradeService.php';
//        require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'./../config.php';

        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $param['out_trade_no'];

        //订单名称，必填
        $subject = $param['subject'];

        //付款金额，必填
        $total_amount = $param['total_amount'];

        //商品描述，可空
        $body = $param['body'];

        //超时时间
        $timeout_express="1m";

        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setTimeExpress($timeout_express);
//        var_dump($payRequestBuilder);return;
        $payResponse = new AlipayTradeService(self::$config);
        $result=$payResponse->wapPay($payRequestBuilder,$param['return_url'],$param['notify_url']);
//        var_dump($result);
        return ;
    }

    /**
     * 异步回调
     */
    public function notify($data,$verfiyCollback){
        $arr=$data;
        $_POST = $data;
        $alipaySevice = new AlipayTradeService(self::$config);
        $this->writeLog($data, '支付宝支付异步回调');
        $result = $alipaySevice->check($data);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代


            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号

            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号

            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];


            if($_POST['trade_status'] == 'TRADE_FINISHED') {

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序

                //TODO 注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                if(!$verfiyCollback($_POST)) return false;
                // TODO 注意：
                //付款完成后，支付宝系统发送该交易状态通知
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";		//请不要修改或删除

        }else {
            //验证失败
            echo "fail";	//请不要修改或删除

        }
    }

    /**
     * 查询订单
     */
    public function queryOrder($params){
            //商户订单号和支付宝交易号不能同时为空。 trade_no、  out_trade_no如果同时存在优先取trade_no
            //商户订单号，和支付宝交易号二选一
        require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/buildermodel/AlipayTradeQueryContentBuilder.php';
        $out_trade_no = trim($params['out_trade_no']);
//            $out_trade_no = trim($_POST['WIDout_trade_no']);

        //支付宝交易号，和商户订单号二选一
        $trade_no = trim($params['trade_no']);
//            $trade_no = trim($_POST['WIDtrade_no']);

        $RequestBuilder = new AlipayTradeQueryContentBuilder();
        $RequestBuilder->setTradeNo($trade_no);
        $RequestBuilder->setOutTradeNo($out_trade_no);

        $Response = new AlipayTradeService(self::$config);
        $result=$Response->Query($RequestBuilder);
        $result = $this->object_array($result);
        $this->writeLog($result, '查询订单');
        if($result['code'] == '10000' && $result['trade_status'] == 'TRADE_SUCCESS'){
            $res = ['status' => true, 'data' => $result];
        }else{
            if(!isset($result['sub_msg'])) $result['sub_msg'] = '未知错误';
            $res = ['status' => false, 'data' => $result['sub_msg']];
        }
        return $res;
    }

    /**
     * 申请退款
     */
    public function refund($param){
        //先查询该订单是否已经退款
        $queryRes = $this->queryRefund($param);
        if($queryRes['status'] && isset($queryRes['out_request_no']) && $queryRes['out_request_no'] == $param['out_request_no']){
            return ['status' => false, 'data' => '此订单已退款'];
        }
//        require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'service/AlipayTradeService.php';
        require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/buildermodel/AlipayTradeRefundContentBuilder.php';
//        require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'./../config.php';

        //商户订单号和支付宝交易号不能同时为空。 trade_no、  out_trade_no如果同时存在优先取trade_no
        //商户订单号，和支付宝交易号二选一
        $out_trade_no = trim($param['out_trade_no']);

        //支付宝交易号，和商户订单号二选一
        $trade_no = trim($param['trade_no']);

        //退款金额，不能大于订单总金额
        $refund_amount=trim($param['refund_amount']);

        //退款的原因说明
        $refund_reason=trim($param['refund_reason']);

        //标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传。
        $out_request_no=trim($param['out_request_no']);

        $RequestBuilder = new AlipayTradeRefundContentBuilder();
        $RequestBuilder->setTradeNo($trade_no);
        $RequestBuilder->setOutTradeNo($out_trade_no);
        $RequestBuilder->setRefundAmount($refund_amount);
        $RequestBuilder->setRefundReason($refund_reason);
        $RequestBuilder->setOutRequestNo($out_request_no);

        $Response = new AlipayTradeService(self::$config);
        $result=$Response->Refund($RequestBuilder);
        $result = $this->object_array($result);//转化数组
        $this->writeLog($result, '申请退款');
        if($result['code'] == '10000'){
            $res = ['status' => true, 'data' => $result];
        }else{
            if(isset($result['sub_msg']))
                $res = ['status' => false, 'data' => $result['sub_msg']];
            else
                $res = ['status' => false, 'data' => '未知错误'];
        }
        return $res;

    }

    /**
     * 查询退款
     */
    public function queryRefund($param){
        require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/buildermodel/AlipayTradeFastpayRefundQueryContentBuilder.php';

        //商户订单号和支付宝交易号不能同时为空。 trade_no、  out_trade_no如果同时存在优先取trade_no
        //商户订单号，和支付宝交易号二选一
        $out_trade_no = trim($param['out_trade_no']);
        //支付宝交易号，和商户订单号二选一
        $trade_no = trim($param['trade_no']);
        //请求退款接口时，传入的退款请求号，如果在退款请求时未传入，则该值为创建交易时的外部交易号
        $out_request_no = trim($param['out_request_no']);

        $RequestBuilder = new AlipayTradeFastpayRefundQueryContentBuilder();
        $RequestBuilder->setTradeNo($trade_no);
        $RequestBuilder->setOutTradeNo($out_trade_no);
        $RequestBuilder->setOutRequestNo($out_request_no);

        $Response = new AlipayTradeService(self::$config);
        $result=$Response->refundQuery($RequestBuilder);
        $result = $this->object_array($result);
        $this->writeLog($result, '查询退款');
        if($result['code'] == '10000'){
            $res = ['status' => true, 'data' => $result];
        }else{
            if(isset($result['sub_msg']))
                $res = ['status' => false, 'data' => $result['sub_msg']];
            else
                $res = ['status' => false, 'data' => '未知错误'];
        }
        return $res;
    }

    /**
     * 关闭订单
     */
    public function closeOrder($param){
        require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/buildermodel/AlipayTradeCloseContentBuilder.php';

        //商户订单号和支付宝交易号不能同时为空。 trade_no、  out_trade_no如果同时存在优先取trade_no
        //商户订单号，和支付宝交易号二选一
        $out_trade_no = trim($param['out_trade_no']);

        //支付宝交易号，和商户订单号二选一
        $trade_no = trim($param['trade_no']);

        $RequestBuilder = new AlipayTradeCloseContentBuilder();
        $RequestBuilder->setTradeNo($trade_no);
        $RequestBuilder->setOutTradeNo($out_trade_no);

        $Response = new AlipayTradeService(self::$config);
        $result=$Response->Close($RequestBuilder);
        $result = $this->object_array($result);
        $this->writeLog($result, '关闭订单');
        if($result['code'] == '10000'){
            $res = ['status' => true, 'data' => $result];
        }else{
            if(isset($result['sub_msg']))
                $res = ['status' => false, 'data' => $result['sub_msg']];
            else
                $res = ['status' => false, 'data' => '未知错误'];
        }
        return $res;
    }

    /**
     * 下载对账单
     */
    public function downloadBill($param){
        require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/buildermodel/AlipayDataDataserviceBillDownloadurlQueryContentBuilder.php';
            //账单类型，商户通过接口或商户经开放平台授权后其所属服务商通过接口可以获取以下账单类型：trade、signcustomer；
            //trade指商户基于支付宝交易收单的业务账单；signcustomer是指基于商户支付宝余额收入及支出等资金变动的帐务账单；
            $bill_type = trim($param['bill_type']);
            //账单时间：日账单格式为yyyy-MM-dd，月账单格式为yyyy-MM。
            $bill_date = trim($param['bill_date']);

            $RequestBuilder = new AlipayDataDataserviceBillDownloadurlQueryContentBuilder();
            $RequestBuilder->setBillType($bill_type);
            $RequestBuilder->setBillDate($bill_date);
            $Response = new AlipayTradeService(self::$config);
            $result=$Response->downloadurlQuery($RequestBuilder);
            $result = $this->object_array($result);
            $this->writeLog($result, '下载对账单');
            if($result['code'] == '10000'){
                $res = ['status' => true, 'data' => $result['bill_download_url']];
            }else{
                if(isset($result['sub_msg']))
                    $res = ['status' => false, 'data' => $result['sub_msg']];
                else
                    $res = ['status' => false, 'data' => '未知错误'];
            }
            return $res;
    }

    /**
     * 写入日志
     */
    public function writeLog($result, $intro){
        $data = [
            'write_time' => get_time('mysql'),
            'write_intro' => $intro,
            'data' => $result
        ];
        $file_tmp = fopen(PLUGINS_PATH.'nlyd-student/library/Vendor/Alipay/log.txt', 'a+');
        fwrite($file_tmp, "\r\n".json_encode($data, JSON_UNESCAPED_UNICODE));
        fclose($file_tmp);
    }

    /**
     * 对象转数组
     */
    private function object_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        } if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }
}