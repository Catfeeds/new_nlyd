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
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">   
            <div class="layui-row nl-border nl-content">
                <div class="signs_box">
                    <div class="img-box">
                        <img src="<?=student_css_url.'image/signs/success.png'?>">
                    </div> 
                    <h1 class="signs_tips">恭喜你，签到成功</h1>
                    <p class="signs_title">“达智优”2018脑力世界杯中国赛</p>
                    <div class="signs_row">
                        <span class="signs_label">选手姓名：</span>
                        <span class="signs_value">孙中则</span>
                    </div>
                    <div class="signs_row">
                        <span class="signs_label">证件号码：</span>
                        <span class="signs_value">5111**********5144</span>
                    </div>
                    <div class="signs_row">
                        <span class="signs_label">城市信息：</span>
                        <span class="signs_value">内蒙古巴彦卓尔</span>
                    </div>
                </div>
            </div>
        </div>           
    </div>
</div>