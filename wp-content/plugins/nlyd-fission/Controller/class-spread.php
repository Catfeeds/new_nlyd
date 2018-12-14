<?php
//组织主体控制器
class Spread{
    public function __construct($is_list = false)
    {
        if($is_list === false){
            add_action( 'admin_menu', array($this,'register_organize_menu_page') );
//            add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
        }
    }
    public function register_organize_menu_page(){
        if ( current_user_can( 'administrator' ) && !current_user_can( 'fission' ) ) {
            global $wp_roles;
            $role = 'profit_set';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'add_profit_set';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'profit_log';//权限名
            $wp_roles->add_cap('administrator', $role);
        }
        add_submenu_page('fission','收益设置','收益设置','profit_set','fission-profit-set',array($this,'profitSet'));
        add_submenu_page('fission','新增收益设置','新增收益设置','add_profit_set','fission-add-profit-set',array($this,'addProfitSet'));
        add_submenu_page('fission','收益记录','收益记录','profit_log','fission-profit-log',array($this,'profitLog'));
    }

    /**
     * 收益设置
     */
    public function profitSet(){
        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}spread_set WHERE parent_id=0", ARRAY_A);

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">主体权限列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize-power')?>" class="page-title-action">添加主体权限</a>

            <hr class="wp-header-end">

            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <br class="clear">
            </div>
            <h2 class="screen-reader-text">主体列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th scope="col" id="name" class="manage-column column-name column-primary">名称</th>
                    <th scope="col" id="profit_amount" class="manage-column column-profit_amount">金额</th>
                    <th scope="col" id="option1" class="manage-column column-option1">操作</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){
                    ?>
                    <tr class="parent_tr" data-id="<?=$row['id']?>" style="background-color: #00c4c4">
                        <td class="name column-name has-row-actions column-primary" data-colname="名称" colspan="3" style="">
                            <strong><?=$row['profit_name']?></strong>
                            <a href="javascript:;" class="hide-or-show" style="cursor: pointer">隐藏</a>
                            <br>

                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td>
                            <a href="<?=admin_url('admin.php?page=fission-add-profit-set&id='.$row['id'])?>">编辑</a>
                            |
                            <a href="javascript:;" class="remove-set">删除</a>
                        </td>

                    </tr>
                    <tbody data-type="show" class="tobdy-<?=$row['id']?>">
                    <?php
                    $childRows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}spread_set WHERE parent_id='{$row['id']}'", ARRAY_A);
                    foreach ($childRows as $cRow){
                    ?>
                        <tr data-id="<?=$cRow['id']?>">
                            <th scope="row" class="check-column">
                                <label class="screen-reader-text" for="cb-select-407">选择<?=$cRow['profit_name']?></label>
                                <input id="cb-select-<?=$cRow['id']?>" type="checkbox" name="post[]" value="<?=$cRow['id']?>">
                                <div class="locked-indicator">
                                    <span class="locked-indicator-icon" aria-hidden="true"></span>
                                    <span class="screen-reader-text">“<?=$cRow['profit_name']?>”已被锁定</span>
                                </div>
                            </th>
                            <td class="name column-name has-row-actions column-primary" data-colname="名称">
                                <?=$cRow['profit_name']?>
                                <br>
                                <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                            </td>
                            <td class="profit_amount column-profit_amount has-row-actions" data-colname="金额">
                                <?=$cRow['profit_amount']?>

                            </td>
                            <td class="option1 column-option1 has-row-actions" data-colname="操作">
                                <a href="<?=admin_url('admin.php?page=fission-add-profit-set&id='.$cRow['id'])?>">编辑</a>
                                |
                                <a href="javascript:;" class="remove-set">删除</a>
                            </td>

                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <?php
                }
                ?>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-name column-primary">名称</th>
                    <th scope="col" class="manage-column column-profit_amount">金额</th>
                    <th scope="col" class="manage-column column-option1">操作</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">


                <br class="clear">
            </div>

            <br class="clear">
            <script>
                jQuery(document).ready(function($) {
                    $('.hide-or-show').on('click',function () {
                        var _tr = $(this).closest('.parent_tr');
                        var _id = _tr.attr('data-id');
                        var _tbody = $('.tobdy-'+_id);
                        var _type = _tbody.attr('data-type');
                        console.log(_type);
                        if(_type == 'show'){
                            _tbody.hide();
                            _tbody.attr('data-type','hide')
                            $(this).text('显示');
                        }else{
                            _tbody.show();
                            _tbody.attr('data-type','show')
                            $(this).text('隐藏');
                        }
                    });
                    //删除
                    $('.remove-set').on('click',function () {
                        var _id = $(this).closest('tr').attr('data-id');
                        if(confirm('确认要删除此项设置吗?删除后无法恢复!')){
                            $.ajax({
                                url : ajaxurl,
                                data : {'action':'delSpreadSet','id':_id},
                                type:'post',
                                dataType : 'json',
                                success : function (response) {
                                    alert(response.data.info);
                                    if(response['success']){
                                        window.location.reload();
                                    }
                                },error : function () {
                                    alert('请求失败!');
                                }
                            });
                        }
                    });
                });
            </script>
        </div>
        <?php
    }


    /**
     * 新增收益设置
     */
    public function addProfitSet(){
        global $wpdb;
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $error_msg = '';
        $success_msg = '';
        if(is_post()){
            $profit_name = isset($_POST['profit_name']) ? trim($_POST['profit_name']) : '';
            $profit_amount = isset($_POST['profit_amount']) ? trim($_POST['profit_amount']) : '';
            $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : '';
            if($profit_name == '') $error_msg = '请填写名称';
            if($error_msg == ''){
                $inserData = [
                    'profit_name' => $profit_name,
                    'profit_amount' => $profit_amount,
                    'parent_id' => $parent_id,
                ];
                if($id > 0){
                    $bool = $wpdb->update($wpdb->prefix.'spread_set',$inserData,['id' => $id]);
                }else{
                    $bool = $wpdb->insert($wpdb->prefix.'spread_set',$inserData);
                }
                if($bool){
                    $success_msg = '操作成功!';
                }else{
                    $error_msg = '操作失败!';
                }
            }
        }
        if($id > 0){
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}spread_set WHERE id='{$id}'", ARRAY_A);
        }
        $parentList = $wpdb->get_results("SELECT id,profit_name FROM {$wpdb->prefix}spread_set WHERE parent_id=0", ARRAY_A);
        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加/编辑分成项</h1>

            <div id="ajax-response">
                <span style="color: #2bc422"><?=$success_msg?></span>
                <span style="color: #c44e00"><?=$error_msg?></span>
            </div>

            <form method="post" action="" id="adduser" class="validate" novalidate="novalidate">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="5f6ea9ff44"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php"><table class="form-table">
                    <tbody>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="profit_name">收益名称 </label></th>
                        <td>
                            <input name="profit_name" type="text" id="profit_name" value="<?=isset($row)?$row['profit_name']:''?>" maxlength="60">
                        </td>
                    </tr>

                    <tr class="form-field form-required">
                        <th scope="row"><label for="profit_amount">收益金额/分成百分比 </label></th>
                        <td>
                            <input type="text" name="profit_amount" value="<?=isset($row)?$row['profit_amount']:''?>" id="profit_amount" maxlength="60">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="parent_id">上级 </label></th>
                        <td>
                            <select name="parent_id" id="parent_id">
                                <option value="0">无上级</option>
                                <?php foreach ($parentList as $plv){ ?>
                                    <option <?=isset($row) && $row['parent_id'] == $plv['id']?'selected="selected"':''?> value="<?=$plv['id']?>"><?=$plv['profit_name']?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                    </tbody>
                </table>

                <p class="submit"><input type="submit" class="button button-primary" value="提交"></p>
            </form>
        </div>
        <?php
    }

    /**
     * 收益记录
     */
    public function profitLog(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';

        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $where = "WHERE 1=1";
        if($searchStr != ''){
            $where .= " AND (p.post_title LIKE '%{$searchStr}%' OR um.meta_value LIKE '%{$searchStr}%')";
        }
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS 
                il.order_type,il.match_id,p.post_title,il.referee_income,il.indirect_referee_income,il.person_liable_income,il.sponsor_income,il.manager_income,
                il.user_id,il.referee_id,il.indirect_referee_id,il.person_liable_id,il.sponsor_id,il.manager_id,il.income_status,il.id,
                um.meta_value AS user_real_name,  
                um2.meta_value AS referee_real_name,  
                um3.meta_value AS indirect_referee_real_name,  
                um4.meta_value AS person_liable_real_name,  
                um5.meta_value AS sponsor_real_name,  
                um6.meta_value AS manager_real_name 
                FROM {$wpdb->prefix}income_logs AS il 
                LEFT JOIN `{$wpdb->usermeta}` AS um ON um.user_id=il.user_id AND um.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->usermeta}` AS um2 ON um2.user_id=il.referee_id AND um2.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->usermeta}` AS um3 ON um3.user_id=il.indirect_referee_id AND um3.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->usermeta}` AS um4 ON um4.user_id=il.person_liable_id AND um4.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->usermeta}` AS um5 ON um5.user_id=il.sponsor_id AND um5.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->usermeta}` AS um6 ON um6.user_id=il.manager_id AND um6.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->posts}` AS p ON p.ID=il.match_id 
                {$where} 
                LIMIT {$start},{$pageSize}",ARRAY_A);
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
            <h1 class="wp-heading-inline">主体列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize')?>" class="page-title-action">添加主体</a>

            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤主体列表</h2>



            <p class="search-box">
                <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                <input type="search" id="search_val" name="search_val" placeholder="付款人/项目" value="<?=$searchStr?>">
                <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=fission-profit-log&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
            </p>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
                        <option value="agree">通过申请</option>
                        <option value="refuse">拒绝申请</option>
                        <option value="frozen">冻结</option>
                        <option value="thaw">解冻</option>
                    </select>
                    <input type="button" id="doaction" class="button action all_options" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">主体列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th scope="col" id="real_name" class="manage-column column-real_name column-primary">付款人</th>
                    <th scope="col" id="project" class="manage-column column-project">付款项目</th>
                    <th scope="col" id="referee" class="manage-column column-referee">直接推广</th>
                    <th scope="col" id="indirect_referee" class="manage-column column-indirect_referee">间接推广</th>
                    <th scope="col" id="person_liable" class="manage-column column-person_liable">负责人</th>
                    <th scope="col" id="sponsor" class="manage-column column-sponsor">主办方</th>
                    <th scope="col" id="manager" class="manage-column column-manager">事业员</th>
                    <th scope="col" id="income_status" class="manage-column column-income_status">状态</th>
                    <th scope="col" id="options1" class="manage-column column-options1">操作</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){
                    if(empty($row['user_real_name'])){
                        $real_name = get_user_by('ID',$row['user_id'])->user_login;
                    }else{
                        $real_name = unserialize($row['user_real_name'])['real_name'];
                    }
                    ?>
                    <tr data-uid="<?=$row['id']?>">
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="cb-select-407">选择<?=$real_name?></label>
                            <input id="cb-select-<?=$row['id']?>" type="checkbox" name="post[]" value="<?=$row['id']?>">
                            <div class="locked-indicator">
                                <span class="locked-indicator-icon" aria-hidden="true"></span>
                                <span class="screen-reader-text">“<?=$real_name?>”已被锁定</span>
                            </div>
                        </th>
                        <td class="real_name column-real_name has-row-actions column-primary" data-colname="付款人">
                            <?=$real_name?>
                            <br>
                            <div class="row-actions">
<!--                                <span class="edit"><a href="">编辑</a></span>-->
                                <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                                <!--                               <span class="view"><a href="">资料</a></span>-->
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="project column-project" data-colname="直接推广">
                            <?=$row['post_title']?>

                        </td>
                        <td class="referee column-referee" data-colname="直接推广">
                            <?=!empty($row['referee_real_name'])?unserialize($row['referee_real_name'])['real_name']:get_user_by('ID',$row['referee_id'])->user_login?>
                            (<?=$row['referee_income']?>)
                        </td>
                        <td class="indirect_referee column-indirect_referee" data-colname="间接推广">
                            <?=!empty($row['indirect_referee_name'])?unserialize($row['indirect_referee_name'])['real_name']:get_user_by('ID',$row['indirect_referee_id'])->user_login?>
                            <?=$row['indirect_referee_income']>0?'('.$row['indirect_referee_income'].')':''?>
                        </td>
                        <td class="person_liable column-person_liable" data-colname="负责人">
                            <?=!empty($row['person_liable_name'])?unserialize($row['person_liable_name'])['real_name']:get_user_by('ID',$row['person_liable_id'])->user_login?>
                            <?=$row['person_liable_income']>0?'('.$row['person_liable_income'].')':''?>
                        </td>
                        <td class="sponsor column-sponsor" data-colname="主办方">
                            <?=!empty($row['sponsor_name'])?unserialize($row['sponsor_name'])['real_name']:get_user_by('ID',$row['sponsor_id'])->user_login?>
                            <?=$row['sponsor_income']>0?'('.$row['sponsor_income'].')':''?>
                        </td>
                        <td class="manager column-manager" data-colname="事业员">
                            <?=!empty($row['manager_name'])?unserialize($row['manager_name'])['real_name']:get_user_by('ID',$row['manager_id'])->user_login?>
                            <?=$row['manager_income']>0?'('.$row['manager_income'].')':''?>
                        </td>
                        <td class="income_status column-income_status" data-colname="状态" id="cardImg-<?=$row['user_id']?>">
                            <?=$row['income_status'] == '1'?'待确认':'已确认'?>
                        </td>
                        <td class="options1 column-options1" data-colname="操作">

                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-real_name column-primary">付款人</th>
                    <th scope="col" class="manage-column column-project">付款项目</th>
                    <th scope="col" class="manage-column column-referee">直接推广</th>
                    <th scope="col" class="manage-column column-indirect_referee">间接推广</th>
                    <th scope="col" class="manage-column column-person_liable">负责人</th>
                    <th scope="col" class="manage-column column-sponsor">主办方</th>
                    <th scope="col" class="manage-column column-manager">事业员</th>
                    <th scope="col" class="manage-column column-income_status">状态</th>
                    <th scope="col" class="manage-column column-options1">操作</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
                        <option value="agree">通过申请</option>
                        <option value="refuse">拒绝申请</option>
                        <option value="frozen">冻结</option>
                        <option value="thaw">解冻</option>
                    </select>
                    <input type="button" id="doaction2" class="button action all_options" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>

            <br class="clear">
            <script>
                jQuery(document).ready(function($) {

                });
            </script>
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
new Spread();