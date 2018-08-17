<?php
class Order {
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_order_menu_page') );
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));

    }

    public function register_order_menu_page(){

        add_menu_page('订单', '订单', 'administrator', 'order',array($this,'orderLists'),'dashicons-businessman',99);
        add_submenu_page('order','退款单','退款单','administrator','order-refundOrder',array($this,'refundOrder'));
        add_submenu_page('order','申请退款','申请退款','administrator','order-refund',array($this,'refund'));
//        add_submenu_page('order','我的学员','我的学员','administrator','teacher-student',array($this,'student'));
//        add_submenu_page('order','我的课程','我的课程','administrator','teacher-course',array($this,'course'));
    }

    /**
     * 查询字段
     */
    public function getSelectField(){
        return 'o.serialnumber,
        o.id,
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
        o.created_time ';
    }
    public function orderLists(){
        global $wpdb;
        $page = ($page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1) < 1 ? 1 : $page;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;

        $type = isset($_GET['type']) ? intval($_GET['type']) : 1;
        if($type < 1) $type = 1;
        switch ($type){
            case 1:
                $pay_status = '1=1';
                break;
            case 2:
                $pay_status = 'pay_status=1';
                break;
            case 3:
                $pay_status = 'pay_status=2';
                break;
            case 4:
                $pay_status = 'pay_status=-1';
                break;
            case 5:
                $pay_status = 'pay_status=-2';
                break;
            default:
                return false;
        }
        $rows = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS '.$this->getSelectField().' FROM '.$wpdb->prefix.'order AS o
        LEFT JOIN '.$wpdb->prefix.'users AS u ON u.ID=o.user_id
        LEFT JOIN '.$wpdb->prefix.'posts AS p ON p.ID=o.match_id 
        WHERE '.$pay_status.' 
        ORDER BY o.created_time DESC LIMIT '.$start.','.$pageSize, ARRAY_A);
//        var_dump($rows);
        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page
        ));

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">订单</h1>


<!--            <h2 class="screen-reader-text">过滤用户列表</h2><ul class="subsubsub">-->
<!--                <li class="all"><a href="users.php" class="current" aria-current="page">全部<span class="count">（5）</span></a> |</li>-->
<!--                <li class="administrator"><a href="users.php?role=administrator">管理员<span class="count">（1）</span></a> |</li>-->
<!--                <li class="editor"><a href="users.php?role=editor">教练<span class="count">（2）</span></a> |</li>-->
<!--                <li class="subscriber"><a href="users.php?role=subscriber">学生<span class="count">（2）</span></a></li>-->
<!--            </ul>-->
            <div>
                <ul id="tab">
                    <li class="<?php if($type == 1) echo 'active'?>" onclick="window.location.href='?page=order&type=1'">全部</li>
                    <li class="<?php if($type == 2) echo 'active'?>" onclick="window.location.href='?page=order&type=2'">待支付</li>
                    <li class="<?php if($type == 3) echo 'active'?>" onclick="window.location.href='?page=order&type=3'">支付完成</li>
                    <li class="<?php if($type == 4) echo 'active'?>" onclick="window.location.href='?page=order&type=4'">待退款</li>
                    <li class="<?php if($type == 5) echo 'active'?>" onclick="window.location.href='?page=order&type=5'">已退款</li>
                </ul>
            </div>




                <input type="hidden" id="_wpnonce" name="_wpnonce" value="0f5195546a"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">	<div class="tablenav top">

                    <div class="alignleft actions bulkactions">
