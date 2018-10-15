
<?php if(!empty($list)){ ?>
<?php foreach ($list as $v){ ?>
<div>
    <?=$v['project_type_cn']?>
    <?=$v['my_score']?>
    <?=$v['created_time']?>
    <a href="<?=home_url('trains/logs/id/'.$v['id'].'/type/'.$v['project_type'])?>">详情</a>
</div>
<?php } ?>
<?php }else{ ?>
<div>暂无训练记录</div>
<?php }?>
