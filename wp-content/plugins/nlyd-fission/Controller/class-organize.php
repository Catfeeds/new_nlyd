<?php
use library\AliSms;
//组织机构控制器
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

//            $role = 'organize_power';//权限名
//            $wp_roles->add_cap('administrator', $role);

//            $role = 'add_organize_power';//权限名
//            $wp_roles->add_cap('administrator', $role);

            $role = 'organize_coach';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'add_organize_coach';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'organize_statistics';//权限名
            $wp_roles->add_cap('administrator', $role);

//            $role = 'organize_income_log';//权限名
//            $wp_roles->add_cap('administrator', $role);
        }
        add_submenu_page('fission','机构详情','机构详情','organize_detail','fission-organize-detail',array($this,'organizeDetails'));
        add_submenu_page('fission','机构类型','机构类型','organize_type','fission-organize-type',array($this,'organizeType'));
//        add_submenu_page('fission','机构权限','机构权限','organize_power','fission-organize-power',array($this,'organizePower'));
        add_submenu_page('fission','机构成员','机构成员','organize_coach','fission-organize-coach',array($this,'organizeCoach'));
        add_submenu_page('fission','新增机构','新增机构','add_organize','fission-add-organize',array($this,'addOrganize'));
        add_submenu_page('fission','新增机构类型','新增机构类型','add_organize_type','fission-add-organize-type',array($this,'addOrganizeType'));
//        add_submenu_page('fission','新增机构权限','新增机构权限','add_organize_power','fission-add-organize-power',array($this,'addOrganizePower'));
        add_submenu_page('fission','新增机构成员','新增机构成员','add_organize_coach','fission-add-organize-coach',array($this,'addOrganizeCoach'));
        add_submenu_page('fission','机构统计信息','机构统计信息','organize_statistics','fission-organize-statistics',array($this,'organizeStatistics'));
//        add_submenu_page('fission','机构收益记录','机构收益记录','organize_income_log','fission-organize-income-log',array($this,'organizeIncomeLog'));
    }

    /**
     *机构列表
     */
    public function organizeList(){
        $typeList = $this->getOrganizeTypeList();
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $type = isset($_GET['ctype']) ? intval($_GET['ctype']) : $typeList[0]['id'];
        $status_type = isset($_GET['stype']) ? intval($_GET['stype']) : 0;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';

        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;

        $leftJoin = '';
        $joinWhere = '';
        $where = "WHERE zm.type_id='{$type}'";
        if($status_type !== 0){
            $where .= "AND zm.user_status='{$status_type}'";
        }
        if($status_type != -2){
            $where .= "AND zm.user_status!=-2";
        }
        if($searchStr != ''){
            $leftJoin = " LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=zm.user_id AND um.meta_key='user_real_name'";
            $joinWhere = " AND (um.meta_value LIKE '%{$searchStr}%' OR u.user_mobile LIKE '%{$searchStr}%' OR u.user_login LIKE '%{$searchStr}%')";
        }
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS u.user_login,u.user_mobile,zm.user_id,zm.type_id,zm.referee_id,zm.created_time,zm.audit_time,zm.user_status,zt.zone_type_name,zm.zone_name,zm.is_able,
                zm.zone_address,zm.business_licence_url,zm.center_manager_id,
                zm.opening_bank,zm.opening_bank_address,zm.bank_card_num,zm.id,zm.zone_match_type,zm.apply_id,
                zm.chairman_id,zm.secretary_id,zm.zone_city,zm.term_time,zm.user_status,zm.is_double,zm.zone_number,
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
                ORDER BY zm.user_status ASC
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
        $numSql = "SELECT count(id) FROM {$wpdb->prefix}zone_meta WHERE type_id='{$type}'";
        $ok_num  = $wpdb->get_var($numSql.' AND user_status=1');
        $apply_num  = $wpdb->get_var($numSql.' AND user_status=-1');
        $refuse_num  = $wpdb->get_var($numSql.' AND user_status=-2');
        $all_num = $wpdb->get_var($numSql.' AND user_status IN(1,-1)');
        //类型列表

        $typeListCount = count($typeList)-1;
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">机构列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize')?>" class="page-title-action">添加机构</a>

            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤机构列表</h2>
            <ul class="subsubsub">
<!--                <li class="all"><a href="--><?//=admin_url('admin.php?page=fission&ctype=0&stype='.$status_type)?><!--" --><?//=$type===0?'class="current"':''?><!-- aria-current="page">全部<span class="count">（--><?//=$all_num?><!--）</span></a> |</li>-->
                <?php
                foreach ($typeList as $tlk => $tlv){
                    $typeNum = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}zone_meta WHERE type_id='{$tlv['id']}' AND user_status IN(1,-1)");
                    ?>
                    <li class="all"><a href="<?=admin_url('admin.php?page=fission&ctype='.$tlv['id'])?>" <?=$type==$tlv['id']?'class="current"':''?> aria-current="page"><?=$tlv['zone_type_name']?><span class="count">（<?=$typeNum>0?$typeNum:0?>）</span></a><?=$tlk<$typeListCount?' | ':''?></li>
                    <?php
                }
                ?>
            </ul>
            <br class="clear">
            <ul class="subsubsub">
                <li class="all"><a href="<?=admin_url('admin.php?page=fission&stype=0&ctype='.$type)?>" <?=$status_type===0?'class="current"':''?> aria-current="page">全部<span class="count">（<?=$all_num?>）</span></a> |</li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission&stype=1&ctype='.$type)?>" <?=$status_type===1?'class="current"':''?> aria-current="page">已通过<span class="count">（<?=$ok_num?>）</span></a> |</li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission&stype=-1&ctype='.$type)?>" <?=$status_type===-1?'class="current"':''?> aria-current="page">待审核<span class="count">（<?=$apply_num?>）</span></a> |</li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission&stype=-2&ctype='.$type)?>" <?=$status_type===-2?'class="current"':''?> aria-current="page">未通过<span class="count">（<?=$refuse_num?>）</span></a> </li>
            </ul>

            <style type="text/css">
                .column-name{
                    width: 230px;
                }
            </style>

            <p class="search-box">
                <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                <input type="search" id="search_val" name="search_val" placeholder="负责人/手机/用户名" value="<?=$searchStr?>">
                <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=fission&ctype='.$type.'&stype='.$status_type.'&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
            </p>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
<!--                        <option value="frozen">冻结</option>-->
<!--                        <option value="thaw">解冻</option>-->
                    </select>
                    <input type="button" id="doaction" class="button action all_options" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
            <style type="text/css">
                .widefat td, .widefat th{
                    padding: 15px 10px;;
                }
            </style>
            <h2 class="screen-reader-text">机构列表</h2>

            <?php
            $type_alias = $wpdb->get_var("SELECT zone_type_alias FROM {$wpdb->prefix}zone_type WHERE id='{$type}'");

            switch ($type_alias){
                case 'match':
                    $this->matchListHtml($rows,$type_alias);
                    break;
                case 'trains':
                    $this->trainCenterListHtml($rows,$type_alias);
                    break;
                case 'test':
                    $this->gradingCenterListHtml($rows,$type_alias);
                    break;
            }
            ?>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
