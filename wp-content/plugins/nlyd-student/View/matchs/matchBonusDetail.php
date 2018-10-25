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
                        <div class="width-margin width-margin-pc c_black ti_28"><?=isset($row['user_real_name']) ? unserialize($row['user_real_name'])['real_name'] : ''?>(ID<?=$row['userID']?>)</div>
                    </div>
                </div>
                <div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc"><?=__('奖金状态', 'nlyd-student')?>：</div>
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
                        <div class="width-margin width-margin-pc c_black ti_28"><?=$row['team_name']?></div>
                    </div>
                </div>
                <div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc"><?=__('收款途径', 'nlyd-student')?><?=__('奖项与奖金', 'nlyd-student')?>：</div>
                    </div>
                    <div class="money_row">
                        <div class="width-margin width-margin-pc c_black ti_28"><?=$row['match_id'] == 56522 ? __('银行卡收款', 'nlyd-student') : __('二维码收款', 'nlyd-student')?></div>
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