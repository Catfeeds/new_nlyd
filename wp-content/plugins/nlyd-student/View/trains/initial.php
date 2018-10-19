<?php
switch ($_GET['type']){
    case 'szzb';
        $title = __('数字争霸', 'nlyd-student');
        include_once 'ready-numberBattle.php';
        break;
    case 'pkjl';
        $title = __('扑克接力', 'nlyd-student');
        include_once 'ready-pokerRelay.php';
        break;
    case 'wzsd';
        $title = __('文章速读', 'nlyd-student');
        include_once 'ready-reading.php';
        break;
    case 'kysm';
        $title = __('快眼扫描', 'nlyd-student');
        include_once 'ready-kysmSetting.php';
        break;
    case 'zxss';
        $title = __('正向速算', 'nlyd-student');
        include_once 'matching-fastCalculation.php';
        break;
    case 'nxss';
        $title = __('逆向速算', 'nlyd-student');
        include_once 'matching-fastReverse.php';
        break;

    default:

        break;
}
