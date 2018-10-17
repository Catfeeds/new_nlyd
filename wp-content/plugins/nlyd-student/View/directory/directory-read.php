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
            <header class="mui-bar mui-bar-nav system-list system-course">
                <a class="mui-pull-left nl-goback">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <div class="item-wrapper">
                    <div class="center-detail">
                        <div class="system-font">
                            <p><?=__('速读水平认证名录', 'nlyd-student')?></p>
                            <p>SPEED READING</p> 
                        </div>
                    </div>
                </div>  
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding width-padding-pc contentP-wrapper">
                    <div class="nl-table-wapper">
                        <table class="nl-table">
                            <thead>
                                <tr>
                                    <td><?=__('头像', 'nlyd-student')?></td>
                                    <td><?=__('学员姓名', 'nlyd-student')?></td>
                                    <td><?=__('ID', 'nlyd-student')?></td>
                                    <td><?=__('性别', 'nlyd-student')?></td>
                                    <td><?=__('速读级别', 'nlyd-student')?></td>
                                    <td><?=__('主训教练', 'nlyd-student')?></td>
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
                    type:2,//1脑力健将,2国际速读水平认证,3国际心算水平认证,4国际记忆水平认证
                }
                var lis = [];
                $.ajax({
                    data:postData,success:function(res,ajaxStatu,xhr){
                        console.log(res)
                        if(res.success){
                            $.each(res.data.info,function(index,value){
                                var real_name=value.real_name ? value.real_name :'-';
                                var user_id=value.userID ? value.userID :'-';
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
                                            +'<td>'+real_name+'</td>'
                                            +'<td>'+user_id+'</td>'
                                            +'<td>'+sex+'</td>'
                                            +'<td>'+level+'</td>'
                                            +'<td>'+coach_name+'</td>'
                                        +'</tr>';
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
                    }
                })
            }
        });
 //--------------------分页--------------------------  
    })
})
</script>