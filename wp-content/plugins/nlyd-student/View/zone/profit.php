
<style>
@media screen and (max-width: 1199px){
    .layui-fluid>.layui-row>.layui-bg-white:first-child,#page{
        background-color:#f6f6f6!important;
    }
    #page{
        top:0;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <a class="mui-pull-left nl-goback">
                <div><i class="iconfont">&#xe610;</i></div>
            </a>
            <div class="layui-row nl-border nl-content">
                <div class="apply profit_title layui-row layui-bg-white">
                    <div class="ta_c c_black"><?=__('可提现金额(元)', 'nlyd-student')?></div>
                    <div class="ta_c c_green bold  fs_22 profit_money_">¥ <?=$balance?></div>
                    <?php if($balance > 0){?>
                        <a class="bg_gradient_green tixian c_white ta_c dis_table" href="<?=home_url('zone/getCash');?>"><div class="dis_cell"><?=__('提 现', 'nlyd-student')?></div></a>
                    <?php }else{ ?>
                        <a class="bg_gradient_grey tixian c_white ta_c dis_table"><div class="dis_cell"><?=__('提 现', 'nlyd-student')?></div></a>
                    <?php } ?>
                    <div class="profit_footer flex-h">
                        <div class="flex1 ta_c">
                            <span class="c_black"><?=__('今日收益', 'nlyd-student')?>：</span>
                            <span class="c_green">¥ <?=$stream > 0 ? $stream : number_format($stream,2)?></span>
                        </div>
                        <div class="flex1 ta_c">
                            <span class="c_black"><?=__('累计收益', 'nlyd-student')?>：</span>
                            <span class="c_green">¥ <?=$stream_total > 0 ? $stream_total : number_format($stream_total,2)?></span>
                        </div>
                    </div>
                </div>

                <div class="apply width-padding layui-row layui-bg-white width-margin-pc">
                    <div class="profit-layui-tab layui-tab layui-tab-brief" lay-filter="profit" style="margin:0">
                        <ul style="margin:0;padding:0" class="layui-tab-title layui-row">
                            <li class="layui-this dis_table" lay-id="1"><div class="dis_cell"><?=__('全部记录', 'nlyd-student')?></div></li>
                            <li class="dis_table" lay-id="2"><div class="dis_cell"><?=__('收益记录', 'nlyd-student')?></div></li>
                            <li class="dis_table" lay-id="3"><div class="dis_cell"><?=__('提现记录', 'nlyd-student')?></div></li>
                        </ul>
                        <div class="layui-tab-content">
                            <!-- 全部记录 -->
                            <div class="layui-tab-item layui-show">
                                <div class="layui-row flow-default" id="1">
                                  
                                </div>
                            </div>
                            <!-- 收益记录 -->
                            <div class="layui-tab-item">
                                <div class="layui-row flow-default" id="2">
                                   
                                </div>
                            </div>
                            <!-- 提现记录 -->
                            <div class="layui-tab-item">
                                <div class="layui-row flow-default" id="3">
                                 
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
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
                        action:'get_user_profit_logs',
                        page:profit_page,
                    }
                    if(parseInt(id)==1){//全部
                        postData['map']="all";
                    }else if(parseInt(id)==3){//提现
                        postData['map']="extract";
                    }else{//收益
                        postData['map']="profit";
                    }
                    var lis = [];
                    $.ajax({
                        data: postData,
                        success:function(res,ajaxStatu,xhr){
                            console.log(res)
                            profit_page++
                            isClick[id]=true
                            if(res.success){
                                var profit_url = "<?=home_url('zone/profitDetail/id/');?>"; //收益
                                var extract_url = "<?=home_url('zone/getCashDetail/id/');?>";   //提现
                                $.each(res.data.info,function(i,v){
                                    var color=v.income_type=="extract" ? "c_black":"c_green";
                                    var income_type_class=v.income_type=="extract" ? "bg_reduce":"bg_add";
                                    var Detail_url = v.user_income > 0 ? profit_url+v.id : extract_url+v.id;
                                    var dom='<a class="profit_list c_black layui-row" href="'+Detail_url+'">'
                                                +'<div class="profit_inline profit_icon">'
                                                    +'<div class="zone_bg '+income_type_class+'"></div>'
                                                +'</div>'
                                                +'<div class="profit_inline profit_time fs_14">'
                                                    +'<span>'+v.income_type_title+'</span><br>'
                                                    +'<span class="c_black3">'+v.created_time+'</span>'
                                                +'</div>'
                                                +'<div class="'+color+' profit_inline profit_money fs_14">'+v.user_income+'</div>'
                                                +'<div class="profit_inline profit_arrow"><i class="iconfont fs_20">&#xe727;</i></div>'
                                            +'</a>'
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
        var isClick={}
    //    if(location.hash.replace(/^#profit=/, '').length==0){
    //        //获取hash来切换选项卡，假设当前地址的hash为lay-id对应的值
    //        location.hash = 'profit='+ <?=$anchor?>;
    //    }
        // var layid = location.hash.replace(/^#profit=/, '');
     
        // element.tabChange('profit', layid);
        pagation($('.layui-this').attr('lay-id'),1)
        var lefts=$('.layui-this').position().left;
        $('.nl-transform').css({
            'transform':'translate3d('+lefts+'px, -5px, 0px)'
        })
        element.on('tab(profit)', function(){//profit
            // location.hash = 'profit='+ $(this).attr('lay-id');
            var left=$(this).position().left+parseInt($(this).css('marginLeft'));
            var id=$(this).attr('lay-id')
            $('.nl-transform').css({
                'transform':'translate3d('+left+'px, 0px, 0px)'
            })
            if(!isClick[id]){
                pagation(id,1)
            }
        });

    });
})
</script>
