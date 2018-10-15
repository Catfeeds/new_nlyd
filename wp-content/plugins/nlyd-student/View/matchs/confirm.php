
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">

        <?php if(isset($_GET['match_id'])){ ?>
            <a class="mui-pull-left nl-goback static" href="<?=home_url('matchs/info/match_id/'.$_GET['match_id'])?>">
        <?php }else{ ?>
                <a class="mui-pull-left nl-goback">
        <?php } ?>

        <i class="iconfont">&#xe610;</i>
        </a>
        <h1 class="mui-title"><?=__('报名信息确认', 'nlyd-student')?></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <form id="pay-form" class="layui-form width-margin width-margin-pc" action="" >
                    <ul style="margin:0">
                        <li class="nl-match">
                            <div class="nl-match-header ">
                                <span class="fs_16 c_blue"><?=$match['post_title']?></span>
                                <p class="fs_12 c_black3" style="margin:0"><?=$match['post_content']?></p>
                            </div>
                            <div class="nl-match-body ">
                                <div class="nl-match-detail">
                                    <div class="nl-match-label"><?=__('开赛日期', 'nlyd-student')?>：</div>
                                    <div class="nl-match-info">
                                        <span class="c_black"><?=$match['match_start_time']?></span>
                                    </div>
                                </div>
                                <div class="nl-match-detail">
                                    <div class="nl-match-label"><?=__('开赛地点', 'nlyd-student')?>：</div>
                                    <!-- <span >开赛地点：</span> -->
                                    <div class="nl-match-info">
                                        <span class="c_black"><?=$match['match_address']?></span>
                                    </div>
                                </div>
                                <div class="nl-match-detail">
                                    <div class="nl-match-label"><?=__('报名费用', 'nlyd-student')?>：</div>
                                    <!-- <span >报名费用：</span> -->
                                    <div class="nl-match-info">
                                        <input class="c_black" type="text" readonly name="cost" value="<?=$match['match_cost']?>">
                                    <div>
                                </div>
                            </div>
                        </li>
                        <!-- 比赛项目 -->
                        <?php if(!empty($match_project)): ?>
                        <li class="nl-match">
                            <div class="nl-match-header ">
                                <span class="fs_16 c_blue"><?=__('参赛项目', 'nlyd-student')?></span>
                            </div>
                            <div class="nl-match-body ">
                                <?php foreach ($match_project as $k => $val ){ ?>
                                <div class="nl-match-detail">
                                    <div class="nl-match-label"><?=$val['parent_title']?>：</div>
                                    <!-- <span ><?=$val['parent_title']?>：</span> -->
                                    <div class="nl-match-info">
                                        <input type="hidden" name="project_id[]" value="<?=$k?>"/>
                                        <span class="c_black">
                                            <?php
                                                $str = '';
                                                foreach ($val['project'] as $v) {
                                                    $str .= $v['post_title'].'/';
                                                }
                                                echo rtrim($str,'/');
                                            ?>
                                            &nbsp;&nbsp;&nbsp;
                                        </span>
                                        <span class="nl-see-link"><?=__('主训教练', 'nlyd-student')?>：</span>
                                        <?php if(isset($val['major_coach']) && isset($val['coach_id'])){ ?>
                                        <input type="hidden" name="major_coach[]" value="<?=$val['coach_id']?>"/>
                                        <?=$val['major_coach']?>
                                        <?php }else{ ?>
                                            <a href="<?=home_url('/teams/myCoach/match_id/'.$_GET['match_id']).'/category_id/'.$k;?>" class="nl-see-link"><?=__('去设置', 'nlyd-student')?></a>
                                        <?php } ?>
                                        </div>
                                </div>
                                <?php } ?>
                            </div>
                        </li>
                        <?php endif;?>
                        <!-- 选手信息 -->
                        <li class="nl-match">
                            <div class="nl-match-header ">
                                <span class="fs_16 c_blue"><?=__('选手信息', 'nlyd-student')?></span>
                            </div>
                            <div class="nl-match-body ">
                                <div class="nl-match-detail rz">
                                    <div class="nl-match-label"><?=__('选手姓名', 'nlyd-student')?>：</div>
                                    <!-- <span >选手姓名：</span> -->
                                    <div class="nl-match-info">
                                        <?php if(!empty($player['real_name'])){?>
                                        <span class="c_black"><?=$player['real_name']?></span>
                                        <div class="nl-match-rz img-box"><img src="<?=student_css_url.'image/confirm/rz.png'?>"></div>
                                        <?php }else{?>
                                            <a href="<?=home_url('account/certification/match_id/'.$_GET['match_id'])?>" class="nl-see-link"><?=__('实名认证', 'nlyd-student')?></a>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="nl-match-detail">
                                    <div class="nl-match-label"><?=__('所属战队', 'nlyd-student')?>：</div>
                                    <!-- <span >所属战队：</span> -->
                                    <div class="nl-match-info">
                                        <span class="c_black">
                                            <?php if(!empty($player['team_id'])){ ?>
                                            <input type="hidden" name="team_id" value="<?=$player['team_id']?>"/>
                                            <?=$player['user_team']?>
                                            <?php }else{
                                                $url = home_url('teams/index');
                                                if(!empty($_GET['match_id'])) $url .= '/match_id/'.$_GET['match_id'];
                                            ?>
                                                <a href="<?=$url?>" class="nl-see-link"><?=__('加入战队', 'nlyd-student')?></a>
                                            <?php }?>
                                        </span>
                                    </div>
                                </div>
                                <div class="nl-match-detail">
                                    <div class="nl-match-label"><?=__('选手ID', 'nlyd-student')?>：</div>
                                    <!-- <span >选手ID：</span> -->
                                    <div class="nl-match-info">
                                        <span class="c_black"><?=$player['user_ID']?></span>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <!-- 邮寄地址 -->
                        <!-- <li class="nl-match">
                            <div class="nl-match-header ">
                                <span class="fs_16 c_blue">邮寄地址</span>
                                <a class="nl-match-people c_blue" href="<?=home_url('/account/address/match_id/'.$_GET['match_id'])?>">增加/修改</a>
                            </div>
                            <div class="nl-match-body ">
                                <?php if(!empty($address)){ ?>
                                <div class="nl-match-detail">
                                    <div class="nl-match-label">收货人姓名：</div>
                                    <div class="nl-match-info">
                                        <input class="c_black" readonly type="text" name="fullname" value="<?=$address['fullname']?>">
                                    </div>
                                </div>
                                <div class="nl-match-detail">
                                    <div class="nl-match-label">联系电话：</div>
                                    <div class="nl-match-info">
                                        <input class="c_black" readonly type="text" name="telephone" value="<?=$address['telephone']?>">
                                    </div>
                                </div>
                                <div class="nl-match-detail">
                                    <div class="nl-match-label">收货地址：</div>
                                    <div class="nl-match-info">
                                        <span class="c_black">
                                        <?=$address['user_address']?>
                                            <input type="hidden" name="address" value="<?=$address['user_address']?>">
                                        </span>
                                    </div>
                                </div>
                                <?php }else{ ?>
                                暂无地址
                                <?php } ?>
                            </div>
                        </li> -->
                    </ul>
                    <input type="hidden" name="action" value="entry_pay">
                    <input type="hidden" name="_wpnonce" id="payForm" value="<?=wp_create_nonce('student_go_pay_code_nonce');?>">
                    <input type="hidden" name="match_id" value="<?=$_GET['match_id']?>">

                        <?php if($orderStatus['status'] == 1){ ?>
                            <a class="a-btn" id="goPay" lay-filter="pay-formbtn" lay-submit=""><?=__('去支付', 'nlyd-student')?></a>
                        <?php }elseif($orderStatus['status'] == 2){ ?>
                            <a class="a-btn"><?=__('已报名', 'nlyd-student')?> </a>
                        <?php }elseif($orderStatus['status'] == 0){ ?>
                            <a class="a-btn" id="goPay" lay-filter="pay-formbtn" lay-submit=""><?=__('去支付', 'nlyd-student')?></a>
                        <?php } ?>


                </form>
            </div>
        </div>           
    </div>
</div>
<!-- 底部弹出框 -->
<div class="selectBottom">
    <div class="grayLayer cancel"></div>
    <div class="selectBox">
        <div class="selectOption pay" id="weiChat"><i class="iconfont">&#xe63e;</i><?=__('微信', 'nlyd-student')?></div>
        <div class="selectOption pay" id="zfb"><i class="iconfont">&#xe611;</i><?=__('支付宝', 'nlyd-student')?></div>
        <div class="selectOption pay" id="visa"><i class="iconfont">&#xe615;</i><?=__('银联支付', 'nlyd-student')?></div>
        <div class="selectOption cancel"><?=__('取消', 'nlyd-student')?></div>
    </div>
</div>
<input type="hidden" name="_wpnonce" id="inputPay" value="<?=wp_create_nonce('student_get_ranking_code_nonce');?>">
<script>
jQuery(function($) { 
    var serialnumber='';//订单号
    $('body').on('click','.layui-form-checkbox',function(){
        var _this=$(this);
        $('.layui-form-checkbox').each(function(){
            var __this=$(this);
            if(__this.hasClass('layui-form-checked')){
                __this.removeClass('layui-form-checked');
            }
        })
        _this.toggleClass('layui-form-checked');
    })
    var prams=''
    function jsApiCall()
    {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            prams,
            function(res){
                if(res.err_msg=='get_brand_wcpay_request:ok'){
                    window.location.href=window.home_url+'/matchs/info/match_id/'+$.Request('match_id')
                }
            }
        );
    }
	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
    layui.use(['form'], function(){
            var form = layui.form
            form.render();
            // 自定义验证规则
            form.verify($.validationLayui.allRules); 
            // 监听提交
            form.on('submit(pay-formbtn)', function(data){
                $.ajax({
                    data:data.field,success:function(res){
                        // console.log(res)
                        if(res.success){
                            //不需要支付
                            if(res.data.is_pay == 0){
                                window.location.href=res.data.url;
                                return false;
                            }
                            serialnumber=res.data.serialnumber;//获取订单号
                            var total=<?=$match['match_cost']?>;
                            if(total>0){
                                // $('.selectBottom').addClass('selectBottom-show')
                                var content='<div class="box-conent-wrapper"><?=__('本次共需支付', 'nlyd-student')?>￥'+total+'</div>'
                                            +'<div style="text-align:left;margin:auto;width:100px;" class="fs_14"><div id="weiChat" class="layui-unselect layui-form-checkbox layui-form-checked" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>&nbsp;&nbsp;&nbsp;&nbsp;<?=__('微信', 'nlyd-student')?></div>'
                                            +'<div style="text-align:left;margin:auto;width:100px;margin-top:10px" class="fs_14"><div id="zfb" class="layui-unselect layui-form-checkbox" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>&nbsp;&nbsp;&nbsp;&nbsp;<?=__('支付宝', 'nlyd-student')?></div>'
                                            //    +'<div style="text-align:left;margin:auto;width:100px;" class="fs_14 c_orange"><div id="visa" class="layui-unselect layui-form-checkbox" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>&nbsp;&nbsp;&nbsp;&nbsp;银联支付</div>'
                                layer.open({
                                    type: 1
                                    ,maxWidth:300
                                    ,title: '<?=__('选择支付方式', 'nlyd-student')?>' //不显示标题栏
                                    ,skin:'nl-box-skin'
                                    ,id: 'certification' //防止重复弹出
                                    ,content:content
                                    ,btn: ['<?=__('取消支付', 'nlyd-student')?>', '<?=__('确认支付', 'nlyd-student')?>' ]
                                    ,cancel:function(){

                                    }
                                    ,success: function(layero, index){
                                        
                                    }
                                    ,yes: function(index, layero){
                                        layer.closeAll();
                                    }
                                    ,btn2: function(index, layero){
                                        var id=$('.layui-form-checked').attr('id')
                                        var pay_type=''
                                        if(id=='weiChat'){//微信支付
                                            pay_type='wxh5pay'
                                        }else if(id=='zfb'){//支付宝支付
                                            pay_type='alipay'
                                        }else{
                                            pay_type=null;
                                        }

                                        var datas={
                                            action:'pay',
                                            pay_type:pay_type,
                                            _wpnonce:$('#inputPay').val(),
                                            serialnumber:serialnumber,
                                            match_id:$.Request('match_id')
                                        }
                                        // alert(pay_type)
                                        if(pay_type){
                                        
                                                $.ajax({
                                                    data:datas,success:function(response){
                                                        if(response.success){
                                                            if(response.data.info){
                                                                window.location.href=response.data.info;
                                                            }else{//微信公众号支付
                                                                if(response.data.params){
                                                                    prams=response.data.params;
                                                                    jsApiCall()
                                                                }
                                                            }
                                                            
                                                        }else{
                                                            $.alerts(response.data.info)
                                                        }
                                                    }
                                                })
                                        }
                                    }
                                    ,closeBtn:2
                                    ,btnAagn: 'c' //按钮居中
                                    ,shade: 0.3 //遮罩
                                    ,isOutAnim:true//关闭动画
                                });
                            }else{
                                window.location.href=window.home_url+'/matchs/info/match_id/'+$.Request('match_id')
                            }
                        }else{
                            // if(res.data.info=="请先实名认证"){
                            if(res.data.info=="<?=__('请先实名认证', 'nlyd-student')?>"){
                                setTimeout(function(){
                                    window.location.href=window.home_url+'/account/certification/match_id/'+$.Request('match_id');
                                }, 1000);
                            }else{
                                $.alerts(res.data.info)
                            }

                        }
                    }
                })
                return false;
            });
        })
        
    $('.selectBottom').on('click','.cancel',function(){
        $(this).parents('.selectBottom').removeClass('selectBottom-show');
    })

})
</script>