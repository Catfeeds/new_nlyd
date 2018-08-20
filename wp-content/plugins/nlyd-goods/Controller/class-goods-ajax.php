<?php

class Goods_Ajax{
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
     * 商品上下架
     */
    public function goodsShelf(){
        if(is_array($_POST['id'])){
            $idStr = '(';
            foreach ($_POST['id'] as $v){
                $idStr .= intval($v).',';
            }

            $idStr = substr($idStr, 0, strlen($idStr)-1);
            $idStr .= ')';
        }else{
            $idStr = '('.intval($_POST['id']).')';
        }
        $shelf = intval($_POST['status']);
        if($idArr = '' || ($shelf != 1 && $shelf != 2)) return;
        if($shelf == 1) $whereShelf = 2;
        else $whereShelf = 1;
        global $wpdb;
        $sql = 'UPDATE '.$wpdb->prefix.'goods'.' SET shelf='.$shelf.' WHERE id IN '.$idStr.' AND shelf='.$whereShelf;
        $bool = $wpdb->query($sql);
        if($bool) wp_send_json_success(['info' => '操作成功']);
        else wp_send_json_error(['info' => '操作失败']);
    }


}

new Goods_Ajax();