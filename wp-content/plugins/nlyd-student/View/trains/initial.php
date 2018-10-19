<?php


switch ($_GET['type']){
    case 'szzb';
        $match_title = __('数字争霸', 'nlyd-student');
        include_once 'ready-numberBattle.php';
        break;
    case 'pkjl';
        $match_title = __('扑克接力', 'nlyd-student');
        include_once 'ready-pokerRelay.php';
        break;
    case 'wzsd';
        $match_title = __('文章速读', 'nlyd-student');
        include_once 'ready-reading.php';
        break;
    case 'kysm';
        $match_title = __('快眼扫描', 'nlyd-student');
        include_once 'ready-kysmSetting.php';
        break;
    case 'zxss';
        $match_title = __('正向速算', 'nlyd-student');
        include_once 'matching-fastCalculation.php';
        break;
    case 'nxss';
        $match_title = __('逆向速算', 'nlyd-student');
        include_once 'matching-fastReverse.php';
        break;

    default:

        break;
}
