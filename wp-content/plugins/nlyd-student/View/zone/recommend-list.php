

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
                <h1 class="mui-title"><div><?=__('推荐管理', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="layui-row layui-bg-white">
                    <div class="profit-layui-tab recommend-5 layui-tab layui-tab-brief" lay-filter="profit" style="margin:0">
                        <ul style="margin:0;padding:0" class="layui-tab-title layui-row">
                            <li class="layui-this dis_table" lay-id="1"><div class="dis_cell"><?=__('个人用户（'.$user_total.'）', 'nlyd-student')?></div></li>
                            <li class="dis_table" lay-id="2"><div class="dis_cell"><?=__('机构用户（'.$zone_total.'）', 'nlyd-student')?></div></li>
                        </ul>
                        <div class="layui-tab-content">
                            <!-- 全部记录 -->
                            <div class="layui-tab-item layui-show">
                                <div class="nl-table-wapper">
                                    <table class="nl-table">
                                        <thead>
                                            <tr class='table-head'>
                                                <td><?=__('姓名/编号', 'nlyd-student')?></td>
                                                <td><?=__('年 龄', 'nlyd-student')?></td>
                                                <td><?=__('性 别', 'nlyd-student')?></td>
                                                <td><?=__('时 间', 'nlyd-student')?></td>
                                                <td><?=__('购 课', 'nlyd-student')?></td>
                                            </tr>
                                        </thead>
                                        <tbody id="1">
                                           
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- 收益记录 -->
                            <div class="layui-tab-item">
                                <div class="nl-table-wapper">
                                    <table class="nl-table">
                                        <thead>
                                            <tr class='table-head'>
                                                <td><?=__('名 字', 'nlyd-student')?></td>
                                                <td><?=__('时 间', 'nlyd-student')?></td>
                                                <td><?=__('操 作', 'nlyd-student')?></td>
                                            </tr>
                                        </thead>
                                        <tbody id="2">
                                            
                                        </tbody>
                                    </table>
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
                        action:'get_my_offline',
                        page:profit_page,  
                    }
                    if(parseInt(id)==2){//机构
                        postData['map']="zone";
                    }
                    console.log(postData)
                    var lis = [];
                    $.ajax({
                        data: postData,
                        success:function(res,ajaxStatu,xhr){
                            console.log(res)
                            isClick[id]=true
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var dom_1='';
                                    if(parseInt(id)==1){//个人
                                        var is_shop_1='-';

                                        if(v.is_shop=='y'){
                                            is_shop_1='<div class="table_content c_green"><?=__("是", "nlyd-student")?></div>';
                                        }else if(v.is_shop=='n'){
                                            is_shop_1='<div class="table_content c_black"><?=__("否", "nlyd-student")?></div>';
                                        }
                                        dom_1='<tr>'+
                                                '<td><div class="table_content"><div class="c_black ta_c">'+v.real_name+'</div><div class="ff_num fs_12 ta_c">'+v.user_ID+'</div></div></td>'+
                                                '<td><div class="table_content">'+v.real_age+'</div></td>'+
                                                '<td><div class="table_content c_black">'+v.user_gender+'</div></td>'+
                                                '<td><div class="table_content c_black">'+v.referee_time+'</div></td>'+
                                                '<td>'+is_shop_1+'</td>'+
                                            '</tr>'
                                        lis.push(dom_1)
                                        if(v.child){
                                            $.each(v.child,function(index,val){
                                                var dom_2="";
                                                var is_shop_2='-';
                                                if(val.is_shop=='y'){
                                                    is_shop_2='<div class="table_content c_green"><?=__("是", "nlyd-student")?></div>';
                                                }else if(val.is_shop=='n'){
                                                    is_shop_2='<div class="table_content c_black"><?=__("否", "nlyd-student")?></div>';
                                                }
                                                dom_2='<tr>'+
                                                        '<td><div class="table_content"><div class="c_orange ta_c">'+val.real_name+'</div><div class="ff_num fs_12 ta_c">'+val.user_ID+'</div></div></td>'+
                                                        '<td><div class="table_content">'+val.real_age+'</div></td>'+
                                                        '<td><div class="table_content c_black">'+val.user_gender+'</div></td>'+
                                                        '<td><div class="table_content c_black">'+val.referee_time+'</div></td>'+
                                                        '<td>'+is_shop_2+'</td>'+
                                                    '</tr>'
                                                lis.push(dom_2)
                                            })
                                        }
                                    }else if(parseInt(id)==2){//机构
                                        dom_1='<tr>'+
                                                '<td>'+
                                                    '<div class="table_content">'+
                                                        '<div class="c_black ta_l pl_10">'+ v.zone_name+'</div>'+
                                                        // '<div class="ff_num fs_12 ta_c">'+v.audit_time+'</div>'+
                                                    '</div>'+
                                                '</td>'+
                                                '<td><div class="ff_num fs_12 ta_c">'+v.audit_time+'</div></td>'+
                                                '<td><div class="table_content"><a class="c_blue disabled_a">详 情</a></div></td>'+
                                            '</tr>'
                                        lis.push(dom_1)
                                        if(v.child){
                                            $.each(v.child,function(index,val){
                                                var dom_2="";
                                                dom_2='<tr>'+
                                                        '<td>'+
                                                            '<div class="table_content">'+
                                                                '<div class="c_orange ta_l pl_10">'+val.zone_name+'</div>'+
                                                                // '<div class="ff_num fs_12 ta_c">'+val.audit_time+'</div>'+
                                                            '</div>'+
                                                        '</td>'+
                                                        '<td><div class="ff_num fs_12 ta_c">'+val.audit_time+'</div></td>'+
                                                        '<td><div class="table_content"><a class="c_blue disabled_a">详 情</a></div></td>'+
                                                    '</tr>'
                                                lis.push(dom_2)
                                            })
                                        }
                                    }

                                })
                                if(res.data.info!== null){
                                    if (res.data.info.length<50) {
                                        next(lis.join(''),false) 
                                    }else{
                                        next(lis.join(''),true) 
                                    }
                                }else{
                                    next(lis.join(''),false) 
                                }
                                
                            }else{
                                next(lis.join(''),false)
                            }
                            profit_page++
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
        element.on('tab(profit)', function(){//profit
            // location.hash = 'profit='+ $(this).attr('lay-id');
            var id=$(this).attr('lay-id')
            if(!isClick[id]){
                pagation(id,1)
            }
        });

    });
})
</script>
