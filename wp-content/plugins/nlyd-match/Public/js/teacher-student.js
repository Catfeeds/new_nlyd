
jQuery(document).ready(function($) {
    $('#tab').on('mouseover', 'li', function () {
        $(this).css({'background-color': '#23282d', 'color' : '#ffffff'});
    }).on('mouseout', 'li', function () {
        if(!$(this).is('.active')){
            $(this).css({'background-color': '#ffffff', 'color' : '#000000'});
        }
    });
    $('.apply_option').on('click', '.agree', function () {
        var id = $(this).closest('tr').attr('data-id');
        post_ajax(id,2);
    }).on('click', '.refuse', function () {
        var id = $(this).closest('tr').attr('data-id');
        post_ajax(id,-1);
    });
    $('.batch-btn').off('click').on('click', function () {
        var status = $(this).prev().val();
        if(status == false) return false;
        var arr = [];
        $.each($('.check'), function (i, v) {
            if($(v).find('input[type="checkbox"]').prop('checked') == true) {
                arr.push($(v).closest('tr').attr('data-id'));
            }
        });
        post_ajax(arr, status);
    });

    function post_ajax(id,sta) {
        $.ajax({
            url : ajax_url,
            data : {'id':id, 'action' : 'coachApplyStatus', 'status': sta},
            dataType : 'json',
            type : 'post',
            success : function (response, responseStatus) {
                alert(response.data.info)
                if(response.success){
                    window.location.reload();
                }
            }
        });
    }

    $('.check-all').on('click', function () {
       var status = $(this).find('input[type="checkbox"]').prop('checked');

       $('.check').find('input[type="checkbox"]').prop('checked', status);
       $('.check-all').find('input[type="checkbox"]').prop('checked', status);
    });
});