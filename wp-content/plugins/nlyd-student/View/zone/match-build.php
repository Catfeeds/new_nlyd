
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('/zone/match/')?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__(isset($_GET['match_id']) ? '编辑比赛' : '发布比赛', 'nlyd-student')?></div></h1>
            </header> 
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="width-padding layui-row width-margin-pc">
                    <form class="layui-form apply_form" lay-filter='layform'>
                        <?php if(!empty($scene_list)):?>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('比赛场景', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input type="hidden" name="match_scene"  value="<?=$match['match_scene']?>" id="match_scene">
                                <input class="radius_input_row nl-foucs" type="text" id="match_type1" readonly lay-verify="required" autocomplete="off" placeholder="<?=__('比赛场景', 'nlyd-student')?>" value="<?=$match['scene_title']?>">
                            </div>
                        </div>
                        <?php endif;?>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('比赛类型', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input type="hidden" name="match_genre" value="<?=$match['match_genre']?>" id="match_genre">
                                <input class="radius_input_row nl-foucs" type="text" id="match_type2" readonly lay-verify="required" autocomplete="off" placeholder="<?=__('比赛类型', 'nlyd-student')?>" value="<?=$match['genre_title']?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('比赛名称', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="post_title" lay-verify="required" autocomplete="off" placeholder="<?=__('填写本次比赛名称', 'nlyd-student')?>" value="<?=$match['post_title']?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('比赛地点', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="match_address" lay-verify="required" autocomplete="off" placeholder="<?=__('填写本次比赛地点', 'nlyd-student')?>" value="<?=$match['match_address']?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('比赛费用', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row" disabled type="text" id="match_cost" name="match_cost" value="<?=$match['match_cost'] > 0 ? $match['match_cost'] : $match_cost?>"></div>
                        </div>
                        <?php if(!empty($match['entry_end_time'])):?>
                            <div>
                                <div class="lable_row"><span class="c_black"><?=__('报名截止', 'nlyd-student')?>：</span></div>
                                <div class="input_row">
                                    <input class="radius_input_row" disabled type="text" value="<?=$match['entry_end_time']?>">
                                </div>
                            </div>
                        <?php endif;?>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('开赛日期', 'nlyd-student')?>：</span>
                                <?php if(isset($_GET['match_id'])):?>
                                <a class="c_blue pull-right" href="<?=home_url('/zone/matchTime/match_id/'.$_GET['match_id']);?>"><?=__('查看/修改时间表', 'nlyd-student')?></a>
                                <?php endif;?>
                            </div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" type="text" readonly name="match_start_time" data-time="<?=$match['data_time']?>"  id="match_date" lay-verify="required" autocomplete="off" placeholder="<?=__('选择开赛日期', 'nlyd-student')?>" value="<?=$match['match_start_time']?>">
                            </div>
                        </div>
                        <div class="c_red mt_10">
                            <?=__('比赛发布成功后，管理员可根据实际情况自定义修改赛程时间', 'nlyd-student')?>
                        </div>
                        <input type="hidden" name="action" value="zone_create_match">
                        <input type="hidden" name="match_id" value="<?=$_GET['match_id']?>">
                        <a class="a-btn a-btn-table" lay-filter="layform" lay-submit=""><div><?=__(isset($_GET['match_id']) ? '保存更新' : '确认发布', 'nlyd-student')?></div></a>
                    </form>
                </div>
            </div>
        </div>            
    </div>
</div>
<script>
jQuery(function($) { 
var match_type1_Data=<?=$scene_list?>;//比赛类别
var match_type2_Data=<?=$match_genre?>;//比赛类型
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
    title: "<?=__('比赛场景', 'nlyd-student')?>",
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
            $('#match_scene').val(data[0]['id']);
            var post_data={
                action:'get_match_cost',
                type:data[0]['role_alias']
            }
            $.ajax({
                data: post_data,
                success: function(res, textStatus, jqXHR){//获取比赛费用
                    if(res.data){
                        $('#match_cost').val(res.data)
                    }
                },
                complete: function(jqXHR, textStatus){
                    if(textStatus=='timeout'){
                        $.alerts("<?=__('获取比赛费用失败', 'nlyd-student')?>")
            　　　　 }
                }
            })
       }
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
    title: "<?=__('比赛类型', 'nlyd-student')?>",
    wheels: [
        {data: match_type2_Data}
    ],
    position:posiotion_match_type2, //初始化定位 打开时默认选中的哪个 如果不填默认为0
    transitionEnd:function(indexArr, data){
        // console.log(data);
    },
    callback:function(indexArr, data){
        $('#match_type2').val(data[0]['value']);
        $('#match_genre').val(data[0]['id']);
    }
});
//---------------------------开赛日期------------------------------
if($('#match_date').length>0 && $('#match_date').attr('data-time').length>0){
    var timeValue=$('#match_date').attr('data-time').split('-');
    $.each(match_date_Data,function(index,value){
        if(timeValue[0]==value.value+""){
            posiotion_match_date=[index,0,0,0,0];
            $.each(value.childs,function(i,v){
                if(timeValue[1]==v.value+""){
                    posiotion_match_date=[index,i,0,0,0];
                    $.each(v.childs,function(j,val){
                        if(timeValue[2]==val.value+""){
                            posiotion_match_date=[index,i,j,0,0];
                            $.each(val.childs,function(k,b){
                                if(timeValue[3]==b.value+""){
                                    posiotion_match_date=[index,i,j,k,0];
                                    $.each(b.childs,function(l,c){
                                        if(timeValue[4]==c.value+""){
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
    title: "<?=__('开赛日期', 'nlyd-student')?>",
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
        var text1=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']+'-'+data[3]['value']+'-'+data[4]['value'];
        $('#match_date').val(text).attr('data-time',text1);
       
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
            var _this=$(this);
            if(!_this.hasClass('disabled')){
                $.ajax({
                    data: data.field,
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
    });
})
</script>
