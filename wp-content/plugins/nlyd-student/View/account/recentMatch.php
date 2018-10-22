
<div class="layui-fluid">
    <div class="layui-row">

        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>

        <?php if($row){ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" onclick="window.location.href = '<?=home_url('account')?>' ">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('我的比赛', 'nlyd-student')?></div></h1>
            </header>
                <div class="layui-row nl-border nl-content">
                    <div class="layui-tab layui-tab-brief width-margin width-margin-pc" lay-filter="tabs">
                        <div class="layui-tab-content" style="padding: 0;">
                            <div class="layui-tab-item layui-show">
                                <ul class="flow-default layui-row layui-col-space20" id="flow-list">

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php }else{ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper layui-bg-white">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('我的比赛', 'nlyd-student')?></div></h1>
            </header>    
                <div class="layui-row nl-border nl-content">
                    <div class="no-info-page">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noMatch1042@2x.png'?>">
                        </div>
                        <p class="no-info-text"><?=__('您暂未参加任何比赛', 'nlyd-student')?></p>
                        <a class="a-btn a-btn-table" href="<?=home_url('/matchs');?>"><div><?=__('看看最近比赛', 'nlyd-student')?></div></a>
                    </div>
                </div>
            </div>
        <?php } ?>
       
    </div>
</div>
