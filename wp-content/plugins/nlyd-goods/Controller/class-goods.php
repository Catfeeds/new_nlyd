<?php

class Goods
{
    public function __construct()
    {
        add_menu_page('所有商品', '所有商品', 'administrator', 'goods',array($this,'index'),'dashicons-businessman',99);
        add_submenu_page('新增商品','新增商品','新增商品','administrator','feedback-lists',array($this,'lists'));
//        add_submenu_page('order','申请退款','申请退款','administrator','order-refund',array($this,'refund'));
//        add_submenu_page('order','我的学员','我的学员','administrator','teacher-student',array($this,'student'));
//        add_submenu_page('order','我的课程','我的课程','administrator','teacher-course',array($this,'course'));
    }
    public function index(){

    }

    public function lists(){

    }
}

new Goods();