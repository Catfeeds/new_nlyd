<?php
/**
 * Ajax操作类
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/6/22
 * Time: 14:10
 */
use library\AliSms;
use library\TwentyFour;
class Match_Ajax
{

    public $redis;
    function __construct() {

        //如果有提交并且提交的内容中有规定的提交数据
        $data = $_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST : $_GET;
        if(empty($data['action']) ) return;
        $action = $data['action'];

        //判斷方法是否存在
        if(!method_exists($this,$action)) return;

        add_action( 'wp_ajax_'.$action,array($this, $action) );
        add_action( 'wp_ajax_nopriv_'.$action,  array($this,$action) );
    }

    /**
     * 设置发布机构
     */
    public function admin_get_zone_list(){

        global $wpdb,$current_user;
        if(!empty($_GET['term'])){
            $where = " and  b.zone_name like '%{$_GET['term']}%' ";
        }
        $sql = "select a.user_id as id,if(a.zone_match_type=1,'战队精英赛','城市赛') as match_type, a.zone_city,a.zone_name from {$wpdb->prefix}zone_meta a 
                left join {$wpdb->prefix}users b on a.user_id = b.ID
                where a.user_id > 0 {$where}
                limit 10;
                ";
        //print_r($sql);
        $zones = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($zones)){
            $arr = array();
            foreach ($zones as $key => $value) {
                $city = !empty($value['zone_city']) ? '（'.$value['zone_city'].'）' : '';
                $arr[$key]['text'] = $value['zone_name'].$city.$value['match_type'].'组委会';
                $arr[$key]['id'] = $value['id'];
            }
        }
        wp_send_json_success($arr);
        //print_r($arr);die;
    }

    /**
     * 一键生成比赛项目
     */
    public function found_match(){

        global $wpdb,$current_user;
        $id = $wpdb->get_var("select id from {$wpdb->prefix}match_meta_new where match_id = {$_POST['match_id']} ");
        if(empty($id)){
            wp_send_json_error('请先进行比赛发布,再进行项目生成');
        }
        require_once LIBRARY_PATH.'Vendor/PHPExcel/Classes/PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $PHPReader = new PHPExcel_Reader_Excel2007();
        $file_path = leo_match_path.'/upload/match_template/nlgj_match_template.xlsx';
        //为了可以读取所有版本Excel文件
        if(!$PHPReader->canRead($file_path))
        {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($file_path))
            {
                echo '未发现Excel文件！';
                return;
            }
        }

        //不需要读取整个Excel文件而获取所有工作表数组的函数，感觉这个函数很有用，找了半天才找到
        $sheetNames  = $PHPReader->listWorksheetNames($file_path);
        //读取Excel文件
        $PHPExcel = $PHPReader->load($file_path);
        //选择第一个工作表
        $currentSheet = $PHPExcel->getSheet(0);
        //取得一共有多少列
        $allColumn = $currentSheet->getHighestColumn();
        //取得一共有多少行
        $allRow = $currentSheet->getHighestRow();

        //循环读取数据，并且保存数据
        $str = '';
        $match_project_id = array();
        $match_start_day = $_POST['match_start_time'];
        $match_start_time = '';
        //('1473', '197', '1', '2018-12-07 10:32:00', '2018-12-07 10:52:00', '20', '1', '2018-12-07 16:23:27')
        for($currentRow = 2;$currentRow<=$allRow;$currentRow++)
        {
            $a = $currentSheet->getCell('A'.$currentRow)->getValue();
            $b = $currentSheet->getCell('B'.$currentRow)->getValue();
            $c = $currentSheet->getCell('C'.$currentRow)->getValue();
            $d = $currentSheet->getCell('D'.$currentRow)->getValue();
            $e = $currentSheet->getCell('E'.$currentRow)->getValue();
            if(!empty($a) && !empty($b) && !empty($c) && !empty($d) && !empty($e)){
                $project_id = $wpdb->get_var("select post_id from {$wpdb->prefix}postmeta where meta_key = 'project_alias' and meta_value='{$a}' ");
                $start_time = $match_start_day.$c;
                $end_time = $match_start_day.$d;


                $created_time = get_time('mysql');
                $str .= "('{$_POST['match_id']}','{$project_id}','{$b}','{$start_time}','{$end_time}','{$e}','{$current_user->ID}','{$created_time}'),";
                //print_r($str.'</br>');
                if($currentRow == 2){
                    $match_start_time = $start_time;
                }
                if($currentRow == $allRow){
                    $str = rtrim($str, ',');
                    $match_end_time = $end_time;
                }
                $match_project_id[] = $project_id;

            }else{
                continue;
            }
        }
        /*print_r($str);
        die;*/

        $wpdb->delete($wpdb->prefix.'match_project_more',array('match_id'=>$_POST['match_id']));

        $sql = "INSERT INTO `{$wpdb->prefix}match_project_more` 
                (`match_id`, `project_id`, `more`, `start_time`, `end_time`, `use_time`, `created_id`, `created_time`) 
                VALUES ".$str;

        $wpdb->query('START TRANSACTION');
        $result = $wpdb->query($sql);

        $update = array(
            'entry_end_time'=>date_i18n('Y-m-d H:i:s',strtotime('-10 minute',strtotime($match_start_time))),
            'match_start_time' => $match_start_time,
            'match_end_time' => $match_end_time,
            'match_project_id' => arr2str(array_unique($match_project_id)),
        );

        $result1 = $wpdb->update($wpdb->prefix.'match_meta_new',$update,array('match_id'=>$_POST['match_id']));
        if($result && $result1){
            $wpdb->query('COMMIT');
            wp_send_json_success('生成成功');
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error('生成失败');
        }
    }

    /**
     * 手动换座位
     *
     */
    public function save_seating(){
        global $wpdb;

        $order = $wpdb->get_row("select  * from {$wpdb->prefix}order where id = {$_POST['orderid']}");
        if(empty($order)) wp_send_json_error('未匹配到订单信息');

        //获取总人数
        $total = $wpdb->get_var("select  count(*) from {$wpdb->prefix}order where match_id = {$order->match_id} and pay_status in (2,3,4) ");

        if($_POST['new_seat_number'] > $total || $_POST['new_seat_number'] < 1) wp_send_json_error('座位号范围:1~'.$total);
        $old_seat_number = $order->seat_number;

        $wpdb->query('START TRANSACTION');

        $b = $wpdb->update($wpdb->prefix.'order',array('seat_number'=>$order->seat_number),array('match_id'=>$order->match_id,'seat_number'=>$_POST['new_seat_number']));
        $a = $wpdb->update($wpdb->prefix.'order',array('seat_number'=>$_POST['new_seat_number']),array('id'=>$_POST['orderid']));

        //print_r($a .'&&'. $b);die;
        if($a && $b){
            $wpdb->query('COMMIT');
            wp_send_json_success('换座成功');
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error('换座失败');
        }

        die;

    }

    /**
     * 生成座位号
     */
    public function seating(){
        global $wpdb;
        $match_status = $wpdb->get_var(" select match_status from {$wpdb->prefix}match_meta_new where match_id = {$_POST['id']} ");
        if($match_status != -2) wp_send_json_error(array('info'=>'只有报名截止状态下才能生成座位'));
        //获取报名数据
        $rows = $wpdb->get_results("select id,user_id,match_id from {$wpdb->prefix}order where match_id = {$_POST['id']} and pay_status in (2,3,4) order by id asc",ARRAY_A);

        if(!empty($rows)){
            $new_arr = array();
            $a = 0;
            foreach ($rows as $k => $v) {
                //获取选手真实姓名
                /*$user_real_name = get_user_meta($v['user_id'],'user_real_name')[0];
                if(!empty($user_real_name)){
                    $v['real_name'] = $user_real_name['real_name'];
                    if (preg_match("/[\x7f-\xff]/", $user_real_name['real_name'])) {

                        array_push($new_arr,$v);
                    }else{

                        array_unshift($new_arr,$v);
                    }
                    ++$a;
                }*/
                //获取选手国籍
                $nationality_short = get_user_meta($v['user_id'],'user_nationality_short')[0];
                //print_r($v['user_id'].'**'.$nationality_short.'</br>');
                if(!empty($nationality_short)){

                    $v['nationality_short'] = $nationality_short;
                    if(in_array($nationality_short,array('CHN','TWN','HKG','MAC'))){
                        array_push($new_arr,$v);
                    }else{
                        array_unshift($new_arr,$v);
                    }
                    ++$a;
                }
            }
            // print_r($new_arr);
            //die;
            //開始分配座位號
            $c = 0;
            foreach ($new_arr as $key => $val) {
                $index = $key+1;
                $b = $wpdb->update($wpdb->prefix.'order',array('seat_number'=>$index),array('id'=>$val['id'],'user_id'=>$val['user_id']));
                if($b){
                    ++$c;
                }
            }
            //print_r($a.'==='.$c);
            wp_send_json_success(array('info'=>'生成成功'));

        }
        wp_send_json_error(array('info'=>'生成失败'));

    }

    /**
     * 清除历史操作
     */
    public function clear_history(){
        global $wpdb;
        if($_POST['type'] == 'category'){
            $args = array(
                'taxonomy' => 'question_genre', //自定义分类法
                'pad_counts' => false,
                'hide_empty' => false,
            );
            $category = get_categories($args);
            //print_r($category);
            if(!empty($category)){
                $category_id = array();
                foreach ($category as $k => $v){
                    $category_id[] = $v->term_id;
                }
                $id = arr2str($category_id);

                $sql = "select object_id,term_taxonomy_id from {$wpdb->prefix}term_relationships where term_taxonomy_id in($id) ";
                $object_id = $wpdb->get_results($sql,ARRAY_A);

                if(!empty($object_id)){
                    foreach ($object_id as $val){
                        if($val['term_taxonomy_id'] != $_POST['id'] ){
                            //$object_ids[] = $val['object_id'];
                            $sql_ = "UPDATE `{$wpdb->prefix}term_relationships` SET `term_taxonomy_id`= {$_POST['id']} WHERE object_id in({$val['object_id']})";
                            $a = $wpdb->query($sql_);
                        }
                    }
                    wp_send_json_success('操作完成');
                }

                wp_send_json_error('操作失败');
            }
            die;
        }

        if(empty($_POST['id'])) wp_send_json_error('请选择需要删除的数据');

        $ids = arr2str($_POST['id']);

        $wpdb->query('START TRANSACTION');
        switch ($_POST['type']){
            case 'user':
            case 'teacher':

                $table = "`{$wpdb->prefix}users`";
                $a = $wpdb->query("DELETE FROM `{$wpdb->prefix}usermeta` WHERE user_id in({$ids})");
                $wpdb->query("DELETE FROM `{$wpdb->prefix}order` WHERE user_id in({$ids})");
                $wpdb->query("DELETE FROM `{$wpdb->prefix}my_coach` WHERE user_id in({$ids})");

                break;
            case 'match':

                $table = "`{$wpdb->prefix}posts`";
                $a = $wpdb->query("DELETE FROM `{$wpdb->prefix}match_meta_new` WHERE match_id in({$ids})");
                $wpdb->query("DELETE FROM `{$wpdb->prefix}match_project_more` WHERE match_id in({$ids})");
                $wpdb->query("DELETE FROM `{$wpdb->prefix}order` WHERE match_id in({$ids})");

                break;
            case 'question':

                //获取当前分类下所有文章
                $rows = $wpdb->get_results("select object_id from {$wpdb->prefix}term_relationships where term_taxonomy_id in ({$ids})",ARRAY_A);
                if(empty($rows)) wp_send_json_error('所选分类下没有文章');
                $ids = $post_ids = arr2str(array_column($rows, 'object_id'),',');

                $table = "`{$wpdb->prefix}posts`";

                $sql = "select ID from {$wpdb->prefix}posts where post_parent in ({$post_ids})";
                $problem_id = $wpdb->get_results($sql,ARRAY_A);
                //print_r($problem_id);die;
                $problem_ids = arr2str(array_column($problem_id,'ID'));

                if(!empty($problem_id)){

                    $a = $wpdb->query("DELETE FROM `{$wpdb->prefix}posts` WHERE post_parent  in({$post_ids})");    //删除题目
                    $problem_ids = arr2str(array_column($problem_id,'ID'));
                    $wpdb->query("DELETE FROM `{$wpdb->prefix}problem_meta` WHERE problem_id  in({$problem_ids})"); //删除题目选项

                    $wpdb->query("DELETE FROM `{$wpdb->prefix}term_relationships` WHERE term_taxonomy_id  in({$ids})");    //删除题型分类
                }else{
                    $a = 1;
                }

                break;
            case 'team':

                $table = "`{$wpdb->prefix}posts`";
                $a = $wpdb->query("DELETE FROM `{$wpdb->prefix}team_meta` WHERE team_id in({$ids})");

                break;
            default:
                wp_send_json_error('非法操作');
                break;
        }
        $sql = "DELETE FROM $table WHERE ID in({$ids})";
        $c = $wpdb->query($sql);
        //var_dump($a .'---'.$c);
        if($a && $c ){
            $wpdb->query('COMMIT');
            wp_send_json_success('清除完成');
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error('清除失败');
        }
    }

    /**
     * 获取所有题目分类
     */
    public function admin_get_category_list(){
        $args = array(
            'taxonomy' => 'question_genre', //自定义分类法
            'pad_counts' => false,
            'hide_empty' => false,
        );
        $category = get_categories($args);

        $new_category = array();
        if(!empty($category)){
            foreach ($category as $k => $v){

                if(in_array($v->slug,array('cn-match-question','en-match-question','cn-test-question','en-test-question','en-grading-question','cn-grading-question','en-grading-test-question','cn-grading-test-question'))){
                    $new_category[] = array(
                        'id'=>$v->term_id,
                        'text'=>$v->name,
                    );
                }
            }
        }
        wp_send_json_success($new_category);
    }


    /**
     * 获取所有题目
     */
    public function get_question_list(){
        if($_GET['team_type'] == 'problem'){
            global $wpdb;
            $map = array();
            $map[] = ' post_status = "publish" ';
            $map[] = ' post_type = "question" ';
            if(!empty($_GET['term'])){
                $map[] = " post_title like '%{$_GET['term']}%' ";
            }
            if(!empty($_GET['question_id'])){
                $map[] = " ID = {$_GET['question_id']} ";
            }
            $where = join(' and ',$map);
            $sql = "select ID as id,post_title as text from {$wpdb->prefix}posts where {$where} limit 0,10";
            $rows = $wpdb->get_results($sql,ARRAY_A);
        }else{

            $args = array(
                'taxonomy' => 'question_genre', //自定义分类法
                'pad_counts' => false,
                'hide_empty' => false,
            );
            $category = get_categories($args);
            foreach ($category as $k => $v){
                $rows[] = array('id'=>$v->term_id,'text'=>$v->cat_name);
            }
        }

        wp_send_json_success($rows);
    }

    /**
     * 获取所有比赛
     */
    public function admin_get_match_list(){
        global $wpdb;
        $map = array();
        $map[] = ' post_type = "match" ';
        if(!empty($_GET['term'])){
            $map[] = " post_title like '%{$_GET['term']}%' ";
        }

        $where = join(' and ',$map);
        $sql = "select ID as id,post_title as text from {$wpdb->prefix}posts where {$where} limit 0,10";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        wp_send_json_success($rows);
    }


    /**
     * 获取所有战队
     */
    public function admin_get_team_list(){
        global $wpdb;
        $map = array();
        $map[] = ' post_type = "team" ';
        if(!empty($_GET['term'])){
            $map[] = " post_title like '%{$_GET['term']}%' ";
        }

        $where = join(' and ',$map);
        $sql = "select ID as id,post_title as text from {$wpdb->prefix}posts where {$where} limit 0,10";
        //print_r($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        wp_send_json_success($rows);
    }


    /**
     * 获取所有用户
     */
    public function admin_get_user_list(){
        global $wpdb;
        $map = array();
        if(!empty($_GET['term'])){
            $map[] = " (a.user_login like '%{$_GET['term']}%') ";
            $map[] = " (a.user_nicename like '%{$_GET['term']}%') ";
            $map[] = " (a.user_mobile like '%{$_GET['term']}%') ";
            $map[] = " (a.user_email like '%{$_GET['term']}%') ";
        }
        $where = !empty($map) ? join(' or ',$map) .' and ' : '';

        if($_GET['type'] == 'teacher'){
            $where .= ' (b.meta_value = 7) ';
        }else{
            $where .= ' (b.meta_value >= 0) ';
        }
        $sql = "select a.ID as id,
                case 
                when a.user_login != '' then a.user_login
                when a.user_mobile != '' then a.user_mobile
                when a.user_email != '' then a.user_email
                else a.user_nicename
                end as text 
                from {$wpdb->prefix}users a 
                left join {$wpdb->prefix}usermeta b  on a.ID = b.user_id and b.meta_key = '{$wpdb->prefix}user_level'
                where {$where}
                limit 0,10";
        //print_r($sql);die;
        $rows = $wpdb->get_results($sql,ARRAY_A);
        if(!empty($rows)){
            foreach ($rows as $k => $v){
                $user_real_name = get_user_meta($v['id'],'user_real_name')[0];
                if(!empty($user_real_name)){
                    $rows[$k]['text'] = $user_real_name['real_name'];
                }
            }
            wp_send_json_success($rows);
        }else{
            wp_send_json_error('');
        }
    }


    /****************************************************以下为后台Ajax功能方法***********************************************************************/

    /**
     * 批量申请教练审核和批量解除关系
     */
    public function coachApplyStatus(){
        global $wpdb;
        $status = intval($_POST['status']);
        $apply_status = 1;
        if($status == 2){
            $statusName = '通过审核';
        }elseif ($status == -1){
            $statusName = '被拒绝';
        }elseif ($status == 3){
            $apply_status = 2;
            $statusName = '解除关系';
        }
        $coach_id = isset($_POST['coach_id']) ? intval($_POST['coach_id']) : 0;
        $user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
        $category_id = isset($_POST['category']) ? trim($_POST['category']) : '';
        if($user_id=='' || $coach_id < 1){
            wp_send_json_error(array('info' => '参数错误'));
        }
        if($category_id  == '') wp_send_json_error(array('info' => '请选择类别!'));
        $user = $wpdb->get_results('SELECT u.ID AS user_id,u.user_mobile,um.meta_value AS user_real_name,u.user_email,GROUP_CONCAT(p.post_title separator "/") AS category_name 
            FROM '.$wpdb->prefix.'my_coach AS mc  
            LEFT JOIN '.$wpdb->users.' AS u ON mc.user_id=u.ID AND u.ID!=""  
            LEFT JOIN '.$wpdb->prefix.'usermeta AS um ON um.user_id=u.ID AND um.meta_key="user_real_name"  
            LEFT JOIN '.$wpdb->posts.' AS p ON p.ID=mc.category_id   
            WHERE mc.user_id IN('.$user_id.') AND mc.category_id IN('.$category_id.') and mc.coach_id='.$coach_id.' 
            GROUP BY mc.user_id',ARRAY_A);
        //TODO
        $sql = 'UPDATE '.$wpdb->prefix.'my_coach SET `apply_status`='.$status.' WHERE coach_id='.$coach_id.' AND user_id IN('.$user_id.') AND `apply_status`='.$apply_status.' AND category_id IN('.$category_id.')';
        if($status != -1  && $status != 2 && $status != 3){
            wp_send_json_error(array('info' => '操作失败,状态参数异常'));
        }
        $bool = $wpdb->query($sql);

//        $bool = true;
        if($bool) {
            //TODO 发送通知
            //获取教练姓名
            $coach_user_meta = get_user_meta($coach_id);
            if(!$coach_user_meta) wp_send_json_error(['info' => '未获取到教练信息']);
            if(isset($coach_user_meta['user_real_name'][0]) && !empty($coach_user_meta['user_real_name'][0])){
                $coach_real_name = unserialize($coach_user_meta['user_real_name'][0])['real_name'];
            }elseif(isset($coach_user_meta['last_name'][0]) && isset($coach_user_meta['first_name'][0]) && !empty($coach_user_meta['first_name'][0]) && !empty($coach_user_meta['first_name'][0])){
                $coach_real_name = $coach_user_meta['last_name'][0].$coach_user_meta['first_name'][0];
            }else{
                $coach_real_name = $coach_user_meta['nickname'][0];
            }
            $sendErr = "\n";
            foreach ($user as $v){
                //查询类别名称
                $user_real_name = $v['user_real_name'] ? unserialize($v['user_real_name'])['real_name'] : $v['user_login'];

//                if($status == 3){
//                    if(!empty($v['user_mobile']) == 'mobile'){
//                        $ali = new AliSms();
//                        $result = $ali->sendSms($v['user_mobile'], 7, array('user'=>$user_real_name, 'cate' => "({$v['category_name']})", 'coach' => $coach_real_name));
//                        if(!$result) $sendErr .= $user_real_name.': '.$v['user_mobile'].'短信发送失败'."\n";
//                    }else{
//                        $result = $this->send_mail($v['user_email'], '国际脑力运动', '尊敬的'.$user_real_name.'您好，您的('.$v['category_name'].')教练'.$coach_real_name.'解除了与您的教学关系，您可登录系统查看');
//                        if($result !== true) $sendErr .= $user_real_name.': '.$v['user_email'].'邮件发送失败'."\n";
//                    }
//                }else{
//                    if(!empty($v['user_mobile'])){
//                        $ali = new AliSms();
//                        $result = $ali->sendSms($v['user_mobile'], 10, array('user'=>$user_real_name,'cate'=>"({$v['category_name']})", 'coach' => $coach_real_name, 'type' => $statusName));
//                        if(!$result) $sendErr .= $user_real_name.': '.$v['user_mobile'].'短信发送失败'."\n";
//                    }else{
//                        $result = $this->send_mail($v['user_email'], '国际脑力运动', '尊敬的'.$user_real_name.',您申请的('.$v['category_name'].')教练'.$coach_real_name.'已'.$statusName);
//                        if($result !== true) $sendErr .= $user_real_name.': '.$v['user_email'].'邮件发送失败'."\n";
//                    }
//                }

            }

            wp_send_json_success(array('info'=>'操作成功'.$sendErr));
        } else{
            wp_send_json_error(array('info' => '操作失败'));
        }
    }

    /**
     * 申请加入战队审核
     */
    public function matchTeamApplyStatus(){
        global $wpdb;
        $status = intval($_POST['status']); // -1 退队; 1 入队
        $type = intval($_POST['type']); //1 同意; 2 拒绝
        if(($type != 1 && $type != 2) || ($status != -1 && $status != 1)) wp_send_json_error(array('info' => '参数错误'));

        if($status == -1){
            //退队
            $statusName = '退出';
            if($type == 1)
                $teamStatus = -3;
            else
                $teamStatus = 2;
        }elseif($status == 1){
            //入队
            $statusName = '加入';
            if($type == 1)
                $teamStatus = 2;
            else
                $teamStatus = -2;
        }
        $typeName = $type == 1 ? '同意' : '被拒绝';


        if($teamStatus != -3 && $teamStatus != -2 && $teamStatus != 2) wp_send_json_error(array('info' => '操作失败,状态参数异常'));
        if(is_array($_POST['id'])){
            $id = '';
            foreach ($_POST['id'] as $v){
                $id.= $v.',';
            }
            $id = substr($id,0,strlen($id)-1);
        }else{
            $id = intval($_POST['id']);
        }

        $users = $wpdb->get_results('SELECT u.user_mobile,u.ID,u.display_name,m.team_id,u.user_login,u.user_email FROM '.$wpdb->prefix.'match_team AS m 
            LEFT JOIN '.$wpdb->users.' AS u ON u.ID=m.user_id 
            WHERE m.id IN('.$id.') AND m.status='.$status, ARRAY_A);
        if(!$users) wp_send_json_error(['info' => '获取申请学员失败']);
        $sql = 'UPDATE '.$wpdb->prefix.'match_team SET status='.$teamStatus.' WHERE status='.$status.' AND id IN('.$id.')';
        //战队名称
        $post_title = get_post($users[0]['team_id'])->post_title;
        if(!$post_title) wp_send_json_error(['info' => '获取战队名称失败']);
        $bool = $wpdb->query($sql);
//        $bool = true;
        if($bool){
            //TODO 发送通知

            $sendErr = "\n";
            foreach ($users as $v){
                $userContact = $this->getMobileOrEmailAndRealname($v['user_id'],$v['user_mobile'], $v['user_email']);
                if($userContact == false){
                    $sendErr .= $v['user_login'].': 用户信息不完整, 未发送信息'."\n";
                    continue;
                }

                if($userContact['type'] == 'mobile'){
                    $ali = new AliSms();
                    $result = $ali->sendSms($userContact['contact'], 9, array('user'=>$userContact['real_name'], 'applytype' => $statusName, 'team' => $post_title, 'type' => $typeName));
                    if(!$result) $sendErr .= $userContact['real_name'].': '.$userContact['contact'].'短信发送失败'."\n";
                }else{
                    $result = $this->send_mail($userContact['contact'], '国际脑力运动', '尊敬的'.$userContact['real_name'].',您申请'.$statusName.'战队'.$post_title.'已'.$typeName);
                    if($result !== true) $sendErr .= $userContact['real_name'].': '.$userContact['contact'].'邮件发送失败'."\n";
                }

            }
            wp_send_json_success(array('info'=>'操作成功'.$sendErr));
        }else{
            wp_send_json_error(array('info' => '操作失败'));
        }

    }

    /**
     * 支付查询订单
     */
    public function queryPayOrder(){
        $id = intval($_POST['id']);
        global $wpdb;
        $order = $wpdb->get_row('SELECT pay_lowdown,serialnumber,pay_status,pay_type FROM '.$wpdb->prefix.'order WHERE id='.$id, ARRAY_A);
        if(!$order) wp_send_json_error(array('info' => '未找到订单'));
        if($order && $order['pay_status'] == 2){
            require_once WP_CONTENT_DIR.'/plugins/nlyd-student/Controller/class-student-payment.php';
            switch ($order['pay_type']){
                case 'wx':
                    //微信
                    $payClass = new Student_Payment('wxpay');
                    $param = [
                        'transaction_id' => unserialize($order['pay_lowdown'])['transaction_id'], //微信订单号(二选一)
                        'order_no' => $order['serialnumber']// 商户订单号 (二选一)
                    ];
                    $result = $payClass->payClass->orderQuery($param); //return array
                    break;
                case 'zfb':
                    $param = [
                        'out_trade_no' => $order['serialnumber'],
                        'trade_no' => unserialize($order['pay_lowdown'])['trade_no'],
                    ];
                    $payClass = new Student_Payment('alipay');
                    $result = $payClass->payClass->queryOrder($param);
                    break;
                case 'ylk':

                    break;
                default:
                    wp_send_json_error(array('info' => '订单无支付方式'));
            }
        }else{
            wp_send_json_error(array('info' => '订单支付状态异常'));
        }

        if($result){
            if($result['status'] == true){
                //TODO
                wp_send_json_success(array('info'=>$result['data']));
            }else{
                wp_send_json_error(array('info' => $result['data']));
            }

        }else{
            wp_send_json_error(array('info' => '查询失败'));
        }
    }

    /**
     * 支付申请退款
     */
    public function refundPay(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_refund_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        $serial = intval($_POST['serial']);
        global $wpdb;
        $refundFee = $_POST['refund_fee'];
        $order = $wpdb->get_row('SELECT id,serialnumber,user_id,pay_lowdown,cost,pay_type,fullname,telephone FROM '.$wpdb->prefix.'order WHERE serialnumber="'.$serial.'" AND pay_status!=1 AND pay_status!=5', ARRAY_A);



        if(!$order) wp_send_json_error(array('info' => '未找到订单或此订单不可退款'));


        if($refundFee > $order['cost']) wp_send_json_error(array('info' => '退款金额不能超过订单金额'));

        $wpdb->query('START TRANSACTION');
        $refund_no = $order['serialnumber'].date_i18n('dHis',current_time('timestamp')).rand(000,999);
//        var_dump(['order_id' => $order['id'], 'refund_no' => $refund_no, 'created_time' => date('Y-m-d H:i:s')]);die;
        //更新订单状态
        if(!$wpdb->update($wpdb->prefix.'order', ['pay_status' => -2], ['id' => $order['id']])){
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>'更新订单状态失败'));
        }

        //创建退款单
        if(!$wpdb->insert($wpdb->prefix.'order_refund',['order_id' => $order['id'], 'refund_no' => $refund_no, 'refund_cost' => $refundFee,  'created_time' => date_i18n('Y-m-d H:i:s', current_time('timestamp'))])){
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info'=>'生成退款单失败'));
        }

        $orderRefundId = $wpdb->insert_id;
        require_once PLUGINS_PATH.'/nlyd-student/Controller/class-student-payment.php';
        switch ($order['pay_type']){
            case 'wx':
                $param['transaction_id'] = unserialize($order['pay_lowdown'])['transaction_id']; //微信订单号
                $param['out_trade_no'] = $order['serialnumber'];
                $param['out_refund_no'] = $refund_no; //TODO 商户的微信退款单号
                $param['refund_fee'] = $refundFee;
                $param['price'] = $order['cost'];
                $payClass = new Student_Payment('wxpay');
                $result = $payClass->payClass->refund($param);
                break;
            case 'zfb':


                $param = [
                    'out_trade_no' => $order['serialnumber'],     //商户订单号，和支付宝交易号二选一
                    'trade_no' => unserialize($order['pay_lowdown'])['trade_no'],    //支付宝交易号，和商户订单号二选一
                    'refund_amount' => $refundFee,       //退款金额，不能大于订单总金额
                    'refund_reason' => '商家退款',     //退款的原因说明
                    'out_request_no' => $refund_no,      //标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传。
                ];
                $payClass = new Student_Payment('alipay');

                $result = $payClass->payClass->refund($param);
                break;
            case 'ylk':

                break;
            default:
                $wpdb->query('ROLLBACK');
                wp_send_json_error(array('info' => '此订单无支付方式'));
        }

        if($result){
            if($result['status'] == true){
                //保存第三方信息
                $wpdb->update($wpdb->prefix.'order_refund', ['refund_lowdown' => serialize($result['data'])], ['id' => $orderRefundId]);
                //发送短信


                $ali = new AliSms();
                if(!$order['telephone']){
                    $user = $wpdb->get_row('SELECT ID AS user_id,user_mobile,user_email FROM '.$wpdb->users.' WHERE ID='.$order['user_id'] ,ARRAY_A);
                    if(!$user) {
                        $sendMsg = ', 用户信息不完整, 未发送信息';
                    }else{
                        $userContact = $this->getMobileOrEmailAndRealname($order['user_id'],$user['user_mobile'], $user['user_email']);
                        if($userContact == false){
                            $sendMsg = ', 用户信息不完整, 未发送信息';
                        }else{
                            if($userContact['type'] == 'mobile'){
                                $ali = new AliSms();
                                $result = $ali->sendSms($userContact['contact'], 8, array('user'=> $userContact['real_name'], 'order' => $order['serialnumber'], 'cost' => $refundFee));
                                $sendMsg = !$result ? '短信发送失败' : '已发送短信通知';
                            }else{
                                $result = $this->send_mail($userContact['contact'], '国际脑力运动', '尊敬的'.$userContact['real_name'].', 您的订单号为'.$order['serialnumber'].'的订单已退款,退款金额'.$refundFee.',请注意查收');
                                $sendMsg = !$result ? '邮件发送失败' : '已发送邮件通知';
                            }
                        }
                    }
                }else{
                    $result = $ali->sendSms($order['telephone'], 8, array('user'=> $order['funllname'], 'order' => $order['serialnumber'], 'cost' => $refundFee));
                    if($result){
                        $sendMsg = ', 已发送短信通知';
                    }else{
                        $sendMsg = ', 短信发送失败';
                    }
                }

                $wpdb->query('COMMIT');
                wp_send_json_success(array('info'=> '申请退款成功'.$sendMsg));
            }else{
                $wpdb->query('ROLLBACK');
                wp_send_json_error(array('info' => $result['data']));
            }

        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('info' => '查询失败'));
        }
    }

    /**
     * 不退款,顶单状态修改为支付完成
     */
    public function noRefund(){
        $id = intval($_POST['id']);
        global $wpdb;
        if($wpdb->update($wpdb->prefix.'order', ['pay_status' => 2], ['id' => $id, 'pay_status' => -1])){
            //TODO 发送通知
//            $ali = new AliSms();
//            $row = $wpdb->get_row('SELECT serialnumber,telephone FROM '.$wpdb->prefix.'order WHERE id='.$id, ARRAY_A);
//            $result = $ali->sendSms($row['telephone'], 19, array('code'=>'您的订单号'.$row['serialnumber'].'申请退款已被拒绝'));

            wp_send_json_success(array('info'=> '操作成功,已修改支付状态'));
        }else{
            wp_send_json_error(array('info' => '操作失败'));
        }
    }

    /**
     * 支付查询退款
     */
    public function wxRefundQuery(){
        $id = intval($_POST['id']);
        global $wpdb;
        $order = $wpdb->get_row('SELECT o.serialnumber,o.pay_lowdown,o.cost,o.pay_type,r.refund_no,r.refund_lowdown FROM '
            .$wpdb->prefix.'order_refund AS r 
                                LEFT JOIN '.$wpdb->prefix.'order AS o ON o,id=r.order_id   
                                WHERE r.id='.$id, ARRAY_A);

        if(!$order) wp_send_json_error(array('info' => '未找到订单,请刷新重试'));
        require_once WP_CONTENT_DIR.'/plugins/nlyd-student/Controller/class-student-payment.php';
        switch ($order['pay_type']){
            case 'wx':

                $param = [
                    'transaction_id' => unserialize($order['pay_lowdown'])['transaction_id'], //微信订单号 (四选一)
                    'out_trade_no' => $order['serialnumber'], //商户订单号 (四选一)
                    'out_refund_no' => $order['refund_no'], //商户退款单号 (四选一)
                    'refund_id' => unserialize($order['refund_lowdown'])['refund_id'], //微信退款单号 (四选一)
                ];
                $payClass = new Student_Payment('wxpay');
                $res = $payClass->payClass->refundQuery($param);
                break;
            case 'zfb':
                $param = [
                    'out_trade_no' => $order['serialnumber'],        //商户订单号，和支付宝交易号二选一
                    'trade_no' =>  unserialize($order['pay_lowdown'])['trade_no'],        //支付宝交易号，和商户订单号二选一
                    'out_request_no' => unserialize($order['refund_lowdown'])['out_request_no'],        //请求退款接口时，传入的退款请求号，如果在退款请求时未传入，则该值为创建交易时的外部交易号
                ];
                $payClass = new Student_Payment('alipay');
                $res = $payClass->payClass->refund($param);
                break;
            case 'ylk':

                break;
            default:
                wp_send_json_error(array('info' => '此订单无支付方式'));
        }

        if($res['status']){
            wp_send_json_success(array('info'=>$res['data']));
        }else{
            wp_send_json_error(array('info' => $res['data']));
        }
    }

    /**
     * 支付关闭订单
     */
    public function closePayOrder(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_closepay_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        $id = intval($_POST['id']);
        global $wpdb;
        $order = $wpdb->get_row('SELECT serialnumber,pay_lowdown,cost,pay_type FROM '.$wpdb->prefix.'order WHERE id='.$id, ARRAY_A);
        if(!$order) wp_send_json_error(array('info' => '未找到订单,请刷新重试'));
        require_once WP_CONTENT_DIR.'/plugins/nlyd-student/Controller/class-student-payment.php';
        switch ($order['pay_type']){
            case 'wx':
                //微信
                $payClass = new Student_Payment('wxpay');
                $result = $payClass->payClass->closeOrder($order['serialnumber']); //return array
                break;
            case 'zfb':
                $param = [
                    'out_trade_no' => $order['serialnumber'],//商户订单号，和支付宝交易号二选一
                    'trade_no' => unserialize($order['pay_lowdown'])['trade_no'],        //支付宝交易号，和商户订单号二选一
                ];
                $payClass = new Student_Payment('alipay');
                $result = $payClass->payClass->closeOrder($param);
                break;
            case 'yld':

                break;
            default:
                wp_send_json_error(array('info' => '此订单无支付方式'));
        }

        if($result['status']){
            //TODO 关闭成功
            wp_send_json_success(array('info' => '支付订单已关闭'));

        }else{
            wp_send_json_error(array('info' => $result['data']));

        }
    }

    /**
     *根据查询条件匹配战队成员列表 学生或者教练
     */
    public function getMemberByWhere(){
        $type = $_GET['team_type'];
        $team_id = $_GET['team_id'];

        switch ($type){
            case 'team_leader': //队长
                $user_role = 0;
                $user_type = 1;
                break;
            case 'team_director':   //负责人
                $user_role = 7;
                $user_type = 2;
                break;
        }
        global $wpdb;

        $select = "SELECT a.user_id as id ,b.team_id,c.display_name as text FROM {$wpdb->prefix}usermeta a ";

        $left_join = "left join {$wpdb->prefix}match_team b on a.user_id = b.user_id
                      left join {$wpdb->prefix}users c on a.user_id = c.ID";

        $where = " where a.meta_key = '{$wpdb->prefix}user_level' AND a.meta_value = {$user_role} ";
        $where_ = " AND b.status = 2 AND b.user_type = {$user_type} AND b.team_id = {$team_id} ";
        if(!empty($_GET['term'])){
            $where .= "  and (c.user_login like '%{$_GET['term']}%' or c.user_nicename like '%{$_GET['term']}%' or c.user_email like '%{$_GET['term']}%' or c.display_name like '%{$_GET['term']}%') ";
        }
        $limit = " limit 0,10 ";

        $sql = $select.$left_join.$where.$where_.$limit;
        //print_r($sql);die;
        $rows = $wpdb->get_results($sql,ARRAY_A);

        if(empty($rows)){
            $select = "SELECT a.user_id as id ,c.display_name as text FROM wp_usermeta a ";
            $left_join = " left join {$wpdb->prefix}users c  on a.user_id = c.ID ";

            $sql = $select.$left_join.$where.$limit;
            $rows = $wpdb->get_results($sql,ARRAY_A);

        }
        if(!empty($rows)){
            foreach ($rows as $k => $val ){
                $user = get_user_meta($val['id'],'user_real_name');
                if(!empty($user[0])){
                    $rows[$k]['text'] = $user[0]['real_name'];
                }else{
                    $rows[$k]['text'] = preg_replace('/, /','',$val['text']);
                }
            }
        }
        wp_send_json_success($rows);
    }

    /**
     * 设置战队队长或战队负责人
     */
    public function setMatchTeamLeaderOrDirector(){
        $team_id = intval($_POST['team_id']);
        $match_team_id = intval($_POST['match_team_id']);
        $type = $_POST['type'];
        switch ($type){
            case 'leader':
                $field = 'team_leader';
                break;
            case 'director':
                $field = 'team_director';
                break;
            default:
                wp_send_json_error(['info' => '参数错误!']);
        }
        if($team_id < 1 || $match_team_id < 1) wp_send_json_error(['info' => '参数错误!']);
        global $wpdb;
        $bool = $wpdb->update($wpdb->prefix.'team_meta', [$field => $match_team_id], ['team_id' => $team_id]);
        if($bool)
            wp_send_json_success(['info' => '设置成功']);
        else
            wp_send_json_error(['info' => '设置失败']);
    }

    /**
     * 删除一条意见反馈
     */
    public function remFeedback(){
        $id = intval($_POST['id']);
        if($id < 1) wp_send_json_error(['info' => '参数错误']);
        global $wpdb;
        $row = $wpdb->get_row('SELECT images FROM '.$wpdb->prefix.'feedback'.' WHERE id='.$id, ARRAY_A);
        if(!$row) wp_send_json_error(['info' => '没有找到此条意见反馈']);
        $bool = $wpdb->delete($wpdb->prefix.'feedback', ['id' => $id]);
        if($bool){
            //删除图片
            foreach (unserialize($row['images']) as $v){
                $filePa = explode('uploads',$v);
                if(is_file(wp_upload_dir()['basedir'].$filePa[1])) unlink(wp_upload_dir()['basedir'].$filePa[1]);
            }
            wp_send_json_success(['info' => '删除成功']);
        }else{
            wp_send_json_error(['info' => '删除失败']);
        }
    }

    /**
     * 教练解除教学关系
     */
    public function relieveMyStudent(){
        $id = $_POST['id'];
        if($id == '') wp_send_json_error(['info' => '参数错误']);
        $id = join(',',$id);
        //查询是否是战队成员
        global $wpdb;
        $res = $wpdb->get_results('SELECT id,user_id,category_id,coach_id FROM '.$wpdb->prefix.'my_coach WHERE id IN ('.$id.') AND apply_status=2', ARRAY_A);
        //不存在或已解除
        if(!$res) wp_send_json_error(['info' => '该学员不存在或已解除']);

        //获取用户信息
        $user = $wpdb->get_row('SELECT ID,user_mobile,display_name,user_email FROM '.$wpdb->users.' WHERE ID='.$res[0]['user_id'], ARRAY_A);


        if(!$user) wp_send_json_error(['info' => '该学员不存在']);


        //开始解除
        $bool = $wpdb->query("UPDATE {$wpdb->prefix}my_coach SET apply_status=3 WHERE id IN({$id})");
        if($bool){
            //TODO 发送短信通知学员
            //教练名字

            $user_coach_meta = get_user_meta($res[0]['coach_id']);
            if(isset($user_user_meta['user_real_name'][0]) && !empty($user_coach_meta['user_real_name'][0])){
                $coach_real_name = unserialize($user_coach_meta['user_real_name'][0])['real_name'];
            }elseif(isset($user_coach_meta['last_name'][0]) && isset($user_coach_meta['first_name'][0]) && !empty($user_coach_meta['first_name'][0]) && !empty($user_coach_meta['first_name'][0])){
                $coach_real_name = $user_coach_meta['last_name'][0].$user_coach_meta['first_name'][0];
            }elseif(isset($user_coach_meta['nickname'][0])){
                $coach_real_name = $user_coach_meta['nickname'][0];
            }else{
                $coach_real_name = '';
            }
            $post_titleArr = [];
           foreach ($res as $rv){
               $post_titleArr[] = get_post($rv['category_id'])->post_title;

           }
            $post_title = ('(').join('/',$post_titleArr).')';
            $userContact = $this->getMobileOrEmailAndRealname($user['ID'],$user['user_mobile'], $user['user_email']);
            if($userContact == false){
                $sendMsg = ', 用户信息不完整, 未发送信息';
            }else{
                if($userContact['type'] == 'mobile'){
                    $ali = new AliSms();
                    $result = $ali->sendSms($userContact['contact'], 7, array('user'=> $userContact['real_name'], 'cate' => $post_title, 'coach' => $coach_real_name));
                }else{
                    $result = $this->send_mail($userContact['contact'], '国际脑力运动', '尊敬的'.$userContact['real_name'].'您好，您的'.$post_title.'教练'.$coach_real_name.'解除了与您的教学关系，您可登录系统查看');
                }
            }
            wp_send_json_success(['info' => '已解除教学关系']);

        }else{
            wp_send_json_error(['info' => '解除失败']);
        }
    }

    /**
     * 踢出战队
     */
    public function expelTeam(){
        $id = intval($_POST['id']);
        if($id < 1) wp_send_json_error(['info' => '参数错误']);
        global $wpdb;
        $bool = $wpdb->update($wpdb->prefix.'match_team', ['status' => -3], ['id' => $id]);
        if($bool){
            wp_send_json_success(['info' => '操作成功']);
        }else{
            wp_send_json_error(['info' => '操作失败']);
        }
    }

    /**
     * 关闭比赛
     */
    public function closeMatch(){
        $id = intval($_POST['id']);
        if($id < 1) wp_send_json_error(['info' => '参数错误']);
        global $wpdb;
//        $wpdb->query('START TRANSACTION');
//        $bool = $wpdb->update($wpdb->prefix.'match_meta', ['match_status' => -3], ['match_id' => $id]);
//        if(!$bool){
//            $wpdb->query('ROLLBACK');
//            wp_send_json_error(['info' => '关闭失败']);
//        }
        //移入回收站
        if(wp_trash_post($id)){
//            $wpdb->query('COMMIT');
            wp_send_json_success(['info' => '关闭成功']);
        }else{
//            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '关闭失败']);
        }
    }

    /**
     * 删除比赛
     */
    public function delMatch(){
        $id = intval($_POST['id']);
        //删除订单 meta 分数


        if($id < 1) wp_send_json_error(['info' => '参数错误']);
        //判断是否是已关闭的比赛(回收站中的)
        $post = get_post($id);
        if($post->post_status == 'trash'){
            global $wpdb;
            $wpdb->query('START TRANSACTION');
            //删除post
            if(!$wpdb->delete($wpdb->prefix.'posts',['ID'=>$id])){
                $wpdb->query('ROLLBACK');
                wp_send_json_error(['info' => '比赛删除失败']);
            }
            //删除meta
            $meta = $wpdb->get_row("SELECT match_id FROM {$wpdb->prefix}match_meta_new WHERE match_id={$id}");
            if($meta){
                $metaBool = $wpdb->delete($wpdb->prefix.'match_meta_new',['match_id' => $id]);
                if(!$metaBool){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => '比赛外键删除失败']);
                }
            }
            //删除订单
            $order = $wpdb->get_row("SELECT match_id FROM {$wpdb->prefix}order WHERE match_id={$id} AND order_type=1");
            if($order){
                $orderBool = $wpdb->update($wpdb->prefix.'order', ['pay_status' => 5], ['match_id' => $id, 'order_type' => 1]);
                if(!$orderBool){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => '订单删除失败']);
                }
            }
            //删除答题记录
            $question = $wpdb->get_row("SELECT match_id FROM {$wpdb->prefix}match_questions WHERE match_id={$id}");
            if($question){
                $questionBool = $wpdb->delete($wpdb->prefix.'match_questions', ['match_id' => $id]);
                if(!$questionBool){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => '答题记录删除失败']);
                }
            }
            $wpdb->query('COMMIT');
            wp_send_json_success(['info' => '比赛已删除']);

        }else{
            wp_send_json_error(['info' => '请先关闭比赛']);
        }
    }

    /**
     * 添加比赛报名学员
     */
    public function joinMatch(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'student_join_match_code_nonce') ) {
            wp_send_json_error(array('info'=>'非法操作'));
        }
        $match_id = isset($_POST['mid']) ? intval($_POST['mid']):0;
        $user_id = isset($_POST['uid']) ? intval($_POST['uid']):0;
        $sub_centres_id = isset($_POST['sub_centres_id']) ? intval($_POST['sub_centres_id']):0;
        if($match_id < 1 || $user_id < 1) wp_send_json_error(array('info'=>'非法操作'));
        if($sub_centres_id < 1) wp_send_json_error(array('info'=>'请选择参赛机构!'));
        global $wpdb;
        //判断是否已报名该比赛
        $orderRes = $wpdb->get_var('SELECT id FROM '.$wpdb->prefix.'order WHERE user_id='.$user_id.' AND match_id='.$match_id.' AND order_type=1 AND pay_status IN(2,3,4)');
        if($orderRes) wp_send_json_error(array('info'=>'此学员已报名该比赛'));
        //清除未支付的当前学员当前比赛的订单
        $wpdb->query('DELETE FROM '.$wpdb->prefix.'order WHERE user_id='.$user_id.' AND match_id='.$match_id.' AND order_type=1 AND pay_status NOT IN(2,3,4)');

        //判断是否有主训和默认收货地址
        //主训
