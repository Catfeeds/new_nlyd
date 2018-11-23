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

            $role = 'spread_money';//权限名
            $wp_roles->add_cap('administrator', $role);
        }

        add_menu_page('推广', '推广', 'spread', 'spread',array($this,'spreadSetting'),'dashicons-businessman',102);
        add_submenu_page('spread','推广金额设置','推广金额设置','spread_money','spread-spread_money',array($this,'spreadMoneySetting'));
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

    /**
     * 推广金额设置
     */
    public function spreadMoneySetting(){
        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}spread_money_set`", ARRAY_A)
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">推广金额设置</h1>

            <hr class="wp-header-end">

                    <br class="clear">
                <button class="button" type="button" id="add-money-set">添加</button>
<!--                <div class="tr-" style="display: none">-->

<!--                </div>-->
                </div>
                <h2 class="screen-reader-text">推广金额</h2><table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <th scope="col" id="money_name" class="manage-column column-money_name column-primary sortable">
                               名称
                        </th>
                        <th scope="col" id="project" class="manage-column column-project">项目</th>
                        <th scope="col" id="user_type" class="manage-column column-user_type">收益体</th>
                        <th scope="col" id="money" class="manage-column column-money">金额</th>
                        <th scope="col" id="is_enable" class="manage-column column-is_enable">状态</th>
                        <th scope="col" id="options" class="manage-column column-options">操作</th>
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">

                    <tr style="display: none;">
                        <td class="money_name column-money_name has-row-actions column-primary" data-colname="名称">
                            <input type="text" name="money_name">
                            <br>
<!--                            <div class="row-actions">-->
<!--                                <span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/user-edit.php?user_id=16&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">编辑</a> | </span>-->
<!--                                <span class="delete"><a class="submitdelete" href="users.php?action=delete&amp;user=16&amp;_wpnonce=e1d5f660de">删除</a> | </span>-->
<!--                                <span class="view"><a href="http://127.0.0.1/nlyd/author/1115996550qq-com/" aria-label="阅读1115996550@qq.com, 1115996550@qq.com的文章">查看</a></span>-->
<!--                            </div>-->
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="project column-project" data-colname="项目">
                            <select name="project" id="">
                                <?php foreach ($this->getMoneyProject() as $mpv){ ?>
                                    <option value="<?=$mpv['key']?>"><?=$mpv['name']?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td class="user_type column-user_type" data-colname="收益体">
                            <select name="user_type" id="">
                                <?php foreach ($this->getMoneyUserType() as $mutv){ ?>
                                    <option value="<?=$mutv['key']?>"><?=$mutv['name']?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td class="money column-money" data-colname="金额">
                            <input type="text" name="money">
                        </td>
                        <td class="is_enable column-is_enable" data-colname="状态">
                            <input type="radio" name="is_enable_-1" checked="checked" value="1">开启
                            <input type="radio" name="is_enable_-1" value="2">关闭
                        </td>
                        <td class="options column-options" data-colname="操作">
                            <button type="button" class="button enterUpdate" data-id="-1">确认修改</button>
                        </td>
                    </tr>
                    <?php foreach ($rows as $row){ ?>
                        <tr>
                            <td class="money_name column-money_name has-row-actions column-primary" data-colname="名称">
                                <input type="text" name="money_name" value="<?=$row['money_name']?>">
                                <br>
                                <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                            </td>
                            <td class="project column-project" data-colname="项目">
                                <select name="project" id="">
                                    <?php foreach ($this->getMoneyProject() as $mpv){ ?>
                                        <option <?=$row['project_type']==$mpv['key']?'selected="selected"':''?> value="<?=$mpv['key']?>"><?=$mpv['name']?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td class="user_type column-user_type" data-colname="收益体">
                                <select name="user_type" id="">
                                    <?php foreach ($this->getMoneyUserType() as $mutv){ ?>
                                        <option <?=$row['user_type']==$mutv['key']?'selected="selected"':''?> value="<?=$mutv['key']?>"><?=$mutv['name']?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td class="money column-money" data-colname="金额">
                                <input type="text" name="money" value="<?=$row['money']?>">
                            </td>
                            <td class="is_enable column-is_enable" data-colname="状态">
                                <input type="radio" name="is_enable_<?=$row['id']?>" <?=$row['is_enable']==1?'checked="checked"':''?> value="1">开启
                                <input type="radio" name="is_enable_<?=$row['id']?>" <?=$row['is_enable']==2?'checked="checked"':''?> value="2">关闭
                            </td>
                            <td class="options column-options" data-colname="操作">
                                <button type="button" class="button enterUpdate" data-id="<?=$row['id']?>">确认修改</button>
                            </td>
                        </tr>
                    <?php } ?>
                    <tfoot>
                    <tr>
                        <th scope="col" class="manage-column column-money_name column-primary sortable">
                            名称
                        </th>
                        <th scope="col" class="manage-column column-project">项目</th>
                        <th scope="col" class="manage-column column-user_type">收益体</th>
                        <th scope="col" class="manage-column column-money">金额</th>
                        <th scope="col" class="manage-column column-is_enable">状态</th>
                        <th scope="col" class="manage-column column-options">操作</th>
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">



                    <br class="clear">
                </div>
                <script>
                    jQuery(document).ready(function($) {
                        $('#add-money-set').on('click',function () {
                            var _html = $('#the-list').find('tr').first().clone(true);
                            _html.css('display','table-row')
                            $('#the-list').append(_html);
                        });
                        $('#the-list').on('click','.enterUpdate',function () {
                            var _data = {},_tr = $(this).closest('tr');
                            _data.id = $(this).attr('data-id');
                            _data.name = _tr.find('input[name="money_name"]').val();
                            _data.project = _tr.find('select[name="project"]').val();
                            _data.user_type = _tr.find('select[name="user_type"]').val();
                            _data.money = _tr.find('input[name="money"]').val();
                            _data.is_enable = _tr.find('input[name="is_enable_'+_data.id+'"]:checked').val();
                            _data.action = 'updateSpreadMoneySet';
                            $.ajax({
                                url : ajaxurl,
                                data : _data,
                                type : 'post',
                                dataType : 'json',
                                success : function (response) {
                                    alert(response.data.info);
                                    if(response['success']){
                                        window.location.reload();
                                    }
                                }, error : function () {
                                    alert('请求失败!')
                                }
                            });
                        });
                    })
                </script>
            <br class="clear">
        </div>
        <?php
    }
    /**
     * 分钱项目
     */
    public function getMoneyProject(){
        return [
            ['key'=>1,'name'=>'考级报名费'],
            ['key'=>2,'name'=>'战队赛报名费'],
            ['key'=>3,'name'=>'城市赛报名费'],
            ['key'=>4,'name'=>'购买商品'],
        ];
    }
    /**
     * 获取收益主体
     */
    public function getMoneyUserType(){
        return [
            ['key'=>1,'name'=>'分中心'],
            ['key'=>2,'name'=>'教练'],
            ['key'=>3,'name'=>'用户'],
            ['key'=>4,'name'=>'战队'],
        ];
    }

}
new Setting();