<?php

/**
 * 首页-训练模块
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16
 * Time: 14:38
 */
class Student_Trains extends Student_Home
{
    private $action;
    public $ajaxControll;
    public function __construct($action)
    {

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        parent::__construct();


        //添加短标签
        add_shortcode('train-home',array($this,$action));
    }



    /**
     * 首页
     */
    public function index(){

        //获取所有比赛类型
        $args = array(
            'post_type' => array('genre'),
            'post_status' => array('publish'),
            'order' => 'DESC',
            'orderby' => 'ID',
        );
        $the_query = new WP_Query( $args );

        $view = student_view_path.CONTROLLER.'/index.php';
        load_view_template($view,array('list'=>$the_query->posts));
    }

    public function lists(){

        if(empty($_GET['id'])) $this->get_404('参数错误');

        //获取当前
        $row = get_post($_GET['id']);

        $args = array(
            'post_type' => array('match-category'),
            'post_status' => array('publish'),
            'post_parent'=>$_GET['id'],
            'order' => 'ASC',
            'orderby' => 'menu_order',
        );
        $the_query = new WP_Query( $args );
        if(!empty($the_query->posts)){
            $list = array();
            foreach ($the_query->posts as $v){
                $list[$v->ID]['title'] = $v->post_title;
            }
            //print_r($list);
            $ids = arr2str(array_column((array)$the_query->posts,'ID'));

            global $wpdb;
            $sql = "SELECT ID,post_title,post_parent FROM {$wpdb->prefix}posts WHERE post_parent in($ids) ORDER BY menu_order ASC ";
            $rows = $wpdb->get_results($sql);
            if(!empty($rows)){
                foreach ($rows as $val){

                    $val->project_alias = get_post_meta($val->ID,'project_alias')[0];
                    $list[$val->post_parent]['children'][] = $val;
                }
            }
            //print_r($list);
        }

        $view = student_view_path.CONTROLLER.'/lists.php';
        load_view_template($view,array('list'=>$list,'post_title'=>$row->post_title,'genre_id'=>$_GET['id']));
    }

    /**
     * 专项训练准备页
     */
    public function ready(){
        if(empty($_GET['id']) || empty($_GET['type']) || empty($_GET['genre_id'])) $this->get_404('参数错误');

        $genre = get_post($_GET['genre_id']);

        $project = get_post($_GET['id']);

        //print_r($row);
        $view = student_view_path.CONTROLLER.'/ready.php';

        load_view_template($view,array('project_title'=>$project->post_title,'genre_title'=>$genre->post_title));
    }

    /**
     * 初始页面
     */
    public function initial(){


        if(empty($_GET['type'])) $this->get_404('参数错误');
        global $wpdb,$current_user;
        $post_id = '';
        switch ($_GET['type']){
            case 'wzsd':
                $sql = "select b.object_id,b.term_taxonomy_id from {$wpdb->prefix}terms a 
                        left join {$wpdb->prefix}term_relationships b on a.term_id = b.term_taxonomy_id 
                        where a.slug = 'test-question' ";
                $rows = $wpdb->get_results($sql,ARRAY_A);

                if(empty($rows)) $this->get_404('测试题库暂无文章,请联系管理员添加');

                $posts_arr = array_column($rows,'object_id');
                //print_r($posts_arr);

                //获取已训练文章
                $sql1 = "select post_id from {$wpdb->prefix}user_post_use where user_id = {$current_user->ID}";
                //print_r($sql1);
                $post_str = $wpdb->get_var($sql1);
                if(!empty($post_str)){
                    $post_arr = str2arr($post_str,',');

                    $result = array_diff($posts_arr,$post_arr);

                }else{
                    $result = $posts_arr;
                }
                $post_id = end($result);

                //获取题目
                $content = get_post($post_id );
                //print_r($content);
                $count_down = 900;
                break;
            case 'szzb':

                $count_down = 1200;
                break;
            case 'kysm':
                $count_down = 600;
                break;
            case 'pkjl':
                $count_down = 900;
                break;
            case 'zxss':
                $count_down = 540;
                break;
            case 'nxss':
                $count_down = 600;
                break;
            default:
                $this->get_404('没有该比赛项目');
                break;
        }

        $data = array(
            'content'=>$content,
            'count_down'=>$count_down,
            'url'=>home_url('trains/answer/genre_id/'.$_GET['genre_id'].'/type/'.$_GET['type']),
        );
        if(!empty($post_id)) $data['url'] .= '/post_id/'.$post_id;

        $view = student_view_path.CONTROLLER.'/initial.php';
        load_view_template($view,$data);

    }

