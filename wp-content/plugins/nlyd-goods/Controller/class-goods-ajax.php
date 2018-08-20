<?php

class Goods_Ajax{
    /**
     * 商品上下架
     */
    public function goodsShelf(){
        wp_send_json_success(['info' => '进来了']);
    }


}

new Goods_Ajax();