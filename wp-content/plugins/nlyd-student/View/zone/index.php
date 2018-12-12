
<style>
@media screen and (max-width: 1199px){
    .layui-fluid>.layui-row>.layui-bg-white:first-child,#page{
        background-color:#f6f6f6!important;
    }
    #page{
        top:0;
    }
}
</style>
<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper have-footer">
            
            <div class="layui-row nl-border nl-content">
                <div class="zone_user width-padding layui-row layui-bg-white width-margin-pc">
                    <div class="img-box zone_user_img pull-left">
                        <img src="<?=$row['user_head']?>">
                    </div>    
                    <div class="zone_user_detail pull-left">
                        <span class="qr_code c_orange"><i class="iconfont fs_26">&#xe651;</i></span>
                        <div class="c_black">
                            <span class="bold">
                                <?=$row['user_real_name']?>
                                <?php if(!empty($row['legal_person'])):?>
                                    /<?=$row['zone_name']?>
                                <?php endif;?>
                            </span></div>
                        <div class="c_black">
                            <?=__('ID/编  号', 'nlyd-student')?>:<?=!empty($row['legal_person']) ? dispRepair($row['id'],4,0) : $row['user_ID']?>
                        </div>

                        <div class="c_black">
                            <span><?=__(!empty($row['legal_person'])?'管理员':'推荐人', 'nlyd-student')?>：<?=$row['referee_name']?></span>
                            <?php if($row['user_status'] == 1){ ?>
                            <a class="pull-right c_blue"><?=__('更多资料', 'nlyd-student')?></a>
                            <?php }
                            elseif ($row['user_status'] == -1){ ?>
                            <a class="pull-right c_blue"><?=__('修改', 'nlyd-student')?></a>
                            <span class="pull-right c_red mr_10"><?=__('资料审核中', 'nlyd-student')?></span>
                            <?php } ?>
                        </div>

                        <!--<div class="c_black"><span><?/*=__('管理员', 'nlyd-student')*/?>：王二</span><a class="pull-right c_blue"><?/*=__('更多资料', 'nlyd-student')*/?></a></div>
                        <div class="c_black">
                            <span><?/*=__('管理员', 'nlyd-student')*/?>：王二</span>
                            <a class="pull-right c_blue"><?/*=__('修改', 'nlyd-student')*/?></a>
                            <span class="pull-right c_red mr_10"><?/*=__('资料审核中', 'nlyd-student')*/?></span>
                        </div>-->
                    </div>
                </div>
                <div class="apply width-padding layui-row layui-bg-white width-margin-pc">
                    <a class="apply_list c_black layui-row" style="border-top:none">
                        <div class="apply_list_line pull-left ">
                            <div class="zone_bg bg_money"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('收益管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_share"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('下级推广', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_black3 mr_10"><?=__('今日收入', 'nlyd-student')?>0.00</div>
                    </a>
                </div>
                <?php if(empty($row['legal_person'])){ ?>
                <!-- 合作申请 (普通用户控制台，入口推广合作)-->
                <div class="apply width-padding layui-row layui-bg-white width-margin-pc">
                    <div class="bold ta_c c_black apply_title"><?=__('合作申请', 'nlyd-student')?></div>
                    <a class="apply_list c_black layui-row" href="<?=home_url('zone/introduce');?>">
                        <div class="apply_list_line pull-left c_blue ml"><i class="iconfont fs_20">&#xe650;</i></div>
                        <div class="apply_list_line center"><?=__('申请设立脑力训练中心', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right mr"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('zone/introduce');?>">
                        <div class="apply_list_line pull-left c_green ml"><i class="iconfont fs_20">&#xe650;</i></div>
                        <div class="apply_list_line center"><?=__('申请设立脑力水平测评中心', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right mr"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('zone/introduce');?>">
                        <div class="apply_list_line pull-left c_orange ml"><i class="iconfont fs_20">&#xe650;</i></div>
                        <div class="apply_list_line center"><?=__('申请承办赛事', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right mr"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('zone/introduce');?>">
                        <div class="apply_list_line pull-left c_yellow ml"><i class="iconfont fs_20">&#xe650;</i></div>
                        <div class="apply_list_line center"><?=__('赞助脑力比赛', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right mr"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('zone/introduce');?>">
                        <div class="apply_list_line pull-left c_red ml"><i class="iconfont fs_20">&#xe650;</i></div>
                        <div class="apply_list_line center"><?=__('申请代理赛事赞助', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right mr"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                </div>
                <?php } ?>
                <!-- (资料审核通过的控制台)-->
                <div class="apply width-padding layui-row layui-bg-white width-margin-pc">
                    <a class="apply_list c_black3 layui-row " style="border-top:none">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_course"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('课程管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_coach"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('教练管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row">
                        <div class="apply_list_line pull-left ">
                            <div class="zone_bg bg_student"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('学员管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_match"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('比赛管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row">
                        <div class="apply_list_line pull-left ">
                            <div class="zone_bg bg_level"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('考级管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                </div>
            </div>
        </div>            
    </div>
</div>

<script>
jQuery(function($) { 
})
</script>
