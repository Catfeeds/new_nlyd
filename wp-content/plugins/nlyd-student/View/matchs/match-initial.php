<?php
/**
 * 比赛准备页面
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/22
 * Time: 20:28
 */
?>
<?php
switch ($project_alias){
    case 'szzb':    //数字争霸
        require_once student_view_path.'matchs/ready-numberBattle.php';
        break;
    case 'pkjl':    //扑克接力
        require_once student_view_path.'matchs/ready-pokerRelay.php';
        break;
    case 'zxss':    //正向速算
        require_once student_view_path.'matchs/matching-fastCalculation.php';
        break;
    case 'nxss':    //逆向速算
        require_once student_view_path.'matchs/matching-fastReverse.php';
        break;
    case 'wzsd':     //文章速读
        require_once student_view_path.'matchs/ready-reading.php';
        break;
    case 'kysm':    //快眼扫描
        require_once student_view_path.'matchs/matching-fastScan.php';
        break;
    default:
        require_once student_view_path.'public/my-404.php';
        break;
}
?>
