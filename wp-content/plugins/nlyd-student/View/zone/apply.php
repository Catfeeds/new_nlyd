
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper have-bottom">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('分中心资料填写', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding layui-row width-margin-pc">
                    <form class="layui-form" lay-filter='layform'>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('训练中心编号', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row" disabled type="text" name="zone_num" value="111"></div>
                        </div>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('训练中心名称', 'nlyd-student')?>：</span>
                                <span class="c_black3"><?=__('规则：IISC+“名字”+国际脑力训练中心+城市', 'nlyd-student')?></span>
                            </div>
                            <div class="input_row"><input class="radius_input_row" type="text" name="zone_name" lay-verify="required" autocomplete="off" placeholder="<?=__('输入您的分中心名字', 'nlyd-student')?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('训练中心营业地址', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row" type="text" name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('输入您的营业地址，与证件保持一致', 'nlyd-student')?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('上传营业执照', 'nlyd-student')?>：</span></div>
                            <div class="input_row img-zoos img-zoos1">
                                <div class="post-img dash">
                                    <div class="add-zoo" data-file="img-zoos1">
                                        <div class="transverse"></div>
                                        <div class="vertical"></div>
                                    </div>
                                </div>
                                <span class="fs_12 c_black3 tips"><?=__('原件影印件或盖有鲜章的复印件，文件不超过2m大小', 'nlyd-student')?></span>
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('法定代表人', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row" type="text" name="legal_person" lay-verify="required" autocomplete="off" placeholder="<?=__('法定代表人姓名', 'nlyd-student')?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('选择对公账户开户行', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row" type="text" name="opening_bank" lay-verify="required" autocomplete="off" placeholder="<?=__('选择对公账户开户行', 'nlyd-student')?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('开户详细地址', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row" type="text" name="opening_bank_address" lay-verify="required" autocomplete="off" placeholder="<?=__('输入对公账户详细开户地址', 'nlyd-student')?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('事业管理员', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row" disabled type="text" name="chairman_id" value="事业管理员">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('中心管理员', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row" disabled type="text" name="name" value="中心管理员">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('管理员电话', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row" disabled type="text" name="name" value="管理员电话">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('管理员证件', 'nlyd-student')?>：</span></div>
                            <div class="input_row img-zoos img-zoos0">
                                <div class="post-img dash">
                                    <div class="add-zoo" data-file="img-zoos0">
                                        <div class="transverse"></div>
                                        <div class="vertical"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="a-btn" lay-filter="layform" lay-submit=""><?=__('提交资料', 'nlyd-student')?></div>
                    </form>
                </div>
            </div>
        </div>            
    </div>
</div>
<input style="display:none;" type="file" name="meta_val" id="img-zoos0" data-this="img-zoos0" value="" accept="image/*"/>
<input style="display:none;" type="file" name="meta_val" id="img-zoos1" data-this="img-zoos1" value="" accept="image/*"/>
<script>
jQuery(function($) { 
    $('.img-zoos').on('click','.add-zoo',function(){//上传图片
        var id=$(this).attr('data-file')
        $('#'+id).click()
    })
    var imgs=[]
    var imgs1=[]
    $('.img-zoos').each(function(){
        var _this=$(this);
        if(_this.hasClass('img-zoos1')){//营业执照
            if(_this.find('.post-img.no-dash').length>=1){
                _this.find('.dash').css('display','none')
            }
        }else if(_this.hasClass('img-zoos0')){//身份证
            if(_this.find('.post-img.no-dash').length>=2){
                _this.find('.dash').css('display','none')
            }
        }
    })

    function changes(e,_this,array) {
        var file=e.target.files[0];
        var fileSize=file.size;
        var fSize=2;
        if(fileSize > 1024*1024*fSize){
            alert("<?=__('图片大小不能大于', 'nlyd-student')?>"+fSize+"M");
            return false;
        }
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
            layer.photos({//图片预览
                photos: '.img-zoos',
                anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            })
            if(className=="img-zoos0"){
                if($('.'+className+' .post-img.no-dash').length>=2){
                    $('.'+className+' .dash').css('display','none')
                }
            }else if(className=="img-zoos1"){
                if($('.'+className+' .post-img.no-dash').length>=1){
                    $('.'+className+' .dash').css('display','none')
                }
            }
        }
        reader.readAsDataURL(file);
        $(e.target).val('')
    }

    $("#img-zoos0").change(function(e) {
        changes(e,$("#img-zoos0"),imgs)
    });
    $("#img-zoos1").change(function(e) {
        changes(e,$("#img-zoos1"),imgs1)
    });
    $('.img-zoos').on('click','.del',function(){//删除图片
        var _this=$(this);
        var index =_this.parents('.post-img').index();
        _this.parents('.img-zoos').find('.post-img.dash').css('display','block');
        _this.parents('.post-img').remove()
        if(_this.parents('.img-zoos').hasClass('img-zoos0')){
            imgs.splice(index, 1);
        }else if(_this.parents('.img-zoos').hasClass('img-zoos1')){
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
        form.verify($.validationLayui.allRules);
        // 监听提交
        form.on('submit(certificationFormBtn)', function(data){//实名认证提交
            
            var fd = new FormData();
            fd.append('action','zone_apply_submit');
            fd.append('zone_num',data.field['zone_num']);
            fd.append('type_id',data.field['type_id']);
            fd.append('zone_name',data.field['zone_name']);
            fd.append('zone_address',data.field['zone_address']);
            fd.append('legal_person',data.field['legal_person']);
            fd.append('opening_bank',data.field['opening_bank']);
            fd.append('opening_bank_address',data.field['opening_bank_address']);
            fd.append('bank_card_num',data.field['bank_card_num']);
            fd.append('chairman_id',data.field['chairman_id']);
            fd.append('secretary_id',data.field['secretary_id']);
            fd.append('business_licence',imgs[0]);
            $.ajax({
                data: fd,
                contentType : false,
                processData : false,
                cache : false,
                success: function(res, textStatus, jqXHR){
                    $.alerts(res.data.info)
                    if(res.data.url){
                        setTimeout(function() {
                            window.location.href=res.data.url
                        }, 300);

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
