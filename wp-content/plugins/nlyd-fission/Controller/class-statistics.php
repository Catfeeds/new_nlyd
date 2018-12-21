<?php
class Statistics{
    public function __construct($is_list = false)
    {
        if($is_list === false){
            add_action( 'admin_menu', array($this,'register_organize_menu_page') );
//            add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
        }
    }
    public function register_organize_menu_page(){
        if ( current_user_can( 'administrator' ) && !current_user_can( 'statistics' ) ) {
            global $wp_roles;
            $role = 'statistics';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'branch';//权限名
            $wp_roles->add_cap('administrator', $role);
        }
        add_menu_page('数据统计', '数据统计', 'statistics', 'statistics',array($this,'index'),'dashicons-businessman',99);
        add_submenu_page('statistics','赛区统计','赛区统计','branch','statistics-branch',array($this,'branch'));
    }

    /**
     * 统计首页
     */
    public function index(){
        $stype = isset($_GET['stype']) ? intval($_GET['stype']) : 1;
        $link_num = isset($_GET['link_num']) ? intval($_GET['link_num']) : 0;
        $cate = isset($_GET['cate']) ? intval($_GET['cate']) : 1;
        $where = 'WHERE 1=1';
        $date_format = '';
        switch ($cate){
            case 1://比赛
                $where .= ' AND order_type=1';
                break;
            case 2://考级
                $where .= ' AND order_type=2';
                break;
            case 3://脑力产品
                $where .= ' AND order_type=3';
                break;
            case 4://课程相关
                $where .= ' AND order_type=4';
                break;
            case 5://其它收支
                $where .= ' AND order_type=1';
                break;
        }
        global $wpdb;
        $rows = [];
        //收入
        $all_income = $wpdb->get_results("SELECT SUM(cost) AS pay_amount,DATE_FORMAT(created_time,'%Y-%m') AS times FROM {$wpdb->prefix}order {$where} AND pay_status IN(2,3,4) GROUP BY YEAR(created_time),MONTH(created_time) ORDER BY created_time DESC", ARRAY_A);
        foreach ($all_income as $key => $info) {
            $rows[$info['times']] = isset($rows[$info['times']]) ? array_merge($rows[$info['times']],$info) : $info;
        }

        //退款
        $all_income2 = $wpdb->get_results("SELECT SUM(ord.refund_cost) AS refund_amount,DATE_FORMAT(ord.created_time,'%Y-%m') AS times FROM {$wpdb->prefix}order_refund AS ord 
                      LEFT JOIN {$wpdb->prefix}order AS o ON o.id=ord.order_id
                      WHERE o.order_type='{$cate}' 
                      GROUP BY YEAR(ord.created_time),MONTH(ord.created_time) ORDER BY ord.created_time DESC",ARRAY_A);

        foreach ($all_income2 as $key => $info) {
            $rows[$info['times']] = isset($rows[$info['times']]) ? array_merge($rows[$info['times']],$info) : $info;
        }
        if($cate === 1){
            //奖金
            $all_income3 = $wpdb->get_results("SELECT SUM(all_bonus) AS bonus_amount,DATE_FORMAT(send_time,'%Y-%m') AS times FROM {$wpdb->prefix}match_bonus WHERE is_send=2 GROUP BY YEAR(send_time),MONTH(send_time) ORDER BY send_time DESC",ARRAY_A);
            foreach ($all_income3 as $key => $info) {
                $rows[$info['times']] = isset($rows[$info['times']]) ? array_merge($rows[$info['times']],$info) : $info;
            }
        }

//        leo_dump($rows);die;

//        switch ($stype){
//            case 1://年
//                $dateWhere = "YEAR( created_time ) = YEAR( curdate( ))+{$link_num}";
//                $link_name = '年';
//                break;
//            case 2://季度
//                $dateWhere = "QUARTER( created_time ) = QUARTER( curdate( ))+{$link_num} AND YEAR( created_time ) = YEAR( curdate( ))";
//                $link_name = '季度';
//                break;
//            case 3://月
//                $dateWhere = "MONTH(created_time) = MONTH(curdate())+{$link_num} AND YEAR( created_time ) = YEAR( curdate( ))";
//                $link_name = '月';
//                break;
//            case 4://周
//                $dateWhere = "WEEK(created_time) = WEEK(curdate())+{$link_num} AND YEAR( created_time ) = YEAR( curdate( ))";
//                $link_name = '周';
//                break;
//            case 5://日
//                $dateWhere = "DAY(created_time) = DAY(curdate())+{$link_num} AND MONTH(created_time) = MONTH(curdate()) AND YEAR( created_time ) = YEAR( curdate( ))";
//                $link_name = '天';
//                break;
//        }


        ?>

        <div class="wrap">
            <h1 class="wp-heading-inline">主体类型列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize-type')?>" class="page-title-action">添加主体类型</a>

            <hr class="wp-header-end">
            <ul class="subsubsub">
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&cate=1')?>" <?=$cate===1?'class="current"':''?> aria-current="page">比赛相关</a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&cate=2')?>" <?=$cate===2?'class="current"':''?> aria-current="page">考级相关</a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&cate=3')?>" <?=$cate===3?'class="current"':''?> aria-current="page">脑力产品</a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&cate=4')?>" <?=$cate===4?'class="current"':''?> aria-current="page">课程相关</a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&cate=5')?>" <?=$cate===5?'class="current"':''?> aria-current="page">其它收支</a></li>
            </ul>
            <br class="clear">

            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
                        <option value="delete">删除</option>
                    </select>
                    <input type="submit" id="doaction" class="button action" value="应用">
                </div>

