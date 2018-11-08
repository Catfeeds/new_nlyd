

jQuery(document).ready(function($) {
    /**
     * 关闭比赛
      */
    $('.closeMatch').on('click', function () {
        var id = $(this).attr('data-id');
        var match_status = $(this).attr('data-status');
        var notice = '警告: 当前比赛 (';
        switch (match_status){
            case '1': //报名中
                notice += '正在报名中';
                break;
            case '2': //比赛中
                notice += '正在比赛中';
                break;
            case '-3': //已结束
                notice += '已结束';
                break;
            case '-2': //等待开赛
                notice += '正在等待开赛';
                break;
            case '-1': //未开始
                notice += '未开始';
                break;
        }
        notice += '), 是否确定要关闭比赛';
        if(confirm(notice)){
            ajaxRequest('closeMatch', id);
        }
    });
    /**
     * 删除比赛
     */
    $('.delMatch').on('click', function () {
        var id = $(this).attr('data-id');
        if(confirm('警告: 删除比赛后数据将无法恢复, 请慎重决定是否执行该操作')){
            ajaxRequest('delMatch', id);
        }

    });
    function ajaxRequest(action, id) {
        $.ajax({
            url : ajaxurl,
            data : {'action' : action, 'id' : id},
            dataType : 'json',
            type : 'post',
            success : function (response) {
                alert(response['data']['info']);
                if(response.success){
                    window.location.reload();
                }
            },error : function () {

            }
        });
    }

});

/**
 * 复制签到链接
 */
function copyUrl(str)
{
    var save = function (e){
        e.clipboardData.setData('text/plain',str);//下面会说到clipboardData对象
        e.preventDefault();//阻止默认行为
    }
    document.addEventListener('copy',save);
    document.execCommand("copy");//使文档处于可编辑状态，否则无效

}


