
jQuery(document).ready(function($) {
    var eventToken = 1;
    $('#tab').on('mouseover', 'li', function () {
        $(this).css({'background-color': '#23282d', 'color' : '#ffffff'});
    }).on('mouseout', 'li', function () {
        if(!$(this).is('.active')){
            $(this).css({'background-color': '#ffffff', 'color' : '#000000'});
        }
    });

    $('.batch-btn').off('click').on('click', function () {
        var _div = $(this).closest('div');
        var status = _div.find('.all_select').val();
        var category = [];
        var coach_id = _div.closest('form').attr('data-cid');
        $.each(_div.find('input[type="checkbox"]:checked'),function (i,v) {
            category[i]=$(v).val();
        });
        if(status == false) return false;
        var user_id = [];
        $.each($('.check'), function (i, v) {
            if($(v).find('input[type="checkbox"]').prop('checked') == true) {
                user_id.push($(v).closest('tr').attr('data-uid'));
            }
        });
        post_ajax(coach_id, status, category.join(),user_id.join());
    });

    function post_ajax(coach_id,sta,category,user_id) {
        if(eventToken == 0) return false;
        eventToken = 0;
        $.ajax({
            url : ajax_url,
            data : {'coach_id':coach_id, 'action' : 'coachApplyStatus', 'status': sta,'category':category,'user_id':user_id},
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
     * 通过审核
     */
    $('.agree').on('click',function () {
        options_click($(this),2,'通过');
    });
    /**
     * 拒绝审核
     */
    $('.refuse').on('click',function () {
        options_click($(this),-1,'拒绝');
    });
    
    /**
     * 解除教学关系
     */
    $('.relieve').on('click', function () {
        options_click($(this),3,'解除');
    });

    function options_click(_this,status,btn_name) {
        var _tr = _this.closest('tr');
        var _options_div = _tr.find('.option-child');
        _options_div.show();
        _options_div.find('.confirm-option').text('确定'+btn_name);
        _options_div.find('.cancel-option').off('click').on('click',function () {
            _tr.find('.option-child').hide();
        });
        _options_div.find('.confirm-option').off('click').on('click',function () {
            var coach_id = _this.closest('form').attr('data-cid');
            var user_id = _this.closest('tr').attr('data-uid');

            var val = [];
            $.each(_options_div.find('input[type="checkbox"]:checked'),function (i,v) {
                val[i]=$(v).val();
            });
            post_ajax(coach_id, status, val.join(),user_id);
            // $.ajax({
            //     data : {'action' : _action, 'id' : val},
            //     type : 'post',
            //     dataType : 'json',
            //     url : ajax_url,
            //     success : function (response) {
            //         alert(response.data.info)
            //         if(response.success){
            //             window.location.reload();
            //         }
            //         eventToken = 1;
            //     },error : function () {
            //         eventToken = 1;
            //     }
            // });
        });
    }
});