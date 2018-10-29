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
            <div class="layui-row nl-border nl-content">
                <div class="width-margin flow-default" id="flow-list">
                    <?php if(!empty($rows)){ ?>
                    <a class="message-row active_bg read" href="http://127.0.0.1/nlyd/account/messageDetail/messages_id/1">
                        <div class="message-title"><span class="accept-name">测试1</span><span class="message-time">2018-07-12 11:22:57</span></div>
                        <p class="message-detail">测试内容1</p>
                    </a>
                    <?php }else{?>
                        <div class="no-info-page">
                            <div class="no-info-img">
                                <img src="http://127.0.0.1/nlyd/wp-content/plugins/nlyd-student/Public/css/image/noInfo/noMessage1040@2x.png">
                            </div>
                            <p class="no-info-text">暂未上传任何监赛记录</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>