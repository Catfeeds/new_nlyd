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
    layui.use(['laydate','form',], function(){
        var laydate = layui.laydate;
        var form = layui.form
      //日期时间选择器
      $('.date-picker').each(function(){
          var id=$(this).attr('id');
            laydate.render({
                elem: '#'+id
                ,type: 'datetime'
            });
      })
    })
});