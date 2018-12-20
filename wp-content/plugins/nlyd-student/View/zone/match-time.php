
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
                    <?php if(!empty($list)):?>
                    <?php foreach ($list as $k => $val){ ?>
                    <div class="match_time_row" data-title="<?=$val['title']?>">
                        <div class="match_xiang_name">
                            <span class="bold fs_16 c_black mr_10"><?=$val['title']?></span>
                            <span class="fs_12 mr_10"><span class="intervalTime"><?=$val['use_time']?></span><?=__('分钟/每轮', 'nlyd-student')?></span>
                            <a class="add_lun c_black6 pull-right">
                                <div class="add_coin bg_gradient_blue">+</div>
                                <div class="add_text fs_12 match_date add_new_lun" data-project="<?=$k?>"><?=__('新增1轮', 'nlyd-student')?></div>
                            </a>
                        </div>
                        <?php if(!empty($val['child'])): ?>
                        <?php foreach ($val['child'] as $v){
                            $data_time = preg_replace('/\/|\s|:/',',',$v['start_time']);
                        ?>
                        <div class="add_lun_row">
                            <span class="close_coin bg_gradient_orange mr_10">+</span>
                            <span class="mr_10"><?=__('第', 'nlyd-student')?><span class="project_more"><?=$v['more']?></span><?=__('轮', 'nlyd-student')?></span>
                            <a class="c_blue match_date edit_time" data-id ="<?=$v['id']?>" start-time="<?=$v['start_time']?>"  data-time="<?=$data_time?>"><?=__('修改开始时间', 'nlyd-student')?></a>
                            <br>
                            <span class="start_time mr_10 c_black ff_num"><?=$v['start_time']?></span>
                            <span class="mr_10 c_black"><?=__('至', 'nlyd-student')?></span>
                            <span class="end_time mr_10 c_black ff_num"><?=$v['end_time']?></span>
                        </div>
                        <?php } ?>
                        <?php endif;?>
                    </div>
                    <?php } ?>
                    <?php endif;?>
                </div>
            </div>
            <a class="a-btn a-btn-table submit"><div><?=__('保存更新', 'nlyd-student')?></div></a>
        </div>            
    </div>
