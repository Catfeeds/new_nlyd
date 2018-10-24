<style>
    @media screen and (max-width: 1199px){
        #page {
            top: 0;
            padding-bottom:0;
        }
    }

</style>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <div class="layui-row nl-border nl-content">
                <div class="signs_box">
                    <div class="img-box right_img">
                        <img src="<?=student_css_url.'image/signs/success.png'?>">
                    </div>
                    <h3 class="signs_tips"><?=__('恭喜你，签到成功', 'nlyd-student')?></h3>
                    <p class="signs_title fs_16 c_black"><?=__($match_title, 'nlyd-student')?></p>
                    <div class="signs_row fs_16">
                        <span class="signs_label c_black"><?=__('座位号', 'nlyd-student')?>：</span>
                        <span class="signs_value"><?=$index?></span>
                    </div>
                    <div class="signs_row fs_16">
                        <span class="signs_label c_black"><?=__('选手姓名', 'nlyd-student')?>：</span>
                        <span class="signs_value"><?=$real_name?></span>
                    </div>
                    <div class="signs_row fs_16">
                        <span class="signs_label c_black"><?=__('证件号码', 'nlyd-student')?>：</span>
                        <span class="signs_value"><?=$real_ID?></span>
                    </div>
                    <div class="signs_row fs_16">
                        <span class="signs_label c_black"><?=__('城市信息', 'nlyd-student')?>：</span>
                        <span class="signs_value"><?=$address?></span>
                    </div>
                    <a href="<?=home_url('/matchs');?>" class="go_match"><span><?=__('去赛事中心', 'nlyd-student')?></span></a>
                </div>

                <div class="signs_footer">
                    <div class="img-box logo_img">
                        <img src="<?=student_css_url.'image/nlyd-big.png'?>">
                    </div>
                    <p class="ta_c fs_16"><?=__('脑力世界杯组委会', 'nlyd-student')?></p>
                </div>
            </div>
        </div>
    </div>
</div>