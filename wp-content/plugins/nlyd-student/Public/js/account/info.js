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
        $('.nicenameFormBtn').click(function(){
            $('#nicenameFormBtn').click()
        })
        $('.certificationFormBtn').click(function(){
            $('#certificationFormBtn').click()
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

                    $('.nicenameFormBtn').css('display','none')
                    $('.certificationFormBtn').css('display','block')
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

            $('.nicenameFormBtn').css('display','block')
            $('.certificationFormBtn').css('display','none')
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
                // sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
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
            form.on('submit(nicenameFormBtn)', function(data){//昵称
                sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
                return false;
            });
            layer.photos({//图片预览
                photos: '.img-zoos',
                anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            }) 
  
        });
<<<<<<< HEAD
=======

>>>>>>> 4405c74e74fedd91b4a09a8d9cd74017a1dde5dd
})
