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
        if(!isset($_GET['action']) || !method_exists($this, $_GET['action'])) exit;
        $action = $_GET['action'];
        $this->$action();
        exit;
    }

    public function custom_rewrite_basic(){

    }

    public function order(){
        global $wpdb;
        if(empty($_POST['start_date']) || empty($_POST['end_date'])) exit('请选择日期');
        $start = date_i18n('Y-m-d H:i:s', strtotime($_POST['start_date']));
        $end = date_i18n('Y-m-d H:i:s', strtotime($_POST['end_date']));

        $rows = $wpdb->get_results('SELECT
        o.serialnumber,
        o.cost,
        IFNULL(o.fullname,"-") AS fullname,
        IFNULL(o.telephone,"-") AS telephone,
        IFNULL(o.address,"-") AS address,
        IFNULL(o.express_number,"-") AS express_number,
        IFNULL(o.express_company,"-") AS express_company,
        CASE o.order_type WHEN 1 THEN "比赛订单" ELSE "-" END AS order_type,
        CASE o.pay_type WHEN "zfb" THEN "支付宝" WHEN "wx" THEN "微信" WHEN "ylk" THEN "银联卡" ELSE o.pay_type END AS pay_type,
        CASE o.pay_status WHEN 1 THEN "待支付" WHEN -1 THEN "待退款" WHEN -2 THEN "已退款" WHEN 2 THEN "支付完成" ELSE "-" END AS pay_title,
        u.user_login,
        p.post_title,
        o.pay_status,
        o.created_time
        FROM '.$wpdb->prefix.'order AS o
        LEFT JOIN '.$wpdb->users.' AS u ON o.user_id=u.ID
        LEFT JOIN '.$wpdb->posts.' AS p ON o.match_id=p.ID
        WHERE o.created_time BETWEEN "'.$start.'" AND "'.$end.'"', ARRAY_A);


        $date = date_i18n('YmdHis', strtotime($_POST['start_date'])).'-'.date_i18n('YmdHis', strtotime($_POST['end_date']));
        $filename = 'order_';
        $filename .= $date."_";
        $filename .= current_time('timestamp').".xls";
//        $path = self::$downloadPath.$filename;
//        file_put_contents($path,$html);
        header('Pragma:public');
        header('Content-Type:application/x-msexecl;name="'.$filename.'"');
        header('Content-Disposition:inline;filename="'.$filename.'"');
        require_once LIBRARY_PATH.'Vendor/PHPExcel/Classes/PHPExcel.php';
        require_once LIBRARY_PATH.'Vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();


        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('center');
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical('center');
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);


        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $start.'至'.$end.'订单');
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(35);//第一行行高
        $objPHPExcel->getActiveSheet()->mergeCells('A1:M1');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
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
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', '收获地址');
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
        $rows = $wpdb->get_results('SELECT u.ID,u.user_login,u.display_name,u.user_mobile,u.user_email,o.created_time,o.address,o.telephone FROM '.$wpdb->prefix.'order AS o 
        LEFT JOIN '.$wpdb->users.' AS u ON u.ID=o.user_id 
        WHERE o.order_type=1 AND o.pay_status!=-2 AND o.match_id='.$match->ID.' LIMIT '.$start.','.$pageSize, ARRAY_A);




        $filename = 'match_student_';
        $filename .= current_time('timestamp').".xls";
//        $path = self::$downloadPath.$filename;
//        file_put_contents($path,$html);
        header('Pragma:public');
        header('Content-Type:application/x-msexecl;name="'.$filename.'"');
        header('Content-Disposition:inline;filename="'.$filename.'"');
        require_once LIBRARY_PATH.'Vendor/PHPExcel/Classes/PHPExcel.php';
        require_once LIBRARY_PATH.'Vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal('center');
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('center');


        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $match->post_title);

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


        $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', 'ID');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', '用户名');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', '真实姓名');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', '性别');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', '出生日期');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', '年龄组别');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', '所在地区');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', '手机');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', '邮箱');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J2', '报名时间');
        foreach ($rows as $k => $row){
            $usermeta = get_user_meta($row['ID'], '', true);
            $age = unserialize($usermeta['user_real_name'][0])['real_age'];
            $group = $this->getAgeGroupNameByAge($age);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($k+3),' '.$usermeta['user_ID'][0]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($k+3),' '.$row['user_login']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($k+3),' '.unserialize($usermeta['user_real_name'][0])['real_name']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($k+3),' '.$usermeta['user_gender'][0]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($k+3),' '.$usermeta['user_birthday'][0]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($k+3),' '.$group);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($k+3),' '.unserialize($usermeta['user_address'][0])['province'].unserialize($usermeta['user_address'][0])['city']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($k+3),' '.$row['telephone']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+3),' '.$row['user_email']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.($k+3),' '.$row['created_time']);
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
    public function match_ranking(){

        global $wpdb;

        //首先获取当前比赛
        $post = get_post(intval($_GET['match_id']));
        $match = $wpdb->get_row('SELECT match_status,match_more FROM '.$wpdb->prefix.'match_meta WHERE match_id='.$post->ID, ARRAY_A);

        //TODO 判断比赛是否结束
        if(!$match || $match['match_status'] != -3){
            echo '<br /><h2 style="color: #a80000">比赛未结束!</h2>';
            return;
        }

        //查询比赛小项目

//        $projectArr = $wpdb->get_results('SELECT ID,post_title FROM '.$wpdb->posts.' WHERE post_type="project" AND post_status="publish"', ARRAY_A);
        $projectArr = get_match_end_time($post->ID);

        $categoryArr = []; //分类选项卡数组
        foreach ($projectArr as $pak => $pav) {
            //获取类别和项目选项卡
            if (in_array($pav['project_alias'], ['pkjl', 'szzb'])) {
                $categoryArr['sjl']['post_title'] = '速记类';
                if(!isset($categoryArr['sjl']['ids'])) $categoryArr['sjl']['ids'] = '';
                $categoryArr['sjl']['ids'] .= $pav['match_project_id'].',';
            } elseif (in_array($pav['project_alias'], ['kysm', 'wzsd'])) {
                $categoryArr['sdl']['post_title'] = '速读类';
                if(!isset($categoryArr['sdl']['ids'])) $categoryArr['sdl']['ids'] = '';
                $categoryArr['sdl']['ids'] .= $pav['match_project_id'].',';
            } elseif (in_array($pav['project_alias'], ['zxss', 'nxss'])) {
                $categoryArr['ssl']['post_title'] = '速算类';
                if(!isset($categoryArr['ssl']['ids'])) $categoryArr['ssl']['ids'] = '';
                $categoryArr['ssl']['ids'] .= $pav['match_project_id'].',';;
            }
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
                        foreach ($projectArr as $pak => $pav) {
                            if($pav['match_project_id'] == $_GET['op3']){
                                $post_title = $pav['post_title'];
                                break;
                            }
                        }

                    }else{
                        //默认地一个项目
                        $where = ' AND project_id ='.$projectArr[0]['match_project_id'].' GROUP BY match_id';
                        $post_title = $projectArr[0]['post_title'];
                        $_GET['op3'] = $projectArr[0]['match_project_id'];
                    }
                    $selectArr = [['post_title' => $post_title]];
                    $downloadParam .= '&op3='.$_GET['op3'].'&op4='.$_GET['op4'];;
                    break;
                default:
                    $selectArr = $projectArr;
            }
        }else{
            $selectArr = $projectArr;
        }
