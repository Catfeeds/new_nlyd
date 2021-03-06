
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__(!empty($_GET['grading_id']) ? '编辑考级' : '发布考级', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content"  style="padding-bottom: 333px;">
                <div class="zone-form-tips width-padding width-padding-pc"><i class="iconfont">&#xe65b;</i> <?=__('任职人员需在平台注册并实名认证，否则审核无法通过', 'nlyd-student')?></div>
                <div class="width-padding layui-row width-margin-pc">
                    <form class="layui-form apply_form" lay-filter='layform'>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('考级责任人', 'nlyd-student')?>：</span>
                            </div>
                            <div class="input_row change_num_row">
                                <input class="radius_input_row change_num nl-foucs" value="<?=$match['person_liable_name']?>" type="tel" lay-verify="required" autocomplete="off" placeholder="<?=__('输入任职人员注册手机号查询，未注册无法选择', 'nlyd-student')?>">
                                <a class="coach_add_btn c_blue">确认</a> 
                                <input type="hidden" name="person_liable" value="<?=$match['person_liable']?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级场景', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input type="hidden" name="scene" id="match_type1_id" value="<?=$match['scene']?>">
                                <input class="radius_input_row nl-foucs" type="text" id="match_type1" readonly value="<?=$match['scene_title']?>" lay-verify="required" autocomplete="off" placeholder="<?=__('考级场景', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级类别', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input type="hidden" name="category_id" id="match_type2_id" value="<?=$match['category_id']?>">
                                <input class="radius_input_row nl-foucs" type="text" id="match_type2" readonly lay-verify="required" value="<?=$match['genre_title']?>" autocomplete="off" placeholder="<?=__('考级类别', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级名称', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="post_title"  value="<?=$match['post_title']?>" lay-verify="required" autocomplete="off" placeholder="<?=__('填写本次考级名称', 'nlyd-student')?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级地点', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="address" value="<?=$match['address']?>" lay-verify="required" autocomplete="off" placeholder="<?=__('填写本次考级地点', 'nlyd-student')?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级费用', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row" disabled type="text" id="cost" name="cost" value="<?=isset($match['cost']) ? $match['cost'] : 80.00?>" ></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('报名截止', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" value="<?=$match['entry_end_time']?>" type="text" readonly name="entry_end_time" data-time="<?=$match['data_entry_end_time']?>" id="entry_end_time" lay-verify="required" autocomplete="off" placeholder="<?=__('选择考级报名截止日期', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('考级开始日期', 'nlyd-student')?>：</span>
                            </div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" value="<?=$match['start_time']?>" type="text" readonly name="start_time" data-time="<?=$match['data_start_time']?>" id="match_date" lay-verify="required" autocomplete="off" placeholder="<?=__('选择考级开始日期', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('考级结束日期', 'nlyd-student')?>：</span>
                            </div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" value="<?=$match['end_time']?>" type="text" readonly name="end_time" data-time="<?=$match['data_end_time']?>" id="match_end_date" lay-verify="required" autocomplete="off" placeholder="<?=__('选择考级结束日期', 'nlyd-student')?>">
                            </div>
                        </div>
                        <input type="hidden" name="action" value="zone_create_grading">
                        <input type="hidden" name="grading_id" value="<?=$_GET['grading_id']?>">
                        <?php if($match['allow_cancel'] == 'y') { ?>
                            <span class="details_btn flex-h">
                                <div class="details-button flex1">
                                    <button class="save" type="button" id="end_match"><?=__('取消考级', 'nlyd-student')?></button>
                                </div>
                                <div class="details-button flex1 last-btn">
                                    <button class="see_button" type="button" lay-filter='layform' lay-submit=""><?=__(!empty($_GET['grading_id']) ? '保存更新' :'确认发布', 'nlyd-student')?></button>
                                </div>
                            </span>   
                        <?php }else{ ?>
                            <a class="a-btn a-btn-table" lay-filter="layform" lay-submit=""><div><?=__(!empty($_GET['grading_id']) ? '保存更新' :'确认发布', 'nlyd-student')?></div></a>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>            
    </div>
</div>
<script>
jQuery(function($) { 
var match_type1_Data=<?=!empty($scene_list) ? $scene_list : '[]';?>;//考级类别
var match_type2_Data=<?=!empty($category_list) ? $category_list : '[]';?>;//考级类型
var match_date_Data=$.validationLayui.dates2;//考级日期
var posiotion_match_type1=[0];//初始化位置，高亮展示
var posiotion_match_type2=[0];//初始化位置，高亮展示
var posiotion_match_date=[0,0,0,0,0];//初始化位置，高亮展示
var posiotion_gradeEnd_date=[0,0,0,0,0];
var posiotion_gradeSign_date=[0,0,0,0,0];
$('body').on('change','.change_num',function(){
        var _this=$(this);
        _this.next().next('input').val('')
    })
$('body').on('click','.coach_add_btn',function(){
        var _this=$(this);
        var val=_this.prev('input').val();
        _this.next('input').val('');
        $.ajax({
            data: {
                mobile:val,
                action:'get_mobile_user',
            },
            success: function(res, textStatus, jqXHR){
                if(res.success){
                    _this.next('input').val(res.data.user_id);
                    _this.prev('input').val(res.data.user_name)
                }else{
                    $.alerts(res.data.info)
                }
            },
            complete: function(jqXHR, textStatus){
                if(textStatus=='timeout'){
                    $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
                }
            }
        })
    })
//---------------------------考级类别------------------------------
if($('#match_type1').val().length>0 && $('#match_type1').val()){
    $.each(match_type1_Data,function(index,value){
        if(value['value']==$('#match_type1').val()){
            posiotion_match_type1=[index]
            return false;
        }
    })
}
var mobileSelect1 = new MobileSelect({
    trigger: '#match_type1',
    title: "<?=__('考级类别', 'nlyd-student')?>",
    wheels: [
        {data: match_type1_Data}
    ],
    position:posiotion_match_type1, //初始化定位 打开时默认选中的哪个 如果不填默认为0
    transitionEnd:function(indexArr, data){
        // console.log(data);
    },
    callback:function(indexArr, data){
        var old_val=$('#match_type1').val();
        var new_val=data[0]['value'];
        if(new_val!==old_val){
                $('#match_type1').val(data[0]['value']);
                $('#match_type1_id').val(data[0]['id']);
                var post_data={
                    action:'get_match_cost',
                    type:data[0]['role_alias'],
                    spread_type:'grading'
                }
                $.ajax({
                    data: post_data,
                    success: function(res, textStatus, jqXHR){//获取比赛费用
                        if (res.success) {
                            if(res.data){
                                $('#cost').val(res.data)
                            }else{
                                $.alerts(res.data.info)
                            }
                        }else{
                            $.alerts(res.data.info)
                        }

                    },
                    complete: function(jqXHR, textStatus){
                        if(textStatus=='timeout'){
                            $.alerts("<?=__('获取费用失败', 'nlyd-student')?>")
                　　　　 }
                    }
                })
        }
    }
});
//---------------------------考级类型------------------------------
if($('#match_type2').val().length>0 && $('#match_type2').val()){
    $.each(match_type2_Data,function(index,value){
        if(value['value']==$('#match_type2').val()){
            posiotion_match_type2=[index]
            return false;
        }
    })
}
var mobileSelect2 = new MobileSelect({
    trigger: '#match_type2',
    title: "<?=__('考级类型', 'nlyd-student')?>",
    wheels: [
        {data: match_type2_Data}
    ],
    position:posiotion_match_type2, //初始化定位 打开时默认选中的哪个 如果不填默认为0
    transitionEnd:function(indexArr, data){
        // console.log(data);
    },
    callback:function(indexArr, data){
        $('#match_type2').val(data[0]['value']);
        $('#match_type2_id').val(data[0]['id']);
    }
});
//---------------------------考级开始日期------------------------------
if($('#match_date').length>0 && $('#match_date').attr('data-time') && $('#match_date').attr('data-time').length>0){
    var timeValue=$('#match_date').attr('data-time').split('-');
    $.each(match_date_Data,function(index,value){
        if(parseInt(timeValue[0])==parseInt(value.value)){
            $.each(value.childs,function(i,v){
                if(parseInt(timeValue[1])==parseInt(v.value)){
                    $.each(v.childs,function(j,val){
                        if(parseInt(timeValue[2])==parseInt(val.value)){
                            $.each(val.childs,function(k,b){
                                if(parseInt(timeValue[3])==parseInt(b.value)){
                                    $.each(b.childs,function(l,c){
                                        if(parseInt(timeValue[4])==parseInt(c.value)){
                                            posiotion_match_date=[index,i,j,k,l];
                                        }
                                    })
                                }
                            })
                        }
                    })
                }
            })
        }
    })
}
var mobileSelect3 = new MobileSelect({
    trigger: '#match_date',
    title: "<?=__('开始日期', 'nlyd-student')?>",
    wheels: [
        {data: match_date_Data}
    ],
    new_title:["<?=__('年', 'nlyd-student')?>","<?=__('月', 'nlyd-student')?>","<?=__('日', 'nlyd-student')?>","<?=__('时', 'nlyd-student')?>","<?=__('分', 'nlyd-student')?>"],
    position:posiotion_match_date, //初始化定位 打开时默认选中的哪个 如果不填默认为0
    transitionEnd:function(indexArr, data){
        // console.log(data);
    },
    callback:function(indexArr, data){
        var text=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']+' '+data[3]['value']+':'+data[4]['value'];
        $('#match_date').val(text);
       
    }
});

//---------------------------考级结束日期------------------------------
if($('#match_end_date').length>0 && $('#match_end_date').attr('data-time') && $('#match_end_date').attr('data-time').length>0){
    var timeValue=$('#match_end_date').attr('data-time').split('-');
    $.each(match_date_Data,function(index,value){
        if(parseInt(timeValue[0])==parseInt(value.value)){
            $.each(value.childs,function(i,v){
                if(parseInt(timeValue[1])==parseInt(v.value)){
                    $.each(v.childs,function(j,val){
                        if(parseInt(timeValue[2])==parseInt(val.value)){
                            $.each(val.childs,function(k,b){
                                if(parseInt(timeValue[3])==parseInt(b.value)){
                                    $.each(b.childs,function(l,c){
                                        if(parseInt(timeValue[4])==parseInt(c.value)){
                                            posiotion_gradeEnd_date=[index,i,j,k,l];
                                        }
                                    })
                                }
                            })
                        }
                    })
                }
            })
        }
    })
}
var mobileSelect4 = new MobileSelect({
    trigger: '#match_end_date',
    title: "<?=__('结束日期', 'nlyd-student')?>",
    wheels: [
        {data: match_date_Data}
    ],
    new_title:["<?=__('年', 'nlyd-student')?>","<?=__('月', 'nlyd-student')?>","<?=__('日', 'nlyd-student')?>","<?=__('时', 'nlyd-student')?>","<?=__('分', 'nlyd-student')?>"],
    position:posiotion_gradeEnd_date, //初始化定位 打开时默认选中的哪个 如果不填默认为0
    transitionEnd:function(indexArr, data){
        console.log(data);
    },
    callback:function(indexArr, data){
        var text=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']+' '+data[3]['value']+':'+data[4]['value'];
        $('#match_end_date').val(text);
       
    }
});
//---------------------------报名结束日期------------------------------
if($('#entry_end_time').length>0 && $('#entry_end_time').attr('data-time') && $('#entry_end_time').attr('data-time').length>0){
    var timeValue=$('#entry_end_time').attr('data-time').split('-');
    $.each(match_date_Data,function(index,value){
        if(parseInt(timeValue[0])==parseInt(value.value)){
            $.each(value.childs,function(i,v){
                if(parseInt(timeValue[1])==parseInt(v.value)){
                    $.each(v.childs,function(j,val){
                        if(parseInt(timeValue[2])==parseInt(val.value)){
                            $.each(val.childs,function(k,b){
                                if(parseInt(timeValue[3])==parseInt(b.value)){
                                    $.each(b.childs,function(l,c){
                                        if(parseInt(timeValue[4])==parseInt(c.value)){
                                            posiotion_gradeSign_date=[index,i,j,k,l];
                                        }
                                    })
                                }
                            })
                        }
                    })
                }
            })
        }
    })
}
var mobileSelect5 = new MobileSelect({
    trigger: '#entry_end_time',
    title: "<?=__('报名截止', 'nlyd-student')?>",
    wheels: [
        {data: match_date_Data}
    ],
    new_title:["<?=__('年', 'nlyd-student')?>","<?=__('月', 'nlyd-student')?>","<?=__('日', 'nlyd-student')?>","<?=__('时', 'nlyd-student')?>","<?=__('分', 'nlyd-student')?>"],
    position:posiotion_gradeSign_date, //初始化定位 打开时默认选中的哪个 如果不填默认为0
    transitionEnd:function(indexArr, data){
        console.log(data);
    },
    callback:function(indexArr, data){
        var text=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']+' '+data[3]['value']+':'+data[4]['value'];
        $('#entry_end_time').val(text);
       
    }
});
    layui.use(['form'], function(){
        var form = layui.form
        form.render();
        // 自定义验证规则
        form.verify($.validationLayui.allRules);
        // 监听提交
        form.on('submit(layform)', function(data){//实名认证提交
            var _this=$(this);
            console.log(data.field)
            if(data.field['person_liable']==""){
                $.alerts("<?=__('请确考级责任人', 'nlyd-student')?>")
                $('.change_num').focus().addClass('layui-form-danger')
                return false;
            }
            if(!_this.hasClass('disabled')){
                $.ajax({
                    data: data.field,
                    beforeSend:function(XMLHttpRequest){
                        _this.addClass('disabled')
                    },
                    success: function(res, textStatus, jqXHR){
                        console.log(res)
                        $.alerts(res.data.info)
                        if(res.data.url){
                            setTimeout(function() {
                                window.location.href=res.data.url;
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

        $('#end_match').click(function(){//取消考级
            var _this=$(this);
            layer.open({
                type: 1
                ,maxWidth:300
                ,title: "<?=__('提示', 'nlyd-student')?>" //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: "<div class='box-conent-wrapper'><?=__('考级取消不可恢复，是否确认取消', 'nlyd-student')?>？</div>"
                ,btn: [ "<?=__('按错了', 'nlyd-student')?>","<?=__('确认', 'nlyd-student')?>",]
                ,success: function(layero, index){
                },
                cancel: function(index, layero){
                    
                    layer.closeAll();
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    if(!_this.hasClass('disabled')){
                        var data={
                            action:'end_grading',
                            grading_id:$.Request('grading_id'),
                        }
                        $.ajax({
                            data: data,
                            beforeSend:function(XMLHttpRequest){
                                _this.addClass('disabled')
                            },
                            success: function(res, textStatus, jqXHR){
                                console.log(res)
                                if(res.success){
                                    if(res.data.url){
                                        window.location.href=res.data.url
                                    }
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
                        $.alerts("<?=__('正在处理您的请求，请稍后再试', 'nlyd-student')?>",1200)
                    }
                    layer.closeAll();
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
        })
    });
})
</script>
