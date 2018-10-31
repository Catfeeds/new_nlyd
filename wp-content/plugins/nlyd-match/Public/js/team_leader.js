jQuery(document).ready(function($) {
    $('.team_leader_director').on('change', function () {
        var val = $(this).val();
        var team_id = $(this).attr('data-team');
        var sel = $(this).next();
        var data_type = $(this).attr('data-type');
        $.ajax({
            url : ajax_url,
            data : {'action': 'getMemberByWhere', 'val':val, 'team_id':team_id,'type':data_type},
            dataType : 'json',
            type : 'post',
            success : function (response) {
                var html = '';
                if(response.success){
                    $.each(response.data.info, function (i, v) {
                        html += '<option value="'+v.match_team_id+'">'+v.user_nicename+'</option>'
                    })
                }
                sel.html(html);
            }
        })
    });
    $('.confirmLeaderOrDirector').on('click', function () {
        var match_team_id = $(this).prev().val();
        var team_id = $(this).prev().prev().attr('data-team');
        var data_type = $(this).attr('data-type');
        var msg = $(this).next();
        // console.log(match_team_id);
        // console.log(team_id);
        msg.text('');
        $.ajax({
            url : ajax_url,
            data : {'action': 'setMatchTeamLeaderOrDirector', 'team_id':team_id, 'match_team_id':match_team_id, 'type':data_type},
            dataType : 'json',
            type : 'post',
            success : function (response) {
                if(response.success){
                    msg.css('color', '#2aa52e');
                }else{
                    msg.css('color', '#ff443c');
                }
                msg.text(response.data.info);
            }
        });
    });
 
	$( '#project_meta_box').DDSort({//拖拽
		target: '.match_project',		
        up:function(){
            $(this).parents('.postbox').find('input').each(function(){
                var _this=$(this);
                var index=_this.parents('.match_project').index();
                var oldName=_this.attr('name');
                if(oldName){
                    var n=0;
                    var pre="";
                    var next=""
                    for(var i=0;i<oldName.length;i++){
                        if(oldName.charAt(i)==']'){
                            n++
                            if(n==1){
                                pre=oldName.slice(0,i+1)
                            }
                            if(n==2){
                                next=oldName.slice(i+1)
                            }
                        }
                    }
                    _this.attr('name',pre+'['+index+']'+next);
                }
            })
        },
        down:function(){
		  return false
        },
        floatStyle: {
            'border': '1px solid #ccc',
            'background-color': '#fff'
        },
        cloneStyle: {
            'border': '1px solid #fff',
            'background-color': '#fff',
            'width':'100%',
            // 'height':'20px'
        },

	});
    layui.use(['laydate','form',], function(){
        var laydate = layui.laydate;
        var form = layui.form
      //日期时间选择器
      $('.date-picker').each(function(){
          var id=$(this).attr('id');
            laydate.render({
                elem: '#'+id
                ,type: 'datetime'
                ,format: 'yyyy-MM-dd HH:mm'
            });
      })
    })
    $('.remove_more').live('click',function(event){//删除
        var id = $(this).attr('data-id');
        $.post(ajaxurl,{action:'remove_match_more',id:id},function (data) {
            alert(data.data);
            history.go(0);
        },'json')
        return false;
    });
    $('.update_more').live('click',function(event){//编辑
        var _this=$(this);
        var project_id=_this.attr('data-project')
        var project_name=_this.attr('data-name');
        var title=project_name+_this.parent('li').find('.match_more').text();//弹框title
        var start_time=_this.parent('li').find('.start_time').text();//开始时间
        var end_time=_this.parent('li').find('.end_time').text()//结束时间
        var _time="";//比赛时常
        showForm(title)
        //表单数据
        // $('input:radio[name="status"]').removeAttr('checked')
        // $('.show_form .ayui-form-radio').removeClass('layui-form-radioed')
        $('#match_more_id').val($(this).attr('data-id'))
        $('input[name=project_id]').val(project_id)
        $('input[name=start_time]').val(start_time)
        $('input[name=end_time]').val(end_time)
        $('input[name=use_time]').val(_time)
 
        return false;
    });
    $('.add_new').live('click',function(event){//新增
        var _this=$(this);
        var project_id=_this.attr('data-project');
        var project_name=_this.attr('data-name');
        //表单数据清空
        $('#match_more_id').val('')
        // $('input:radio[name="status"]').removeAttr('checked')
        // $('.show_form .ayui-form-radio').removeClass('layui-form-radioed')
        $('input[name=project_id]').val(project_id)
        $('input[name=start_time]').val('')
        $('input[name=end_time]').val('')
        $('input[name=use_time]').val('')
        showForm(project_name+'新增轮数')
        return false;
    })
    function showForm(title) {
        layer.open({
            type: 1
            ,maxWidth:1000
            ,title: title //不显示标题栏
            ,skin:'nl-box-skin'
            ,id: 'certification1' //防止重复弹出
            ,content:$('.show_form')
            ,btn: ['按错了','提交',  ]
            ,success: function(layero, index){
                
            }
            ,yes: function(index, layero){
                layer.closeAll();
            }
            ,btn2: function(index, layero){
                //按钮【按钮二】的回调
                layer.closeAll();
                // var query = $('.add_more_form').serialize();
                var query={
                    action:$('input[name=action]').val(),
                    post_id:$('input[name=post_id]').val(),
                    project_id:$('input[name=project_id]').val(),
                    more_id:$('#match_more_id').val(),
                    start_time:$('input[name=start_time]').val(),
                    end_time:$('input[name=end_time]').val(),
                    use_time:$('input[name=use_time]').val(),
                    status:$('input[name=status]:checked').val()
                }
                console.log(query)
                $.post(ajaxurl,query,function (data) {
                    alert(data.data);
                    history.go(0);
                    /*setTimeout(function () {
                    },900)*/
                },'json')
            }
            ,closeBtn:2
            ,btnAagn: 'c' //按钮居中
            ,shade: 0.3 //遮罩
            ,isOutAnim:true//关闭动画
        });
       
    }


});