<!--                        <option value="frozen">冻结</option>-->
<!--                        <option value="thaw">解冻</option>-->
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
                            var user_id = _this.closest('tr').attr('data-id');
                            var request_type = _this.attr('data-type');
                        }
                        if(user_id == false || user_id == '') return false;
                        var data = {'action':action,'id':user_id,'request_type':request_type}
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
                            photos: '#cardImg-<?=$row['id']?>',
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
     * 赛区列表html
     */
    private function matchListHtml($rows,$type_alias){
        global $wpdb;
        ?>
        <table class="wp-list-table widefat fixed striped users">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                <th scope="col" id="name" class="manage-column column-name column-primary">名称</th>
                <th scope="col" id="zone_match_type" class="manage-column column-zone_match_type">办赛类型</th>
                <th scope="col" id="nums" class="manage-column column-nums">编号</th>
                <th scope="col" id="referee_id" class="manage-column column-referee_id">推荐人</th>
                <th scope="col" id="person" class="manage-column column-person">负责人</th>
                <th scope="col" id="chairman_id" class="manage-column column-chairman_id">主席</th>
                <th scope="col" id="secretary_id" class="manage-column column-secretary_id">秘书长</th>
                <th scope="col" id="apply_id" class="manage-column column-apply_id">申请人</th>
                <th scope="col" id="term_time" class="manage-column column-term_time">有效期</th>
                <th scope="col" id="match_num" class="manage-column column-match_num">办赛次数</th>
                <th scope="col" id="match_member_num" class="manage-column column-match_member_num">参赛人次(累计)</th>
                <th scope="col" id="grading_num" class="manage-column column-grading_num">考级次数</th>
                <th scope="col" id="grading_member_num" class="manage-column column-grading_member_num">考级人次(累计)</th>
                <th scope="col" id="zone_status" class="manage-column column-zone_status">申请状态</th>
                <th scope="col" id="options1" class="manage-column column-options1">操作</th>
            </tr>
            </thead>

            <tbody id="the-list" data-wp-lists="list:user">

            <?php
            foreach ($rows as $row){
//                        $usermeta = get_user_meta($row['user_id']);
//                        $user_real_name = isset($usermeta['user_real_name']) ? unserialize($usermeta['user_real_name'][0]) : [];
                $referee_real_name = get_user_meta($row['referee_id'],'user_real_name',true);
                $chairman_real_name = get_user_meta($row['chairman_id'],'user_real_name',true);
                $secretary_real_name = get_user_meta($row['secretary_id'],'user_real_name',true);
                $apply_real_name = get_user_meta($row['apply_id'],'user_real_name',true);
                //负责人
                $person_real_name = get_user_meta($row['center_manager_id'],'user_real_name',true);
                if($row['user_id'] > 0){
                    //办赛次数
                    $match_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}match_meta_new WHERE created_id='{$row['user_id']}'");
                    //参赛人次
                    $match_member_num = $wpdb->get_var("SELECT COUNT(o.id) FROM {$wpdb->prefix}match_meta_new AS mmn 
                                           LEFT JOIN {$wpdb->prefix}order AS o ON o.match_id=mmn.match_id
                                           WHERE mmn.created_id='{$row['user_id']}' AND o.order_type=1 AND o.pay_status IN(2,3,4)");
                    //考级次数
                    $grading_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}grading_meta WHERE created_person='{$row['user_id']}'");
                    //考级人次
                    $grading_member_num = $wpdb->get_var("SELECT COUNT(o.id) FROM {$wpdb->prefix}grading_meta AS gm 
                                           LEFT JOIN {$wpdb->prefix}order AS o ON o.match_id=gm.grading_id
                                           WHERE gm.created_person='{$row['user_id']}' AND o.order_type=2 AND o.pay_status IN(2,3,4)");
                }else{
                    $match_num = 0;
                    $match_member_num = 0;
                    $grading_num = 0;
                    $grading_member_num = 0;
                }

//                       leo_dump($wpdb->last_query);die;
                ?>
                <tr data-uid="<?=$row['user_id']?>" data-id="<?=$row['id']?>">
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="cb-select-407"></label>
                        <input id="cb-select-<?=$row['id']?>" type="checkbox" name="post[]" value="<?=$row['id']?>">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                            <span class="screen-reader-text">“<?=$this->echoZoneName($type_alias,$row['zone_city'],$row['zone_name'],$row['zone_match_type'],$row['zone_number'])?>”已被锁定</span>
                        </div>
                    </th>
                    <td class="name column-name has-row-actions column-primary" data-colname="名称">
                        <?=$this->echoZoneName($type_alias,$row['zone_city'],$row['zone_name'],$row['zone_match_type'],$row['zone_number'])?>
                        <br>
                        <div class="row-actions">
                            <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                            <!--                               <span class="view"><a href="">资料</a></span>-->
                        </div>
                        <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                    </td>
                    <td class="zone_match_type column-zone_match_type" data-colname="办赛类型">

                        <?php
                        if($row['zone_match_type']=='1'){
                            echo '战队精英赛';
                        }else{
                            echo ($row['is_double']=='1'?'多区县':'单区县').'城市赛';
                        }
                        ?>
                    </td>
                    <td class="nums column-nums" data-colname="编号"><?=$row['zone_number'];?></td>
                    <td class="referee_id column-referee_id" data-colname="推荐人">
                        <a href="<?=admin_url('users.php?page=users-info&ID='.$row['referee_id'])?>">
                            <?=isset($referee_real_name['real_name'])?$referee_real_name['real_name']:($row['referee_id']>0?get_user_by('ID',$row['referee_id'])->user_login:'')?>
                        </a>
                    </td>
                    <td class="person column-person" data-colname="负责人">
                        <a href="<?=admin_url('users.php?page=users-info&ID='.$row['center_manager_id'])?>">
                            <?=isset($person_real_name['real_name'])?$person_real_name['real_name']:($row['center_manager_id']>0?get_user_by('ID',$row['center_manager_id'])->user_login:'')?>
                        </a>
                    </td>
                    <td class="chairman_id column-chairman_id" data-colname="主席">
                        <a href="<?=admin_url('users.php?page=users-info&ID='.$row['chairman_id'])?>">
                            <?=isset($chairman_real_name['real_name'])?$chairman_real_name['real_name']:($row['chairman_id']>0?get_user_by('ID',$row['chairman_id'])->user_login:'')?>
                        </a>
                    </td>
                    <td class="secretary_id column-secretary_id" data-colname="秘书长">
                        <a href="<?=admin_url('users.php?page=users-info&ID='.$row['secretary_id'])?>">
                            <?=isset($secretary_real_name['real_name'])?$secretary_real_name['real_name']:($row['secretary_id']>0?get_user_by('ID',$row['secretary_id'])->user_login:'')?>
                        </a>
                    </td>
                    <td class="apply_id column-apply_id" data-colname="申请人">
                        <a href="<?=admin_url('users.php?page=users-info&ID='.$row['apply_id'])?>">
                            <?=isset($apply_real_name['real_name'])?$apply_real_name['real_name']:($row['apply_id']>0?get_user_by('ID',$row['apply_id'])->user_login:'')?>
                        </a>
                    </td>

                    <td class="term_time column-term_time" data-colname="有效期"><?=$row['term_time']?$row['term_time']:'无'?></td>
                    <td class="match_num column-match_num" data-colname="办赛次数"><?=$match_num?></td>
                    <td class="match_member_num column-match_member_num" data-colname="参赛人次"><?=$match_member_num?></td>
                    <td class="grading_num column-grading_num" data-colname="考级次数"><?=$grading_num?></td>
                    <td class="grading_member_num column-grading_member_num" data-colname="考级人次"><?=$grading_member_num?></td>
                    <td class="zone_status column-zone_status" data-colname="申请状态">
                        <?php
                        if($row['user_status'] == '-1'){
                            echo '<span style="color: #c43800">待审核</span>';
                        } elseif ($row['user_status'] == '1'){
                            echo '<span style="color: #0dc42b">已通过</span>';
                        }elseif ($row['user_status'] == '-2'){
                            echo '<span style="">已拒绝</span>';
                        }
                        ?>
                    </td>

                    <td class="options1 column-options1" data-colname="操作">

                        <?php
                        //操作列表
                        $optionsArr = ["<a href='".admin_url('admin.php?page=fission-organize-statistics&id='.$row['id'])."' data-type='thaw' class=''>查看</a>"];
//                        if($row['user_status'] == '1'){
//                            switch ($row['is_able']){
//                                case 1:
//                                    array_push($optionsArr,"<a href='javascript:;' data-type='frozen' class='edit-frozen'>冻结</a>");
//                                    break;
//                                case 2:
//                                    array_push($optionsArr,"<a href='javascript:;' data-type='thaw' class='edit-thaw'>解冻</a>");
//                                    break;
//                            }
//                        }
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
                <th scope="col" class="manage-column column-name column-primary">名称</th>
                <th scope="col" class="manage-column column-zone_match_type">办赛类型</th>
                <th scope="col" class="manage-column column-nums">编号</th>
                <th scope="col" class="manage-column column-referee_id">推荐人</th>
                <th scope="col" class="manage-column column-person">负责人</th>
                <th scope="col" class="manage-column column-chairman_id">主席</th>
                <th scope="col" class="manage-column column-secretary_id">秘书长</th>
                <th scope="col" class="manage-column column-apply_id">申请人</th>
                <th scope="col" class="manage-column column-term_time">有效期</th>
                <th scope="col" class="manage-column column-match_num">办赛次数</th>
                <th scope="col" class="manage-column column-match_member_num">参赛人次(累计)</th>
                <th scope="col" class="manage-column column-grading_num">考级次数</th>
                <th scope="col" class="manage-column column-grading_member_num">考级人次(累计)</th>
                <th scope="col" class="manage-column column-zone_status">申请状态</th>
                <th scope="col" class="manage-column column-options1">操作</th>
            </tr>
            </tfoot>

        </table>
        <?php
    }

    /**
     * 训练中心/分中心列表html
     */
    private function trainCenterListHtml($rows,$type_alias){
        global $wpdb;
        ?>
        <table class="wp-list-table widefat fixed striped users">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                <th scope="col" id="name" class="manage-column column-name column-primary">训练中心名称</th>
                <th scope="col" id="zone_city" class="manage-column column-zone_city">所在地</th>
                <th scope="col" id="nums" class="manage-column column-nums">编号</th>
                <th scope="col" id="person" class="manage-column column-person">负责人</th>
                <th scope="col" id="referee_id" class="manage-column column-referee_id">事业管理员</th>
                <th scope="col" id="term_time" class="manage-column column-term_time">有效期</th>
                <th scope="col" id="match_num" class="manage-column column-match_num">赛事数量</th>
                <th scope="col" id="course_num" class="manage-column column-course_num">课程数量</th>
                <th scope="col" id="coach_num" class="manage-column column-coach_num">教练数量</th>
                <th scope="col" id="student_num" class="manage-column column-student_num">学员数量</th>
                <th scope="col" id="grading_num" class="manage-column column-grading_num">考级数量</th>
                <th scope="col" id="user_status" class="manage-column column-user_status">申请状态</th>
                <th scope="col" id="able_status" class="manage-column column-able_status">冻结状态</th>
                <th scope="col" id="options1" class="manage-column column-options1">操作</th>
            </tr>
            </thead>

            <tbody id="the-list" data-wp-lists="list:user">

            <?php
            foreach ($rows as $row){
                //事业管理员
                $referee_real_name = get_user_meta($row['referee_id'],'user_real_name',true);
                //负责人/管理员
                $person_real_name = get_user_meta($row['center_manager_id'],'user_real_name',true);
                if($row['user_id'] > 0){
                    //办赛次数
                    $match_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}match_meta_new WHERE created_id='{$row['user_id']}'");
                    //考级次数
                    $grading_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}grading_meta WHERE created_person='{$row['user_id']}'");
                    //课程数量
                    $course_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}course WHERE zone_id='{$row['user_id']}'");
                    //教练数量
                    $coach_id_str = $wpdb->get_var("SELECT GROUP_CONCAT(zjc.coach_id) FROM {$wpdb->prefix}zone_join_coach AS zjc 
                     LEFT JOIN {$wpdb->prefix}coach_skill as cs ON cs.coach_id=zjc.coach_id
                     WHERE zone_id='{$row['user_id']}' AND cs.id!=''");
                    $coach_id_arr = explode(',', $coach_id_str);
                    $coach_num = count($coach_id_arr);
                    //学员数量
                    $student_num = $wpdb->get_results("SELECT COUNT(id) FROM {$wpdb->prefix}my_coach WHERE coach_id IN({$coach_id_str}) AND apply_status=2 GROUP BY user_id", ARRAY_A);
                    $student_num = count($student_num);
                }else{
                    $match_num = 0;
                    $grading_num = 0;
                    $course_num = 0;
                    $coach_num = 0;
                    $student_num = 0;
                }
                ?>
                <tr data-uid="<?=$row['user_id']?>" data-id="<?=$row['id']?>">
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="cb-select-407"></label>
                        <input id="cb-select-<?=$row['id']?>" type="checkbox" name="post[]" value="<?=$row['id']?>">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                            <span class="screen-reader-text">“<?=$this->echoZoneName($type_alias,$row['zone_city'],$row['zone_name'],$row['zone_match_type'],$row['zone_number'])?>”已被锁定</span>
                        </div>
                    </th>
                    <td class="name column-name has-row-actions column-primary" data-colname="名称">
                        <?=$this->echoZoneName($type_alias,$row['zone_city'],$row['zone_name'],$row['zone_match_type'],$row['zone_number'])?>
                        <br>
                        <div class="row-actions">
                        </div>
                        <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                    </td>
                    <td class="zone_city column-zone_city" data-colname="所在地">

                        <?php
                       echo $row['zone_city'];
                        ?>
                    </td>
                    <td class="nums column-nums" data-colname="编号"><?=$row['zone_number'];?></td>
                    <td class="person column-person" data-colname="负责人">
                        <a href="<?=admin_url('users.php?page=users-info&ID='.$row['center_manager_id'])?>">
                            <?=isset($person_real_name['real_name'])?$person_real_name['real_name']:($row['center_manager_id']>0?get_user_by('ID',$row['center_manager_id'])->user_login:'')?>
                        </a>
                    </td>
                    <td class="referee_id column-referee_id" data-colname="事业管理员">
                        <a href="<?=admin_url('users.php?page=users-info&ID='.$row['referee_id'])?>">
                            <?=isset($referee_real_name['real_name'])?$referee_real_name['real_name']:($row['referee_id']>0?get_user_by('ID',$row['referee_id'])->user_login:'')?>
                        </a>
                    </td>

                    <td class="term_time column-term_time" data-colname="有效期"><?=$row['term_time']?$row['term_time']:'无'?></td>
                    <td class="match_num column-match_num" data-colname="赛事数量"><?=$match_num?></td>
                    <td class="course_num column-course_num" data-colname="课程数量"><?=$course_num?></td>
                    <td class="coach_num column-coach_num" data-colname="教练数量"><?=$coach_num?></td>
                    <td class="student_num column-student_num" data-colname="学员数量"><?=$student_num?></td>
                    <td class="grading_num column-grading_num" data-colname="考级数量"><?=$grading_num?></td>
                    <td class="user_status column-user_status" data-colname="申请状态">
                        <?php
                        if($row['user_status'] == '-1'){
                            echo '<span style="color: #c43800">待审核</span>';
                        } elseif ($row['user_status'] == '1'){
                            echo '<span style="color: #0dc42b">已通过</span>';
                        }elseif ($row['user_status'] == '-2'){
                            echo '<span style="">已拒绝</span>';
                        }
                        ?>
                    </td>
                    <td class="able_status column-able_status" data-colname="冻结状态">
                        <?php
                        if ($row['user_status'] == '1'){
                            echo '<span style="color: #0dc42b">正常</span>';
                        }elseif ($row['user_status'] == '-2'){
                            echo '<span style="color: #c43800">冻结</span>';
                        }
                        ?>
                    </td>


                    <td class="options1 column-options1" data-colname="操作">

                        <?php
                        //操作列表
                        $optionsArr = ["<a href='".admin_url('admin.php?page=fission-organize-statistics&id='.$row['id'])."' data-type='thaw' class=''>查看</a>"];
//                        if($row['user_status'] == '1'){
//                            switch ($row['is_able']){
//                                case 1:
//                                    array_push($optionsArr,"<a href='javascript:;' data-type='frozen' class='edit-frozen'>冻结</a>");
//                                    break;
//                                case 2:
//                                    array_push($optionsArr,"<a href='javascript:;' data-type='thaw' class='edit-thaw'>解冻</a>");
//                                    break;
//                            }
//                        }
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
                <th scope="col" class="manage-column column-name column-primary">训练中心名称</th>
                <th scope="col" class="manage-column column-zone_city">所在地</th>
                <th scope="col" class="manage-column column-nums">编号</th>
                <th scope="col" class="manage-column column-person">负责人</th>
                <th scope="col" class="manage-column column-referee_id">事业管理员</th>
                <th scope="col" class="manage-column column-term_time">有效期</th>
                <th scope="col" class="manage-column column-match_num">赛事数量</th>
                <th scope="col" class="manage-column column-course_num">课程数量</th>
                <th scope="col" class="manage-column column-coach_num">教练数量</th>
                <th scope="col" class="manage-column column-student_num">学员数量</th>
                <th scope="col" class="manage-column column-grading_num">考级数量</th>
                <th scope="col" class="manage-column column-user_status">申请状态</th>
                <th scope="col" class="manage-column column-able_status">冻结状态</th>
                <th scope="col" class="manage-column column-options1">操作</th>
            </tr>
            </tfoot>

        </table>
        <?php
    }

    /**
     * 测评/考级中心中心列表html
     */
    private function gradingCenterListHtml($rows,$type_alias){
        global $wpdb;
        ?>
        <table class="wp-list-table widefat fixed striped users">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                <th scope="col" id="name" class="manage-column column-name column-primary">训练中心名称</th>
                <th scope="col" id="zone_city" class="manage-column column-zone_city">所在地</th>
                <th scope="col" id="nums" class="manage-column column-nums">编号</th>
                <th scope="col" id="person" class="manage-column column-person">负责人</th>
                <th scope="col" id="referee_id" class="manage-column column-referee_id">事业管理员</th>
                <th scope="col" id="term_time" class="manage-column column-term_time">有效期</th>
                <th scope="col" id="match_num" class="manage-column column-match_num">赛事数量</th>
                <th scope="col" id="course_num" class="manage-column column-course_num">课程数量</th>
                <th scope="col" id="coach_num" class="manage-column column-coach_num">教练数量</th>
                <th scope="col" id="student_num" class="manage-column column-student_num">学员数量</th>
                <th scope="col" id="grading_num" class="manage-column column-grading_num">考级数量</th>
                <th scope="col" id="user_status" class="manage-column column-user_status">申请状态</th>
                <th scope="col" id="able_status" class="manage-column column-able_status">冻结状态</th>
                <th scope="col" id="options1" class="manage-column column-options1">操作</th>
            </tr>
            </thead>

            <tbody id="the-list" data-wp-lists="list:user">

            <?php
            foreach ($rows as $row){
                //事业管理员
                $referee_real_name = get_user_meta($row['referee_id'],'user_real_name',true);
                //负责人/管理员
                $person_real_name = get_user_meta($row['center_manager_id'],'user_real_name',true);
                if($row['user_id'] > 0){
                    //办赛次数
                    $match_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}match_meta_new WHERE created_id='{$row['user_id']}'");
                    //考级次数
                    $grading_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}grading_meta WHERE created_person='{$row['user_id']}'");
                    //课程数量
                    $course_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}course WHERE zone_id='{$row['user_id']}'");
                    //教练数量
                    $coach_id_str = $wpdb->get_var("SELECT GROUP_CONCAT(zjc.coach_id) FROM {$wpdb->prefix}zone_join_coach AS zjc 
                     LEFT JOIN {$wpdb->prefix}coach_skill as cs ON cs.coach_id=zjc.coach_id
                     WHERE zone_id='{$row['user_id']}' AND cs.id!=''");
                    $coach_id_arr = explode(',', $coach_id_str);
                    $coach_num = count($coach_id_arr);
                    //学员数量
                    $student_num = $wpdb->get_results("SELECT COUNT(id) FROM {$wpdb->prefix}my_coach WHERE coach_id IN({$coach_id_str}) AND apply_status=2 GROUP BY user_id", ARRAY_A);
                    $student_num = count($student_num);
                }else{
                    $match_num = 0;
                    $grading_num = 0;
                    $course_num = 0;
                    $coach_num = 0;
                    $student_num = 0;
                }
                ?>
                <tr data-uid="<?=$row['user_id']?>" data-id="<?=$row['id']?>">
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="cb-select-407"></label>
                        <input id="cb-select-<?=$row['id']?>" type="checkbox" name="post[]" value="<?=$row['id']?>">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                            <span class="screen-reader-text">“<?=$this->echoZoneName($type_alias,$row['zone_city'],$row['zone_name'],$row['zone_match_type'],$row['zone_number'])?>”已被锁定</span>
                        </div>
                    </th>
                    <td class="name column-name has-row-actions column-primary" data-colname="名称">
                        <?=$this->echoZoneName($type_alias,$row['zone_city'],$row['zone_name'],$row['zone_match_type'],$row['zone_number'])?>
                        <br>
                        <div class="row-actions">
                        </div>
                        <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                    </td>
                    <td class="zone_city column-zone_city" data-colname="所在地">

                        <?php
                        echo $row['zone_city'];
                        ?>
                    </td>
                    <td class="nums column-nums" data-colname="编号"><?=$row['zone_number'];?></td>
                    <td class="person column-person" data-colname="负责人">
                        <a href="<?=admin_url('users.php?page=users-info&ID='.$row['center_manager_id'])?>">
                            <?=isset($person_real_name['real_name'])?$person_real_name['real_name']:($row['center_manager_id']>0?get_user_by('ID',$row['center_manager_id'])->user_login:'')?>
                        </a>
                    </td>
                    <td class="referee_id column-referee_id" data-colname="事业管理员">
                        <a href="<?=admin_url('users.php?page=users-info&ID='.$row['referee_id'])?>">
                            <?=isset($referee_real_name['real_name'])?$referee_real_name['real_name']:($row['referee_id']>0?get_user_by('ID',$row['referee_id'])->user_login:'')?>
                        </a>
                    </td>

                    <td class="term_time column-term_time" data-colname="有效期"><?=$row['term_time']?$row['term_time']:'无'?></td>
                    <td class="match_num column-match_num" data-colname="赛事数量"><?=$match_num?></td>
                    <td class="course_num column-course_num" data-colname="课程数量"><?=$course_num?></td>
                    <td class="coach_num column-coach_num" data-colname="教练数量"><?=$coach_num?></td>
                    <td class="student_num column-student_num" data-colname="学员数量"><?=$student_num?></td>
                    <td class="grading_num column-grading_num" data-colname="考级数量"><?=$grading_num?></td>
                    <td class="user_status column-user_status" data-colname="申请状态">
                        <?php
                        if($row['user_status'] == '-1'){
                            echo '<span style="color: #c43800">待审核</span>';
                        } elseif ($row['user_status'] == '1'){
                            echo '<span style="color: #0dc42b">已通过</span>';
                        }elseif ($row['user_status'] == '-2'){
                            echo '<span style="">已拒绝</span>';
                        }
                        ?>
                    </td>
                    <td class="able_status column-able_status" data-colname="冻结状态">
                        <?php
                        if ($row['user_status'] == '1'){
                            echo '<span style="color: #0dc42b">正常</span>';
                        }elseif ($row['user_status'] == '-2'){
                            echo '<span style="color: #c43800">冻结</span>';
                        }
                        ?>
                    </td>


                    <td class="options1 column-options1" data-colname="操作">

                        <?php
                        //操作列表
                        $optionsArr = ["<a href='".admin_url('admin.php?page=fission-organize-statistics&id='.$row['id'])."' data-type='thaw' class=''>查看</a>"];
                        //                        if($row['user_status'] == '1'){
                        //                            switch ($row['is_able']){
                        //                                case 1:
                        //                                    array_push($optionsArr,"<a href='javascript:;' data-type='frozen' class='edit-frozen'>冻结</a>");
                        //                                    break;
                        //                                case 2:
                        //                                    array_push($optionsArr,"<a href='javascript:;' data-type='thaw' class='edit-thaw'>解冻</a>");
                        //                                    break;
                        //                            }
                        //                        }
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
                <th scope="col" class="manage-column column-name column-primary">训练中心名称</th>
                <th scope="col" class="manage-column column-zone_city">所在地</th>
                <th scope="col" class="manage-column column-nums">编号</th>
                <th scope="col" class="manage-column column-person">负责人</th>
                <th scope="col" class="manage-column column-referee_id">事业管理员</th>
                <th scope="col" class="manage-column column-term_time">有效期</th>
                <th scope="col" class="manage-column column-match_num">赛事数量</th>
                <th scope="col" class="manage-column column-course_num">课程数量</th>
                <th scope="col" class="manage-column column-coach_num">教练数量</th>
                <th scope="col" class="manage-column column-student_num">学员数量</th>
                <th scope="col" class="manage-column column-grading_num">考级数量</th>
                <th scope="col" class="manage-column column-user_status">申请状态</th>
                <th scope="col" class="manage-column column-able_status">冻结状态</th>
                <th scope="col" class="manage-column column-options1">操作</th>
            </tr>
            </tfoot>

        </table>
        <?php
    }

    /**
     * 新增/编辑机构类型
     */
    public function addOrganizeType(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        global $wpdb;
        if(is_post()){
            $success_msg = '';
            $error_msg = '';
            $zone_type_name = isset($_POST['zone_type_name']) ? trim($_POST['zone_type_name']) : '';
            $zone_type_alias = isset($_POST['zone_type_alias']) ? trim($_POST['zone_type_alias']) : '';
            $zone_type_class = isset($_POST['zone_type_class']) ? trim($_POST['zone_type_class']) : '';
            $zone_type_status = isset($_POST['zone_type_status']) ? intval($_POST['zone_type_status']) : 0;
//            $match_power = isset($_POST['match_power']) ? $_POST['match_power'] : [];
            $admin_power = isset($_POST['admin_power']) ? $_POST['admin_power'] : [];
            if($zone_type_name == '') $error_msg = '请填写类型名称';
//            if(!is_array($match_power)) $error_msg = $error_msg==''?'赛事权限错误':$error_msg.'<br >赛事权限错误';
            if(!is_array($admin_power)) $error_msg = $error_msg==''?'课程权限错误':$error_msg.'<br >管理权限错误';
            if($zone_type_alias == '') $error_msg = $error_msg==''?'请填写类型别名':$error_msg.'<br >请填写类型别名';
            if($zone_type_status != 1 && $zone_type_status != 2) $error_msg = $error_msg==''?'请选择类型状态':$error_msg.'<br >请选择类型状态';

//            $match_role_ids = join(',',$match_power);
//            $admin_power_ids = join(',',$admin_power);
            if($error_msg == ''){
                $insertData = [
                    'zone_type_name' => $zone_type_name,
                    'zone_type_alias' => $zone_type_alias,
                    'zone_type_class' => $zone_type_class,
                    'zone_type_status' => $zone_type_status,
                ];
                $wpdb->query('START TRANSACTION');
                if($id > 0){
                    $bool = $wpdb->update($wpdb->prefix.'zone_type',$insertData,['id'=>$id]);
                    $powerOne = $wpdb->get_row("SELECT id,role_id FROM {$wpdb->prefix}zone_join_role WHERE zone_type_id='{$id}'", ARRAY_A);
                    $delBool = true;
                    $powerBool = true;
                    if($powerOne){
                        $delBool = $wpdb->delete($wpdb->prefix.'zone_join_role',['zone_type_id' => $id]);
                    }
                    if($admin_power != []){
                        $powerSql = "INSERT INTO {$wpdb->prefix}zone_join_role (`zone_type_id`,`role_id`) VALUES";
                        $powerValuesArr = [];
                        foreach ($admin_power as $apv){
                            $powerValuesArr[] = " ('{$id}','{$apv}')";
                        }
                        $powerBool = $wpdb->query($powerSql.join(',',$powerValuesArr));
                    }
                    if($bool || ($delBool&&$powerBool)){
                        $wpdb->query('COMMIT');
                        $success_msg = '操作成功';
                    }else{
                        $wpdb->query('ROLLBACK');
                        $error_msg = '操作失败!';
                    }

                }else{
                    $bool = $wpdb->insert($wpdb->prefix.'zone_type',$insertData);
                    if($bool){
                        $zone_type_id = $wpdb->insert_id;
                        $powerBool = true;
                        if($admin_power != []){
                            $powerSql = "INSERT INTO {$wpdb->prefix}zone_join_role (`zone_type_id`,`role_id`) VALUES";
                            $powerValuesArr = [];
                            foreach ($admin_power as $apv){
                                $powerValuesArr[] = " ('{$zone_type_id}','{$apv}')";
                            }
                            $powerBool = $wpdb->query($powerSql.join(',',$powerValuesArr));
                        }
                        if($powerBool) {
                            $wpdb->query('COMMIT');
                            $success_msg = '操作成功';
                        }else{
                            $wpdb->query('ROLLBACK');
                            $error_msg = '操作失败!';
                        }
                    }else{
                        $wpdb->query('ROLLBACK');
                        $error_msg = '操作失败!';
                    }
                }
            }
        }
        $oldPowerLists = [];            //已有权限
        if($id > 0){
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}zone_type WHERE id='{$id}'", ARRAY_A);
            $oldPowerLists = $wpdb->get_results("SELECT role_id FROM {$wpdb->prefix}zone_join_role WHERE zone_type_id='{$id}'", ARRAY_A);
            $oldPowerLists = array_reduce($oldPowerLists, function ($result, $value) {
                return array_merge($result, array_values($value));
            }, array());
        }
        //权限列表
        $powerList = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}zone_type_role", ARRAY_A);
        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加/编辑机构类型</h1>

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
                            <?php if(isset($row['zone_type_alias'])) {?>
                            <input name="zone_type_alias" type="hidden" id="zone_type_alias"  value="<?=$row['zone_type_alias']?>" maxlength="60">
                                <?=$row['zone_type_alias']?>
                            <?php }else { ?>
                                <input name="zone_type_alias" type="text" id="zone_type_alias" value="" maxlength="60">
                            <?php } ?>
                         </td>
                    </tr>

                    <tr class="form-field form-required">
                        <th scope="row"><label for="zone_type_class">样式类名 </label></th>
                        <td>
                            <?php if(isset($row['zone_type_class'])) {?>
                                <input name="zone_type_class" type="hidden" id="zone_type_class"  value="<?=$row['zone_type_class']?>" maxlength="60">
                                <?=$row['zone_type_class']?>
                            <?php }else { ?>
                                <input name="zone_type_class" type="text" id="zone_type_class" value="" maxlength="60">
                            <?php } ?>
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
                        <th scope="row">管理权限</th>
                        <td>
                            <?php
                            foreach ($powerList as $plv){
                                ?>
                                <label for="admin_power_<?=$plv['id']?>"><input <?=in_array($plv['id'],$oldPowerLists)?'checked="checked"':''?> id="admin_power_<?=$plv['id']?>" type="checkbox" name="admin_power[]" value="<?=$plv['id']?>"><?=$plv['role_name']?></label>
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
     * 机构类型列表
     */
    public function organizeType(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;

        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS id,zone_type_name,zone_type_alias,zone_type_class,
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
            <h1 class="wp-heading-inline">机构类型列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize-type')?>" class="page-title-action">添加机构类型</a>

            <hr class="wp-header-end">
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
                        <option value="delType">删除</option>
                    </select>
                    <input type="button" id="doaction" class="button action allOption" value="应用">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">机构列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th scope="col" id="name" class="manage-column column-name column-primary">名称</th>
                    <th scope="col" id="zone_type_alias" class="manage-column column-zone_type_alias">别名</th>
                    <th scope="col" id="zone_type_class" class="manage-column column-zone_type_class">类名</th>
                    <th scope="col" id="status" class="manage-column column-status">状态</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){
                    ?>
                    <tr data-id="<?=$row['id']?>">
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="cb-select-407">选择<?=$row['zone_type_name']?></label>
                            <input id="cb-select-<?=$row['id']?>" class="check_v" type="checkbox" name="post[]" value="<?=$row['id']?>">
                            <div class="locked-indicator">
                                <span class="locked-indicator-icon" aria-hidden="true"></span>
                                <span class="screen-reader-text">“<?=$row['zone_type_name']?>”已被锁定</span>
                            </div>
                        </th>
                        <td class="name column-name has-row-actions column-primary" data-colname="名称">
                            <?=$row['zone_type_name']?>
                            <br>
                            <div class="row-actions">
                                <span class="edit"><a href="<?=admin_url('admin.php?page=fission-add-organize-type&id='.$row['id'])?>">编辑</a> | </span>
                                <span class="delete"><a class="delType" href="javascript:;">删除</a></span>
                                <!--                               <span class="view"><a href="">资料</a></span>-->
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="zone_type_alias column-zone_type_alias" data-colname="别名">
                            <?=$row['zone_type_alias']?>
                        </td>
                        <td class="zone_type_class column-zone_type_class" data-colname="类名">
                            <?=$row['zone_type_class']?>
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
                    <th scope="col" class="manage-column column-zone_type_class">类名</th>
                    <th scope="col" class="manage-column column-status">状态</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
                        <option value="delType">删除</option>
                    </select>
                    <input type="button" id="doaction2" class="button action allOption" value="应用">
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
                    $('.delType').on('click', function () {
                        postAjax($(this).closest('tr').attr('data-id'));

                    });
                    $('.allOption').on('click', function () {
                        var status = $(this).prev().val();
                        if(status != 'delType') return false;
                        var idArr = new Array();
                        $.each($('#the-list').find('.check_v:checked'), function (i,v) {
                            idArr.push($(v).val());
                        });
                        postAjax(idArr.join(','));
                    });
                    function postAjax(ids) {
                        if(ids == '' || ids == undefined) return false;
                        $.ajax({
                            url : ajaxurl,
                            data : {'action':'delZoneType','id':ids},
                            type : 'post',
                            dataType : 'json',
                            success : function (response) {
                                alert(response.data.info);
                                if(response['success']){
                                    window.location.reload();
                                }
                            }, error : function () {
                                alert('请求失败');
                            }
                        });
                    }
                });
            </script>
        </div>
        <?php
    }

    /**
     * 新增/编辑机构
     */
    public function addOrganize($user_id = 0){
        global $wpdb;
        if($user_id > 0){
            $old_zm_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}zone_meta WHERE user_id='{$user_id}'");
        }else{
            $old_zm_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        }
        if(is_post()){
            $success_msg = '';
            $error_msg = '';
//            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $zone_type = isset($_POST['zone_type']) ? intval($_POST['zone_type']) : 0;
            $referee_id = isset($_POST['referee_id']) ? intval($_POST['referee_id']) : 0;
//            $user_status = isset($_POST['user_status']) ? intval($_POST['user_status']) : 0;
//            $zone_title = isset($_POST['zone_title']) ? trim($_POST['zone_title']) : '';
//            $zone_city = isset($_POST['zone_city']) ? trim($_POST['zone_city']) : '';
            $zone_address = isset($_POST['zone_address']) ? trim($_POST['zone_address']) : '';
            $zone_title = isset($_POST['zone_title']) ? trim($_POST['zone_title']) : '';
            $business_licence = isset($_POST['business_licence']) ? trim($_POST['business_licence']) : '';
            $legal_person = isset($_POST['legal_person']) ? trim($_POST['legal_person']) : '';
            $opening_bank = isset($_POST['opening_bank']) ? trim($_POST['opening_bank']) : '';
            $opening_bank_address = isset($_POST['opening_bank_address']) ? trim($_POST['opening_bank_address']) : '';
            $bank_card_num = isset($_POST['bank_card_num']) ? trim($_POST['bank_card_num']) : '';
            $chairman_id = isset($_POST['chairman_id']) && $_POST['chairman_id'] > 0 ? intval($_POST['chairman_id']) : $user_id;
            $secretary_id = isset($_POST['secretary_id']) ? intval($_POST['secretary_id']) : 0;
            $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
            $zone_match_type = isset($_POST['zone_match_type']) ? intval($_POST['zone_match_type']) : 0;
            $match_power = isset($_POST['match_power']) ? $_POST['match_power'] : [];
            $admin_power = isset($_POST['admin_power']) ? $_POST['admin_power'] : [];
            $user_status = isset($_POST['user_status']) ? intval($_POST['user_status']) : 0;
            $is_double = isset($_POST['is_double']) ? intval($_POST['is_double']) : 0;
            $is_able = isset($_POST['is_able']) ? intval($_POST['is_able']) : 2;
            $term_time = isset($_POST['term_time']) ? trim($_POST['term_time']) : '';
            $zone_number = isset($_POST['zone_number']) ? trim($_POST['zone_number']) : '';
            $whereProvicone = isset($_POST['whereProvicone']) ? trim($_POST['whereProvicone']) : '';
            $whereCity = isset($_POST['whereCity']) ? trim($_POST['whereCity']) : '';
            $whereArea = isset($_POST['whereArea']) ? trim($_POST['whereArea']) : '';
            $bank_card_name = isset($_POST['bank_card_name']) ? trim($_POST['bank_card_name']) : '';
            $center_manager_id = isset($_POST['center_manager_id']) ? intval($_POST['center_manager_id']) : 0;
            $apply_id = isset($_POST['apply_id']) ? intval($_POST['apply_id']) : 0;
            $zone_city = '';
            if($whereProvicone != '' && $whereProvicone != '-1') $zone_city .= $whereProvicone;
            if($whereCity != '' && $whereCity != '-1') $zone_city .= '-'.$whereCity;
            if($whereArea != '' && $whereArea != '-1') $zone_city .= '-'.$whereArea;

//            if($user_id < 0) $error_msg = '请选择负责人';
//            if($zone_match_type < 0) $error_msg = $error_msg==''?'请选择赛区类型':$error_msg.'<br >请选择赛区类型';
            if($zone_type === 0) $error_msg = $error_msg==''?'请选择机构类型':$error_msg.'<br >请选择机构类型';
//            if($user_id == $referee_id && $user_status !==1 && $user_status !== -2 && $user_status !== 99) $error_msg = $error_msg==''?'推荐人不能为机构账号':$error_msg.'<br >推荐人不能为机构账号';
            if(!is_array($match_power)) $error_msg = $error_msg==''?'赛事权限错误':$error_msg.'<br >赛事权限错误';
            if(!is_array($admin_power)) $error_msg = $error_msg==''?'课程权限错误':$error_msg.'<br >课程权限错误';
            if($user_status !== 1 && $user_status !== -2 && $user_status !== 99) $error_msg = $error_msg==''?'审核状态错误':$error_msg.'<br >审核状态错误';
//            if($zone_title == '') $error_msg = $error_msg==''?'请填写机构名称':$error_msg.'<br >请填写机构名称';
            if($zone_address == '' && $zone_match_type === 1) $error_msg = $error_msg==''?'请填写机构地址':$error_msg.'<br >请填写机构地址';
//            if($business_licence == '') $error_msg = $error_msg==''?'请填写营业执照':$error_msg.'<br >请填写营业执照';
            if($legal_person == '') $error_msg = $error_msg==''?'请填写法人':$error_msg.'<br >请填写法人';
            if($zone_city == '' && $zone_match_type == 2 && $user_status != -2) $error_msg = $error_msg==''?'请选择机构城市':$error_msg.'<br >请选择机构城市';
            if($opening_bank == '') $error_msg = $error_msg==''?'请填写开户行':$error_msg.'<br >请填写开户行';
           // if($opening_bank_address == '') $error_msg = $error_msg==''?'请填写开户行地址':$error_msg.'<br >请填写开户行地址';
            if($bank_card_num == '') $error_msg = $error_msg==''?'请填写银行卡号':$error_msg.'<br >请填写银行卡号';
//            if($chairman_id < 1) $error_msg = $error_msg==''?'请选择组委会主席':$error_msg.'<br >请选择组委会主席';
            if($zone_number == '' && ($user_status == 1 || $user_status == 99)) $error_msg = $error_msg==''?'机构编号不能为空':$error_msg.'<br >机构编号不能为空';
            if($old_zm_id < 1){
                if($center_manager_id === 0) $error_msg .= '<br >请选择负责人';
            }
            //是否已存在编号

            $number_old = $wpdb->get_row("SELECT id,zone_number,user_status FROM {$wpdb->prefix}zone_meta WHERE zone_number='{$zone_number}'", ARRAY_A);
            if($number_old && $number_old['id'] != $old_zm_id && $number_old['zone_number']) $error_msg .= '<br >当前机构编号已存在';
            if($parent_id > 0){
                $old_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}zone_meta WHERE id='{$old_zm_id}'");
                if($old_id == $parent_id) $error_msg = $error_msg==''?'上级不能是自身':$error_msg.'<br >上级不能是自身';
            }
            $zone_type_row = $this->getOrganizeTypeList($zone_type);
            if($zone_type_row['zone_type_alias'] != 'match') $zone_match_type = 0;

            if($zone_match_type !== 1 && $zone_type_row['zone_type_alias'] != 'trains' && $zone_type_row['zone_type_alias'] != 'test') $zone_title = '';
            if($zone_match_type !== 2 || $zone_match_type === 0) $is_double = 0;
            if($zone_match_type === 0 && $zone_type_row['zone_type_alias'] != 'trains' && $zone_type_row['zone_type_alias'] != 'test') $zone_title = '';
            if($error_msg == ''){
                $insertData = [
                    'type_id' => $zone_type,
//                    'user_id' => $user_id,
                    'referee_id' => $referee_id,
                    'zone_name' => $zone_title,
                    'bank_card_name' => $bank_card_name,
                    'zone_address' => $zone_address,
                    'zone_city' => $zone_city,
                    'business_licence' => $business_licence,
                    'legal_person' => $legal_person,
                    'is_able' => $is_able,
                    'opening_bank' => $opening_bank,
                    'zone_number' => $zone_number,
                    'opening_bank_address' => $opening_bank_address,
                    'bank_card_num' => $bank_card_num,
                    'chairman_id' => $chairman_id,
                    'secretary_id' => $secretary_id,
                    'is_double' => $is_double,
                    'term_time' => $term_time,
                    'match_role_id' => join(',',$match_power),
                    'role_id' => join(',',$admin_power),
                    'parent_id' => $parent_id,
                    'zone_match_type' => $zone_match_type,
                ];
                if($user_status !== 0 && $user_status !== 99) $insertData['user_status'] = $user_status;
                if($user_status === 1 || $user_status === -2) $insertData['audit_time'] = get_time('mysql');//审核时间
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
                $wpdb->query('START TRANSACTION');
                if($old_zm_id>0){
                    $zmv = $wpdb->get_row("SELECT user_id,type_id,id,apply_id,center_manager_id,zone_name FROM {$wpdb->prefix}zone_meta WHERE id='{$old_zm_id}' AND (user_status='-1' OR user_status='-2')",ARRAY_A);
                    $bool = $wpdb->update($wpdb->prefix.'zone_meta',$insertData,['id'=>$old_zm_id]);
                }else{
                    $insertData['created_time'] = get_time('mysql');
                    $insertData['center_manager_id'] = $center_manager_id;
                    $insertData['apply_id'] = $apply_id;
                    $insertData['audit_time'] = get_time('mysql');
                    $bool = $wpdb->insert($wpdb->prefix.'zone_meta',$insertData);
                    $zmv = [
                        'user_id' => 0,
                        'type_id' => $insertData['type_id'],
                        'id' =>$wpdb->insert_id,
                        'apply_id' =>$apply_id,
                        'zone_name' =>$zone_title,
                        'center_manager_id' =>$center_manager_id,
                    ];
                }
                if(!$bool){
                    $wpdb->query('ROLLBACK');
                    $error_msg = '操作失败!';
                    is_file($upload_dir['basedir'].$dir.$file) && unlink($upload_dir['basedir'].$dir.$file);
                }else{
                    //收益和机构
                    if($user_status === 1){
                        if($zmv && $zmv['user_id'] < 1){
                            //不存在user_id,创建新用户和战队以及收益,如果已经存在user_Id,不再创建用户和收益战队

                            //创建新账号
                            $user_email = rand(0000,9999).date('is', get_time()).'@gjnlyd.com';
                            $user_password = '123456';
                            $user_id = wp_create_user($user_email,$user_password,$user_email);
                            if($user_id < 1) {
                                $error_msg = '操作失败!';
                            }
                            update_user_meta($user_id, 'user_ID', 10000000+$user_id);
                            if($error_msg == '') {
                                //更新机构所有者id
                                if (!$wpdb->update($wpdb->prefix . 'zone_meta', ['user_id' => $user_id], ['id' => $zmv['id']])) {
                                    $error_msg = '更新机构所有者id失败!';
                                }
                            }
                            if($error_msg == '') {
                                //添加机构管理员
                                $zone_manager_user_id = $old_zm_id > 0?$zmv['apply_id']:$zmv['center_manager_id'];
                                if (!$wpdb->insert($wpdb->prefix . 'zone_manager', ['zone_id' => $zmv['id'], 'user_id' => $zone_manager_user_id])) {
                                    $error_msg = '添加管理员失败!';
                                }
                            }
                            if($error_msg == '' && $old_zm_id > 0) {
                                //更新管理员/负责人
                                $center_manager_id = $wpdb->get_var("SELECT center_manager_id FROM {$wpdb->prefix}zone_meta WHERE user_id='{$user_id}'");
                                if(!$center_manager_id){
                                    if (!$wpdb->update($wpdb->prefix . 'zone_meta', ['center_manager_id' => $zmv['apply_id']], ['user_id' => $user_id])) {
                                        $error_msg = '更新管理员失败!';
                                    }
                                }
                            }

                            //创建战队
                            if($error_msg == '') {
                                if(!empty($zone_city)){
                                    $city_arr = str2arr($zone_city,'-');
                                    if(!empty($city_arr[2])){
                                        $city = $city_arr[2];
                                    }elseif ($city_arr[1] != '市辖区'){
                                        $city = $city_arr[1];
                                    }else{
                                        $city = $city_arr[0];
                                    }
                                }
                                $team_title = $zmv['zone_name'].$city.'战队';
//                                $team_title = date_i18n('Y',get_time()).'脑力世界杯'.$city.'战队';
                                $team_id = wp_insert_post(['post_title' => $team_title,'post_status' => 'publish', 'comment_status' => 'close', 'ping_status' => 'close','post_type' => 'team']);

                                if($team_id > 0){
                                    $teamInsert = [
                                        'user_id' => $user_id,
                                        'team_id' => $team_id,
                                        'team_director' => $user_id,
                                        'team_world' => '暂未设置',
                                        'team_slogan' => '普及脑力运动,造福世界人民',
                                        'team_brief' => '暂未设置',
                                        'team_status' => 2,
                                        'created_time' => get_time('mysql'),
                                    ];
                                    if(!$wpdb->insert($wpdb->prefix.'team_meta',$teamInsert)){
                                        $error_msg = '添加战队失败!';
                                    }
                                }else{
                                    $error_msg = '添加战队失败!';
                                }
                            }


                            //============
                            if($error_msg == '' && $zmv['apply_id'] > 0){
                                $orgType = $wpdb->get_row("SELECT zone_type_alias,zone_type_name FROM {$wpdb->prefix}zone_type WHERE id='{$zmv['type_id']}'", ARRAY_A);
                                if(!$orgType){
                                    $error_msg = '<br />获取机构类型失败!';
                                }
                                if($error_msg == ''){
                                    if($orgType['zone_type_alias'] == 'match') $orgType['zone_type_name'] = '赛区';
                                    $spread_set = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}spread_set WHERE spread_type='{$orgType['zone_type_alias']}' AND spread_status=1", ARRAY_A);
                                    //更新新账号推荐人和推荐时间
                                    $apply_user = $wpdb->get_row("SELECT referee_id,user_mobile FROM {$wpdb->users} WHERE ID='{$zmv['apply_id']}'", ARRAY_A);
                                    if(!$apply_user) $error_msg = '<br />查询申请人信息失败';
                                    $referee_id = $apply_user['referee_id'];
                                }

                                if($error_msg == '' && $referee_id > 0){
                                    if(!$wpdb->update($wpdb->users,['referee_id' => $referee_id,'referee_time'=>get_time('mysql')],['ID' => $user_id])){
                                        $error_msg = '更新机构推荐人失败!';
                                    }
                                    if(!$spread_set){
                                        $error_msg = '无收益设置!';
                                    }
                                     if($error_msg == ''){
                                         //机构类型
                                         if($spread_set){
                                             //添加上级收益
                                             //获取一级上级
                                             $referee_id1 = $wpdb->get_var("SELECT referee_id FROM {$wpdb->users} WHERE ID='{$user_id}'");
                                             $referee_id2 = 0;
                                             if($referee_id1 > 0) $referee_id2 = $wpdb->get_var("SELECT referee_id FROM {$wpdb->users} WHERE ID='{$referee_id1}'");
                                             if($spread_set['direct_superior'] > 0 || $spread_set['indirect_superior'] > 0){
                                                 //添加分成记录
//                                                 $insertData3 = [
//                                                     'income_type' => 'subject',
//                                                     'user_id' => $zmv['apply_id'],
//                                                     'referee_id' => $referee_id1,
//                                                     'referee_income' => $spread_set['direct_superior'],
//                                                     'indirect_referee_id' => $referee_id2 > 0 ? $referee_id2 : 0,
//                                                     'indirect_referee_income' => $referee_id2 > 0 ? $spread_set['indirect_superior'] : 0,
//                                                     'income_status' => 2,
//                                                     'match_id' => $zone_type,
//                                                     'created_time' => get_time('mysql'),
//                                                 ];
//                                                 $bool = $wpdb->insert($wpdb->prefix.'user_income_logs',$insertData3);
//                                                 if(!$bool) {
//                                                     $error_msg = '添加分成记录失败!';
//                                                 }
                                                 if($error_msg == ''){
                                                     $stream_type = '';
                                                     switch ($this->getOrganizeTypeList($zone_type)['zone_type_alias']){
                                                         case 'trains':
                                                             $stream_type = 'recommend_trains_zone';
                                                             break;
                                                         case 'test':
                                                             $stream_type = 'recommend_test_zone';
                                                             break;
                                                         case 'match':
                                                             $stream_type = 'recommend_match_zone';
                                                             break;
                                                     }
//                                                     $user_income_logs_id = $wpdb->insert_id;
                                                     if($referee_id1 > 0 && $spread_set['direct_superior'] > 0){
                                                         //添加一级上级收益流水
                                                         $insertData1 = [
                                                             'user_id' => $referee_id1,
                                                             'provide_id' => $user_id,
                                                             'user_type' => $zone_type,
                                                             'match_id' => 0,
                                                             'income_type' => $stream_type,
                                                             'income_status' => 2,
                                                             'user_income' => $spread_set['direct_superior'],
                                                             'created_time' => get_time('mysql'),
                                                         ];
                                                         $bool = $wpdb->insert($wpdb->prefix.'user_stream_logs',$insertData1);
                                                         if(!$bool) {
                                                             $error_msg = '添加直接上级收益失败!';
                                                         }
                                                         if($error_msg == ''){
                                                             //获取二级上级
                                                             if($referee_id2 > 0 && $spread_set['indirect_superior'] > 0){
                                                                 //添加二级上级收益流水
                                                                 $insertData2 = [
                                                                     'user_id' => $referee_id2,
                                                                     'provide_id' => $user_id,
                                                                     'user_type' => $zone_type,
                                                                     'match_id' => 0,
                                                                     'income_type' => $stream_type,
                                                                     'income_status' => 2,
                                                                     'user_income' => $spread_set['indirect_superior'],
                                                                     'created_time' => get_time('mysql'),
                                                                 ];
                                                                 $bool = $wpdb->insert($wpdb->prefix.'user_stream_logs',$insertData2);
                                                                 if(!$bool) {
                                                                     $error_msg = '添加间接上级收益失败!';
                                                                 }
                                                             }
                                                         }
                                                     }
                                                 }
                                             }

                                         }
                                     }

                                }
                            }
                            if($error_msg == ''){
                                $wpdb->query('COMMIT');
                                $ali = new AliSms();
                                $msg_bool = $ali->sendSms($apply_user['user_mobile'], 6, array('type_name'=>$orgType['zone_type_name'], 'user_login' => $user_email, 'password' => $user_password));
                                $success_msg = '操作成功';
                                if(!$msg_bool) $send_msg = '<br />短信发送失败!';
                            }else{
                                $wpdb->query('ROLLBACK');
                                is_file($upload_dir['basedir'].$dir.$file) && unlink($upload_dir['basedir'].$dir.$file);
                            }
                        }else{
                            if($old_zm_id == 0 || (isset($zmv['user_id']) && $zmv['user_id'] > 0)){
                                $wpdb->query('COMMIT');
                                $success_msg = '操作成功!';
                            }else{
                                $wpdb->query('ROLLBACK');
                                $error_msg = '操作失败!';
                            }

                        }

                    }
                    else{
                        $wpdb->query('COMMIT');
                        $success_msg = '操作成功';
                    }
                }
            }
        }
        //类型列表
        $typeList = $this->getOrganizeTypeList();
        $zone_citys = [];
        if($old_zm_id > 0){
            $row = $wpdb->get_row("SELECT zm.user_id,zm.type_id,zm.referee_id,zm.user_status,u.user_mobile,u.user_login,um.meta_value AS user_real_name,zm.zone_name,zm.is_able,
                   um2.meta_value AS referee_real_name,u2.user_login AS referee_login,u2.user_mobile AS referee_mobile,zm.zone_address,zm.business_licence,zm.business_licence_url,
                   zm.legal_person,zm.opening_bank,zm.opening_bank_address,zm.bank_card_num,um3.meta_value AS chairman_real_name,um4.meta_value AS secretary_real_name,zm.apply_id,zm.created_time,
                   zm.chairman_id,zm.secretary_id,zm.match_role_id,zm.role_id,zmp.zone_name AS parent_name,zm.parent_id,zm.zone_match_type,zm.zone_city,zm.term_time,zm.is_double,zm.zone_number,zm.bank_card_name, 
                   zt.zone_type_alias
                   FROM {$wpdb->prefix}zone_meta AS zm 
                   LEFT JOIN {$wpdb->prefix}zone_type AS zt ON zt.id=zm.type_id 
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
            $zone_citys = explode('-', $row['zone_city']);
            $role_id = $row['role_id']; //已有课程权限
        }else{
            $role_id = $wpdb->get_row("SELECT role_id FROM {$wpdb->prefix}zone_join_role WHERE zone_type_id='{$typeList[0]['id']}'",ARRAY_A);
            $role_id = $role_id['role_id'];
            $match_role_id = $wpdb->get_row("SELECT match_role_id FROM {$wpdb->prefix}zone_join_match_role WHERE zone_type_id='{$typeList[0]['id']}'",ARRAY_A);
            $match_role_id = $match_role_id['match_role_id'];
        }
        $match_role_id = explode(',',$match_role_id);
        $role_id = explode(',',$role_id);
        //所有赛事权限
        $allMatchPowerList = $wpdb->get_results("SELECT id,role_name FROM {$wpdb->prefix}zone_match_role WHERE status=1", ARRAY_A);
        //所有基础权限
        $allPowerList = $wpdb->get_results("SELECT id,role_name FROM {$wpdb->prefix}zone_type_role", ARRAY_A);
        ?>
        <div class="wrap">
            <?php
            if($user_id == 0) echo '<h1 id="add-new-user">添加/编辑机构</h1>';
            ?>

            <div id="ajax-response">
                <span style="color: #2bc422"><?=$success_msg?></span>
                <span style="color: #c44e00"><?=$error_msg?></span>
                <span style="color: #c44e00"><?=isset($send_msg) ? $send_msg : ''?></span>
            </div>

            <style type="text/css">
                input[type=text]{
                    height: 35px;!important;
                    width: 300px;
                }
                .wp-admin select {
                    padding: 2px;
                    line-height: 35px;
                    height: 35px;
                    width: 300px;
                }
            </style>
            <form method="post" action="" class="validate" novalidate="novalidate" enctype="multipart/form-data">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="5f6ea9ff44">
                <input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php">
                <table class="form-table">
                    <tbody>
                    <?php
                    if($old_zm_id > 0){
                        ?>
                        <tr class="form-field form-required">
                            <th scope="row"><label for="">申请信息 </label></th>
                            <td>
                                <div style="font-size: 22px;color: #3f77c4">由<?=get_user_meta($row['apply_id'],'user_real_name',true)['real_name']?>于<?=$row['created_time']?>申请</div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr class="" style="">
                        <th scope="row"><label for="zone_number">机构编号 </label></th>
                        <td>
<!--                            --><?//=$row['zone_number']?>
                            <input type="text" name="zone_number" id="zone_number" value="<?=$row['zone_number']?>">
                        </td>
                    </tr>
                     <?php if($old_zm_id > 0){?>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="user_id">赛区账号 </label></th>
                        <td>
                            <input type="hidden" name="user_id" value="<?=$row['user_id']?>">
                            <?=($real_name = isset($row['user_real_name']) ? unserialize($row['user_real_name'])['real_name'] : $row['user_login']) ? $real_name : '无'?>
                            <?=!empty($row['user_mobile'])?'('.$row['user_mobile'].')':''?>
                        </td>
                    </tr>
                     <?php }else{ ?>
                         <tr class="form-field form-required">
                             <th scope="row"><label for="center_manager_id">负责人 </label></th>
                             <td>
                                 <select class="js-data-select-ajax" name="center_manager_id" data-action="get_base_user_list" data-type="base">

                                 </select>
                             </td>
                         </tr>
                         <tr class="form-field form-required">
                             <th scope="row"><label for="apply_id">申请人 </label></th>
                             <td>
                                 <select class="js-data-select-ajax" name="apply_id" data-action="get_base_user_list" data-type="base">

                                 </select>
                             </td>
                         </tr>
                         <?php
                     }
                     ?>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="zone_type">机构类型</label></th>
                        <td>
                            <select name="zone_type" id="zone_type" <?=$old_zm_id < 1 ? 'id="zone_type"':''?>>
                                <?php foreach ($typeList as $tlv){ ?>
                                    <option value="<?=$tlv['id']?>" data-alias="<?=$tlv['zone_type_alias']?>" <?=$row['type_id']==$tlv['id']?'selected="selected"':''?> ><?=$tlv['zone_type_name']?></option>
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
                        <th scope="row">管理权限</th>
                        <td id="admin_power_td">
                            <?php foreach ($allPowerList as $plv){
                                ?>
                                <label for="admin_power_<?=$plv['id']?>"><input <?=in_array($plv['id'],$role_id)?'checked="checked"':''?> id="admin_power_<?=$plv['id']?>" type="checkbox" name="admin_power[]" value="<?=$plv['id']?>"><?=$plv['role_name']?></label>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
<!--                    <tr class="form-field">-->
<!--                        <th scope="row"><label for="referee_id">推荐人 </label></th>-->
<!--                        <td>-->
<!--                            --><?php
//                            if($old_zm_id > 0){
//                                ?>
<!--                                <input type="hidden" name="referee_id" value="--><?//=$row['referee_id']?><!--">-->
<!--                                --><?//=($referee_name = isset($row['referee_real_name']) ? unserialize($row['referee_real_name'])['real_name'] : $row['referee_login']) ? $referee_name : '无'?>
<!--                                --><?//=!empty($row['referee_mobile'])?'('.$row['referee_mobile'].')':''?>
<!--                                --><?php
//                            }else{
//                                ?>
<!--                                <select class="js-data-select-ajax" name="referee_id" data-action="get_base_user_list" data-type="all">-->
<!--                                    <option value="--><?//=$row['referee_id']?><!--" selected="selected">-->
<!--                                        --><?//=isset($row['referee_real_name']) ? unserialize($row['referee_real_name'])['real_name'] : $row['referee_login']?>
<!--                                        --><?//=!empty($row['referee_mobile'])?'('.$row['referee_mobile'].')':''?>
<!--                                    </option>-->
<!--                                </select>-->
<!--                                --><?php
//
//                            }
//                            ?>
<!--                        </td>-->
<!--                    </tr>-->
                    <tr class="form-field">
                        <th scope="row"><label for="parent_id">上级机构 </label></th>
                        <td>
                            <?php
                            if($old_zm_id > 0){
                                ?>
                                <input type="hidden" name="parent_id" value="<?=$row['parent_id']?>">
                                <?=$row['parent_name'] ? $row['parent_name'] :'无'?>
                                <?php
                            }else{
                                ?>
                                <select class="js-data-select-ajax" name="parent_id" data-action="get_base_zone_list" data-type="parent">
                                    <option value="<?=$row['parent_id']?>" selected="selected">
                                        <?=$row['parent_name']?>
                                    </option>
                                </select>
                                <?php

                            }
                            ?>

                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="zone_match_type">赛区类型 </label></th>
                        <td>
                            <select name="zone_match_type" id="zone_match_type">
                                <option <?=$row['zone_match_type']=='1'?'selected="selected"':''?> value="1">战队精英赛</option>
                                <option <?=$row['zone_match_type']=='2'?'selected="selected"':''?> value="2">城市赛</option>
                                <option <?=$row['zone_match_type']=='0'?'selected="selected"':''?> value="0">其它</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="" style="<?=isset($row) && $row['zone_match_type'] != '1' && $row['zone_type_alias'] != 'trains' && $row['zone_type_alias'] != 'test' ? 'display: none':''?>" id="zone_title_tr">
                        <th scope="row"><label for="zone_title">字号 </label></th>
                        <td>
                            <input type="text" name="zone_title" id="zone_title" value="<?=$row['zone_name']?>">
                        </td>
                    </tr>
                    <tr class="" style="<?=$row['zone_match_type'] != '2' ? 'display: none':''?>" id="is_double_tr">
                        <th scope="row"><label for="is_double">区县 </label></th>
                        <td>
                            <label for="is_double_1"><input type="radio" <?=$row['is_double'] == '1' ? 'checked="checked"':''?> id="is_double_1" name="is_double" value="1">多区县</label>
                            <label for="is_double_2"><input type="radio" <?=$row['is_double'] == '2' ? 'checked="checked"':''?> id="is_double_2" name="is_double" value="2">单区县</label>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="zone_city">机构城市 </label></th>
                        <td>
                            <select name="whereProvicone" id="whereProvicone"></select>
                            <select name="whereCity" id="whereCity"></select>
                            <select name="whereArea" id="whereArea"></select>
<!--                            <input type="text" name="zone_city" value="--><?//=$row['zone_city']?><!--">-->
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="chairman_id">主席 </label></th>
                        <td>
                            <select class="js-data-select-ajax" name="chairman_id" data-action="get_base_user_list" data-type="select">
                                <option value="<?=$row['chairman_id']?>" selected="selected">
                                    <?=isset($row['chairman_real_name']) ? unserialize($row['chairman_real_name'])['real_name'] : ''?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="secretary_id">秘书长 </label></th>
                        <td>
                            <select class="js-data-select-ajax" name="secretary_id" data-action="get_base_user_list" data-type="select">
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
                    <!--<tr class="">
                        <th scope="row"><label for="business_licence">营业执照 </label></th>
                        <td>
                            <input type="text" name="business_licence" value="<?/*=$row['business_licence']*/?>">
                        </td>
                    </tr>-->
                    <tr class="">
                        <th scope="row"><label for="business_licence_url">营业执照照片 </label></th>
                        <td id="img-y">
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
                        <th scope="row"><label for="bank_card_name">机构开户名称 </label></th>
                        <td>
                            <input type="text" name="bank_card_name" value="<?=$row['bank_card_name']?>">
                        </td>
                    </tr>
                    <!--<tr class="">
                        <th scope="row"><label for="opening_bank_address">开户行地址 </label></th>
                        <td>
                            <input type="text" name="opening_bank_address" value="<?/*=$row['opening_bank_address']*/?>">
                        </td>
                    </tr>-->
                    <tr class="">
                        <th scope="row"><label for="bank_card_num">银行卡号 </label></th>
                        <td>
                            <input type="text" name="bank_card_num" value="<?=$row['bank_card_num']?>">
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="term_time">有效期 </label></th>
                        <td>
                            <label for="term_time_radio_1"><input id="term_time_radio_1" <?=isset($row['term_time']) && $row['term_time']? 'checked="checked"':''?> type="radio" value="1" name="term_time_radio" class="term_time_radio">有</label>
                            <label for="term_time_radio_2"><input id="term_time_radio_2" <?=!isset($row['term_time']) || !$row['term_time'] ? 'checked="checked"':''?> type="radio" value="2" name="term_time_radio" class="term_time_radio">无</label>
                             <input style="<?=!isset($row['term_time']) || !$row['term_time']? 'display:none;':''?>" type="text" style="max-width: 500px;" value="<?=isset($row['term_time']) ? $row['term_time'] : date('Y-m-d H:i:s',strtotime('+1year', get_time()))?>" name="term_time" class="layui-input date-picker y-m-d-h-m-s" readonly  id="term_time" placeholder="有效期">

                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="">申请状态 </label></th>
                        <td>
                        <?php if($row['user_status'] == '-1' || $old_zm_id == 0 || $row['user_status'] == '-2'){
                            ?>
                            <label for="user_status"> <input type="checkbox" class="apply_ch" name="user_status" id="user_status" value="1" />通过审核 </label>
                            <label for="user_status2"> <input type="checkbox" <?=$row['user_status'] == '-2' ? 'checked="checked"':''?> class="apply_ch" name="user_status" id="user_status2" value="-2" />拒绝申请 </label>
                            <?php
                        }else{
                            switch ($row['user_status']){
                                case '1':
                                    echo '正常';
                                    break;
                                case '-2':
                                    echo '未通过';
                                    break;
                            }
                            echo '<input type="hidden" class="" name="user_status" value="99" />';
                        } ?>

                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="">冻结状态 </label></th>
                        <td>
                            <label for="is_able"> <input type="checkbox" <?=$row['is_able'] == '1' || !isset($row)? 'checked="checked"':''?> class="apply_ch2" name="is_able" id="is_able" value="1" />正常 </label>
                            <label for="is_able2"> <input type="checkbox" <?=$row['is_able'] == '2' ? 'checked="checked"':''?> class="apply_ch2" name="is_able" id="is_able2" value="2" />冻结 </label>
                        </td>
                    </tr>

                    </tbody>
                </table>
                <?php if($old_zm_id > 0){ ?>

                    <button type="button" id="editForm" data-type="edit" class="button">编辑</button>
                <?php } ?>
                    <input type="submit" name="createuser" id="createusersub" class="button button-primary" value="提交">

            </form>
            <script>
                jQuery(document).ready(function($) {
                    layui.use(['laydate',], function(){
                        var laydate = layui.laydate;
                        //日期时间选择器
                        $('.date-picker').each(function(){
                            var id=$(this).attr('id');
                            var format='yyyy-MM-dd HH:mm';
                            var type='datetime';
                            if($(this).hasClass('y-m-d')){
                                format='yyyy-MM-dd';
                                type='date'
                            }else if($(this).hasClass('y-m-d-h-m-s')){
                                format='yyyy-MM-dd HH:mm:ss';
                                type='datetime';
                            }


                            laydate.render({
                                elem: '#'+id
                                ,type: type
                                ,format: format
                            });
                        })
                    })
                    $('#zone_type').on('change', function () {
                        var val = $(this).val();
                        var alias = $(this).find('option:selected').attr('data-alias');

                        if(alias == 'trains' || alias == 'test'){
                            $('#zone_title_tr').show();
                        }
                        $.ajax({
                            url : ajaxurl,
                            data : {'action':'getPowerListByType','val':val},
                            dataType : 'json',
                            type : 'post',
                            success : function (response) {
                                if(response['success']){
                                    $.each($('#admin_power_td').find('input'),function (i,v) {
                                        if($.inArray($(v).val(),response.data.data) >= 0) {
                                            $(v).prop('checked',true);
                                        }else{
                                            $(v).prop('checked',false);
                                        }
                                    });
                                }
                            }
                        });
                    });
                    $('.apply_ch').on('change',function () {
                        var val = $(this).val();
                        var status = !$(this).prop('checked');
                        if(val == '-2'){
                            $('#user_status').prop('checked',status);
                        }else if(val == '1'){
                            $('#user_status2').prop('checked',status);
                        }
                    });
                    $('.apply_ch2').on('change',function () {
                        var val = $(this).val();
                        var status = !$(this).prop('checked');
                        if(val == '2'){
                            $('#is_able').prop('checked',status);
                        }else if(val == '1'){
                            $('#is_able2').prop('checked',status);
                        }
                    });
                    $('#zone_match_type').on('change', function () {
                        var val = $(this).val();
                        var alias  = $('#zone_type').find('option:selected').attr('data-alias');

                        if(val == '1'){
                            $('#zone_title_tr').show();
                            $('#is_double_tr').hide();
                        }else if(val == '0'){
                            $('#is_double_tr').hide();
                            $('#zone_title_tr').hide();
                        }else{
                            $('#zone_title_tr').hide();
                            $('#is_double_tr').show();
                        }
                        if(alias == 'trains' || alias == 'test'){
                            $('#zone_title_tr').show();
                        }
                    });
                    $('.term_time_radio').on('click',function () {
                        var val = $(this).val();
                        if(val == '1'){
                            $('input[name="term_time"]').show();
                        }else{
                            $('input[name="term_time"]').hide();
                            $("#term_time").val('');
                        }
                    });
                    <?php if($old_zm_id > 0){ ?>
                    $('input').prop('disabled','disabled');
                    $('select').prop('disabled','disabled');
                    $('#editForm').on('click', function () {
                        var _type = $(this).attr('data-type');
                        var _ab = '';
                        if(_type == 'edit'){
                            $(this).text('取消编辑');
                            $(this).attr('data-type','disable');
                        }else if(_type == 'disable'){
                            $(this).text('编辑');
                            $(this).attr('data-type','edit');
                            _ab = 'disabled';
                        }
                        $('input').prop('disabled',_ab);
                        $('select').prop('disabled',_ab);
                    });
                    <?php } ?>
                    initAddress('where','<?=isset($zone_citys[0])?$zone_citys[0]:''?>',
                        '<?=isset($zone_citys[1])?$zone_citys[1]:''?>',
                        '<?=isset($zone_citys[2])?$zone_citys[2]:''?>');
                    function initAddress(_name, province,city,area){
                        var provicone_html = '<option data-index="-1" value="-1">请选择</option>'
                        var city_html = '<option data-index="-1" value="-1">请选择</option>';
                        var area_html = '<option data-index="-1" value="-1">请选择</option>',selectedArea='';
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
                    function changeProvicone(_this,_name){
                        var val = _this.find('option:selected').attr('data-index');
                        if(val > -1){
                            var city_html = '<option data-index="-1" value="-1">请选择</option>';
                            $.each($.validationLayui.allArea.area[val].childs,function (cindex,cvalue) {
                                city_html += '<option data-index="'+val+'_'+cindex+'" value="'+cvalue.value+'">'+cvalue.value+'</option>';
                            });
                            $('#'+_name+'City').html(city_html);
                            // var area_html = '';
                            // $.each($.validationLayui.allArea.area[val].childs[0].childs,function (aindex,avalue) {
                            //     area_html += '<option data-index="'+aindex+'" value="'+avalue.value+'">'+avalue.value+'</option>';
                            // });
                            // $('#'+_name+'Area').html(area_html);
                        }else{
                            $('#'+_name+'City').html('<option data-index="-1" value="-1">请选择</option>');
                        }
                        $('#'+_name+'Area').html('<option data-index="-1" value="-1">请选择</option>');
                    }
                    $('#whereCity').on('change', function () {
                        changeCity($(this),'where');
                    });
                    function changeCity(_this,_name) {
                        var val = _this.find('option:selected').attr('data-index');
                        val = val.split('_');
                        if(val[0] > -1){
                            var area_html = '<option data-index="-1" value="-1">请选择</option>';
                            $.each($.validationLayui.allArea.area[val[0]].childs[val[1]].childs,function (aindex,avalue) {
                                area_html += '<option value="'+avalue.value+'">'+avalue.value+'</option>';
                            });
                            $('#'+_name+'Area').html(area_html);
                        }else{
                            $('#'+_name+'Area').html('');
                        }
                    }

                });
            </script>

            <script>
                jQuery(document).ready(function($) {
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        var _title = '';
                        layer.photos({//图片预览
                            photos: '#img-y',
                            move : false,
                            anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                        })
                    });
                });
            </script>
        </div>
        <?php
    }

    /**
     * 新增/编辑机构权限
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
                if($role_type === 2){
                    //基础权限
                    if($id > 0){
                        $bool = $wpdb->update($wpdb->prefix.'zone_type_role',$insertData,['id'=>$id]);
                    }else{
                        $bool = $wpdb->insert($wpdb->prefix.'zone_type_role',$insertData);
                    }
                }else{
                    //赛事考级权限
                    if($id > 0){
                        $bool = $wpdb->update($wpdb->prefix.'zone_match_role',$insertData,['id'=>$id]);
                    }else{
                        $bool = $wpdb->insert($wpdb->prefix.'zone_match_role',$insertData);
                    }
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
            <h1 id="add-new-user">添加/编辑机构权限</h1>

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
                                <option value="2" <?=$row['role_type'] == '2'?'selected="selected"':''?>>基本权限</option>
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
     * 机构权限
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
        //数量
        //全部
        $sql = "SELECT COUNT(id) FROM {$wpdb->prefix}zone_type_role";
        $all_num = $wpdb->get_var($sql);
        $match_num = $wpdb->get_var($sql." WHERE role_type='1'");
        $course_num = $wpdb->get_var($sql." WHERE role_type='2'");
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">机构权限列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize-power')?>" class="page-title-action">添加机构权限</a>

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
            <h2 class="screen-reader-text">机构列表</h2><table class="wp-list-table widefat fixed striped users">
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
                                    echo '赛事/考级';
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
     * 机构成员
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
        //机构资料
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
            <h2 class="screen-reader-text">机构列表</h2><table class="wp-list-table widefat fixed striped users">
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
     * 新增机构成员
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
        //机构资料
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
                        <th scope="row"><label for="zone_title">机构名称 </label></th>
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
     * 机构收益提现记录
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
            <h1 class="wp-heading-inline">机构收益记录</h1>

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
            <h2 class="screen-reader-text">机构列表</h2><table class="wp-list-table widefat fixed striped users">
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
     * 机构统计信息
     */
    public function organizeStatistics(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $type = isset($_GET['type']) ? intval($_GET['type']) : 6;

        $id < 1 && exit('参数错误!');
        global $wpdb;
        //查询机构信息
        $zone_meta = $wpdb->get_row("SELECT zone_name,user_id,zone_city,zone_match_type,type_id,zone_number FROM {$wpdb->prefix}zone_meta WHERE id='{$id}'", ARRAY_A);
        if($zone_meta['user_id'] > 0){
            //各种数量
            //比赛数量
            $match_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}match_meta_new WHERE created_id='{$zone_meta['user_id']}'");

            //考级数量
            $grading_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}grading_meta WHERE created_person='{$zone_meta['user_id']}'");
            //课程数量
            $course_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}course WHERE zone_id='{$zone_meta['user_id']}'");

            //总收益
            $stream_all = $wpdb->get_var("SELECT SUM(user_income) FROM {$wpdb->prefix}user_stream_logs WHERE user_id='{$zone_meta['user_id']}'");
        }else{
            $match_num = 0;
            $grading_num = 0;
            $course_num = 0;
            $stream_all = 0;
        }

