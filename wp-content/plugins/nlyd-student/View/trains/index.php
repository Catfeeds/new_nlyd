

<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
            require_once leo_student_public_view.'leftMenu.php';
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12  detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title">
                <div><?=__('训练类型', 'nlyd-student')?></div>
                </h1>
            </header>
  <!-- <div id="bdtts_div_id">
    <audio id="tts_autio_id" autoplay="autoplay">
      <source id="tts_source_id" src="http://tts.baidu.com/text2audio?lan=zh&amp;ie=UTF-8&amp;spd=5&amp;per=1&amp;text=请输入文字" type="audio/mpeg">
      <embed id="tts_embed_id" height="0" width="0" src="">
    </audio>
  </div> -->
                    <?php if (empty($list)){ ?>
                        <div class="layui-row nl-border nl-content">
                            <div class="width-padding width-padding-pc">
                                <div class="no-info-page">
                                    <div class="no-info-img">
                                        <img src="<?=student_css_url.'image/noInfo/noTrain1045@3x.png'?>">
                                    </div>
                                    <p class="no-info-text"><?=__('暂无专项比賽训练', 'nlyd-student')?></p>
                                </div>
                            </div>
                        </div>
                    <?php }else{ ?>
                        <div class="layui-row nl-border nl-content have-footer">
                            <div class="width-padding width-padding-pc">
                                <?php foreach ($list as $v){ ?>
                                    <a class="train_row <?=$v->post_status == 'draft' ? 'disable' : 'c_black';?>" <?php if($v->post_status == 'draft') echo 'onclick="return false;"'?> href="<?= $v->post_status == 'draft' ? '' : home_url('trains/lists/id/'.$v->ID)?>">
                                        <div class="train-img">
                                            <?php
                                                $thumbnail_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($v->ID), 'thumbnail');
                                            ?>
                                            <img src="<?=$thumbnail_image_url[0]?>">
                                        </div>
                                        <div class="train_name fs_16"><?=__($v->post_title, 'nlyd-student')?></div>
                                        <?php if($v->post_status == 'draft'):?>
                                            <div class="train_tips"><?=__('暂未开放', 'nlyd-student')?></div>
                                        <?php endif;?>
                                    </a>
                                <?php }?>
                            </div>
                        </div>
                    <?php }?>

        </div>
    </div>
</div>
<script>
    // function doTTS() {
    //   var ttsDiv = document.getElementById('bdtts_div_id');
    //   var ttsAudio = document.getElementById('tts_autio_id');
    // //   var ttsText = document.getElementById('ttsText').value;
    // var ttsText ="1,2,3,4,5,6,7"
    //   // 文字转语音
    //   ttsDiv.removeChild(ttsAudio);
    //   var au1 = '<audio id="tts_autio_id" autoplay="autoplay">';
    //   var sss = '<source id="tts_source_id" src="http://tts.baidu.com/text2audio?lan=zh&ie=UTF-8&per=3&spd=5&text=' + ttsText + '" type="audio/mpeg">';
    //   var eee = '<embed id="tts_embed_id" height="0" width="0" src="">';
    //   var au2 = '</audio>';
    //   ttsDiv.innerHTML = au1 + sss + eee + au2;

    //   ttsAudio = document.getElementById('tts_autio_id');

    //   ttsAudio.play();
    // }
    // doTTS()
jQuery(function($) {

})
</script>

