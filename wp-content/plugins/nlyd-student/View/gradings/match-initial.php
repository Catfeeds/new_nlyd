<?php
/**
 * 考级准备页面
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/22
 * Time: 20:28
 */
?>
<?php
switch ($project_alias){
    case 'memory':    //速记
        switch ($_GET['type']){
            case 'sz': //数字记忆
            case 'zm': //字母记忆
                require_once student_view_path.CONTROLLER.'/grading-szzb.php';
                break;
            case 'cy': //词语记忆
                require_once student_view_path.CONTROLLER.'/grading-zwcy.php';
                break;
            case 'yzl': //圆周率记忆
                require_once student_view_path.CONTROLLER.'/matching-PI.php';
                break;
            case 'wz': //国学
                require_once student_view_path.CONTROLLER.'/matching-silent.php';
                break;
            case 'tl': //听力记忆
                require_once student_view_path.CONTROLLER.'/grading-voice.php';
                break;
            case 'rm': //人脉信息
                require_once student_view_path.CONTROLLER.'/grading-rmxx.php';
                break;
        }
        break;
    case 'reading':    //速读
        require_once student_view_path.CONTROLLER.'/ready-reading.php';
        break;
    case 'arithmetic':    //速算
        switch ($_GET['type']){
            case 'nxys': //逆向运算
                require_once student_view_path.CONTROLLER.'/matching-fastReverse.php';
                break;
            default: //正向运算
                require_once student_view_path.CONTROLLER.'/matching-fastCalculation.php';
                break;
        }
        break;
    default:
        require_once student_view_path.'public/my-404.php';
        break;
}
?>
