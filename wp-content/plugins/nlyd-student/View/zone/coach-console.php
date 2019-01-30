
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
                        <img src="<?=student_css_url.'image/zone/head.png'?>"> 
                    </div>    
                    <div class="zone_user_detail pull-left">
                        <div class="zone_title_row  c_black" style="margin-bottom:9px;">
                            <span class="bold fs_16 zone_title_name">
                               陈卫东
                            </span>
                            <!-- <span class="qr_code c_orange"><i class="iconfont fs_26">&#xe651;</i></span> -->
                        </div>
                        <div class="c_black" style="margin-bottom:9px;"><?=__('IISC教练', 'nlyd-student')?></div>
                        <div class="c_black">
                            <span class="c_blue">xxx温江训练中心（001）</span>
                            <span class="pull-right">
                            <a class="back_user c_orange"><?=__('返回个人账号', 'nlyd-student')?></a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="apply width-padding layui-row layui-bg-white width-padding-pc">
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/coachUpload');?>">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_upload"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('教练证书', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_black3 mr_10"><?=__('未上传', 'nlyd-student')?></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/coachIntroduction');?>">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_introduction"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('教练简介', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_black3 mr_10"><?=__('未填写', 'nlyd-student')?></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/course');?>">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_course"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('课程管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right mr_10"></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/student');?>">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_student"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('学员管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_orange mr_10">1<?=__('个新申请', 'nlyd-student')?></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/match');?>">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_match"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('比赛管理', 'nlyd-student')?></div>
                        <div class="apply_list_line pull-right"><i class="iconfont fs_20">&#xe727;</i></div>
                        <div class="apply_list_line pull-right c_orange mr_10"></div>
                    </a>
                    <a class="apply_list c_black layui-row" href="<?=home_url('/zone/grading');?>">
                        <div class="apply_list_line pull-left">
                            <div class="zone_bg bg_level"></div>
                        </div>
                        <div class="apply_list_line center"><?=__('考级管理', 'nlyd-student')?></div>
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
                    }
                    _this.removeClass('disabled');
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
