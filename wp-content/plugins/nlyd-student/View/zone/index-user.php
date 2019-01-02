
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
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <a class="mui-pull-left nl-goback static" href="<?=home_url('/account/');?>">
                <div><i class="iconfont">&#xe610;</i></div>
            </a>
            <div class="layui-row nl-border nl-content">
                <div class="apply width-padding layui-row layui-bg-white width-padding-pc" style="margin-top:0">
                    <div class="img-box qr_code_img img-z">
                        <img src="<?=$referee_code?>">
                    </div>
                    <div class="ta_c" style="margin-bottom:12px;">
                        <?=__('点击查看我的推广码', 'nlyd-student')?>
                    </div>
                </div>
                <div class="width-padding layui-row width-padding-pc">
                    <div class="flex-h fs_12">
                        <div class="flex1">
                            <span><?=__('您的推荐人ID', 'nlyd-student')?>：</span>
                            <span class="c_blue"><?=$referee_id > 0 ? $referee_id+10000000 : '无'?></span>
                        </div>
                        <div class="flex1">
                            <span><?=__('推荐时间', 'nlyd-student')?>：</span>
                            <span class="c_blue"><?=!empty($referee_time) ? $referee_time  : '-'?></span>
                        </div>
                    </div>
                </div>
                <div class="apply width-padding layui-row layui-bg-white width-padding-pc">
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/profit/type/user');?>">
                        <div class="apply_list_line pull-left ">
                            <div class="zone_bg bg_money"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('收益管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_black3 mr_10"><?=__('今日收入', 'nlyd-student')?><?=$total_income > 0 ? $total_income : '0.00'?></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/recommend/');?>">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_share"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('我的推荐', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/settingCash/');?>">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_cash"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('提现设置', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                </div>
                <!-- 普通用户 (控制台)-->
                <div class="apply have_title width-padding layui-row layui-bg-white width-padding-pc">
                    <div class="bold ta_c c_black apply_title"><?=__('合作申请', 'nlyd-student')?></div>
                    <?php if(!empty($list)){?>
                        <?php foreach ($list as $v){
                            if($v['user_status'] == -1){
                                $title = '审核中';
                                $url = '';
                            }
                            elseif ($v['user_status'] == -2){
                                $title = '审核失败';
                                $url = home_url('/zone/apply/zone_id/'.$v['zone_id'].'/type_id/'.$v['id'].'/zone_type_alias/'.$v['zone_type_alias']);
                            }
                            else{
                                $title = '';
                                $url = home_url('/zone/apply/type_id/'.$v['id'].'/zone_type_alias/'.$v['zone_type_alias']);
                            }
                        ?>
                            <a class="apply_list c_black layui-row" href="<?= empty($url) ? 'javascript:void(0)' : $url ;?>">
                                <div class="apply_list_line pull-left <?=$v['zone_type_class']?> ml"><i class="iconfont fs_20">&#xe650;</i></div>
                                <div class="apply_list_line center">
                                    <?php $title1 = $v['zone_type_alias'] == 'match' ? "承办":'设立' ?>
                                    <?=__('申请'.$title1.$v['zone_type_name'], 'nlyd-student')?>
                                </div>
                                <div class="apply_list_line pull-right mr"><i class="iconfont fs_20">&#xe727;</i></div>
                                <?php if(!empty($title)):?>
                                <div class="apply_list_line pull-right c_orange mr_10"><?=__($title, 'nlyd-student')?></div>
                                <?php endif;?>
                            </a>
                        <?php } ?>
                    <?php } ?>
                    <!--<a class="apply_list c_black layui-row" href="<?/*=home_url('zone/introduce');*/?>">
                        <div class="apply_list_line pull-left c_yellow ml"><i class="iconfont fs_20">&#xe650;</i></div>
                        <div class="apply_list_line center"><?/*=__('申请设立脑力训练中心', 'nlyd-student')*/?></div>
                        <div class="apply_list_line pull-right mr"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_orange mr_10"></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?/*=home_url('zone/introduce');*/?>">
                        <div class="apply_list_line pull-left c_yellow ml"><i class="iconfont fs_20">&#xe650;</i></div>
                        <div class="apply_list_line center"><?/*=__('申请设立脑力水平测评中心', 'nlyd-student')*/?></div>
                        <div class="apply_list_line pull-right mr"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_orange mr_10"></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?/*=home_url('zone/introduce');*/?>">
                        <div class="apply_list_line pull-left c_yellow ml"><i class="iconfont fs_20">&#xe650;</i></div>
                        <div class="apply_list_line center"><?/*=__('申请承办赛事', 'nlyd-student')*/?></div>
                        <div class="apply_list_line pull-right mr"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_orange mr_10"></div>
                    </a>-->
                    <a class="apply_list c_black layui-row" href="<?=home_url('zone/introduce');?>">
                        <div class="apply_list_line pull-left c_yellow ml"><i class="iconfont fs_20">&#xe650;</i></div>
                        <div class="apply_list_line center"><?=__('赞助脑力比赛', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right mr"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_orange mr_10"></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('zone/introduce');?>">
                        <div class="apply_list_line pull-left c_red ml"><i class="iconfont fs_20">&#xe650;</i></div>
                        <div class="apply_list_line center"><?=__('申请代理赛事赞助', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right mr"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_orange mr_10"></div>
                    </a>
                </div>
               
        </div>            
    </div>
</div>

<script>
jQuery(function($) {
    layui.use(['layer'], function(){
        layer.photos({//图片预览
            photos: '.img-z',
            anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        })
    })
})
</script>
