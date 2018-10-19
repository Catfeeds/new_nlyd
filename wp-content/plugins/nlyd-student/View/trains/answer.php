<?php
switch ($_GET['type']){
    case 'szzb';
        $match_title = __('数字争霸', 'nlyd-student');
        include_once 'matching-numberBattle.php';
        break;
    case 'pkjl';
        $match_title = __('扑克接力', 'nlyd-student');
        include_once 'matching-pokerRelay.php';
        break;
    case 'wzsd';
        $match_title = __('文章速读', 'nlyd-student');
        include_once 'matching-reading.php';
        break;
    case 'kysm';
        $match_title = __('快眼扫描', 'nlyd-student');
        include_once 'matching-fastScan.php';
        break;
    default:

        break;
}
