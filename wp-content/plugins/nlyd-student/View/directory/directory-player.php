<style>
@media screen and (max-width: 1199px){
    #page {
        top: 130px;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav system-list system-teacher">
                <a class="mui-pull-left nl-goback">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <div class="item-wrapper">
                    <div class="center-detail">
                        <div class="system-font">
                            <p><?=__('脑力健将名录', 'nlyd-student')?></p>
                            <p>BRAIN POWER</p> 
                        </div>
                    </div>
                </div>  
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding width-padding-pc contentP-wrapper">
                    <div class="nl-table-wapper">
                        <table class="nl-table" >
                            <thead>
                                <tr>
                                    <td><div class="table_content"><?=__('头像', 'nlyd-student')?></div></td>
                                    <td><div class="table_content"><?=__('学员姓名', 'nlyd-student')?></div></td>
                                    <td><div class="table_content"><?=__('ID', 'nlyd-student')?></div></td>
                                    <td><div class="table_content"><?=__('性 别', 'nlyd-student')?></div></td>
                                    <td><div class="table_content"><?=__('类&nbsp;&nbsp;&nbsp;&nbsp;别', 'nlyd-student')?></div></td>
                                    <td><div class="table_content"><?=__('级 别', 'nlyd-student')?></div></td>
                                    <td><div class="table_content"> <?=__('主训教练', 'nlyd-student')?></div></td>
                                    <td><div class="table_content"><?=__('国 籍', 'nlyd-student')?></div></td>
                                </tr>
                            </thead>
                            <tbody id="flow-table">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
jQuery(function($) {   
    layui.use(['layer','flow'], function(){
        var flow = layui.flow;//流加载
//--------------------分页--------------------------
        flow.load({
            elem: '#flow-table' //流加载容器
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                var postData={
                    action:'getDirectories',
                    page:page,
                    type:1,//1脑力健将,2国际速读水平认证,3国际心算水平认证,4国际记忆水平认证
                }
                var lis = [];
                $.ajax({
                    data:postData,success:function(res,ajaxStatu,xhr){
                        if(res.success){
                            $.each(res.data.info,function(index,value){
                                var real_name=value.real_name ? value.real_name :'-';
                                var userID=value.userID ? value.userID :'-';
                                var sex=value.sex ? value.sex :'-';
                                var category_name=value.category_name ? value.category_name :'-';
                                var level=value.level ? value.level :'-';
                                var coach_name=value.coach_name ? value.coach_name :'-';
                                var ranges=value.ranges ? value.ranges :'-';
                                var header_img=value.header_img ? value.header_img :'';
                                var dom='<tr>'
                                            +'<td>'
                                                +'<div class="player-img img-box">'
                                                    +'<img src="'+header_img+'">'
                                                +'</div>'
                                            +'</td>'
                                            +'<td><div class="table_content">'+real_name+'</div></td>'
                                            +'<td><div class="table_content">'+userID+'</div></td>'
                                            +'<td><div class="table_content">'+sex+'</div></td>'
                                            +'<td><div class="table_content">'+category_name+'</div></td>'
                                            +'<td><div class="table_content">'+level+'</div></td>'
                                            +'<td><div class="table_content">'+coach_name+'</div></td>'
                                            +'<td><div class="table_content">'+ranges+'</div></td>'
                                        +'</tr>';
                                lis.push(dom)
                            })
                            if (res.data.info.length<10) {
                                next(lis.join(''),false)
                            }else{
                                next(lis.join(''),true)
                            }
                        }else{
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