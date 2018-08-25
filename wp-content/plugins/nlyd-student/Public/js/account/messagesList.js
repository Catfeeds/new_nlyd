
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
                $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res){
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
                            // if(page==1){
                            //     var dom='<div class="no-info">暂无消息</div>'
                            //     lis.push(dom)
                            // }else{
                            //     $.alerts('没有更多了')
                            // }
                            next(lis.join(''),false)
                        }
                })
            }
        });
    })
})