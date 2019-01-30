
<style>
@media screen and (max-width: 1199px){
    .layui-fluid>.layui-row>.layui-bg-white:first-child,#page{
        background-color:#f6f6f6!important;
    }
    #page{
        top:0;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper have-bottom no-header">
            
            <div class="layui-row nl-border nl-content">
                <div class="zone_user width-padding layui-row layui-bg-white width-padding-pc">
                    <div class="img-box zone_user_img pull-left">
                        <?php if($row['zone_type_alias'] == 'match'){ ?>
                        <!-- 赛区展示 -->
                            <img src="<?=student_css_url.'image/zone/match_head.png'?>">
                        <?php }else{ ?>
                            <img src="<?=student_css_url.'image/zone/head.png'?>">
                        <?php } ?>
                    </div>    
                    <div class="zone_user_detail pull-left">

                        <!-- 审核通过 -->
                        <div class="zone_title_row  <?=$row['user_status'] == 1 ? 'c_black' : 'c_black3'?>">
                            <span class="bold fs_16 zone_title_name">
                               <?= $row['zone_title'];?>
                            </span>
                            <span class="qr_code c_orange"><i class="iconfont fs_26">&#xe651;</i></span>
                        </div>

                        <div class="c_black">
                            <?=__('编 号', 'nlyd-student')?>：<?=!empty($row['legal_person']) ? $row['zone_number'] : $row['user_ID']?>
                            <?php if($row['user_status'] == 1){ ?>
                                <div class="img-box zone_pass mr_10"><img src="<?=student_css_url.'image/pass.png'?>" alt="<?=__('已认证', 'nlyd-student')?>"></div>
                                <a class=" c_blue" href="<?=home_url('/zone/apply/zone_id/'.$row['id'].'/type_id/'.$row['zone_match_type'])?>"><?=__('更多资料', 'nlyd-student')?></a>
                            <?php } ?>
                        </div>

                        <div class="c_black">
                            <span><?=__(!empty($row['legal_person'])?'管理员':'推荐人', 'nlyd-student')?>：<?=empty($row['referee_user_ID'])? '无' : $row['referee_user_ID'];?></span>
                            <span class="pull-right">
                                <?php if ($row['user_status'] == 1 && $_SESSION['manager_id'] > 0){ ?>
                                    <a class="back_user c_orange"><?=__('返回个人账号', 'nlyd-student')?></a>
                                <?php } ?>
                                <?php if ($row['user_status'] == -1){ ?>
                                    <span class=" c_orange mr_10"><?=__('资料审核中', 'nlyd-student')?></span>
                                <?php } ?>
                                <?php if(!empty($row['id']) && $row['user_status'] == -2):?>
                                    <a class=" c_blue" href="<?=home_url('/zone/apply/type_id/'.$row['type_id'].'/zone_type_alias/'.$row['zone_type_alias'])?>"><?=__('修改', 'nlyd-student')?></a>
                                <?php endif;?>
                            </span>
                        </div>

                    </div>
                </div>
                <div class="apply width-padding layui-row layui-bg-white width-padding-pc">
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/profit/');?>">
                        <div class="apply_list_line pull-left ">
                            <div class="zone_bg bg_money"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('收益管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_black3 mr_10"><?=__('今日收入', 'nlyd-student')?><?=$stream > 0 ? $stream : number_format($stream,2)?></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/recommend/');?>">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_share"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('我的推荐', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                </div>
                <!-- 训练中心(控制台)-->
                <div class="apply width-padding layui-row layui-bg-white width-padding-pc">
                    <?php if(!empty($role_list)):?>
                    <?php foreach ($role_list as $x){
                            //$thumbnail_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($v->ID), 'thumbnail');
                    ?>
                    <a class="apply_list <?=$row['user_status'] == 1 ? 'c_black' : 'c_black3'?> layui-row" <?php if($row['user_status'] == 1){echo "href=".home_url('/zone/'.$x['role_action']);} ?> >
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg <?=$x['role_back']?>"></div>
                        </div>
                        <div class="apply_list_line center"><?=__($x['role_name'], 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_orange mr_10"></div>
                    </a>
                    <?php } ?>
                    <?php endif;?>
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/data');?>">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_data"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('数据统计', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_orange mr_10"></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/setting');?>">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_setting"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('其他设置', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_orange mr_10"></div>
                    </a>
                </div>
                <?php if(empty($_SESSION['manager_id'])):?>
                <a class="a-btn a-btn-table" id="loginOut"><div><?=__('退出登录', 'nlyd-student')?></div></a>
                <?php endif;?>
            </div>  
        </div>            
    </div>
</div>

<script>
jQuery(function($) {
    history.pushState(null, null, document.URL);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.URL);
    });
    $('#loginOut').click(function(){//登出
        var _this=$(this);
        if(!_this.hasClass('disabled')){
            $.ajax({
                data: {action:'user_logout'},
                beforeSend:function(XMLHttpRequest){
                    _this.addClass('disabled')
                },
                success: function(res, textStatus, jqXHR){
                    $.alerts(res.data.info)
                    if(res.success){
                        if(res.data.url){
                            setTimeout(function(){
                                window.location.href=res.data.url
                            }, 1000);
                        }
                    }
                    return false;
                },
                complete: function(jqXHR, textStatus){
                    if(textStatus=='timeout'){
                        $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
                        _this.removeClass('disabled');
                    }
                }
            })
        }
        
    })
    $('.back_user').click(function(){
        var _this=$(this);
        if(!_this.hasClass('disabled')){
            var data={
                action:'change_user'
            }
            $.ajax({
                data: data,
                beforeSend:function(XMLHttpRequest){
                    _this.addClass('disabled')
                },
                success: function(res, textStatus, jqXHR){
                    if(res.data.url){
                        window.location.href=res.data.url
                    }else{
                        $.alerts("<?=__('返回失败', 'nlyd-student')?>")
                    }
                },
                complete: function(jqXHR, textStatus){
                    if(textStatus=='timeout'){
                        $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
                    }
                    _this.removeClass('disabled');
                }
            })
        }
        return false;
    })
    layui.use(['flow','layer','element'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

        var flow = layui.flow;//流加载
        $('.qr_code').click(function () {
            $.post(admin_ajax,{'action':'qrcode'},function (data) {
                if(data.success == true){
                    var html = "<img src='"+data.data+"'>";
                    var json={
                        "title": "<?=__('推广码', 'nlyd-student')?>", //相册标题
                        "id": "coach_see", //相册id
                        "start": 0, //初始显示的图片序号，默认0
                        "data": [   //相册包含的图片，数组格式
                            {
                            "alt": "",
                            "pid": 1, //图片id
                            "src":data.data, //原图地址
                            "thumb":data.data, //缩略图地址
                            }
                        ]
                    }
                    if(json['data'].length==0){
                        $.alerts("<?=__('当前教练未上传证书', 'nlyd-student')?>")
                    }else{
                        layer.photos({//图片预览
                            photos: json,
                            anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                        })
                    }
                }

            },'json')
        })
    })
})
</script>
