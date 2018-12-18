
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
                    <div class="team_row width-padding layui-row layui-bg-white width-margin-pc" style="margin-top: 0;">
                        <div class="team_row_title">
                            <span class="bold fs_16 c_black">2019脑力世界杯江西（赣州）战队 </span>
                            <a class="fs_12 c_blue" href="<?=home_url('/zone/teamBuild/');?>"><?=__('编辑资料', 'nlyd-student')?></a>
                        </div>
                        <div class="team_row_tag"><span class="c_yellow mr_10"><i class="iconfont">&#xe658;</i></span><?=__('璀璨赣州 智慧闪耀', 'nlyd-student')?></div>
                        <div class="c_black fs_14 ti_28 mt_10">
                            <?=__('益贝教育是一家致力于提升孩子学习动力和学习能力的专业 训练机构。通过体验式和交互式的教学，全面提升孩子的专注力、 记忆力、想象力、创造力、自信心等学习能力，同时增强孩子的感 恩心、责任心等，让孩子学习更轻松，让亲子关系更和谐！', 'nlyd-student')?>
                        </div>
                    </div>

                    <div class="team_row width-padding layui-row layui-bg-white width-margin-pc">
                        <div class="team_row_title">
                            <span>
                                <span class="bold fs_16 c_black"><?=__('战队成员', 'nlyd-student')?>（1）</span>
                                <span class="c_orange fs_12"><?=__('新的申请', 'nlyd-student')?>（1）</span>
                            </span>
                            <a class="fs_12 c_blue" href="<?=home_url('/zone/teamAddMember/');?>"><?=__('添加成员', 'nlyd-student')?></a>
                        </div>
                    </div>
                    <div class="layui-row layui-bg-white">
                        <div class="nl-table-wapper">
                            <table class="nl-table">
                                <tbody>
                                    <tr class='table-head'>
                                        <td><?=__('序 号', 'nlyd-student')?></td>
                                        <td><?=__('姓名/编号', 'nlyd-student')?></td>
                                        <td><?=__('年 龄', 'nlyd-student')?></td>
                                        <td><?=__('性 别', 'nlyd-student')?></td>
                                        <td><?=__('联系方式', 'nlyd-student')?></td>
                                        <td><?=__('操 作', 'nlyd-student')?></td>
                                    </tr>
                                    <tr>
                                        <td><div class="table_content">1</div></td>
                                        <td><div class="table_content"><span class="c_black">王好学</span><br><span class="ff_num fs_12">10000888</span></div></td>
                                        <td><div class="table_content">18</div></td>
                                        <td><div class="table_content c_black">男</div></td>
                                        <td><div class="table_content c_black">刘亿亿</div></td>
                                        <td><div class="table_content"><a class="c_blue" href="<?=home_url('/zone/studentDetail/');?>">详 情</a></div></td>
                                    </tr>
                                    <tr>
                                        <td><div class="table_content">1</div></td>
                                        <td><div class="table_content"><span class="c_black">王好学</span><br><span class="ff_num fs_12">10000888</span></div></td>
                                        <td><div class="table_content c_black">18</div></td>
                                        <td><div class="table_content c_black">男</div></td>
                                        <td><div class="table_content">刘亿亿</div></td>
                                        <td><div class="table_content"><a class="c_blue" href="<?=home_url('/zone/studentDetail/');?>">详 情</a></div></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>    
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