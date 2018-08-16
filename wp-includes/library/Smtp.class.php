<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/6/28
 * Time: 17:38
 */

namespace library;

class Smtp
{
    public function __construct()
    {

        /*$interface_config = get_option('interface_config');
        $smtp = $interface_config['smtp'];
        var_dump($smtp);
        include_once(ABSPATH.'\wp-includes\library\Vendor\PHPMailer\class.phpmailer.php');
        $mail = new \PHPMailer();
        $mail->Host = $smtp['host']; // SMTP 服务器

        $mail->Port = $smtp['port']; // SMTP服务器的端口号

        $mail->Username = $smtp['user_name']; // SMTP服务器用户名

        $mail->Password = $smtp['user_pass']; // SMTP服务器密码*/
    }

    /**
     * @param $data 发送内容
     * @param $template_code 内容模板
     * @return string
     */
    public function get_smtp_template($data,$template_code){

        $body = "";
        switch ($template_code){
            case 21:
                $title = '身份验证验证码';
                $body .=  "验证码{$data['code']}，您正在进行身份验证，打死不要告诉别人哦！";

                break;
            case 19:
                $title = '登录确认验证码';
                $body .=  "验证码{$data['code']}，您正在登录，若非本人操作，请勿泄露。";
                break;
            case 18:
                $title = '登录异常验证码';
                $body .=  "验证码{$data['code']}，您正尝试异地登录，若非本人操作，请勿泄露。";
                break;
            case 17:
                $title = '用户注册验证码';
                $body .=  "验证码{$data['code']}，您正在注册成为新用户，感谢您的支持！";
                break;
            case 16:
                $title = '修改密码验证码';
                $body .=  "验证码{$data['code']}，您正在尝试修改登录密码，请妥善保管账户信息。";
                break;
            case 15:
                $title = '信息变更验证码';
                $body .=  "验证码{$data['code']}，您正在尝试变更重要信息，请妥善保管账户信息。";
                break;
            default:
                $template['code'] =  'SMS_119635020';
                $title = '邮件测试';
                $template['text'] =  '尊敬的${customer}，欢迎您使用阿里云通信服务！';
                $body .=  "验证码{$data['code']}，欢迎使用邮件发送接口。";
                break;
        }

        return array('title'=>$title,'html'=>$body);
    }
}