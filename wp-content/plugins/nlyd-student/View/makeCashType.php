<!-- 提现 -->
<style>
@media screen and (max-width: 991px){
    #content,.detail-content-wrapper{
        background:#fff;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_left_path.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title">提现方式</h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <form class="nl-page-form layui-form" lay-filter='addAdress'>
                    <div class="form-inputs">
                        <a class="form-input-row">
                            <div class="form-input-label"><div class="circle-metal">民</div></div>
                            <div class="nl-input">民生银行 尾号2630 储蓄卡</div>
                        </a>
                        <a class="form-input-row">
                            <div class="form-input-label"><div class="circle-metal">微</div></div>
                            <div class="nl-input">微信零钱包</div>
                        </a>
                        <a class="form-input-row">
                            <div class="form-input-label"><div class="circle-metal">支</div></div>
                            <div class="nl-input">支付宝余额</div>
                        </a>
                    </div>
                </form>
            </div>
        </div>           
    </div>
</div>


<script>
jQuery(function($) { 

})
</script>