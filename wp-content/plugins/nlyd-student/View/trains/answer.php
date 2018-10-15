<?php
switch ($_GET['type']){
    case 'szzb';
        $title = '数字争霸';
        include_once 'matching-numberBattle.php';
        break;
    case 'pkjl';
        $title = '扑克接力';
        include_once 'matching-pokerRelay.php';
        break;
    case 'wzsd';
        $title = '文章速读';
        include_once 'matching-reading.php';
        break;

    default:

        break;
}
