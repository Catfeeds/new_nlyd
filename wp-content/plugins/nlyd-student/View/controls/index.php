
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
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/profit/');?>">
                        <div class="apply_list_line pull-left ">
                            <div class="zone_bg bg_money"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('收益管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_black3 mr_10"><?=__('今日收入', 'nlyd-student')?><?=$stream > 0 ? $stream : number_format($stream,2)?></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/recommend/');?>">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_share"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('我的推荐', 'nlyd-student')?></div>
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
