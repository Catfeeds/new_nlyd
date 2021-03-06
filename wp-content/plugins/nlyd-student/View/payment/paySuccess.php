
<div class="layui-fluid">
    <div class="layui-row">
        <?php
            require_once leo_student_public_view.'leftMenu.php';

            if($row->order_type == 1){
                $url = home_url('/matchs/info/match_id/'.$row->match_id);
            }elseif ($row->order_type == 2){
                $url = home_url('/gradings/info/grad_id/'.$row->match_id);
            }elseif ($row->order_type == 3){
                $url = home_url('/courses/courseDetail/id/'.$row->match_id);
                if(!empty($zone_id)){
                    $url .= '/center_id/'.$zone_id;
                }
            }

        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper layui-bg-white">
        <header class="mui-bar mui-bar-nav">
        <a class="mui-pull-left nl-goback">
            <div><i class="iconfont">&#xe610;</i></div>
        </a>
        <h1 class="mui-title"><div><?=__('支付成功', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="pay-success layui-row">
                    <div class="pay-info"><div class="nl-badge"><i class="iconfont">&#xe608;</i></div><?=__('报名成功', 'nlyd-student')?></div>
                    <?php if(!empty($match_title)): ?>
                        <div class="order-info"><?=__('您已成功报名', 'nlyd-student')?><span>“<?=$match_title?>”</span></div>
                    <?php endif;?>
                    <a class='top' href="<?=$url;?>"><?=__('查看详情', 'nlyd-student')?></a>
                    <!--<a class='bottom' href="javascript:;">订单详情</a>-->
                </div>
            </div>
        </div>           
    </div>
</div>