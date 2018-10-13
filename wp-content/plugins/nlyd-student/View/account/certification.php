<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <div class="main-header">
                <header class="mui-bar mui-bar-nav">
                    <a class="mui-pull-left nl-goback">
                        <i class="iconfont">&#xe610;</i>
                    </a>
                    <h1 class="mui-title">实名认证</h1>
                </header>
            </div> 
            <div class="layui-row nl-border nl-content">
                <!-- 实名认证 -->
                        <form class="layui-form nl-page-form width-margin-pc have-bottom" lay-filter='certificationForm'>
                            <div class="form-inputs">
                                <div class="form-input-row">
                                    <div class="form-input-label">国籍</div>
                                    <input class="nl-input" value='' readonly  id="trigger4" placeholder="选择国籍">
                                    <span class="form-input-right"><img id="flags" style="width:16px;height:11px;" src=""></span>
                                    
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">证件类型</div>
                                    <input value='<?= !empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_type_c'] : '';?>' type="text" readonly id="trigger1" placeholder="选择证件类型" class="nl-input" lay-verify="required">
                                    <input value='<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_type'] : '';?>'  type="hidden" name="meta_val[real_type]" id="trigger2">
                                    <input  type="hidden" name="action" value="student_saveInfo"/>
                                    <input type="hidden" name="_wpnonce" id="student_saveInfo_code_nonce" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">
                                    <input  type="hidden" name="meta_key" value="user_real_name"/>
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">证件号码</div>
                                    <input type="text" name="meta_val[real_ID]" id="meta_val[real_ID]" value="<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_ID'] : '';?>" placeholder="输入证件上的真实证件号" lay-verify="required"  class="nl-input nl-foucs">
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">姓 名</div>
                                    <input type="text" name="meta_val[real_name]" id="meta_val[real_name]" value="<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_name'] : '';?>" placeholder="输入证件上的真实姓名" lay-verify="chineseName" class="nl-input nl-foucs">
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">性 别</div>
                                    <input name='user_gender' value='<?=isset($user_info['user_gender']) ? $user_info['user_gender'] : '';?>' type="text" readonly id="trigger3" placeholder="请选择您的性别" class="nl-input" lay-verify="required">
                                </div>
                                <div class="form-input-row" id="birth" style="display:none">
                                    <div class="form-input-label">生 日</div>
                                    <input class="nl-input" value='' readonly  id="birthdaySelect" placeholder="选择生日">
                                    <input  type="hidden" id="year" name="user_birthday[year]" value=""/>
                                    <input  type="hidden" id="month" name="user_birthday[month]" value="">
                                    <input  type="hidden" id="day" name="user_birthday[day]" value=""/>
                                </div>
                                <div class="form-input-row" id="age" style="display:block">
                                    <div class="form-input-label">年 龄</div>
                                    <input type="text" name="meta_val[real_age]" readonly id="meta_val[real_age]" value="<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_age'] : '';?>" placeholder="年龄" lay-verify="required"  class="nl-input nl-foucs">
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">所在城市</div>
                                    <input readonly id="areaSelect" type="text" placeholder="所在城市" value="<?=!empty($user_info['user_address']) ? $user_info['user_address']['province'].$user_info['user_address']['city'].$user_info['user_address']['area'] : ''?>" class="nl-input" lay-verify="required">
                                    <input  type="hidden" id="province" name="user_address[province]" value="<?=!empty($user_info['user_address']) ? $user_info['user_address']['province'] : ''?>"/>
                                    <input  type="hidden" id="city" name="user_address[city]" value="<?=!empty($user_info['user_address']) ? $user_info['user_address']['city'] : ''?>">
                                    <input  type="hidden" id="area" name="user_address[area]" value="<?=!empty($user_info['user_address']) ? $user_info['user_address']['area'] : ''?>"/>
                                </div>
                                <div class="layui-bg-white img-zoos">
                                    <p class="tps">上传身份证</p>
                                        <?php if(!empty($user_info['user_ID_Card'])){ ?>
                                        <?php foreach ($user_info['user_ID_Card'] as $val){ ?>
                                        <div class="post-img no-dash">
                                            <div class="img-zoo img-box">
                                                <img src="<?=$val?>"/>
                                            </div>
                                            <input type="hidden" name="user_ID_Card[]" value="<?=$val?>" />
                                            <div class="del">
                                                <i class="iconfont">&#xe633;</i>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php } ?>
                                    <div class="post-img" id="add-img">
                                        <div class="add-zoo">
                                            <div class="transverse"></div>
                                            <div class="vertical"></div>
                                        </div>
                                    </div>
                                </div>
                                <a class="a-btn" id="certificationFormBtn" lay-filter="certificationFormBtn" lay-submit="">更新实名认证</a>
                                <input type="hidden" class="sbu_type" name="type" value="<?=$_GET['type']?>">
                            </div>
                        </form>
                    <!-- <a class="a-btn certificationFormBtn">更新实名认证</a> -->
            </div>
        </div>           
    </div>
<input style="display:none;" type="file" name="meta_val" id="img" value="" accept="image/*" multiple/>
<input style="display:none;" type="file" name="meta_val" id="file" class="file" value="" accept="image/*" multiple/>
<input type="hidden" name="_wpnonce" id="inputImg" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">
</div>
<script>
jQuery(document).ready(function($) {

        $("input[name='meta_val[real_ID]']").focusout(function(){//身份证号码输入框失焦事件
            var value=$(this).val();
            var datas={
                real_ID:value,
                action:'reckon_age',
            }
            $.ajax({
                data: datas,
                success: function(data, textStatus, jqXHR){
                    if(data.success){
                        $("input[name='meta_val[real_age]']").val(data.data.info);
                    }else{
                        $.alerts(data.data.info);
                    }
                    return false;
                }
            });
        })
        $('.certificationFormBtn').click(function(){
            $('#certificationFormBtn').click()
        })
        //模拟手机下拉列表，选择证件类型
        var certificationSelectData= [
            {key:'sf',value:'身份证'},
            {key:'jg',value:'军官证'},
            {key:'hz',value:'护照'},
            {key:'tb',value:'台胞证'},
            {key:'ga',value:'港澳证'},
        ];
        var posiotionCertification=[0]
        if(typeof($('#trigger1').val())!='undefined'){
            if($('#trigger1').val().length>0){
                $.each(certificationSelectData,function(index,value){
                    if(value.value==$('#trigger1').val()){
                        posiotionCertification=[index]
                        return false;
                    }
                })
            }
        }
        var mobileSelect1 = new MobileSelect({
            trigger: '#trigger1',
            title: '证件类型',
            wheels: [
                {data: certificationSelectData}
            ],
            position:posiotionCertification, //初始化定位 打开时默认选中的哪个 如果不填默认为0
            transitionEnd:function(indexArr, data){
                // console.log(data);
            },
            callback:function(indexArr, data){
                $('#trigger1').val(data[0].value)
                $('#trigger2').val(data[0].key)
            }
        });
        // 模拟手机下拉列表，选择性别
        var sexSelectData= ['男','女',]
        var posiotionSex=[0];//初始化位置，高亮展示
        if($('#trigger3').val().length>0 && $('#trigger3').val()){
            $.each(sexSelectData,function(index,value){
                if(value==$('#trigger3').val()){
                    posiotionSex=[index]
                    return false;
                }
            })
        }
        var mobileSelect2 = new MobileSelect({
            trigger: '#trigger3',
            title: '性别',
            wheels: [
                {data: sexSelectData}
            ],
            position:posiotionSex, //初始化定位 打开时默认选中的哪个 如果不填默认为0
            transitionEnd:function(indexArr, data){
                // console.log(data);
            },
            callback:function(indexArr, data){
                $('#trigger3').val(data[0])
            }
        });

        //省市区三级联动
        var posiotionarea=[0,0,0];//初始化位置，高亮展示
        if($('#areaSelect').val().length>0 && $('#areaSelect').val()){
            var areaValue=$('#areaSelect').val()
            $.each($.validationLayui.allArea.area,function(index,value){
                if(areaValue.indexOf(value.value)!=-1){
                    // console.log(value)
                    posiotionarea=[index,0,0];
                    $.each(value.childs,function(i,v){
                        if(areaValue.indexOf(v.value)!=-1){
                            posiotionarea=[index,i,0];
                            $.each(v.childs,function(j,val){
                                if(areaValue.indexOf(val.value)!=-1){
                                    posiotionarea=[index,i,j];
                                }
                            })
                        }
                    })
                }
            })
        }
        var mobileSelect3 = new MobileSelect({
            trigger: '#areaSelect',
            title: '地址',
            wheels: [
                {data: $.validationLayui.allArea.area},
            ],
            position:posiotionarea, //初始化定位 打开时默认选中的哪个 如果不填默认为0
            transitionEnd:function(indexArr, data){

            },
            callback:function(indexArr, data){
                var text=data[0]['value']+data[1]['value']+data[2]['value'];
                $('#province').val(data[0]['value']);
                $('#city').val(data[1]['value']);
                $('#area').val(data[2]['value']);
                $('#areaSelect').val(text);
            }
        });
        //选择生日
        var posiotionBirthday=[0,0,0];//初始化位置，高亮展示
        // if($('#birthdaySelect').val().length>0 && $('#birthdaySelect').val()){
        //     var birthdayValue=$('#birthdaySelect').val()
        //     $.each($.validationLayui.allArea.area,function(index,value){
        //         if(birthdayValue.indexOf(value.value)!=-1){
        //             // console.log(value)
        //             posiotionBirthday=[index,0,0];
        //             $.each(value.childs,function(i,v){
        //                 if(birthdayValue.indexOf(v.value)!=-1){
        //                     posiotionBirthday=[index,i,0];
        //                     $.each(v.childs,function(j,val){
        //                         if(birthdayValue.indexOf(val.value)!=-1){
        //                             posiotionBirthday=[index,i,j];
        //                         }
        //                     })
        //                 }
        //             })
        //         }
        //     })
        // }
        var mobileSelect5 = new MobileSelect({
            trigger: '#birthdaySelect',
            title: '生日',
            wheels: [
                {data:  $.validationLayui.dates},
            ],
            position:posiotionBirthday, //初始化定位 打开时默认选中的哪个 如果不填默认为0
            transitionEnd:function(indexArr, data){

            },
            callback:function(indexArr, data){
                var text=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value'];
                $('#year').val(data[0]['value'])
                $('#month').val(data[1]['value'])
                $('#day').val(data[2]['value'])
                $('#birthdaySelect').val(text);
            }
        });

        if($('#trigger4').val()=="中华人民共和国"){
            $('#birth').css('display','none')
            $('#age').css('display','block')
        }else{
            $('#birth').css('display','block')
            $('#age').css('display','none')
        }
        // 模拟手机下拉列表，选择国籍
        var contrySelectData=$.validationLayui.contry;
        var posiotioncontry=[0];//初始化位置，高亮展示
        if($('#trigger4').val().length>0 && $('#trigger4').val()){
            $.each(contrySelectData,function(index,value){
                if(value==$('#trigger4').val()){
                    posiotioncontry=[index]
                    return false;
                }
            })
        }
        var mobileSelect4 = new MobileSelect({
            trigger: '#trigger4',
            title: '国籍',
            wheels: [
                {data: contrySelectData}
            ],
            position:posiotioncontry, //初始化定位 打开时默认选中的哪个 如果不填默认为0
            transitionEnd:function(indexArr, data){
                // console.log(data);
            },
            callback:function(indexArr, data){
                // console.log(data)
                $('#trigger4').val(data[0]['value'])
                $('#flags').attr('src',window.home_url+"/wp-content/plugins/nlyd-student/Public/css/image/flags/"+data[0]['src']+".png")
                if(data[0]['value']=="中华人民共和国"){
                    $('#birth').css('display','none')
                    $('#age').css('display','block')
                }else{
                    $('#birth').css('display','block')
                    $('#age').css('display','none')
                }
            }
        });

        sendloginAjax=function(formData){
            //type：确定回调函数
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
            $('#img').click()
        })
        var imgs=[]
        if($('.post-img.no-dash').length>=3){
            $('#add-img').css('display','none')
        }
        $("#img").change(function(e) {
            var file=e.target.files[0];
            imgs.unshift(file)
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
                $('.tps').after(dom)
                layer.photos({//图片预览
                    photos: '.img-zoos',
                    anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                })
                if($('.post-img.no-dash').length>=3){
                    $('#add-img').css('display','none')
                }
            }
            reader.readAsDataURL(file);
            $(e.target).val('')

    
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
            form.on('submit(certificationFormBtn)', function(data){//实名认证提交
                var match_id=$.Request('match_id')
                var fd = new FormData();
                fd.append('action',data.field.action);
                fd.append('_wpnonce',data.field._wpnonce);
                fd.append('meta_key',data.field.meta_key);
                fd.append('meta_val[real_type]',data.field['meta_val[real_type]']);
                fd.append('meta_val[real_name]',data.field['meta_val[real_name]']);
                fd.append('meta_val[real_ID]',data.field['meta_val[real_ID]']);
                fd.append('user_gender',data.field.user_gender);
                fd.append('meta_val[real_age]',data.field['meta_val[real_age]']);
                fd.append('user_address[area]',data.field['user_address[area]']);
                fd.append('user_address[city]',data.field['user_address[city]']);
                fd.append('user_address[province]',data.field['user_address[province]']);
                fd.append('type',$('.sbu_type').val());

                if(match_id!=null){
                    fd.append('match_id',match_id);
                }else{
                    fd.append('match_id','');
                }
                $.each(imgs, function (i, v) {
                    fd.append('images[]',v);
                })
                $('.post-img.no-dash input').each(function () {
                    fd.append('user_ID_Card[]',$(this).val());
                })
                $.ajax({
                    data: fd,
                    contentType : false,
                    processData : false,
                    cache : false,
                    success: function(res, textStatus, jqXHR){
                        if(res.success){
                            $.alerts(res.data.info)
                            if(res.data.url){
                                window.location.href=res.data.url
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
