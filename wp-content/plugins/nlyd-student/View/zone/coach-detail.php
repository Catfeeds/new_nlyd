
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('教练详情', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="_relative">
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('教练姓名', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black coach_name"><?=$real_name?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('教练ID', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$coach_ID?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('教练职称', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=__('IISC高级教练', 'nlyd-student')?></div>   
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('教学类别', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$coach_skill?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('教练性别', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=__($user_gender, 'nlyd-student')?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('身份证号', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$real_ID?></div>
                    </div>
                    <div class="detail_table_row img-z">
                         <div class="detail_label"><?=__('证件照片', 'nlyd-student')?>：</div>
                         <?php if(!empty($user_ID_Card)){ ?>
                         <?php foreach ($user_ID_Card as $v){ ?>
                         <div class="detail_detail_img"><img src="<?=$v?>"></div>
                         <?php } ?>
                         <?php }else{ ?>
                         <div class="detail_detail_img"><img src="<?=student_css_url.'image/noInfo/noMatch1042@2x.png'?>"></div>
                         <div class="detail_detail_img"><img src="<?=student_css_url.'image/noInfo/noMatch1042@2x.png'?>"></div>
                         <?php } ?>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('联系方式', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$user_mobile?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('累计学员', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$total?></div>
                    </div> 
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('教练简介', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$coach_brief?></div>
                    </div>
                    <div class="coach_head_img img-z">
                        <div class="img-box">
                            <img src="<?=$work_photo?>">
                        </div>
                    </div>
                </div>
                <a class="a-btn a-btn-table coachClear"><div><?=__('解除关系', 'nlyd-student')?></div></a>
            </div>
        </div>           
    </div>
    <div id="coachList" style="display:none"></div>
</div>
<script>
jQuery(function($) { 
    var mobileSelect4 = new MobileSelect({
        trigger: '#coachList',
        title: "<?=__('教练列表', 'nlyd-student')?>",
        wheels: [
            {data: [{coach_id:'coach_id', real_name:'real_name'}]}
        ],
        triggerDisplayData:false,
        keyMap:{id:'coach_id', value:'real_name'},
        position:[0], //初始化定位 打开时默认选中的哪个 如果不填默认为0
        transitionEnd:function(indexArr, data){
            // console.log(data);
        },
        callback:function(indexArr, data){
            // console.log(data)
            var data={
                action:'zone_coach_relieve',
                coach_id:$.Request('coach_id'),
                new_coach_id:data[0]['coach_id']
            }
            $.ajax({
                data: data,
                success: function(res, textStatus, jqXHR){
                    console.log(res)
                    $.alerts(res.data.info)
                    if(res.success){
                        setTimeout(function() {
                            window.location.href=window.home_url+'/zone/coach/'
                        }, 1000);
                    }
                }
            })
        }
    });
    layui.use(['layer'], function(){
        layer.photos({//图片预览
            photos: '.img-z',
            anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        }) 
        $('.coachClear').click(function(){
            var _this=$(this);
            layer.open({
                type: 1
                ,maxWidth:300
                ,title: "<?=__('提示', 'nlyd-student')?>" //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: "<div class='box-conent-wrapper'><?=__('是否解除', 'nlyd-student')?>"+$('.coach_name').text()+"<?=__('的教练关系', 'nlyd-student')?>？</div>"
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
                            action:'zone_coach_relieve',
                            coach_id:$.Request('coach_id'),
                        }
                        $.ajax({
                            data: data,
                            beforeSend:function(XMLHttpRequest){
                                _this.addClass('disabled')
                            },
                            success: function(res, textStatus, jqXHR){
                                console.log(res)
                                if(res.success){
                                    if(res.data.info){
                                        $.alerts(res.data.info);
                                        setTimeout(function() {
                                            window.location.href=window.home_url+'/zone/coach/'
                                        }, 1000);
                                    }
                                    if(res.data.list){
                                        $.alerts("<?=__('当前教练下存在学员，请为学员绑定新的教练关系并进行解绑教练操作', 'nlyd-student')?>",3000)
                                        var arr=JSON.parse(res.data.list);
                                        mobileSelect4.updateWheel(0,arr);
                                        mobileSelect4.show();
                                        _this.removeClass('disabled');
                                    }
                                    
                                }else{
                                    $.alerts(res.data.info);
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
                        $.alerts("<?=__('正在删除此轮比赛，请稍后再试', 'nlyd-student')?>",1200)
                    }
                    layer.closeAll();
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
        })
    }) 
})
</script>
