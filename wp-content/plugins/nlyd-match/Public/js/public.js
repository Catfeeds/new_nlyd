jQuery(document).ready(function($) {
    var array = _array.data;
    var parentArray = [
        $('#toplevel_page_teacher'),
        $('#toplevel_page_order'),
        $('#toplevel_page_brainpower'),
        $('#toplevel_page_feedback'),
        $('#menu-posts-team'),
        $('#menu-posts-match'),
        $('#menu-posts-grading'),
        $('#menu-users'),
        $('#toplevel_page_fission'),
    ];
    $.each(parentArray,function (pi,pv) {
        $.each($(pv).find('a'), function (i,v) {
            if($.inArray($(v).prop('href'),array) != -1){
                $(v).closest('li').css('display', 'none');
            }
        })
    });
});