    /**
     * 答题页面
     */
    public function answer(){
        global $wpdb;

        switch ($_GET['type']){
            case 'wzsd':
                if(empty($_GET['post_id'])) $this->get_404('参数错误');
                //获取比赛题目
                $sql1 = "select a.ID,a.post_title,b.problem_select,problem_answer
                        from {$wpdb->prefix}posts a 
                        left join {$wpdb->prefix}problem_meta b on a.ID = b.problem_id
                        where a.post_parent = {$_GET['post_id']} order by b.id asc
                        ";

                $rows = $wpdb->get_results($sql1,ARRAY_A);
                $questions_answer = array();
                $match_questions = array();
                if(!empty($rows)){
                    $answer_total = 1;  //默认答案个数
                    foreach ($rows as $k => $val){
                        //$val['problem_answer'] = 1;
                        $key = &$val['ID'];
                        $questions_answer[$key]['problem_select'][] = $val['problem_select'];
                        $questions_answer[$key]['problem_answer'][] = $val['problem_answer'];
                        //if($val['problem_answer'] == 1) $answer_total += 1;
                    }
                    $match_questions = array_unique(array_column($rows,'post_title','ID'));
                }
                //print_r($questions_answer);
                //print_r($match_questions);
                break;
        }
        //var_dump($_COOKIE);
        $view = student_view_path.CONTROLLER.'/answer.php';
        load_view_template($view);
    }

