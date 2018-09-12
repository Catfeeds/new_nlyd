
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper layui-bg-white">
        <header class="mui-bar mui-bar-nav">
        <a class="mui-pull-left nl-goback">
            <i class="iconfont">&#xe610;</i>
        </a>
        <h1 class="mui-title">支付成功</h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="pay-success layui-row">
                    <div class="pay-info"><div class="nl-badge"><i class="iconfont">&#xe608;</i></div>报名成功</div>
                    <?php if(!empty($match_title)): ?>
                        <div class="order-info">您已成功报名<span>“<?=$match_title?>”</span></div>
                    <?php endif;?>
                    <a class='top' href="<?=home_url('matchs/info/match_id/'.$row->match_id);?>">比赛详情</a>
                    <!--<a class='bottom' href="javascript:;">订单详情</a>-->
                </div>
            </div>
        </div>           
    </div>
</div>