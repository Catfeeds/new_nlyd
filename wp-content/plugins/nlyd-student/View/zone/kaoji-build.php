
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
                <h1 class="mui-title"><div><?=__('发布考级', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="width-padding layui-row width-margin-pc">
                    <form class="layui-form apply_form" lay-filter='layform'>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级责任人', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <select class="js-data-select-ajax" name="person_liable" style="width: 100%" data-action="get_manage_user" data-placeholder="选择考级责任人" >
                                    <option value="<?=$row['chairman_id']?>" selected><?=$row['chairman_name']?></option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级场景', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input type="hidden" name="scene" id="match_type1_id" value="">
                                <input class="radius_input_row nl-foucs" type="text" id="match_type1" readonly lay-verify="required" autocomplete="off" placeholder="<?=__('考级场景', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级类别', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input type="hidden" name="category_id" id="match_type2_id" value="">
                                <input class="radius_input_row nl-foucs" type="text" id="match_type2" readonly lay-verify="required" autocomplete="off" placeholder="<?=__('考级类别', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级名称', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="post_title" lay-verify="required" autocomplete="off" placeholder="<?=__('填写本次考级名称', 'nlyd-student')?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级地点', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="address" lay-verify="required" autocomplete="off" placeholder="<?=__('填写本次考级地点', 'nlyd-student')?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级费用', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row" disabled type="text" id="cost" name="cost" value=""></div>
                        </div>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('考级开始日期', 'nlyd-student')?>：</span>
                            </div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" value="" type="text" readonly name="start_time" data-time="2019,10,11,11,11" id="match_date" lay-verify="required" autocomplete="off" placeholder="<?=__('选择考级开始日期', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('考级结束日期', 'nlyd-student')?>：</span>
                            </div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" value="" type="text" readonly name="end_time" data-time="2019,10,11,11,11" id="match_end_date" lay-verify="required" autocomplete="off" placeholder="<?=__('选择考级结束日期', 'nlyd-student')?>">
                            </div>
                        </div>
                        <input type="hidden" name="action" value="zone_create_grading">
                        <input type="hidden" name="grading_id" value="<?=$_GET['grading_id']?>">
                        <a class="a-btn a-btn-table" lay-filter="layform" lay-submit=""><div><?=__('确认发布/保存更新', 'nlyd-student')?></div></a>
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
                    type:data[0]['role_alias']
                }
                $.ajax({
                    data: post_data,
                    success: function(res, textStatus, jqXHR){//获取比赛费用
                        if(res.data){
                            $('#cost').val(res.data)
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
//---------------------------考级结束日期------------------------------
if($('#match_date').length>0 && $('#match_date').attr('data-time') && $('#match_date').attr('data-time').length>0){
    var timeValue=$('#match_date').attr('data-time').split(',');
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
    title: "<?=__('结束日期', 'nlyd-student')?>",
    wheels: [
        {data: match_date_Data}
    ],
    new_title:["<?=__('年', 'nlyd-student')?>","<?=__('月', 'nlyd-student')?>","<?=__('日', 'nlyd-student')?>","<?=__('时', 'nlyd-student')?>","<?=__('分', 'nlyd-student')?>"],
    position:posiotion_match_date, //初始化定位 打开时默认选中的哪个 如果不填默认为0
    transitionEnd:function(indexArr, data){
        // console.log(data);
    },
    callback:function(indexArr, data){
        var text=data[0]['value']+'/'+data[1]['value']+'/'+data[2]['value']+' '+data[3]['value']+':'+data[4]['value'];
        $('#match_date').val(text);
       
    }
});

//---------------------------考级日期------------------------------
if($('#match_end_date').length>0 && $('#match_end_date').attr('data-time') && $('#match_end_date').attr('data-time').length>0){
    var timeValue=$('#match_end_date').attr('data-time').split(',');
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
    title: "<?=__('考级日期', 'nlyd-student')?>",
    wheels: [
        {data: match_date_Data}
    ],
    new_title:["<?=__('年', 'nlyd-student')?>","<?=__('月', 'nlyd-student')?>","<?=__('日', 'nlyd-student')?>","<?=__('时', 'nlyd-student')?>","<?=__('分', 'nlyd-student')?>"],
    position:posiotion_gradeEnd_date, //初始化定位 打开时默认选中的哪个 如果不填默认为0
    transitionEnd:function(indexArr, data){
        // console.log(data);
    },
    callback:function(indexArr, data){
        var text=data[0]['value']+'/'+data[1]['value']+'/'+data[2]['value']+' '+data[3]['value']+':'+data[4]['value'];
        $('#match_end_date').val(text);
       
    }
});
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
   
    layui.use(['form'], function(){
        var form = layui.form
        form.render();
        // 自定义验证规则
        form.verify($.validationLayui.allRules);
        // 监听提交
        form.on('submit(layform)', function(data){//实名认证提交
            var _this=$(this);
            console.log(data.field)
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
    });
})
</script>
