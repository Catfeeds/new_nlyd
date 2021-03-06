<?php
namespace Controller;
class Download
{
    private static $downloadPath;
    public function __construct()
    {
//        //配置自己的重写规则
        add_action( 'init', array($this,'custom_rewrite_basic'),10,0);
        self::$downloadPath = WP_PLUGIN_DIR.'/downloadFile/';
        require_once PLUGINS_PATH.'nlyd-student/library/Vendor/PHPExcel/Classes/PHPExcel.php';
        require_once PLUGINS_PATH.'nlyd-student/library/Vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';
        if(!isset($_GET['action']) || !method_exists($this, $_GET['action'])) exit;
        $action = $_GET['action'];
        $this->$action();
        exit;
    }

    public function custom_rewrite_basic(){

    }

    public function order(){

//        global $wpdb;
//        $bool = $wpdb->update($wpdb->prefix.'match_questions', ['my_score' => 0,'surplus_time' => ''],['match_id' => 56522, 'project_id' => 52701]);
////
////        var_dump($wpdb->last_query);
////        die;
//        $times = 15*60;
//        $sql = "SELECT b.`truename`,a.member_id,a.score,a.cost_time,a.created,a.round FROM `sckm_match_games` a left JOIN sckm_members b ON a.member_id = b.id WHERE a.match_id = 238 AND game_type='wzsd'";
//        $rows = $wpdb->get_results($sql,ARRAY_A);
//
//        foreach ($rows as $row){
//            $sql1 = "SELECT * FROM `zlin_usermeta` WHERE meta_value LIKE '%{$row['truename']}%' ";
//            $row2 = $wpdb->get_row($sql1,ARRAY_A);
//            print_r($row);
//            echo '</br>';
//            if(!$row2) continue;
//            $sub = $times-$row['cost_time'];
//            $wpdb->query("UPDATE {$wpdb->prefix}match_questions SET my_score={$row['score']},surplus_time={$sub} WHERE match_id=56522 AND project_id=52701 AND match_more={$row['round']} AND user_id={$row2['user_id']}");
//        }
////        echo '<pre />';
////        print_r($rows);
//        die;
//        $wpdb->query('UPDATE '.$wpdb->prefix.'match_question SET my_score=0,surplus_time=0,created_microtime=0 WHERE match_id=56522 AND project_id=52704');
//        $rows = $wpdb->get_results("SELECT member_id,score,total_score,high_score,round,game_type,cost_time FROM sckm_match_games WHERE match_id=238 AND member_id=20977", ARRAY_A);
//        foreach ($rows as $row){
//            $wpdb->query("UPDATE {$wpdb->prefix}match_question SET my_score={$row['score']} WHERE match_id=56522 AND project_id=52701 AND match_more={$row['round']} AND user_id={}");
//        }
//
//
//        echo '<pre />';
//        print_r($rows);
//
//        die;
        global $wpdb;
        if(empty($_POST['start_date']) || empty($_POST['end_date'])) exit('请选择日期');
        $start = date_i18n('Y-m-d H:i:s', strtotime($_POST['start_date']));
        $end = date_i18n('Y-m-d H:i:s', strtotime($_POST['end_date']));
        $orderType = isset($_POST['order_type']) ? intval($_POST['order_type']) : 1;

        $orderTypeWhere = '';
        switch ($orderType){
            case 2:
                $orderTypeWhere = ' AND order_type=1';
                break;
            case 3:
                $orderTypeWhere = ' AND order_type=2';
        }

        $rows = $wpdb->get_results('SELECT
        o.serialnumber,
        o.cost,
        IFNULL(o.fullname,"-") AS fullname,
        IFNULL(o.telephone,"-") AS telephone,
        IFNULL(o.address,"-") AS address,
        IFNULL(o.express_number,"-") AS express_number,
        IFNULL(o.express_company,"-") AS express_company,
        CASE o.order_type WHEN 1 THEN "比赛订单" WHEN 2 THEN "考级订单" WHEN 3 THEN "课程订单" ELSE "-" END AS order_type,
        CASE o.pay_type WHEN "zfb" THEN "支付宝" WHEN "wx" THEN "微信" WHEN "ylk" THEN "银联卡" ELSE o.pay_type END AS pay_type,
        CASE o.pay_status WHEN 1 THEN "待支付" WHEN -1 THEN "待退款" WHEN -2 THEN "已退款" WHEN 2 THEN "已支付" WHEN 3 THEN "待收货" WHEN 4 THEN "已完成" WHEN 5 THEN "已失效" ELSE "-" END AS pay_title,
        u.user_login,
        p.post_title,
        o.pay_status,
        o.created_time
        FROM '.$wpdb->prefix.'order AS o
        LEFT JOIN '.$wpdb->users.' AS u ON o.user_id=u.ID
        LEFT JOIN '.$wpdb->posts.' AS p ON o.match_id=p.ID
        WHERE o.created_time BETWEEN "'.$start.'" AND "'.$end.'"'.$orderTypeWhere, ARRAY_A);


        $date = date_i18n('YmdHis', strtotime($_POST['start_date'])).'-'.date_i18n('YmdHis', strtotime($_POST['end_date']));
        $filename = 'order_';
        $filename .= $date."_";
        $filename .= current_time('timestamp').".xls";
//        $path = self::$downloadPath.$filename;
//        file_put_contents($path,$html);
        header('Pragma:public');
        header('Content-Type:application/x-msexecl;name="'.$filename.'"');
        header('Content-Disposition:inline;filename="'.$filename.'"');
        $objPHPExcel = new \PHPExcel();


        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('center');
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical('center');
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);


        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $start.'至'.$end.'订单');
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(35);//第一行行高
        $objPHPExcel->getActiveSheet()->mergeCells('A1:M1');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);

        //加粗
        $objPHPExcel->getActiveSheet()->getStyle( 'A1')->getFont()->setSize(16)->setBold(true);

        $objPHPExcel->getActiveSheet()->getStyle( 'A2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'B2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'C2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'D2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'E2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'F2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'G2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'H2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'I2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'J2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'K2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'L2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'M2')->getFont()->setBold(true);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '订单流水');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', '用户名');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', '比赛');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', '收件人');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', '联系电话');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', '收货地址');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', '订单类型');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', '快递单号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', '快递公司');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J2', '支付类型');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K2', '订单总价');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L2', '支付状态');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M2', '创建时间');
        foreach ($rows as $k => $row){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($k+3),' '.$row['serialnumber']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($k+3),' '.$row['user_login']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($k+3),' '.$row['post_title']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($k+3),' '.$row['funllname']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($k+3),' '.$row['telephone']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($k+3),' '.$row['address']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($k+3),' '.$row['order_type']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($k+3),' '.$row['express_number']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+3),' '.$row['express_company']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.($k+3),' '.$row['pay_type']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.($k+3),' '.$row['cost']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.($k+3),' '.$row['pay_title']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.($k+3),' '.$row['created_time']);
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        return;
    }

    public function question(){
        $filename = 'question_template.xlsx';
        $path = self::$downloadPath.'question/'.$filename;
        $file_temp = fopen ( $path, "r");

        // Begin writing headers
        header ( "Pragma: public" );
        header ( "Expires: 0" );
        header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header ( "Cache-Control: public" );
        header ( "Content-Description: File Transfer" );
        // Use the switch-generated Content-Type
        header ( "Content-Type: application/vnd.ms-word" );
        // Force the download
        $header = "Content-Disposition: attachment; filename=" . $filename . ";";
        header ( $header );
        header ( "Content-Transfer-Encoding: binary" );
        header ( "Content-Length: " . filesize($path) );

        //@readfile ( $file );
        echo fread ($file_temp, filesize ($path) );
        fclose ($file_temp);
        exit;
    }

    /**
     * 导出报名学员
     */
    public function matchStudent(){
        $match_id = intval($_GET['match_id']);
        $match = get_post($match_id);
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;

//        $rows = $wpdb->get_results('SELECT u.ID,u.user_login,u.display_name,u.user_mobile,u.user_email,o.created_time,o.address,o.telephone FROM '.$wpdb->prefix.'order AS o
//        LEFT JOIN '.$wpdb->users.' AS u ON u.ID=o.user_id
//        WHERE o.order_type=1 AND o.pay_status IN (2,3,4) AND u.ID != "" AND o.match_id='.$match->ID, ARRAY_A);

        $rows = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS u.ID,u.user_login,u.display_name,u.user_mobile,u.user_email,o.created_time,o.address,o.telephone,u.user_mobile,p.post_title AS team_name,o.seat_number FROM '.$wpdb->prefix.'order AS o 
        LEFT JOIN '.$wpdb->users.' AS u ON u.ID=o.user_id 
        LEFT JOIN '.$wpdb->prefix.'match_team AS mt ON mt.user_id=o.user_id AND mt.status=2 
        LEFT JOIN '.$wpdb->posts.' p ON p.ID=mt.team_id AND p.ID!="" 
        WHERE o.order_type=1 AND o.pay_status IN(2,3,4)  AND o.match_id='.$match->ID.' AND u.ID!="" ORDER BY o.seat_number ASC', ARRAY_A);



        $filename = 'match_student_';
        $filename .= current_time('timestamp').".xls";
//        $path = self::$downloadPath.$filename;
//        file_put_contents($path,$html);
        header('Pragma:public');
        header('Content-Type:application/x-msexecl;name="'.$filename.'"');
        header('Content-Disposition:inline;filename="'.$filename.'"');
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal('center');
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('center');


        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(45);
//        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(45);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $match->post_title.'(报名选手)');

        $objPHPExcel->getActiveSheet()->getStyle( 'A1')->getFont()->setSize(16)->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'A2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'B2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'C2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'D2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'E2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'F2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'G2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'H2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'I2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'J2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'K2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'L2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'M2')->getFont()->setBold(true);
