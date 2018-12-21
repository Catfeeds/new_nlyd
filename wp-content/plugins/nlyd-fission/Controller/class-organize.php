<?php
//组织主体控制器
class Organize{
    public function __construct($is_list = false)
    {
        if($is_list === false){
            add_action( 'admin_menu', array($this,'register_organize_menu_page') );
            add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
        }
    }
    public function register_organize_menu_page(){
        if ( current_user_can( 'administrator' ) && !current_user_can( 'fission' ) ) {
            global $wp_roles;
            $role = 'organize_detail';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'add_organize';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'organize_type';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'add_organize_type';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'organize_power';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'add_organize_power';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'organize_coach';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'add_organize_coach';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'organize_income_log';//权限名
            $wp_roles->add_cap('administrator', $role);
        }
        add_submenu_page('fission','主体详情','主体详情','organize_detail','fission-organize-detail',array($this,'organizeDetails'));
        add_submenu_page('fission','主体类型','主体类型','organize_type','fission-organize-type',array($this,'organizeType'));
        add_submenu_page('fission','主体权限','主体权限','organize_power','fission-organize-power',array($this,'organizePower'));
        add_submenu_page('fission','主体成员','主体成员','organize_coach','fission-organize-coach',array($this,'organizeCoach'));
        add_submenu_page('fission','新增主体','新增主体','add_organize','fission-add-organize',array($this,'addOrganize'));
        add_submenu_page('fission','新增主体类型','新增主体类型','add_organize_type','fission-add-organize-type',array($this,'addOrganizeType'));
        add_submenu_page('fission','新增主体权限','新增主体权限','add_organize_power','fission-add-organize-power',array($this,'addOrganizePower'));
        add_submenu_page('fission','新增主体成员','新增主体成员','add_organize_coach','fission-add-organize-coach',array($this,'addOrganizeCoach'));
        add_submenu_page('fission','主体收益记录','主体收益记录','organize_income_log','fission-organize-income-log',array($this,'organizeIncomeLog'));
    }

