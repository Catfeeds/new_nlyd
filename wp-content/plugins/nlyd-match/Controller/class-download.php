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
        return $group;
    }

    /**
     * 导出比赛排名
     */
    public function match_ranking(){
        global $wpdb;

        //首先获取当前比赛
        $post = get_post(intval($_GET['match_id']));
//        $match = $wpdb->get_row('SELECT match_status FROM '.$wpdb->prefix.'match_meta WHERE match_id='.$post->ID, ARRAY_A);
        //TODO 判断比赛是否结束
        //根据成绩排序查询比赛学员
        $matchQuestions = $wpdb->get_results('SELECT u.user_email,mq.user_id,mq.project_id,mq.match_more,mq.my_score,mq.answer_status,p.post_title,o.created_time,o.telephone FROM '.$wpdb->prefix.'match_questions AS mq 
        LEFT JOIN '.$wpdb->prefix.'order AS o ON o.match_id=mq.match_id AND o.user_id=mq.user_id 
        LEFT JOIN '.$wpdb->users.' AS u ON u.ID=mq.user_id 
        LEFT JOIN '.$wpdb->posts.' AS p ON p.ID=mq.project_id WHERE mq.match_id='.$post->ID,ARRAY_A);
        //处理数据
        $rankingArr = [];
        $titleArr = [];
        foreach ($matchQuestions as $mqk => $mqv){
            $usermeta = get_user_meta($mqv['user_id'], '', true);

            if(!isset($titleArr[$mqv['project_id']])) $titleArr[$mqv['project_id']] = $mqv['post_title'];
//            var_dump($usermeta);
            //基础数据
            if(!isset($rankingArr[$mqv['user_id']])){
                $rankingArr[$mqv['user_id']] = [
                    'user_ID' => $usermeta['user_ID'][0],
                    'real_name' => unserialize($usermeta['user_real_name'][0])['real_name'],
                    'sex' => $usermeta['user_gender'][0],
                    'birthday' => $usermeta['user_birthday'][0],
                    'age' => $this->getAgeGroupNameByAge(unserialize($usermeta['user_real_name'][0])['real_age']),
                    'address' => unserialize($usermeta['user_address'][0])['province'].unserialize($usermeta['user_address'][0])['city'],
                    'mobile' => $mqv['telephone'],
                    'email' => $mqv['user_email'],
                    'created_time' => $mqv['created_time'],
                ];

                $rankingArr[$mqv['user_id']]['total_score'] = $mqv['my_score'];
            }else{

                $rankingArr[$mqv['user_id']]['total_score'] += $mqv['my_score'];
            }
            //每个项目每一轮比赛成绩
            foreach ($titleArr as $titleK => $titleV){
                if($mqv['project_id'] == $titleK) {
                    if(isset($rankingArr[$mqv['user_id']]['project'][$titleK]) && !empty($rankingArr[$mqv['user_id']]['project'][$titleK])){
                        $rankingArr[$mqv['user_id']]['project'][$titleK] .= '/'.$mqv['my_score'];
                    }else{
                        $rankingArr[$mqv['user_id']]['project'][$titleK] = $mqv['my_score'];
                    }
                }else{
                    $rankingArr[$mqv['user_id']]['project'][$titleK] .= '';
                }
            }

        }

        $arr = [];
        foreach ($rankingArr as $rv){
            $arr[] = $rv;
        }
        $rankingArr = $arr;
        //排序
        for ($i = 0; $i < count($rankingArr)-1; ++$i){
            for ($j = $i+1; $j < count($rankingArr); ++$j){
                if($rankingArr[$i]['total_score'] < $rankingArr[$j]['total_score']){
                    $a = $rankingArr[$i];
                    $rankingArr[$i] = $rankingArr[$j];
                    $rankingArr[$j] = $a;
                }

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
        $a = 'J';
        foreach ($titleArr as $titleV){
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


        $a = 'J';
        foreach ($titleArr as $titleV){
            ++$a;
            $objPHPExcel->getActiveSheet()->getStyle( $a.'2')->getFont()->setBold(true);

        }

        $objPHPExcel->getActiveSheet()->getStyle('A1:'.--$a.'1')->getBorders()->getAllBorders()->setBorderStyle('thin');

        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.--$a.'1');

        $objPHPExcel->getActiveSheet()->getStyle('A1:'.--$a.'1')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');

        for ($b = 'A'; $b <= 'J'; ++$b){
            $objPHPExcel->getActiveSheet()->getStyle( $b.'2')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');
        }
        $a = 'K';
        foreach ($titleArr as $titleV){
            $objPHPExcel->getActiveSheet()->getStyle($a. '2')->getFill()->setFillType('solid')->getStartColor()->setARGB('00FCE4D6');
            ++$a;
        }

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '学员ID');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', '真实姓名');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', '性别');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', '出生日期');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', '年龄组别');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', '所在地区');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', '手机');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', '邮箱');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', '报名时间');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J2', '总得分');

        for ($b = 'A'; $b <= 'J'; ++$b){
            $objPHPExcel->getActiveSheet()->getStyle($b.'2')->getBorders()->getAllBorders()->setBorderStyle('thin');
        }

        $a = 'K';
         foreach ($titleArr as $titleV){
             $objPHPExcel->setActiveSheetIndex(0)->setCellValue($a.'2', $titleV.'得分');
             $objPHPExcel->getActiveSheet()->getStyle($a.'2')->getBorders()->getAllBorders()->setBorderStyle('thin');
             ++$a;
         }


        $k = 0;
        foreach ($rankingArr as $raV){

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($k+3),' '.$usermeta['user_ID'][0]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($k+3),' '.$raV['real_name']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($k+3),' '.$raV['sex']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($k+3),' '.$raV['birthday']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($k+3),' '.$raV['age']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($k+3),' '.$raV['address']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($k+3),' '.$raV['mobile']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($k+3),' '.$raV['email']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+3),' '.$raV['created_time']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.($k+3),' '.$raV['total_score']);
            for ($b = 'A'; $b <= 'J'; ++$b){
                $objPHPExcel->getActiveSheet()->getStyle($b.($k+3))->getBorders()->getAllBorders()->setBorderStyle('thin');
            }
            $a = 'K';
            foreach ($raV['project'] as $ravV){
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