//        var_dump($projectWhere);
//        //是否选择组别分类
//        $group = 0;
//        if(is_post()){
//            $group = intval($_POST['age_group']);
//        }



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
            foreach ($selectArr as  $pak => $pav) {
                $trv['projectScore'][$pak] = 0;
                if($where == '' || $whereT == 1) {
                    $where = ' AND project_id='.$pav['match_project_id'];
                    $whereT = 1;
                }
                if($selectType == 2){
                    $trv['projectScore'][$pak] = 0;
                }else{
                    $trv['projectScore'][$pak] = '';
                }
//                $trv['projectScore'][$pak] = '';
                $res = $wpdb->get_results('SELECT '.$score.',match_more,surplus_time,project_id FROM '.$wpdb->prefix.'match_questions AS mq 
                WHERE match_id='.$post->ID.' AND user_id='.$trv['user_id'].$where, ARRAY_A);


//            var_dump($where);
                $moreArr = [];
                $scoreArr = [];
                $surplus_timeArr = [];
                if($selectType == 1){
                    for($mi = 1; $mi <= $match['match_more']; ++$mi){
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
                        $trv['projectScore'][$pak] += $mav;
                    }else{
                        $trv['projectScore'][$pak] .= $mav;
                    }
                }
                if($selectType == 2) $trv['projectScore'][$pak] .= '/';

                if(!$trv['projectScore'][$pak]) $trv['projectScore'][$pak] = '0/';
//                var_dump($trv['projectScore'][$pak]);
                $trv['projectScore'][$pak] = substr($trv['projectScore'][$pak], 0, strlen($trv['projectScore'][$pak])-1);
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
        require_once LIBRARY_PATH.'Vendor/PHPExcel/Classes/PHPExcel.php';
        require_once LIBRARY_PATH.'Vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';
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
            ++$a;
            $objPHPExcel->getActiveSheet()->getColumnDimension($a)->setWidth(15);

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
            ++$a;
            $objPHPExcel->getActiveSheet()->getStyle( $a.'2')->getFont()->setBold(true);

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
}
new Download();

//TODO 导出排名原方法




//
//
//        global $wpdb;
//
//        //首先获取当前比赛
//        $post = get_post(intval($_GET['match_id']));
////        $match = $wpdb->get_row('SELECT match_status FROM '.$wpdb->prefix.'match_meta WHERE match_id='.$post->ID, ARRAY_A);
//        //TODO 判断比赛是否结束
//
//        //查询比赛项目
//        $projectArr = $wpdb->get_results('SELECT ID,post_title FROM '.$wpdb->posts.' WHERE post_type="project" AND post_status="publish"', ARRAY_A);
//
//        //根据成绩排序查询比赛学员
//        $matchQuestions = $wpdb->get_results('SELECT u.user_email,mq.user_id,mq.project_id,mq.match_more,mq.my_score,mq.answer_status,p.post_title,o.created_time,o.telephone,mq.surplus_time  FROM '.$wpdb->prefix.'match_questions AS mq
//        LEFT JOIN '.$wpdb->prefix.'order AS o ON o.match_id=mq.match_id AND o.user_id=mq.user_id
//        LEFT JOIN '.$wpdb->users.' AS u ON u.ID=mq.user_id
//        LEFT JOIN '.$wpdb->posts.' AS p ON p.ID=mq.project_id WHERE mq.match_id='.$post->ID,ARRAY_A);
//        //处理数据
//        $rankingArr = [];
//        foreach ($matchQuestions as $mqk => $mqv){
//            $usermeta = get_user_meta($mqv['user_id'], '', true);
//
////            var_dump($usermeta);
//            //基础数据
//            if(!isset($rankingArr[$mqv['user_id']])){
//                $rankingArr[$mqv['user_id']] = [
//                    'user_ID' => $usermeta['user_ID'][0],
//                    'real_name' => unserialize($usermeta['user_real_name'][0])['real_name'],
//                    'sex' => $usermeta['user_gender'][0],
//                    'birthday' => $usermeta['user_birthday'][0],
//                    'age' => $this->getAgeGroupNameByAge(unserialize($usermeta['user_real_name'][0])['real_age']),
//                    'address' => unserialize($usermeta['user_address'][0])['province'].unserialize($usermeta['user_address'][0])['city'],
//                    'mobile' => $mqv['telephone'],
//                    'email' => $mqv['user_email'],
//                    'created_time' => $mqv['created_time'],
//                ];
//
//                $rankingArr[$mqv['user_id']]['total_score'] = $mqv['my_score'];
//                $rankingArr[$mqv['user_id']]['surplus_time'] = $mqv['surplus_time'];
//            }else{
//
//                $rankingArr[$mqv['user_id']]['total_score'] += $mqv['my_score'];
//                $rankingArr[$mqv['user_id']]['surplus_time'] += $mqv['surplus_time'];
//            }
//            //每个项目每一轮比赛成绩
//            foreach ($projectArr as $titleK => $titleV){
//                if($mqv['project_id'] == $titleV['ID']) {
//                    if(isset($rankingArr[$mqv['user_id']]['project'][$titleV['ID']]) && !empty($rankingArr[$mqv['user_id']]['project'][$titleV['ID']])){
//                        $rankingArr[$mqv['user_id']]['project'][$titleV['ID']] .= '/'.$mqv['my_score'];
//                    }else{
//                        $rankingArr[$mqv['user_id']]['project'][$titleV['ID']] = $mqv['my_score'];
//                    }
//                }else{
//                    if($rankingArr[$mqv['user_id']]['project'][$titleV['ID']] != '0') $rankingArr[$mqv['user_id']]['project'][$titleV['ID']] .= '0';
//                }
//            }
//
//        }
//
//        $arr = [];
//        foreach ($rankingArr as $rv){
//            $arr[] = $rv;
//        }
//        $rankingArr = $arr;
//        //排序
//        for ($i = 0; $i < count($rankingArr)-1; ++$i){
//            for ($j = $i+1; $j < count($rankingArr); ++$j){
//                if($rankingArr[$i]['total_score'] < $rankingArr[$j]['total_score']){
//                    $a = $rankingArr[$i];
//                    $rankingArr[$i] = $rankingArr[$j];
//                    $rankingArr[$j] = $a;
//                }elseif ($rankingArr[$i]['total_score'] == $rankingArr[$j]['total_score']){
//                    //分数相同根据剩余时间
//                    if($rankingArr[$i]['surplus_time'] < $rankingArr[$j]['surplus_time']){
//                        $a = $rankingArr[$i];
//                        $rankingArr[$i] = $rankingArr[$j];
//                        $rankingArr[$j] = $a;
//                    }
//                }
//
//            }
//        }
//


//
//
//
//global $wpdb;
//
////首先获取当前比赛
//$post = get_post(intval($_GET['match_id']));
//$match = $wpdb->get_row('SELECT match_status FROM '.$wpdb->prefix.'match_meta WHERE match_id='.$post->ID, ARRAY_A);
//
////TODO 判断比赛是否结束
//if(!$match || $match['match_status'] != -3){
//    echo '<br /><h2 style="color: #a80000">比赛未结束!</h2>';
//    return;
//}
//
////查询比赛小项目
//$projectArr = $wpdb->get_results('SELECT ID,post_title FROM '.$wpdb->posts.' WHERE post_type="project" AND post_status="publish"', ARRAY_A);
//
////是否选择组别分类
//$group = 0;
//if(is_post()){
//    $group = intval($_POST['age_group']);
//}
//$ageWhere = '';
//switch ($group){
//    case 4://儿童组
//        $ageWhere = ' AND um.mate_value<12';
//        break;
//    case 3://少年组
//        $ageWhere = ' AND um.mate_value>11 AND um.mate_value<18';
//        break;
//    case 2://成年组
//        $ageWhere = ' AND um.mate_value>17 AND um.mate_value<60';
//        break;
//    case 1://老年组
//        $ageWhere = ' AND um.mate_value>59';
//        break;
//    default://全部
//
//}
//
//
////查询每个参赛学员的总分排名
////分页
//
//$totalRanking = $wpdb->get_results('SELECT o.telephone,u.user_email,mq.user_id,mq.project_id,mq.match_more,SUM(mq.my_score) as my_score,mq.answer_status,SUM(mq.surplus_time) AS surplus_time,o.created_time FROM '.$wpdb->prefix.'match_questions AS mq
//            LEFT JOIN '.$wpdb->users.' AS u ON u.ID=mq.user_id
//            LEFT JOIN '.$wpdb->prefix.'order AS o ON o.user_id=mq.user_id AND o.match_id=mq.match_id
//            WHERE mq.match_id='.$post->ID.' GROUP BY user_id ORDER BY my_score DESC', ARRAY_A);
//
//
////剩余时间 | 正确率
//for($i = 0; $i < count($totalRanking)-1; ++$i){
//    for ($j = $i+1; $j < count($totalRanking); ++$j){
//        if($totalRanking[$i]['my_score'] == $totalRanking[$j]['my_score']){
//            if($totalRanking[$j]['surplus_time'] > $totalRanking[$i]['surplus_time']){
//                $a = $totalRanking[$j];
//                $totalRanking[$j] = $totalRanking[$i];
//                $totalRanking[$j] = $a;
//            }elseif ($totalRanking[$j]['surplus_time'] == $totalRanking[$i]['surplus_time']){}
//            //正确率, 获取分数最高一轮的正确率
//            $iCorce = $this->getCorrect($totalRanking[$i]['user_id'],$totalRanking[$i]['project_id'],$post->ID);
//            $jCorce = $this->getCorrect($totalRanking[$j]['user_id'],$totalRanking[$j]['project_id'],$post->ID);
//            if($iCorce < $jCorce){
//                $a = $totalRanking[$j];
//                $totalRanking[$j] = $totalRanking[$i];
//                $totalRanking[$j] = $a;
//            }
//        }
//    }
//}
//
//
////查询每个学员每个小项目每一轮的分数
//foreach ($totalRanking as &$trv) {
//    foreach ($projectArr as $pak => $pav) {
//        $trv['projectScore'][$pak] = '';
//        $res = $wpdb->get_results('SELECT my_score,match_more FROM ' . $wpdb->prefix . 'match_questions AS mq
//                 WHERE match_id=' . $post->ID . ' AND user_id=' . $trv['user_id'] . ' AND project_id=' . $pav['ID']);
//        foreach ($res as $rv) {
//            $trv['projectScore'][$pak] .= ($rv->my_score ? $rv->my_score : 0) . '/';
//        }
//        $trv['projectScore'][$pak] = substr($trv['projectScore'][$pak], 0, strlen($trv['projectScore'][$pak]) - 1);
////                print_r($res);
//    }
//    $usermeta = get_user_meta($trv['user_id'], '', true);
//    $user_real_name = unserialize($usermeta['user_real_name'][0]);
//    $age = $user_real_name['real_age'];
//    $user_real_name = $user_real_name['real_name'];
//    $trv['age'] = $age;
//    $trv['ageGroup'] = $this->getAgeGroupNameByAge($age);
//    $trv['userID'] = $usermeta['user_ID'][0];
//    $trv['real_name'] = $user_real_name;
//    $trv['sex'] = $usermeta['user_gender'][0];
//    $trv['birthday'] = $usermeta['user_birthday'][0];
//    $trv['address'] = unserialize($usermeta['user_address'][0])['province'] . unserialize($usermeta['user_address'][0])['city'];
//
//
//}
//
//
//
//
//
//$filename = 'match_ranking_';
//$filename .= strtotime(current_time('mysql')).".xls";
////        $path = self::$downloadPath.$filename;
////        file_put_contents($path,$html);
//header('Pragma:public');
//header('Content-Type:application/x-msexecl;name="'.$filename.'"');
//header('Content-Disposition:inline;filename="'.$filename.'"');
//require_once LIBRARY_PATH.'Vendor/PHPExcel/Classes/PHPExcel.php';
//require_once LIBRARY_PATH.'Vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';
//$objPHPExcel = new \PHPExcel();
////边框
//
//
//
////居中显示
//$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal('center');
//$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical('center');
//
////行高
//$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);
//
//$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(40);
//$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);
//
//$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
//$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
//$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
//$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
//$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
//$a = 'J';
//foreach ($projectArr as $titleV){
//    ++$a;
//    $objPHPExcel->getActiveSheet()->getColumnDimension($a)->setWidth(15);
//
//}
//
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $post->post_title);
//
////加粗
//$objPHPExcel->getActiveSheet()->getStyle( 'A1')->getFont()->setSize(16)->setBold(true);
//$objPHPExcel->getActiveSheet()->getStyle( 'A2')->getFont()->setBold(true);
//$objPHPExcel->getActiveSheet()->getStyle( 'B2')->getFont()->setBold(true);
//$objPHPExcel->getActiveSheet()->getStyle( 'C2')->getFont()->setBold(true);
//$objPHPExcel->getActiveSheet()->getStyle( 'D2')->getFont()->setBold(true);
//$objPHPExcel->getActiveSheet()->getStyle( 'E2')->getFont()->setBold(true);
//$objPHPExcel->getActiveSheet()->getStyle( 'F2')->getFont()->setBold(true);
//$objPHPExcel->getActiveSheet()->getStyle( 'G2')->getFont()->setBold(true);
//$objPHPExcel->getActiveSheet()->getStyle( 'H2')->getFont()->setBold(true);
//$objPHPExcel->getActiveSheet()->getStyle( 'I2')->getFont()->setBold(true);
//$objPHPExcel->getActiveSheet()->getStyle( 'J2')->getFont()->setBold(true);
//
//
//$a = 'J';
//foreach ($projectArr as $titleV){
//    ++$a;
//    $objPHPExcel->getActiveSheet()->getStyle( $a.'2')->getFont()->setBold(true);
//
//}
//
//$objPHPExcel->getActiveSheet()->getStyle('A1:'.--$a.'1')->getBorders()->getAllBorders()->setBorderStyle('thin');
//
//$objPHPExcel->getActiveSheet()->mergeCells('A1:'.--$a.'1');
//
//$objPHPExcel->getActiveSheet()->getStyle('A1:'.--$a.'1')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');
//
//for ($b = 'A'; $b <= 'J'; ++$b){
//    $objPHPExcel->getActiveSheet()->getStyle( $b.'2')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');
//}
//$a = 'K';
//foreach ($projectArr as $titleV){
//    $objPHPExcel->getActiveSheet()->getStyle($a. '2')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');
//    ++$a;
//}
//
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '学员ID');
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', '真实姓名');
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', '性别');
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', '年龄');
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', '年龄组别');
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', '所在地区');
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', '手机');
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', '邮箱');
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', '报名时间');
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J2', '总得分');
//
//for ($b = 'A'; $b <= 'J'; ++$b){
//    $objPHPExcel->getActiveSheet()->getStyle($b.'2')->getBorders()->getAllBorders()->setBorderStyle('thin');
//}
//
//$a = 'K';
//foreach ($projectArr as $titleV){
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($a.'2', $titleV['post_title'].'得分');
//    $objPHPExcel->getActiveSheet()->getStyle($a.'2')->getBorders()->getAllBorders()->setBorderStyle('thin');
//    ++$a;
//}
//
//
//$k = 0;
//foreach ($totalRanking as $raV){
//
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($k+3),' '.$raV['userID']);
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($k+3),' '.$raV['real_name']);
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($k+3),' '.$raV['sex']);
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($k+3),' '.$raV['age']);
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($k+3),' '.$raV['ageGroup']);
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($k+3),' '.$raV['address']);
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($k+3),' '.$raV['telephone']);
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($k+3),' '.$raV['user_email']);
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+3),' '.$raV['created_time']);
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.($k+3),' '.$raV['my_score']);
//    for ($b = 'A'; $b <= 'J'; ++$b){
//        $objPHPExcel->getActiveSheet()->getStyle($b.($k+3))->getBorders()->getAllBorders()->setBorderStyle('thin');
//    }
//    $a = 'K';
//    foreach ($raV['projectScore'] as $ravV){
//        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($a.($k+3),' '.$ravV);
//        $objPHPExcel->getActiveSheet()->getStyle($a.($k+3))->getBorders()->getAllBorders()->setBorderStyle('thin');
//        ++$a;
//    }
//    ++$k;
//}
//
//
//$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->save('php://output');