//        $objPHPExcel->getActiveSheet()->getStyle( 'M2')->getFont()->setBold(true);


        $objPHPExcel->getActiveSheet()->mergeCells('A1:L1');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '用户名');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', 'ID');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', '真实姓名');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', '座位号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', '证件号码');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', '国籍');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', '性别');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', '年龄');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', '年龄组别');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J2', '所在地区');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K2', '电话');
//        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K2', '邮箱');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L2', '报名时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M2', '战队名称');
        foreach ($rows as $k => $row){
            $usermeta = get_user_meta($row['ID'], '', true);
            $age = unserialize($usermeta['user_real_name'][0])['real_age'];
            $user_nationality = isset($usermeta['user_nationality']) ? $usermeta['user_nationality'][0] : '';
            $group = $this->getAgeGroupNameByAge($age);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($k+3),' '.$row['user_login']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($k+3),' '.$usermeta['user_ID'][0]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($k+3),' '.unserialize($usermeta['user_real_name'][0])['real_name']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($k+3),' '.$row['seat_number']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($k+3),' '.unserialize($usermeta['user_real_name'][0])['real_ID'].' ('.unserialize($usermeta['user_real_name'][0])['real_type'].')');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($k+3),' '.$user_nationality);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($k+3),' '.$usermeta['user_gender'][0]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($k+3),' '.$age);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+3),' '.$group);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.($k+3),' '.unserialize($usermeta['user_address'][0])['province'].unserialize($usermeta['user_address'][0])['city']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.($k+3),' '.$row['user_mobile']);
