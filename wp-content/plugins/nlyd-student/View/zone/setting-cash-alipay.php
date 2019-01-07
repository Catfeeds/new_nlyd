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
                    <h1 class="mui-title"><div><?=__('上传收款二维码', 'nlyd-student')?></div></h1>
                </header>
            </div> 
            <div class="layui-row nl-border nl-content">
                <form class="nl-page-form layui-form width-margin-pc have-bottom">   
                    <div class="input_row img-zoos img-zoos1 upload_qr_code">
                        <?php if((!empty($row['business_licence_url']))){?>
                            <input type="hidden" name="business_licence_url" class="business_licence_url" value="<?=$row['business_licence_url']?>">
                            <div class="post-img no-dash">
                                <div class="img-zoo img-box">
                                    <img src="<?=$row['business_licence_url']?>"/>
                                </div>
                                <div class="del">
                                    <i class="iconfont">&#xe633;</i>
                                </div>
                            </div>

                        <?php }?>
                        <div class="post-img dash">
                            <div class="add-zoo img-box" data-file="img-zoos1">
                                <!-- <div class="transverse"></div>
                                <div class="vertical"></div> -->
                                <img src="<?=student_css_url.'image/zone/upload_bg.png'?>"/>
                            </div>
                        </div>
                        
                    </div>
                    <div class="input_row ta_c mt_10">
                    <?=__('上传不设金额的收款二维码', 'nlyd-student')?>
                    </div>
                    <a class="a-btn a-btn-table" id="layuiForm" lay-filter="layuiForm" lay-submit=""><div><?=__('保 存', 'nlyd-student')?></div></a>
                </form>
            </div>
        </div>           
    </div>
</div>
<input style="display:none;" type="file" name="meta_val" id="img-zoos1" data-this="img-zoos1" value="" accept="image/*"/>
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

    $('.img-zoos').on('click','.add-zoo',function(){//上传图片
        var id=$(this).attr('data-file')
        $('#'+id).click()
    })
    var imgs1=[]
    $('.img-zoos').each(function(){
        var _this=$(this);
        if(_this.hasClass('img-zoos1')){//营业执照
            if(_this.find('.post-img.no-dash').length>=1){
                _this.find('.dash').css('display','none')
            }
        }
    })
    function changes(e,_this,array) {
        var file=e.target.files[0];
        // var fileSize=file.size;
        // var fSize=2;
        // if(fileSize > 1024*1024*fSize){
        //     alert("<?=__('图片大小不能大于', 'nlyd-student')?>"+fSize+"M");
        //     return false;
        // }
        array.unshift(file)
        console.log(array)
        var reader = new FileReader();
        var src='';
        //读取File对象的数据
        reader.onload = function(evt){
            //data:img base64 编码数据显示
            var dom='<div class="post-img no-dash">'
                +'<div class="img-zoo img-box">'
                +'<img src="'+evt.target.result+'"/>'
                +'</div>'
                +'<div class="del">'
                +'<i class="iconfont">&#xe633;</i>'
                +'</div>'
                +'</div>'
            var className=_this.attr('data-this')
            $('.'+className).prepend(dom)
          
            if(className=="img-zoos1"){
                if($('.'+className+' .post-img.no-dash').length>=1){
                    $('.'+className+' .dash').css('display','none')
                }
            }
        }
        reader.readAsDataURL(file);
        $(e.target).val('')
    }

    $("#img-zoos1").change(function(e) {
        changes(e,$("#img-zoos1"),imgs1)
    });
    $('body').on('click','.del',function(){//删除图片
        var _this=$(this);
        var index =_this.parents('.post-img').index();
        _this.parents('.img-zoos').find('.post-img.dash').css('display','block');

        if(_this.parents('.img-zoos').hasClass('img-zoos1')){
            imgs1.splice(index, 1);
            $('.business_licence_url').val('')
        }
        _this.parents('.post-img').remove();
    })
    layui.use(['form'], function(){
        var form = layui.form
        form.render();
        // 自定义验证规则
        form.verify($.validationLayui.allRules);
        form.on('submit(layuiForm)', function(data){
            var _this=$(this);
            if(!_this.hasClass('disabled')){
                var fd = new FormData();
                $.each(imgs1, function (i, v) {
                    fd.append('images_aliPay[]',v);
                })
                $.ajax({
                    data: fd,
                    contentType : false,
                    processData : false,
                    cache : false,
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