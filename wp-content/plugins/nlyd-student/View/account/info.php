<div class="nl-cropper-bg">
    <div class="img-container">
        <img id="image" src="">
    </div>
    <div class="nl-cropper-footer">
        <button type="button" class="pull-left" id='crop-cancel'>取消</button>
        <button type="button" class="pull-right" id="crop">确认</button>
    </div>

</div>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            <div class="main-header">
                <header class="mui-bar mui-bar-nav main">
                    <a class="mui-pull-left nl-goback static" href="<?=home_url('account/')?>">
                        <i class="iconfont">&#xe610;</i>
                    </a>
                    <h1 class="mui-title">个人资料</h1>
                </header>
                <header class="mui-bar mui-bar-nav certification" href="certification">
                    <a class="mui-pull-left nl-goPage">
                        <i class="iconfont">&#xe610;</i>
                    </a>
                    <h1 class="mui-title">实名认证</h1>
                </header>
            </div> 
            <div class="layui-row nl-border nl-content">
                <div class="main-page">
                    <form class="nl-page-form layui-form width-margin-pc have-bottom" lay-filter='nicenameForm'>   
                    
                        <div class="nl-form-tips width-padding width-padding-pc">为了保证您考级及比赛的真实有效性，请您确保个人资料准确无误</div>
                        <div class="form-inputs">
                            <div class="form-input-row">
                                <div class="form-input-label">账户头像</div>
                                <!-- <span class="Mobile form-input-right">修改</span> -->
                                <div id="imgBox" class="imgBox">
                                    <img class="logoImg" src="<?=$user_info['user_head'];?>">
                                </div>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">账户ID</div>
                                <div class="nl-input"><?=isset($user_info['user_ID']) ? $user_info['user_ID'] : '暂无ID';?></div>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">账户昵称</div>
                                <input name='meta_val' value="<?=isset($user_info['nickname']) ? $user_info['nickname'] : '';?>" type="text" placeholder="账户昵称" class="nl-input nl-foucs" lay-verify="required">
                                <input  type="hidden" name="action" value="student_saveInfo"/>
                                <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">
                                <input  type="hidden" name="meta_key" value="user_nicename"/>
                            </div>
                            <div class="form-input-row" href="certification">
                                <div class="form-input-label">实名认证</div>
                                <span class="form-input-right"><i class="iconfont">&#xe727;</i></span>
                                <div class="nl-input"><?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_name'] : '未认证';?></div>
                            </div>
                            <a class="form-input-row a address-row layui-row" href="<?=home_url('/account/address');?>">
                                <div class="form-input-label">收货地址</div>
                                <span class="form-input-right"><i class="iconfont">&#xe727;</i></span>
                                <div  class="nl-input">  
                                    <?php if($user_address){ ?>
                                        <p class="accept-address">
                                            <?=$user_address['fullname']?>&nbsp;&nbsp;&nbsp;&nbsp;<?=$user_address['telephone']?>
                                            <br><?=$user_address['address']?>
                                        </p>
                                    <?php }else{ ?>
                                        暂无地址
                                    <?php }?>
                                        
                                </div>
                            </a>
                            <div class="a-btn" lay-filter="nicenameFormBtn" lay-submit="">更新个人资料</div>
                        </div>
                
                    </form>
                </div>
                <!-- 实名认证 -->
                    <div id="certification" class="form-page">
                        <form class="layui-form nl-page-form width-margin-pc have-bottom" lay-filter='certificationForm'>
                            <div class="form-inputs">
                                <div class="form-input-row">
                                    <div class="form-input-label">证件类型</div>
                                    <input value='<?= !empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_type_c'] : '';?>' type="text" readonly id="trigger1" placeholder="选择证件类型" class="nl-input" lay-verify="required">
                                    <input value='<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_type'] : '';?>'  type="hidden" name="meta_val[real_type]" id="trigger2">
                                    <input  type="hidden" name="action" value="student_saveInfo"/>
                                    <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">
                                    <input  type="hidden" name="meta_key" value="user_real_name"/>
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">证件姓名</div>
                                    <input type="text" name="meta_val[real_name]" value="<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_name'] : '';?>" placeholder="输入证件上的真实姓名" lay-verify="required" class="nl-input nl-foucs">
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">证件号码</div>
                                    <input type="text" name="meta_val[real_ID]" value="<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_ID'] : '';?>" placeholder="输入证件上的真实证件号" lay-verify="required"  class="nl-input nl-foucs">
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">性 别</div>
                                    <input name='user_gender'  value='<?=isset($user_info['user_gender']) ? $user_info['user_gender'] : '';?>' type="text" readonly id="trigger3" placeholder="请选择您的性别" class="nl-input" lay-verify="required">
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">年龄</div>
                                    <input type="text" name="meta_val[real_age]" value="<?=!empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_age'] : '';?>" placeholder="年龄" lay-verify="required"  class="nl-input nl-foucs">
                                </div>
                                <div class="form-input-row">
                                    <div class="form-input-label">所在城市</div>
                                    <input readonly id="areaSelect" type="text" placeholder="所在城市" value="<?=!empty($user_info['user_address']) ? $user_info['user_address']['province'].$user_info['user_address']['city'].$user_info['user_address']['area'] : ''?>" class="nl-input" lay-verify="required">
                                    <input  type="hidden" id="province" name="user_address[province]" value="<?=!empty($user_info['user_address']) ? $user_info['user_address']['province'] : ''?>"/>
                                    <input  type="hidden" id="city" name="user_address[city]" value="<?=!empty($user_info['user_address']) ? $user_info['user_address']['city'] : ''?>">
                                    <input  type="hidden" id="area" name="user_address[area]" value="<?=!empty($user_info['user_address']) ? $user_info['user_address']['area'] : ''?>"/>
                                </div>
                                <div class="a-btn" lay-filter="certificationFormBtn" lay-submit="">更新实名认证</div>
                            </div>
                            
                        </form>
                    </div>
            </div>
        </div>           
    </div>

<input style="display:none;" type="file" name="meta_val" id="file" class="file" value="" accept="image/*" multiple/>
<input type="hidden" name="_wpnonce" id="inputImg" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">

</div>
