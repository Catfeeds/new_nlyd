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
                <a class="mui-pull-left nl-goback nl-goback static" href="<?=home_url('/courses/cenerCourse/id/'.$_GET['center_id']);?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('课程详情', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="course_detail_row width-padding width-padding-pc">
                    <div class="detail_table_row"><span class="c_black bold fs_16"><?=$course_title?>·<?=$city?></span></div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('训练中心：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14"><?=$zone_title?></div>
                    </div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('开课时间：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14"><?=$course_start_time?></div>
                    </div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('授课教练：', 'nlyd-student')?></div>
                        <div class="detail_detail c_blue fs_14"><?=$user_name?></div>
                    </div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('抢占名额：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14"><span class="c_blue"><?=$order_total?></span>/<?=$open_quota?></div>
                    </div>
                </div>

                <div class="course_detail_row width-padding width-padding-pc">
                    <div class="detail_table_row"><span class="c_black bold fs_16"><?=__('课程介绍', 'nlyd-student')?></span></div>
                    <div class="detail_table_row">
                        <span class="c_black6 fs_14">
                            <!--上课时间：周六15:00-17:00，共计32课时。
                            <br>
                            第一次上课时间：2018-04-21 15:00-17:00。
                            <br>
                            点此阅读“乐学乐分享大型奖励活动”介绍。
                            <br>
                            报名成功以后，请自行购买教辅资料，并根据公布的上课时间 做好准备。-->
                            <?=!empty($course_details) ? $course_details : '暂无';?>
                        </span>
                    </div>
                </div>

                <div class="course_detail_row width-padding width-padding-pc">
                    <div class="detail_table_row"><span class="c_black bold fs_16"><?=__('配套教材', 'nlyd-student')?></span></div>
                    <a class="detail_table_row">
                        <div class="course_detail_img img-box img-z">
                            <img src="<?=student_css_url.'image/course/course_pic.png'?>">
                        </div>
                        <div class="course_img_info">
                            <div class="c_black bold fs_16"><?=__('乐学乐分享学员专享教材', 'nlyd-student')?></div>
                            <div class="c_black6 fs_14"><?=__('乐学乐分享”教辅资料（仅限“乐学乐分享”学员购买）。', 'nlyd-student')?></div>
                            <div class="c_blue fs_14">¥ 100.00</div>
                        </div>
                    </a>
                </div>
            </div>
            <?php if(empty($is_entered)):?>
            <a href="<?=home_url('/courses/courseSign/center_id'.$_GET['center_id'].'/id/'.$_GET['id']);?>" class="a-btn a-btn-table"><div><?=__('抢占名额', 'nlyd-student')?></div></a>
            <?php endif;?>
        </div>           
    </div>
</div>
