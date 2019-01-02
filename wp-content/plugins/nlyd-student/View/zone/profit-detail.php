
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
                <div class="layui-row width-margin width-margin-pc profit_detail_item">
                        <div class="profit_detail_row">
                            <div class="profit_detail_label"><?=__('收益金额', 'nlyd-student')?>：</div>
                            <div class="profit_detail_info c_black">¥ <?=$row['user_income']?></div>
                        </div>
                        <div class="profit_detail_row">
                            <div class="profit_detail_label"><?=__('收益来源', 'nlyd-student')?>：</div>
                            <div class="profit_detail_info c_black">
                                <?=$row['profit_lv']?>
                                <?=$row['channel']?>
                                <?=__($row['income_type_title'], 'nlyd-student')?>
                            </div>
                        </div>
                        <?php if(!empty($match)): ?>
                        <div class="profit_detail_row">
                            <div class="profit_detail_label"><?=__('比赛类型', 'nlyd-student')?>：</div>
                            <div class="profit_detail_info c_black"><?=__($match['role_name'], 'nlyd-student')?></div>
                        </div>
                        <div class="profit_detail_row">
                            <div class="profit_detail_label"><?=__('比赛详情', 'nlyd-student')?>：</div>
                            <div class="profit_detail_info c_black"><?=__($match['match_title'], 'nlyd-student')?></div>
                        </div>
                        <?php endif;?>
                        <!--<div class="profit_detail_row">
                            <div class="profit_detail_label"><?/*=__('收益级别', 'nlyd-student')*/?>：</div>
                            <div class="profit_detail_info c_black"><?/*=__($row['profit_lv'], 'nlyd-student')*/?></div>
                        </div>-->
                        <?php if(!empty($row['channel'])):?>
                        <div class="profit_detail_row">
                            <div class="profit_detail_label"><?=__('收益途径', 'nlyd-student')?>：</div>
                            <div class="profit_detail_info c_black"><?=__($row['income_channel'], 'nlyd-student')?></div>
                        </div>
                        <?php endif;?>
                        <div class="profit_detail_row">
                            <div class="profit_detail_label"><?=__('收益状态', 'nlyd-student')?>：</div>
                            <div class="profit_detail_info c_green"><?=__('已到账', 'nlyd-student')?></div>
                        </div>
                        <!--<div class="profit_detail_row">
                            <div class="profit_detail_label"><?/*=__('到账时间', 'nlyd-student')*/?>：</div>
                            <div class="profit_detail_info c_black">2018/12/12 13:18</div>
                        </div>-->
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
                        action:'get_my_profit_detail',
                        page:profit_page,
                    }
                    var lis = [];
                    $.ajax({
                        data: postData,
                        success:function(res,ajaxStatu,xhr){
                            console.log(res)
                            profit_page++
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var dom1='<div class="layui-row width-margin width-margin-pc profit_detail_item">'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("收益金额", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_black">¥ </div>'+
                                                '</div>'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("收益来源", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_black">'+
                                                       
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("比赛类型", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_black"></div>'+
                                                '</div>'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("比赛详情", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_black"></div>'+
                                                '</div>'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("收益途径", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_black"></div>'+
                                                '</div>'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("收益状态", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_green"></div>'+
                                                '</div>'+
                                            '</div>'
                                    var dom2='<div class="layui-row width-margin width-margin-pc profit_detail_item">'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("提现金额", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_black">¥ </div>'+
                                                '</div>'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("提现路径", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_black">'+
                                                        
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("发起时间", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_black"></div>'+
                                                '</div>'+
                                                '<div class="profit_detail_row">'+
                                                    '<div class="profit_detail_label"><?=__("提现状态", "nlyd-student")?>：</div>'+
                                                    '<div class="profit_detail_info c_green"></div>'+
                                                '</div>'+
                                            '</div>'
                                            console.log(dom)
                                    lis.push(dom1) 
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

