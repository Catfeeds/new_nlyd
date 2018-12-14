<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('提 现', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding layui-row width-margin-pc">
                    <div class="bold c_black getCash_type"><?=__('选择提现方式', 'nlyd-student')?></div>
                    <form class="layui-form ">
                        <div class="getCash_type_row">
                            <input type="radio" name="getCash_type" value="bank" title="<?=__('提现至银行卡（民生银行 789***9080）', 'nlyd-student')?>" checked>
                        </div>
                        <div class="getCash_type_row">
                            <input type="radio" name="getCash_type" value="wallet" title="<?=__('提现至平台账户钱包', 'nlyd-student')?>">
                        </div>
                        <div class="c_red fs_12">
                            <?=__('*充值进平台的余额只可用于消费，无法再次提现，谨慎操作', 'nlyd-student')?>
                        </div>
                        <div class="enter_num">
                            <div class="danwei bold c_black fs_20">￥</div>
                            <input class="radius_input_row nl-foucs" type="text" name="num" lay-verify="required" autocomplete="off" placeholder="<?=__('输入金额，最多可提现500.00', 'nlyd-student')?>">
                        </div>
                        <a class="a-btn a-btn-table bg_gradient_green" lay-filter="layform" lay-submit=""><div><?=__('提 现', 'nlyd-student')?></div></a>
                    </form>
                </div>  
            </div>
        </div>            
    </div>
</div>

<script>
jQuery(function($) { 
    layui.use(['form'], function(){
        var form = layui.form;
        form.render();
        form.verify($.validationLayui.allRules);
        form.on('submit(layform)', function(data){//实名认证提交
            console.log(data.field)
            $.ajax({
                data:data.field,
                success:function(res){
                    
                },
            })
        })
    })
})
</script>
