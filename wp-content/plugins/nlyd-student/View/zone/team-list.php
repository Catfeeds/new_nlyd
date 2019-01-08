<style>
@media screen and (max-width: 1199px){
    #content,.detail-content-wrapper{
        background:#fff;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12  detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback static" href="<?=home_url('/zone/');?>">
                <div><i class="iconfont">&#xe610;</i></div>
            </a>
            <h1 class="mui-title">
            <div><?=__('战队列表', 'nlyd-student')?></div>
            </h1>
        </header>
            <div class="layui-row nl-border nl-content have-bottom">
            <?php if(!$row){?>
                    <div class="width-margin width-margin-pc layui-row flow-default" id="team-flow" style="margin-top:15px">
                        
                    </div>
                <?php }else{ ?>
                    <div class="no-info-page layui-row">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noTeam1094@2x.png'?>">
                        </div>
                        <p class="no-info-text"><?=__('暂无任何战队相关', 'nlyd-student')?></p>
                       
                    </div>
                <?php } ?>
                <a class="a-btn a-btn-table" href="<?=home_url('/zone/teamBuild/');?>"><div><?=__('新建战队', 'nlyd-student')?></div></a>
            </div>
        </div>           
    </div>
</div>
<!-- 战队分页 -->
<input type="hidden" name="_wpnonce" id="getTeam" value="<?=wp_create_nonce('student_get_team_code_nonce');?>">

<script>
jQuery(function($) {      
var searchValue=""; 
layui.use(['layer','flow'], function(){
    var flow = layui.flow;//流加载
//--------------------分页--------------------------
        function pagation() {
            flow.load({
                elem: '#team-flow' //流加载容器
                ,isAuto: false
                ,isLazyimg: true
                ,done: function(page, next){ //加载下一页
                    //模拟插入
                    var postData={
                        action:'getTeamsBySearch',
                        _wpnonce:$('#searchTeam').val(),
                        page:page,
                        search:searchValue,
                    }
                    var lis = [];
                    $.ajax({
                        data:postData,
                        success:function(res,ajaxStatu,xhr){  
                            console.log(res)
                            if(res.success){
                                // 战队状态 -3:已退出;-2:已拒绝;-1:退队申请;1:入队申请;2:我的战队  
                                $.each(res.data.info,function(index,value){
                                    var url=window.home_url+'/zone/team/team_id/'+value.ID
                                    var dom='<a class="team-row" href="'+url+'">'
                                                +'<div class="team-detail">'
                                                    +'<div class="team-detail-row">'
                                                        +'<span class="fs_16 c_blue">'+value.post_title+'</span>'
                                                    +'</div>'
                                                    +'<div class="team-detail-row">'
                                                        +'<span class="team-info-label"><?=__('战队负责人', 'nlyd-student')?>:</span>'
                                                        +'<span class="team-info">'+value.team_director+'</span>'
                                                    +'</div>'
                                                    +'<div class="team-detail-row">'
                                                        +'<span class="team-info-label"><?=__('战队口号', 'nlyd-student')?>:</span>'
                                                        +'<span class="team-info">'+value.team_slogan+'</span>'
                                                    +'</div>'
                                                    +'<div class="team-detail-row">'
                                                        +'<span class="team-info-label"><?=__('战队成员', 'nlyd-student')?>:</span>'
                                                        +'<span class="team-info">'+value.team_total+'<?=__('人', 'nlyd-student')?></span>'
                                                    +'</div>'
                                                +'</div>'
                                            +'</a>'   
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
								$.alerts('<?=__('网络质量差,请重试', 'nlyd-student')?>')
								next(lis.join(''),true)
							}
                        }
                    })         
                }
            });
        }
        pagation()
 //--------------------分页--------------------------  
    })

})
</script>