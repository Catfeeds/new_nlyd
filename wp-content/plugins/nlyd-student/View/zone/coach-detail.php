
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('教练详情', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content ">
                <div class="_relative">
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('教练姓名', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$real_name?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('教练ID', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$coach_ID?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('教练职称', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=__('IISC高级教练', 'nlyd-student')?></div>   
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('教学类别', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=__('记忆/速读/心算', 'nlyd-student')?></div>   
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('教练性别', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=__($user_gender, 'nlyd-student')?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('身份证号', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$real_ID?></div>
                    </div>
                    <div class="detail_table_row img-z">
                         <div class="detail_label"><?=__('证件照片', 'nlyd-student')?>：</div>
                         <?php if(!empty($user_ID_Card)){ ?>
                         <?php foreach ($user_ID_Card as $v){ ?>
                         <div class="detail_detail_img"><img src="<?=$v?>"></div>
                         <?php } ?>
                         <?php }else{ ?>
                         <div class="detail_detail_img"><img src="<?=student_css_url.'image/noInfo/noMatch1042@2x.png'?>"></div>
                         <div class="detail_detail_img"><img src="<?=student_css_url.'image/noInfo/noMatch1042@2x.png'?>"></div>
                         <?php } ?>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('联系方式', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=__('1588888888888', 'nlyd-student')?></div>   
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('累计学员', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=__('88位', 'nlyd-student')?></div>   
                    </div> 
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('教练简介', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=__('我是教练简介我是教练简介我是教练简介我是教练简介我是教练简介我是教练简介我是教练简介我是教练简介我是教练简介我是教练简介我是教练简介', 'nlyd-student')?></div>   
                    </div>
                    <div class="coach_head_img img-z">
                        <div class="img-box">
                            <img src="<?=student_css_url.'image/nlyd.png'?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>           
    </div>
</div>
<script>
jQuery(function($) { 
    layui.use(['layer'], function(){
        layer.photos({//图片预览
            photos: '.img-z',
            anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        }) 
    }) 
})
</script>
