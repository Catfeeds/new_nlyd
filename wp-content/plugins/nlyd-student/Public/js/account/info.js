jQuery(document).ready(function($) {
        $("input[name='meta_val[real_ID]']").focusout(function(){//身份证号码输入框失焦事件
            var value=$(this).val();
            var datas={
                real_ID:value,
                action:'reckon_age',
            }
            $.ajax({
                type: "POST",
                url: window.admin_ajax+"?date="+new Date().getTime(),
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
        layui.use(['form'], function(){
            var form = layui.form
            form.render();
            // 自定义验证规则
            form.verify($.validationLayui.allRules);
            // 监听提交
            form.on('submit(certificationFormBtn)', function(data){//实名认证提交
                var match_id=$.Request('match_id')
                if(match_id!=null){
                    data.field.match_id=match_id
                }else{
                    data.field.match_id=''
                }
                sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
                return false;
            });
            form.on('submit(nicenameFormBtn)', function(data){//昵称
                console.log(data.field)
                sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
                return false;
            });

            layer.photos({//图片预览
                photos: '.imgBox',
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
                    url: window.admin_ajax+"?date="+new Date().getTime(),
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
