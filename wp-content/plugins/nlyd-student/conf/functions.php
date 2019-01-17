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

/**
 * 收入来源默认数组
 */
function income_stream_array(){
    $default = array(
        'open_match'=>array('title'=>'开设比赛'),
        'open_grading'=>array('title'=>'开设考级'),
        'open_course'=>array('title'=>'课程渠道'),
        'recommend_match'=>array('title'=>'推荐比赛'),
        'recommend_grading'=>array('title'=>'推荐考级'),
        'director_match'=>array('title'=>'参赛机构'),
        'director_grading'=>array('title'=>'考级负责人'),
        'recommend_match_zone'=>array('title'=>'推荐赛区'),
        'recommend_trains_zone'=>array('title'=>'推荐训练中心'),
        'recommend_test_zone'=>array('title'=>'推荐测评中心'),
        'recommend_course'=>array('title'=>'推荐购课'),
        'recommend_qualified'=>array('title'=>'推荐达标'),
        'grading_qualified'=>array('title'=>'考级达标'),
        'cause_manager'=>array('title'=>'事业管理员'),
        'cause_minister'=>array('title'=>'事业部长'),
    );
}