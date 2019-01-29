<?php
/**
 * 公用PC菜单
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/17
 * Time: 11:22
 */
?>
<div class="nl-left-menu layui-col-lg4 layui-show-lg-block layui-hide-md layui-hide-sm layui-hide-xs">
    <div class="userCenter-info layui-row">
        <!-- 头像 -->
        <div class="userCenter-main isMobile layui-row">
            <img src="<?=$user_info['user_head'];?>" class="logoImg">
        </div>
        <!-- 用户名称 -->
        <div class="userCenter-name layui-row"><div class="userCenter-names"><?=$user_info['nickname']?></div><?=$user_info['user_type'] ? '<div class="userCenter-type layui-hide-md layui-hide-lg">'.$user_info['user_type'].'</div>':'';?></div>
    </div>
    <div class="width-margin layui-row menu-wrapper">
        <div class="userCenter-detail layui-row layui-bg-white width-margin-pc">
            <a class="c_black8"  href="<?=home_url('/account/course/');?>">
                <div class="userCenter-detail-head">
                    <div class="menuImg-wrapper my-course">
                    </div>
                </div>
                <div class="userCenter-detail-foot"><?=__('我的课程', 'nlyd-student')?></div>
            </a>
            <a class="c_black8"  href="<?=home_url('/trains/history_list/');?>">
                <div class="userCenter-detail-head">
                    <div class="menuImg-wrapper my-train">
                    </div>
                </div>
                <div class="userCenter-detail-foot"><?=__('训练记录', 'nlyd-student')?></div>
            </a>
            <a class="c_black8" href="<?=home_url('/account/recentMatch/type/2');?>">
                <div class="userCenter-detail-head">
                    <div class="menuImg-wrapper my-kaoji">
                    </div>
                </div>
                <div class="userCenter-detail-foot"><?=__('我的考级', 'nlyd-student')?></div>
            </a>
            <a class="c_black8" href="<?=home_url('/account/recentMatch');?>">
                <div class="userCenter-detail-head">
                    <div class="menuImg-wrapper my-match">
                    </div>

                </div>
                <div class="userCenter-detail-foot"><?=__('我的比赛', 'nlyd-student')?></div>
            </a>
            <a class="c_black8" href="<?=home_url('/teams/myCoach');?>">
                <div class="userCenter-detail-head">
                    <div class="menuImg-wrapper my-coach">
                    </div>
                </div>
                <div class="userCenter-detail-foot"><?=__('我的教练', 'nlyd-student')?></div>
            </a>
            <a class="c_black8" href="<?=home_url('/zone/indexUser');?>">
                <div class="userCenter-detail-head">
                    <div class="menuImg-wrapper my-tuiguang">
                    </div>
                </div>
                <div class="userCenter-detail-foot"><?=__('我的推广', 'nlyd-student')?></div>
            </a>
            <a class="c_black8 disabled_a"  href="<?=home_url('/teams/');?>">
                <div class="userCenter-detail-head">
                    <div class="menuImg-wrapper my-wallet">
                    </div>
                </div>
                <div class="userCenter-detail-foot"><?=__('我的钱包', 'nlyd-student')?></div>
            </a>
            <a class="c_black8 disabled_a"  href="<?=home_url('/orders/');?>">
                <div class="userCenter-detail-head">
                    <div class="menuImg-wrapper my-order">
                    </div>
                </div>
                <div class="userCenter-detail-foot"><?=__('我的订单', 'nlyd-student')?></div>
            </a>
            <a class="c_black8"  href="<?=home_url('/account/info/');?>">
                <div class="userCenter-detail-head">
                    <div class="menuImg-wrapper edit-info">
                    </div>
                </div>
                <div class="userCenter-detail-foot"><?=__('基本信息', 'nlyd-student')?></div>
            </a>
            <a class="c_black8"  href="<?=home_url('/account/secure');?>">
                <div class="userCenter-detail-head">
                    <div class="menuImg-wrapper my-secure">
                    </div>
                </div>
                <div class="userCenter-detail-foot"><?=__('安全中心', 'nlyd-student')?></div>
            </a>
            <a class="c_black8" href="<?=home_url('/safety/setting/');?>">
                <div class="userCenter-detail-head">
                    <div class="menuImg-wrapper my-setting">
                    </div>
                </div>
                <div class="userCenter-detail-foot"><?=__('其他设置', 'nlyd-student')?></div>
            </a>
        </div>
    </div>
</div>