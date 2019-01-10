
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
            <h1 class="mui-title"><div><?=__('意见反馈', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="have-bottom">
                    <form class="layui-form nl-page-form width-margin-pc" lay-filter='suggestions'>
                        <div class="form-inputs">
                            <div class="layui-bg-white suggest-row">
                                <div class="">
                                    <input type="text" id="contact" name="contact" value="" placeholder="<?=__('您的联系方式', 'nlyd-student')?>" lay-verify="phoneOrEmail"  class="suggest-input nl-foucs">
                                </div>    
                            </div>
                            <div class="layui-bg-white suggest-row">
                                <div class="">
                                    <textarea type="tel" id="contents" name="content" value="" placeholder="<?=__('您对我们的平台有什么建议或反馈？请告诉我们', 'nlyd-student')?>" lay-verify="required|filterSqlStr|validate"  class="suggest-textarea nl-foucs"  style="resize:none"></textarea>
                                </div>    
                            </div>

                            <div class="layui-bg-white suggest-row img-zoos">
                                <p class="tps"><?=__('上传照片(最多四张)', 'nlyd-student')?></p>
                                <div class="post-img" id="add-img">
                                    <div class="add-zoo">
                                        <div class="transverse"></div>
                                        <div class="vertical"></div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="_wpnonce" id="inputSuggest" value="<?=wp_create_nonce('student_get_ranking_code_nonce');?>">
                            <input style="display:none;" type="file" name="meta_val" id="img" value="" accept="image/*"/>
                            <a class="a-btn a-btn-table" lay-filter="suggestionBtn" lay-submit=""><div><?=__('提交意见反馈', 'nlyd-student')?></div></a>
                        </div>   
                    </form>
                </div>
            </div>
        </div>           
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
        $('.img-zoos').on('click','.add-zoo',function(){//上传图片
            $('#img').click()
        })
        var imgs=[]
        $("#img").change(function(e) {
            var file=e.target.files[0];
            if( !file.type.match(/.png|.jpg|.jpeg/) ) {
                alert("<?=__('上传错误,文件格式必须为', 'nlyd-student')?>：png/jpg/jpeg");
                return;  
            }
            //读取File对象的数据
            imgCompress(file,function(imgBase64){
                imgBase64 = imgBase64;    //存储转换的base64编码
                imgs.unshift(imgBase64)
                //data:img base64 编码数据显示
                var dom='<div class="post-img no-dash">'
                        +'<div class="img-zoo img-box">'
                            +'<img src="'+imgBase64+'"/>'
                        +'</div>'
                        +'<div class="del">'
                            +'<i class="iconfont">&#xe633;</i>'
                        +'</div>'
                    +'</div>'
                $('.tps').after(dom)
                layer.photos({//图片预览
                    photos: '.img-zoos',
                    anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                }) 
                if(imgs.length==4){
                    $('#add-img').css('display','none')
                }
                $(e.target).val('')
            });
        });
        $('.img-zoos').on('click','.del',function(){//删除图片
            var _this=$(this);
            var index =_this.parents('.post-img').index();
            imgs.splice(index, 1);
            _this.parents('.post-img').remove()
            $('#add-img').css('display','block');
            layer.photos({//图片预览
                photos: '.img-zoos',
                anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            }) 
        })
        layui.use(['form'], function(){
            var form = layui.form
            form.render();
            // 自定义验证规则
            form.verify($.validationLayui.allRules);
            // 监听提交
            form.on('submit(suggestionBtn)', function(data){
                var _this=$(this);
                if(!_this.hasClass('disabled')){
                    var fd = new FormData();
                    var contact=$('#contact').val()
                    var content=$('#contents').val()
                    fd.append('action','feedback');
                    fd.append('_wpnonce',$("#inputSuggest").val());
                    fd.append('contact',contact);
                    fd.append('content',content);
                    $.each(imgs, function (i, v) {
                        fd.append('images[]',v);
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
                            if(res.success){
                                $.alerts(res.data.info)
                                setTimeout(function() {
                                    window.location.reload() 
                                }, 300);
                            
                            }else{
                                $.alerts(res.data.info)
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
