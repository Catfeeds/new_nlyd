<div class="wrap">
    <h1>速记上传</h1>
    <form method="post" id="grading_form" enctype="multipart/form-data" >
        <input type="hidden" name="action" value="grading_content_upload">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><label for="blogname">速记类别</label></th>
                <td>
                    <select name="memory_type" class="memory_type">
                        <option value="">选择</option>
                        <option value="vocabulary">词汇</option>
                        <option value="book">书籍</option>
                        <option value="people">人脉</option>
                    </select>
                    <select name="memory_grade" class="memory_grade">
                        <?php
                        for ($i=3;$i<=10;$i++){
                            echo '<option value="'.$i.'">记忆'.chinanum($i).'级</option>';
                        }
                        ?>
                    </select>
                    <select name="handle" class="memory_handle">
                        <option value="1">追加</option>
                        <option value="2">覆盖</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="blogname">文件名</label></th>
                <td><input type="file" name="file[]" id="file" multiple></td>
            </tr>
            </tbody>
        </table>
        <p class="submit"><input type="button" name="submit" id="submit_form" class="button button-primary" value="提交"></p>
    </form>
</div>
<script>
    jQuery(document).ready(function($){
        jQuery('.memory_grade').hide();
        jQuery('.memory_handle').hide();
        jQuery('.memory_type').change(function () {
            jQuery('.memory_handle').show();
            if($(this).find("option:selected").val() == 'book'){
                jQuery('.memory_grade').val(3).show();
            }else {
                jQuery('.memory_grade').hide().val('');
            }
             //console.log($(this).find("option:selected").text());
        })

        jQuery('#submit_form').click(function () {

          //   var form = new FormData(document.getElementById("grading_form"));
          //
          // var query =   jQuery('#grading_form').serialize();
          //
          //   $.post(ajaxurl,form,function (data) {
          //       alert(data.data);
          //   },'json')
            var fd = new FormData(document.getElementById("grading_form"));
            // fd.append('nationality_short',);value
            $.ajax({
                data: fd,
                aysnc: true ,
                type: "POST" , // 默认使用POST方式
                dataType:'json',
                timeout:2000,
                url:ajaxurl,
                contentType : false,
                processData : false,
                cache : false,
                success: function(res, textStatus, jqXHR){
                    //return false;
                    alert(res.data);
                    if(res.success){
                        history.go(0)
                    }
                }
            })
            return false;
        })
    });
</script>