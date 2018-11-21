<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
        require_once leo_student_public_view.'leftMenu.php';

        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('奖金明细', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content have-footer">
                <div class="width-margin width-margin-pc">
                    <div class="match-title c_black"><?=$row['post_title']?><br><?=$row['post_content']?></div>
                </div>
                <div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc"><?=__('获奖选手', 'nlyd-student')?>：</div>
                    </div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc c_black ti_28"><?=$row['real_name']?>(ID<?=$row['userID']?>)</div>
                    </div>
                </div>
                <div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc"><?=__('奖金状态', 'nlyd-student')?>：
                            <?php if($is_admin){ ?>
                                <span class="c_blue pull-right" id="updateSendStatus"><?=__('更改发放状态', 'nlyd-student')?></span>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc c_black ti_28">
                            <?=$row['is_send'] == 2 ? __('已发放', 'nlyd-student'):__('等待发放', 'nlyd-student')?>

                        </div>
                    </div>
                </div>
                <div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc"><?=__('所属战队', 'nlyd-student')?>：</div>
                    </div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc c_black ti_28"><?=$row['team'] ? $row['team'] : '未加入战队'?></div>
                    </div>
                </div>
                <div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc"><?=__('收款途径', 'nlyd-student')?>：
                            <?php if($qrCodeUrl != '' && $is_admin){ ?>
                            <span class="c_blue see_code pull-right"><?=__('查看二维码', 'nlyd-student')?></span>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc c_black ti_28">
                            <?php if($row['match_id'] == 56522 || $row['match_id'] == 56927){ ?>
                                <?=$row['match_id'] == 56522 || ($row['match_id'] == 56927 && in_array($row['user_id'],[389,165,726,740])) ? __('银行卡收款', 'nlyd-student') : __('二维码收款', 'nlyd-student')?>
                            <?php }else{ ?>
                                <?=$row['collect_name']?>
                            <?php } ?>

                        </div>
                    </div>
                </div>
                <div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc"><?=__('奖项与奖金', 'nlyd-student')?>：</div>
                    </div>
                    <?php foreach ($row['bonus_list'] as $bonus_list){ ?>
                        <div class="money_row">
                            <div class="width-margin width-margin-pc c_black ti_28"><?=__($bonus_list['bonus_name'],'nlyd-student')?><div class="pull-right money_right ">¥ <?=$bonus_list['bonus']?></div></div>
                        </div>
                    <?php } ?>
                    <!--                    <div class="money_row">-->
                    <!--                        <div class="width-margin width-margin-pc c_black ti_28">心算类总排名优秀选手<div class="pull-right money_right ">¥ 200.00</div></div>-->
                    <!--                    </div>-->
                    <!--                    <div class="money_row">-->
                    <!--                        <div class="width-margin width-margin-pc c_black ti_28">心算类成年组季军<div class="pull-right money_right">¥ 200.00</div></div>-->
                    <!--                    </div>-->
                    <!--                    <div class="money_row">-->
                    <!--                        <div class="width-margin width-margin-pc c_black ti_28">快眼扫描项目冠军<div class="pull-right money_right">¥ 200.00</div></div>-->
                    <!--                    </div>-->
                </div>
                <div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc"><?=__('奖金总额', 'nlyd-student')?>：<div class="pull-right money_right c_orange">¥ <?=$row['all_bonus']?></div></div>
                    </div>
                </div>
                <div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc"><?=__('扣税金额(20%)', 'nlyd-student')?>：<div class="pull-right money_right">¥ <?=$row['tax_all']?></div></div>
                    </div>
                </div>
                <div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc"><?=__('实发金额', 'nlyd-student')?>：<div class="pull-right money_right c_green">¥ <?=$row['tax_send_bonus']?></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
        layui.use('layer', function(){
            var layer = layui.layer;
        });
        $('.see_code').click(function(){
            var json={
                "title": "二维码", //相册标题
                "id": 123, //相册id
                "start": 0, //初始显示的图片序号，默认0
                "data": [   //相册包含的图片，数组格式
                    {
                        "alt": "二维码",
                        "pid": 666, //图片id
                        "src": "<?=$qrCodeUrl?>", //原图地址
                        "thumb": "<?=$qrCodeUrl?>" //缩略图地址
                    }
                ]
            }
            layer.photos({
                photos: json
                ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            });
        })
        $('#updateSendStatus').on('click',function () {
            var match_id = '<?=$row['match_id']?>';
            var user_id = '<?=$row['user_id']?>';
            $.ajax({
                url:window.ajax_url,
                data : {'match_id':match_id,'user_id':user_id,'action':'updateSendStatus'},
                type : 'post',
                dataType : 'json',
                success : function (response) {
                    $.alerts(response.data.info);
                    if(response['success']){
                        window.location.reload();
                    }
                },error : function () {
                    $.alerts('网络错误!');
                }
            });
        });
    })
</script>