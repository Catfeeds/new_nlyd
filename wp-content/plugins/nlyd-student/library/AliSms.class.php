<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/6/28
 * Time: 9:45
 */

namespace library;

use Vendor\Aliyun\SignatureHelper;

class AliSms
{

    private $accessKeyId = '';
    private $accessKeySecret = '';
    private $accessHost = '';
    private $params = array ();
    public function __construct()
    {
        $interface_config = get_option('interface_config');
        if(empty($interface_config)) wp_send_json_error(array('info'=>'短信接口未配置'));
        $this->accessKeyId = $interface_config['sms']['key'];
        $this->accessKeySecret = $interface_config['sms']['secret'];
        $this->accessHost = $interface_config['sms']['host'];
    }


    /**
     * 短信模版
     * 添加一个模块,对应的阿里云上也要添加一个模版
     * @param $template_code
     */
    public function get_sms_template($template_code){
        switch ($template_code){
            case 21:
                $template['code'] =  'SMS_119635021';
                $template['title'] = '身份验证验证码';
                $template['text'] =  '验证码${code}，您正在进行身份验证，打死不要告诉别人哦！';
                break;
            case 19:
                $template['code'] =  'SMS_119635019';
                $template['title'] = '登录确认验证码';
                $template['text'] =  '验证码${code}，您正在登录，若非本人操作，请勿泄露。';
                break;
            case 18:
                $template['code'] =  'SMS_119635018';
                $template['title'] = '登录异常验证码';
                $template['text'] =  '验证码${code}，您正尝试异地登录，若非本人操作，请勿泄露。';
                break;
            case 17:
                $template['code'] =  'SMS_119635017';
                $template['title'] = '用户注册验证码';
                $template['text'] =  '验证码${code}，您正在注册成为新用户，感谢您的支持！';
                break;
            case 16:
                $template['code'] =  'SMS_119635016';
                $template['title'] = '修改密码验证码';
                $template['text'] =  '验证码${code}，您正在尝试修改登录密码，请妥善保管账户信息。';
                break;
            case 15:
                $template['code'] =  'SMS_119635015';
                $template['title'] = '信息变更验证码';
                $template['text'] =  '验证码${code}，您正在尝试变更重要信息，请妥善保管账户信息。';
                break;
            case 14:
                $template['code'] =  'SMS_142946294';
                $template['title'] = '解除教学关系';
                $template['text'] =  '尊敬的${coach}您好，ID为${user_id}的学员解除了与您的${cate}教练关系，您可登录系统查看详情。';
                break;
            case 13:
                $template['code'] =  'SMS_142946289';
                $template['title'] = '申请成为教练';
                $template['text'] =  '尊敬的${coach}您好, ID为${user}的学员申请您成为其${cate}教练, 请您尽快登录系统处理该申请';
                break;
            case 12:
                $template['code'] =  'SMS_142951263';
                $template['title'] = '退出战队申请';
                $template['text'] =  '尊敬的${teams}您好，ID为${user_id}的学员向您发送了“退出战队”申请，请您尽快登录系统处理该申请。';
                break;
            case 11:
                $template['code'] =  'SMS_142946275';
                $template['title'] = '加入战队申请';
                $template['text'] =  '尊敬的${teams}您好，ID为${user_id}的学员向您发送了“加入战队”申请，请您尽快登录系统处理该申请。';
                break;
            case 10:
                $template['code'] =  'SMS_143710292';
                $template['title'] = '同意和拒绝教练申请';
                $template['text'] =  '尊敬的${user},您申请的${cate}教练${coach}已${type}';
                break;
            case 9:
                $template['code'] =  'SMS_143715303';
                $template['title'] = '入队和退队受理';
                $template['text'] =  '尊敬的${user},您申请${applytype}战队${team}已${type}';
                break;
            case 8:
                $template['code'] =  'SMS_143711215';
                $template['title'] = '退款时发送短信';
                $template['text'] =  '尊敬的${user}, 您的订单号为${order}的订单已退款,退款金额${cost},请注意查收';
                break;
            case 7:
                $template['code'] =  'SMS_143716094';
                $template['title'] = '后台解除教学关系';
                $template['text'] =  '尊敬的${user}您好，您的${cate}教练${coach}解除了与您的教学关系，您可登录系统查看';
                break;
            case 6:
                $template['code'] =  'SMS_153886130';
                $template['title'] = '申请主体审核通知';
                $template['text'] =  '尊敬的用户您好，您的“${type_name}”申请资料审核已经完成，您可使用账号“${user_login}”密码“${password}”或使用您的个人账号登录个人中心进入控制台。';
                break;
            case 99:
                $template['code'] =  'SMS_144150453';
                $template['title'] = '临时通知';
                $template['text'] =  '.....';
                break;
            default:
                $template['code'] =  'SMS_119635020';
                $template['title'] = '短信测试';
                $template['text'] =  '尊敬的${customer}，欢迎您使用阿里云通信服务！';
                break;
        }

        return $template['code'];
    }

