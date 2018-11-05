<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <div class="main-header">
                <header class="mui-bar mui-bar-nav main">
                    <a class="mui-pull-left nl-goback static" href="<?=home_url('supervisor/logs')?>">
                        <div><i class="iconfont">&#xe610;</i></div>
                    </a>
                    <h1 class="mui-title"><div><?=__('赛场信息记录', 'nlyd-student')?></div></h1>
                </header>
            </div> 
            <div class="layui-row nl-border nl-content">
                <form class="nl-page-form layui-form width-margin-pc have-bottom">   
                    <div class="form-inputs">
                        <div class="form-input-row">
                            <div class="form-input-label"><div><?=__('选择比赛', 'nlyd-student')?></div></div>
                            <input type="text" value="<?=$list['match_title']?>" lay-verify="required"  class="nl-input nl-foucs" readonly id="trigger1">
                            <input type="hidden" name="match_id" value="<?=$list['match_id']?> lay-verify="required"  class="nl-input nl-foucs" id="trigger2">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><div><?=__('选手座位号', 'nlyd-student')?></div></div>
                            <input type="text" name="seat_number" value="<?=$list['seat_number']?>" lay-verify="required"  class="nl-input nl-foucs">
                        </div>
                        <div class="form-input-row student_name" style="display: none">
                            <div class="form-input-label"><div><?=__('选手姓名', 'nlyd-student')?></div></div>
                            <input type="text" name="student_name" value="<?=$list['student_name']?>"  class="nl-input nl-foucs" disabled placeholder="<?=__('填写座位号自动获取姓名', 'nlyd-student')?>">
                        </div>
                        <!--<div class="form-input-row">
                            <div class="form-input-label"><div><?/*=__('比赛项目/场次', 'nlyd-student')*/?></div></div>
                            <input type="text" name="match" value="" lay-verify="required"  class="nl-input nl-foucs">
                        </div>-->
                               
                        <div class="layui-bg-white img-zoos img-zoos1">
                            <p class="tps"><?=__('拍照佐证（点击拍照或上传）', 'nlyd-student')?></p>
                            <?php if(!empty($list['evidence'])){
                                foreach ($list['evidence'] as $val){
                                    ?>
                                    <div class="post-img no-dash">
                                        <div class="img-zoo img-box">
                                            <img src="<?=$val?>"/>
                                        </div>
                                        <input type="hidden" name="evidence[]" value="<?=$val?>" />
                                        <div class="del">
                                            <i class="iconfont">&#xe633;</i>
                                        </div>
                                    </div>
                                <?php } }?>
                            <div class="post-img dash">
                                <div class="add-zoo" data-file="img-zoos1">
                                    <div class="transverse"></div>
                                    <div class="vertical"></div>
                                </div>
                            </div>
                        </div>        
                        <div class="form-input-row">
                            <div class="form-input-label"><div><?=__('备注说明', 'nlyd-student')?></div></div>
                            <input type="text" name="describe" value="<?=$list['describe']?>"  class="nl-input nl-foucs">
                        </div> 
                        <a class="a-btn a-btn-table" lay-filter="sub" lay-submit=""><div><?=__('提交', 'nlyd-student')?></div></a>
                    </div>
                    <input type="hidden" name="id" value="<?=$list['id']?>">
                </form>
            </div>
        </div>           
    </div>
