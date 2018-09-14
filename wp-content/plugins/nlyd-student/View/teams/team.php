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
            <a class="mui-pull-left nl-goback">
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title">
            战队列表
            </h1>
        </header>
            <div class="layui-row nl-border nl-content">
            <?php if($row){?>
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad1.png'?>"></div>
                            </div>
                            <div class="swiper-slide">
                                <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad2.png'?>"></div>
                            </div>
                            <div class="swiper-slide">
                                <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad3.png'?>"></div>
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                    <div class="width-margin width-margin-pc layui-row flow-default" id="team-flow">
                        <p class="team-tips">*每位学员仅可加入1个战队，要加入新战队需退出旧的战队后申请加入</p>
                    </div>
                <?php }else{ ?>
                    <div class="no-info-page layui-row">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noTeam1094@2x.png'?>">
                        </div>
                        <p class="no-info-text">暂无任何战队相关</p>
                    </div>
                <?php } ?>
            </div>
        </div>           
    </div>
</div>

<!-- 加入战队 -->
<input type="hidden" name="_wpnonce" id="setTeam" value="<?=wp_create_nonce('student_set_team_code_nonce');?>">
<!-- 战队分页 -->
<input type="hidden" name="_wpnonce" id="getTeam" value="<?=wp_create_nonce('student_get_team_code_nonce');?>">

<script>
jQuery(function($) {     
    var mySwiper = new Swiper('.swiper-container', {
        loop : true,
        autoplay:{
            disableOnInteraction:false
        },//可选选项，自动滑动
        autoplayDisableOnInteraction : false,
        initialSlide :0,//初始展示页
        pagination: {
            el: '.swiper-pagination',
            dynamicBullets: true,
            dynamicMainBullets: 2,
            clickable :true,
        },
    });  
layui.use(['layer','flow'], function(){
    var flow = layui.flow;//流加载
    //建造实例
    // carousel.render({//轮播
    //     elem: '#test1'
    //     ,width: '100%' //设置容器宽度
    //     ,arrow: 'none' //始终显示箭头
    //     ,height:'172px'
    //     ,interval:'2000'//自动切换的时间间隔
    // });
//--------------------分页--------------------------
    flow.load({
        elem: '#team-flow' //流加载容器
        ,isAuto: false
        ,isLazyimg: true
        ,done: function(page, next){ //加载下一页
            //模拟插入
                var postData={
                    action:'get_team_lists',
                    _wpnonce:$("#getTeam").val(),
                    page:page
                }
                var lis = [];
                $.ajax({
                    data:postData,success:function(res,ajaxStatu,xhr){  
                    console.log(res)
                        if(res.success){
                            // 战队状态 -3:已退出;-2:已拒绝;-1:退队申请;1:入队申请;2:我的战队  
                            $.each(res.data.info,function(index,value){
                                var statue='<div class="team-join canJoin" data-id="'+value.ID+'">'
                                            +'<i class="iconfont">&#xe761;</i>'
                                        +'</div>';//可加入按钮
                                var wait=""
                                if(typeof(value.user_id)!='undefined'&&value.user_id!=null&&value.user_id.length>0){
                                    if(parseInt(value.status)==-1){//退队申请
                                        statue='<div class="team-join myTeam" data-id="'+value.ID+'">'
                                                    +'<i class="iconfont">&#xe62f;</i>'
                                                +'</div>' 
                                        wait='<div class="team-detail-row waitting">'
                                                    +'<span class="team-info">退队申请审核中</span>'
                                                +'</div>'
                                    }else if(parseInt(value.status)==2){//我的战队
                                        statue='<div class="team-join myTeam" data-id="'+value.ID+'">'
                                                    +'<i class="iconfont">&#xe608;</i>'
                                                +'</div>'  
                                    }else if(parseInt(value.status)==1){//入队申请审核中
                                        statue='<div class="team-join waitting" data-id="'+value.ID+'">'
                                                    +'<i class="iconfont">&#xe62f;</i>'
                                                +'</div>'  
                                        wait='<div class="team-detail-row waitting">'
                                                +'<span class="team-info">入队申请审核中</span>'
                                            +'</div>'
                                    }
                                    
                                }
                                var dom='<a class="team-row" href="'+value.team_url+'">'+statue
                                            +'<div class="team-detail">'
                                                +'<div class="team-detail-row">'
                                                    +'<span class="fs_16 c_blue">'+value.post_title+'</span>'
                                                +'</div>'
                                                +'<div class="team-detail-row">'
                                                    +'<span class="team-info-label">战队负责人：</span>'
                                                    +'<span class="team-info">'+value.team_director+'</span>'
                                                +'</div>'
                                                +'<div class="team-detail-row">'
                                                    +'<span class="team-info-label">战队口号：</span>'
                                                    +'<span class="team-info">'+value.team_slogan+'</span>'
                                                +'</div>'
                                                +'<div class="team-detail-row">'
                                                    +'<span class="team-info-label">战队成员：</span>'
                                                    +'<span class="team-info">'+value.team_total+'人</span>'
                                                +'</div>'+wait
                                            +'</div>'
                                        +'</a>'   
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
    $('body').on('click','.canJoin',function(){
        var _this=$(this);
            layer.open({
            type: 1
            ,maxWidth:300
            ,title: '提示' //不显示标题栏
            ,skin:'nl-box-skin'
            ,id: 'certification' //防止重复弹出
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
                $.ajax({
                    data:data,success:function(res,ajaxStatu,xhr){  //设为主训教练
                        $.alerts(res.data.info)
                        if(res.success){
                        wait='<div class="team-detail-row waitting">'
                                +'<span class="team-info">入队申请审核中</span>'
                            +'</div>'
                        _this.removeClass('canJoin').addClass('waitting').html('<i class="iconfont">&#xe62f;</i>');
                        _this.parents('.team-row').find('.team-detail').append(wait)
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