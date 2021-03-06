
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
                <h1 class="mui-title"><div><?=__($zone_type_name.'资料填写', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content have-bottom-footer">
                <div class="success">
                    <div class="apply-success bold ta_c c_blue fs_16"><div class="nl-badge"><i class="iconfont">&#xe608;</i></div>
                        &nbsp;&nbsp;&nbsp;&nbsp;<?=__($zone_type_name.'审核资料提交成功！', 'nlyd-student')?></div>
                        <div class="c_black">1、<?=__('请在3日内使用提交的对公账户向组委会对公账户', 'nlyd-student')?>
                        转入一笔<span class="c_red"><?=__('不高于0.1元', 'nlyd-student')?></span>的资金，<span class="c_red"><?=__('并备注“'.$zone_type_name.'申请”字样', 'nlyd-student')?></span>用于验证您的对公账户真实性。<br>
                        <span class="c_red"><?=__('开户名称：脑力（中国）运动开发有限公司', 'nlyd-student')?></span><br>
                        <span class="c_red"><?=__('开户银行：中国农业银行成都桂溪支行', 'nlyd-student')?></span><br>
                        <span class="c_red"><?=__('开户账号：22822201040011959', 'nlyd-student')?></span><br>
                        </div>
                        <div class="c_black">2、<?=__('审核时间大约2~3个工作日，法定节假日顺延。', 'nlyd-student')?></div>
                        <div class="c_black">3、<?=__('审核资料期间请管理员保持电话畅通，若资料有误或缺失平台工作人员将以电话的方式与您沟通联系。', 'nlyd-student')?></div>
                        <div class="c_black">4、<?=__('审核过程中，如有任何疑问，请拨打组委会工作电话：028-69956166。秘书长（王思淇）电话：13880452715。', 'nlyd-student')?></div>
                    <!-- <div class="c_black">1、<?=__('审核时间大约2~3个工作日，法定节假日顺延。', 'nlyd-student')?></div>
                    <div class="c_black">2、<?=__('审核资料期间请管理员保持电话畅通，若资料有误或缺失平台工作人员将以电话的方式与您沟通联系。', 'nlyd-student')?></div>
                    <div class="c_black">3、<?=__('审核期间，平台将会对机构对公账户进行打款验证，请留意账户信息。', 'nlyd-student')?></div>
                    <div class="c_black">4、<?=__('审核过程中，如有任何疑问，请拨打组委会工作电话：028-69956166。秘书长（王思淇）电话：13880452715。', 'nlyd-student')?></div> -->
                </div>
                <a class="a-btn a-btn-table a-btn-top" href="tel:028-69956166"><div><i class="iconfont">&#xe728;</i> <?=__('拨打组委会电话', 'nlyd-student')?></div></a>
                <a class="a-btn a-btn-table" href="<?=home_url('/zone/indexUser/');?>"><div><?=__('返回推广合作中心', 'nlyd-student')?></div></a>
            </div>
        </div>
    </div>
</div>
