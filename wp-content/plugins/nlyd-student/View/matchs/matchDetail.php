<!-- 比赛详情 -->
<style>
    @media screen and (max-width: 991px){
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


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback static" href="<?=home_url('matchs')?>">
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title">比赛详情</h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-margin width-margin-pc content-border">
                    <div class="width-padding-pc">
                        <ul class="flow-default">
                            <li class="nl-match">
                                <!-- <span class="nl-match-people">28报名</span> -->
                                <div class="nl-match-header">
                                    <span class="nl-match-name <?=$match['match_status'] != 1?'finish':'';?>"><?=$match['post_title']?></span>
                                    <?php if($match['is_me'] == 'y'): ?>
                                        <div class="nl-badge"><i class="iconfont">&#xe608;</i></div>
                                    <?php endif ?>
                                    <p class="long-name"><?=$match['post_content']?></p>
                                </div>
                                <div class="nl-match-body">
                                    <div class="nl-match-detail">
                                        <div class="nl-match-label">开赛日期：</div>
                                        <div class="nl-match-info">
                                            <?=$match['match_start_time']?>
                                            <span class="nl-match-type <?=$match['match_status'] == 2?'orange':'';?> "><?=$match['match_status_cn']?></span>
                                        </div>
                                    </div>
                                    <div class="nl-match-detail">
                                        <div class="nl-match-label">开赛地点：</div>
                                        <div class="nl-match-info"><?=$match['match_address']?></div>
                                    </div>
                                    <div class="nl-match-detail">
                                        <div class="nl-match-label">报名费用：</div>
                                        <div class="nl-match-info">¥<?=$match['match_cost']?></div>
                                    </div>
                                    <?php if($match['match_status'] == 1): ?>
                                    <div class="nl-match-detail">
                                        <div class="nl-match-label">报名截止：</div>
                                        <div class="nl-match-info">已截止</div>
                                    </div>
                                    <?php endif ?>
                                </div>
                            </li>
                            <?php if(!empty($match_project)): ?>
                            <!-- 比赛项目 -->
                            <li class="nl-match">
                                <div class="nl-match-header noMargin">
                                    <span class="nl-match-name <?=$match['match_status'] != 1?'finish':'';?> ">比赛项目</span>
                                </div>
                                <div class="nl-match-body">
                                    <?php foreach ($match_project as $val){ ?>
                                    <div class="nl-match-detail layui-row">
                                        <div class="nl-match-label"><?=$val['parent_title']?>：</div>
                                        <div class="nl-match-info">
                                        <?php foreach ($val['project'] as $v ){ ?>
                                        
                                            <div class="one-item"><?=$v['post_title']?>
                                                &nbsp;&nbsp;<a href="<?=$v['rule_url']?>" class="nl-see-link">查看规则</a>
                                             </div>
                                        
                                        <?php } ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </li>
                            <?php endif;?>
                            
                            <li class="nl-match">
                                <div class="nl-match-header">
                                    <span class="nl-match-name <?=$match['match_status'] != 1?'finish':'';?>">报名列表</span>
                                    <span class="nl-match-people <?=$match['match_status'] != 1?'finish':'';?>"><?=$total?>位选手已报名</span>
                                </div>
                                <div class="nl-match-body">
                                    <table class="nl-table"  id="flow-table">
                                    

                                    </table>
                                </div>
                            </li>
                        </ul>
                        <?php if($match['is_me'] != 'y' && $match['match_status'] == 1): ?>
                        <a class="a-btn" href="<?=home_url('/matchs/confirm/match_id/'.$_GET['match_id']);?>">报名参赛</a>
                        <?php endif; ?>
                        <?php if( $match['is_me'] == 'y' && $match['match_status'] == 2):?>
                            <a class="a-btn" href="<?=home_url('/matchs/matching/match_id/'.$_GET['match_id']);?>">进入比赛</a>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>           
    </div>
</div>
<!-- 比赛详情报名选手列表获取 -->
<input type="hidden" name="_wpnonce" id="inputPlayer" value="<?=wp_create_nonce('student_get_entry_code_nonce');?>">

<script>
jQuery(function($) { 
    layui.use(['element','flow'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        var flow = layui.flow;//流加载
        flow.load({
            elem: '#flow-table' //流加载容器
            ,scrollElem: '#flow-table' //滚动条所在元素，一般不用填，此处只是演示需要。
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                var postData={
                    action:'get_entry_list',
                    _wpnonce:$('#inputPlayer').val(),
                    page:page,
                    match_id:<?=$_GET['match_id']?>,
                }
                var lis = [];
                $.post(window.admin_ajax,postData,function(res,ajaxStatu,xhr){
                        if(res.success){
                            $.each(res.data.info,function(i,v){
                                var dom='<tr>'
                                            +'<td>'
                                                +'<div class="player-img"><img src="'+v.user_head+'"></div>'
                                            +'</td>'
                                            +'<td>'+v.nickname+'</td>'
                                            +'<td>'+v.user_gender+'</td>'
                                            +'<td>'+v.real_age+'岁</td>'
                                            +'<td>'+v.created_time+'报名</td>'
                                        +'<tr>'
                                lis.push(dom) 
                            })
                            if (res.data.info.length<10) {
                                next(lis.join(''),false) 
                            }else{
                                next(lis.join(''),true) 
                            } 
                        }else{
                            if(page==1){
                                var dom='<tr><td colspan="5">无记录</td><tr>'
                                lis.push(dom) 
                            }else{
                                $.alerts('没有更多了')
                            }
                            next(lis.join(''),false)
                        }
                })       
            }
        });
    });
})
</script>