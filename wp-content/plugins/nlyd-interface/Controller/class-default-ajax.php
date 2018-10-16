<?php
/**
 * Ajax操作类
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/6/22
 * Time: 14:10
 */
use library\AliSms;
use library\TwentyFour;
class Default_Ajax
{   
    
    function __construct() {

        //如果有提交并且提交的内容中有规定的提交数据
        $data = $_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST : $_GET;
        if(empty($data['action']) ) return;
        $action = $data['action'];

        //判斷方法是否存在
        if(!method_exists($this,$action)) return;

        add_action( 'wp_ajax_'.$action,array($this, $action) );
        add_action( 'wp_ajax_nopriv_'.$action,  array($this,$action) );
    }

    /**
     * 项目默认项设置
     */
    public function update_default_setting(){
        unset($_POST['action']);
        update_option('default_setting',$_POST);
        wp_send_json_success('保存成功');
    }


    /**
     * 设置比赛项目默认时长
     */
    public function setting_project_use(){

        unset($_POST['action']);
        update_option('match_project_use',$_POST);
        wp_send_json_success('保存成功');
    }
}

new Default_Ajax();