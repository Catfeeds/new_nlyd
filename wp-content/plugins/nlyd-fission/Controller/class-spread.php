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
        }
        add_submenu_page('fission','收益设置','收益设置','profit_set','fission-profit-set',array($this,'profitSet'));
        add_submenu_page('fission','新增收益设置','新增收益设置','add_profit_set','fission-add-profit-set',array($this,'addProfitSet'));
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

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
                        <option value="delete">删除</option>
                    </select>
                    <input type="submit" id="doaction" class="button action" value="应用">
                </div>


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

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
                        <option value="delete">删除</option>
                    </select>
                    <input type="submit" id="doaction2" class="button action" value="应用">
                </div>

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