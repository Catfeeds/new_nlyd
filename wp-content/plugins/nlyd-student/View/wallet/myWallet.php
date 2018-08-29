<!--我的钱包-->
<style>
@media screen and (max-width: 991px){
    #content,.detail-content-wrapper{
        background:#f6f6f6;
    }
}
</style>
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
            <h1 class="mui-title">我的钱包</h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-margin">
                    <div class="wallet-title">
                        <span class="wallet-flex">余额：<span class="bold">4200.00</span></span>
                        <span class="wallet-flex">脑币：<span class="bold">0</span></span>
                        <span class="wallet-flex">银行卡：<span class="bold">1</span></span>
                    </div>
                    <div class="wallet-row">
                        <div class="wallet-row-title c_blue">
                            <div class="pull-left">
                                <i class="iconfont"></i>
                                <span>余 额</span>
                            </div>
                            <div class="pull-right wallet-btn">
                                <a href="<?=home_url('wallet/balanceWater');?>" class="c_blue">收支记录</a>
                                <a href="<?=home_url('wallet/makeCash');?>" class="c_blue">提 现</a>
                            </div>
                        </div>
                        <div class="row-detail">
                            <span>乐学乐分享学费全额补贴</span>
                            <span>+￥3200.00</span>
                            <span>2018-07-16</span>
                        </div>
                        <div class="row-detail">
                            <span>乐学乐分享补贴</span>
                            <span>+￥3200.00</span>
                            <span>2018-07-16</span>
                        </div>
                        <div class="dot">
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                        </div>
                    </div>

                    <div class="wallet-row">
                        <div class="wallet-row-title c_blue">
                        <div class="pull-left">
                                <i class="iconfont"></i>
                                <span>脑 币</span>
                            </div>
                            <div class="pull-right wallet-btn">
                                <a href="<?=home_url('wallet/coinWaterList');?>" class="c_blue">更多脑币记录</a>
                            </div>
                        </div>
                        <div class="row-detail">
                            <span>连续登录21天</span>
                            <span class="c_blue">+10脑币</span>
                            <span>2018-07-16</span>
                        </div>
                        <div class="row-detail">
                            <span>完善个人资料</span>
                            <span class="c_blue">+10脑币</span>
                            <span>2018-07-16</span>
                        </div>
                        <div class="dot">
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                            <div class="t"></div>
                            <div class="w"></div>
                        </div>
                    </div>

                    <div class="wallet-row">
                        <div class="wallet-row-title c_blue">
                        <div class="pull-left">
                                <i class="iconfont"></i>
                                <span>收款账户</span>
                            </div>
                            <div class="pull-right wallet-btn">
                                <a href="<?=home_url('wallet/bindCard');?>" class="c_blue">更 换</a>
                            </div>
                        </div>
                        <div class="card-info">
                            民生银行（储蓄卡） **** **** **** 1234
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