
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper have-footer">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <h1 class="mui-title">行业新闻</h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-margin width-margin-pc news-wrapper layui-row flow-default">
                    <?php foreach ($rows as $row){ ?>
                        <a class="nl-ad-row layui-bg-white" href="<?=home_url('/student/account/news/?action=newsDetail&id='.$row->ID);?>">
                            <div class="layui-row foot-info">
                                <div class="layui-col-lg5 layui-col-md5 layui-col-sm5 layui-col-xs5 img-box">
                                    <img src="<?=wp_get_attachment_image_src(get_post_thumbnail_id($row->ID), 'thumbnail')[0]?>">
                                </div>
                                <div class="layui-col-lg7 layui-col-md7 layui-col-sm7 layui-col-xs7">
                                    <p class="nl-ad-name"><?=$row->post_title?></p>
                                    <p class="nl-ad-detail"><?=strip_tags($row->post_content)?></p>
                                </div>
                            </div>
                        </a>
                    <?php } ?>

<!--                    <a class="nl-ad-row layui-bg-white" href="--><?//=home_url('/student/account/news/?action=newsDetail');?><!--">-->
<!--                        <div class="layui-row foot-info">-->
<!--                            <div class="layui-col-lg5 layui-col-md5 layui-col-sm5 layui-col-xs5 img-box">-->
<!--                                <img src="--><?//=student_css_url.'image/homePage/course1.png'?><!--">-->
<!--                            </div>-->
<!--                            <div class="layui-col-lg7 layui-col-md7 layui-col-sm7 layui-col-xs7">-->
<!--                                <p class="nl-ad-name">脑力运动测评体系让脑力运脑力运动测评体系让脑力运</p>-->
<!--                                <p class="nl-ad-detail">近日，由脑力中国授权并经国际脑力运动委员会（IISC）备案的全国几十家脑力运动专业机构纷纷开展“国际脑力水平等级测近日，由脑力中国授权并经国际脑力运动委员会（IISC）备案的全国几十家脑力运动专业机构纷纷开展“国际脑力水平等级测</p>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </a>-->
<!--                    <a class="nl-ad-row layui-bg-white" href="--><?//=home_url('/student/account/news/?action=newsDetail');?><!--">-->
<!--                        <div class="layui-row foot-info">-->
<!--                            <div class="layui-col-lg5 layui-col-md5 layui-col-sm5 layui-col-xs5 img-box">-->
<!--                                <img src="--><?//=student_css_url.'image/homePage/course1.png'?><!--">-->
<!--                            </div>-->
<!--                            <div class="layui-col-lg7 layui-col-md7 layui-col-sm7 layui-col-xs7">-->
<!--                                <p class="nl-ad-name">脑力运动测评体系让脑力运脑力运动测评体系让脑力运</p>-->
<!--                                <p class="nl-ad-detail">近日，由脑力中国授权并经国际脑力运动委员会（IISC）备案的全国几十家脑力运动专业机构纷纷开展“国际脑力水平等级测近日，由脑力中国授权并经国际脑力运动委员会（IISC）备案的全国几十家脑力运动专业机构纷纷开展“国际脑力水平等级测</p>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </a>-->
<!--                    <a class="nl-ad-row layui-bg-white" href="--><?//=home_url('/student/account/news/?action=newsDetail');?><!--">-->
<!--                        <div class="layui-row foot-info">-->
<!--                            <div class="layui-col-lg5 layui-col-md5 layui-col-sm5 layui-col-xs5 img-box">-->
<!--                                <img src="--><?//=student_css_url.'image/homePage/course1.png'?><!--">-->
<!--                            </div>-->
<!--                            <div class="layui-col-lg7 layui-col-md7 layui-col-sm7 layui-col-xs7">-->
<!--                                <p class="nl-ad-name">脑力运动测评体系让脑力运脑力运动测评体系让脑力运</p>-->
<!--                                <p class="nl-ad-detail">近日，由脑力中国授权并经国际脑力运动委员会（IISC）备案的全国几十家脑力运动专业机构纷纷开展“国际脑力水平等级测近日，由脑力中国授权并经国际脑力运动委员会（IISC）备案的全国几十家脑力运动专业机构纷纷开展“国际脑力水平等级测</p>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </a>-->
                </div>
            </div>
        </div>
    </div>
</div>