//            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.($k+3),' '.$row['user_email']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.($k+3),' '.$row['created_time']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.($k+3),' '.$row['team_name']);
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        return;

    }

    /**
     * 组别名称
     */
    public function getAgeGroupNameByAge($age){
        switch ($age){
            case $age > 59:
                $group = '老年组';
                break;
            case $age > 17:
                $group = '成人组';
                break;
            case $age > 11:
                $group = '少年组';
                break;
            default:
                $group = '儿童组';
                break;
        }
        return $group;
    }

    /**
     * 获取正确率
     */
    public function getCorrect($user_id,$project_id,$match_id){
        global $wpdb;
        $av = $wpdb->get_row('SELECT questions_answer,my_answer,my_score FROM '.$wpdb->prefix.'match_questions 
                    WHERE my_score=(SELECT MAX(my_score) FROM '.$wpdb->prefix.'match_questions WHERE match_id='.$match_id.' AND user_id='.$user_id.' AND project_id='.$project_id.') 
                    AND user_id='.$user_id.' AND project_id='.$project_id.' AND match_id='.$match_id, ARRAY_A);


        $correct = 0;
        $av['my_answer'] = $my_answer = json_decode($av['my_answer'], true);

        $av['questions_answer'] = $questions_answer = json_decode($av['questions_answer'], true);
        if(!$my_answer) return 0;
        $abc = 0;
        $bcd = count($questions_answer);
        foreach ($my_answer as $k => $avv){
            if(is_array($avv) && isset($questions_answer[$k]['problem_answer'])){
                //速度类选项题
                foreach ($questions_answer[$k]['problem_answer'] as $pak => $pav){
                    if($pav == true && $avv[$pak] == true){
                        ++$abc;
                    }
                }
            }else{
                $title = get_post($project_id)->post_title;
                if($avv == 'unsolvable' || (preg_match('/[\+\*\/\-\×\÷]/', $avv) && preg_match('/逆向/', $title))){
                    //逆向速算,总分除十=正确题目数
                    $abc += $av['my_score'] / 10;
                }elseif($avv == $questions_answer[$k]){
                    ++$abc;
                }
            }
        }
        $correct += $abc/$bcd;
        return $correct;
    }



    /**
     * 导出比赛排名
     */
    public function match_rankingsss(){

        global $wpdb;

        //首先获取当前比赛
        $post = get_post(intval($_GET['match_id']));
        $match = $wpdb->get_row('SELECT match_status FROM '.$wpdb->prefix.'match_meta_new WHERE match_id='.$post->ID, ARRAY_A);

        //TODO 判断比赛是否结束
        $matchEnd = true;
        if(!$match || $match['match_status'] != -3){
//            echo '<br /><h2 style="color: #a80000">比赛未结束!</h2>';
//            return;
            $matchEnd = false;
        }

        //查询比赛小项目
        $projectArr = get_match_end_time($post->ID);

        $categoryArr = []; //分类选项卡数组
        foreach ($projectArr as $pak => &$pav) {

            $project_id_array[] = $pav['match_project_id'];
            //获取类别和项目选项卡
            if (in_array($pav['project_alias'], ['pkjl', 'szzb'])) {
                $cate = ['str' => 'sjl','name' => '速记类'];
            } elseif (in_array($pav['project_alias'], ['kysm', 'wzsd'])) {
                $cate = ['str' => 'sdl','name' => '速读类'];
            } elseif (in_array($pav['project_alias'], ['zxss', 'nxss'])) {
                $cate = ['str' => 'ssl','name' => '速算类'];
            }
            $categoryArr[$cate['str']]['is_end'] = $categoryArr[$cate['str']]['is_end'] == 'false' ? 'false' : $pav['is_end'];
            $categoryArr[$cate['str']]['post_title'] = $cate['name'];
            if(!isset($categoryArr[$cate['str']]['ids'])) $categoryArr['sjl']['ids'] = '';
            $categoryArr[$cate['str']]['ids'] .= $pav['match_project_id'].',';
        }
        $where = '';
        $score = 'my_score';
        $selectArr = []; //循环查询分数数组
        $op1 = true;
        $op2 = false;
        $op3 = false;
        $selectType = 1;

        $downloadParam = '';
        $ageWhere = '';
//       选项卡条件
        if(isset($_GET['op1'])){
            switch ($_GET['op1']){
                case 1://总排名
                    $selectArr = $projectArr;
                    $downloadParam .= '&op1=1';
                    break;
                case 2: //分类排名
                    $downloadParam .= '&op1=2';
                    $op2 = true;
                    $selectType = 2;
                    $score = 'MAX(my_score) AS my_score';
                    if(isset($_GET['op2'])){
                        $selectArr = [$categoryArr[$_GET['op2']]];
                        $where = ' AND project_id IN('.substr($categoryArr[$_GET['op2']]['ids'],0,strlen($categoryArr['sdl']['ids'])-1).') GROUP BY project_id';
                    }else{
                        //默认第一个分类
                        $selectArr = [$categoryArr[key($categoryArr)]];
                        $where = ' AND project_id IN('.substr($categoryArr[key($categoryArr)]['ids'],0,strlen($categoryArr['sdl']['ids'])-1).') GROUP BY project_id';
                        $_GET['op2'] = key($categoryArr);
                    }
                    $downloadParam .= '&op2='.$_GET['op2'];
                    break;
                case 3: //单项排名
                    $downloadParam .= '&op1=3';
                    $op3 = true;
                    $selectType = 3;
                    $score = 'MAX(my_score) AS my_score';
                    if(isset($_GET['op4'])){
                        switch ($_GET['op4']){
                            case 4://儿童组
                                $ageWhere = ' AND  um.meta_value<13';
                                break;
                            case 3://少年组
                                $ageWhere = ' AND um.meta_value>12 AND um.meta_value<18';
                                break;
                            case 2://成年组
                                $ageWhere = ' AND um.meta_value>17 AND um.meta_value<60';
                                break;
                            case 1://老年组
                                $ageWhere = ' AND um.meta_value>59';
                                break;
                            default://全部
                        }
                    }else{
                        $_GET['op4'] = 0;
                    }
                    if(isset($_GET['op3'])){
                        $where = ' AND project_id ='.$_GET['op3'].' GROUP BY match_id';

                        reset($projectArr);
                        foreach ($projectArr as $pakOp3 => $pavOp3) {
                            if($pavOp3['match_project_id'] == $_GET['op3']){
                                $selects = $pavOp3;
                                break;
                            }
                        }
                    }else{
                        //默认地一个项目
                        $where = ' AND project_id ='.$projectArr[0]['match_project_id'].' GROUP BY match_id';
                        $selects = $projectArr[0];
                        $_GET['op3'] = $projectArr[0]['match_project_id'];
                    }
                    $selectArr = [$selects];
                    $downloadParam .= '&op3='.$_GET['op3'].'&op4='.$_GET['op4'];;
                    break;
                default:
                    $selectArr = $projectArr;
            }
        }else{
            $selectArr = $projectArr;
        }

        //查询每个参赛学员的总分排名
        //分页
        $_GET['cpage'] = isset($_GET['cpage']) ? $_GET['cpage'] : 1;
        $page = intval($_GET['cpage']) < 1 ? 1 : intval($_GET['cpage']);
        $pageSize = 50;
        $start = ($page-1)*$pageSize;
        //TODO 分页排序需要处理, 暂时关闭分页   LIMIT '.$start.','.$pageSize
        $totalRanking = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS o.telephone,u.user_email,o.user_id,mq.project_id,u.user_mobile,o.created_time,um.meta_value 
            FROM '.$wpdb->prefix.'order AS o 
            LEFT JOIN '.$wpdb->users.' AS u ON u.ID=o.user_id 
            LEFT JOIN '.$wpdb->usermeta.' AS um ON um.user_id=u.ID AND um.meta_key="user_age" 
            LEFT JOIN '.$wpdb->prefix.'match_questions AS mq ON mq.user_id=u.ID 
            WHERE o.match_id='.$post->ID.' AND o.pay_status IN(2,3,4) AND u.ID != ""'.$ageWhere.' GROUP BY o.user_id ORDER BY u.ID ASC', ARRAY_A);
//
//        var_dump($wpdb->last_query);
//        die;
//        $count  = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
//        $pageAll = ceil($count['count']/$pageSize);
//        $pageHtml = paginate_links( array(
//            'base' => add_query_arg( 'cpage', '%#%' ),
//            'format' => '',
//            'prev_text' => __('&laquo;'),
//            'next_text' => __('&raquo;'),
//            'total' => $pageAll,
//            'current' => $page
//        ));

//        echo '<pre />';
//        var_dump($selectArr);
        //剩余时间 | 正确率
        //获取比赛轮数

        //查询每个学员每个小项目每一轮的分数
        $whereT = 0;
        foreach ($totalRanking as &$trv){
            $trv['my_score'] = 0;
            $trv['surplus_time'] = 0;
            $trv['projectScore'] = [];
            reset($selectArr);
            foreach ($selectArr as  $paks => $pavs) {
                if($pavs['is_end'] == 'false') {
                    unset($trv['projectScore']);
                    continue;//未结束
                }
                $trv['projectScore'][$paks] = 0;
                if($where == '' || $whereT == 1) {
                    $where = ' AND project_id='.$pavs['match_project_id'];
                    $whereT = 1;
                }
                if($selectType == 2){
                    $trv['projectScore'][$paks] = 0;
                }else{
                    $trv['projectScore'][$paks] = '';
                }
//                $trv['projectScore'][$pak] = '';
                $res = $wpdb->get_results('SELECT '.$score.',match_more,surplus_time,project_id FROM '.$wpdb->prefix.'match_questions AS mq 
                WHERE match_id='.$post->ID.' AND user_id='.$trv['user_id'].$where, ARRAY_A);

                $moreArr = [];
                $scoreArr = [];
                $surplus_timeArr = [];
                $match_more_all = $wpdb->get_var('SELECT MAX(match_more) AS match_more FROM '.$wpdb->prefix.'match_questions WHERE match_id='.$match['match_id'].' AND user_id='.$trv['user_id'].' AND project_id='.$pavs['match_project_id'].' GROUP BY project_id,user_id');

                if($selectType == 1){
                    for($mi = 1; $mi <= $match_more_all; ++$mi){
//                    for($mi = 1; $mi <= $match['match_more']; ++$mi){
                        $moreArr[$mi] = '0/';
                    }
                }

                foreach ($res as $rv){
                    if($selectType == 2){
                        $moreArr[] = ($rv['my_score'] > 0 ? $rv['my_score'] : 0);
                    }else{
                        $moreArr[$rv['match_more']] = ($rv['my_score'] > 0 ? $rv['my_score'] : '0').'/';
                    }

                    $scoreArr[] = $rv['my_score'];
                    $surplus_timeArr[] = $rv['surplus_time'];
                }

                $trv['my_score'] += ($scoreArr != [] ? max($scoreArr) : 0);
                $trv['surplus_time'] += ($surplus_timeArr != [] ? max($surplus_timeArr) : 0);

                foreach ($moreArr as $mav){
                    if($selectType == 2){
                        $trv['projectScore'][$paks] += $mav;
                    }else{
                        $trv['projectScore'][$paks] .= $mav;
                    }
                }
                if($selectType == 2) $trv['projectScore'][$paks] .= '/';

                if(!$trv['projectScore'][$paks]) $trv['projectScore'][$paks] = '0/';
//                var_dump($trv['projectScore'][$pak]);
                $trv['projectScore'][$paks] = substr($trv['projectScore'][$paks], 0, strlen($trv['projectScore'][$paks])-1);
//                print_r($res);
            }
            $usermeta = get_user_meta($trv['user_id'], '', true);
            $user_real_name = unserialize($usermeta['user_real_name'][0]);
            $age = $user_real_name['real_age'];
            $user_real_name = $user_real_name['real_name'];
            $trv['age'] = $age;
            $trv['ageGroup'] = getAgeGroupNameByAge($age);
            $trv['userID'] = $usermeta['user_ID'][0];
            $trv['real_name'] = $user_real_name;
            $trv['sex'] = $usermeta['user_gender'][0];
            $trv['birthday'] = isset($usermeta['user_birthday']) ? $usermeta['user_birthday'][0] : '';
            $trv['address'] = unserialize($usermeta['user_address'][0])['province'].unserialize($usermeta['user_address'][0])['city'];
        }
        $rankingType = 1;
        for($i = 0; $i < count($totalRanking); ++$i){
            if(isset($totalRanking[$i+1])){
//                var_dump(2222222);
                for ($j = $i+1; $j < count($totalRanking); ++$j){
                    if($totalRanking[$i]['my_score'] == $totalRanking[$j]['my_score']){
//                        if($totalRanking[$i]['my_score'] < 1){
//                            $rankingAuto = false;
//                        }else
                        if($totalRanking[$j]['surplus_time'] > $totalRanking[$i]['surplus_time']){

                            $a = $totalRanking[$j];
                            $totalRanking[$j] = $totalRanking[$i];
                            $totalRanking[$i] = $a;
                        }elseif ($totalRanking[$j]['surplus_time'] == $totalRanking[$i]['surplus_time']){
                            //TODO 正确率, 获取分数最高一轮的正确率
//                    $iCorce = $this->getCorrect($totalRanking[$i]['user_id'],$totalRanking[$i]['project_id'],$post->ID);
//                    $jCorce = $this->getCorrect($totalRanking[$j]['user_id'],$totalRanking[$j]['project_id'],$post->ID);
//                    if($iCorce < $jCorce){
//                        $a = $totalRanking[$j];
//                        $totalRanking[$j] = $totalRanking[$i];
//                        $totalRanking[$i] = $a;
//                    }
                        }
                    }elseif ($totalRanking[$j]['my_score'] > $totalRanking[$i]['my_score']){
                        $a = $totalRanking[$j];
                        $totalRanking[$j] = $totalRanking[$i];
                        $totalRanking[$i] = $a;
                    }
                }
            }
        }
        //名次
        $ranking = 1;
        foreach ($totalRanking as $k => $v){
            $totalRanking[$k]['ranking'] = $ranking;
            if(isset($totalRanking[$k+1]) && $totalRanking[$k+1]['my_score'] == $totalRanking[$k]['my_score'] && $totalRanking[$k+1]['surplus_time'] == $totalRanking[$k]['surplus_time']){

            }else{
                ++$ranking;
            }
        }

        $filename = 'match_ranking_';
        $filename .= strtotime(current_time('mysql')).".xls";
        //        $path = self::$downloadPath.$filename;
        //        file_put_contents($path,$html);
        header('Pragma:public');
        header('Content-Type:application/x-msexecl;name="'.$filename.'"');
        header('Content-Disposition:inline;filename="'.$filename.'"');

        $objPHPExcel = new \PHPExcel();
        //边框



        //居中显示
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('center');
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical('center');

        //行高
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);

        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(40);
        $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);

        if($selectType == 1){
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $a = 'K';
        }else{
            $a = 'J';
        }
        foreach ($selectArr as $titleV){
            if($titleV['is_end'] == 'true'){
                ++$a;
                $objPHPExcel->getActiveSheet()->getColumnDimension($a)->setWidth(15);
            }
        }

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $post->post_title);

        //加粗
        $objPHPExcel->getActiveSheet()->getStyle( 'A1')->getFont()->setSize(16)->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'A2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'B2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'C2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'D2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'E2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'F2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'G2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'H2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'I2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'J2')->getFont()->setBold(true);

        if($selectType == 1){
            $objPHPExcel->getActiveSheet()->getStyle( 'K2')->getFont()->setBold(true);
            $a = 'K';
        }else{
            $a = 'J';
        }
        foreach ($selectArr as $titleV){
            if($titleV['is_end'] == 'true'){
                ++$a;
                $objPHPExcel->getActiveSheet()->getStyle( $a.'2')->getFont()->setBold(true);
            }


        }

        $objPHPExcel->getActiveSheet()->getStyle('A1:'.--$a.'1')->getBorders()->getAllBorders()->setBorderStyle('thin');

        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.--$a.'1');

        $objPHPExcel->getActiveSheet()->getStyle('A1:'.--$a.'1')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');
        if($selectType == 1){
            $a = 'K';
        }else{
            $a = 'J';
        }
        for ($b = 'A'; $b <= $a; ++$b){
            $objPHPExcel->getActiveSheet()->getStyle( $b.'2')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');
        }
        if($selectType == 1){
            $a = 'L';
        }else{
            $a = 'K';
        }
        foreach ($selectArr as $titleV){
            $objPHPExcel->getActiveSheet()->getStyle($a. '2')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');
            ++$a;
        }

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '学员ID');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', '真实姓名');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', '性别');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', '年龄');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', '年龄组别');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', '所在地区');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', '手机');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', '邮箱');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', '报名时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J2', '名次');
        if($selectType == 1){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K2', '总得分');
            $a = 'K';
        }else{
            $a = 'J';
        }

        for ($b = 'A'; $b <= $a; ++$b){
            $objPHPExcel->getActiveSheet()->getStyle($b.'2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        }

        if($selectType == 1){
            $a = 'L';
        }else{
            $a = 'K';
        }
        foreach ($selectArr as $titleV){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($a.'2', $titleV['post_title'].'得分');
            $objPHPExcel->getActiveSheet()->getStyle($a.'2')->getBorders()->getAllBorders()->setBorderStyle('thin');
            ++$a;
        }


        $k = 0;
        foreach ($totalRanking as $raV){
            $mobile = $raV['telephone'] ? $raV['telephone'] : $raV['user_mobile'];
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($k+3),' '.$raV['userID']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($k+3),' '.$raV['real_name']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($k+3),' '.$raV['sex']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($k+3),' '.$raV['age']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($k+3),' '.$raV['ageGroup']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($k+3),' '.$raV['address']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($k+3),' '.$mobile);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($k+3),' '.$raV['user_email']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+3),' '.$raV['created_time']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.($k+3),' '.$raV['ranking']);
            if($selectType == 1){
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.($k+3),' '.$raV['my_score']);
                $a = 'K';
            }else{
                $a = 'J';
            }
            for ($b = 'A'; $b <= $a; ++$b){
                $objPHPExcel->getActiveSheet()->getStyle($b.($k+3))->getBorders()->getAllBorders()->setBorderStyle('thin');
            }
            if($selectType == 1){
                $a = 'L';
            }else{
                $a = 'K';
            }
            foreach ($raV['projectScore'] as $ravV){
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($a.($k+3),' '.$ravV);
                $objPHPExcel->getActiveSheet()->getStyle($a.($k+3))->getBorders()->getAllBorders()->setBorderStyle('thin');
                ++$a;
            }
            ++$k;
        }


        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');





    }


    /**
     * 比赛排名
     */
    public function match_ranking(){
        global $wpdb;
        //首先获取当前比赛
        $post = get_post(intval($_GET['match_id']));
        $match = $wpdb->get_row('SELECT match_status,match_id FROM '.$wpdb->prefix.'match_meta_new WHERE match_id='.$post->ID, ARRAY_A);

        //TODO 判断比赛是否结束
        $matchEnd = true;
        if(!$match || $match['match_status'] != -3){
            $matchEnd = false;
        }
        $rankingView = ['status' => true];

        //查询比赛小项目
        $projectArr = get_match_end_time($post->ID);
        $categoryArr = []; //分类选项卡数组
        $currentDateTime = get_time('mysql');
        //选项卡查询
        $op1 = isset($_GET['op1']) ? $_GET['op1'] : 3; //一级选项卡, 默认单项排名
        $op2 = isset($_GET['op2']) ? $_GET['op2'] : 'sdl'; //二级选项卡, 默认第一个分类
        $op3 = isset($_GET['op3']) ? $_GET['op3'] : $projectArr[0]['match_project_id']; //三级选项卡, 默认第一个项目
        $op4 = isset($_GET['op4']) ? $_GET['op4'] : 0; //四级选项卡, 默认全部年龄
        $op5 = isset($_GET['op5']) ? $_GET['op5'] : 1; //五级选项卡(总排名和战队排名), 默认总排名
        $downloadParam = "&op1={$op1}&op2={$op2}&op3={$op3}&op4={$op4}";
//        leo_dump($op2);
        $data = [];

        $match_student_class = new \Match_student();

        $fileRankingName = '';
        $titleRankingName = '';
        if($op1 == 1){
            if($matchEnd == false){
                $rankingView = ['status' => false, 'msg' => '当前比赛未结束!'];
            }else{
                $data = $match_student_class->getAllRankingData($match,$projectArr,$op5);
            }
            $fileRankingName .= ($op5 == 2 ? 'team' : 'personal');
            $titleRankingName = ($op5 == 2 ? '战队排名' : '个人总排名');
        }elseif ($op1 == 2){
            //获取当前分类的id字符串
            $project_id_array = [];//项目id数组
            $project_alias_arr = [];// 分类下的项目数组
            switch ($op2){
                case 'sdl':
                    $titleRankingName = '速度类';
                    $project_alias_arr = ['option' => ['wzsd','kysm'], 'name' => 'read'];
                    break;
                case 'ssl':
                    $titleRankingName = '心算类';
                    $project_alias_arr = ['option' => ['zxss','nxss'], 'name' => 'count'];
                    break;
                case 'sjl':
                    $titleRankingName = '记忆类';
                    $project_alias_arr = ['option' => ['szzb','pkjl'], 'name' => 'remember'];
                    break;
                default:
                    exit('参数错误');
            }
            $cateName = '';
            foreach ($projectArr as $pavGetIds){
                if(in_array($pavGetIds['project_alias'],$project_alias_arr['option'])){
//                    if($currentDateTime < $pavGetIds['project_end_time']){
                    if(!$pavGetIds['is_end']){
                        $rankingView = ['status' => false, 'msg' => '当前分类未结束!'];
                        break;
                    }
                    $project_id_array[] = $pavGetIds['match_project_id'];
                    $cateName = $project_alias_arr['name'];
                };
            }
            $fileRankingName .= $cateName;
            switch ($op4){
                case 4://儿童组
                    $titleRankingName .= '儿童组';
                    break;
                case 3://少年组
                    $titleRankingName .= '少年组';
                    break;
                case 2://成年组
                    $titleRankingName .= '成年组';
                    break;
                case 1://老年组
                    $titleRankingName .= '老年组';
                    break;
            }
            $titleRankingName .= '排名';
            if($rankingView['status'] == true){
                $data = $match_student_class->getCategoryRankingData($match,join(',',$project_id_array),$op4);
            }

        }elseif ($op1 == 3){

            foreach ($projectArr as $pavGetIds){
                if($pavGetIds['match_project_id'] == $op3){
                    if(!$pavGetIds['is_end']){
                        $rankingView = ['status' => false, 'msg' => '当前项目未结束!'];
                        break;
                    }
                    $fileRankingName .= $pavGetIds['project_alias'];
                    $titleRankingName = $pavGetIds['post_title'].'排名';
                }
            }

            switch ($op4){
                case 4:
                    $fileRankingName .= '_children';
                    break;
                case 3:
                    $fileRankingName .= '_juvenile';
                    break;
                case 2:
                    $fileRankingName .= '_adult';
                    break;
                case 1:
                    $fileRankingName .= '_old';
                    break;
                default:

            }
            if($rankingView['status'] == true) $data = $match_student_class->getCategoryRankingData($match,$op3,$op4);
        }else{
            exit('参数错误!');
        }
        if($rankingView['status'] == false) exit($rankingView['msg']);

        $filename = $fileRankingName.'_'.date('Y-m-d', get_time()).'_'.get_time().".xls";


        //        $path = self::$downloadPath.$filename;
        //        file_put_contents($path,$html);
        header('Pragma:public');
        header('Content-Type:application/x-msexecl;name="'.$filename.'"');
        header('Content-Disposition:inline;filename="'.$filename.'"');
        $objPHPExcel = new \PHPExcel();
        //边框

        //居中显示
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('center');
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical('center');

        //行高
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(40);
        $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);

        if($op1 == 1 && $op5 == 2){
            //战队
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);


            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $post->post_title.'('.$titleRankingName.')');

            //加粗
            $objPHPExcel->getActiveSheet()->getStyle( 'A1')->getFont()->setSize(16)->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'A2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'B2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'C2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'D2')->getFont()->setBold(true);


            $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getBorders()->getAllBorders()->setBorderStyle('thin');
            $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
            $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');
            for ($b = 'A'; $b <= 'E'; ++$b){
                $objPHPExcel->getActiveSheet()->getStyle( $b.'2')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');
                $objPHPExcel->getActiveSheet()->getStyle($b.'2')->getBorders()->getAllBorders()->setBorderStyle('thin');
            }


            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '名次');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', '战队');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', 'ID');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', '总成绩');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', '贡献成员');



            $k = 0;
            foreach ($data as $raV){
                //自动换行
                $objPHPExcel->getActiveSheet()->getStyle('E'.($k+3))->getAlignment()->setWrapText(true);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($k+3),' '.$raV['ranking']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($k+3),' '.$raV['team_name']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($k+3),' '.$raV['team_id']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($k+3),' '.$raV['my_score']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($k+3),' '.$raV['userScore']);
                for ($b = 'A'; $b <= 'E'; ++$b){
                    $objPHPExcel->getActiveSheet()->getStyle($b.($k+3))->getBorders()->getAllBorders()->setBorderStyle('thin');
                }
                ++$k;
            }
         } else{
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $end = 'M';
            if(isset($data[0]['projectScore'])) {
                $a = 'N';
                foreach ($projectArr as $titleV) {
                    $objPHPExcel->getActiveSheet()->getColumnDimension($a)->setWidth(18);
                    ++$end;
                    ++$a;
                }
            }

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $post->post_title.'('.$titleRankingName.')');

            //加粗
            $objPHPExcel->getActiveSheet()->getStyle( 'A1')->getFont()->setSize(16)->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'A2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'B2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'C2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'D2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'E2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'F2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'G2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'H2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'I2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'J2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'K2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'L2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle( 'M2')->getFont()->setBold(true);

            if(isset($data[0]['projectScore'])) {
                $a = 'M';
                foreach ($projectArr as $titleV) {
                    ++$a;
                    $objPHPExcel->getActiveSheet()->getStyle( $a.'2')->getFont()->setBold(true);
                }
            }
            $objPHPExcel->getActiveSheet()->getStyle('A1:'.--$end.'1')->getBorders()->getAllBorders()->setBorderStyle('thin');
            $objPHPExcel->getActiveSheet()->mergeCells('A1:'.--$end.'1');
            $objPHPExcel->getActiveSheet()->getStyle('A1:'.--$end.'1')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');


            for ($b = 'A'; $b <= 'M'; ++$b){
                $objPHPExcel->getActiveSheet()->getStyle( $b.'2')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');
            }

            if(isset($data[0]['projectScore'])) {
                $a = 'N';
                foreach ($projectArr as $titleV) {
                    $objPHPExcel->getActiveSheet()->getStyle($a. '2')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');
                    ++$a;
                }
            }

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '学员ID');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', '真实姓名');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', '性别');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', '国籍');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', '证件号码');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', '年龄');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', '年龄组别');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', '所在地区');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', '手机');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J2', '战队');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K2', '报名时间');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L2', '名次');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M2', '得分');
            for ($b = 'A'; $b <= 'M'; ++$b){
                $objPHPExcel->getActiveSheet()->getStyle($b.'2')->getBorders()->getAllBorders()->setBorderStyle('thin');
            }

            if(isset($data[0]['projectScore'])) {
                $a = 'N';
                foreach ($projectArr as $titleV) {
                    $objPHPExcel->getActiveSheet()->getStyle($b.'2')->getBorders()->getAllBorders()->setBorderStyle('thin');
                    ++$a;
                }
            }

            if(isset($data[0]['projectScore'])) {
                $a = 'N';
                foreach ($projectArr as $titleV) {
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($a.'2', $titleV['post_title'].'得分');
                    $objPHPExcel->getActiveSheet()->getStyle($a.'2')->getBorders()->getAllBorders()->setBorderStyle('thin');
                    ++$a;
                }
            }

            $k = 0;
            foreach ($data as $raV){
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($k+3),' '.$raV['userID']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($k+3),' '.$raV['real_name']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($k+3),' '.$raV['sex']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($k+3),' '.$raV['user_nationality']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($k+3),' '.$raV['card']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($k+3),' '.$raV['age']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($k+3),' '.$raV['ageGroup']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($k+3),' '.$raV['address']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+3),' '.$raV['user_mobile']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.($k+3),' '.$raV['team_name']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.($k+3),' '.$raV['created_time']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.($k+3),' '.$raV['ranking']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.($k+3),' '.$raV['my_score']);

                for ($b = 'A'; $b <= 'M'; ++$b){
                    $objPHPExcel->getActiveSheet()->getStyle($b.($k+3))->getBorders()->getAllBorders()->setBorderStyle('thin');
                }

                if(isset($data[0]['projectScore'])) {
                    $a = 'N';
                    foreach ($raV['projectScore'] as $ravV) {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($a.($k+3),' '.$ravV);
                        $objPHPExcel->getActiveSheet()->getStyle($a.($k+3))->getBorders()->getAllBorders()->setBorderStyle('thin');
                        ++$a;
                    }

                }
                ++$k;
            }

        }


        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }


    /**
     * 排名分类和单项数据
     */
    public function getCategoryRankingData($match,$projectIdStr,$ageType){
        global $wpdb;
        //获取每个用户的每个分类的分数和排名
        switch ($ageType){
            case 4://儿童组
                $ageWhere = ' y.meta_value<13';
                break;
            case 3://少年组
                $ageWhere = ' y.meta_value>12 AND y.meta_value<18';
                break;
            case 2://成年组
                $ageWhere = ' y.meta_value>17 AND y.meta_value<60';
                break;
            case 1://老年组
                $ageWhere = ' y.meta_value>59';
                break;
            default://全部
                $ageWhere = ' 1=1';
        }

        $result = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS x.user_id,SUM(x.my_score) my_score ,x.telephone,SUM(x.surplus_time) surplus_time,u.user_login,u.user_mobile,u.user_email,x.created_time,x.project_id,x.created_microtime  
                    FROM(
                        SELECT a.user_id,a.match_id,c.project_id,MAX(c.my_score) my_score ,a.telephone, MAX(c.surplus_time) surplus_time,if(MAX(c.created_microtime) > 0, MAX(c.created_microtime) ,0) created_microtime,a.created_time 
                        FROM `{$wpdb->prefix}order` a 
                        LEFT JOIN {$wpdb->prefix}match_questions c ON a.user_id = c.user_id  and c.match_id = {$match['match_id']} and c.project_id IN({$projectIdStr}) and c.is_true = 1                 
                        WHERE a.match_id = {$match['match_id']} AND a.pay_status = 4 and a.order_type = 1 
                        GROUP BY user_id,project_id
                    ) x
                    left join `{$wpdb->prefix}usermeta` y on x.user_id = y.user_id and y.meta_key='user_age' 
                    left join `{$wpdb->users}` u on u.ID=y.user_id 
                    WHERE {$ageWhere}
                    GROUP BY user_id
                    ORDER BY my_score DESC,surplus_time DESC,x.created_microtime ASC ", ARRAY_A);

        $list = array();
        $ranking = 1;
        foreach ($result as $k => $val){
//            $result[$k]['projectScore'] = [$result[$k]['my_score']];//与总排名数据格式一致
            $sql1 = " select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id = {$val['user_id']} and meta_key in('user_address','user_ID','user_real_name','user_age','user_gender','user_birthday','user_nationality') ";
            $info = $wpdb->get_results($sql1,ARRAY_A);

            if(!empty($info)){
                //战队
                $team_name = $wpdb->get_var("SELECT p.post_title FROM {$wpdb->prefix}match_team AS mt 
                LEFT JOIN {$wpdb->posts} AS p ON p.ID=mt.team_id WHERE mt.user_id={$val['user_id']} AND mt.status=2");
                $result[$k]['team_name'] = $team_name;

                $user_info = array_column($info,'meta_value','meta_key');
                $user_real_name = !empty($user_info['user_real_name']) ? unserialize($user_info['user_real_name']) : '';
                $result[$k]['real_name'] = !empty($user_real_name['real_name']) ? $user_real_name['real_name'] : '-';
                $result[$k]['card'] = !empty($user_real_name['real_ID']) ? $user_real_name['real_ID'] : '-';
                $result[$k]['real_type'] = !empty($user_real_name['real_type']) ? $user_real_name['real_type'] : '-';
                $result[$k]['user_nationality'] = isset($user_info['user_nationality']) ? $user_info['user_nationality'] : '';

                if(!empty($user_info['user_age'])){
                    $age = $user_info['user_age'];
                    $group = getAgeGroupNameByAge($age);

                }else{
                    $group = '-';
                }
                if(!empty($user_info['user_address'])){
                    $user_address = unserialize($user_info['user_address']);
//                    $city = $user_address['city'] == '市辖区' ? $user_address['city'] : $user_address['province'];
                    $city = $user_address['province'].$user_address['city'];
                }else{
                    $city = '-';
                }

                $result[$k]['userID'] = $user_info['user_ID'];
                $result[$k]['address'] = $city;
                //$list[$k]['score'] = $val['my_score'];
                $result[$k]['ageGroup'] = $group;
                $result[$k]['age'] = $age;
                $result[$k]['sex'] = $user_info['user_gender'] ? $user_info['user_gender'] : '-';
                $result[$k]['birthday'] = isset($user_info['user_birthday']) ? $user_info['user_birthday'] : '-';
                $result[$k]['score'] = $val['my_score'] > 0 ? $val['my_score'] : 0;
                $result[$k]['my_score'] = $val['my_score'] > 0 ? $val['my_score'] : 0;
                $result[$k]['ranking'] = $ranking;
                if($val['my_score'] > 0) ++$ranking;

//                if($k != 0){
//                    if(($val['my_score'] == $result[$k-1]['my_score'] && $val['surplus_time'] == $result[$k-1]['surplus_time']) || ($val['my_score']== 0 && $result[$k-1]['my_score']==0)){
//                        $result[$k]['ranking'] = $result[$k-1]['ranking'];
//                    }
//                }
//                if($val['user_id'] == $current_user->ID){
//                    $my_ranking = $list[$k];
//                }
            }
        }
        return $result;
    }

    /**
     * 排名总数据
     */
    public function getAllRankingData($match,$projectArr,$op5){
        global $wpdb;
        if($op5 == 1){
            //个人排名
            //先查询所有成员
            $totalRanking = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS o.telephone,u.user_email,o.user_id,mq.project_id,u.user_mobile,o.created_time,um.meta_value AS user_age 
               FROM '.$wpdb->prefix.'order AS o
               LEFT JOIN '.$wpdb->users.' AS u ON u.ID=o.user_id
               LEFT JOIN '.$wpdb->usermeta.' AS um ON um.user_id=u.ID AND um.meta_key="user_age"
               LEFT JOIN '.$wpdb->prefix.'match_questions AS mq ON mq.user_id=u.ID
               WHERE o.match_id='.$match['match_id'].' AND o.pay_status IN(2,3,4) AND u.ID != "" GROUP BY o.user_id ORDER BY u.ID ASC', ARRAY_A);

            //查询每个成员分数
            foreach ($totalRanking as &$trv){
                $trv['my_score'] = 0;
                $trv['surplus_time'] = 0;
                $trv['created_microtime'] = 0;
                $trv['projectScore'] = []; //项目分数数组
                foreach ($projectArr as $paks => $pavs) {
                    $res = $wpdb->get_results('SELECT my_score,match_more,surplus_time,project_id,created_microtime FROM '.$wpdb->prefix.'match_questions 
                        WHERE match_id='.$match['match_id'].' AND user_id='.$trv['user_id'].' AND project_id='.$pavs['match_project_id'].' AND is_true = 1', ARRAY_A);
                    $scoreArr = [];//项目所有分数数组
                    $surplus_timeArr = [];//项目所有剩余时间数组
                    $created_microtimeArrr = [];//项目所提交毫秒数组
                    $moreArr = []; //每一轮分数数组
//                    $match_more_all = $wpdb->get_var('SELECT MAX(match_more) AS match_more FROM '.$wpdb->prefix.'match_questions WHERE match_id='.$match['match_id'].' AND user_id='.$trv['user_id'].' AND project_id='.$pavs['match_project_id'].' GROUP BY project_id,user_id');
                    $match_more_all = $wpdb->get_var('SELECT count(id) FROM '.$wpdb->prefix.'match_project_more WHERE match_id='.$match['match_id'].' AND project_id='.$pavs['match_project_id']);
//                    $match_more_all = $pavs['match_more'] > 0 ? $pavs['match_more'] : $match['match_more'];
                    for($mi = 1; $mi <= $match_more_all; ++$mi){
                        $moreArr[$mi] = '0';
                    }
                    foreach ($res as $resV){
                        $surplus_timeArr[] = $resV['surplus_time'];
                        $scoreArr[] = $resV['my_score'];
                        $created_microtimeArrr[] = $resV['created_microtime'];
                        $moreArr[$resV['match_more']] = $resV['my_score'] ? $resV['my_score'] : '0';
                    }
                    $trv['projectScore'][$paks] = join('/', $moreArr);//每个项目分数字符串
                    $trv['my_score'] += $scoreArr == [] ? 0 : max($scoreArr);//每个项目最大分数和
                    $trv['surplus_time'] += $scoreArr == [] ? 0 : max($surplus_timeArr);//每个项目最大剩余时间和
                    $trv['created_microtime'] += $created_microtimeArrr == [] ? 0 : max($created_microtimeArrr);//每个项目提交毫秒时间和
                }
                //战队
                $team_name = $wpdb->get_var("SELECT p.post_title FROM {$wpdb->prefix}match_team AS mt 
                LEFT JOIN {$wpdb->posts} AS p ON p.ID=mt.team_id WHERE mt.user_id={$trv['user_id']} AND mt.status=2");
                $trv['team_name'] = $team_name;

                $usermeta = get_user_meta($trv['user_id'], '', true);
                $user_real_name = unserialize($usermeta['user_real_name'][0]);
                $age = $user_real_name['real_age'];
                $real_name = $user_real_name['real_name'];
                $trv['age'] = $age;
                $trv['ageGroup'] = getAgeGroupNameByAge($age);
                $trv['userID'] = $usermeta['user_ID'][0];
                $trv['real_name'] = $real_name;
                $trv['sex'] = $usermeta['user_gender'][0];
                $trv['birthday'] = isset($usermeta['user_birthday']) ? $usermeta['user_birthday'][0] : '-';
                $trv['address'] = unserialize($usermeta['user_address'][0])['province'].unserialize($usermeta['user_address'][0])['city'];
                $trv['user_nationality'] = isset($usermeta['user_nationality']) ? $usermeta['user_nationality'][0] : '';
                $trv['card'] = isset($user_real_name['real_ID']) ? $user_real_name['real_ID'] : '';
                $trv['real_type'] = isset($user_real_name['real_type']) ? $user_real_name['real_type'] : '';
            }

        }else{
            //战队排名

            //获取参加比赛的成员
            $sql = "SELECT p.post_title,p.ID,o.user_id FROM `{$wpdb->prefix}order` AS o 
                    LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON o.user_id=mt.user_id AND mt.status=2 
                    LEFT JOIN `{$wpdb->posts}` AS p ON p.ID=mt.team_id 
                    WHERE o.match_id={$match['match_id']} AND o.pay_status IN(2,3,4) AND mt.team_id!='' AND p.post_title!=''";
            $result = $wpdb->get_results($sql, ARRAY_A);
            //处理每个战队的成员
            $teamsUsers = []; //每个战队的每个成员
            foreach ($result as $resV){
                if(!isset($teamsUsers[$resV['ID']])) {
                    $teamsUsers[$resV['ID']] = [];
                    $teamsUsers[$resV['ID']]['user_ids'] = [];
                    $teamsUsers[$resV['ID']]['team_name'] = $resV['post_title'];
                    $teamsUsers[$resV['ID']]['team_id'] = $resV['ID'];
                }
                $teamsUsers[$resV['ID']]['user_ids'][] = $resV['user_id'];
            }
            foreach ($teamsUsers as &$tuV){
                $tuV['user_ids'] = join(',',$tuV['user_ids']);
            }
            $totalRanking = [];
            foreach ($teamsUsers as $tuV2){
                //每个战队的分数


                $sql = "SELECT SUM(my_score) AS my_score,SUM(surplus_time) AS surplus_time,SUM(created_microtime) AS created_microtime,user_id FROM 
                  (SELECT MAX(my_score) AS my_score,MAX(surplus_time) AS surplus_time,if(MAX(created_microtime) > 0, MAX(created_microtime) ,0) AS created_microtime,mq.user_id FROM `{$wpdb->prefix}match_questions` AS mq 
                  LEFT JOIN `{$wpdb->prefix}match_team` AS mt ON mt.user_id=mq.user_id AND mt.status=2 AND mt.team_id={$tuV2['team_id']}
                  WHERE mq.match_id={$match['match_id']} AND mt.team_id={$tuV2['team_id']} AND mq.user_id IN({$tuV2['user_ids']}) AND mq.is_true = 1 
                  GROUP BY mq.project_id,mq.user_id) AS child  
                  GROUP BY user_id 
                  ORDER BY my_score DESC limit 0,5
               ";
                $rows = $wpdb->get_results($sql,ARRAY_A);
                // leo_dump($wpdb->last_query);
                $tuV2['my_score'] = 0;
                $tuV2['surplus_time'] = 0;
                $tuV2['created_microtime'] = 0;
                $userScore = [];
                foreach ($rows as $key => $value) {
                    $user_real_name = get_user_meta($value['user_id'],'user_real_name', true);
                    $real_name = $user_real_name ? $user_real_name['real_name'] : '-';
                    $userScore[] = $real_name.': '.$value['my_score'];
                    $tuV2['my_score'] += $value['my_score'];
                    $tuV2['surplus_time'] += $value['surplus_time'];
                    $tuV2['created_microtime'] += $value['created_microtime'];
                }
                $tuV2['userScore'] = join("\r\n", $userScore);
                $totalRanking[] = $tuV2;
            }
        }

        //排序
        for($i = 0; $i < count($totalRanking); ++$i){
            if(isset($totalRanking[$i+1])){
                for ($j = $i+1; $j < count($totalRanking); ++$j){
                    if($totalRanking[$i]['my_score'] == $totalRanking[$j]['my_score']){
//                       if($totalRanking[$i]['my_score'] < 1){
//                           $rankingAuto = false;
//                       }else
                        if($totalRanking[$j]['surplus_time'] > $totalRanking[$i]['surplus_time']){

                            $a = $totalRanking[$j];
                            $totalRanking[$j] = $totalRanking[$i];
                            $totalRanking[$i] = $a;
                        }elseif ($totalRanking[$j]['surplus_time'] == $totalRanking[$i]['surplus_time']){
                            if($totalRanking[$j]['created_microtime'] < $totalRanking[$i]['created_microtime']){
                                $a = $totalRanking[$j];
                                $totalRanking[$j] = $totalRanking[$i];
                                $totalRanking[$i] = $a;
                            }
                        }
                    }elseif ($totalRanking[$j]['my_score'] > $totalRanking[$i]['my_score']){
                        $a = $totalRanking[$j];
                        $totalRanking[$j] = $totalRanking[$i];
                        $totalRanking[$i] = $a;
                    }
                }
            }
        }
        //名次
        $ranking = 1;
        foreach ($totalRanking as $k => $v){
            $totalRanking[$k]['ranking'] = $ranking;
            if( $totalRanking[$k]['my_score'] > 0){
                ++$ranking;
            }
        }
        return $totalRanking;
    }

    /**
     * 导出比奖金明细
     */
    public function match_bonus(){
        $match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;
        if($match_id < 1) exit('比赛id参数此错误');

        global $wpdb;
        $match = $wpdb->get_row('SELECT match_status FROM '.$wpdb->prefix.'match_meta_new WHERE match_id='.$match_id, ARRAY_A);

        //TODO 判断比赛是否结束
        if(!$match || $match['match_status'] != -3){
            exit('当前比赛未结束');
        }
        $match = get_post($match_id);
        $orderAllData = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}match_bonus WHERE match_id={$match_id} ORDER BY all_bonus DESC", ARRAY_A);

        //汇总
        $countData = [
            'bonus_all' => 0,
            'tax_all' => 0,
            'tax_send_all' => 0,
        ];
        foreach ($orderAllData as &$v) {
            $countData['bonus_all'] += $v['all_bonus'];
            $countData['tax_all'] += $v['tax_all'];
            $countData['tax_send_all'] += $v['tax_send_bonus'];
            $v['bonus_list'] = unserialize($v['bonus_list']);
        }

//        echo '<pre />';
//        print_r($orderAllData);

        $filename = 'bonus_';
        $filename .= current_time('timestamp').".xls";
//        $path = self::$downloadPath.$filename;
//        file_put_contents($path,$html);
        header('Pragma:public');
        header('Content-Type:application/x-msexecl;name="'.$filename.'"');
        header('Content-Disposition:inline;filename="'.$filename.'"');
        require_once PLUGINS_PATH.'nlyd-student/library/Vendor/PHPExcel/Classes/PHPExcel/RichText.php';
        require_once PLUGINS_PATH.'nlyd-student/library/Vendor/PHPExcel/Classes/PHPExcel/Style/Color.php';
        $objPHPExcel = new \PHPExcel();

//        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal('center');
//        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('center');

        //居中显示
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('center');
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical('center');

        //行高
//        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(50);
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(70);
        $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(40);




        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);

        $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getAlignment()->setWrapText(true);
        $titleObjRichText = new \PHPExcel_RichText();
        $titlePayable = $titleObjRichText->createTextRun($match->post_title);
        $titlePayable->getFont()->setBold( true);
        $titlePayable->getFont()->setSize( 20);
//        $objRichText->createTextRun($is_send)->getFont()->setColor( new \PHPExcel_Style_Color( $color ) );//设置颜色
        $titleObjRichText->createTextRun("\n（奖金总额{$countData['bonus_all']}元，税后发放额{$countData['tax_send_all']}元，代扣税{$countData['tax_all']}元）")->getFont()->setSize( 16);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $titleObjRichText);
        //边加粗
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('C2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('D2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('E2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('F2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('G2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('H2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('I2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('J2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('K2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('L2')->getBorders()->getAllBorders()->setBorderStyle('thin');

//        $objPHPExcel->getActiveSheet()->getStyle( 'A1')->getFont()->setSize(16)->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'A2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'B2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'C2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'D2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'E2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'F2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'G2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'H2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'I2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'J2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'K2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'L2')->getFont()->setBold(true);

        //自动换行
        $objPHPExcel->getActiveSheet()->mergeCells('A1:L1');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '选手ID');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', '选手姓名');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', '奖项/类别');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', '奖金数额');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', '奖金总额');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', '扣税总额');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', '税后发放总额');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', '收款路径');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', '身份证号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J2', '电话号码');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K2', '所属战队');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L2', '是否发放');

        foreach ($orderAllData as $k => $row){

            $bonus_name_str = [];
            $bonus_str = [];
             foreach ($row['bonus_list'] as $bonus_name){
                 $bonus_name_str[] = $bonus_name['bonus_name'];
                 $bonus_str[] = $bonus_name['bonus'];
            }
             if($row['is_send'] == 2){
                 $is_send = '已发放';
                 $color = \PHPExcel_Style_Color::COLOR_DARKGREEN;
             }else{
                 $is_send = '未发放';
                 $color = '00bf0000';
             }
            $objRichText = new \PHPExcel_RichText();
            $objRichText->createTextRun($is_send)->getFont()->setColor( new \PHPExcel_Style_Color( $color ) );//设置颜色

            $tax_allObjRichText = new \PHPExcel_RichText();
            $tax_allObjRichText->createTextRun(' ￥'.$row['tax_all'])->getFont()->setColor( new \PHPExcel_Style_Color( '00FF0000' ) );//设置颜色

            $tax_send_bonusObjRichText = new \PHPExcel_RichText();
            $tax_send_bonusObjRichText->createTextRun(' ￥'.$row['tax_send_bonus'])->getFont()->setColor( new \PHPExcel_Style_Color( '0008C715' ) );//设置颜色

            //换行
            $objPHPExcel->getActiveSheet()->getStyle('C'.($k+3))->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('D'.($k+3))->getAlignment()->setWrapText(true);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($k+3),' '.$row['userID']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($k+3),' '.$row['real_name']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($k+3),' '.join("\n",$bonus_name_str));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($k+3),' '.join("\n",$bonus_str));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($k+3),' ￥'.$row['all_bonus']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($k+3),$tax_allObjRichText);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($k+3),$tax_send_bonusObjRichText);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+3),' '.$row['card_num']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.($k+3),' '.$row['mobile']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.($k+3),' '.$row['team']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.($k+3),$objRichText);

            //超链接
            if(get_user_meta($row['user_id'],'user_coin_code',true)[0]){
                $path_bonusObjRichText = new \PHPExcel_RichText();
                $path_bonusObjRichText->createTextRun('二维码收款路径')->getFont()->setColor( new \PHPExcel_Style_Color( '005e70cc' ) );//设置颜色
                $objPHPExcel->getActiveSheet()->setCellValue('H'.($k+3), $path_bonusObjRichText);
                $objPHPExcel->getActiveSheet()->getCell('H'.($k+3))->getHyperlink()->setUrl(get_user_meta($row['user_id'],'user_coin_code',true)[0]);
            }else{
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($k+3),' ');
            }

