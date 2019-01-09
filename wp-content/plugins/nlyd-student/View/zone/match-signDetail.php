
<style>
@media screen and (max-width: 1199px){
    #page{
        background-color:#f6f6f6!important;
    }
}
</style>

<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback nl-goback static" href="<?=home_url('/zone/match/');?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('报名签到表', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="match_sign_row width-padding width-padding-pc">
                    <div class="bold fs_16 c_black">2018脑力世界杯战队精英赛</div>
                    <div class="flex-h mt_10">
                        <div class="flex1">
                            <span class="c_blue"><?=__('报 名', 'nlyd-student')?>：</span>
                            <span class="ff_num">18</span>
                        </div>
                        <div class="flex1 ">
                            <span class="c_blue"><?=__('签 到', 'nlyd-student')?>：</span>
                            <span class="ff_num">18</span>
                        </div>
                    </div>
                </div>
                <div class="nl-table-wapper layui-bg-white">
                    <table class="nl-table">
                        <tbody id="coach_flow">
                            <tr class='table-head'>
                                <td><?=__('头 像', 'nlyd-student')?></td>
                                <td><?=__('姓 名', 'nlyd-student')?></td>
                                <td><?=__('性 别', 'nlyd-student')?></td>
                                <td><?=__('年 龄', 'nlyd-student')?></td>
                                <td><?=__('签 到', 'nlyd-student')?></td>
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
                                                '<td><div class="table_content"><div class="img-box coach_img"><img src="'+v.work_photo+'"></div></div></td>'+
                                                '<td><div class="table_content">'+v.real_name+'</div></td>'+
                                                '<td><div class="table_content c_black">'+v.user_gender+'</div></td>'+
                                                '<td><div class="table_content c_black">'+v.user_age+'</div></td>'+
                                                '<td><div class="table_content c_green">已签</div></td>'+
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


