<?php
switch ($_GET['type']){
    case 'szzb';
        $title = '数字争霸';
        include_once 'ready-numberBattle.php';
        break;
    case 'pkjl';
        $title = '扑克接力';
        include_once 'ready-pokerRelay.php';
        break;
    case 'wzsd';
        $title = '扑克接力';
        include_once 'ready-reading.php';
        break;

    default:

        break;
}
