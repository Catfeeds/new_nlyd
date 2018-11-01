<?php
/**
 * Ajax操作类
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/6/22
 * Time: 14:10
 */
use library\TwentyFour;
class Spread_Ajax
{

    public $redis;
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

     public function changeUserSpreadLevel(){
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $type = isset($_POST['type']) ? trim($_POST['type']) : '';
        if($user_id<1 || !isset($_POST['level']) || ($type!='lxl' && $type!='fx')) wp_send_json_error(['info' => '参数错误!']);
        $level = intval($_POST['level']);
        if($level > 2 || $level < 0) wp_send_json_error(['info' => '参数错误!']);
        $res = update_user_meta($user_id,$type.'_level',$level);
        if($res) wp_send_json_success(['info' => '操作成功!']);
        else wp_send_json_error(['info' => '操作失败!']);
    }
}

new Spread_Ajax();