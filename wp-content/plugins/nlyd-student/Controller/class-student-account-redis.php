<?php

/**
 * Redis测试
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Student_Account_Redis
{
    public function __construct($shortCode)
    {

        //引入当前页面css/js
        add_action('wp_enqueue_scripts', array($this,'scripts_default'));


        //添加短标签
        add_shortcode('student-account',array($this,'index'));
    }

    public function index(){
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379,1);
        $redis->auth('leo626');
        //echo "Server is running: " . $redis->ping();

        //进队列
        /*$redis->lpush('list', 'html');
        $redis->lpush('list', 'css');
        $redis->lpush('list', 'php');*/

        echo "数据进队列完成\n";

        //可查看队列
        //获取列表中所有的值
        $list = $redis->lrange('list', 0, -1);
        print_r($list);echo '<br>';

        //从右侧加入一个
        /*$redis->rpush('list', 'mysql');
        $list = $redis->lrange('list', 0, -1);
        print_r($list);echo '<br>';*/

        //从左侧弹出一个
        /*$redis->rpop('list');
        $list = $redis->lrange('list', 0, -1);
        print_r($list);echo '<br>';*/

        /*//出队列
        $redis->lpop('queue_name');
        //查看队列
        $res = $redis->lrange('queue_name',0,-1);
        print_r($res);*/
    }

    /**
     * 默认公用js/css引入
     */
    public function scripts_default(){
        wp_register_script( 'student-cookie',student_js_url.'cookie.url.config.js',array('jquery'), leo_student_version  );
        wp_enqueue_script( 'student-cookie' );

        wp_register_style( 'my-student-userCenter', student_css_url.'userCenter.css',array('my-student') );
        wp_enqueue_style( 'my-student-userCenter' );
    }
}