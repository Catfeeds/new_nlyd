<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('/zone/indexUser/');?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('关联账号设置', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="apply width-padding layui-row layui-bg-white width-padding-pc" style="margin-top:0">
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/settingCashCard');?>">
                        <div class="apply_list_line left_bg">
                            <div class="zone_bg bg_bankCard"></div>
                        </div>
                        <div class="apply_list_line center left_label"><?=__('提现银行卡', 'nlyd-student')?></div>
                        <div class="apply_list_line c_black3 right_tips">
                            <?php if(empty($user_cheques_bank)){
                                        echo '未设置';
                                    }else{
                                        $bank = unserialize($user_cheques_bank);
                                        echo $bank['open_bank'].' 尾号'.substr($bank['open_card_num'],-4,4).' 储蓄卡';
                                    }
                            ?>
                        </div>
                        <div class="apply_list_line right_icoin"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/settingCashWechat/');?>">
                        <div class="apply_list_line left_bg">
                            <div class="zone_bg bg_wechat_qr"></div>
                        </div>
                        <div class="apply_list_line center left_label"><?=__('收款二维码', 'nlyd-student')?></div>
                        <div class="apply_list_line c_black3 right_tips"><?=__(empty($user_coin_code) ? '未设置' : '已设置', 'nlyd-student')?></div>
                        <div class="apply_list_line right_icoin"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/settingCashAlipay/');?>">
                        <div class="apply_list_line left_bg">
                            <div class="zone_bg bg_alipay_qr"></div>
                        </div>
                        <div class="apply_list_line center left_label"><?=__('收款二维码', 'nlyd-student')?></div>
                        <div class="apply_list_line c_black3 right_tips"><?=__(empty($aliPay_coin_code) ? '未设置' : '已设置', 'nlyd-student')?></div>
                        <div class="apply_list_line right_icoin"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                </div>
            </div>            
        </div>
    </div>
</div>