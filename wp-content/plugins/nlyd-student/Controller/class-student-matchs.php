<?php
use library\TwentyFour;
/**
 * 学生-往期/近期比赛
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Matchs extends Student_Home
{

    /********************比赛默认属性****************************/

    /*
     * 比賽id
     */
    public $match_id;

    /*
     * 比赛标题
     */
    public $match_title;

    /*
     * 比赛开赛时间
     */
    public $match_start_time;

    /*
     * 后台设定的比赛轮数
     */
    public $match_more;

    /*
     * 每轮项目初始倒计时
     */
    public $match_count_down;

    /*
     * 每轮项目间隔时间
     */
    public $match_project_interval;

    /*
     * 每轮题目间隔时间
     */
    public $match_subject_interval;

    /********************比赛类别默认属性***三大类*************************/

    /*
     * 比赛类型排序后数组
     */
    public $category_order_array = array();

    /*
     * 比赛类型标题
     */
    public $category_title;

    /*
     * 比赛类型别名
     */
    public $category_alias;

    /********************比赛项目默认属性***六小项*************************/

    /*
     * 比赛项目id
     */
    public $project_id;

    /*
     * 比赛类项目排序后数组
     */
    public $project_order_array = array();

    /*
     * 比赛类项目为键的数组
     */
    public $project_key_array = array();

    /*
     * 比赛类项目id的数组
     */
    public $project_id_array = array();

    /*
     * 比赛项目总数
     */
    public $match_project_total;

    /*
     * 比赛项目标题
     */
    public $project_title;

    /*
    * 比赛项目别名
    */
    public $project_alias;

    /*
     * 后台设定的比赛项目轮数
     */
    public $project_match_more;

    /*
     * 后台设定的比赛项目轮数间的间隔时间
     */
    public $project_time_interval;

    /*
    * 比赛项目开始时间
    */
    public $project_start_time;

    /*
    * 比赛项目结束时间
    */
    public $project_end_time;

    /*
     * 比赛项目内部小项倒计时
     * 主要针对 正向速算/
     */
    public $child_count_down;

    /*
     * 比赛项目初始字符长度
     * 主要针对 数字争霸/快眼扫描
     */
    public $project_str_len;

    /*************************************************************/

    public $default_match_more = 1;         //初始比赛轮数
    public $default_count_down;             //比赛初始倒计时
    public $default_more_interval;         //比赛轮数间的倒计时
    public $current_more;                  //当前进行的比赛轮数

    public $redis;

    public function __construct($action)
    {

        parent::__construct();

        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1',6379,1);
        $this->redis->auth('leo626');

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        $this->ajaxControll = new Student_Ajax();

        /**********************获取比赛信息********************************/
        if(isset($_GET['match_id'])){

            $result = $this->get_match_content($_GET['match_id']);
            if($result['status'] == -1){
                $this->get_404($result['info']);
                return;
            }

        }
        /**********************获取比赛信息end********************************/

        /*******************获取当前比赛项目配置******************************/
        if(isset($_GET['project_id'])){

            if (empty($this->project_key_array[$_GET['project_id']])){
                $this->get_404('比赛项目错误');
                return;
            }

            $match_project = $this->project_key_array[$_GET['project_id']];
            //print_r($match_project);
            $this->project_id = $match_project['match_project_id'];
            $this->project_title = $match_project['post_title'];
            $this->project_alias = $match_project['project_alias'];
            $this->project_match_more = $match_project['match_more'];
            $this->project_time_interval = $match_project['project_time_interval'];
            $this->project_start_time = strtotime($match_project['project_start_time']);
            $this->project_end_time = strtotime($match_project['project_end_time']);
            //print_r($match_project);

            /**********************初始配置********************************/

            $this->setting_default_config($match_project);

        }
        /**********************获取当前比赛项目end********************************/

        //print_r($this->default_count_down);

        //添加短标签
        add_shortcode('match-home',array($this,$action));
    }

    /**
     * 列表
     */
    public function index(){

         /*$a = new TwentyFour();
        var_dump($a->calculate(array(5,5,5,9)));*/

        global $wpdb;

        $row = $wpdb->get_row('SELECT ID FROM '.$wpdb->prefix.'posts WHERE post_status="publish" AND post_type="match"');

        $view = student_view_path.CONTROLLER.'/matchList.php';
        load_view_template($view,array('row' => $row));
    }

    /**
     * 获取比赛详情
     */
    public function get_match_info($match_id){

        global $wpdb,$current_user;

        //获取比赛详情
        $sql = "select a.ID,a.post_title,a.post_content,b.match_start_time,b.match_use_time,b.match_more,b.match_project_interval,b.match_subject_interval,b.match_address,b.match_cost,
                b.match_address,b.entry_end_time,b.match_category_order,b.str_bit,b.match_status,c.user_id,
                case b.match_status 
                    when -3 then '已结束' 
                    when -2 then '等待开赛' 
                    when -1 then '未开始' 
                    when 1 then '报名中' 
                    when 2 then '比赛中' 
                    end match_status_cn
                from {$wpdb->prefix}posts a 
                left join {$wpdb->prefix}match_meta b on a.ID = b.match_id
                left join {$wpdb->prefix}order c on a.ID = c.match_id
                where a.ID = {$match_id}
                ";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(empty($rows)){
            //$this->get_404('数据错误');
            return $rows;
        }
        $entry_user_id = array_column($rows,'user_id');
        $match = $rows[0];

        if(in_array($current_user->ID,$entry_user_id)) $match['is_me'] = 'y';
        if(!empty($match['entry_end_time'])) $match['entry_end_time_arr'] = str2arr(time_format(strtotime($match['entry_end_time']),'Y-m-d-h-i-s'),'-');
        return $match;
    }

    /**
     * 获取比赛项目
     * @param $match_id 比赛id
     * @param $default 是否调取默认项目
     */
    public function get_match_project($match_id,$default=false){
        global $wpdb,$current_user;
        if(!$default){
            $sql1 = "select a.id,a.post_id,a.match_project_id,b.post_title,b.post_content,b.post_parent,c.post_title parent_title
                 from {$wpdb->prefix}match_project a 
                 left join {$wpdb->prefix}posts b on a.match_project_id = b.ID
                 left join {$wpdb->prefix}posts c on b.post_parent = c.ID
                 where a.post_id = {$match_id}   
                ";
        }else{
            $sql1 = "select b.ID,b.post_title,b.post_parent,c.post_title parent_title
                 from {$wpdb->prefix}posts b
                 left join {$wpdb->prefix}posts c on b.post_parent = c.ID
                 where b.post_type = 'project' and b.post_status = 'publish' ";
        }
        //print_r($sql1);
        $projects = $wpdb->get_results($sql1,ARRAY_A);
        //print_r($projects);
        $project = array();
        if(!empty($projects)){
            foreach ($projects as $val){
                $k = &$val['post_parent'];
                $project[$k]['parent_title'] = $val['parent_title'];
                //获取主训教练

                if(empty($project[$k]['major_coach']) && empty($project[$k]['coach_id'])){

                    $sql = "select a.coach_id,b.display_name 
                        from {$wpdb->prefix}my_coach a 
                        left join {$wpdb->prefix}users b on a.coach_id = b.ID
                        where user_id = {$current_user->ID} and category_id = $k and major = 1
                        ";
                    $row = $wpdb->get_row($sql);
                    //print_r($row);
                    if(!empty($row)){
                        /*$user_meta = get_user_meta($row->coach_id);
                        if(!empty($user_meta['user_real_name'])){
                            $user_real_name = unserialize($user_meta['user_real_name'][0]);
                            $coach = $user_real_name['real_name'];
                        }elseif (!empty($user_meta['last_name']) || !empty($user_meta['first_name'])){
                            $coach = $user_meta['last_name'][0].$user_meta['first_name'][0];
                        }else{
                            $coach = $user_meta['nickname'][0];
                        }
                        $project[$k]['major_coach'] = $coach;*/
                        $project[$k]['major_coach'] = preg_replace('/, /','',$row->display_name);
                        $project[$k]['coach_id'] = $row->coach_id;
                    }
                }

                $val['rule_url'] = home_url('matchs/matchRule/match_id/'.$val['post_id'].'/project_id/'.$val['match_project_id']);
                $project[$k]['project'][] = $val;

            }
        }
         //print_r($project);
        return $project;
    }

    /**
     * 比赛详情页
     */
    public function info(){

        if(!isset($_GET['match_id'])) {
            $this->get_404('参数错误');
            return;
        }
        global $wpdb,$current_user;

        //获取比赛详情
        $match = $this->get_match_info($_GET['match_id']);
        if(empty($match)){
            $this->get_404('参数错误');
            return;
        }
        //print_r($match);
        //获取比赛项目
        $project = $this->get_match_project($_GET['match_id']);
        //print_r($project);

        //获取报名选手列表
        $sql2 = "select user_id,created_time from {$wpdb->prefix}order where match_id = {$_GET['match_id']} order by id desc limit 0,10";
        $orders = $wpdb->get_results($sql2,ARRAY_A);
        $order_total = !empty($orders) ? count($orders) : 0;
        if (!empty($orders)){
            //print_r($orders);
            foreach ($orders as $k => $v){
                $user = get_user_meta($v['user_id']);
                $orders[$k]['nickname'] = $user['nickname'][0];
                $orders[$k]['user_gender'] = !empty($user['user_gender'][0]) ? $user['user_gender'][0] : '--' ;
                $orders[$k]['user_head'] = isset($user['user_head']) ? $user['user_head'][0] : student_css_url.'image/nlyd.png';
                if(!empty($user['user_real_name'])){
                    $user_real = unserialize($user['user_real_name'][0]);
                    $orders[$k]['real_age'] = $user_real['real_age'];

                }else{
                    $orders[$k]['real_age'] = '--';
                }
            }
        }
        //print_r($match);

        $data = array('match'=>$match,'match_project'=>$project,'total'=>$order_total,'entry_list'=>$orders);
        $view = student_view_path.CONTROLLER.'/matchDetail.php';
        load_view_template($view,$data);
    }

    /**
     * 查看比赛规则
     */
    public function matchRule(){

        if(!isset($_GET['project_id'])){
            $this->get_404('参数错误');
            return;
        }
        $row = get_post($_GET['project_id']);

        $data = array(
            'post_content'=>$row->post_content,
        );
        $view = student_view_path.CONTROLLER.'/match-Rule.php';
        load_view_template($view,$data);
    }
    /**
     * 比赛等待倒计时页面
     */
     public function matchWaitting(){

         global $wpdb,$current_user;

         if(!isset($_GET['match_id'])){
             //获取用户即将开赛的比赛信息
             $sql = "select a.match_id,a.match_start_time from {$wpdb->prefix}match_meta a 
                left join {$wpdb->prefix}order b on a.match_id = b.match_id
                WHERE a.match_status = -2 AND a.match_start_time > NOW() AND b.user_id = {$current_user->ID} AND pay_status = 2 
                ORDER BY match_start_time asc limit 1
                ";
             //print_r($sql);
             $row = $wpdb->get_row($sql,ARRAY_A);
             //var_dump($row);
             if(empty($row)){
                 $this->get_404('最近暂无比赛');
                 return;
             }
             if(empty($this->project_order_array)){
                 $result = $this->get_match_content($row['match_id']);
                 if($result['status'] == -1){
                     $this->get_404($result['info']);
                     return;
                 }
             }

             $this->match_id = $row['match_id'];
         }/*else{

             //获取比赛状态
             $sql = "select match_status from {$wpdb->prefix}match_meta where match_id = {$_GET['match_id']}";
             $row = $wpdb->get_row($sql,ARRAY_A);
             print_r($row);
         }*/

         $start = reset($this->project_order_array);
         $end = end($this->project_order_array);
         //print_r($this->project_order_array);
         if(strtotime($start['project_start_time']) > time()){
             $next_match_project = $start;
             $num = 1;
         }elseif (strtotime($end['project_end_time']) < time()){
             $this->get_404('比赛结束');
             return;
         }else{
             foreach ($this->project_order_array as $k => $val){

                 $project_start_time = strtotime($val['project_start_time']);
                 $project_end_time = strtotime($this->project_order_array[$k+1]['project_start_time']);
                 //leo_dump($this->project_order_array[$k+1]['project_start_time']);
                 if( ($project_start_time < time() && time() < $project_end_time) ){
                     $next_match_project = $this->project_order_array[$k+1];
                     $num = ($k+1)+1;   //第几个项目
                     break;
                 }
             }
         }

         //print_r($this->project_order_array);
         //print_r($next_match_project);

         $data['match_url'] = home_url('matchs/initialMatch/match_id/'.$this->match_id.'/project_id/'.$next_match_project['match_project_id']);
         $data['count_down'] = strtotime($next_match_project['project_start_time'])-time();
         $data['match_title'] = $this->match_title;
         $data['project_title'] = $next_match_project['post_title'];
         $data['project_num'] = $num;

        $view = student_view_path.CONTROLLER.'/match-waitting.php';
        load_view_template($view,$data);
    }

    /*
     * 比赛项目初始页
     */
    public function initialMatch(){

        if(empty($_GET['match_id']) || empty($_GET['project_id'])){
            $this->get_404('参数错误');
            return;
        }

        global $wpdb,$current_user;

        $row = $this->get_match_order($current_user->ID,$_GET['match_id']);
        //print_r($row);
        if(empty($row)){

            $this->get_404('你未报名');
            return;
        }else{
            if($row->pay_status == 1){
                $this->get_404('你未付款');
                return;
            }
        }
        //正式时取消此test
        if(empty($_GET['test'])){

            if( time() > $this->project_end_time){

                $this->get_404(array('message'=>'该比赛项目已结束','match_url'=>home_url('/matchs/info/match_id/'.$this->match_id),'waiting_url'=>home_url('matchs/matchWaitting/match_id/'.$this->match_id)));
                return;
            }

            if( time() < $this->project_start_time ){
                $error_data = array(
                                'message'=>'该比赛项目未开始','match_url'=>home_url('/matchs/info/match_id/'.$this->match_id),
                                'waiting_url'=>home_url('matchs/matchWaitting/match_id/'.$this->match_id),
                                'start_count_down' => $this->project_start_time - time(),
                            );
                //var_dump($error_data);
                $this->get_404($error_data);
                return;
            }

        }

        /*leo_dump(date('Y-m-d H:i:s',time()));
        leo_dump(date('Y-m-d H:i:s',$this->project_start_time));
        leo_dump(date('Y-m-d H:i:s',$this->project_end_time));*/
        //var_dump($this->project_alias);

        $match_more = isset($_GET['match_more']) ? $_GET['match_more'] : 1;

        if($match_more > $this->default_match_more) $match_more = $this->default_match_more;

        //设置倒计时

        /*****测试使用*****/
        if($_GET['test'] == 1){
            $this->redis->del($this->project_alias.'_question'.$current_user->ID.'_'.$this->current_more);
            $this->redis->del('count_down'.$current_user->ID.$this->project_alias.$this->current_more);
        }

        $count_down_redis = $this->redis->get('count_down'.$current_user->ID.$this->project_alias.$this->current_more);
        //print_r($count_down);
        //var_dump($this->default_count_down);
        if(empty($count_down_redis)){
            $count_down = time()+$this->default_count_down;
            $this->redis->setex('count_down'.$current_user->ID.$this->project_alias.$this->current_more,$this->default_count_down,$count_down);
        }

        $match_questions = '';
        $questions_answer = '';

        if($this->project_alias == 'wzsd'){
            //var_dump('wzsd_question'.$current_user->ID.'_'.$this->current_more);
            //var_dump($this->redis->get('wzsd_question'.$current_user->ID.'_'.$this->current_more));
            if(!empty($this->redis->get($this->project_alias.'_question'.$current_user->ID.'_'.$this->current_more))){
                $question = json_decode($this->redis->get($this->project_alias.'_question'.$current_user->ID.'_'.$this->current_more));
                //var_dump($question);
            }else{

                //获取文章速读考题
                $category = get_term_by( 'slug', 'match-question', 'question_genre' );
                //var_dump($category);
                $posts = get_posts(array(
                        'numberposts' => 10, //输出的文章数量
                        'post_type' => 'question',	//自定义文章类型名称
                        'orderby'=>'rand',
                        'tax_query'=>array(
                            array(
                                'taxonomy'=>'question_genre', //自定义分类法名称
                                'terms'=>$category->term_id //id为64的分类。也可是多个分类array(12,64)
                            )
                        ),
                    )
                );
                $question = $posts[0];
                //print_r($question);

                $this->redis->setex('wzsd_question'.$current_user->ID.'_'.$this->current_more,$this->default_count_down,json_encode($question));

                //获取当前题目所有问题
                $sql1 = "select a.ID,a.post_title,b.problem_select,problem_answer
                    from {$wpdb->prefix}posts a 
                    left join {$wpdb->prefix}problem_meta b on a.ID = b.problem_id
                    where a.post_parent = {$question->ID} order by b.id asc
                    ";
                $rows = $wpdb->get_results($sql1,ARRAY_A);
                $questions_answer = array();
                $match_questions = array();

                if(!empty($rows)){
                    foreach ($rows as $k => $val){
                        $key = &$val['ID'];
                        $questions_answer[$key]['problem_select'][] = $val['problem_select'];
                        $questions_answer[$key]['problem_answer'][] = $val['problem_answer'];
                    }
                    $match_questions = array_unique(array_column($rows,'post_title','ID'));
                }

            }
        }
        elseif ($this->project_alias == 'pkjl'){
            //$this->redis->del('wzsd_question'.$current_user->ID);
            //var_dump($this->redis->get('wzsd_question'.$current_user->ID));
            if(!empty($this->redis->get($this->project_alias.'_question'.$current_user->ID))){
                $poker = json_decode($this->redis->get($this->project_alias.'_question'.$current_user->ID),true);
                //var_dump($question);
            }else{

                $poker = poker_create();
                $this->redis->setex($this->project_alias.'_question'.$current_user->ID,$this->default_count_down,json_encode($poker));

                $match_questions = $questions_answer = $poker;
            }

            if(!empty($poker)){
                $poker_array = array();
                foreach ($poker as $k =>$val){
                    $poker_array[] = str2arr($val,'-');
                }
                $question = json_encode($poker_array);
            }
        }
        elseif ($this->project_alias == 'szzb'){
            if(!empty($this->redis->get($this->project_alias.'_question'.$current_user->ID))){
                $rang_str = $this->redis->get($this->project_alias.'_question'.$current_user->ID);
                //var_dump($rang_str);
                $question = !empty($rang_str) ? json_decode($rang_str,true) : '';
            }else{
                $rang_array = rang_str_arr($this->project_str_len);
                $this->redis->setex($this->project_alias.'_question'.$current_user->ID,$this->default_count_down,json_encode($rang_array));

                $match_questions = $questions_answer = $question = $rang_array;
            }
        }

        //保存题目
        $sql = "select id,user_id,match_id,project_id,match_questions,answer_status from {$wpdb->prefix}match_questions where user_id = {$current_user->ID} and match_id = {$_GET['match_id']} and project_id = {$_GET['project_id']} and match_more = {$match_more}";
        //print_r($sql);
        $row = $wpdb->get_row($sql ,ARRAY_A);

        if(empty($row)){

            $insert_data = array(
                'user_id'=>$current_user->ID,
                'match_id'=>$_GET['match_id'],
                'project_id'=>$_GET['project_id'],
                'match_more'=>$match_more,
                'match_questions'=>empty($match_questions) ? '' : json_encode($match_questions),
                'questions_answer'=>empty($questions_answer) ? '' : json_encode($questions_answer),
                'created_time'=>date('Y-m-d H:i:s',time()),
            );
            //print_r($insert_data);die;
            $a = $wpdb->insert($wpdb->prefix.'match_questions',$insert_data);
            //leo_dump($a);

        }
        else{

            //判断状态
            if(!empty($row['answer_status'])){
                if($row['answer_status'] == 1){
                    $messahe = '答案已提交';
                    $this->get_404($messahe);
                    return;
                }
            }

        }

        $data = array(
            'questions'=>$question,
            'match_title'=>$this->match_title,
            'match_more_cn'=>chinanum($this->current_more),
            'count_down'=> !empty($count_down_redis) ? $count_down_redis-time() : $count_down - time(),
            'project_title'=>$this->project_title,
            'project_alias'=>$this->project_alias,
        );
        //$data['count_down'] = 3000;

        if(in_array($this->project_alias,array('zxss','kysm'))){

            $data['child_count_down'] = $this->child_count_down;
            /*var_dump($this->redis->get('even_add'.$current_user->ID.'_'.$this->current_more));
            leo_dump($this->redis->get('even_add'.$current_user->ID.'_'.$this->current_more)-time());
            leo_dump($this->redis->get('add_and_subtract'.$current_user->ID.'_'.$this->current_more)-time());
            leo_dump($this->redis->get('wax_and_wane'.$current_user->ID.'_'.$this->current_more)-time());*/

            if(!empty($this->redis->get('even_add'.$current_user->ID.'_'.$this->current_more))){

                $data['child_type_down'] = $this->redis->get('even_add'.$current_user->ID.'_'.$this->current_more) - time();
                $data['child_type'] = 0;

            }elseif (!empty($this->redis->get('add_and_subtract'.$current_user->ID.'_'.$this->current_more))){

                $data['child_type_down'] = $this->redis->get('add_and_subtract'.$current_user->ID.'_'.$this->current_more) - time();
                $data['child_type'] = 1;

            }elseif (!empty($this->redis->get('wax_and_wane'.$current_user->ID.'_'.$this->current_more))){

                $data['child_type_down'] = $this->redis->get('wax_and_wane'.$current_user->ID.'_'.$this->current_more) - time();
                $data['child_type'] = 2;
            }
            //$data['child_type'] = $this->redis->get('child_type'.$current_user->ID);

            //$data['child_count_down'] = 700;
        }

        //print_r($data);die;
        $view = student_view_path.'matchs/match-initial.php';
        load_view_template($view,$data);
        /*if( $this->project_start_time < time() && time() < $this->project_end_time ){


        }else{
            $this->redis->del('count_down'.$current_user->ID.$this->project_alias.$this->current_more);
            $this->get_404(array('message'=>'非法操作','match_url'=>home_url('/matchs/info/match_id/'.$this->match_id),'waiting_url'=>home_url('matchs/matchWaitting/match_id/'.$this->match_id)));
            return;

        }*/

    }

    /*
     * 比赛项目记忆完成答题页
     */
    public function answerMatch(){

        if(empty($_GET['match_id']) || empty($_GET['project_id'])){
            $this->get_404('参数错误');
            return;
        }

        global $wpdb,$current_user;

        $row = $this->get_match_order($current_user->ID,$_GET['match_id']);
        //print_r($row);
        if(empty($row)){

            $this->get_404('你未报名');
            return;
        }else{
            if($row->pay_status == 1){
                $this->get_404('你未付款');
                return;
            }
        }


        $match_more = isset($_GET['match_more']) ? $_GET['match_more'] : 1;

        if($match_more > $this->default_match_more) $match_more = $this->default_match_more;

        //获取比赛考题
        $sql = "select id,user_id,match_id,project_id,match_questions,questions_answer,answer_status from {$wpdb->prefix}match_questions where user_id = {$current_user->ID} and match_id = {$_GET['match_id']} and project_id = {$_GET['project_id']} and match_more = {$match_more}";
        //print_r($sql);
        $row = $wpdb->get_row($sql ,ARRAY_A);
        //print_r($row);

        if(empty($row)){

            $this->get_404('数据错误');
            return;

        }else{

            //判断状态
            if(!empty($row['answer_status'])){
                if($row['answer_status'] == 1){
                    $messahe = '答案已提交';
                    $this->get_404($messahe);
                    return;
                }
            }

        }

        $count_down = $this->redis->get('count_down'.$current_user->ID.$this->project_alias.$this->current_more);

        $data = array(
            'match_title'=>$this->match_title,
            'project_alias'=>$this->project_alias,
            'match_more_cn'=>chinanum($match_more),
            'count_down'=>$count_down-time(),
            'project_title'=>$this->project_title,
            'match_questions'=>empty($row['match_questions']) ? '' : json_decode($row['match_questions'],true),
            'questions_answer' =>empty($row['questions_answer']) ? '' : json_decode($row['questions_answer'],true),
        );

        if ($this->project_alias == 'pkjl'){
            $poker = poker_create(false);

            if(!empty($poker)){
                $list = array();
                foreach ($poker as $v){
                    $val = str2arr($v,'-');
                    //'heart','club','diamond','spade'
                    if($val[0] == 'heart'){

                        $list['heart']['content'][] = $val[1];
                        $list['heart']['color'] = '638;';

                    }elseif ($val[0] == 'club'){

                        $list['club']['content'][] = $val[1];
                        $list['club']['color'] = '635';

                    } elseif ($val[0] == 'diamond'){

                        $list['diamond']['content'][] = $val[1];
                        $list['diamond']['color'] = '634';

                    }elseif ($val[0] == 'spade'){

                        $list['spade']['content'][] = $val[1];
                        $list['spade']['color'] = '636';

                    }
                }

            }

            $data['list'] = $list;
            $data['list_keys'] = array_keys($list);

        }
        if($this->project_alias == 'szzb'){
            $data['str_length'] = $this->project_str_len;
        }
        //print_r($data);die;
        $view = student_view_path.CONTROLLER.'/match-answer.php';
        load_view_template($view,$data);
    }

    /*
     * 比赛项目答题结果页
     */
    public function answerLog(){

        if(empty($_GET['match_id']) || empty($_GET['project_id']) || empty($_GET['match_more'])){
            $this->get_404('参数错误');
            return;
        }
        global $wpdb,$current_user;

        //清空倒计时
        if(!empty($this->redis->get('count_down'.$current_user->ID.$this->project_alias.$this->current_more))){
            $this->redis->del('count_down'.$current_user->ID.$this->project_alias.$this->current_more);
        }
        //清空题目
        if(!empty($this->redis->get($this->project_alias.'_question'.$current_user->ID.'_'.$this->current_more))){
            $this->redis->del($this->project_alias.'_question'.$current_user->ID.'_'.$this->current_more);
        }


        $order = $this->get_match_order($current_user->ID,$_GET['match_id']);
        if(empty($order)){
            $this->get_404('你未报名');
            return;
        }else{

            if($order->pay_status != 2){
                $this->get_404('订单未付款');
                return;
            }
        }

        $row = $this->get_match_questions($_GET['match_id'],$_GET['project_id'],$_GET['match_more']);

        if(empty($row)){
            $this->get_404('数据错误');
            return;
        }else{
            if($row['answer_status'] != 1){
                $this->get_404('操作错误,你未进行答题');
                return;
            }
        }
        $match_questions = json_decode($row['match_questions'],true);
        $questions_answer = json_decode($row['questions_answer'],true);
        $my_answer = !empty($row['my_answer']) ? json_decode($row['my_answer'],true) : array();

        if(in_array($this->project_alias,array('wzsd'))){
            $len = count($questions_answer);
            $success_len = 0;

            foreach ($questions_answer as $k=>$val){
                $arr = array();
                $answerArr = array();
                foreach ($val['problem_answer'] as $key => $v){
                    if($v == 1){
                        $arr[] = $key;
                        $answerArr[] = $key;
                    }
                }
                $questions_answer[$k]['problem_answer'] = $answerArr;
                if(isset($my_answer[$k])){
                    if(arr2str($arr) == arr2str($my_answer[$k])) ++$success_len;
                }
            }

        }else{

            if(!empty($questions_answer)){
                $len = count($questions_answer);
                $error_arr = array_diff_assoc($questions_answer,$my_answer);
                $error_len = count($error_arr);
                $success_len = $len - $error_len;
            }else{
                $my_answer = array();
                $error_arr = array();
                $success_len = 0;
                $len = 0;
            }
        }
        $ranking = '';
        if($this->project_end_time < time()){
            //获取本轮排名
            $sql = "select user_id from {$wpdb->prefix}match_questions where match_id = {$_GET['match_id']} and project_id = {$_GET['project_id']} and match_more = {$_GET['match_more']} group by my_score desc,surplus_time desc";
            $rows = $wpdb->get_results($sql,ARRAY_A);

            $ranking = array_search($current_user->ID,array_column($rows,'user_id'))+1;
        }

        //判断是否有新的比赛轮次或者新的项目
        $next = false;
        if($_GET['match_more'] < $this->default_match_more){

            $next_count_down = $this->default_more_interval;
            $match_more = (int)$this->current_more+1;
            $project_id = $this->project_id;
            $next = true;
            $next_type = 1;

        }else{

            $match_more = 1;
            $key = array_search($this->project_id,$this->project_id_array);
            $next_key = $this->project_id_array[$key+1];
            //获取下一个比赛项
            $next_match_project = $this->project_key_array[$next_key];
            //正式上线取消test注释
            if($_GET['test'] == 1){

                if(!empty($next_match_project)){

                    $next_count_down = strtotime($next_match_project['project_start_time'])-time();
                    $project_id = $next_match_project['match_project_id'];
                    $next = true;
                    $next_type = 2;
                }

            }else{

                if(!empty($next_match_project)){
                    $next_count_down = strtotime($next_match_project['project_start_time'])-time();
                    if($next_count_down > 0){
                        $project_id = $next_match_project['match_project_id'];
                        $next = true;
                        $next_type = 2;
                    }else{
                        $next_type = 3;
                    }
                    //var_dump($next_type);
                }
            }
            //print_r($next_match_project);
        }

        if($next){
            if(empty($this->redis->get('next_count_down'.$current_user->ID.$this->project_alias.'_'.$this->current_more))){

                $this->redis->setex('next_count_down'.$current_user->ID.$this->project_alias.'_'.$this->current_more,$next_count_down,time()+$next_count_down);
            }
            $next_project_url = home_url('/matchs/initialMatch/match_id/'.$this->match_id.'/project_id/'.$project_id.'/match_more/'.$match_more);
        }else{
            if($next_type == 3){
                $next_project_url = home_url('/matchs/matchWaitting/match_id/'.$this->match_id);
            }else{

                $next_project_url = home_url('/matchs/info/match_id/'.$this->match_id);
                $next_type = 4;
            }
        }

        $data = array(
            'project_alias'=>$this->project_alias,
            'next_type'=>$next_type,
            'str_len'=>$len,
            'match_more_cn'=>chinanum($_GET['match_more']),
            'success_length'=>$success_len,
            'use_time'=>$this->default_count_down-$row['surplus_time'],
            'surplus_time'=>$row['surplus_time'],
            'accuracy'=>$success_len > 0 ? round($success_len/$len,2)*100 : 0,
            'ranking'=>$ranking,
            'match_questions'=>$match_questions,
            'questions_answer'=>$questions_answer,
            'my_answer'=>$my_answer,
            'my_score'=>$row['my_score'],
            'project_title'=>$this->project_title,
            'error_arr'=>!empty($error_arr) ? array_keys($error_arr) : array(),
            'next_count_down'=>$this->redis->get('next_count_down'.$current_user->ID.$this->project_alias.'_'.$this->current_more)-time(),
            'next_project_url'=>$next_project_url,
            'record_url'=>home_url('matchs/record/type/project/match_id/'.$this->match_id.'/project_id/'.$this->project_id.'/match_more/'.$this->current_more),
        );

        /********测试使用*********/
        if($_GET['test'] == 1){
            $data['next_count_down'] = 50;
        }
        //print_r($data);
        $view = student_view_path.CONTROLLER.'/match-answer-log.php';
        load_view_template($view,$data);
    }



    /**
     * 信息确认页
     */
    public function confirm(){

        if(!isset($_GET['match_id'])) {
            $this->get_404('参数错误');
            return;
        }
        //获取比赛详情
        $match = $this->get_match_info($_GET['match_id']);

        //获取比赛项目
        //update_option('match_project_default',true);
        $match_project_default = get_option('match_project_default');
        $project = $this->get_match_project($_GET['match_id'],$match_project_default);
        //print_r($project);
        //获取选手信息
        global $current_user,$user_info,$wpdb;
        $player['real_name'] = isset($user_info['user_real_name']['real_name']) ? $user_info['user_real_name']['real_name'] : '';
        //获取比赛战队
        $sql= "select a.team_id,b.post_title,b.post_content from {$wpdb->prefix}match_team a left join {$wpdb->prefix}posts b on a.team_id = b.ID where a.user_id = {$current_user->ID} and a.status = 2";
        $row = $wpdb->get_row($sql);
        $player['user_team'] = $row->post_title;
        $player['team_id'] = $row->team_id;
        $player['user_ID'] = $user_info['user_ID'];
        //print_r($player);
        //获取邮寄地址
        $where[] = " user_id = {$current_user->ID} ";
        if(isset($_GET['address_id'])){
            $where[] = " id = {$_GET['address_id']} ";
        }else{
            $where[] = " is_default = 1 ";
        }
        $where = join(' and ',$where);
        $sql1 = "select fullname,telephone,concat_ws('',country,province,city,area,address) user_address from {$wpdb->prefix}my_address where {$where}";
        $address = $wpdb->get_row($sql1,ARRAY_A);
        //print_r($sql1);
        $data = array('match'=>$match,'match_project'=>$project,'player'=>$player,'address'=>$address);

        $view = student_view_path.CONTROLLER.'/confirm.php';
        load_view_template($view,$data);
    }


    /**
     * 战绩排名
     */
     public function record(){

         if(empty($_GET['match_id'])){
             $this->get_404('参数错误');
             return;
         }

         //获取当前项目排名
         global $wpdb,$current_user;

         $page = ($page = isset($_GET['page']) ? intval($_GET['page']) : 1) < 1 ? 1 : $page;
         $pageSize = 10;
         $start = ($page-1) * $pageSize;

         if($_GET['type'] == 'project'){

             if(empty($_GET['project_id']) || empty($_GET['match_more'])){

                 $this->get_404('参数错误');
                 return;
             }
             $sql = "select user_id,my_score from {$wpdb->prefix}match_questions where match_id = {$_GET['match_id']} and project_id = {$_GET['project_id']} and match_more = {$_GET['match_more']} order by my_score desc,surplus_time desc limit {$start},{$pageSize} ";
             //print_r($sql);
             $rows = $wpdb->get_results($sql,ARRAY_A);

             $title = $this->match_title;
         }else{
             $where = " where match_id = {$_GET['match_id']} ";
             //判断是否存在分类id
             if(!empty($_GET['category_id'])){

                 //获取当前分类下项目
                 $sql_ = "select ID from {$wpdb->prefix}posts where post_parent = {$_GET['category_id']}";
                 $match_category = $wpdb->get_results($sql_,ARRAY_A);
                 if(!empty($match_category)){
                     $project_id = arr2str(array_column($match_category,'ID'));
                     $where .= " and project_id in ({$project_id}) ";
                 }
             }

             if(!empty($_GET['project_id'])){
                 $where .= " and project_id = {$_GET['project_id']} ";
             }

             $sql = "select a.user_id,SUM(a.score) my_score from (select user_id,project_id,match_more,surplus_time,MAX(my_score) score from {$wpdb->prefix}match_questions {$where} GROUP BY user_id,project_id) a GROUP BY user_id order by my_score desc limit {$start},{$pageSize} ";
             //print_r($sql);
             $rows = $wpdb->get_results($sql,ARRAY_A);
             //var_dump($rows);
             $title = $this->match['post_title'];
             //获取比赛类别
             $category = $this->ajaxControll->get_coach_category(false);

         }
         $list = array();
         if(!empty($rows)){
             foreach ($rows as $k => $val){
                 $sql1 = " select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id = {$val['user_id']} and meta_key in('user_address','user_ID','user_real_name') ";
                 $info = $wpdb->get_results($sql1,ARRAY_A);

                 if(!empty($info)){
                     $user_info = array_column($info,'meta_value','meta_key');
                     $user_real_name = !empty($user_info['user_real_name']) ? unserialize($user_info['user_real_name']) : '';

                     $list[$k]['user_name'] = !empty($user_real_name['real_name']) ? $user_real_name['real_name'] : '-';
                     if(!empty($user_real_name['real_age'])){
                         $age = $user_real_name['real_age'];
                         switch ($age){
                             case $age > 59:
                                 $group = '老年组';
                                 break;
                             case $age > 18:
                                 $group = '成人组';
                                 break;
                             case $age > 13:
                                 $group = '少年组';
                                 break;
                             default:
                                 $group = '儿童组';
                                 break;
                         }

                     }else{
                         $group = '-';
                     }
                     if(!empty($user_info['user_address'])){
                         $user_address = unserialize($user_info['user_address']);
                         $city = $user_address['city'];
                     }else{
                         $city = '-';
                     }
                     $list[$k]['ID'] = $user_info['user_ID'];
                     $list[$k]['city'] = $city;
                     $list[$k]['score'] = $val['my_score'] > 0 ? $val['my_score'] : 0;
                     $list[$k]['group'] = $group;
                     $list[$k]['ranking'] = $k+1;

                     if($val['user_id'] == $current_user->ID){
                         $my_ranking = $list[$k];
                     }
                 }
             }
         }
         $match_category = empty($category) ? '' : $category;
         $default_category = empty($this->project_order_array) ? '' : $this->project_order_array;
         //print_r($default_category);
         //判断是否报名该比赛
         $order = $this->get_match_order($current_user->ID,$_GET['match_id']);

         $data = array('pay_status'=>$order->pay_status,'list'=>$list,'my_ranking'=>$my_ranking,'match_title'=>$title,'match_category'=>$match_category,'count'=>count($rows) > 10 ? count($rows) : 0,'default_category'=>$default_category);
         //print_r($data);
         $view = student_view_path.CONTROLLER.'/record.php';
         load_view_template($view,$data);
    }
    /**
     * 单项成绩排名
     */
     public function singleRecord (){
         if(empty($_GET['match_id']) || empty($_GET['project_id'])){
             $this->get_404('参数错误');
             return;
         }
         //print_r($this->match_alias);
         switch ($this->match_alias){

             case 'szzb':    //数字争霸
                 $action = 'subjectNumberBattle';
                 break;
             case 'pkjl':    //扑克接力
                 $action = 'subjectPokerRelay';
                 break;
             case 'zxss':    //正向速算
                 $action = 'subjectFastCalculation';
                 break;
             case 'nxss':    //逆向速算
                 $action = 'subjectFastReverse';
                 break;
             case 'wzsd':     //文章速读
                 $action = 'subjectReading';
                 break;
             case 'kysm':    //快眼扫描
                 $action = 'subjectfastScan';
                 break;
             default:
                 $action = 'numberBattleReady';
                 break;
         }
         $data = array(
             'post_title'=>$this->match['post_title'],
             'match_title'=>$this->match_title,
             'match_more'=>$this->default_match_more,
             'answer_url'=>home_url('/matchs/'.$action.'/match_id/'.$_GET['match_id'].'/project_id/'.$_GET['project_id'].'/type/select'),
         );

         //print_r($data);
         $view = student_view_path.CONTROLLER.'/singleRecord.php';
         load_view_template($view,$data);
    }
     /**
     * 数字争霸本轮答题记录
     */
     public function subjectNumberBattle (){

         if(empty($_GET['match_id']) || empty($_GET['project_id']) || empty($_GET['match_more'])){
             $this->get_404('参数错误');
         }
         global $wpdb,$current_user;

         $row1 = $this->get_match_order($current_user->ID,$_GET['match_id']);
         if(empty($row1)){
             $this->get_404('你未报名');
             return;
         }
         $row = $this->get_match_questions($_GET['match_id'],$_GET['project_id'],$_GET['match_more']);

         if(empty($row)){
             $this->get_404('数据错误');
             return;
         }else{
             if($row['answer_status'] != 1){
                 $this->get_404('操作错误,你未进行答题');
                 return;
             }
         }
         $questions_answer = json_decode($row['questions_answer']);
         $len = count($questions_answer);
         $my_answer = !empty($row['my_answer']) ? json_decode($row['my_answer']) : array();
         $error_arr = array_diff_assoc($questions_answer,$my_answer);
         $error_len = count($error_arr);
         $success_len = $len - $error_len;
         $ranking = '';

         if(empty($this->default_count_down)){
             //获取本轮排名
             $sql = "select user_id from {$wpdb->prefix}match_questions where match_id = {$_GET['match_id']} and project_id = {$_GET['project_id']} and match_more = {$_GET['match_more']} order by my_score desc,surplus_time desc";
             $rows = $wpdb->get_results($sql,ARRAY_A);

             $ranking = array_search($current_user->ID,array_column($rows,'user_id'))+1;
         }

         $data = array(
                'str_len'=>$len,
                'match_more_cn'=>chinanum($_GET['match_more']),
                'success_length'=>$success_len,
                'use_time'=>$this->default_use_time-$row['surplus_time'],
                'surplus_time'=>$row['surplus_time'],
                'accuracy'=>round($success_len/$len,2)*100,
                'ranking'=>$ranking,
                'questions_answer'=>$questions_answer,
                'my_answer'=>$my_answer,
                'my_score'=>$row['my_score'],
                'match_title'=>$row['post_title'],
                'error_arr'=>array_keys($error_arr),
                'next_more_down'=>'',
                'next_project_down'=>'',
                'record_url'=>home_url('/matchs/record/type/project/match_id/'.$_GET['match_id'].'/project_id/'.$_GET['project_id'].'/match_more/'.$_GET['match_more']),
             );


         //判断是否有新的比赛轮次或者新的项目
         if($_GET['match_more'] < $this->default_match_more){

             $data['next_more_down'] = $this->default_subject_interval + $this->default_count_down;
             $match_more = (int)$_GET['match_more']+1;
             $data['next_more_url'] = home_url('matchs/numberBattleReady/match_id/'.$_GET['match_id'].'/project_id/'.$_GET['project_id'].'/match_more/'.$match_more);
         }else{

             $next = array_search($this->match_alias,$this->default_match)+1;

             if(!empty($this->default_category[$next])){
                 $match_category = $this->default_category[$next];
                 $data['next_project_down'] = $this->default_project_interval + $this->default_count_down;

                 switch ($match_category['project_alias']){

                     case 'szzb':
                         $action = 'numberBattleReady';
                         break;
                     case 'pkjl':
                         $action = 'pokerRelayReady';
                         break;
                     case 'zxss':
                         $action = 'fastCalculation';
                         break;
                     case 'nxss':
                         $action = 'fastReverse';
                         break;
                     case 'wzsd':
                         $action = 'readingReady';
                         break;
                     case 'kysm':
                         $action = 'fastScan';
                         break;
                     default:
                         $action = 'numberBattleReady';
                         break;
                 }
                 $data['next_project_url'] = home_url('matchs/'.$action.'/match_id/'.$_GET['match_id'].'/project_id/'.$match_category['ID'].'/match_more/1');
             }
         }

         //print_r($data);
        $view = student_view_path.CONTROLLER.'/subject-numberBattle.php';
        load_view_template($view,$data);
     }
     /**
     * 扑克接力本轮答题记录
     */
     public function subjectPokerRelay (){

         if(empty($_GET['match_id']) || empty($_GET['project_id']) || empty($_GET['match_more'])){
             $this->get_404('参数错误');
         }
         global $wpdb,$current_user;

         $row = $this->get_match_order($current_user->ID,$_GET['match_id']);
         if(empty($row)){
             $this->get_404('你未报名');
             return;
         }
         $row = $this->get_match_questions($_GET['match_id'],$_GET['project_id'],$_GET['match_more']);

         if(empty($row)){
             $this->get_404('数据错误');
             return;
         }else{
             if($row['answer_status'] != 1){
                 $this->get_404('操作错误,你未进行答题');
                 return;
             }
         }
         $questions_answer = json_decode($row['questions_answer']);
         $len = count($questions_answer);
         $my_answer = !empty($row['my_answer']) ? json_decode($row['my_answer']) : array();
         $error_arr = array_diff_assoc($questions_answer,$my_answer);
         $error_len = count($error_arr);
         $success_len = $len - $error_len;
         $ranking = '';

         if(empty($this->default_count_down)){
             //获取本轮排名
             $sql = "select user_id from {$wpdb->prefix}match_questions where match_id = {$_GET['match_id']} and project_id = {$_GET['project_id']} and match_more = {$_GET['match_more']} group by my_score desc,surplus_time desc";
             $rows = $wpdb->get_results($sql,ARRAY_A);

             $ranking = array_search($current_user->ID,array_column($rows,'user_id'))+1;
         }

         $data = array(
             'str_len'=>$len,
             'match_more_cn'=>chinanum($_GET['match_more']),
             'success_length'=>$success_len,
             'use_time'=>$this->default_use_time-$row['surplus_time'],
             'surplus_time'=>$row['surplus_time'],
             'accuracy'=>round($success_len/$len,2)*100,
             'ranking'=>$ranking,
             'questions_answer'=>$questions_answer,
             'my_answer'=>$my_answer,
             'my_score'=>$row['my_score'],
             'match_title'=>$row['post_title'],
             'error_arr'=>array_keys($error_arr),
             'next_more_down'=>'',
             'next_project_down'=>'',
             'record_url'=>home_url('matchs/record/type/project/match_id/'.$_GET['match_id'].'/project_id/'.$_GET['project_id'].'/match_more/'.$_GET['match_more']),
         );


         //判断是否有新的比赛轮次或者新的项目
         if($_GET['match_more'] < $this->default_match_more){

             $data['next_more_down'] = $this->default_subject_interval + $this->default_count_down;
             $match_more = (int)$_GET['match_more']+1;
             $data['next_more_url'] = home_url('/matchs/pokerRelayReady/match_id/'.$_GET['match_id'].'/project_id/'.$_GET['project_id'].'/match_more/'.$match_more);
         }else{
             $next = array_search($this->match_alias,$this->default_match)+1;

             if(!empty($this->default_category[$next])){
                 $match_category = $this->default_category[$next];
                 $data['next_project_down'] = $this->default_project_interval + $this->default_count_down;
                 switch ($match_category['project_alias']){

                     case 'szzb':
                         $action = 'numberBattleReady';
                         break;
                     case 'pkjl':
                         $action = 'pokerRelayReady';
                         break;
                     case 'zxss':
                         $action = 'fastCalculation';
                         break;
                     case 'nxss':
                         $action = 'fastReverse';
                         break;
                     case 'wzsd':
                         $action = 'readingReady';
                         break;
                     case 'kysm':
                         $action = 'fastScan';
                         break;
                     default:
                         $action = 'numberBattleReady';
                         break;
                 }
                 $data['next_project_url'] = home_url('/matchs/'.$action.'/match_id/'.$_GET['match_id'].'/project_id/'.$match_category['ID'].'/match_more/1');
             }
         }

         //print_r($data);
         $view = student_view_path.CONTROLLER.'/subject-pokerRelay.php';
         load_view_template($view,$data);
     }

    /**
     * 进入数字争霸准备页面
     */
    public function numberBattleReady (){

        if(empty($_GET['match_id']) || empty($_GET['project_id'])){
            $this->get_404('参数错误');
            return;
        }

        global $wpdb,$current_user;

        $row = $this->get_match_order($current_user->ID,$_GET['match_id']);
        //print_r($row);
        if(empty($row)){
           
            $this->get_404('你未报名');
            return;
        }else{
            if($row->pay_status == 1){
                $this->get_404('你未付款');
            return;
            }
        }
        

        $match_more = isset($_GET['match_more']) ? $_GET['match_more'] : 1;

        if($match_more > $this->default_match_more) $match_more = $this->default_match_more;

        //保存题目
        $sql = "select id,user_id,match_id,project_id,match_questions,answer_status from {$wpdb->prefix}match_questions where user_id = {$current_user->ID} and match_id = {$_GET['match_id']} and project_id = {$_GET['project_id']} and match_more = {$match_more}";
        //print_r($sql);
        $row = $wpdb->get_row($sql ,ARRAY_A);
        if(empty($row)){

            $arr = rang_str_arr($this->default_str_length);

            $insert_data = array(
                'user_id'=>$current_user->ID,
                'match_id'=>$_GET['match_id'],
                'project_id'=>$_GET['project_id'],
                'match_more'=>$match_more,
                'match_questions'=>json_encode($arr),
                'questions_answer'=>json_encode($arr),
                'created_time'=>date('Y-m-d H:i:s',time()),
            );
            //print_r($insert_data);die;
            $a = $wpdb->insert($wpdb->prefix.'match_questions',$insert_data);
            //leo_dump($a);
            $data['list'] = $arr;

        }else{

            //判断状态
            if(!empty($row['answer_status'])){
                if($row['answer_status'] == 1){
                    $messahe = '答案已提交';
                }elseif ($row['answer_status'] == -1){
                    $messahe = '记忆已完成';
                }else{
                    $messahe = '参数错误';
                }
                $this->get_404($messahe);
                return;
            }

            $data['list'] = json_decode($row['match_questions']);
        }
        $data['count_down'] = $this->default_count_down;
        $data['match_title'] = $this->match_title;
        $data['match_more_cn'] = chinanum($match_more);
        $data['post_title'] = $this->match['post_title'];

        $view = student_view_path.CONTROLLER.'/ready-numberBattle.php';

        load_view_template($view,$data);
    }

    /**
     * 进入扑克接力准备页面
     */
    public function pokerRelayReady (){

        if(empty($_GET['match_id']) || empty($_GET['project_id'])){
            $this->get_404('参数错误');
            return;
        }

        global $wpdb,$current_user;

        $row = $this->get_match_order($current_user->ID,$_GET['match_id']);
        //print_r($row);
        if(empty($row)){

            $this->get_404('你未报名');
            return;
        }else{
            if($row->pay_status == 1){
                $this->get_404('你未付款');
                return;
            }
        }

        //var_dump($this->default_str_length);

        $match_more = isset($_GET['match_more']) ? $_GET['match_more'] : 1;

        if($match_more > $this->default_match_more) $match_more = $this->default_match_more;
        //保存题目
        $sql = "select id,user_id,match_id,project_id,match_questions,answer_status from {$wpdb->prefix}match_questions where user_id = {$current_user->ID} and match_id = {$_GET['match_id']} and project_id = {$_GET['project_id']} and match_more = {$match_more}";
        //print_r($sql);
        $row = $wpdb->get_row($sql ,ARRAY_A);
        if(empty($row)){

            $arr = poker_create();

            $insert_data = array(
                'user_id'=>$current_user->ID,
                'match_id'=>$_GET['match_id'],
                'project_id'=>$_GET['project_id'],
                'match_more'=>$match_more,
                'match_questions'=>json_encode($arr),
                'questions_answer'=>json_encode($arr),
                'created_time'=>date('Y-m-d H:i:s',time()),
            );
            //print_r($insert_data);die;
            $a = $wpdb->insert($wpdb->prefix.'match_questions',$insert_data);
            //leo_dump($a);
            $data['list'] = $arr;

        }else{

            //判断状态
            if(!empty($row['answer_status'])){
                if($row['answer_status'] == 1){
                    $messahe = '答案已提交';
                }elseif ($row['answer_status'] == -1){
                    $messahe = '记忆已完成';
                }else{
                    $messahe = '参数错误';
                }
                $this->get_404($messahe);
                return;
            }

            $data['list'] = json_decode($row['match_questions']);
        }
        if(!empty($data['list'])){

            foreach ($data['list'] as $k =>$val){
                $list[] = str2arr($val,'-');
            }
        }
        $data['list'] = json_encode($list);
        $data['count_down'] = $this->match_title;
        $data['post_title'] = $this->match['post_title'];
        $data['count_down'] = $this->default_count_down;

        $data['match_title'] = $this->match_title;
        $data['match_more_cn'] = chinanum($match_more);

        $view = student_view_path.CONTROLLER.'/ready-pokerRelay.php';
        load_view_template($view,$data);
    }
    /**
     * 数字争霸
     */
    public function numberBattle (){

        if(empty($_GET['match_id']) || empty($_GET['project_id']) || empty($_GET['match_more'])){
            $this->get_404('参数错误');
            return;
        }

        $row = $this->get_match_questions($_GET['match_id'],$_GET['project_id'],$_GET['match_more']);
        if(empty($row)){
            $this->get_404('信息错误');
            return;
        }else{

            //判断状态
            if(!empty($row['answer_status'])){
                if($row['answer_status'] == 1){
                    $this->get_404('答案已提交');
                    return;
                }
            }else{
                $this->get_404('请先进行记忆再答题');
                return;
            }
        }
        $match_more = isset($_GET['match_more']) ? $_GET['match_more'] : 1;

        $data = array(
            'match_more_cn'=>chinanum($match_more),
            'match_title'=>$this->match_title,
            'count_down'=>$this->default_count_down,
            'str_length'=>$this->default_str_length
        );
        //print_r($data);
        $view = student_view_path.CONTROLLER.'/matching-numberBattle.php';
        load_view_template($view,$data);
    }

    /**
     * 判断进入哪一个比赛项目
     */
    public function matching(){

        //获取比赛信息
        $match = $this->get_match_info($_GET['match_id']);
        if(empty($match)){
            $this->get_404('参数错误');
            return;
        }
        if(!empty($match['match_category_order'])) $match['match_category_order'] = unserialize($match['match_category_order']);
        //print_r($match);
        //判断排序 进入相关页面
        //wp_redirect(home_url('account/matchList/?action=numberBattleReady/match_id/'.$_GET['match_id']));

    }

    /**
     * 获取用户当前比赛信息
     */
    public function get_match_questions($match_id,$project_id,$match_more){

        global $wpdb,$current_user;
        $sql = "select a.answer_status,a.match_questions,a.questions_answer,a.my_answer,a.surplus_time,if(a.my_score>0,a.my_score,0) as my_score,b.post_title
                from {$wpdb->prefix}match_questions a 
                left join {$wpdb->prefix}posts b on a.project_id = b.ID
                where a.user_id = {$current_user->ID} and a.match_id = {$match_id} and a.project_id = {$project_id} and a.match_more = {$match_more}
                ";
        //print_r($sql);
        $row = $wpdb->get_row($sql,ARRAY_A);
        
        return $row;
    }


    /**
     * 获取项目初始配置
     * @param $project_id 比赛项目id
     */
    public function get_project_default($project_id){
        global $wpdb,$current_user;
        $sql = "select a.*,b.post_title 
                from {$wpdb->prefix}match_project a 
                left join {$wpdb->prefix}posts b on a.match_id = b.ID 
                where a.match_id = {$project_id}  ";
        $match_project = $wpdb->get_row($sql,ARRAY_A);
        leo_dump($sql);
    }

    /**
     * 设置比赛初始配置
     * @param $match_project 当前比赛项目配置
     */
    public function setting_default_config($match_project){

        //每个项目比赛轮数
        $this->default_match_more = $match_project['match_more'] > 0 ? $match_project['match_more'] : $this->match_more;

        //每个项目初始倒计时
        $this->default_count_down = $match_project['project_use_time'] > 0 ? $match_project['project_use_time'] * 60 : $this->match_count_down * 60;

        //每个项目轮数间隔
        $this->default_more_interval = $match_project['project_time_interval'] > 0 ? $match_project['project_time_interval'] * 60: $this->match_subject_interval * 60;

        //当前比赛进行的轮数
        $this->current_more = !empty($_GET['match_more']) ? $_GET['match_more'] : 1;

        //子项倒计时
        if(in_array($this->project_alias,array('zxss','kysm'))){

            $child_count_down = get_post_meta($match_project['match_project_id'],'child_count_down')[0];

            if($this->project_alias == 'zxss'){

                if($match_project['child_count_down'] > 0){
                    $child_count_down['even_add'] = $match_project['child_count_down'] * 60;
                    $child_count_down['add_and_subtract'] = $match_project['child_count_down'] * 60;
                    $child_count_down['wax_and_wane'] = $match_project['child_count_down'] * 60;
                }elseif (!empty($child_count_down)){

                    $child_count_down['even_add'] *= 60;
                    $child_count_down['add_and_subtract'] *= 60;
                    $child_count_down['wax_and_wane'] *= 60;
                }else{

                    $child_count_down['even_add'] = 180;
                    $child_count_down['add_and_subtract'] = 180;
                    $child_count_down['wax_and_wane'] = 180;
                }

                $this->default_count_down = $child_count_down['even_add']+$child_count_down['add_and_subtract']+$child_count_down['wax_and_wane'];

                global $current_user;
                $new_time = time();
                $first_child = $child_count_down['even_add'];
                $two_child = $first_child+$child_count_down['add_and_subtract'];
                $three_child = $two_child+$child_count_down['wax_and_wane'];
                if($_GET['test'] == 1){
                    $this->redis->del('even_add'.$current_user->ID.'_'.$this->current_more);
                    $this->redis->del('add_and_subtract'.$current_user->ID.'_'.$this->current_more);
                    $this->redis->del('wax_and_wane'.$current_user->ID.'_'.$this->current_more);
                }
                if(empty($this->redis->get('even_add'.$current_user->ID.'_'.$this->current_more)) && empty($this->redis->get('add_and_subtract'.$current_user->ID.'_'.$this->current_more)) && empty($this->redis->get('wax_and_wane'.$current_user->ID.'_'.$this->current_more)) ){
                    $this->redis->setex('even_add'.$current_user->ID.'_'.$this->current_more,$first_child,$first_child+$new_time);
                    $first = true;
                }
                if($first){
                    $this->redis->setex('add_and_subtract'.$current_user->ID.'_'.$this->current_more,$two_child,$two_child+$new_time);
                    $two = true;
                }
                if($two){
                    $this->redis->setex('wax_and_wane'.$current_user->ID.'_'.$this->current_more,$three_child,$three_child+$new_time);
                }
                /*var_dump($this->redis->get('even_add'.$current_user->ID.'_'.$this->current_more));
                var_dump($this->redis->get('add_and_subtract'.$current_user->ID.'_'.$this->current_more));
                var_dump($this->redis->get('wax_and_wane'.$current_user->ID.'_'.$this->current_more));*/

                //leo_dump($this->default_count_down);die;
            }else if($this->project_alias == 'kysm'){

                if($match_project['child_count_down'] > 0){
                    $child_count_down = $match_project['child_count_down'];
                }elseif (!empty($child_count_down)){
                    $child_count_down = $child_count_down;
                }else{
                    $child_count_down = 5;
                }

            }
            $this->child_count_down = $child_count_down;
        }

        //初始字符长度
        if(in_array($this->project_alias,array('szzb','kysm'))){

            $str_length = get_post_meta($match_project['default_str_length'],'default_str_length')[0];

            if($match_project['str_bit'] > 0){
                $default_str_length = $match_project['str_bit'];
            }elseif (!empty($str_length)){
                $default_str_length = $str_length;
            }

            if($this->project_alias == 'szzb'){
                $this->project_str_len = $default_str_length > 0 ? $default_str_length : 100;
            }elseif ($this->project_alias == 'kysm'){
                $this->project_str_len = $default_str_length > 0 ? $default_str_length : 6;
            }
        }

    }


    //判断是否报名
    public function get_match_order($user_id,$match_id){
        global $wpdb;
        $sql = "select id,pay_status from {$wpdb->prefix}order where user_id = {$user_id} and match_id = {$match_id} ";
        //print_r($sql);
        return $wpdb->get_row($sql);
    }


    /**
     * 获取比赛相关信息
     */
    public function get_match_content($match_id){

        $this->match = $this->get_match_info($match_id);
        if(empty($this->match)){
            return array('status'=>-1,'info'=>'比赛信息错误');
        }
        //print_r($this->match);die;
        $this->match_id = $this->match['ID'];
        $this->match_title = $this->match['post_title'];
        $this->match_start_time = $this->match['match_start_time'];
        $this->match_more = $this->match['match_more'];
        $this->match_count_down = $this->match['match_use_time'];
        $this->match_project_interval = $this->match['match_project_interval'];
        $this->match_subject_interval = $this->match['match_subject_interval'];

        global $wpdb;
        //对比赛项目进行排序
        $sql1 = "SELECT b.post_title,c.meta_value as project_alias,a.post_id match_id,a.match_project_id,a.project_use_time,a.match_more,a.project_start_time,a.project_time_interval,a.str_bit,a.child_count_down
                     FROM {$wpdb->prefix}match_project a
                     LEFT JOIN {$wpdb->prefix}posts b ON a.match_project_id = b.ID
                     LEFT JOIN {$wpdb->prefix}postmeta c ON a.match_project_id = c.post_id AND meta_key = 'project_alias'
                     WHERE a.post_id = {$match_id} ORDER BY a.project_start_time ASC , a.id ASC 
                     ";
        //print_r($sql1);
        $rows = $wpdb->get_results($sql1,ARRAY_A);
        if(empty($rows)){
            return array('status'=>-1,'info'=>'该比赛未绑定比赛项');
        }
        //print_r($this->match_start_time);
        //计算每个项目结束时间
        foreach ($rows as $k => $row){
            //print_r($row);
            if($row['project_alias'] == 'zxss'){

                $child_count_down = get_post_meta($row['match_project_id'],'child_count_down')[0];
                if($row['child_count_down'] > 0){
                    $child_count_down['even_add'] = $row['child_count_down'];
                    $child_count_down['add_and_subtract'] = $row['child_count_down'];
                    $child_count_down['wax_and_wane'] = $row['child_count_down'];
                }elseif (!empty($child_count_down)){

                    $child_count_down['even_add'] *= 1;
                    $child_count_down['add_and_subtract'] *= 1;
                    $child_count_down['wax_and_wane'] *= 1;
                }else{

                    $child_count_down['even_add'] = 3;
                    $child_count_down['add_and_subtract'] = 3;
                    $child_count_down['wax_and_wane'] = 3;
                }
                $project_use_time = $child_count_down['even_add']+$child_count_down['add_and_subtract']+$child_count_down['wax_and_wane'];
                //print_r($project_use_time);
            }else{
                $project_use_time = $row['project_use_time'] > 0 ? $row['project_use_time'] : $this->match_count_down;
            }
            $match_more = $row['match_more'] > 0 ? $row['match_more'] : $this->match_more;
            $project_time_interval = $row['project_time_interval'] > 0 ? $row['project_time_interval'] : $this->match_subject_interval;

            if(strtotime($row['project_start_time']) > 0){
                $end_time = strtotime($row['project_start_time']) + ($project_use_time*$match_more + ($match_more-1)*$project_time_interval)*60;
                $rows[$k]['project_end_time'] = $row['project_end_time'] = date('Y-m-d H:i:s',$end_time);

            }else{

                $project_end_time = !empty($rows[$k-1]['project_end_time']) ? strtotime($rows[$k-1]['project_end_time']) + $this->match_project_interval*60 : strtotime($this->match_start_time);
                $end_time = $project_end_time + ($project_use_time*$match_more + ($match_more-1)*$project_time_interval)*60;
                $rows[$k]['project_end_time'] = $row['project_end_time'] = date('Y-m-d H:i:s',$end_time);
                $rows[$k]['project_start_time'] = $row['project_start_time'] = date('Y-m-d H:i:s',$project_end_time);
            }
            $this->project_key_array[$row['match_project_id']] = $row;
            //leo_dump($rows[$k]['project_start_time'].'-----'.$rows[$k]['project_end_time']);

        }

        //判断当前时间应该进行哪个项目
        /*$match_start_time = strtotime($rows[$k]['project_start_time']);
        switch ($match_start_time){
            case '':
                break;
        }*/
        //print_r($rows);die;
        $this->project_order_array = $rows;
        $this->match_project_total = count($rows);
        $this->project_id_array = array_column($rows,'match_project_id');
    }

    /*
     * 根据比赛别名获取链接方法
     */
    public function get_match_action($alias){
        switch ($alias){

            case 'szzb':    //数字争霸
                $action = 'numberBattleReady';
                break;
            case 'pkjl':    //扑克接力
                $action = 'pokerRelayReady';
                break;
            case 'zxss':    //正向速算
                $action = 'fastCalculation';
                break;
            case 'nxss':    //逆向速算
                $action = 'fastReverse';
                break;
            case 'wzsd':     //文章速读
                $action = 'readingReady';
                break;
            case 'kysm':    //快眼扫描
                $action = 'fastScan';
                break;
            default:
                $action = 'numberBattleReady';
                break;
        }
        return $action;
    }



    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
        if(ACTION=='info'){//比赛详情页
            wp_register_style( 'my-student-matchDetail', student_css_url.'matchDetail.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchDetail' );
            
        }


        //比赛初始页面
        if(ACTION == 'initialMatch'){

            wp_register_script( 'student-Hammer',student_js_url.'Mobile/Hammer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-Hammer' );

            if($this->project_alias=='nxss'){//逆向速算初始页
                wp_register_style( 'my-student-fastReverse', student_css_url.'matching-fastReverse.css',array('my-student') );
                wp_enqueue_style( 'my-student-fastReverse' );

            }

            if($this->project_alias=='zxss'){//正向速算初始页
                wp_register_style( 'my-student-fastCalculation', student_css_url.'matching-fastCalculation.css',array('my-student') );
                wp_enqueue_style( 'my-student-fastCalculation' );

            }

            if($this->project_alias=='wzsd'){//文章速读初始页
                wp_register_style( 'my-student-matchDetail', student_css_url.'ready-reading.css',array('my-student') );
                wp_enqueue_style( 'my-student-matchDetail' );

            }

            if($this->project_alias=='kysm'){//快眼扫描比赛页
                wp_register_style( 'my-student-fastScan', student_css_url.'matching-fastScan.css',array('my-student') );
                wp_enqueue_style( 'my-student-fastScan' );

            }

            if($this->project_alias=='szzb'){//进入数字争霸准备页面
                wp_register_style( 'my-student-numberBattleReady', student_css_url.'ready-numberBattle.css',array('my-student') );
                wp_enqueue_style( 'my-student-numberBattleReady' );
            }

            if($this->project_alias=='pkjl'){//进入扑克接力准备页面
                wp_register_style( 'my-student-pokerRelayReady', student_css_url.'ready-pokerRelay.css',array('my-student') );
                wp_enqueue_style( 'my-student-pokerRelayReady' );
            }
        }

        //比赛记忆后答题页面
        if(ACTION == 'answerMatch'){
            wp_register_script( 'student-Hammer',student_js_url.'Mobile/Hammer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-Hammer' );

            if($this->project_alias=='wzsd'){//文章速读
                wp_register_style( 'my-student-matchDetail', student_css_url.'matching-reading.css',array('my-student') );
                wp_enqueue_style( 'my-student-matchDetail' );
            }

            if($this->project_alias=='szzb'){//数字争霸
                wp_register_style( 'my-student-matching', student_css_url.'matching-numberBattle.css',array('my-student') );
                wp_enqueue_style( 'my-student-matching' );
            }

            if($this->project_alias=='pkjl'){//扑克接力
                wp_register_style( 'my-student-pokerRelay', student_css_url.'matching-pokerRelay.css',array('my-student') );
                wp_enqueue_style( 'my-student-pokerRelay' );
            }
        }

        //答案记录页面
        if(ACTION == 'answerLog'){

            if($this->project_alias=='nxss'){//逆向速算成绩页
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }

            if($this->project_alias=='zxss'){//正向速算成绩页
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }

            if($this->project_alias=='wzsd'){//文章速读成绩页
                wp_register_style( 'my-student-matchDetail', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-matchDetail' );

            }

            if($this->project_alias=='kysm'){//快眼扫描成绩页
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }

            if($this->project_alias=='szzb'){//数字争霸本轮答题记录
                wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
                wp_enqueue_style( 'my-student-subject' );
            }
            if($this->project_alias=='pkjl'){//扑克接力本轮答题记录
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
            wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-cookie' );
            wp_register_style( 'my-student-match', student_css_url.'match.css',array('my-student') );
            wp_enqueue_style( 'my-student-match' );
        }
    }
}