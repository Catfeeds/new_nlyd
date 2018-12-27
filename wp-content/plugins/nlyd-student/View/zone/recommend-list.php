

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
                            <li class="layui-this dis_table" lay-id="1"><div class="dis_cell"><?=__('个人用户（2）', 'nlyd-student')?></div></li>
                            <li class="dis_table" lay-id="2"><div class="dis_cell"><?=__('机构用户（3）', 'nlyd-student')?></div></li>
                        </ul>
                        <div class="layui-tab-content">
                            <!-- 全部记录 -->
                            <div class="layui-tab-item layui-show">
                                <div class="nl-table-wapper">
                                    <table class="nl-table">
                                        <thead>
                                            <tr class='table-head'>
                                                <td><?=__('序 号', 'nlyd-student')?></td>
                                                <td><?=__('姓名/编号', 'nlyd-student')?></td>
                                                <td><?=__('年 龄', 'nlyd-student')?></td>
                                                <td><?=__('性 别', 'nlyd-student')?></td>
                                                <td><?=__('时 间', 'nlyd-student')?></td>
                                                <td><?=__('购 课', 'nlyd-student')?></td>
                                            </tr>
                                        </thead>
                                        <tbody id="1">
                                            <tr>
                                                <td><div class="table_content">1</div></td>
                                                <td><div class="table_content"><span class="c_black">王好学</span><br><span class="ff_num fs_12">10000888</span></div></td>
                                                <td><div class="table_content">18</div></td>
                                                <td><div class="table_content c_black">男</div></td>
                                                <td><div class="table_content c_black">2018/12/17</div></td>
                                                <td><div class="table_content c_black">否</div></td>
                                            </tr>
                                            <tr>
                                                <td><div class="table_content">1</div></td>
                                                <td><div class="table_content "><span class="c_black">王好学</span><br><span class="ff_num fs_12">10000888</span></div></td>
                                                <td><div class="table_content c_black">18</div></td>
                                                <td><div class="table_content c_black">男</div></td>
                                                <td><div class="table_content">2018/12/17</div></td>
                                                <td><div class="table_content c_green">是</div></td>
                                            </tr>
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
                                                <td><?=__('序 号', 'nlyd-student')?></td>
                                                <td><?=__('名 字', 'nlyd-student')?></td>
                                                <td><?=__('操 作', 'nlyd-student')?></td>
                                            </tr>
                                        </thead>
                                        <tbody id="2">
                                            <tr>
                                                <td><div class="table_content">1</div></td>
                                                <td>
                                                    <div class="table_content">
                                                        <span class="c_black">IIsc脑博瑞国际脑力训练中心（成都）</span>
                                                        <br>
                                                        <span class="ff_num fs_12">2018/12/12</span>
                                                    </div>
                                                </td>
                                                <td><div class="table_content"><a class="c_blue">详 情</a></div></td>
                                            </tr>
                                            <tr>
                                                <td><div class="table_content">2</div></td>
                                                <td>
                                                    <div class="table_content">
                                                        <span class="c_black">IIsc脑博瑞国际脑力训练中心（成都）</span>
                                                        <br>
                                                        <span class="ff_num fs_12">2018/12/12</span>
                                                    </div>
                                                </td>
                                                <td><div class="table_content"><a class="c_blue">详 情</a></div></td>
                                            </tr>
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
                    var lis = [];
                    $.ajax({
                        data: postData,
                        success:function(res,ajaxStatu,xhr){
                            console.log(res)
                            profit_page++
                            isClick[id]=true
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var dom=''
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
