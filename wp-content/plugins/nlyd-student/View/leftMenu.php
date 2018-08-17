<!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
<!--[if lt IE 9]>
  <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
  <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<div class="nl-left-menu layui-col-lg4 layui-col-md4 layui-show-lg-block layui-show-md-block layui-hide-sm layui-hide-xs">
        <div class="userCenter-info layui-row">
            <div class='bubble bubble1 layui-hide-md layui-hide-lg'></div>
            <div class='bubble bubble2 layui-hide-md layui-hide-lg'></div>
            <div class='bubble bubble3 layui-hide-md layui-hide-lg'></div>
            <div class='bubble bubble4 layui-hide-md layui-hide-lg'></div>
            <div class='bubble bubble5 layui-hide-md layui-hide-lg'></div>
            <?php global $user_info,$wpdb;
            $message_total = $wpdb->get_row("select if(count(id)>0,count(id),0) total from {$wpdb->prefix}messages where user_id = {$user_info['user_id']} and read_status = 1 ")->total;
            ?>
            <!-- 消息 -->
            <a href="<?=home_url('student/account/messages')?>" class="userCenter-message layui-hide-md layui-hide-lg"><i class="iconfont">&#xe60d;</i>&nbsp;&nbsp;消息<?=$message_total > 0 ? '<span class="layui-badge-dot"></span>' : '';?></a>
            <!-- 编辑 -->
            <a href="<?=home_url('student/account/info')?>" class="userCenter-edit layui-hide-md layui-hide-lg"><i class="iconfont">&#xe600;</i>&nbsp;&nbsp;编辑资料</a>
            <!-- 级别 -->
            <div class="userCenter-level width-left layui-row layui-bg-white layui-hide-md layui-hide-lg">
                <div class="userCenter-level-item">
                    <p><?=empty($my_skill['reading'])?0:$my_skill['reading']?>级</p>
                    <p>速读级别</p>
                </div>
                <div class="userCenter-level-item">
                    <p><?=empty($my_skill['memory'])?0:$my_skill['memory']?>级</p>
                    <p>速记级别</p>
                </div>
                <div class="userCenter-level-item">
                    <p><?=empty($my_skill['compute'])?0:$my_skill['compute']?>级</p>
                    <p>速算级别</p>
                </div>
            </div>
            <!-- 头像 -->
            <div class="userCenter-main isMobile layui-row">
                <img src="<?=$user_info['user_head'];?>" class="logoImg">
            </div>
            <!-- 用户名称 -->
            <div class="userCenter-name layui-row"><div class="userCenter-names"><?=$user_info['nickname']?></div><?=$user_info['user_type'] ? '<div class="userCenter-type layui-hide-md layui-hide-lg">'.$user_info['user_type'].'</div>':'';?></div>
            <!-- 用户标签 -->
            <div class="userCenter-describe layui-row layui-hide-md layui-hide-lg">
            <span class="userCenter-item">ID<?=isset($user_info['user_ID']) ? ':'.$user_info['user_ID'] : '';?></span>
                    <span class="userCenter-item"><?= !empty($user_info['user_gender']) ? $user_info['user_gender'] : '性别';?></span>
                    <span class="userCenter-item"><?= !empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_age'] : '年龄';?></span>
                    <span class="userCenter-item"><?= !empty($user_info['user_address']) ? $user_info['user_address']['city'].$user_info['user_address']['area'] : '所在地';?></span>
            </div>
            <!-- 战队信息 -->
            <div class="userCenter-operations layui-hide-md layui-hide-lg">
                <?php if(!empty($my_team['mental'])): ?>
                    <i class="iconfont">&#xe64a;</i> <?=$my_team['mental']?>
                <?php endif;?>
            </div>
        </div>
        <div class="width-margin layui-row menu-wrapper">
            <?php if(!empty($my_team['my_team'])):?>
            <!-- 我的战队 -->
                <a class="userCenter-row layui-row layui-bg-white layui-hide-md layui-hide-lg" href="<?=home_url('student/account/team/?action=teamDetail&team_id='.$my_team['ID'])?>"><span class="pull-left"><?=$my_team['my_team']?></span><span class="pull-right">查看</span></a>
            <?php endif;?>
            <!-- 我的钱包 -->
            <a class="userCenter-row layui-row layui-bg-white layui-hide-md layui-hide-lg" href="<?=home_url('student/account/wallet')?>">
                <span class="pull-left">我的余额：<i class="iconfont">&#xe61e;</i>3200.00</span>
                <span class="pull-right">我的脑币：21</span>
            </a>
            <div class="userCenter-detail layui-row layui-bg-white width-margin-pc">
                    <a href="<?=home_url('/account/info');?>" class="layui-show-lg-block layui-show-md-block layui-hide-sm layui-hide-xs">
                        <div class="userCenter-detail-head">
                            <!-- <i class="iconfont">&#xe60f;</i> -->
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/match-big.png'?>" class="menuImg">
                            </div>
                            
                        </div>
                        <div class="userCenter-detail-foot">个人资料</div>
                    </a>
                    <a href="<?=home_url('/account/messages');?>" class="layui-show-lg-block layui-show-md-block layui-hide-sm layui-hide-xs">
                        <div class="userCenter-detail-head">
                            <!-- <i class="iconfont">&#xe60f;</i> -->
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/match-big.png'?>" class="menuImg">
                            </div>
                            
                        </div>
                        <div class="userCenter-detail-foot">消 息</div>
                    </a>
                    <a href="<?=home_url('/account/match/?action=recentMatch');?>">
                        <div class="userCenter-detail-head">
                            <!-- <i class="iconfont">&#xe60f;</i> -->
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/match-big.png'?>" class="menuImg">
                            </div>
                            
                        </div>
                        <div class="userCenter-detail-foot">比 赛</div>
                    </a>
                    <a>
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/train-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">训 练</div>
                    </a>
                    <a>
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
                    <a>
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
                    <a href="<?=home_url('/account/teams');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/setting-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">战 队</div>
                    </a>
                    <a>
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