<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/6/21
 * Time: 14:14
 */

/*require_once(ABSPATH . 'wp-admin/admin.php');
require_once(ABSPATH . 'wp-admin/admin-header.php');*/
?>
<div class="wrap">
    <h1 id="add-new-user">logo设置</h1>
    <div id="ajax-response"></div>
    <form name="interface" id="interface" class="validate interface" novalidate="novalidate">

        <input name="action" type="hidden" value="saveLogo">
        logo上传:
        <p>
            <input type="text" size="60" value="" name="logo_url" id="upload_input"/>
             <img src="<?=!empty($logo_url) ? $logo_url : '';?>" class="logoImg" style="width: 20em">
             <a id="upload" class="upload_button button" href="#">上传</a>
         </p>

        <p class="submit"><input type="submit" id="interfaceSub" class="button button-primary" value="提交"></p>
    </form>
</div>

<?php wp_enqueue_media();?>
<script>

    jQuery(document).ready(function($){
        var upload_frame;
        var value_id;
        jQuery('.upload_button').live('click',function(event){
            value_id =jQuery( this ).attr('id');
            event.preventDefault();
            if( upload_frame ){
                upload_frame.open();
                return;
            }
            upload_frame = wp.media({
                title: 'logo 上传',
                button: {
                    text: '提交',
                },
                multiple: false
            });
            upload_frame.on('select',function(){
                console.log(upload_frame.state().get('selection'));
                attachment = upload_frame.state().get('selection').first().toJSON();
                jQuery('input[name="logo_url"]').val(attachment.url);
                jQuery('.logoImg').attr('src',attachment.url);
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



    });
</script>


