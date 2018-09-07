<?php

/**
 * 学生-个人中心首页
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Account extends Student_Home
{
    private $ajaxControll;
    public function __construct($action)
    {

        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        $this->ajaxControll = new Student_Ajax();

        //添加短标签
        add_shortcode('student-home',array($this,$action));
    }

    /**
     * 个人中心首页
     */
    public function index(){

        global $user_info,$wpdb;
        if($user_info){

            //获取消息
            $message_total = $wpdb->get_row("select if(count(id)>0,count(id),0) total from {$wpdb->prefix}messages where user_id = {$user_info['user_id']} and read_status = 1 ");

            //获取我的战队
            $sql = "select b.ID,b.post_title my_team,a.status,
                case a.status
                when -1 then '退队审核中'
                when 1 then '入队审核中'
                when 2 then '正式队员'
                else '--'
                end as status_cn
                from {$wpdb->prefix}match_team a 
                left join {$wpdb->prefix}posts b on a.team_id = b.ID 
                where a.user_id = {$user_info['user_id']} and a.user_type = 1 and a.status > -2 ";
            $my_team = $wpdb->get_row($sql,ARRAY_A);
            //var_dump($my_team);
            //print_r($sql);
            //获取我的技能
            $sql1 = "select 
                if(`read` >0,`read`,0) reading,
                if(memory >0,memory,0) memory,
                if(compute >0,compute,0) compute,
                nationality,mental_lv,mental_type
                from {$wpdb->prefix}user_skill_rank 
                where user_id = {$user_info['user_id']}  ";
            $my_skill = $wpdb->get_row($sql1,ARRAY_A);
            //print_r($sql1);
            //var_dump($my_skill);
            $data = array('user_info'=>$user_info,'message_total'=>$message_total->total,'my_team'=>$my_team,'my_skill'=>$my_skill);
        }else{
            $user_info['user_head'] = student_css_url.'image/nlyd.png';
            $data = array('user_info'=>$user_info);
        }


        $view = student_view_path.CONTROLLER.'/userCenter.php';
        load_view_template($view,$data);

    }

    /**
     * 消息列表
     */
    public function messages(){//消息列表
        global $user_info,$wpdb;
        //print_r($user_info);
        $result = $wpdb->get_row('SELECT id from '.$wpdb->prefix.'messages WHERE user_id='.$user_info['user_id']);

        $view = student_view_path.CONTROLLER.'/messagesList.php';
        load_view_template($view, array('is_show' => $result));
    }

    /**
     * 消息详情
     */
    public function messageDetail(){//消息详情
        global $user_info,$wpdb;
        //print_r($user_info);
        $id = intval($_GET['messages_id']);

        $row = $wpdb->get_row('SELECT title,content,message_time FROM '.$wpdb->prefix.'messages '.'WHERE'
            .' id='.$id.' AND user_id='.$user_info['user_id'].' AND status=1');
        if($row)
            $wpdb->update($wpdb->prefix.'messages', array(
                'read_status' => 2
            ),array(
                'id' => $id
            ));
        $view = student_view_path.CONTROLLER.'/messageDetail.php';
        load_view_template($view, array('row' => $row));
    }

    /**
     * 个人资料
     */
    public function info(){

        global $user_info,$wpdb;

        //获取默认收货地址
        $user_address = $wpdb->get_row("select fullname,telephone,concat_ws('',country,province,city,area) address from {$wpdb->prefix}my_address where user_id = {$user_info['user_id']} order by  is_default desc ",ARRAY_A);

        $view = student_view_path.CONTROLLER.'/info.php';
        load_view_template($view,array('user_info'=>$user_info,'user_address'=>$user_address));
    }

    /*
     * 我的比赛列表
     */
    public function recentMatch(){

        global $wpdb,$current_user;
        $sql = "select c.id
                  from {$wpdb->prefix}order c 
                  left join {$wpdb->prefix}match_meta b on c.match_id = b.match_id 
                  where user_id = {$current_user->ID} and (pay_status=2 or pay_status=3 or pay_status=4) LIMIT 1";
        //var_dump($sql);
        $row = $wpdb->get_row($sql);
        //var_dump($row);
        $view = student_view_path.CONTROLLER.'/recentMatch.php';
        load_view_template($view, array('row' => $row));

    }

    /**
     *地址列表
     */
    public function address(){
        global $wpdb,$current_user;
        $sql = "select id,fullname,telephone,concat_ws('',country,province,city,area,address) user_address,is_default from {$wpdb->prefix}my_address where user_id = {$current_user->ID} order by is_default desc";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($rows);
        $view = student_view_path.CONTROLLER.'/address.php';
        load_view_template($view,array('lists'=>$rows));
    }
    /**
     * 新增地址
     */
    public function addAddress(){

        if(isset($_GET['address_id'])){

            $_POST['id'] = $_GET['address_id'];
            $row = $this->ajaxControll->get_address(false);

            if(empty($row)){
                $this->get_404('数据错误');
                return;
            }
        }

        $view = student_view_path.CONTROLLER.'/addAddress.php';
        load_view_template($view,array('row'=>$row,'get' => $_GET));
    }

    /**
     * 安全中心
     */
    public function secure(){

        // //获取教练分类
        $category = $this->ajaxControll->get_coach_category(false);

        // //获取我的教练列表
        // $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : $category[0]->ID;
        // global $current_user;
        // $coach_lists = $this->ajaxControll->get_coach_lists($category_id,'',false);

        $data = array('category'=>$category);

        $view = student_view_path.CONTROLLER.'/secure.php';
        load_view_template($view,$data);
    }

    /**
     * 我的训练
     */
    public function matchList(){
        $view = student_view_path.CONTROLLER.'/train.php';
        load_view_template($view);
    }

    /**
     * 我的课程
     */
    public function course(){
        // //获取教练分类
        $category = $this->ajaxControll->get_coach_category(false);

        // //获取我的教练列表
        // $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : $category[0]->ID;
        // global $current_user;
        // $coach_lists = $this->ajaxControll->get_coach_lists($category_id,'',false);

        $data = array('category'=>$category);

        $view = student_view_path.CONTROLLER.'/course.php';
        load_view_template($view,$data);
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

        if(in_array(ACTION,array('index','info'))){
            wp_register_script( 'student-cropper',student_js_url.'cropper/cropper.js',array('jquery'), leo_student_version );
            wp_enqueue_script( 'student-cropper' );
            wp_register_style( 'my-student-cropper', student_css_url.'cropper/cropper.css',array('my-student'));
            wp_enqueue_style( 'my-student-cropper' );

            if(ACTION == 'info'){
                wp_register_script( 'student-mobileSelect',student_js_url.'Mobile/mobileSelect.js',array('jquery'), leo_student_version  );
                wp_enqueue_script( 'student-mobileSelect' );
                wp_register_script( 'student-info',student_js_url.'account/info.js',array('jquery'), leo_student_version ,true );
                wp_enqueue_script( 'student-info' );
                wp_register_style( 'my-student-mobileSelect', student_css_url.'Mobile/mobileSelect.css',array('my-student') );
                wp_enqueue_style( 'my-student-mobileSelect' );
                wp_register_style( 'my-student-info', student_css_url.'info.css',array('my-student') );
                wp_enqueue_style( 'my-student-info' );
            }

        }

        if(ACTION == 'messages'){
            wp_register_script( 'student-messagesList',student_js_url.'account/messagesList.js',array('jquery'), leo_student_version ,true );
            wp_enqueue_script( 'student-messagesList' );
            wp_register_style( 'my-student-messagesList', student_css_url.'messagesList.css',array('my-student') );
            wp_enqueue_style( 'my-student-messagesList' );
        }

        if(ACTION == 'messageDetail'){
            wp_register_style( 'my-student-messageDetail', student_css_url.'messageDetail.css',array('my-student') );
            wp_enqueue_style( 'my-student-messageDetail' );

        }


        if(ACTION=='recentMatch'){//我的比赛
            wp_register_script( 'student-recentMatch',student_js_url.'account/recentMatch.js',array('jquery'), leo_student_version ,true );
            wp_enqueue_script( 'student-recentMatch' );
            wp_register_style( 'my-student-matchList', student_css_url.'matchList.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchList' );
        }

        if(ACTION=='address'){//地址列表
            wp_register_script( 'student-hammer',student_js_url.'Mobile/Hammer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-hammer' );
            wp_register_script( 'student-address',student_js_url.'account/address.js',array('jquery'), leo_student_version ,true );
            wp_enqueue_script( 'student-address' );
            wp_register_style( 'my-student-address', student_css_url.'address.css',array('my-student') );
            wp_enqueue_style( 'my-student-address' );
        }

        if(ACTION=='addAddress'){//新增地址
            wp_register_script( 'student-mobileSelect',student_js_url.'Mobile/mobileSelect.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-mobileSelect' );
            wp_register_script( 'student-addAddress',student_js_url.'account/addAddress.js',array('jquery'), leo_student_version ,true );
            wp_enqueue_script( 'student-addAddress' );
            wp_register_style( 'my-student-mobileSelect', student_css_url.'Mobile/mobileSelect.css',array('my-student') );
            wp_enqueue_style( 'my-student-mobileSelect' );
            wp_register_style( 'my-student-addAddress', student_css_url.'addAddress.css',array('my-student') );
            wp_enqueue_style( 'my-student-addAddress' );
        }

        if(ACTION == 'train'){//我的训练
            wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-cookie' );

            wp_register_style( 'my-student-match', student_css_url.'match.css',array('my-student') );
            wp_enqueue_style( 'my-student-match' );
        }

        if(ACTION == 'course'){//我的课程
            wp_register_script( 'student-Hammer',student_js_url.'Mobile/Hammer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-Hammer' );
            wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-cookie' );
        }

        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
    }
}