<?php
/**
 * 项目默认配置
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/21
 * Time: 9:48
 */
$setting = get_option('default_setting');

?>

<div class="wrap">
    <h1>常规选项</h1>

    <form method="post" id="default_form" novalidate="novalidate">
        <input type="hidden" name="action" value="update_default_setting">
        <table class="form-table">

            <tbody>
            <tr>
                <th scope="row"><label for="blogname">定时器开关</label></th>
                <td><input name="default_timer" type="checkbox" value="1" <?= $setting['default_timer']  ? 'checked' : '';?> class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="blogname">使用默认比赛项目</label></th>
                <td><input type="checkbox" name="default_match_project" value="1" <?= $setting['default_match_project']  ? 'checked' : '';?> class="regular-text" /></td>
            </tr>
        <p class="submit"><input type="submit" name="submit" id="submit_form" class="button button-primary" value="保存更改"></p>
    </form>
</div>
<script>
    jQuery(document).ready(function($){
        var upload_frame;
        var value_id;
        jQuery('#submit_form').live('click',function(event){
            var query = $('#default_form').serialize();
            $.post(ajaxurl,query,function (data) {
                alert(data.data);
            },'json')
            return false;
        });
    });
</script>
