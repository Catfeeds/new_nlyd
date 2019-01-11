<?php
//组织机构控制器
class Spread{
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
            $role = 'profit_set';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'add_profit_set';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'profit_log';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'profit_user_log';//权限名
            $wp_roles->add_cap('administrator', $role);

//            $role = 'profit_extract_log';//权限名
//            $wp_roles->add_cap('administrator', $role);

            $role = 'profit_match_log';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'profit_match_log_detail';//权限名
            $wp_roles->add_cap('administrator', $role);
        }
        add_submenu_page('fission','收益设置','收益设置','profit_set','fission-profit-set',array($this,'profitSet'));
        add_submenu_page('fission','新增收益设置','新增收益设置','add_profit_set','fission-add-profit-set',array($this,'addProfitSet'));
        add_submenu_page('fission','用户分成记录','用户分成记录','profit_log','fission-profit-log',array($this,'profitLog'));
        add_submenu_page('fission','赛事分成记录','赛事分成记录','profit_match_log','fission-profit-match-log',array($this,'profitMatchLog'));
        add_submenu_page('fission','用户收益流水','用户收益流水','profit_user_log','fission-profit-user-log',array($this,'profitUserLog'));
        add_submenu_page('fission','提现记录','提现记录','profit_extract_log','fission-profit-extract-log',array($this,'profitExtractLog'));
        add_submenu_page('fission','赛事分成记录详情','赛事分成记录详情','profit_match_log_detail','fission-profit-match-log-detail',array($this,'profitMatchLogDetail'));
    }

    /**
     * 收益设置
     */
    public function profitSet(){
        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}spread_set", ARRAY_A);

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">收益设置列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-profit-set')?>" class="page-title-action">添加收益设置</a>

            <hr class="wp-header-end">

            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <br class="clear">
            </div>
            <h2 class="screen-reader-text">机构列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th scope="col" id="spread_type" class="manage-column column-spread_type column-primary">分成类别</th>
                    <th scope="col" id="match_type" class="manage-column column-match_type">赛事类型</th>
                    <th scope="col" id="pay_amount" class="manage-column column-pay_amount">支付金额</th>
                    <th scope="col" id="direct_superior" class="manage-column column-direct_superior">直接上级</th>
                    <th scope="col" id="indirect_superior" class="manage-column column-indirect_superior">间接上级</th>
                    <th scope="col" id="first_cause" class="manage-column column-first_cause">一级事业管理员</th>
                    <th scope="col" id="second_cause" class="manage-column column-second_cause">二级事业管理员</th>
                    <th scope="col" id="coach" class="manage-column column-coach">教练</th>
                    <th scope="col" id="sub_center" class="manage-column column-sub_center">赛区/分中心/考级中心</th>
                    <th scope="col" id="mechanism" class="manage-column column-mechanism">参赛机构</th>
                    <th scope="col" id="spread_status" class="manage-column column-spread_status">状态</th>
                    <th scope="col" id="option1" class="manage-column column-option1">操作</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){
                    ?>
                        <tr data-id="<?=$row['id']?>">
                            <th scope="row" class="check-column">
                                <label class="screen-reader-text" for="cb-select-407">选择<?=$row['spread_name']?></label>
                                <input id="cb-select-<?=$row['id']?>" type="checkbox" name="post[]" value="<?=$row['id']?>">
                                <div class="locked-indicator">
                                    <span class="locked-indicator-icon" aria-hidden="true"></span>
                                    <span class="screen-reader-text">“<?=$row['spread_name']?>”已被锁定</span>
                                </div>
                            </th>
                            <td class="name column-name has-row-actions column-primary" data-colname="名称">
                                <?=$row['spread_name']?>
                                <br>
                                <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                            </td>

                            <td class="match_type column-match_type" data-colname="赛事类型">
                                <?php
                                    switch ($row['match_type']){
                                        case '1':
                                            echo '战队赛';
                                            break;
                                        case '2':
                                            echo '多区县';
                                            break;
                                        case '3':
                                            echo '单区县';
                                            break;
                                    }
                                ?>
                            </td>
                            <td class="direct_superior column-direct_superior" data-colname="直接上级">
                                <?=$row['pay_amount']?>
                            </td>
                            <td class="pay_amount column-pay_amount" data-colname="支付金额">
                                <?=$row['direct_superior']?>
                            </td>
                            <td class="indirect_superior column-indirect_superior" data-colname="间接上级">
                                <?=$row['indirect_superior']?>
                            </td>
                            <td class="first_cause column-first_cause" data-colname="一级事业管理员">
                                <?=$row['first_cause']?>
                            </td>
                            <td class="second_cause column-second_cause" data-colname="二级事业管理员">
                                <?=$row['second_cause']?>
                            </td>
                            <td class="coach column-coach" data-colname="教练">
                                <?=$row['coach']?>
                            </td>
                            <td class="sub_center column-sub_center" data-colname="赛区/分中心/考级中心">
                                <?=$row['sub_center']?>
                            </td>
                            <td class="mechanism column-mechanism" data-colname="参赛机构">
                                <?=$row['mechanism']?>
                            </td>
                            <td class="spread_status column-spread_status" data-colname="状态">
                                <?php
                                    switch ($row['spread_status']){
                                        case '1':
                                            echo '<span style="color: #00c445">正常</span>';
                                            break;
                                        case '2':
                                            echo '<span style="color: #c41c00">禁用</span>';
                                            break;
                                    }
                                ?>
                            </td>
                            <td class="option1 column-option1 has-row-actions" data-colname="操作">
                                <a href="<?=admin_url('admin.php?page=fission-add-profit-set&id='.$row['id'])?>">编辑</a>
                                |
                                <a href="javascript:;" class="remove-set">删除</a>
                            </td>

                        </tr>
                    </tbody>
                    <?php
                }
                ?>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-spread_type column-primary">分成类别</th>
                    <th scope="col" class="manage-column column-match_type">赛事类型</th>
                    <th scope="col" class="manage-column column-pay_amount">支付金额</th>
                    <th scope="col" class="manage-column column-direct_superior">直接上级</th>
                    <th scope="col" class="manage-column column-indirect_superior">间接上级</th>
                    <th scope="col" class="manage-column column-first_cause">一级事业管理员</th>
                    <th scope="col" class="manage-column column-second_cause">二级事业管理员</th>
                    <th scope="col" class="manage-column column-coach">教练</th>
                    <th scope="col" class="manage-column column-sub_center">赛区/分中心/考级中心</th>
                    <th scope="col" class="manage-column column-mechanism">参赛机构</th>
                    <th scope="col" class="manage-column column-spread_status">状态</th>
                    <th scope="col" class="manage-column column-option1">操作</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">


                <br class="clear">
            </div>

            <br class="clear">
            <script>
                jQuery(document).ready(function($) {
                    //删除
                    $('.remove-set').on('click',function () {
                        var _id = $(this).closest('tr').attr('data-id');
                        if(confirm('确认要删除此项设置吗?删除后无法恢复!')){
                            $.ajax({
                                url : ajaxurl,
                                data : {'action':'delSpreadSet','id':_id},
                                type:'post',
                                dataType : 'json',
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
                    });
                });
            </script>
        </div>
        <?php
    }


    /**
     * 新增收益设置
     */
    public function addProfitSet(){
        global $wpdb;
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $error_msg = '';
        $success_msg = '';
        if(is_post()){
            $spread_type = isset($_POST['spread_type']) ? trim($_POST['spread_type']) : '';
            $direct_superior = isset($_POST['direct_superior']) ? trim($_POST['direct_superior']) : '';
            $indirect_superior = isset($_POST['indirect_superior']) ? trim($_POST['indirect_superior']) : '';
            $first_cause = isset($_POST['first_cause']) ? trim($_POST['first_cause']) : '';
            $second_cause = isset($_POST['second_cause']) ? trim($_POST['second_cause']) : '';
            $coach = isset($_POST['coach']) ? trim($_POST['coach']) : '';
            $sub_center = isset($_POST['sub_center']) ? trim($_POST['sub_center']) : '';
            $mechanism = isset($_POST['mechanism']) ? trim($_POST['mechanism']) : '';
            $spread_status = isset($_POST['spread_status']) ? intval($_POST['spread_status']) : '';
            $pay_amount = isset($_POST['pay_amount']) ? floatval($_POST['pay_amount']) : '';
            $match_grading = 0;
            $match_type = 0;
            $spread_arr = explode('()',$spread_type);
            $spread_type = $spread_arr[0];
            $spread_name = $spread_arr[1];
            if($spread_status !== 1 && $spread_status !== 2) $error_msg = '请选择状态!';
            if($spread_type == 'match_grading_run'){
                //赛事考级收益
                $spread_type2 = isset($_POST['spread_type2']) ? trim($_POST['spread_type2']) : '';
                $match_grading = isset($_POST['match_grading']) ? intval($_POST['match_grading']) : 0;
                $match_type = isset($_POST['match_type']) ? intval($_POST['match_type']) : 0;
                if($match_grading < 1) $error_msg .= '<br />请选择比赛或考级';
                if($match_type < 1) $error_msg .= '<br />请选择赛事类型';
                $spread_arr = explode('()',$spread_type2);
                $spread_type = $spread_arr[0];
                $spread_name = $spread_arr[1].($match_grading === 1 ? '比赛' : '考级');
            }

            if($error_msg == ''){
                $insertData = [
                    'pay_amount' => $pay_amount,
                    'spread_name' => $spread_name,
                    'spread_type' => $spread_type,
                    'direct_superior' => $direct_superior,
                    'indirect_superior' => $indirect_superior,
                    'first_cause' => $first_cause,
                    'second_cause' => $second_cause,
                    'coach' => $coach,
                    'sub_center' => $sub_center,
                    'mechanism' => $mechanism,
                    'spread_status' => $spread_status,
                    'match_type' => $match_type,
                    'match_grading' => $match_grading,
                ];
                if($id > 0){
                    $bool = $wpdb->update($wpdb->prefix.'spread_set',$insertData,['id' => $id]);
                }else{
                    //判断是否已经存在此类设置

                    $bool = $wpdb->insert($wpdb->prefix.'spread_set',$insertData);
//                    leo_dump($wpdb->last_query);die;
                }
                if($bool){
                    $success_msg = '操作成功!';
                }else{
                    $error_msg = '操作失败!';
                }
            }
        }
        if($id > 0){
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}spread_set WHERE id='{$id}'", ARRAY_A);
        }
        //获取机构类型
        $organizeClass = new Organize();
        $organizeList = $organizeClass->getOrganizeTypeList();
        //获取比赛权限类型

        $spreadCategory = getSpreadCategory();
        foreach ($organizeList as $olv){
            $spreadCategory[$olv['zone_type_alias']] = '成为'.$olv['zone_type_name'];
        }
        //默认比赛权限
//        $zoneMatchRoleList = $wpdb->get_results("SELECT role_name,role_alias FROM {$wpdb->prefix}zone_join_match_role AS zjmr
//                                 LEFT JOIN {$wpdb->prefix}zone_match_role AS zmr ON zmr.id=zjmr.match_role_id
//                                 WHERE zjmr.zone_type_id='{$olv['id']}' AND zmr.is_profit=1", ARRAY_A);
//        foreach ($zoneMatchRoleList as $zmrlv){
//            $spreadCategory[$olv['zone_type_alias'].'_'.$zmrlv['role_alias']] = $olv['zone_type_name'].$zmrlv['role_name'];
//        }
        $spreadCategory['match_grading_run'] = '机构比赛考级';

        //=..去除已有类型
        $oldList = $wpdb->get_results("SELECT spread_type FROM {$wpdb->prefix}spread_set", ARRAY_A);
        foreach ($oldList as $oldv){
            if(isset($spreadCategory[$oldv['spread_type']]) && $row['spread_type'] != $oldv['spread_type']) unset($spreadCategory[$oldv['spread_type']]);
        }
//        leo_dump($spreadCategory);die;
        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加/编辑分成项</h1>
            <style type="text/css">
                .match_spread_tr{
                    <?php if($row['match_grading'] < 1){
                        echo 'display: none;';
                    }?>

                }
            </style>
            <div id="ajax-response">
                <span style="color: #2bc422"><?=$success_msg?></span>
                <span style="color: #c44e00"><?=$error_msg?></span>
            </div>

            <form method="post" action="" id="adduser" class="validate" novalidate="novalidate">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="5f6ea9ff44"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php"><table class="form-table">
                    <tbody>
                    <tr class="">
                        <th scope="row"><label for="spread_type">支付类别 </label></th>
                        <td>
                            <select name="spread_type" id="spread_type">
                                <?php foreach ($spreadCategory as $sck => $scv){ ?>
                                    <option <?=isset($row) && (($row['spread_type'] == $sck && $row['match_grading'] < 1) || ($sck == 'match_grading_run' && $row['match_grading'] > 0))?'selected="selected"':''?> value="<?=$sck.'()'.$scv?>"><?=$scv?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="match_spread_tr">
                        <th scope="row"><label for="spread_type2">机构类型 </label></th>
                        <td>
                            <select name="spread_type2" id="spread_type2">
                                <?php foreach ($organizeList as $olv2){ ?>
                                    <option <?=isset($row) && $row['spread_type'] == $olv2['zone_type_alias']?'selected="selected"':''?> value="<?=$olv2['zone_type_alias'].'()'.$olv2['zone_type_name']?>"><?=$olv2['zone_type_name']?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="match_spread_tr">
                        <th scope="row"><label for="spread_type2">考级比赛 </label></th>
                        <td>
                            <label for="match_grading_1"><input type="radio" <?=isset($row['match_grading']) && $row['match_grading'] == '1' ? 'checked="checked"' : ''?> name="match_grading" value="1" id="match_grading_1">比赛</label>
                            <label for="match_grading_2"><input type="radio" <?=isset($row['match_grading']) && $row['match_grading'] == '2' ? 'checked="checked"' : ''?> name="match_grading" value="2" id="match_grading_2">考级</label>
                        </td>
                    </tr>
                    <tr class="match_spread_tr">
                        <th scope="row"><label for="spread_type2">赛事类型 </label></th>
                        <td>
                            <label for="match_type_1"><input type="radio" <?=isset($row['match_type']) && $row['match_type'] == '1' ? 'checked="checked"' : ''?> name="match_type" value="1" id="match_type_1">战队赛</label>
                            <label for="match_type_2"><input type="radio" <?=isset($row['match_type']) && $row['match_type'] == '2' ? 'checked="checked"' : ''?> name="match_type" value="2" id="match_type_2">多区县城市赛</label>
                            <label for="match_type_3"><input type="radio" <?=isset($row['match_type']) && $row['match_type'] == '3' ? 'checked="checked"' : ''?> name="match_type" value="3" id="match_type_3">单区县城市赛</label>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="pay_amount">支付金额 </label></th>
                        <td>
                            <input type="text" id="pay_amount" name="pay_amount" value="<?=isset($row)?$row['pay_amount']:'0.00'?>">
                        </td>
                    </tr>

                    <tr class="form-field form-required">
                        <th scope="row"><label for="direct_superior">直接上级</label></th>
                        <td>
                            <input type="text" name="direct_superior" value="<?=isset($row)?$row['direct_superior']:''?>" id="direct_superior" maxlength="60"><span>元/百分比</span>
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="indirect_superior">间接上级</label></th>
                        <td>
                            <input type="text" name="indirect_superior" value="<?=isset($row)?$row['indirect_superior']:''?>" id="indirect_superior" maxlength="60"><span>元/百分比</span>
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="first_cause">一级事业管理员</label></th>
                        <td>
                            <input type="text" name="first_cause" value="<?=isset($row)?$row['first_cause']:''?>" id="first_cause" maxlength="60"><span>元/百分比</span>
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="second_cause">二级事业管理员</label></th>
                        <td>
                            <input type="text" name="second_cause" value="<?=isset($row)?$row['second_cause']:''?>" id="second_cause" maxlength="60"><span>元/百分比</span>
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="coach">教练</label></th>
                        <td>
                            <input type="text" name="coach" value="<?=isset($row)?$row['coach']:''?>" id="coach" maxlength="60"><span>元/百分比</span>
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="mechanism">参赛机构</label></th>
                        <td>
                            <input type="text" name="mechanism" value="<?=isset($row)?$row['mechanism']:''?>" id="mechanism" maxlength="60"><span>元/百分比</span>
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="sub_center">赛区/分中心/考级中心</label></th>
                        <td>
                            <input type="text" name="sub_center" value="<?=isset($row)?$row['sub_center']:''?>" id="sub_center" maxlength="60"><span>元/百分比</span>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="spread_status">状态</label></th>
                        <td>
                            <label for="spread_status_1"><input type="radio" <?=!isset($row) || $row['spread_status'] == '1' ? 'checked="checked"': ''?> name="spread_status" id="spread_status_1" value="1">正常</label>
                            <label for="spread_status_2"><input type="radio" <?=isset($row) && $row['spread_status'] == '2' ? 'checked="checked"': ''?> name="spread_status" id="spread_status_2" value="2">禁用</label>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <p class="submit"><input type="submit" class="button button-primary" value="提交"></p>
            </form>
            <script>
                jQuery(document).ready(function($) {
                    $('#spread_type').on('change', function () {
                        var val = $(this).val();
                        if(!val) return false;
                        val = val.split('()')[0];
                        if(val == 'match_grading_run'){
                            $('.match_spread_tr').show();
                        }else{
                            $('.match_spread_tr').hide();
                        }
                    });
                });
            </script>
        </div>
        <?php
    }

    /**
     * 用户分成记录
     */
    public function profitLog(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $where = "WHERE 1=1";
        if($searchStr != ''){
            $where .= " AND (p.post_title LIKE '%{$searchStr}%' OR um.meta_value LIKE '%{$searchStr}%')";
        }
        if($user_id > 0){
            $where .= " AND il.user_id='{$user_id}'";
        }
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS 
                il.income_type,il.match_id,il.referee_income,il.indirect_referee_income,il.person_liable_income,il.sponsor_income,il.manager_income,
                il.user_id,il.referee_id,il.indirect_referee_id,il.person_liable_id,il.sponsor_id,il.manager_id,il.income_status,il.id,
                um.meta_value AS user_real_name,  
                um2.meta_value AS referee_real_name,  
                um3.meta_value AS indirect_referee_real_name,  
                um4.meta_value AS person_liable_real_name,  
                zm.zone_name,  
                um6.meta_value AS manager_real_name 
                FROM {$wpdb->prefix}user_income_logs AS il 
                LEFT JOIN `{$wpdb->usermeta}` AS um ON um.user_id=il.user_id AND um.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->usermeta}` AS um2 ON um2.user_id=il.referee_id AND um2.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->usermeta}` AS um3 ON um3.user_id=il.indirect_referee_id AND um3.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->usermeta}` AS um4 ON um4.user_id=il.person_liable_id AND um4.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->prefix}zone_meta` AS zm ON zm.user_id=il.sponsor_id  
                LEFT JOIN `{$wpdb->usermeta}` AS um6 ON um6.user_id=il.manager_id AND um6.meta_key='user_real_name' 
                {$where} AND il.income_type NOT IN('match','grading')
                LIMIT {$start},{$pageSize}",ARRAY_A);
//        leo_dump($rows);
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
            <h1 class="wp-heading-inline">用户分成列表</h1>


            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤用户分成列表</h2>

            <?php if($user_id < 1){ ?>
                <p class="search-box">
                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                    <input type="search" id="search_val" name="search_val" placeholder="付款人/项目" value="<?=$searchStr?>">
                    <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=fission-profit-log&user_id='.$user_id.'&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
                </p>
            <?php } ?>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
                        <option value="2">改为已确认</option>
                    </select>
                    <input type="button" id="doaction" class="button action all_options" value="应用">
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
                    <th scope="col" id="real_name" class="manage-column column-real_name column-primary">付款人</th>
                    <th scope="col" id="project" class="manage-column column-project">类型</th>
                    <th scope="col" id="referee" class="manage-column column-referee">直接推广</th>
                    <th scope="col" id="indirect_referee" class="manage-column column-indirect_referee">间接推广</th>
                    <th scope="col" id="person_liable" class="manage-column column-person_liable">负责人</th>
                    <th scope="col" id="sponsor" class="manage-column column-sponsor">主办方</th>
                    <th scope="col" id="manager" class="manage-column column-manager">事业员</th>
                    <th scope="col" id="income_status" class="manage-column column-income_status">状态</th>
                    <th scope="col" id="options1" class="manage-column column-options1">操作</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){
                    if(empty($row['user_real_name'])){
                        $real_name = get_user_by('ID',$row['user_id'])->user_login;
                    }else{
                        $real_name = unserialize($row['user_real_name'])['real_name'];
                    }
                    ?>
                    <tr data-id="<?=$row['id']?>">
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="cb-select-407">选择<?=$real_name?></label>
                            <input id="cb-select-<?=$row['id']?>" class="check_list" type="checkbox" name="post[]" value="<?=$row['id']?>">
                            <div class="locked-indicator">
                                <span class="locked-indicator-icon" aria-hidden="true"></span>
                                <span class="screen-reader-text">“<?=$real_name?>”已被锁定</span>
                            </div>
                        </th>
                        <td class="real_name column-real_name has-row-actions column-primary" data-colname="付款人">
                            <?=$real_name?>
                            <br>
                            <div class="row-actions">
<!--                                <span class="edit"><a href="">编辑</a></span>-->
                                <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                                <!--                               <span class="view"><a href="">资料</a></span>-->
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="project column-project" data-colname="类型">
                            <?php
                            switch ($row['income_type']){
                                case 'subject':
                                    $zone_type_name = $wpdb->get_var("SELECT zone_type_name FROM {$wpdb->prefix}zone_type WHERE id='{$row['match_id']}'");
                                    echo '申请'.$zone_type_name;
                                    break;
                                case 'match':
                                    echo '比赛';
                                    break;
                                case 'grading':
                                    echo '考级';
                                    break;
                                case 'undertake':
                                    echo '承办';
                                    break;
                                case 'extract':
                                    echo '提现';
                                    break;
                            }
                            ?>

                        </td>
                        <td class="referee column-referee" data-colname="直接推广">
                            <?=!empty($row['referee_real_name'])?unserialize($row['referee_real_name'])['real_name']:get_user_by('ID',$row['referee_id'])->user_login?>
                            (<?=$row['referee_income']?>)
                        </td>
                        <td class="indirect_referee column-indirect_referee" data-colname="间接推广">
                            <?=!empty($row['indirect_referee_name'])?unserialize($row['indirect_referee_name'])['real_name']:get_user_by('ID',$row['indirect_referee_id'])->user_login?>
                            <?=$row['indirect_referee_income']>0?'('.$row['indirect_referee_income'].')':''?>
                        </td>
                        <td class="person_liable column-person_liable" data-colname="负责人">
                            <?=!empty($row['person_liable_name'])?unserialize($row['person_liable_name'])['real_name']:get_user_by('ID',$row['person_liable_id'])->user_login?>
                            <?=$row['person_liable_income']>0?'('.$row['person_liable_income'].')':''?>
                        </td>
                        <td class="sponsor column-sponsor" data-colname="主办方">
                            <?=$row['zone_name']?>
                            <?=$row['sponsor_income']>0?'('.$row['sponsor_income'].')':''?>
                        </td>
                        <td class="manager column-manager" data-colname="事业员">
                            <?=!empty($row['manager_name'])?unserialize($row['manager_name'])['real_name']:get_user_by('ID',$row['manager_id'])->user_login?>
                            <?=$row['manager_income']>0?'('.$row['manager_income'].')':''?>
                        </td>
                        <td class="income_status column-income_status" data-colname="状态" id="cardImg-<?=$row['user_id']?>">
                            <?=$row['income_status'] == '1'?'待确认':'已确认'?>
                        </td>
                        <td class="options1 column-options1" data-colname="操作">
                            <?=$row['income_status'] == '1'?'<a href="javascript:;" class="update_status" data-status="2">改为已确认</a>':''?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-real_name column-primary">付款人</th>
                    <th scope="col" class="manage-column column-project">类型</th>
                    <th scope="col" class="manage-column column-referee">直接推广</th>
                    <th scope="col" class="manage-column column-indirect_referee">间接推广</th>
                    <th scope="col" class="manage-column column-person_liable">负责人</th>
                    <th scope="col" class="manage-column column-sponsor">主办方</th>
                    <th scope="col" class="manage-column column-manager">事业员</th>
                    <th scope="col" class="manage-column column-income_status">状态</th>
                    <th scope="col" class="manage-column column-options1">操作</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
                        <option value="2">改为已确认</option>
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
                    //修改确认状态
                    $('.update_status').on('click',function () {
                        var status = $(this).attr('data-status');
                        var _id = $(this).closest('tr').attr('data-id');
                        postAjax(status,_id);
                    });

                    $('.all_options').on('click', function () {
                        var status = $(this).prev().val();
                        var _id = [];
                        $.each($('#the-list').find('.check_list:checked'),function (i,v) {
                            _id.push($(v).val());
                        });
                        _id = _id.join(',');
                        postAjax(status,_id);
                    });
                    function postAjax(status,_id) {
                        if(status != '1' && status != '2') return false;
                        if(_id == '' || _id == undefined) return false;
                        $.ajax({
                            url : ajaxurl,
                            data : {'action':'updateIncomeLogsStatus', 'status':status,'id':_id},
                            dataType : 'json',
                            type : 'post',
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
     * 赛事/考级分成记录
     */
    public function profitMatchLog(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';

        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $where = "WHERE 1=1";
        if($searchStr != ''){
            $where .= " AND (p.post_title LIKE '%{$searchStr}%')";
        }
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS 
                SUM(il.referee_income) AS referee_income,SUM(il.indirect_referee_income) AS indirect_referee_income,
                SUM(il.person_liable_income) AS person_liable_income,SUM(il.sponsor_income) AS sponsor_income,SUM(il.manager_income) AS manager_income,
                il.income_status,p.post_title,il.match_id,gm.grading_id,mmn.match_status,gm.status 
                FROM {$wpdb->prefix}user_income_logs AS il 
                LEFT JOIN `{$wpdb->posts}` AS p ON p.ID=il.match_id 
                LEFT JOIN `{$wpdb->prefix}match_meta_new` AS mmn ON il.match_id=mmn.match_id 
                LEFT JOIN `{$wpdb->prefix}grading_meta` AS gm ON il.match_id=gm.grading_id 
                {$where} AND il.income_type IN('match','grading') AND (mmn.id!='' OR gm.id!='')
                GROUP BY p.ID
                LIMIT {$start},{$pageSize}",ARRAY_A);
//        leo_dump($rows);
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
            <h1 class="wp-heading-inline">赛事/考级分成列表</h1>
            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤列表</h2>
            <p class="search-box">
                <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                <input type="search" id="search_val" name="search_val" placeholder="赛事/考级名称" value="<?=$searchStr?>">
                <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=fission-profit-match-log&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
            </p>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
                        <option value="2">改为已确认</option>
                    </select>
                    <input type="button" id="doaction" class="button action all_options" value="应用">
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
                    <th scope="col" id="real_name" class="manage-column column-real_name column-primary">赛事考级名称</th>
                    <th scope="col" id="project" class="manage-column column-project">付款项目</th>
                    <th scope="col" id="referee" class="manage-column column-referee">直接推广</th>
                    <th scope="col" id="indirect_referee" class="manage-column column-indirect_referee">间接推广</th>
                    <th scope="col" id="person_liable" class="manage-column column-person_liable">负责人</th>
                    <th scope="col" id="sponsor" class="manage-column column-sponsor">主办方</th>
                    <th scope="col" id="manager" class="manage-column column-manager">事业员</th>
                    <th scope="col" id="income_status" class="manage-column column-income_status">状态</th>
                    <th scope="col" id="options1" class="manage-column column-options1">操作</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){
                    ?>
                    <tr data-id="<?=$row['match_id']?>">
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="cb-select-407">选择<?=$row['post_title']?></label>
                            <input id="cb-select-<?=$row['match_id']?>" class="check_list" type="checkbox" name="post[]" value="<?=$row['match_id']?>">
                            <div class="locked-indicator">
                                <span class="locked-indicator-icon" aria-hidden="true"></span>
                                <span class="screen-reader-text">“<?=$row['post_title']?>”已被锁定</span>
                            </div>
                        </th>
                        <td class="real_name column-real_name has-row-actions column-primary" data-colname="付款人">
                            <?=$row['post_title']?>
                            <br>
                            <div class="row-actions">
                                <!--                                <span class="edit"><a href="">编辑</a></span>-->
                                <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                                <!--                               <span class="view"><a href="">资料</a></span>-->
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="project column-project" data-colname="直接推广">
                            <?=$row['post_title']?>

                        </td>
                        <td class="referee column-referee" data-colname="直接推广">
                            <?=!empty($row['referee_real_name'])?unserialize($row['referee_real_name'])['real_name']:get_user_by('ID',$row['referee_id'])->user_login?>
                            (<?=$row['referee_income']?>)
                        </td>
                        <td class="indirect_referee column-indirect_referee" data-colname="间接推广">
                            <?=!empty($row['indirect_referee_name'])?unserialize($row['indirect_referee_name'])['real_name']:get_user_by('ID',$row['indirect_referee_id'])->user_login?>
                            <?=$row['indirect_referee_income']>0?'('.$row['indirect_referee_income'].')':''?>
                        </td>
                        <td class="person_liable column-person_liable" data-colname="负责人">
                            <?=!empty($row['person_liable_name'])?unserialize($row['person_liable_name'])['real_name']:get_user_by('ID',$row['person_liable_id'])->user_login?>
                            <?=$row['person_liable_income']>0?'('.$row['person_liable_income'].')':''?>
                        </td>
                        <td class="sponsor column-sponsor" data-colname="主办方">
                            <?=$row['zone_name']?>
                            <?=$row['sponsor_income']>0?'('.$row['sponsor_income'].')':''?>
                        </td>
                        <td class="manager column-manager" data-colname="事业员">
                            <?=!empty($row['manager_name'])?unserialize($row['manager_name'])['real_name']:get_user_by('ID',$row['manager_id'])->user_login?>
                            <?=$row['manager_income']>0?'('.$row['manager_income'].')':''?>
                        </td>
                        <td class="income_status column-income_status" data-colname="状态" id="cardImg-<?=$row['user_id']?>">
                            <?=$row['income_status'] == '1'?'待确认':'已确认'?>
                        </td>
                        <td class="options1 column-options1" data-colname="操作">
                            <?php if($row['match_status'] == '-3' || $row['status'] == '-3'){ ?>
                            <?=$row['income_status'] == '1'?'<a href="javascript:;" class="update_status" data-status="2">改为已确认</a> |':''?>
                            <?php } ?>
                            <a href="<?=admin_url('admin.php?page=fission-profit-match-log-detail&match_id='.$row['match_id'])?>">查看详情</a>
                       </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-real_name column-primary">赛事考级名称</th>
                    <th scope="col" class="manage-column column-project">付款项目</th>
                    <th scope="col" class="manage-column column-referee">直接推广</th>
                    <th scope="col" class="manage-column column-indirect_referee">间接推广</th>
                    <th scope="col" class="manage-column column-person_liable">负责人</th>
                    <th scope="col" class="manage-column column-sponsor">主办方</th>
                    <th scope="col" class="manage-column column-manager">事业员</th>
                    <th scope="col" class="manage-column column-income_status">状态</th>
                    <th scope="col" class="manage-column column-options1">操作</th>
                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">批量操作</option>
                        <option value="2">改为已确认</option>
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
                    //修改确认状态
                    $('.update_status').on('click',function () {
                        var status = $(this).attr('data-status');
                        var _id = $(this).closest('tr').attr('data-id');
                        postAjax(status,_id);
                    });

                    $('.all_options').on('click', function () {
                        var status = $(this).prev().val();
                        var _id = [];
                        $.each($('#the-list').find('.check_list:checked'),function (i,v) {
                            _id.push($(v).val());
                        });
                        _id = _id.join(',');
                        postAjax(status,_id);
                    });
                    function postAjax(status,_id) {
                        if(status != '2') return false;
                        if(_id == '' || _id == undefined) return false;
                        $.ajax({
                            url : ajaxurl,
                            data : {'action':'updateMatchIncomeLogsStatus', 'status':status,'id':_id},
                            dataType : 'json',
                            type : 'post',
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
     * 赛事分成详细流水
     */
    public function profitMatchLogDetail(){
        $match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;
        $match_id < 1 && exit('参数错误');

        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;


        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS 
                il.income_type,il.match_id,il.referee_income,il.indirect_referee_income,il.person_liable_income,il.sponsor_income,il.manager_income,
                il.user_id,il.referee_id,il.indirect_referee_id,il.person_liable_id,il.sponsor_id,il.manager_id,il.income_status,il.id,
                um.meta_value AS user_real_name,  
                um2.meta_value AS referee_real_name,  
                um3.meta_value AS indirect_referee_real_name,  
                um4.meta_value AS person_liable_real_name,  
                zm.zone_name,  
                um6.meta_value AS manager_real_name 
                FROM {$wpdb->prefix}user_income_logs AS il 
                LEFT JOIN `{$wpdb->usermeta}` AS um ON um.user_id=il.user_id AND um.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->usermeta}` AS um2 ON um2.user_id=il.referee_id AND um2.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->usermeta}` AS um3 ON um3.user_id=il.indirect_referee_id AND um3.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->usermeta}` AS um4 ON um4.user_id=il.person_liable_id AND um4.meta_key='user_real_name' 
                LEFT JOIN `{$wpdb->prefix}zone_meta` AS zm ON zm.user_id=il.sponsor_id  
                LEFT JOIN `{$wpdb->usermeta}` AS um6 ON um6.user_id=il.manager_id AND um6.meta_key='user_real_name' 
                WHERE il.match_id='{$match_id}' AND il.income_type IN('match','grading')
                LIMIT {$start},{$pageSize}",ARRAY_A);
//        leo_dump($rows);
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
            <h1 class="wp-heading-inline"><?=get_post($match_id)->post_title.'-分成详情'?></h1>


            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤用户分成列表</h2>


            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">



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
                    <th scope="col" id="real_name" class="manage-column column-real_name column-primary">付款人</th>
                    <th scope="col" id="project" class="manage-column column-project">类型</th>
                    <th scope="col" id="referee" class="manage-column column-referee">直接推广</th>
                    <th scope="col" id="indirect_referee" class="manage-column column-indirect_referee">间接推广</th>
                    <th scope="col" id="person_liable" class="manage-column column-person_liable">负责人</th>
                    <th scope="col" id="sponsor" class="manage-column column-sponsor">主办方</th>
                    <th scope="col" id="manager" class="manage-column column-manager">事业员</th>
                    <th scope="col" id="income_status" class="manage-column column-income_status">状态</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){
                    if(empty($row['user_real_name'])){
                        $real_name = get_user_by('ID',$row['user_id'])->user_login;
                    }else{
                        $real_name = unserialize($row['user_real_name'])['real_name'];
                    }
                    ?>
                    <tr data-id="<?=$row['id']?>">
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="cb-select-407">选择<?=$real_name?></label>
                            <input id="cb-select-<?=$row['id']?>" class="check_list" type="checkbox" name="post[]" value="<?=$row['id']?>">
                            <div class="locked-indicator">
                                <span class="locked-indicator-icon" aria-hidden="true"></span>
                                <span class="screen-reader-text">“<?=$real_name?>”已被锁定</span>
                            </div>
                        </th>
                        <td class="real_name column-real_name has-row-actions column-primary" data-colname="付款人">
                            <?=$real_name?>
                            <br>
                            <div class="row-actions">
                                <!--                                <span class="edit"><a href="">编辑</a></span>-->
                                <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                                <!--                               <span class="view"><a href="">资料</a></span>-->
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="project column-project" data-colname="类型">
                            <?php
                            switch ($row['income_type']){
                                case 'subject':
                                    $zone_type_name = $wpdb->get_var("SELECT zone_type_name FROM {$wpdb->prefix}zone_type WHERE id='{$row['match_id']}'");
                                    echo '申请'.$zone_type_name;
                                    break;
                                case 'match':
                                    echo '比赛';
                                    break;
                                case 'grading':
                                    echo '考级';
                                    break;
                                case 'undertake':
                                    echo '承办';
                                    break;
                                case 'extract':
                                    echo '提现';
                                    break;
                            }
                            ?>

                        </td>
                        <td class="referee column-referee" data-colname="直接推广">
                            <?=!empty($row['referee_real_name'])?unserialize($row['referee_real_name'])['real_name']:get_user_by('ID',$row['referee_id'])->user_login?>
                            (<?=$row['referee_income']?>)
                        </td>
                        <td class="indirect_referee column-indirect_referee" data-colname="间接推广">
                            <?=!empty($row['indirect_referee_name'])?unserialize($row['indirect_referee_name'])['real_name']:get_user_by('ID',$row['indirect_referee_id'])->user_login?>
                            <?=$row['indirect_referee_income']>0?'('.$row['indirect_referee_income'].')':''?>
                        </td>
                        <td class="person_liable column-person_liable" data-colname="负责人">
                            <?=!empty($row['person_liable_name'])?unserialize($row['person_liable_name'])['real_name']:get_user_by('ID',$row['person_liable_id'])->user_login?>
                            <?=$row['person_liable_income']>0?'('.$row['person_liable_income'].')':''?>
                        </td>
                        <td class="sponsor column-sponsor" data-colname="主办方">
                            <?=$row['zone_name']?>
                            <?=$row['sponsor_income']>0?'('.$row['sponsor_income'].')':''?>
                        </td>
                        <td class="manager column-manager" data-colname="事业员">
                            <?=!empty($row['manager_name'])?unserialize($row['manager_name'])['real_name']:get_user_by('ID',$row['manager_id'])->user_login?>
                            <?=$row['manager_income']>0?'('.$row['manager_income'].')':''?>
                        </td>
                        <td class="income_status column-income_status" data-colname="状态" id="cardImg-<?=$row['user_id']?>">
                            <?=$row['income_status'] == '1'?'待确认':'已确认'?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                    <th scope="col" class="manage-column column-real_name column-primary">付款人</th>
                    <th scope="col" class="manage-column column-project">类型</th>
                    <th scope="col" class="manage-column column-referee">直接推广</th>
                    <th scope="col" class="manage-column column-indirect_referee">间接推广</th>
                    <th scope="col" class="manage-column column-person_liable">负责人</th>
                    <th scope="col" class="manage-column column-sponsor">主办方</th>
                    <th scope="col" class="manage-column column-manager">事业员</th>
                    <th scope="col" class="manage-column column-income_status">状态</th>
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

            <br class="clear">

        </div>
        <?php
    }

    /**
     *用户收益流水
     */
    public function profitUserLog(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $type_id = isset($_GET['type_id']) ? intval($_GET['type_id']) : 0;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $where = "WHERE 1=1";
        if($searchStr != ''){
            $where .= "  AND (u.user_login LIKE '%{$searchStr}%' OR um.meta_value LIKE '%{$searchStr}%' OR zm.zone_name LIKE '%{$searchStr}%')";
        }
        if($type_id === 1){
            $where .= " AND zm.id != ''";
        }elseif($type_id === 2){
            $where .= " AND zm.id IS NULL";
        }
        if($user_id > 0){
            $where .= " AND usl.user_id='{$user_id}'";
        }
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS 
                usl.user_id,usl.income_type,usl.income_type,usl.match_id,usl.user_income,usl.created_time,usl.id,u.user_login,zm.zone_name,zm.id AS zone_id,usl.user_type,
                um.meta_value AS user_real_name 
                FROM {$wpdb->prefix}user_stream_logs AS usl 
                LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=usl.user_id AND um.meta_key='user_real_name' AND um.user_id!=''
                LEFT JOIN {$wpdb->users} AS u ON u.ID=usl.user_id 
                LEFT JOIN {$wpdb->prefix}zone_meta AS zm ON zm.user_id=usl.user_id 
                {$where} 
                ORDER BY usl.created_time DESC
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
        ?>
        <div class="wrap">

            <h1 class="wp-heading-inline">用户收益流水</h1>


            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤用户收益流水</h2>

            <?php
            if($user_id < 1){

                ?>
                <p class="search-box">
                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                    <input type="search" id="search_val" name="search_val" placeholder="姓名/用户名/机构名" value="<?=$searchStr?>">
                    <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=fission-profit-user-log&type_id='.$type_id.'&user_id='.$user_id.'&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
                </p>
                <?php
            }
            ?>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">
                <?php
                if($user_id < 1){

                    ?>
                    <ul class="subsubsub">
                        <li class="all"><a href="<?=admin_url('admin.php?page=fission-profit-user-log&type_id=0&user_id='.$user_id)?>" <?=$type_id===0?'class="current"':''?> aria-current="page">全部<span class="count"></span></a> |</li>
                        <li class="all"><a href="<?=admin_url('admin.php?page=fission-profit-user-log&type_id=1&user_id='.$user_id)?>" <?=$type_id===1?'class="current"':''?> aria-current="page">机构流水<span class="count"></span></a> |</li>
                        <li class="all"><a href="<?=admin_url('admin.php?page=fission-profit-user-log&type_id=2&user_id='.$user_id)?>" <?=$type_id===2?'class="current"':''?> aria-current="page">用户流水<span class="count"></span></a></li>

                    </ul>
                    <?php
                }
                ?>


                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">机构列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                   <th scope="col" id="real_name" class="manage-column column-real_name column-primary">姓名/用户名(机构名称)</th>
                    <th scope="col" id="role" class="manage-column column-role">角色</th>
                    <th scope="col" id="income_type" class="manage-column column-income_type">类型</th>
<!--                    <th scope="col" id="match_id" class="manage-column column-match_id">项目</th>-->
                    <th scope="col" id="user_income" class="manage-column column-user_income">数额</th>
                    <th scope="col" id="created_time" class="manage-column column-created_time">时间</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){

                    if(empty($row['user_real_name'])){
                        $real_name = $row['user_login'];
                    }else{
                        $real_name = unserialize($row['user_real_name'])['real_name'];
                    }
                    if(!empty($row['zone_name'])) $real_name .= "({$row['zone_name']})";

                    ?>
                    <tr data-uid="<?=$row['id']?>">

                        <td class="real_name column-real_name has-row-actions column-primary" data-colname="姓名/用户名(机构名称)">
                            <?=$real_name?>
                            <br>
                            <div class="row-actions">
                                <!--                                <span class="edit"><a href="">编辑</a></span>-->
                                <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                                <!--                               <span class="view"><a href="">资料</a></span>-->
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="role column-role" data-colname="角色">
                            <?=$row['zone_id'] > 0 ? '机构': '用户'?>

                        </td>
                        <td class="income_type column-income_type" data-colname="类型">
                            <?php
                            switch ($row['income_type']){
                                case 'match':
                                    echo  '比赛';
                                    break;
                                case 'grading':
                                    echo '考级';
                                    break;
                                case 'extract':
                                    echo '提现';
                                    break;
                                case 'subject':
                                    $zone_type_name = $wpdb->get_var("SELECT zone_type_name FROM {$wpdb->prefix}zone_type WHERE id='{$row['user_type']}'");
                                    if($zone_type_name == '赛事') $zone_type_name = '赛区';
                                    echo '推荐'.$zone_type_name;
                                    break;
                                case 'undertake':
                                    echo '承办';
                                    break;
                            }
                            ?>
                        </td>
                        <td class="user_income column-user_income" data-colname="数额">
                            <span style="<?=$row['user_income'] < 0 ? 'color:#c41d00;': ($row['user_income']>0?'color:#0087c4;':'')?>;"><?=$row['user_income']?$row['user_income']:'未到账'?></span>

                        </td>
                        <td class="created_time column-created_time" data-colname="时间"><?=$row['created_time']?> </td>

                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th scope="col" class="manage-column column-real_name column-primary">姓名/用户名(机构名称)</th>
                    <th scope="col" class="manage-column column-role">角色</th>
                    <th scope="col" class="manage-column column-income_type">类型</th>
<!--                    <th scope="col" class="manage-column column-match_id">项目</th>-->
                    <th scope="col" class="manage-column column-user_income">数额</th>
                    <th scope="col" class="manage-column column-created_time">时间</th>
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

            <br class="clear">
            <script>
                jQuery(document).ready(function($) {

                });
            </script>
        </div>
        <?php
    }

    /**
     * 提现记录
     */
    public function profitExtractLog(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $type = isset($_GET['ctype']) ? intval($_GET['ctype']) : 0;
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $where = "";
        $join = '';
        if($searchStr != ''){
            $where = " WHERE (u.user_login LIKE '%{$searchStr}%' OR um2.meta_value LIKE '%{$searchStr}%' OR zm.zone_name LIKE '%{$searchStr}%')";
            $join = " LEFT JOIN {$wpdb->users} AS u ON u.ID=ue.extract_id 
                      LEFT JOIN {$wpdb->usermeta} AS um2 ON um2.user_id=ue.extract_id AND um2.meta_key='user_real_name'";
        }
        if($type > 0){
            $where .=  $where == '' ? " WHERE extract_status='{$type}'" : " AND extract_status='{$type}'";
        }
        if($user_id > 0){
            $where .=  $where == '' ? " WHERE ue.extract_id='{$user_id}'" : " AND ue.extract_id='{$user_id}'";
        }
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS ue.*,um.meta_value AS censor_real_name,zm.zone_name 
                FROM {$wpdb->prefix}user_extract_logs AS ue 
                LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=ue.censor_user_id AND um.meta_key='user_real_name'
                LEFT JOIN {$wpdb->prefix}zone_meta AS zm ON zm.user_id=ue.extract_id
                {$join}
                {$where} 
                LIMIT {$start},{$pageSize}",ARRAY_A);
//        leo_dump($rows);die;
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
        $sql = "SELECT COUNT(id) FROM {$wpdb->prefix}user_extract_logs";
        $num_where = $user_id>0?" WHERE extract_id='$user_id' AND extract_status=":" WHERE extract_status=";
        $all_num = $wpdb->get_var($sql);
        $wait_num = $wpdb->get_var($sql.$num_where."'1'");
        $agree_num = $wpdb->get_var($sql.$num_where."'2'");
        $refuse_num = $wpdb->get_var($sql.$num_where."'3'");
        ?>
        <div class="wrap">

            <h1 class="wp-heading-inline">提现记录</h1>


            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤提现记录</h2>

            <?php
            if($user_id < 1){
                ?>
                <p class="search-box">
                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                    <input type="search" id="search_val" name="search_val" placeholder="姓名/用户名/机构名" value="<?=$searchStr?>">
                    <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=fission-profit-extract-log&user_id='.$user_id.'&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
                </p>
                  <ul class="subsubsub">
                <li class="all"><a href="<?=admin_url('admin.php?page=fission-profit-extract-log&ctype=0&user_id='.$user_id)?>" <?=$type===0?'class="current"':''?> aria-current="page">全部<span class="count">（<?=$all_num?>）</span></a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission-profit-extract-log&ctype=1&user_id='.$user_id)?>" <?=$type===1?'class="current"':''?> aria-current="page">待处理<span class="count">（<?=$wait_num?>）</span></a> |</li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission-profit-extract-log&ctype=2&user_id='.$user_id)?>" <?=$type===2?'class="current"':''?> aria-current="page">已通过<span class="count">（<?=$agree_num?>）</span></a> | </li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission-profit-extract-log&ctype=3&user_id='.$user_id)?>" <?=$type===3?'class="current"':''?> aria-current="page">未通过<span class="count">（<?=$refuse_num?>）</span></a></li>
                </ul>
                <?php
            }
            ?>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">



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
                    <th scope="col" id="real_name" class="manage-column column-real_name column-primary">姓名/机构</th>
                    <th scope="col" id="extract_amount" class="manage-column column-extract_amount">提现金额</th>
                    <th scope="col" id="extract_type" class="manage-column column-extract_type">收款类型</th>
                    <th scope="col" id="extract_account" class="manage-column column-extract_account">收款账号</th>
                    <th scope="col" id="apply_time" class="manage-column column-apply_time">申请时间</th>
                    <th scope="col" id="censor_time" class="manage-column column-censor_time">审核时间</th>
                    <th scope="col" id="extract_status" class="manage-column column-extract_status">提现状态</th>
                    <th scope="col" id="censor_user_id" class="manage-column column-censor_user_id">审核人</th>
                    <th scope="col" id="options1" class="manage-column column-options1">操作</th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <?php
                foreach ($rows as $row){
                    $real_name = get_user_meta($row['extract_id'],'user_real_name',true)['real_name'];
                    ?>
                    <tr data-id="<?=$row['id']?>">
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="cb-select-407">选择<?=$real_name?></label>
                            <input id="cb-select-<?=$row['id']?>" type="checkbox" name="post[]" value="<?=$row['id']?>">
                            <div class="locked-indicator">
                                <span class="locked-indicator-icon" aria-hidden="true"></span>
                                <span class="screen-reader-text">“<?=$real_name?>”已被锁定</span>
                            </div>
                        </th>
                        <td class="real_name column-real_name has-row-actions column-primary" data-colname="姓名/用户名">
                            <?=$real_name?>
                            <br>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="extract_amount column-extract_amount" data-colname="提现金额"><?=$row['extract_amount']?> </td>
                        <td class="extract_type column-extract_type" data-colname="收款类型">
                            <?php
                                switch ($row['extract_type']){
                                    case 'weChat':
                                        echo '微信';
                                        break;
                                    case 'wallet':
                                        echo '钱包';
                                        break;
                                    case 'bank':
                                        echo $row['bank_address'];
                                        break;
                                }
                            ?>
                        </td>
                        <td class="extract_account column-extract_account" data-colname="收款账号" <?=$row['extract_type'] == 'weChat' ? 'id="imgs-'.$row['extract_id'].'"':''?>>
                            <?php
                            switch ($row['extract_type']){
                                case 'weChat':
                                    echo '<img style="height:50px;" src="'.$row['extract_code_img'].'" />';
                                    break;
                                case 'wallet':
                                    echo '钱包';
                                    break;
                                case 'bank':
                                    echo $row['extract_account'];
                                    break;
                            }
                            ?>
                        </td>
                        <td class="apply_time column-apply_time" data-colname="申请时间"> <?=$row['apply_time']?></td>
                        <td class="censor_time column-censor_time" data-colname="审核时间"> <?=$row['censor_time']?></td>
                        <td class="extract_status column-extract_status" data-colname="提现状态">
                        <?php
                            switch ($row['extract_status']){
                                case '1':
                                    echo '审核中';
                                    break;
                                case '2':
                                    echo '已提现';
                                    break;
                                case '3':
                                    echo '未通过';
                                    break;
                            }
                        ?>
                        </td>
                        <td class="censor_user_id column-censor_user_id" data-colname="审核人">
                            <?=$row['censor_real_name'] ? unserialize($row['censor_real_name'])['real_name']:get_user_by('ID','censor_user_id')->user_login?>
                        </td>
                        <td class="options1 column-options1" data-colname="操作">
                            <?php
                            switch ($row['extract_status']){
                                case '1':
                                    echo '<a href="javascript:;" class="option-a" data-status="2">改为已提现</a> | <a href="javascript:;" class="option-a" data-status="3">改为未通过</a>';
                                    break;
                                case '2':
//                                    echo '<a href="javascript:;" class="option-a" data-status="1">改为审核中</a> | <a href="javascript:;" class="option-a" data-status="3">改为未通过</a>';
                                    break;
                                case '3':
                                    echo '<a href="javascript:;" class="option-a" data-status="1">改为审核中</a> | <a href="javascript:;" class="option-a" data-status="2">改为已提现</a>';
                                    break;
                            }
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
                    <th scope="col" class="manage-column column-real_name column-primary">姓名/机构</th>
                    <th scope="col" class="manage-column column-extract_amount">提现金额</th>
                    <th scope="col" class="manage-column column-extract_type">收款类型</th>
                    <th scope="col" class="manage-column column-extract_account">收款账号</th>
                    <th scope="col" class="manage-column column-apply_time">申请时间</th>
                    <th scope="col" class="manage-column column-censor_time">审核时间</th>
                    <th scope="col" class="manage-column column-extract_status">提现状态</th>
                    <th scope="col" class="manage-column column-censor_user_id">审核人</th>
                    <th scope="col" class="manage-column column-options1">操作</th>
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

            <br class="clear">
            <script>
                jQuery(document).ready(function($) {
                    $('.option-a').on('click', function () {
                        var _id = $(this).closest('tr').attr('data-id');
                        var status = $(this).attr('data-status');
                        if(_id == '' || _id == undefined) return false;
                        if(status != '2' && status != '3' && status != '1') return false;
                        if(confirm('是否确定将状态修改发放状态?')){
                            $.ajax({
                                url : ajaxurl,
                                data : {'action':'updateExtractStatus','id':_id,'status':status},
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
                    });
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        var _title = '';
                        <?php
                        foreach ($rows as $row){
                        ?>
                        layer.photos({//图片预览
                            photos: '#imgs-<?=$row['extract_id']?>',
                            move : false,
                            title : '',
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
     * 引入当前页面css/js
     */
    public function register_scripts(){

        switch ($_GET['page']){
//            case 'fission':
//                wp_register_script('layui-js',match_js_url.'layui/layui.js');
//                wp_enqueue_script( 'layui-js' );
//                break;
            case 'fission-profit-extract-log':
                wp_register_script('layui-js',match_js_url.'layui/layui.js');
                wp_enqueue_script( 'layui-js' );
                break;
        }
    }
}
new Spread();