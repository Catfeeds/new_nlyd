<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Ggbx_Form_Ajax {

	private $form_name;

	function __construct() {

        //如果有提交并且提交的内容中有规定的提交数据
        if(empty($_POST['action']) ) return;

        $this->form_name = $_POST['action'];

        //判斷方法是否存在
        if(!method_exists($this,$this->form_name)) return;

        add_action( 'wp_ajax_'.$this->form_name,array($this, $this->form_name) );
        add_action( 'wp_ajax_nopriv_'.$this->form_name,  array($this,$this->form_name) );


    }

    function role_jurisdiction_settings(){

        $role = $_POST['role'];
        $check_role = $_POST['check_role'];
        $check = $_POST['check'];
        if($check_role && current_user_can( 'ggbx_role' ) && $role && $check){
            global $wp_roles;

            if($check == 'y'){
                //添加角色权限
                $flag = '添加';
                $result = $wp_roles->add_cap( trim($role), trim($check_role));

            }else{
                $flag = '取消';
                $result = $wp_roles->remove_cap( trim($role), trim($check_role));
            }


            echo json_encode(array('flag'=>y,'info'=>'权限'.$flag.'成功'));

        }
        exit;
    }

    function role_delete(){

        $role_name = $_POST['role_name'];
        if(current_user_can( 'ggbx_role' ) && ($role_name != 'administrator')){
            global $wp_roles;

            $wp_roles->remove_role( $role_name );

            echo json_encode(array('flag'=>y,'info'=>'操作成功'));

        }
        exit;
    }

    function role_add(){

        foreach($_POST as $value){
            if(empty($value)){
                exit(json_encode(array('flag'=>n,'info'=>'有空值。')));
            }
        }

        if( current_user_can( 'ggbx_role' ) ) {

            global $wp_roles;

            $role = $_POST['role'];
            $display_name = $_POST['display_name'];
            $role_s = $_POST['role_s'];

            $role_s_arr = array();

            foreach($role_s as $value){
                $role_s_arr[$value] = true;
            }

            $result = add_role( $role,$display_name , $role_s_arr);

            if( $result ){
                echo json_encode(array('flag'=>y,'info'=>'用户角色创建成功'));

            }else {
                echo json_encode(array('flag'=>n,'info'=>'因为用户角色已经存在或者其它原因导致创建失败'));

            }

        }

        exit;
    }

}

new Ggbx_Form_Ajax();