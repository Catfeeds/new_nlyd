<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">

            <?php if(isset($_GET['last'])){ ?>
            <a class="mui-pull-left nl-goback static" href="<?=home_url('matchs/matchWaitting/match_id/'.$_GET['match_id'])?>">
                <?php }else{ ?>
                <a class="mui-pull-left nl-goback">
                    <?php } ?>

                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title">成绩</h1>
        </header>    
            <div class="layui-row nl-border nl-content ">
                <div class="layui-tab layui-tab-brief width-margin width-margin-pc" lay-filter="tabs">
                   
                    <div class="match-title c_black"><?=$match_title?>
                        <?php if($pay_status == 2):?>
                        <a class="share" id="shareBtn">分享战绩</a>
                        <?php endif;?>
                    </div>
                   
                    <?php if($_GET['type'] != 'project'): ?>
                    <ul style="margin-left: 0" class="layui-tab-title">
                        <li class="layui-this">总排名</li>
                        <li>分类排名</li>
                        <li>单项排名</li>
                        <div class="nl-transform">总排名</div>
                    </ul>
                    <?php endif;?>
                    <div class="layui-tab-content" style="padding: 0;">
                        <!-- 总排名 -->
                        <div class="layui-tab-item layui-show">
                            <div class="nl-table-wapper">
                                <table class="nl-table">
                                    <thead>
                                        <tr class='table-head'>
                                            <td>名次</td>
                                            <td>学员姓名</td>
                                            <td><span>ID</span></td>
                                            <td>城市</td>
                                            <td><span>项目总分</span></td>
                                            <td>组别</td>
                                        </tr>
                                        <?php if(!empty($my_ranking) && $list[0]['ranking']!=$my_ranking['ranking'] ): ?>
                                        <tr class="nl-me" id="allRanking">
                                            <td>
                                                <div class="nl-circle <?= $my_ranking['ranking'] <= 3 ? 'top3' : '';?>"><?=$my_ranking['ranking']?></div>
                                            </td>
                                            <td><div class="table_content"><div class="table_content"><?=$my_ranking['user_name']?></div></td>
                                            <td id="meid"><div class="table_content c_orange"><?=$my_ranking['ID']?></div></td>
                                            <td><div class="table_content"><?=$my_ranking['city']?></div></td>
                                            <td><div class="table_content c_orange"><?=$my_ranking['score']?></div></td>
                                            <td><div class="table_content"><?=$my_ranking['group']?></div></td>
                                        </tr>
                                        <?php endif;?>
                                    </thead>
                                    <tbody id="flow">
                                        <?php if(!empty($list)){ ?>
                                        <?php foreach ($list as $k => $v){ ?>
                                        <tr class="<?= $my_ranking['ranking']==$v['ranking'] && $my_ranking['ID'] == $v['ID'] ? 'nl-me' : '';?>">
                                            <td>
                                                <div class="nl-circle <?= $k < 3 ? 'top3' : '';?>"><?=$v['ranking']?></div>
                                            </td>
                                            <td><div class="table_content"><?=$v['user_name']?></div></td>
                                            <td><div class="table_content c_orange"><?=$v['ID']?></div></td>
                                            <td><div class="table_content"><?=$v['city']?></div></td>
                                            <td><div class="table_content c_orange"><?=$v['score']?></div></td>
                                            <td><div class="table_content"><?=$v['group']?></div></td>
                                        </tr>
                                        <?php } ?>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
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
                            <div class="nl-table-wapper">
                                <table class="nl-table">
                                    <thead>
                                        <tr class='table-head'>
                                            <td>名次</td>
                                            <td>学员姓名</td>
                                            <td><span>ID</span></td>
                                            <td>城市</td>
                                            <td><span>项目总分</span></td>
                                            <td>组别</td>
                                        </tr>
                                        <tr class="nl-me" id="fenlei_me">
                                        </tr>
                                    </thead>
                                    <tbody id="flow-fenlei">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- 单项排名 -->
                        <div class="layui-tab-item">
                            <?php if(!empty($default_category)): ?>
                            <div class="btn-wrapper one-rank">
                                <div class="btn-zoo">
                                    <div class="btn-window">
                                        <div class="btn-inner-wrapper">
                                            <?php foreach ($default_category as $k =>$val){ ?>
                                            <div class="classify-btn <?=$k == 0 ? 'classify-active' : '';?>" data-post-id=<?=$val['match_project_id']?> ><?=$val['post_title']?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>    
                            </div>
                            <!-- <div class="btn-wrapper">
                                <?php foreach ($default_category as $k =>$val){ ?>
                                <div class="classify-btn <?=$k == 0 ? 'classify-active' : '';?>" data-post-id=<?=$val['match_project_id']?> ><?=$val['post_title']?></div>
                                <?php } ?>
                            </div> -->
                            <?php endif;?>
                            <div class="nl-table-wapper have-bottom-footer" style="min-height:145px;">
                                <table class="nl-table">
                                    <thead>
                                        <tr class='table-head'>
                                            <td>名次</td>
                                            <td>学员姓名</td>
                                            <td><span>ID</span></td>
                                            <td>城市</td>
                                            <td><span>项目总分</span></td>
                                            <td class="select-td">
                                                <div class="td-type">
                                                    <div class="show-type" id="show-type" data-group=""><span id="show_text">全部</span><i class="iconfont">&#xe644;</i></div>
                                                    <ul class="ul-select" >
                                                        <li class="show-type" data-group="">全部</li>
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
                            </div>
                            <a class="a-btn get_footer">查看本项目比赛详情</a>
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
    <div class="selectBox shareBox flex-h">
        <img class="share-bgs" src="<?=student_css_url.'image/share/share-bg.png'?>">
        <div class="shareItem flex1">
            <div class="shareContent shareLeft" data-id="wechatFriend">
                <div class="shareTop wechatFriend">
                    <!-- <i class="iconfont">&#xe695;</i> -->
                </div>
                <div class="shareBottom">微信好友</div>
            </div>
        </div>
        <div class="shareItem flex1">
            <div class="shareContent shareMid" data-id="wechatTimeline">
                <div class="shareTop wechatTimeline">
                    <!-- <i class="iconfont">&#xe639;</i> -->
                </div>
                <div class="shareBottom">朋友圈</div>
            </div>
        </div>
        <div class="shareItem flex1">
            <div class="shareContent shareRight" data-id="qqFriend">
                <div class="shareTop qqFriend">
                    <!-- <i class="iconfont">&#xe603;</i> -->
                </div>
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
    window.location.href=href;
})
initWidth=function() {//按钮滚动区域宽度
    var len=$('.btn-inner-wrapper .classify-btn ').length;
    var width=$('.btn-inner-wrapper .classify-btn ').width();
    var marginRight=parseInt($('.btn-inner-wrapper .classify-btn ').css('marginRight'))
    var W=width*len+marginRight*(len-1)+'px';
    $('.btn-inner-wrapper').css('width',W);
}
initWidth()
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
     element.on('tab(tabs)', function(){//tabs
        var left=$(this).position().left+parseInt($(this).css('marginLeft'));
        var html=$(this).html();
        var data_id=$(this).attr('data-id')
        $('.nl-transform').css({
            'transform':'translate3d('+left+'px, 0px, 0px)'
        }).html(html)
       
    })
    flow.load({
            elem: '#flow' //流加载容器
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                var lis = [];
                if(hasTwoPage){//第二页的数据是否存在
                    $('#allRanking').css('display','none')
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
                        $.ajax({
                            data:postData,success:function(res,ajaxStatu,xhr){  
                                if(res.success){ 
                                    $.each(res.data.info,function(index,value){
                                        var nl_me='';
                                        if(res.data.my_ranking!=null){
                                            if(value.ranking==res.data.my_ranking.ranking){
                                                nl_me='nl-me'
                                                if(value.ranking!=1){
                                                    $('#allRanking').css('display','table-row')
                                                }
                                            }
                                        }  
                                        var dom='<tr class="'+nl_me+'">'
                                                    +'<td>'
                                                        +'<div class="nl-circle">'+value.ranking+'</div>'
                                                    +'</td>'
                                                    +'<td><div class="table_content">'+value.user_name+'</div></td>'
                                                    +'<td><div class="table_content c_orange">'+value.ID+'</div></td>'
                                                    +'<td><div class="table_content">'+value.city+'</div></td>'
                                                    +'<td><div class="table_content c_orange">'+value.score+'</div></td>'
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
                                    // $.alerts('没有更多了')
                                    next(lis.join(''),false)
                                }
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
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                $('#fenlei_me').css('display','none');
                fenleiPage++
                var lis = [];
                var postData={
                    action:'get_score_ranking',
                    _wpnonce:$('#inputRank').val(),
                    match_id:$.Request('match_id'),
                    category_id:category_id,
                    page:fenleiPage
                }
                $.ajax({
                        data:postData,success:function(res,ajaxStatu,xhr){  
                        if(res.success){ 
                            if(res.data.my_ranking!=null){//我的成绩
                                var rows=res.data.my_ranking
                                var top3=rows.ranking<=3 ? "top3" : ''
                                var Html='<td>'
                                            +'<div class="nl-circle '+top3+'">'+rows.ranking+'</div>'
                                        +'</td>'
                                        +'<td><div class="table_content">'+rows.user_name+'</div></td>'
                                        +'<td><div class="table_content c_orange">'+rows.ID+'</div></td>'
                                        +'<td><div class="table_content">'+rows.city+'</div></td>'
                                        +'<td><div class="table_content c_orange">'+rows.score+'</div></td>'
                                        +'<td><div class="table_content">'+rows.group+'</div></td>'
                                // $('#fenlei_me').html(Html)
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
                                            +'<td><div class="table_content c_orange">'+value.ID+'</div></td>'
                                            +'<td><div class="table_content">'+value.city+'</div></td>'
                                            +'<td><div class="table_content c_orange">'+value.score+'</div></td>'
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
                            next(lis.join(''),false)
                        }
                    }
                }) 
            }
        });
  }
  initFenlei(0,$('.fenlei .classify-active').attr('data-post-id'))
   initDanxiang=function(fenleiPage,project_id,age_group){
       
       flow.load({
            elem: '#flow-one' //流加载容器
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                $('#danxiang_me').css('display','none');
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
                $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res){
                    if(res.success){ 
                        if(res.data.my_ranking!=null){//我的成绩
                            var rows=res.data.my_ranking
                            var top3=rows.ranking<=3 ? "top3" : ''
                            var Html='<td>'
                                            +'<div class="nl-circle '+top3+'">'+rows.ranking+'</div>'
                                        +'</td>'
                                        +'<td><div class="table_content">'+rows.user_name+'</div></td>'
                                        +'<td><div class="table_content c_orange">'+rows.ID+'</div></td>'
                                        +'<td><div class="table_content">'+rows.city+'</div></td>'
                                        +'<td><div class="table_content c_orange">'+rows.score+'</div></td>'
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
                                        $('#danxiang_me').html(Html).css('display','table-row');
                                    }
                                }
                            }  
                            var dom='<tr class="'+nl_me+'">'
                                        +'<td>'
                                            +'<div class="nl-circle '+top3+'">'+value.ranking+'</div>'
                                        +'</td>'
                                        +'<td><div class="table_content">'+value.user_name+'</div></td>'
                                        +'<td><div class="table_content c_orange">'+value.ID+'</div></td>'
                                        +'<td><div class="table_content">'+value.city+'</div></td>'
                                        +'<td><div class="table_content c_orange">'+value.score+'</div></td>'
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
                        next(lis.join(''),false)
                    }
                }) 
            }
        });
    }
    initDanxiang(0,$('.one-rank .classify-active').attr('data-post-id'))
})
$('body').click(function(e){
    if(!$(e.target).hasClass('show-type')&&$(e.target).parents('.show-type').length<=0){
        $('.ul-select').removeClass('ul-select-show')
    }
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
    var select= $("#show_text").text()
    var thisText=_this.text();
    _this.parents('td').find('.ul-select').toggleClass("ul-select-show");
    if(select!=thisText){
        if(_this.attr('id')!='show-type' || !_this.attr('id')){
            var data_group=_this.attr('data-group')
            $('#flow-one').empty();
            $('#show_text').text(thisText).attr('data-group',data_group)
            initDanxiang(0,$('.one-rank .classify-active').attr('data-post-id'),data_group)
        }
    }
})

})
</script>