    /**
     *主体列表
     */
    public function organizeList(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $type = isset($_GET['ctype']) ? intval($_GET['ctype']) : 0;
        $status_type = isset($_GET['stype']) ? intval($_GET['stype']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';

        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $where = "WHERE zm.user_status='{$status_type}'";
        $leftJoin = '';
        $joinWhere = '';
        if($type>0){
            $where .= " AND zm.type_id='{$type}'";
        }

        if($searchStr != ''){
            $leftJoin = " LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=zm.user_id AND um.meta_key='user_real_name'";
            $joinWhere = " AND (zm.zone_name LIKE '%{$searchStr}%' OR um.meta_value LIKE '%{$searchStr}%' OR u.user_mobile LIKE '%{$searchStr}%' OR u.user_login LIKE '%{$searchStr}%')";
        }
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS u.user_login,u.user_mobile,zm.user_id,zm.type_id,zm.referee_id,zm.created_time,zm.audit_time,zm.user_status,zt.zone_type_name,zm.zone_name,zm.is_able,
                zm.zone_address,zm.business_licence,zm.business_licence_url,
                zm.legal_person,zm.opening_bank,zm.opening_bank_address,zm.bank_card_num,zm.id,
                zm.chairman_id,zm.secretary_id,
                CASE zm.user_status 
                WHEN 1 THEN '正常' 
                WHEN -1 THEN '正在审核' 
                WHEN -2 THEN '未通过' 
                END AS user_status_name,
                CASE zm.is_able 
                WHEN 1 THEN '正常' 
                WHEN 2 THEN '被冻结' 
                ELSE '-' 
                END AS able_name 
                FROM {$wpdb->prefix}zone_meta AS zm 
                LEFT JOIN `{$wpdb->users}` AS u ON u.ID=zm.user_id 
                LEFT JOIN `{$wpdb->prefix}zone_type` AS zt ON zt.id=zm.type_id 
                {$leftJoin} 
                {$where} 
                {$joinWhere} 
                LIMIT {$start},{$pageSize}",ARRAY_A);
//        leo_dump($wpdb->last_query);die;
        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page
        ));
        //各种数量
        $numSql = "SELECT count(id) FROM {$wpdb->prefix}zone_meta";
        $ok_num  = $wpdb->get_var($numSql.' WHERE user_status=1');
        $apply_num  = $wpdb->get_var($numSql.' WHERE user_status=-1');
        $refuse_num  = $wpdb->get_var($numSql.' WHERE user_status=-2');
        $numSql .= " WHERE user_status='{$status_type}'";
        $all_num = $wpdb->get_var($numSql);
        //类型列表
        $typeList = $wpdb->get_results("SELECT id,zone_type_name FROM {$wpdb->prefix}zone_type", ARRAY_A);
        $typeListCount = count($typeList)-1;
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">主体列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize')?>" class="page-title-action">添加主体</a>

            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤主体列表</h2>
            <ul class="subsubsub">
                <li class="all"><a href="<?=admin_url('admin.php?page=fission&stype=1&ctype='.$type)?>" <?=$status_type===1?'class="current"':''?> aria-current="page">已通过<span class="count">（<?=$ok_num?>）</span></a> |</li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission&stype=-1&ctype='.$type)?>" <?=$status_type===-1?'class="current"':''?> aria-current="page">待审核<span class="count">（<?=$apply_num?>）</span></a> |</li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission&stype=-2&ctype='.$type)?>" <?=$status_type===-2?'class="current"':''?> aria-current="page">未通过<span class="count">（<?=$refuse_num?>）</span></a> </li>
            </ul>
            <br class="clear">
            <ul class="subsubsub">
                <li class="all"><a href="<?=admin_url('admin.php?page=fission&ctype=0&stype='.$status_type)?>" <?=$type===0?'class="current"':''?> aria-current="page">全部<span class="count">（<?=$all_num?>）</span></a> |</li>
                <?php
                foreach ($typeList as $tlk => $tlv){
                    $typeNum = $wpdb->get_var($numSql." AND type_id='{$tlv['id']}'");
                ?>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission&ctype='.$tlv['id']).'&stype='.$status_type?>" <?=$type==$tlv['id']?'class="current"':''?> aria-current="page"><?=$tlv['zone_type_name']?><span class="count">（<?=$typeNum>0?$typeNum:0?>）</span></a><?=$tlk<$typeListCount?' | ':''?></li>
                <?php
                }
                ?>
            </ul>


            <p class="search-box">
                <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                <input type="search" id="search_val" name="search_val" placeholder="名称/负责人/手机/用户名" value="<?=$searchStr?>">
                <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=fission&ctype='.$type.'&stype='.$status_type.'&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
            </p>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
                        <option value="agree">通过申请</option>
                        <option value="refuse">拒绝申请</option>
                        <option value="frozen">冻结</option>
                        <option value="thaw">解冻</option>
                    </select>
                    <input type="button" id="doaction" class="button action all_options" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">主体列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                        <th scope="col" id="zone_title" class="manage-column column-zone_title column-primary">主体名称</th>
                        <th scope="col" id="real_name" class="manage-column column-real_name">负责人姓名</th>
                        <th scope="col" id="referee_id" class="manage-column column-referee_id">推荐人</th>
                        <th scope="col" id="legal_person" class="manage-column column-legal_person">法人</th>
                        <th scope="col" id="chairman_id" class="manage-column column-chairman_id">主席</th>
                        <th scope="col" id="zone_address" class="manage-column column-zone_address">地址</th>
                        <th scope="col" id="business_licence" class="manage-column column-business_licence">营业执照</th>
                        <th scope="col" id="bank_card_num" class="manage-column column-bank_card_num">银行卡</th>
                        <th scope="col" id="zone_type" class="manage-column column-zone_type">主体类型</th>
                        <th scope="col" id="zone_status" class="manage-column column-zone_status">申请状态</th>
                        <th scope="col" id="able_status" class="manage-column column-able_status">冻结状态</th>
                        <th scope="col" id="view_member" class="manage-column column-view_member">查看成员</th>
                        <th scope="col" id="profit" class="manage-column column-profit">收益提现</th>
                        <th scope="col" id="created_time" class="manage-column column-created_time">提交时间</th>
                        <th scope="col" id="audit_time" class="manage-column column-audit_time">审核时间</th>
                        <th scope="col" id="options1" class="manage-column column-options1">操作</th>
                    </tr>
                 </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                   <?php
                   foreach ($rows as $row){
                        $usermeta = get_user_meta($row['user_id']);
                        $user_real_name = isset($usermeta['user_real_name']) ? $usermeta['user_real_name'][0] : [];
                        $referee_real_name = get_user_meta($row['referee_id'],'user_real_name',true);
                        $chairman_real_name = get_user_meta($row['chairman_id'],'user_real_name',true);
                   ?>
                   <tr data-uid="<?=$row['user_id']?>">
                       <th scope="row" class="check-column">
                           <label class="screen-reader-text" for="cb-select-407">选择<?=$row['zone_name']?></label>
                           <input id="cb-select-<?=$row['user_id']?>" type="checkbox" name="post[]" value="<?=$row['user_id']?>">
                           <div class="locked-indicator">
                               <span class="locked-indicator-icon" aria-hidden="true"></span>
                               <span class="screen-reader-text">“<?=$row['zone_name']?>”已被锁定</span>
                           </div>
                       </th>
                       <td class="zone_title column-zone_title has-row-actions column-primary" data-colname="主体名称">
                            <?=$row['zone_name']?>
                           <br>
                           <div class="row-actions">
                               <span class="edit"><a href="<?=admin_url('admin.php?page=fission-add-organize&id='.$row['id'])?>">编辑</a></span>
<!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
<!--                               <span class="view"><a href="">资料</a></span>-->
                           </div>
                           <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                       </td>
                       <td class="real_name column-real_name" data-colname="负责人姓名"><?=isset($user_real_name['real_name'])?$user_real_name['real_name']:$row['user_login']?></td>
                       <td class="referee_id column-referee_id" data-colname="推荐人"><?=isset($referee_real_name['real_name'])?$referee_real_name['real_name']:($row['referee_id']>0?get_user_by('ID',$row['referee_id'])->user_login:'')?></td>

                       <td class="legal_person column-legal_person" data-colname="法人"><?=$row['legal_person']?></td>
                       <td class="chairman_id column-chairman_id" data-colname="主席"><?=isset($chairman_real_name['real_name'])?$chairman_real_name['real_name']:($row['chairman_id']>0?get_user_by('ID',$row['chairman_id'])->user_login:'')?></td>
                       <td class="zone_address column-zone_address" data-colname="地址"><?=$row['zone_address']?></td>
                       <td class="business_licence column-business_licence" data-colname="营业执照" id="cardImg-<?=$row['user_id']?>">
                           <?=$row['business_licence']?>
                           <img src="<?=$row['business_licence_url']?>" style="height: 60px;" alt="">
                       </td>
                       <td class="bank_card_num column-bank_card_num" data-colname="银行卡"><?=$row['bank_card_num']?>(<?=$row['opening_bank']?>)</td>

                       <td class="zone_type column-zone_type" data-colname="主体类型"><?=$row['zone_type_name']?></td>
                       <td class="zone_status column-zone_status" data-colname="申请状态">
                           <span style="<?=$row['user_status'] == '-1'?'color:#00c415':''?>"><?=$row['user_status_name']?></span>
                       </td>
                       <td class="able_status column-able_status" data-colname="冻结状态">
                           <span style="<?=$row['is_able'] == '2'?'color:#c41800':''?>"><?=$row['able_name']?></span>
                       </td>
                       <td class="view_member column-view_member" data-colname="查看成员"><a href="<?=admin_url('admin.php?page=fission-organize-coach&user_id='.$row['user_id'])?>">查看成员</a></td>
                       <td class="profit column-profit" data-colname="收益提现"><a href="<?=admin_url('admin.php?page=fission-organize-income-log&id='.$row['id'])?>">查看记录</a></td>
                       <td class="created_time column-created_time" data-colname="提交时间"><?=$row['created_time']?></td>
                       <td class="audit_time column-audit_time" data-colname="审核时间"><?=$row['audit_time']?></td>

                       <td class="options1 column-options1" data-colname="操作">
                       <?php
                       //操作列表
                       $optionsArr = [];
                       if($row['user_status'] == '-1'){
                           array_push($optionsArr,"<a href='javascript:;' data-type='agree' class='edit-agree'>通过</a>");
                           array_push($optionsArr,"<a href='javascript:;' data-type='refuse' class='edit-refuse'>拒绝</a>");
                       }
                       if($row['user_status'] == '1'){
                           switch ($row['is_able']){
                               case 1:
                                   array_push($optionsArr,"<a href='javascript:;' data-type='frozen' class='edit-frozen'>冻结</a>");
                                   break;
                               case 2:
                                   array_push($optionsArr,"<a href='javascript:;' data-type='thaw' class='edit-thaw'>解冻</a>");
                                   break;
                           }
                       }
                       echo join(' | ',$optionsArr);
                       ?>
                       </td>
                   </tr>
                   <?php
                   }
                   ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                        <th scope="col" class="manage-column column-zone_title column-primary">主体名称</th>
                        <th scope="col" class="manage-column column-real_name">负责人姓名</th>
                        <th scope="col" class="manage-column column-referee_id">推荐人</th>
                        <th scope="col" class="manage-column column-legal_person">法人</th>
                        <th scope="col" class="manage-column column-chairman_id">主席</th>
                        <th scope="col" class="manage-column column-zone_address">地址</th>
                        <th scope="col" class="manage-column column-business_licence">营业执照</th>
                        <th scope="col" class="manage-column bank_card_num-referee_id">银行卡</th>
                        <th scope="col" class="manage-column column-zone_type">主体类型</th>
                        <th scope="col" class="manage-column column-zone_status">申请状态</th>
                        <th scope="col" class="manage-column column-able_status">冻结状态</th>
                        <th scope="col" class="manage-column column-view_member">查看成员</th>
                        <th scope="col" class="manage-column column-profit">收益提现</th>
                        <th scope="col" class="manage-column column-created_time">提交时间</th>
                        <th scope="col" class="manage-column column-audit_time">审核时间</th>
                        <th scope="col" class="manage-column column-options1">操作</th>
                    </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
                        <option value="agree">通过申请</option>
                        <option value="refuse">拒绝申请</option>
                        <option value="frozen">冻结</option>
                        <option value="thaw">解冻</option>
                    </select>
                    <input type="button" id="doaction2" class="button action all_options" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>

            <br class="clear">
            <script>
                jQuery(document).ready(function($) {
                    function postAjax(_this,action,type){
                        if(type == 'all'){
                            var _select = _this.prev('select');
                            var _type = _select.val();
                            if(_type == false || _type == '' || _type == '-1') return false;
                            var user_id = [];
                            $.each($('#the-list').find('tr').find('input[type="checkbox"]:checked'),function (i,v) {
                                user_id.push($(v).val());
                            });
                           if('agree' == _type || 'refuse' == _type){
                               action = 'editOrganizeApply';
                           }else if('frozen' == _type || 'thaw' == _type){
                               action = 'editOrganizeAble';
                           }else{
                               return false;
                           }
                            user_id = user_id.join(',');
                            var request_type = _type;
                        }else{
                            var user_id = _this.closest('tr').attr('data-uid');
                            var request_type = _this.attr('data-type');
                        }
                        if(user_id == false || user_id == '') return false;
                        var data = {'action':action,'user_id':user_id,'request_type':request_type}
                        $.ajax({
                            url:ajaxurl,
                            data : data,
                            dataType : 'json',
                            type : 'post',
                            success : function (response) {
                                alert(response.data.info);
                                if(response['success']){
                                    window.location.reload();
                                }
                            },error : function () {
                                alert('请求失败!');
                            }
                        });
                    }
                    $('.edit-agree').on('click',function () {
                        postAjax($(this),'editOrganizeApply','');
                    });
                    $('.edit-refuse').on('click',function () {
                        postAjax($(this),'editOrganizeApply','');
                    });
                    $('.edit-frozen').on('click',function () {
                        postAjax($(this),'editOrganizeAble','');
                    });
                    $('.edit-thaw').on('click',function () {
                        postAjax($(this),'editOrganizeAble','');
                    });
                    //批量
                    $('.all_options').on('click',function () {
                        postAjax($(this),'','all');
                    });

                    layui.use('layer', function(){
                        var layer = layui.layer;
                        var _title = '';
                        <?php foreach ($rows as $row){ ?>
                        layer.photos({//图片预览
                            photos: '#cardImg-<?=$row['user_id']?>',
                            move : false,
                            anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                        })
                        <?php } ?>
                    });
                });
            </script>
        </div>
        <?php
    }

    /**
     * 新增/编辑主体类型
     */
    public function addOrganizeType(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        global $wpdb;
        if(is_post()){
            $success_msg = '';
            $error_msg = '';
            $zone_type_name = isset($_POST['zone_type_name']) ? trim($_POST['zone_type_name']) : '';
            $zone_type_alias = isset($_POST['zone_type_alias']) ? trim($_POST['zone_type_alias']) : '';
            $zone_type_status = isset($_POST['zone_type_status']) ? intval($_POST['zone_type_status']) : 0;
            $match_power = isset($_POST['match_power']) ? $_POST['match_power'] : [];
            $course_power = isset($_POST['course_power']) ? $_POST['course_power'] : [];
            if($zone_type_name == '') $error_msg = '请填写类型名称';
            if(!is_array($match_power)) $error_msg = $error_msg==''?'赛事权限错误':$error_msg.'<br >赛事权限错误';
            if(!is_array($course_power)) $error_msg = $error_msg==''?'课程权限错误':$error_msg.'<br >课程权限错误';
            if($zone_type_alias == '') $error_msg = $error_msg==''?'请填写类型别名':$error_msg.'<br >请填写类型别名';
            if($zone_type_status != 1 && $zone_type_status != 2) $error_msg = $error_msg==''?'请选择类型状态':$error_msg.'<br >请选择类型状态';

            $match_role_ids = join(',',$match_power);
            $course_role_ids = join(',',$course_power);
            if($error_msg == ''){
                $insertData = [
                    'zone_type_name' => $zone_type_name,
                    'zone_type_alias' => $zone_type_alias,
                    'zone_type_status' => $zone_type_status,
                ];
                $wpdb->query('START TRANSACTION');
                if($id > 0){
                    $bool = $wpdb->update($wpdb->prefix.'zone_type',$insertData,['id'=>$id]);
                    $powerOne = $wpdb->get_row("SELECT id,match_role_id,role_id FROM {$wpdb->prefix}zone_join_role WHERE zone_type_id='{$id}'", ARRAY_A);
                    if($powerOne && $powerOne['match_role_id'] == $match_role_ids && $powerOne['role_id'] == $course_role_ids){
                       if(!$bool){
                           $wpdb->query('ROLLBACK');
                           $error_msg = '操作失败!';
                       }else{
                           $wpdb->query('COMMIT');
                           $success_msg = '操作成功';
                       }
                    }else{
                        if($powerOne){
                            $powerBool = $wpdb->update($wpdb->prefix.'zone_join_role', ['match_role_id'=>$match_role_ids,'role_id'=>$course_role_ids],['id'=>$powerOne['id']]);
                        }else{
                            $powerBool = $wpdb->insert($wpdb->prefix.'zone_join_role', ['zone_type_id'=>$id,'match_role_id'=>$match_role_ids,'role_id'=>$course_role_ids]);
                        }
                        if($powerBool) {
                            $wpdb->query('COMMIT');
                            $success_msg = '操作成功';
                        }else{
                            $wpdb->query('ROLLBACK');
                            $error_msg = '操作失败!';
                        }
                    }
                }else{
                    $bool = $wpdb->insert($wpdb->prefix.'zone_type',$insertData);
                    if($bool){
                        if(!empty($match_power) || !empty($course_power)){
                            $zone_type_id = $wpdb->insert_id;
                            $match_role_ids = join(',',$match_power);
                            $course_role_ids = join(',',$course_power);
                            $powerSql = "INSERT INTO {$wpdb->prefix}zone_join_role (`zone_type_id`,`match_role_id`,`role_id`) VALUES ('{$zone_type_id}','{$match_role_ids}','{$course_role_ids}')";
                            $powerBool = $wpdb->query($powerSql);
                            if($powerBool) {
                                $wpdb->query('COMMIT');
                                $success_msg = '操作成功';
                            }else{
                                $wpdb->query('ROLLBACK');
                                $error_msg = '操作失败!';
                            }
                        }else{
                            $wpdb->query('COMMIT');
                            $success_msg = '操作成功';
                        }
                    }else{
                        $wpdb->query('ROLLBACK');
                        $error_msg = '操作失败!';
                    }
                }
            }
        }
        $oldMatchPowerList = [];   //已有赛事/考级权限
        $oldPowerList = [];            //已有课程/考级权限
        if($id > 0){
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}zone_type WHERE id='{$id}'", ARRAY_A);

            $oldPowerLists = $wpdb->get_row("SELECT match_role_id,role_id FROM {$wpdb->prefix}zone_join_role WHERE zone_type_id='{$id}'", ARRAY_A);
            $oldMatchPowerList = explode(',', $oldPowerLists['match_role_id']);   //已有赛事权限
            $oldPowerList = explode(',', $oldPowerLists['role_id']);            //已有基础权限
        }
        //权限列表
        $matchPowerList = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}zone_match_role",ARRAY_A);
        $powerList = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}zone_type_role", ARRAY_A);
        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加/编辑主体类型</h1>

            <div id="ajax-response">
                <span style="color: #2bc422"><?=$success_msg?></span>
                <span style="color: #c44e00"><?=$error_msg?></span>
            </div>

            <form method="post" action="" id="adduser" class="validate" novalidate="novalidate">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="5f6ea9ff44"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php"><table class="form-table">
                    <tbody>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="zone_type_name">类型名称 </label></th>
                        <td>
                            <input name="zone_type_name" type="text" id="zone_type_name" value="<?=isset($row['zone_type_name'])?$row['zone_type_name']:''?>" maxlength="60">
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="zone_type_alias">类型别名 </label></th>
                        <td>
                            <input name="zone_type_alias" type="text" id="zone_type_alias" value="<?=isset($row['zone_type_alias'])?$row['zone_type_alias']:''?>" maxlength="60">
                        </td>
                    </tr>

                    <tr class="">
                        <th scope="row"><label for="zone_type_status">状态 </label></th>
                        <td>
                            <input type="radio" <?=isset($row['zone_type_status']) && $row['zone_type_status'] == '1'?'checked="checked"':''?> name="zone_type_status" id="zone_type_status_1" value="1"><label for="zone_type_status_1">正常</label>  &ensp;
                            <input type="radio" <?=isset($row['zone_type_status']) && $row['zone_type_status'] == '2'?'checked="checked"':''?> name="zone_type_status" id="zone_type_status_2" value="2"><label for="zone_type_status_2">关闭</label>


                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row">比赛权限</th>
                        <td>
                            <?php
                            foreach ($matchPowerList as $mplv){
                            ?>
                                <label for="match_power_<?=$mplv['id']?>"><input <?=in_array($mplv['id'],$oldMatchPowerList)?'checked="checked"':''?> id="match_power_<?=$mplv['id']?>" type="checkbox" name="match_power[]" value="<?=$mplv['id']?>"><?=$mplv['role_name']?></label>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row">基础权限</th>
                        <td>
                            <?php
                            foreach ($powerList as $plv){
                                ?>
                                <label for="course_power_<?=$plv['id']?>"><input <?=in_array($plv['id'],$oldPowerList)?'checked="checked"':''?> id="course_power_<?=$plv['id']?>" type="checkbox" name="course_power[]" value="<?=$plv['id']?>"><?=$plv['role_name']?></label>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <p class="submit"><input type="submit" class="button button-primary" value="提交"></p>
            </form>
        </div>
        <?php
    }

    /**
     * 主体类型列表
     */
    public function organizeType(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;

        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS id,zone_type_name,zone_type_alias,
                CASE zone_type_status 
                WHEN 1 THEN '<span style=\"color:#2ac40a;\">正常</span>' 
                WHEN 2 THEN '<span style=\"color:#c44f09;\">关闭</span>' 
                ELSE '-' 
                END AS zone_type_status_name 
                FROM {$wpdb->prefix}zone_type  
                LIMIT {$start},{$pageSize}",ARRAY_A);
        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page
        ));

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">主体类型列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize-type')?>" class="page-title-action">添加主体类型</a>

            <hr class="wp-header-end">
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
                        <option value="delete">删除</option>
                    </select>
                    <input type="submit" id="doaction" class="button action" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">主体列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th scope="col" id="name" class="manage-column column-name column-primary">名称</th>
                    <th scope="col" id="zone_type_alias" class="manage-column column-zone_type_alias">别名</th>
                    <th scope="col" id="status" class="manage-column column-status">状态</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){
                    ?>
                    <tr>
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="cb-select-407">选择<?=$row['zone_type_name']?></label>
                            <input id="cb-select-<?=$row['id']?>" type="checkbox" name="post[]" value="<?=$row['id']?>">
                            <div class="locked-indicator">
                                <span class="locked-indicator-icon" aria-hidden="true"></span>
                                <span class="screen-reader-text">“<?=$row['zone_type_name']?>”已被锁定</span>
                            </div>
                        </th>
                        <td class="name column-name has-row-actions column-primary" data-colname="名称">
                            <?=$row['zone_type_name']?>
                            <br>
                            <div class="row-actions">
                                <span class="edit"><a href="<?=admin_url('admin.php?page=fission-add-organize-type&id='.$row['id'])?>">编辑</a> </span>
                                <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                                <!--                               <span class="view"><a href="">资料</a></span>-->
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="zone_type_alias column-zone_type_alias" data-colname="别名">
                            <?=$row['zone_type_alias']?>
                        </td>
                        <td class="status column-status" data-colname="状态">
                            <?=$row['zone_type_status_name']?>
                        </td>

                    </tr>
                    <?php
                }
                ?>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-name column-primary">名称</th>
                    <th scope="col" class="manage-column column-zone_type_alias">别名</th>
                    <th scope="col" class="manage-column column-status">状态</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
                        <option value="delete">删除</option>
                    </select>
                    <input type="submit" id="doaction2" class="button action" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>

            <br class="clear">
        </div>
        <?php
    }

    /**
     * 新增/编辑主体
     */
    public function addOrganize(){
        global $wpdb;
        $old_zm_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if(is_post()){
            $success_msg = '';
            $error_msg = '';
            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $zone_type = isset($_POST['zone_type']) ? intval($_POST['zone_type']) : 0;
            $referee_id = isset($_POST['referee_id']) ? intval($_POST['referee_id']) : 0;
            $user_status = isset($_POST['user_status']) ? intval($_POST['user_status']) : 0;
            $zone_title = isset($_POST['zone_title']) ? trim($_POST['zone_title']) : '';
            $zone_address = isset($_POST['zone_address']) ? trim($_POST['zone_address']) : '';
            $business_licence = isset($_POST['business_licence']) ? trim($_POST['business_licence']) : '';
            $legal_person = isset($_POST['legal_person']) ? trim($_POST['legal_person']) : '';
            $opening_bank = isset($_POST['opening_bank']) ? trim($_POST['opening_bank']) : '';
            $opening_bank_address = isset($_POST['opening_bank_address']) ? trim($_POST['opening_bank_address']) : '';
            $bank_card_num = isset($_POST['bank_card_num']) ? trim($_POST['bank_card_num']) : '';
            $chairman_id = isset($_POST['chairman_id']) ? intval($_POST['chairman_id']) : 0;
            $secretary_id = isset($_POST['secretary_id']) ? intval($_POST['secretary_id']) : 0;
            $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
            $match_power = isset($_POST['match_power']) ? $_POST['match_power'] : [];
            $course_power = isset($_POST['course_power']) ? $_POST['course_power'] : [];

            if($user_id < 0) $error_msg = '请选择负责人';
            if($zone_type === 0) $error_msg = $error_msg==''?'请选择主体类型':$error_msg.'<br >请选择主体类型';
            if($user_id == $referee_id) $error_msg = $error_msg==''?'推荐人不能为主体账号':$error_msg.'<br >推荐人不能为主体账号';
            if(!is_array($match_power)) $error_msg = $error_msg==''?'赛事权限错误':$error_msg.'<br >赛事权限错误';
            if(!is_array($course_power)) $error_msg = $error_msg==''?'课程权限错误':$error_msg.'<br >课程权限错误';
            if($zone_title == '') $error_msg = $error_msg==''?'请填写主体名称':$error_msg.'<br >请填写主体名称';
            if($zone_address == '') $error_msg = $error_msg==''?'请填写机构地址':$error_msg.'<br >请填写机构地址';
            if($business_licence == '') $error_msg = $error_msg==''?'请填写营业执照':$error_msg.'<br >请填写营业执照';
            if($legal_person == '') $error_msg = $error_msg==''?'请填写法人':$error_msg.'<br >请填写法人';
            if($opening_bank == '') $error_msg = $error_msg==''?'请填写开户行':$error_msg.'<br >请填写开户行';
            if($opening_bank_address == '') $error_msg = $error_msg==''?'请填写开户行地址':$error_msg.'<br >请填写开户行地址';
            if($bank_card_num == '') $error_msg = $error_msg==''?'请填写银行卡号':$error_msg.'<br >请填写银行卡号';
            if($chairman_id < 1) $error_msg = $error_msg==''?'请选择组委会主席':$error_msg.'<br >请选择组委会主席';
            if($parent_id > 0){
                $old_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}zone_meta WHERE id='{$old_zm_id}'");
                if($old_id == $parent_id) $error_msg = $error_msg==''?'上级不能是自身':$error_msg.'<br >上级不能是自身';
            }

            if($error_msg == ''){
                $insertData = [
                    'type_id' => $zone_type,
                    'user_id' => $user_id,
                    'referee_id' => $referee_id,
                    'user_status' => $user_status,
                    'zone_name' => $zone_title,
                    'zone_address' => $zone_address,
                    'business_licence' => $business_licence,
                    'legal_person' => $legal_person,
                    'opening_bank' => $opening_bank,
                    'opening_bank_address' => $opening_bank_address,
                    'bank_card_num' => $bank_card_num,
                    'chairman_id' => $chairman_id,
                    'secretary_id' => $secretary_id,
                    'match_role_id' => join(',',$match_power),
                    'role_id' => join(',',$course_power),
                    'parent_id' => $parent_id,
                ];
                //图片
                if(isset($_FILES['business_licence_url'])){
                    $upload_dir = wp_upload_dir();
                    $dir = '/user/'.$user_id.'/';
                    //print_r($upd);
                    $file = saveIosFile($_FILES['business_licence_url']['tmp_name'],$upload_dir['basedir'].$dir);
                    if($file){
                        $insertData['business_licence_url'] = $upload_dir['baseurl'].$dir.$file;
                    }
                }

                if($old_zm_id>0){
                    $bool = $wpdb->update($wpdb->prefix.'zone_meta',$insertData,['id'=>$old_zm_id]);
                }else{
                    $insertData['created_time'] = get_time('mysql');
                    $insertData['audit_time'] = get_time('mysql');
                    $bool = $wpdb->insert($wpdb->prefix.'zone_meta',$insertData);
                }
                if(!$bool){
                    $error_msg = '操作失败!';
                    is_file($upload_dir['basedir'].$dir.$file) && unlink($upload_dir['basedir'].$dir.$file);
                }else{
                    $success_msg = '操作成功';

                }
            }
        }
        //类型列表
        $typeList = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}zone_type WHERE zone_type_status=1", ARRAY_A);
        if($old_zm_id > 0){
            $row = $wpdb->get_row("SELECT zm.user_id,zm.type_id,zm.referee_id,zm.user_status,u.user_mobile,u.user_login,um.meta_value AS user_real_name,zm.zone_name,
                   um2.meta_value AS referee_real_name,u2.user_login AS referee_login,u2.user_mobile AS referee_mobile,zm.zone_address,zm.business_licence,zm.business_licence_url,
                   zm.legal_person,zm.opening_bank,zm.opening_bank_address,zm.bank_card_num,um3.meta_value AS chairman_real_name,um4.meta_value AS secretary_real_name,
                   zm.chairman_id,zm.secretary_id,zm.match_role_id,zm.role_id,zmp.zone_name AS parent_name,zm.parent_id 
                   FROM {$wpdb->prefix}zone_meta AS zm 
                   LEFT JOIN {$wpdb->users} AS u ON u.ID=zm.user_id AND u.ID!='' 
                   LEFT JOIN {$wpdb->users} AS u2 ON u2.ID=zm.referee_id AND u2.ID!='' 
                   LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=zm.user_id AND um.meta_key='user_real_name' 
                   LEFT JOIN {$wpdb->usermeta} AS um2 ON um2.user_id=zm.referee_id AND um2.meta_key='user_real_name' 
                   LEFT JOIN {$wpdb->usermeta} AS um3 ON um3.user_id=zm.chairman_id AND um3.meta_key='user_real_name' 
                   LEFT JOIN {$wpdb->usermeta} AS um4 ON um4.user_id=zm.secretary_id AND um4.meta_key='user_real_name' 
                   LEFT JOIN {$wpdb->prefix}zone_meta AS zmp ON zmp.id=zm.parent_id  
                   WHERE zm.id='{$old_zm_id}'", ARRAY_A);
//            leo_dump($wpdb->last_query);die;
            $match_role_id = $row['match_role_id']; //已有赛事权限
            $role_id = $row['role_id']; //已有课程权限
        }else{
            $role_id = $wpdb->get_row("SELECT match_role_id,role_id FROM {$wpdb->prefix}zone_join_role WHERE zone_type_id='{$typeList[0]['id']}'",ARRAY_A);
            $match_role_id = $role_id['match_role_id'];
            $role_id = $role_id['role_id'];
        }
        $match_role_id = explode(',',$match_role_id);
        $role_id = explode(',',$role_id);
        //所有赛事权限
        $allMatchPowerList = $wpdb->get_results("SELECT id,role_name FROM {$wpdb->prefix}zone_match_role", ARRAY_A);
        //所有基础权限
        $allPowerList = $wpdb->get_results("SELECT id,role_name FROM {$wpdb->prefix}zone_type_role", ARRAY_A);
        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加/编辑主体</h1>

            <div id="ajax-response">
                <span style="color: #2bc422"><?=$success_msg?></span>
                <span style="color: #c44e00"><?=$error_msg?></span>
            </div>

            <form method="post" action="" class="validate" novalidate="novalidate" enctype="multipart/form-data">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="5f6ea9ff44">
                <input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php">
                <table class="form-table">
                    <tbody>
                    <tr class="">
                        <th scope="row"><label for="zone_title">主体名称 </label></th>
                        <td>
                            <input type="text" name="zone_title" id="zone_title" value="<?=$row['zone_name']?>">
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="user_id">负责人 </label></th>
                        <td>

                            <?php if($old_zm_id > 0){?>
                                <input type="hidden" name="user_id" value="<?=$row['user_id']?>">
                                <?=isset($row['user_real_name']) ? unserialize($row['user_real_name'])['real_name'] : $row['user_login']?>
                                <?=!empty($row['user_mobile'])?'('.$row['user_mobile'].')':''?>
                            <?php }else{ ?>
                                <select class="js-data-select-ajax" name="user_id" style="width: 50%" data-action="get_base_user_list" data-type="base">
                                    <option value="<?=$row['user_id']?>" selected="selected">
                                        <?=isset($row['user_real_name']) ? unserialize($row['user_real_name'])['real_name'] : $row['user_login']?>
                                        <?=!empty($row['user_mobile'])?'('.$row['user_mobile'].')':''?>
                                    </option>
                                </select>

                            <?php
                           }
                           ?>
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="zone_type">主体类型</label></th>
                        <td>
                            <select name="zone_type" <?=$old_zm_id < 1 ? 'id="zone_type"':''?>>
                                <?php foreach ($typeList as $tlv){ ?>
                                    <option value="<?=$tlv['id']?>" <?=$row['type_id']==$tlv['id']?'selected="selected"':''?> ><?=$tlv['zone_type_name']?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row">赛事权限</th>
                        <td id="match_power_td">
                            <?php foreach ($allMatchPowerList as $amplv){
                                ?>
                                <label for="match_power_<?=$amplv['id']?>"><input <?=in_array($amplv['id'],$match_role_id)?'checked="checked"':''?> id="match_power_<?=$amplv['id']?>" type="checkbox" name="match_power[]" value="<?=$amplv['id']?>"><?=$amplv['role_name']?></label>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row">基础权限</th>
                        <td id="course_power_td">
                            <?php foreach ($allPowerList as $plv){
                                ?>
                                <label for="course_power_<?=$plv['id']?>"><input <?=in_array($plv['id'],$role_id)?'checked="checked"':''?> id="course_power_<?=$plv['id']?>" type="checkbox" name="course_power[]" value="<?=$plv['id']?>"><?=$plv['role_name']?></label>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="referee_id">推荐人 </label></th>
                        <td>
                            <select class="js-data-select-ajax" name="referee_id" style="width: 50%" data-action="get_base_user_list" data-type="all">
                                <option value="<?=$row['referee_id']?>" selected="selected">
                                    <?=isset($row['referee_real_name']) ? unserialize($row['referee_real_name'])['real_name'] : $row['referee_login']?>
                                    <?=!empty($row['referee_mobile'])?'('.$row['referee_mobile'].')':''?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="parent_id">上级主体 </label></th>
                        <td>
                            <select class="js-data-select-ajax" name="parent_id" style="width: 50%" data-action="get_base_zone_list" data-type="all">
                                <option value="<?=$row['parent_id']?>" selected="selected">
                                    <?=$row['parent_name']?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="user_status">状态 </label></th>
                        <td>
                            <select name="user_status" id="user_status">
                                <option <?=$row['user_status']=='1'?'selected="selected"':''?> value="1">正常</option>
                                <option <?=$row['user_status']=='-1'?'selected="selected"':''?> value="-1">正在审核</option>
                                <option <?=$row['user_status']=='-2'?'selected="selected"':''?> value="-2">未通过</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="chairman_id">主席 </label></th>
                        <td>
                            <select class="js-data-select-ajax" name="chairman_id" style="width: 50%" data-action="get_base_user_list" data-type="base">
                                <option value="<?=$row['chairman_id']?>" selected="selected">
                                    <?=isset($row['chairman_real_name']) ? unserialize($row['chairman_real_name'])['real_name'] : ''?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="secretary_id">秘书长 </label></th>
                        <td>
                            <select class="js-data-select-ajax" name="secretary_id" style="width: 50%" data-action="get_base_user_list" data-type="base">
                                <option value="<?=$row['secretary_id']?>" selected="selected">
                                    <?=isset($row['secretary_real_name']) ? unserialize($row['secretary_real_name'])['real_name'] : ''?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="zone_address">机构地址 </label></th>
                        <td>
                            <input type="text" name="zone_address" value="<?=$row['zone_address']?>">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="business_licence">营业执照 </label></th>
                        <td>
                            <input type="text" name="business_licence" value="<?=$row['business_licence']?>">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="business_licence_url">营业执照照片 </label></th>
                        <td>
                            <img src="<?=$row['business_licence_url']?>" alt="" style="height: 80px;">
                            <input type="file" name="business_licence_url" id="business_licence_url">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="legal_person">法人 </label></th>
                        <td>
                            <input type="text" name="legal_person" value="<?=$row['legal_person']?>">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="opening_bank">开户行 </label></th>
                        <td>
                            <input type="text" name="opening_bank" value="<?=$row['opening_bank']?>">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="opening_bank_address">开户行地址 </label></th>
                        <td>
                            <input type="text" name="opening_bank_address" value="<?=$row['opening_bank_address']?>">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="bank_card_num">银行卡号 </label></th>
                        <td>
                            <input type="text" name="bank_card_num" value="<?=$row['bank_card_num']?>">
                        </td>
                    </tr>


                    </tbody>
                </table>


                <p class="submit"><input type="submit" name="createuser" id="createusersub" class="button button-primary" value="提交"></p>
            </form>
            <script>
                jQuery(document).ready(function($) {
                    $('#zone_type').on('change', function () {
                        var val = $(this).val();
                        $.ajax({
                            url : ajaxurl,
                            data : {'action':'getPowerListByType','val':val},
                            dataType : 'json',
                            type : 'post',
                            success : function (response) {
                                if(response['success']){
                                    var m_r_id = response.data.data.match_role_id != null ? response.data.data.match_role_id.split(','):[];
                                    var c_r_id = response.data.data.role_id != null ? response.data.data.role_id.split(','):[];
                                    $.each($('#match_power_td').find('input'),function (i,v) {
                                        if($.inArray($(v).val(),m_r_id) >= 0) {
                                            $(v).prop('checked','checked');
                                        }else{
                                            $(v).prop('checked','');
                                        }
                                    });
                                    $.each($('#course_power_td').find('input'),function (i,v) {
                                        if($.inArray($(v).val(),c_r_id) >= 0) {
                                            $(v).prop('checked',true);
                                        }else{
                                            $(v).prop('checked',false);
                                        }
                                    });
                                }
                            }
                        });
                    });
                });
            </script>
        </div>
        <?php
    }

    /**
     * 新增/编辑主体权限
     */
    public function addOrganizePower(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        global $wpdb;
        if(is_post()){
            $success_msg = '';
            $error_msg = '';
            $role_name = isset($_POST['role_name']) ? trim($_POST['role_name']) : '';
            $role_type = isset($_POST['role_type']) ? intval($_POST['role_type']) : 0;

            if($role_name == '') $error_msg = '请填写类型名称';
            if($role_type < 1) $error_msg = $error_msg==''?'权限类型参数错误':$error_msg.'<br >权限类型参数错误';

            if($error_msg == ''){
                $insertData = [
                    'role_name' => $role_name,
                    'role_type' => $role_type,
                ];
                if($id > 0){
                    $bool = $wpdb->update($wpdb->prefix.'zone_type_role',$insertData,['id'=>$id]);
                }else{
                    $bool = $wpdb->insert($wpdb->prefix.'zone_type_role',$insertData);
                }
                if($bool) $success_msg = '操作成功!';
                else $error_msg = '操作失败!';
            }
        }
        if($id > 0){
            $row = $wpdb->get_row("SELECT role_name,role_type FROM {$wpdb->prefix}zone_type_role WHERE id='{$id}'", ARRAY_A);
        }
        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加/编辑主体权限</h1>

            <div id="ajax-response">
                <span style="color: #2bc422"><?=$success_msg?></span>
                <span style="color: #c44e00"><?=$error_msg?></span>
            </div>

            <form method="post" action="" id="adduser" class="validate" novalidate="novalidate">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="5f6ea9ff44"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php"><table class="form-table">
                    <tbody>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="role_name">权限名称 </label></th>
                        <td>
                            <input name="role_name" type="text" id="role_name" value="<?=isset($row['role_name'])?$row['role_name']:''?>" maxlength="60">
                        </td>
                    </tr>

                    <tr class="form-field form-required">
                        <th scope="row"><label for="role_type">权限类型 </label></th>
                        <td>
                            <select name="role_type" id="role_type">
                                <option value="1" <?=$row['role_type'] == '1'?'selected="selected"':''?>>赛事/考级</option>
                                <option value="2" <?=$row['role_type'] == '2'?'selected="selected"':''?>>课程权限</option>
                            </select>
                        </td>
                    </tr>


                    </tbody>
                </table>

                <p class="submit"><input type="submit" class="button button-primary" value="提交"></p>
            </form>
        </div>
        <?php
    }

    /**
     * 主体权限
     */
    public function organizePower(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $type = isset($_GET['ctype']) ? intval($_GET['ctype']) : 0;
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $where = '';
        if($type > 0){
            $where = "WHERE role_type='{$type}'";
        }
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS id,role_name,role_type 
                FROM {$wpdb->prefix}zone_type_role 
                {$where} 
                LIMIT {$start},{$pageSize}",ARRAY_A);
        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page
        ));
        //数量
        //全部
        $sql = "SELECT COUNT(id) FROM {$wpdb->prefix}zone_type_role";
        $all_num = $wpdb->get_var($sql);
        $match_num = $wpdb->get_var($sql." WHERE role_type='1'");
        $course_num = $wpdb->get_var($sql." WHERE role_type='2'");
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">主体权限列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize-power')?>" class="page-title-action">添加主体权限</a>

            <hr class="wp-header-end">
            <ul class="subsubsub">
                <li class="all"><a href="<?=admin_url('admin.php?page=fission-organize-power&ctype=0')?>" <?=$type===0?'class="current"':''?> aria-current="page">全部<span class="count">（<?=$all_num?>）</span></a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission-organize-power&ctype=1')?>" <?=$type===1?'class="current"':''?> aria-current="page">赛事/考级<span class="count">（<?=$match_num?>）</span></a> |</li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission-organize-power&ctype=2')?>" <?=$type===-2?'class="current"':''?> aria-current="page">课程权限<span class="count">（<?=$course_num?>）</span></a></li>
            </ul>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
                        <option value="delete">删除</option>
                    </select>
                    <input type="submit" id="doaction" class="button action" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">主体列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th scope="col" id="name" class="manage-column column-name column-primary">名称</th>
                    <th scope="col" id="role_type" class="manage-column column-role_type">类型</th>
                    <th scope="col" id="option1" class="manage-column column-option1">操作</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){
                    ?>
                    <tr>
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="cb-select-407">选择<?=$row['role_name']?></label>
                            <input id="cb-select-<?=$row['id']?>" type="checkbox" name="post[]" value="<?=$row['id']?>">
                            <div class="locked-indicator">
                                <span class="locked-indicator-icon" aria-hidden="true"></span>
                                <span class="screen-reader-text">“<?=$row['role_name']?>”已被锁定</span>
                            </div>
                        </th>
                        <td class="name column-name has-row-actions column-primary" data-colname="名称">
                            <?=$row['role_name']?>
                            <br>

                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="role_type column-role_type" data-colname="类型">
                            <?php
                            switch ($row['role_type']){
                                case '1':
                                    echo '赛斯/考级';
                                    break;
                                case '2':
                                    echo '课程权限';
                                    break;

                            }
                            ?>
                        </td>
                        <td class="option1 column-option1" data-colname="状态">
                            <a href="<?=admin_url('admin.php?page=fission-add-organize-power&id='.$row['id'])?>">编辑</a>
                        </td>

                    </tr>
                    <?php
                }
                ?>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-name column-primary">名称</th>
                    <th scope="col" class="manage-column column-role_type">类型</th>
                    <th scope="col" class="manage-column column-option1">操作</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
                        <option value="delete">删除</option>
                    </select>
                    <input type="submit" id="doaction2" class="button action" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>

            <br class="clear">
        </div>
        <?php
    }

    /**
     * 主体成员
     */
    public function organizeCoach(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        if($user_id < 1) exit('参数错误!');
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS u.user_login,u.user_mobile,zjc.coach_id  
                FROM {$wpdb->prefix}zone_join_coach AS zjc 
                LEFT JOIN {$wpdb->users} AS u ON u.ID=zjc.coach_id AND u.ID!='' 
                WHERE zjc.zone_id='{$user_id}' 
                LIMIT {$start},{$pageSize}",ARRAY_A);
        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page
        ));
        //主体资料
        $zone_meta = $wpdb->get_row("SELECT zone_title FROM {$wpdb->prefix}zone_meta WHERE user_id='{$user_id}'",ARRAY_A);
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?=$zone_meta['zone_title'].'-'?>成员</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize-coach&user_id='.$user_id)?>" class="page-title-action">添加成员</a>

            <hr class="wp-header-end">

            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
                        <option value="delete">删除</option>
                    </select>
                    <input type="submit" id="doaction" class="button action" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">主体列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th scope="col" id="real_name" class="manage-column column-real_name column-primary">姓名</th>
                    <th scope="col" id="ID" class="manage-column column-ID">ID</th>
                    <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                    <th scope="col" id="option1" class="manage-column column-option1">操作</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){
                    $usermeta = get_user_meta($row['coach_id']);
                    $user_real_name = isset($usermeta['user_real_name']) ? unserialize($usermeta['user_real_name'][0]) : [];
                    $real_name = isset($user_real_name['real_name']) ? $user_real_name['real_name'] : $row['user_login'];
//                    leo_dump($usermeta);
                    ?>
                    <tr data-id="<?=$row['coach_id']?>">
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="cb-select-407">选择<?=$real_name?></label>
                            <input id="cb-select-<?=$row['coach_id']?>" type="checkbox" name="post[]" value="<?=$row['coach_id']?>">
                            <div class="locked-indicator">
                                <span class="locked-indicator-icon" aria-hidden="true"></span>
                                <span class="screen-reader-text">“<?=$real_name?>”已被锁定</span>
                            </div>
                        </th>
                        <td class="real_name column-real_name has-row-actions column-primary" data-colname="姓名">
                            <?=$real_name?>
                            <br>

                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="ID column-ID" data-colname="ID"><?=isset($usermeta['user_ID'])?$usermeta['user_ID'][0]:''?></td>
                        <td class="mobile column-mobile" data-colname="手机"><?=$row['user_mobile']?></td>
                        <td class="option1 column-option1" data-colname="操作">
                            <a href="javascript:;" class="del-member">删除</a>
                        </td>

                    </tr>
                    <?php
                }
                ?>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-real_name column-primary">姓名</th>
                    <th scope="col" class="manage-column column-ID">ID</th>
                    <th scope="col" class="manage-column column-mobile">手机</th>
                    <th scope="col" class="manage-column column-option1">操作</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
                        <option value="delete">删除</option>
                    </select>
                    <input type="submit" id="doaction2" class="button action" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>

            <br class="clear">
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('.del-member').on('click',function () {
                        var member_id = $(this).closest('tr').attr('data-id');
                        if(member_id == '' || member_id == undefined) return false;
                        if(confirm('是否确认删除此成员?')){
                            $.ajax({
                                url : ajaxurl,
                                data : {'action':'deleteOrganizeMember','member_id':member_id,'user_id':'<?=$user_id?>'},
                                dataType : 'json',
                                type : 'post',
                                success : function(response){
                                    alert(response.data.info);
                                    if(response['success']){
                                        window.location.reload();
                                    }
                                }
                            });
                        }
                    });
                })
            </script>
        </div>
        <?php
    }

    /**
     * 新增主体成员
     */
    public function addOrganizeCoach(){
        global $wpdb;
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        if(is_post()){
            $success_msg = '';
            $error_msg = '';
            $member_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            if($member_id < 1) $error_msg = '请选择成员';
            //该成员是否已存在机构
            $var = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}zone_join_coach WHERE coach_id='{$member_id}'");
            if($var) $error_msg = '该成员已存在机构!';
            if($error_msg == ''){
                $insertData = [
                    'zone_id' => $user_id,
                    'coach_id' => $member_id,
                ];
                $bool = $wpdb->insert($wpdb->prefix.'zone_join_coach',$insertData);
                if(!$bool){
                    $error_msg = '操作失败!';
                }else{
                    $success_msg = '操作成功!';
                }
            }

        }
        //主体资料
        $zone_meta = $wpdb->get_row("SELECT zone_title FROM {$wpdb->prefix}zone_meta WHERE user_id='{$user_id}'",ARRAY_A);
        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加-<?=$zone_meta['zone_title']?>-成员</h1>

            <div id="ajax-response">
                <span style="color: #2bc422"><?=$success_msg?></span>
                <span style="color: #c44e00"><?=$error_msg?></span>
            </div>

            <form method="post" action="" class="validate" novalidate="novalidate">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="5f6ea9ff44"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php"><table class="form-table">
                    <tbody>
                    <tr class="">
                        <th scope="row"><label for="zone_title">主体名称 </label></th>
                        <td>
                            <?=$zone_meta['zone_title']?>
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="user_id">选择成员 </label></th>
                        <td>
                            <select class="js-data-select-ajax" name="user_id" style="width: 50%" data-action="get_base_user_list" data-type="base">

                            </select>

                        </td>
                    </tr>


                    </tbody>
                </table>


                <p class="submit"><input type="submit" name="createuser" id="createusersub" class="button button-primary" value="提交"></p>
            </form>
        </div>
        <?php
    }

    /**
     * 主体收益提现记录
     */
    public function organizeIncomeLog(){
        die;
        global $wpdb;
        $wpdb->get_results("SELECT * FROM {$wpdb->prefix}zone_meta AS zm 
        LEFT JOIN {$wpdb->prefix}user_stream_logs AS usl ON zm.user_id=usl.user_id
        ");
        $rows = [];
        ?>

        <div class="wrap">
            <h1 class="wp-heading-inline">主体收益记录</h1>

            <hr class="wp-header-end">
            <ul class="subsubsub">
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&cate=1')?>" <?=$cate===1?'class="current"':''?> aria-current="page">比赛相关</a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&cate=2')?>" <?=$cate===2?'class="current"':''?> aria-current="page">考级相关</a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&cate=3')?>" <?=$cate===3?'class="current"':''?> aria-current="page">脑力产品</a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&cate=4')?>" <?=$cate===4?'class="current"':''?> aria-current="page">课程相关</a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=statistics&cate=5')?>" <?=$cate===5?'class="current"':''?> aria-current="page">其它收支</a></li>
            </ul>
            <br class="clear">

            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
                        <option value="delete">删除</option>
                    </select>
                    <input type="submit" id="doaction" class="button action" value="应用">
                </div>

                <div class="tablenav-pages">

                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">主体列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th scope="col" id="dates" class="manage-column column-dates column-primary">日期</th>
                    <th scope="col" id="order_pay" class="manage-column column-order_pay">订单支付</th>
                    <th scope="col" id="order_refund" class="manage-column column-order_refund">订单退款</th>
                    <th scope="col" id="bonus_send" class="manage-column column-bonus_send">奖金发放</th>
                    <th scope="col" id="status" class="manage-column column-status">状态</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $k =>$row){
                    ?>
                    <tr>
                        <th scope="row" class="check-column">
                            <input id="cb-select-<?=$k?>" type="checkbox" name="post[]" value="<?=$k?>">
                        </th>
                        <td class="dates column-dates has-row-actions column-primary" data-colname="日期">
                            <?=$row['times']?>
                            <br>
                            <div class="row-actions">
                                <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                                <!--                               <span class="view"><a href="">资料</a></span>-->
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="order_pay column-order_pay" data-colname="订单支付"> <?=isset($row['pay_amount']) ? $row['pay_amount'] : 0?> </td>
                        <td class="order_refund column-order_refund" data-colname="订单退款"><?=isset($row['refund_amount']) ? $row['refund_amount'] : 0?> </td>
                        <td class="bonus_send column-bonus_send" data-colname="奖金发放"><?=isset($row['bonus_amount']) ? $row['bonus_amount'] : 0?> </td>
                        <td class="status column-status" data-colname="状态">

                        </td>

                    </tr>
                    <?php
                }
                ?>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-dates column-primary">日期</th>
                    <th scope="col" class="manage-column column-order_pay">订单支付</th>
                    <th scope="col" class="manage-column column-order_refund">订单退款</th>
                    <th scope="col" class="manage-column column-bonus_send">奖金发放</th>
                    <th scope="col" class="manage-column column-status">状态</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
                        <option value="delete">删除</option>
                    </select>
                    <input type="submit" id="doaction2" class="button action" value="应用">
                </div>

                <div class="tablenav-pages">
                </div>
                <br class="clear">
            </div>

            <br class="clear">
        </div>
        <?php
    }

    /**
     * 引入当前页面css/js
     */
    public function register_scripts(){

        switch ($_GET['page']){
            case 'fission':
                wp_register_script('layui-js',match_js_url.'layui/layui.js');
                wp_enqueue_script( 'layui-js' );
                break;
        }
    }
}
new Organize();