//            $objPHPExcel->getActiveSheet()->getCell('H'.($k+3))->getHyperlink()->setTooltip('Navigate to website');
//            $objPHPExcel->getActiveSheet()->getStyle('H'.($k+3))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        return;
    }

    /**
     * 导出战队成员
     */
    public function team_member(){
        $team_id = isset($_GET['team_id']) ? intval($_GET['team_id']) : 0;
        $team_id < 1 && exit('参数错误');

        global $wpdb;
        $sql = 'SELECT m.id,u.user_login,u.user_email,u.user_mobile,m.status,m.user_id,m.user_type 
                FROM '.$wpdb->prefix.'match_team AS m 
                LEFT JOIN '.$wpdb->users.' AS u ON u.ID=m.user_id 
                WHERE m.team_id='.$team_id.' AND m.status=2 AND u.ID !=""';
        $rows = $wpdb->get_results($sql, ARRAY_A);



        $filename = 'team_member_';
        $filename .= current_time('timestamp').".xls";
//        $path = self::$downloadPath.$filename;
//        file_put_contents($path,$html);
        header('Pragma:public');
        header('Content-Type:application/x-msexecl;name="'.$filename.'"');
        header('Content-Disposition:inline;filename="'.$filename.'"');
        require_once PLUGINS_PATH.'nlyd-student/library/Vendor/PHPExcel/Classes/PHPExcel/RichText.php';
        require_once PLUGINS_PATH.'nlyd-student/library/Vendor/PHPExcel/Classes/PHPExcel/Style/Color.php';
        $objPHPExcel = new \PHPExcel();

//        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal('center');
//        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('center');

        //居中显示
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('center');
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical('center');

        //行高
//        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(50);
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(70);
        $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(40);




        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

        $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getAlignment()->setWrapText(true);
        $titleObjRichText = new \PHPExcel_RichText();
        $titlePayable = $titleObjRichText->createTextRun(get_post($team_id)->post_title);
        $titlePayable->getFont()->setBold( true);
        $titlePayable->getFont()->setSize( 20);
//        $objRichText->createTextRun($is_send)->getFont()->setColor( new \PHPExcel_Style_Color( $color ) );//设置颜色

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $titleObjRichText);
        //边加粗
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('C2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('D2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('E2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('F2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('G2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('H2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $objPHPExcel->getActiveSheet()->getStyle('I2')->getBorders()->getAllBorders()->setBorderStyle('thin');

//        $objPHPExcel->getActiveSheet()->getStyle( 'A1')->getFont()->setSize(16)->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'A2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'B2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'C2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'D2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'E2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'F2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'G2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'H2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle( 'I2')->getFont()->setBold(true);

        //自动换行
        $objPHPExcel->getActiveSheet()->mergeCells('A1:L1');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '用户名');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', '姓名');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', 'ID');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', '所在地区');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', '邮箱');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', '手机');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', '年龄');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', '性别');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', '类别');

        foreach ($rows as $k => $row){
            $usermeta = get_user_meta($row['user_id']);
            $user_real_name = isset($usermeta['user_real_name'][0]) ? unserialize($usermeta['user_real_name'][0]) : [];
            $user_address = isset($usermeta['user_address'][0]) ? unserialize($usermeta['user_address'][0]) : [];
            if($user_address != []) $user_address = $user_address['province'].$user_address['city'].$user_address['area'];
            else $user_address = '';

//            $objRichText = new \PHPExcel_RichText();
//            $objRichText->createTextRun($is_send)->getFont()->setColor( new \PHPExcel_Style_Color( $color ) );//设置颜色
//
//            $tax_allObjRichText = new \PHPExcel_RichText();
//            $tax_allObjRichText->createTextRun(' ￥'.$row['tax_all'])->getFont()->setColor( new \PHPExcel_Style_Color( '00FF0000' ) );//设置颜色
//
//            $tax_send_bonusObjRichText = new \PHPExcel_RichText();
//            $tax_send_bonusObjRichText->createTextRun(' ￥'.$row['tax_send_bonus'])->getFont()->setColor( new \PHPExcel_Style_Color( '0008C715' ) );//设置颜色

            //换行
            $objPHPExcel->getActiveSheet()->getStyle('C'.($k+3))->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('D'.($k+3))->getAlignment()->setWrapText(true);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($k+3),' '.$row['user_login']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($k+3),' '.isset($user_real_name['real_name']) ? $user_real_name['real_name'] : '-');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($k+3),' '.$usermeta['user_ID'][0]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($k+3),' '.$user_address);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($k+3),' '.$row['user_email']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($k+3),' '.$row['user_mobile']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($k+3),' '.isset($user_real_name['real_age']) ? $user_real_name['real_age'] : '');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($k+3),' '.isset($usermeta['user_gender'][0]) ? $usermeta['user_gender'][0] : '');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+3),' '.$row['user_type'] == 2 ? '教练' : '学员');

        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        return;

    }
    /**
     * 测试公共导出
     */
    public function testPublicExport(){
        $data = [
            ['age' => 10, 'name' => '马尿', 'sex' => '女'],
            ['age' => 12, 'name' => '罗一斤', 'sex' => '女', 'link' => 'http://dssd.dsad.cc/img.img', 'link_name' => '图片', 'link_key' => 'name'],
        ];
        $title = '测试导出';
        $field = [
            ['letter' => 'A','width' => 80, 'title_key' => 'age', 'title' => '年龄'],
            ['letter' => 'B','width' => 60, 'title_key' => 'name', 'title' => '名字'],
            ['letter' => 'C','width' => 40, 'title_key' => 'sex', 'title' => '性别'],
        ];
        $file_name = 'testPublic';
        $this->publicExport($data,$field,$title,$file_name);
    }

    /**
     * 导出提现记录
     */
    public function exportExtractLog(){
        $start_date = isset($_POST['start']) ? trim($_POST['start']) : '';
        $end_date1 = isset($_POST['end']) ? trim($_POST['end']) : '';
        if($start_date == '' || $end_date1 == '') exit;
        $start_date = date("Y-m-d",strtotime($start_date));
        $end_date = date("Y-m-d",strtotime("+1 day",strtotime($end_date1)));
        global $wpdb;
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS ue.*,um.meta_value AS censor_real_name,zm.zone_name 
                FROM {$wpdb->prefix}user_extract_logs AS ue 
                LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=ue.censor_user_id AND um.meta_key='user_real_name'
                LEFT JOIN {$wpdb->prefix}zone_meta AS zm ON zm.user_id=ue.extract_id
                WHERE ue.apply_time BETWEEN '{$start_date}' AND '{$end_date}'",ARRAY_A);
        $title = $start_date.'_'.$end_date.'extractLog';
        $field = [
            ['letter' => 'A','width' => 50, 'title_key' => 'real_name', 'title' => '提现主体'],
            ['letter' => 'B','width' => 20, 'title_key' => 'extract_type', 'title' => '提现类型'],
            ['letter' => 'C','width' => 20, 'title_key' => 'extract_amount', 'title' => '提现金额'],
            ['letter' => 'D','width' => 30, 'title_key' => 'extract_mode', 'title' => '提现方式'],
            ['letter' => 'E','width' => 80, 'title_key' => 'extract_account', 'title' => '提现账户', 'link'],
            ['letter' => 'F','width' => 30, 'title_key' => 'apply_time', 'title' => '发起时间'],
            ['letter' => 'G','width' => 30, 'title_key' => 'censor_time', 'title' => '处理时间'],
            ['letter' => 'H','width' => 30, 'title_key' => 'extract_status', 'title' => '提现状态'],
            ['letter' => 'I','width' => 30, 'title_key' => 'censor_user_id', 'title' => '处理人'],
        ];
        foreach ($rows as &$row){
            $zone_meta = $wpdb->get_row("SELECT zone_name,zone_city,zone_match_type,type_id FROM {$wpdb->prefix}zone_meta WHERE user_id='{$row['extract_id']}'", ARRAY_A);
            $type_name = '';
            if($zone_meta){
                $type_alias = $wpdb->get_var("SELECT zone_type_alias FROM {$wpdb->prefix}zone_type WHERE id={$zone_meta['type_id']}");
                switch ($type_alias){
                    case 'match':
                        $real_name = date('Y').'脑力世界杯' .$zone_meta['zone_city'].($zone_meta['zone_match_type']=='1'?'战队精英赛':'城市赛');
                        break;
                    case 'trains':
                        $real_name = 'IISC'.$zone_meta['zone_name'].'国际脑力训练中心';
                        break;
                    case 'test':
                        $real_name =  'IISC' .$zone_meta['zone_name'].'国际脑力测评中心';
                        break;
                }
                $type_name = '机构';
            }else{
                $real_name = get_user_meta($row['extract_id'],'user_real_name',true)['real_name'];
                $type_name = '个人';
            }
            switch ($row['extract_type']){
                case 'weChat':
                    $row['extract_mode'] = '微信';
                    break;
                case 'wallet':
                    $row['extract_mode'] = '钱包';
                    break;
                case 'bank':
                    $row['extract_mode'] = $row['bank_address'];
                    break;
            }
            switch ($row['extract_type']){
                case 'weChat':
                    $row['link_name'] = '收款二维码';
                    $row['link_key'] = 'extract_account';
                    $row['link'] = $row['extract_code_img'];
                    break;
                case 'wallet':
                    $row['extract_account'] = '钱包';
                    echo '钱包';
                    break;
                case 'bank':
//                    $row['extract_account'] = $row['extract_account'];
                    break;
            }
            switch ($row['extract_status']){
                case '1':
                    $row['extract_status'] =  '审核中';
                    break;
                case '2':
                    $row['extract_status'] = '已提现';
                    break;
                case '3':
                    $row['extract_status'] = '未通过';
                    break;
            }
            $row['real_name'] = $real_name;
            $row['extract_type'] = $type_name;
            if($row['censor_real_name']){
                $row['censor_user_id'] = unserialize($row['censor_real_name'])['real_name'];
            }else{
                $row['censor_user_id'] = $wpdb->get_var("SELECT user_login FROM {$wpdb->users} WHERE ID='{$row['censor_user_id']}'");
            }
        }
        $this->publicExport($rows,$field,$title,$start_date.'_'.$end_date1.'ExtractLog');
    }

    /**
     * 导出考级过级记录
     */
    public function exportGradingAdoptLog() {
        $start_date = isset($_POST['begin']) ? trim($_POST['begin']) : '';
        $end_date1 = isset($_POST['end']) ? trim($_POST['end']) : '';
        $cate_type = isset($_POST['type_id']) ? intval($_POST['type_id']) : 0;
        if($start_date == '' || $end_date1 == '') exit;
        $start_date = date("Y-m-d",strtotime($start_date));
        $end_date = date("Y-m-d",strtotime("+1 day",strtotime($end_date1)));
        $where = '';
        if($cate_type > 0){
            $where .= " AND gm.category_id='{$cate_type}'";
        }
        global $wpdb;
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS gl.*,ma.fullname,ma.telephone,ma.country,ma.province,ma.city,ma.area,ma.address,gm.category_id
                FROM {$wpdb->prefix}grading_logs AS gl
                LEFT JOIN {$wpdb->prefix}my_address AS ma ON ma.user_id=gl.user_id AND ma.is_default=1   
                LEFT JOIN {$wpdb->prefix}grading_meta AS gm ON gm.grading_id=gl.grading_id   
                WHERE gl.grading_result=1{$where} AND gl.created_time BETWEEN '{$start_date}' AND '{$end_date}'
                ORDER BY gl.created_time DESC", ARRAY_A);
        $title = $start_date.'_'.$end_date.'GradingAdoptLog';
        $field = [
            ['letter' => 'A','width' => 30, 'title_key' => 'real_name', 'title' => '姓名/ID'],
            ['letter' => 'B','width' => 15, 'title_key' => 'user_gender', 'title' => '性别'],
            ['letter' => 'C','width' => 30, 'title_key' => 'card_num', 'title' => '证件号码'],
            ['letter' => 'D','width' => 15, 'title_key' => 'cimg', 'title' => '用户寸照'],
            ['letter' => 'E','width' => 20, 'title_key' => 'grading_level', 'title' => '考级类别'],
            ['letter' => 'F','width' => 20, 'title_key' => 'coach', 'title' => '教练'],
            ['letter' => 'G','width' => 20, 'title_key' => 'created_time', 'title' => '过级时间'],
            ['letter' => 'H','width' => 60, 'title_key' => 'address', 'title' => '收件地址'],
            ['letter' => 'I','width' => 30, 'title_key' => 'prove', 'title' => '证书'],
//            ['letter' => 'J','width' => 30, 'title_key' => 'prove_time', 'title' => '发放时间'],
        ];
        $categoryArr = getCategory();
        $categoryArr = array_column($categoryArr, NULL, 'ID');
        foreach ($rows as &$row){
            $user_meta = get_user_meta($row['user_id']);
            $coach_name = get_user_meta($row['grading_coach_id'],'user_real_name',true)['real_name'];
            $row['real_name'] = unserialize($user_meta['user_real_name'][0])['real_name'].'/'.$user_meta['user_ID'][0];
            $row['user_gender'] = $user_meta['user_gender'][0];
            $row['card_num'] = unserialize($user_meta['user_real_name'][0])['real_ID'];
            $row['link'] = unserialize($user_meta['user_images_color'][0])[0];
            $row['link_key'] = 'cimg';
            $row['link_name'] = '查看';
            $row['grading_level'] = $categoryArr[$row['category_id']]['post_title'].$row['grading_lv'].'级';
            $row['coach'] = $coach_name;
            $row['address'] = $row['fullname'].'  '.$row['telephone'].'  '.$row['province'].$row['city'].$row['area'].$row['address'];
            $row['prove'] = $row['prove_grant_status'] == '2' ? "{$row['prove_number']}\n{$row['prove_grant_time']}" : '未发放';
//            $row['prove_time'] = $row['prove_grant_time'];

        }

        $this->publicExport($rows,$field,$title,$start_date.'_'.$end_date1.'GradingAdoptLog');
    }

    /**
     * 公用导出方法
     */
    public function publicExport($data,$field,$title,$file_name){
        if(!is_array($data) || !is_array($data[0])) return false;
        $filename = $file_name.".xls";
//        $path = self::$downloadPath.$filename;
//        file_put_contents($path,$html);
        header('Pragma:public');
        header('Content-Type:application/x-msexecl;name="'.$filename.'"');
        header('Content-Disposition:inline;filename="'.$filename.'"');
        require_once PLUGINS_PATH.'nlyd-student/library/Vendor/PHPExcel/Classes/PHPExcel/RichText.php';
        require_once PLUGINS_PATH.'nlyd-student/library/Vendor/PHPExcel/Classes/PHPExcel/Style/Color.php';
        $objPHPExcel = new \PHPExcel();

//        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal('center');
//        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('center');

        //居中显示
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('center');
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical('center');

        //行高
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(40);
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
        $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
        $a = 'A';
        foreach ($field as $fv){
            $objPHPExcel->getActiveSheet()->getColumnDimension($fv['letter'])->setWidth($fv['width']);

        }
        $c = count($field)-1;
        for ($i = 0; $i < $c; ++$i){
            ++$a;
        };
        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$a.'1');//合并行


        $titleObjRichText = new \PHPExcel_RichText();
        $titlePayable = $titleObjRichText->createTextRun($title);
        $titlePayable->getFont()->setBold( true);
        $titlePayable->getFont()->setSize( 20);
