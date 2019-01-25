<?php

/**
 * 董事长-控制台
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Controls extends Student_Home
{
    public function __construct($action)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        //添加短标签
        add_shortcode('student-home',array($this,$action));
    }

    /**
     * 控制台首页
     */
    public function index(){


        $view = student_view_path.CONTROLLER.'/userCenter.php';
        load_view_template($view,$data);

    }


    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

        if(ACTION == 'index'){
            wp_register_script( 'student-cropper',student_js_url.'cropper/cropper.js',array('jquery'), leo_student_version );
            wp_enqueue_script( 'student-cropper' );
            wp_register_style( 'my-student-cropper', student_css_url.'cropper/cropper.css',array('my-student'));
            wp_enqueue_style( 'my-student-cropper' );
            wp_register_script( 'student-mobileSelect',student_js_url.'Mobile/mobileSelect.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-mobileSelect' );
            wp_localize_script('student-mobileSelect','_mobileSelect',[
                'sure'=>__('确认','nlyd-student'),
                'cancel'=>__('取消','nlyd-student')
            ]);
            wp_register_style( 'my-student-mobileSelect', student_css_url.'Mobile/mobileSelect.css',array('my-student') );
            wp_enqueue_style( 'my-student-mobileSelect' );
        }
        if(ACTION == 'info'){
            wp_register_script( 'student-cropper',student_js_url.'cropper/cropper.js',array('jquery'), leo_student_version );
            wp_enqueue_script( 'student-cropper' );
            wp_register_style( 'my-student-cropper', student_css_url.'cropper/cropper.css',array('my-student'));
            wp_enqueue_style( 'my-student-cropper' );
            wp_register_script( 'student-mobileSelect',student_js_url.'Mobile/mobileSelect.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-mobileSelect' );
            wp_localize_script('student-mobileSelect','_mobileSelect',[
                'sure'=>__('确认','nlyd-student'),
                'cancel'=>__('取消','nlyd-student')
            ]);
            wp_register_style( 'my-student-mobileSelect', student_css_url.'Mobile/mobileSelect.css',array('my-student') );
            wp_enqueue_style( 'my-student-mobileSelect' );
            wp_register_style( 'my-student-info', student_css_url.'info.css',array('my-student') );
            wp_enqueue_style( 'my-student-info' );
        }
        if(ACTION == 'messages'){
            wp_register_script( 'student-messagesList',student_js_url.'account/messagesList.js',array('jquery'), leo_student_version ,true );
            wp_enqueue_script( 'student-messagesList' );
            wp_register_style( 'my-student-messagesList', student_css_url.'messagesList.css',array('my-student') );
            wp_enqueue_style( 'my-student-messagesList' );
        }

        if(ACTION == 'messageDetail'){
            wp_register_style( 'my-student-messagesList', student_css_url.'messagesList.css',array('my-student') );
            wp_enqueue_style( 'my-student-messagesList' );

        }


        if(ACTION=='recentMatch'){//我的比赛
            wp_register_script( 'student-recentMatch',student_js_url.'account/recentMatch.js',array('jquery'), leo_student_version ,true );
            wp_enqueue_script( 'student-recentMatch' );
            wp_localize_script('student-recentMatch','_recentMatch',[
                'stop'=>__('报名结束','nlyd-student'),
                'date'=>__('开赛日期','nlyd-student'),
                'date_kaoji'=>__('考级日期','nlyd-student'),
                'address'=>__('比赛地点','nlyd-student'),
                'address_kaoji'=>__('考级地点','nlyd-student'),
                'money'=>__('报名费用','nlyd-student'),
                'end'=>__('报名截止','nlyd-student'),
                'player'=>__('已报人数','nlyd-student'),
                'look'=>__('查看详情','nlyd-student'),
                'must'=>__('参赛须知','nlyd-student'),
                'must_kaoji'=>__('考级须知','nlyd-student'),
                'people'=>__('人','nlyd-student'),
                'day'=>__('天','nlyd-student'),
            ]);
            wp_register_style( 'my-student-matchList', student_css_url.'matchList.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchList' );
        }
        if(ACTION=='course'){//我的比赛
            wp_register_style( 'my-student-matchList', student_css_url.'matchList.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchList' );
        }
        if(ACTION=='address'){//地址列表
            // wp_register_script( 'student-mTouch',student_js_url.'Mobile/mTouch.js',array('jquery'), leo_student_version  );
            // wp_enqueue_script( 'student-mTouch' );
            wp_register_script( 'student-address',student_js_url.'account/address.js',array('jquery'), leo_student_version ,true );
            wp_enqueue_script( 'student-address' );
            wp_localize_script('student-address','_address',[
                'set'=>__('是否确认设为默认地址','nlyd-student'),
                'setDefault'=>__('设为默认','nlyd-student'),
                'default'=>__('默认地址','nlyd-student'),
                'tips'=>__('提示','nlyd-student'),
                'think'=>__('再想想','nlyd-student'),
                'sure'=>__('确认','nlyd-student'),
                'delete'=>__('是否确认删除地址','nlyd-student'),
            ]);
            wp_register_style( 'my-student-address', student_css_url.'address.css',array('my-student') );
            wp_enqueue_style( 'my-student-address' );
        }

        if(ACTION=='addAddress'){//新增地址
            wp_register_script( 'student-mobileSelect',student_js_url.'Mobile/mobileSelect.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-mobileSelect' );
            wp_localize_script('student-mobileSelect','_mobileSelect',[
                'sure'=>__('确认','nlyd-student'),
                'cancel'=>__('取消','nlyd-student')
            ]);
            wp_register_script( 'student-addAddress',student_js_url.'account/addAddress.js',array('jquery'), leo_student_version ,true );
            wp_enqueue_script( 'student-addAddress' );
            wp_register_style( 'my-student-mobileSelect', student_css_url.'Mobile/mobileSelect.css',array('my-student') );
            wp_enqueue_style( 'my-student-mobileSelect' );
            wp_register_style( 'my-student-address', student_css_url.'address.css',array('my-student') );
            wp_enqueue_style( 'my-student-address' );
        }

        if(ACTION == 'train'){//我的训练

            wp_register_style( 'my-student-match', student_css_url.'match.css',array('my-student') );
            wp_enqueue_style( 'my-student-match' );
        }
        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
    }
}