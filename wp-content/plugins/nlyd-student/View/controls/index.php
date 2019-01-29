
<style>
@media screen and (max-width: 1199px){
    .layui-fluid>.layui-row>.layui-bg-white:first-child,#page{
        background-color:#f6f6f6!important;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper have-bottom no-header">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback nl-goback static" href="<?=home_url('/account/');?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('董事长控制台', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content">
               
                <div class="apply width-padding layui-row layui-bg-white width-padding-pc" style="margin-top:0">
                    <a class="apply_list c_black layui-row">
                        <div class="apply_list_line pull-left ">
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
                        <div class="apply_list_line pull-left">
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
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_team"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('战队管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_level"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('考级管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_money"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('分配设置', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <a class="apply_list c_black layui-row">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_data"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('数据统计', 'nlyd-student')?></div>
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
