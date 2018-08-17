<!-- 提现 -->
<!-- <style>
@media screen and (max-width: 991px){
    #content,.detail-content-wrapper{
        background:#fff;
    }
}
</style> -->
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
        <a class="mui-pull-left nl-goback">
        <i class="iconfont">&#xe610;</i>
        </a>
        <h1 class="mui-title">脑币收支记录</h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <form class="nl-page-form layui-form" lay-filter='addAdress'>
                    <div class="form-inputs">
                        <div class="form-input-row">
                            <div>购买抵扣</div>
                            <div>-50</div>
                            <div>2018-07-16</div>
                        </div>
                        <div class="form-input-row">
                            <div>完善个人信息</div>
                            <div>+10</div>
                            <div>2018-07-16</div>
                        </div>
                        <div class="form-input-row">
                            <div>完善个人信息</div>
                            <div>+100</div>
                            <div>2018-07-16</div>
                        </div>
                    </div>
                </form>
                <!-- <div class="no-info-page">
                    <div class="no-info-img">
                        <img src="<?=student_css_url.'image/noInfo/noCoin1047@2x.png'?>">
                    </div>
                    <p class="no-info-text">暂无脑币收支记录</p>
                </div> -->
            </div>
        </div>           
    </div>
</div>

<script>
jQuery(function($) {  
})
</script>