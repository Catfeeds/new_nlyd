jQuery(document).ready(function($) {
   $('.upper').on('click', function () {
      //上架
       var id = $(this).closest('tr').attr('data-id');
       ajaxRequest(id,1);
   });
   $('.lower').on('click', function () {
       //下架
       var id = $(this).closest('tr').attr('data-id');
       ajaxRequest(id,2);
   });
   //批量
    $('.batch').on('click', function () {
        var arr = [];
        var status = $(this).prev().val();
        $.each($('.check-children'), function (i, v) {
            if($(v).prop('checked')){
                arr.push($(v).val());
            }
        });
        ajaxRequest(arr,status);
    });

   function ajaxRequest(id,status) {
        $.ajax({
            data : {'action' : 'goodsShelf', 'id' : id, 'status' : status},
            url : ajax_url,
            type : 'post',
            dataType : 'json',
            success : function (response) {
                alert(response['data']['info'])
                if(response['success']){
                    window.location.reload();
                }
            }
        });     
   }
});