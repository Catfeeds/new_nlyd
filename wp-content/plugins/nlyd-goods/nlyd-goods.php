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

            define( 'leo_goods_path', plugin_dir_path( __FILE__ ) );
            define( 'leo_goods_url', plugins_url('',__FILE__ ) );
            define( 'leo_goods_version','1.0' );//样式版本
            define( 'goods_js_url', leo_goods_url.'/Public/js/' );



            add_action('admin_menu',array($this,'add_submenu'));
            add_action('admin_enqueue_scripts', array($this,'scripts_default'));
//            add_action( 'wp_ajax_saveInterface',array($this,'saveInterface'));
//            add_action( 'wp_ajax_saveLogo',array($this,'saveLogo'));
//            add_action( 'wp_ajax_saveBanner',array($this,'saveBanner'));
            add_action('admin_enqueue_scripts', array($this, 'register_scripts'));


            //引入ajax操作文件
            include_once(leo_goods_path.'/Controller/class-goods-ajax.php');
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
            global $wpdb;
            $page = ($page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1) < 1 ? 1 : $page;
            $pageSize = 20;
            $start = ($page-1)*$pageSize;
            //上下架
            $shelftype = isset($_GET['shelftype']) ? intval($_GET['shelftype']) : 0;
            switch ($shelftype){
                case 1:
                    $shelfWhere = 'shelf=1';
                    break;
                case 2:
                    $shelfWhere = 'shelf=2';
                    break;
                default:
                    $shelfWhere = '1=1';
            }
            $rows = $wpdb->get_results('
            SELECT SQL_CALC_FOUND_ROWS id,goods_title,goods_intro,price,brain,stock,sales,shelf,
            CASE shelf 
            WHEN 1 THEN "<span style=\'color:#02892E;\'>上架</span>" 
            WHEN 2 THEN "<span style=\'color:#902800;\'>下架</span>" 
            END AS shelf_name 
            FROM '.$wpdb->prefix.'goods 
            WHERE '.$shelfWhere.' 
            ORDER BY `id` DESC LIMIT '.$start.','.$pageSize, ARRAY_A);

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
                <h1 class="wp-heading-inline">商品</h1>

                <a href="?page=goods-add" class="page-title-action">添加商品</a>
                <hr class="wp-header-end">


                <form method="get">

                    <p class="search-box">
                        <label class="screen-reader-text" for="user-search-input">搜索商品:</label>
                        <input type="search" id="user-search-input" name="s" value="">
                        <input type="button" id="search-submit" class="button" value="搜索商品">
                    </p>

                    <input type="hidden" id="_wpnonce" name="_wpnonce" value="1f250f719f"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">	<div class="tablenav top">

                        <div class="alignleft actions bulkactions">
                            <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                            <select name="action" id="bulk-action-selector-top">
                                <option value="0">批量操作</option>
                                <option value="1">上架</option>
                                <option value="2">下架</option>
                            </select>
                            <input type="button" id="doaction" class="button action batch" value="应用">
                        </div>
                        <div class="alignleft actions">
                            <label class="screen-reader-text" for="new_role">将上架状态变更为…</label>
                            <select name="new_role" id="new_role">
                                <option value="0" <?php if($shelftype == 0) echo 'selected="selected"' ?>>将上架状态变更为…</option>

                                <option value="1" <?php if($shelftype == 1) echo 'selected="selected"' ?>>上架</option>
                                <option value="2" <?php if($shelftype == 2) echo 'selected="selected"' ?>>下架</option>
                            </select>
                            <input type="button" onclick="window.location.href='<?='?page=goods&shelftype='?>'+document.getElementById('new_role').value" name="changeit" id="changeit" class="button" value="更改">
                        </div>
                        <div class="tablenav-pages one-page">
                            <span class="displaying-num"><?=$count['count']?>个项目</span>
                         <?=$pageHtml?>
                        </div>
                        <br class="clear">
                    </div>


                    <h2 class="screen-reader-text">商品列表</h2><table class="wp-list-table widefat fixed striped users">
                        <thead>
                        <tr>
                            <td id="cb" class="manage-column column-cb check-column">
                                <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                                <input id="cb-select-all-1" type="checkbox">
                            </td>
                            <th scope="col" id="goods_title" class="manage-column column-goods_title column-primary sortable desc">
                                <span>商品名称</span>
                            </th>
                            <th scope="col" id="goods_shelf_name" class="manage-column column-goods_shelf_name">上架状态</th>
                            <th scope="col" id="goods_intro" class="manage-column column-goods_intro">商品简介</th>
                            <th scope="col" id="goods_price" class="manage-column column-goods_price">商品价格</th>
                            <th scope="col" id="goods_brain" class="manage-column column-goods_brain">可用脑币</th>
                            <th scope="col" id="goods_stock" class="manage-column column-goods_stock">商品库存</th>
                            <th scope="col" id="goods_sales" class="manage-column column-goods_sales">商品销量</th>

                        </tr>
                        </thead>

                        <tbody id="the-list" data-wp-lists="list:user">

                        <?php foreach ($rows as $row) { ?>

                            <tr id="" data-id="<?=$row['id']?>">
                                <th scope="row" class="check-column">
                                    <label class="screen-reader-text" for="user_5"></label>
                                    <input type="checkbox" name="users[]" id="user_5" class="subscriber check-children" value="<?=$row['id']?>">
                                </th>
                                <td class="goods_title column-goods_title has-row-actions column-primary" data-colname="商品名称">
                                    <strong><?=$row['goods_title']?></strong>
                                    <br>
                                    <div class="row-actions">
                                        <span class="edit"><a href="?page=goods-add&goodsId=<?=$row['id']?>">编辑</a> |</span>
                                        <?php if($row['shelf'] == 1){ ?>
                                            <span class="delete"><a class="lower" href="javascript:;">下架</a> </span>
                                        <?php }elseif ($row['shelf'] == 2){ ?>
                                            <span class="edit"><a href="javascript:;" class="upper" style="color: #02892E">上架</a> </span>
                                        <?php } ?>
<!--                                        <span class="view"><a href="http://127.0.0.1/nlyd/author/13982242710/" aria-label="阅读13982242710的文章">查看</a></span-->
                                    </div>
                                    <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                                </td>
                                <td class="goods_intro column-goods_shelf_name" data-colname="上架状态">
                                    <?=$row['shelf_name']?>
                                </td>
                                <td class="goods_intro column-goods_intro" data-colname="商品简介">
                                    <span aria-hidden="true"><?=$row['goods_intro']?></span>
                                    <span class="screen-reader-text">无</span>
                                </td>
                                <td class="goods_price column-goods_price" data-colname="商品价格"><?=$row['price']?></td>
                                <td class="goods_brain column-goods_brain" data-colname="可用脑币"><?=$row['brain']?></td>
                                <td class="goods_stock column-goods_stock" data-colname="商品库存"><?=$row['stock']?></td>
                                <td class="gods_sales column-gods_sales" data-colname="商品销量"><?=$row['sales']?></td>

                            </tr>
                        <?php } ?>

                        <tfoot>
                        <tr>
                            <td class="manage-column column-cb check-column">
                                <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                                <input id="cb-select-all-2" type="checkbox">
                            </td>
                            <th scope="col" class="manage-column column-goods_title column-primary sortable desc">
                                    <span>商品名称</span>
                            </th>
                            <th scope="col" class="manage-column column-goods_shelf_name">上架状态</th>
                            <th scope="col" class="manage-column column-goods_intro">商品简介</th>
                            <th scope="col" class="manage-column column-goods_price">商品价格</th>
                            <th scope="col" class="manage-column column-goods_brain">可用脑币</th>
                            <th scope="col" class="manage-column column-goods_stock">商品库存</th>
                            <th scope="col" class="manage-column column-goods_sales">商品销量</th>

                        </tr>
                        </tfoot>

                    </table>
                    <div class="tablenav bottom">

                        <div class="alignleft actions bulkactions">
                            <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                            <select name="action2" id="bulk-action-selector-bottom">
                                <option value="0">批量操作</option>
                                <option value="1">上架</option>
                                <option value="2">下架</option>
                            </select>
                            <input type="button" id="doaction2" class="button action batch" value="应用">
                        </div>
                        <div class="alignleft actions">
                            <label class="screen-reader-text" for="new_role2">将上架状态变更为…</label>
                            <select name="new_role2" id="new_role2">
                                <option value="0" <?php if($shelftype == 0) echo 'selected="selected"' ?>>将上架状态变更为…</option>

                                <option value="1" <?php if($shelftype == 1) echo 'selected="selected"' ?>>上架</option>
                                <option value="2" <?php if($shelftype == 2) echo 'selected="selected"' ?>>下架</option>
                            </select>
                            <input type="button" name="changeit2" onclick="window.location.href='<?='?page=goods&shelftype='?>'+document.getElementById('new_role2').value" id="changeit2" class="button" value="更改">		</div>
                        <div class="tablenav-pages one-page">
                            <span class="displaying-num"><?=$count['count']?>个项目</span>
                            <?=$pageHtml?>
                        </div>
                        <br class="clear">
                    </div>
                </form>

                <br class="clear">
            </div>
            <?php
        }

        /**
         * 新增商品
         */
        public function addGoods(){
            global $wpdb;
            if(is_post()){
                //去除空数组
                $imagesArr = [];
                if(is_array($_POST['goods_images'])){
                    foreach ($_POST['goods_images'] as $image){
                        if(empty($image)) continue;
                        $imagesArr[] = $image;
                    }
                }
                //插入数据
                $data = [
                  'goods_title' => trim($_POST['goods_title']),
                  'goods_intro' => trim($_POST['goods_intro']),
                  'price' => trim($_POST['goods_price']),
                  'brain' => trim($_POST['goods_brain']),
                  'stock' => trim($_POST['goods_stock']),
                  'sales' => trim($_POST['goods_sales']),
                  'images' => serialize($imagesArr),
                ];
                //是否存在id
                if(($goodsId = intval($_POST['goods_id'])) > 0){
                    $bool = $wpdb->update($wpdb->prefix.'goods', $data, ['id' => $goodsId]);
                }else{
                    $bool = $wpdb->insert($wpdb->prefix.'goods', $data);
                }
                if($bool) echo '<script type="text/javascript"> alert("编辑商品成功") </script>';
                else echo '<script type="text/javascript"> alert("编辑商品失败") </script>';
            }
            if(isset($_GET['goodsId'])){
                $id = intval($_GET['goodsId']);
                if($id > 0){
                    $row = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'goods WHERE id='.$id, ARRAY_A);
                }
            }

            ?>
            <div id="wpbody-content" aria-label="主内容" tabindex="0">
                <div id="screen-meta" class="metabox-prefs">


                </div>

                <div class="wrap">
                    <h1>添加商品</h1>

                        <form method="post" action="" novalidate="novalidate" enctype="multipart/form-data">
                            <table class="form-table">

                                <tbody><tr>
                                    <th scope="row"><label for="goods_title">商品名称</label></th>
                                    <td><input name="goods_title" type="text" id="goods_title" value="<?=isset($row['goods_title']) ? $row['goods_title'] : ''?>" class="goods_title-text"></td>
                                </tr>

                                <tr>
                                    <th scope="row"><label for="goods_intro">商品简介</label></th>
                                    <td><input name="goods_intro" type="text" id="goods_intro" aria-describedby="goods_intro-description" value="<?=isset($row['goods_intro']) ? $row['goods_intro'] : ''?>" class="regular-text">
                                        <p class="description" id="goods_intro-description">用简洁的文字描述商品。</p></td>
                                </tr>

                                <tr>
                                    <th scope="row"><label for="goods_price">商品价格</label></th>
                                    <td><input name="goods_price" type="text" id="goods_price" aria-describedby="goods_price-description" value="<?=isset($row['price']) ? $row['price'] : ''?>" class="goods_price-text">
                                        <p class="description" id="goods_price-description">默认0.00</p></td>
                                </tr>

                                <tr>
                                    <th scope="row"><label for="goods_brain">使用脑币</label></th>
                                    <td><input name="goods_brain" type="text" id="goods_brain" aria-describedby="goods_brain-description" value="<?=isset($row['brain']) ? $row['brain'] : ''?>" class="goods_brain-text">
    <!--                                    <p class="description" id="goods_brain-description">需要</p></td>-->
                                </tr>

                                <tr>
                                    <th scope="row"><label for="goods_stock">商品库存</label></th>
                                    <td><input name="goods_stock" type="text" id="goods_stock" aria-describedby="goods_stock-description" value="<?=isset($row['stock']) ? $row['stock'] : ''?>" class="goods_stock-text">
    <!--                                    <p class="description" id="goods_brain-description">需要</p></td>-->
                                </tr>

                                <tr>
                                    <th scope="row"><label for="goods_sales">商品销量</label></th>
                                    <td><input name="goods_sales" type="text" id="goods_sales" aria-describedby="goods_sales-description" value="<?=isset($row['sales']) ? $row['sales'] : ''?>" class="goods_sales-text">
    <!--                                    <p class="description" id="goods_brain-description">需要</p></td>-->
                                </tr>
                                <tr>

                                    <th>
                                        商品相册:
                                        <input type="button" id="add-banner" class="button button-primary" value="添加">
                                    </th>
                                    <style type="text/css">
                                        .logoImg{
                                            width: <?php echo (is_mobile() ? '99%;' : '20em;');?>
                                        }
                                        #template{
                                            display: none;
                                        }
                                    </style>
                                   <td>
                                       <div id="pro-box">

                                           <?php
                                           if(isset($row) && is_array(unserialize($row['images']))) {
                                               foreach (unserialize($row['images']) as $image) {
                                           ?>
                                                  <p>
                                                      <input type="text" size="60" value="<?=$image?>" name="goods_images[]" class="upload_input">
                                                      <img src="<?=$image?>" class="logoImg">
                                                      <a class="upload_button button" href="#">上传</a>
                                                      <a class="del_button button" href="#">删除</a>
                                                  </p>
                                           <?php
                                               }
                                           }
                                           ?>



                                       </div>
                                   </td>


                                </tr>
                                <tr>
                                    <th>
                                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="保存更改"></p>
                                        <input type="hidden" name="goods_id" value="<?=isset($row['id']) ? $row['id'] : 0?>">
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                        </form>



                </div>


                <div class="clear">

                </div>
                <div id="template">
                    <p>
                        <input type="text" size="60" value="" name="goods_images[]" class="upload_input"/>
                        <img src="" class="logoImg">
                        <a class="upload_button button" href="#">上传</a>
                        <a class="del_button button" href="#">删除</a>
                    </p>
                </div>
            </div>
            <?php wp_enqueue_media();?>
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

        /**
         * css / js
         */
        public function register_scripts(){
            switch ($_GET['page']){
                case 'goods':
                    wp_register_script('lists-js',goods_js_url.'lists.js');
                    wp_enqueue_script( 'lists-js' );
//                    wp_register_style('list-css',match_css_url.'order-lists.css');
//                    wp_enqueue_style( 'list-css' );
                    break;
            }
            echo "<script>var ajax_url='".admin_url('admin-ajax.php' )."';</script>";
        }
    }

}
new GoodsController();