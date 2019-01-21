
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
                <h1 class="mui-title"><div><?=__('结课成绩', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="nl-table-wapper">
                    <div class="c_black width-padding width-padding-pc bold fs_16 courseEnd_title"><?=__('高效记忆术·G预报班·成都郫县学员结课成绩', 'nlyd-student')?></div>
                    <table class="nl-table">
                        <tbody id="course_end">
                            <tr class='table-head'>
                                <td><?=__('姓名/ID', 'nlyd-student')?></td>
                                <td><?=__('技 能', 'nlyd-student')?></td>
                                <td><?=__('分 享', 'nlyd-student')?></td>
                                <td><?=__('学费补贴', 'nlyd-student')?></td>
                                <td><?=__('奖 励', 'nlyd-student')?></td>
                            </tr>
                            <tr>
                                <td><div class="table_content"><span class="c_black">王好学</span><br><span class="ff_num fs_12">10000888</span></div></td>
                                <td><div class="table_content c_green"><?=__('达标', 'nlyd-student')?></td>
                                <td><div class="table_content c_green"><?=__('达标', 'nlyd-student')?></div></td>
                                <td><div class="table_content c_orange">3000.00</div></td>
                                <td><div class="table_content c_black">500.00</div></td>
                            </tr>
                            <tr>
                                <td><div class="table_content"><span class="c_black">王好学</span><br><span class="ff_num fs_12">10000888</span></div></td>
                                <td><div class="table_content"><?=__('未达标', 'nlyd-student')?></td>
                                <td><div class="table_content"><?=__('未达标', 'nlyd-student')?></div></td>
                                <td><div class="table_content">--</div></td>
                                <td><div class="table_content">--</div></td>
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
    var _id=$.Request('id');
    layui.use(['element','flow'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        var flow = layui.flow;//流加载
        function pagation(id,match_page){
            flow.load({
                elem: '#'+id //流加载容器
                ,isAuto: false
                ,isLazyimg: true
                ,done: function(page, next){ //加载下一页
                    var postData={
                        action:'get_class_ending',
                        page:match_page,
                        id:_id,
                    }
                    var lis = [];
                    $.ajax({
                        data: postData,
                        success:function(res,ajaxStatu,xhr){
                            console.log(res)
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var dom='<tr>'
                                                +'<td><div class="table_content"><span class="c_black">王好学</span><br><span class="ff_num fs_12">10000888</span></div></td>'
                                                +'<td><div class="table_content c_green"><?=__('达标', 'nlyd-student')?></td>'
                                                +'<td><div class="table_content c_green"><?=__('达标', 'nlyd-student')?></div></td>'
                                                +'<td><div class="table_content c_orange">3000.00</div></td>'
                                                +'<td><div class="table_content c_black">500.00</div></td>'
                                            +'</tr>'
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
                            match_page++
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
        pagation('course_end',1)
        
    })
})
</script>
