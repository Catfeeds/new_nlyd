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
        }
        add_menu_page('财务统计', '财务统计', 'statistics', 'statistics',array($this,'index'),'dashicons-businessman',99);
    }

    /**
     * 统计首页
     */
    public function index(){
        $stype = isset($_GET['stype']) ? intval($_GET['stype']) : 1;
        $link_num = isset($_GET['link_num']) ? intval($_GET['link_num']) : 0;
        global $wpdb;
        //总收入
        $all_income = $wpdb->get_var("SELECT SUM(cost) FROM {$wpdb->prefix}order WHERE pay_status IN(2,3,4)");
        //总支出
        $all_expenses = $wpdb->get_var("SELECT SUM(cost) FROM {$wpdb->prefix}order WHERE pay_status=-2");
        //总盈利
        $all_profit = $all_income-$all_expenses;
        $link_name = '';
        switch ($stype){
            case 1://年
                $dateWhere = "YEAR( created_time ) = YEAR( curdate( ))+{$link_num}";
                $link_name = '年';
                break;
            case 2://季度
                $dateWhere = "QUARTER( created_time ) = QUARTER( curdate( ))+{$link_num} AND YEAR( created_time ) = YEAR( curdate( ))";
                $link_name = '季度';
                break;
            case 3://月
                $dateWhere = "MONTH(created_time) = MONTH(curdate())+{$link_num} AND YEAR( created_time ) = YEAR( curdate( ))";
                $link_name = '月';
                break;
            case 4://周
                $dateWhere = "WEEK(created_time) = WEEK(curdate())+{$link_num} AND YEAR( created_time ) = YEAR( curdate( ))";
                $link_name = '周';
                break;
            case 5://日
                $dateWhere = "DAY(created_time) = DAY(curdate())+{$link_num} AND MONTH(created_time) = MONTH(curdate()) AND YEAR( created_time ) = YEAR( curdate( ))";
                $link_name = '天';
                break;
        }
        $income = $wpdb->get_var("SELECT SUM(cost) FROM {$wpdb->prefix}order WHERE pay_status IN(2,3,4) AND {$dateWhere}");
        $expenses = $wpdb->get_var("SELECT SUM(cost) FROM {$wpdb->prefix}order WHERE pay_status IN(-2) AND {$dateWhere}");
//        leo_dump($wpdb->last_query);
        $rows = [];
        $status_type = 1;
        ?>
            <div>总收入<?=$all_income?></div>
            <div>总支出<?=$all_expenses?></div>
            <div>总利润<?=$all_profit?></div>
            <div>收入<?=$income?></div>
            <div>支出<?=$expenses?></div>
            <div>利润<?=$income-$expenses?></div>

        <div class="wrap">
            <h1 class="wp-heading-inline">主体类型列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize-type')?>" class="page-title-action">添加主体类型</a>

            <hr class="wp-header-end">
            <ul class="subsubsub">
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&stype=1')?>" <?=$status_type===1?'class="current"':''?> aria-current="page">按年<span class="count">（<?=$all_income?>）</span></a> |</li>
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&stype=2')?>" <?=$status_type===2?'class="current"':''?> aria-current="page">按季度<span class="count">（<?=$all_expenses?>）</span></a> |</li>
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&stype=3')?>" <?=$status_type===3?'class="current"':''?> aria-current="page">按月<span class="count">（<?=$all_profit?>）</span></a> </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&stype=4')?>" <?=$status_type===4?'class="current"':''?> aria-current="page">按周<span class="count">（<?=$all_profit?>）</span></a> </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&stype=5')?>" <?=$status_type===-5?'class="current"':''?> aria-current="page">按天<span class="count">（<?=$all_profit?>）</span></a> </li>
            </ul>
            <br class="clear">
            <ul class="subsubsub">
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&stype='.$stype.'&link_num='.($link_num-1))?>" <?=$status_type===1?'class="current"':''?> aria-current="page">上一<?=$link_name?></a> |</li>
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&stype='.$stype.'&link_num='.($link_num+1))?>" <?=$status_type===2?'class="current"':''?> aria-current="page">下一<?=$link_name?></a> </li>
             </ul>
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
                    <th scope="col" id="name" class="manage-column column-name column-primary">名称</th>
                    <th scope="col" id="zone_type_alias" class="manage-column column-zone_type_alias">别名</th>
                    <th scope="col" id="status" class="manage-column column-status">状态</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){
                    ?>
                    <tr>
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="cb-select-407">选择<?=$row['zone_type_name']?></label>
                            <input id="cb-select-<?=$row['id']?>" type="checkbox" name="post[]" value="<?=$row['id']?>">
                            <div class="locked-indicator">
                                <span class="locked-indicator-icon" aria-hidden="true"></span>
                                <span class="screen-reader-text">“<?=$row['zone_type_name']?>”已被锁定</span>
                            </div>
                        </th>
                        <td class="name column-name has-row-actions column-primary" data-colname="名称">
                            <?=$row['zone_type_name']?>
                            <br>
                            <div class="row-actions">
                                <span class="edit"><a href="<?=admin_url('admin.php?page=fission-add-organize-type&id='.$row['id'])?>">编辑</a> </span>
                                <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                                <!--                               <span class="view"><a href="">资料</a></span>-->
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="zone_type_alias column-zone_type_alias" data-colname="别名">
                            <?=$row['zone_type_alias']?>
                        </td>
                        <td class="status column-status" data-colname="状态">
                            <?=$row['zone_type_status_name']?>
                        </td>

                    </tr>
                    <?php
                }
                ?>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-name column-primary">名称</th>
                    <th scope="col" class="manage-column column-zone_type_alias">别名</th>
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