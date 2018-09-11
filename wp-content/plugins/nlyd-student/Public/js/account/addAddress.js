jQuery(function($) { 
    layui.use(['layer','form'], function(){
        var form = layui.form
        form.verify($.validationLayui.allRules);
        form.on('submit(addAdressBtn)', function(data){//新增修改地址
            if(data.field.is_default){
                data.field.is_default=1
            }
            $.ajax({
                data: data.field,success(res,ajaxStatu,xhr){
                    $.alerts(res.data.info)
                    if(res.success){
                        setTimeout(function(){
                            window.location.href=res.data.url
                        },1600)
                    }    
                }
            })
            return false;
        });
    })
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
            $("input[name='province']").val(data[0]['value'])
            $("input[name='city']").val(data[1]['value'])
            $("input[name='area']").val(data[2]['value'])
            $('#areaSelect').val(text)
        }
    });
})