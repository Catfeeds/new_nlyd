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
    <h1 id="add-new-user">接口Key设置</h1>
    <div id="ajax-response"></div>
    <p>添加各项接口，并将接口加入此站点。<span style="color: red">备注:若值不止一个,请用英文逗号分割,例:a,b,c </span></p>
    <form name="interface" id="interface" class="validate interface" novalidate="novalidate">

        <input name="action" type="hidden" value="saveInterface">
        <?php foreach ($config as $k => $val ){ ?>
        <div class="wrapperTable">
            <div class="tabsBt"><?=$val['title']?></div>
            <table class="form-table" title="<?=$k?>">
                <tbody>
                <?php foreach ($val['tag'] as $v){ ?>
                    <tr class="form-field form-required">
                        <th scope="row"><label><?=$v['title']?></label></th>
                        <td>
                            <?php switch($v['type']){
                                case 'input':
                            ?>
                            <input name="interface[<?=$k?>][<?=$v['name']?>]" type="text" value="<?=$interface_config[$k][$v['name']]?>"  placeholder="<?=$v['placeholder']?>">

                            <?php
                                break;
                                case 'checkbox':
                            ?>
                             <input type="checkbox" name="interface[<?=$k?>][<?=$v['name']?>]" value="1" <?= !empty($interface_config[$k][$v['name']])  ? 'checked' : '';?> /><?=$v['placeholder']?>
                            <?php
                                break;
                            ?>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>

        <p class="submit"><input type="submit" id="interfaceSub" class="button button-primary" value="提交"></p>
    </form>
</div>
<?php
//include( ABSPATH . 'wp-admin/admin-footer.php' );



