<?php

/**
 * 学生-我的钱包
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Wallet extends Student_Home
{
    private $action;
    public function __construct($action)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

  
//        if(isset($_GET['action'])){
//            $function = $_GET['action'];
//        }else {
//            $function = $_GET['action'] = 'index';
//        }
        $this->action = $action;
        //添加短标签
        add_shortcode('wallet-home',array($this,$action));
    }

    /**
     * 列表
     */
    public function index(){
        $view = student_view_path.CONTROLLER.'/myWallet.php';
        load_view_template($view);
    }
    /**
     * 余额收支记录
     */
     public function balanceWater(){
        $view = student_view_path.CONTROLLER.'/balanceWater.php';
        load_view_template($view);
    }
    /**
     * 提现
     */
     public function makeCash(){
        $view = student_view_path.CONTROLLER.'/makeCash.php';
        load_view_template($view);
    }
    /**
     * 更多脑币记录
     */
     public function coinWaterList(){
        $view = student_view_path.CONTROLLER.'/coinWaterList.php';
        load_view_template($view);
    }
 
    /**
     * 绑定银行卡
     */
     public function bindCard(){
        $view = student_view_path.CONTROLLER.'/bindCard.php';
        load_view_template($view);
    }
    /**
     * 提现方式
     */
     public function makeCashType(){
        $view = student_view_path.CONTROLLER.'/makeCashType.php';
        load_view_template($view);
    }
    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

        if($this->action=='index'){//我的钱包
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-myWallet', student_css_url.'myWallet.css',array('my-student') );
            wp_enqueue_style( 'my-student-myWallet' );
        }
        if($this->action=='balanceWater'){//余额收支记录
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-balanceWater', student_css_url.'balanceWater.css',array('my-student') );
            wp_enqueue_style( 'my-student-balanceWater' );
        }
        if($this->action=='makeCash'){//提现
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-makeCash', student_css_url.'makeCash.css',array('my-student') );
            wp_enqueue_style( 'my-student-makeCash' );
        }
        if($this->action=='coinWaterList'){//更多脑币记录
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-coinWaterList', student_css_url.'coinWaterList.css',array('my-student') );
            wp_enqueue_style( 'my-student-coinWaterList' );
        }
        if($this->action=='bindCard'){//绑定银行卡
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-bindCard', student_css_url.'bindCard.css',array('my-student') );
            wp_enqueue_style( 'my-student-bindCard' );
        }
        if($this->action=='makeCashType'){//提现方式
            wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
            wp_enqueue_style( 'my-student-userCenter' );
            wp_register_style( 'my-student-makeCashType', student_css_url.'makeCashType.css',array('my-student') );
            wp_enqueue_style( 'my-student-makeCashType' );
        }
    }
}