<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title">国际脑力运动</h1>
        </header>
            <?php if($param['status'] == false){ ?>
                <div class="layui-row nl-border nl-content layui-bg-white">
                    <p class="ta_c c_black" style="margin-top:100px"><?=$param['data']?></p>
                    <a class="a-btn" style="position: relative;top: 20px" href="">返回重新支付</a>
                </div>
            <?php } ?>
        </div>           
    </div>
</div>
<script>
jQuery(function($) {
    var prams='<?=$param['data']?>';
    function jsApiCall()
    {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            prams,
            function(res){
                if(res.err_msg=='get_brand_wcpay_request:ok'){//支付成功
                    window.location.href=window.home_url+'/matchs/info/match_id/'+$.Request('match_id')
                }else{//失败

                }
            }
        );
    }
	function callpay()
	{
	    alert(1);
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
    }
    <?php if($param['status']) echo 'callpay()'?>
})
</script>