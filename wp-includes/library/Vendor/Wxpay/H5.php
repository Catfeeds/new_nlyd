<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/28
 * Time: 11:22
 */






class wechatAppPay
{
//接口API URL前缀
    const API_URL_PREFIX = 'https://api.mch.weixin.qq.com';
//下单地址URL
    const UNIFIEDORDER_URL = "/pay/unifiedorder";
//查询订单URL
    const ORDERQUERY_URL = "/pay/orderquery";
//关闭订单URL
    const CLOSEORDER_URL = "/pay/closeorder";
//申请退款URL
    const REFUND_URL = "/secapi/pay/refund";
//公众账号ID
    private $appid = 'wx4b9c68ca93325828';
//商户号
    private $mch_id;
//随机字符串
    private $nonce_str;
//签名
    private $sign;
//商品描述
    private $body;
//商户订单号
    private $out_trade_no;
//支付总金额
    private $total_fee;
//终端IP
    private $spbill_create_ip;
//支付结果回调通知地址
    private $notify_url;
//交易类型
    private $trade_type;
//支付密钥
    private $key;
//证书路径
    private $SSLCERT_PATH;
    private $SSLKEY_PATH;
//所有参数
    private $params = array();
    public function __construct($appid, $mch_id, $key)
    {
        $this->appid = $appid;
        $this->mch_id = $mch_id;
//        $this->notify_url = $notify_url;
        $this->key = $key;
    }
    /**
     * 下单方法
     * @param $params 下单参数
     */
    public function unifiedOrder( $params ){
        $this->body = $params['body'];
        $this->out_trade_no = $params['out_trade_no'];
        $this->total_fee = $params['total_fee'];
        $this->trade_type = $params['trade_type'];
        $this->nonce_str = $this->genRandomString();
        $this->spbill_create_ip = $this->get_client_ip();
        $this->params['appid'] = $this->appid;
        $this->params['mch_id'] = $this->mch_id;
        $this->params['nonce_str'] = $this->nonce_str;
        $this->params['body'] = $this->body;
        $this->params['attach'] = $params['attach'];
        $this->params['out_trade_no'] = $this->out_trade_no;
        $this->params['total_fee'] = $this->total_fee;
        $this->params['spbill_create_ip'] = $this->spbill_create_ip;
        $this->params['notify_url'] = $params['http://nlyd.com'];
        $this->params['trade_type'] = $this->trade_type;
//获取签名数据

        $this->sign = $this->MakeSign( $this->params );
        $this->params['sign'] = $this->sign;
        $xml = $this->data_to_xml($this->params);
        $response = $this->postXmlCurl($xml, self::API_URL_PREFIX.self::UNIFIEDORDER_URL);
        if( !$response ){
            return false;
        }
        $result = $this->xml_to_data( $response );
        if( !empty($result['result_code']) && !empty($result['err_code']) ){
            $result['err_msg'] = $this->error_code( $result['err_code'] );
        }
        return $result;
    }


