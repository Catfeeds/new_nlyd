
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
                <a class="mui-pull-left nl-goback static" href="<?=home_url('/zone/');?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('数据统计', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding layui-row width-padding-pc">
                    <div class="mt_10"><?=__('数据更新于', 'nlyd-student')?>：<?=date_i18n('Y/m/d',get_time())?></div>
                    <div class="mt_10 data_item_wrapper layui-row">
                        <div class="data_item">
                            <div class="data_num fs_22 ">
                                <span class="fs_12">￥</span>
                                <?=$extract_income > 0 ? number_format($extract_income,2) : number_format(0,2);?></div>
                            <div class="data_detail fs_13"><?=__('累计提现', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item active">
                            <div class="data_num fs_22 ">
                                <span class="fs_12">￥</span>
                                <?=$user_income > 0 ? number_format($user_income,2) : number_format(0,2);?>
                            </div>
                            <div class="data_detail fs_13"><?=__('累计收益', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 ">
                                <div class="data_num fs_22 ">
                                    <?=$match_total > 0 ? $match_total : 0;?>
                                </div>
                            </div>
                            <div class="data_detail fs_13"><?=__('累计开设比赛次数', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <?php if($is_standard == 'y'): ?>
                            <div class="data_tag ta_c c_white fs_12">
                                <?=__('达标', 'nlyd-student')?>
                                <div class="kailong"></div>
                            </div>
                            <?php endif;?>
                            <div class="data_num fs_22 ">
                                <div class="data_num fs_22 ">
                                    <?=$match_order > 0 ? $match_order : 0;?>
                                </div>
                            </div>
                            <div class="data_detail fs_13"><?=__('累计参与比赛人次', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 ">
                                <?=$grading_total > 0 ? $grading_total : 0;?>
                            </div>
                            <div class="data_detail fs_13"><?=__('累计开设考级次数', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 ">
                                <?=$grading_order > 0 ? $grading_order : 0;?>
                            </div>
                            <div class="data_detail fs_13"><?=__('累计参与考级人次', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 ">
                                <?=$course_order > 0 ? $course_order : 0;?>
                            </div>
                            <div class="data_detail fs_13"><?=__('累计开设课程', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 ">
                                <?=$zone_total > 0 ? $zone_total : 0;?>
                            </div>
                            <div class="data_detail fs_13"><?=__('累计推荐机构', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 ">
                                <?=$course_total > 0 ? $course_total : 0;?>
                            </div>
                            <div class="data_detail fs_13"><?=__('累计推荐用户', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 ">
                                <span class="fs_12">￥</span>
                                <?=$match_income > 0 ? number_format($match_income,2) : number_format(0,2);?>
                            </div>
                            <div class="data_detail fs_13"><?=__('比赛累计收益', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 ">
                                <span class="fs_12">￥</span>
                                <?=$grading_income > 0 ? number_format($grading_income,2) : number_format(0,2);?>
                            </div>
                            <div class="data_detail fs_13"><?=__('考级累计收益', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 ">
                                <span class="fs_12">￥</span>
                                <?=$recommend_income > 0 ? number_format($recommend_income,2) : number_format(0,2);?>
                            </div>
                            <div class="data_detail fs_13"><?=__('推荐收益', 'nlyd-student')?></div>
                        </div>
                        <div class="data_item">
                            <div class="data_num fs_22 ">
                                <span class="fs_12">￥</span>
                                <?=$course_income > 0 ? number_format($course_income,2) : number_format(0,2);?>
                            </div>
                            <div class="data_detail fs_13"><?=__('课程收益', 'nlyd-student')?></div>
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
