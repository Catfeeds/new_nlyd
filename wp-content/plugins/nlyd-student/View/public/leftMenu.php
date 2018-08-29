<?php
/**
 * 公用PC菜单
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/17
 * Time: 11:22
 */
?>
<!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
<!--[if lt IE 9]>
  <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
  <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<div class="nl-left-menu layui-col-lg4 layui-col-md4 layui-show-lg-block layui-show-md-block layui-hide-sm layui-hide-xs">
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
            <a href="<?=home_url('/account/recentMatch');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-match">
                            </div>
                            
                        </div>
                        <div class="userCenter-detail-foot">我的比赛</div>
                    </a>
                    <a data-tips="1" href="<?=home_url('/account/matchList');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-train">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的训练</div>
                    </a>
                    <a data-tips="1" href="<?=home_url('/account/course');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-course">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的课程</div>
                    </a>
                    <a href="<?=home_url('/teams/myCoach');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-coach">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的教练</div>
                    </a>
                    <a data-tips="1" href="<?=home_url('orders');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-order">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的订单</div>
                    </a>
                    <a data-tips="1">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-kaoji">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的考级</div>
                    </a>
                    <a data-tips="1">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-tuiguang">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的推广</div>
                    </a>
                    <a data-tips="1" href="<?=home_url('/account/secure');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-secure">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">安全中心</div>
                    </a>
                    <a class="no_border" href="<?=home_url('/safety/setting');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-setting">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">其他设置</div>
                    </a>
                    <a data-tips="1" class="no_border" href="<?=home_url('/teams');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-wallet">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的钱包</div>
                    </a>
                </div>
        </div>
</div>