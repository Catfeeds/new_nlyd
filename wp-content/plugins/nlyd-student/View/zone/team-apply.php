<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12  detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
                <div><i class="iconfont">&#xe610;</i></div>
            </a>
            <h1 class="mui-title">
            <div><?=__('战队申请管理', 'nlyd-student')?></div>
            </h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="teamApply_row width-padding width-padding-pc">
                    <div class="teamApply_row_info fs_14">
                        <span class="c_blue"><?=__('詹冬梅', 'nlyd-student')?></span>
                        <span><?=__('申请', 'nlyd-student')?></span>
                        <span class="c_blue"><?=__('加入', 'nlyd-student')?></span>
                        <span><?=__('战队', 'nlyd-student')?></span>
                    </div>
                    <div class="teamApply_row_btns fs_14 pull-right">
                        <a class="c_blue mr_10"><?=__('同意', 'nlyd-student')?></a>
                        <a class="c_black6"><?=__('拒绝', 'nlyd-student')?></a>
                    </div>
                </div>

                <div class="teamApply_row width-padding width-padding-pc">
                    <div class="teamApply_row_info fs_14">
                        <span class="c_blue"><?=__('詹冬梅', 'nlyd-student')?></span>
                        <span><?=__('申请', 'nlyd-student')?></span>
                        <span class="c_blue"><?=__('退出', 'nlyd-student')?></span>
                        <span><?=__('战队', 'nlyd-student')?></span>
                    </div>
                    <div class="teamApply_row_btns fs_14 pull-right">
                        <a class="c_blue mr_10"><?=__('同意', 'nlyd-student')?></a>
                        <a class="c_black6"><?=__('拒绝', 'nlyd-student')?></a>
                    </div>
                </div>
            </div>
        </div>           
    </div>
</div>

<script>
jQuery(function($) {     

})
</script>