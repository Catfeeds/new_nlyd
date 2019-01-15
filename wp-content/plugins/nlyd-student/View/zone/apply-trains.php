<!-- 训练中心(分中心) -->
<div class="layui-row nl-border nl-content have-bottom">
    <div class="width-padding layui-row width-margin-pc">
        <form class="layui-form apply_form" lay-filter='layform'>
            <!-- <div>
                <div class="lable_row">
                    <span class="c_black"><?=__('训练中心名字', 'nlyd-student')?>：</span>
                    <span class="c_black3"><?=__('示例：', 'nlyd-student')?>
                        IISC<span class="c_green">明德</span><?=__('国际脑力训练中心', 'nlyd-student')?>
                                </span>
                </div>
                <div class="input_row">
                    <input class="radius_input_row nl-foucs" type="text" name="zone_name" autocomplete="off" placeholder="<?=__('绿色示例部分即为字号，最多5字', 'nlyd-student')?>" value="<?=!empty($row['id']) ? $row['zone_name'] :''?>">
                </div>
            </div> -->
            <div>
                <div class="lable_row"><span class="c_black"><?=__('训练中心所在地', 'nlyd-student')?>：</span></div>
                <div class="input_row">
                    <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                    <input
                            class="radius_input_row nl-foucs"
                            type="text"
                            readonly
                            id="areaSelect"
                            name="zone_match_address"
                            lay-verify="required"
                            autocomplete="off"
                            placeholder="<?=__('请按照合同约定的区域如实选择', 'nlyd-student')?>"
                            value="">
                </div>
            </div>
            <div>
                <div class="lable_row"><span class="c_black"><?=__('训练中心营业地址', 'nlyd-student')?>：</span></div>
                <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('输入您的营业地址，与证件保持一致', 'nlyd-student')?>" value="<?=!empty($row) ? $row['zone_address'] :''?>"></div>
            </div>
            <div>
                <div class="lable_row"><span class="c_black"><?=__('上传营业执照', 'nlyd-student')?>：</span></div>
                <div class="input_row img-zoos img-zoos1">
                    <?php if((!empty($row['business_licence_url']))){?>
                        <input type="hidden" name="business_licence_url" class="business_licence_url" value="<?=$row['business_licence_url']?>">
                        <div class="post-img no-dash">
                            <div class="img-zoo img-box">
                                <img src="<?=$row['business_licence_url']?>"/>
                            </div>
                            <div class="del">
                                <i class="iconfont">&#xe633;</i>
                            </div>
                        </div>

                    <?php }?>
                    <div class="post-img dash">
                        <div class="add-zoo" data-file="img-zoos1">
                            <div class="transverse"></div>
                            <div class="vertical"></div>
                        </div>
                    </div>
                    <span class="fs_12 c_black3 _tips"><?=__('原件影印件或盖有鲜章的复印件', 'nlyd-student')?></span>
                </div>
            </div>
            <div>
                <div class="lable_row"><span class="c_black"><?=__('法定代表人', 'nlyd-student')?>：</span></div>
                <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="legal_person" lay-verify="required" autocomplete="off" placeholder="<?=__('法定代表人姓名', 'nlyd-student')?>" value="<?=!empty($row) ? $row['legal_person'] :''?>"></div>
            </div>
            <div>
                <div class="lable_row"><span class="c_black"><?=__('对公账户开户行', 'nlyd-student')?>：</span></div>
                <div class="input_row">
                    <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                    <input class="radius_input_row nl-foucs" type="text" readonly id="opening_bank" name="opening_bank"  lay-verify="required" autocomplete="off" placeholder="<?=__('选择对公账户开户行', 'nlyd-student')?>" value="<?=!empty($row) ? $row['opening_bank'] :''?>">
                </div>
            </div>
            <div>
                <div class="lable_row"><span class="c_black"><?=__('对公账户开户详细地址', 'nlyd-student')?>：</span></div>
                <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="opening_bank_address" lay-verify="required" autocomplete="off" placeholder="<?=__('输入对公账户详细开户地址', 'nlyd-student')?>" value="<?=!empty($row) ? $row['opening_bank_address'] :''?>"></div>
            </div>
            <div>
                <div class="lable_row"><span class="c_black"><?=__('对公账户开户名称', 'nlyd-student')?>：</span></div>
                <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="bank_card_name" lay-verify="required" autocomplete="off" placeholder="<?=__('输入对公账户开户名称', 'nlyd-student')?>" value="<?=!empty($row) ? $row['bank_card_name'] :''?>"></div>
            </div>
            <div>
                <div class="lable_row"><span class="c_black"><?=__('对公账户开户号码', 'nlyd-student')?>：</span></div>
                <div class="input_row"><input class="radius_input_row nl-foucs" type="tel" name="bank_card_num" lay-verify="required" autocomplete="off" placeholder="<?=__('输入对公账户号码', 'nlyd-student')?>" value="<?=!empty($row) ? $row['bank_card_num'] :''?>"></div>
            </div>

            <div>
                <div class="lable_row">
                    <span class="c_black"><?=__('分中心总经理', 'nlyd-student')?>：</span>
                    <span class="c_red fs_12"><?=__('任职人员需在平台注册并实名认证，否则审核无法通过', 'nlyd-student')?></span>
                </div>
                <div class="input_row">
                    <input class="radius_input_row change_num nl-foucs" name="center_manager" value="<?=$row['center_manager']?>" type="tel" lay-verify="phone" autocomplete="off" placeholder="<?=__('输入用户注册手机号码查询，未注册无法选择', 'nlyd-student')?>">
                </div>
            </div>
            <?php if(!empty($referee_name)):?>
            <div>
                <div class="lable_row"><span class="c_black"><?=__('事业管理员', 'nlyd-student')?>：</span></div>
                <div class="input_row">
                    <input class="radius_input_row" disabled type="text" value="<?=$referee_name?>">
                </div>
            </div>
            <?php endif;?>
            <?php if($row['user_status'] != 1):?>
                <a class="a-btn a-btn-table" lay-filter="layform" lay-submit=""><div><?=__('提交资料', 'nlyd-student')?></div></a>
            <?php endif;?>
        </form>
    </div>
</div>
