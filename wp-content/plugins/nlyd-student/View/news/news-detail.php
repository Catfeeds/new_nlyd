
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper have-footer">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <h1 class="mui-title"><?=__('新闻详情', 'nlyd-student')?></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-margin width-margin-pc news-wrapper layui-row">
                    <div class="news-title lauyi-row">
                        <div class="layui-row">
                            <?php if($next->ID){ ?>
                                <a class="news-prev" href="<?=home_url('student/account/news/?action=newsDetail&id='.$next->ID)?>"><?=__('上一篇', 'nlyd-student')?></a>
                            <?php }else{ ?>
                                <a class="news-prev" href="javascript:;"><?=__('无上篇', 'nlyd-student')?></a>
                            <?php }?>
                            <p class="news-name"><?=$row->post_title?></p>
                            <?php if($prev->ID){ ?>
                                <a class="news-next" href="<?=home_url('student/account/news/?action=newsDetail&id='.$prev->ID)?>"><?=__('下一篇', 'nlyd-student')?></a>
                            <?php }else{ ?>
                                <a class="news-next" href="javascript:;"><?=__('无下篇', 'nlyd-student')?></a>
                            <?php }?>
                        </div>
                        <div class="layui-row">
                            <div class="pull-left news-info">
                                <span class="news-build"><?=__('发布日期', 'nlyd-student')?>：<?=explode(' ',$row->post_date)[0]?></span>
                                <span class="news-scan"><?=__('浏览数量', 'nlyd-student')?>：<?=$readNum?></span>
                            </div>
                            <div class="pull-right news-share"><?=__('分享', 'nlyd-student')?></div>
                        </div>
                    </div>

                    <div class="new-content">
                        <?=$row->post_content?>
<!--                        <p class="news-content-p">近日“国际脑力水平等级测评”活动，全程线上操作、线上监考、线上自动评判，测试达标者由国际脑力运动委员会(IISC)统一颁发等级证书，并在“国际脑力运动”官网和微信平台公示。凸显了国际脑力运动测评体系的专业性、公正性、高效性和权威性，彻底告别了以往脑力运动行业无标准可依的局面。</p>-->
<!--                        <div class="img-box">-->
<!--                            <img src="--><?//=student_css_url.'image/homePage/course1.png'?><!--">-->
<!--                        </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>