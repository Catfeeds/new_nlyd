<!--监赛提交列表-->
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';

        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('/account/')?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('监赛记录', 'nlyd-student')?></div></h1>
            </header>
            <?php if(!empty($lists)){  ?>
                <div class="layui-row nl-border nl-content">
                    <div class="width-margin flow-default" id="flow-list">
                    <?php
                        foreach ($lists as $v){
                    ?>
                    <a class="message-row active_bg read" href="<?=home_url('supervisor/index/id/'.$v['id'])?>">
                        <div class="message-title">
                            <span class="accept-name"><?=$v['student_name']?>(第<?=$v['seat_number']?>座位号)</span>
                            <span class="message-time"><?=$v['created_time']?></span>
                        </div>
                        <p class="message-detail"><?=$v['student_name'].'在'.$v['match_title'].$v['project_title'].'第'.$v['match_more']?>轮未到场答题</p>
                    </a>
                    <?php } ?>
                </div>
            </div>
            <?php }else{?>
                <div class="layui-row nl-border nl-content">
                    <div class="no-info-page">
                        <div class="no-info-img">
                            <img src="http://127.0.0.1/nlyd/wp-content/plugins/nlyd-student/Public/css/image/noInfo/noMessage1040@2x.png">
                        </div>
                        <p class="no-info-text"><?=__('暂未上传任何监赛记录', 'nlyd-student')?></p>
                    </div>
                </div>
            <?php } ?>
            <a class="a-btn" href="<?=home_url('supervisor')?>" ><?=__('添加监赛记录', 'nlyd-student')?></a>
        </div>
    </div>
</div>