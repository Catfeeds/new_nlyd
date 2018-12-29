
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
                <h1 class="mui-title"><div><?=__($zone_type_name.'资料填写', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding layui-row width-margin-pc">
                    <form class="layui-form apply_form" lay-filter='layform'>
                        <?php if(!empty($row['id'])):?>
                            <div>
                                <div class="lable_row"><span class="c_black"><?=__($zone_type_name.'编号', 'nlyd-student')?>：</span></div>
                                <div class="input_row"><input class="radius_input_row" disabled type="text" name="zone_num" value="<?=dispRepair($row['id'],4,0)?>"></div>
                            </div>
                        <?php endif;?>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__($zone_type_name.'类型', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input type="hidden" id="zone_match_type" name="zone_match_type" value="<?=$row['zone_match_type']?>">
                                <input
                                        class="radius_input_row nl-foucs"
                                        type="text"
                                        readonly
                                        id="zone_match_type_val"
                                        lay-verify="required"
                                        autocomplete="off"
                                        placeholder="<?=__('选择承办赛事类型', 'nlyd-student')?>"
                                        value="<?=$row['zone_match_type_cn']?>">
                            </div>
                        </div>
                        <div class="name_row dis_none">
                            <div class="lable_row">
                                <span class="c_black"><?=__($zone_type_name.'字号', 'nlyd-student')?>：</span>
                                <span class="c_black3"><?=__('规则：IISC+“名字”+国际脑力训练中心+城市', 'nlyd-student')?></span>
                            </div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" type="text" name="zone_name" autocomplete="off" placeholder="<?=__('输入您的'.$zone_type_name.'名字', 'nlyd-student')?>" value="<?=!empty($row['id']) ? $row['zone_name'] :''?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__($zone_type_name.'营业地址', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('输入您的营业地址，与证件保持一致', 'nlyd-student')?>" value="<?=!empty($row) ? $row['zone_address'] :''?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('上传营业执照', 'nlyd-student')?>：</span></div>
                            <div class="input_row img-zoos img-zoos1">
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
                                    <div class="add-zoo" data-file="img-zoos1">
                                        <div class="transverse"></div>
                                        <div class="vertical"></div>
                                    </div>
                                </div>
                                <span class="fs_12 c_black3 _tips"><?=__('原件影印件或盖有鲜章的复印件，文件不超过10m大小', 'nlyd-student')?></span>
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('法定代表人', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="legal_person" lay-verify="required" autocomplete="off" placeholder="<?=__('法定代表人姓名', 'nlyd-student')?>" value="<?=!empty($row) ? $row['legal_person'] :''?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('选择对公账户开户行', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" type="text" readonly id="opening_bank" name="opening_bank"  lay-verify="required" autocomplete="off" placeholder="<?=__('选择对公账户开户行', 'nlyd-student')?>" value="<?=!empty($row) ? $row['opening_bank'] :''?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('银行卡号', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="bank_card_num" lay-verify="required" autocomplete="off" placeholder="<?=__('输入对公账户银行卡号', 'nlyd-student')?>" value="<?=!empty($row) ? $row['bank_card_num'] :''?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('开户详细地址', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="opening_bank_address" lay-verify="required" autocomplete="off" placeholder="<?=__('输入对公账户详细开户地址', 'nlyd-student')?>" value="<?=!empty($row) ? $row['opening_bank_address'] :''?>"></div>
                        </div>
                        <?php if($_GET['zone_type_alias'] == 'match'):?>
                            <div>
                                <div class="lable_row"><span class="c_black"><?=__('组委会主席', 'nlyd-student')?>：</span></div>
                                <div class="input_row">
                                    <!-- <input class="get_id" name="chairman_id" style="display:none" value="<?=$row['chairman_id']?>"> -->
                                    <!-- <input class="radius_input_row change_ajax" value="<?=$row['secretary_name']?>" type="text" lay-verify="required" autocomplete="off" placeholder="<?=__('选择组委会主席', 'nlyd-student')?>"> -->
                                    <select class="js-data-select-ajax" name="chairman_id" style="width: 100%" data-action="get_manage_user" data-placeholder="选择组委会主席" >
                                        <option value="<?=$row['chairman_id']?>" selected><?=$row['chairman_name']?></option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <div class="lable_row"><span class="c_black"><?=__('组委会秘书', 'nlyd-student')?>：</span></div>
                                <div class="input_row">
                                    <select class="js-data-select-ajax" name="secretary_id" style="width: 100%" data-action="get_manage_user" data-placeholder="选择组委会秘书" >
                                        <option value="<?=$row['secretary_id']?>" selected><?=$row['secretary_name']?></option>
                                    </select>
                                    <!-- <input class="get_id" name="secretary_id" style="display:none" value="<?=$row['secretary_id']?>"> -->
                                    <!-- <input class="radius_input_row change_ajax" name="secretary_id"  value="<?=$row['secretary_name']?>" type="text" lay-verify="required" autocomplete="off" placeholder="<?=__('选择组委会秘书', 'nlyd-student')?>"> -->

                                </div>
                            </div>
                        <?php endif;?>
                        <?php if(!empty($referee_name)):?>
                            <div>
                                <div class="lable_row"><span class="c_black"><?=__('事业管理员', 'nlyd-student')?>：</span></div>
                                <div class="input_row">
                                    <input class="radius_input_row" disabled type="text" value="<?=$referee_name?>">
                                </div>
                            </div>
                        <?php endif;?>
                        <!--<div>
                            <div class="lable_row"><span class="c_black"><?/*=__($zone_type_name.'管理员', 'nlyd-student')*/?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row" disabled type="text" value="<?/*=$director*/?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?/*=__('管理员电话', 'nlyd-student')*/?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row" disabled type="text" value="<?/*=$contact*/?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?/*=__('管理员证件', 'nlyd-student')*/?>：</span></div>
                            <div class="input_row img-zoos img-zoos0">
                                <?php /*if(!empty(!empty($user_ID_Card))):*/?>
                                    <?php /*foreach ($user_ID_Card as $v){ */?>
                                        <div class="post-img no-dash">
                                            <div class="img-zoo img-box">
                                                <img src="<?/*=$v*/?>"/>
                                            </div>
                                        </div>
                                    <?php /*} */?>
                                <?php /*endif;*/?>
                            </div>
                        </div>-->
                        <?php if($row['user_status'] != 1):?>
                            <a class="a-btn a-btn-table" lay-filter="layform" lay-submit=""><div><?=__('提交资料', 'nlyd-student')?></div></a>
                        <?php endif;?>
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
        var zone_type_alias=$.Request('zone_type_alias')
        var type_id=$.Request('type_id')
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
        function nameRowIsShow() {
            var id=$('#zone_match_type').val();
            if(id==1){
                $('.name_row').removeClass('dis_none')
            }else{
                $('.name_row').addClass('dis_none')
            }
        }
        nameRowIsShow()
        var match_type_data=[{id:1,value:"<?=__('战队赛', 'nlyd-student')?>"},{id:2,value:"<?=__('城市赛', 'nlyd-student')?>"}]
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
        $('.js-data-select-ajax').each(function () {
            var _this=$(this);
            var _placeholder = _this.attr('data-placeholder');
            _this.select2({
                placeholder : _placeholder,
                ajax: {
                    url: admin_ajax +'?action=get_manage_user'  ,
                    dataType: 'json',
                    delay: 600, //wait 250 milliseconds before triggering the request
                    processResults: function (res) {
                        return {
                            results: res.data
                        };
                    }
                }
            });
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
            var fSize=10;
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
                    // fd.append('zone_num',data.field['zone_num']);
                    fd.append('zone_address',data.field['zone_address']);
                    fd.append('legal_person',data.field['legal_person']);
                    fd.append('opening_bank',data.field['opening_bank']);
                    fd.append('opening_bank_address',data.field['opening_bank_address']);
                    fd.append('bank_card_num',data.field['bank_card_num']);
                    if($('.business_licence_url').val()=='' || !$('.business_licence_url').val()){//修改具有初始图片
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
                    if(data.field['chairman_id']){
                        fd.append('chairman_id',data.field['chairman_id']);
                    }
                    if(data.field['secretary_id']){
                        fd.append('secretary_id',data.field['secretary_id']);
                    }
                    fd.append('zone_match_type',data.field['zone_match_type']);
                    if(data.field['zone_match_type']==1){//战队赛
                        fd.append('zone_name',data.field['zone_name']);
                    }
                    if(data.field['zone_num']){//战队赛
                        fd.append('zone_num',data.field['zone_num']);
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
                            }
                        },
                        complete: function(jqXHR, textStatus){
                            if(textStatus=='timeout'){
                                $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
                            }
                            _this.removeClass('disabled');
                        }
                    })
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
