
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
        <a class="mui-pull-left nl-goback">
            <i class="iconfont">&#xe610;</i>
        </a>
        <h1 class="mui-title">单项成绩排名</h1>
        </header>
            <div class="layui-row nl-border nl-content ">
                <div class="width-margin width-margin-pc">
                    <div class="match-title c_black"><?=$post_title?><div class="share" id="shareBtn">分享我的战绩</div></div>
                    <div class="single-match-title">
                        <div class="single-match-name"><?=$match_title?></div>
                        <?php if($match_more > 0):?>
                        <?php for ($i=0;$i<$match_more;++$i){?>
                        <div class="single-match-lun <?=$i==0?'lun-active':'';?>" data-post-id="<?=$i+1?>">第<?=chinanum($i+1)?>轮</div>
                        <?php } ?>
                        <?php endif; ?>
                    </div>
                        <table class="nl-table">
                            <thead>
                                <tr class='table-head'>
                                    <td>名次</td>
                                    <td>学员姓名</td>
                                    <td>ID</td>
                                    <td>城市</td>
                                    <td>项目总分</td>
                                    <td>组别</td>
                                </tr>  
                                <tr class="nl-me" id="danxiang_me">
                          
                                </tr>  
                            </thead>
                            <tbody id="flow-one">
                               
                            </tbody>
                        </table>
                    </div>
                    <div class="a-btn"  href="<?=$answer_url?>">查看本轮我的答题记录</div>
                </div> 
            </div>
        </div>           
    </div>
</div>
<!-- 底部弹出框 -->
<div class="selectBottom">
    <div class="grayLayer cancel"></div>
    <div class="selectBox shareBox">
        <img class="share-bgs" src="<?=student_css_url.'image/share/share-bg.png'?>">
        <div class="shareItem">
            <div class="shareContent shareLeft" data-id="wechatFriend">
                <div class="shareTop"><i class="iconfont">&#xe695;</i></div>
                <div class="shareBottom">微信好友</div>
            </div>
        </div>
        <div class="shareItem">
            <div class="shareContent shareMid" data-id="wechatTimeline">
                <div class="shareTop"><i class="iconfont">&#xe639;</i></div>
                <div class="shareBottom">朋友圈</div>
            </div>
        </div>
        <div class="shareItem">
            <div class="shareContent shareRight" data-id="qqFriend">
                <div class="shareTop"><i class="iconfont">&#xe603;</i></div>
                <div class="shareBottom">QQ</div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="_wpnonce" id="inputRank" value="<?=wp_create_nonce('student_get_ranking_code_nonce');?>">
<script>
jQuery(function($) { 
share();
$('.a-btn').click(function(){
    var _this=$(this);
    var hrefs=_this.attr('href');
    var match_more=$('.lun-active').attr('data-post-id');
    var newHref=hrefs+"/match_more/"+match_more;
    window.location.href=newHref;
})
layui.use(['element','flow'], function(){
    var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
    var flow = layui.flow;//流加载
    initDanxiang=function(fenleiPage){
       flow.load({
            elem: '#flow-one' //流加载容器
            ,scrollElem: '#flow-one' //滚动条所在元素，一般不用填，此处只是演示需要。
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                $('#danxiang_me').css('display','none');
                fenleiPage++
                var lis = [];
                var postData={
                    action:'get_score_ranking',
                    _wpnonce:$('#inputRank').val(),
                    match_id:<?=$_GET['match_id']?>,
                    project_id:<?=$_GET['project_id']?>,
                    match_more:$('.lun-active').attr('data-post-id'),
                    page:fenleiPage
                }
                $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res){
                    if(res.success){
                        if(res.data.my_ranking!=null){//我的成绩
                            var rows=res.data.my_ranking
                              var Html='<td>'
                                            +'<div class="nl-circle">'+rows.ranking+'</div>'
                                        +'</td>'
                                        +'<td><div class="table_content">'+rows.user_name+'</div></td>'
                                        +'<td><div class="table_content">'+rows.ID+'</div></td>'
                                        +'<td><div class="table_content">'+rows.city+'</div></td>'
                                        +'<td><div class="table_content">'+rows.score+'</div></td>'
                                        +'<td><div class="table_content">'+rows.group+'</div></td>'
                            // $('#danxiang_me').html(Html)
                        }
                        $.each(res.data.info,function(index,value){
                            var top3=value.ranking<=3 ? 'top3' : '';
                            var nl_me='';
                            if(res.data.my_ranking!=null){
                                if(value.ranking==res.data.my_ranking.ranking){
                                    nl_me='nl-me'
                                    if(value.ranking!=1){
                                        $('#fenlei_me').html(Html).css('display','table-row');
                                    }
                                }
                            }  
                            var dom='<tr class="'+nl_me+'">'
                                        +'<td>'
                                            +'<div class="nl-circle '+top3+'">'+value.ranking+'</div>'
                                        +'</td>'
                                        +'<td><div class="table_content">'+value.user_name+'</div></td>'
                                        +'<td><div class="table_content">'+value.ID+'</div></td>'
                                        +'<td><div class="table_content">'+value.city+'</div></td>'
                                        +'<td><div class="table_content">'+value.score+'</div></td>'
                                        +'<td><div class="table_content">'+value.group+'</div></td>'
                                    +'</tr>'
                            lis.push(dom)                           
                        })  
                        if (res.data.info.length<10) {
                            next(lis.join(''),false)
                        }else{
                            next(lis.join(''),true)
                        }
                    }else{
                        // if(fenleiPage==1){
                        //     var dom='<tr><td colspan="6">暂无数据</td></tr>'
                        //     lis.push(dom) 
                        // }else{
                        //     $.alerts('没有更多了')
                        // }
                        next(lis.join(''),false)
                    }
                }) 
            }
        });
    }
    initDanxiang(0)

})
$('#shareBtn').click(function(){//分享按钮
    $('.selectBottom').addClass('selectBottom-show')
})
$('.selectBottom').on('click','.cancel',function(){
    $(this).parents('.selectBottom').removeClass('selectBottom-show');
})
$('.single-match-lun').click(function(){
    var _this=$(this);
    if(!_this.hasClass('lun-active')){
        _this.parents('.single-match-title').find('.single-match-lun').removeClass('lun-active')
        _this.addClass('lun-active')
        $('#flow-one').empty();
        initDanxiang(0)
    }

})

})
</script>