//        $rows = [];
        //获取数据

        //获取机构类型别名显示机构名称
        $type_alias = $wpdb->get_var("SELECT zone_type_alias FROM {$wpdb->prefix}zone_type WHERE id={$zone_meta['type_id']}");

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">
                <?php
//                $city_arr = str2arr($zone_meta['zone_city'],'-');
//                if(!empty($city_arr[2])){
//                    $city = $city_arr[2];
//                }elseif ($city_arr[1] != '市辖区'){
//                    $city = $city_arr[1];
//                }else{
//                    $city = $city_arr[0];
//                }
//
//                switch ($type_alias){
//                    case 'match':
//                        echo date('Y').'脑力世界杯'. '<span style="color: #c40c0f">' .$city.'</span>'.($zone_meta['zone_match_type']=='1'?'战队精英赛':'城市赛');
//                        break;
//                    case 'trains':
//                        echo 'IISC'. '<span style="color: #c40c0f">' .$zone_meta['zone_name'].'</span>'.'国际脑力训练中心';
//                        break;
//                    case 'test':
//                        echo 'IISC'. '<span style="color: #c40c0f">' .$zone_meta['zone_name'].'</span>'.'国际脑力测评中心';
//                        break;
//                }
                $this->echoZoneName($type_alias,$zone_meta['zone_city'],$zone_meta['zone_name'],$zone_meta['zone_match_type'],$zone_meta['zone_number']);
                ?>
                -统计信息
            </h1>
            <hr class="wp-header-end">
            <ul class="subsubsub">
                <li class="all"><a href="<?=admin_url('admin.php?page=fission-organize-statistics&id='.$id.'&type=6')?>" <?=$type===6?'class="current"':''?> aria-current="page">基础资料<span class="count"></span></a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission-organize-statistics&id='.$id.'&type=1')?>" <?=$type===1?'class="current"':''?> aria-current="page">比赛<span class="count">（<?=$match_num > 0 ? $match_num : 0?>）</span></a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission-organize-statistics&id='.$id.'&type=2')?>" <?=$type===2?'class="current"':''?> aria-current="page">考级<span class="count">（<?=$grading_num > 0 ? $grading_num : 0?>）</span></a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission-organize-statistics&id='.$id.'&type=3')?>" <?=$type===3?'class="current"':''?> aria-current="page">课程<span class="count">（<?=$course_num > 0 ? $course_num : 0?>）</span></a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission-organize-statistics&id='.$id.'&type=4')?>" <?=$type===4?'class="current"':''?> aria-current="page">成员<span class="count"></span></a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission-organize-statistics&id='.$id.'&type=5')?>" <?=$type===5?'class="current"':''?> aria-current="page">收益<span class="count">（<?=$stream_all > 0 ? $stream_all : 0?>）</span></a></li>
            </ul>

            <br class="clear">
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <?php
            switch ($type){
                case 1:
                    $this->getOrganizeStatisticsMatch($zone_meta['user_id']);
                    break;
                case 2:
                    $this->getOrganizeStatisticsGrading($zone_meta['user_id']);
                    break;
                case 3:
                    $this->getOrganizeStatisticsCourse($zone_meta['user_id']);
                    break;
                case 4:
                    $this->getOrganizeStatisticsMember($zone_meta['user_id'],$id);
                    break;
                case 5:
                    $this->getOrganizeStatisticsIncome($zone_meta['user_id'],$id,$zone_meta);
                    break;
                case 6:
                    $this->addOrganize($zone_meta['user_id']);
                    break;
            }

            ?>

            <br class="clear">
        </div>
        <?php
    }

    /**
     * 机构统计信息比赛数据
     */
    public function getOrganizeStatisticsMatch($user_id){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS p.post_title AS match_name,mmn.* 
                FROM {$wpdb->prefix}match_meta_new AS mmn
                LEFT JOIN {$wpdb->posts} AS p ON p.ID=mmn.match_id
                LEFT JOIN {$wpdb->prefix}zone_meta AS zm ON zm.user_id=mmn.created_id
                WHERE mmn.created_id='{$user_id}'
                LIMIT {$start},{$pageSize}", ARRAY_A);
        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page,
            'add_fragment' => '&s='.$searchStr,
        ));
