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
                            <a class="nl-team-btn" id="team-leave" data-id="<?=$team['ID']?>"><?=__('退出战队', 'nlyd-student')?></a>
                            <?php }elseif($team['status'] == 1){?>
                                <span class="nl-team-step"><?=__('战队申请审核中', 'nlyd-student')?></span>
                            <?php }elseif ($team['status'] == -1){?>
                                <span class="nl-team-step"><?=__('离队申请审核中', 'nlyd-student')?></span>
                            <?php } ?>
                            <!-- <span class="nl-team-step">战队申请审核中</span> -->
                        </div>
                        <div class="teamDetail-infoRow">
                            <span><?=__('战队负责人', 'nlyd-student')?>：</span>
                            <span><?=$team['team_director']?></span>
                        </div>
                        <div class="teamDetail-infoRow">
                            <span><?=__('战队口号', 'nlyd-student')?>：</span>
                            <span><?=$team['team_slogan']?></span>
                        </div>
                        <div class="teamDetail-infoRow">
                            <span><?=__('战队成员', 'nlyd-student')?>：</span>
                            <span><?=$team['team_total']?>人</span>
                        </div>
                    </div>
                </div>
                <?php if(!empty($team['post_content'])):?>
                <div class="teamDetail-row width-padding-pc have-metal layui-row layui-bg-white">
                    <div class="teamDetail-metal"><?=__('简 介', 'nlyd-student')?></div>
                    <div class="width-padding team-info-detail"><?=$team['post_content']?></div>
                </div>
                <?php endif;?>
                <div class="teamDetail-row layui-row layui-bg-white">
                    <div class="layui-tab layui-tab-brief width-margin width-margin-pc" lay-filter="tabs">
                        <ul style="margin-left: 0" class="layui-tab-title">
                            <li class="layui-this"><?=__('普通队员', 'nlyd-student')?></li>
                            <li><?=__('认证教练', 'nlyd-student')?></li>
                            <div class="nl-transform"><?=__('普通队员', 'nlyd-student')?></div>
                        </ul>
                        <div class="teamDetail-top">*<?=__('M、R、A分别代表记忆、速读、心算', 'nlyd-student')?></div>
                        <div class="layui-tab-content" style="padding: 0;">
                            <!-- 普通队员 -->
                            <div class="layui-tab-item layui-show" id="student">
                                <div class="nl-table-wapper">
                                    <table class="nl-table">
                                        <thead>
                                            <tr>
                                                <td><?=__('头像', 'nlyd-student')?></td>
                                                <td><?=__('学员姓名', 'nlyd-student')?></td>
                                                <td><?=__('学员ID', 'nlyd-student')?></td>
                                                <td><?=__('M级别', 'nlyd-student')?></td>
                                                <td><?=__('R级别', 'nlyd-student')?></td>
                                                <td><?=__('A级别', 'nlyd-student')?></td>
                                                <td><?=__('脑力健将级别', 'nlyd-student')?></td>
                                            </tr>
                                        </thead>
                                        <tbody  id="flow-table">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- 认证教练 -->
                            <div class="layui-tab-item"  id="coach">
                                <div class="nl-table-wapper">
                                    <table class="nl-table">
                                        <thead>
                                            <tr>
                                                <td><?=__('头像', 'nlyd-student')?></td>
                                                <td><?=__('学员姓名', 'nlyd-student')?></td>
                                                <td><?=__('学员ID', 'nlyd-student')?></td>
                                                <td><?=__('M级别', 'nlyd-student')?></td>
                                                <td><?=__('R级别', 'nlyd-student')?></td>
                                                <td><?=__('A级别', 'nlyd-student')?></td>
                                                <td><?=__('脑力健将级别', 'nlyd-student')?></td>
                                            </tr>
                                        </thead>
                                        <tbody  id="flow-table1">

                                        </tbody>   
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if(!in_array($team['status'],array(-1,1,2))): ?>
                <a class="a-btn" id="team-join" data-id="<?=$team['ID']?>"><?=__('加入战队', 'nlyd-student')?></a>
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
    element.on('tab(tabs)', function(){//tabs
        var left=$(this).position().left+parseInt($(this).css('marginLeft'));
        var html=$(this).html();
        var data_id=$(this).attr('data-id')
        $('.nl-transform').css({
            'transform':'translate3d('+left+'px, 0px, 0px)'
        }).html(html)
    })
