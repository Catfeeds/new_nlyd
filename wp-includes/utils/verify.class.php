<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 提交简单匹配验证
 * 返回1或true代表验证成功
 * Class C8L_Form_Verify
 */
class Verify {

    protected $verify_name;

    function __construct() {


    }

    /**
     * 判断邮箱
     * @param $email
     * @return int
     */
    public function email($email){
        return preg_match( "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $email );
    }

    /**
     * 判断手机
     * @param $email
     * @return int
     */
    public function phone($phone){
        return preg_match("/^1[34578]\d{9}$/", $phone) ;
    }

    /**
     * 密码验证,只能输入6-20个字母、数字、下划线
     */
    public function password($password){
        return preg_match("/^(\w){6,20}$/", $password);
    }

     /**
     * 验证国内电话号码
     */
    public function telephone($telephone){
        return preg_match("\d{3}-\d{8}|\d{4}-\d{7}", $telephone);
    }

    /**
     * 简单匹配身份证，身份证为15位或18位
     */
    public function idCard($telephone){
        return preg_match("/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/", $telephone);
    }

    /**
     * 域名验证，判断域名是否有效
     */
    public function ip($ip){
        return preg_match("/^(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?/i", $ip);
    }




}