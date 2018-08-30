<?php
/**
 * Archive pages.
 *
 * @package vogue
 * @since 1.0.0
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

$config = presscore_get_config();
$config->set( 'template', 'archive' );
$config->set( 'layout', 'masonry' );
$config->set( 'template.layout.type', 'masonry' );

presscore_config_base_init();

get_header(); ?>

			<!-- Content -->
			<div id="content" class="content" role="main">
				<div class="layui-fluid">
					<div class="layui-row">
						<div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper have-footer">
							<header class="mui-bar mui-bar-nav">
								<a class="mui-pull-left nl-goback static" onclick="window.location.href='<?=home_url('/student/index');?>'">
									<i class="iconfont">&#xe610;</i>
								</a>
								<h1 class="mui-title">行业新闻</h1>
							</header>
							<div class="layui-row nl-border nl-content">
								<div class="width-margin width-margin-pc news-wrapper layui-row flow-default" id="flow">

								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div><!-- #content -->

			<?php do_action('presscore_after_content'); ?>

<?php get_footer(); ?>
<script>
jQuery(function($) {   
    layui.use(['layer','flow'], function(){
        var flow = layui.flow;//流加载
//--------------------分页--------------------------
	flow.load({
            elem: '#flow'
            ,scrollElem: '#flow'
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                var postData={
                    action:'getNewsLists',
                    page:page,
                }
                var lis = [];
                $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res,ajaxStatu,xhr){
					console.log(res)
					if(res.success){
						$.each(res.data.info,function(i,v){
							
							var dom='<a class="nl-ad-row layui-bg-white" href="'+v.guid+'">'
										+'<div class="layui-row foot-info">'
											+'<div class="layui-col-lg5 layui-col-md5 layui-col-sm5 layui-col-xs5 img-box">'
												+'<img src="'+v.image+'">'
											+'</div>'
											+'<div class="layui-col-lg7 layui-col-md7 layui-col-sm7 layui-col-xs7">'
												+'<p class="nl-ad-name">'+v.post_title+'</p>'
												+'<p class="nl-ad-detail">'+v.post_content+'</p>'
											+'</div>'
										+'</div>'
									+'</a>'
							lis.push(dom) 
						})
						if (res.data.info.length<10) {
							next(lis.join(''),false) 
						}else{
							next(lis.join(''),true) 
						}
						
					}else{
						if(page==1){
							var dom='<div class="no-info">无新闻信息</div>'
							lis.push(dom) 
						}else{
							$.alerts('没有更多了')
						}
						next(lis.join(''),false)
					}
                })       
            }
        });
 //--------------------分页--------------------------  
    })
})
</script>