<!--我的消息列表-->
<style>
@media screen and (max-width: 991px){
    #content,.detail-content-wrapper{
        background:#eee;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';

        ?>


        <?php if($is_show){ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('student/account/')?>">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <h1 class="mui-title">我的消息</h1>
            </header>
                <div class="layui-row nl-border nl-content">
                    <div class="width-margin flow-default" id="flow-list">

                    </div>
                </div>
            </div>
        <?php }else{ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper layui-bg-white">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('student/account/')?>">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <h1 class="mui-title">我的消息</h1>
            </header>
                <div class="layui-row nl-border nl-content">

                    <div class="no-info-page">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noMessage1040@2x.png'?>">
                        </div>
                        <p class="no-info-text">暂无任何站内消息</p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script>
jQuery(function($) {
    layui.use(['flow','layer'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

        var flow = layui.flow;//流加载
        flow.load({
            elem: '#flow-list' //流加载容器
            ,scrollElem: '#flow-list' //滚动条所在元素，一般不用填，此处只是演示需要。
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                var postData={
                    action:'getMessagesLists',
                    page:page
                }
                var lis = [];
                $.post(window.admin_ajax,postData,function(res){
                    console.log(res)
                        if(res.success){
                            $.each(res.data.info,function(index,value){
                                var type="";
                                if(value.read_status==2){
                                    type='read';
                                }
                                var url=window.home_url+'/account/messageDetail/messages_id/'+value.id;

                                var dom='<a class="message-row '+type+'" href="'+url+'">'
                                            +'<div class="message-title">'
                                                +'<span class="accept-name">'+value.title+'</span>'

                                                +'<span class="message-time">'+value.message_time+'</span>'
                                            +'</div>'
                                            +'<p class="message-detail">'+value.content+'</p>'
                                        +'</a>'
                                lis.push(dom)
                            })
                            if (res.data.info.length<10) {
                                next(lis.join(''),false)
                            }else{
                                next(lis.join(''),true)
                            }
                        }else{
                            if(page==1){
                                var dom='<div class="no-info">暂无消息</div>'
                                lis.push(dom)
                            }else{
                                $.alerts('没有更多了')
                            }
                            next(lis.join(''),false)
                        }
                })
            }
        });
    })
})
</script>