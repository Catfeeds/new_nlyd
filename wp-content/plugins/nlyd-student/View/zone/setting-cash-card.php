<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <div class="main-header">
                <header class="mui-bar mui-bar-nav main">
                    <a class="mui-pull-left nl-goback">
                        <div><i class="iconfont">&#xe610;</i></div>
                    </a>
                    <h1 class="mui-title"><div><?=__('添加银行账户', 'nlyd-student')?></div></h1>
                </header>
            </div> 
            <div class="layui-row nl-border nl-content">
                <form class="nl-page-form layui-form width-margin-pc have-bottom">
                    <input type="hidden" name="action" value="set_receivables"/>
                    <input type="hidden" name="type" value="bank"/>
                    <div class="form-inputs">
                        <div class="form-input-row">
                            <div class="form-input-label"><div><?=__('开户姓名', 'nlyd-student')?></div></div>
                            <input type="text" name="open_name" value="<?=$open_name?>" placeholder="<?=__('开户姓名', 'nlyd-student')?>" lay-verify="required"  class="nl-input nl-foucs">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><div><?=__('开户银行', 'nlyd-student')?></div></div>
                            <input type="text" id="opening_bank" name="open_bank" readonly value="<?=$open_bank?>" placeholder="<?=__('开户银行', 'nlyd-student')?>" lay-verify="required"  class="nl-input nl-foucs">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><div><?=__('开户详细地址', 'nlyd-student')?></div></div>
                            <input type="text" name="open_address" value="<?=$open_address?>" placeholder="<?=__('开户详细地址', 'nlyd-student')?>" lay-verify="required"  class="nl-input nl-foucs">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><div><?=__('银行卡号', 'nlyd-student')?></div></div>
                            <input type="tel" name="open_card_num" value="<?=$open_card_num?>" placeholder="<?=__('银行卡号', 'nlyd-student')?>" lay-verify="number"  class="nl-input nl-foucs">
                        </div>
                        <a class="a-btn a-btn-table" id="layuiForm" lay-filter="layuiForm" lay-submit=""><div><?=__('保 存', 'nlyd-student')?></div></a>
                    </div>
                </form>
            </div>
        </div>           
    </div>
</div>
<script>
jQuery(document).ready(function($) {
    var opening_bank_Data=$.validationLayui.back;

    var posiotion_back=[0];//初始化位置，高亮展示
    if($('#opening_bank').length>0){
        if($('#opening_bank').val().length>0 && $('#opening_bank').val()){
            $.each(opening_bank_Data,function(index,value){
                if(value['value']==$('#opening_bank').val()){
                    posiotion_back=[index]
                    return false;
                }
            })
        }
        var mobileSelect4 = new MobileSelect({
            trigger: '#opening_bank',
            title: "<?=__('开户行', 'nlyd-student')?>",
            wheels: [
                {data: opening_bank_Data}
            ],
            position:posiotion_back, //初始化定位 打开时默认选中的哪个 如果不填默认为0
            transitionEnd:function(indexArr, data){
                // console.log(data);
            },
            callback:function(indexArr, data){
                $('#opening_bank').val(data[0]['value']);

            }
        });
    }
        layui.use(['form'], function(){
            var form = layui.form
            form.render();
            // 自定义验证规则
            form.verify($.validationLayui.allRules);
            form.on('submit(layuiForm)', function(data){
                var _this=$(this);
                if(!_this.hasClass('disabled')){
                    $.ajax({
                        data: data.field,
                        beforeSend:function(XMLHttpRequest){
                            _this.addClass('disabled')
                        },
                        success: function(res, textStatus, jqXHR){
                            $.alerts(res.data.info)
                            if(res.data.url){
                                setTimeout(function() {
                                    window.location.href=res.data.url
                                }, 300);
                            }else{
                                _this.removeClass('disabled');
                            }
                        },
                        complete: function(jqXHR, textStatus){
                            if(textStatus=='timeout'){
                                $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
                                _this.removeClass('disabled');
                            }
                            
                        }
                    })
                }else{
                    $.alerts("<?=__('正在处理您的请求..', 'nlyd-student')?>")
                }
                return false;
            });
        });
})

</script>