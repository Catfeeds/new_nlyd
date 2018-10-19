<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/6/21
 * Time: 14:14
 */

require_once(ABSPATH . 'wp-admin/admin.php');
require_once(ABSPATH . 'wp-admin/admin-header.php');
?>

<style>
    .wrapperTable{
        float: left;
        overflow: hidden;
    }
</style>

<div class="wrap">
    <h1 id="add-new-user">项目时长设置</h1>
    <div id="ajax-response"></div>
    <form name="match_default" id="match_default" class="validate" novalidate="novalidate">

        <input name="action" type="hidden" value="setting_project_use">
        <?php if(!empty($lists)){?>
        <?php
            foreach ($lists as $v){
                $alias = get_post_meta($v->ID,'project_alias')[0];
               //print_r($alias);
        ?>
        <div>
            <label><?=$v->post_title?></label>
            <?php if($alias == 'zxss'){ ?>
                <div>
                    <label>连加运算</label>
                    <input type="text" name="project_use[<?=$alias?>][even_add]" value="<?=$match_project[$alias]['even_add']?>"/>分钟
                </div>
                <div>
                    <label>加减运算</label>
                    <input type="text" name="project_use[<?=$alias?>][add_and_subtract]" value="<?=$match_project[$alias]['add_and_subtract']?>"/>分钟
                </div>
                <div>
                    <label>乘除运算</label>
                    <input type="text" name="project_use[<?=$alias?>][wax_and_wane]" value="<?=$match_project[$alias]['wax_and_wane']?>"/>分钟
                </div>
            <?php }else{ ?>
            <input type="text" name="project_use[<?=$alias?>]" value="<?=$match_project[$alias]?>"/>分钟
            <?php } ?>
        </div>
        <?php } ?>
        <?php } ?>
        <div>
            <label>快眼闪烁时间</label>
            <input type="text" name="project_default['kysm'][flicker]" value="<?=!empty($project_default['kysm']['flicker']) ? $project_default['kysm']['flicker'] : 5;?>"/>分钟
        </div>
        <p><input type="submit" id="match_default_sub" class="button button-primary" value="提交"></p>
    </form>
</div>
<script>
    jQuery(document).ready(function($){

        jQuery('#match_default_sub').live('click',function(event){
            var query = jQuery('#match_default').serialize();
            $.post(ajaxurl,query,function (data) {
                alert(data.data);
            },'json')
            return false;
        });
    });
</script>



