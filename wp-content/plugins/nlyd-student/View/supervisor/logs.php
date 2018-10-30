<!--监赛提交列表-->


<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('/account/')?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('监赛记录', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                    <?php if(!empty($lists)){
                        foreach ($lists as $v){
                    ?>
                    <div class="address-row width-margin width-margin-pc"
                    <?php if(isset($_GET['match_id'])){?>
                        href="<?=home_url('supervisor/index/id/'.$v['id'])?>"
                    <?php } ?>
                    >
                        <div class="address-left">
                            <div class="address-title">
                                <span class="accept-name"><?=$val['fullname']?></span>
                                <span class="phone-number ff_num"><?=$val['telephone']?></span>
                            </div>
                            <p class="address-detail"><?=$val['user_address']?></p>
                        </div>
                        <div  class="address-right">
                            <a class="address-btn bg_gradient_blue c_white" href="<?=home_url('supervisor/index/id/'.$v['id'])?>"><?=__('修改', 'nlyd-student')?></a>
                            <div class="address-btn del bg_gradient_grey c_white" data-id="<?=$v['id']?>"><?=__('删除', 'nlyd-student')?></div>
                        </div>
                    </div>
                    <?php  } }else{ ?>
                        <p class="no-info"><?=__('暂未上传任何监赛记录', 'nlyd-student')?></p>
                    <?php } ?>
                <a class="a-btn a-btn-table" href="<?=home_url('supervisor/')?>"><div><?=__('新增监赛记录', 'nlyd-student')?></div></a>
            </div>
        </div>
    </div>
</div>
<script>
jQuery(function($) {
    layui.use(['layer'], function(){
        $('body').on('click','.del',function(){//删除地址
            var _this=$(this);
            layer.open({
                type: 1
                ,maxWidth:300
                ,title: '提示' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper">'+_address.delete+'</div>'
                ,btn: [_address.think, _address.sure, ]
                ,success: function(layero, index){

                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    var postData={
                        action:'remove_address',
                        _wpnonce:$("#delAddress").val(),
                        id:_this.attr('data-id')
                    }
                    $.ajax({
                        data: postData,success:function(res,ajaxStatu,xhr){
                            $.alerts(res.data.info)
                            if(res.success){
                                _this.parents('.address-row').remove()
                            }
                        }
                    })
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
            return false;
        })
    })
    $('body').on('click','.address-row',function(){
        if($(this).attr('href')){
            window.location.href=$(this).attr('href')
        }
    })
$('.address-row').each(function(i){//左滑动
    var _this=$(this)
    new AlloyFinger(_this[0], {
        touchMove:function(evt) {
            if (Math.abs(evt.deltaX) >= Math.abs(evt.deltaY)) {
                evt.preventDefault();
            }
        },
        swipe:function(evt){
            if(evt.direction==="Left"){
                _this.addClass('swipeleft')
            }else if(evt.direction==="Right"){
                _this.removeClass('swipeleft')
            }
        }
    });
})
});

})
</script>