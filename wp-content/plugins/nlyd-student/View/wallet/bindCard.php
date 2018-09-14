<!-- 提现 -->
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12  detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
        <a class="mui-pull-left nl-goback">
        <i class="iconfont">&#xe610;</i>
        </a>
        <h1 class="mui-title">收款账户管理</h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <form class="nl-page-form layui-form" lay-filter='addAdress'>
                    <div class="form-inputs">
                        <div class="form-input-row">
                            <div class="form-input-label">开户姓名</div>
                            <input type="text" name="country" autocomplete="off" class="show-value nl-foucs" placeholder="开户姓名">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label">开户行</div>
                            <input type="text" name="country" autocomplete="off" class="show-value nl-foucs" placeholder="开户行">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label">开户账号</div>
                            <input type="text" name="country" autocomplete="off" class="show-value nl-foucs" placeholder="开户账号">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label">银行预留手机号</div>
                            <input type="text" name="country" autocomplete="off" class="show-value nl-foucs" placeholder="预留手机号">
                        </div>
                    </div>
                    <a class="a-btn" lay-filter="addAccount" lay-submit="">保存</a>
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
        // form.on('submit(addAccount)', function(data){
        //     if(data.field.is_default){
        //         data.field.is_default=1
        //     }
        //     $.post(window.admin_ajax+"?date="+new Date().getTime(),data.field,function(res){
        //         console.log(res)
        //         $.alerts(res.data.info)
        //         if(res.success){
        //             setTimeout(function(){
        //                 window.location.href=
        //             },1600)
        //         }    
        //     })
        //     return false;
        // });
    })

})
</script>