<!--                        <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label><select name="action" id="bulk-action-selector-top">-->
<!--                            <option value="-1">批量操作</option>-->
<!--                            <option value="delete">删除</option>-->
<!--                        </select>-->
<!--                        <input type="submit" id="doaction" class="button action" value="应用">-->

                    </div>

                    <div class="alignleft actions">
                        <form action="?page=download&action=order" method="post">

                            <label class="" for="">导出订单</label>
                            <input type="date" name="start_date" /> -
                            <input type="date" name="end_date" />
                            <input type="submit" name="changeit" id="changeit" class="button" value="导出">
                        </form>
                    </div>
                    <div class="tablenav-pages one-page">
                        <?=$pageHtml?>
                    </div>
                    <br class="clear">

                </div>

                <h2 class="screen-reader-text">用户列表</h2><table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="serialnumber" class="manage-column column-serialnumber column-primary sortable desc">
                            <a href="javascript:;">
                                <span>订单流水</span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" id="username" class="manage-column column-username">用户名</th>
                        <th scope="col" id="post_title" class="manage-column column-post_title">比赛</th>
                        <th scope="col" id="funllname" class="manage-column column-funllname">收件人</th>
                        <th scope="col" id="telephone" class="manage-column column-telephone">联系电话</th>
                        <th scope="col" id="address" class="manage-column column-address">收获地址</th>
                        <th scope="col" id="order_type" class="manage-column column-order_type">订单类型</th>
                        <th scope="col" id="express_number" class="manage-column column-express_number">快递单号</th>
                        <th scope="col" id="express_company" class="manage-column column-express_company">快递公司</th>
                        <th scope="col" id="pay_type" class="manage-column column-pay_type">支付类型</th>
                        <th scope="col" id="cost" class="manage-column column-cost">订单总价</th>
                        <th scope="col" id="pay_status" class="manage-column column-pay_status">支付状态</th>
                        <th scope="col" id="created_time" class="manage-column column-created_time">创建时间</th>

                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">
                        <?php foreach($rows as $row){ ?>
                            <tr id="user-5" data-id="<?=$row['id']?>">
                                <th scope="row" class="check-column">
                                    <label class="screen-reader-text" for="user_5"></label>
                                    <input type="checkbox" name="users[]" id='' class="subscriber" value="5">
                                </th>
                                <td class="username column-serialnumber has-row-actions column-primary" data-colname="订单流水">
                                    <strong><a href="javascript:;"><?=$row['serialnumber']?></a></strong><br>
                                    <div class="row-actions">
<!--                                        <span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/user-edit.php?user_id=5&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">编辑</a>| </span>-->
                                        <?php
                                            switch ($row['pay_status']){
                                                case -1:
                                        ?>
                                                    <span class="edit"><a href="javascript:;" class="no_refund">不退款</a>| </span>
                                                    <span class="delete"><a class="submitdelete" href="?page=order-refund&serial=<?=$row['serialnumber']?>">退款</a> | </span>
                                        <?php
                                                break;
                                            }
                                        ?>
                                    </div>
                                    <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                                </td>
                                <td class="name column-username" data-colname="用户名"><span aria-hidden="true">—</span><span class="screen-reader-text"><?=$row['user_login']?></span></td>
                                <td class="email column-post_title" data-colname=""><?=$row['post_title']?></td>
                                <td class="role column-role" data-colname="收件人"><?=$row['funllname']?></td>
                                <td class="posts column-telephone" data-colname="联系电话"><?=$row['telephone']?></td>
                                <td class="posts column-address" data-colname="收货地址"><?=$row['address']?></td>
                                <td class="posts column-order_type" data-colname="订单类型"><?=$row['order_type']?></td>
                                <td class="posts column-express_number" data-colname="快递单号"><?=$row['express_number']?></td>
                                <td class="posts column-express_company" data-colname="快递公司"><?=$row['express_company']?></td>
                                <td class="posts column-pay_type" data-colname="支付类型"><?=$row['pay_type']?></td>
                                <td class="posts column-cost" data-colname="订单总价"><?=$row['cost']?></td>
                                <td class="posts column-pay_status" data-colname="支付状态"><?=$row['pay_title']?></td>
                                <td class="posts column-created_time" data-colname="创建时间"><?=$row['created_time']?></td>

                            </tr>
                        <?php } ?>

                    </tbody>
                    <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-serialnumber column-primary sortable desc">
                            <a href="javascript:;"><span>订单流水</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" class="manage-column column-username">用户名</th>
                        <th scope="col" class="manage-column column-post_title">比赛</th>
                        <th scope="col" class="manage-column column-funllname">收件人</th>
                        <th scope="col" class="manage-column column-telephone">联系电话</th>
                        <th scope="col" class="manage-column column-address">收获地址</th>
                        <th scope="col" class="manage-column column-order_type">订单类型</th>
                        <th scope="col" class="manage-column column-express_number">快递单号</th>
                        <th scope="col" class="manage-column column-express_company">快递公司</th>
                        <th scope="col" class="manage-column column-pay_type">支付类型</th>
                        <th scope="col" class="manage-column column-cost">订单总价</th>
                        <th scope="col" class="manage-column column-pay_status">支付状态</th>
                        <th scope="col" class="manage-column column-created_time">创建时间</th>

                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">

                    <div class="alignleft actions bulkactions">
