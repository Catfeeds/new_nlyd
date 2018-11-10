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