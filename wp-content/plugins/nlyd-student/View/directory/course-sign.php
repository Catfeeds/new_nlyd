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


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('课程报名信息确认', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="course_detail_row width-padding width-padding-pc">
                    <div class="detail_table_row"><span class="c_blue bold fs_16">课程信息</span></div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('课程名称：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14">高效记忆术·G预报班·成都郫县</div>
                    </div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('训练地点：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14">四川成都武侯区丰德万瑞中心A座25楼</div>
                    </div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('课程费用：', 'nlyd-student')?></div>
                        <div class="detail_detail c_blue fs_14">￥ 3000.00</div>
                    </div>
                </div>

                <div class="course_detail_row width-padding width-padding-pc">
                    <div class="detail_table_row"><span class="c_blue bold fs_16">学员信息</span></div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('选手姓名：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14">大大宝</div>
                    </div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('选手ID：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14">
                            19875
                            <div class="nl-match-rz img-box"><img src="<?=student_css_url.'image/confirm/rz.png'?>"></div>
                        </div>
                    </div>
                </div>
            </div>
            <a href="<?=home_url('/directory/courseSignSuccess');?>" class="a-btn a-btn-table"><div><?=__('确认支付3000.00', 'nlyd-student')?></div></a>
        </div>           
    </div>
</div>
<script>
jQuery(function($) { 
  
})
</script>
