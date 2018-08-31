
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
            <h1 class="mui-title">意见反馈</h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="have-bottom">
                    <form class="layui-form nl-page-form width-margin-pc" lay-filter='suggestions'>
                        <div class="form-inputs">
                            <div class="layui-bg-white suggest-row">
                                <div class="">
                                    <input type="text" id="contact" name="contact" value="" placeholder="您的联系方式" lay-verify="phoneOrEmail"  class="suggest-input nl-foucs">
                                </div>    
                            </div>
                            <div class="layui-bg-white suggest-row">
                                <div class="">
                                    <textarea type="tel" id="contents" name="content" value="" placeholder="您对我们的平台有什么建议或反馈？请告诉我们" lay-verify="required"  class="suggest-textarea nl-foucs"  style="resize:none"></textarea>
                                </div>    
                            </div>

                            <div class="layui-bg-white suggest-row img-zoos">
                                <p class="tps">上传照片(最多四张)</p>
                                <div class="post-img" id="add-img">
                                    <div class="add-zoo">
                                        <div class="transverse"></div>
                                        <div class="vertical"></div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="_wpnonce" id="inputSuggest" value="<?=wp_create_nonce('student_get_ranking_code_nonce');?>">
                            <input style="display:none;" type="file" name="meta_val" id="img" value="" accept="image/*" multiple/>
                            <div class="a-btn" lay-filter="suggestionBtn" lay-submit="">提交意见反馈</div>
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
            imgs.unshift(file)
            var reader = new FileReader();
            var src='';
            //读取File对象的数据
            reader.onload = function(evt){
                //data:img base64 编码数据显示
                var dom='<div class="post-img no-dash">'
                        +'<div class="img-zoo">'
                            +'<img src="'+evt.target.result+'"/>'
                        +'</div>'
                        +'<div class="del">'
                            +'<i class="iconfont">&#xe633;</i>'
                        +'</div>'
                    +'</div>'
                $('.tps').after(dom)
            }
            reader.readAsDataURL(file);
           if(imgs.length==4){
               $('#add-img').css('display','none')
           }
           $(e.target).val('')

        });
        $('.img-zoos').on('click','.del',function(){//删除图片
            var _this=$(this);
            var index =_this.parents('.post-img').index();
            imgs.splice(index, 1);
            _this.parents('.post-img').remove()
            $('#add-img').css('display','block');
        })
        sendloginAjax=function(url,formData){
            //type：确定回调函数
            //url:ajax地址
            //formData:ajax传递的参数
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                dataType:'json',
                timeout:3000,
                success: function(data, textStatus, jqXHR){
                    console.log(data)
                    $.alerts(data.data.info)
                    if(data.data.url){
                        setTimeout(function(){
                            window.location.href=data.data.url
                        },300)
                    }
                    return false;
                }
            });
        }
        layui.use(['form'], function(){
            var form = layui.form
            form.render();
            // 自定义验证规则
            form.verify($.validationLayui.allRules);
            // 监听提交
            form.on('submit(suggestionBtn)', function(data){//实名认证提交
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
                    type: "POST",
                    url: window.admin_ajax+"?date="+new Date().getTime(),
                    data: fd,
                    dataType:'json',
                    timeout:3000,
                    contentType : false,
                    processData : false,
                    cache : false,
                    success: function(res, textStatus, jqXHR){
                        if(res.success){
                            $.alerts(res.data.info)
                            setTimeout(() => {
                                 window.location.reload() 
                            }, 300);
                           
                        }else{
                            $.alerts(res.data.info)
                        }
                        
                    }
                })
                return false;
            });
        });
        $('#loginOut').click(function(){//登出
            sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),{action:'user_logout'})
        })
    })
</script>
