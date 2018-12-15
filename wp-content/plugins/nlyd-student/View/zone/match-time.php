
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
                <h1 class="mui-title"><div><?=__('赛程时间表', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content  have-bottom">
                <div class="width-padding layui-row width-margin-pc">
                    <div class="c_red fs_12 mt_10"><?=__('每轮比赛间隔3分钟,每个项目间隔10分钟，管理员可根据实际情况进行修改。', 'nlyd-student')?></div>
                    <div class="match_time_row">
                        <div class="match_xiang_name">
                            <span class="bold fs_16 c_black mr_10">数字争霸</span>
                            <span class="bold fs_12 mr_10">20分钟/每轮</span>
                            <a class="add_lun c_black6">
                                <div class="add_coin bg_gradient_blue">+</div>
                                <div class="add_text fs_12"><?=__('新增1轮', 'nlyd-student')?></div>
                            </a>
                        </div>

                        <div class="add_lun_row">
                            <span class="close_coin bg_gradient_orange mr_10">+</span>
                            <span class="mr_10">第1轮</span>
                            <span class="mr_10 c_black">2018/12/12 09:30</span>
                            <span class="mr_10 c_black">至</span>
                            <span class="mr_10 c_black">2018/12/12 17:50</span>
                            <a class="c_blue">修改</a>
                        </div>
                        <div class="add_lun_row">
                            <span class="close_coin bg_gradient_orange mr_10">+</span>
                            <span class="mr_10">第1轮</span>
                            <span class="mr_10 c_black">2018/12/12 09:30</span>
                            <span class="mr_10 c_black">至</span>
                            <span class="mr_10 c_black">2018/12/12 17:50</span>
                            <a class="c_blue">修改</a>
                        </div>
                    </div>
                </div>
            </div>
            <a class="a-btn a-btn-table"><div><?=__('保存更新', 'nlyd-student')?></div></a>
        </div>            
    </div>
</div>
<script>
jQuery(function($) { 

})
</script>
