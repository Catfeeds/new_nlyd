
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('赛程时间表', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content  have-bottom">
                <div class="width-padding layui-row width-margin-pc">
                    <div class="c_red fs_12 mt_10"><?=__('每轮比赛间隔3分钟,每个项目间隔10分钟，管理员可根据实际情况进行修改。', 'nlyd-student')?></div>
                    <div class="match_time_row">
                        <div class="match_xiang_name">
                            <span class="bold fs_16 c_black mr_10">数字争霸</span>
                            <span class="fs_12 mr_10">20分钟/每轮</span>
                            <a class="add_lun c_black6 pull-right">
                                <div class="add_coin bg_gradient_blue">+</div>
                                <div class="add_text fs_12"><?=__('新增1轮', 'nlyd-student')?></div>
                            </a>
                        </div>

                        <div class="add_lun_row">
                            <span class="close_coin bg_gradient_orange mr_10">+</span>
                            <span class="mr_10">第1轮</span>
                            <a class="c_blue match_date" start-time="2019,12,20,09,30">修改开始时间</a>
                            <br>
                            <span class="mr_10 c_black ff_num">2019/12/20 09:30</span>
                            <span class="mr_10 c_black">至</span>
                            <span class="mr_10 c_black ff_num">2018/12/12 17:50</span>
                        </div>
                        <div class="add_lun_row">
                            <span class="close_coin bg_gradient_orange mr_10">+</span>
                            <span class="mr_10">第1轮</span>
                            <a class="c_blue match_date" start-time="2019,1,1,09,30">修改开始时间</a>
                            <br>
                            <span class="mr_10 c_black ff_num">2019/1/2019 09:30</span>
                            <span class="mr_10 c_black">至</span>
                            <span class="mr_10 c_black ff_num">2018/12/12 17:50</span>
                        </div>
                    </div>

                    <div class="match_time_row">
                        <div class="match_xiang_name">
                            <span class="bold fs_16 c_black mr_10">数字争霸</span>
                            <span class="fs_12 mr_10">20分钟/每轮</span>
                            <a class="add_lun c_black6 pull-right">
                                <div class="add_coin bg_gradient_blue">+</div>
                                <div class="add_text fs_12"><?=__('新增1轮', 'nlyd-student')?></div>
                            </a>
                        </div>

                        <div class="add_lun_row">
                            <span class="close_coin bg_gradient_orange mr_10">+</span>
                            <span class="mr_10">第1轮</span>
                            <a class="c_blue match_date" start-time="2018,12,22,09,30">修改开始时间</a>
                            <br>
                            <span class="mr_10 c_black ff_num">2018/12/22 09:30</span>
                            <span class="mr_10 c_black">至</span>
                            <span class="mr_10 c_black ff_num">2018/12/12 17:50</span>
                        </div>
                        <div class="add_lun_row">
                            <span class="close_coin bg_gradient_orange mr_10">+</span>
                            <span class="mr_10">第1轮</span>
                            <a class="c_blue match_date" start-time="2018,12,20,09,30">修改开始时间</a>
                            <br>
                            <span class="mr_10 c_black ff_num">2018/12/20 09:30</span>
                            <span class="mr_10 c_black">至</span>
                            <span class="mr_10 c_black ff_num">2018/12/12 17:50</span>
                        </div>
                    </div>
                </div>
            </div>
            <a class="a-btn a-btn-table"><div><?=__('保存更新', 'nlyd-student')?></div></a>
            <div id="match_date" style="display:none"></div>
        </div>            
    </div>
</div>
<script>
jQuery(function($) { 
    var match_date_Data=$.validationLayui.dates2;//开赛日期
    var posiotion_match_date=[0,0,0,0,0];//初始化位置，高亮展示
    var dateValue=[];
    
    $('.match_date').each(function(_index){
        var _this=$(this);
        dateValue=_this.attr('start-time').split(',');
        _this.attr('id','match_date'+_index)
    })

    $('.match_date').click(function(){
        var _this=$(this);
        var id=_this.attr('id')
        // dateValue=_this.attr('start-time').split(',');
        // $.each($.validationLayui.dates2,function(index,value){
        //     if(dateValue[0]==value.value){
        //         $.each(value.childs,function(i,v){
        //             if(dateValue[1]==v.value){
        //                 $.each(v.childs,function(j,val){
        //                     if(dateValue[2]==val.value){
        //                         $.each(v.childs,function(k,b){
        //                             if(dateValue[3]==b.value){
        //                                 $.each(v.childs,function(l,c){
        //                                     if(dateValue[4]==c.value){
        //                                         posiotion_match_date=[index,i,j,k,l];
        //                                     }
        //                                 })
        //                             }
        //                         })
        //                     }
        //                 })
        //             }
        //         })
        //     }
        // })
     
        $('#match_date').attr('from-id',id).click();
    })

     new MobileSelect({
        trigger: '#match_date',
        title: '<?=__('开赛日期', 'nlyd-student')?>',
        wheels: [
            {data: match_date_Data}
        ],
        position:posiotion_match_date, //初始化定位 打开时默认选中的哪个 如果不填默认为0
        transitionEnd:function(indexArr, data){
            // console.log(data);
        },
        callback:function(indexArr, data){
            var el=$(this.trigger);
            var from_id=el.attr('from-id');
            console.log($('#'+from_id))
            // var text=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']+'-'+data[3]['value']+'-'+data[4]['value'];
            // $('#match_date').val(text);
        
        }
    });
})
</script>