//        leo_dump($rows);
        ?>
        <div class="tablenav top">
            <div class="tablenav-pages">
                <span class="displaying-num"><?=$count['count']?>个项目</span>
                <?=$pageHtml?>
            </div>
            <br class="clear">
        </div>
        <h2 class="screen-reader-text">统计信息</h2>
        <table class="wp-list-table widefat fixed striped users">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                <th scope="col" id="match_name" class="manage-column column-match_name column-primary">比赛名称</th>
                <th scope="col" id="scene" class="manage-column column-scene">场景</th>
                <th scope="col" id="match_people_num" class="manage-column column-match_people_num">参赛人数</th>
                <th scope="col" id="cost" class="manage-column column-cost">费用</th>
                <th scope="col" id="match_address" class="manage-column column-match_address">比赛地点</th>
                <th scope="col" id="match_date" class="manage-column column-match_date">比赛日期</th>
                <th scope="col" id="sign_date" class="manage-column column-sign_date">报名日期</th>
                <th scope="col" id="created_date" class="manage-column column-created_date">发布日期</th>
                <th scope="col" id="match_status" class="manage-column column-match_status">比赛状态</th>
                <th scope="col" id="match_options" class="manage-column column-match_options">操作</th>
            </tr>
            </thead>

            <tbody id="the-list" data-wp-lists="list:user">

            <?php
            foreach ($rows as $row){
                //参赛人数
                $match_people_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}order WHERE match_id='{$row['match_id']}' AND order_type=1 AND pay_status IN(2,3,4)");
                ?>
                <tr>
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="cb-select-407">选择<?=$row['match_name']?></label>
                        <input id="cb-select-<?=$row['id']?>" type="checkbox" name="post[]" value="<?=$row['id']?>">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                            <span class="screen-reader-text">“<?=$row['match_name']?>”已被锁定</span>
                        </div>
                    </th>
                    <td class="match_name column-match_name has-row-actions column-primary" data-colname="比赛名称">
                        <?=$row['match_name']?>
                        <br>

                        <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                    </td>
                    <td class="scene column-scene" data-colname="场景">
                        <?php
                        switch ($row['match_scene']){
                            case '1':
                                echo '正式比赛';
                                break;
                            case '2':
                                echo '模拟比赛';
                                break;
                            default:
                                echo '未知';
                        }
                        ?>
                    </td>
                    <td class="match_people_num column-match_people_num" data-colname="参赛人数">
                        <a href="<?=admin_url('edit.php?post_type=match&page=match_student&match_id='.$row['match_id'])?>"> <?=$match_people_num > 0 ? $match_people_num : 0?></a>
                    </td>
                    <td class="cost column-cost" data-colname="费用"><?=$row['match_cost']?></td>
                    <td class="match_address column-match_address" data-colname="比赛地点"><?=$row['match_address']?></td>
                    <td class="match_date column-match_date" data-colname="比赛日期"><?=$row['match_start_time']?><br /><?=$row['match_end_time']?></td>
                    <td class="sign_date column-sign_date" data-colname="报名截止日期"><?=$row['entry_end_time']?></td>
                    <td class="created_date column-created_date" data-colname="发布日期"><?=$row['created_time']?></td>
                    <td class="match_status column-match_status" data-colname="比赛状态">
                    <?php
                    switch ($row['match_status']){
                        case '-3':
                            echo '已结束';
                            break;
                        case '-2':
                            echo '等待开赛';
                            break;
                        case '1':
                            echo '报名中';
                            break;
                        case '2':
                            echo '进行中';
                            break;
                        default:
                            echo '未知';
                    }
                    ?>
                    </td>
                    <td class="match_options column-match_options" data-colname="操作">
                        <a href="<?=admin_url('admin.php?page=match_student-ranking&match_id='.$row['match_id'])?>">成绩</a> |
                        <a href="<?=admin_url('post.php?post='.$row['match_id'].'&action=edit');?>">编辑</a>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tfoot>
            <tr>
                <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                <th scope="col" class="manage-column column-match_name column-primary">比赛名称</th>
                <th scope="col" class="manage-column column-scene">场景</th>
                <th scope="col" class="manage-column column-match_people_num">参赛人数</th>
                <th scope="col" class="manage-column column-cost">费用</th>
                <th scope="col" class="manage-column column-match_address">比赛地点</th>
                <th scope="col" class="manage-column column-match_date">比赛日期</th>
                <th scope="col" class="manage-column column-sign_date">报名日期</th>
                <th scope="col" class="manage-column column-created_date">发布日期</th>
                <th scope="col" class="manage-column column-match_status">比赛状态</th>
                <th scope="col" class="manage-column column-match_options">操作</th>
            </tr>
            </tfoot>

        </table>
        <div class="tablenav bottom">


            <div class="tablenav-pages">
                <span class="displaying-num"><?=$count['count']?>个项目</span>
                <?=$pageHtml?>
            </div>
            <br class="clear">
        </div>
        <?php
    }

    /**
     * 机构统计信息考级数据
     */
    public function getOrganizeStatisticsGrading($user_id){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS p.post_title AS grading_name,gm.*,p2.post_title AS grading_type 
                FROM {$wpdb->prefix}grading_meta AS gm
                LEFT JOIN {$wpdb->posts} AS p ON p.ID=gm.grading_id
                LEFT JOIN {$wpdb->posts} AS p2 ON p2.ID=gm.category_id
                WHERE gm.created_person='{$user_id}'
                LIMIT {$start},{$pageSize}", ARRAY_A);
        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page,
            'add_fragment' => '&s='.$searchStr,
        ));
