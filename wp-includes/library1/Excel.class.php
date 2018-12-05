<?php


class Excel{

    /**数据导出
     * @param arr    $data      条件信息
     * @param string $type     需要导出的表数据 user order  job
     */

    public function export($data,$table=''){

        if(empty($data) || empty($table)) return -1;

        if(!empty($data['id'])) $id_str = arr2str($data['id']);
        //print_r($table);
        switch ($table){
            case 'user':
                $thead = array(
                            'username'=>array('name'=>'用户名','type'=>'string','widths'=>20),
                            'realname'=>array('name'=>'真实姓名','type'=>'string','widths'=>20),
                            'mobile'=>array('name'=>'电话号码','type'=>'string','widths'=>20),
                            'email'=>array('name'=>'邮箱','type'=>'string','widths'=>20),
                            'total'=>array('name'=>'关联墓穴','type'=>'integer','widths'=>20),
                            'status'=>array('name'=>'用户状态','type'=>'string','widths'=>20),
                            'type'=>array('name'=>'注册平台','type'=>'string','widths'=>20),
                            'reg_time'=>array('name'=>'注册时间','type'=>'datetime','widths'=>20),
                            'last_time'=>array('name'=>'最近登录','type'=>'datetime','widths'=>20)
                        );

                /*准备获取数据*/

                if(!empty($id_str)) $map[] = " a.id in($id_str) ";

                //查询条件
                if(isset($data['search'])){
                    $map[] = " a.username = '{$data['val']}' or a.realname = '{$data['val']}' or a.mobile = {$data['val']} or a.email = '{$data['val']}' ";
                }
                //注册平台
                if(isset($data['type'])){
                    $map[] = " a.type = '{$data['type']}' ";
                }
                //用户状态
                if(isset($data['status'])){
                    $map[] = " a.status = '{$data['status']}' ";
                }
                
                //最小时间
                if(isset($data['mid'])){
                    $mid = strtotime($data['mid']);
                    $map[] = " a.reg_time>='{$mid}'";
                }
                //最大时间
                if(isset($data['mad'])){
                    $mad = strtotime($data['mad']);
                    $map[] = " a.reg_time<='{$mad}'";
                }

                $where = join('and',$map);
                $list=M('User')
                    ->alias('a')
                    ->join("__USER_OPENGRAVE__ b on a.id = b.uid",'left')
                    ->field("if(a.username != '' ,username,'--') username,
                            if(a.realname != '' ,realname,'--') realname,
                            if(a.email != '' ,email,'--') email,
                            if(a.mobile != '' ,mobile,'--') mobile,
                            if(a.type = 1 ,'平台注册','微信注册') type, 
                            if(a.reg_time > 1,FROM_UNIXTIME(a.reg_time,'%Y-%m-%d %H:%i:%s'),'--') reg_time,
                            if(a.last_time > 1,FROM_UNIXTIME(a.reg_time,'%Y-%m-%d %H:%i:%s'),'--') last_time,
                            count(*) total"
                        )
                    ->where($where)
                    ->group('a.id')
                    ->order('a.id desc')
                    //->fetchSql(true)
                    ->select();
                    $total = count($list);  //总数
                    $excelName = 'User';
                break;
            case 'order':
            case 'job':
                $thead = array(
                    'serialnumber'=>array('name'=>'订单编号','type'=>'string','widths'=>20),
                    'type'=>array('name'=>'缴费类型','type'=>'string','widths'=>20),
                    'category'=>array('name'=>'服务类型','type'=>'string','widths'=>20),
                    'paygrave'=>array('name'=>'墓穴编号','type'=>'string','widths'=>40),
                    'realname'=>array('name'=>'下单人姓名','type'=>'string','widths'=>20),
                    'mobile'=>array('name'=>'下单人电话','type'=>'string','widths'=>20),
                    'create_time'=>array('name'=>'下单时间','type'=>'datetime','widths'=>25),
                    'cost'=>array('name'=>'总价','type'=>'price','widths'=>20),
                    'status'=>array('name'=>'支付状态','type'=>'string','widths'=>15),
                    'method'=>array('name'=>'支付方式','type'=>'string','widths'=>15),
                    'pay_time'=>array('name'=>'付款时间','type'=>'datetime','widths'=>25),
                    'mail'=>array('name'=>'开票方式','type'=>'string','widths'=>10),
                    'billing'=>array('name'=>'开票状态','type'=>'string','widths'=>10),
                    'number'=>array('name'=>'收据单号','type'=>'string','widths'=>20),
                    'company'=>array('name'=>'快递公司','type'=>'string','widths'=>20),
                    'express'=>array('name'=>'快递单号','type'=>'string','widths'=>20),
                    'consignee'=>array('name'=>'收件人','type'=>'string','widths'=>20),
                    'phone'=>array('name'=>'收件电话','type'=>'string','widths'=>20),
                    'province'=>array('name'=>'收件省份','type'=>'string','widths'=>20),
                    'city'=>array('name'=>'收件城市','type'=>'string','widths'=>20),
                    'area'=>array('name'=>'收件区域','type'=>'string','widths'=>40),
                    'details'=>array('name'=>'详细地址','type'=>'string','widths'=>40),
                    'handle_status'=>array('name'=>'订单状态','type'=>'string','widths'=>10),
                    'remarks'=>array('name'=>'用户备注','type'=>'string','widths'=>40),
                );


                if(!empty($id_str)) $map[] = " a.id in($id_str) ";
                
                //查询条件
                if(isset($_GET['map']) && in_array($_GET['map'],array(1,2,3)) && !empty($_GET['search'])){
                    $val = $_GET['search'];
                    if($_GET['map'] == 1){  //订单号
                        $map[] = " a.serialnumber = $val ";
                    }else{
                        $map[] = " (e.username like '%{$val}%' or e.realname like '%{$val}%' or e.mobile = '{$val}') ";
                    }
                }
                /*园区*/
                if(!empty($_GET['park'])){
                    $map[] = " d.id = {$_GET['park']} ";
                }
                //缴费状态
                if(isset($data['status'])){
                    $map[] = " a.status={$data['status']} ";
                }
                //缴费类型
                /*if(isset($data['type'])){
                    $map[] = " a.type = {$data['type']} ";
                }*/
                $map[]  = $table == 'order' ? ' a.type = 1 ' : ' a.type = 2 ';
                //缴费方式
                if(isset($data['method'])){
                    $map[] = " a.method = {$data['method']} ";
                }
				if(isset($data['mail'])){
					$map[] = " a.mail = {$data['mail']} ";
				}
				if(isset($data['billing'])){
					$map[] = " a.billing = {$data['billing']} ";
				}
                //操作状态
                if(isset($data['handle_status'])){
                    $map[] = " a.handle_status = {$data['handle_status']} ";
                }
                //最小时间
                if(isset($data['mid'])){
                    $mid = strtotime($data['mid']);
                    $t = $data['status']==1 ? 'pay_time' : 'create_time';
                    $map[] = " a.{$t}>='{$mid}'";
                }
                //最大时间
                if(isset($data['mad'])){
                    $mad = strtotime($data['mad']);
                    $t = $data['status']==1 ? 'pay_time' : 'create_time';
                    $map[] = " a.{$t}<='{$mad}'";
                }
                //操作状态
                if(isset($data['handle_status'])){
                    $map[] = " a.handle_status={$data['handle_status']} ";
                }
                //缴费项目
                if(isset($data['category'])){
                    $map[] = " b.category={$data['category']} ";
                }

                $where = join('and',$map);

                $list = M('payLog')
                    ->alias('a')
                    ->field("a.serialnumber,a.cost,a.remarks,
                        case a.type when 1 then '管理费' when 2 then '维修费'  else '--' end type,
                        case a.status when -1 then '未缴费' else '已缴费' end status,
                        case a.method when 1 then '支付宝' when 2 then '微信' when 3 then '线下缴费' else '--' end method,
                        if(a.create_time > 1,FROM_UNIXTIME(a.create_time,'%Y-%m-%d %H:%i:%s'),'0000-00-00 00:00:00') create_time,
                        if(a.pay_time > 1,FROM_UNIXTIME(a.pay_time,'%Y-%m-%d %H:%i:%s'),'0000-00-00 00:00:00') pay_time,
                        case a.mail when 1 then '自取' when 2 then '邮寄' end mail,
                        if(a.billing = 1,'已开票','未开票') billing,
                        a.number,a.company,a.express,g.consignee,g.mobile phone,g.province,g.city,g.area,g.details,
                        case a.handle_status when -1 then '待受理' when 1 then '已受理' when 2 then '已完成' else '--' end handle_status,
                        e.realname,e.mobile,
                        GROUP_CONCAT(CONCAT_WS('',d.parkname,c.row,'排',c.column,'号【',b.price,'元】') SEPARATOR '\r\n') paygrave,
                        if(f.title != 'NULL',GROUP_CONCAT(f.title SEPARATOR '\r\n'),'--') category
                        ")
                    ->join('__PAY_ITEMS__ b ON b.pid = a.id ','RIGHT')
                    ->join('__OPENGRAVE__ c ON b.oid = c.id ','LEFT')
                    ->join('__PARK__ d ON c.park = d.id ','LEFT')
                    ->join('__USER__ e ON a.uid = e.id ','LEFT')
                    ->join('__REPAIR_PROJECT__ f ON f.id = b.category ','LEFT')
                    ->join('__EXPRESS_ADDRESS__ g ON a.id = g.pid','LEFT')
                    ->where($where)
                    ->group('a.id')
                    ->order('a.id desc')
                    //->fetchSql(true)
                    ->select();
                    //var_dump($list);die;
                    $total = count($list);  //总数
                    $excelName = 'Order-Job';
                break;
            case 'grave':
                $thead = array(
                        'parkname'=>array('name'=>'园区名称','type'=>'string','widths'=>20),
                        'row'=>array('name'=>'排号','type'=>'string','widths'=>10),
                        'column'=>array('name'=>'序号','type'=>'string','widths'=>10),
                        'username'=>array('name'=>'墓主','type'=>'string','widths'=>40),
                        'manage_time'=>array('name'=>'管理费期限','type'=>'string','widths'=>25),
                        'sname'=>array('name'=>'亲属','type'=>'string','widths'=>40),
                        'uname'=>array('name'=>'绑定用户','type'=>'string','widths'=>40),
                        'closing'=>array('name'=>'是否合棺','type'=>'string','widths'=>10),
                        'call_time'=>array('name'=>'催缴时间','type'=>'datetime','widths'=>25),
                );

                if(!empty($id_str)) $map[] = " o.id in($id_str) ";

                if (isset($data['username'])) {
                    /*通过墓主名称查询*/
                    $grave_id = M('Departed')
                        ->field('grave_id')
                        ->where("username like '%{$data['username']}%'")->buildSql();
                    /*通过亲属查询*/
                    $grave_idTow = M('Departed')
                        ->alias('d')
                        ->join("__OGRELEVANCE__ og on d.id = og.Did")
                        ->join("__SACRIFICIALPEOPLE__ s on og.Sid = s.id")
                        ->field('grave_id')
                        ->where("s.name like '%{$data['username']}%'")->buildSql();
                    /*通过绑定用户姓名查询*/

                    $grave_id3 = M('User_opengrave')
                        ->alias('uo')
                        ->join("__USER__ u on u.id = uo.uid")
                        ->field('uo.oid')
                        ->where("u.realname like '%{$data['username']}%'")->buildSql();


                    $map[] = " o.id IN ($grave_id) or o.id IN ($grave_idTow) or o.id IN ($grave_id3) ";
                }
                if (isset($data['mobile'])) {
                    $grave_id = M('Departed')
                        ->alias('d')
                        ->join("__OGRELEVANCE__ og on d.id = og.Did")
                        ->join("__SACRIFICIALPEOPLE__ s on og.Sid = s.id")
                        ->field('grave_id')
                        ->where("s.mobile = {$data['mobile']}")->buildSql();

                    $Ugrave_id = M('User_opengrave')
                        ->alias('uo')
                        ->join("__USER__ u on u.id = uo.uid")
                        ->field('uo.oid')
                        ->where("u.mobile = {$data['mobile']}")->buildSql();

                    $map[] = " o.id IN ($grave_id) or o.id IN($Ugrave_id)";
                }
                if (isset($data['parkname'])) {
                    $map[] = " p.id = '{$data['parkname']}' ";
                }
                if (isset($data['row'])) {
                    $map[] = " o.row = '{$data['row']}' ";
                }
                if (isset($data['column'])) {
                    $map[] = " o.column = '{$data['column']}' ";
                }
                if (isset($data['closing'])) {
                    $map[] = " o.closing = '{$data['closing']}' ";
                }
                if(isset($data['manage_time'])){
                    if($data['manage_time']=='nobuy'){
                        /*未出售*/
                        $map[] = " o.buy_time ='0000-00-00' ";
                    }else{
                        if($data['manage_time']=='finish'){
                            /*永久性管理费*/
                            $map[] = " o.checkout ='1' ";
                        }
                        $map[] = " o.buy_time <>'0000-00-00' ";
                    }
                    
                }
                if(in_array($data['manage_time'], array('overtime','overdue','five','ten','moreten'))){
                    $map[] = " o.checkout = -1 ";
                    $map[] = " o.manage_time >'0000-00-00' ";
                }
                switch ($data['manage_time']) {
                    case 'overtime':
                        $map[] = " o.manage_time <= now() ";
                        break;
                    case 'finish':
                        $map[] = " o.checkout = 1 ";
                        break;
                    case 'overdue':
                        $map[] = " o.manage_time > DATE_ADD(CURDATE(),INTERVAL 1 YEAR) ";
                        break;
                    case 'five':
                        $map[] = " o.manage_time between DATE_SUB(CURDATE(),INTERVAL 4 YEAR) and DATE_ADD(CURDATE(),INTERVAL 1 YEAR) ";
                        break;
                    case 'ten':
                        $map[] = " o.manage_time between DATE_SUB(CURDATE(),INTERVAL 9 YEAR) and DATE_SUB(CURDATE(),INTERVAL 4 YEAR) ";
                        break;
                    case 'moreten':
                        $map[] = " o.manage_time < DATE_SUB(CURDATE(),INTERVAL 9 YEAR) ";
                        break;
                }

                $where = join('and', $map);
                $order = 'o.park,(o.row+0),(o.column+0)';

                 $list = M('Opengrave')
                    ->alias('o')
                    ->join("__PARK__ p on p.id = o.park")
                    ->join("__DEPARTED__ d on o.id = d.grave_id and d.status='1'", 'left')
                    ->join("__OGRELEVANCE__ sd on sd.Did = d.id", 'left')
                    ->join("__SACRIFICIALPEOPLE__ s on s.id = sd.Sid", 'left')
                    ->join("__USER_OPENGRAVE__ uo on uo.oid = o.id", 'left')
                    ->join("__USER__ u on u.id = uo.uid", 'left')
                    ->field("o.pictures,o.status,
                            p.parkname,o.id,o.row,
                            o.column,
                            case buy_time when '0000-00-00' then '未出售' else 
                            (case o.checkout when '1' then '永久性管理费' else o.manage_time end) 
                            end manage_time,
                            case buy_time when '0000-00-00' then '未出售' else 
                            (case o.closing when '1' then '合棺' when '-1' then '未合棺' end) 
                            end closing,
                            o.clos_time,
                            GROUP_CONCAT(distinct d.username SEPARATOR '、') username,
                            GROUP_CONCAT(distinct CONCAT(s.name,s.mobile) SEPARATOR '\r\n') sname,
                            GROUP_CONCAT(distinct CONCAT(u.realname,u.mobile) SEPARATOR '\r\n') uname")
                    ->where($where)
                    ->group('o.id')
                    ->order($order)
                    ->select();
                    //GROUP_CONCAT(distinct CONCAT(d.username,'(',case d.b_type when '1' then '阴历 ' when '-1' then '阳历 ' else '未知 ' end ,d.birthday,' - ',case d.l_type when '1' then '阴历 ' when '-1' then '阳历 ' else '未知 ' end ,d.last_birthday,')') SEPARATOR '\r\n') username,
                $total = count($list);  //总数
                $excelName = 'Grave';
                
                break;
            case 'park':
                $thead = array(
                    'parkname'=>array('name'=>'园区','type'=>'string','widths'=>30),
                    'suggest'=>array('name'=>'简介','type'=>'string','widths'=>40),
                    'manage_fees'=>array('name'=>'管理费','type'=>'price','widths'=>20),
                    'buy_fees'=>array('name'=>'购买费','type'=>'price','widths'=>20),
                    'status'=>array('name'=>'状态','type'=>'string','widths'=>20),
                );

                if(!empty($id_str)) $map[] = " id in($id_str) ";

                if(isset($_GET['parkname'])){
                    $map[] = " parkname like '%{$_GET['parkname']}%' ";
                }
                $where = join('and',$map);
                $list = M('Park')
                    ->field("parkname,suggest,manage_fees,buy_fees,if(status = 1 ,'正常','禁用') status
                        ")
                    ->where($where)
                    ->order('id desc')
                //    ->fetchSql(true)
                    ->select();
                $total = count($list);  //总数
                $excelName = 'Park';
                break;
            case 'history':
                $thead = array(
                        'parkname'=>array('name'=>'园区名称','type'=>'string','widths'=>30),
                        'row'=>array('name'=>'排号','type'=>'string','widths'=>10),
                        'column'=>array('name'=>'序号','type'=>'string','widths'=>10),
                        'event'=>array('name'=>'操作事件','type'=>'string','widths'=>40),
                        'username'=>array('name'=>'操作人','type'=>'string','widths'=>20),
                        'time'=>array('name'=>'操作时间','type'=>'datetime','widths'=>25),
                );
                
                if(isset($data['mid'])){
                    $mid = strtotime($data['mid']);
                    $map[] = " h.time >= {$mid} ";
                }
                if(isset($data['mad'])){
                    $mad = strtotime($data['mad']);
                    $map[] = " h.time >= '{$mad}' ";
                }
                if(isset($data['oid'])){
                    $map[] = " h.table_id = {$data['oid']}";
                }
                
                $where = join('and', $map);
                            
                $list = M('handle_history')
                ->alias('h')
                ->join('__OPENGRAVE__ o on h.table_id = o.id')
                ->join('__PARK__ p on o.park = p.id')
                ->join('__UCENTER_MEMBER__ m on h.uid = m.id')
                ->field('p.parkname,o.id oid,o.row,o.column,m.username,h.event,FROM_UNIXTIME(h.time) time')
                ->where($where)
                ->order('h.time desc')
                ->select();
                
                $total = count($list);  //总数
                $excelName = 'Grave-history';
                break;
            case 'interaction':
                $thead = array(
                    'realname'=>array('name'=>'用户名','type'=>'string','widths'=>10),
                    'mobile'=>array('name'=>'电话','type'=>'string','widths'=>20),
                    'content'=>array('name'=>'发表内容','type'=>'string','widths'=>40),
                    'type_cn'=>array('name'=>'类型','type'=>'string','widths'=>10),
                    'address'=>array('name'=>'地址','type'=>'string','widths'=>40),
                    'status'=>array('name'=>'状态','type'=>'string','widths'=>10),
                    'auditing_mode'=>array('name'=>'审核模式','type'=>'string','widths'=>10),
                    'date_added'=>array('name'=>'发表时间','type'=>'datetime','widths'=>25),
                    'date_published'=>array('name'=>'投屏时间','type'=>'datetime','widths'=>25),
                    'date_audited'=>array('name'=>'审核时间','type'=>'datetime','widths'=>25),
                );

                /*准备获取数据*/

                if(!empty($id_str)) $map[] = " a.id in($id_str) ";

                if(!empty($data['type'])) $map[] = " i.type = '{$data['type']}' ";
                if(!empty($data['auditing_mode'])) $map[] = " i.auditing_mode = '{$data['auditing_mode']}' ";
                if(!empty($data['status'])) $map[] = " i.status = '{$data['status']}' ";
                if(!empty($data['user_id'])) $map[] = " u.id = '{$data['user_id']}' ";
                if(!empty($data['search'])) $map[] = " (u.username like '%{$data['search']}%' or u.realname like '%{$data['search']}%' or u.mobile like '%{$data['search']}%' ) ";

                if($data['date_type'] == 1){

                    $date_type = 'date_added';  //发表时间

                }else if($data['date_published'] == 2){

                    $date_type = 'date_published';  //投屏时间

                }else{

                    $date_type = 'date_audited';    //审核时间

                }

                if(!empty($data['mid'])) $map[] = " i.$date_type >= '{$data['mid']}' ";
                if(!empty($data['mad'])) $map[] = " i.$date_type <= '{$data['mad']}' ";

                $where = join('and',$map);
                $list = M('Interaction')
                    ->alias('i')
                    ->join('__USER__ u ON i.uid = u.id','left')
                    ->join('__PRESENT_TYPE__ t ON i.type = t.alias','left')
                    ->field(" i.*,case i.status when -1 then '屏蔽' when 1 then '已投屏' when 2 then '等待审核' when 3 then '等待投屏' else '--' end status , 
                            case i.auditing_mode when 1 then '人工' when 2 then '智能' when 3 then '自动' else '--' end auditing_mode ,
                            if(t.name != '',t.name,'--') type_cn, u.realname,u.mobile ")
                    ->where($where)
                    ->order('i.id desc')
                    //->fetchSql(true)
                    ->select();
                /*var_dump($list);
                die;*/
                $total = count($list);  //总数
                $excelName = 'Interaction';

                break;
            case 'present':
                $thead = array(
                    'title'=>array('name'=>'礼品名','type'=>'string','widths'=>20),
                    'code'=>array('兑换码'=>'礼品名','type'=>'string','widths'=>20),
                    'price'=>array('name'=>'单价','type'=>'price','widths'=>10),
                    'type_cn'=>array('name'=>'类型','type'=>'string','widths'=>10),
                    'status_cn'=>array('name'=>'状态','type'=>'string','widths'=>10),
                    'single_max'=>array('name'=>'每人限量','type'=>'integer','widths'=>20),
                    'receive_max'=>array('name'=>'礼品数量','type'=>'integer','widths'=>20),
                    'remarks'=>array('name'=>'备注','type'=>'string','widths'=>40),
                    'date_start'=>array('name'=>'开始时间','type'=>'datetime','widths'=>25),
                    'date_expiry'=>array('name'=>'过期时间','type'=>'datetime','widths'=>25),
                    'date_added'=>array('name'=>'创建时间','type'=>'datetime','widths'=>25),
                );

                /*准备获取数据*/

                if(!empty($id_str)) $map[] = " id in($id_str) ";

                //查询条件
                if(!empty($data['search'])){
                    $map[] = " (p.title  like '%{$data['search']}%' or p.code  like '%{$data['search']}%') ";
                }
                if($data['date_type'] == 1){
                    //领取时间
                    $date_type = 'date_received';
                }elseif ($data['date_type'] == 2){
                    //过期时间
                    $date_type = 'date_expiry';
                }else{
                    //创建时间
                    $date_type = 'date_added';
                }

                if(isset($_GET['mid'])){
                    $map[] = " p.{$date_type}>='{$data['mid']}' ";
                }

                if(isset($_GET['mad'])){
                    $map[] = " p.{$date_type}<='{$data['mad']}' ";
                }

                if(!empty($data['status'])) $map[] = " p.status = '{$data['status']}' ";
                if(!empty($data['type'])) $map[] = " p.type = '{$data['type']}' ";

                $where = join('and',$map);

                $list = D('Present')
                    ->alias('p')
                    ->field("SQL_CALC_FOUND_ROWS p.*,if(p.status = 1,'开启','关闭') status_cn,if(t.name != '',t.name,'--') type_cn")
                    ->join('__PRESENT_TYPE__ t ON p.type = t.id','left')
                    ->where($where)
                    ->order('p.id desc')
                    //->fetchSql(true)
                    ->select();
                $excelName = 'Present';

                break;
            case 'storage':

                $thead = array(
                    'departed'=>array('name'=>'故者姓名','type'=>'string','widths'=>20),
                    'gender'=>array('name'=>'故者性别','type'=>'string','widths'=>20),
                    'storage_num'=>array('name'=>'寄存卡号','type'=>'string','widths'=>20),
                    'contacts'=>array('name'=>'联系电话','type'=>'string','widths'=>20),
                    'storage_operator'=>array('name'=>'经办人','type'=>'string','widths'=>20),
                    'storage_time'=>array('name'=>'寄存时间','type'=>'datetime','widths'=>25),
                    'security'=>array('name'=>'押金','type'=>'string','widths'=>25),
                    'break_operator'=>array('name'=>'物品取出经办人','type'=>'string','widths'=>25),
                    'return_security'=>array('name'=>'退还押金','type'=>'string','widths'=>25),
                    'break_time'=>array('name'=>'取出时间','type'=>'datetime','widths'=>25),
                    'date_added'=>array('name'=>'创建时间','type'=>'datetime','widths'=>25),
                );

                /*准备获取数据*/

                if(!empty($id_str)) $map[] = " id in($id_str) ";

                if(isset($_GET['username'])){
                    $map[] = " (departed like '%{$_GET['username']}%' or contacts like '%{$_GET['username']}%') ";
                }
                if(isset($_GET['mobile'])){
                    $map[] = " contact_mobile = '{$_GET['mobile']}' ";
                }
                if(isset($_GET['num'])){
                    $map[] = " storage_num = '{$_GET['num']}' ";
                }

                /* if(isset($_GET['status'])){
                    $map[] = " status={$_GET['status']} ";
                } */

                $date_type = isset($_GET['date_type']) ? $_GET['date_type'] : 'storage_time';
                if(isset($_GET['date_start'])){
                    $map[] = " {$date_type} >= '{$_GET['date_start']}' ";
                }
                if(isset($_GET['date_end'])){
                    $map[] = " {$date_type} <= '{$_GET['date_end']}' ";
                }

                $where = join('and',$map);

                $list = M('itemsStorage')
                    ->field('
                    SQL_CALC_FOUND_ROWS *,
                    if(break_time="0000-00-00 00:00:00","寄存中",break_time) break_time,
                    if(break_operator=" ","--",break_operator) break_operator
                    ')
                    ->where($where)
                    ->order('id DESC')
                    //->fetchSql(true)
                    ->select();
                //var_dump($list);die;
                $total = count($list);  //总数
                $excelName = 'Storage';
                break;
            default:
                return -2;
                break;
        }
        //var_dump($getOrder);die;

        /*准备导出*/

       set_include_path( get_include_path().PATH_SEPARATOR."..");

        $writer = new XLSXWriter();
        $filename = $excelName ? $excelName : 'yn-lfs';
        $filename .= date('-Y-m-d',time())."-";
        $filename .= time().".xlsx";
        //设置 header，用于浏览器下载
        header('Content-disposition: attachment; filename="'.$filename.'"');
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
       $hd = array();
        $widths=array(); 
        foreach ($thead as $key => $value) {
            $hd[$value['name']]= empty($value['type'])?'string':$value['type'];
            $widths[] = empty($value['widths'])?20:$value['widths'];
        }
        $styles1 = array( 'font'=>'宋体','color'=>'#fff','font-size'=>14,'font-style'=>'bold', 'fill'=>'#008000',
'halign'=>'center','valign'=>'center','height'=>30,'widths'=>$widths);
        $styles2 = array( 'font'=>'宋体','font-size'=>12,'halign'=>'center','valign'=>'center','height'=>25);
        $styles3 = array( 'font'=>'宋体','font-size'=>12,'fill'=>'#f1f1f1','halign'=>'center','valign'=>'center','height'=>25);
        
        //$writer->writeSheetRow('Sheet1', $rowdata = $hd, $styles1 );
        $writer->writeSheetHeader('Sheet1', $hd,$styles1);
        $i=0;
        foreach ($list as $k => $v) {
            $body=array();
            foreach ($thead as $key => $value) {
                if($key == 'call_time'){
                    if(!empty($v[$key])) $body[] = date('Y-m-d',$v[$key]);
                }else{
                    $body[] =  $v[$key];
                }
            }
            if($body){
                $styles4 = $i%2==1?$styles3:$styles2;
                $i++;
                $writer->writeSheetRow('Sheet1', $body,$styles4);
            }
            
        }
        
        $writer->writeToStdOut();
    }
}

