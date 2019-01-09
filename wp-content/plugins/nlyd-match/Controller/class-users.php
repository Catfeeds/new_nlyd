<?php
class Users {
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_order_menu_page') );
//        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
//        die;

        add_action('user_row_actions', array($this,'users_Row_actions'), 10, 2);
    }

    public function users_Row_actions($actions,$userObj){
        $actions['view'] = '<a href="'.admin_url('users.php?page=users-info&ID='.$userObj->ID).'">资料</a>';
        return $actions;
    }
    public function register_order_menu_page(){

        if ( current_user_can( 'administrator' ) && !current_user_can( 'users' ) ) {
            global $wp_roles;

            $role = 'user_info';//权限名
            $wp_roles->add_cap('administrator', $role);
        }
        add_users_page('用户资料','用户资料','user_info','users-info',array($this,'userInfo'));
//        add_submenu_page('order','我的课程','我的课程','administrator','teacher-course',array($this,'course'));
    }

    /**
     * 用户资料
     */
    public function userInfo(){
        $user_id = isset($_GET['ID']) ? intval($_GET['ID']) : 0;
        $user_id < 1 && exit('参数错误!');
        global $wpdb;
        if(is_post()){
            $nationality = isset($_POST['nationality']) ? trim($_POST['nationality']) : '';
            $real_name = isset($_POST['real_name']) ? trim($_POST['real_name']) : '';
            $age = isset($_POST['age']) ? intval($_POST['age']) : '';
            $card_type = isset($_POST['card_type']) ? trim($_POST['card_type']) : '';
            $card_num = isset($_POST['card_num']) ? trim($_POST['card_num']) : '';
            $sex = isset($_POST['sex']) ? trim($_POST['sex']) : '';
            $receiptProvicone = isset($_POST['receiptProvicone']) ? trim($_POST['receiptProvicone']) : '';
            $receiptCity = isset($_POST['receiptCity']) ? trim($_POST['receiptCity']) : '';
            $receiptArea = isset($_POST['receiptArea']) ? trim($_POST['receiptArea']) : '';
            $receiptAddress = isset($_POST['receiptAddress']) ? trim($_POST['receiptAddress']) : '';
            $whereProvicone = isset($_POST['whereProvicone']) ? trim($_POST['whereProvicone']) : '';
            $whereCity = isset($_POST['whereCity']) ? trim($_POST['whereCity']) : '';
            $whereArea = isset($_POST['whereArea']) ? trim($_POST['whereArea']) : '';
            $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $team = isset($_POST['team']) ? intval($_POST['team']) : '';

            $bool = false;
            $msg = '';
            //更新国籍
            if($nationality != ''){
                $nationality = explode(',',$nationality);
                update_user_meta($user_id,'user_nationality_pic',$nationality[0]) && true;
                update_user_meta($user_id,'user_nationality_short',$nationality[1]) && true;
                update_user_meta($user_id,'user_nationality',$nationality[2]) && true;
            }

            //实名信息
            update_user_meta($user_id,'user_real_name',['real_type'=>$card_type,'real_name'=>$real_name,'real_ID'=>$card_num,'real_age'=>$age]) && true;

            //性别
            update_user_meta($user_id,'user_gender',$sex) && true;

            //所在地区
            update_user_meta($user_id,'user_address',['province'=>$whereProvicone,'city'=>$whereCity,'area'=>$whereArea]) && true;

            //收货地址
            $address_id = $wpdb->get_var("SELECT id FROM `{$wpdb->prefix}my_address` WHERE `user_id`='{$user_id}' AND `is_default`=1");
            if($address_id){
                $wpdb->update($wpdb->prefix.'my_address',['province'=>$receiptProvicone,'city'=>$receiptCity,'area'=>$receiptArea,'address'=>$receiptAddress],['id'=>$address_id]) && true;
            }else{
                $wpdb->insert($wpdb->prefix.'my_address',
                    ['province'=>$receiptProvicone,'city'=>$receiptCity,
                    'area'=>$receiptArea,'user_id'=>$user_id,'is_default'=>1,
                    'fullname'=>$real_name,'telephone'=>$mobile,'address'=>$receiptAddress]) && true;
            }

            //手机和邮箱
            if(preg_match('/^1[3456789][0-9]{9}$/',$mobile) && preg_match('/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/',$email)){
                $wpdb->update($wpdb->users,['user_mobile'=>$mobile,'user_email'=>$email],['ID'=>$user_id]) && true;
            }else{
                $msg = '手机或邮箱格式不正确';
            }

            //战队
            if($team > 0){
                $team_id = $wpdb->get_var("SELECT id FROM `{$wpdb->prefix}match_team` WHERE status=2 AND user_id='{$user_id}'");
                if($team_id){
                    $wpdb->update($wpdb->prefix.'match_team',['team_id'=>$team],['id'=>$team_id]) && true;
                }else{
                    $wpdb->insert($wpdb->prefix.'match_team',['team_id'=>$team,'user_id'=>$user_id,'status'=>2,'created_time'=>get_time('mysql'),'user_type'=>1]) && true;
                }
            }

            //证件照片
//            $upload_dir = wp_upload_dir();
//            $dir = '/user/'.$user_id.'/';
//            $file = saveIosFile($_FILES['cardImg']['tmp_name'],$upload_dir['basedir'].$dir);
//            if($file){
//                update_user_meta($user_id,'user_ID_Card',[$upload_dir['baseurl'].$dir.$file]) && true;
//            }
            $imgArr = [];
            if(isset($_FILES['cardImg'])){
                $upload_dir = wp_upload_dir();
                $dir = '/user/'.$user_id.'/';
                foreach ($_FILES['cardImg']['tmp_name'] as $k => $upd){
                    $file = saveIosFile($upd,$upload_dir['basedir'].$dir);
                    if($file){
                        $imgArr[$k] = $upload_dir['baseurl'].$dir.$file;
                    }
                }

                $unsetImgArr = [];
                if($imgArr != []){
                    //查询要删除的原图片
                    $cardOldImg = get_user_meta($user_id, 'user_ID_Card', true);
                    foreach ($cardOldImg as $coik => $coiv){
                        if(isset($imgArr[$coik])) {
                            $unsetImgArr[] = $coiv;
                        }else{
                            $imgArr[$coik] = $coiv;
                        }
                    }
                    update_user_meta($user_id,'user_ID_Card',$imgArr);
                }
                foreach ($unsetImgArr as $uiav){
                    $filePa = explode('uploads',$uiav);
                    if(is_file(wp_upload_dir()['basedir'].$filePa[1])) unlink(wp_upload_dir()['basedir'].$filePa[1]);
                };
            }



        }

        $user = $wpdb->get_row("SELECT * FROM `{$wpdb->users}` WHERE `ID`='{$user_id}'", ARRAY_A);
        $usermeta = get_user_meta($user_id,'',true);
        $user_real_name = isset($usermeta['user_real_name']) ? unserialize($usermeta['user_real_name'][0]) : [];
        //注册方式
        if(preg_match('/^1[3456789][0-9]{9}$/',$user['user_login'])){
            $register_type = '手机';
        }elseif (preg_match('/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/',$user['user_login'])){
            $register_type = '邮箱';
        }else{
            $register_type = '未知';
        }
        //收件地址
        $address = $wpdb->get_row("SELECT `province`,`city`,area,address FROM `{$wpdb->prefix}my_address` WHERE `user_id`='{$user_id}' AND `is_default`=1",ARRAY_A);

        //所属战队
        $team = $wpdb->get_row("SELECT p.post_title,p.ID FROM `{$wpdb->prefix}match_team` AS mt 
                LEFT JOIN {$wpdb->posts} AS p ON p.ID = mt.team_id 
                WHERE mt.status=2 AND mt.user_id='{$user_id}'");

        //国籍
        $str = file_get_contents(leo_student_path."conf/nationality_array.json");
        $nationalityArr = json_decode($str, true);

        //证件照片
        $cardImg = isset($usermeta['user_ID_Card']) ? unserialize($usermeta['user_ID_Card'][0]) : '';

        //所在地区
        $whereAddress = isset($usermeta['user_address']) ? unserialize($usermeta['user_address'][0]) : [];
//        leo_dump($usermeta);
//        die;

        ?>
        <div class="wrap" id="profile-page">
            <h1 class="wp-heading-inline">个人资料</h1>


            <hr class="wp-header-end">

            <form id="your-profile" action="" method="post" novalidate="novalidate" enctype="multipart/form-data" >
                <input type="hidden" id="_wpnonce" name="_wpnonce" value="9b9192a610">
                <input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/profile.php">
                <p>
                    <input type="hidden" name="from" value="profile">
                    <input type="hidden" name="checkuser_id" value="1">
                </p>

                <h2>个人资料</h2>

                <table class="form-table">
                    <tbody>

                    <tr class="user-user-created-wrap">
                        <th><label for="user_created">注册时间</label></th>
                        <td><input type="text" name="user_created" id="user_created" value="<?=$user['user_registered']?>" disabled="disabled" class="regular-text"></td>
                    </tr>
                    <tr class="user-user-user_ID-wrap">
                        <th><label for="user_ID">账户ID</label></th>
                        <td><input type="text" name="user_ID" id="user_ID" value="<?=isset($usermeta['user_ID'])?$usermeta['user_ID'][0]:''?>" disabled="disabled" class="regular-text"></td>
                    </tr>
                    <tr class="user-user-created-type-wrap">
                        <th><label for="user_created_type">注册方式</label></th>
                        <td><input type="text" name="user_created_type" id="user_created_type" value="<?=$register_type?>" disabled="disabled" class="regular-text"></td>
                    </tr>

                    <tr class="user-user-login-wrap">
                        <th><label for="user_login">用户名</label></th>
                        <td><input type="text" name="user_login" id="user_login" value="<?=$user['user_login']?>" disabled="disabled" class="regular-text"> </td>
                    </tr>


                    <tr>
                        <th><label for="nationality">用户国籍</label></th>
                        <td>
                            <select name="nationality" id="nationality">
                                <?php foreach ($nationalityArr as $nav){ ?>
                                    <option <?php if(isset($usermeta['user_nationality_pic']) && esc_attr( $usermeta['user_nationality_pic'][0] ) == $nav['id']){ echo 'selected="selected"'; } ?> value="<?=$nav['id'].','.$nav['short'].','.$nav['value']?>"><?=$nav['value']?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="real_name">真实姓名</label></th>
                        <td><input type="text" name="real_name" id="real_name" value="<?=isset($user_real_name['real_name']) ? $user_real_name['real_name']:''?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="sex">性别</label></th>
                        <td>
                            <input type="radio" <?=isset($usermeta['user_gender']) && $usermeta['user_gender'][0] == '男' ? 'checked="checked"' : ''?> name="sex" value="男" class="regular-text">男
                            <input type="radio" <?=isset($usermeta['user_gender']) && $usermeta['user_gender'][0] == '女' ? 'checked="checked"' : ''?> name="sex" value="女" class="regular-text">女<br>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="card_type">认证证件</label></th>
                        <td>
                            <select name="card_type" id="card_type">
                                <option <?=isset($user_real_name['real_type']) && $user_real_name['real_type']=='sf' ? 'selected="selected"':''?> value="sf">身份证</option>
                                <option <?=isset($user_real_name['real_type']) && $user_real_name['real_type']=='jg' ? 'selected="selected"':''?> value="jg">军官证</option>
                                <option <?=isset($user_real_name['real_type']) && $user_real_name['real_type']=='hz' ? 'selected="selected"':''?> value="hz">护照</option>
                                <option <?=isset($user_real_name['real_type']) && $user_real_name['real_type']=='tb' ? 'selected="selected"':''?> value="tb">台胞证</option>
                                <option <?=isset($user_real_name['real_type']) && $user_real_name['real_type']=='ga' ? 'selected="selected"':''?> value="ga">港澳证</option>
                            </select>
                        </td>
                    </tr
                    <tr>
                        <th><label for="card_num">证件号码</label></th>
                        <td><input type="text" name="card_num" id="card_num" value="<?=isset($user_real_name['real_ID']) ? $user_real_name['real_ID']:''?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="age">用户年龄</label></th>
                        <td><input type="text" name="age" id="age" value="<?=isset($user_real_name['real_age']) ? $user_real_name['real_age'] : ''?>" class="regular-text"> </td>
                    </tr>
                    <tr>
                        <th><label for="card_img">证件图片</label></th>
                        <td>
                            <?php
                            $num = 1;
                            foreach ($cardImg as $k => $civ) {

                                ?>
                                <div>
                                    <img src="<?=$civ?>" style="height: 80px;" alt="">
                                    <input type="file" name="cardImg[<?=$k?>]" value="重新上传" >
                                    <a href="javascript:;" data-k="<?=$k?>" class="cardImg">删除</a>
                                </div>
                                <?php
                                if($k >= $num) $num = ++$k;
                            }
                            ?>
                           <div>
                               <input type="file" name="cardImg[<?=$num?>]" value="新增图片">
                           </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="whereProvicone">所在地区</label></th>
                        <td>
                            <select name="whereProvicone" id="whereProvicone"></select>
                            <select name="whereCity" id="whereCity"></select>
                            <select name="whereArea" id="whereArea"></select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="receiptProvicone">收件地址</label></th>
                        <td>
                            <select name="receiptProvicone" id="receiptProvicone">
                                <option value="">请选择</option>
                            </select>
                            <select name="receiptCity" id="receiptCity">
                                <option value="">请选择</option>
                            </select>
                            <select name="receiptArea" id="receiptArea">
                                <option value="">请选择</option>
                            </select>
                            <input type="text" name="receiptAddress" id="receiptAddress" value="<?=$address['address']?>" class="regular-text"> </td>
                    </tr>
                    <tr>
                        <th><label for="team">所属战队</label></th>
                        <td>
                            <select class="js-data-select-ajax" name="team" style="width: 50%" data-action="get_team_list" data-type="<?=$team->ID?>">
                                <option value="0" selected="selected"><?=$team->post_title?></option>
                            </select>

                        </td>
                    </tr>
                    <tr>
                        <th><label for="wechat">绑定微信</label></th>
                        <td>
                            <input type="text" name="wechat" id="wechat" value="<?=isset($usermeta['wechat_nickname']) ? $usermeta['wechat_nickname'][0] : ''?>" disabled="disabled" class="regular-text">
                            <?=$user['weChat_openid'] ? '<span style="color: #00aff9; cursor: pointer" id="relieveWechat">解绑</span>' : ''?>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="mobile">手机号码</label></th>
                        <td><input type="text" name="mobile" id="mobile" value="<?=$user['user_mobile']?>" class="regular-text"> </td>
                    </tr>
                    <tr>
                        <th><label for="email">联系邮箱</label></th>
                        <td><input type="text" name="email" id="email" value="<?=$user['user_email']?>" class="regular-text"> </td>
                    </tr>
                    </tbody>
                </table>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="user_id" id="user_id" value="1">

                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="更新个人资料"></p>
            </form>

            <?php
            wp_register_script( 'student-languages',student_js_url.'validator/verify-ZH-CN.js',array('jquery'), leo_student_version  );
            wp_enqueue_script( 'student-languages' );
            wp_localize_script('student-languages','verify_ZH',[
                'required'=>__('必填项不能为空','nlyd-student'),
                'phone'=>__('手机号格式不正确','nlyd-student'),
                'email'=>__('邮箱格式不正确','nlyd-student'),
                'url'=>__('链接格式不正确','nlyd-student'),
                'number'=>__('只能填写数字','nlyd-student'),
                'date'=>__('日期格式不正确','nlyd-student'),
                'identity'=>__('请输入正确的身份证号','nlyd-student'),
                'phoneOrEmail'=>__('手机号或邮箱格式不正确','nlyd-student'),
                'password'=>__('密码必须是6位以上含字母及数字','nlyd-student'),
                'differPass'=>__('两次输入密码不一致','nlyd-student'),
                'chineseName'=>__('姓名格式不正确','nlyd-student'),
                'filterSqlStr'=>__('含有非法字符','nlyd-student'),
            ]);
            ?>


            <script>

                jQuery(document).ready(function($) {

                    //省市区三级联动
                    // console.log(provicone_html);
                    // $('#whereProvicone').html(where_provicone_html);
                    // $('#receiptProvicone').html(receipt_provicone_html);
                    initAddress('where','<?=isset($whereAddress['province'])?$whereAddress['province']:''?>',
                        '<?=isset($whereAddress['city'])?$whereAddress['city']:''?>',
                        '<?=isset($whereAddress['area'])?$whereAddress['area']:''?>');
                    initAddress('receipt','<?=isset($address['province'])?$address['province']:''?>',
                        '<?=isset($address['city'])?$address['city']:''?>',
                        '<?=isset($address['area'])?$address['area']:''?>');
                    function initAddress(_name, province,city,area){
                        var provicone_html = '<option data-index="-1" value="-1">请选择</option>'
                        var city_html = '<option data-index="-1" value="-1">请选择</option>';
                        var area_html = '',selectedArea='';
                        $.each($.validationLayui.allArea.area,function(index,value){
                            if(province == value.value){
                                provicone_html += '<option data-index="'+index+'" selected="selected" value="'+value.value+'">'+value.value+'</option>';
                                $.each(value.childs,function (cityIndex,cityValue) {
                                    if(cityValue.value == city){
                                        city_html += '<option data-index="'+index+'_'+cityIndex+'" selected="selected" value="'+cityValue.value+'">'+cityValue.value+'</option>';
                                        $.each(cityValue.childs,function (areaIndex,areaValue) {
                                            selectedArea = areaValue.value == area ?'selected="selected"':'';
                                            area_html += '<option data-index="'+areaIndex+'" '+selectedArea+' value="'+areaValue.value+'">'+areaValue.value+'</option>';
                                        });
                                    }else{
                                        city_html += '<option data-index="'+index+'_'+cityIndex+'"  value="'+cityValue.value+'">'+cityValue.value+'</option>';
                                    }
                                });
                            }else{
                                provicone_html += '<option data-index="'+index+'"  value="'+value.value+'">'+value.value+'</option>';
                            }
                        });
                        $('#'+_name+'Provicone').html(provicone_html);
                        $('#'+_name+'City').html(city_html);
                        $('#'+_name+'Area').html(area_html);
                    }

                    $('#whereProvicone').on('change', function () {
                        changeProvicone($(this),'where');
                    });
                    $('#receiptProvicone').on('change', function () {
                        changeProvicone($(this),'receipt');
                    });
                    function changeProvicone(_this,_name){
                        var val = _this.find('option:selected').attr('data-index');
                        if(val > -1){
                            var city_html = '';
                            $.each($.validationLayui.allArea.area[val].childs,function (cindex,cvalue) {
                                city_html += '<option data-index="'+val+'_'+cindex+'" value="'+cvalue.value+'">'+cvalue.value+'</option>';
                            });
                            $('#'+_name+'City').html(city_html);
                            var area_html = '';
                            $.each($.validationLayui.allArea.area[val].childs[0].childs,function (aindex,avalue) {
                                area_html += '<option data-index="'+aindex+'" value="'+avalue.value+'">'+avalue.value+'</option>';
                            });
                            $('#'+_name+'Area').html(area_html);
                        }else{
                            $('#'+_name+'City').html('');
                            $('#'+_name+'Area').html('');
                        }
                    }
                    $('#whereCity').on('change', function () {
                        changeCity($(this),'where');
                    });
                    $('#receiptCity').on('change', function () {
                        changeCity($(this),'receipt');
                    });
                    function changeCity(_this,_name) {
                        var val = _this.find('option:selected').attr('data-index');
                        val = val.split('_');
                        if(val[0] > -1){
                            var area_html = '';
                            $.each($.validationLayui.allArea.area[val[0]].childs[val[1]].childs,function (aindex,avalue) {
                                area_html += '<option value="'+avalue.value+'">'+avalue.value+'</option>';
                            });
                            $('#'+_name+'Area').html(area_html);
                        }else{
                            $('#'+_name+'Area').html('');
                        }
                    }
                    $('#card_num').on('change',function () {
                        var age = GetAge($(this).val());
                        if(age > 0) $("#age").val(age);
                    });
                    function GetAge(identityCard) {
                        var len = (identityCard + "").length;
                        if (len == 0) {
                            return 0;
                        } else {
                            if ((len != 15) && (len != 18))//身份证号码只能为15位或18位其它不合法
                            {
                                return 0;
                            }
                        }
                        var strBirthday = "";
                        if (len == 18)//处理18位的身份证号码从号码中得到生日和性别代码
                        {
                            strBirthday = identityCard.substr(6, 4) + "/" + identityCard.substr(10, 2) + "/" + identityCard.substr(12, 2);
                        }
                        if (len == 15) {
                            strBirthday = "19" + identityCard.substr(6, 2) + "/" + identityCard.substr(8, 2) + "/" + identityCard.substr(10, 2);
                        }
                        //时间字符串里，必须是“/”
                        var birthDate = new Date(strBirthday);
                        var nowDateTime = new Date();
                        var age = nowDateTime.getFullYear() - birthDate.getFullYear();
                        //再考虑月、天的因素;.getMonth()获取的是从0开始的，这里进行比较，不需要加1
                        if (nowDateTime.getMonth() < birthDate.getMonth() || (nowDateTime.getMonth() == birthDate.getMonth() && nowDateTime.getDate() < birthDate.getDate())) {
                            age--;
                        }
                        return age;
                    }

                    /**
                     * 解绑微信
                     */
                    $('#relieveWechat').on('click',function () {
                        if(confirm('是否确认解绑?解绑后将无法恢复!')){
                            $.ajax({
                                url : ajaxurl,
                                data : {'action':'relieveWechat','user_id':'<?=$user_id?>'},
                                dataType : 'json',
                                type : 'post',
                                success : function (response) {
                                    alert(response.data.info);
                                    if(response['success'] == true){
                                        window.location.reload();
                                    }
                                },error : function () {
                                    alert('请求失败!');
                                }
                            });
                        }
                    })
                    /**
                     * 删除图片
                     */
                    $('.cardImg').on('click', function () {
                        if(confirm('是否确认删除证件照片?')){
                            var k = $(this).attr('data-k');
                            var user_id = '<?=$user_id?>';
                            var _this = $(this);
                            $.ajax({
                                url : ajaxurl,
                                data : {'action' : 'delCardImg', 'k':k, 'user_id':user_id},
                                dataType : 'json',
                                type : 'post',
                                success : function (response) {
                                    alert(response.data.info);
                                    if(response['success']){
                                        _this.closest('div').remove();
                                    }
                                }, error : function () {
                                    alert('请求失败!');
                                }
                            });
                        }
                    });
                })

            </script>
        </div>
        <?php
    }

}

new Users();