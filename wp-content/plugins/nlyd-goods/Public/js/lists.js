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
   function ajaxRequest(id,status) {
        $.ajax({
            data : {'action' : 'goodsShelf', 'id' : id, 'status' : status},
            url : ajax_url,
            type : 'post',
            dataType : 'json',
            success : function (response) {
                
            }
        });     
   }
});