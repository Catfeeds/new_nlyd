<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/6/21
 * Time: 14:14
 */

/*require_once(ABSPATH . 'wp-admin/admin.php');
require_once(ABSPATH . 'wp-admin/admin-header.php');*/

//global $wpdb;
//$rows = $wpdb->get_results('SELECT * FROM '.$wpdb->options)
$rows = get_option('index_banner_url');
?>
<style type="text/css">
    .logoImg{
        width: 18em;
    }
    #template{
        display: none;
    }
</style>
<div class="wrap">
    <h1 id="add-new-user">banner设置</h1>
    <div id="ajax-response"></div>
    <form name="interface" id="interface" class="validate interface" novalidate="novalidate">

        <input name="action" type="hidden" value="saveBanner">
        banner上传:
          <div id="pro-box">
              <?php if(!empty($rows)){foreach ($rows as $row){ ?>
                  <p>
                      <input type="text" size="60" value="<?=!empty($row) ? $row : '';?>" name="index_banner_url[]" class="upload_input"/>
                      <img src="<?=!empty($row) ? $row : '';?>" class="logoImg">
                      <a class="upload_button button" href="#">上传</a>
                      <a class="del_button button" href="#">删除</a>
                  </p>
              <?php }} ?>

          </div>
<!--        <p>-->
<!--            <input type="text" size="60" value="" name="index_banner_url[]" class="upload_input"/>-->
<!--            <img src="--><?//=!empty($logo_url) ? $logo_url : '';?><!--" class="logoImg">-->
<!--            <a class="upload_button button" href="#">上传</a>-->
<!--        </p>-->


        <p class="submit">
            <input type="submit" id="interfaceSub" class="button button-primary" value="提交">
            <input type="button" id="add-banner" class="button button-primary" value="添加">
        </p>
    </form>
    <div id="template">
        <p>
            <input type="text" size="60" value="" name="index_banner_url[]" class="upload_input"/>
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


