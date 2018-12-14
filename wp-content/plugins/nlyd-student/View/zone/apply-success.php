
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
                    <div class="apply-success bold ta_c c_blue fs_16"><div class="nl-badge"><i class="iconfont">&#xe608;</i></div>
                    &nbsp;&nbsp;&nbsp;&nbsp;<?=__('分中心审核资料提交成功！', 'nlyd-student')?></div>
                    <div class="c_black">1、<?=__('审核时间大约xx~xx工作日，法定节假日顺延。', 'nlyd-student')?></div>
                    <div class="c_black">2、<?=__('审核资料期间请管理员保持电话畅通，若资料有误或缺失平台工作人员将以电话的方式与您沟通联系。', 'nlyd-student')?></div>
                    <div class="c_black">3、<?=__('审核期间，平台将会对机构对公账户进行打款验证，请留意账户信息。', 'nlyd-student')?></div>
                    <div class="c_black">4、<?=__('审核过程中，如有任何疑问，请拨打组委会工作电话：xxx-xxxxx。', 'nlyd-student')?></div>
                </div>
                <a class="a-btn a-btn-table" href="<?=home_url('/zone/');?>"><div><?=__('返回推广合作中心', 'nlyd-student')?></div></a>
            </div>
        </div>            
    </div>
</div>
