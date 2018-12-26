
<style>
@media screen and (max-width: 1199px){
    #page{
        background-color:#f6f6f6!important;
    }
}
</style>
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
                <h1 class="mui-title"><div><?=__('数据统计', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding layui-row width-padding-pc">
                    <div class="mt_10"><?=__('数据更新于', 'nlyd-student')?>：2019/12/25</div>
                    <div class="mt_10 data_item_wrapper layui-row">
                        <div class="data_item">
                            <div class="data_num fs_22 ">8</div>
                            <div class="data_detail fs_13"><?=__('累计开设比赛次数', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 ">8</div>
                            <div class="data_detail fs_13"><?=__('累计开设考级次数', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_tag ta_c c_white fs_12">
                                <?=__('达标', 'nlyd-student')?>
                                <div class="kailong"></div>
                            </div>
                            <div class="data_num fs_22 ">8</div>
                            <div class="data_detail fs_13"><?=__('累计参与比赛人次', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 ">8</div>
                            <div class="data_detail fs_13"><?=__('累计参与考级人次', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 "><span class="fs_12">￥</span>8000.00</div>
                            <div class="data_detail fs_13"><?=__('比赛累计收益', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 "><span class="fs_12">￥</span>8000.00</div>
                            <div class="data_detail fs_13"><?=__('考级累计收益', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item active">
                            <div class="data_num fs_22 "><span class="fs_12">￥</span>8000.00</div>
                            <div class="data_detail fs_13"><?=__('累计收益', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 "><span class="fs_12">￥</span>8000.00</div>
                            <div class="data_detail fs_13"><?=__('累计提现', 'nlyd-student')?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function($) {

    })
</script>
