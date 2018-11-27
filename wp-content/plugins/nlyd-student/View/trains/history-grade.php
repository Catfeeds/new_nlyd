
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
                <div><?=__('我的训练记录', 'nlyd-student')?></div>
                </h1>
            </header>

                  <?php if(empty($list)){ ?>
                    <div class="layui-row nl-border nl-content have-bottom">
                        <div class="width-padding width-padding-pc ta_c c_blue fs_12 his_tips"><?=__('温馨提示:训练记录最多保存100条', 'nlyd-student')?> <span class="c_black pull-right close fs_20">×</span></div>
                        <div class="width-padding width-padding-pc">
                            <div class="ta_c mt_10">训练达标：记忆<span class="c_blue">1</span>级 心算<span class="c_blue">2</span>级 速读<span class="c_blue">2</span>级</div>

                            <div class="ta_c mt_10"><?=__('今 天 2018/10/26', 'nlyd-student')?></div>
                            <a class="his_row"  href="">
                                <div class="bold c_black pull-left his_first"> 记忆考级训练</div>
                                <div class="pull-right his_thir"> 
                                    <span class="c_red">2级未达标</span>
                                    <span class="c_black6">15:18</span>
                                </div>
                                <div class="arrow_box"><img src="<?=student_css_url.'image/trains/arrow.png'?>"></div>
                            </a>
                            <div class="ta_c mt_10"><?=__('今 天 2018/10/26', 'nlyd-student')?></div>
                            <a class="his_row"  href="">
                                <div class="bold c_black pull-left his_first"> 记忆考级训练</div>
                                <div class="pull-right his_thir"> 
                                    <span class="c_green">2级已达标</span>
                                    <span class="c_black6">15:18</span>
                                </div>
                                <div class="arrow_box"><img src="<?=student_css_url.'image/trains/arrow.png'?>"></div>
                            </a>
                            <a class="a-btn" href="<?=home_url('trains')?>"><?=__('马上去训练', 'nlyd-student')?></a>
                        </div>
                    </div>               
                    <?php }else{ ?>
                        <div class="layui-row nl-border nl-content">
                            <div class="width-padding width-padding-pc">
                                <div class="no-info-page">
                                    <div class="no-info-img">
                                        <img src="<?=student_css_url.'image/noInfo/noTrain1045@3x.png'?>">
                                    </div>
                                    <p class="no-info-text"><?=__('您暂无训练记录', 'nlyd-student')?></p>
                                    <a class="a-btn" href="<?=home_url('trains')?>"><?=__('马上去训练', 'nlyd-student')?></a>
                                </div>
                            </div>
                        </div>
                    <?php }?>

        </div>
    </div>
</div>

<script>
jQuery(function($) { 
    $('body').on('click','.close',function(){
        $('.his_tips').hide()
    })
})
</script>