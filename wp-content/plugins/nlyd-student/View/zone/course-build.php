
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
                            <div class="lable_row"><span class="c_black"><?=__('教学类型', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" type="text" name="zone_num" value="<?=dispRepair(!empty($row) ? $row['id'] : $zone_num,4,0)?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('教学类型', 'nlyd-student')?>：</span>
                            </div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" type="text" name="zone_name" lay-verify="required" autocomplete="off" placeholder="<?=__('输入您的分中心名字', 'nlyd-student')?>" value="<?=!empty($row) ? $row['zone_name'] :''?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程名称', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('输入您的营业地址，与证件保持一致', 'nlyd-student')?>" value="<?=!empty($row) ? $row['zone_address'] :''?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程时长', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="legal_person" lay-verify="required" autocomplete="off" placeholder="<?=__('法定代表人姓名', 'nlyd-student')?>" value="<?=!empty($row) ? $row['legal_person'] :''?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('授课教练', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <select class="js-data-select-ajax" name="secretary_id" style="width: 100%" data-action="get_manage_user"  data-placeholder="输入用户名/手机/邮箱/昵称" ></select>
                                <input class="get_id" name="secretary_id" style="display:none" value="<?=$row['secretary_id']?>">
                                <!-- <input class="radius_input_row change_ajax" name="secretary_id"  value="<?=$row['secretary_name']?>" type="text" lay-verify="required" autocomplete="off" placeholder="<?=__('选择组委会秘书', 'nlyd-student')?>"> -->
                                
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('课程费用', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" disabled type="text">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('开放名额', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" type="tel" name="opening_bank_address" lay-verify="required" autocomplete="off" placeholder="<?=__('输入对公账户详细开户地址', 'nlyd-student')?>" value="<?=!empty($row) ? $row['opening_bank_address'] :''?>">
                            </div>
                        </div>
                  
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('开课日期', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <input class="radius_input_row nl-foucs" readonly type="text" value="<?=$referee_name?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('开课日期', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                                <textarea class="radius_input_row nl-foucs" type="text"></textarea>
                            </div>
                        </div>
                       
                        
                        <a class="a-btn a-btn-table" lay-filter="layform" lay-submit=""><div><?=__('提交资料', 'nlyd-student')?></div></a>
                    </form>
                </div>
            </div>
        </div>            
    </div>
</div>
<input style="display:none;" type="file" name="meta_val" id="img-zoos0" data-this="img-zoos0" value="" accept="image/*"/>
<input style="display:none;" type="file" name="meta_val" id="img-zoos1" data-this="img-zoos1" value="" accept="image/*"/>
<script>
jQuery(function($) { 

        var opening_bank_Data=$.validationLayui.back;

        var posiotion_back=[0];//初始化位置，高亮展示
        if($('#opening_bank').val().length>0 && $('#opening_bank').val()){
            $.each(opening_bank_Data,function(index,value){
                if(value['value']==$('#opening_bank').val()){
                    posiotion_back=[index]
                    return false;
                }
            })
        }
        var mobileSelect4 = new MobileSelect({
            trigger: '#opening_bank',
            title: '<?=__('开户行', 'nlyd-student')?>',
            wheels: [
                {data: opening_bank_Data}
            ],
            position:posiotion_back, //初始化定位 打开时默认选中的哪个 如果不填默认为0
            transitionEnd:function(indexArr, data){
                // console.log(data);
            },
            callback:function(indexArr, data){
                $('#opening_bank').val(data[0]['value']);
               
            }
        });
        $('.img-zoos').on('click','.add-zoo',function(){//上传图片
            var id=$(this).attr('data-file')
            $('#'+id).click()
        })
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
    layui.use(['form'], function(){
        var form = layui.form
        form.render();
        // 自定义验证规则
        form.verify($.validationLayui.allRules);
        // 监听提交
        form.on('submit(layform)', function(data){//实名认证提交
            
            var fd = new FormData();
            fd.append('action','zone_apply_submit');
            fd.append('zone_num',data.field['zone_num']);
            fd.append('type_id',$.Request('type_id'));
            fd.append('zone_type_alias',$.Request('zone_type_alias'));
            fd.append('zone_name',data.field['zone_name']);
            fd.append('zone_address',data.field['zone_address']);
            fd.append('legal_person',data.field['legal_person']);
            fd.append('opening_bank',data.field['opening_bank']);
            fd.append('opening_bank_address',data.field['opening_bank_address']);
            fd.append('bank_card_num',data.field['bank_card_num']);
            fd.append('chairman_id',data.field['chairman_id']);
            fd.append('secretary_id',data.field['secretary_id']);
            fd.append('business_licence',imgs1[0]);
            console.log(data.field)
            $.ajax({
                data: fd,
                contentType : false,
                processData : false,
                cache : false,
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
