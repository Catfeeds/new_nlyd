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

    }


    /**
     * 新增收益设置
     */
    public function addProfitSet(){
        global $wpdb;
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
                $bool = $wpdb->insert($wpdb->prefix.'spread_set',$inserData);
                if($bool){
                    $success_msg = '添加成功!';
                }else{
                    $error_msg = '添加失败!';
                }
            }
        }
        $parentList = $wpdb->get_results("SELECT id,profit_name FROM {$wpdb->prefix}spread_set WHERE parent_id=0", ARRAY_A);
        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加/编辑主体类型</h1>

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
                            <input name="profit_name" type="text" id="profit_name" value="" maxlength="60">
                        </td>
                    </tr>

                    <tr class="form-field form-required">
                        <th scope="row"><label for="profit_amount">收益金额/分成百分比 </label></th>
                        <td>
                            <input type="text" name="profit_amount" id="profit_amount" maxlength="60">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="parent_id">上级 </label></th>
                        <td>
                            <select name="parent_id" id="parent_id">
                                <option value="0">无上级</option>
                                <?php foreach ($parentList as $plv){ ?>
                                    <option value="<?=$plv['id']?>"><?=$plv['profit_name']?></option>
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