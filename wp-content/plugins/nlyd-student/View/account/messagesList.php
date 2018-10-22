<!--我的消息列表-->
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';

        ?>


        <?php if($is_show){ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('/account/')?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('我的消息', 'nlyd-student')?></div></h1>
            </header>
                <div class="layui-row nl-border nl-content">
                    <div class="width-margin flow-default" id="flow-list">

                    </div>
                </div>
            </div>
        <?php }else{ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper layui-bg-white">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('/account/')?>">
                <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('我的消息', 'nlyd-student')?></div></h1>
            </header>
                <div class="layui-row nl-border nl-content">
                    <div class="no-info-page">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noMessage1040@2x.png'?>">
                        </div>
                        <p class="no-info-text"><?=__('暂无任何站内消息', 'nlyd-student')?></p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>