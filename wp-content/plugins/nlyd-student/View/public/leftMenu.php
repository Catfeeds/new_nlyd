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
                    <a href="<?=home_url('/account/info');?>" class="layui-show-lg-block layui-show-md-block layui-hide-sm layui-hide-xs">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/match-big.png'?>" class="menuImg">
                            </div>
                            
                        </div>
                        <div class="userCenter-detail-foot">个人资料</div>
                    </a>
                    <a href="<?=home_url('/account/messages');?>" class="layui-show-lg-block layui-show-md-block layui-hide-sm layui-hide-xs">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/match-big.png'?>" class="menuImg">
                            </div>
                            
                        </div>
                        <div class="userCenter-detail-foot">消 息</div>
                    </a>
                    <a href="<?=home_url('/account/recentMatch');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/match-big.png'?>" class="menuImg">
                            </div>
                            
                        </div>
                        <div class="userCenter-detail-foot">比 赛</div>
                    </a>
                    <a href="<?=home_url('/account/train');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/train-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">训 练</div>
                    </a>
                    <a href="<?=home_url('/account/course');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/course-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">课 程</div>
                    </a>
                    <a href="<?=home_url('/teams/myCoach');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/coach-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">教 练</div>
                    </a>
                    <a href="<?=home_url('orders');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/order-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">订 单</div>
                    </a>
                    <a>
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/kaoji-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">考 级</div>
                    </a>
                    <a>
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/tuiguang-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">推 广</div>
                    </a>
                    <a>
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/zice-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">自 测</div>
                    </a>
                    <a href="<?=home_url('/safety/setting');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/setting-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">设 置</div>
                    </a>
                    <a href="<?=home_url('/teams');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/setting-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">战 队</div>
                    </a>
                    <a href="<?=home_url('/account/secure');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/secure-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">安全中心</div>
                    </a>
                </div>
        </div>
</div>