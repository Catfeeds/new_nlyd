<!-- 手机绑定匹配 -->
<extend name="Base/common"/>
<block name="style">
<style>
    body{background: #a31616 none;}
    .paycont{text-align: center;}
    .boa_i_a{font-style: normal;}
    .text_center{color: #fff; font-size: 0.2rem;}
    .boa_p1{margin-top: 20px;}
    .boa_img{width:150px;margin: auto;}
    .boa_img img{width:100%;}
    .boa_img_android{margin-top: 20px;}
</style>
</block>
<block name="body">
   <div class="paycont">
    <input type="hidden" id="serialnumber" value="{:$serialnumber}" />
    <p class="text_center boa_p1">请点击屏幕右上角[ ... ]</p>
    <p class="text_center boa_p4">请用safari浏览器打开</p>
    <p>
        <img class="boa_img boa_img_android" src="__IMG__/phoneSkip/android_open_01.png" alt=""/>
    </p>
    <p>
        <img class="boa_img boa_bottom_img" src="__IMG__/phoneSkip/phone_bottom.png" alt=""/>
    </p>
</div>
</block>
<block name="script">
   <script>
        (function($, doc) {
            var comUrl =  "{:__ROOT_URL__.__CHILD__}";
            var serial_number =  doc.getElementById('serialnumber').value;
            var setTime =0;
            getData = function(){
                $.ajax(comUrl+'/Home/Pay/payInquiry',{
                    dataType:'json',//服务器返回json格式数据
                    type:'POST',//HTTP请求类型
                    data:{'serial_number':serial_number,'count':setTime},
                    crossDomain: true,
                    success:function(datas){
                        if (datas.status=='200') {
                            window.location.href = datas.url;

                          }else{
                            setTime = datas.count;
                            if(datas.count == 48){ //四分钟
                                clearInterval(setTime);
                             } 
						}
                     },
					error:function(xhr,type,errorThrown){
						mui.alert('网络连接失败，请稍后重试');
					}
                });
            };
            setTime = setInterval(getData,1000);
        }(mui,document));

    </script>
</block>
