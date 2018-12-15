<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>

        <?php if(!$row){ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper have-footer">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('考级管理', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content match_tabs">
                <div class="layui-tab layui-tab-brief width-padding width-padding-pc" lay-filter="matchList" style="margin-top:20px">
                    <ul style="margin:0;padding:0" class="layui-tab-title">
                        <li class="layui-this" lay-id="1">
                            <div><?=__('全部考级', 'nlyd-student')?></div>
                        </li>
                        <li lay-id="2">
                        <div><?=__('近期考级', 'nlyd-student')?></div>
                        </li>
                        <li lay-id="3"><div><?=__('往期考级', 'nlyd-student')?></div></li>
                        <div class="nl-transform"><div><?=__('近期考级', 'nlyd-student')?></div></div>
                    </ul>
                    <div class="layui-tab-content">
                        <!-- 全部考级 -->
                        <div class="layui-tab-item layui-show">
                            <ul class="flow-default layui-row" id="1" style="margin:0">
                                <li class="match_row"> 
                                     <div class="bold c_black f_16 mt_10">国际速读水平测试（南京）</div>
                                     <div class="match_body">
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('考级日期：', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">2018-08-08 09:32 <span class="c_blue ml_10">报名中</span></div>
                                         </div>
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('考级地点：', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">四川省成都市武侯区航空路丰德万瑞25楼</div>
                                         </div>
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('责任人', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">李四</div>
                                         </div>
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('已报选手：', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">18</div>
                                         </div>
                                     </div>  
                                     <div class="match_footer ta_r">
                                         <a class="clocs_match c_black6 "><span class="c_blue"><i class="iconfont fs_20">&#xe652;</i></span><span class="ml_10">关闭考级</span></a>
                                         <a href="<?=home_url('/zone/kaojiBuild/');?>" class="edit_match c_black6"><span class="c_blue"><i class="iconfont fs_20">&#xe654;</i></span><span class="ml_10">编辑考级</span></a>
                                    </div>
                                </li>    
                                <li class="match_row"> 
                                     <div class="bold c_black f_16 mt_10">国际速读水平测试（南京）</div>
                                     <div class="match_body">
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('考级日期：', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">2018-08-08 09:32 <span class="c_blue ml_10">考级中</span></div>
                                         </div>
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('考级地点：', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">四川省成都市武侯区航空路丰德万瑞25楼</div>
                                         </div>
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('责任人', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">李四</div>
                                         </div>
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('已报选手：', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">18</div>
                                         </div>
                                     </div>  
                                     <div class="match_footer ta_r">
                                        <a class="clocs_match c_black6"><span class="c_blue"><i class="iconfont fs_20">&#xe652;</i></span><span class="ml_10">关闭考级</span></a>
                                        <a href="<?=home_url('/zone/kaojiBuild/');?>" class="edit_match c_black6"><span class="c_blue"><i class="iconfont fs_20">&#xe654;</i></span><span class="ml_10">编辑考级</span></a>
                                        <a class="end_match c_black6"><span class="c_orange"><i class="iconfont">&#xe6a1;</i></span><span class="ml_10">结束考级</span></a>
                                    </div>
                                </li> 
                            </ul>
                        </div>
                        <!-- 近期考级 -->
                        <div class="layui-tab-item">
                            <ul class="flow-default layui-row" id="2" style="margin:0">
                                <li class="match_row"> 
                                     <div class="bold c_black f_16 mt_10">国际速读水平测试（南京）</div>
                                     <div class="match_body">
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('考级日期：', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">2018-08-08 09:32 <span class="c_blue ml_10">考级中</span></div>
                                         </div>
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('考级地点：', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">四川省成都市武侯区航空路丰德万瑞25楼</div>
                                         </div>
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('责任人', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">李四</div>
                                         </div>
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('已报选手：', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">18</div>
                                         </div>
                                     </div>  
                                     <div class="match_footer ta_r">
                                        <a class="clocs_match c_black6"><span class="c_blue"><i class="iconfont fs_20">&#xe652;</i></span><span class="ml_10">关闭考级</span></a>
                                        <a href="<?=home_url('/zone/kaojiBuild/');?>" class="edit_match c_black6"><span class="c_blue"><i class="iconfont fs_20">&#xe654;</i></span><span class="ml_10">编辑考级</span></a>
                                    </div>
                                </li> 
                            </ul>
                        </div>
                        <!-- 往期考级 -->
                        <div class="layui-tab-item">
                            <ul class="flow-default layui-row" id="3" style="margin:0">
                                <li class="match_row"> 
                                     <div class="bold c_black f_16 mt_10">国际速读水平测试（南京）</div>
                                     <div class="match_body">
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('考级日期：', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">2018-08-08 09:32 <span class="c_blue ml_10">已结束</span></div>
                                         </div>
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('考级地点：', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">四川省成都市武侯区航空路丰德万瑞25楼</div>
                                         </div>
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('责任人', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">李四</div>
                                         </div>
                                         <div class="match_body_row">
                                             <div class="match_body_label"><?=__('已报选手：', 'nlyd-student')?></div>
                                             <div class="match_body_info c_black">18</div>
                                         </div>
                                     </div>  
                                     <div class="match_footer ta_r">
                                        <a class="clocs_match c_black6"><span class="c_blue"><i class="iconfont fs_20">&#xe652;</i></span><span class="ml_10">关闭考级</span></a>
                                        <a href="<?=home_url('/zone/kaojiBuild/');?>" class="edit_match c_black6"><span class="c_blue"><i class="iconfont fs_20">&#xe654;</i></span><span class="ml_10">编辑考级</span></a>
                                    </div>
                                </li>    
                            </ul>
                        </div>
                    </div>
                </div>    
            </div>
        </div>  
        <?php }else{ ?>
        <style>
            @media screen and (max-width: 1199px){
                #page {
                    top: 0;
                }
            }

        </style>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper layui-bg-white">
                <div class="layui-row nl-border nl-content">
                    <div class="no-info-page">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noMatch1042@2x.png'?>">
                        </div>
                        <p class="no-info-text"><?=__('无考级信息', 'nlyd-student')?></p>
                    </div>
                </div>
            </div>
        <?php } ?>  
        <a class="a-btn" href="<?=home_url('/zone/kaojiBuild/');?>"><?=__('发布新的考级', 'nlyd-student')?></a>   
    </div>
</div>
<script>
    
jQuery(function($) { 
    $('body').on('click','.nl-match-button button',function(){
        var _this=$(this);
        var href=_this.attr('href');
        if(href){
            _this.addClass('opacity')
            setTimeout(function(){
                _this.removeClass('opacity')
            }, 100);
            window.location.href=href;
        }
    })
    layui.use(['element','flow'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        var flow = layui.flow;//流加载
    //     function pagation(id,match_page){
    //         flow.load({
    //             elem: '#'+id //流加载容器
    //             ,isAuto: false
    //             ,isLazyimg: true
    //             ,done: function(page, next){ //加载下一页
    //                 var postData={
    //                     action:'get_match_list',
    //                     _wpnonce:$('#inputMatch').val(),
    //                     page:match_page,
    //                     match_type:'',
    //                 }
    //                 if(parseInt(id)==1){//报名
    //                     postData['match_type']="signUp";
    //                 }else if(parseInt(id)==2){//考级
    //                     postData['match_type']="matching";
    //                 }else{//往期
    //                     postData['match_type']="history";
    //                 }
    //                 var lis = [];
    //                 $.ajax({
    //                     data: postData,
    //                     success:function(res,ajaxStatu,xhr){
    //                         console.log(res)
    //                         match_page++
    //                         isClick[id]=true
    //                         if(res.success){
    //                             $.each(res.data.info,function(i,v){
    //                                 // 已结束-3
    //                                 // 等待考级-2
    //                                 // 未开始-1
    //                                 // 报名中1
    //                                 // 进行中2
    //                                 var isMe='';//标签
    //                                 var match_status='c_blue';//考级中高亮
    //                                 var rightBtn='';  
    //                                 var endTime="";//报名截止
    //                                 var domTime=v.entry_end_time.replace(/-/g,'/');
    //                                 var end_time = new Date(domTime).getTime();//月份是实际月份-1
    //                                 var serverTimes=new Date(xhr.getResponseHeader('Date')).getTime()
    //                                 var sys_second = (end_time-serverTimes)/1000;
    //                                 var sys_second_text=sys_second>0 ? '' :  "<?=__('报名结束', 'nlyd-student')?>";
    //                                 var match_notice_url='';//参赛须知
    //                                 if(v.user_id!=null){//我报名参加的赛事
    //                                     isMe='<div class="nl-badge"><i class="iconfont">&#xe608;</i></div>'
    //                                 }
    //                                 if(v.match_status==2 || v.match_status==-2){//考级进行中或等待考级
    //                                     if(v.match_status==2){
    //                                         match_status='c_orange';  
    //                                     }
    //                                     if(v.user_id==null){//未报名（未登录）
    //                                         rightBtn=""
    //                                     }else{
    //                                         if(v.right_url.length>0){
    //                                             rightBtn='<div class="nl-match-button flex1 last-btn">'
    //                                                         +'<button type="button" href="'+v.right_url+'">'+v.button_title+'</button>'
    //                                                     +'</div>'
    //                                         }
    //                                     }
    //                                     endTime='<div class="nl-match-detail layui-row">'
    //                                                 +'<div class="nl-match-label"><div><?=__('报名截止', 'nlyd-student')?>:</div></div>'
    //                                                 +'<div class="nl-match-info">'
    //                                                     +'<span class="c_black getTimes'+id+'" data-seconds="'+sys_second+'">'
    //                                                     +sys_second_text+'</span>'
    //                                                 +'</div>'
    //                                             +'</div>'
    //                                 }else if(v.match_status==-3){//已结束
    //                                     rightBtn='<div class="nl-match-button flex1 last-btn">'
    //                                                 +'<button type="button" href="'+v.right_url+'"><?=__('查看战绩', 'nlyd-student')?></button>'
    //                                             +'</div>';   
    //                                 }else{
    //                                     if(v.right_url.length>0){
    //                                         rightBtn='<div class="nl-match-button flex1 last-btn">'
    //                                                     +'<button type="button" class="not_sign" data-id="'+v.ID+'" href="'+v.right_url+'">'+v.button_title+'</button>'
    //                                                 +'</div>'
    //                                         if(v.match_status==1 && v.user_id!=null){//报名中已报名
    //                                             rightBtn='<div class="nl-match-button flex1 last-btn">'
    //                                                         +'<button type="button" class="bg_gradient_grey"><?=__('已报名参赛', 'nlyd-student')?></button>'
    //                                                     +'</div>'
                                                
    //                                         }
    //                                     }
    //                                     endTime='<div class="nl-match-detail layui-row">'
    //                                                 +'<div class="nl-match-label"><div><?=__('报名截止', 'nlyd-student')?>:</div></div>'
    //                                                 +'<div class="nl-match-info">'
    //                                                     +'<span class="c_black getTimes'+id+'" data-seconds="'+sys_second+'">'
    //                                                     +sys_second_text+'</span>'
    //                                                 +'</div>'
    //                                             +'</div>'
    //                                 }
    //                                 if(v.match_notice_url && v.match_notice_url.length>0){//参赛须知
    //                                     match_notice_url='<a class="c_orange" style="margin-left:10px" href="'+v.match_notice_url+'"><?=__('参赛须知', 'nlyd-student')?></a>'
    //                                 }
    //                                 var onBtn="" ;
    //                                 if(rightBtn.length==0){
    //                                     onBtn="onBtn"
    //                                 }
    //                                 var dom='<li class="layui-col-lg4 layui-col-sm12 layui-col-xs12 layui-col-md12">'
    //                                             +'<div class="nl-match">'
    //                                                 +'<div class="nl-match-header">'
    //                                                     +'<span class="nl-match-name fs_16 c_blue">'+v.post_title+'</span>'
    //                                                     +isMe
    //                                                     +'<p class="long-name fs_12 c_black8">'+v.post_content+'</p>'
    //                                                 +'</div>'
    //                                                 +'<div class="nl-match-body">'
    //                                                     +'<div class="nl-match-detail layui-row">'
    //                                                         +'<div class="nl-match-label"><div><?=__('考级日期', 'nlyd-student')?>:</div></div>'
    //                                                         +'<div class="nl-match-info">'
    //                                                             +'<span class="c_black">'+v.match_start_time+'</span>'
    //                                                             +'<span class="nl-match-type '+match_status+'">'+v.match_status_cn+'</span>'
    //                                                         +'</div>'
    //                                                     +'</div>'
    //                                                     +'<div class="nl-match-detail layui-row">'
    //                                                         +'<div class="nl-match-label"><div><?=__('考级地点', 'nlyd-student')?>:</div></div>'
    //                                                         +'<div class="nl-match-info">'
    //                                                             +'<span class="c_black">'+v.match_address+'</span>'
    //                                                         +'</div>'
    //                                                     +'</div>'
    //                                                     +'<div class="nl-match-detail layui-row">'
    //                                                         +'<div class="nl-match-label"><div><?=__('报名费用', 'nlyd-student')?>:</div></div>'
    //                                                         +'<div class="nl-match-info">'
    //                                                             +'<span class="c_black">¥'+v.match_cost+'</span>'
    //                                                         +'</div>'
    //                                                     +'</div>'
    //                                                     +endTime
    //                                                     +'<div class="nl-match-detail layui-row">'
    //                                                         +'<div class="nl-match-label"><div><?=__('已报选手', 'nlyd-student')?>:</div></div>'
    //                                                         +'<div class="nl-match-info">'
    //                                                             +'<span class="c_black">'+v.entry_total+'<?=__('人', 'nlyd-student')?></span>'
    //                                                             +match_notice_url
    //                                                         +'</div>'
    //                                                     +'</div>'
    //                                                 +'</div>'
    //                                                 +'<div class="nl-match-footer flex-h">'
    //                                                     +'<div class="nl-match-button flex1">'
    //                                                         +'<button type="button" class="'+onBtn+'" href="'+v.left_url+'"><?=__('查看详情', 'nlyd-student')?></button>'
    //                                                     +'</div>'
    //                                                     +rightBtn
    //                                                 +'</div>'
    //                                             +'</div>'
    //                                         +'</li>'
    //                                 lis.push(dom) 
    //                             })
    //                             if (res.data.info.length<50) {
    //                                 next(lis.join(''),false) 
    //                             }else{
    //                                 next(lis.join(''),true) 
    //                             }
                                
    //                         }else{
    //                             next(lis.join(''),false)
    //                         }
                        
    //                         $('.getTimes'+id).countdown(function(S, d){//倒计
    //                                 var D=d.day>0 ? d.day+'<?=__('天', 'nlyd-student')?>' : '';
    //                                 var h=d.hour<10 ? '0'+d.hour : d.hour;
    //                                 var m=d.minute<10 ? '0'+d.minute : d.minute;
    //                                 var s=d.second<10 ? '0'+d.second : d.second;
    //                                 var time=D+h+':'+m+':'+s;
    //                                 $(this).text(time);
    //                             if(S==0){
    //                                 $(this).text("<?=__('报名结束', 'nlyd-student')?>");
    //                                 setTimeout(function() {
    //                                     window.location.reload()  
    //                                 }, 1000);
    //                             }
    //                         });
    //                     },
    //                     complete:function(XMLHttpRequest, textStatus){
	// 						if(textStatus=='timeout'){
	// 							$.alerts('<?=__('网络质量差,请重试', 'nlyd-student')?>')
	// 							next(lis.join(''),true)
	// 						}
    //                     }
    //                 })       
    //             }
    //         });
    //     }
    //     var isClick={}
    // //    if(location.hash.replace(/^#matchList=/, '').length==0){
    // //        //获取hash来切换选项卡，假设当前地址的hash为lay-id对应的值
    // //        location.hash = 'matchList='+ <?=$anchor?>;
    // //    }
    //     var layid = location.hash.replace(/^#matchList=/, '');
     
    //     element.tabChange('matchList', layid);
    //     pagation($('.layui-this').attr('lay-id'),1)
    //     var lefts=$('.layui-this').position().left;
    //     $('.nl-transform').css({
    //         'transform':'translate3d('+lefts+'px, -5px, 0px)'
    //     })
        element.on('tab(matchList)', function(){//matchList
            // location.hash = 'matchList='+ $(this).attr('lay-id');
            var left=$(this).position().left+parseInt($(this).css('marginLeft'));
            var id=$(this).attr('lay-id')
            $('.nl-transform').css({
                'transform':'translate3d('+left+'px, 0px, 0px)'
            })
            // if(!isClick[id]){
            //     pagation(id,1)
            // }
        });
    })
})
</script>