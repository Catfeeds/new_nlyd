
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('发布成功', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="success">
                    <div class="apply-success bold ta_c c_blue fs_16"><div class="nl-badge mr_10"><i class="iconfont">&#xe608;</i></div><span><?=__('发布成功', 'nlyd-student')?></span></div>
                    <div class="c_black ta_l fs_16 c_black"><?=__('您已成功发布比赛“2018脑力世界杯大爱长青国际脑力战队精英赛”。', 'nlyd-student')?></div>
                    <div class="ta_l fs_12 c_red mt_10"><?=__('*请在发布比赛后再次编辑生成比赛时间，管理员可根据实际情况自定义修改赛程时间', 'nlyd-student')?></div>
                    <a class="a-btn a-btn-table" href="<?=home_url('/zone/matchList/');?>"><div><?=__('返回比赛列表', 'nlyd-student')?></div></a>
                </div>
            </div>
        </div>            
    </div>
</div>
