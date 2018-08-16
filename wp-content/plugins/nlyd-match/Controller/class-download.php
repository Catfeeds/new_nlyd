<?php
namespace Controller;
class Download
{
    private static $downloadPath;
    public function __construct()
    {
        //配置自己的重写规则
        add_action( 'init', array($this,'custom_rewrite_basic'),10,0);
        self::$downloadPath = WP_PLUGIN_DIR.'/downloadFile/';
        if(!isset($_GET['action']) || !method_exists($this, $_GET['action'])) exit;
        $action = $_GET['action'];
        $this->$action();
    }

    public function order(){
        global $wpdb;
        if(empty($_POST['start_date']) || empty($_POST['end_date'])) exit('请选择日期');
        $start = date('Y-m-d H:i:s', strtotime($_POST['start_date']));
        $end = date('Y-m-d H:i:s', strtotime($_POST['end_date'].' +1 day'));
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

        $html = '<table cellspacing="0" cellpadding="0" style="color: black;text-align: center" border="1px solid #000000">
                <tr>
                    <th>订单流水</th>
                    <th>用户名</th>
                    <th>比赛</th>
                    <th>收件人</th>
                    <th>联系电话</th>
                    <th>收获地址</th>
                    <th>订单类型</th>
                    <th>快递单号</th>
                    <th>快递公司</th>
                    <th>支付类型</th>
                    <th>订单总价</th>
                    <th>支付状态</th>
                    <th>创建时间</th>
                </tr>';
        foreach ($rows as $row){
            $html .= '<tr>
                         <td style="text-align: center; vnd.ms-excel.numberformat:@">'.$row['serialnumber'].'</td>               
                         <td style="text-align: center; vnd.ms-excel.numberformat:@">'.$row['user_login'].'</td>               
                         <td style="text-align: center; vnd.ms-excel.numberformat:@">'.$row['post_title'].'</td>               
                         <td style="text-align: center; vnd.ms-excel.numberformat:@">'.$row['funllname'].'</td>               
                         <td style="text-align: center; vnd.ms-excel.numberformat:@">'.$row['telephone'].'</td>               
                         <td style="text-align: center; vnd.ms-excel.numberformat:@">'.$row['address'].'</td>               
                         <td style="text-align: center; vnd.ms-excel.numberformat:@">'.$row['order_type'].'</td>               
                         <td style="text-align: center; vnd.ms-excel.numberformat:@">'.$row['express_number'].'</td>               
                         <td style="text-align: center; vnd.ms-excel.numberformat:@">'.$row['express_company'].'</td>               
                         <td style="text-align: center; vnd.ms-excel.numberformat:@">'.$row['pay_type'].'</td>               
                         <td style="text-align: center; vnd.ms-excel.numberformat:@">'.$row['cost'].'</td>               
                         <td style="text-align: center; vnd.ms-excel.numberformat:@">'.$row['pay_name'].'</td>               
                         <td style="text-align: center; vnd.ms-excel.numberformat:@">'.$row['created_time'].'</td>               
                    </tr>';
        }
        $html .= '</table>';
        $date = $_POST['start_date'].'-'.$_POST['end_date'];
        $filename = 'order_';
        $filename .= $date."_";
        $filename .= time().".xls";
        $path = self::$downloadPath.$filename;
        file_put_contents($path,$html);
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
}
new Download();