<!--                        <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label><select name="action2" id="bulk-action-selector-bottom">-->
<!--                            <option value="-1">批量操作</option>-->
<!--                            <option value="delete">删除</option>-->
<!--                        </select>-->
<!--                        <input type="submit" id="doaction2" class="button action" value="应用">-->
                    </div>
                    <div class="alignleft actions">
                        <form action="?page=download&action=order" method="post">

                            <label class="" for="">导出订单</label>
                            <input type="date" name="start_date" /> -
                            <input type="date" name="end_date" />
                            <input type="submit" name="changeit" id="changeit2" class="button" value="导出">
                        </form>
                    </div>
                    <div class="tablenav-pages one-page">
                        <?=$pageHtml?>
                    </div>
                    <br class="clear">

                </div>
        </div>

        <?php
    }



    /**
     * 退款页
     */
    public function refund(){
        $serial = trim($_GET['serial']);
        global $wpdb;
        $row = $wpdb->get_row('SELECT o.pay_lowdown,o.cost,o.serialnumber,o.telephone,o.address,o.express_number,o.express_company,u.user_login,
        CASE o.pay_type WHEN "zfb" THEN "支付宝" WHEN "wx" THEN "微信" WHEN "ylk" THEN "银联卡" ELSE "-" END AS pay_type 
        FROM '.$wpdb->prefix.'order AS o
        LEFT JOIN '.$wpdb->prefix.'users AS u ON U.id=o.user_id 
        WHERE serialnumber='.$serial, ARRAY_A);
        ?>
            <div id="box">
                <table id="form-table">
                    <tr>
                        <td>订单流水</td>
                        <td><?=$row['serialnumber']?></td>
                    </tr>
                    <tr>
                        <td>下单用户名</td>
                        <td><?=$row['user_login']?></td>
                    </tr>
                        <td>支付方式</td>
                        <td><?=$row['pay_type']?></td>
                    </tr>
                    <tr>
                        <td>订单金额</td>
                        <td><?=$row['cost']?></td>
                    </tr>
                    <tr>
                        <td>输入退款金额</td>
                        <td><input type="text" name="refund-cost"></td>
                    </tr>
                    <tr>
                        <td> <button class="btn-pri refund-btn" type="button">确定</button></td>
                    </tr>

                </table>
                <input type="hidden" name="_wpnonce" id="" value="<?=wp_create_nonce('student_refund_code_nonce');?>">
            </div>

        <?php
    }

    /**
     * 退款单
     */
    public function refundOrder(){
        global $wpdb;
        $page = ($page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1) < 1 ? 1 : $page;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS 
        o.serialnumber,
        r.refund_lowdown,
        o.pay_lowdown,o.telephone,
        o.address,
        o.cost,
        u.user_login, 
        r.refund_cost, 
        o.telephone,
        CASE o.pay_type 
        WHEN "zfb" THEN "支付宝" 
        WHEN "wx" THEN "微信" 
        WHEN "ylk" THEN "银联卡" 
        END AS pay_type,
        CASE o.pay_status 
        WHEN -2 THEN "已退款" 
        WHEN -1 THEN "待退款" 
        WHEN 1 THEN "待支付" 
        WHEN 2 THEN "支付完成" 
        END AS pay_status,
        CASE o.order_type WHEN 1 THEN "报名订单" END AS order_type  
        FROM '.$wpdb->prefix.'order_refund AS r 
        LEFT JOIN '.$wpdb->prefix.'order AS o ON o.id=r.order_id 
        LEFT JOIN '.$wpdb->users.' AS u ON u.ID=o.user_id LIMIT '.$start.','.$pageSize, ARRAY_A);
//        var_dump($rows);
        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page
        ));
        ?>

        <div class="wrap">
            <h1 class="wp-heading-inline">退款单</h1>







            <div class="tablenav top">