</div>
<script>
jQuery(function($) { 
    var match_date_Data=$.validationLayui.dates2;//开赛日期
    var  posiotion_match_date={};//初始化位置，高亮展示
    var mobileSelect={}

    function judgFailTime(startTime,interval) {
        var time = new Date(startTime.replace("-","/"));
        time.setMinutes(time.getMinutes() + interval, time.getSeconds(), 0);
        return time;
    }
    $('.match_date').each(function(_index){
        var _this=$(this);
        var title="<?=__('开赛日期', 'nlyd-student')?>";
        _this.attr('id','match_date'+_index)
        var data_time=_this.attr('data-time');
        var use_time=_this.parents('.match_time_row').find('.intervalTime').text()
        // console.log(data_time)
        if(!_this.hasClass('add_new_lun') && data_time && data_time.length>0){
            var timeValue=data_time.split(',');
            $.each($.validationLayui.dates2,function(index,value){
                if(timeValue[0]==value.value+""){
                    posiotion_match_date[_index]=[index,0,0,0,0];
                    $.each(value.childs,function(i,v){
                        if(timeValue[1]==v.value+""){
                            posiotion_match_date[_index]=[index,i,0,0,0];
                            $.each(v.childs,function(j,val){
                                if(timeValue[2]==val.value+""){
                                    posiotion_match_date[_index]=[index,i,j,0,0];
                                    
                                    $.each(val.childs,function(k,b){
                                        if(timeValue[3]==b.value+""){
                                            posiotion_match_date[_index]=[index,i,j,k,0];
                                            $.each(b.childs,function(l,c){
                                                if(timeValue[4]==c.value+""){
                                                    posiotion_match_date[_index]=[index,i,j,k,l];
                                                }
                                            })
                                        }
                                    })
                                }
                            })
                        }
                    })
                }
            })
        }else {
            title="<?=__('新增轮数', 'nlyd-student')?>";
        } 
        mobileSelect['match_date'+_index]=new MobileSelect({
            trigger: '#match_date'+_index,
            title: "<?=__('开赛日期', 'nlyd-student')?>",
            wheels: [
                {data: match_date_Data}
            ],
            triggerDisplayData:false,
            position: posiotion_match_date[_index], //初始化定位 打开时默认选中的哪个 如果不填默认为0
            transitionEnd:function(indexArr, data){

            },
            callback:function(indexArr, data){
                var start_time=data[0]['value']+'-'+data[1]['value']+'-'+data[2]['value']+' '+data[3]['value']+':'+data[4]['value'];
                if(!_this.hasClass('add_new_lun')){//修改时间
                    var old_start_time=_this.attr('start-time');
                    if(old_start_time!=start_time.replace(/-/g,'/')){//时间已修改
                        _this.parent('.add_lun_row').find('.start_time').text(start_time);
                        var _time=judgFailTime(start_time,use_time);
                        var end_time=_time.Format("yyyy-MM-dd hh:mm");
                        _this.parent('.add_lun_row').find('.end_time').text(end_time);
                        _this.parents('.match_time_row').find('.add_new_lun').attr('cantChoose',true);//选中时间后判断是否可选
                    }else{
                        _this.parents('.match_time_row').find('.add_new_lun').attr('cantChoose',false)//提交时判断新增还是编辑
                    }
                }else {//新增轮数
                    if(_this.attr('cantChoose')){
                        $.alerts("<?=__('此项目已经新修改时间,请先保存再新增轮数', 'nlyd-student')?>");    
                    }else{
                        if(!_this.hasClass("disabled")){
                            var project_more=_this.parents('.match_time_row').find('.add_lun_row').length;
                            project_more+=1;
                            var _time=judgFailTime(start_time,use_time);
                            var end_time=_time.Format("yyyy-MM-dd hh:mm"); 
                            var project_id=_this.attr('data-project')
                            var addRow={
                                action:'add_match_time',
                                project_id:project_id,
                                start_time:start_time,
                                end_time:end_time,
                                match_id:$.Request('match_id'),
                                project_more:project_more,
                                use_time:use_time,
                                match_type:'match'
                            }
                            
                            $.ajax({
                                data: addRow,
                                beforeSend:function(XMLHttpRequest){
                                    _this.addClass('disabled')
                                },
                                success: function(res, textStatus, jqXHR){
                                    if(res.success){
                                        if(res.data.url){
                                            window.location.reload()
                                        }else{
                                            $.alerts(res.data.info)
                                        }
                                    }else{
                                        $.alerts(res.data.info)
                                    }
                                },
                                complete: function(jqXHR, textStatus){
                                    if(textStatus=='timeout'){
                                        $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
                            　　　　 }
                                    _this.removeClass('disabled');
                                }
                            })
                        }else{
                            $.alerts("<?=__('正在新增轮数，请稍后再试', 'nlyd-student')?>")
                        }
                    }
                }
            }
        });
    })
    $('.submit').click(function () {
        var data=[];
        var _this=$(this);
        if(!_this.hasClass('disabled')){
            $('.match_date').each(function () {
                var __this=$(this);
                if(__this.hasClass('edit_time')){
                    var start_time=__this.parent('.add_lun_row').find('.start_time').text();
                    var end_time=__this.parent('.add_lun_row').find('.end_time').text();
                    var project_title=__this.parents('.match_time_row').attr('data-title');
                    var project_more=__this.parent('.add_lun_row').find('.project_more').text();
                    var id=__this.attr('data-id');
                    // var old_start_time=_this.attr('start-time');
                    var row={
                        id:id,
                        start_time:start_time,
                        end_time:end_time,
                        project_title:project_title,
                        project_more:project_more,
                    };
                    // if(old_start_time!=start_time.replace(/-/g,'/')){
                    data.push(row)
                }
                // }
            });
            var postData={
                action:'update_match_time',
                data:data,
                match_id:$.Request('match_id'),
                match_type:'match'
            }
            $.ajax({
                data: postData,
                beforeSend:function(XMLHttpRequest){
                    _this.addClass('disabled')
                },
                success: function (res, ajaxStatu, xhr) {
                    if(res.success){
                        if(res.data.url){
                            window.location.reload()
                        }else{
                            $.alerts(res.data.info)
                        }
                    }else{
                        $.alerts(res.data.info)
                    }
                },
                complete: function(jqXHR, textStatus){
                    if(textStatus=='timeout'){
                        $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
            　　　　}
                    _this.removeClass('disabled');
                }
            })
        }
    })
})
</script>
