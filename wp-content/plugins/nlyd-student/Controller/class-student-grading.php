<?php

/**
 * 学生-考级中心首页
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Grading extends Student_Home
{
    private $ajaxControll;
    public function __construct($action)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        $this->ajaxControll = new Student_Ajax();

        //添加短标签
        add_shortcode('grading-home',array($this,$action));
    }

    public function index(){

        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view);
    }
    public function ready_szzb(){//数字争霸准备页
        $view = student_view_path.CONTROLLER.'/ready-numberBattle.php';
        load_view_template($view);
    }
    public function match_szzb(){//数字争霸比赛页
        $view = student_view_path.CONTROLLER.'/matching-numberBattle.php';
        load_view_template($view);
    }
    public function ready_word(){//随机中文词语记忆准备页
        $view = student_view_path.CONTROLLER.'/ready-word.php';
        load_view_template($view);
    }
    public function match_word(){//随机中文词语记忆比赛页
        $view = student_view_path.CONTROLLER.'/matching-word.php';
        load_view_template($view);
    }

    public function matching_PI(){//圆周率默写
        $view = student_view_path.CONTROLLER.'/matching-PI.php';
        load_view_template($view);
    }

    public function ready_card(){//人脉信息记忆准备页
        $view = student_view_path.CONTROLLER.'/ready-card.php';
        load_view_template($view);
    }
    public function match_card(){//人脉信息记忆比赛页
        $view = student_view_path.CONTROLLER.'/matching-card.php';
        load_view_template($view);
    }
    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_script( 'student-leavePage',student_js_url.'matchs/leavePage.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-leavePage' );
        wp_localize_script('student-leavePage','_leavePage',[
            'submit'=>__('离开考级页面,自动提交答题','nlyd-student'),
        ]);
        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
        wp_register_style( 'my-public', student_css_url.'matchs/matching-public.css',array('my-student') );
        wp_enqueue_style( 'my-public' );
        if(ACTION == 'index'){

        }
        if(ACTION == 'match_szzb' || ACTION == 'matching_PI'){//进入数字争霸比赛页面
            wp_register_style( 'my-student-matching-numberBattle', student_css_url.'matching-numberBattle.css',array('my-student') );
            wp_enqueue_style( 'my-student-matching-numberBattle' );
        }
        if(ACTION == 'ready_szzb'){//进入数字争霸准备页面
            wp_register_style( 'my-student-numberBattleReady', student_css_url.'ready-numberBattle.css',array('my-student') );
            wp_enqueue_style( 'my-student-numberBattleReady' );
        }

        if(ACTION == 'match_word' || ACTION == 'ready_word'){//随机中文词语记忆
            wp_register_style( 'my-student-matching-word', student_css_url.'grading/word.css',array('my-student') );
            wp_enqueue_style( 'my-student-matching-word' );
        }

        if(ACTION == 'match_card' || ACTION == 'ready_card'){//人脉信息记忆
            wp_register_style( 'my-student-matching-card', student_css_url.'grading/card.css',array('my-student') );
            wp_enqueue_style( 'my-student-matching-card' );
        }
    }
}