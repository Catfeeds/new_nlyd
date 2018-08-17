
<style>
    @media screen and (max-width: 991px){
        #content,.detail-content-wrapper{
            background:#efeff4;
        }
    }
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
        <a class="mui-pull-left nl-goback static" href="<?=home_url('matchs/info/match_id/'.$_GET['match_id'])?>">
        <i class="iconfont">&#xe610;</i>
        </a>
        <h1 class="mui-title">报名信息确认</h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <form id="pay-form" class="layui-form" action="" >
                    <ul style="margin:0">
                        <li class="nl-match">
                            <div class="nl-match-header width-margin">
                                <span class="nl-match-name"><?=$match['post_title']?></span>
                                <p class="long-name" style="margin:0"><?=$match['post_content']?></p>
                            </div>
                            <div class="nl-match-body width-margin">
                                <div class="nl-match-detail">
                                    <span class="nl-match-label">开赛日期：</span>
                                    <span class="nl-match-info"><?=$match['match_start_time']?></span>
                                </div>
                                <div class="nl-match-detail">
                                    <span class="nl-match-label">开赛地点：</span>
                                    <span class="nl-match-info"><?=$match['match_address']?></span>
                                </div>
                                <div class="nl-match-detail">
                                    <span class="nl-match-label">报名费用：</span>
                                    <input class="confirm_info" type="text" readonly name="cost" value="<?=$match['match_cost']?>">
                                    <!-- <span class="nl-match-info">¥<?=$match['match_cost']?></span> -->
                                </div>
                            </div>
                        </li>
                        <!-- 比赛项目 -->
                        <?php if(!empty($match_project)): ?>
                        <li class="nl-match">
                            <div class="nl-match-header width-margin">
                                <span class="nl-match-name">参赛项目</span>
                            </div>
                            <div class="nl-match-body width-margin">
                                <?php foreach ($match_project as $k => $val ){ ?>
                                <div class="nl-match-detail">
                                    <span class="nl-match-label"><?=$val['parent_title']?>：</span>
                                    <input type="hidden" name="project_id[]" value="<?=$k?>"/>
                                    <span class="nl-match-info">
                                        <?php
                                            $str = '';
                                            foreach ($val['project'] as $v) {
                                                $str .= $v['post_title'].'/';
                                            }
                                            echo rtrim($str,'/');
                                        ?>
                                        &nbsp;&nbsp;&nbsp;
                                    </span>
                                    <span class="nl-see-link">主训教练：</span>
                                    <?php if(isset($val['major_coach']) && isset($val['coach_id'])){ ?>
                                    <input type="hidden" name="major_coach[]" value="<?=$val['coach_id']?>"/>
                                    <?=$val['major_coach']?>
                                    <?php }else{ ?>
                                        <a href="<?=home_url('/teams/myCoach/match_id/'.$_GET['match_id']).'/category_id/'.$k;?>" class="nl-see-link">去设置</a>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                            </div>
                        </li>
                        <?php endif;?>
                        <!-- 选手信息 -->
                        <li class="nl-match">
                            <div class="nl-match-header width-margin">
                                <span class="nl-match-name">选手信息</span>
                            </div>
                            <div class="nl-match-body width-margin">
                                <div class="nl-match-detail">
                                    <span class="nl-match-label">选手姓名：</span>
                                    <span class="nl-match-info"><?=$player['real_name']?></span>
                                    <span class="nl-match-rz">已认证</span>
                                </div>
                                <div class="nl-match-detail">
                                    <span class="nl-match-label">所属战队：</span>
                                    <span class="nl-match-info">
                                        <?php if(!empty($player['team_id'])){ ?>
                                        <input type="hidden" name="team_id" value="<?=$player['team_id']?>"/>
                                        <?=$player['user_team']?>
                                        <?php }else{ ?>
                                            <a href="<?=home_url('student/account/team/?action=lists')?>" class="nl-see-link">加入战队</a>
                                        <?php }?>
                                    </span>
                                </div>
                                <div class="nl-match-detail">
                                    <span class="nl-match-label">选手ID：</span>
                                    <span class="nl-match-info"><?=$player['user_ID']?></span>
                                </div>
                            </div>
                        </li>

                        <!-- 邮寄地址 -->
                        <li class="nl-match">
                            <!-- <div class="nl-match-metal">我</div> -->
                            <!-- <span class="nl-match-people">28报名</span> -->
                            <div class="nl-match-header width-margin">
                                <span class="nl-match-name">邮寄地址</span>
                                <a class="nl-match-people" href="<?=home_url('/account/info/?action=address&match_id='.$_GET['match_id'])?>">增加/修改</a>
                            </div>
                            <div class="nl-match-body width-margin">
                                <?php if(!empty($address)){ ?>
                                <div class="nl-match-detail">
                                    <span class="nl-match-label">收货人姓名：</span>
                                    <input class="confirm_info" readonly type="text" name="fullname" value="<?=$address['fullname']?>">
                                
                                </div>
                                <div class="nl-match-detail">
                                    <span class="nl-match-label">联系电话：</span>
                                    <input class="confirm_info" readonly type="text" name="telephone" value="<?=$address['telephone']?>">
                                    
                                </div>
                                <div class="nl-match-detail">
                                    <span class="nl-match-label">收货地址：</span>
                                    <span class="nl-match-info">
                                    <?=$address['user_address']?>
                                        <input type="hidden" name="address" value="<?=$address['user_address']?>">
                                    </span>
                                </div>
                                <?php }else{ ?>
                                暂无地址
                                <?php } ?>
                            </div>
                        </li>
                    </ul>
                    <input type="hidden" name="action" value="entry_pay">
                    <input type="hidden" name="_wpnonce" id="payForm" value="<?=wp_create_nonce('student_go_pay_code_nonce');?>">
                    <input type="hidden" name="match_id" value="<?=$_GET['match_id']?>">
                    <div class="a-btn" id="goPay" lay-filter="pay-formbtn" lay-submit="">去支付</div>
                </form>
            </div>
        </div>           
    </div>
</div>
<!-- 底部弹出框 -->
<div class="selectBottom">
    <div class="grayLayer cancel"></div>
    <div class="selectBox">
        <div class="selectOption pay" id="weiChat"><i class="iconfont">&#xe63e;</i>微信<input type="radio" name="pay" value="wx" class="payRadio"></div>
        <div class="selectOption pay" id="zfb"><i class="iconfont">&#xe611;</i>支付宝<input type="radio" name="pay" value="zfb" class="payRadio"></div>
        <div class="selectOption pay" id="visa"><i class="iconfont">&#xe615;</i>银联支付<input type="radio" name="pay" value="visa" class="payRadio"></div>
        <div class="selectOption cancel">取消</div>
    </div>
</div>
<input type="hidden" name="_wpnonce" id="inputPay" value="<?=wp_create_nonce('student_get_ranking_code_nonce');?>">
<script>
jQuery(function($) { 
    var serialnumber='';//订单号
    layui.use(['form'], function(){
            var form = layui.form
            form.render();
            // 自定义验证规则
            form.verify($.validationLayui.allRules); 
            // 监听提交
            form.on('submit(pay-formbtn)', function(data){
                $.post(window.admin_ajax,data.field,function(res){
                    if(res.success){
                        serialnumber=res.data.serialnumber;//获取订单号
                        $('.selectBottom').addClass('selectBottom-show')
                    }else{
                        $.alerts(res.data.info)
                    }
                })
                return false;
            });
        })
    $('.selectBottom').on('click','.cancel',function(){
        $(this).parents('.selectBottom').removeClass('selectBottom-show');
    })
    $('.pay').click(function(){
        var _this=$(this);
        var id=_this.attr('id')
        var pay_type=''
        if(id=='weiChat'){//微信支付
            pay_type='wxh5pay'
        }else if(id=='zfb'){//支付宝支付
            pay_type='alipay'
        }else{
            return false;
        }
        var data={
            action:'pay',
            pay_type:pay_type,
            _wpnonce:$('#inputPay').val(),
            serialnumber:serialnumber
        }
        $.post(window.admin_ajax,data,function(res){
            if(res.success){
                window.open(res.data.info); 
            }else{
                $.alerts(res.data.info)
            }
        })
    })

})
</script>