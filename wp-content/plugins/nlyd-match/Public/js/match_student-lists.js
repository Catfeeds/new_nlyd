jQuery(document).ready(function($) {
    $('.joinMatch').on('click', function () {
        var mid = $(this).attr('data-id');
        var uid = $(this).attr('data-uid');
        var _wpnonce = $('#getWpnonce').val();
        if(!mid || !uid) return false;
        $.ajax({
            data : {'action' : 'joinMatch', 'mid' : mid, '_wpnonce' : _wpnonce, 'uid' : uid},
            dataType : 'json',
            type : 'post',
            url : ajax_url,
            success : function (response) {
                alert(response.data.info);
            }
        });

    });
    /**
     * 确认脑力健将
     */
    $('#enterBrainpower').on('click', function () {
        var type = $('#brainpowerType').val(); //类型
        var category = $('#brainpowerCate').val(); //类别
        var val = $('#brainpowerVal').val(); // 数值

    });
})