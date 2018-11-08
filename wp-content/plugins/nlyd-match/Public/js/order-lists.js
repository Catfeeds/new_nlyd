jQuery(document).ready(function($) {

    $('.refund-btn').on('click', function () {
        if(confirm('是否确定退款? 确认后将立即执行退款')){
            var serial = $('#form-table').find('tr:first').find('td:last').text();
            var refund_cost = $('input[name="refund-cost"]').val();
            var _wpnonce = $('input[name="_wpnonce"]').val();
            if(refund_cost == ''){
                alert('请填写退款金额');
                return false;
            }
            if(!serial) return false;
            $.ajax({
                url : ajax_url,
                data : {'action': 'refundPay','refund_fee':refund_cost,'serial':serial,'_wpnonce':_wpnonce},
                dataType : 'json',
                type : 'post',
                success : function (response) {
                    alert(response.data.info);
                    $('#notice-box').hide();
                }
            });
        }
    });


    $('.no_refund').on('click', function () {
        var id = $(this).closest('tr').attr('data-id');
        if(confirm('是否确定拒绝退款?')){
            $.ajax({
                url : ajax_url,
                data : {'action' : 'noRefund', 'id' : id},
                dataType : 'json',
                type : 'post',
                success : function (response) {
                    alert(response['data']['info']);
                    if(response['success']){
                        window.location.reload();
                    }
                }
            })
        }
    })







    layui.use(['laydate','form',], function(){
        var laydate = layui.laydate;
        var form = layui.form
        //日期时间选择器
        $('.date-picker').each(function(){
            var id=$(this).attr('id');
            laydate.render({
                elem: '#'+id
                ,type: 'datetime'
            });
        })
    })

    /**
     * 删除订单
     */
    $('.order-tr').on('click','.close-order',function () {
        var _id = $(this).closest('.order-tr').attr('data-id');
        if(confirm('订单删除后将无法恢复,是否确定删除订单')){
            $.ajax({
                url:ajaxurl,
                data : {'action':'closeOrder','id':_id},
                dataType : 'json',
                type : 'post',
                success : function (response) {
                    alert(response.data.info);
                    if(response['success'] == true){
                        window.location.reload();
                    }
                },error : function () {
                    alert('请求失败');
                }
            });
        }
    })
})