//        leo_dump($rows);
        ?>
        <div class="tablenav top">
            <div class="tablenav-pages">
                <span class="displaying-num"><?=$count['count']?>个项目</span>
                <?=$pageHtml?>
            </div>
            <br class="clear">
        </div>
        <h2 class="screen-reader-text">统计信息</h2>
        <table class="wp-list-table widefat fixed striped users">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                <th scope="col" id="grading_name" class="manage-column column-grading_name column-primary">考级名称</th>
                <th scope="col" id="scene" class="manage-column column-scene">考级场景</th>
                <th scope="col" id="grading_type" class="manage-column column-grading_type">考级类型</th>
                <th scope="col" id="created_person" class="manage-column column-created_person">责任人</th>
                <th scope="col" id="grading_people_num" class="manage-column column-grading_people_num">考级人数</th>
                <th scope="col" id="cost" class="manage-column column-cost">费用</th>
                <th scope="col" id="grading_address" class="manage-column column-grading_address">考级地点</th>
                <th scope="col" id="grading_date" class="manage-column column-grading_date">考级日期</th>
                <th scope="col" id="sign_date" class="manage-column column-sign_date">报名日期</th>
                <th scope="col" id="created_date" class="manage-column column-created_date">发布日期</th>
                <th scope="col" id="grading_status" class="manage-column column-grading_status">考级状态</th>
                <th scope="col" id="grading_options" class="manage-column column-grading_options">操作</th>
            </tr>
            </thead>

            <tbody id="the-list" data-wp-lists="list:user">

            <?php
            foreach ($rows as $row){
                //参赛人数
                $match_people_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}order WHERE match_id='{$row['grading_id']}' AND order_type=2 AND pay_status IN(2,3,4)");
                //责任人
                $created_person = get_user_meta($row['created_person'], 'user_real_name', true)['real_name'];
                $created_person = $created_person ? $created_person : get_user_by('ID', $row['created_person'])->user_login;
                ?>
                <tr>
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="cb-select-407">选择<?=$row['grading_name']?></label>
                        <input id="cb-select-<?=$row['id']?>" type="checkbox" name="post[]" value="<?=$row['id']?>">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                            <span class="screen-reader-text">“<?=$row['grading_name']?>”已被锁定</span>
                        </div>
                    </th>
                    <td class="grading_name column-grading_name has-row-actions column-primary" data-colname="考级名称">
                        <?=$row['grading_name']?>
                        <br>

                        <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                    </td>
                    <td class="scene column-scene" data-colname="考级场景">
                        <?php
                        switch ($row['scene']){
                            case '1':
                                echo '正式考级';
                                break;
                            case '2':
                                echo '模拟考级';
                                break;
                            default:
                                echo '未知';
                        }
                        ?>
                    </td>
                    <td class="grading_type column-grading_type" data-colname="考级类型"><?=$row['grading_type']?></td>
                    <td class="created_person column-created_person" data-colname="责任人"><?=$created_person?></td>
                    <td class="grading_people_num column-grading_people_num" data-colname="考级人数">
                        <?=$match_people_num > 0 ? $match_people_num : 0?>
                    </td>
                    <td class="cost column-cost" data-colname="费用"><?=$row['cost']?></td>
                    <td class="grading_address column-grading_address" data-colname="考级地点"><?=$row['address']?></td>
                    <td class="grading_date column-grading_date" data-colname="考级日期"><?=$row['start_time']?><br /><?=$row['end_time']?></td>
                    <td class="sign_date column-sign_date" data-colname="报名截止日期"><?=$row['entry_end_time']?></td>
                    <td class="created_date column-created_date" data-colname="发布日期"><?=$row['created_time']?></td>
                    <td class="grading_status column-grading_status" data-colname="考级状态">
                        <?php
                        switch ($row['status']){
                            case '-3':
                                echo '已结束';
                                break;
                            case '-2':
                                echo '等待开赛';
                                break;
                            case '1':
                                echo '报名中';
                                break;
                            case '2':
                                echo '进行中';
                                break;
                            default:
                                echo '未知';
                        }
                        ?>
                    </td>
                    <td class="match_options column-match_options" data-colname="操作">
                        <a href="<?=admin_url('edit.php?post_type=grading&page=grading-students&grading_id='.$row['grading_id'])?>">成绩</a> |
                        <a href="<?=admin_url('post.php?post='.$row['grading_id'].'&action=edit');?>">编辑</a>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tfoot>
            <tr>
                <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                <th scope="col" class="manage-column column-grading_name column-primary">考级名称</th>
                <th scope="col" class="manage-column column-scene">考级场景</th>
                <th scope="col" class="manage-column column-grading_type">考级类型</th>
                <th scope="col" class="manage-column column-created_person">责任人</th>
                <th scope="col" class="manage-column column-grading_people_num">考级人数</th>
                <th scope="col" class="manage-column column-cost">费用</th>
                <th scope="col" class="manage-column column-grading_address">考级地点</th>
                <th scope="col" class="manage-column column-grading_date">考级日期</th>
                <th scope="col" class="manage-column column-sign_date">报名日期</th>
                <th scope="col" class="manage-column column-created_date">发布日期</th>
                <th scope="col" class="manage-column column-grading_status">考级状态</th>
                <th scope="col" class="manage-column column-grading_options">操作</th>
            </tr>
            </tfoot>

        </table>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <span class="displaying-num"><?=$count['count']?>个项目</span>
                <?=$pageHtml?>
            </div>
            <br class="clear">
        </div>
        <?php
    }


    /**
     * 机构统计信息收益数据
     */
    public function getOrganizeStatisticsIncome($user_id,$id,$zone_meta){
        exit('暂未开发');
        global $wpdb;
        $spreadClass = new Spread();
        $typeArr = $spreadClass->getIncomeTypeArr();
//        $itype = isset($_GET['itype']) ? trim($_GET['itype']) : key($typeArr);
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS usl.*,uil.user_id AS pay_user_id 
                FROM {$wpdb->prefix}user_stream_logs AS usl
                LEFT JOIN {$wpdb->prefix}user_income_logs AS uil ON uil.id=usl.match_id
                WHERE usl.user_id='{$user_id}' AND usl.income_type='{$itype}'
                LIMIT {$start},{$pageSize}", ARRAY_A);
        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page,
            'add_fragment' => '&s='.$searchStr,
        ));