    /**
     * 训练答题记录
     */
    public function logs(){

    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
        wp_register_script( 'student-leavePage',student_js_url.'matchs/leavePage.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-leavePage' );


        //比赛初始页面
        if(ACTION == 'initial'){

            // wp_register_script( 'student-mTouch',student_js_url.'Mobile/mTouch.js',array('jquery'), leo_student_version  );
            // wp_enqueue_script( 'student-mTouch' );
            wp_register_style( 'my-public', student_css_url.'matchs/matching-public.css',array('my-student') );
            wp_enqueue_style( 'my-public' );
            if($_GET['type']=='nxss'){//逆向速算初始页
                wp_register_script( 'student-check24_answer',student_js_url.'matchs/check24_answer.js',array('jquery'), leo_student_version  );
                wp_enqueue_script( 'student-check24_answer' );
                wp_register_style( 'my-student-fastReverse', student_css_url.'matching-fastReverse.css',array('my-student') );
                wp_enqueue_style( 'my-student-fastReverse' );

            }

            if($_GET['type']=='zxss'){//正向速算初始页
                wp_register_style( 'my-student-fastCalculation', student_css_url.'matching-fastCalculation.css',array('my-student') );
                wp_enqueue_style( 'my-student-fastCalculation' );

            }

            if($_GET['type']=='wzsd'){//文章速读初始页
                wp_register_style( 'my-student-matchDetail', student_css_url.'ready-reading.css',array('my-student') );
                wp_enqueue_style( 'my-student-matchDetail' );

            }

            if($_GET['type']=='kysm'){//快眼扫描比赛页
                wp_register_style( 'my-student-fastScan', student_css_url.'matching-fastScan.css',array('my-student') );
                wp_enqueue_style( 'my-student-fastScan' );

            }

            if($_GET['type']=='szzb'){//进入数字争霸准备页面
                wp_register_style( 'my-student-numberBattleReady', student_css_url.'ready-numberBattle.css',array('my-student') );
                wp_enqueue_style( 'my-student-numberBattleReady' );
            }

            if($_GET['type']=='pkjl'){//进入扑克接力准备页面
                wp_register_style( 'my-student-pokerRelayReady', student_css_url.'ready-pokerRelay.css',array('my-student') );
                wp_enqueue_style( 'my-student-pokerRelayReady' );
            }
        }

        //比赛记忆后答题页面
        if(ACTION == 'answer'){
            wp_register_style( 'my-public', student_css_url.'matchs/matching-public.css',array('my-student') );
            wp_enqueue_style( 'my-public' );
            if($_GET['type']=='wzsd'){//文章速读
                wp_register_style( 'my-student-matchDetail', student_css_url.'matching-reading.css',array('my-student') );
                wp_enqueue_style( 'my-student-matchDetail' );
            }

            if($_GET['type']=='szzb'){//数字争霸
                wp_register_style( 'my-student-matching', student_css_url.'matching-numberBattle.css',array('my-student') );
                wp_enqueue_style( 'my-student-matching' );
            }

            if($_GET['type']=='pkjl'){//扑克接力
                wp_register_style( 'my-student-pokerRelay', student_css_url.'matching-pokerRelay.css',array('my-student') );
                wp_enqueue_style( 'my-student-pokerRelay' );
            }
        }

        //答案记录页面
        if(in_array(ACTION,array('answerLog','checkAnswerLog'))){
            if($_GET['type']=='nxss'){//逆向速算成绩页
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }

            if($_GET['type']=='zxss'){//正向速算成绩页
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }

            if($_GET['type']=='wzsd'){//文章速读成绩页
                wp_register_style( 'my-student-matchDetail', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-matchDetail' );

            }

            if($_GET['type']=='kysm'){//快眼扫描成绩页
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }

            if($_GET['type']=='szzb'){//数字争霸本轮答题记录
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }
            if($_GET['type']=='pkjl'){//扑克接力本轮答题记录
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }
        }

        if(ACTION=='confirm'){//信息确认页
            wp_register_style( 'my-student-confirm', student_css_url.'confirm.css',array('my-student') );
            wp_enqueue_style( 'my-student-confirm' );
        }
        if(ACTION=='record'){//战绩排名页
            wp_register_script( 'student-share',student_js_url.'share/NativeShare.js', leo_student_version  );
            wp_enqueue_script( 'student-share' );

            wp_register_style( 'my-student-record', student_css_url.'record.css',array('my-student') );
            wp_enqueue_style( 'my-student-record' );

        }
        if(ACTION=='singleRecord'){//单项比赛成绩排名页
            wp_register_script( 'student-share',student_js_url.'share/NativeShare.js', leo_student_version  );
            wp_enqueue_script( 'student-share' );
            wp_register_style( 'my-student-singleRecord', student_css_url.'singleRecord.css',array('my-student') );
            wp_enqueue_style( 'my-student-singleRecord' );
        }

        if(ACTION=='index'){//比赛列表页
            wp_register_style( 'my-student-matchList', student_css_url.'matchList.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchList' );
        }
        if(ACTION=='matchRule'){//比赛规则
            wp_register_style( 'my-student-matchRule', student_css_url.'match-Rule.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchRule' );
        }
        if(ACTION=='matchWaitting'){//比赛等待倒计时页面
            wp_register_style( 'my-student-matchWaitting', student_css_url.'match-waitting.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchWaitting' );
        }
        if(ACTION=='startMatch'){//开始比赛
            wp_register_style( 'my-student-match', student_css_url.'match.css',array('my-student') );
            wp_enqueue_style( 'my-student-match' );
        }
    }
}