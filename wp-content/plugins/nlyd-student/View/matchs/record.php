
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
            <h1 class="mui-title">成绩</h1>
        </header>    
            <div class="layui-row nl-border nl-content ">
                <div class="layui-tab layui-tab-brief width-margin width-margin-pc" lay-filter="tabs">
                    <div class="match-title"><?=$match_title?><a class="share" id="shareBtn">分享我的战绩</a></div>
                    <?php if($_GET['type'] != 'project'): ?>
                    <ul style="margin-left: 0" class="layui-tab-title">
                        <li class="layui-this">总排名</li>
                        <li>分类排名</li>
                        <li>单项排名</li>
                    </ul>
                    <?php endif;?>
                    <div class="layui-tab-content" style="padding: 0;">
                        <!-- 总排名 -->
                        <div class="layui-tab-item layui-show">
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
                                    <?php if(!empty($my_ranking)): ?>
                                    <tr class="nl-me">
                                        <td>
                                            <div class="nl-circle"><?=$my_ranking['ranking']?></div>
                                        </td>
                                        <td><?=$my_ranking['user_name']?></td>
                                        <td id="meid"><?=$my_ranking['ID']?></td>
                                        <td><?=$my_ranking['city']?></td>
                                        <td><?=$my_ranking['score']?></td>
                                        <td><?=$my_ranking['group']?></td>
                                    </tr>
                                    <?php endif;?>
                                </thead>
                                <tbody id="flow">
                                    <?php if(!empty($list)){ ?>
                                    <?php foreach ($list as $k => $v){ ?>
                                    <tr>
                                        <td>
                                            <div class="nl-circle <?= $k<2 ? 'top3' : '';?>"><?=$v['ranking']?></div>
                                        </td>
                                        <td><?=$v['user_name']?></td>
                                        <td><?=$v['ID']?></td>
                                        <td><?=$v['city']?></td>
                                        <td><?=$v['score']?></td>
                                        <td><?=$v['group']?></td>
                                    </tr>
                                    <?php } ?>
                                    <?php }else{ ?>
                                        <tr><td colspan="6">暂无数据</td></tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- 分类排名 -->
                        <div class="layui-tab-item">
                            <div class="btn-wrapper">
                                <?phP if(!empty($match_category)): ?>
                                <?php foreach ($match_category as $k => $v){ ?>
                                <div class="btn-wrap fenlei">
                                    <div class="classify-btn <?=$k==0 ? 'classify-active' : '';?>" data-post-id="<?=$v['ID']?>"><?=$v['post_title']?></div>
                                </div>
                                <?php }?>
                                <?php endif;?>
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
                                    <tr class="nl-me" id="fenlei_me">
                                    </tr>
                                </thead>
                                <tbody id="flow-fenlei">
                                   
                                </tbody>
                            </table>
                        </div>
                        <!-- 单项排名 -->
                        <div class="layui-tab-item">
                            <?php if(!empty($default_category)): ?>
                            <div class="btn-wrapper one-rank">
                                <?php foreach ($default_category as $k =>$val){ ?>
                                <div class="classify-btn <?=$k == 0 ? 'classify-active' : '';?>" data-post-id=<?=$val['ID']?> ><?=$val['post_title']?></div>
                                <?php } ?>
                            </div>
                            <?php endif;?>
                            <table class="nl-table">
                                <thead>
                                    <tr class='table-head'>
                                        <td>名次</td>
                                        <td>学员姓名</td>
                                        <td>ID</td>
                                        <td>城市</td>
                                        <td>项目总分</td>
                                        <td class="select-td">
                                            <div class="td-type">
                                                <div class="show-type" id="show-type" data-group="">全部<i class="iconfont">&#xe644;</i></div>
                                                <ul class="ul-select" >
                                                    <li class="show-type" data-group="">全部<i class="iconfont">&#xe644;</i></li>
                                                    <?php
                                                        $group = get_age_group();
                                                        foreach ($group as $k =>$y){
                                                    ?>
                                                    <li class="show-type" data-group="<?=$k?>"><?=$y;?></li>
                                                    <?php }?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="nl-me"  id="danxiang_me">
                                    
                                    </tr>
                                </thead>
                                <tbody id="flow-one">
                       
                                </tbody>
                            </table>
                            <div class="a-btn">查看本项目比赛详情</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>           
    </div>
</div>
<!-- 底部弹出框 -->
<div class="selectBottom">
    <div class="grayLayer cancel"></div>
    <div class="selectBox shareBox">
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
$('#shareBtn').click(function(){//分享按钮
    $('.selectBottom').addClass('selectBottom-show')
})
$('.selectBottom').on('click','.cancel',function(){
    $(this).parents('.selectBottom').removeClass('selectBottom-show');
})
$('.a-btn').click(function(){//查看本项目比赛详情
    var match_id=<?=$_GET['match_id']?>;
    var project_id=$('.one-rank .classify-active').attr('data-post-id')
    var href=window.home_url+'/matchs/singleRecord/match_id/'+match_id+'/project_id/'+project_id;
    window.location.href=href
})
layui.use(['element','flow'], function(){
    var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
    var flow = layui.flow;//流加载
    var hasTwoPage=false;
    var userid=$('#meid').text();
     <?php 
     if($count >10 ): 
     ?>
     hasTwoPage=true;
     <?php 
    endif;
    ?>
    flow.load({
            elem: '#flow' //流加载容器
            ,scrollElem: '#flow' //滚动条所在元素，一般不用填，此处只是演示需要。
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                var lis = [];
                if(hasTwoPage){//第二页的数据是否存在
                    if(page==1){
                        next(lis.join(''),true)
                    }else{
                        var cocah_id=$.Request('coach_id');
                        var postData={
                            action:'get_score_ranking',
                            _wpnonce:$('#inputRank').val(),
                            match_id:$.Request('match_id'),
                            page:page
                        }
                        $.post(window.admin_ajax,postData,function(res){
                            if(res.success){ 
                                $.each(res.data.info,function(index,value){
                                    var dom='<tr>'
                                                +'<td>'
                                                    +'<div class="nl-circle">'+value.ranking+'</div>'
                                                +'</td>'
                                                +'<td>'+value.user_name+'</td>'
                                                +'<td>'+value.ID+'</td>'
                                                +'<td>'+value.city+'</td>'
                                                +'<td>'+value.score+'</td>'
                                                +'<td>'+value.group+'</td>'
                                            +'</tr>'
                                    lis.push(dom)                           
                                })
                                if (res.data.info.length<10) {
                                    next(lis.join(''),false)
                                }else{
                                    next(lis.join(''),true)
                                }
                            }else{
                                $.alerts('没有更多了')
                                next(lis.join(''),false)
                            }
                        }) 
                    }
                }else{
                    next(lis.join(''),false)
                }
            }
        });

  initFenlei=function(fenleiPage,category_id) {
        flow.load({
            elem: '#flow-fenlei' //流加载容器
            ,scrollElem: '#flow-fenlei' //滚动条所在元素，一般不用填，此处只是演示需要。
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                fenleiPage++
                var lis = [];
                var postData={
                    action:'get_score_ranking',
                    _wpnonce:$('#inputRank').val(),
                    match_id:$.Request('match_id'),
                    category_id:category_id,
                    page:fenleiPage
                }
                $.post(window.admin_ajax,postData,function(res){
                    if(res.success){ 
                        $('#fenlei_me').empty();
                        if(res.data.my_ranking!=null){//我的成绩
                            var rows=res.data.my_ranking
                              var fenlei_meHtml='<td>'
                                                    +'<div class="nl-circle">'+rows.ranking+'</div>'
                                                +'</td>'
                                                +'<td>'+rows.user_name+'</td>'
                                                +'<td>'+rows.ID+'</td>'
                                                +'<td>'+rows.city+'</td>'
                                                +'<td>'+rows.score+'</td>'
                                                +'<td>'+rows.group+'</td>'
                            $('#fenlei_me').html(fenlei_meHtml)
                        }
                        $.each(res.data.info,function(index,value){
                            var top3=value.ranking<=3 ? 'top3' : '';
                            var nl_me='';
                            if(res.data.my_ranking!=null){
                                if(value.ranking==res.data.my_ranking.ranking){
                                    nl_me='nl-me'
                                }
                            }  
                            var dom='<tr class="'+nl_me+'">'
                                        +'<td>'
                                            +'<div class="nl-circle '+top3+'">'+value.ranking+'</div>'
                                        +'</td>'
                                        +'<td>'+value.user_name+'</td>'
                                        +'<td>'+value.ID+'</td>'
                                        +'<td>'+value.city+'</td>'
                                        +'<td>'+value.score+'</td>'
                                        +'<td>'+value.group+'</td>'
                                    +'</tr>'
                            lis.push(dom)                           
                        })  
                        if (res.data.info.length<10) {
                            next(lis.join(''),false)
                        }else{
                            next(lis.join(''),true)
                        }
                    }else{
                        if(fenleiPage==1){
                            var dom='<tr><td colspan="6">暂无数据</td></tr>'
                            lis.push(dom) 
                        }else{
                            $.alerts('没有更多了')
                        }
                        next(lis.join(''),false)
                    }
                }) 
            }
        });
  }
  initFenlei(0,$('.fenlei .classify-active').attr('data-post-id'))
   initDanxiang=function(fenleiPage,project_id,age_group){
       flow.load({
            elem: '#flow-one' //流加载容器
            ,scrollElem: '#flow-one' //滚动条所在元素，一般不用填，此处只是演示需要。
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                fenleiPage++
                var lis = [];
                var postData={
                    action:'get_score_ranking',
                    _wpnonce:$('#inputRank').val(),
                    match_id:$.Request('match_id'),
                    project_id:project_id,
                    page:fenleiPage
                }
                
                if(typeof(age_group)!='undefined'){
                    postData.age_group=age_group;
                }
                console.log(postData)
                $.post(window.admin_ajax,postData,function(res){
                    if(res.success){ 
                        $('#danxiang_me').empty();
                        if(res.data.my_ranking!=null){//我的成绩
                            var rows=res.data.my_ranking
                              var danxiang_meHtml='<td>'
                                                    +'<div class="nl-circle">'+rows.ranking+'</div>'
                                                +'</td>'
                                                +'<td>'+rows.user_name+'</td>'
                                                +'<td>'+rows.ID+'</td>'
                                                +'<td>'+rows.city+'</td>'
                                                +'<td>'+rows.score+'</td>'
                                                +'<td>'+rows.group+'</td>'
                            $('#danxiang_me').html(danxiang_meHtml)
                        }
                        $.each(res.data.info,function(index,value){
                            var top3=value.ranking<=3 ? 'top3' : '';
                            var nl_me='';
                            if(res.data.my_ranking!=null){
                                if(value.ranking==res.data.my_ranking.ranking){
                                    nl_me='nl-me'
                                }
                            }  
                            var dom='<tr class="'+nl_me+'">'
                                        +'<td>'
                                            +'<div class="nl-circle '+top3+'">'+value.ranking+'</div>'
                                        +'</td>'
                                        +'<td>'+value.user_name+'</td>'
                                        +'<td>'+value.ID+'</td>'
                                        +'<td>'+value.city+'</td>'
                                        +'<td>'+value.score+'</td>'
                                        +'<td>'+value.group+'</td>'
                                    +'</tr>'
                            lis.push(dom)                           
                        })  
                        if (res.data.info.length<10) {
                            next(lis.join(''),false)
                        }else{
                            next(lis.join(''),true)
                        }
                    }else{
                        if(fenleiPage==1){
                            var dom='<tr><td colspan="6">暂无数据</td></tr>'
                            lis.push(dom) 
                        }else{
                            $.alerts('没有更多了')
                        }
                        next(lis.join(''),false)
                    }
                }) 
            }
        });
    }
    initDanxiang(0,$('.one-rank .classify-active').attr('data-post-id'))
})

$('.classify-btn').click(function(){//选择比赛项目
    var _this=$(this);
    if(!_this.hasClass('classify-active')){
        _this.parents('.btn-wrapper').find('.classify-btn').removeClass('classify-active');
        _this.addClass('classify-active');
        if(_this.parents('.btn-wrapper').hasClass('one-rank')){//单项排名
            var id=_this.attr('data-post-id');
            $('#flow-one').empty();
            initDanxiang(0,id,$('#show-type').attr('data-group'))
        }else{//分类排名
            var id=_this.attr('data-post-id');
            $('#flow-fenlei').empty();
            initFenlei(0,id)
            
        }
    }
})
$('.show-type').click(function(){//下拉
    var _this=$(this);
    var select= $(this).parents('td').find('.show-type').eq(0).html()
    var thisHtml=_this.html();
    _this.parents('td').find('.ul-select').toggleClass("ul-select-show");
    if(select!=thisHtml){
        var data_group=_this.attr('data-group')
        $('#flow-one').empty();
        $('#show-type').html(thisHtml).attr('data-group',data_group)
        initDanxiang(0,$('.one-rank .classify-active').attr('data-post-id'),data_group)
    }
})

})
</script>