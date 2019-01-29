<style>
@media screen and (max-width: 1199px){
    #page {
        top: 130px;
    }
    #content,.detail-content-wrapper{
        background:#f6f6f6;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav system-list system-match">
                <a class="mui-pull-left nl-goback">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <div class="item-wrapper">
                    <div class="center-detail">
                        <div class="system-font">
                            <p><?=__('赛区授权信息', 'nlyd-student')?></p>
                            <p>AUTHORITY</p> 
                        </div>
                    </div>
                </div>  
            </header>
            <?php foreach ($rows as $row){ ?>
            <div class="layui-row nl-border nl-content">
                <div class="dir_zone_list width-padding width-padding-pc">
                    <div class="dir_table_name"><?=$row['zone_title_name']?></div>
                    <div class="dir_tab_wap">
                        <div class="dir_tab_row">
                            <div class="dir_tab_label"><?=__('编 号', 'nlyd-student')?>：</div>
                            <div class="dir_tab_info"><?=$row['zone_number']?></div>
                        </div>
                        <div class="dir_tab_row">
                            <div class="dir_tab_label"><?=__('承办单位', 'nlyd-student')?>：</div>
                            <div class="dir_tab_info"><?=$row['bank_card_name']?></div>
                        </div>
                        <div class="dir_tab_row">
                            <div class="dir_tab_label"><?=__('主 席', 'nlyd-student')?>：</div>
                            <div class="dir_tab_info"><?=$row['chairman_name']?></div>
                        </div>
                        <div class="dir_tab_row">
                            <div class="dir_tab_label"><?=__('秘书长', 'nlyd-student')?>：</div>
                            <div class="dir_tab_info"><?=$row['secretary_name']?></div>
                        </div>
                    </div>
                </div>
            <?php } ?>

<!---->
<!--                <div class="dir_zone_list width-padding width-padding-pc">-->
<!--                    <div class="dir_table_name">2019脑力世界杯<span class="c_blue">成都</span>城市赛</div>-->
<!--                    <div class="dir_tab_wap">-->
<!--                        <div class="dir_tab_row">-->
<!--                            <div class="dir_tab_label">--><?//=__('编 号', 'nlyd-student')?><!--：</div>-->
<!--                            <div class="dir_tab_info">111</div>-->
<!--                        </div>-->
<!--                        <div class="dir_tab_row">-->
<!--                            <div class="dir_tab_label">--><?//=__('承办单位', 'nlyd-student')?><!--：</div>-->
<!--                            <div class="dir_tab_info">士大夫</div>-->
<!--                        </div>-->
<!--                        <div class="dir_tab_row">-->
<!--                            <div class="dir_tab_label">--><?//=__('主 席', 'nlyd-student')?><!--：</div>-->
<!--                            <div class="dir_tab_info">风格的风格</div>-->
<!--                        </div>-->
<!--                        <div class="dir_tab_row">-->
<!--                            <div class="dir_tab_label">--><?//=__('秘书长', 'nlyd-student')?><!--：</div>-->
<!--                            <div class="dir_tab_info">士大夫</div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!---->
<!--                <div class="dir_zone_list width-padding width-padding-pc">-->
<!--                    <div class="dir_table_name">2019脑力世界杯<span class="c_blue">成都</span>城市赛</div>-->
<!--                    <div class="dir_tab_wap">-->
<!--                        <div class="dir_tab_row">-->
<!--                            <div class="dir_tab_label">--><?//=__('编 号', 'nlyd-student')?><!--：</div>-->
<!--                            <div class="dir_tab_info">111</div>-->
<!--                        </div>-->
<!--                        <div class="dir_tab_row">-->
<!--                            <div class="dir_tab_label">--><?//=__('承办单位', 'nlyd-student')?><!--：</div>-->
<!--                            <div class="dir_tab_info">士大夫</div>-->
<!--                        </div>-->
<!--                        <div class="dir_tab_row">-->
<!--                            <div class="dir_tab_label">--><?//=__('主 席', 'nlyd-student')?><!--：</div>-->
<!--                            <div class="dir_tab_info">风格的风格</div>-->
<!--                        </div>-->
<!--                        <div class="dir_tab_row">-->
<!--                            <div class="dir_tab_label">--><?//=__('秘书长', 'nlyd-student')?><!--：</div>-->
<!--                            <div class="dir_tab_info">士大夫</div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
        </div>
    </div>
</div>
