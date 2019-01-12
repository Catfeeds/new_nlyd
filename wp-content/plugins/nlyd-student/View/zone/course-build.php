
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
                        <input type="hidden" name="action" value="zone_course_created"/>
                        <input type="hidden" name="id" value="<?=$_GET['id']?>"/>
                        <?php if(!empty($course_type)):?>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程类型', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input type="hidden" name="course_type"  value="<?=$course['course_type']?>" id="course_scene">
                                <input class="radius_input_row nl-foucs" readonly id="course_type1" type="text" lay-verify="required" value="<?=$course['type_name']?>" placeholder="<?=__('课程类型', 'nlyd-student')?>">
                            </div>
                        </div>
                        <?php endif;?>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('教学类型', 'nlyd-student')?>：</span>
                            </div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input type="hidden" name="course_category_id"  value="<?=$course['course_category_id']?>" id="course_genre">
                                <input class="radius_input_row nl-foucs" readonly id="course_type2" type="text" lay-verify="required" autocomplete="off" placeholder="<?=__('记忆课程教学', 'nlyd-student')?>" value="<?=$course['category_type']?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程名称', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="course_title" lay-verify="required" autocomplete="off" placeholder="<?=__('填写课程名称', 'nlyd-student')?>" value="<?=$course['course_title']?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程时长', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="duration" lay-verify="required" autocomplete="off" placeholder="<?=__('填写课程时长', 'nlyd-student')?>" value="<?=$course['duration']?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black">
                                <?=__('授课教练', 'nlyd-student')?>：</span>
                                <span class="c_red fs_12"><?=__('任职人员需在平台注册并实名认证，否则审核无法通过', 'nlyd-student')?></span>
                            </div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" type="tel" lay-verify="required" autocomplete="off" name="coach_phone" placeholder="<?=__('输入用户注册手机号码查询，未注册无法选择', 'nlyd-student')?>" value="<?=$course['coach_phone']?>">
                                <!--<select class="js-data-select-ajax" name="coach_id" style="width: 100%" data-action="get_manage_user" lay-verify="required"  data-placeholder="<?/*=__('输入用户注册手机号码查询，未注册无法选择', 'nlyd-student')*/?>" ></select>-->
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程费用', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" disabled type="text" name="const" value="500.00">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('开放名额', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" type="tel" name="open_quota" lay-verify="required" autocomplete="off" placeholder="<?=__('输入开放名额', 'nlyd-student')?>" value="<?=$course['open_quota']?>">
                            </div>
                        </div>
                  
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('开课日期', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" type="text" readonly name="course_start_time" data-time="<?=$course['data_start_time']?>"  id="course_start_date" lay-verify="required" autocomplete="off" placeholder="<?=__('选择开课日期', 'nlyd-student')?>" value="<?=$course['start_time']?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('结课日期', 'nlyd-student')?>：</span><a href="" class="c_blue pull-right"><?=__('立即结课', 'nlyd-student')?></a></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" type="text" readonly name="course_end_time" data-time="<?=$course['data_end_time']?>"  id="course_end_date" lay-verify="required" autocomplete="off" placeholder="<?=__('选择开课日期', 'nlyd-student')?>" value="<?=$course['end_time']?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程简介', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <textarea class="radius_input_row nl-foucs" type="text" name="course_details" placeholder="<?=__('课程简介', 'nlyd-student')?>"></textarea>
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
    var course_type1_Data=<?=$course_type?>;//课程类型
    var course_type2_Data=<?=$category_type?>;//教学类型
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
        title: "<?=__('课程类型', 'nlyd-student')?>",
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
        title: "<?=__('教学类型', 'nlyd-student')?>",
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
    if($('#course_start_date').length>0 && $('#course_start_date').attr('data-time') && $('#course_start_date').attr('data-time').length>0){
        var timeValue=$('#course_start_date').attr('data-time').split('-');
        $.each(course_date_Data,function(index,value){
            if(timeValue[0]==value.value+""){
                posiotion_course_date=[index,0,0,0,0];
                $.each(value.childs,function(i,v){
                    if(timeValue[1]==v.value+""){
                        posiotion_course_date=[index,i,0,0,0];
                        $.each(v.childs,function(j,val){
                            if(timeValue[2]==val.value+""){
                                posiotion_course_date=[index,i,j,0,0];
                                $.each(val.childs,function(k,b){
                                    if(timeValue[3]==b.value+""){
                                        posiotion_course_date=[index,i,j,k,0];
                                        $.each(b.childs,function(l,c){
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
        trigger: '#course_start_date',
        title: "<?=__('开课日期', 'nlyd-student')?>",
        wheels: [
            {data: course_date_Data}
        ],
        new_title:["<?=__('年', 'nlyd-student')?>","<?=__('月', 'nlyd-student')?>","<?=__('日', 'nlyd-student')?>","<?=__('时', 'nlyd-student')?>","<?=__('分', 'nlyd-student')?>"],
        position:posiotion_course_date, //初始化定位 打开时默认选中的哪个 如果不填默认为0
        transitionEnd:function(indexArr, data){
            // console.log(data);
        },
        callback:function(indexArr, data){
            var text=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']+' '+data[3]['value']+':'+data[4]['value'];
            var text1=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']+'-'+data[3]['value']+'-'+data[4]['value'];
            $('#course_start_date').val(text).attr('data-time',text1);
        
        }
    });

    var posiotion_course_end_date=[0,0,0,0,0]
    //---------------------------结课日期------------------------------
    if($('#course_end_date').length>0 && $('#course_end_date').attr('data-time') && $('#course_end_date').attr('data-time').length>0){
        var timeValue=$('#course_end_date').attr('data-time').split('-');
        $.each(course_date_Data,function(index,value){
            if(timeValue[0]==value.value+""){
                posiotion_course_end_date=[index,0,0,0,0];
                $.each(value.childs,function(i,v){
                    if(timeValue[1]==v.value+""){
                        posiotion_course_end_date=[index,i,0,0,0];
                        $.each(v.childs,function(j,val){
                            if(timeValue[2]==val.value+""){
                                posiotion_course_end_date=[index,i,j,0,0];
                                $.each(val.childs,function(k,b){
                                    if(timeValue[3]==b.value+""){
                                        posiotion_course_end_date=[index,i,j,k,0];
                                        $.each(b.childs,function(l,c){
                                            if(timeValue[4]==c.value+""){
                                                posiotion_course_end_date=[index,i,j,k,l];
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
        trigger: '#course_end_date',
        title: "<?=__('开课日期', 'nlyd-student')?>",
        wheels: [
            {data: course_date_Data}
        ],
        new_title:["<?=__('年', 'nlyd-student')?>","<?=__('月', 'nlyd-student')?>","<?=__('日', 'nlyd-student')?>","<?=__('时', 'nlyd-student')?>","<?=__('分', 'nlyd-student')?>"],
        position:posiotion_course_end_date, //初始化定位 打开时默认选中的哪个 如果不填默认为0
        transitionEnd:function(indexArr, data){
            // console.log(data);
        },
        callback:function(indexArr, data){
            var text=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']+' '+data[3]['value']+':'+data[4]['value'];
            var text1=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']+'-'+data[3]['value']+'-'+data[4]['value'];
            $('#course_end_date').val(text).attr('data-time',text1);
        
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