    /**
     * 查询订单信息
     * @param $out_trade_no 订单号
     * @return array
     */
    public function orderQuery( $noArr ){
        $this->params['appid'] = $this->appid;
        $this->params['mch_id'] = $this->mch_id;
        $this->params['transaction_id'] = $noArr['transaction_id'];//微信订单号,与商户订单号二选一, (建议优先使用)
        $this->params['nonce_str'] = $this->genRandomString();
        $this->params['out_trade_no'] = $noArr['order_no'];
//获取签名数据
        $this->sign = $this->MakeSign( $this->params );
        $this->params['sign'] = $this->sign;
        $xml = $this->data_to_xml($this->params);
        $response = $this->postXmlCurl($xml, self::API_URL_PREFIX.self::ORDERQUERY_URL);
        if( !$response ){
            return false;
        }
        $result = $this->xml_to_data( $response );
        if( !empty($result['result_code']) && !empty($result['err_code']) ){
            $result['err_msg'] = $this->error_code( $result['err_code'] );
        }
        return $result;
    }
    /**
     * 关闭订单
     * @param $out_trade_no 订单号
     * @return array
     */
    public function closeOrder( $out_trade_no ){
        $this->params['appid'] = $this->appid;
        $this->params['mch_id'] = $this->mch_id;
        $this->params['nonce_str'] = $this->genRandomString();
        $this->params['out_trade_no'] = $out_trade_no;
//获取签名数据
        $this->sign = $this->MakeSign( $this->params );
        $this->params['sign'] = $this->sign;
        $xml = $this->data_to_xml($this->params);
        $response = $this->postXmlCurl($xml, self::API_URL_PREFIX.self::CLOSEORDER_URL);
        if( !$response ){
            return false;
        }
        $result = $this->xml_to_data( $response );
        return $result;
    }
    /**
     *
     * 获取支付结果通知数据
     * return array
     */
    public function getNotifyData(){
//获取通知的数据
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $data = array();
        if( empty($xml) ){
            return false;
        }
        $data = $this->xml_to_data( $xml );
        if( !empty($data['return_code']) ){
            if( $data['return_code'] == 'FAIL' ){
                return false;
            }
        }
        return $data;
    }
    /**
     * 接收通知成功后应答输出XML数据
     * @param string $xml
     */
    public function replyNotify(){
        $data['return_code'] = 'SUCCESS';
        $data['return_msg'] = 'OK';
        $xml = $this->data_to_xml( $data );
        echo $xml;
        die();
    }
    /**
     * 生成APP端支付参数
     * @param $prepayid 预支付id
     */
    public function getAppPayParams( $prepayid ){
        $data['appid'] = $this->appid;
        $data['partnerid'] = $this->mch_id;
        $data['prepayid'] = $prepayid;
        $data['package'] = 'Sign=WXPay';
        $data['noncestr'] = $this->genRandomString();
        $data['timestamp'] = time();
        $data['sign'] = $this->MakeSign( $data );
        return $data;
    }
    /**
     * 生成签名
     * @return 签名
     */
    public function MakeSign( $params ){
//签名步骤一：按字典序排序数组参数
        ksort($params);
        $string = $this->ToUrlParams($params);
//签名步骤二：在string后加入KEY
        $string = $string . "&key=".$this->key;
//签名步骤三：MD5加密
        $string = md5($string);
//签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    /**
     * 将参数拼接为url: key=value&key=value
     * @param $params
     * @return string
     */
    public function ToUrlParams( $params ){
        $string = '';
        if( !empty($params) ){
            $array = array();
            foreach( $params as $key => $value ){
                $array[] = $key.'='.$value;
            }
            $string = implode("&",$array);
        }
        return $string;
    }
    /**
     * 输出xml字符
     * @param $params 参数名称
     * return string 返回组装的xml
     **/
    public function data_to_xml( $params ){
        if(!is_array($params)|| count($params) <= 0)
        {
            return false;
        }
        $xml = "<xml>";
        foreach ($params as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
    /**
     * 将xml转为array
     * @param string $xml
     * return array
     */
    public function xml_to_data($xml){
        if(!$xml){
            return false;
        }
//将XML转为array
//禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }
    /**
     * 获取毫秒级别的时间戳
     */
    private static function getMillisecond(){
//获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }
    /**
     * 产生一个指定长度的随机字符串,并返回给用户
     * @param type $len 产生字符串的长度
     * @return string 随机字符串
     */
    private function genRandomString($len = 32) {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
// 将数组打乱
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }
    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml 需要post的xml数据
     * @param string $url url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second url执行超时时间，默认30s
     * @throws WxPayException
     */
    public function postXmlCurl($xml, $url, $useCert = false, $second = 30){
        $ch = curl_init();
//设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
//设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if($useCert == true){
//设置证书
//使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
curl_setopt($ch,CURLOPT_SSLCERT, './wp-includes/library/Vendor/Wxpay/cert/apiclient_cert.pem');
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
curl_setopt($ch,CURLOPT_SSLKEY, './wp-includes/library/Vendor/Wxpay/cert/apiclient_key.pem');
        }
//post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
//运行curl
        $data = curl_exec($ch);
//返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return false;
        }
    }
    /**
     * 错误代码
     * @param $code 服务器输出的错误代码
     * return string
     */
    public function error_code( $code ){
        $errList = array(
            'NOAUTH' => '商户未开通此接口权限',
            'NOTENOUGH' => '用户帐号余额不足',
            'ORDERNOTEXIST' => '订单号不存在',
            'ORDERPAID' => '商户订单已支付，无需重复操作',
            'ORDERCLOSED' => '当前订单已关闭，无法支付',
            'SYSTEMERROR' => '系统错误!系统超时',
            'APPID_NOT_EXIST' => '参数中缺少APPID',
            'MCHID_NOT_EXIST' => '参数中缺少MCHID',
            'APPID_MCHID_NOT_MATCH' => 'appid和mch_id不匹配',
            'LACK_PARAMS' => '缺少必要的请求参数',
            'OUT_TRADE_NO_USED' => '同一笔交易不能多次提交',
            'SIGNERROR' => '参数签名结果不正确',
            'XML_FORMAT_ERROR' => 'XML格式错误',
            'REQUIRE_POST_METHOD' => '未使用post传递参数 ',
            'POST_DATA_EMPTY' => 'post数据不能为空',
            'NOT_UTF8' => '未使用指定编码格式',
        );
        if( array_key_exists( $code , $errList ) ){
            return $errList[$code];
        }
    }


    /**
     * 获取客户端ip
     */
    public function get_client_ip(){
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown')) {

            $ip = getenv('HTTP_CLIENT_IP');

        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'),'unknown')) {

            $ip = getenv('HTTP_X_FORWARDED_FOR');

        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'),'unknown')) {

            $ip = getenv('REMOTE_ADDR');

        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {

            $ip = $_SERVER['REMOTE_ADDR'];

        }

        return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    }


    /**
     * @param array $param 订单参数
     * TODO 注意 :
     *  1、交易时间超过一年的订单无法提交退款

        2、微信支付退款支持单笔交易分多次退款，多次退款需要提交原支付订单的商户订单号和设置不同的退款单号。申请退款总金额不能超过订单金额。
           一笔退款失败后重新提交，请不要更换退款单号，请使用原商户退款单号

        3、请求频率限制：150qps，即每秒钟正常的申请退款请求次数不超过150次错误或无效请求频率限制：6qps，即每秒钟异常或错误的退款申请请求不超过6次

        4、每个支付订单的部分退款次数不能超过50次
     */
    public function refund($param) {
        $this->params['appid'] = $this->appid;
        $this->params['mch_id'] = $this->mch_id;
        $this->params['nonce_str'] = $this->genRandomString();
        $this->params['transaction_id'] = $param['transaction_id'];
        $this->params['out_trade_no'] = $param['order_no'];
        $this->params['out_refund_no'] = $param['out_refund_no']; //商户系统内部的退款单号，商户系统内部唯一，只能是数字、大小写字母_-|*@ ，同一退款单号多次请求只退一笔。
        $this->params['total_fee'] = $param['price']*100; //订单金额
        $this->params['refund_fee'] = $param['refund_price']*100; //退款金额
        $this->params['sign'] =  $this->sign = $this->MakeSign( $this->params );
        $requestXml = $this->data_to_xml($this->params);
        $requestXml = '<xml>
   <appid>wx4b9c68ca93325828</appid>
   <mch_id>1495185102</mch_id>
   <nonce_str>6cefdb308e1e2e8aabd48cf79e546a02</nonce_str> 
   <out_refund_no>1415701182</out_refund_no>
   <out_trade_no>1415757673</out_trade_no>
   <refund_fee>1</refund_fee>
   <total_fee>1</total_fee>
   <transaction_id></transaction_id>
   <sign>FE56DD4AA85C0EECA82C35595A69E153</sign>
</xml>';
        $requestXml = $this->data_to_xml($this->xml_to_data($requestXml));

        $responseXml = $this->postXmlCurl($requestXml, self::API_URL_PREFIX.self::REFUND_URL, true);
        $responseArr = $this->xml_to_data($responseXml);
        echo '<pre />';
        var_dump($responseXml);
    }
}
?>






