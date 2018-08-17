<style>
@media screen and (max-width: 991px){
    #page {
        top: 130px;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav system-list system-match">
                <a class="mui-pull-left nl-goback">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <div class="item-wrapper">
                    <div class="center-detail">
                        <div class="system-font">
                            <p>脑力战队名录</p>
                            <p>BRAIN TEAM</p> 
                        </div>
                    </div>
                </div>  
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding width-padding-pc contentP-wrapper">
                    <table class="nl-table">
                        <tbody id="flow-table">
                            <tr>
                                <td>战队名称</td>
                                <td>战队负责人</td>
                                <td>战队口号</td>
                                <td>战队成员</td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 战队分页 -->
<input type="hidden" name="_wpnonce" id="getTeam" value="<?=wp_create_nonce('student_get_team_code_nonce');?>">
<script>
jQuery(function($) {   
    layui.use(['layer','flow'], function(){
        var flow = layui.flow;//流加载
//--------------------分页--------------------------
        flow.load({
                elem: '#flow-table' //流加载容器
                ,scrollElem: '#flow-table' //滚动条所在元素，一般不用填，此处只是演示需要。
                ,isAuto: false
                ,isLazyimg: true
                ,done: function(page, next){ //加载下一页
                    //模拟插入
                        var postData={
                            action:'get_team_lists',
                            _wpnonce:$("#getTeam").val(),
                            page:page
                        }
                        var lis = [];
                        $.post(window.admin_ajax,postData,function(res){
                                if(res.success){
                                    // 战队状态 -3:已退出;-2:已拒绝;-1:退队申请;1:入队申请;2:我的战队  
                                    $.each(res.data.info,function(index,value){
                                        
                                        var dom='<tr>'
                                                    +'<td><span>'+value.post_title+'</span></td>'
                                                    +'<td><span>'+value.team_leader+'</span></td>'
                                                    +'<td><span>'+value.team_slogan+'</span></td>'
                                                    +'<td><span>'+value.team_total+'</span></td>'
                                                +'</tr>'
                                        lis.push(dom)                           
                                    })  
                                    
                                    if (res.data.info.length<10) {
                                        next(lis.join(''),false) 
                                    }else{
                                        next(lis.join(''),true) 
                                    } 
                                }else{
                                    if(page==1){
                                        var dom='<tr><td colspan="4">无教练信息</td></tr>'
                                        lis.push(dom)     
                
                                        next(lis.join(''),false)
                                    }else{
                                        $.alerts('没有更多了')
                                        next(lis.join(''),false)
                                    }
                                    
                                }
                        })         
                }
            });
 //--------------------分页--------------------------  
    })
})
</script>