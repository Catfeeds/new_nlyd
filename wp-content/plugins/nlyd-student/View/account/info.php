<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('account/')?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('个人资料', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <form class="nl-page-form layui-form width-margin-pc" style="padding-bottom:200px">   
                
                    <div class="nl-form-tips width-padding width-padding-pc"><?=__('为了保证您考级及比赛的真实有效性，请您确保个人资料准确无误', 'nlyd-student')?></div>
                    <div class="form-inputs">
                        <div class="form-input-row no_edit">
                            <div class="form-input-label"><div><?=__('用户账号', 'nlyd-student')?></div></div>
                            <div class="nl-input"><div><?=$user_info['contact']?></div></div>
                        </div>
                        <div class="form-input-row no_edit">
                            <div class="form-input-label"><div>ID</div></div>
                            <div class="nl-input"><div><?=isset($user_info['user_ID']) ? $user_info['user_ID'] : '';?></div></div>
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><div><span class="c_red">*</span><?=__('用户姓名', 'nlyd-student')?></div></div>
                            <input type="text" name="meta_val[real_name]" id="meta_val[real_name]" value="<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_name'] : '';?>" placeholder="<?=__('输入证件上的真实姓名（必填）', 'nlyd-student')?>" class="nl-input nl-foucs" lay-verify="required|validate">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><div><span class="c_red">*</span><?=__('性 别', 'nlyd-student')?></div></div>
                            <input name='user_gender' data-value='<?=isset($user_info['user_gender']) ? $user_info['user_gender'] : '';?>' value='<?=isset($user_info['user_gender']) ? __($user_info['user_gender'],'nlyd-student') : '';?>' type="text" readonly id="trigger3" placeholder="<?=__('请选择您的性别（必填）', 'nlyd-student')?>" class="nl-input">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><div><span class="c_red">*</span><?=__('证件类型', 'nlyd-student')?></div></div>
                            <input value='<?= !empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_type_c'] : '';?>' type="text" readonly id="trigger1" placeholder="<?=__('选择证件类型（必填）', 'nlyd-student')?>" class="nl-input" lay-verify="required">
                            <input value='<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_type'] : '';?>'  type="hidden" name="meta_val[real_type]" id="trigger2">
                            <input  type="hidden" name="action" value="student_saveInfo"/>
                            <input type="hidden" name="_wpnonce" id="student_saveInfo_code_nonce" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">
                            <input  type="hidden" name="meta_key" value="user_real_name"/>
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><div><span class="c_red">*</span><?=__('证件号码', 'nlyd-student')?></div></div>
                            <input type="text" name="meta_val[real_ID]" id="meta_val[real_ID]" value="<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_ID'] : '';?>" placeholder="<?=__('输入证件上的真实证件号（必填）', 'nlyd-student')?>" lay-verify="required|filterSqlStr|validate"  class="nl-input nl-foucs">
                        </div>
                        <div class="form-input-row" id="age" style="display:block">
                            <div class="form-input-label"><div><span class="c_red">*</span><?=__('年 龄', 'nlyd-student')?></div></div>
                            <input type="text" name="meta_val[real_age]" readonly id="meta_val[real_age]" value="<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_age'] : '';?>" placeholder="<?=__('年龄', 'nlyd-student')?>"  class="nl-input nl-foucs">
                        </div>
                        <div class="form-input-row" id="birth" style="display:none">
                            <div class="form-input-label"><div><span class="c_red">*</span><?=__('生 日', 'nlyd-student')?></div></div>
                            <input class="nl-input" name="birthday" value='<?=$user_info['user_birthday']?>' readonly  id="birthdaySelect" placeholder="<?=__('选择生日（必填）', 'nlyd-student')?>">
                        </div>
                        <div class="form-input-row img-zoos img-zoos0" style="padding:0">
                            <div class="form-input-label pull-left"><div><?=__('上传证件照片', 'nlyd-student')?></div></div>
                            <?php if(!empty($user_info['user_ID_Card'])){ ?>
                                <?php foreach ($user_info['user_ID_Card'] as $val){ ?>
                                <div class="post-img no-dash"  style="top:3px">
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
                            <div class="post-img dash"  style="top:3px">
                                <div class="add-zoo" data-file="img-zoos0" >
                                    <div class="transverse"></div>
                                    <div class="vertical"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-input-row img-zoos img-zoos2" style="padding:0">
                            <div class="form-input-label pull-left"><div><?=__('上传彩色寸照', 'nlyd-student')?></div></div>
                            <?php if(!empty($user_info['user_images_color'])){ ?>
                                <?php foreach ($user_info['user_images_color'] as $value){ ?>
                                <div class="post-img no-dash"  style="top:3px">
                                    <div class="img-zoo img-box">
                                        <img src="<?=$value?>"/>
                                    </div>
                                    <input type="hidden" name="user_images_color[]" value="<?=$value?>" />
                                    <div class="del">
                                        <i class="iconfont">&#xe633;</i>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php } ?>
                            <div class="post-img dash"  style="top:3px">
                                <div class="add-zoo" data-file="img-zoos2">
                                    <div class="transverse"></div>
                                    <div class="vertical"></div>
                                </div>
                            </div>
                            <span class="c_red fs_12">（*<?=__('考级用户和机构任职人员需上传1寸或2寸照）', 'nlyd-student')?>）</span>
                        </div>
                        <!-- <div class="layui-bg-white img-zoos img-zoos2">
                            <p class="tps"><?=__('上传彩色1寸照', 'nlyd-student')?><span class="c_red fs_12">（*<?=__('考级用户和机构任职人员需上传', 'nlyd-student')?>）</span></p>
                                <?php if(!empty($user_info['user_images_color'])){ ?>
                                <?php foreach ($user_info['user_images_color'] as $value){ ?>
                                <div class="post-img no-dash">
                                    <div class="img-zoo img-box">
                                        <img src="<?=$value?>"/>
                                    </div>
                                    <input type="hidden" name="user_ID_Card[]" value="<?=$value?>" />
                                    <div class="del">
                                        <i class="iconfont">&#xe633;</i>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php } ?>
                            <div class="post-img dash">
                                <div class="add-zoo" data-file="img-zoos2">
                                    <div class="transverse"></div>
                                    <div class="vertical"></div>
                                </div>
                            </div>
                        </div> -->
                        <div class="form-input-row">
                            <div class="form-input-label"><div><span class="c_red">*</span><?=__('国家地区', 'nlyd-student')?></div></div>
                            <input class="nl-input" name="nationality" value='<?=empty($user_info['user_nationality']) ? '中华人民共和国' : $user_info['user_nationality'];?>' readonly  id="trigger4" placeholder="<?=__('选择国籍', 'nlyd-student')?>">
                            <input type="hidden" name="nationality_pic" value='<?=empty($user_info['user_nationality_pic']) ? 'cn' : $user_info['user_nationality_pic']?>'  id="src">
                            <input type="hidden" name="nationality_short" value='<?=empty($user_info['user_nationality_short']) ? 'CHN' : $user_info['user_nationality_short']?>'  id="short">
                            <span class="form-input-right" id="nationality_pic"><span class="fastbannerform__span f32 NOFLAG <?=empty($user_info['user_nationality_pic']) ? 'cn': $user_info['user_nationality_pic']?>"></span></span>
                            <!-- <span class="form-input-right"><img id="flags" style="width:16px;height:11px;" src="<?=empty($user_info['user_nationality_pic']) ? student_css_url.'image/flags/cn.png': student_css_url.'image/flags/'.$user_info['user_nationality_pic'].'.png'?>"></span> -->
                            
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><div><span class="c_red">*</span><?=__('所在城市', 'nlyd-student')?></div></div>
                            <input readonly id="areaSelect" type="text" placeholder="<?=__('所在城市', 'nlyd-student')?>" value="<?=!empty($user_info['user_address']) ? $user_info['user_address']['province'].$user_info['user_address']['city'].$user_info['user_address']['area'] : ''?>" class="nl-input" lay-verify="required">
                            <input  type="hidden" id="province" name="user_address[province]" value="<?=!empty($user_info['user_address']) ? $user_info['user_address']['province'] : ''?>"/>
                            <input  type="hidden" id="city" name="user_address[city]" value="<?=!empty($user_info['user_address']) ? $user_info['user_address']['city'] : ''?>">
                            <input  type="hidden" id="area" name="user_address[area]" value="<?=!empty($user_info['user_address']) ? $user_info['user_address']['area'] : ''?>"/>
                        </div>
                        <a class="form-input-row a address-row layui-row" href="<?=home_url('/account/address');?>">
                            <div class="form-input-label"><div><?=__('收件地址', 'nlyd-student')?></div></div>
                            <div  class="nl-input">  
                                <div>
                                <?php if($user_address){ ?>
                                    <?=$user_address['fullname']?>&nbsp;&nbsp;&nbsp;&nbsp;<?=$user_address['telephone']?>
                                    <br><?=$user_address['address']?>
                                <?php }else{ ?>
                                    <?=__('暂无地址', 'nlyd-student')?>
                                <?php }?>
                                </div>
                            </div>
                            <div class="form-input-right c_blue"><div><?=__('修改', 'nlyd-student')?></div></div>
                        </a>
                        <a class="a-btn a-btn-table" id="certificationFormBtn" lay-filter="certificationFormBtn" lay-submit=""><div><?=__('更新实名认证', 'nlyd-student')?></div></a>
                        <input type="hidden" class="sbu_type" name="type" value="<?=$_GET['type']?>">
                    </div>
                </form>
            </div>
        </div>           
    </div>
</div>
<input style="display:none;" type="file" id="img-zoos0" data-this="img-zoos0" value="" accept="image/*"/>
<input style="display:none;" type="file" id="img-zoos1" data-this="img-zoos1" value="" accept="image/*"/>
<input style="display:none;" type="file" id="img-zoos2" data-this="img-zoos2" value="" accept="image/*"/>
<input type="hidden" name="_wpnonce" id="inputImg" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">
<script>
jQuery(document).ready(function($) {
        $("input[name='meta_val[real_ID]']").keyup(function(){//身份证号码输入框失焦事件
            var _this=$(this)
            var value=_this.val();
            var exg=$.validationLayui.allRules.identity[0]
            if($('#trigger4').val()=="中华人民共和国" && exg.test(value)){
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
            }else{
                $("input[name='meta_val[real_age]']").val('');
            }
        })
        // $('.certificationFormBtn').click(function(){
        //     $('#certificationFormBtn').click()
        // })
        //模拟手机下拉列表，选择证件类型
        var certificationSelectData= [
            {key:'sf',value:"<?=__('身份证', 'nlyd-student')?>"},
            {key:'jg',value:"<?=__('军官证', 'nlyd-student')?>"},
            {key:'hz',value:"<?=__('护照', 'nlyd-student')?>"},
            {key:'tb',value:"<?=__('台胞证', 'nlyd-student')?>"},
            {key:'ga',value:"<?=__('港澳证', 'nlyd-student')?>"},
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
            title: "<?=__('证件类型', 'nlyd-student')?>",
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
        var sexSelectData= [{id:"男",value:'<?=__('男', 'nlyd-student')?>'},{id:"女",value:'<?=__('女', 'nlyd-student')?>'},]
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
            title: "<?=__('性别', 'nlyd-student')?>",
            wheels: [
                {data: sexSelectData}
            ],
            position:posiotionSex, //初始化定位 打开时默认选中的哪个 如果不填默认为0
            transitionEnd:function(indexArr, data){
                // console.log(data);
            },
            callback:function(indexArr, data){
                $('#trigger3').val(data[0]['value']).attr('data-value',data[0]['id'])
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
            title: "<?=__('地址', 'nlyd-student')?>",
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
        if($('#birthdaySelect').val().length>0 && $('#birthdaySelect').val()){
            var birthdayValue=$('#birthdaySelect').val().split('-')
            $.each($.validationLayui.dates,function(index,value){
                if(birthdayValue[0]==value.value){
                    posiotionBirthday=[index,0,0];
                    $.each(value.childs,function(i,v){
                        if(birthdayValue[1]==v.value){
                            posiotionBirthday=[index,i,0];
                            $.each(v.childs,function(j,val){
                                if(birthdayValue[2]==val.value){
                                    posiotionBirthday=[index,i,j];
                                }
                            })
                        }
                    })
                }
            })
        }
        var mobileSelect5 = new MobileSelect({
            trigger: '#birthdaySelect',
            title: "<?=__('生日', 'nlyd-student')?>",
            wheels: [
                {data:  $.validationLayui.dates},
            ],
            new_title:["<?=__('年', 'nlyd-student')?>","<?=__('月', 'nlyd-student')?>","<?=__('日', 'nlyd-student')?>"],
            position:posiotionBirthday, //初始化定位 打开时默认选中的哪个 如果不填默认为0
            transitionEnd:function(indexArr, data){

            },
            callback:function(indexArr, data){
                var text=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value'];
                $('#birthdaySelect').val(text);
            }
        });

        if($('#trigger4').val()=="中华人民共和国"){
            $('#birth').css('display','none')
            $('#age').css('display','block')
        }else{
            $('#birth').css('display','block');
            $('#age').css('display','none');
        }
        //模拟手机下拉列表，选择国籍
        var contrySelectData=$.validationLayui.contry;
        var posiotioncontry=[0];//初始化位置，高亮展示
        if($('#trigger4').val().length>0 && $('#trigger4').val()){
            $.each(contrySelectData,function(index,value){
                if(value['value']==$('#trigger4').val()){
                    posiotioncontry=[index]
                    return false;
                }
            })
        }
        var mobileSelect4 = new MobileSelect({
            trigger: '#trigger4',
            title: "<?=__('国家&地区', 'nlyd-student')?>",
            wheels: [
                {data: contrySelectData}
            ],
            position:posiotioncontry, //初始化定位 打开时默认选中的哪个 如果不填默认为0
            transitionEnd:function(indexArr, data){
                // console.log(data);
            },
            callback:function(indexArr, data){
                // console.log(data)
                var dom='<span class="fastbannerform__span f32 NOFLAG '+data[0]['src']+'"></span>'
                $('#trigger4').val(data[0]['value'])
                $('#src').val(data[0]['src'])
                $('#short').val(data[0]['short'])
                $('#nationality_pic').empty().html(dom)
                if(data[0]['value']=="中华人民共和国"){
                    $('#birth').css('display','none')
                    $('#age').css('display','block')
                    $('input[name="birthday"]').val('');
                }else{
                    $('#birth').css('display','block');
                    $('input[name="meta_val[real_age]"').val('');
                    $('#age').css('display','none');
                }
            }
        });

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
        var imgs=[];//身份证
        var imgs1=[];//收款账户
        var imgs2=[];//寸照
        $('.img-zoos').each(function(){
            var _this=$(this);
            if(_this.hasClass('img-zoos1')){//微信
                if(_this.find('.post-img.no-dash').length>=1){
                    _this.find('.post-img.dash').css('display','none')
                }
            }else if(_this.hasClass('img-zoos0')){//身份证
                if(_this.find('.post-img.no-dash').length>=2){
                    _this.find('.post-img.dash').css('display','none')
                }
            }else if(_this.hasClass('img-zoos2')){//寸照
                if(_this.find('.post-img.no-dash').length>=1){
                    _this.find('.post-img.dash').css('display','none')
                }
            }
        })

        function changes(e,_this,array) {
            var file=e.target.files[0];
            if( !file.type.match(/.png|.jpg|.jpeg/) ) {
                alert("<?=__('上传错误,文件格式必须为', 'nlyd-student')?>：png/jpg/jpeg");
                return;  
            }
            //读取File对象的数据
            imgCompress(file,function(imgBase64){
                imgBase64 = imgBase64;    //存储转换的base64编码
                array.unshift(imgBase64)
                // console.log(imgBase64);  
                var dom='<div class="post-img no-dash" style="top:3px">'
                        +'<div class="img-zoo img-box">'
                            +'<img src="'+imgBase64+'"/>'
                        +'</div>'
                        +'<div class="del">'
                            +'<i class="iconfont">&#xe633;</i>'
                        +'</div>'
                    +'</div>'
                var className=_this.attr('data-this')
                $('.'+className+' .form-input-label').after(dom)
                layer.photos({//图片预览
                    photos: '.img-zoos',
                    anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                })
                if(className=="img-zoos0"){
                    if($('.'+className+' .post-img.no-dash').length>=2){
                        $('.'+className+' .post-img.dash').css('display','none')
                    }
                }else if(className=="img-zoos1"){
                    if($('.'+className+' .post-img.no-dash').length>=1){
                        $('.'+className+' .post-img.dash').css('display','none')
                    }
                }
                else if(className=="img-zoos2"){
                    if($('.'+className+' .post-img.no-dash').length>=1){
                        $('.'+className+' .post-img.dash').css('display','none')
                    }
                }
                $(e.target).val('')
            });
        }

        $("#img-zoos0").change(function(e) {
            changes(e,$("#img-zoos0"),imgs)
        });
        $("#img-zoos1").change(function(e) {
            changes(e,$("#img-zoos1"),imgs1)
        });
        $("#img-zoos2").change(function(e) {
            changes(e,$("#img-zoos2"),imgs2)
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
            }else if(_this.parents('.img-zoos').hasClass('img-zoos1')){
                imgs2.splice(index, 1);
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
            form.on('submit(nicenameFormBtn)', function(data){//昵称
                sendloginAjax(data.field)
                return false;
            });
            form.on('submit(certificationFormBtn)', function(data){//实名认证提交
                var _this=$(this);
                if(!_this.hasClass('disabled')){
                    var match_id=$.Request('match_id')
                    var grad_id=$.Request('grad_id')
                    var fd = new FormData();
                    fd.append('action',data.field.action);
                    fd.append('_wpnonce',data.field._wpnonce);
                    fd.append('meta_key',data.field.meta_key);
                    fd.append('meta_val[real_type]',data.field['meta_val[real_type]']);
                    fd.append('meta_val[real_name]',data.field['meta_val[real_name]']);
                    fd.append('meta_val[real_ID]',data.field['meta_val[real_ID]']);
                    fd.append('user_gender',$('#trigger3').attr('data-value'));
                    fd.append('meta_val[real_age]',data.field['meta_val[real_age]']);
                    fd.append('user_address[area]',data.field['user_address[area]']);
                    fd.append('user_address[city]',data.field['user_address[city]']);
                    fd.append('user_address[province]',data.field['user_address[province]']);
                    fd.append('type',$('.sbu_type').val());
                    fd.append('nationality',data.field['nationality']);
                    fd.append('nationality_pic',data.field['nationality_pic']);
                    fd.append('birthday',data.field['birthday']);
                    fd.append('nationality_short',data.field['nationality_short']);
                    fd.append('sign_match',$.Request('sign_match'));
                    fd.append('order_index',$.Request('order_index'));
                    // console.log(data.field)
                    if(match_id!=null){
                        fd.append('match_id',match_id);
                    }else{
                        fd.append('match_id','');
                    }
                    if(grad_id!=null){
                        fd.append('grad_id',grad_id);
                    }else{
                        fd.append('grad_id','');
                    }
                    $.each(imgs, function (i, v) {
                        fd.append('images[]',v);
                    })
                    // $.each(imgs1, function (i, v) {
                    //     fd.append('images_wechat[]',v);
                    // })
                    $.each(imgs2, function (i, v) {
                        fd.append('images_color[]',v);
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
                    return false;
                }else{
                    $.alerts("<?=__('正在处理您的请求..', 'nlyd-student')?>")
                }
            });
            layer.photos({//图片预览
                photos: '.img-zoos',
                anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            }) 
        });
})

</script>