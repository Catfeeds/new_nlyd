<style>

@media screen and (max-width: 991px){
    #content{
        background:#fff;
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
        <h1 class="mui-title">支付成功</h1>
        </header>
            <div class="layui-row nl-border nl-content layui-bg-gray">
                <div class="pay-success layui-row">
                    <div class="pay-info">成功报名</div>
                    <div class="btn-zoo">
                        <div class="btn-z"><a class='left' href="<?=home_url('/account/matchList/?action=info&match_id='.$row->match_id);?>">比赛详情</a></div>
                        <div class="btn-z"><a class='right' href="<?=home_url('/account/order/?action=info&order_id='.$row->id);?>">订单详情</a></div>
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