                <div class="tablenav-pages">

                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">主体列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th scope="col" id="dates" class="manage-column column-dates column-primary">日期</th>
                    <th scope="col" id="order_pay" class="manage-column column-order_pay">订单支付</th>
                    <th scope="col" id="order_refund" class="manage-column column-order_refund">订单退款</th>
                    <th scope="col" id="bonus_send" class="manage-column column-bonus_send">奖金发放</th>
                    <th scope="col" id="status" class="manage-column column-status">状态</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $k =>$row){
                    ?>
                    <tr>
                        <th scope="row" class="check-column">
                            <input id="cb-select-<?=$k?>" type="checkbox" name="post[]" value="<?=$k?>">
                        </th>
                        <td class="dates column-dates has-row-actions column-primary" data-colname="日期">
                            <?=$row['times']?>
                            <br>
                            <div class="row-actions">
                                <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                                <!--                               <span class="view"><a href="">资料</a></span>-->
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="order_pay column-order_pay" data-colname="订单支付"> <?=isset($row['pay_amount']) ? $row['pay_amount'] : 0?> </td>
                        <td class="order_refund column-order_refund" data-colname="订单退款"><?=isset($row['refund_amount']) ? $row['refund_amount'] : 0?> </td>
                        <td class="bonus_send column-bonus_send" data-colname="奖金发放"><?=isset($row['bonus_amount']) ? $row['bonus_amount'] : 0?> </td>
                        <td class="status column-status" data-colname="状态">

                        </td>

                    </tr>
                    <?php
                }
                ?>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-dates column-primary">日期</th>
                    <th scope="col" class="manage-column column-order_pay">订单支付</th>
                    <th scope="col" class="manage-column column-order_refund">订单退款</th>
                    <th scope="col" class="manage-column column-bonus_send">奖金发放</th>
                    <th scope="col" class="manage-column column-status">状态</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
                        <option value="delete">删除</option>
                    </select>
                    <input type="submit" id="doaction2" class="button action" value="应用">
                </div>

                <div class="tablenav-pages">
                </div>
                <br class="clear">
            </div>

            <br class="clear">
        </div>
        <?php
    }

    /**
     * 赛区统计
     */
    public function branch(){
        global $wpdb;
        //开赛数量
        $match = $wpdb->get_results("SELECT zm.id,zm.zone_name,zm.user_id,zt.zone_type_name,COUNT(mmn.id) AS match_num,GROUP_CONCAT(mmn.match_id) AS match_ids 
                FROM {$wpdb->prefix}zone_meta AS zm 
                LEFT JOIN {$wpdb->prefix}zone_type AS zt ON zt.id=zm.type_id     
                LEFT JOIN {$wpdb->prefix}match_meta_new AS mmn ON mmn.created_id=zm.user_id AND mmn.id!=''                  
                GROUP BY zm.id", ARRAY_A);

        foreach ($match AS &$row){
            //比赛总人数
            $row['match_people_num'] = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}order WHERE order_type=1 AND pay_status IN(2,3,4) AND match_id IN({$row['match_ids']})");
            //收益金额
            $row['match_profit'] = $wpdb->get_var("SELECT SUM(user_income) FROM {$wpdb->prefix}user_stream_logs WHERE income_type='match' AND user_id='{$row['user_id']}'");
        }

        //考级数量
        $grading = $wpdb->get_results("SELECT zm.id,zm.zone_name,zm.user_id,zt.zone_type_name,COUNT(gm.id) AS grading_num,GROUP_CONCAT(gm.grading_id) AS grading_ids 
                FROM {$wpdb->prefix}zone_meta AS zm 
                LEFT JOIN {$wpdb->prefix}grading_meta AS gm ON gm.created_person=zm.user_id 
                LEFT JOIN {$wpdb->prefix}zone_type AS zt ON zt.id=zm.type_id                       
                GROUP BY zm.id", ARRAY_A);
        //考级总人数
        foreach ($grading AS &$grow){
            $grow['grading_people_num'] = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}order WHERE order_type=2 AND pay_status IN(2,3,4) AND match_id IN({$row['grading_ids']})");
            $grow['grading_profit'] = $wpdb->get_var("SELECT SUM(user_income) FROM {$wpdb->prefix}user_stream_logs WHERE income_type='grading' AND user_id='{$row['user_id']}'");
        }

        $match = array_column($match,NULL,'id');
        $grading = array_column($grading,NULL,'id');
        foreach ($grading as $ggk => $ggv){
            if(isset($match[$ggk])){
                $match[$ggk] = array_merge($match[$ggk],$ggv);
            }else{
                $match[$ggk] = $ggv;
            }
        }
        $rows = $match;
        //总收益
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">主体数据</h1>


            <hr class="wp-header-end">
            <br class="clear">

            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

<!--                <div class="alignleft actions bulkactions">-->
<!--                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>-->
<!--                    <select name="action" id="bulk-action-selector-top">-->
<!--                        <option value="-1">批量操作</option>-->
<!--                        <option value="delete">删除</option>-->
<!--                    </select>-->
<!--                    <input type="submit" id="doaction" class="button action" value="应用">-->
<!--                </div>-->

                <div class="tablenav-pages">

                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">主体列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th scope="col" id="zone_name" class="manage-column column-zone_name column-primary">主体名称</th>
                    <th scope="col" id="match_num" class="manage-column column-match_num">开赛数量</th>
                    <th scope="col" id="match_people_num" class="manage-column column-match_people_num">参赛人数</th>
                    <th scope="col" id="match_profit" class="manage-column column-match_profit">比赛收益</th>
                    <th scope="col" id="grading_num" class="manage-column column-grading_num">开考级数量</th>
                    <th scope="col" id="grading_people_num" class="manage-column column-grading_people_num">考级人数</th>
                    <th scope="col" id="grading_profit" class="manage-column column-grading_profit">考级收益</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $k =>$row){
                    ?>
                    <tr>
                        <th scope="row" class="check-column">
                            <input id="cb-select-<?=$k?>" type="checkbox" name="post[]" value="<?=$k?>">
                        </th>
                        <td class="dates column-dates has-row-actions column-primary" data-colname="日期">
                            <?=$row['zone_name']?>
                            <br>

                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="match_num column-match_num" data-colname="开赛数量"> <?=isset($row['match_num']) ? $row['match_num'] : 0?> </td>
                        <td class="match_people_num column-match_people_num" data-colname="参赛人数"> <?=isset($row['match_people_num']) ? $row['match_people_num'] : 0?> </td>
                        <td class="match_profit column-match_profit" data-colname="比赛收益"><?=isset($row['match_profit']) ? $row['match_profit'] : 0?> </td>
                        <td class="grading_num column-grading_num" data-colname="开考级数量"><?=isset($row['grading_num']) ? $row['grading_num'] : 0?> </td>
                        <td class="grading_people_num column-grading_people_num" data-colname="考级人数"><?=isset($row['grading_people_num']) ? $row['grading_people_num'] : 0?> </td>
                        <td class="grading_profit column-grading_profit" data-colname="考级收益"><?=isset($row['grading_profit']) ? $row['grading_profit'] : 0?> </td>

                    </tr>
                    <?php
                }
                ?>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-zone_name column-primary">主体名称</th>
                    <th scope="col" class="manage-column column-match_num">开赛数量</th>
                    <th scope="col" class="manage-column column-match_people_num">参赛人数</th>
                    <th scope="col" class="manage-column column-match_profit">比赛收益</th>
                    <th scope="col" class="manage-column column-grading_num">开考级数量</th>
                    <th scope="col" class="manage-column column-grading_people_num">考级人数</th>
                    <th scope="col" class="manage-column column-grading_profit">考级收益</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

<!--                <div class="alignleft actions bulkactions">-->
<!--                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>-->
<!--                    <select name="action2" id="bulk-action-selector-bottom">-->
<!--                        <option value="-1">批量操作</option>-->
<!--                        <option value="delete">删除</option>-->
<!--                    </select>-->
<!--                    <input type="submit" id="doaction2" class="button action" value="应用">-->
<!--                </div>-->

                <div class="tablenav-pages">
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
            case 'fission':
                wp_register_script('layui-js',match_js_url.'layui/layui.js');
                wp_enqueue_script( 'layui-js' );
                break;
        }
    }
}
new Statistics();