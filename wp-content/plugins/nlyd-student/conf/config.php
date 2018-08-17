<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/6/25
 * Time: 16:03
 */

/**
 * Student
 *学生端基础配置
 */

return array(

        'page'=>array(  //页面生成
            'post_title'=>'学生首页',
            'post_name'=>'student',
            'post_content'=>'[student-index]',
            'url'=>'/student',
            'child_page'=>array(
                array(
                    'post_title'=>'学生登录',
                    'post_name'=>'student-login',
                    'post_content'=>'[student-login]',
                    'url'=>'/student/login',
                ),
                array(
                    'post_title'=>'学生注册',
                    'post_name'=>'student-register',
                    'post_content'=>'[student-register]',
                    'url'=>'/student/register',
                ),
                array(
                    'post_title'=>'密码找回',
                    'post_name'=>'student-reset',
                    'post_content'=>'[student-reset]',
                    'url'=>'/student/reset',
                ),
                array(
                    'post_title'=>'用户协议',
                    'post_name'=>'student-agreement',
                    'post_content'=>'[student-agreement]',
                    'url'=>'/student/agreement',
                ),
                array(
                    'post_title'=>'account',
                    'post_name'=>'个人中心',
                    'post_content'=>'[student-account]',
                    'url'=>'/student/account',
                    'child_page'=>array(
                        array(
                            'post_title'=>'我的比赛',
                            'post_name'=>'student-match',
                            'post_content'=>'[student-account-match]',
                            'url'=>'/student/account/match',
                        ),
                        array(
                            'post_title'=>'我的战队',
                            'post_name'=>'student-team',
                            'post_content'=>'[student-account-team]',
                            'url'=>'/student/account/team',
                        ),
                        array(
                            'post_title'=>'安全中心',
                            'post_name'=>'student-secure',
                            'post_content'=>'[student-account-secure]',
                            'url'=>'/student/account/secure',
                        ),
                        array(
                            'post_title'=>'订单中心',
                            'post_name'=>'student-order',
                            'post_content'=>'[student-account-order]',
                            'url'=>'/student/account/order',
                        ),
                        array(
                            'post_title'=>'教练课程',
                            'post_name'=>'student-course',
                            'post_content'=>'[student-account-course]',
                            'url'=>'/student/account/course',
                        ),
                        array(
                            'post_title'=>'安全设置',
                            'post_name'=>'student-setting',
                            'post_content'=>'[student-account-setting]',
                            'url'=>'/student/account/setting',
                        ),
                    )
                ),
            ),
        ),
        'rewrite_rules'=>array( //  url规则
            //add_rewrite_rule('student/?([^/]*)/?(.*)/?$','index.php?pagename=$matches[1]&view-order=$matches[3]','top'),
        ),
);