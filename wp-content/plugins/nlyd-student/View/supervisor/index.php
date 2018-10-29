<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <div class="main-header">
                <header class="mui-bar mui-bar-nav main">
                    <a class="mui-pull-left nl-goback static" href="<?=home_url('account/')?>">
                        <div><i class="iconfont">&#xe610;</i></div>
                    </a>
                    <h1 class="mui-title"><div><?=__('赛场信息记录', 'nlyd-student')?></div></h1>
                </header>
            </div> 
            <div class="layui-row nl-border nl-content">
                <form class="nl-page-form layui-form width-margin-pc have-bottom">   
                    <div class="form-inputs">
                        <div class="form-input-row">
                            <div class="form-input-label"><div><?=__('选手姓名', 'nlyd-student')?></div></div>
                            <input type="text" name="student_name" value=""  class="nl-input nl-foucs">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><div><?=__('选手座位号', 'nlyd-student')?></div></div>
                            <input type="text" name="seat_number" value="" lay-verify="required"  class="nl-input nl-foucs">
                        </div>
                        <!--<div class="form-input-row">
                            <div class="form-input-label"><div><?/*=__('比赛项目/场次', 'nlyd-student')*/?></div></div>
                            <input type="text" name="match" value="" lay-verify="required"  class="nl-input nl-foucs">
                        </div>-->
                               
                        <div class="layui-bg-white img-zoos img-zoos1">
                            <p class="tps"><?=__('拍照佐证（点击拍照或上传）', 'nlyd-student')?></p>
                            <div class="post-img dash">
                                <div class="add-zoo" data-file="img-zoos1">
                                    <div class="transverse"></div>
                                    <div class="vertical"></div>
                                </div>
                            </div>
                        </div>        
                        <div class="form-input-row">
                            <div class="form-input-label"><div><?=__('备注说明', 'nlyd-student')?></div></div>
                            <input type="text" name="describe" value=""  class="nl-input nl-foucs">
                        </div>       
                        <a class="a-btn a-btn-table" lay-filter="sub" lay-submit=""><div><?=__('提交记录', 'nlyd-student')?></div></a>
                    </div>
                </form>
            </div>
        </div>           
    </div>
</div>
<input style="display:none;" type="file" name="meta_val" id="img-zoos1" data-this="img-zoos1" value="" accept="image/*"/>
<script>
jQuery(document).ready(function($) {
       sendloginAjax=function(formData){
            //type:确定回调函数
            //url:ajax地址
            //formData:ajax传递的参数
            $.ajax({
                data: formData,
                success: function(data, textStatus, jqXHR){
                    $.alerts(data.data.info)
                    if(data.success){
                        if(data.data.url){
                            window.location.href=data.data.url
                        }
                    }
                    return false;
                }
            });
        }
        $('.img-zoos').on('click','.add-zoo',function(){//上传图片
            var id=$(this).attr('data-file')
            $('#'+id).click()
        })
        // var imgs=[]
        var imgs1=[]
        $('.img-zoos').each(function(){
            var _this=$(this);
            if(_this.hasClass('img-zoos1')){//微信
                if(_this.find('.post-img.no-dash').length>=3){
                    _this.find('.post-img.dash').css('display','none')
                }
            }
            // else if(_this.hasClass('img-zoos0')){//身份证
            //     if(_this.find('.post-img.no-dash').length>=3){
            //         _this.find('.post-img.dash').css('display','none')
            //     }
            // }
        })

        function changes(e,_this,array) {
            var file=e.target.files[0];
            array.unshift(file)
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
                $('.'+className+' p').after(dom)
                layer.photos({//图片预览
                    photos: '.img-zoos',
                    anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                })
                // if(className=="img-zoos0"){
                //     if($('.'+className+' .post-img.no-dash').length>=3){
                //         $('.'+className+' .post-img.dash').css('display','none')
                //     }
                // }else 
                if(className=="img-zoos1"){
                    if($('.'+className+' .post-img.no-dash').length>=3){
                        $('.'+className+' .post-img.dash').css('display','none')
                    }
                }
            }
            reader.readAsDataURL(file);
            $(e.target).val('')
        }

        // $("#img-zoos0").change(function(e) {
        //     changes(e,$("#img-zoos0"),imgs)
        // });
        $("#img-zoos1").change(function(e) {
            changes(e,$("#img-zoos1"),imgs1)
        });
        $('.img-zoos').on('click','.del',function(){//删除图片
            var _this=$(this);
            var index =_this.parents('.post-img').index();
            _this.parents('.img-zoos').find('.post-img.dash').css('display','block');
            _this.parents('.post-img').remove()
            // if(_this.parents('.img-zoos').hasClass('img-zoos0')){
            //     imgs.splice(index, 1);
            // }else 
            if(_this.parents('.img-zoos').hasClass('img-zoos1')){
                imgs1.splice(index, 1);
            }
            layer.photos({//图片预览
                    photos: '.img-zoos',
                    anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                }) 
        })
        layui.use(['form'], function(){
            var form = layui.form
            form.render();
            // 自定义验证规则
            // form.verify($.validationLayui.allRules);
            // 监听提交
            form.on('submit(sub)', function(data){//实名认证提交
                var fd = new FormData();
                fd.append('student_name',data.field.student_name);
                fd.append('seat_number',data.field.seat_number);
                fd.append('describe',data.field.describe);
                fd.append('action','upload_match_evidence');
                // $.each(imgs, function (i, v) {
                //     fd.append('images[]',v);
                // })
                $.each(imgs1, function (i, v) {
                    fd.append('evidence[]',v);
                })
                $('.post-img.no-dash input').each(function () {
                    var name=$(this).attr('name')
                    fd.append(name,$(this).val());
                })
                $.ajax({
                    data: fd,
                    contentType : false,
                    processData : false,
                    cache : false,
                    success: function(res, textStatus, jqXHR){
                        if(res.success){
                            if(res.data.info){
                                $.alerts(res.data.info)
                            }
                            if(res.data.url){
                                setTimeout(function() {
                                    window.location.href=res.data.url
                                }, 300);
                                
                            }
                            return false;
                           
                        }else{
                            $.alerts(res.data.info)
                        }
                        
                    }
                })
                return false;
            });
            layer.photos({//图片预览
                photos: '.img-zoos',
                anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            }) 
        });
})

</script>