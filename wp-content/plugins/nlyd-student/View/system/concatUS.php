
<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=bAXxnnlcOdxyHxkxpkKoaPfkEnMqSTcV&callback=initialize"></script>  
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
             <header class="mui-bar mui-bar-nav">
                    <a class="mui-pull-left nl-goback">
                        <i class="iconfont">&#xe610;</i>
                    </a>
                    <h1 class="mui-title">合作联系</h1>
                </header>
            <div class="layui-row nl-border nl-content  layui-bg-white">
                <div id="baiduMap"></div> 
                <div class="layui-row width-padding ">
                    <div class="concat-wrap">
                        <div class="concat-row blue-b">
                            <p class="concat-info">
                                <span>秘书处电话：</span>
                                <span>028-69956166</span>
                            </p>
                            <p class="concat-info">
                                <span>商务部电话：</span>
                                <span>028-66286610</span>
                            </p>
                            <p class="concat-info">
                                <span>赛事部电话：</span>
                                <span>028-66795112</span>
                            </p>
                        </div>
                        <div class="concat-row orange-b">
                            <p class="concat-info">
                                <span>邮箱地址：</span>
                                <span>gjnlyd @163.com</span>
                            </p>
                            <p class="concat-info">
                                <span>通讯地址：</span>
                                <span>成都市新希望路7号丰徳万瑞中心A 座25楼</span>
                            </p>
                            <p class="concat-info">
                                <span>官网地址：</span>
                                <span>www.gjnlyd.com</span>
                            </p>
                        </div>
                        <div class="concat-row violet-b">
                            <div class="img-we">
                                <img src="<?=student_css_url.'image/concatUs/weChat.png'?>">
                            </div>
                            <div class="img-detail">
                                <p class="concat-info">
                                    <span>官方微信号</span>
                                </p>
                                <p class="concat-info">
                                    <span>国际脑力运动(gjnlyd)</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">  
jQuery(function($) { 
    initialize=function(params) {
        var map = new BMap.Map("baiduMap");
        // 创建地图实例  
        var point = new BMap.Point(104.077964,30.621708);
        // 创建点坐标  
        map.centerAndZoom(point, 15);
        // 初始化地图，设置中心点坐标和地图级别
        var marker = new BMap.Marker(point);        // 创建标注    
        map.addOverlay(marker);                     // 将标注添加到地图中 
        // 定义一个控件类，即function    
        // function ZoomControl() {
        //     // 设置默认停靠位置和偏移量  
        //     this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
        //     this.defaultOffset = new BMap.Size(10, 10);
        // }
        // // 通过JavaScript的prototype属性继承于BMap.Control   
        // ZoomControl.prototype = new BMap.Control();

        // // 自定义控件必须实现initialize方法，并且将控件的DOM元素返回   
        // // 在本方法中创建个div元素作为控件的容器，并将其添加到地图容器中
        // ZoomControl.prototype.initialize = function (map) {
        //     //创建一个DIV
        //     var mydiv = document.createElement("div");
        //     //创建一个放大用的img
        //     var img_plus = document.createElement("img");
        //     //设置img的src属性
        //     img_plus.setAttribute("src", "./images/plus_2.png");
        //     //为img设置点击事件
        //     img_plus.onclick = function () {
        //         map.zoomTo(map.getZoom() + 1);
        //     }
        //     //创建一个缩小用的img
        //     var img_minus = document.createElement("img");
        //     img_minus.setAttribute("src", "./images/minus_2.png");
        //     img_minus.onclick = function () {
        //         map.zoomTo(map.getZoom() - 1);
        //     }
        //     //添加放大的img图标到div中
        //     mydiv.appendChild(img_plus);
        //     //加一个换行符，使2个图标上下排列
        //     mydiv.appendChild(document.createElement("br"));
        //     //添加缩小的img图标到div中
        //     mydiv.appendChild(img_minus);
        //     //添加DOM元素到地图中
        //     map.getContainer().appendChild(mydiv);
        //     //将DOM元素返回；
        //     return mydiv;
        // }

        // // 创建控件实例    
        // var myZoomCtrl = new ZoomControl();
        // // 添加到地图当中    
        // map.addControl(myZoomCtrl);  
        var opts = {    
            width : 0,     // 信息窗口宽度    
            height: 0,     // 信息窗口高度    
            title : "丰德万瑞中心"  // 信息窗口标题   
        }    
        var infoWindow = new BMap.InfoWindow("成都市新希望路7号丰徳万瑞中心A 座25楼", opts);  // 创建信息窗口对象    
        map.openInfoWindow(infoWindow, map.getCenter());      // 打开信息窗口
        marker.addEventListener("click", function(){    
            map.zoomTo(map.getZoom() + 1);   
        });  
    }
     
})
</script>  