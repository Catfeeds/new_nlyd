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
                <div class="signs_header">
                    <div class="ta_c c_black fs_16">
                    <?=$match_title?><br>
                    <span class="fs_12"><?=$match_content?></span>
                    </div>
                </div>
                <div class="signs_box">
                    <div class="img-box right_img">
                        <img src="<?=student_css_url.'image/signs/success.png'?>">
                    </div>
                    <h3 class="signs_tips c_blue"><?=__('恭喜您，签到成功', 'nlyd-student')?></h3>
                    <div class="signs_row">
                        <div class="signs_label"><?=__('参赛座位号', 'nlyd-student')?>：</div>
                        <div class="signs_value c_blue fs_15"><?=sprintf(__('第%s号','nlyd-student'),$index)?></div>
                    </div>
                    <div class="signs_row">
                        <div class="signs_label"><?=__('选手国籍', 'nlyd-student')?>：</div>
                        <div class="signs_value c_black fs_15"><?=$user_nationality?> <span class="fastbannerform__span f32 NOFLAG <?=$user_nationality_pic?>"></span></div>
                    </div>
                    <div class="signs_row">
                        <div class="signs_label"><?=__('选手姓名', 'nlyd-student')?>&<?=__('证件号码', 'nlyd-student')?>：</div>
                        <div class="signs_value c_black fs_15"><?=$real_name?><br><?=$real_ID?></div>
                    </div>
                    <div class="signs_row">
                        <div class="signs_label"><?=__('性别', 'nlyd-student')?>&<?=__('年龄', 'nlyd-student')?>：</div>
                        <div class="signs_value c_black fs_15"><?=__($user_gender,'nlyd-student')?> <?=$user_birthday?> <?=__($age_type,'nlyd-student')?></div>
                    </div>
                    <!-- <div class="signs_row fs_16">
                        <div class="signs_label"><?=__('证件号码', 'nlyd-student')?>：</div>
                        <div class="signs_value c_black"><?=$real_ID?></div>
                    </div> -->
                    <!-- <div class="signs_row fs_16">
                        <div class="signs_label"><?=__('城市信息', 'nlyd-student')?>：</div>
                        <div class="signs_value c_black"><?=$address?></div>
                    </div> -->
                    <a href="<?=home_url('/matchs');?>" class="a-btn"><div><?=__('去赛事中心', 'nlyd-student')?></div></a>
                </div>

                <div class="signs_footer">
                    <div class="img-box logo_img">
                        <img src="<?=student_css_url.'image/nlyd-big.png'?>">
                    </div>
                    <p class="ta_c c_black"><?=__('脑力世界杯组委会', 'nlyd-student')?></p>
                </div>
            </div>
        </div>
    </div>
</div>