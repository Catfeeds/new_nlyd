
<div class="layui-fluid nl-content">
    <div class="layui-row">
        <div class="layui-row mt_10">
            <div class="layui-row dir_nav_content active">
                <?php foreach ($rows as $row){ ?>
                    <div class="dir_zone_table">
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
            </div>
        </div>
    </div>
</div>