//-----------------------普通队员分页-------------------
    flow.load({
        elem: '#flow-table' //流加载容器。
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
                $.ajax({
                    data:postData,
                    success:function(res,ajaxStatu,xhr){ 
                        if(res.success){
                            $.each(res.data.info,function(index,value){
                                var dom='<tr>'
                                            +'<td>'
                                                +'<div class="player-img">'
                                                    +'<img src="'+value.user_head+'">'
                                                +'</div>'
                                            +'</td>'
                                            +'<td><div class="table_content">'+value.nickname+'</div></td>'
                                            +'<td><div class="table_content">'+value.user_ID+'</div></td>'
                                            +'<td><div class="table_content">'+value.memory+'</div></td>'
                                            +'<td><div class="table_content">'+value.read+'</div></td>'
                                            +'<td><div class="table_content">'+value.compute+'</div></td>'
                                            +'<td><div class="table_content">'+value.mental+'</div></td>'
                                        +'</tr>'
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
                    },
                    error:function(){
                        $.alerts('<?=__('网络质量差,请重试', 'nlyd-student')?>')
                        next(lis.join(''),true)
                    }
                })         
        }
    }); 
//-----------------------认证教练分页-------------------
    flow.load({
        elem: '#flow-table1' //流加载容器
        // ,isAuto: false
        // ,isLazyimg: true
        ,done: function(page, next){ //加载下一页
            //模拟插入
                var postData={
                    action:'get_team_member',
                    team_id:<?=$team['ID']?>,
                    type:2,//类别 1:队员;2:教练
                    page:page
                }
                var lis = [];
                $.ajax({
                    data:postData,
                    success:function(res,ajaxStatu,xhr){ 
                        if(res.success){ 
                            $.each(res.data.info,function(index,value){
                                var dom='<tr>'
                                            +'<td>'
                                                +'<div class="player-img">'
                                                    +'<img src="'+value.user_head+'">'
                                                +'</div>'
                                            +'</td>'
                                            +'<td><div class="table_content">'+value.nickname+'</div></td>'
                                            +'<td><div class="table_content">'+value.user_ID+'</div></td>'
                                            +'<td><div class="table_content">'+value.memory+'</div></td>'
                                            +'<td><div class="table_content">'+value.read+'</div></td>'
                                            +'<td><div class="table_content">'+value.compute+'</div></td>'
                                            +'<td><div class="table_content">'+value.mental+'</div></td>'
                                        +'</tr>'
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
                    },
                    error:function(){
                        $.alerts('<?=__('网络质量差,请重试', 'nlyd-student')?>')
                        next(lis.join(''),true)
                    }
                })         
        }
    }); 
    $('body').on('click','#team-join',function(){
            var _this = $(this);
            layer.open({
            type: 1
            ,maxWidth:300
            ,title: '<?=__('提示', 'nlyd-student')?>' //不显示标题栏
            ,skin:'nl-box-skin'
            ,id: 'team-join1' //防止重复弹出
            ,content: '<div class="box-conent-wrapper"><?=printf(__('是否确认加入%s', 'nlyd-student'), $team['post_title'])?>？</div>'
            ,btn: ['<?=__('再想想', 'nlyd-student')?>', '<?=__('确认', 'nlyd-student')?>', ]
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
                $.ajax({//设为主训教练
                    data:data,success:function(res,ajaxStatu,xhr){ 
                        $.alerts(res.data.info)
                        if(res.success){
                            _this.css('display','none');
                            $('.nl-team-name').after('<span class="nl-team-step"><?=__('入队申请审核中', 'nlyd-student')?></span>')
                        }
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
                ,title: '<?=__('提示', 'nlyd-student')?>' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'team-leave1' //防止重复弹出
                ,content: '<div class="box-conent-wrapper"><?=printf(__('是否确认退出%s', 'nlyd-student'),$team['post_title'])?>？</div>'
                ,btn: ['<?=__('再想想', 'nlyd-student')?>', '<?=__('确认', 'nlyd-student')?>', ]
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
                    $.ajax({
                        data:data,success:function(res,ajaxStatu,xhr){ 
                            $.alerts(res.data.info)
                            if(res.success){
                                _this.after('<span class="nl-team-step"><?=__('离队申请审核中', 'nlyd-student')?></span>').css('display','none');
                            }
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