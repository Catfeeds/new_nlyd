<?php
class Setting{

    public function __construct()
    {
        //add_action( 'init', array($this,'add_wp_roles'));

        add_action( 'admin_menu', array($this,'register_teacher_menu_page') );
//        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));

    }

    public function register_teacher_menu_page(){

        if ( current_user_can( 'administrator' )) {
            global $wp_roles;

            $role = 'spread';//权限名
            $wp_roles->add_cap('administrator', $role);
        }

        add_menu_page('推广', '推广', 'spread', 'spread',array($this,'spreadSetting'),'dashicons-businessman',102);
    }

    public function spreadSetting(){
        global $wpdb;
        $msg = '';
        if(is_post()){
            $type = isset($_POST['type']) ? intval($_POST['type']) : 0;
            $is_enable = isset($_POST['is_enable']) ? intval($_POST['is_enable']) : 0;
            $where_money = isset($_POST['where_money']) ? trim($_POST['where_money']) : 0;
            if(($type !== 1 && $type !== 2) || ($is_enable !== 1 && $is_enable !== 2)){
                $msg = '<span style="color: #bf0000">参数错误</span>';
            }else{
                if($type === 1){
                    $moneyOrProportionField = 'proportion';
                    $moneyOrProportion = isset($_POST['moneyOrProportion']) ? intval($_POST['moneyOrProportion']) : 0;
                    if($moneyOrProportion >= 100) $msg = '<span style="color: #bf0000">比例不能超过99</span>';
                }else{
                    $moneyOrProportionField = 'money';
                    $moneyOrProportion = isset($_POST['moneyOrProportion']) ? trim($_POST['moneyOrProportion']) : 0;
                }
                if($msg == ''){
                    $id = $wpdb->get_var("SELECT id FROM `{$wpdb->prefix}spread_set`");
                    if($id > 0){
                        $sql = "UPDATE `{$wpdb->prefix}spread_set` SET `type`='{$type}',`{$moneyOrProportionField}`='{$moneyOrProportion}',`is_enable`='{$is_enable}',`where_money`='{$where_money}'";
                    }else{
                        $sql = "INSERT INTO `{$wpdb->prefix}spread_set` (`type`,`{$moneyOrProportionField}`,`is_enable`,`where_money`) 
                                VALUES ('{$type}','{$moneyOrProportion}','{$is_enable}','{$where_money}')";
                    }
                    $bool = $wpdb->query($sql);
                    if($bool) $msg = '<span style="color: #20a831">更新成功</span>';
                    else $msg = '<span style="color: #bf0000">更新失败</span>';
                }
            }
        }
        $row = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}spread_set`", ARRAY_A);
        ?>
        <div class="wrap" id="profile-page">
            <h1 class="wp-heading-inline">推广默认设置</h1>
            <form id="your-profile" action="" method="post" novalidate="novalidate">
                <p>
                    <input type="hidden" name="from" value="profile">
                    <input type="hidden" name="checkuser_id" value="1">
                </p>


                <h2>设置</h2>
                <div><?=$msg?></div>
                <table class="form-table">
                    <tbody>

                    <tr class="user-role-wrap">
                        <th><label for="type">抽成方式</label></th>
                        <td>
                            <select name="type" id="type">
                                <option <?php if($row['type'] == 1) echo 'selected="selected"'; ?> value="1">百分比</option>
                                <option <?php if($row['type'] == 2) echo 'selected="selected"'; ?> value="2">固定数额</option>
                            </select>
                        </td>
                    </tr>

                    <tr class="user-first-name-wrap">
                        <th><label for="moneyOrProportion">抽成率/金额</label></th>
                        <td><input type="text" name="moneyOrProportion" id="moneyOrProportion" value="<?=$row['type'] == 2 ? $row['money'] : $row['proportion']?>" class="regular-text"></td>
                    </tr>

                    <tr class="user-first-name-wrap">
                        <th><label for="where_money">条件金额</label></th>
                        <td>
                            <input type="text" name="where_money" id="where_money" value="<?=$row['where_money']?>" class="regular-text">
                            <span>消费达到此金额获得推广权限</span>
                        </td>
                    </tr>
                    <tr class="user-first-name-wrap">
                        <th><label for="lxl_level">乐学乐开放下级</label></th>
                        <td>
                            <select name="lxl_level" id="lxl_level">
                                <option value="0">无下级</option>
                                <option value="1">一级</option>
                                <option value="2">二级</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="user-first-name-wrap">
                        <th><label for="fx_level">普通分销开放下级</label></th>
                        <td>
                            <select name="fx_level" id="fx_level">
                                <option value="0">无下级</option>
                                <option value="1">一级</option>
                                <option value="2">二级</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="user-first-name-wrap">
                        <th><label for="where_money">条件金额</label></th>
                        <td>
                            <input type="text" name="where_money" id="where_money" value="<?=$row['where_money']?>" class="regular-text">
                            <span>消费达到此金额获得推广权限</span>
                        </td>
                    </tr>

                    <tr class="user-last-name-wrap">
                        <th><label for="is_enable">开关</label></th>
                        <td>
                            <input type="radio" <?php if($row['is_enable'] == 1) echo 'checked="checked"'; ?> name="is_enable" value="1" class="regular-text">开
                            <input type="radio" <?php if($row['is_enable'] == 2) echo 'checked="checked"'; ?> name="is_enable" value="2" class="regular-text">关
                        </td>
                    </tr>

                    </tbody>
                </table>

                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="更新设置"></p>
            </form>
        </div>
        <?php
    }

}
new Setting();