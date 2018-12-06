<?php

/**
 * 学生-安全中心
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Safety extends Student_Home
{
    private $ajaxControll;
    public function __construct($action)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        $this->ajaxControll = new Student_Ajax();
        //添加短标签
        add_shortcode('safety-home',array($this,$action));
    }

    /**
     * 获取用户二维码
     * @param $code
     * @return string
     */
    function ly_phpqrcode( $code )
    {

        $upload_dir = wp_upload_dir();
        // print_r($upload_dir);

        $code_baseurl = $upload_dir['baseurl'] . '/account/qrcode/' . $code . '.png';
        $code_basedir = $upload_dir['basedir'] . '/account/qrcode/' . $code . '.png';

        if (!file_exists($code_basedir)) {
            //if(  file_exists( $code_basedir ) ){

            $file = WP_L8Y_PATH . 'includes/utils/phpqrcode/phpqrcode.php';
            if (file_exists($file)) {

                require_once($file);

                ly_mkdirs($upload_dir['basedir'] . '/account/qrcode/');//创建路径
                //header('Content-Type: image/png');
                $value = home_url('login/?invite_code=' . $code); //二维码内容
                $errorCorrectionLevel = 'M';//容错级别
                $matrixPointSize = 6;//生成图片大小
                //生成二维码图片
                QRcode::png($value, $code_basedir, $errorCorrectionLevel, $matrixPointSize, 2);

                $back_logo = WP_L8Y_PATH . 'templates/_inc/img/lywlfx_img.jpg';//准备好背景图片
                $logo = WP_L8Y_PATH . 'templates/_inc/img/qrcode-logo.png';//准备好的logo图片
                $qr_code = $code_basedir; //已经生成的原始二维码图

                $qr_code = imagecreatefromstring(file_get_contents($qr_code));
                $logo = imagecreatefromstring(file_get_contents($logo));

                $qr_code_width = imagesx($qr_code);//二维码图片宽度
                $qr_code_height = imagesy($qr_code);//二维码图片高度
                $logo_width = imagesx($logo);//logo图片宽度
                $logo_height = imagesy($logo);//logo图片高度
                $logo_qr_width = $qr_code_width / 5;
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                $from_width = ($qr_code_width - $logo_qr_width) / 2;

                //第一次组合，logo嵌入二维码
                imagecopyresampled($qr_code, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
            }
        }
    }

    public function qrcode(){

        include_once leo_student_path."library/Vendor/phpqrcode/phpqrcode.php"; //引入PHP QR库文件
        global $current_user;
        $value="https://www.baidu.com";
        $upload_dir = wp_upload_dir();
        $dir = '/spread/'.$current_user->ID.'/';
        $path = $upload_dir['basedir'].$dir;
        if(!file_exists($path)){
            mkdir($path,0755,true);
        }
        $filename = date('YmdHis').'_'.rand(1000,9999).'.jpg';          //定义图片名字及格式
        $qrcode_path = $path.$filename;

        $errorCorrectionLevel = "L"; //容错级别
        $matrixPointSize = "6"; //生成图片大小
        QRcode::png($value, $qrcode_path, $errorCorrectionLevel, $matrixPointSize, 2);
        //生成带logo的二维码
        $logo = 'E:\php\tool\wamp64\www\nlyd\wp-content\plugins\nlyd-student\Public\css\image\logo1.jpg';
        //die;
        if ($logo !== FALSE) {
            $QR = imagecreatefromstring ( file_get_contents ( $qrcode_path ) );
            $logo = imagecreatefromstring ( file_get_contents ( $logo ) );
            $QR_width = imagesx ( $QR );
            $QR_height = imagesy ( $QR );
            $logo_width = imagesx ( $logo );
            $logo_height = imagesy ( $logo );
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            imagecopyresampled ( $QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height );
        }
        imagejpeg ( $QR, $qrcode_path );//带Logo二维码的文件名
        //die;
        $back = 'E:\php\tool\wamp64\www\nlyd\wp-content\plugins\nlyd-student\Public\css\image\test.jpg';
        if ($back !== FALSE) {
            $back_ = imagecreatefromstring ( file_get_contents ( $back ) );
            $qrcode = imagecreatefromstring ( file_get_contents ( $qrcode_path ) );
           // $back_width = imagesx ( $back_ );
            //$back_height = imagesy ( $back_ );
            $qrcode_width = imagesx ( $qrcode );
            $qrcode_height = imagesy ( $qrcode );
            /*$logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;*/
            imagecopyresampled ( $back_, $qrcode, 95, 195, 0, 0, $qrcode_width, $qrcode_height, $qrcode_width, $qrcode_height );
        }
        imagejpeg ( $back_, $qrcode_path );//带Logo二维码的文件名
    }


    /*
     * 设置
     */
    public function setting(){

        $view = student_view_path.CONTROLLER.'/setting.php';
        load_view_template($view);
    }

    /**
     *隐私协议
     *
     **/
    public function privacyAgreement(){
        $view = student_view_path.CONTROLLER.'/privacyAgreement.php';
        load_view_template($view);
    }

    /**
     *建议反馈
     *
     **/
    public function suggest(){
        $view = student_view_path.CONTROLLER.'/suggest.php';
        load_view_template($view);
    }
    /**
     *
     *用户协议
     *
     **/
    public function userAgreement(){
        $view = student_view_path.CONTROLLER.'/userAgreement.php';
        load_view_template($view);
    }

    /**
     *安全设置详细项目
     *
     **/
     public function safetySetting(){
        global $user_info;

        //重置密码/更换手机号/绑定手机号/绑定邮箱
         switch ($_GET['type']){
             case 'pass':
                $title = __('密码重置', 'nlyd-student');
                break;
             case 'mobile':
                 $title = __('手机绑定', 'nlyd-student');
                 break;
             case 'email':
                 $title = __('邮箱绑定', 'nlyd-student');
                 break;
             case 'weChat':
                 $title = __('微信绑定', 'nlyd-student');
                 break;
             case 'qq':
                 $title = __('QQ绑定', 'nlyd-student');
                 break;
             default:
                 $title=__('安全中心', 'nlyd-student');
                 break;
         }

        $view = student_view_path.CONTROLLER.'/safety-settings.php';
        load_view_template($view,array('user_info'=>$user_info,'title'=>$title));
    }




    

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        if(ACTION=='suggest'){
            wp_register_style( 'my-student-suggest', student_css_url.'suggest.css',array('my-student') );
            wp_enqueue_style( 'my-student-suggest' );
        }

        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
    }
}