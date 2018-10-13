
<ul>
    <?php if (empty($list)){ ?>
    <li>暂无专项比賽训练</li>
    <?php }else{ ?>
    <?php foreach ($list as $v){ ?>
    <li>
        <a href="<?=home_url('trains/lists/id/'.$v->ID)?>"><?=$v->post_title?></a>
    </li>
    <?php }?>
    <?php }?>
</ul>