</div>
<input style="display:none;" type="file" name="meta_val" id="img-zoos1" data-this="img-zoos1" value="" accept="image/*"/>
<script>
jQuery(document).ready(function($) {
        $("input[name=seat_number]").focusout(function(){//获取名字
            var value=$(this).val();
            var datas={
                seat_number:value,
                match_id: $("input[name='match_id']").val(),
                action:'get_student_name',
            }
            $.ajax({
                data: datas,
                success: function(data, textStatus, jqXHR){
                    if(data.success){
                        $('.student_name').show();
                        $("input[name='student_name']").val(data.data.info);
                    }else{
                        $.alerts(data.data.info);
                    }
                    return false;
                }
            });
        })
        //模拟手机下拉列表，选择比赛
            var SelectData= <?=$match_list?>;
        var posiotion=[0]
        if(typeof($('#trigger1').val())!='undefined'){
            if($('#trigger1').val().length>0){
                $.each(SelectData,function(index,value){
                    if(value.value==$('#trigger1').val()){
                        posiotion=[index]
                        return false;
                    }
                })
            }
        }
        var mobileSelect1 = new MobileSelect({
            trigger: '#trigger1',
            title: '<?=__('比赛', 'nlyd-student')?>',
            wheels: [
                {data: SelectData}
            ],
            position:posiotion, //初始化定位 打开时默认选中的哪个 如果不填默认为0
            transitionEnd:function(indexArr, data){
                // console.log(data);
            },
            callback:function(indexArr, data){
                $('#trigger1').val(data[0].value)
                $('#trigger2').val(data[0].id)
            }
        });
       sendloginAjax=function(formData){
            //type:确定回调函数
            //url:ajax地址
            //formData:ajax传递的参数
            $.ajax({
                data: formData,
                success: function(data, textStatus, jqXHR){
                    $.alerts(data.data.info)
                    if(data.success){
                        if(data.data.url){
                            window.location.href=data.data.url
                        }
                    }
                    return false;
                }
            });
        }
        $('.img-zoos').on('click','.add-zoo',function(){//上传图片
            var id=$(this).attr('data-file')
            $('#'+id).click()
        })
        // var imgs=[]
        var imgs1=[]
        $('.img-zoos').each(function(){
            var _this=$(this);
            if(_this.hasClass('img-zoos1')){//微信
                if(_this.find('.post-img.no-dash').length>=3){
                    _this.find('.post-img.dash').css('display','none')
                }
            }
            // else if(_this.hasClass('img-zoos0')){//身份证
            //     if(_this.find('.post-img.no-dash').length>=3){
            //         _this.find('.post-img.dash').css('display','none')
            //     }
            // }
        })

        function changes(e,_this,array) {
            var file=e.target.files[0];
            array.unshift(file)
            var reader = new FileReader();
            var src='';
            //读取File对象的数据
            reader.onload = function(evt){
                //data:img base64 编码数据显示
                var dom='<div class="post-img no-dash">'
                        +'<div class="img-zoo img-box">'
                            +'<img src="'+evt.target.result+'"/>'
                        +'</div>'
                        +'<div class="del">'
                            +'<i class="iconfont">&#xe633;</i>'
                        +'</div>'
                    +'</div>'
                var className=_this.attr('data-this')
                $('.'+className+' p').after(dom)
                layer.photos({//图片预览
                    photos: '.img-zoos',
                    anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                })
                // if(className=="img-zoos0"){
                //     if($('.'+className+' .post-img.no-dash').length>=3){
                //         $('.'+className+' .post-img.dash').css('display','none')
                //     }
                // }else 
                if(className=="img-zoos1"){
                    if($('.'+className+' .post-img.no-dash').length>=3){
                        $('.'+className+' .post-img.dash').css('display','none')
                    }
                }
            }
            reader.readAsDataURL(file);
            $(e.target).val('')
        }

        // $("#img-zoos0").change(function(e) {
        //     changes(e,$("#img-zoos0"),imgs)
        // });
        $("#img-zoos1").change(function(e) {
            changes(e,$("#img-zoos1"),imgs1)
        });
        $('.img-zoos').on('click','.del',function(){//删除图片
            var _this=$(this);
            var index =_this.parents('.post-img').index();
            _this.parents('.img-zoos').find('.post-img.dash').css('display','block');
            _this.parents('.post-img').remove()
            // if(_this.parents('.img-zoos').hasClass('img-zoos0')){
            //     imgs.splice(index, 1);
            // }else 
            if(_this.parents('.img-zoos').hasClass('img-zoos1')){
                imgs1.splice(index, 1);
            }
            layer.photos({//图片预览
                    photos: '.img-zoos',
                    anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                }) 
        })
        layui.use(['form'], function(){
            var form = layui.form
            form.render();
            // 自定义验证规则
            // form.verify($.validationLayui.allRules);
            // 监听提交
            form.on('submit(sub)', function(data){//实名认证提交
                var fd = new FormData();
                fd.append('student_name',data.field.student_name);
                fd.append('seat_number',data.field.seat_number);
                fd.append('match_id',data.field.match_id);
                fd.append('describe',data.field.describe);
                fd.append('action','upload_match_evidence');
                fd.append('id',data.field.id);
                // $.each(imgs, function (i, v) {
                //     fd.append('images[]',v);
                // })
                console.log(imgs1);
                // return false;
                $.each(imgs1, function (i, v) {
                    fd.append('evidence[]',v);
                })
                $('.post-img.no-dash input').each(function () {
                    var name=$(this).attr('name')
                    fd.append(name,$(this).val());
                })
                $.ajax({
                    data: fd,
                    contentType : false,
                    processData : false,
                    cache : false,
                    success: function(res, textStatus, jqXHR){
                        if(res.success){
                            if(res.data.info){
                                $.alerts(res.data.info)
                            }
                            if(res.data.url){
                                setTimeout(function() {
                                     window.location.href=res.data.url
                                }, 300);

                            }
                            return false;

                        }else{
                            $.alerts(res.data.info)
                        }

                    }
                })
                return false;
            });
            layer.photos({//图片预览
                photos: '.img-zoos',
                anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            }) 
        });
})

</script>