<style>
 @media screen and (max-width: 991px){
    #content,.detail-content-wrapper{
        background:#eee;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_left_path.'leftMenu.php';
            
        ?>

        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title">战队详情</h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="teamDetail-row layui-row layui-bg-white">
                    <div class="width-margin width-margin-pc">
                        <div class="teamDetail-infoRow">
                            <span class="nl-team-name"><?=$team['post_title']?></span>
                            <?php if($team['status'] == 2){?>
                            <span class="nl-team-btn" id="team-leave" data-id="<?=$team['ID']?>">退出战队</span>
                            <?php }elseif($team['status'] == 1){?>
                                <span class="nl-team-step">战队申请审核中</span>
                            <?php }elseif ($team['status'] == -1){?>
                                <span class="nl-team-step">离队申请审核中</span>
                            <?php } ?>
                            <!-- <span class="nl-team-step">战队申请审核中</span> -->
                        </div>
                        <div class="teamDetail-infoRow">
                            <span class="nl-grey-font">战队负责人：</span>
                            <span class="nl-grey-font"><?=$team['team_director']?></span>
                        </div>
                        <div class="teamDetail-infoRow">
                            <span class="nl-grey-font">战队口号：</span>
                            <span class="nl-grey-font"><?=$team['team_slogan']?></span>
                        </div>
                        <div class="teamDetail-infoRow">
                            <span class="nl-grey-font">战队成员：</span>
                            <span class="nl-grey-font"><?=$team['team_total']?>人</span>
                        </div>
                    </div>
                </div>
                <?php if(!empty($team['post_content'])):?>
                <div class="teamDetail-row width-padding-pc have-metal layui-row layui-bg-white">
                    <div class="teamDetail-metal">简 介</div>
                    <p class="width-padding"><?=$team['post_content']?></p>
                </div>
                <?php endif;?>
                <div class="teamDetail-row layui-row layui-bg-white">
                    <div class="layui-tab layui-tab-brief width-margin width-margin-pc" lay-filter="tabs">
                        <ul style="margin-left: 0" class="layui-tab-title">
                            <li class="layui-this">普通队员</li>
                            <li>认证教练</li>
                        </ul>
                        <div class="teamDetail-top">*M、R、A分别代表记忆、速读、心算</div>
                        <div class="layui-tab-content" style="padding: 0;">
                            <!-- 普通队员 -->
                            <div class="layui-tab-item layui-show" id="student">
                                <table class="nl-table">
                                    <tbody  id="flow-table">
                                        <tr>
                                            <td>头像</td>
                                            <td>学员姓名</td>
                                            <td>学员ID</td>
                                            <td>M级别</td>
                                            <td>R级别</td>
                                            <td>A级别</td>
                                            <td>脑力健将级别</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- 认证教练 -->
                            <div class="layui-tab-item"  id="coach">
                                <table class="nl-table">
                                    <tbody  id="flow-table1">
                                        <tr>
                                            <td>头像</td>
                                            <td>学员姓名</td>
                                            <td>学员ID</td>
                                            <td>M级别</td>
                                            <td>R级别</td>
                                            <td>A级别</td>
                                            <td>脑力健将级别</td>
                                        </tr>
                                    </tbody>   
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if(!in_array($team['status'],array(-1,1,2))): ?>
                <div class="a-btn" id="team-join" data-id="<?=$team['ID']?>">加入战队</div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>

<!-- 加入/退出战队 -->
<input type="hidden" name="_wpnonce" id="setTeam" value="<?=wp_create_nonce('student_set_team_code_nonce');?>">

<script>
jQuery(function($) { 
layui.use(['element','layer','flow'], function(){
    var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
    var flow = layui.flow;//流加载
//-----------------------普通队员分页-------------------
    flow.load({
        elem: '#flow-table' //流加载容器
        ,scrollElem: '#flow-table' //滚动条所在元素，一般不用填，此处只是演示需要。
        ,isAuto: false
        ,isLazyimg: true
        ,done: function(page, next){ //加载下一页
            //模拟插入
                var postData={
                    action:'get_team_member',
                    team_id:<?=$team['ID']?>,
                    type:1,//类别 1:队员;2:教练
                    page:page
                }
                var lis = [];
                $.post(window.admin_ajax,postData,function(res){
                        if(res.success){
                            $.each(res.data.info,function(index,value){
                                var dom='<tr>'
                                            +'<td>'
                                                +'<div class="player-img">'
                                                    +'<img src="'+value.user_head+'">'
                                                +'</div>'
                                            +'</td>'
                                            +'<td>'+value.nickname+'</td>'
                                            +'<td>'+value.user_ID+'</td>'
                                            +'<td >'+value.memory+'</td>'
                                            +'<td>'+value.read+'</td>'
                                            +'<td>'+value.compute+'</td>'
                                            +'<td>'+value.mental+'</td>'
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
                                var dom='<tr><td colspan="7">无队员信息</td></tr>'
                                lis.push(dom) 
                            }else{
                                $.alerts('没有更多了')
                            }
                            next(lis.join(''),false)
                        }
                })         
        }
    }); 
//-----------------------认证教练分页-------------------
    flow.load({
        elem: '#flow-table1' //流加载容器
        ,scrollElem: '#flow-table1' //滚动条所在元素，一般不用填，此处只是演示需要。
        ,isAuto: false
        ,isLazyimg: true
        ,done: function(page, next){ //加载下一页
            //模拟插入
                var postData={
                    action:'get_team_member',
                    team_id:<?=$team['ID']?>,
                    type:2,//类别 1:队员;2:教练
                    page:page
                }
                var lis = [];
                $.post(window.admin_ajax,postData,function(res){
                        if(res.success){ 
                            $.each(res.data.info,function(index,value){
                                var dom='<tr>'
                                            +'<td>'
                                                +'<div class="player-img">'
                                                    +'<img src="'+value.user_head+'">'
                                                +'</div>'
                                            +'</td>'
                                            +'<td>'+value.nickname+'</td>'
                                            +'<td>'+value.user_ID+'</td>'
                                            +'<td >'+value.memory+'</td>'
                                            +'<td>'+value.read+'</td>'
                                            +'<td>'+value.compute+'</td>'
                                            +'<td>'+value.mental+'</td>'
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
                                var dom='<tr><td colspan="7">无教练信息</td></tr>'
                                lis.push(dom) 
                            }else{
                                $.alerts('没有更多了')
                            }
                            next(lis.join(''),false)
                        }
                })         
        }
    }); 
    $('body').on('click','#team-join',function(){
            var _this = $(this);
            layer.open({
            type: 1
            ,maxWidth:300
            ,title: '提示' //不显示标题栏
            ,skin:'nl-box-skin'
            ,id: 'team-join1' //防止重复弹出
            ,content: '<div class="box-conent-wrapper">是否确认加入大爱长青国际脑力战队？</div>'
            ,btn: ['再想想', '确认', ]
            ,success: function(layero, index){
                
            }
            ,yes: function(index, layero){
                layer.closeAll();
            }
            ,btn2: function(index, layero){
                var id=_this.attr('data-id')
                var _wpnonce=$('#setTeam').val();
                var data={
                    action:'set_team',
                    _wpnonce:_wpnonce,
                    team_id:id,
                    handle:'join',//操作 join:入队 其他:离队
                };
                $.post(window.admin_ajax,data,function(res){//设为主训教练
                    console.log(data)
                    console.log(res)
                    $.alerts(res.data.info)
                    if(res.success){
                        _this.css('display','none');
                        $('.nl-team-name').after('<span class="nl-team-step">入队申请审核中</span>')
                    }
                })
            }
            ,closeBtn:2
            ,btnAagn: 'c' //按钮居中
            ,shade: 0.3 //遮罩
            ,isOutAnim:true//关闭动画
        });
        return false
    })

        $('body').on('click','#team-leave',function(){//退出战队
            var _this = $(this);
            layer.open({
                type: 1
                ,maxWidth:300
                ,title: '提示' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'team-leave1' //防止重复弹出
                ,content: '<div class="box-conent-wrapper">是否确认退出大爱长青国际脑力战队？</div>'
                ,btn: ['再想想', '确认', ]
                ,success: function(layero, index){
                    
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    var id=_this.attr('data-id')
                    var _wpnonce=$('#setTeam').val();
                    var data={
                        action:'set_team',
                        _wpnonce:_wpnonce,
                        team_id:id,
                        handle:'leave',//操作 join:入队 其他:离队
                    };
                    $.post(window.admin_ajax,data,function(res){//
                        console.log(res)
                        $.alerts(res.data.info)
                        if(res.success){
                            _this.after('<span class="nl-team-step">离队申请审核中</span>').css('display','none');
                        }
                    })
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
            return false
        })
})

})
</script>