<?php
/**
 * 年齡組別
 */
function get_age_group(){
    return array('1'=>__('儿童组', 'nlyd-student'),'2'=>__('少年组', 'nlyd-student'),'3'=>__('成年组', 'nlyd-student'),'4'=>__('老年组', 'nlyd-student'));
}

/**
 * @param $age 年龄
 * 根据年龄获取组别名称
 */
if(!function_exists('getAgeGroupNameByAge')) {
    function getAgeGroupNameByAge($age)
    {
        switch ($age) {
            case $age > 59:
                $group = __('老年组', 'nlyd-student');
                break;
            case $age > 17:
                $group = __('成年组', 'nlyd-student');
                break;
            case $age > 12:
                $group = __('少年组', 'nlyd-student');
                break;
            default:
                $group = __('儿童组', 'nlyd-student');
                break;
        }
        return $group;
    }
}