//        leo_dump($rows);
        //各种收益数量
//        $sql = "SELECT SUM(user_income) FROM {$wpdb->prefix}user_stream_logs WHERE user_id='{$zone_meta['user_id']}' AND income_type=";
//        $match_income = $wpdb->get_var($sql."'match'");
//        $grading_income = $wpdb->get_var($sql."'grading'");
//        $subject_income = $wpdb->get_var($sql."'subject'");
//        $extract_income = $wpdb->get_var($sql."'extract'");
//        $undertake_income = $wpdb->get_var($sql."'undertake'");
//        leo_dump($wpdb->last_query);die;

        $typeLiArr = [];
//        foreach ($typeArr as $tak => $tav){
//            $incom_num = $wpdb->get_var($sql."'{$tak}'");
//            $typeLiArr[] = '<li class="all"><a href="'.admin_url('admin.php?page=fission-organize-statistics&id='.$id.'&type=5&itype='.$tak).'" '.($itype==$tak?'class="current"':"").' aria-current="page">'.$tav.'<span class="count">（'.($incom_num != false ? $incom_num : 0).'）</span></a></li>';
//        }
//        leo_dump($typeArr);die;
        ?>
        <ul class="subsubsub">
<!--            --><?//=join(' | ',$typeLiArr)?>
        </ul>
        <div class="tablenav top">
            <div class="tablenav-pages">
                <span class="displaying-num"><?=$count['count']?>个项目</span>
                <?=$pageHtml?>
            </div>
            <br class="clear">
        </div>
        <h2 class="screen-reader-text">统计信息</h2>
        <table class="wp-list-table widefat fixed striped users">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                <th scope="col" id="match_name" class="manage-column column-match_name column-primary">付款人</th>
                <th scope="col" id="scene" class="manage-column column-scene">金额</th>
                <th scope="col" id="match_status" class="manage-column column-match_status">类型</th>
                <th scope="col" id="date_time" class="manage-column column-date_time">时间</th>
            </tr>
            </thead>

            <tbody id="the-list" data-wp-lists="list:user">

            <?php
            foreach ($rows as $row){
                $user_real_name = get_user_meta($row['pay_user_id'],'user_real_name',true);
//                leo_dump($user_real_name);
                ?>
                <tr>
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="cb-select-407">选择<?=isset($user_real_name['real_name']) ? $user_real_name['real_name'] : get_user_by('ID',$row['pay_user_id'])->user_login?></label>
                        <input id="cb-select-<?=$row['id']?>" type="checkbox" name="post[]" value="<?=$row['id']?>">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                            <span class="screen-reader-text">“<?=isset($user_real_name['real_name']) ? $user_real_name['real_name'] : get_user_by('ID',$row['pay_user_id'])->user_login?>”已被锁定</span>
                        </div>
                    </th>
                    <td class="match_name column-match_name has-row-actions column-primary" data-colname="付款人">
                        <?=isset($user_real_name['real_name']) ? $user_real_name['real_name'] : get_user_by('ID',$row['pay_user_id'])->user_login?>
                        <br>

                        <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                    </td>
                    <td class="scene column-scene" data-colname="金额">
                        <?=$row['user_income']?>
                    </td>
                    <td class="match_status column-match_status" data-colname="类型">
                        <?php
                        echo $typeArr[$row['income_type']];
//                        switch ($row['income_type']){
//                            case 'match':
//                                echo '比赛';
//                                break;
//                            case 'grading':
//                                echo '考级';
//                                break;
//                            case 'extract':
//                                echo '提现';
//                                break;
//                            case 'subject':
//                                $zone_type_name = $wpdb->get_var("SELECT zone_type_name FROM {$wpdb->prefix}zone_type WHERE id='{$row['user_type']}'");
//                                echo '申请'.$zone_type_name;
//                                break;
//                            case 'undertake':
//                                echo '承办';
//                                break;
//                        }
                        ?>
                    </td>
                    <td class="date_time column-date_time" data-colname="时间">
                        <?=$row['created_time']?>
                    </td>

                </tr>
                <?php
            }
            ?>
            <tfoot>
            <tr>
                <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                <th scope="col" class="manage-column column-match_name column-primary">付款人</th>
                <th scope="col" class="manage-column column-scene">金额</th>
                <th scope="col" class="manage-column column-match_status">类型</th>
                <th scope="col" class="manage-column column-date_time">时间</th>
            </tr>
            </tfoot>

        </table>
        <div class="tablenav bottom">



            <div class="tablenav-pages">
                <span class="displaying-num"><?=$count['count']?>个项目</span>
                <?=$pageHtml?>
            </div>
            <br class="clear">
        </div>
        <?php
    }

    /**
     * 机构统计信息课程数据
     */
    public function getOrganizeStatisticsCourse($user_id){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS cou.*,um.meta_value AS coach_real_name
                FROM {$wpdb->prefix}course AS cou
                LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=cou.coach_id AND um.meta_key='user_real_name' 
                WHERE cou.zone_id='{$user_id}' AND {$user_id}>0
                LIMIT {$start},{$pageSize}", ARRAY_A);
        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page,
            'add_fragment' => '&s='.$searchStr,
        ));
