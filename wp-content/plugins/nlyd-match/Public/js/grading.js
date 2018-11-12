jQuery(document).ready(function($) {
    /**
     * 关闭考级
     */
    $('.closeGrading').on('click', function () {
        var id = $(this).attr('data-id');
        if(id < 1 || id == undefined) return false;
        $.ajax({
            url : ajaxurl,
            data : {'action':'closeMatch','id':id},//调用关闭比赛方法,相同操作
            dataType : 'json',
            type : 'post',
            success : function (response) {
                alert(response.data.info);
                if(response['success'] == true){
                    window.location.reload();
                }
            },error : function () {
                alert('请求失败!');
            }
        });
    });

    /**
     * 删除考级
     */

    $('.delGrading').on('click', function () {
        var id = $(this).attr('data-id');
        if(id < 1 || id == undefined) return false;
        if(confirm('删除后将无法恢复! 确定要删除吗?')){
            $.ajax({
                url : ajaxurl,
                data : {'action':'deleteGrading','id':id},//调用关闭比赛方法,相同操作
                dataType : 'json',
                type : 'post',
                success : function (response) {
                    alert(response.data.info);
                    if(response['success'] == true){
                        window.location.reload();
                    }
                },error : function () {
                    alert('请求失败!');
                }
            });
        }
    });
});