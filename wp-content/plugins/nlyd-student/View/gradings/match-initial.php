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
    case 'memory':    //速记
        require_once student_view_path.CONTROLLER.'/ready-numberBattle.php';
        break;
    case 'reading':    //速读

        require_once student_view_path.CONTROLLER.'/ready-pokerRelay.php';
        break;
    case 'arithmetic':    //速算
        require_once student_view_path.CONTROLLER.'/matching-fastCalculation.php';
        break;
    default:
        require_once student_view_path.'public/my-404.php';
        break;
}
?>
