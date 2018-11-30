
jQuery(document).ready(function($) {
    var eventToken = 1;
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
        if(eventToken == 0) return false;
        eventToken = 0;
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
                eventToken = 1;
            },
            error : function () {
                eventToken = 1;
            }
        });
    }

    $('.check-all').on('click', function () {
       var status = $(this).find('input[type="checkbox"]').prop('checked');

       $('.check').find('input[type="checkbox"]').prop('checked', status);
       $('.check-all').find('input[type="checkbox"]').prop('checked', status);
    });
    /**
     * 解除教学关系
     */
    $('.relieve').on('click', function () {
        var _tr = $(this).closest('tr');
        var _options_div = _tr.find('.option-child');
        _options_div.show();
        _options_div.find('.cancel-option').off('click').on('click',function () {
            _tr.find('.option-child').hide();
        });
        _options_div.find('.confirm-option').off('click').on('click',function () {
            var val = [];
            $.each(_options_div.find('input[type="checkbox"]:checked'),function (i,v) {
                val[i]=$(v).val();
            });
            $.ajax({
                data : {'action' : 'relieveMyStudent', 'id' : val},
                type : 'post',
                dataType : 'json',
                url : ajax_url,
                success : function (response) {
                    alert(response.data.info)
                    if(response.success){
                        window.location.reload();
                    }
                    eventToken = 1;
                },error : function () {
                    eventToken = 1;
                }
            });
        });








        return false;
        if(eventToken == 0) return false;
        eventToken = 0;
        var id = $(this).closest('tr').attr('data-id');

        $.ajax({
            data : {'action' : 'relieveMyStudent', 'id' : id},
            type : 'post',
            dataType : 'json',
            url : ajax_url,
            success : function (response) {
                alert(response.data.info)
                if(response.success){
                    window.location.reload();
                }
                eventToken = 1;
            },error : function () {
                eventToken = 1;
            }
        });
    });
});