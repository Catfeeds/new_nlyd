
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
                <h1 class="mui-title"><div><?=__('提现详情', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="layui-row width-padding width-margin-pc">
                    <div class="profit_detail_row">
                        <div class="profit_detail_label"><?=__('提现金额', 'nlyd-student')?>：</div>
                        <div class="profit_detail_info c_black">¥ <?=$extract_amount?></div>
                    </div>
                    <div class="profit_detail_row">
                        <div class="profit_detail_label"><?=__('提现路径', 'nlyd-student')?>：</div>
                        <div class="profit_detail_info c_black">
                            <?=$extract_type_cn;?>
                        </div>
                    </div>
                    <div class="profit_detail_row">
                        <div class="profit_detail_label"><?=__('发起时间', 'nlyd-student')?>：</div>
                        <div class="profit_detail_info c_black"><?=$apply_time?></div>
                    </div>
                    <div class="profit_detail_row">
                        <div class="profit_detail_label"><?=__('提现状态', 'nlyd-student')?>：</div>
                        <div class="profit_detail_info c_green"><?=$extract_status_cn?></div>
                    </div>
                    <?php if($extract_status == 2 && !empty($censor_time)):?>
                    <div class="profit_detail_row">
                        <div class="profit_detail_label"><?=__('到账时间', 'nlyd-student')?>：</div>
                        <div class="profit_detail_info c_black"><?=$censor_time?></div>
                    </div>
                    <?php endif;?>
                </div>
            </div>
        </div>            
    </div>
</div>
