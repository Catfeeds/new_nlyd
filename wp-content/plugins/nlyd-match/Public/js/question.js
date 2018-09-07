
jQuery(document).ready(function($) {
    $('.js-data-example-ajax').each(function () {
        var _this=$(this)
        var team_type = _this.attr('data-type');
        var team_id = _this.attr('team-id');
        _this.select2({
            ajax: {
                url: ajaxurl+'?action='+_this.attr('data-action')+'&team_type='+team_type+'&team_id='+team_id,
                dataType: 'json',
                delay: 600, //wait 250 milliseconds before triggering the request
                processResults: function (res) {
                    // Tranforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: res.data
                    };
                }
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }

        });
    })

    $('#addSelect').click(function(){
        var _this=$(this);
        var index=$('.select-list .select-li').length
        var dom='<li class="select-li"><input class="select-checkBox" type="checkbox" name="problem['+index+'][answer]" value="1"><input class="select-input" type="text" name="problem['+index+'][select]" value=""><span class="delSelect">删除</span></li>'
        $('.select-list').append(dom);
       
    })
    $('body').on('click','.delSelect',function(){
        var _this=$(this);
        _this.parents('.select-li').remove();
        $('.select-li').each(function(){
            var index=$(this).index()
            $(this).children('.select-checkBox').attr('name','problem['+index+'][answer]')
            $(this).children('.select-input').attr('name','problem['+index+'][select]')
        })
       
    })
    /**
     * 删除题库
     */
    $('.del_question').on('click', function () {
        var id = $(this).closest('tr').find('input[type="checkbox"]').val();
        if(confirm('是否确定删除题目? 所属答案将全部删除')){
            $.ajax({
                type : 'post',
                dataType : 'json',
                data : {'action' : 'delQuestion', 'id' : id},
                url : ajaxurl,
                success : function (response) {
                    alert(response['data']['info']);
                    if(response['success']){
                        window.location.reload();
                    }
                },error : function () {
                    alert('请求失败!')
                }
            });
        }
    });
})