//        $objRichText->createTextRun($is_send)->getFont()->setColor( new \PHPExcel_Style_Color( $color ) );//设置颜色

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $titleObjRichText);
        //边加粗
        foreach ($field as $fv2){
            $objPHPExcel->getActiveSheet()->getStyle($fv2['letter'].'2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        }

//        $objPHPExcel->getActiveSheet()->getStyle( 'A1')->getFont()->setSize(16)->setBold(true);
        foreach ($field as $fv3){
            $objPHPExcel->getActiveSheet()->getStyle($fv3['letter'].'2')->getFont()->setBold(true);
        }

        //自动换行
        foreach ($field as $fv4){
            $objPHPExcel->getActiveSheet()->getStyle($fv4['letter'].'2')->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($fv4['letter'].'2', $fv4['title']);
        }

        foreach ($data as $k => $dataV){
            foreach ($field as $fv5){
                $objPHPExcel->getActiveSheet()->getStyle($fv5['letter'].($k+3))->getAlignment()->setWrapText(true);//自动换行
                if(isset($dataV['link_key']) && $dataV['link_key'] == $fv5['title_key'] && $dataV['link']){
                    //超链接
                    $path_bonusObjRichText = new \PHPExcel_RichText();
                    $path_bonusObjRichText->createTextRun($dataV['link_name'])->getFont()->setColor( new \PHPExcel_Style_Color( '005e70cc' ) );//设置颜色
                    $objPHPExcel->getActiveSheet()->setCellValue($fv5['letter'].($k+3), $path_bonusObjRichText);
                    $objPHPExcel->getActiveSheet()->getCell($fv5['letter'].($k+3))->getHyperlink()->setUrl($dataV['link']);

                }else{
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($fv5['letter'].($k+3),' '.$dataV[$fv5['title_key']]);
                }
            }
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        return;
    }
}
new Download();

