
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper"  style="padding-bottom: 333px;" >
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__($_GET['id'] > 0 ? '编辑课程':'发布课程', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="zone-form-tips width-padding width-padding-pc"><i class="iconfont">&#xe65b;</i> <?=__('任职人员需在平台注册并实名认证，否则审核无法通过', 'nlyd-student')?></div>
                <div class="width-padding layui-row width-margin-pc">
                    <form class="layui-form apply_form" lay-filter='layform'>
                        <input type="hidden" name="action" value="zone_course_created"/>
                        <input type="hidden" id="course_id" name="id" value="<?=$_GET['id']?>"/>
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
                            </div>
                            <div class="input_row change_num_row">
                                <input class="radius_input_row change_num nl-foucs" type="text" lay-verify="required" autocomplete="off" placeholder="<?=__('输入任职人员注册手机号查询，未注册无法选择', 'nlyd-student')?>" value="<?=$course['coach_phone']?>">
                                <a class="coach_add_btn c_blue">确认</a> 
                                <input type="hidden" name="coach_phone">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black">
                                <?=__('授课地点', 'nlyd-student')?>：</span>
                            </div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" type="text" lay-verify="required" autocomplete="off" name="address" placeholder="<?=__('输入授课地点', 'nlyd-student')?>" value="<?=$course['address']?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程费用', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" disabled type="text" id="cost" name="const" value="<?=$course['const'] > 0 ? $course['const'] : 3000.00 ?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('开放名额', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" type="tel" name="open_quota" lay-verify="required" autocomplete="off" placeholder="<?=__('输入开放名额', 'nlyd-student')?>" value="<?=$course['open_quota']?>">
                            </div>
                        </div>
                  
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('开课时间', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" type="text" readonly name="course_start_time" data-time="<?=$course['data_start_time']?>"  id="course_start_date" autocomplete="off" placeholder="<?=__('选择开课时间', 'nlyd-student')?>" value="<?=$course['start_time']?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('结课日期', 'nlyd-student')?>：</span>
                                <?php if(!empty($course['start_time']) && strtotime($course['start_time']) < get_time()):?>
                                <a class="c_blue pull-right" id="close_course"><?=__('立即结课', 'nlyd-student')?></a>
                                <?php endif;?>
                            </div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" type="text" readonly name="course_end_time" data-time="<?=$course['data_end_time']?>"  id="course_end_date" autocomplete="off" placeholder="<?=__('选择结课日期', 'nlyd-student')?>" value="<?=$course['end_time']?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程简介', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <textarea class="radius_input_row nl-foucs" type="text" name="course_details" placeholder="<?=__('课程简介', 'nlyd-student')?>"></textarea>
                            </div>
                        </div>
                        <a class="a-btn a-btn-table" lay-filter='layform' lay-submit="" href="<?=home_url('orders/logistics')?>"><div><?=__($_GET['id'] > 0 ? '确认修改':'发 布', 'nlyd-student')?></div></a>
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
    var course_date_Data=$.validationLayui.dates2;//开课时间
    var posiotion_course_type1=[0];//初始化位置，高亮展示
    var posiotion_course_type2=[0];//初始化位置，高亮展示
    var posiotion_course_date=[0,0,0,0,0];//初始化位置，高亮展示
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
    function getcost(post_data) {
        $.ajax({
            data: post_data,
            success: function(res, textStatus, jqXHR){//获取比赛费用
                if(res.data){
                    $('#cost').val(res.data)
                }
            },
            complete: function(jqXHR, textStatus){
                if(textStatus=='timeout'){
                    $.alerts("<?=__('获取比赛费用失败', 'nlyd-student')?>")
        　　　　 }
            }
        })
    }
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
            // $('#course_type1').val(data[0]['value']);
            // $('#course_scene').val(data[0]['id']);
            var old_val=$('#course_type1').val();
            var new_val=data[0]['value'];
            if(new_val!==old_val){
                // $('#match_type1').val(data[0]['value']);
                // $('#match_scene').val(data[0]['id']);
                $('#course_type1').val(data[0]['value']);
                $('#course_scene').val(data[0]['id']);
                var post_data={
                    action:'get_match_cost',
                    type:data[0]['role_alias']
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
                            $.alerts("<?=__('获取比赛费用失败', 'nlyd-student')?>")
                　　　　 }
                    }
                })
            }
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
    //---------------------------开课时间------------------------------
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
        title: "<?=__('开课时间', 'nlyd-student')?>",
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
        title: "<?=__('结课日期', 'nlyd-student')?>",
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
            if(data.field['coach_phone']==""){
                $.alerts("<?=__('请确教练后再添加教练', 'nlyd-student')?>")
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
        $('#close_course').click(function(){//立即结课
            var course_id=$('#course_id').val();
            var _this=$(this);
            layer.open({
                type: 1
                ,maxWidth:300
                ,title: '<?=__("提示", "nlyd-student")?>' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper"><?=__("是否立即结课", "nlyd-student")?>？</div>'
                ,btn: [ '<?=__("按错了", "nlyd-student")?>','<?=__("提交", "nlyd-student")?>', ]
                ,success: function(layero, index){
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    layer.closeAll();
                    if(!_this.hasClass('disabled')){
                        $.ajax({
                            data: {id:course_id,action:'zone_close_course'},
                            beforeSend:function(XMLHttpRequest){
                                _this.addClass('disabled')
                            },
                            success: function(res, textStatus, jqXHR){
                                if(res.data.info){
                                    $.alerts(res.data.info)
                                }
                                if(res.success){
                                    if(res.data.url){
                                        setTimeout(function() {
                                            window.location.href=res.data.url
                                        }, 300);

                                    }else{
                                        _this.removeClass('disabled');
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
                        $.alerts("<?=__('正在处理您的请求..', 'nlyd-student')?>")
                    }
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
