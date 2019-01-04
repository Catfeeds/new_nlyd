
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
                <h1 class="mui-title"><div><?=__('学员详情', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content">
                <div class="nl-table-wapper student_detail">
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('学员姓名', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$real_name?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('学员ID', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$user_ID?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('学员年龄', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$user_age?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('学员性别', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=__($user_gender, 'nlyd-student')?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('身份证号', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$real_ID?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('证件照片', 'nlyd-student')?>：</div>
                         <?php if(!empty($user_ID_Card)){ ?>
                         <?php foreach ($user_ID_Card as $v){?>
                         <div class="detail_detail_img"><img src="<?=$v?>"></div>
                         <?php } ?>
                         <?php }else{ ?>
                         <div class="detail_detail_img"><img src="<?=student_css_url.'image/zone/upload_bg.png'?>"></div>
                         <div class="detail_detail_img"><img src="<?=student_css_url.'image/zone/upload_bg.png'?>"></div>
                         <?php } ?>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('联系方式', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$user_mobile?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('记忆教练', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=!empty($coach['memory']) ? $coach['memory'] : '暂无'?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('速读教练', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=!empty($coach['reading']) ? $coach['reading'] : '暂无'?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('心算教练', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=!empty($coach['arithmetic']) ? $coach['arithmetic'] : '暂无'?></div>
                    </div>
                    <div class="detail_table_row">
                         <div class="detail_label"><?=__('推荐用户', 'nlyd-student')?>：</div>   
                         <div class="detail_detail c_black"><?=$referee_id?></div>
                    </div>
                </div>
          
            </div>
        </div>           
    </div>
</div>
<script>
jQuery(function($) { 
//     layui.use(['layer'], function(){
//         layer.photos({//图片预览
//             photos: '.img-zoos',
//             anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
//         }) 
//     }) 
})
</script>
