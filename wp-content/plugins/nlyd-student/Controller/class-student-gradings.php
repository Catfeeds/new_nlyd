<?php

/**
 * 学生-考级中心首页
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Gradings extends Student_Home
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


    /**
     * 考级列表页
     */
    public function index(){

        global $wpdb;
        $sql = "select a.id,a.grading_id,a.status,a.start_time,a.end_time,a.entry_end_time,c.meta_value match_switch
                from {$wpdb->prefix}grading_meta a  
                LEFT JOIN {$wpdb->prefix}postmeta c ON a.grading_id = c.post_id and meta_key = 'default_match_switch'
                ";
        $rows = $wpdb->get_results($sql,ARRAY_A);

        if(!empty($rows)){
            $new_time = get_time('mysql');
            foreach ($rows as $v){

                if($v['match_switch'] == 'ON') {
                    if($new_time < $v['entry_end_time']){
                        //报名中
                        $save['status'] = 1;
                    }
                    elseif ($v['entry_end_time'] <= $new_time && $new_time < $v['start_time']){
                        //等待开赛
                        $save['status'] = -2;
                    }
                    elseif ($v['start_time'] <= $new_time && $new_time < $v['end_time']){
                        //进行中
                        $save['status'] = 2;
                    }else{
                        //已结束
                        $save['status'] = -3;
                    }
                }
                $a = $wpdb->update($wpdb->prefix.'grading_meta',$save,array('id'=>$v['id'],'grading_id'=>$v['grading_id']));
            }
        }

        //获取最近一场考级
        $data['new_grading'] = $wpdb->get_row("select grading_id,start_time from {$wpdb->prefix}grading_meta where status = -2 order by start_time asc ");

        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view,$data);
    }

    /**
     * 考级详情页
     */
    public function info(){

        global $wpdb,$current_user;
        $match = $this->get_grading($_GET['grad_id']);
        if(empty($match)){
            $this->get_404(array('message'=>'数据错误','return_url'=>home_url('grading')));
            return;
        }
        //print_r($match);
        $data['match'] = $match;

        //获取报名人数
        $total = $wpdb->get_var("select count(*) total from {$wpdb->prefix}order where match_id = {$match['grading_id']} and order_type = 2 and pay_status in (2,3,4)");
        $data['total'] = $total > 0 ? $total : 0;

        //获取订单
        $data['memory_lv'] = $wpdb->get_var("select memory_lv from {$wpdb->prefix}order where match_id = {$match['grading_id']} and user_id = {$current_user->ID}");

        $view = student_view_path.CONTROLLER.'/matchDetail.php';
        load_view_template($view,$data);
    }


    /**
     * 考级报名页
     */
    public function confirm(){

        global $wpdb,$current_user;
        $match = $this->get_grading($_GET['grad_id']);
        if(empty($match)){
            $this->get_404(array('message'=>'数据错误','return_url'=>home_url('grading')));
            return;
        }
        //print_r($match);
        $data['match'] = $match;

        //主训教练
        $coach_id = $wpdb->get_var("select coach_id from {$wpdb->prefix}my_coach where user_id = {$current_user->ID} and category_id = {$match['category_id']} and major = 1");
        if($coach_id > 0){
            $data['coach_real_name'] = get_user_meta($coach_id,'user_real_name')[0];
        }else{
            $data['coach_real_name'] = '';
        }

        //实名认证
        $data['user_real_name'] = get_user_meta($current_user->ID,'user_real_name')[0];

        //战队
        $data['team_title'] = $wpdb->get_var("select b.post_title team_title 
                                            from {$wpdb->prefix}match_team a  
                                            left join {$wpdb->prefix}posts b on a.team_id = b.ID
                                            where user_id = {$current_user->ID} and status = 2 and user_type = 1");
        //选手ID
        $data['user_ID'] = get_user_meta($current_user->ID,'user_ID')[0];

        //获取当前比赛是否报名
        $order = $wpdb->get_row("select memory_lv,pay_status from {$wpdb->prefix}order where match_id = {$match['grading_id']}",ARRAY_A);
        $data['memory_lv'] = !empty($order['memory_lv']) ? $order['memory_lv'] : 1;
        //print_r($order);
        $view = student_view_path.CONTROLLER.'/confirm.php';
        load_view_template($view,$data);
    }


    /**
     * 考级等待页
     */
    public function matchWaitting(){

        //获取数据
        $row = $this->get_grading($_GET['grad_id']);
        if(empty($row)){
            $this->get_404(array('message'=>__('暂无考级', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
            return;
        }
        if($row['status'] == -3){
            $this->get_404(array('message'=>__('比赛已结束', 'nlyd-student'),'match_url'=>home_url(CONTROLLER.'/info/grad_id/'.$_GET['grad_id'])));
            return;
        }


        $view = student_view_path.CONTROLLER.'/match-waitting.php';
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

    public function ready_voice(){//语音听记数字记忆
        $view = student_view_path.CONTROLLER.'/ready-voice.php';
        load_view_template($view);
    }
    public function match_voice(){//语音听记数字记忆
        $view = student_view_path.CONTROLLER.'/matching-voice.php';
        load_view_template($view);
    }
    public function matching_silent(){//国学经典默写
        $view = student_view_path.CONTROLLER.'/matching-silent.php';
        load_view_template($view);
    }

    public function matching_wzsd(){//文章速读比赛页
        $view = student_view_path.CONTROLLER.'/matching-reading.php';
        load_view_template($view);
    }
    public function ready_wzsd(){//文章速读准备页
        $view = student_view_path.CONTROLLER.'/matching-ready.php';
        load_view_template($view);
    }
    public function matching_zxss(){//正向速算比赛页
        $view = student_view_path.CONTROLLER.'/matching-fastCalculation.php';
        load_view_template($view);
    }
    public function matching_nxss(){//逆向速算比赛页
        $view = student_view_path.CONTROLLER.'/matching-fastReverse.php';
        load_view_template($view);
    }
    public function record(){//考级成绩
        $view = student_view_path.CONTROLLER.'/record.php';
        load_view_template($view);
    }



    /**
     * 获取考级信息
     * $grad_id 考试比赛id
     */
    public function get_grading($grad_id){
        global $wpdb;
        $sql = "select a.*,b.post_title grading_title,b.post_content,
                c.post_title grading_type,if(d.id>0,'y','') is_me,
                DATE_FORMAT(a.start_time,'%Y-%m-%d %H:%i') start_time,
                DATE_FORMAT(a.end_time,'%Y-%m-%d %H:%i') end_time,
                case a.status
                when -2 then '等待开赛'
                when 1 then '报名中'
                when 2 then '进行中'
                else '已结束'
                end status_cn,
                case e.meta_value
                when 'memory' then '记忆'
                when 'arithmetic' then '速算'
                when 'reading' then '速读'
                end project_alias_cn,
                e.meta_value project_alias
                from wp_grading_meta a 
                left join wp_posts b on a.grading_id = b.ID 
                left join wp_posts c on a.category_id = c.ID 
                left join wp_order d on a.grading_id = d.match_id
                left join wp_postmeta e ON a.category_id = e.post_id AND meta_key = 'project_alias'
                where a.grading_id = {$grad_id}
                ";
        //print_r($sql);
        $row = $wpdb->get_row($sql,ARRAY_A);
        return $row;
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
        if(ACTION == 'index'){//考级列表
            wp_register_style( 'my-student-matchList', student_css_url.'matchList.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchList' );
        }
        if(ACTION == 'info'){//考级详情
            wp_register_style( 'my-student-matchDetail', student_css_url.'matchDetail.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchDetail' );
        }
        if(ACTION=='confirm'){//信息确认页
            wp_register_script( 'student-mobileSelect',student_js_url.'Mobile/mobileSelect.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-mobileSelect' );
            wp_localize_script('student-mobileSelect','_mobileSelect',[
                'sure'=>__('确认','nlyd-student'),
                'cancel'=>__('取消','nlyd-student')
            ]);
            wp_register_style( 'my-student-mobileSelect', student_css_url.'Mobile/mobileSelect.css',array('my-student') );
            wp_enqueue_style( 'my-student-mobileSelect' );
            wp_register_style( 'my-student-confirm', student_css_url.'confirm.css',array('my-student') );
            wp_enqueue_style( 'my-student-confirm' );
        }
        if(ACTION=='matchWaitting'){//考级等待倒计时页面
            wp_register_style( 'my-student-matchWaitting', student_css_url.'match-waitting.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchWaitting' );
        }
        if(ACTION == 'match_szzb' || ACTION == 'matching_PI'){//进入数字争霸比赛页面
            wp_register_style( 'my-student-matching-numberBattle', student_css_url.'matching-numberBattle.css',array('my-student') );
            wp_enqueue_style( 'my-student-matching-numberBattle' );
        }
        if(ACTION == 'ready_voice'){
            wp_register_style( 'my-student-voice', student_css_url.'grading/voice.css',array('my-student') );
            wp_enqueue_style( 'my-student-voice' );
        }
        if(ACTION == 'match_voice'){
            wp_register_style( 'my-student-matching-numberBattle', student_css_url.'matching-numberBattle.css',array('my-student') );
            wp_enqueue_style( 'my-student-matching-numberBattle' );
        }
        if(ACTION == 'ready_szzb' ){//进入数字争霸准备页面
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
        if(ACTION == 'matching_silent'){//国学经典默写
            wp_register_style( 'my-student-matching-silent', student_css_url.'grading/silent.css',array('my-student') );
            wp_enqueue_style( 'my-student-matching-silent' );
        }

        if(ACTION == 'matching_wzsd'){//文章速读比赛页
            wp_register_style( 'my-student-reading', student_css_url.'matching-reading.css',array('my-student') );
            wp_enqueue_style( 'my-student-reading' );
        }
        if(ACTION == 'ready_wzsd'){//文章速读准备页
            wp_register_style( 'my-student-ready-reading', student_css_url.'ready-reading.css',array('my-student') );
            wp_enqueue_style( 'my-student-ready-reading' );
        }
        if(ACTION == 'matching_zxss' ){//正向速算比赛页
            wp_register_style( 'my-student-fastCalculation', student_css_url.'matching-fastCalculation.css',array('my-student') );
            wp_enqueue_style( 'my-student-fastCalculation' );
        }

        if(ACTION == 'matching_nxss'){//逆向速算比赛页
            wp_register_script( 'student-check24_answer',student_js_url.'matchs/check24_answer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-check24_answer' );
            wp_register_style( 'my-student-fastReverse', student_css_url.'matching-fastReverse.css',array('my-student') );
            wp_enqueue_style( 'my-student-fastReverse' );
            wp_register_style( 'my-student-matching-fastReverse', student_css_url.'matching-fastReverse.css',array('my-student') );
            wp_enqueue_style( 'my-student-matching-fastReverse' );
        }
    }
}