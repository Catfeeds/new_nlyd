jQuery(document).ready(function($) {
    $('.rem').on('click', function () {
        var id = $(this).closest('tr').attr('data-id');
        if(confirm('是否确定删除')){
            $.ajax({
                url : ajax_url,
                data : {'action':'remFeedback','id':id},
                dataType : 'json',
                type : 'post',
                success : function (response) {
                    alert(response.data.info);
                    if(response['success']){
                        window.location.reload();
                    }
                }
            });
        }
    });
});