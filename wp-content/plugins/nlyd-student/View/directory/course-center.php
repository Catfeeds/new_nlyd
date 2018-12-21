<style>
@media screen and (max-width: 1199px){
    .layui-fluid>.layui-row>.layui-bg-white:first-child,#page{
        background-color:#f6f6f6!important;
    }
}
</style>
<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper have-footer">
            <header class="mui-bar mui-bar-nav layui-bg-white">
                <div class="search-zoo">
                    <i class="iconfont search-Icon">&#xe63b;</i>
                    <input type="text" class="serach-Input nl-foucs" placeholder="<?=__('搜索名录/课程/教练等', 'nlyd-student')?>">
                </div>
            </header>
            <div class="layui-row nl-border nl-content">
                <!-- 头部导航 -->
                <div class="layui-row width-padding layui-bg-white">
                    <div class="top-nav">
                        <div class="top-nav-btn"><a class="fs_16 c_black6" href="<?=home_url('/student/index');?>"><?=__('首 页', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_blue c_black6"  href="<?=home_url('/directory/');?>"><?=__('名 录', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn active"><a class="fs_16 c_blue"  href="<?=home_url('/directory/course');?>"><?=__('课 程', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6 disabled_a" href="<?=home_url('/shops/');?>"><?=__('商 城', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6 disabled_a"><?=__('公 益', 'nlyd-student')?></a></div>
                    </div>
                </div>
                <div class="swiper-container layui-bg-white" style="margin-bottom:0">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad1.png'?>"></div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad2.png'?>"></div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad3.png'?>"></div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>

                <div>
                    <div class="course_city width-padding width-padding-pc">
                        <span class="c_black"><?=__('您所在的城市', 'nlyd-student')?>：</span>
                        <span class="addres c_blue mr_10"><?=__('成都', 'nlyd-student')?></span>
                        <a class="c_black"><?=__('重新定位', 'nlyd-student')?></a>
                    </div>

                    <a class="course_row width-padding width-padding-pc c_black6" href="<?=home_url('/directory/cenerCourse/');?>">
                        <div class="course_city_icon c_blue"><i class="iconfont">&#xe659;</i></div>
                        <div class="course_info">
                            <div class="course_info_row fs_16 c_black">IISC脑力训练中心（NO.0001.明德）</div>
                            <div class="course_info_row fs_14">所在地：四川·成都</div>
                            <div class="course_info_row fs_12 c_orange">1个课程抢占名额中</div>
                        </div>
                        <div class="course_right_icon c_black">
                            <i class="iconfont">&#xe727;</i>
                        </div>
                    </a>

                    <a class="course_row width-padding width-padding-pc c_black6" href="<?=home_url('/directory/cenerCourse/');?>">
                        <div class="course_city_icon c_blue"><i class="iconfont">&#xe659;</i></div>
                        <div class="course_info">
                            <div class="course_info_row fs_16 c_black">IISC脑力训练中心（NO.0001.明德）</div>
                            <div class="course_info_row fs_14">所在地：郫县</div>
                            <div class="course_info_row fs_12 c_orange">1个课程抢占名额中</div>
                        </div>
                        <div class="course_right_icon c_black">
                            <i class="iconfont">&#xe727;</i>
                        </div>
                    </a>
                </div>

                <div>
                    <div class="course_city width-padding width-padding-pc">
                        <span class="c_black"><?=__('其他城市', 'nlyd-student')?>：</span>
                    </div>

                    <a class="course_row width-padding width-padding-pc c_black6" href="<?=home_url('/directory/cenerCourse/');?>">
                        <div class="course_city_icon c_blue"><i class="iconfont">&#xe659;</i></div>
                        <div class="course_info">
                            <div class="course_info_row fs_16 c_black">IISC脑力训练中心（NO.0001.明德）</div>
                            <div class="course_info_row fs_14">所在地：重庆</div>
                            <div class="course_info_row fs_12 c_orange">1个课程抢占名额中</div>
                        </div>
                        <div class="course_right_icon c_black">
                            <i class="iconfont">&#xe727;</i>
                        </div>
                    </a>

                    <a class="course_row width-padding width-padding-pc c_black6" href="<?=home_url('/directory/cenerCourse/');?>">
                        <div class="course_city_icon c_blue"><i class="iconfont">&#xe659;</i></div>
                        <div class="course_info">
                            <div class="course_info_row fs_16 c_black">IISC脑力训练中心（NO.0001.明德）</div>
                            <div class="course_info_row fs_14">所在地：重庆</div>
                            <div class="course_info_row fs_12 c_orange">1个课程抢占名额中</div>
                        </div>
                        <div class="course_right_icon c_black">
                            <i class="iconfont">&#xe727;</i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(function($) { 
    var mySwiper = new Swiper('.swiper-container', {
        loop : true,
        autoplay:{
            disableOnInteraction:false
        },//可选选项，自动滑动
        autoplayDisableOnInteraction : false,    /* 注意此参数，默认为true */ 
        initialSlide :0,//初始展示页
        pagination: {
            el: '.swiper-pagination',
            dynamicBullets: true,
            dynamicMainBullets: 2,
            clickable :true,
        },
    }); 
       //定义一个空的位置构造函数
  function Location(){};
  //定义一个可以获得经纬度的方法
  Location.prototype.getLocation = function(callback){
    var options = {
      enableHighAccuracy: true,
      maximumAge: 1000
    };
    this.callback = Object.prototype.toString.call(callback) =="[object Function]" ?
        callback :
        function(address){
          alert(address.province + address.city);
          console.log("getocation(callbackFunction) 可获得定位信息对象");
        };
    var self = this;
    if (navigator.geolocation) {
      //浏览器支持geolocation
      navigator.geolocation.getCurrentPosition(function(position){
        //经度
        var longitude = position.coords.longitude;
        //纬度
        var latitude = position.coords.latitude;
        self.loadMapApi(longitude,latitude);
      }, self.onError, options);
    } else {
      //浏览器不支持geolocation
      alert("浏览器不支持");
    }
  };
  //定义一个可以解析经纬度的方法，调用百度的api
  Location.prototype.loadMapApi = function(longitude, latitude){
    var self = this;
    var oHead = document.getElementsByTagName('HEAD').item(0);
    var oScript= document.createElement("script");
    oScript.type = "text/javascript";
    oScript.src="https://api.map.baidu.com/getscript?v=2.0&ak=A396783ee700cfdb9ba1df281ce36862&services=&t=20140930184510";
    oHead.appendChild(oScript);
    oScript.onload = function(date){
      var point = new BMap.Point(longitude, latitude);
      var gc = new BMap.Geocoder();
      gc.getLocation(point, function(rs) {
        var addComp = rs.addressComponents;
        self.callback(addComp);
      });
    }
  };
  //定义出现查询位置出现意外的方法
  Location.prototype.onError = function(error) {
    switch (error.code) {
      case 1:
        alert("位置服务被拒绝");
        break;
      case 2:
        alert("暂时获取不到位置信息");
        break;
      case 3:
        alert("获取信息超时");
        break;
      case 4:
        alert("未知错误");
        break;
    }
  };
  //调用
  var local = new Location();
  local.getLocation(function(res){
    var str=""
    var city=res['city'];
    var province=res['province'];
    for(i in res ){
      str=res[i]+str
    }
    // str=province+city
    // alert(str)
    $('.addres').text(str)
  })

})
</script>