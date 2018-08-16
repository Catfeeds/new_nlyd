<?php
class Student_Orders extends Student_Home
{
    public function __construct($action)
    {
        parent::__construct();

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));

        $this->ajaxControll = new Student_Ajax();
        //添加短标签
        add_shortcode('order-home',array($this,$action));
    }

    /**
     * 订单列表
     */
    public function index(){
        global $wpdb, $current_user;
        $row = $wpdb->get_row('SELECT count(id) as count FROM '.$wpdb->prefix.'order WHERE user_id='.$current_user->ID, ARRAY_A);
        // var_dump($row['count']);
        $view = student_view_path.'order.php';
        load_view_template($view, array('is_show' => $result));
    }

    /**
     * 订单详情
     */
    public function details(){
        $id = intval($_GET['id']);;
        global $wpdb,$current_user;
        require_once 'class-student-account-order.php';
        $row = $wpdb->get_row('SELECT '.self::selectField().' FROM '.$wpdb->prefix.'order WHERE user_id='.$current_user->ID.' AND id='.$id, ARRAY_A);
        var_dump($row);
    }
    /**
     * 查询字段
     */
    public static function selectField ($type = false){
        if($type == true){
            $str = 'o.id,
            o.serialnumber,
            p.post_title,
            u.user_login,
            IFNULL(o.fullname, "-") AS fullname,
            o.telephone,
            IFNULL(o.address, "-") AS address,
            CASE o.order_type 
            WHEN 1 THEN "报名订单" 
            END AS order_type,
            IFNULL(o.express_number, "-") AS express_number,
            IFNULL(o.express_company, "-") AS express_company,
            CASE o.pay_type 
            WHEN "zfb" THEN "支付宝" 
            WHEN "wx" THEN "微信" 
            WHEN "ylk" THEN "银联卡" 
            ELSE "-" 
            END AS pay_type,
            o.cost,
            CASE o.pay_status
            WHEN -2 THEN "已退款" 
            WHEN -1 THEN "待退款" 
            WHEN 1 THEN "待支付" 
            WHEN 2 THEN "支付完成" 
            END AS pay_status,
            o.created_time';
        }else{
            $str = 'id,
            serialnumber,
            match_id,
            IFNULL(fullname, "-") AS fullname,
            telephone,
            IFNULL(address, "-") AS address,
            CASE order_type 
            WHEN 1 THEN "报名订单" 
            END AS order_type,
            IFNULL(express_number, "-") AS express_number,
            IFNULL(express_company, "-") AS express_company,
            CASE pay_type 
            WHEN "zfb" THEN "支付宝" 
            WHEN "wx" THEN "微信" 
            WHEN "ylk" THEN "银联卡" 
            ELSE "-" 
            END AS pay_type,
            cost,
            CASE pay_status
            WHEN -2 THEN "已退款" 
            WHEN -1 THEN "待退款" 
            WHEN 1 THEN "待支付" 
            WHEN 2 THEN "支付完成" 
            END AS pay_status,
            created_time';
        }
        return $str;
    }

    /**
     * 导出订单
     */
    public function orderXls(){
        global $wpdb;
        $start_date = $_GET['start'];
        $end_date = $_GET['end'];
        $result = $wpdb->get_results('SELECT '.self::selectField(true).' FROM '.$wpdb->prefix.'order AS o 
         LEFT JOIN '.$wpdb->posts.' AS p ON o.match_id=p.ID 
         LEFT JOIN '.$wpdb->users.' AS u ON o.user_id=u.ID 
         WHERE o.created_time BETWEEN "'.$start_date.'" AND "'.$end_date.'"', ARRAY_A);
        $th = '<tr>
                   <th style="width: 35px;color: black">id</th>
                   <th style="width: 200px;color: black">订单流水</th>
                   <th style="width: 200px;color: black">用户账号</th>
                   <th style="width: 200px;color: black">比赛名称</th>
                   <th style="width: 150px;color: black">全名</th>
                   <th style="width: 120px;color: black">电话</th>
                   <th style="width: 300px;color: black">收货地址</th>
                   <th style="width: 150px;color: black">订单类型</th>
                   <th style="width: 200px;color: black">快递单号</th>
                   <th style="width: 150px;color: black">快递公司</th>
                   <th style="width: 150px;color: black">支付方式</th>
                   <th style="width: 150px;color: black">订单金额</th>
                   <th style="width: 100px;color: black">支付状态</th>
                   <th style="width: 150px;color: black">创建时间</th>
              </tr>';

        $td = '';
        foreach ($result as $res){
            $td .= '<tr style="text-align: center">
                        <td>'.$res['id'].'</td>
                        <td style="vnd.ms-excel.numberformat:@">'.$res['serialnumber'].'</td>
                        <td style="vnd.ms-excel.numberformat:@">'.$res['user_login'].'</td>
                        <td>'.$res['post_title'].'</td>
                        <td>'.$res['fullname'].'</td>
                        <td style="vnd.ms-excel.numberformat:@">'.$res['telephone'].'</td>
                        <td>'.$res['address'].'</td>
                        <td>'.$res['order_type'].'</td>
                        <td style="vnd.ms-excel.numberformat:@">'.$res['express_number'].'</td>
                        <td>'.$res['express_company'].'</td>
                        <td>'.$res['pay_type'].'</td>
                        <td>'.$res['cost'].'</td>
                        <td>'.$res['pay_status'].'</td>
                        <td>'.$res['created_time'].'</td>
                    </tr>';
        }
        $html = '<table style="text-align: center" border="1px solid #000000">'.$th.$td.'</table>';
        $filename = 'order_';
        $filename .= date('YmdHis',strtotime($start_date))."-".date('YmdHis',strtotime($end_date)).'_';
        $filename .= time().".xls";

        file_put_contents('./download/'.$filename,$html);

        $fileTmp = fopen('./download/'.$filename, 'r');

        // Begin writing headers
        header ( "Pragma: public" );
        header ( "Expires: 0" );
        header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header ( "Cache-Control: public" );
        header ( "Content-Description: File Transfer" );
        // Use the switch-generated Content-Type
        header ( "Content-Type: application/vnd.ms-excel" );
        // Force the download
        $header = "Content-Disposition: attachment; filename=" . $filename . ";";
        header ( $header );
        header ( "Content-Transfer-Encoding: binary" );
        header ( "Content-Length: " . filesize('./download/'.$filename) );

        //@readfile ( $file );
        echo fread ($fileTmp, filesize ('./download/'.$filename) );
        fclose($fileTmp);
        exit;

    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){

        if(ACTION=='index'){//比赛详情页
            wp_register_style( 'my-student-orderList', student_css_url.'order-list.css',array('my-student') );
            wp_enqueue_style( 'my-student-orderList' );
        }

    }
}