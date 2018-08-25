
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';

        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <?php
            if(isset($_GET['match_id']))
                echo '<a class="mui-pull-left nl-goback static" href="'.home_url('matchs/confirm/match_id/'.$_GET['match_id']).'">';
            else
                echo '<a class="mui-pull-left nl-goback static" href="'.home_url('account/info/').'">';
          ?>
        <i class="iconfont">&#xe610;</i>
        </a>
        <h1 class="mui-title">收货地址管理</h1>
        </header>
            <div class="layui-row nl-border nl-content">

                    <?php if(!empty($lists)){
                        foreach ($lists as $val){
                    ?>
                    <div class="address-row width-margin width-margin-pc"
                    <?php if(isset($_GET['match_id'])){?>
                        href="<?=home_url('/matchs/confirm/match_id/'.$_GET['match_id'].'/address_id/'.$val['id']);?>"
                    <?php } ?>
                    >
                        <div class="address-left">
                            <div class="address-title">
                                <span class="accept-name"><?=$val['fullname']?></span>
                                <span class="phone-number ff_num"><?=$val['telephone']?></span>
                                <span data-id="<?=$val['id']?>" class="default-address <?=$val['is_default'] != 1 ? '':'set-address';?>"><?=$val['is_default'] != 1 ? '设为默认':'默认地址'?></span>
                            </div>
                            <p class="address-detail"><?=$val['user_address']?></p>
                        </div>
                        <div  class="address-right">
                            <a class="address-btn bg_gradient_blue c_white" href="<?=home_url('/account/addAddress/address_id/'.$val['id']);?>">修改</a>
                            <div class="address-btn del bg_gradient_grey c_white" data-id="<?=$val['id']?>">删除</div>
                        </div>
                    </div>
                    <?php  } }else{ ?>
                        <p class="no-info">您未设置收货地址</p>
                    <?php } ?>
                <?php
                $add_url = home_url('/account/addAddress');
                if(isset($_GET['match_id'])) $add_url .= '/match_id/'.$_GET['match_id'];
                ?>
                <a class="a-btn" href="<?=$add_url;?>">新增收货地址</a>
            </div>
        </div>
    </div>
</div>
<!-- 删除地址 -->
<input type="hidden" name="_wpnonce" id="delAddress" value="<?=wp_create_nonce('student_remove_address_code_nonce');?>">
<!-- 设置默认地址 -->
<input type="hidden" name="_wpnonce" id="defaultAddress" value="<?=wp_create_nonce('student_set_default_code_nonce');?>">