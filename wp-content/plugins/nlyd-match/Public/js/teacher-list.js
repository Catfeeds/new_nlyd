jQuery(document).ready(function($) {
    $('tbody').on('mouseover', 'tr', function () {
        $(this).find('.options').css('left', 0);
    }).on('mouseout', 'tr', function () {
        $(this).find('.options').css('left', '-999px');
    });
})