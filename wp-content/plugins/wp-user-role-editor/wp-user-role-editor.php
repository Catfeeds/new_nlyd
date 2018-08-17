<?php
/**
 * @package celerity-login
 */
/*
Plugin Name: ggbx-user-role 简单权限编辑
Plugin URI: https://baidu.com/
Description: 最简洁的权限列表
Version: 0.1
Author: ggbx
Author URI: https://baidu.com
License:
Text Domain:
*/
define( 'WPGGbx_Role_PATH', plugin_dir_path( __FILE__ ) );


if ( ! class_exists( 'WP_User_Role_Editor' ) ) {

    class WP_User_Role_Editor {


        public function __construct() {



            add_action('admin_menu',array($this,'add_role_menu'));

            add_action('admin_enqueue_scripts',array($this,'my_enqueue_options_style'));
            //add_action('admin_menu',array($this,'add_role_menu_role'));
            add_action('init',array($this,'add_wp_roles'));

            include(WPGGbx_Role_PATH . 'includes/class-ggbx-form-ajax.php');//加载ajax
        }

        function my_enqueue_options_style() {

            $screen       = get_current_screen();
            $screen_id    = $screen ? $screen->id : '';

            if ( in_array( $screen_id, array('toplevel_page_ggbx_role','toplevel_page_role_add') ) ) {

                wp_register_script( 'wp-role-jquery-ui-min',plugins_url('/templates/_inc/jqeury-ui/jqeury-ui.js', __FILE__),array() );
                wp_enqueue_script( 'wp-role-jquery-ui-min' );

                wp_register_script( 'wp-role-alerts',plugins_url('/templates/_inc/alerts.js', __FILE__),array()  );
                wp_enqueue_script( 'wp-role-alerts' );

                wp_register_style( 'wp-role',plugins_url('/templates/_inc/wp-role.css', __FILE__),array()  );
                wp_enqueue_style( 'wp-role' );

                wp_register_style( 'wp-role-jquery-ui-min',plugins_url('/templates/_inc/jqeury-ui/jquery-ui.min.css', __FILE__),array()  );
                wp_enqueue_style( 'wp-role-jquery-ui-min' );

             }
        }

        function add_wp_roles(){

            if ( !current_user_can( 'ggbx_role' ) ) {

                global $wp_roles;

                $role = 'ggbx_role'; // 权限名
                $wp_roles->add_cap('administrator', $role); // 为管理员添加编辑商品权限
                //$GLOBALS['role_list'][$role] = '角色管理';
                //
                $role = 'ggbx_role_add'; // 权限名
                $wp_roles->add_cap('administrator', $role); // 为管理员添加编辑商品权限
                //$GLOBALS['role_list'][$role] = '角色管理';


            }


        }

        function add_role_menu()
        {

            add_menu_page(__('角色权限'), __('角色权限'), 'ggbx_role', 'ggbx_role', array($this,'menu_role'),'dashicons-share1');
            add_submenu_page('ggbx_role', '新增角色', '新增角色', 'ggbx_role_add', 'ggbx_role_add', array($this,'menu_role_add'));

        }

        /**
         * 角色列表
         */
        function menu_role()
        {
            echo '<h1>已有角色</h1>';

            global $wp_roles;

            $wp_role_list = $wp_roles->roles['administrator']['capabilities'];

            $role = $wp_roles->get_names();
            unset($role['administrator']);

            ?>
            <div id="accordion">
                <?php
                if (empty($role)) {
                    exit('<h1>当前没有可用的角色</h1>');
                }
                foreach ($role as $key => $value) {
                    ?>
                    <h3><?=$value;?><span data-name="<?=$value;?>" data-role="<?=$key;?>" class="delect-role">×</span></h3>
                    <div>
                        <p>
                            <?php
                            foreach ($wp_role_list as $k => $v) {

                                ?>
                                <!--<span> <b><?/*=$v;*/?></b>-->
                                <span><b><?=$k;?><?=$k=='read'? '可登录后台':''?></b>
                        <input data-name="<?=$wp_roles->roles[$key]['capabilities'][$k]?>" type="checkbox"   data-role="<?=$key;?>" name="check_role" <?=$wp_roles->roles[$key]['capabilities'][$k] ? 'checked="checked"' : '';?> value="<?=$k;?>" /></span>
                                <?php
                            }
                            ?>
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>

            <script>
                jQuery(document).ready(function($) {
                    //手风琴

                   var icons = {
                        header: "ui-icon-circle-arrow-e",
                        activeHeader: "ui-icon-circle-arrow-s"
                    };
                    $( "#accordion" ).accordion({
                        icons: icons
                    });


                    //点击添加权限
                    $('input[name=check_role]').click(function(){
                        var _this = $(this);
                        var flag = _this.is(':checked');
                        if(flag){
                            var check = 'y';

                        }else{
                            var check = 'n';

                        }
                        var role = _this.attr('data-role');
                        var check_role = $(this).val();
                        $.ajax({
                            type: "POST",
                            url: ajaxurl,
                            data:{'action':'role_jurisdiction_settings','role':role,'check_role':check_role,'check':check},
                            dataType: "json",
                            success: function(data){
                                if(data.flag == 'y'){
                                    $.alerts(data.info);

                                }else if(data.flag == 'n'){
                                    $.alerts(data.info);
                                    _this.attr("checked",false);

                                }
                            }
                        });
                    });

                    //点击删除角色
                    $('.delect-role').click(function(){
                        var _this = $(this);
                        var role_name = _this.attr('data-role');
                        var role_text = _this.attr('data-name');

                        if(!confirm("确认要删除 "+role_text+" 这个角色吗？")){
                            return false;
                        }

                        $.ajax({
                            type: "POST",
                            url: ajaxurl,
                            data:{'action':'role_delete','role_name':role_name},
                            dataType: "json",
                            success: function(data){
                                if(data.flag == 'y'){
                                    $.alerts(data.info);
                                    history.go(0);
                                }else if(data.flag == 'n'){
                                    $.alerts(data.info);

                                }
                            }
                        });

                        return false;
                    });
                });
            </script>

            <?php
        }

        function menu_role_add()
        {
            echo '<h1>新增角色</h1>';

            global $wp_roles;

            $role = $wp_roles->roles['administrator']['capabilities'];

            ?>
            <style>
                #role_form {
                    background: #fff;
                    padding: 10px;
                }

                #role_form p {
                    margin: 0;
                }
                .role_add {
                    border-bottom: 1px solid #ccc;
                }
                .role_add span {
                    display: inline-block;width: 100%;padding: 5px 0;
                }
                .role_adda_qx {
                    width: 70px;min-height: 100px;display: inline-block;float: left;font-weight: bold;padding-top: 5px;
                }
                .ckssa {
                    width: 100%;display: inline-block;margin-top: 50px;text-align: center;
                }
                .role_adda_right {
                    display: inline-block;width: 90%;float: left;
                }
                .role_adda {
                    padding-top: 30px;border: normal;
                }
                .role_adda_right b {
                    width: 500px;display: inline-block;font-weight: normal;position: relative;text-indent: 30px;padding: 5px 0;
                }
                .role_adda_right b input {
                    position: absolute;left: 0;
                }
                #ckss {
                    width: 100px;height: 40px;background: #0073AA;border: none;color: #Fff;border-radius: 5px;cursor: pointer;
                }
            </style>
            <form id="role_form">
                <input type="hidden" name="action" value="role_add" />
                <p class="role_add">
                <span> <b>角色名称：</b> <input type="text" name="role"
                                           placeholder="角色名，如：admin" />
                </span> <span><b>角色昵称：</b> <input type="text" name="display_name"
                                                  placeholder="角色昵称，如：管理员" /> </span>
                </p>
                <p class="role_add">
                    <span>全选&nbsp;<input id="all" type="checkbox" style="margin-top: -3px;"/></span>
                </p>
                <p class="role_adda" id="list">
                    <span class="role_adda_qx">应用权限：</span> <span class="role_adda_right">
            <?php
            foreach ($role as $key => $value) {
                $flag = '';
                if($key=='read'){
                    //$flag = 'id="read_ck" checked="checked" onclick="return false;"';
                    $flag = '&nbsp;选中可登录后台';
                }

                echo '<b>' . $key . $flag .'<input type="checkbox" '. $flag . '  name="role_s[]" value="' . $key . '"/></b>';
            }
            ?>
                </span> <span class="ckssa"><input type="button"  checked="checked" id="ckss" value="提交" /></span>
                </p>
            </form>
            <script>
                jQuery(document).ready(function($) {
                    $('#ckss').click(function(){
                        $.ajax({
                            type: "POST",
                            url: ajaxurl,
                            data: $('#role_form').serialize(),
                            dataType: "json",
                            success: function(data){
                                if(data.flag == 'y'){
                                    alert(data.info);
                                    window.location.href ='<?= admin_url('admin.php?page=ggbx_role')?>';
                                }else if(data.flag == 'n'){
                                    alert(data.info);

                                }
                            }
                        });

                    });

                    //全选或全不选
                    $("#all").click(function(){
                        if(this.checked){
                            $("#list :checkbox").prop("checked", true);
                        }else{
                            $("#list :checkbox").prop("checked", false);
                            $("#read_ck").prop("checked", true);
                        }
                    });
                });

            </script>
            <?php
        }


    }

    // 实例化
    new WP_User_Role_Editor();

}
