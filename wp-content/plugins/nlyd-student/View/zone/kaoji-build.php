
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
                            <div class="input_row"><input class="radius_input_row change_ajax" type="text" name="zone_num" value=""></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级类别', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" type="text" id="match_type1" readonly name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('考级类别', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级类型', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" type="text" id="match_type2" readonly name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('考级类型', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级名称', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('填写本次考级名称', 'nlyd-student')?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级地点', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('填写本次考级地点', 'nlyd-student')?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('考级费用', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row" disabled type="text" name="zone_num" value=""></div>
                        </div>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('考级日期', 'nlyd-student')?>：</span>
                            </div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" value="" type="text" readonly name="zone_name"  id="match_date" lay-verify="required" autocomplete="off" placeholder="<?=__('选择考级日期', 'nlyd-student')?>">
                            </div>
                        </div>
                        <a class="a-btn a-btn-table" lay-filter="layform" lay-submit=""><div><?=__('确认发布/保存更新', 'nlyd-student')?></div></a>
                    </form>
                </div>
            </div>
        </div>            
    </div>
</div>
<script>
jQuery(function($) { 
var match_type1_Data=[{id:1,value:"<?=__('正式考级', 'nlyd-student')?>"},{id:2,value:"<?=__('模拟考级', 'nlyd-student')?>"}];//考级类别
var match_type2_Data=[{id:1,value:"速算"},{id:2,value:"速读"},{id:3,value:"速记"}];//考级类型
var match_date_Data=$.validationLayui.dates2;//考级日期
var posiotion_match_type1=[0];//初始化位置，高亮展示
var posiotion_match_type2=[0];//初始化位置，高亮展示
var posiotion_match_date=[0,0,0,0,0];//初始化位置，高亮展示

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
    title: '<?=__('考级类别', 'nlyd-student')?>',
    wheels: [
        {data: match_type1_Data}
    ],
    position:posiotion_match_type1, //初始化定位 打开时默认选中的哪个 如果不填默认为0
    transitionEnd:function(indexArr, data){
        // console.log(data);
    },
    callback:function(indexArr, data){
        $('#match_type1').val(data[0]['value']);
       
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
    title: '<?=__('考级类型', 'nlyd-student')?>',
    wheels: [
        {data: match_type2_Data}
    ],
    position:posiotion_match_type2, //初始化定位 打开时默认选中的哪个 如果不填默认为0
    transitionEnd:function(indexArr, data){
        // console.log(data);
    },
    callback:function(indexArr, data){
        $('#match_type2').val(data[0]['value']);
       
    }
});
//---------------------------考级日期------------------------------
if($('#match_date').val().length>0 && $('#match_date').val().length>0){
    var birthdayValue=$('#match_date').val().split('-');
    $.each($.validationLayui.dates2,function(index,value){
        if(birthdayValue[0]==value.value){
            $.each(value.childs,function(i,v){
                if(birthdayValue[1]==v.value){
                    $.each(v.childs,function(j,val){
                        if(birthdayValue[2]==val.value){
                            $.each(v.childs,function(k,b){
                                if(birthdayValue[3]==b.value){
                                    $.each(v.childs,function(l,c){
                                        if(birthdayValue[4]==c.value){
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
    title: '<?=__('考级日期', 'nlyd-student')?>',
    wheels: [
        {data: match_date_Data}
    ],
    position:posiotion_match_date, //初始化定位 打开时默认选中的哪个 如果不填默认为0
    transitionEnd:function(indexArr, data){
        // console.log(data);
    },
    callback:function(indexArr, data){
        var text=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']+'-'+data[3]['value']+'-'+data[4]['value'];
        $('#match_date').val(text);
       
    }
});

    $('.change_ajax').keyup(function(){
        var _this=$(this);
        _this.next('.select_box').remove()
        //if(!_this.hasClass('loading')){
            var keywords = _this.val();/*
            if (keywords=='') { _this.next('.select_box').remove();_this.removeClass('loading'); return };*/
            var data={
                action:"get_manage_user",
                value:keywords
            };
            _this.parents('div').append('<div class="select_box" id="select_box"></div>')
            // _this.parents('div').css("position","relative");
            $.ajax({
                data:data,
                type:"POST",
                beforeSend:function(){
                    _this.next('.select_box').empty().append('<div class="select_row">正在加载...</div>');
                    _this.addClass('loading')
                },
                success:function(res){
                    var dom="";
                    _this.next('.select_box').empty().show();
                    if(res.success){
                        if(res.data == ''){
                            var item='<div class="select_row">未搜到该用户</div>'
                            dom+=item
                        }else {

                            $.each(res.data,function(i,v){
                                var item='<div class="select_row choose" data-id="'+v.user_id+'" data-value="'+v.text+'">' + v.text + '</div>'
                                dom+=item
                            })

                        }
                    }else {
                        var item='<div class="select_row">未搜到该用户....</div>'
                        dom+=item
                    }
                    _this.next('.select_box').append(dom)
                    _this.removeClass('loading')
                },
                error:function(){
                    _this.next('.select_box').empty().show();
                    _this.next('.select_box').append('<div class="select_row">网络延迟</div>');
                    _this.next('.select_box').remove()
                    _this.removeClass('loading')
                }
            })
        //}
    })
    $('body').on('click','.choose',function(){
        var _this=$(this);
        var val=_this.attr('data-value');
        var id=_this.attr('data-id');
        _this.parent('.select_box').parent('div').find('.change_ajax').val(val);
        _this.parent('.select_box').parent('div').find('.get_id').val(id)
    })
    $('body').click(function(e){
        if($('#select_box').length>0){
            var box=$('#select_box');
            if(!$(e.target).hasClass('choose') && !$(e.target).hasClass('change_ajax')){
                box.parent('div').find('input').val('');
            }
        }
        
        $('.select_box').remove()
    })
    layui.use(['form'], function(){
        var form = layui.form
        form.render();
        // 自定义验证规则
        form.verify($.validationLayui.allRules);
        // 监听提交
        form.on('submit(layform)', function(data){//实名认证提交
            console.log(data.field)
            $.ajax({
                data: data.field,
                success: function(res, textStatus, jqXHR){
                    $.alerts(res.data.info)
                    if(res.data.url){
                        setTimeout(function() {
                            window.location.href=res.data.url
                        }, 300);

                    }
                }
            })
            return false;
        });
    });
})
</script>
