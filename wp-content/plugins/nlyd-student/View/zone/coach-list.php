
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('教练管理', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="nl-table-wapper">
                    <table class="nl-table">
                        <tbody id="coach_flow">
                            <tr class='table-head'>
                                <td><?=__('序 号', 'nlyd-student')?></td>
                                <td><?=__('工作照', 'nlyd-student')?></td>
                                <td><?=__('姓名/ID', 'nlyd-student')?></td>
                                <td><?=__('年 龄', 'nlyd-student')?></td>
                                <td><?=__('性 别', 'nlyd-student')?></td>
                                <td><?=__('操 作', 'nlyd-student')?></td>
                            </tr>
                          
                        </tbody>
                    </table>
                    <a href="<?=home_url('/zone/coachAdd/');?>" class="a-btn a-btn-table"><div><?=__('添加教练', 'nlyd-student')?></div></a>
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
        function pagation(id,team_page){
            flow.load({
                elem: '#'+id //流加载容器
                ,isAuto: false
                ,isLazyimg: true
                ,done: function(page, next){ //加载下一页
                    var postData={
                        action:'zone_coach_list',
                        page:team_page,
                    }
                    var lis = [];
                    $.ajax({
                        data: postData,
                        success:function(res,ajaxStatu,xhr){
                            console.log(res)
                            team_page++
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var dom= '<tr>'+
                                                '<td><div class="table_content">1</div></td>'+
                                                '<td><div class="table_content"><div class="img-box coach_img"><img src="<?=student_css_url.'image/nlyd.png'?>"></div></div></td>'+
                                                '<td><div class="table_content">刘亿亿<br>10001</div></td>'+
                                                '<td><div class="table_content c_black">18</div></td>'+
                                                '<td><div class="table_content c_black">男</div></td>'+
                                                '<td><div class="table_content"><a class="c_blue" href="">详 情</a></div></td>'+
                                            '</tr>'
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
        pagation('coach_flow',1)

    });
})
</script>