<!--                <div class="alignleft actions bulkactions">-->
<!--                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label><select name="action" id="bulk-action-selector-top">-->
<!--                        <option value="-1">批量操作</option>-->
<!--                        <option value="delete">删除</option>-->
<!--                    </select>-->
<!--                    <input type="submit" id="doaction" class="button action" value="应用">-->
<!--                </div>-->
<!--                <div class="alignleft actions">-->
<!--                    <form action="?page=download&amp;action=order" method="post">-->
<!---->
<!--                        <label class="" for="">导出订单</label>-->
<!--                        <input type="date" name="start_date"> --->
<!--                        <input type="date" name="end_date">-->
<!--                        <input type="submit" name="changeit" id="changeit" class="button" value="导出">-->
<!--                    </form>-->
<!--                </div>-->
                <div class="tablenav-pages one-page">
                    <?=$pageHtml?>
                </div>
                <br class="clear">

            </div>

            <h2 class="screen-reader-text">用户列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                        <input id="cb-select-all-1" type="checkbox">
                    </td>
                    <th scope="col" id="serialnumber" class="manage-column column-serialnumber column-primary sortable desc">
                        <a href="javascript:;">
                            <span>订单流水</span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" id="username" class="manage-column column-username">用户名</th>
                    <th scope="col" id="telephone" class="manage-column column-telephone">联系电话</th>
                    <th scope="col" id="order_type" class="manage-column column-order_type">订单类型</th>
                    <th scope="col" id="pay_type" class="manage-column column-pay_type">支付类型</th>
                    <th scope="col" id="cost" class="manage-column column-cost">订单总价</th>
                    <th scope="col" id="refund_cost" class="manage-column column-refund_cost">退款金额</th>
                    <th scope="col" id="pay_status" class="manage-column column-pay_status">支付状态</th>

                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">
                <?php foreach ($rows as $row ){?>
                    <tr id="user-5">
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="user_5"></label>
                            <input type="checkbox" name="" id="" class="subscriber" value="">
                        </th>
                        <td class="username column-serialnumber has-row-actions column-primary" data-colname="订单流水">
                            <strong><a href="javascript:;"><?=$row['serialnumber']?></a></strong><br>
<!--                            <div class="row-actions">-->
<!--                                <span class="delete"><a class="submitdelete" href="?page=order-refund&amp;serial=18081173129159">退款</a> </span>-->
<!--                            </div>-->
<!--                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>-->
                        </td>
                        <td class="name column-username" data-colname="用户名"><span aria-hidden="true"><?=$row['user_login']?></span><span class="screen-reader-text"></span></td>
                        <td class="posts column-telephone" data-colname="联系电话"><?=$row['telephone']?></td>
                        <td class="posts column-order_type" data-colname="订单类型"><?=$row['order_type']?></td>
                        <td class="posts column-pay_type" data-colname="支付类型"><?=$row['pay_type']?></td>
                        <td class="posts column-cost" data-colname="订单总价"><?=$row['cost']?></td>
                        <td class="posts column-refund_cost" data-colname="退款金额"><?=$row['refund_cost']?></td>
                        <td class="posts column-pay_status" data-colname="支付状态"><?=$row['pay_status']?></td>

                    </tr>
                <?php } ?>

                </tbody>
                <tfoot>

                        <tr>
                            <td class="manage-column column-cb check-column">
                                <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                                <input id="cb-select-all-2" type="checkbox">
                            </td>
                            <th scope="col" class="manage-column column-serialnumber column-primary sortable desc">
                                <a href="javascript:;"><span>订单流水</span><span class="sorting-indicator"></span></a>
                            </th>
                            <th scope="col" class="manage-column column-username">用户名</th>
                            <th scope="col" class="manage-column column-telephone">联系电话</th>
                            <th scope="col" class="manage-column column-order_type">订单类型</th>
                            <th scope="col" class="manage-column column-pay_type">支付类型</th>
                            <th scope="col" class="manage-column column-cost">订单总价</th>
                            <th scope="col" class="manage-column column-refund_cost">退款金额</th>
                            <th scope="col" class="manage-column column-pay_status">支付状态</th>

                        </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

<!--                <div class="alignleft actions bulkactions">-->
<!--                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label><select name="action2" id="bulk-action-selector-bottom">-->
<!--                        <option value="-1">批量操作</option>-->
<!--                        <option value="delete">删除</option>-->
<!--                    </select>-->
<!--                    <input type="submit" id="doaction2" class="button action" value="应用">-->
<!--                </div>-->
<!--                <div class="alignleft actions">-->
<!--                    <form action="?page=download&amp;action=order" method="post">-->
<!---->
<!--                        <label class="" for="">导出订单</label>-->
<!--                        <input type="date" name="start_date"> --->
<!--                        <input type="date" name="end_date">-->
<!--                        <input type="submit" name="changeit" id="changeit2" class="button" value="导出">-->
<!--                    </form>-->
<!--                </div>-->
                <div class="tablenav-pages one-page">
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
            <br class="clear">
        </div>
        <?php
    }


    /**
     * 引入当前页面css/js
     */
    public function register_scripts(){
        switch ($_GET['page']){
            case 'order':
                wp_register_script('list-js',match_js_url.'order-lists.js');
                wp_enqueue_script( 'list-js' );
                wp_register_style('list-css',match_css_url.'order-lists.css');
                wp_enqueue_style( 'list-css' );
                break;
            case 'order-refund':
                wp_register_style('datum-css',match_css_url.'teacher-datum.css');
                wp_enqueue_style( 'datum-css' );
                wp_register_script('list-js',match_js_url.'order-lists.js');
                wp_enqueue_script( 'list-js' );
                break;
        }
        echo "<script>var ajax_url='".admin_url('admin-ajax.php' )."';</script>";
    }
}

new Order();