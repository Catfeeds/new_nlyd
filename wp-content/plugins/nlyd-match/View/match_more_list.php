<div class="wrap">
    <?php if(empty($_GET['post_id']) || empty($_GET['project_id'])){ ?>
        <h4>参数错误</h4>
    <?php }else{ ?>
    <h1 class="wp-heading-inline"><?=get_post($_GET['post_id'])->post_title?>-<?=get_post($_GET['project_id'])->post_title?></h1>

    <button class="page-title-action">新增轮数</button>
    <form method="get" onsubmit="return false;">

        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                <select name="action" id="bulk-action-selector-top">
                    <option value="">批量操作</option>
                    <option value="-2">一键删除</option>
                </select>
                <input type="submit" id="doaction" class="button action  all-btn" value="应用">
            </div>
            <h2 class="screen-reader-text">轮数列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                        <input id="cb-select-all-1" type="checkbox">
                    </td>
                    <th scope="col" id="title" class="manage-column column-name">轮数</th>
                    <th scope="col" id="name" class="manage-column column-name">开始时间</th>
                    <th scope="col" id="status" class="manage-column column-status status">结束时间</th>
                    <th scope="col" id="status" class="manage-column column-status status">时长</th>
                    <th scope="col" id="mobile" class="manage-column column-mobile">比赛状态</th>
                    <th scope="col" id="age" class="manage-column column-age">操作</th>
                </tr>
                </thead>
                <tbody id="the-list" data-wp-lists="list:user">
                <?php if(!empty($rows)){ ?>
                    <?php foreach ($rows as $k => $v){ ?>
                    <tr>
                        <th scope="row" class="check-column">			<label class="screen-reader-text" for="cb-select-622">选择Timer定时器</label>
                            <input type="checkbox" name="id[]" value="<?=$v['id']?>">
                        </th>
                        <td class="column-title">第<?=$v['more']?>轮</td>
                        <td class="column-title"><?=$v['start_time']?></td>
                        <td class="column-title"><?=$v['end_time']?></td>
                        <td class="column-title"><?=(strtotime($v['end_time'])-strtotime($v['start_time']))/60?></td>
                        <td class="column-title"><?=$v['status_cn']?></td>
                        <td class="column-title">
                            <span class="update_more" data-id="<?=$v['id']?>">编辑</span>/
                            <span class="remove_more" data-id="<?=$v['id']?>">删除</span>
                        </td>
                    </tr>
                    <?php }?>
                <?php }else{ ?>
                    <tr>
                        <td colspan="6" style="text-align: center">暂无列表</td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                        <input id="cb-select-all-2" type="checkbox">
                    </td>
                    <th scope="col" id="name" class="manage-column column-name">轮数</th>
                    <th scope="col" id="name" class="manage-column column-name">开始时间</th>
                    <th scope="col" id="status" class="manage-column column-status status">结束时间</th>
                    <th scope="col" id="status" class="manage-column column-status status">时长</th>
                    <th scope="col" id="mobile" class="manage-column column-mobile">比赛状态</th>
                    <th scope="col" id="age" class="manage-column column-age">操作</th>

                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="">批量操作</option>
                        <option value="-2">一键删除</option>
                    </select>
                    <input type="submit" id="doaction2" class="button action all-btn" value="应用">
                </div>
                <div class="tablenav-pages one-page">
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
    </form>
    <!--轮数新增/修改form-->
    <form class="add_more_form" style="display: none" >
        <input type="hidden" name="action" value="match_more_add"/>
        <input type="hidden" name="post_id" value="<?=$_GET['post_id']?>"/>
        <input type="hidden" name="project_id" value="<?=$_GET['project_id']?>"/>
        <input id="match_more_id" type="hidden" name="more_id" value=""/>
        <div>
            <label>开始时间</label>
            <input type="text" value="" name="start_time"/>
        </div>
        <div>
            <label>结束时间</label>
            <input type="text" value="" name="end_time"/>
        </div>
        <div>
            <label>比赛时长</label>
            <input type="text" value="" name="use_time"/>分钟
        </div>
        <div>
            <label>比赛状态</label>
            已结束<input type="radio" value="-1" name="status"/>
            未开始<input type="radio" value="1" name="status"/>
            进行中<input type="radio" value="2" name="status"/>
        </div>

        <input type="submit" class="add_more_submit" value="提交"/>
    </form>
    <br class="clear">
    <?php } ?>
</div>
<script>
    jQuery(document).ready(function($){


        jQuery('.page-title-action').live('click',function(event){
            jQuery('.add_more_form').show();
        });

        jQuery('.update_more').live('click',function(event){
            jQuery('.add_more_form').show();
            jQuery('#match_more_id').val(jQuery(this).attr('data-id'))
        });


        //新增/编辑
        jQuery('.add_more_submit').live('click',function(event){
            var query = jQuery('.add_more_form').serialize();
            $.post(ajaxurl,query,function (data) {
                alert(data.data);
            },'json')
            return false;
        });

        //删除
        jQuery('.remove_more').live('click',function(event){
            var id = jQuery(this).attr('data-id');
            $.post(ajaxurl,{action:'remove_match_more',id:id},function (data) {
                alert(data.data);
            },'json')
            return false;
        });

    });
</script>