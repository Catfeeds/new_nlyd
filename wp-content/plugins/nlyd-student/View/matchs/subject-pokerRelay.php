
<div class="layui-fluid">
    <div class="layui-row">
        <?php
            if(!is_mobile()){
                require_once leo_student_public_view.'leftMenu.php';
            }
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
        <h1 class="mui-title">本轮答题记录</h1>
        </header>
            <div class="layui-row nl-border nl-content">
                 <div class="width-margin width-margin-pc">
                     <div class="match-subject-info">
                         <div class="subject-title">
                             <div class="subject-title-info"><?=$match_title?> 第<?=$match_more_cn?>轮</div>
                             <div class="subject-title-info">您的得分<span class="font-darkBlue"><?=$my_score?>分</span></div>
                             <div class="subject-title-info"><a <?= !empty($ranking) ? "href='{$record_url}'" :'class="disabled-a"';?> >全部排名</a></div>
                         </div>
                         <div class="subject-row">
                             <div class="one-info">
                                 <div class="left-label">复位数字：</div><span class="font-darkBlue"><?=$str_len;?></span>
                             </div>
                             <div class="one-info">
                                 <div class="left-label">正确数量：</div><span class="font-darkBlue"><?=$success_length;?></span>
                             </div>
                         </div>
                         <div class="subject-row">
                             <div class="one-info">
                                 <div class="left-label">答题用时：</div><span class="font-darkBlue"><?=$use_time;?>s</span>
                             </div>
                             <div class="one-info">
                                 <div class="left-label">剩余时间：</div><span class="font-darkBlue"><?=$surplus_time;?>s</span>
                             </div>
                         </div>
                         <div class="subject-row">
                             <div class="one-info">
                                 <div class="left-label">&nbsp;&nbsp;&nbsp;正确率：</div><span class="font-darkBlue"><?=$accuracy;?>%</span>
                             </div>
                             <?php if(!empty($ranking)):?>
                                 <div class="one-info">
                                     <div class="left-label">本轮排名：</div><span class="font-darkBlue"><?=$ranking?></span>
                                 </div>
                             <?php endif;?>
                         </div>
                     </div>

                    <div class="answer-zoo">
                        <div class="answerBtn">你的答案</div>
                        <div class="your-answer">
                            <div class="porker-zoo">
                                <div class="poker-window">
                                <?php if(!empty($my_answer)): ?>
                                    <div class="poker-wrapper">
                                        <?php foreach ($my_answer as $k => $v ){
                                            $val = str2arr($v,'-');
                                            switch ($val[0]){
                                                case 'club':
                                                    $ico = '&#xe635;';
                                                    break;
                                                case 'heart':
                                                    $ico = '&#xe638;';
                                                    break;
                                                case 'spade':
                                                    $ico = '&#xe636;';
                                                    break;
                                                case 'diamond':
                                                    $ico = '&#xe634;';
                                                    break;
                                            }
                                            ?>
                                            <div class="poker <?=$val[0]?> <?= in_array($k,$error_arr) ? 'active' : '';?>">
                                                <div class="poker-detail poker-top">
                                                    <div class="poker-name"><?=$val[1]?></div>
                                                    <div class="poker-type"><i class="iconfont"><?=$ico?></i></div>
                                                </div>
                                                <div class="poker-logo">
                                                    <img src="<?=student_css_url.'image/nlyd-big.png'?>">
                                                </div>
                                                <div class="poker-detail poker-bottom">
                                                    <div class="poker-name"><?=$val[1]?></div>
                                                    <div class="poker-type"><i class="iconfont"><?=$ico?></i></div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php endif;?>
                                </div>
                            </div>
                        </div>
                        <div class="answerBtn">正确答案</div>
                        <div class="right-answer">
                            <div class="porker-zoo">
                                <div class="poker-window">
                                    <?php if(!empty($questions_answer)): ?>
                                    <div class="poker-wrapper">
                                        <?php foreach ($questions_answer as $k => $v ){
                                                $val = str2arr($v,'-');
                                                switch ($val[0]){
                                                    case 'club':
                                                        $ico = '&#xe635;';
                                                        break;
                                                    case 'heart':
                                                        $ico = '&#xe638;';
                                                        break;
                                                    case 'spade':
                                                        $ico = '&#xe636;';
                                                        break;
                                                    case 'diamond':
                                                        $ico = '&#xe634;';
                                                        break;
                                                }
                                        ?>
                                            <div class="poker <?=$val[0]?> <?= in_array($k,$error_arr) ? 'active' : '';?>">
                                                <div class="poker-detail poker-top">
                                                    <div class="poker-name"><?=$val[1]?></div>
                                                    <div class="poker-type"><i class="iconfont"><?=$ico?></i></div>
                                                </div>
                                                <div class="poker-logo">
                                                    <img src="<?=student_css_url.'image/nlyd-big.png'?>">
                                                </div>
                                                <div class="poker-detail poker-bottom">
                                                    <div class="poker-name"><?=$val[1]?></div>
                                                    <div class="poker-type"><i class="iconfont"><?=$ico?></i></div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(!empty($next_more_url)): ?>
                    <div class="a-btn" href="<?=$next_more_url?>">距下一轮开赛&nbsp;&nbsp;&nbsp;&nbsp; <span class="count_down next_more_down" data-seconds="<?=$next_more_down?>">subjectFastScan00:00:00</span></div>
                    <a href="<?=$next_more_url?>">进入下一轮比赛</a>
                <?php endif;?>
                <?php if(!empty($next_project_url)): ?>
                    <div class="a-btn" href="<?=$next_project_url?>">距下一项目开赛 <span class="count_down next_project_down" data-seconds="<?=$next_project_down?>">subjectFastScan00:00:00</span></div>
                    <a href="<?=$next_project_url?>">进入下一项比赛</a>
                <?php endif;?>
            </div>
        </div>           
    </div>
</div>

<input style="display:none;" type="file" name="meta_val" id="file" class="file" value="" accept="image/jpg,image/jpeg,image/png,image/bmp" multiple/>
<input  type="hidden" name="meta_key" value="user_head"/>
<input  type="hidden" name="action" value="student_saveInfo"/>
<input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">

<script>
jQuery(function($) { 
    //设置扑克窗口宽度
    initWidth=function() {
        var len=$('.your-answer .poker-wrapper .poker').length;
        var width=$('.your-answer .poker-wrapper .poker').width()+2;
        var marginRight=parseInt($('.your-answer .poker-wrapper .poker').css('marginRight'))
        var W=width*len+marginRight*(len-1)+'px';
        $('.your-answer .poker-wrapper').css('width',W);

        var len1=$('.right-answer .poker-wrapper .poker').length;
        var width1=$('.right-answer .poker-wrapper .poker').width()+2;
        var marginRight1=parseInt($('.right-answer .poker-wrapper .poker').css('marginRight'))
        var W1=width1*len1+marginRight1*(len1-1)+'px';
        $('.right-answer .poker-wrapper').css('width',W1);
    }
    initWidth();

$('.count_down').countdown(function(S, d){//倒计时
    var _this=$(this);
    var D=d.day>0 ? d.day+'天' : '';
    var h=d.hour<10 ? '0'+d.hour : d.hour;
    var m=d.minute<10 ? '0'+d.minute : d.minute;
    var s=d.second<10 ? '0'+d.second : d.second;
    var time=D+h+':'+m+':'+s;
     _this.text(time);
    if(S<=0){
        window.location.href=_this.parents('.a-btn').attr('href')
    }
});
    $('.your-answer .poker-window').scroll(function(){
        var left=$(this).children('.poker-wrapper').position().left;
        $('.right-answer .poker-window').scrollLeft(-left)
    })
    $('.right-answer .poker-window').scroll(function(){
        var left=$(this).children('.poker-wrapper').position().left;
        $('.your-answer .poker-window').scrollLeft(-left)
    })
 
})
</script>