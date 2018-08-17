
jQuery(document).ready(function($) {
    $('#interfaceSub').click(function () {
        var query = $('#interface').serialize();

        $.post(ajaxurl,query, function(data) {

            alert(data.data);
        },'json');
        return false;
    })
    $('.tabsBt').on('click',function () {
        $('.form-table').css('display','none')
        $(this).next('.form-table').css('display','block')
    })
})
