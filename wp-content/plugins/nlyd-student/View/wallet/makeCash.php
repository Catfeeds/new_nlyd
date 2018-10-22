<!-- 提现 -->
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12  detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
                <div><i class="iconfont">&#xe610;</i></div>
            </a>
            <h1 class="mui-title"><div><?=__('提现', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <form class="nl-page-form layui-form" lay-filter='addAdress'>
                    <div class="form-inputs">
                        <div class="form-input-row">
                            <div class="form-input-label">提现至</div>
                            <div class="show-value">民生银行 尾号2630 储蓄卡</div>
                            <a href="<?=home_url('wallet/makeCashType');?>" class="form-input-right c_blue">更 换</a>
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label">提现金额</div>
                            <i class="iconfont nl-icon">&#xe61e;</i>
                            <input type="tel" name="country" autocomplete="off" class="show-value nl-foucs" placeholder="本次最多提现900.00">
                            
                        </div>
                    </div>
                    <a class="a-btn" lay-filter="makeCash" lay-submit=""><?=__('保存', 'nlyd-student')?></a>
                </form>
            </div>
        </div>           
    </div>
</div>

<script>
jQuery(function($) { 
layui.use(['layer','form'], function(){
    var form = layui.form
    form.verify($.validationLayui.allRules);
    form.on('submit(makeCash)', function(data){//新增修改地址
        if(data.field.is_default){
            data.field.is_default=1
        }
        $.post(window.admin_ajax+"?date="+new Date().getTime(),data.field,function(res){
            console.log(res)
            $.alerts(res.data.info)
            if(res.success){
                setTimeout(function(){
                    // window.location.href=
                },1600)
            }    
        })
        return false;
    });
})

})
</script>