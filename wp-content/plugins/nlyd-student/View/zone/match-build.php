
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
                <h1 class="mui-title"><div><?=__('发布比赛', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding layui-row width-margin-pc">
                    <form class="layui-form apply_form" lay-filter='layform'>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('比赛类别', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" type="text" id="match_type1" readonly name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('比赛类别', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('比赛类型', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" type="text" id="match_type2" readonly name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('比赛类型', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('比赛名称', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('填写本次比赛名称', 'nlyd-student')?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('比赛地点', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('填写本次比赛地点', 'nlyd-student')?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('比赛费用', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row" disabled type="text" name="zone_num" value=""></div>
                        </div>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('开赛日期', 'nlyd-student')?>：</span>
                                <a class="c_blue"><?=__('生成赛程时间表', 'nlyd-student')?></a>
                                <a class="c_blue pull-right" href="<?=home_url('/zone/matchTime/');?>"><?=__('查看/修改时间表', 'nlyd-student')?></a>
                            </div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" value="" type="text" readonly name="zone_name"  id="match_date" lay-verify="required" autocomplete="off" placeholder="<?=__('选择开赛日期', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div class="c_red mt_10">
                            <?=__('请在发布比赛后再次编辑生成比赛时间，管理员可根据实际情况自定义修改赛程时间', 'nlyd-student')?>
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
var match_type1_Data=[{id:1,value:"<?=__('正式比赛', 'nlyd-student')?>"},{id:2,value:"<?=__('模拟赛', 'nlyd-student')?>"}];//比赛类别
var match_type2_Data=[{id:1,value:"脑力世界杯"},{id:2,value:"国学脑王大赛"}];//比赛类型
var match_date_Data=$.validationLayui.dates2;//开赛日期
var posiotion_match_type1=[0];//初始化位置，高亮展示
var posiotion_match_type2=[0];//初始化位置，高亮展示
var posiotion_match_date=[0,0,0,0,0];//初始化位置，高亮展示

//---------------------------比赛类别------------------------------
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
    title: '<?=__('比赛类别', 'nlyd-student')?>',
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
//---------------------------比赛类型------------------------------
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
    title: '<?=__('比赛类型', 'nlyd-student')?>',
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
//---------------------------开赛日期------------------------------
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
    title: '<?=__('开赛日期', 'nlyd-student')?>',
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
