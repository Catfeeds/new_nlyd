jQuery(document).ready(function($) {
    var template =
        '<div style="position: relative; top: 0;">' +
        '<div id="alert_title">{title}</div>' +
        '<div id="alert_content">{content}</div>' +
        '' +
        '</div>';

    function myAlert(title, content, color){
        template.replace('{title}', title);
    }
});