    /**
     * 短信发送 
     * 群发消息时 接收的参数基本都是数组 请注意传参的类型!!!!
     * @param $mobile 必填: 短信接收号码 支持JSON格式的批量调用，批量上限为100个手机号码
     * @param $template 必填: 短信模板Code，应严格按"模板CODE"填写,
     * @param $param array 必填 模板参数, 假如模板中存在变量需要替换则为必填项
     * @param $signname 必填: 短信签名，支持不同的号码发送不同的短信签名，
     * @param string $smsupextendcode 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
     */
    public function sendSms($mobile,$template,$param,$signname='',$smsupextendcode=''){
        $signname = empty($signname) ? '国际脑力运动' : $signname;
        $this->params["TemplateCode"] = $this->get_sms_template($template);

        if(is_array($mobile)){

            $this->params["PhoneNumberJson"] = $mobile;
            $this->params["SignNameJson"] = $signname;
            $this->params['TemplateParamJson'] = $param;
            if(!empty($smsupextendcode)){
                $this->params["SmsUpExtendCodeJson"] = json_encode($smsupextendcode);
            }

            // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
            $this->params["TemplateParamJson"]  = json_encode($this->params["TemplateParamJson"], JSON_UNESCAPED_UNICODE);
            $this->params["SignNameJson"] = json_encode($this->params["SignNameJson"], JSON_UNESCAPED_UNICODE);
            $this->params["PhoneNumberJson"] = json_encode($this->params["PhoneNumberJson"], JSON_UNESCAPED_UNICODE);

            if(!empty($this->params["SmsUpExtendCodeJson"] && is_array($this->params["SmsUpExtendCodeJson"]))) {
                $this->params["SmsUpExtendCodeJson"] = json_encode($this->params["SmsUpExtendCodeJson"], JSON_UNESCAPED_UNICODE);
            }

            $action = 'SendBatchSms';
            
        }else{

            $this->params['PhoneNumbers'] = $mobile;
            $this->params["SignName"] = $signname;
            $this->params["TemplateParam"] = $param;
            if(!empty($smsupextendcode)){
                $this->params["SmsUpExtendCode"] = $smsupextendcode;
            }

            // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
            if(!empty($this->params["TemplateParam"]) && is_array($this->params["TemplateParam"])) {
                $this->params["TemplateParam"] = json_encode($this->params["TemplateParam"], JSON_UNESCAPED_UNICODE);
            }

            $action = 'SendSms';
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $this->accessKeyId,
            $this->accessKeySecret,
            $this->accessHost,
            array_merge($this->params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => $action,
                "Version" => '2017-05-25',
            ))
        // fixme 选填: 启用https
        // ,true
        );
        //var_dump($content);
        if($content->Message =='OK' && $content->Code == 'OK'){

            return true;
        }else{

            return false;
        }
    }
}