jQuery(document).ready(function($) {
    $('.agree').on('click', function () {
        var id = $(this).closest('tr').attr('data-id');
        var status = $(this).closest('div').attr('data-status');
        post_ajax(id,status,1);
    });
    $('.refuse').on('click', function () {
        var id = $(this).closest('tr').attr('data-id');
        var status = $(this).closest('div').attr('data-status');
        post_ajax(id,status,2);
    });
    $('.all-btn').on('click', function () {
        var status = $(this).prev().val();
        var teamStatus,type;

        switch (status){
            case '1':
                //同意入队
                type = 1;
                teamStatus =  1;
                break;
            case '2':
                //拒绝入队
                type = 2;
                teamStatus =  1;
                break;
            case '3':
                //同意退队
                type = 1;
                teamStatus =  -1;
                break;
            case '4':
                //驳回退队
                teamStatus =  -1;
                type = 2;
                break;
            default:
                return false;
        }
        var arr = [];
        $.each($('.check-column'), function (i, v) {
            if($(v).find('.check').prop('checked')){
                arr.push($(v).find('.check').val());
            }
        });
        post_ajax(arr,teamStatus,type);
    });
    function post_ajax(id,status,type) {
        $.ajax({
            url : ajax_url,
            data : {'action' : 'matchTeamApplyStatus','id':id,'status':status,'type':type},
            dataType : 'json',
            type : 'post',
            success : function(response){
                alert(response['data']['info']);
                if(response['success']){
                    window.location.reload();
                }
            }
        });
    }

    //踢出战队
    $('.expel').on('click', function () {
        if(!confirm('是否确定将此成员踢出战队?')) return false;
        var id = $(this).closest('tr').attr('data-id');
        $.ajax({
            data : {'action' : 'expelTeam','id':id},
            dataType : 'json',
            type : 'post',
            url : ajax_url,
            success : function (response) {
                alert(response['data']['info']);
                if(response['success']){
                    window.location.reload();
                }
            }
        });
    })
});