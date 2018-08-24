<?php
/**
 * 比赛答题记录页面
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/22
 * Time: 20:28
 */
?>
<?php
switch ($project_alias){
    case 'szzb':    //数字争霸
        require_once student_view_path.'matchs/matching-numberBattle.php';
        break;
    case 'pkjl':    //扑克接力
        require_once student_view_path.'matchs/matching-pokerRelay.php';
        break;
    case 'wzsd':     //文章速读
        require_once student_view_path.'matchs/matching-reading.php';
        break;
    default:
        require_once student_view_path.'public/my-404.php';
        break;
}
?>
