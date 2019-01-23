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
                        <span class="c_black"><?=__('您所在的位置', 'nlyd-student')?>：</span>
                        <a class="addres c_blue mr_10" id="areaSelect"><?=__('请选择', 'nlyd-student')?></a>
                    </div>
                    <div id="flowMyAdress">
                    
                    </div>
                </div>

                <!-- <div>
                    <div class="course_city width-padding width-padding-pc">
                        <span class="c_black"><?=__('所有城市', 'nlyd-student')?>：</span>
                    </div>
                    <div id="flowAllAdress">
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>
<script src="http://pv.sohu.com/cityjson?ie=utf-8"></script>
<script>
jQuery(function($) { 
    layui.use(['element','flow'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        var flow = layui.flow;//流加载
        var userAddress=returnCitySN["cname"] || "<?=__('请选择', 'nlyd-student')?>";
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
        function pagation(id,_page,address){
            flow.load({
                elem: '#'+id //流加载容器
                ,isAuto: false
                ,isLazyimg: true
                ,done: function(page, next){ //加载下一页
                    // var address=$('#areaSelect').text();
                    var postData={
                        action:'get_course_zone',
                        page:_page,
                    }
                    if(address && address.length>0 && address!="<?=__('请选择', 'nlyd-student')?>"){
                        postData.city=address;
                    }
                    var lis = [];
                    $.ajax({
                        data: postData,
                        success:function(res,ajaxStatu,xhr){
                            _page++
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var dom= '<a class="course_row width-padding width-padding-pc c_black6" href="'+window.home_url+'/courses/cenerCourse/id/'+v.user_id+'/">'
                                                +'<div class="course_city_icon c_blue"><i class="iconfont">&#xe659;</i></div>'
                                                +'<div class="course_info">'
                                                    +'<div class="course_info_row fs_16 c_black">IISC脑力训练中心'+v.content+'</div>'
                                                    +'<div class="course_info_row fs_14"><?=__("所在地", "nlyd-student")?>：'+v.zone_city+'</div>'
                                                    +'<div class="course_info_row fs_12 c_orange">'+v.course_total+'个课程抢占名额中</div>'
                                                +'</div>'
                                                +'<div class="course_right_icon c_black">'
                                                    +'<i class="iconfont">&#xe727;</i>'
                                                +'</div>'
                                            +'</a>'
                                    lis.push(dom) 

                                })
                                if (res.data.info.length<50) {
                                    next(lis.join(''),false) 
                                }else{
                                    next(lis.join(''),true) 
                                }
                            }else{
                                next(lis.join(''),false)
                            }
                            if(!$('#'+id).hasClass('flow-default')){
                                $('#'+id).addClass('flow-default')
                            }
                            
                        },
                        complete:function(XMLHttpRequest, textStatus){
							if(textStatus=='timeout'){
								$.alerts("<?=__('网络质量差,请重试', 'nlyd-student')?>")
								next(lis.join(''),true)
							}
                        }
                    })       
                }
            });
        }
        pagation('flowMyAdress',1,userAddress)

    
        // //定义一个空的位置构造函数
        // function Location(){};
        // //定义一个可以获得经纬度的方法
        // Location.prototype.getLocation = function(callback){
        //     var options = {
        //     enableHighAccuracy: true,
        //     maximumAge: 1000
        //     };
        //     this.callback = Object.prototype.toString.call(callback) =="[object Function]" ?
        //         callback :
        //         function(address){
        //         // alert(address.province + address.city);
        //         console.log("getocation(callbackFunction) 可获得定位信息对象");
        //         };
        //     var self = this;
        //     if (navigator.geolocation) {
        //     //浏览器支持geolocation
        //     navigator.geolocation.getCurrentPosition(function(position){
        //         //经度
        //         var longitude = position.coords.longitude;
        //         //纬度
        //         var latitude = position.coords.latitude;
        //         self.loadMapApi(longitude,latitude);
        //     }, self.onError, options);
        //     } else {
        //     //浏览器不支持geolocation
        //     alert("浏览器不支持");
        //     }
        // };
        // //定义一个可以解析经纬度的方法，调用百度的api
        // Location.prototype.loadMapApi = function(longitude, latitude){
        //     var self = this;
        //     var oHead = document.getElementsByTagName('HEAD').item(0);
        //     var oScript= document.createElement("script");
        //     oScript.type = "text/javascript";
        //     oScript.src="https://api.map.baidu.com/getscript?v=2.0&ak=A396783ee700cfdb9ba1df281ce36862&services=&t=20140930184510";
        //     oHead.appendChild(oScript);
        //     oScript.onload = function(date){
        //     var point = new BMap.Point(longitude, latitude);
        //     var gc = new BMap.Geocoder();
        //     gc.getLocation(point, function(rs) {
        //         var addComp = rs.addressComponents;
        //         self.callback(addComp);
        //     });
        //     }
        // };
        // //定义出现查询位置出现意外的方法
        // Location.prototype.onError = function(error) {
        //     switch (error.code) {
        //     case 1:
        //         alert("位置服务被拒绝");
        //         break;
        //     case 2:
        //         alert("暂时获取不到位置信息");
        //         break;
        //     case 3:
        //         alert("获取信息超时");
        //         break;
        //     case 4:
        //         alert("未知错误");
        //         break;
        //     }
        // };

        var area=$.validationLayui.allArea.area;//省市区三级联动
        var posiotionarea=[0,0,0];//初始化位置，高亮展示
        $('#areaSelect').text(userAddress)
        if($('#areaSelect').length>0){
            $.each(area,function(i1,v1){
                $.each(v1.childs,function(i2,v2){
                    v2.childs.unshift({
                        id:'-',
                        value:''
                    })
                })
            })
            // console.log(JSON.stringify(area))
            var mobileSelect3 = new MobileSelect({
                trigger: '#areaSelect',
                title: "<?=__('您的位置', 'nlyd-student')?>",
                wheels: [
                    {data: area},
                ],
                triggerDisplayData:false,
                position:posiotionarea, //初始化定位 打开时默认选中的哪个 如果不填默认为0
                transitionEnd:function(indexArr, data){

                },
                callback:function(indexArr, data){
                    var old= $('#areaSelect').text();
                    var three=data[2]['value'].length==0 ? '' : '-'+data[2]['value']
                    var text=data[0]['value']+'-'+data[1]['value']+three;
                    $('#areaSelect').text(text);
                    if(old!==text){
                        $('#flowMyAdress').empty()
                        pagation('flowMyAdress',1,text)
                    }
                    
                }
            });
        }
        // alert(userAddress)
        //调用
        // if(!$.GetSession('nl_address')){
            
        //     if('ontouchstart' in window){//移动端
        //         var local = new Location();
        //         local.getLocation(function(res){
        //             var str=""
        //             var city=res['city'];
        //             var province=res['province'];
        //             var district=res['district'];
        //             // for(i in res ){
        //             //     alert(i)
        //             // str=res[i]+str
        //             // }
        //             str=district ? province+'-'+city+'-'+district : province+'-'+city;
        //             $.SetSession('nl_address',str)
        //             // alert(str)
        //             $('#areaSelect').text(str)
        //             if(str.length>0 && str){
        //                 var areaValue=str.split('-');
        //                 $.each(area,function(index,value){
        //                     if(areaValue[0]==value.value){
        //                         // posiotionarea=[index,0,0];
        //                         mobileSelect3.locatePosition(0,index)
        //                         $.each(value.childs,function(i,v){
        //                             if(areaValue[1]==v.value){
        //                                 // posiotionarea=[index,i,0];
        //                                 mobileSelect3.locatePosition(1,i)
        //                                 $.each(v.childs,function(j,val){
        //                                     if(areaValue[2] && areaValue[2]==val.value){
        //                                         // posiotionarea=[index,i,j];
        //                                         mobileSelect3.locatePosition(2,j)
        //                                     }
        //                                 })
        //                             }
        //                         })
        //                     }
        //                 })
        //             }
        //             $('#flowMyAdress').empty()
        //             pagation('flowMyAdress',1)
        //         })
        //     }else{
        //         console.log(returnCitySN["cip"]+','+returnCitySN["cname"])
        //     }
        // }else{
        //     var str=$.GetSession('nl_address');
        //     $('#areaSelect').text(str)
        //     if(str.length>0 && str){
        //         var areaValue=str.split('-');
        //         $.each(area,function(index,value){
        //             if(areaValue[0]==value.value){
        //                 // posiotionarea=[index,0,0];
        //                 mobileSelect3.locatePosition(0,index)
        //                 $.each(value.childs,function(i,v){
        //                     if(areaValue[1]==v.value){
        //                         // posiotionarea=[index,i,0];
        //                         mobileSelect3.locatePosition(1,i)
        //                         $.each(v.childs,function(j,val){
        //                             if(areaValue[2] && areaValue[2]==val.value){
        //                                 // posiotionarea=[index,i,j];
        //                                 mobileSelect3.locatePosition(2,j)
        //                             }
        //                         })
        //                     }
        //                 })
        //             }
        //         })
        //     }
           
        //     $('#flowMyAdress').empty()
        //     pagation('flowMyAdress',1)
        // }
    });
})
</script>