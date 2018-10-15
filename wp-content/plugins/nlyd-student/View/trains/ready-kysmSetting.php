
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><?=$title?></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                闪现时间<input type="text" id="flash" name="flash_time" value="800" />毫秒

                <div class="a-btn" id="complete" href="<?=home_url('trains/answer/genre_id/'.$_GET['genre_id'].'/type/'.$_GET['type'])?>">开始训练</div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function($) {
        $('#complete').click(function () {
            var url = $(this).attr('href')+'/flash_time/'+$('#flash').val();
            //alert(url);
            window.location.href = url;
        })
    })
</script>