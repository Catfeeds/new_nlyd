<?php
$path = leo_student_public_view.'cache/match_info/'.$_GET['match_id'].'_'.$language.'.php';
if( file_exists($path) && $match['match_status'] != 1){
    include_once $path;
}else{

if ($match['match_status'] != 1):
    ob_start();
endif;
?>
<!-- 比赛详情 -->
<?php require_once PLUGINS_PATH . 'nlyd-student/View/public/student-footer-menu.php'; ?>

<div class="layui-fluid">
    <div class="layui-row">
        <?php
        require_once leo_student_public_view . 'leftMenu.php';
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?= home_url('matchs') ?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?= __('比赛详情', 'nlyd-student') ?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-margin width-margin-pc content-border">
                    <div class="width-padding-pc">
                        <ul class="flow-default">
                            <li class="nl-match">
                                <!-- <span class="nl-match-people">28报名</span> -->
                                <div class="nl-match-header">
                                    <span class="nl-match-name fs_16 <?= $match['match_status'] != -3 ? 'c_blue' : ' '; ?>"><?= $match['post_title'] ?></span>
                                    <?php if ($match['is_me'] == 'y'): ?>
                                        <div class="nl-badge"><i class="iconfont">&#xe608;</i></div>
                                    <?php endif ?>
                                    <p class="long-name fs_12 c_black3"><?= $match['post_content'] ?></p>
                                </div>
                                <div class="nl-match-body">
                                    <div class="nl-match-detail">
                                        <div class="nl-match-label"><?= __('开赛日期', 'nlyd-student') ?>:</div>
                                        <div class="nl-match-info c_black">
                                            <?= $match['match_start_time'] ?>
                                            <span class="nl-match-type fs_12 <?= $match['match_status'] == 2 ? 'c_orange' : 'c_blue'; ?> "><?= $match['match_status_cn'] ?></span>
                                        </div>
                                    </div>
                                    <div class="nl-match-detail">
                                        <div class="nl-match-label"><?= __('结束时间', 'nlyd-student') ?>:</div>
                                        <div class="nl-match-info c_black">
                                            <?= $match['match_end_time'] ?>
                                        </div>
                                    </div>
                                    <div class="nl-match-detail">
                                        <div class="nl-match-label"><?= __('开赛地点', 'nlyd-student') ?>:</div>
                                        <div class="nl-match-info c_black"><?= $match['match_address'] ?></div>
                                    </div>
                                    <div class="nl-match-detail">
                                        <div class="nl-match-label"><?= __('报名费用', 'nlyd-student') ?>:</div>
                                        <div class="nl-match-info c_black">¥<?= $match['match_cost'] ?></div>
                                    </div>
                                    <div class="nl-match-detail">
                                        <div class="nl-match-label"><?= __('报名截止', 'nlyd-student') ?>:</div>
                                        <div class="nl-match-info c_black" id="time_count"
                                             data-end="<?= $match['entry_end_time'] ?>"><?= $match['entry_end_time'] < get_time('mysql') ? __('已截止', 'nlyd-student') : ''; ?></div>

                                    </div>
                                </div>
                            </li>
                            <?php if (!empty($match_project)): ?>
                                <!-- 比赛项目 -->
                                <li class="nl-match">
                                    <div class="nl-match-header noMargin">
                                        <span class="nl-match-name fs_16 <?= $match['match_status'] != -3 ? 'c_blue' : ''; ?> "><?= __('比赛项目', 'nlyd-student') ?></span>
                                        <a class="c_blue" style="float:right"
                                           href="https://mp.weixin.qq.com/s/p5c8L-afyE-HvTbH59D8vA"><?= __('参赛须知', 'nlyd-student') ?></a>
                                    </div>
                                    <div class="nl-match-body">
                                        <?php foreach ($match_project as $val) { ?>
                                            <div class="nl-match-detail layui-row">
                                                <div class="nl-match-label"><?= $val['parent_title'] ?>:</div>
                                                <div class="nl-match-info">
                                                    <?php foreach ($val['project'] as $v) { ?>
                                                        <?= $v['post_title'] ?>&nbsp;&nbsp;<a
                                                                href="<?= $v['rule_url'] ?>"
                                                                class="c_blue"><?= __('比赛规则', 'nlyd-student') ?></a>&nbsp;&nbsp;
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </li>
                            <?php endif; ?>

                            <li class="nl-match">
                                <div class="nl-match-header">
                                    <span class="nl-match-name fs_16 <?= $match['match_status'] != -3 ? 'c_blue' : ''; ?>"><?= __('报名列表', 'nlyd-student') ?></span>
                                    <span class="nl-match-people <?= $match['match_status'] != -3 ? 'c_blue' : ''; ?>"><?php printf(__('%s位选手已报名', 'nlyd-student'), $total) ?></span>
                                </div>
                                <div class="nl-match-body">
                                    <div class="nl-table-wapper">
                                        <table class="nl-table">
                                            <thead>
                                            <tr>
                                                <td>
                                                    <div class="table_content"><?= __('头 像', 'nlyd-student') ?></div>
                                                </td>
                                                <td>
                                                    <div class="table_content"><?= __('姓 名', 'nlyd-student') ?> </div>
                                                </td>
                                                <td>
                                                    <div class="table_content"><?= __('性 别', 'nlyd-student') ?></div>
                                                </td>
                                                <td>
                                                    <div class="table_content"><?= __('年 龄', 'nlyd-student') ?></div>
                                                </td>
                                                <td>
                                                    <div class="table_content"><?= __('国 籍', 'nlyd-student') ?></div>
                                                </td>
                                            </tr>
                                            </thead>
                                            <tbody id="flow-table">
                                            <?php
                                            if ($match['match_status'] != 1):
                                                foreach ($orders as $v) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div class="player-img">
                                                                <img src="<?=$v['user_head']?>">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="table_content"><?=$v['nickname']?></div>
                                                        </td>
                                                        <td>
                                                            <div class="table_content"><?=$v['user_gender']?></div>
                                                        </td>
                                                        <td>
                                                            <div class="table_content"><?=$v['real_age']?></div>
                                                        </td>
                                                        <td>
                                                            <div class="table_content">
                                                                <span class="fastbannerform__span f32 NOFLAG <?=$v['nationality']?>"></span><?=$v['nationality_short']?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <?php
                        if ($match['match_status'] != 1):
                            $path = leo_student_public_view . 'cache/match_info/' . $_GET['match_id'] . '_' . $language . '.php';
                            $info = ob_get_contents(); //得到缓冲区的内容并且赋值给$info
                            $file = fopen($path, 'w'); //打开文件info.txt
                            fwrite($file, $info); //写入信息到info.txt
                            fclose($file); //关闭文件info.txt
                        endif;
                        }
                        ?>

                        <?php if($match['is_me'] != 'y' && $match['match_status'] == 1): ?>
                        <a class="a-btn get_footer" href="<?=home_url('/matchs/confirm/match_id/'.$_GET['match_id']);?>"><?=__('报名参赛', 'nlyd-student')?></a>
                        <?php endif; ?>
                        <?php if( ($match['is_me'] == 'y' && $match['match_status'] == 2) && ($match['match_status'] == -3 || $match['match_status'] == 2)){?>
                            <div class="a-btn two get_footer">
                                <?php if($match['match_status'] == -3 || $match['match_status'] == 2):?>
                                    <a class="a-two left c_white" href="<?=home_url('/matchs/record/match_id/'.$_GET['match_id']);?>"><?=__('查看战绩', 'nlyd-student')?></a>
                                <?php endif;?>
                                <?php if( $match['is_me'] == 'y' && $match['match_status'] == 2):?>
                                    <a class="a-two right c_white" href="<?=home_url('/matchs/matchWaitting/match_id/'.$_GET['match_id']);?>"><?=__('进入比赛', 'nlyd-student')?></a>
                                <?php endif;?>
                            </div>              
                        <?php }else { ?>
                            <?php if($match['match_status'] == -3 || $match['match_status'] == 2):?>
                                <a class="a-btn get_footer" href="<?=home_url('/matchs/record/match_id/'.$_GET['match_id']);?>"><?=__('查看战绩', 'nlyd-student')?></a>
                            <?php endif;?>
                            <?php if( $match['is_me'] == 'y' && $match['match_status'] == 2):?>
                                <a class="a-btn get_footer" href="<?=home_url('/matchs/matchWaitting/match_id/'.$_GET['match_id']);?>"><?=__('进入比赛', 'nlyd-student')?></a>
                            <?php endif;?>
                       <?php }?>
                        
                        <?php if($match['is_me'] == 'y' && $match['match_status'] == -2 && $match['down_time'] > 0):?>
                        <!--倒计时-->
                            <div class="a-btn count_down get_footer" data-seconds="<?=$match['down_time']?>" href="<?=$match['match_url']?>"></div>
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
    matchDetail()
    if($('.count_down').attr('data-seconds')<=120){
        $.DelSession('waitting')
        window.location.href='<?=$match['match_url']?>'
    }
    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day+'<?=__('天', 'nlyd-student')?>' : '';
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=D+h+':'+m+':'+s;
        $(this).text(time);
        if(S==120){//
            $.DelSession('waitting')
            window.location.href='<?=$match['match_url']?>'
            return false;
        }
    });
    layui.use(['element','flow'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        <?php if($match['match_status'] == 1){ ?>
        var flow = layui.flow;//流加载
        var match_page=1;
        flow.load({
            elem: '#flow-table' //流加载容器
            // ,isAuto: false
            // ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                var postData={
                    action:'get_entry_list',
                    _wpnonce:$('#inputPlayer').val(),
                    page:match_page,
                    match_id:<?=$_GET['match_id']?>,
                    match_end_date:new Date().getTime()
                }
                var lis = [];
                $.ajax({
                    data:postData,
                    success:function(res,ajaxStatu,xhr){
                        match_page++
                        var domTime=$('#time_count').attr('data-end').replace(/-/g,'/');
                        var end_time = new Date(domTime).getTime();//月份是实际月份-1
                        var serverTimes=new Date(xhr.getResponseHeader('Date')).getTime()
                        var sys_second = (parseInt(end_time)-parseInt(serverTimes))/1000;
                        $('#time_count').attr('data-seconds',sys_second).countdown(function(S, d){//倒计时
                            var D=d.day>0 ? d.day+'<?=__('天', 'nlyd-student')?>' : '';
                            var h=d.hour<10 ? '0'+d.hour : d.hour;
                            var m=d.minute<10 ? '0'+d.minute : d.minute;
                            var s=d.second<10 ? '0'+d.second : d.second;
                            var time=D+h+':'+m+':'+s;
                            $(this).text(time);
                            if(S==0){
                                setTimeout(function() {
                                    window.location.reload()
                                }, 1000);
                            }
                        });
                        if(res.success){
                            $.each(res.data.info,function(i,v){
                                var dom='<tr>'
                                            +'<td>'
                                                +'<div class="player-img"><img src="'+v.user_head+'"></div>'
                                            +'</td>'
                                            +'<td><div class="table_content">'+v.nickname+'</div></td>'
                                            +'<td><div class="table_content">'+v.user_gender+'</div></td>'
                                            +'<td><div class="table_content">'+v.real_age+'</div></td>'
                                            +'<td><div class="table_content"><span class="fastbannerform__span f32 NOFLAG '+v.nationality+'"></span>'+v.nationality_short+'</div></td>'
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
        <?php } ?>
    });
})
</script>