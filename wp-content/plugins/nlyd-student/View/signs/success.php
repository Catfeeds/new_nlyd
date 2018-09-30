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
                    <h3 class="signs_tips">恭喜你，签到成功</h3>
                    <p class="signs_title fs_16 c_black">“达智优”2018脑力世界杯中国赛</p>
                    <div class="signs_row fs_16">
                        <span class="signs_label c_black">选手姓名：</span>
                        <span class="signs_value"><?=$real_name?></span>
                    </div>
                    <div class="signs_row fs_16">
                        <span class="signs_label c_black">证件号码：</span>
                        <span class="signs_value"><?=$real_ID?></span>
                    </div>
                    <div class="signs_row fs_16">
                        <span class="signs_label c_black">城市信息：</span>
                        <span class="signs_value"><?=$address?></span>
                    </div>
                    <a href="<?=home_url('/matchs');?>" class="go_match"><span>去赛事中心</span></a>
                </div>

                <div class="signs_footer">
                    <div class="img-box logo_img">
                        <img src="<?=student_css_url.'image/nlyd-big.png'?>">
                    </div> 
                    <!--<p class="ta_c fs_16">脑力世界杯赛事</p>-->
                </div>
            </div>
        </div>           
    </div>
</div>