//        leo_dump($rows);
        ?>
        <div class="tablenav top">
            <div class="tablenav-pages">
                <span class="displaying-num"><?=$count['count']?>个项目</span>
                <?=$pageHtml?>
            </div>
            <br class="clear">
        </div>
        <h2 class="screen-reader-text">统计信息</h2>
        <table class="wp-list-table widefat fixed striped users">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                <th scope="col" id="course_title" class="manage-column column-course_title column-primary">课程名称</th>
                <th scope="col" id="course_img" class="manage-column column-course_img">课程图片</th>
                <th scope="col" id="coach_id" class="manage-column column-coach_id">授课教练</th>
                <th scope="col" id="course_start_time" class="manage-column column-course_start_time">开课时间</th>
                <th scope="col" id="course_end_time" class="manage-column column-course_end_time">结课时间</th>
                <th scope="col" id="address" class="manage-column column-address">授课地址</th>
                <th scope="col" id="open_quota" class="manage-column column-open_quota">开放名额</th>
                <th scope="col" id="seize_quota" class="manage-column column-seize_quota">已抢占名额</th>
                <th scope="col" id="zone_user_id" class="manage-column column-zone_user_id">所属机构</th>
                <th scope="col" id="is_enable" class="manage-column column-is_enable">状态</th>
                <th scope="col" id="created_time" class="manage-column column-created_time">创建时间</th>
            </tr>
            </thead>

            <tbody id="the-list" data-wp-lists="list:user">

            <?php
            foreach ($rows as $row){
                ?>
                <tr data-id="<?=$row['id']?>">
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="cb-select-407">选择<?=$row['course_title']?></label>
                        <input id="cb-select-<?=$row['course_title']?>" type="checkbox" class="th-check" name="post[]" value="<?=$row['id']?>">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                            <span class="screen-reader-text">“<?=$row['course_title']?>”已被锁定</span>
                        </div>
                    </th>
                    <td class="course_title column-course_title has-row-actions column-primary" data-colname="机构名称">
                        <?=$row['course_title']?>
                        <br>
                        <div class="row-actions">
                            <span class="edit"><a href="<?=admin_url('admin.php?page=course-add-course&id='.$row['id'])?>">编辑</a></span>
                            <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                            <!--                               <span class="view"><a href="">资料</a></span>-->
                        </div>
                        <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                    </td>
                    <td class="course_img column-course_img" data-colname="课程图片" id="course_img_<?=$row['id']?>">
                        <img src="<?=$row['course_img']?>" alt="" style="height: 60px;">
                    </td>
                    <td class="coach_id column-coach_id" data-colname="授课教练">
                        <?=!empty($row['coach_real_name']) ? unserialize($row['coach_real_name'])['real_name'] : ''?>
                    </td>
                    <td class="course_start_time column-course_start_time" data-colname="开课时间"><?=$row['course_start_time']?></td>
                    <td class="course_end_time column-course_end_time" data-colname="结课时间"><?=$row['course_end_time'] == '0000-00-00 00:00:00' ? '待定' : $row['course_end_time']?></td>
                    <td class="address column-address" data-colname="授课地址"><?=$row['province'].$row['city'].$row['area'].$row['address']?></td>
                    <td class="open_quota column-open_quota" data-colname="开放名额"><?=$row['open_quota']?></td>
                    <td class="seize_quota column-seize_quota" data-colname="已抢占名额"><?=$row['seize_quota']?></td>
                    <td class="course_type column-course_type" data-colname="课程类型"><?=$row['course_type'] == '1' ? '乐学乐分享' :''?></td>
                    <td class="is_enable column-is_enable" data-colname="状态"><?=$row['is_enable'] == '2' ? '<span style="color: #c42800;">禁用</span>':'正常'?></td>
                    <td class="created_time column-created_time" data-colname="创建时间"><?=$row['created_time']?></td>


                </tr>
                <?php
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                <th scope="col" class="manage-column column-course_title column-primary">课程名称</th>
                <th scope="col" class="manage-column column-course_img">课程图片</th>
                <th scope="col" class="manage-column column-coach_id">授课教练</th>
                <th scope="col" class="manage-column column-course_start_time">开课时间</th>
                <th scope="col" class="manage-column column-course_end_time">结课时间</th>
                <th scope="col" class="manage-column column-address">授课地址</th>
                <th scope="col" class="manage-column column-open_quota">开放名额</th>
                <th scope="col" class="manage-column column-seize_quota">已抢占名额</th>
                <th scope="col" class="manage-column column-zone_user_id">所属机构</th>
                <th scope="col" class="manage-column column-is_enable">状态</th>
                <th scope="col" class="manage-column column-created_time">创建时间</th>
            </tr>
            </tfoot>

        </table>
        <div class="tablenav bottom">



            <div class="tablenav-pages">
                <span class="displaying-num"><?=$count['count']?>个项目</span>
                <?=$pageHtml?>
            </div>
            <br class="clear">
            <script>
                jQuery(document).ready(function($) {
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        var _title = '';
                        <?php foreach ($rows as $row){ ?>
                        layer.photos({//图片预览
                            photos: '#course_img_<?=$row['id']?>',
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
     * 机构统计信息成员数据
     */
    public function getOrganizeStatisticsMember($user_id,$id){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $mtype = isset($_GET['mtype']) ? intval($_GET['mtype']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;

        //管理员数量
        $admin_num = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}zone_manager WHERE zone_id={$id}");
        //教练数量
        $coach_ids = $wpdb->get_var("SELECT GROUP_CONCAT(zjc.coach_id) FROM {$wpdb->prefix}zone_join_coach AS zjc 
                     LEFT JOIN {$wpdb->prefix}coach_skill as cs ON cs.coach_id=zjc.coach_id
                     WHERE zone_id='{$user_id}' AND cs.id!=''");
        $coach_num = $coach_ids ? count(explode(',',$coach_ids)) : 0;
        //学员数量
        $student_num = $wpdb->get_results("SELECT user_id FROM {$wpdb->prefix}my_coach WHERE coach_id IN({$coach_ids}) AND apply_status=2 GROUP BY user_id", ARRAY_A);
        $student_num = count($student_num);
        switch ($mtype){
            case 1:
                $sql = "SELECT SQL_CALC_FOUND_ROWS zm.user_id AS coach_id,b.user_mobile FROM {$wpdb->prefix}zone_manager AS zm 
                LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=zm.user_id AND um.meta_key='user_real_name'
                LEFT JOIN {$wpdb->prefix}users b ON zm.user_id = b.ID     
                WHERE zm.zone_id='{$id}' AND {$user_id} > 0
                LIMIT {$start},{$pageSize}";
                break;
            case 2:
                $sql = "SELECT SQL_CALC_FOUND_ROWS b.user_login,a.id,a.coach_id,a.read,a.memory,a.compute,b.user_mobile 
                    FROM {$wpdb->prefix}coach_skill a 
                    LEFT JOIN {$wpdb->prefix}zone_join_coach AS zjc ON a.coach_id = zjc.coach_id 
                    LEFT JOIN {$wpdb->prefix}users b ON a.coach_id = b.ID         
                    WHERE a.coach_id > 0 AND b.ID !='' AND zjc.zone_id='{$user_id}' AND {$user_id} > 0
                    LIMIT {$start},{$pageSize}";
                break;
            case 3:
                $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT zm.user_id AS coach_id,b.user_mobile 
                FROM {$wpdb->prefix}my_coach AS zm 
                LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=zm.user_id AND um.meta_key='user_real_name'
                LEFT JOIN {$wpdb->prefix}users b ON zm.user_id = b.ID       
                WHERE zm.coach_id IN({$coach_ids}) AND zm.apply_status=2 AND {$user_id} > 0
                LIMIT {$start},{$pageSize}";
                break;
        }
        $rows = $wpdb->get_results($sql, ARRAY_A);
        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page,
            'add_fragment' => '&s='.$searchStr,
        ));
//        leo_dump($rows);

        ?>
        <ul class="subsubsub">
            <li class="all"><a href="<?=admin_url('admin.php?page=fission-organize-statistics&id='.$id.'&type=4&mtype=1')?>" <?=$mtype===1?'class="current"':''?> aria-current="page">管理员<span class="count">（<?=$admin_num > 0 ? $admin_num : 0?>）</span></a> | </li>
            <li class="all"><a href="<?=admin_url('admin.php?page=fission-organize-statistics&id='.$id.'&type=4&mtype=2')?>" <?=$mtype===2?'class="current"':''?> aria-current="page">教练<span class="count">（<?=$coach_num > 0 ? $coach_num : 0?>）</span></a> | </li>
            <li class="all"><a href="<?=admin_url('admin.php?page=fission-organize-statistics&id='.$id.'&type=4&mtype=3')?>" <?=$mtype===3?'class="current"':''?> aria-current="page">学员<span class="count">（<?=$student_num > 0 ? $student_num : 0?>）</span></a> </li>
        </ul>

        <div class="tablenav top">
            <?php if($mtype === 1 && $user_id > 0){ ?>
            <div>
                <button class="button" id="confirmAddAdmin">添加管理员</button>
                <div style="margin-top: 5px;">
                    <select class="js-data-select-ajax" name="admin_id" style="width: 50%" data-action="get_base_user_list" data-type="select">

                    </select>
                </div>
            </div>
            <?php }elseif($mtype === 2 && $user_id > 0){ ?>
                <div>
                    <button class="button" id="confirmAddCoach">添加教练</button>
                    <div style="margin-top: 5px;">
                        <select class="js-data-select-ajax" name="add_coach_id" style="width: 50%" data-action="get_not_zone_coach" data-type="select">

                        </select>
                    </div>
                </div>
            <?php } ?>
            <div class="tablenav-pages">
                <span class="displaying-num"><?=$count['count']?>个项目</span>
                <?=$pageHtml?>
            </div>
            <br class="clear">
        </div>

        <h2 class="screen-reader-text">统计信息</h2>
        <table class="wp-list-table widefat fixed striped users">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                    <input id="cb-select-all-1" type="checkbox">
                </td>
                <th scope="col" id="name" class="manage-column column-name column-primary">姓名</th>
                <th scope="col" id="sex" class="manage-column column-sex">性别</th>
                <th scope="col" id="age" class="manage-column column-age">年龄</th>
                <th scope="col" id="student" class="manage-column column-mobile">手机</th>
                <th scope="col" id="ID" class="manage-column column-ID">ID</th>
               <?php
               if($mtype === 2){
                   echo ' <th scope="col" id="image" class="manage-column column-image">教练照片</th>
                <th scope="col" id="category" class="manage-column column-category">教学类别 </th>
                <th scope="col" id="student_num" class="manage-column column-student_num">学员数量 </th>
                <th scope="col" id="course_num" class="manage-column column-course_num">课程数量 </th>
                <th scope="col" id="options" class="manage-column column-options">操作 </th>';
               }elseif ($mtype === 1){
                   ?>
                   <th scope="col" id="remove_admin" class="manage-column column-remove_admin">删除</th>
                   <?php
               }
               ?>
            </tr>
            </thead>

            <tbody id="the-list" data-wp-lists="list:user">
            <?php
            $teacherClass = new Teacher();
            foreach ($rows as $row){
                //有多少学员
                $studentNumArr = $teacherClass->getStudentNum($row['coach_id']);
                //课程数量
                $courseNum = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}course WHERE coach_id='{$row['coach_id']}'");
                //教练信息
                $usermeta = get_user_meta($row['coach_id']);
                $user_real_name = isset($usermeta['user_real_name'][0]) ? unserialize($usermeta['user_real_name'][0]) : [];
//                        leo_dump($usermeta);
//                        die;
                $user_ID = $usermeta['user_ID'][0];
                //有多少类别

                ?>
                <tr>
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="teacher_<?=$row['coach_id']?>"></label>
                        <input type="checkbox" name="users[]" id="teacher_<?=$row['coach_id']?>" class="subscriber" value="<?=$row['coach_id']?>">
                    </th>

                    <td class="name column-name column-primary" data-colname="姓名">
                        <span aria-hidden="true"><?=isset($user_real_name['real_name']) ? $user_real_name['real_name'] : '-'?></span>
                        <button type="button" class="toggle-row">
                            <span class="screen-reader-text">显示详情</span>
                        </button>
                    </td>
                    <td class="sex column-sex" data-colname="性别">
                        <span aria-hidden="true"><?=isset($usermeta['user_gender']) ? $usermeta['user_gender'][0] : '-'?></span>
                    </td>
                    <td class="age column-age" data-colname="年龄">
                        <span aria-hidden="true"><?=isset($user_real_name['real_age']) ? $user_real_name['real_age'] : '-'?></span>
                    </td>
                    <td class="name column-mobile" data-colname="手机">
                        <span aria-hidden="true"><?=$row['user_mobile']?></span>
                        <span class="screen-reader-text">-</span>
                    </td>
                    <td class="ID column-ID" data-colname="ID">
                        <span aria-hidden="true"><?=$user_ID?></span>
                        <span class="screen-reader-text">未知</span>
                    </td>

                    <?php
                    if($mtype === 2){
                        $categoryArr = [];
                        if($row['read']) $categoryArr[]='速读';
                        if($row['memory']) $categoryArr[]='记忆';
                        if($row['compute']) $categoryArr[]='心算';
                        ?>
                        <td class="image column-image" data-colname="教练照片" id="cardImg-<?=$row['coach_id']?>">
                            <img src="<?=isset($usermeta['user_head'])?$usermeta['user_head'][0]:''?>" style="height: 60px;" alt="">
                        </td>
                        <td class="category column-category" data-colname="教学类别">
                            <?=join('/',$categoryArr)?>
                        </td>
                        <td class="student_num column-student_num" data-colname="学员数量">
                            <a href="<?php echo '?page=teacher-student&id='.$row['coach_id']?><?=$studentNumArr['apply']>0?'&type=1':''?>" aria-label="">
                                <span style="color: #00aff9"><?=$studentNumArr['member']?></span>
                                <?php if($studentNumArr['apply']>0) echo '<span style="color: #c42e00">+'.$studentNumArr['apply'].'</span>'; ?>
                            </a>
                        </td>
                        <td class="course_num column-course_num" data-colname="课程数量">
                            <?=$courseNum > 0 ? $courseNum :0 ?>
                        </td>
                        <td class="options column-options" data-colname="操作">
                            <a href="<?=admin_url('admin.php?page=teacher-datum&id='.$row['coach_id'])?>" class="view_coach">查看</a>
                        </td>
                        <?php
                    }elseif ($mtype === 1){
                        ?>
                        <td class="remove_admin column-remove_admin" data-colname="删除">
                            <a href="javascript:;" data-uid="<?=$row['coach_id']?>" class="remove_admin_a">删除</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
            <?php } ?>

            </tbody>

            <tfoot>

            <tr>
                <td class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                    <input id="cb-select-all-2" type="checkbox">
                </td>
                <th scope="col" class="manage-column column-datum column-primary">姓名 </th>
                <th scope="col" class="manage-column column-sex"> 性别</th>
                <th scope="col" class="manage-column column-age"> 年龄</th>
                <th scope="col" class="manage-column column-mobile">手机</th>
                <th scope="col" class="manage-column column-ID"> ID</th>
                <?php
                if($mtype === 2){
                    echo '      <th scope="col" class="manage-column column-image"> 教练照片</th>
                <th scope="col" class="manage-column column-category">教学类别</th>
                <th scope="col" class="manage-column column-student_num">学员数量 </th>
                <th scope="col" class="manage-column column-course_num">课程数量 </th>
                <th scope="col" class="manage-column column-options">操作 </th>';
                }elseif ($mtype === 1){
                    ?>
                    <th scope="col" class="manage-column column-remove_admin">删除</th>
                    <?php
                }
                ?>

            </tr>
            </tfoot>

        </table>
        <div class="tablenav bottom">



            <div class="tablenav-pages">
                <span class="displaying-num"><?=$count['count']?>个项目</span>
                <?=$pageHtml?>
            </div>
            <br class="clear">
            <script>
                jQuery(document).ready(function($) {
                    <?php if($mtype === 1 && $user_id > 0){ ?>
                    $('#confirmAddAdmin').on('click', function () {
                        var id = $('select[name="admin_id"]').val();
                        var zid = '<?=$id?>';
                        if(id < 1) return false;
                        $.ajax({
                            url : ajaxurl,
                            data : {'action' : 'addZoneAdmin', 'zid' : zid, 'uid' : id},
                            dataType : 'json',
                            type : 'post',
                            success : function (response) {
                                alert(response.data.info);
                                if(response['success']){
                                    window.location.reload();
                                }
                            }, error : function () {
                                alert('请求失败!');
                            }
                        });
                    });
                    $('.remove_admin_a').on('click', function () {
                        var id = $(this).attr('data-uid');
                        var zid = '<?=$id?>';
                        if(id < 1) return false;
                        $.ajax({
                            url : ajaxurl,
                            data : {'action' : 'removeZoneAdmin', 'zid' : zid, 'uid' : id},
                            dataType : 'json',
                            type : 'post',
                            success : function (response) {
                                alert(response.data.info);
                                if(response['success']){
                                    window.location.reload();
                                }
                            }, error : function () {
                                alert('请求失败!');
                            }
                        });
                    });
                    
                    <?php }elseif ($mtype === 2 && $user_id > 0){ ?>
                    $('#confirmAddCoach').on('click', function () {
                        var id = $('select[name="add_coach_id"]').val();
                        var zid = '<?=$user_id?>';
                        if(id < 1) return false;
                        $.ajax({
                            url : ajaxurl,
                            data : {'action' : 'addZoneCoach', 'zid' : zid, 'uid' : id},
                            dataType : 'json',
                            type : 'post',
                            success : function (response) {
                                alert(response.data.info);
                                if(response['success']){
                                    window.location.reload();
                                }
                            }, error : function () {
                                alert('请求失败!');
                            }
                        });
                    });
                    $('.remove_coach_a').on('click', function () {
                        var id = $(this).attr('data-uid');
                        var zid = '<?=$user_id?>';
                        if(id < 1) return false;
                        $.ajax({
                            url : ajaxurl,
                            data : {'action' : 'removeZoneCoach', 'zid' : zid, 'uid' : id},
                            dataType : 'json',
                            type : 'post',
                            success : function (response) {
                                alert(response.data.info);
                                if(response['success']){
                                    window.location.reload();
                                }
                            }, error : function () {
                                alert('请求失败!');
                            }
                        });
                    });
                    <?php } ?>

                });

            </script>
        </div>
        <?php
    }

    /**
     * 获取机构类型列表
     */
    public function getOrganizeTypeList($id=0){
        global $wpdb;
        if($id > 0){
            $typeList = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}zone_type WHERE id='{$id}'", ARRAY_A);
            if($typeList['zone_type_alias'] == 'match') $typeList['zone_type_name'] = '赛区';
        }else{
            $typeList = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}zone_type WHERE zone_type_status=1 ORDER BY id DESC", ARRAY_A);
            foreach ($typeList as &$tlv){
                if($tlv['zone_type_alias'] == 'match') $tlv['zone_type_name'] = '赛区';
            }
        }

        return $typeList;
    }

    /**
     * 获取机构名称
     */
    public function echoZoneName($alias = 'match',$zone_city='',$zone_name='',$zone_match_type=0,$zone_number = '',$type='',$color = '#c40c0f'){
        $span1 = '';
        $span2 = '';
        if($color != false){
            $span1 = '<span style="color: '.$color.'">';
            $span2 = '</span>';
        }
        $city_arr = str2arr($zone_city,'-');
        if(!empty($city_arr[2])){
            $city = $city_arr[2];
        }elseif ($city_arr[1] != '市辖区'){
            $city = $city_arr[1];
        }else{
            $city = $city_arr[0];
        }
        $b = mb_substr($city,-1,1);
        if($b == '市') $city = substr($city,0,strlen($city)-3);

        switch ($alias){
            case 'match':
//                $name = $span1 .$zone_name.$city.$span2.($zone_match_type=='1'?'战队精英赛':'城市赛');
                if($zone_match_type == '1'){
                    $name = date('Y').'脑力世界杯'. $span1 .$zone_name.'（'.$city.'）'.$span2.($zone_match_type=='1'?'战队精英赛':'城市赛');
                }else{
                    $name = date('Y').'脑力世界杯'. $span1 .$zone_name.$city.$span2.($zone_match_type=='1'?'战队精英赛':'城市赛');
                }
//                $name = date('Y').'脑力世界杯'. $span1 .$zone_name.'（'.$city.'）'.$span2.($zone_match_type=='1'?'战队精英赛':'城市赛');
                break;
            case 'trains':
                $name = $span1 .$zone_name.$span2.'训练中心 · '.$city;
//                $name = $span1 .$zone_name.$span2.'训练中心 ('.$zone_number.')';
                break;
            case 'test':
                $name = $span1 .$zone_name.$span2.'测评中心 · '.$city;
//                $name = $span1 .$zone_name.$span2.'测评中心 ('.$zone_number.')';
                break;
            default:
                $name = '';
        }
        if($type == 'get'){
            return $name;
        }else{
            echo $name;
        }
    }

    /**
     * 引入当前页面css/js
     */
    public function register_scripts(){
        switch ($_GET['page']){
            case 'fission-organize-statistics':
                wp_register_script( 'admin_layui_js',match_js_url.'layui/layui.js',array('jquery'), leo_match_version  );
                wp_enqueue_script( 'admin_layui_js' );
                wp_register_script( 'student-languages',student_js_url.'validator/verify-ZH-CN.js',array('jquery'), leo_student_version  );
                wp_enqueue_script( 'student-languages' );
                wp_localize_script('student-languages','verify_ZH',[
                ]);
                break;
            case 'fission':
                wp_register_script( 'admin_layui_js',match_js_url.'layui/layui.js',array('jquery'), leo_match_version  );
                wp_enqueue_script( 'admin_layui_js' );
                break;
            case 'fission-add-organize':
                wp_register_script( 'admin_layui_js',match_js_url.'layui/layui.js',array('jquery'), leo_match_version  );
                wp_enqueue_script( 'admin_layui_js' );
                wp_register_script( 'student-languages',student_js_url.'validator/verify-ZH-CN.js',array('jquery'), leo_student_version  );
                wp_enqueue_script( 'student-languages' );
                wp_localize_script('student-languages','verify_ZH',[
                ]);
                break;
        }
    }
}
new Organize();