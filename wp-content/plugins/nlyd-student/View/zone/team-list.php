<style>
@media screen and (max-width: 1199px){
    #content,.detail-content-wrapper{
        background:#f6f6f6;
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
                    <div class="layui-row flow-default" id="team-flow">
                        <div class="team_type_name width-padding width-padding-pc"><?=__('赛区默认战队', 'nlyd-student')?></div>
                        <a class="team_list_row layui-row width-padding width-padding-pc c_black" href="<?=home_url('/zone/settingCashWechat/');?>" style="margin-bottom:0">
                            <div class="left_name c_black"><?=__('收款二维码', 'nlyd-student')?></div>
                            <div class="right_tips c_orange">1个新的申请</div>
                            <div class="right_icoin"><i class="iconfont fs_20">&#xe727;</i></div>
                        </a>
                        <div class="team_type_name width-padding width-padding-pc"><?=__('赛区其他参赛（团体）战队', 'nlyd-student')?></div>
                        <a class="team_list_row layui-row width-padding width-padding-pc c_black" href="<?=home_url('/zone/settingCashWechat/');?>">
                            <div class="left_name c_black"><?=__('收款二维码', 'nlyd-student')?></div>
                            <div class="right_tips c_orange">1个新的申请</div>
                            <div class="right_icoin"><i class="iconfont fs_20">&#xe727;</i></div>
                        </a>
                        <a class="team_list_row layui-row width-padding width-padding-pc c_black" href="<?=home_url('/zone/settingCashWechat/');?>">
                            <div class="left_name c_black"><?=__('收款二维码', 'nlyd-student')?></div>
                            <div class="right_tips c_orange">1个新的申请</div>
                            <div class="right_icoin"><i class="iconfont fs_20">&#xe727;</i></div>
                        </a>
                    </div>
                <?php }else{ ?>
                    <div class="no-info-page layui-row">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noTeam1094@2x.png'?>">
                        </div>
                        <p class="no-info-text"><?=__('暂无任何战队相关', 'nlyd-student')?></p>
                       
                    </div>
                <?php } ?>
                <a class="a-btn a-btn-table" href="<?=home_url('/zone/teamBuild/type/child');?>"><div><?=__('新建战队', 'nlyd-student')?></div></a>
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
                        action:'get_zone_teams',
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
                                    var url=window.home_url+'/zone/teamDetail/team_id/'+value.team_id
                                    var team_type='';
                                    switch (index) {
                                        case 0:
                                            team_type='<div class="team_type_name width-padding width-padding-pc"><?=__("赛区默认战队", "nlyd-student")?></div>'
                                            break;
                                        case 1:
                                            team_type='<div class="team_type_name width-padding width-padding-pc"><?=__("赛区其他参赛（团体）战队", "nlyd-student")?></div>'
                                            break;
                                        default:
                                            break;
                                    }
                                   
                                    var dom='<a class="team_list_row layui-row width-padding width-padding-pc c_black" href="'+url+'">'
                                                +'<div class="left_name c_black"><?=__("收款二维码", "nlyd-student")?></div>'
                                                +'<div class="right_tips c_orange">1个新的申请</div>'
                                                +'<div class="right_icoin"><i class="iconfont fs_20">&#xe727;</i></div>'
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