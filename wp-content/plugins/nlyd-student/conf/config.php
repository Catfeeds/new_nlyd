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
        '_dt_sidebar_position'=>'disabled',
        'page'=>array(  //页面生成
            array(
                'post_title'=>'学生首页',
                'post_content'=>'[student-index]',
                'url'=>'/student',
            ),
            array(
                'post_title'=>'登录页面',
                'post_content'=>'[match-login]',
                'url'=>'/logins',
            ),
            array(
                'post_title'=>'个人中心',
                'post_content'=>'[student-home]',
                'url'=>'/account',
            ),
            array(
                'post_title'=>'安全中心',
                'post_content'=>'[safety-home]',
                'url'=>'/safety',
            ),
            array(
                'post_title'=>'体系标准',
                'post_content'=>'[system-home]',
                'url'=>'/system',
            ),
            array(
                'post_title'=>'我的钱包',
                'post_content'=>'[wallet-home]',
                'url'=>'/wallet',
            ),
            array(
                'post_title'=>'战队页面',
                'post_content'=>'[team-home]',
                'url'=>'/teams',
            ),
            array(
                'post_title'=>'支付页面',
                'post_content'=>'[payment-home]',
                'url'=>'/payment',
            ),
            array(
                'post_title'=>'比赛页面',
                'post_content'=>'[match-home]',
                'url'=>'/matchs',
            ),
            array(
                'post_title'=>'首页-名录',
                'post_content'=>'[directory-home]',
                'url'=>'/directory',
            ),
            array(
                'post_title'=>'Timer定时器',
                'post_content'=>'[timer-index]',
                'url'=>'/timer',
            ),

        ),
);