<?php

/**
 * 学生-我的钱包
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Account_Wallet extends Student_Home
{
    public function __construct($shortCode)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

  
        if(isset($_GET['action'])){
            $function = $_GET['action'];
        }else {
            $function = $_GET['action'] = 'index';
        }    
        //添加短标签
        add_shortcode('student-account',array($this,$function));
    }

    /**
     * 列表
     */
    public function index(){
        $view = student_view_path.'myWallet.php';
        load_view_template($view);
    }
    /**
     * 余额收支记录
     */
     public function balanceWater(){
        $view = student_view_path.'balanceWater.php';
        load_view_template($view);
    }
    /**
     * 提现
     */
     public function makeCash(){
        $view = student_view_path.'makeCash.php';
        load_view_template($view);
    }
    /**
     * 更多脑币记录
     */
     public function coinWaterList(){
        $view = student_view_path.'coinWaterList.php';
        load_view_template($view);
    }
 
    /**
     * 绑定银行卡
     */
     public function bindCard(){
        $view = student_view_path.'bindCard.php';
        load_view_template($view);
    }
    /**
     * 提现方式
     */
     public function makeCashType(){
        $view = student_view_path.'makeCashType.php';
        load_view_template($view);
    }
    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-cookie' );

        if($_GET['action']=='index'){//我的钱包
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-myWallet', student_css_url.'myWallet.css',array('my-student') );
            wp_enqueue_style( 'my-student-myWallet' );
        }
        if($_GET['action']=='balanceWater'){//余额收支记录
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-balanceWater', student_css_url.'balanceWater.css',array('my-student') );
            wp_enqueue_style( 'my-student-balanceWater' );
        }
        if($_GET['action']=='makeCash'){//提现
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-makeCash', student_css_url.'makeCash.css',array('my-student') );
            wp_enqueue_style( 'my-student-makeCash' );
        }
        if($_GET['action']=='coinWaterList'){//更多脑币记录
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-coinWaterList', student_css_url.'coinWaterList.css',array('my-student') );
            wp_enqueue_style( 'my-student-coinWaterList' );
        }
        if($_GET['action']=='bindCard'){//绑定银行卡
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-bindCard', student_css_url.'bindCard.css',array('my-student') );
            wp_enqueue_style( 'my-student-bindCard' );
        }
        if($_GET['action']=='makeCashType'){//提现方式
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-makeCashType', student_css_url.'makeCashType.css',array('my-student') );
            wp_enqueue_style( 'my-student-makeCashType' );
        }
    }
}