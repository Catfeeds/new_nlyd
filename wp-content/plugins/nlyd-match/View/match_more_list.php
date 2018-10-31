<style>
.show_form{
    /* width:600px; */
    padding:20px;
}
.layui-layer.nl-box-skin{
    width:800px;
}
.nl-box-skin .layui-layer-title{
    font-size:20px;
    height:50px;
    line-height:50px;
}
</style>
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
                            <button type="button" class="update_more" data-id="<?=$v['id']?>">编辑</button>/
                            <button type="button" class="remove_more" data-id="<?=$v['id']?>">删除</button>
                        </td>
                    </tr>
                    <?php }?>
                <?php }else{ ?>
                    <tr>
                        <td colspan="7" style="text-align: center">暂无列表</td>
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
    <div class="show_form" style="display: none" >
        <form class="add_more_form layui-form">
            <input type="hidden" name="action" value="match_more_add"/>
            <input type="hidden" name="post_id" value="<?=$_GET['post_id']?>"/>
            <input type="hidden" name="project_id" value="<?=$_GET['project_id']?>"/>
            <input id="match_more_id" type="hidden" name="more_id" value=""/>
            <div class="layui-form-item">
                <label class="layui-form-label">开始时间</label>
                <div class="layui-input-block">
                    <input type="text" value="" name="start_time" id="start_time" class="layui-input date-picker"/>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">结束时间</label>
                <div class="layui-input-block">
                    <input type="text" value="" name="end_time" id="end_time" class="layui-input date-picker"/>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">比赛时长</label>
                <div class="layui-input-block">
                    <input type="text" value="" name="use_time" class="layui-input"/>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">比赛状态</label>
                <div class="layui-input-block">
                    <input type="radio" name="status" value="-1" title="已结束">
                    <input type="radio" name="status" value="1" title="未开始">
                    <input type="radio" name="status" value="2" title="进行中">
                </div>
            </div>
        </form>
    </div>
    <br class="clear">
    <?php } ?>
<script>
    jQuery(document).ready(function($){
        //删除
        $('.remove_more').live('click',function(event){
            var id = $(this).attr('data-id');
            $.post(ajaxurl,{action:'remove_match_more',id:id},function (data) {
                alert(data.data);
                history.go(0);
            },'json')
            return false;
        });
        $('.update_more').live('click',function(event){//编辑
            // $('.add_more_form').show();
            var _this=$(this);
            var title=_this.parents('tr').find('td').eq(0).text()
            var start_time=_this.parents('tr').find('td').eq(1).text()
            var end_time=_this.parents('tr').find('td').eq(2).text()
            var _time=_this.parents('tr').find('td').eq(3).text()
            showForm(title)
            $('#match_more_id').val($(this).attr('data-id'))
            $('input[name=start_time]').val(start_time)
            $('input[name=end_time]').val(end_time)
            $('input[name=use_time]').val(_time)
        });
        $('.page-title-action').live('click',function(event){//新增
            showForm('新增轮数')
            $('#match_more_id').val('')
            $('input[name=start_time]').val('')
            $('input[name=end_time]').val('')
            $('input[name=use_time]').val('')
        })
        layui.use(['layer','laydate'], function(){
            var laydate = layui.laydate;
            $('.date-picker').each(function(){
                var id=$(this).attr('id');
                    laydate.render({
                        elem: '#'+id
                        ,type: 'datetime'
                        ,format: 'yyyy-MM-dd HH:mm'
                    });
            })
        }); 
        function showForm(title) {
            layer.open({
                type: 1
                ,maxWidth:1000
                ,title: title //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content:$('.show_form')
                ,btn: ['按错了','提交',  ]
                ,success: function(layero, index){
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    //按钮【按钮二】的回调
                    layer.closeAll();
                    var query = $('.add_more_form').serialize();
                    $.post(ajaxurl,query,function (data) {
                        alert(data.data);
                        history.go(0);
                        /*setTimeout(function () {
                        },900)*/
                    },'json')
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
           
        }
    });
</script>