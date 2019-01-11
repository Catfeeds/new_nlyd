<!-- 赛区 -->
<div class="layui-fluid">
    <div class="layui-row">
        <?php
        require_once leo_student_public_view.'leftMenu.php';

        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper have-bottom">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('/zone/indexUser/');?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <?php if(isset($_GET['zone_id'])){?>
                    <h1 class="mui-title"><div><?=__('资料查看', 'nlyd-student')?></div></h1>
                <?php }else{ ?>
                <h1 class="mui-title"><div><?=__($zone_type_name.'资料填写', 'nlyd-student')?></div></h1>
                <?php } ?>
            </header>

            <?php

                switch ($_GET['zone_type_alias']){
                    case 'match':
                        require_once student_view_path.CONTROLLER.'/apply-match.php';
                        break;
                    case 'trains':
                        require_once student_view_path.CONTROLLER.'/apply-trains.php';
                        break;
                    case 'test':
                        require_once student_view_path.CONTROLLER.'/apply-test.php';
                        break;
                    default:
                        return false;
                        break;
                }
            ?>
            
              
        </div>
    </div>
</div>
<input style="display:none;" type="file" name="meta_val" id="img-zoos0" data-this="img-zoos0" value="" accept="image/*"/>
<input style="display:none;" type="file" name="meta_val" id="img-zoos1" data-this="img-zoos1" value="" accept="image/*"/>
<script>
    jQuery(function($) {
        var zone_type_alias=$.Request('zone_type_alias');
        var type_id=$.Request('type_id');
        var opening_bank_Data=$.validationLayui.back;//开户行
        var posiotion_back=[0];//开户行初始化位置，高亮展示
        var area=$.validationLayui.allArea.area;//省市区三级联动
        //省市区三级联动
        var posiotionarea=[0,0,0];//初始化位置，高亮展示
        $.each(area,function(i1,v1){
            $.each(v1.childs,function(i2,v2){
                v2.childs.unshift({
                    id:'-',
                    value:''
                })
            })
        })
        if($('#areaSelect').length>0){
            if($('#areaSelect').val().length>0 && $('#areaSelect').val()){
                var areaValue=$('#areaSelect').val()
                $.each(area,function(index,value){
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
            // console.log(JSON.stringify(area))
            var mobileSelect3 = new MobileSelect({
                trigger: '#areaSelect',
                title: "<?=__('地址', 'nlyd-student')?>",
                wheels: [
                    {data: area},
                ],
                position:posiotionarea, //初始化定位 打开时默认选中的哪个 如果不填默认为0
                transitionEnd:function(indexArr, data){

                },
                callback:function(indexArr, data){
                    var three=data[2]['value'].length==0 ? '' : '-'+data[2]['value']
                    var text=data[0]['value']+'-'+data[1]['value']+three;
                    // $('#province').val(data[0]['value']);
                    // $('#city').val(data[1]['value']);
                    // $('#area').val(data[2]['value']);
                    $('#areaSelect').val(text);
                }
            });
        }
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
        function nameRowIsShow() {
            var id=$('#zone_match_type').val();
            if(id==1){
                $('.name_row').removeClass('dis_none')
            }else{
                $('.name_row').addClass('dis_none')
            }
        }
        nameRowIsShow()
        var match_type_data=[{id:'team',value:"<?=__('战队精英赛', 'nlyd-student')?>"},{id:'city_single',value:"<?=__('城市赛(单)', 'nlyd-student')?>"},{id:'city_double',value:"<?=__('城市赛(双)', 'nlyd-student')?>"}]
        var posiotion_match_type=[0];//初始化位置，高亮展示
        if($('#zone_match_type_val').length>0){
            if($('#zone_match_type_val').val() && $('#zone_match_type_val').val().length>0){
                $.each(match_type_data,function(index,value){
                    if(value['value']==$('#zone_match_type_val').val()){
                        posiotion_match_type=[index]
                        return false;
                    }
                })
            }
            var mobileSelect4 = new MobileSelect({
                trigger: '#zone_match_type_val',
                title: "<?=__('承办赛事类型', 'nlyd-student')?>",
                wheels: [
                    {data: match_type_data}
                ],
                position:posiotion_match_type, //初始化定位 打开时默认选中的哪个 如果不填默认为0
                transitionEnd:function(indexArr, data){
                    // console.log(data);
                },
                callback:function(indexArr, data){
                    $('#zone_match_type_val').val(data[0]['value']);
                    $('#zone_match_type').val(data[0]['id']);
                    nameRowIsShow()
                }
            });
        }
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
            if( !file.type.match(/.png|.jpg|.jpeg/) ) {
                alert("<?=__('上传错误,文件格式必须为', 'nlyd-student')?>：png/jpg/jpeg");
                return;  
            }
            //读取File对象的数据
            imgCompress(file,function(imgBase64){
                imgBase64 = imgBase64;    //存储转换的base64编码
                array.unshift(imgBase64)
                // console.log(imgBase64);  
                var dom='<div class="post-img no-dash">'
                    +'<div class="img-zoo img-box">'
                    +'<img src="'+imgBase64+'"/>'
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
                $(e.target).val('')
            });
        }

        $("#img-zoos0").change(function(e) {
            changes(e,$("#img-zoos0"),imgs)
        });
        $("#img-zoos1").change(function(e) {
            changes(e,$("#img-zoos1"),imgs1)
        });
        $('body').on('click','.del',function(){//删除图片
            var _this=$(this);
            var index =_this.parents('.post-img').index();
            _this.parents('.img-zoos').find('.post-img.dash').css('display','block');

            if(_this.parents('.img-zoos').hasClass('img-zoos0')){
                imgs.splice(index, 1);
            }else if(_this.parents('.img-zoos').hasClass('img-zoos1')){
                imgs1.splice(index, 1);
                $('.business_licence_url').val('')
            }
            _this.parents('.post-img').remove();
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
            form.on('submit(layform)', function(data){//实名认证提交
                var _this=$(this);
                if(!_this.hasClass('disabled')){
                    var fd = new FormData();
                    fd.append('action','zone_apply_submit');
                    fd.append('zone_address',data.field['zone_address']);//营业地址
                    fd.append('legal_person',data.field['legal_person']);//法定代表人姓名
                    fd.append('opening_bank',data.field['opening_bank']);//对公账户开户行
                    fd.append('opening_bank_address',data.field['opening_bank_address']);//对公账户开户详细地址
                    fd.append('bank_card_num',data.field['bank_card_num']);//对公账户开户号码
                    fd.append('bank_card_name',data.field['bank_card_name']);//对公账户开户名称
                    fd.append('zone_match_address',data.field['zone_match_address']);//中心所在地
                    
                    if($('.business_licence_url').val()=='' || !$('.business_licence_url').val()){//修改具有初始图片,上传营业执照
                        if(imgs1[0]){
                            fd.append('business_licence',imgs1[0]);
                        }else{
                            fd.append('business_licence','');
                        }
                    }else{
                        if(data.field['business_licence_url']){
                            fd.append('business_licence_url',data.field['business_licence_url']);
                        }else{
                            fd.append('business_licence_url','');
                        }
                    }
                    if(type_id){
                        fd.append('type_id',type_id);
                    }else{
                        fd.append('type_id','');
                    }
                    if(zone_type_alias){
                        fd.append('zone_type_alias',zone_type_alias);
                    }else{
                        fd.append('zone_type_alias','');
                    }
                    if(data.field['chairman_phone']){//赛事组委会主席(赛区)
                        fd.append('chairman_phone',data.field['chairman_phone']);
                    }
                    if(data.field['secretary_phone']){//赛事组委会秘书长(赛区)
                        fd.append('secretary_phone',data.field['secretary_phone']);
                    }
                    if(data.field['zone_match_type']){//赛区
                        fd.append('zone_match_type',data.field['zone_match_type']);
                        if(data.field['zone_match_type']=='team'){//战队赛
                            fd.append('zone_name',data.field['zone_name']);
                        }
                    }else{//训练中心、测评中心字号
                        fd.append('zone_name',data.field['zone_name']);
                    }
                    if(data.field['center_manager']){//训练中心（分中心总经理）
                        fd.append('center_manager',data.field['center_manager']);
                    }
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
            layer.photos({//图片预览
                photos: '.img-zoos',
                anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            })
        });

    })
</script>
