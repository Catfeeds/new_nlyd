<div class="nl-cropper-bg">
    <div class="img-container">
        <img id="image" src="">
    </div>
    <div class="nl-cropper-footer">
        <button type="button" class="pull-left" id='crop-cancel'>取消</button>
        <button type="button" class="pull-right" id="crop">确认</button>
    </div>

</div>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            <div class="main-header">
                <header class="mui-bar mui-bar-nav main">
                    <a class="mui-pull-left nl-goback static" href="<?=home_url('student/account/')?>">
                        <i class="iconfont">&#xe610;</i>
                    </a>
                    <h1 class="mui-title">个人资料</h1>
                </header>
                <header class="mui-bar mui-bar-nav certification" href="certification">
                    <a class="mui-pull-left nl-goPage">
                        <i class="iconfont">&#xe610;</i>
                    </a>
                    <h1 class="mui-title">实名认证</h1>
                </header>
            </div> 
            <div class="layui-row nl-border nl-content">
                <div class="main-page">
                    <form class="nl-page-form layui-form width-margin-pc" lay-filter='nicenameForm'>   
                    
                        <div class="nl-form-tips">为了保证您考级及比赛的真实有效性，请您确保个人资料准确无误</div>
                        <div class="form-inputs">
                            <div class="form-input-row">
                                <div class="form-input-label">账户头像</div>
                                <span class="Mobile form-input-right">修改</span>
                                <div id="imgBox" class="imgBox">
                                    <img class="logoImg" src="<?=$user_info['user_head'];?>">
                                </div>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">账户ID</div>
                                <div class="nl-input"><?=isset($user_info['user_ID']) ? $user_info['user_ID'] : '暂无ID';?></div>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">账户昵称</div>
                                <input name='meta_val' value="<?=isset($user_info['nickname']) ? $user_info['nickname'] : '';?>" type="text" placeholder="账户昵称" class="nl-input nl-foucs" lay-verify="required">
                                <input  type="hidden" name="action" value="student_saveInfo"/>
                                <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">
                                <input  type="hidden" name="meta_key" value="user_nicename"/>
                            </div>
                            <div class="form-input-row" href="certification">
                                <div class="form-input-label">实名认证</div>
                                <span class="form-input-right"><i class="iconfont">&#xe727;</i></span>
                                <div class="nl-input"><?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_name'] : '未认证';?></div>
                            </div>
                            <a class="form-input-row a address-row layui-row" href="<?=home_url('/account/address');?>">
                                <div class="form-input-label">收货地址</div>
                                <span class="form-input-right"><i class="iconfont">&#xe727;</i></span>
                                <div  class="nl-input">  
                                    <?php if($user_address){ ?>
                                        <p class="accept-address">
                                            <?=$user_address['fullname']?>&nbsp;&nbsp;&nbsp;&nbsp;<?=$user_address['telephone']?>
                                            <br><?=$user_address['address']?>
                                        </p>
                                    <?php }else{ ?>
                                        暂无地址
                                    <?php }?>
                                        
                                </div>
                            </a>
                            <div class="a-btn" lay-filter="nicenameFormBtn" lay-submit="">保存</div>
                        </div>
                
                    </form>
                </div>
                <!-- 实名认证 -->
                    <div id="certification" class="form-page">
                        <form class="layui-form nl-page-form width-margin-pc" lay-filter='certificationForm'>
                            <div class="form-inputs">
                                <div class="form-input-row">
                                    <div class="form-input-label">证件类型</div>
                                    <input value='<?= !empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_type_c'] : '';?>' type="text" readonly id="trigger1" placeholder="选择证件类型" class="nl-input" lay-verify="required">
                                    <input value='<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_type'] : '';?>'  type="hidden" name="meta_val[real_type]" id="trigger2">
                                    <input  type="hidden" name="action" value="student_saveInfo"/>
                                    <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">
                                    <input  type="hidden" name="meta_key" value="user_real_name"/>
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">证件姓名</div>
                                    <input type="text" name="meta_val[real_name]" value="<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_name'] : '';?>" placeholder="输入证件上的真实姓名" lay-verify="required" class="nl-input nl-foucs">
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">证件号码</div>
                                    <input type="text" name="meta_val[real_ID]" value="<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_ID'] : '';?>" placeholder="输入证件上的真实证件号" lay-verify="required"  class="nl-input nl-foucs">
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">性 别</div>
                                    <input name='user_gender'  value='<?=isset($user_info['user_gender']) ? $user_info['user_gender'] : '';?>' type="text" readonly id="trigger3" placeholder="请选择您的性别" class="nl-input" lay-verify="required">
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">年龄</div>
                                    <input type="text" name="meta_val[real_age]" value="<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_age'] : '';?>" placeholder="年龄" lay-verify="required"  class="nl-input nl-foucs">
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">所在城市</div>
                                    <input readonly id="areaSelect" type="text" placeholder="所在城市" value="<?=$user_info['user_address']['province']?><?=$user_info['user_address']['city']?><?=$user_info['user_address']['area']?>" class="nl-input" lay-verify="required">
                                    <input  type="hidden" id="province" name="user_address[province]" value="<?=$user_info['user_address']['province']?>"/>
                                    <input  type="hidden" id="city" name="user_address[city]" value="<?=$user_info['user_address']['city']?>">
                                    <input  type="hidden" id="area" name="user_address[area]" value="<?=$user_info['user_address']['area']?>"/>
                                </div>
                                <div class="a-btn" lay-filter="certificationFormBtn" lay-submit="">保存</div>
                            </div>
                            
                        </form>
                    </div>
            </div>
        </div>           
    </div>

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
                type: "POST",
                url: window.admin_ajax,
                data: datas,
                dataType:'json',
                timeout:3000,
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
        $('.form-input-row').click(function(){//展示子页面
            var _this=$(this);
            if(!_this.hasClass('a')){
                var target=_this.attr('href');
                if(target!==undefined){
                    // $(target).addClass('goLeftShow')
                    $('.main-header .'+target).addClass('headerShow')
                    $('.main-header .main').css('display','none')
                   
                    var left=$('.nl-content').width()
                    $('.main-page').css({
                        'transform':'translate3d('+-left+'px, 0px, 0px)'
                    }).delay('200').fadeOut('100')
                    $('#'+target).css({
                        'transform':'translate3d(0px, 0px, 0px)'
                    }).delay('200').fadeIn('200')
                }
            }
        })
        $('.nl-goPage').click(function(){//返回主页面
            var _this=$(this);
            var target=_this.parents('.mui-bar').attr('href');
            _this.parents('.mui-bar').removeClass('headerShow')
            $('.main-header .main').css('display','block')

            var left=$('.nl-content').width()
            $('.main-page').css({
                'transform':'translate3d(0px, 0px, 0px)'
            }).delay('200').fadeIn('200')
            $('#'+target).css({
                'transform':'translate3d('+left+'px, 0px, 0px)'
            }).delay('200').fadeOut('200')
        })
        //模拟手机下拉列表，选择证件类型
        var certificationSelectData= [
            {key:'sf',value:'身份证'},
            {key:'jg',value:'军官证'},
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
        // 模拟手机下拉列表，日期性别
        // var year=$.validationLayui.dates;
        // var posiotionYear=[0,0,0];//初始化位置，高亮展示
        // if($('#birthdaySelect').val().length>0 && $('#birthdaySelect').val()!==undefined){
        //     var birthdayValue=$('#birthdaySelect').val().split('-');
        //     var yearValue=birthdayValue[0];
        //     var monthValue=birthdayValue[1];
        //     var dayValue=birthdayValue[2];
        //     $.each(year,function(index,value){
        //         if(value.value==yearValue){
        //             posiotionYear=[index,0,0]
        //             $.each(value.childs,function(i,v){
        //                 if(v.value==monthValue){
        //                     posiotionYear=[index,i,0]
        //                     $.each(v.childs,function(j,val){
        //                         if(val.value==dayValue){
        //                             posiotionYear=[index,i,j]
        //                         }
        //                     })
        //                 }
        //             })
        //         }
        //     })
        // }

        // var mobileSelect2 = new MobileSelect({
        //     trigger: '#birthdaySelect',
        //     title: '生日',
        //     wheels: [
        //         {data: year},
        //     ],
        //     position:posiotionYear, //初始化定位 打开时默认选中的哪个 如果不填默认为0
        //     transitionEnd:function(indexArr, data){

        //     },
        //     callback:function(indexArr, data){
        //         var text=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']
        //         $('#birthdaySelect').val(text)
        //     }
        // });

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
                    // console.log(data)
                    $.alerts(data.data.info)
                    if(data.success){
                       window.location.reload()
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
            form.on('submit(certificationFormBtn)', function(data){//实名认证提交
                console.log(data.field)
                sendloginAjax(window.admin_ajax,data.field)
                return false;
            });
            form.on('submit(nicenameFormBtn)', function(data){//昵称
                console.log(data.field)
                sendloginAjax(window.admin_ajax,data.field)
                return false;
            });

            layer.photos({//图片预览
                photos: '#imgBox',
                anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            })   
        });
     $('.Mobile').click(function(){
        $("#file").click()
    })
    var avatar = $('#avatar');
    var image = $('#image');
    var input = $('#file');
    var bg=$('.nl-cropper-bg');
    var cropper;
    input.change(function (e) {
        var files = e.target.files;
        var done = function (url) {
            input.val('');
            image.attr('src',url);
            bg.addClass('bg-show')
            cropper = new Cropper(image[0], {
                aspectRatio: 1,
            });
        };
        var reader;
        var file;
        var url;

        if (files && files.length > 0) {
            file = files[0];
            reader = new FileReader();
            reader.onload = function (ev) {
                done(reader.result);
            };
            reader.readAsDataURL(file);
        }
    });

    $('body').on('click','#crop-cancel',function(){
            bg.removeClass('bg-show')
            cropper.destroy();
            cropper = null;
    })
    document.getElementById('crop').addEventListener('click', function () {
        var initialAvatarURL;
        var canvas;
        if (cropper) {
        canvas = cropper.getCroppedCanvas({
            width: 160,
            height: 160,
        });
        initialAvatarURL = avatar.src;
        avatar.src = canvas.toDataURL();
        canvas.toBlob(function (blob) {
            var formData = new FormData();
            formData.append('action','student_saveInfo');
            formData.append('_wpnonce',$("#inputImg").val());
            formData.append('meta_key','user_head');
            formData.append('meta_val',blob);
            $.ajax({
                type: "POST",
                    url: window.admin_ajax,
                    data: formData,
                    dataType:'json',
                    timeout:3000,
                    contentType : false,
                    processData : false,
                    cache : false,
                    success: function(data, textStatus, jqXHR){
                        console.log(data)
                        $.alerts(data.data.info)
                        if(data.data.head_url){
                            $('.logoImg').attr('src',data.data.head_url)
                        }
                        bg.removeClass('bg-show')
                        cropper.destroy();
                        cropper = null;
                    },
                    error: function (data) {
                        console.log(data)
                    },
                })
            }); 
        }
    });
    })
</script>
