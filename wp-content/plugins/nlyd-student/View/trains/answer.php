<?php
switch ($_GET['type']){
    case 'szzb';
        include_once 'ready-numberBattle.php';
        break;
    case 'pkjl';
        include_once 'ready-pokerRelay.php';
        break;
    default:

        break;
}
