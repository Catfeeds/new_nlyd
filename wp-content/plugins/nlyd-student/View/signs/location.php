<script>
    wx.config({
        debug: false,
        appId: '<?php echo $jdk["appId"];?>',
        timestamp: <?php echo $jdk["timestamp"];?>,
        nonceStr: '<?php echo $jdk["nonceStr"];?>',
        signature: '<?php echo $jdk["signature"];?>',
        jsApiList: [
            // 所有要调用的 API 都要加到这个列表中
            'checkJsApi',
            'openLocation',
            'getLocation'
        ]
    });
</script>