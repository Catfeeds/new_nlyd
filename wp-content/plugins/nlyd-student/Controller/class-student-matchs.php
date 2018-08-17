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
    public $match;
    public $default_match;

    public $match_title;       //比赛题目
    public $match_alias;       //比赛别名
    public $current_more;       //比赛轮数
    public $default_use_time;       //项目时间
    public $default_count_down;       //比赛剩余时间
    public $default_match_more;       //比赛轮数
    public $default_subject_interval;       //比赛每轮间隔
    public $default_project_interval;       //比赛项目间隔
    public $default_str_length;    //初始字符长度
    public $child_count_down;    //子项倒计时

    public $ajaxControll;
    public $default_category;       //比赛项目
    public $default_project_more;   //项目轮数
    public $default_post_parent;   //当前比赛项目类别id

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

        if(isset($_GET['match_id'])){
            $this->match = $this->get_match_info($_GET['match_id']);
            //leo_dump($this->match);
            if(empty($this->match)){
                $this->get_404('参数错误');
                return;
            }
            //leo_dump($this->match);
        }
        if(isset($_GET['match_id'])){
            global $wpdb;
            //判断比赛进行顺序
            $sql = "select match_category_order from {$wpdb->prefix}match_meta where match_id = {$_GET['match_id']} ";
            $match_category_order = $wpdb->get_var($sql);
            if(!empty($match_category_order)){
                $match_category_order = unserialize($match_category_order);
                asort($match_category_order);
                $category_key = array_keys($match_category_order);
                $match_category = arr2str($category_key);
                $sql1 = "select a.ID,a.post_title,a.post_parent,a.menu_order, b.match_project_id,b.project_use_time,
                        b.match_more,b.project_start_time,b.project_time_interval,b.str_bit,c.meta_value as project_alias
                        from {$wpdb->prefix}posts a 
                        left join {$wpdb->prefix}match_project b on a.ID = b.match_project_id and post_id = {$_GET['match_id']}
                        left join {$wpdb->prefix}postmeta c on a.ID = c.post_id and meta_key = 'project_alias'
                        where a.post_parent in ({$match_category}) order by a.menu_order asc 
                        ";
                //print_r($sql1);
                $rows = $wpdb->get_results($sql1,ARRAY_A);
                if(!empty($rows)){
                    $category = array();
                    foreach ($category_key as $v){
                        $category[$v] = array();
                        foreach ($rows as $key =>$val){

                            if(isset($_GET['project_id']) && $_GET['project_id'] == $val['match_project_id']) $this->default_post_parent = $val['post_parent'];
                            if($val['post_parent'] == $v) $category[$v][] = $val;
                        }
                    }
                    //print_r($category);
                    foreach ($category as $value){
                        foreach ($value as $y){
                            $this->default_category[] = $y;
                            $this->default_match[] = $y['project_alias'];
                        }
                    }
                }

            }
        }

        if($action == 'matching'){


            if(!empty($match_category_order)){
                $this->default_project_more = 1;

                if(!empty($rows)){

                    $first_match = $this->default_category[0];
                    switch ($first_match['project_alias']){

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

                    //leo_dump($category);
                    wp_redirect(home_url('/matchs/'.$action.'/match_id/'.$_GET['match_id']).'/project_id/'.$first_match['ID']);

                }

            }
        }


        if(isset($_GET['match_id']) && isset($_GET['project_id'])){

            $this->get_match_default($_GET['match_id'],$_GET['project_id']);
        }
        //添加短标签
        add_shortcode('match-home',array($this,$action));
    }

    /**
     * 列表
     */
    public function index(){

        // $a = new TwentyFour();
        //var_dump($a->calculate(array(2,2,12,9)));

        global $wpdb;

        $row = $wpdb->get_row('SELECT ID FROM '.$wpdb->prefix.'posts WHERE post_status="publish" AND post_type="match"');

        $view = student_view_path.'matchList.php';
        load_view_template($view,array('row' => $row));
    }

    /**
     * 获取比赛详情
     */
    public function get_match_info($match_id){

        global $wpdb,$current_user;

        //获取比赛详情
        $sql = "select a.ID,a.post_title,a.post_content,b.match_start_time,b.match_use_time,b.match_more,b.match_subject_interval,b.match_address,b.match_cost,
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
        $view = student_view_path.'matchDetail.php';
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
        $view = student_view_path.'match-Rule.php';
        load_view_template($view,$data);
    }
    /**
     * 比赛等待倒计时页面
     */
     public function matchWaitting(){

         global $wpdb,$current_user;
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
         $first_match = $this->default_category[0];
         switch ($first_match['project_alias']){

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
         $data['match_url'] = home_url('/account/matchList/'.$action.'/match_id/'.$row['match_id']).'/project_id/'.$first_match['ID'];
         $data['count_down'] = strtotime($this->match['match_start_time'])-time();
         $data['match_title'] = $first_match['post_title'];
         //print_r($first_match);

        $view = student_view_path.'match-waitting.php';
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

        $view = student_view_path.'confirm.php';
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
         $default_category = empty($this->default_category) ? '' : $this->default_category;

         $data = array('list'=>$list,'my_ranking'=>$my_ranking,'match_title'=>$title,'match_category'=>$match_category,'count'=>count($rows) > 10 ? count($rows) : 0,'default_category'=>$default_category);
         //print_r($data);
         $view = student_view_path.'record.php';
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
             'answer_url'=>home_url('/matchs/'.$action.'/match_id/'.$_GET['match_id'].'/project_id/'.$_GET['project_id']),
         );

         //print_r($data);
         $view = student_view_path.'singleRecord.php';
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
        $view = student_view_path.'subject-numberBattle.php';
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
         $view = student_view_path.'subject-pokerRelay.php';
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

        $view = student_view_path.'ready-numberBattle.php';

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

        $view = student_view_path.'ready-pokerRelay.php';
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
        $view = student_view_path.'matching-numberBattle.php';
        load_view_template($view,$data);
    }
    /**
     * 扑克接力
     */
     public function pokerRelay (){

         if(empty($_GET['match_id']) || empty($_GET['project_id'])){
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

         $data = array(
             'list'=>$list,
             'match_title'=>$this->match_title,
             'post_title'=>$this->match['post_title'],
             'count_down'=>$this->default_count_down,
             'list_keys'=>array_keys($list),
         );

         $view = student_view_path.'matching-pokerRelay.php';
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
     * 获取比赛初始
     * @param $match_id 比赛id
     * @param $project_id 正比赛项目id
     */
    public function get_match_default($match_id,$project_id){
        global $wpdb,$current_user;
        //leo_dump($this->match);
        //获取比赛项目设置
        $sql = "select a.*,b.post_title from {$wpdb->prefix}match_project a left join {$wpdb->prefix}posts b on a.match_project_id = b.ID where a.post_id = {$match_id} and a.match_project_id = {$project_id} ";
        $match_project = $wpdb->get_row($sql,ARRAY_A);
        //leo_dump($match_project);
        //leo_dump($sql);
        //比赛项目名
        $this->match_title = $match_project['post_title'];
        //leo_dump($match_project);
        //获取比赛别名
        $this->match_alias = get_post_meta($project_id,'project_alias')[0];
        if ((empty($this->match_alias))){
            $this->get_404('请先联系管理员设置比赛别名');
            return;
        }
        //获取项目默认设置
        $sql1 = "select a.*,b.meta_value as project_alias from {$wpdb->prefix}match_meta a left join {$wpdb->prefix}postmeta b on a.match_id = b.post_id and meta_key = 'project_alias' where a.match_id = {$project_id} ";
        $project = $wpdb->get_row($sql1,ARRAY_A);
        //leo_dump($project);

        if(in_array($project['project_alias'],array('szzb','kysm'))){

            //获取初始字符长度
            update_option('default_str_bit',100);
            if($match_project['str_bit'] > 0){
                $str_length = $match_project['str_bit'] ;
            }elseif ($project['str_bit'] > 0){
                $str_length = $project['str_bit'] ;
            }else{
                $str_length =  get_option('default_str_bit');
            }

            $this->default_str_length = $str_length;
        }

        //单项比赛用时
        update_option('default_use_time',20);
        if($match_project['project_use_time'] > 0){
            $use_time = $match_project['project_use_time'] ;
        }elseif ($this->match['match_use_time'] > 0){
            $use_time = $this->match['match_use_time'] ;
        }elseif ($project['match_use_time'] > 0){
            $use_time = $project['match_use_time'] ;
        }else{
            $use_time =  get_option('default_use_time');
        }

        $count_down = $this->default_use_time = $use_time * 60;

        //当前比赛轮数
        $this->current_more = !empty($_GET['match_more']) ? $_GET['match_more'] : 1;

        //进入比赛redis保存结束时间

        if(in_array(ACTION,array('numberBattleReady','pokerRelayReady','fastCalculation','fastReverse','fastScan','readingReady'))){
            //$redis->set('end_time'.$current_user->ID,'');

            $current_match_more = $this->redis->get('current_match_more'.$current_user->ID);

            if($current_match_more != $this->match_alias.'_'.$this->current_more){

                //保存用户当前进行的比赛项目与比赛轮数
                $this->redis->setex('current_match_more'.$current_user->ID,$count_down,$this->match_alias.'_'.$this->current_more);
                $this->redis->setex('end_time'.$current_user->ID,$count_down,time()+$count_down);
            }

            /*if(empty($this->redis->get('end_time'.$current_user->ID))){
                $this->redis->setex('end_time'.$current_user->ID,$count_down,time()+$count_down);
            }*/
        }
        if(empty($this->redis->get('end_time'.$current_user->ID))){

            $this->default_count_down = 0;
        }else{

            $this->default_count_down = $this->redis->get('end_time'.$current_user->ID)-time();
        }

        //子项比赛用时
        if(in_array($project['project_alias'],array('kysm','zxss'))){

            if($project['project_alias'] == 'zxss'){

                update_option('default_child_count_down',180);
                $child_count_down = get_post_meta($project_id,'child_count_down')[0];
                $child_count_down['even_add'] = !empty($child_count_down['even_add']) ? $child_count_down['even_add']  : 180 ;
                $child_count_down['add_and_subtract'] = !empty($child_count_down['add_and_subtract']) ? $child_count_down['add_and_subtract']  : 180 ;
                $child_count_down['child_count_down'] = !empty($child_count_down['child_count_down']) ? $child_count_down['child_count_down']  : 180;
            }else{
                update_option('default_child_count_down',5);
                if($match_project['child_count_down'] > 0){
                    $child_count_down = $match_project['child_count_down'] ;
                }elseif ($project['child_count_down'] > 0){
                    $child_count_down = $project['child_count_down'] ;
                }else{
                    $child_count_down =  get_option('default_child_count_down');
                }
            }
            $this->child_count_down = $child_count_down;

        }

        //比赛轮数
        update_option('default_match_more',1);
        if($match_project['match_more'] > 0){
            $match_more = $match_project['match_more'] ;
        }elseif ($this->match['match_more'] > 0){
            $match_more = $this->match['match_more'] ;
        }elseif ($project['match_more'] > 0){
            $match_more = $project['match_more'] ;
        }else{
            $match_more =  get_option('default_match_more');
        }
        $this->default_match_more = $match_more;

        //每轮间隔
        update_option('default_match_subject_interval',1);
        if($match_project['project_time_interval'] > 0){
            $subject_interval = $match_project['project_time_interval'] ;
        }elseif ($this->match['match_subject_interval'] > 0){
            $subject_interval = $this->match['match_subject_interval'] ;
        }elseif ($project['match_subject_interval'] > 0){
            $subject_interval = $project['match_subject_interval'] ;
        }else{
            $subject_interval =  get_option('default_match_subject_interval');
        }
        $this->default_subject_interval = $subject_interval * 60;

        //判断当前是第几轮
        //获取每轮用时 (比赛用时-(轮数-1)*间隔)/轮数
        $more = ($count_down-($match_more-1)*($subject_interval*60))/$match_more;
        //leo_dump($more);

        //项目间隔时间
        update_option('default_match_project_interval',5);
        if($project['match_project_interval'] > 0){
            $project_interval = $project['match_project_interval'] ;
        }else{
            $project_interval =  get_option('match_project_interval');
        }

        $this->default_project_interval = $project_interval * 60;

        //leo_dump($this->default_count_down);
    }

    //判断是否报名
    public function get_match_order($user_id,$match_id){
        global $wpdb;
        $sql = "select id,pay_status from {$wpdb->prefix}order where user_id = {$user_id} and match_id = {$match_id} ";
        //print_r($sql);
        return $wpdb->get_row($sql);
    }
    /**
     * 文章速读记忆页
     */
     public function readingReady(){

         if(empty($_GET['match_id']) || empty($_GET['project_id'])){
             $this->get_404('参数错误');
             return;
         }

         /*if($this->default_count_down <= 0 ){
             $this->get_404('该轮比赛结束');
             return;
         }*/

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
         //$this->redis->del('wzsd_question'.$current_user->ID);
         if(!empty($this->redis->get('wzsd_question'.$current_user->ID))){

             $question = json_decode($this->redis->get('wzsd_question'.$current_user->ID));
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
             $this->redis->setex('wzsd_question'.$current_user->ID,$this->default_count_down,json_encode($question));

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
                 //var_dump($questions_answer);
                 //die;
                 $a = $wpdb->insert($wpdb->prefix.'match_questions',$insert_data);
                 //leo_dump($a);

             }else{

                 //判断状态
                 if(!empty($row['answer_status'])){
                     if($row['answer_status'] == 1){
                         $messahe = '答案已提交';
                     }
                     if($row['answer_status'] == -1){
                         $messahe = '记忆已完成';
                     }
                     $this->get_404($messahe);
                     return;
                 }

             }
         }
         //print_r($question);
         $data = array(
             'questions'=>$question,
             'match_title'=>$this->match_title,
             'match_more_cn'=>chinanum($match_more),
             'count_down'=>$this->default_count_down,
             'post_title'=>$this->match['post_title'],
         );

        $view = student_view_path.'ready-reading.php';
        load_view_template($view,$data);
    }
    /**
     * 文章速读比赛页
     */
     public function reading(){

         if(empty($_GET['match_id']) || empty($_GET['project_id'])){
             $this->get_404('参数错误');
             return;
         }

         /*if($this->default_count_down <= 0 ){
             $this->get_404('该轮比赛结束');
             return;
         }*/

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

         //获取文章速读考题
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


         $data = array(
             'match_title'=>$this->match_title,
             'match_more_cn'=>chinanum($match_more),
             'count_down'=>$this->default_count_down,
             'post_title'=>$this->match['post_title'],
             'match_questions'=>empty($row['match_questions']) ? '' : json_decode($row['match_questions'],true),
             'questions_answer' =>empty($row['questions_answer']) ? '' : json_decode($row['questions_answer'],true),
         );
         //print_r($data);
        $view = student_view_path.'matching-reading.php';
        load_view_template($view,$data);
    }
    /**
     * 文章速读成绩页
     */
     public function subjectReading(){
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
         $match_questions = json_decode($row['match_questions'],true);
         $questions_answer = json_decode($row['questions_answer'],true);
         $my_answer = !empty($row['my_answer']) ? json_decode($row['my_answer'],true) : array();
         $len = count($questions_answer);
         $success_len = 0;

         foreach ($questions_answer as $k=>$val){
             $arr = array();
             $answerArr = array();
             foreach ($val['problem_answer'] as $key => $v){
                 if($v == 1){
                     $arr[] = $key;
                     $answerArr[] = $key;
                     //$questions_answer[$k]['problem_answer'][] = $key;
                 }
                 /*if($v == 1){
                     if($key == $my_answer[$k]['problem_answer']) ++$success_len;
                     $questions_answer[$k]['problem_answer'] = $key;
                 }*/
             }
             $questions_answer[$k]['problem_answer'] = $answerArr;
             if(isset($my_answer[$k])){
                 if(arr2str($arr) == arr2str($my_answer[$k])) ++$success_len;
             }
         }

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
             'match_questions'=>$match_questions,
             'questions_answer'=>$questions_answer,
             'my_answer'=>$my_answer,
             'my_score'=>$row['my_score'],
             'match_title'=>$row['post_title'],
             'next_more_down'=>'',
             'next_project_down'=>'',
             'record_url'=>home_url('matchs/record/type/project/match_id/'.$_GET['match_id'].'/project_id/'.$_GET['project_id'].'/match_more/'.$_GET['match_more']),
         );

         //判断是否有新的比赛轮次或者新的项目
         if($_GET['match_more'] < $this->default_match_more){

             $data['next_more_down'] = $this->default_subject_interval + $this->default_count_down;
             $match_more = (int)$_GET['match_more']+1;
             $data['next_more_url'] = home_url('/matchs/readingReady/match_id/'.$_GET['match_id'].'/project_id/'.$_GET['project_id'].'/match_more/'.$match_more);
         }else{
             $next = array_search($this->match_alias,$this->default_match)+1;

             if(!empty($this->default_category[$next])){
                 $match_category = $this->default_category[$next];
                 $data['next_project_down'] = $this->default_project_interval + $this->default_count_down;

                 switch ($match_category['project_alias']){

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
                 $data['next_project_url'] = home_url('/matchs/'.$action.'/match_id/'.$_GET['match_id'].'/project_id/'.$match_category['ID'].'/match_more/1');
             }
         }
        $view = student_view_path.'subject-reading.php';
        load_view_template($view,$data);
    }
    /**
     *  快眼扫描比赛页
     */
     public function fastScan(){
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

         $data = array(
             'match_title'=>$this->match_title,
             'match_more_cn'=>chinanum($match_more),
             'count_down'=>$this->default_count_down,
             'child_count_down'=>$this->child_count_down,
             'str_length'=>$this->default_str_length,
             'post_title'=>$this->match['post_title'],
         );
        //print_r($data);
        $view = student_view_path.'matching-fastScan.php';
        load_view_template($view,$data);
    }
    /**
     *  快眼扫描成绩页
     */
     public function subjectfastScan(){

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
         $match_questions = json_decode($row['match_questions']);
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
             $data['next_more_url'] = home_url('/matchs/fastScan/match_id/'.$_GET['match_id'].'/project_id/'.$_GET['project_id'].'/match_more/'.$match_more);
         }else{
             $next = array_search($this->match_alias,$this->default_match)+1;

             if(!empty($this->default_category[$next])){
                 $match_category = $this->default_category[$next];
                 $data['next_project_down'] = $this->default_project_interval + $this->default_count_down;

                 switch ($match_category['project_alias']){

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
                 $data['next_project_url'] = home_url('/matchs/'.$action.'/match_id/'.$_GET['match_id'].'/project_id/'.$match_category['ID'].'/match_more/1');
             }
         }

         $view = student_view_path.'subject-fastScan.php';
         load_view_template($view,$data);
    }
    /**
     *  正向速算比赛页
     */
     public function fastCalculation(){
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

             $insert_data = array(
                 'user_id'=>$current_user->ID,
                 'match_id'=>$_GET['match_id'],
                 'project_id'=>$_GET['project_id'],
                 'match_more'=>$match_more,
                 'match_questions'=>'',
                 'questions_answer'=>'',
                 'created_time'=>date('Y-m-d H:i:s',time()),
             );
             //print_r($insert_data);die;
             $a = $wpdb->insert($wpdb->prefix.'match_questions',$insert_data);
             //leo_dump($a);

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
         $data = array(
             'match_title'=>$this->match_title,
             'match_more_cn'=>chinanum($_GET['match_more']),
             'count_down'=>$this->default_count_down,
             'post_title'=>$this->match['post_title'],
             'child_count_down' => $this->child_count_down,
         );

        $view = student_view_path.'matching-fastCalculation.php';
        load_view_template($view,$data);
    }
    /**
     *  正向速算成绩页
     */
     public function subjectFastCalculation(){

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
         $match_questions = json_decode($row['match_questions']);
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
             'match_questions'=>$match_questions,
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
             $data['next_more_url'] = home_url('/matchs/fastCalculation/match_id/'.$_GET['match_id'].'/project_id/'.$_GET['project_id'].'/match_more/'.$match_more);
         }else{
             $next = array_search($this->match_alias,$this->default_match)+1;

             if(!empty($this->default_category[$next])){
                 $match_category = $this->default_category[$next];
                 $data['next_project_down'] = $this->default_project_interval + $this->default_count_down;

                 switch ($match_category['project_alias']){

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
                 $data['next_project_url'] = home_url('/matchs/'.$action.'/match_id/'.$_GET['match_id'].'/project_id/'.$match_category['ID'].'/match_more/1');
             }
         }

        $view = student_view_path.'subject-fastCalculation.php';
        load_view_template($view,$data);
    }
    /**
     *  逆向速算比赛页
     */
     public function fastReverse(){
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

             $insert_data = array(
                 'user_id'=>$current_user->ID,
                 'match_id'=>$_GET['match_id'],
                 'project_id'=>$_GET['project_id'],
                 'match_more'=>$match_more,
                 'match_questions'=>'',
                 'questions_answer'=>'',
                 'created_time'=>date('Y-m-d H:i:s',time()),
             );
             //print_r($insert_data);die;
             $a = $wpdb->insert($wpdb->prefix.'match_questions',$insert_data);
             //leo_dump($a);

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
         $data = array(
             'match_title'=>$this->match_title,
             'match_more_cn'=>chinanum($_GET['match_more']),
             'count_down'=>$this->default_count_down,
             'post_title'=>$this->match['post_title'],
         );

        $view = student_view_path.'matching-fastReverse.php';
        load_view_template($view,$data);
    }
    /**
     *  逆向速算成绩页
     */
     public function subjectFastReverse(){


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
         $match_questions = json_decode($row['match_questions']);
         $questions_answer = json_decode($row['questions_answer']);
         $my_answer = !empty($row['my_answer']) ? json_decode($row['my_answer']) : array();
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
             'accuracy'=>$success_len > 0 ? round($success_len/$len,2)*100 : 0,
             'ranking'=>$ranking,
             'match_questions'=>$match_questions,
             'questions_answer'=>$questions_answer,
             'my_answer'=>$my_answer,
             'my_score'=>$row['my_score'],
             'match_title'=>$row['post_title'],
             'error_arr'=>!empty($error_arr) ? array_keys($error_arr) : array(),
             'next_more_down'=>'',
             'next_project_down'=>'',
             'record_url'=>home_url('matchs/record/type/project/match_id/'.$_GET['match_id'].'/project_id/'.$_GET['project_id'].'/match_more/'.$_GET['match_more']),
         );

         //判断是否有新的比赛轮次或者新的项目
         if($_GET['match_more'] < $this->default_match_more){

             $data['next_more_down'] = $this->default_subject_interval + $this->default_count_down;
             $match_more = (int)$_GET['match_more']+1;
             $data['next_more_url'] = home_url('/matchs/fastReverse/match_id/'.$_GET['match_id'].'/project_id/'.$_GET['project_id'].'/match_more/'.$match_more);
         }else{
             $next = array_search($this->match_alias,$this->default_match)+1;

             if(!empty($this->default_category[$next])){
                 $match_category = $this->default_category[$next];
                 $data['next_project_down'] = $this->default_project_interval + $this->default_count_down;

                 switch ($match_category['project_alias']){

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
                 $data['next_project_url'] = home_url('/matchs/'.$action.'/match_id/'.$_GET['match_id'].'/project_id/'.$match_category['ID'].'/match_more/1');
             }
         }

        $view = student_view_path.'subject-fastReverse.php';
        load_view_template($view,$data);
    }

    /**
     * 开始比赛
     */
    public function startMatch(){

        $view = student_view_path.'match.php';
        load_view_template($view);
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
        if(ACTION=='readingReady'){//文章速读记忆页
            wp_register_style( 'my-student-matchDetail', student_css_url.'ready-reading.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchDetail' );
            
        }
        if(ACTION=='reading'){//文章速读比赛页
            wp_register_script( 'student-Hammer',student_js_url.'Mobile/Hammer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-Hammer' );
            wp_register_style( 'my-student-matchDetail', student_css_url.'matching-reading.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchDetail' );
            
        }
        if(ACTION=='subjectReading'){//文章速读成绩页
            wp_register_style( 'my-student-matchDetail', student_css_url.'subject.css',array('my-student') );
            wp_enqueue_style( 'my-student-matchDetail' );
            
        }
        if(ACTION=='fastScan'){//快眼扫描比赛页
            wp_register_script( 'student-Hammer',student_js_url.'Mobile/Hammer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-Hammer' );
            wp_register_style( 'my-student-fastScan', student_css_url.'matching-fastScan.css',array('my-student') );
            wp_enqueue_style( 'my-student-fastScan' );

        }
        if(ACTION=='subjectfastScan'){//快眼扫描成绩页
            wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
            wp_enqueue_style( 'my-student-subject' );
        }
        if(ACTION=='fastCalculation'){//正向速算比赛页
            wp_register_script( 'student-Hammer',student_js_url.'Mobile/Hammer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-Hammer' );
            wp_register_style( 'my-student-fastCalculation', student_css_url.'matching-fastCalculation.css',array('my-student') );
            wp_enqueue_style( 'my-student-fastCalculation' );
            
        }
        if(ACTION=='subjectFastCalculation'){//正向速算成绩页
            wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
            wp_enqueue_style( 'my-student-subject' );
        }
        if(ACTION=='fastReverse'){//逆向速算比赛页
            wp_register_script( 'student-Hammer',student_js_url.'Mobile/Hammer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-Hammer' );
            wp_register_style( 'my-student-fastReverse', student_css_url.'matching-fastReverse.css',array('my-student') );
            wp_enqueue_style( 'my-student-fastReverse' );
            
        }
        if(ACTION=='subjectFastReverse'){//逆向速算成绩页
            wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
            wp_enqueue_style( 'my-student-subject' );
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
        if(ACTION=='subjectNumberBattle'){//数字争霸本轮答题记录
            wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
            wp_enqueue_style( 'my-student-subject' );
        }
        if(ACTION=='subjectPokerRelay'){//扑克接力本轮答题记录
            wp_register_style( 'my-student-subject', student_css_url.'subject.css',array('my-student') );
            wp_enqueue_style( 'my-student-subject' );
        }
        if(ACTION=='numberBattleReady'){//进入数字争霸准备页面
            wp_register_script( 'student-Hammer',student_js_url.'Mobile/Hammer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-Hammer' );
            
            wp_register_style( 'my-student-numberBattleReady', student_css_url.'ready-numberBattle.css',array('my-student') );
            wp_enqueue_style( 'my-student-numberBattleReady' );
        }
        if(ACTION=='numberBattle'){//数字争霸
            wp_register_script( 'student-Hammer',student_js_url.'Mobile/Hammer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-Hammer' );
            
            wp_register_style( 'my-student-matching', student_css_url.'matching-numberBattle.css',array('my-student') );
            wp_enqueue_style( 'my-student-matching' );
        }
        if(ACTION=='pokerRelayReady'){//进入扑克接力准备页面
            wp_register_script( 'student-Hammer',student_js_url.'Mobile/Hammer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-Hammer' );
            
            wp_register_style( 'my-student-pokerRelayReady', student_css_url.'ready-pokerRelay.css',array('my-student') );
            wp_enqueue_style( 'my-student-pokerRelayReady' );
        }
        if(ACTION=='pokerRelay'){//扑克接力
            wp_register_script( 'student-Hammer',student_js_url.'Mobile/Hammer.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-Hammer' );
            
            wp_register_style( 'my-student-pokerRelay', student_css_url.'matching-pokerRelay.css',array('my-student') );
            wp_enqueue_style( 'my-student-pokerRelay' );
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