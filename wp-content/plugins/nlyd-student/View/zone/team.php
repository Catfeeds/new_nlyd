
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
            <div><?=__('战队管理', 'nlyd-student')?></div>
            </h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <?php if(!$row){?>
                    <style>
                        @media screen and (max-width: 1199px){
                            #page{
                                background-color:#f6f6f6!important;
                            }
                        }
                    </style>
                    <div class="team_row width-padding layui-row layui-bg-white width-margin-pc">
                        <div class="team_row_title">
                            <span class="bold fs_16 c_black">2019脑力世界杯江西（赣州）战队 </span>
                            <a class="fs_12 c_blue" href="<?=home_url('/zone/teamBuild/');?>"><?=__('编辑资料', 'nlyd-student')?></a>
                        </div>
                        <div class="team_row_tag"><span class="c_yellow mr_10"><i class="iconfont">&#xe658;</i></span><?=__('璀璨赣州 智慧闪耀', 'nlyd-student')?></div>
                    </div>
                <?php }else{ ?>
                    <div class="no-info-page layui-row">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noTeam1094@2x.png'?>">
                        </div>
                        <p class="no-info-text"><?=__('您还没有战队', 'nlyd-student')?></p>
                        <a href="<?=home_url('/zone/teamBuild/');?>" class="a-btn a-btn-table"><div><?=__('创建战队', 'nlyd-student')?></div></a>
                    </div>
                <?php } ?>
            </div>
        </div>           
    </div>
</div>

<script>
jQuery(function($) {     

})
</script>