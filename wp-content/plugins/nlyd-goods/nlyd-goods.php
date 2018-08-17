<?php
/*
Plugin Name: Application Goods 项目后端
Plugin URI: http://localhost/wordpress/
Description: 前端功能集合
Version: 1.0
Author: leo
Author URI: --
*/


//判断插件是否启用
if(!class_exists('GoodsController')){

    class GoodsController{
        public function __construct()
        {
            $this->main();
        }

        public function main(){

            add_action('admin_menu',array($this,'add_submenu'));

            add_action('admin_enqueue_scripts', array($this,'scripts_default'));
//
//            add_action( 'wp_ajax_saveInterface',array($this,'saveInterface'));
//            add_action( 'wp_ajax_saveLogo',array($this,'saveLogo'));
//            add_action( 'wp_ajax_saveBanner',array($this,'saveBanner'));
        }

        public function add_submenu(){
            add_menu_page('商品', '商品', 'administrator', 'goods',array($this,'goodsLists'),'dashicons-businessman',99);
            add_submenu_page( 'goods', '添加商品', '添加商品', 'administrator', 'goods-add', array($this,'addGoods') );
//            add_submenu_page( 'options-general.php', 'logo设置', 'logo设置', 'manage_options', 'logo', array($this,'my_submenu_page_logo') );
//            add_submenu_page( 'options-general.php', 'banner设置', 'banner设置', 'manage_options', 'banner', array($this,'my_submenu_page_banner') );

        }

        /**
         * 商品列表
         */
        public function goodsLists(){

        }

        /**
         * 新增商品
         */
        public function addGoods(){
            ?>
            <div id="wpbody-content" aria-label="主内容" tabindex="0">
                <div id="screen-meta" class="metabox-prefs">


                </div>


                <div class="wrap">
                    <h1>添加商品</h1>

                        <form method="post" action="options.php" novalidate="novalidate">
                            <table class="form-table">

                                <tbody><tr>
                                    <th scope="row"><label for="goods_title">商品名称</label></th>
                                    <td><input name="goods_title" type="text" id="goods_title" value="" class="goods_title-text"></td>
                                </tr>

                                <tr>
                                    <th scope="row"><label for="goods_intro">商品简介</label></th>
                                    <td><input name="goods_intro" type="text" id="goods_intro" aria-describedby="goods_intro-description" value="" class="regular-text">
                                        <p class="description" id="goods_intro-description">用简洁的文字描述商品。</p></td>
                                </tr>

                                <tr>
                                    <th scope="row"><label for="goods_price">商品价格</label></th>
                                    <td><input name="goods_price" type="text" id="goods_price" aria-describedby="goods_price-description" value="" class="goods_price-text">
                                        <p class="description" id="goods_price-description">默认0.00</p></td>
                                </tr>

                                <tr>
                                    <th scope="row"><label for="goods_brain">使用脑币</label></th>
                                    <td><input name="goods_brain" type="text" id="goods_brain" aria-describedby="goods_brain-description" value="" class="goods_brain-text">
    <!--                                    <p class="description" id="goods_brain-description">需要</p></td>-->
                                </tr>

                                <tr>
                                    <th scope="row"><label for="goods_stock">商品库存</label></th>
                                    <td><input name="goods_stock" type="text" id="goods_stock" aria-describedby="goods_stock-description" value="" class="goods_stock-text">
    <!--                                    <p class="description" id="goods_brain-description">需要</p></td>-->
                                </tr>

                                <tr>
                                    <th scope="row"><label for="goods_sales">商品销量</label></th>
                                    <td><input name="goods_sales" type="text" id="goods_sales" aria-describedby="goods_sales-description" value="" class="goods_sales-text">
    <!--                                    <p class="description" id="goods_brain-description">需要</p></td>-->
                                </tr>

                                </tbody>
                            </table>
                        </form>
                    <style type="text/css">
                        .logoImg{
                            width: 20em;
                        }
                    </style>
                        <form name="interface" id="interface" class="validate interface" novalidate="novalidate">

                            <input name="action" type="hidden" value="saveBanner">
                            banner上传:
                            <div id="pro-box">
                                <p>
                                    <input type="text" size="60" value="http://127.0.0.1/new_nlyd/wp-content/uploads/2018/07/leo.jpg" name="index_banner_url[]" class="upload_input">
                                    <img src="http://127.0.0.1/new_nlyd/wp-content/uploads/2018/07/leo.jpg" class="logoImg">
                                    <a class="upload_button button" href="#">上传</a>
                                    <a class="del_button button" href="#">删除</a>
                                </p>
                                <p>
                                    <input type="text" size="60" value="http://127.0.0.1/new_nlyd/wp-content/uploads/2018/08/0117e2571b8b246ac72538120dd8a4.jpg@1280w_1l_2o_100sh.jpg" name="index_banner_url[]" class="upload_input">
                                    <img src="http://127.0.0.1/new_nlyd/wp-content/uploads/2018/08/0117e2571b8b246ac72538120dd8a4.jpg@1280w_1l_2o_100sh.jpg" class="logoImg">
                                    <a class="upload_button button" href="#">上传</a>
                                    <a class="del_button button" href="#">删除</a>
                                </p>

                            </div>
                            <!--        <p>-->
                            <!--            <input type="text" size="60" value="" name="index_banner_url[]" class="upload_input"/>-->
                            <!--            <img src="--><!--?//=!empty($logo_url) ? $logo_url : '';?--><!--" class="logoImg">-->
                            <!--            <a class="upload_button button" href="#">上传</a>-->
                            <!--        </p>-->


                            <p class="submit">
                                <input type="submit" id="interfaceSub" class="button button-primary" value="提交">
                                <input type="button" id="add-banner" class="button button-primary" value="添加">
                            </p>
                        </form>

                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="保存更改"></p></form>

                </div>


                <div class="clear">

                </div>
            </div>
            <script>
                jQuery(document).ready(function($){
                    var upload_frame;
                    var value_id;
                    jQuery('.upload_button').live('click',function(event){
                        value_id =jQuery( this ).attr('id');

                        var _p = jQuery(this).closest('p');
                        event.preventDefault();
                        // if( upload_frame ){
                        //     upload_frame.open();
                        //     return;
                        // }
                        upload_frame = wp.media({
                            title: 'banner 上传',
                            button: {
                                text: '提交',
                            },
                            multiple: false
                        });
                        console.log(upload_frame);
                        upload_frame.on('select',function(){
                            attachment = upload_frame.state().get('selection').first().toJSON();
                            _p.find('.upload_input').val(attachment.url);
                            _p.find('.logoImg').attr('src',attachment.url);
                            upload_frame.remove();
                        });
                        upload_frame.open();
                    });

                    jQuery('#interfaceSub').live('click',function(event){
                        var query = $('#interface').serialize();
                        $.post(ajaxurl,query,function (data) {
                            alert(data.data);
                        },'json')
                        return false;
                    });

                    $('.del_button').on('click', function () {
                        $(this).closest('p').remove();
                    });

                    $('#add-banner').on('click', function () {
                        var _p = $('#template').find('p').clone(true);
                        // $(_p).show();
                        $('#pro-box').append(_p);
                    });



                });
            </script>

            <?php
        }

        /**
         * 首页banner设置
         */
        public function my_submenu_page_banner(){
            require_once( leo_user_interface_path . 'view/banner.php' );
        }

        /**
         * logo设置
         */
        public function my_submenu_page_logo(){
            $logo_url = get_option('logo_url');
            require_once( leo_user_interface_path . 'view/logo.php' );
        }

        /**
         * 页面以及数据处理
         */
        public function my_submenu_page_display(){

            $interface_config = get_option('interface_config');

            $config = $this->getConfig();

            require_once( leo_user_interface_path . 'view/view.php' );
        }

        public function getConfig(){

            return array(
                'smtp'=>array(
                    'title'=>'邮件',
                    'tag'=>array(
                        array(
                            'title'=>'SMTP服务器',
                            'name'=>'host',
                            'placeholder'=>'邮件发送所需SMTP服务器',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'SMTP服务器端口',
                            'name'=>'port',
                            'placeholder'=>'邮件发送所需SMTP服务器端口',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'SMTP服务器用户名',
                            'name'=>'user_name',
                            'placeholder'=>'邮件发送所需SMTP服务器帐号',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'SMTP服务器密码',
                            'name'=>'user_pass',
                            'placeholder'=>'邮件发送所需SMTP服务器密码',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'SMTP服务授权码',
                            'name'=>'user_warrant',
                            'placeholder'=>'邮件发送所需SMTP服务授权码',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'发件人email',
                            'name'=>'from_email',
                            'placeholder'=>'一般设置为服务器用户名',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'发件人名称',
                            'name'=>'from_name',
                            'placeholder'=>'邮件发送所需发件人名称',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'回复人email',
                            'name'=>'reply_email',
                            'placeholder'=>'留空则为发件人email',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'回复人名称',
                            'name'=>'reply_name',
                            'placeholder'=>'留空则为发件人名称',
                            'type'=>'input'
                        ),
                    )
                ),
                'sms'=>array(
                    'title'=>'短信',
                    'tag'=>array(
                        array(
                            'title'=>'短信网关',
                            'name'=>'host',
                            'placeholder'=>'发送短信所需网关url接口',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'短信KeyId',
                            'name'=>'key',
                            'placeholder'=>'发送短信所需API_KEY',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'短信KeySecret',
                            'name'=>'secret',
                            'placeholder'=>'发送短信所需API_Secret',
                            'type'=>'input'
                        ),
                    )
                ),
                'zfb'=>array(
                    'title'=>'支付宝',
                    'tag'=>array(
                        array(
                            'title'=>'支付宝网关',
                            'name'=>'host',
                            'placeholder'=>'支付宝调用所需url接口',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'支付宝APP_ID',
                            'name'=>'api',
                            'placeholder'=>'支付宝调用所需APP_ID',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'支付宝支付密钥',
                            'name'=>'secret_key',
                            'placeholder'=>'支付宝调用所需微信支付密钥',
                            'type'=>'input'
                        ),
                    )
                ),
                'wx'=>array(
                    'title'=>'微信',
                    'tag'=>array(
                        array(
                            'title'=>'微信网关',
                            'name'=>'host',
                            'placeholder'=>'微信调用所需url接口',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'微信APP_ID',
                            'name'=>'api',
                            'placeholder'=>'微信调用所需APP_ID',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'微信AppSecret',
                            'name'=>'secret',
                            'placeholder'=>'AppSecret(应用密钥)',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'微信支付商户号',
                            'name'=>'merchant',
                            'placeholder'=>'微信调用所需微信支付商户号',
                            'type'=>'input'
                        ),
                        array(
                            'title'=>'微信支付密钥',
                            'name'=>'secret_key',
                            'placeholder'=>'微信调用所需微信支付密钥',
                            'type'=>'input'
                        ),

                    )
                ),
                'login'=>array(
                    'title'=>'第三方登录',
                    'tag'=>array(
                        array(
                            'title'=>'微信登录',
                            'name'=>'wx_login',
                            'placeholder'=>'控制微信登录开启/关闭',
                            'type'=>'checkbox'
                        ),
                        array(
                            'title'=>'QQ登录',
                            'name'=>'qq_login',
                            'placeholder'=>'控制QQ登录开启/关闭',
                            'type'=>'checkbox'
                        ),
                        array(
                            'title'=>'其他登录',
                            'name'=>'other_login',
                            'placeholder'=>'控制其他第三方登录开启/关闭',
                            'type'=>'checkbox'
                        ),
                    )
                ),
            );
        }

        /**
         * 引入js/css
         */
        public function scripts_default(){

            $screen       = get_current_screen();
            $screen_id    = $screen ? $screen->id : '';

            if ( in_array( $screen_id, array('settings_page_interface') ) ) {

                //js
                wp_register_script( 'interface-js',plugins_url('/js/interface.js', __FILE__),array(), leo_user_interface_version  );
                wp_enqueue_script( 'interface-js' );

                //css
                wp_register_style( 'interface-css',plugins_url('/css/interface.css', __FILE__),array(), leo_user_interface_version  );
                wp_enqueue_style( 'interface-css' );

            }

        }

        /**
         * Ajax提交
         */
        public function saveInterface(){

            if(empty($_POST['action'])) wp_send_json_error('请求方法错误');

            if(update_option( 'interface_config', $_POST['interface'] )){

                wp_send_json_success('保存成功');

            }else{

                wp_send_json_error('保存失败');
            }
        }

        public function saveLogo(){
            if(empty($_POST['action'])) wp_send_json_error('请求方法错误');
            if(update_option( 'logo_url', $_POST['logo_url'] ) || update_option( 'match_project_default', $_POST['match_project_default'] )){

                wp_send_json_success('保存成功');

            }else{

                wp_send_json_error('保存失败');
            }
        }

        public function saveBanner(){
            if(empty($_POST['action'])) wp_send_json_error('请求方法错误');
            foreach ($_POST['index_banner_url'] as $k => $v){
                if($v == false) unset($_POST['index_banner_url'][$k]);
            }
            if(update_option( 'index_banner_url', $_POST['index_banner_url'] )){

                wp_send_json_success('保存成功');

            }else{

                wp_send_json_error('保存失败');
            }
        }
    }
}
new GoodsController();