
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12  detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
                <div><i class="iconfont">&#xe610;</i></div>
            </a>
            <h1 class="mui-title">
            <div><?=__('战队管理', 'nlyd-student')?></div>
            </h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <?php if(!empty($id)){?>
                    <style>
                        @media screen and (max-width: 1199px){
                            #page{
                                background-color:#f6f6f6!important;
                            }
                        }
                    </style>
                    <div class="team_row width-padding layui-row layui-bg-white width-margin-pc" style="margin-top: 0;">
                        <div class="team_row_title">
                            <span class="bold fs_16 c_black"><?=$post_title?> </span>
                            <a class="fs_12 c_blue" href="<?=home_url('/zone/teamBuild/');?>"><?=__('编辑资料', 'nlyd-student')?></a>
                        </div>
                        <div class="team_row_tag"><span class="c_yellow mr_10"><i class="iconfont">&#xe658;</i></span><?=__($team_slogan, 'nlyd-student')?></div>
                        <div class="c_black fs_14 ti_28 mt_10">
                            <?=$team_brief?>
                        </div>
                    </div>

                    <div class="team_row width-padding layui-row layui-bg-white width-margin-pc">
                        <div class="team_row_title">
                            <span>
                                <span class="bold fs_16 c_black"><?=__('战队成员', 'nlyd-student')?>（1）</span>
                                <a class="c_orange fs_12" href="<?=home_url('/zone/teamApply/');?>"><?=__('新的申请', 'nlyd-student')?>（1）</a>
                            </span>
                            <a class="fs_12 c_blue" href="<?=home_url('/zone/teamAddMember/');?>"><?=__('添加成员', 'nlyd-student')?></a>
                        </div>
                    </div>
                    <div class="layui-row layui-bg-white">
                        <div class="nl-table-wapper">
                            <table class="nl-table">
                           
                                <tbody id="team_flow">
                                    <tr class='table-head'>
                                        <td><?=__('序 号', 'nlyd-student')?></td>
                                        <td><?=__('姓名/编号', 'nlyd-student')?></td>
                                        <td><?=__('年 龄', 'nlyd-student')?></td>
                                        <td><?=__('性 别', 'nlyd-student')?></td>
                                        <td><?=__('联系方式', 'nlyd-student')?></td>
                                        <td><?=__('操 作', 'nlyd-student')?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>    
                    </div>
                <?php }else{ ?>
                    <div class="no-info-page layui-row">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noTeam1094@2x.png'?>">
                        </div>
                        <p class="no-info-text"><?=__('您还没有战队', 'nlyd-student')?></p>
                        <a href="<?=home_url('/zone/teamBuild/');?>" class="a-btn a-btn-table"><div><?=__('创建战队', 'nlyd-student')?></div></a>
                    </div>
                <?php } ?>
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
                        action:'get_team_personnel',
                        page:team_page,
                        team_id:$.Request('team_id'),
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
                                                '<td><div class="table_content"><span class="c_black">王好学</span><br><span class="ff_num fs_12">10000888</span></div></td>'+
                                                '<td><div class="table_content">18</div></td>'+
                                                '<td><div class="table_content c_black">男</div></td>'+
                                                '<td><div class="table_content c_black">刘亿亿</div></td>'+
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
        pagation('team_flow',1)

    });
})
</script>

