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
                <div><i class="iconfont">&#xe610;</i></div>
            </a>
            <h1 class="mui-title">
            <div><?=__('战队列表', 'nlyd-student')?></div>
            </h1>
        </header>
            <div class="layui-row nl-border nl-content">
            <?php if($row){?>
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="swiper-content img-box" style="display:block"><img src="<?=student_css_url.'image/homePage/ad1.png'?>"></div>
                            </div>
                            <div class="swiper-slide">
                                <div class="swiper-content img-box" style="display:block"><img src="<?=student_css_url.'image/homePage/ad2.png'?>"></div>
                            </div>
                            <div class="swiper-slide">
                                <div class="swiper-content img-box" style="display:block"><img src="<?=student_css_url.'image/homePage/ad3.png'?>"></div>
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                    <div class="width-margin width-margin-pc layui-row">
                        <p class="team-tips">*<?=__('每位用户仅可加入1个战队，要加入新战队需退出旧的战队后申请加入', 'nlyd-student')?></p>
                        <div class="search-zoo" style="margin-bottom:10px">
                            <i class="iconfont search-Icon">&#xe63b;</i>
                            <div class="search-btn bg_gradient_blue"><span><?=__('搜 索', 'nlyd-student')?></span></div>
                            <input type="text" class="serach-Input nl-foucs" placeholder="<?=__('搜索战队', 'nlyd-student')?>">
                        </div>
                    </div>
                    <div class="width-margin width-margin-pc grid flow-default" id="team-flow">
                        
                    </div>
                <?php }else{ ?>
                    <div class="no-info-page layui-row">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noTeam1094@2x.png'?>">
                        </div>
                        <p class="no-info-text"><?=__('暂无任何战队相关', 'nlyd-student')?></p>
                    </div>
                <?php } ?>
            </div>
        </div>           
    </div>
</div>
<!-- 搜索 -->
<input type="hidden" name="_wpnonce" id="searchTeam" value="<?=wp_create_nonce('student_get_team_search_code_nonce');?>">
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
                            if(res.success){
                                // 战队状态 -3:已退出;-2:已拒绝;-1:退队申请;1:入队申请;2:我的战队  
                                $.each(res.data.info,function(index,value){
                                    var statue='<div class="team-join canJoin" data-id="'+value.ID+'" data-name="'+value.post_title+'">'
                                                +'<i class="iconfont">&#xe761;</i>'
                                            +'</div>';//可加入按钮
                                    var wait=""
                                    if(typeof(value.user_id)!='undefined'&&value.user_id!=null&&value.user_id.length>0){
                                        if(parseInt(value.status)==-1){//退队申请
                                            statue='<div class="team-join myTeam" data-id="'+value.ID+'">'
                                                        +'<i class="iconfont">&#xe62f;</i>'
                                                    +'</div>' 
                                            wait='<div class="team-detail-row waitting">'
                                                        +'<span class="team-info"><?=__('退队申请审核中', 'nlyd-student')?></span>'
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
                                                    +'<span class="team-info"><?=__('入队申请审核中', 'nlyd-student')?></span>'
                                                +'</div>'
                                        }
                                        
                                    }
                                    var dom='<a class="team-row" href="'+value.team_url+'">'+statue
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
                                                    +'</div>'+wait
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

if($('.search-btn').length>0){
    new AlloyFinger($('.search-btn')[0], {
        touchStart: function () {
            $('.search-btn').addClass("opacity");
        },
        touchMove: function () {
            $('.search-btn').removeClass("opacity");
        },
        touchEnd: function () {
            $('.search-btn').removeClass("opacity");
        },
        touchCancel: function () {
            $('.search-btn').removeClass("opacity");
        },
        tap: function () {
            var _this=$('.search-btn');
            var value=$('.serach-Input').val()
            if(value!=searchValue){
                searchValue=value;
                $('#team-flow').empty()
                pagation()
            }
        }
    });
}
 //--------------------分页--------------------------  
    $('body').on('click','.canJoin',function(){
        var _this=$(this);
            layer.open({
            type: 1
            ,maxWidth:300
            ,title: '提示' //不显示标题栏
            ,skin:'nl-box-skin'
            ,id: 'certification' //防止重复弹出
            ,content: '<div class="box-conent-wrapper"><?=__('是否确认加入', 'nlyd-student')?>'+_this.attr('data-name')+'？</div>'
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
                $.ajax({
                    data:data,success:function(res,ajaxStatu,xhr){  //设为主训教练
                        $.alerts(res.data.info)
                        if(res.success){
                        wait='<div class="team-detail-row waitting">'
                                +'<span class="team-info"><?=__('入队申请审核中', 'nlyd-student')?></span>'
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