//        $sql = "select ID,post_title from {$wpdb->prefix}posts where post_type = 'match-category' and post_status = 'publish' order by menu_order asc  ";
//        $postsRows = $wpdb->get_results($sql,ARRAY_A);
//        foreach ($postsRows as $prow){
//            //每个类别得主训
//            $res = $wpdb->get_row('SELECT id FROM '.$wpdb->prefix.'my_coach WHERE apply_status=2 AND major=1 AND category_id='.$prow['ID']);
//            if(!$res){
//                wp_send_json_error(array('info'=>'当前学员未设置'.$prow['post_title'].'主训教练!'));
//                return;
//            }
//        }
//        //默认收货地址
//        $addressRes = $wpdb->get_row('SELECT fullname,telephone,country,province,city,area,address FROM '.$wpdb->prefix.'my_address WHERE user_id='.$user_id.' AND is_default=1', ARRAY_A);
//        if(!$addressRes){
//            wp_send_json_error(array('info'=>'当前学员未设置默认收货地址!'));
//            return;
//        }
//        $cost = $wpdb->get_var('SELECT match_cost FROM '.$wpdb->prefix.'match_meta_new WHERE match_id='.$match_id);
        //获取座位号
        $num = $wpdb->get_var("SELECT COUNT(`id`) FROM `{$wpdb->prefix}order` WHERE match_id='{$match_id}' AND order_type=1 AND pay_status IN(2,3,4)");
        //新增订单
        $orderInsertData = [
            'user_id' => $user_id,
            'match_id'=>$match_id,
            'cost'=> 0,
//            'fullname'=>$addressRes['fullname'],
//            'telephone'=>$addressRes['telephone'],
//            'address'=>$addressRes['country'].$addressRes['province'].$addressRes['city'].$addressRes['area'].$addressRes['address'],
            'order_type'=>1,
            'sub_centres_id'=>$sub_centres_id,
            'pay_status'=>4,
            'seat_number'=>$num+1,
            'created_time'=>get_time('mysql'),
        ];


        //开启事务
        $wpdb->query('START TRANSACTION');
        $insertRes = $wpdb->insert($wpdb->prefix.'order',$orderInsertData);

        if(!$insertRes){
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '操作失败']);
            return;
        }
        $insertId = $wpdb->insert_id;
        //生成流水号
        $serialnumber = createNumber($user_id,$insertId);
        $updateRes = $wpdb->update($wpdb->prefix.'order',array('serialnumber'=>$serialnumber),array('id'=>$wpdb->insert_id));
        if($updateRes){
            if(insertIncomeLogs($orderInsertData)){
                $wpdb->query('COMMIT');
                wp_send_json_success(['info' => '操作成功']);
            }else{
                $wpdb->query('ROLLBACK');
                wp_send_json_error(['info' => '操作失败']);
            }
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '操作失败']);
        }
    }

    /**
     * 永久删除题目
     */
    public function delQuestion(){
        $id = intval($_POST['id']);
        if($id < 1) wp_send_json_error(['info' => '操作失败']);
        //判断是否是回收站里面的数据
        $post = get_post($id);
        if($post->post_status != 'trash') wp_send_json_error(['info' => '请先将题目移入回收站']);
        global $wpdb;
        //获取答案和答案选项
        $answers = $wpdb->get_results('SELECT ID FROM '.$wpdb->posts.' WHERE post_parent='.$id);
        $answerIdStr = '';
        foreach ($answers as $anv){
            $answerIdStr .= $anv->ID.',';
        }

        $answerIdStr = '('.substr($answerIdStr, 0, strlen($answerIdStr)-1).')';

        //开始删除
        $wpdb->query('START TRANSACTION');
        $questionBool = $wpdb->query('DELETE FROM '.$wpdb->posts.' WHERE ID='.$id.' OR post_parent='.$id);
        $correctBool = $wpdb->query('DELETE FROM '.$wpdb->prefix.'problem_meta WHERE problem_id IN'.$answerIdStr);
        $wpdb->query('DELETE FROM '.$wpdb->prefix.'term_relationships WHERE object_id='.$id);

        if(!$questionBool || (!$correctBool && $answerIdStr != '()')){
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '操作失败']);
        }else{
            $wpdb->query('COMMIT');
            wp_send_json_success(['info' => '题目已删除']);
        }
    }
    /**
     * 永久删除问题
     */
    public function delAnswer(){
        $id = intval($_POST['id']);
        if($id < 1) wp_send_json_error(['info' => '操作失败']);
        //判断是否是回收站里面的数据
        $post = get_post($id);
        if($post->post_status != 'trash') wp_send_json_error(['info' => '请先将题目移入回收站']);
        global $wpdb;
        //判断是否是最后一个问题 //如果是已确认不再判断
        if(intval($_POST['type']) == 0){
            $post = get_post($id);
            $answerArr = $wpdb->get_results('SELECT ID FROM '.$wpdb->posts.' WHERE post_parent='.$post->post_parent.' GROUP BY ID', ARRAY_A);
            if(count($answerArr) < 2){
                wp_send_json_success(['info' => 9527]);
            }
        }

        //开始删除
        $wpdb->query('START TRANSACTION');
        $questionBool = $wpdb->query('DELETE FROM '.$wpdb->posts.' WHERE ID='.$id);
        $correctBool = $wpdb->query('DELETE FROM '.$wpdb->prefix.'problem_meta WHERE problem_id='.$id);
        if(!$questionBool || !$correctBool){
            //是否是答案不存在
            if(!$correctBool && $wpdb->get_row('SELECT id FROM '.$wpdb->prefix.'problem_meta WHERE problem_id='.$id)){
                $wpdb->query('ROLLBACK');
                wp_send_json_error(['info' => '操作失败']);
            }else{
                $wpdb->query('COMMIT');
                wp_send_json_success(['info' => '问题已删除']);
            }
        }else{
            $wpdb->query('COMMIT');
            wp_send_json_success(['info' => '问题已删除']);
        }
    }

    /**
     * 根据user_id user_mobile user_email获取发动信息的手机号码或者邮箱
     */
    public function getMobileOrEmailAndRealname($user_id, $mobile, $email){

        if(empty($mobile) && empty($email)) return false;
        global $wpdb;
        if(empty($mobile)){
            $address = $wpdb->get_row('SELECT telephone,fullname FROM '.$wpdb->prefix.'my_address WHERE is_default=1 AND user_id='.$user_id, ARRAY_A);
            if(isset($address['telephone']) && !empty($address['telephone'])){
                $mobile = $address['telephone'];
            }else{
                //如果收货地址也不存在, 就使用邮箱
                if(empty($email)){
                    return false;//邮箱和手机都没有
                }
            }
        }
        //获取真实姓名
        $user_user_meta = get_user_meta($user_id);
        if(isset($user_user_meta['user_real_name'][0]) && !empty($user_user_meta['user_real_name'][0])){
            $real_name = unserialize($user_user_meta['user_real_name'][0])['real_name'];
        }elseif(isset($user_user_meta['last_name'][0]) && isset($user_user_meta['first_name'][0]) && !empty($user_user_meta['first_name'][0]) && !empty($user_user_meta['first_name'][0])){
            $real_name = $user_user_meta['last_name'][0].$user_user_meta['first_name'][0];
        }elseif(isset($user_user_meta['nickname'][0])){
            $real_name = $user_user_meta['nickname'][0];
        }else{
            $real_name = $mobile != '' && !empty($mobile) ? $mobile : $email;
        }

        if(!isset($real_name)) return false;
        $type = $mobile != '' && !empty($mobile) ? 'mobile' : 'email';
        $contact = $mobile != '' && !empty($mobile) ? $mobile : $email;
        return ['contact' => $contact, 'type' => $type, 'real_name' => $real_name];

    }
    /**
     * 邮件发送
     * @param $to               收件人邮箱
     * @param string $subject   标题
     * @param $name             发送人名称
     * @param string $body      内容
     * @param null $attachment  附件
     * @return bool|string
     * @throws phpmailerException
     */
    public function send_mail($email,$title,$data,$attachment = null){
        $interface_config = get_option('interface_config');
        $config = $interface_config['smtp'];
        if(empty($config)) wp_send_json_error(array('info'=>'邮件接口未配置'));

        if(!is_file(PLUGINS_PATH.'nlyd-student/library/Vendor/PHPMailer/class.phpmailer.php')) wp_send_json_error(array('info'=>'找不到邮件接口文件'));
        include_once (PLUGINS_PATH.'nlyd-student/library/Vendor/PHPMailer/class.phpmailer.php');
        include_once (PLUGINS_PATH.'nlyd-student/library/Vendor/SMTP.php');
        /*ini_set("display_errors","On");
        error_reporting(E_ALL);*/

        $mail = new PHPMailer(); //PHPMailer对象

        $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码

        $mail->IsSMTP(); // 设定使用SMTP服务

        $mail->SMTPDebug = 0; // 关闭SMTP调试功能

        // 1 = errors and messages

        // 2 = messages only
        //print_r($config);die;
        $mail->SMTPAuth = true; // 启用 SMTP 验证功能

        $mail->SMTPSecure = 'ssl'; // 使用安全协议

        $mail->Host = $config['host']; // SMTP 服务器

        $mail->Port = $config['port']; // SMTP服务器的端口号

        $mail->Username = $config['user_name']; // SMTP服务器用户名

        $mail->Password = !empty($config['user_warrant']) ? $config['user_warrant'] : $config['user_pass']; // SMTP服务器密码

        $mail->SetFrom($config['from_email'], $config['from_name']);

        $replyEmail = $config['reply_email']?$config['reply_email']:$config['from_email'];

        $replyName = $config['reply_name']?$config['reply_name']:$config['from_name'];

        $mail->AddReplyTo($replyEmail, $replyName);

        $smtp = new \library\Smtp();
//        $result = $smtp->get_smtp_template($data,$template);

        $mail->Subject = $title;

        $mail->AltBody = "为了查看该邮件，请切换到支持 HTML 的邮件客户端";

        $mail->MsgHTML($data);    //发送内容

        $mail->AddAddress($email, $config['from_name']);

        if(is_array($attachment)){ // 添加附件

            foreach ($attachment as $file){

                is_file($file) && $mail->AddAttachment($file);

            }

        }

        return $mail->Send() ? true : $mail->ErrorInfo;

    }


    /**
     * 新增比赛项目轮数
     */
    public function match_more_add(){

        if(empty($_POST['post_id']) || empty($_POST['project_id']) || empty($_POST['start_time'])){
            wp_send_json_error('请确认必填项是否为空');
        }

        //计算结束时间
        if(!empty($_POST['end_time'])){

            $end_time = $_POST['end_time'];
            $use_time = (strtotime($end_time)-strtotime($_POST['start_time']))/60;
        }
        elseif (!empty($_POST['use_time'])){
            $use_time = $_POST['use_time'];
            $end_time = date_i18n('Y-m-d H:i:s',strtotime($_POST['start_time']) + $_POST['use_time']*60);

        }else{

            //获取比赛时长
            $match_project = get_option('match_project_use')['project_use'];
            $alias = get_post_meta($_POST['project_id'],'project_alias')[0];
            if($alias == 'zxss'){
                $use_time = array_sum(array_values($match_project[$alias]));
            }else{

                $use_time = $match_project[$alias];
            }
            $end_time = date_i18n('Y-m-d H:i',strtotime($_POST['start_time']) + $use_time*60);
        }

        //查询当前该是第几轮
        global $wpdb,$current_user ;
        $sql = "select count(id) total from {$wpdb->prefix}match_project_more where match_id = {$_POST['post_id']} and project_id = {$_POST['project_id']} ";
        $total = $wpdb->get_var($sql);
        //var_dump($total);die;

        $array = array(
            'match_id'=>$_POST['post_id'],
            'project_id'=>$_POST['project_id'],
            'start_time'=>$_POST['start_time'],
            'end_time'=>$end_time,
            'use_time'=>$use_time,
            'status'=>empty($_POST['status']) ? 1 : $_POST['status'],

        );

        //$wpdb->query('START TRANSACTION');

        if(!empty($_POST['more_id'])){  //修改
            $match_more = $wpdb->get_var("select `more` from {$wpdb->prefix}match_project_more where id = {$_POST['more_id']} ");
            unset($array['more']);
            $array['more'] = $match_more;
            $array['revise_id'] = $current_user->ID;
            $array['revise_time'] = get_time('mysql');

            $result = $wpdb->update($wpdb->prefix.'match_project_more',$array,array('id'=>$_POST['more_id']));
            $title = '编辑';
        }
        else{   //新增
            $array['created_id'] = $current_user->ID;
            $array['more'] = $total+1;
            $array['created_time'] = get_time('mysql');
            $result = $wpdb->insert($wpdb->prefix.'match_project_more',$array);
            $title = '新增';
        }

        //对当前项目进行重新排序
        $this->project_more_sort($_POST['post_id'],$_POST['project_id']);

        $sql = "select id,start_time,end_time from {$wpdb->prefix}match_project_more where match_id = {$_POST['post_id']} order by end_time asc";
        $results = $wpdb->get_results($sql,ARRAY_A);
        //print_r($results);die;
        if(!empty($results)){
            $start_time = $results[0]['start_time'];
            $end_time =  end($results)['end_time'];
        }

        $a = $wpdb->update($wpdb->prefix.'match_meta_new',array('match_start_time'=>$start_time,'match_end_time'=>$end_time),array('match_id'=>$_POST['post_id']));
        if($result){
            //$wpdb->query('COMMIT');
            wp_send_json_success($title.'成功');
        }else{
            //$wpdb->query('ROLLBACK');
            wp_send_json_error($title.'失败');
        }
        //var_dump($_POST);
        die;
    }

    /**
     * 对比赛轮数进行重新排序
     */
    public function project_more_sort($match_id,$project_id){
        global $wpdb;
        $projects = $wpdb->get_results("select * from {$wpdb->prefix}match_project_more where match_id = {$match_id} and project_id = {$project_id} order by start_time asc",ARRAY_A);
        if(!empty($projects)){
            foreach ($projects as $k => $v){
                ///print_r($v);
                $wpdb->update($wpdb->prefix.'match_project_more',array('more'=>$k+1),array('id'=>$v['id']));
            }
        }
        //print_r($projects);die;
    }


    /**
     * 删除
     */
    public function remove_match_more(){
        if(empty($_POST['id'])) wp_send_json_error('请选择需要删除的数据');
        global $wpdb;
        if(is_array($_POST['id'])){
            $id = arr2str($_POST['id']);
        }else{
            $id = $_POST['id'];
            $row = $wpdb->get_row("select * from {$wpdb->prefix}match_project_more where id={$id} ",ARRAY_A);
            //print_r($row);die;
        }

        $sql = "DELETE FROM `{$wpdb->prefix}match_project_more` WHERE id in({$id}) ";
        //print_r($sql);
        $result = $wpdb->query($sql);
        if($result){
            //对当前项目进行重新排序
            $this->project_more_sort($row['match_id'],$row['project_id']);

            wp_send_json_success('删除成功');
        }else{
            wp_send_json_error('删除失败');
        }
    }

    /**
     * 开启关闭比赛奖金明细前台显示
     */
    public function matchBonusUserView(){
        $match_id = intval($_POST['match_id']);
        if($match_id < 1) wp_send_json_error(['info' => '参数错误']);
        $is_view = intval($_POST['is_view']);
        if($is_view != 1 && $is_view != 2) wp_send_json_error(['info' => '参数错误']);
        global $wpdb;
        $updateBool = $wpdb->update($wpdb->prefix.'match_bonus', ['is_user_view' => $is_view], ['match_id' => $match_id]);
        if(!$updateBool){
            $id = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}match_bonus WHERE match_id={$match_id}");
            if($id) wp_send_json_error(['info' => '修改失败']);
        }
        wp_send_json_success(['info' => '修改成功']);
    }

    /**
     * 比赛奖金发放状态修改
     */
    public function update_send_bonus(){
        $user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
        $match_id = isset($_POST['match_id']) ? intval($_POST['match_id']) : 0;
        $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
        if($user_id == '' || $match_id < 1 || ($status !=2 && $status != 1)) wp_send_json_error(['info' => '参数错误']);
        global $wpdb;
        $send_time = $status === 2 ? get_time('mysql') : '';
        $sql = "UPDATE {$wpdb->prefix}match_bonus SET is_send='{$status}',`send_time`='{$send_time}' WHERE match_id='{$match_id}' AND user_id IN ({$user_id})";
//        $bool = $wpdb->update($wpdb->prefix.'match_bonus', ['is_send' => $status], ['user_id'=>$user_id,'match_id'=>$match_id]);
        $bool = $wpdb->query($sql);
        if($bool) wp_send_json_success(['info'=>'修改成功']);
        else wp_send_json_error(['info' => '修改失败']);
    }

    /**
     * 根据搜索信息查询用户
     *
     */
    public function getUserBySearch(){
        $val = isset($_POST['val']) ? trim($_POST['val']) : '';
        $type = isset($_POST['type']) ? trim($_POST['type']) : '';
        if($val == '' || $type == '') wp_send_json_error(['info' => '参数错误']);

        global $wpdb;
        $join = '';
        $where = '';
        switch ($type){
            case 'team':
//                $join = " RIGHT JOIN {$wpdb->prefix}match_team AS mt ON u.ID=mt.user_id";
//                $where = ' AND mt.id=NULL';
                break;
        }
//mt.status!=2 AND mt.status!=-1 AND mt.status!=1 OR
        $result = $wpdb->get_results("SELECT  u.user_mobile,u.ID AS user_id,u.user_email,um1.meta_value AS user_real_name,um2.meta_value AS userID FROM {$wpdb->users} AS u 
        LEFT JOIN {$wpdb->usermeta} AS um1 ON um1.user_id=u.ID AND um1.meta_key='user_real_name' 
        LEFT JOIN {$wpdb->usermeta} AS um2 ON um2.user_id=u.ID AND um2.meta_key='user_ID' 
        {$join}
        WHERE (u.user_mobile LIKE '%{$val}%' OR u.user_email LIKE '%{$val}%' OR um1.meta_value LIKE '%{$val}%' OR um2.meta_value LIKE '%{$val}%'){$where}", ARRAY_A);
        if($result){
            foreach ($result as &$res){
                if($res['user_real_name']){
                    $res['user_real_name'] = unserialize($res['user_real_name']);
                }else{
                    $res['user_real_name']['real_name'] = '';
                    $res['user_real_name']['real_ID'] = '';
                    $res['user_real_name']['real_type'] = '';
                    $res['user_real_name']['real_age'] = '';
                }
            }
            wp_send_json_success(['info' => $result]);
        }else{
            wp_send_json_error(['info' => '无数据']);
        }
    }

    /**
     * 后台手动添加战队成员
     */
    public function addTeamMember(){
        $team_id = isset($_POST['team_id']) ? intval($_POST['team_id']) : 0;
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        if($user_id < 1 || $team_id < 1) wp_send_json_error(['info' => '参数错误']);
        global $wpdb;
        $teamUser = $wpdb->get_var("select id from {$wpdb->prefix}match_team where user_id = {$user_id} and user_type = 1 and (status=2 or status=1 or status=-1)");
        if($teamUser) wp_send_json_error(['info' => '该用户已有战队或正在退入队申请']);
        $id = $wpdb->get_var("select id from {$wpdb->prefix}match_team where team_id = {$team_id} and user_id = {$user_id} and user_type = 1");

        if(empty($id)){
            $result = $wpdb->insert($wpdb->prefix.'match_team',array('team_id'=>$team_id,'user_id'=>$user_id,'user_type'=>1,'status'=>2,'created_time'=>get_time('mysql')));
        }else{
            $result = $wpdb->update($wpdb->prefix.'match_team',array('status'=>2,'created_time'=>get_time('mysql')),array('id'=>$id,'team_id'=>$team_id,'user_id'=>$user_id));
        }
        if($result) wp_send_json_success(['info' => '添加成功']);
        else wp_send_json_error(['info' => '添加失败']);
    }
    /**
     * 搜索战队
     */
    public function getTeamSearch(){
        $val = isset($_POST['val']) ? trim($_POST['val']) : '';
        $team_id = isset($_POST['team_id']) ? intval($_POST['team_id']) : 0;
        if($val == '') wp_send_json_error(['info' => '请输入搜索内容']);
        $where = '';
        if($team_id > 0) $where = ' AND ID NOT IN('.$team_id.')';
        global $wpdb;
        $result = $wpdb->get_results("SELECT ID,post_title FROM {$wpdb->posts} WHERE post_title LIKE '%{$val}%' AND post_parent=0 AND post_type='team' AND post_status!='trash'{$where}");
        if($result){
            wp_send_json_success(['info' => $result]);
        }else{
            wp_send_json_error(['info' => '找不到战队']);
        }
    }

    /**
     * 更新奖金明细奖金设置
     */
    public function updateBonusTmp(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $project1 = isset($_POST['project1']) ? intval($_POST['project1']) : 0;
        $project2 = isset($_POST['project2']) ? intval($_POST['project2']) : 0;
        $project3 = isset($_POST['project3']) ? intval($_POST['project3']) : 0;
        $category1 = isset($_POST['category1']) ? intval($_POST['category1']) : 0;
        $category2 = isset($_POST['category2']) ? intval($_POST['category2']) : 0;
        $category3 = isset($_POST['category3']) ? intval($_POST['category3']) : 0;
        $category_excellent = isset($_POST['category_excellent']) ? intval($_POST['category_excellent']) : 0;
        $category1_age = isset($_POST['category1_age']) ? intval($_POST['category1_age']) : 0;
        $category2_age = isset($_POST['category2_age']) ? intval($_POST['category2_age']) : 0;
        $category3_age = isset($_POST['category3_age']) ? intval($_POST['category3_age']) : 0;
        $tmp_name = isset($_POST['tmp_name']) ? trim($_POST['tmp_name']) : '';
        if($tmp_name==''){
            wp_send_json_error(['info' => '奖金金额不能小于1,名称必填']);
        }
        global $wpdb;
        if($id < 1){
            $sql = "INSERT INTO {$wpdb->prefix}match_bonus_tmp (`project1`,`project2`,`project3`,`category1`,`category2`,`category3`,`category_excellent`,`category1_age`,`category2_age`,`category3_age`,`bonus_tmp_name`) 
            VALUES ('{$project1}','{$project2}','{$project3}','{$category1}','{$category2}','{$category3}','{$category_excellent}','{$category1_age}','{$category2_age}','{$category3_age}','{$tmp_name}')";
        }else{
            $sql = "UPDATE {$wpdb->prefix}match_bonus_tmp SET `project1`='{$project1}',`project2`='{$project2}',`project3`='{$project3}',
            `category1`='{$category1}',`category2`='{$category2}',`category3`='{$category3}',`category_excellent`='{$category_excellent}',
            `category1_age`='{$category1_age}',`category2_age`='{$category2_age}',`category3_age`='{$category3_age}',`bonus_tmp_name`='{$tmp_name}' 
            WHERE `id`={$id}";
        }
        $bool = $wpdb->query($sql);
        if($bool) wp_send_json_success(['info' => '操作成功']);
        else wp_send_json_error(['info' => '操作失败']);
    }

    /**
     * 删除奖金明细设置
     */
    public function delBonusTmp(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if($id < 1)  wp_send_json_error(['info' => '参数错误']);
        global $wpdb;
        $bool = $wpdb->delete($wpdb->prefix.'match_bonus_tmp',['id'=>$id]);
        if($bool) wp_send_json_success(['info' => '删除成功']);
        else wp_send_json_error(['info' => '删除失败']);
    }

    /**
     * 获取监赛单条记录
     */
    public function get_prison_row(){
        global $wpdb;
        $row = $wpdb->get_row("select * from {$wpdb->prefix}prison_match_log where id = {$_POST['id']}",ARRAY_A);
        if(empty($row)) wp_send_json_error('数据错误');
        if(!empty($row['evidence'])){
            $row['evidence'] = json_decode($row['evidence'],true);
        }
        wp_send_json_success($row);
    }

    /**
     * 编辑监赛记录
     */
    public function save_prison(){
        global $current_user,$wpdb;

        if(empty($_POST['seat_number'])) wp_send_json_error(array('info'=>'座位号必填'));
        if(isset($_POST['id']) && !empty($_POST['id'])){
            if(empty($_POST['evidence'])) wp_send_json_error(array('info'=>'佐证照不能为空'));
        }
        else{
            if(empty($_FILES['evidence'])) wp_send_json_error(array('info'=>'佐证照不能为空'));
            //获取当前比赛项目
            $sql = "select match_id,project_id,more,start_time,end_time from {$wpdb->prefix}match_project_more ";
            $rows = $wpdb->get_results($sql,ARRAY_A);
            if(!empty($rows)){
                $new_time = get_time('mysql');
                $match = array();
                foreach ($rows as $val){
                    if($val['start_time'] < $new_time && $new_time < $val['end_time']){
                        $match = $val;
                    }
                }
            }
            if(empty($match)) wp_send_json_error(array('info'=>'当前时间段无比赛'));
            //获取所有报名信息
            $sql = "select user_id from {$wpdb->prefix}order where match_id = {$match['match_id']} and pay_status in(2,3,4) order by id desc";
            $results = $wpdb->get_results($sql,ARRAY_A);
            $index = $_POST['seat_number']-1;
            $user_id = $results[$index]['user_id'];
            if(empty($user_id)) wp_send_json_error(array('info'=>'座位信息错误'));
        }


        if(empty($_POST['student_name'])){
            $user_real_name = get_user_meta($user_id,'user_real_name')[0];
            $real_name = $user_real_name['real_name'];
        }else{
            $real_name = $_POST['student_name'];
        }

        if(isset($_FILES['evidence'])){

            $upload_dir = wp_upload_dir();
            $dir = '/evidence/';

            $num = 0;
            foreach ($_FILES['evidence']['tmp_name'] as $upd){
                //print_r($upd);
                $file = $this->saveIosFile($upd,$upload_dir['basedir'].$dir);

                if($file){
                    $_POST['evidence'][] = $upload_dir['baseurl'].$dir.$file;
                    ++$num;
                }
            }
        }

        if(isset($_POST['id']) && !empty($_POST['id'])){
            $update = array(
                'supervisor_id'=>$current_user->ID,
                'student_name'=>$real_name,
                'seat_number'=>$_POST['seat_number'],
                'evidence'=>!empty($_POST['evidence']) ? json_encode($_POST['evidence']) : '',
                'describe'=>!empty($_POST['describe']) ? $_POST['describe'] : '',
            );

            $result = $wpdb->update($wpdb->prefix.'prison_match_log',$update,array('id'=>$_POST['id']));
            //print_r($result);die;
        }else{
            $insert = array(
                'supervisor_id'=>$current_user->ID,
                'match_id'=>$match['match_id'],
                'project_id'=>$match['project_id'],
                'match_more'=>$match['more'],
                'match_id'=>$match['match_id'],
                'student_name'=>$real_name,
                'seat_number'=>$_POST['seat_number'],
                'evidence'=>!empty($_POST['evidence']) ? json_encode($_POST['evidence']) : '',
                'describe'=>!empty($_POST['describe']) ? $_POST['describe'] : '',
                'created_time'=>get_time('mysql'),
            );
            $result = $wpdb->insert($wpdb->prefix.'prison_match_log',$insert);
        }
        if($result){
            wp_send_json_success(array('info'=>'编辑成功'));
        }else{
            wp_send_json_error(array('info'=>'编辑失败'));
        }
    }

    /**
     * 删除监赛记录
     */
    public function remove_prison_log(){
        global $wpdb;

        $row = $wpdb->get_row("select * from {$wpdb->prefix}prison_match_log where id = {$_POST['id']}",ARRAY_A);
        if(empty($row)) wp_send_json_error(array('数据错误'));

        $result = $wpdb->delete($wpdb->prefix.'prison_match_log',array('id'=>$_POST['id']));

        $b = $wpdb->update($wpdb->prefix.'match_questions',array('is_true'=>1),array('match_id'=>$row['match_id'],'project_id'=>$row['project_id'],'match_more'=>$row['match_more'],'user_id'=>$row['user_id']));
        if($result){
            wp_send_json_success('删除成功');
        }else{
            wp_send_json_error('删除失败');
        }
    }

    /**
     * ios关联上传
     * 一般文件流上传
     *@param  $filecontent 文件流
     *@param  $path         文件保存目录
     *
     */
    public function saveIosFile($filecontent,$upload_dir){

        if(empty($filecontent)) wp_send_json_error(array('info'=>__('数据错误', 'nlyd-student')));
        //$base64 = htmlspecialchars($filecontent);
        //$fileName = iconv ( "UTF-8", "GB2312", $filecontent );

        $filename = date('YmdHis').'_'.rand(1000,9999).'.jpg';          //定义图片名字及格式

        if(!file_exists($upload_dir)){
            mkdir($upload_dir,0755,true);
        }
        $savepath = $upload_dir.'/'.$filename;
        // $this->apiReturn(4001,$savepath);
        if (move_uploaded_file($filecontent, $savepath)) {
            return $filename;
        }else{
            return false;
        }
    }

    /**
     * 考级相关文件上传
     */
    public function grading_content_upload(){

        if(empty($_FILES['file']['tmp_name'])) wp_send_json_error();

        switch ($_POST['memory_type']){
            case 'vocabulary':
                $file_path = leo_match_path.'/upload/vocabulary';
                if(!file_exists($file_path)){
                    mkdir($file_path,0755,true);
                }
                $result = move_uploaded_file($_FILES['file']['tmp_name'][0],$file_path.'/string.txt');
                if($result){

                    $str = mb_convert_encoding(file_get_contents($file_path.'/string.txt'), "UTF-8", "GBK");
                    $json = json_encode(str2arr($str,' '));

                    if($_POST['handle'] == 1 && file_exists($file_path.'/vocabulary.json')){

                        $array = json_decode(file_get_contents($file_path.'/vocabulary.json'),true);
                        $new_ = array_unique(array_merge(str2arr($str,' '),$array));
                        $a = file_put_contents($file_path.'/vocabulary.json',$new_);
                    }else{
                        $a = file_put_contents($file_path.'/vocabulary.json',$json);
                    }
                    //print_r($a);die;
                }
                break;
            case 'book':
                if(empty($_POST['memory_grade'])) wp_send_json_error('请选择记忆等级');
                $file_path = leo_match_path.'/upload/book';
                if(!file_exists($file_path)){
                    mkdir($file_path,0755,true);
                }
                /*$a = json_decode(file_get_contents($file_path.'/memory1.json'),true);
                print_r($a);die;*/
                //中转文件
                $result = move_uploaded_file($_FILES['file']['tmp_name'][0],$file_path.'/temporary.txt');
                /*$str = file_get_contents($file_path.'/temporary.txt');

                $str = preg_replace('# #','',mb_convert_encoding(file_get_contents($file_path.'/temporary.txt'), "UTF-8", "GBK"));
                $arr[10] = $str;
                $a = file_put_contents($file_path.'/memory1.json',json_encode($arr));
                print_r($str);
                //preg_replace('# #','',$goodid)
                die;*/
                if($result){
                    $str = preg_replace('# #','',mb_convert_encoding(file_get_contents($file_path.'/temporary.txt'), "UTF-8", "GBK"));
                    //$str = file_get_contents($file_path.'/temporary.txt');
                    //print_r($str);
                    $array = array();
                    if($_POST['handle'] == 1 && isset($array[$_POST['memory_grade']])){

                        //leo_dump($array);
                        $array[$_POST['memory_grade']] .= $str;
                        //leo_dump($array);die;
                    }else{

                        $array[$_POST['memory_grade']] = $str;
                    }
                    ksort($array);
                    /*leo_dump($array);
                    die;*/

                    if(file_exists($file_path.'/memory.json')){
                        $array = json_decode(file_get_contents($file_path.'/memory.json'),true);
                    }else{
                        $array = array();
                    }
                    //leo_dump($array);
                    if($_POST['handle'] == 1 && isset($array[$_POST['memory_grade']])){

                        //leo_dump($array);
                        $array[$_POST['memory_grade']] .= $str;
                        //leo_dump($array);die;
                    }else{

                        $array[$_POST['memory_grade']] = $str;
                    }
                    ksort($array);
                    /*leo_dump($array);
                    die;*/
                    $a = file_put_contents($file_path.'/memory.json',json_encode($array));
                    //print_r($a);die;
                }

                break;
            case 'people':
                if(count($_FILES['file']['name']) > 20){
                    wp_send_json_error('每次上传不能超过20张照片');
                }
                $file_path = leo_match_path.'/upload/people';
                if(!file_exists($file_path)){
                    mkdir($file_path,0755,true);
                }
                if($_POST['handle'] == 2 && file_exists($file_path)){
                    //刪除目录及文件
                    $this->removeDir($file_path);

                    mkdir($file_path,0755,true);

                }

                $success = 0;
                foreach ($_FILES['file']['tmp_name'] as $k => $v){

                    if(move_uploaded_file($v, $file_path.'/'.$_FILES['file']['name'][$k])){
                        $success ++;
                    }else{
                        $error = $success+1;
                    }
                }

                if($success == count($_FILES['file']['tmp_name'])){
                    $a = true;
                }else{
                    wp_send_json_error('第'.$error.'张未上传成功');
                }
                break;
            default:
                wp_send_json_error('请选择类别');
                break;
        }

        if($a){
            wp_send_json_success('上传成功');
        }else{
            wp_send_json_error('上传失败');
        }
    }

    /**
     * 删除非空目录的解决方案
     * @param $dirName
     * @return bool
     */
    public function removeDir($dirName)
    {
        if(! is_dir($dirName))
        {
            return false;
        }
        $handle = @opendir($dirName);
        while(($file = @readdir($handle)) !== false)
        {
            if($file != '.' && $file != '..')
            {
                $dir = $dirName . '/' . $file;
                is_dir($dir) ? removeDir($dir) : @unlink($dir);
            }
        }
        closedir($handle);

        return rmdir($dirName) ;
    }

    /**
     * 删除订单
     */
    public function closeOrder(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if($id < 1) wp_send_json_error(['info' => '参数错误!']);

        global $wpdb;
        $var = $wpdb->get_var("SELECT id FROM `{$wpdb->prefix}order` WHERE id='{$id}'");
        if(!$var) wp_send_json_error(['info' => '订单已删除']);

        $bool = $wpdb->delete($wpdb->prefix.'order',['id'=>$id]);
        if($bool)  wp_send_json_success(['info' => '删除成功']);
        else wp_send_json_error(['info' => '删除失败']);
    }

    /**
     * 加入考级选手
     */
    public function joinGradingMember(){
        $grading_id = isset($_POST['grading_id']) ? intval($_POST['grading_id']) : 0;
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        if($grading_id < 1 || $user_id < 1) wp_send_json_error(['info' => '参数错误!']);
//        $sub_centres_id = isset($_POST['sub_centres_id']) ? intval($_POST['sub_centres_id']) : 0;
//        if($sub_centres_id < 1) wp_send_json_error(array('info'=>'请选择考级机构!'));
        global $wpdb;
        //是否已存在订单
        $id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}order WHERE match_id='{$grading_id}' AND user_id='{$user_id}' AND order_type=2 AND pay_status IN(2,3,4)");
        if($id) wp_send_json_error(['info' => '该用户已存在考级,请勿重复加入!']);

        //是否是记忆类
        $cate = $wpdb->get_var("SELECT p.post_title FROM `{$wpdb->prefix}grading_meta` AS gm 
            LEFT JOIN `{$wpdb->posts}` AS p ON p.ID=gm.category_id 
            WHERE gm.grading_id='{$grading_id}'");
        $memory_lv = null;
        if(preg_match('/记忆/',$cate) || preg_match('/速记/',$cate)){
            $memory_lv = isset($_POST['lv']) ? intval($_POST['lv']) : 0;
            if($memory_lv < 1) wp_send_json_error(['info' => '请输入正确记忆等级!']);
        }

        $orderInsertData = [
            'user_id' => $user_id,
            'match_id'=>$grading_id,
            'cost'=> 0,
//            'fullname'=>$addressRes['fullname'],
//            'telephone'=>$addressRes['telephone'],
//            'address'=>$addressRes['country'].$addressRes['province'].$addressRes['city'].$addressRes['area'].$addressRes['address'],
            'order_type'=>2,
            'pay_status'=>4,
//            'sub_centres_id'=>$sub_centres_id,
            'created_time'=>get_time('mysql'),
            'memory_lv' => $memory_lv
        ];


        //开启事务
        $wpdb->query('START TRANSACTION');
        $insertRes = $wpdb->insert($wpdb->prefix.'order',$orderInsertData);

        if(!$insertRes){
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '加入失败!']);
            return;
        }
        //生成流水号
        $serialnumber = createNumber($user_id,$wpdb->insert_id);
        $updateRes = $wpdb->update($wpdb->prefix.'order',array('serialnumber'=>$serialnumber),array('id'=>$wpdb->insert_id));
        if($updateRes){
            if(insertIncomeLogs($orderInsertData)){
                $wpdb->query('COMMIT');
                wp_send_json_success(['info' => '加入成功!']);
            }else{
                $wpdb->query('ROLLBACK');
                wp_send_json_error(['info' => '加入失败!']);
            }
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '加入失败!']);
        }
    }

    /**
     * 删除考级
     */
    public function deleteGrading(){
        $id = intval($_POST['id']);
        //删除订单 meta 分数


        if($id < 1) wp_send_json_error(['info' => '参数错误']);
        //判断是否是已关闭的比赛(回收站中的)
        $post = get_post($id);
        if($post->post_status == 'trash'){
            global $wpdb;
            $wpdb->query('START TRANSACTION');
            //删除post
            if(!wp_delete_post($id)){
                $wpdb->query('ROLLBACK');
                wp_send_json_error(['info' => '考级删除失败']);
            }
            //删除meta
            $meta = $wpdb->get_row("SELECT grading_id FROM {$wpdb->prefix}grading_meta WHERE grading_id={$id}");
            if($meta){
                $metaBool = $wpdb->delete($wpdb->prefix.'grading_meta',['grading_id' => $id]);
                if(!$metaBool){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => '考级外键删除失败']);
                }
            }
            //删除订单
            $order = $wpdb->get_row("SELECT match_id FROM {$wpdb->prefix}order WHERE match_id={$id} AND order_type=2");
            if($order){
                $orderBool = $wpdb->update($wpdb->prefix.'order', ['pay_status' => 5], ['match_id' => $id, 'order_type' => 2]);
                if(!$orderBool){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => '订单删除失败']);
                }
            }
            //删除答题记录
            $question = $wpdb->get_row("SELECT grading_id FROM {$wpdb->prefix}grading_questions WHERE grading_id={$id}");
            if($question){
                $questionBool = $wpdb->delete($wpdb->prefix.'grading_questions', ['grading_id' => $id]);
                if(!$questionBool){
                    $wpdb->query('ROLLBACK');
                    wp_send_json_error(['info' => '答题记录删除失败']);
                }
            }
            $wpdb->query('COMMIT');
            wp_send_json_success(['info' => '考级已删除']);

        }else{
            wp_send_json_error(['info' => '请先关闭考级']);
        }
    }

    /**
     * 修改奖金发放类型
     */
    public function adminEditBonusSendType(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $val = isset($_POST['val']) ? trim($_POST['val']) : '';
        $id < 1 && wp_send_json_error(['info' => '参数错误']);
       global $wpdb;
        //查询原数据
        $row = $wpdb->get_row("SELECT id,collect_name FROM `{$wpdb->prefix}match_bonus` WHERE `id`='{$id}'",ARRAY_A);
        if(!$row) wp_send_json_error(['info' => '参数错误']);
        $bool = $wpdb->update($wpdb->prefix.'match_bonus',['collect_name' => $val], ['id'=>$id]);
        if($bool) wp_send_json_success(['info'=>'修改成功']);
        else wp_send_json_error(['info' => '修改失败', 'data' => $row['collect_name']]);
    }

    /**
     * 修改用户资料获取战队列表
     */
    public function get_team_list(){
        $searchStr = isset($_GET['term']) ? trim($_GET['term']) : '';
        global $wpdb;
        if($searchStr == ''){
//            $rows = $wpdb->get_results("SELECT ID AS id,post_title AS text FROM {$wpdb->posts} WHERE post_title LIKE '%{$searchStr}%' AND post_parent=0 AND post_type='team' AND post_status!='trash' LIMIT 0,20",ARRAY_A);
            $rows = [];
        }else{
             $id = isset($_GET['type']) ? intval($_GET['type']) : 0;
             $rows = $wpdb->get_results("SELECT ID AS id,post_title AS text FROM {$wpdb->posts} WHERE post_title LIKE '%{$searchStr}%' AND post_parent=0 AND post_type='team' AND post_status!='trash' AND ID!='{$id}'",ARRAY_A);

        }
        wp_send_json_success($rows);
    }

    /**
     * 解绑用户微信
     */
    public function relieveWechat(){
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $user_id < 1 && wp_send_json_error(['info' => '参数错误!']);

        global $wpdb;
        $wpdb->query('START TRANSACTION');
        $bool = $wpdb->update($wpdb->users,['weChat_openid' => ''],['ID'=>$user_id]);
        if($bool){
            if(!delete_user_meta($user_id,'wechat_nickname') && get_user_meta($user_id,'wechat_nickname')){
                $wpdb->query('ROLLBACK');
                wp_send_json_error(['info' => '解绑失败!']);
            };
            $wpdb->query('COMMIT');
            wp_send_json_success(['info' => '解绑成功!']);
        }else{
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['info' => '解绑失败!']);
        }
    }

    /**
     * 添加订单时搜索参赛机构
     */
    public function getCentresList(){
        global $wpdb;
        $s = isset($_GET['term']) ? trim($_GET['term']) : '';
        if($s != ''){
            $rows = $wpdb->get_results("SELECT tm.team_id AS id,p.post_title AS text FROM {$wpdb->prefix}team_meta AS tm 
                    LEFT JOIN {$wpdb->posts} AS p ON p.ID=tm.team_id 
                    WHERE p.post_title LIKE '%{$s}%'");
            if($rows) wp_send_json_success($rows);
        }
    }

    /**
     * 删除用户证件照片
     */
    public function delCardImg(){
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $k = isset($_POST['k']) ? intval($_POST['k']) : 0;
        if($user_id < 1 || $k < 1) wp_send_json_error(['info' => '参数错误']);
        $cardOldImg = get_user_meta($user_id, 'user_ID_Card', true);
        $old = $cardOldImg[$k];
        unset($cardOldImg[$k]);
        $bool = update_user_meta($user_id,'user_ID_Card',$cardOldImg);
        if($bool){
            //删除图片
            $filePa = explode('uploads',$old);
            if(is_file(wp_upload_dir()['basedir'].$filePa[1])) unlink(wp_upload_dir()['basedir'].$filePa[1]);
            wp_send_json_success(['info' => '删除成功!']);
        }else{
            wp_send_json_error(['info' => '删除失败!']);
        }
    }
    /**
     * 删除用户寸照
     */
    public function delImagesColor(){
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $k = isset($_POST['k']) ? intval($_POST['k']) : 0;
        if($user_id < 1 || $k < 0) wp_send_json_error(['info' => '参数错误']);
        $cardOldImg = get_user_meta($user_id, 'user_images_color', true);
        $old = $cardOldImg[$k];
        unset($cardOldImg[$k]);
        $bool = update_user_meta($user_id,'user_images_color',$cardOldImg);
        if($bool){
            //删除图片
            $filePa = explode('uploads',$old);
            if(is_file(wp_upload_dir()['basedir'].$filePa[1])) unlink(wp_upload_dir()['basedir'].$filePa[1]);
            wp_send_json_success(['info' => '删除成功!']);
        }else{
            wp_send_json_error(['info' => '删除失败!']);
        }
    }
    /**
     * 获取机构的教练
     */
    public function get_zone_coach(){
        $zone_user_id = isset($_GET['type']) ? intval($_GET['type']) : 0;
        $s = isset($_GET['term']) ? trim($_GET['term']) : '';
//        if($s != '' && $zone_user_id > 0){
            global $wpdb;
            $rows = $wpdb->get_results("SELECT zjc.coach_id AS id,um.meta_value FROM {$wpdb->prefix}zone_join_coach AS zjc 
                    LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=zjc.coach_id AND um.meta_key='user_real_name'
                    WHERE zjc.zone_id='{$zone_user_id}' AND zjc.coach_id!=''");
            foreach ($rows as &$row){
                $row->text = unserialize($row->meta_value)['real_name'];
            }
            wp_send_json_success($rows);
//        }
    }

    /**
     * 确认考级证书发放状态
     */
    public function gradingProveGrant(){
        $id = $_POST['id'] ? intval($_POST['id']) : 0;
        $number = $_POST['number'] ? trim($_POST['number']) : '';
        $type = $_POST['type'] ? trim($_POST['type']) : '';
        ($id < 1 || $number == '') && wp_send_json_error(['info' => '参数错误!']);
        if($type == 'edit'){
            $arr = ['prove_number' => $number];
        }elseif($type == 'add'){
            $arr = ['prove_grant_status' => 2, 'prove_number' => $number, 'prove_grant_time' => get_time('mysql')];
        }else{
            wp_send_json_error(['info' => '参数错误!']);
        }
        global $wpdb;
        $var = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}grading_logs WHERE prove_number='{$number}'");
        if($var) wp_send_json_error(['info' => '证书编号已存在!']);
        $bool = $wpdb->update($wpdb->prefix.'grading_logs', $arr, ['id' => $id]);
        if($bool) wp_send_json_success(['info' => '操作成功!']);
        else wp_send_json_error(['info' => '操作失败!']);
    }

    /**
     * 搜索所有用户
     */
    public function search_all_user(){
        $s = isset($_GET['term']) ? trim($_GET['term']) : '';
        if($s != ''){
            global $wpdb;
            $res = $wpdb->get_results("SELECT u.ID AS id,um.meta_value FROM {$wpdb->users} AS u 
                   LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=u.ID AND um.meta_key='user_real_name'
                   LEFT JOIN {$wpdb->usermeta} AS um2 ON um2.user_id=u.ID AND um2.meta_key='user_ID'
                   WHERE um.meta_value LIKE '%{$s}%' OR um2.meta_value LIKE '%{$s}%' OR u.user_mobile LIKE '%{$s}%'");
            $rows = [];
            foreach ($res AS $r){
                if($r->meta_value){
                    $rows[] = [
                        'id' => $r->id,
                        'text' => unserialize($r->meta_value)['real_name']
                    ];
                }
            }
            wp_send_json_success($rows);
        }
    }

    /**
     * 添加脑力健将死数据
     */
    public function addBrainMoreData(){
        $range = isset($_POST['range_add']) ? intval($_POST['range_add']) : 0;
        $level = isset($_POST['level_add']) ? intval($_POST['level_add']) : 0;
        $cate_id = isset($_POST['cate_add']) ? intval($_POST['cate_add']) : 0;
        $age = isset($_POST['age_add']) ? intval($_POST['age_add']) : 0;
        $real_name = isset($_POST['real_name_add']) ? trim($_POST['real_name_add']) : '';
        $sex = isset($_POST['sex_add']) ? trim($_POST['sex_add']) : '';
        $nationality = isset($_POST['nationality_add']) ? trim($_POST['nationality_add']) : '';
        if($level < 1 || !in_array($range,[1,2]) || $real_name == '' || $sex == '' || $nationality == '' || $cate_id < 1){
            wp_send_json_error(['info' => '数据不完整!']);
        }
        $staticPath = PLUGINS_PATH.'nlyd-student/view/directory/static/';
        $dataFileName = 'brainMoreData.json';
        $dataFilePath = $staticPath.$dataFileName;
        if(file_exists($dataFilePath)){
            $datas = json_decode(file_get_contents($dataFilePath), true);
        }else{
            $datas = [];
        }
        $nationalityArr = explode(',',$nationality);
        $datas[] = [
            'real_name' => $real_name,
            'level' => $level,
            'sex' => $sex,
            'range' => $range,
            'age' => $age,
            'category_id' => $cate_id,
            'user_nationality' => $nationalityArr[0],
            'nationality_name' => $nationalityArr[1],
        ];
        if(file_put_contents($dataFilePath, json_encode($datas))){
            wp_send_json_success(['info' => '操作成功!']);
        }else{
            wp_send_json_error(['info' => '操作失败!']);
        }
    }
    /**
     * 删除脑力健将死数据
     */
    public function delBrainMore(){
        $k = isset($_POST['k']) ? intval($_POST['k']) : 0;
        $staticPath = PLUGINS_PATH.'nlyd-student/view/directory/static/';
        $dataFileName = 'brainMoreData.json';
        $dataFilePath = $staticPath.$dataFileName;
        if(file_exists($dataFilePath)){
            $datas = json_decode(file_get_contents($dataFilePath),true);
        }else{
            wp_send_json_error(['info' => '未获取到数据!']);
        }
        unset($datas[$k]);
        if(file_put_contents($dataFilePath, json_encode($datas))){
            wp_send_json_success(['info' => '操作成功!']);
        }else{
            wp_send_json_error(['info' => '操作失败!']);
        }
    }

    /**
     * 添加考级名录死数据
     */
    public function addGradingMoreData(){
        $level = isset($_POST['level_add']) ? intval($_POST['level_add']) : 0;
        $cate_id = isset($_POST['cate_add']) ? intval($_POST['cate_add']) : 0;
        $age = isset($_POST['age_add']) ? intval($_POST['age_add']) : 0;
        $real_name = isset($_POST['real_name_add']) ? trim($_POST['real_name_add']) : '';
        $sex = isset($_POST['sex_add']) ? trim($_POST['sex_add']) : '';
        $nationality = isset($_POST['nationality_add']) ? trim($_POST['nationality_add']) : '';
        if($level < 1 || $real_name == '' || $sex == '' || $nationality == '' || $cate_id < 1){
            wp_send_json_error(['info' => '数据不完整!']);
        }
        $staticPath = PLUGINS_PATH.'nlyd-student/view/directory/static/';
        $dataFileName = 'gradingMoreData.json';
        $dataFilePath = $staticPath.$dataFileName;
        if(file_exists($dataFilePath)){
            $datas = json_decode(file_get_contents($dataFilePath), true);
        }else{
            $datas = [];
        }
        $nationalityArr = explode(',',$nationality);
        $datas[] = [
            'real_name' => $real_name,
            'level' => $level,
            'sex' => $sex,
            'age' => $age,
            'category_id' => $cate_id,
            'user_nationality' => $nationalityArr[0],
            'nationality_name' => $nationalityArr[1],
        ];
        if(file_put_contents($dataFilePath, json_encode($datas))){
            if(file_exists($staticPath.'remember_static.html')) unlink($staticPath.'remember_static.html');
            if(file_exists($staticPath.'read_static.html')) unlink($staticPath.'read_static.html');
            if(file_exists($staticPath.'calculation_static.html')) unlink($staticPath.'calculation_static.html');
            wp_send_json_success(['info' => '操作成功!']);
        }else{
            wp_send_json_error(['info' => '操作失败!']);
        }
    }
    /**
     * 删除考级名录死数据
     */
    public function delGradingMore(){
        $k = isset($_POST['k']) ? intval($_POST['k']) : 0;
        $staticPath = PLUGINS_PATH.'nlyd-student/view/directory/static/';
        $dataFileName = 'gradingMoreData.json';
        $dataFilePath = $staticPath.$dataFileName;
        if(file_exists($dataFilePath)){
            $datas = json_decode(file_get_contents($dataFilePath),true);
        }else{
            wp_send_json_error(['info' => '未获取到数据!']);
        }
        unset($datas[$k]);
        if(file_put_contents($dataFilePath, json_encode($datas))){
            if(file_exists($staticPath.'remember_static.html')) unlink($staticPath.'remember_static.html');
            if(file_exists($staticPath.'read_static.html')) unlink($staticPath.'read_static.html');
            if(file_exists($staticPath.'calculation_static.html')) unlink($staticPath.'calculation_static.html');
            wp_send_json_success(['info' => '操作成功!']);
        }else{
            wp_send_json_error(['info' => '操作失败!']);
        }
    }
}

new Match_Ajax();