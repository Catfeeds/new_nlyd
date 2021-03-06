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
                where user_id = {$user_info['user_id']} and skill_type = 1";
            $my_skill = $wpdb->get_row($sql1,ARRAY_A);
            //print_r($sql1);
            //var_dump($my_skill);
            //脑力健将
            $brainpower = $wpdb->get_row("SELECT type_name,MAX(`level`) AS `level`,`range`,`category_name` FROM {$wpdb->prefix}directories WHERE `range`=2 AND user_id={$user_info['user_id']} GROUP BY user_id", ARRAY_A);
            if(!$brainpower) $brainpower = $wpdb->get_row("SELECT type_name,MAX(`level`) AS `level`,`range`,`category_name` FROM {$wpdb->prefix}directories WHERE `range`=1 AND user_id={$user_info['user_id']} GROUP BY user_id", ARRAY_A);

            //获取是否存在管理机构
            $sql_ = "select b.id ,b.zone_number,c.zone_type_alias,c.zone_type_name,if(b.zone_match_type=1,'战队精英赛','城市赛') as match_type,b.is_double,b.zone_city,b.zone_name 
                    from {$wpdb->prefix}zone_manager a 
                    left join {$wpdb->prefix}zone_meta b on a.zone_id = b.id 
                    left join {$wpdb->prefix}zone_type c on b.type_id = c.id 
                    where a.user_id = {$user_info['user_id']} 
                    order by id desc";

            $zones = $wpdb->get_results($sql_,ARRAY_A);
            //print_r($sql_);
            if(!empty($zones)){
                $arr = array();
                foreach ($zones as $key => $value) {
                    if(!empty($value['zone_city'])){

                        $city_arr = str2arr($value['zone_city'],'-');
                        if(!empty($city_arr[2])){
                            $city = rtrim($city_arr[1],'市').preg_replace('/市|区|县/','',$city_arr[2]);
                        }elseif ($city_arr[1] != '市辖区'){
                            $city = rtrim($city_arr[1],'市');
                        }else{
                            $city = rtrim($city_arr[0],'市');
                        }
                    }
                    //print_r($city);

                    //$city = !empty($city) ? '（'.$city.'）' : '';
                    $city = !empty($city) ? $city : '';
                    if($value['zone_type_alias'] == 'match'){
                        $arr[$key]['value'] = $value['zone_name'].$city.$value['match_type'].'组委会';
                    }
                    else{
                        $arr[$key]['value'] = $value['zone_name'].$value['zone_type_name'].' • '.$city;
                    }
                    $arr[$key]['id'] = $value['id'];
                }
            }
            //print_r($arr);

            //获取当前是否有比赛
            $saql = "select a.match_id from {$wpdb->prefix}match_meta_new a left join {$wpdb->prefix}order b on a.match_id = b.match_id where b.user_id = {$user_info['user_id']} and match_status = 2";
            $match_id = $wpdb->get_var($saql);

            $data = array(
                'user_info'=>$user_info,
                'message_total'=>$message_total->total,
                'my_team'=>$my_team,
                'my_skill'=>$my_skill,
                'zones'=>!empty($arr) ? json_encode($arr) : '',
                'brainpower'=>$brainpower,
                'waitting_url'=>!empty($match_id) ? home_url('matchs/matchWaitting/match_id/'.$match_id) : '',
            );
            //print_r($data);
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

        //判断比赛类型
        if($_GET['type'] == 2){
            $type = 2;
            $view = '/recentGrad';
        }else{
            $type = 1;
            $view = '/recentMatch';
        }

        $where = " and c.order_type = {$type} ";
        $sql = "select count(c.id)
                  from {$wpdb->prefix}order c 
                  left join {$wpdb->prefix}match_meta b on c.match_id = b.match_id 
                  where user_id = {$current_user->ID} and (c.pay_status=2 or c.pay_status=3 or c.pay_status=4) {$where} LIMIT 1";
        //var_dump($sql);
        $row = $wpdb->get_var($sql);
        //var_dump($row);
        $view = student_view_path.CONTROLLER.$view.'.php';
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
                $this->get_404(__('数据错误', 'nlyd-student'));
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

        global $user_info;
        //print_r($user_info);
        $data = array('user_info'=>$user_info);

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

        $data = array('category'=>$category);

        $view = student_view_path.CONTROLLER.'/course.php';
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