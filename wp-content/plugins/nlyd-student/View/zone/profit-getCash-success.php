
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
                <h1 class="mui-title"><div><?=__('分中心资料填写', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="success">
                    <div class="apply-success bold ta_c c_green fs_16"><div class="nl-badge bg_gradient_green"><i class="iconfont">&#xe608;</i></div></div>
                    <div class="c_black ta_c fs_16"><?=__('成功发起提现，等待提现到账。', 'nlyd-student')?></div>
                    <a class="a-btn a-btn-table bg_gradient_green" href="<?=home_url('/zone/profit/');?>"><div><?=__('返回收益管理', 'nlyd-student')?></div></a>
                </div>
            </div>
        </div>            
    </div>
</div>
