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
                <h1 class="mui-title"><div><?=__('报名成功', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="course_detail_row width-padding width-padding-pc">
                    <div class="success">
                        <div class="apply-success bold ta_c c_blue fs_16"><div class="nl-badge mr_10"><i class="iconfont">&#xe608;</i></div><span><?=__('报名成功', 'nlyd-student')?></span></div>
                        <div class="c_black ta_l fs_16 c_black6"><?=__('您已成功报名“乐学乐分享公益课程"', 'nlyd-student')?></div>
                        <a href="<?=home_url('/directory/cenerCourse');?>" class="a-btn-course a-btn-border c_black dis_table"><div class="dis_cell"><?=__('返回课程列表', 'nlyd-student')?></div></a>
                    </div>
                </div>
                <div class="course_detail_row width-padding width-padding-pc">
                    <div class="detail_table_row"><span class="c_black bold fs_16">配套教材</span></div>
                    <div class="detail_table_row">
                        <div class="course_detail_img img-box img-z">
                            <img src="<?=student_css_url.'image/course/course_pic.png'?>">
                        </div>
                        <div class="course_img_info">
                            <div class="c_black bold fs_16">乐学乐分享学员专享教材</div>
                            <div class="c_black6 fs_14">乐学乐分享”教辅资料（仅限“乐学乐分享”学员购买）。</div>
                            <div class="c_blue fs_14">¥ 100.00</div>
                        </div>
                    </div>
                    <a href="" class="a-btn-course bg_gradient_blue c_white dis_table"><div class="dis_cell"><?=__('购买配套教材', 'nlyd-student')?></div></a>
                </div>
            </div>
            
        </div>           
    </div>
</div>
<script>
jQuery(function($) { 
  
})
</script>
