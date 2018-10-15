
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <form class="nl-page-form layui-form" lay-filter='addAdress'>
                <input type="hidden" name="match_id" value="<?=$_GET['match_id']?>"/>
                <header class="mui-bar mui-bar-nav">
                    <?php
                        $url = home_url('account/address');
                        if(!empty($_GET['match_id'])) $url .= '/match_id/'.$_GET['match_id'];
                    ?>
                    <a class="mui-pull-left nl-goback static" onclick="window.location.href = '<?=$url?>'">
                        <i class="iconfont">&#xe610;</i>
                    </a>
                    <h1 class="mui-title"><?=__('收件地址管理', 'nlyd-student')?></h1>
                </header>
                <div class="layui-row nl-border nl-content">
                    <div class="form-inputs">
                        <div class="form-input-row">
                            <div class="form-input-label"><?=__('姓名', 'nlyd-student')?></div>
                            <input name='fullname' value='<?=$row['fullname']?>' type="text" placeholder="<?=__('请填写联系人姓名', 'nlyd-student')?>" class="nl-input nl-foucs" lay-verify="chineseName">
                            <input name='action' value='save_address' type="hidden">
                            <input type="hidden" name="_wpnonce"  value="<?=wp_create_nonce('student_save_address_code_nonce');?>">
                            <input type="hidden" name="id"  value="<?php echo isset($row['id']) ? $row['id'] : 0; ?>">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><?=__('国家', 'nlyd-student')?></div>
                            <input type="text" readonly name="country" autocomplete="off" value="<?=empty($row['country'])?'中国':$row['country']?>" class="nl-input">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><?=__('电话号码', 'nlyd-student')?></div>
                            <input type="tel" name="telephone" lay-verify="phone" placeholder="<?=__('电话号码', 'nlyd-student')?>" autocomplete="off" class="nl-input nl-foucs" value="<?=$row['telephone']?>">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><?=__('所在地', 'nlyd-student')?></div>
                            <input readonly id="areaSelect" type="text" placeholder="<?=__('所在地', 'nlyd-student')?>" class="nl-input" lay-verify="required" value="<?=$row['province'].$row['city'].$row['area']?>" >
                            <input name='province' type="hidden" value="<?=$row['province']?>">
                            <input name='city' type="hidden" value="<?=$row['city']?>">
                            <input name='area' type="hidden" value="<?=$row['area']?>">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><?=__('详细地址', 'nlyd-student')?></div>
                            <input type="text" name="address" lay-verify="required" placeholder="<?=__('详细地址', 'nlyd-student')?>" autocomplete="off" class="nl-input nl-foucs" value="<?=$row['address']?>">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><?=__('设为默认', 'nlyd-student')?></div>
                            <div class="layui-input-block">
                                <input type="checkbox" name='is_default' lay-skin="primary" <?=$row['is_default'] == 1 ? 'checked' : '';?> >
                            </div>
                            
                        </div>
                    </div>
                </div>
                <a class="a-btn" lay-filter="addAdressBtn" lay-submit=""><?=__('保存更改', 'nlyd-student')?></a>
            </form>
        </div>           
    </div>
</div>