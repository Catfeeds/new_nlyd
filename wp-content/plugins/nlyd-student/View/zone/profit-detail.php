
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('收益详情', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content flow-default"  id="profit_flow">
         
            </div>
        </div>            
    </div>
</div>
<script>
jQuery(function($) { 
    layui.use(['element','flow'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        var flow = layui.flow;//流加载
        function pagation(id,profit_page){
            flow.load({
                elem: '#'+id //流加载容器
                ,isAuto: false
                ,isLazyimg: true
                ,done: function(page, next){ //加载下一页
                    var postData={
                        action:'get_my_profit_detail',
                        page:profit_page,
                        id:$.Request('id'),
                    }
                    var lis = [];
                    $.ajax({
                        data: postData,
                        success:function(res,ajaxStatu,xhr){
                            console.log(res)
                            profit_page++
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var income_type=''
                                    switch (v.income_type) {
                                        case 'match':
                                            income_type='<?=__("比赛详情", "nlyd-student")?>'
                                            break;
                                        case 'grading':
                                            income_type='<?=__("考级详情", "nlyd-student")?>'
                                        break;
                                        default:
                                            break;
                                    }
                                    var profit_channel = v.profit_channel ? v.profit_channel : '';
                                    var match_detail=v.post_title ? '<div class="profit_detail_row">'+
                                        '<div class="profit_detail_label">'+income_type+'：</div>'+
                                        '<div class="profit_detail_info c_black">'+v.post_title+'</div>'+
                                        '</div>' : '';
                                    var dom='<div class="layui-row width-margin width-margin-pc profit_detail_item">'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("收益金额", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_black">¥ '+v.profit_income+'</div>'+
                                                '</div>'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("收益来源", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_black">'+
                                                        v.income_type_cn+
                                                    '</div>'+
                                                '</div>'+
                                                    match_detail+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("收益途径", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_black">'+profit_channel+' '+v.profit_lv+' '+v.channel_ID+'</div>'+
                                                '</div>'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("收益状态", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_green">'+v.income_status_cn+'</div>'+
                                                '</div>'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("到账时间", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_black">'+v.created_time+'</div>'+
                                                '</div>'+
                                            '</div>'
                                    lis.push(dom) 
                                })
                                if (res.data.info.length<50) {
                                    next(lis.join(''),false) 
                                }else{
                                    next(lis.join(''),true) 
                                }
                                
                            }else{
                                next(lis.join(''),false)
                            }
                        },
                        complete:function(XMLHttpRequest, textStatus){
							if(textStatus=='timeout'){
								$.alerts("<?=__('网络质量差,请重试', 'nlyd-student')?>")
								next(lis.join(''),true)
							}
                        }
                    })       
                }
            });
        }
        pagation('profit_flow',1)

    });
})
</script>

