<!-- 赛区 -->
<div class="layui-row nl-border nl-content">
    <div class="width-padding layui-row width-margin-pc">
        <form class="layui-form apply_form" lay-filter='layform'>
            <?php if(!empty($row['id'])):?>
                <div>
                    <div class="lable_row"><span class="c_black"><?=__($zone_type_name.'编号', 'nlyd-student')?>：</span></div>
                    <div class="input_row"><input class="radius_input_row" disabled type="text" name="zone_num" value="<?=dispRepair($row['id'],4,0)?>"></div>
                </div>
            <?php endif;?>
            <div>
                <div class="lable_row"><span class="c_black"><?=__($zone_type_name.'类型', 'nlyd-student')?>：</span></div>
                <div class="input_row">
                    <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                    <input type="hidden" id="zone_match_type" name="zone_match_type" value="<?=$row['zone_match_type']?>">
                    <input
                            class="radius_input_row nl-foucs"
                            type="text"
                            readonly
                            id="zone_match_type_val"
                            lay-verify="required"
                            autocomplete="off"
                            placeholder="<?=__('请按照合同约定的赛区类型如实选择', 'nlyd-student')?>"
                            value="<?=$row['zone_match_type_cn']?>">
                </div>
            </div>
            <div>
                <div class="lable_row"><span class="c_black"><?=__('办赛地区', 'nlyd-student')?>：</span></div>
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
                            placeholder="<?=__('请按照合同约定的办赛区域如实选择', 'nlyd-student')?>"
                            value="">
                </div>
            </div>
            <div class="name_row dis_none">
                <div class="lable_row">
                    <span class="c_black"><?=__($zone_type_name.'字号', 'nlyd-student')?>：</span>
                    <span class="c_black3"><?=__('规则：IISC+“名字”+国际脑力训练中心+城市', 'nlyd-student')?></span>
                </div>
                <div class="input_row">
                    <input class="radius_input_row nl-foucs" type="text" name="zone_name" autocomplete="off" placeholder="<?=__('输入您的'.$zone_type_name.'名字', 'nlyd-student')?>" value="<?=!empty($row['id']) ? $row['zone_name'] :''?>">
                </div>
            </div>
            <div>
                <div class="lable_row"><span class="c_black"><?=__('营业地址', 'nlyd-student')?>：</span></div>
                <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('输入您的营业地址，与证件保持一致', 'nlyd-student')?>" value="<?=!empty($row) ? $row['zone_address'] :''?>"></div>
            </div>
            <div>
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
                <div class="lable_row"><span class="c_black"><?=__('对公账户开户名称', 'nlyd-student')?>：</span></div>
                <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="bank_card_name" lay-verify="required" autocomplete="off" placeholder="<?=__('输入对公账户开户名称', 'nlyd-student')?>" value="<?=!empty($row) ? $row['bank_card_name'] :''?>"></div>
            </div>
            <div>
                <div class="lable_row"><span class="c_black"><?=__('选择对公账户开户行', 'nlyd-student')?>：</span></div>
                <div class="input_row">
                    <span class="input_row_arrow"><i class="iconfont">&#xe656;</i></span>
                    <input class="radius_input_row nl-foucs" type="text" readonly id="opening_bank" name="opening_bank"  lay-verify="required" autocomplete="off" placeholder="<?=__('选择对公账户开户行', 'nlyd-student')?>" value="<?=!empty($row) ? $row['opening_bank'] :''?>">
                </div>
            </div>
            <div>
                <div class="lable_row"><span class="c_black"><?=__('对公账户号码', 'nlyd-student')?>：</span></div>
                <div class="input_row"><input class="radius_input_row nl-foucs" type="tel" name="bank_card_num" lay-verify="required" autocomplete="off" placeholder="<?=__('输入对公账户号码', 'nlyd-student')?>" value="<?=!empty($row) ? $row['bank_card_num'] :''?>"></div>
            </div>
            <div>
                <div class="lable_row"><span class="c_black"><?=__('开户详细地址', 'nlyd-student')?>：</span></div>
                <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="opening_bank_address" lay-verify="required" autocomplete="off" placeholder="<?=__('输入对公账户详细开户地址', 'nlyd-student')?>" value="<?=!empty($row) ? $row['opening_bank_address'] :''?>"></div>
            </div>
            <?php if($_GET['zone_type_alias'] == 'match' || !empty($row['chairman_name'])):?>
                <div>
                    <div class="lable_row">
                        <span class="c_black"><?=__('组委会主席', 'nlyd-student')?>：</span>
                        <span class="c_red fs_12"><?=__('任职人员需在平台注册并实名认证，否则审核无法通过', 'nlyd-student')?></span>
                    </div>
                    <div class="input_row">
                        <!-- <input class="get_id" name="chairman_id" type="hidden" value="<?=$row['chairman_id']?>"> -->
                        <input class="radius_input_row change_num nl-foucs" name="chairman_phone" value="<?=$row['chairman_phone']?>" type="tel" lay-verify="phone" autocomplete="off" placeholder="<?=__('输入用户注册手机号码查询，未注册无法选择', 'nlyd-student')?>">
                        <!-- <a class="input_row_arrow c_blue search_val"><?=__('确 认', 'nlyd-student')?></a> -->
                        <!-- <select class="js-data-select-ajax" name="chairman_id" style="width: 100%" data-action="get_manage_user" data-placeholder="<?=__('输入用户注册手机号码查询，未注册无法选择', 'nlyd-student')?>" >
                            <option value="<?=$row['chairman_id']?>" selected><?=$row['chairman_name']?></option>
                        </select> -->
                    </div>
                </div>
                <div>
                    <div class="lable_row">
                        <span class="c_black"><?=__('组委会秘书长', 'nlyd-student')?>：</span>
                        <span class="c_red fs_12"><?=__('任职人员需在平台注册并实名认证，否则审核无法通过', 'nlyd-student')?></span>
                    </div>
                    <div class="input_row">
                        <!-- <select class="js-data-select-ajax" name="secretary_id" style="width: 100%" data-action="get_manage_user" data-placeholder="<?=__('输入用户注册手机号码查询，未注册无法选择', 'nlyd-student')?>" >
                            <option value="<?=$row['secretary_id']?>" selected><?=$row['secretary_name']?></option>
                        </select> -->
                        <!-- <input class="get_id" name="secretary_id" type="hidden" value="<?=$row['secretary_id']?>"> -->
                        <input class="radius_input_row change_num nl-foucs" name="secretary_phone" value="<?=$row['secretary_phone']?>" type="tel" lay-verify="phone" autocomplete="off" placeholder="<?=__('输入用户注册手机号码查询，未注册无法选择', 'nlyd-student')?>">
                        <!-- <a class="input_row_arrow c_blue search_val"><?=__('确 认', 'nlyd-student')?></a> -->
                    </div>
                </div>
            <?php endif;?>
            <?php if(!empty($referee_name)):?>
                <div>
                    <div class="lable_row"><span class="c_black"><?=__($_GET['zone_type_alias']=='trains'?'事业管理员':'推荐人', 'nlyd-student')?>：</span></div>
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