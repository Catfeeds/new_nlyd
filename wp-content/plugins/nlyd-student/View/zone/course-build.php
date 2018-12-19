
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
                <h1 class="mui-title"><div><?=__('发布课程', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding layui-row width-margin-pc">
                    <form class="layui-form apply_form" lay-filter='layform'>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程类型', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input type="hidden" name="course_scene"  value="" id="course_scene">
                                <input class="radius_input_row nl-foucs" readonly id="course_type1" type="text" lay-verify="required" value="乐学乐分享基础应用课" placeholder="<?=__('课程类型', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('教学类型', 'nlyd-student')?>：</span>
                            </div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input type="hidden" name="course_genre"  value="" id="course_genre">
                                <input class="radius_input_row nl-foucs" readonly id="course_type2" type="text" lay-verify="required" autocomplete="off" placeholder="<?=__('记忆课程教学', 'nlyd-student')?>" value="记忆课程教学">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程名称', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('填写课程名称', 'nlyd-student')?>" value="<?=!empty($row) ? $row['zone_address'] :''?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程时长', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="legal_person" lay-verify="required" autocomplete="off" placeholder="<?=__('填写课程时长', 'nlyd-student')?>" value="<?=!empty($row) ? $row['legal_person'] :''?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('授课教练', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <select class="js-data-select-ajax" name="secretary_id" style="width: 100%" data-action="get_manage_user" lay-verify="required"  data-placeholder="授课教练" ></select>
                                <!-- <input class="get_id" name="secretary_id" style="display:none" value="<?=$row['secretary_id']?>"> -->
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程费用', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" disabled type="text" value="500.00">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('开放名额', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" type="tel" name="opening_bank_address" lay-verify="required" autocomplete="off" placeholder="<?=__('输入开放名额', 'nlyd-student')?>" value="<?=!empty($row) ? $row['opening_bank_address'] :''?>">
                            </div>
                        </div>
                  
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('开课日期', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" type="text" readonly name="match_start_time" data-time="2019-11-11-11-11"  id="course_date" lay-verify="required" autocomplete="off" placeholder="<?=__('选择开课日期', 'nlyd-student')?>" value="2019-11-11 11:11">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程简介', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <textarea class="radius_input_row nl-foucs" type="text" placeholder="<?=__('课程简介', 'nlyd-student')?>"></textarea>
                            </div>
                        </div>
                        <span class="details_btn flex-h">
                            <div class="details-button flex1">
                                <button class="save" type="button" class=""><?=__('存草稿', 'nlyd-student')?></button>
                            </div>
                            <div class="details-button flex1 last-btn">
                                <button class="see_button" type="button" lay-filter='layform' lay-submit="" href="<?=home_url('orders/logistics')?>"><?=__('发 布', 'nlyd-student')?></button>
                            </div>
                        </span>
                    </form>
                    
                </div>
            </div>
        </div>            
    </div>
</div>
<script>
jQuery(function($) { 
    var course_type1_Data=[{id:1,value:"乐学乐"},{id:2,value:"乐学乐2"}];//课程类型
    var course_type2_Data=[{id:1,value:"基础应用课"},{id:2,value:"提升应用课"}];//教学类型
    var course_date_Data=$.validationLayui.dates2;//开课日期
    var posiotion_course_type1=[0];//初始化位置，高亮展示
    var posiotion_course_type2=[0];//初始化位置，高亮展示
    var posiotion_course_date=[0,0,0,0,0];//初始化位置，高亮展示

    //---------------------------课程类型------------------------------
    if($('#course_type1').val().length>0 && $('#course_type1').val()){
        $.each(course_type1_Data,function(index,value){
            if(value['value']==$('#course_type1').val()){
                posiotion_course_type1=[index]
                return false;
            }
        })
    }
    var mobileSelect1 = new MobileSelect({
        trigger: '#course_type1',
        title: '<?=__('课程类型', 'nlyd-student')?>',
        wheels: [
            {data: course_type1_Data}
        ],
        position:posiotion_course_type1, //初始化定位 打开时默认选中的哪个 如果不填默认为0
        transitionEnd:function(indexArr, data){
            // console.log(data);
        },
        callback:function(indexArr, data){
            $('#course_type1').val(data[0]['value']);
            $('#course_scene').val(data[0]['id']);
        
        }
    });
    //---------------------------教学类型------------------------------
    if($('#course_type2').val().length>0 && $('#course_type2').val()){
        $.each(course_type2_Data,function(index,value){
            if(value['value']==$('#course_type2').val()){
                posiotion_course_type2=[index]
                return false;
            }
        })
    }
    var mobileSelect2 = new MobileSelect({
        trigger: '#course_type2',
        title: '<?=__('教学类型', 'nlyd-student')?>',
        wheels: [
            {data: course_type2_Data}
        ],
        position:posiotion_course_type2, //初始化定位 打开时默认选中的哪个 如果不填默认为0
        transitionEnd:function(indexArr, data){
            // console.log(data);
        },
        callback:function(indexArr, data){
            $('#course_type2').val(data[0]['value']);
            $('#course_genre').val(data[0]['id']);
        
        }
    });
    //---------------------------开课日期------------------------------
    if($('#course_date').length>0 && $('#course_date').attr('data-time') && $('#course_date').attr('data-time').length>0){
        var timeValue=$('#course_date').attr('data-time').split('-');
        $.each($.validationLayui.dates2,function(index,value){
            if(timeValue[0]==value.value+""){
                posiotion_course_date=[index,0,0,0,0];
                $.each(value.childs,function(i,v){
                    if(timeValue[1]==v.value+""){
                        posiotion_course_date=[index,i,0,0,0];
                        $.each(v.childs,function(j,val){
                            if(timeValue[2]==val.value+""){
                                posiotion_course_date=[index,i,j,0,0];
                                $.each(v.childs,function(k,b){
                                    if(timeValue[3]==b.value+""){
                                        posiotion_course_date=[index,i,j,k,0];
                                        $.each(v.childs,function(l,c){
                                            if(timeValue[4]==c.value+""){
                                                posiotion_course_date=[index,i,j,k,l];
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
        trigger: '#course_date',
        title: '<?=__('开课日期', 'nlyd-student')?>',
        wheels: [
            {data: course_date_Data}
        ],
        position:posiotion_course_date, //初始化定位 打开时默认选中的哪个 如果不填默认为0
        transitionEnd:function(indexArr, data){
            // console.log(data);
        },
        callback:function(indexArr, data){
            var text=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']+' '+data[3]['value']+':'+data[4]['value'];
            var text1=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']+'-'+data[3]['value']+'-'+data[4]['value'];
            $('#course_date').val(text).attr('data-time',text1);
        
        }
    });
    $('.js-data-select-ajax').select2({
        ajax: {
            url: function(params){
                return admin_ajax +'?action=get_manage_user'   
                // return "https://api.github.com/search/repositories"
            },
            dataType: 'json',
            delay: 250,//在多少毫秒内没有输入时则开始请求服务器
            processResults: function (data, params) {
                // 此处解析数据，将数据返回给select2
                console.log(data.data)
                var x=data.data;
                return {
                    results:x,// data返回数据（返回最终数据给results，如果我的数据在data.res下，则返回data.res。这个与服务器返回json有关）
                };
            },
            cache: true
        },
        placeholder: '请输入关键字',
        escapeMarkup: function (markup) { return markup; }, // 字符转义处理
        templateResult: formatRepo,//返回结果回调function formatRepo(repo){return repo.text},这样就可以将返回结果的的text显示到下拉框里，当然你可以return repo.text+"1";等
        templateSelection: formatRepoSelection,//选中项回调function formatRepoSelection(repo){return repo.text}
        language:'zh-CN'

    })
    function formatRepo (repo) {//repo对象根据拼接返回结果
        if (repo.loading) {
            return repo.text;
        }
        return repo.text;
    }
    function formatRepoSelection (repo) {//根据选中的最新返回显示在选择框中的文字
        return  repo.text;
    }


    $('.save').click(function(){
        var data=$(".layui-form").serializeArray();
        // serialize
        console.log(data)
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
