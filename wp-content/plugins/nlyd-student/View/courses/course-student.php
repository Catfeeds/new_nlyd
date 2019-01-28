
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback nl-goback static" href="<?=home_url('/courses/cenerCourse/id/'.$_GET['center_id']);?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('课程学员', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content">
                <div class="nl-table-wapper">
                    <div class="c_black width-padding width-padding-pc bold fs_16 student_title"><?=$course_title?>111</div>
                    <table class="nl-table">
                        <tbody id="course_row">
                            <tr class='table-head'>
                                <td><?=__('序 号', 'nlyd-student')?></td>
                                <td><?=__('姓名/编号', 'nlyd-student')?></td>
                                <td><?=__('年 龄', 'nlyd-student')?></td>
                                <td><?=__('性 别', 'nlyd-student')?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>           
    </div>
</div>
<script>
jQuery(function($) { 
    var course_id=$.Request('id');
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
                        action:'get_course_student_list',
                        id:course_id,
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
                                                '<td><div class="table_content">'+v.order+'</div></td>'+
                                                '<td><div class="table_content"><div class="c_black ta_c">'+v.real_name+'</div><div class="ff_num fs_12 ta_c">'+v.user_ID+'</div></div></td>'+
                                                '<td><div class="table_content c_black">'+v.user_age+'</div></td>'+
                                                '<td><div class="table_content c_black">'+v.user_gender+'</div></td>'+
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
        pagation('course_row',1)

    });
})
</script>



