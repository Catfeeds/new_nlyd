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

                <div><i class="iconfont">&#xe610;</i></div>
            </a>
            <h1 class="mui-title"><div><?=__('成绩', 'nlyd-student')?></div></h1>
        </header>    
            <div class="layui-row nl-border nl-content ">
                <div class="layui-tab layui-tab-brief width-margin width-margin-pc" lay-filter="tabs">
                   
                    <div class="match-title c_black"><?=$match_title?>
                        <?php if($pay_status == 2):?>
                        <a class="share" id="shareBtn"><?=__('分享战绩', 'nlyd-student')?></a>
                        <?php endif;?>
                    </div>
                   
                    <?php if($_GET['type'] != 'project'): ?>
                    <ul style="margin-left: 0" class="layui-tab-title">
                        <li class="layui-this" data-id="1"><?=__('单项排名', 'nlyd-student')?></li>
                        <li data-id="2"><?=__('分类排名', 'nlyd-student')?></li>
                        <li data-id="3"><?=__('总排名', 'nlyd-student')?></li>
                        <div class="nl-transform"><?=__('单项排名', 'nlyd-student')?></div>
                    </ul>
                    <?php endif;?>
                    <div class="layui-tab-content" style="padding: 0;">
                        <!-- 单项排名 -->
                        <div class="layui-tab-item layui-show">
                            <?php if(!empty($default_category)): ?>
                            <div class="btn-wrapper one_1">
                                <div class="btn-zoo">
                                    <div class="btn-window">
                                        <div class="btn-inner-wrapper">
                                            <?php foreach ($default_category as $k =>$val){ ?>
                                            <div class="classify-btn <?=$k == 0 ? 'classify-active' : '';?>" data-post-id=<?=$val['ID']?> ><?=$val['post_title']?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>    
                            </div>
                            <?php endif;?>
                            <div class="nl-table-wapper have-bottom-footer" style="min-height:145px;">
                                <table class="nl-table">
                                    <thead>
                                        <tr class='table-head'>
                                            <td><?=__('名次', 'nlyd-student')?></td>
                                            <td><?=__('学员姓名', 'nlyd-student')?></td>
                                            <td><span><?=__('ID', 'nlyd-student')?></span></td>
                                            <td><?=__('城市', 'nlyd-student')?></td>
                                            <td><span><?=__('项目总分', 'nlyd-student')?></span></td>
                                            <td><?=__('组别', 'nlyd-student')?></td>
                                        </tr>
                                        <tr class="nl-me"  id="rank_1">
                                        
                                        </tr>
                                    </thead>
                                    <tbody id="flow_1">
                        
                                    </tbody>
                                </table>
                            </div>
                            <a class="a-btn a-btn-table get_footer"><div><?=__('查看本项目比赛详情', 'nlyd-student')?></div></a>
                        </div>
                        <!-- 分类排名 -->
                        <div class="layui-tab-item">
                            <div class="btn-wrapper one_2">
                                <?phP if(!empty($match_category)): ?>
                                <?php foreach ($match_category as $k => $v){ ?>
                                <div class="btn-wrap">
                                    <div class="classify-btn <?=$k==0 ? 'classify-active' : '';?>" data-post-id="<?=$v['ID']?>"><?=$v['post_title']?></div>
                                </div>
                                <?php }?>
                                <?php endif;?>
                            </div>
                            <div class="nl-table-wapper"  style="min-height:145px;">
                                <table class="nl-table">
                                    <thead>
                                        <tr class='table-head'>
                                            <td><?=__('名次', 'nlyd-student')?></td>
                                            <td><?=__('学员姓名', 'nlyd-student')?></td>
                                            <td><span><?=__('ID', 'nlyd-student')?></span></td>
                                            <td><?=__('城市', 'nlyd-student')?></td>
                                            <td><span><?=__('项目总分', 'nlyd-student')?></span></td>
                                            <td class="select-td">
                                                <div class="td-type">
                                                    <div class="show-type" id="show-type" data-group=""><span id="show_text"><?=__('全部', 'nlyd-student')?></span><i class="iconfont">&#xe644;</i></div>
                                                    <ul class="ul-select" >
                                                        <li class="show-type" data-group=""><?=__('全部', 'nlyd-student')?></li>
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
                                        <tr class="nl-me" id="rank_2">
                                        </tr>
                                    </thead>
                                    <tbody id="flow_2">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 总排名 -->
                        <div class="layui-tab-item">
                            <div class="btn-wrapper one_3">
                                <div class="btn-wrap">
                                    <div class="classify-btn classify-active" data-post-id="0"><?=__('个人排名', 'nlyd-student')?></div>
                                </div>
                                <div class="btn-wrap">
                                    <div class="classify-btn" data-post-id="1"><?=__('战队排名', 'nlyd-student')?></div>
                                </div>
                            </div>
                            <div class="nl-table-wapper">
                                <table class="nl-table">
                                    <thead>
                                        <tr class='table-head' id="one_3_head">
                                            <td><?=__('名次', 'nlyd-student')?></td>
                                            <td><?=__('学员姓名', 'nlyd-student')?></td>
                                            <td><span><?=__('ID', 'nlyd-student')?></span></td>
                                            <td><?=__('城市', 'nlyd-student')?></td>
                                            <td><span><?=__('项目总分', 'nlyd-student')?></span></td>
                                            <td><?=__('组别', 'nlyd-student')?></td>
                                        </tr>
                                        <tr class="nl-me" id="rank_3">

                                        </tr>
                                    </thead>
                                    <tbody id="flow_3">
  
                                    </tbody>
                                </table>
                            </div>
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
                <div class="shareBottom"><?=__('微信好友', 'nlyd-student')?></div>
            </div>
        </div>
        <div class="shareItem flex1">
            <div class="shareContent shareMid" data-id="wechatTimeline">
                <div class="shareTop wechatTimeline">
                    <!-- <i class="iconfont">&#xe639;</i> -->
                </div>
                <div class="shareBottom"><?=__('朋友圈', 'nlyd-student')?></div>
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
<input type="hidden" name="_wpnonce" id="teamRank" value="<?=wp_create_nonce('student_get_team_ranking_code_nonce');?>">
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
        var project_id=$('.one_1 .classify-active').attr('data-post-id')
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
        var lastItem={lastItem_1:{},lastItem_2:{},lastItem_3:{}}
        var isClick={}
        element.on('tab(tabs)', function(){//tabs
            var left=$(this).position().left+parseInt($(this).css('marginLeft'));
            var html=$(this).html();
            var data_id=$(this).attr('data-id')
            $('.nl-transform').css({
                'transform':'translate3d('+left+'px, 0px, 0px)'
            }).html(html)
            if(!isClick[data_id]){
                var datas={data_id:data_id,myPage:1,category_id:null,project_id:null,age_group:null,team:false}
                if(data_id==2){//分类
                    datas['category_id']=$('.one_'+data_id+' .classify-active').attr('data-post-id');
                    datas['age_group']=$("#show-type").attr('data-group');
                }else if(data_id==1){//单项
                    datas['project_id']=$('.one_'+data_id+' .classify-active').attr('data-post-id');
                    datas['age_group']='';
                }
                pagation(datas)
            }
        })
        
        pagation = function(arg) {//总排名，个人成绩
            flow.load({
                    elem: '#flow_'+arg['data_id'] //流加载容器
                    // ,scrollElem:'#flow_'+arg['data_id']
                    // ,isAuto: false
                    // ,isLazyimg: true
                    ,done: function(page, next){ //加载下一页
                        if(arg['myPage']==1){
                            $('#rank_'+arg['data_id']).empty()
                        }
                        if(arg['data_id']==3){//总排名
                            if(arg['team']){//战队
                                var html_='<td><?=__('名次', 'nlyd-student')?></td>'
                                +'<td><?=__('战队名称', 'nlyd-student')?></td>'
                                +'<td><span><?=__('ID', 'nlyd-student')?></span></td>'
                                +'<td><span><?=__('总成绩', 'nlyd-student')?></span></td>'
                            }else{//个人
                                var html_='<td><?=__('名次', 'nlyd-student')?></td>'
                                +'<td><?=__('学员姓名', 'nlyd-student')?></td>'
                                +'<td><span><?=__('ID', 'nlyd-student')?></span></td>'
                                +'<td><?=__('城市', 'nlyd-student')?></td>'
                                +'<td><span><?=__('项目总分', 'nlyd-student')?></span></td>'
                                +'<td><?=__('组&nbsp;&nbsp;&nbsp;&nbsp;别', 'nlyd-student')?></td>'
                            }
                            $('#one_3_head').html(html_)
                        }
                        var lis = [];
                        var postData={}
                        if(!arg['team']){//不是战队列表
                            postData={
                                action:'get_score_ranking',
                                _wpnonce:$('#inputRank').val(),
                                match_id:$.Request('match_id'),
                                page:arg['myPage'],
                            }
                            if(arg['category_id']){
                                postData['category_id']=arg['category_id'];
                            }
                            if(arg['project_id']){
                                postData['project_id']=arg['project_id'];
                            }
                            if(arg['age_group']){
                                postData['age_group']=arg['age_group'];
                            }
                            if(arg['myPage']>1){
                                postData['lastItem']=lastItem['lastItem_'+arg['data_id']];
                            }
                        }else{//战队
                            postData={
                                action:'teamRanking',
                                _wpnonce:$('#teamRank').val(),
                                match_id:$.Request('match_id'),
                                page:arg['myPage'],
                            }
                            if(arg['myPage']>1){
                                postData['ranking']=lastItem['lastItem_'+arg['data_id']];
                            }
                        }

                        $.ajax({
                            data:postData,
                            success:function(res,ajaxStatu,xhr){
                                arg['myPage']++
                                console.log(postData)
                                isClick[arg['data_id']]=true;
                                if(res.success){ 
                                    var itemLen=res.data.info.length;
                                    lastItem['lastItem_'+arg['data_id']]=itemLen>0 ? res.data.info[itemLen-1] : {};
                                if(!arg['team']){//非战队
                                    if(res.data.my_ranking!=null){//我的成绩
                                        var rows=res.data.my_ranking
                                        var Html='<td>'
                                                    +'<div class="nl-circle">'+rows.ranking+'</div>'
                                                +'</td>'
                                                +'<td><div class="table_content">'+rows.user_name+'</div></td>'
                                                +'<td><div class="table_content c_orange">'+rows.ID+'</div></td>'
                                                +'<td><div class="table_content">'+rows.city+'</div></td>'
                                                +'<td><div class="table_content c_orange">'+rows.score+'</div></td>'
                                                +'<td><div class="table_content">'+rows.group+'</div></td>';
                                        if(rows.ranking>3){
                                            $('#rank_'+arg['data_id']).html(Html).css('display','table-row');
                                        }
                                    }
                                    $.each(res.data.info,function(index,value){
                                        var nl_me='';
                                        if(res.data.my_ranking!=null){
                                            if(value.ranking==res.data.my_ranking.ranking && value.ID==res.data.my_ranking.ID){
                                                nl_me='nl-me'
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
                                }else{//战队
                                    if(res.data.my_team){//我的战队
                                        var rows=res.data.my_team
                                        var Html='<td>'
                                                    +'<div class="nl-circle">'+rows.ranking+'</div>'
                                                +'</td>'
                                                +'<td><div class="table_content">'+rows.team_name+'</div></td>'
                                                +'<td><div class="table_content c_orange">'+rows.team_id+'</div></td>'
                                                +'<td><div class="table_content c_orange">'+rows.my_score+'</div></td>'
                                        if(rows.ranking>3){
                                            $('#rank_'+arg['data_id']).html(Html).css('display','table-row');
                                        }
                                    }
                                    $.each(res.data.info,function(index,value){
                                        var nl_me='';
                                        if(res.data.my_team && value.team_id==res.data.my_team.team_id){//我的战队
                                            nl_me='nl-me';
                                        }
                                        var dom='<tr class="'+nl_me+'">'
                                                    +'<td>'
                                                        +'<div class="nl-circle">'+value.ranking+'</div>'
                                                    +'</td>'
                                                    +'<td><div class="table_content">'+value.team_name+'</div></td>'
                                                    +'<td><div class="table_content c_orange">'+value.team_id+'</div></td>'
                                                    +'<td><div class="table_content c_orange">'+value.my_score+'</div></td>'
                                                +'</tr>'
                                        lis.push(dom)                           
                                    })
                                }
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
            })
        }

        pagation({data_id:$('.layui-tab-title .layui-this').attr('data-id'),myPage:1,category_id:null,project_id:$('.one_1 .classify-active').attr('data-post-id'),age_group:$('#show_text').attr('data-group'),team:false})
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
                if(_this.parents('.btn-wrapper').hasClass('one_1')){//单项排名
                    var id=_this.attr('data-post-id');
                    $('#flow_1').empty();
                    pagation({data_id:$('.layui-tab-title .layui-this').attr('data-id'),myPage:1,category_id:null,project_id:id,age_group:$('#show_text').attr('data-group'),team:false})
                }else if(_this.parents('.btn-wrapper').hasClass('one_3')){//总排名
                    var id=_this.attr('data-post-id');
                    $('#flow_3').empty();
                    if(id=='0'){//个人排名
                        pagation({data_id:$('.layui-tab-title .layui-this').attr('data-id'),myPage:1,category_id:null,project_id:null,age_group:null,team:false})
                    }else if(id=="1"){//战队排名
                        pagation({data_id:$('.layui-tab-title .layui-this').attr('data-id'),myPage:1,category_id:null,project_id:null,age_group:null,team:true})
                    }
                }else{//分类排名
                    var id=_this.attr('data-post-id');
                    $('#flow_2').empty();
                    pagation({data_id:$('.layui-tab-title .layui-this').attr('data-id'),myPage:1,category_id:id,project_id:null,age_group:null,team:false})
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
                    $('#flow_2').empty();
                    $('#show_text').text(thisText).attr('data-group',data_group)
                    pagation({data_id:$('.layui-tab-title .layui-this').attr('data-id'),myPage:1,category_id:$('.one_2 .classify-active').attr('data-post-id'),project_id:null,age_group:data_group,team:false})
                }
            }
        })
    })
})
</script>