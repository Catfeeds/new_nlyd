
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($match_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="bold c_blue fs_16 ta_c" style="margin-top:20px;margin-bottom:50px"><?=__('快眼训练设置', 'nlyd-student')?></div>
                <div class="ta_c">
                    <?=__('闪现时间', 'nlyd-student')?>  <input class="nl-foucs" style="border-radius:6px" type="tel" id="flash" name="flash_time" value="800" />  <?=__('毫秒', 'nlyd-student')?>
                </div>
                

                <div class="a-btn a-btn-table" style="position: relative;top:0;margin-top:30px" id="complete" href="<?=$url?>"><div><?=__('开始训练', 'nlyd-student')?></div></div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function($) {
        new AlloyFinger($('#complete')[0], {
            tap:function(){
                var _this=$(this);
                if(!_this.hasClass('disabled')){
                    _this.addClass('disabled')
                    var url = $(this).attr('href')+'/flash_time/'+$('#flash').val();
                    //alert(url);
                    setTimeout(function(){
                        window.location.href = url;
                        _this.removeClass('disabled')
                    }, 800);
                }
            }
        })
    })
</script>