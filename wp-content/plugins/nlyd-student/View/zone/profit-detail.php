
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
                <h1 class="mui-title"><div><?=__('收益详情', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="layui-row width-padding width-margin-pc">
                    <div class="profit_detail_row">
                        <div class="profit_detail_label"><?=__('收益金额', 'nlyd-student')?>：</div>
                        <div class="profit_detail_info c_black">¥ 500.00</div>
                    </div>
                    <div class="profit_detail_row">
                        <div class="profit_detail_label"><?=__('收益来源', 'nlyd-student')?>：</div>
                        <div class="profit_detail_info c_black"><?=__('比赛收益', 'nlyd-student')?></div>
                    </div>
                    <div class="profit_detail_row">
                        <div class="profit_detail_label"><?=__('比赛类型', 'nlyd-student')?>：</div>
                        <div class="profit_detail_info c_black"><?=__('战队精英赛', 'nlyd-student')?></div>
                    </div>
                    <div class="profit_detail_row">
                        <div class="profit_detail_label"><?=__('比赛详情', 'nlyd-student')?>：</div>
                        <div class="profit_detail_info c_black"><?=__('2018脑力世界杯东方欲晓战队精英赛', 'nlyd-student')?></div>
                    </div>
                    <div class="profit_detail_row">
                        <div class="profit_detail_label"><?=__('收益级别', 'nlyd-student')?>：</div>
                        <div class="profit_detail_info c_black"><?=__('1级收益 2级收益', 'nlyd-student')?></div>
                    </div>
                    <div class="profit_detail_row">
                        <div class="profit_detail_label"><?=__('收益途径', 'nlyd-student')?>：</div>
                        <div class="profit_detail_info c_black"><?=__('xxx  XXX（xxx）', 'nlyd-student')?></div>
                    </div>
                    <div class="profit_detail_row">
                        <div class="profit_detail_label"><?=__('收益状态', 'nlyd-student')?>：</div>
                        <div class="profit_detail_info c_green"><?=__('已到账', 'nlyd-student')?></div>
                    </div>
                    <div class="profit_detail_row">
                        <div class="profit_detail_label"><?=__('到账时间', 'nlyd-student')?>：</div>
                        <div class="profit_detail_info c_black">2018/12/12 13:18</div>
                    </div>
                </div>
            </div>
        </div>            
    </div>
</div>
