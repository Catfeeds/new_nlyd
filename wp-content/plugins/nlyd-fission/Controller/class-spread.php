<?php
//组织主体控制器
class Spread{
    public function __construct($is_list = false)
    {
        if($is_list === false){
            add_action( 'admin_menu', array($this,'register_organize_menu_page') );
//            add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
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

            $role = 'profit_extract_log';//权限名
            $wp_roles->add_cap('administrator', $role);
        }
        add_submenu_page('fission','收益设置','收益设置','profit_set','fission-profit-set',array($this,'profitSet'));
        add_submenu_page('fission','新增收益设置','新增收益设置','add_profit_set','fission-add-profit-set',array($this,'addProfitSet'));
        add_submenu_page('fission','分成记录','分成记录','profit_log','fission-profit-log',array($this,'profitLog'));
        add_submenu_page('fission','用户收益流水','用户收益流水','profit_user_log','fission-profit-user-log',array($this,'profitUserLog'));
        add_submenu_page('fission','提现记录','提现记录','profit_extract_log','fission-profit-extract-log',array($this,'profitExtractLog'));
    }

    /**
     * 收益设置
     */
    public function profitSet(){
        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}spread_set", ARRAY_A);
        $spreadCategoryArr = getSpreadCategory();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">主体权限列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize-power')?>" class="page-title-action">添加主体权限</a>

            <hr class="wp-header-end">

            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <br class="clear">
            </div>
            <h2 class="screen-reader-text">主体列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th scope="col" id="spread_type" class="manage-column column-spread_type column-primary">分成类别</th>
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
                                <label class="screen-reader-text" for="cb-select-407">选择<?=$spreadCategoryArr[$row['spread_type']]?></label>
                                <input id="cb-select-<?=$row['id']?>" type="checkbox" name="post[]" value="<?=$row['id']?>">
                                <div class="locked-indicator">
                                    <span class="locked-indicator-icon" aria-hidden="true"></span>
                                    <span class="screen-reader-text">“<?=$spreadCategoryArr[$row['spread_type']]?>”已被锁定</span>
                                </div>
                            </th>
                            <td class="name column-name has-row-actions column-primary" data-colname="名称">
                                <?=$spreadCategoryArr[$row['spread_type']]?>
                                <br>
                                <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                            </td>
                            <td class="direct_superior column-direct_superior" data-colname="直接上级">
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
            if($spread_status !== 1 && $spread_status !== 2) $error_msg = '请选择状态!';
            if($error_msg == ''){
                $insertData = [
                    'spread_type' => $spread_type,
                    'direct_superior' => $direct_superior,
                    'indirect_superior' => $indirect_superior,
                    'first_cause' => $first_cause,
                    'second_cause' => $second_cause,
                    'coach' => $coach,
                    'sub_center' => $sub_center,
                    'mechanism' => $mechanism,
                    'spread_status' => $spread_status,
                ];
                if($id > 0){
                    $bool = $wpdb->update($wpdb->prefix.'spread_set',$insertData,['id' => $id]);
                }else{
                    //判断是否已经存在此类设置

                    $bool = $wpdb->insert($wpdb->prefix.'spread_set',$insertData);
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
        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加/编辑分成项</h1>

            <div id="ajax-response">
                <span style="color: #2bc422"><?=$success_msg?></span>
                <span style="color: #c44e00"><?=$error_msg?></span>
            </div>

            <form method="post" action="" id="adduser" class="validate" novalidate="novalidate">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="5f6ea9ff44"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php"><table class="form-table">
                    <tbody>
                    <tr class="">
                        <th scope="row"><label for="spread_type">所属大类 </label></th>
                        <td>
                            <select name="spread_type" id="spread_type">
                                <?php foreach (getSpreadCategory() as $sck => $scv){ ?>
                                    <option <?=isset($row) && $row['spread_type'] == $sck?'selected="selected"':''?> value="<?=$sck?>"><?=$scv?></option>
                                <?php } ?>
                            </select>
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
                        <th scope="row"><label for="sub_center">赛区/分中心/考级中心</label></th>
                        <td>
                            <input type="text" name="sub_center" value="<?=isset($row)?$row['sub_center']:''?>" id="sub_center" maxlength="60"><span>元/百分比</span>
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="mechanism">参赛机构</label></th>
                        <td>
                            <input type="text" name="mechanism" value="<?=isset($row)?$row['mechanism']:''?>" id="mechanism" maxlength="60"><span>元/百分比</span>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><label for="spread_status">状态</label></th>
                        <td>
                            <label for="spread_status_1"><input type="radio" <?=isset($row) && $row['spread_status'] == '1' ? 'checked="checked"': ''?> name="spread_status" id="spread_status_1" value="1">正常</label>
                            <label for="spread_status_2"><input type="radio" <?=isset($row) && $row['spread_status'] == '2' ? 'checked="checked"': ''?> name="spread_status" id="spread_status_2" value="2">禁用</label>
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
     * 分成记录
     */
    public function profitLog(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';

        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $where = "WHERE 1=1";
        if($searchStr != ''){
            $where .= " AND (p.post_title LIKE '%{$searchStr}%' OR um.meta_value LIKE '%{$searchStr}%')";
        }
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS 
                il.income_type,il.match_id,p.post_title,il.referee_income,il.indirect_referee_income,il.person_liable_income,il.sponsor_income,il.manager_income,
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
                LEFT JOIN `{$wpdb->posts}` AS p ON p.ID=il.match_id 
                {$where} 
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
            <h1 class="wp-heading-inline">主体列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize')?>" class="page-title-action">添加主体</a>

            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤主体列表</h2>
            <p class="search-box">
                <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                <input type="search" id="search_val" name="search_val" placeholder="付款人/项目" value="<?=$searchStr?>">
                <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=fission-profit-log&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
            </p>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">批量操作</option>
                        <option value="2">改为待确认</option>
                        <option value="1">改为已确认</option>
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
                    <th scope="col" id="real_name" class="manage-column column-real_name column-primary">付款人</th>
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
                    if(empty($row['user_id'])){
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
                            <?=$row['income_status'] == '1'?'<a href="javascript:;" class="update_status" data-status="2">改为已确认</a>':'<a href="javascript:;" class="update_status" data-status="1">改为待确认</a>'?>
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
                        <option value="2">改为待确认</option>
                        <option value="1">改为已确认</option>
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
     *用户收益流水
     */
    public function profitUserLog(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';

        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $where = "";
        if($searchStr != ''){
            $where = " WHERE u.user_login LIKE '%{$searchStr}%' OR um.meta_value LIKE '%{$searchStr}%'";
        }
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS 
                usl.user_id,usl.income_type,usl.income_type,usl.match_id,usl.user_income,usl.created_time,usl.id,u.user_login,
                um.meta_value AS user_real_name,p.post_title 
                FROM {$wpdb->prefix}user_stream_logs AS usl 
                LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=usl.user_id AND um.meta_key='user_real_name' 
                LEFT JOIN {$wpdb->users} AS u ON u.ID=usl.user_id 
                LEFT JOIN {$wpdb->posts} AS p ON p.ID=usl.match_id 
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
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">用户收益流水</h1>


            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤用户收益流水</h2>
            <p class="search-box">
                <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                <input type="search" id="search_val" name="search_val" placeholder="姓名/用户名" value="<?=$searchStr?>">
                <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=fission-profit-user-log&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
            </p>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">



                <div class="tablenav-pages">
                    <span class="displaying-num"><?=$count['count']?>个项目</span>
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">主体列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                   <th scope="col" id="real_name" class="manage-column column-real_name column-primary">姓名/用户名</th>
                    <th scope="col" id="income_type" class="manage-column column-income_type">类型</th>
                    <th scope="col" id="match_id" class="manage-column column-match_id">项目</th>
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
                    ?>
                    <tr data-uid="<?=$row['id']?>">

                        <td class="real_name column-real_name has-row-actions column-primary" data-colname="姓名/用户名">
                            <?=$real_name?>
                            <br>
                            <div class="row-actions">
                                <!--                                <span class="edit"><a href="">编辑</a></span>-->
                                <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                                <!--                               <span class="view"><a href="">资料</a></span>-->
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
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
                            }
                            ?>
                        </td>
                        <td class="match_id column-match_id" data-colname="项目"><?=$row['post_title']?> </td>
                        <td class="user_income column-user_income" data-colname="数额">
                            <span style="<?=$row['user_income'] < 0 ? 'color:#c41d00;': ($row['user_income']>0?'color:#0087c4;':'')?>;"><?=$row['user_income']?></span>

                        </td>
                        <td class="created_time column-created_time" data-colname="时间"><?=$row['created_time']?> </td>

                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th scope="col" class="manage-column column-real_name column-primary">姓名/用户名</th>
                    <th scope="col" class="manage-column column-income_type">类型</th>
                    <th scope="col" class="manage-column column-match_id">项目</th>
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

        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $where = "";
        $join = '';
        if($searchStr != ''){
            $where = " WHERE u.user_login LIKE '%{$searchStr}%' OR um2.meta_value LIKE '%{$searchStr}%' OR zm.zone_name LIKE '%{$searchStr}%'";
            $join = " LEFT JOIN {$wpdb->users} AS u ON u.ID=ue.extract_id 
                      LEFT JOIN {$wpdb->usermeta} AS um2 ON um2.user_id=ue.extract_id AND um2.meta_key='user_real_name'
                      LEFT JOIN {$wpdb->prefix}zone_meta AS zm ON zm.id=ue.extract_id";
        }
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS ue.*,um.meta_value AS censor_real_name 
                FROM {$wpdb->prefix}user_extract_logs AS ue 
                LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=ue.censor_user_id AND um.meta_key='user_real_name'
                {$join}
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

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">提现记录</h1>


            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤提现记录</h2>
            <p class="search-box">
                <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                <input type="search" id="search_val" name="search_val" placeholder="姓名/用户名/主体名" value="<?=$searchStr?>">
                <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=fission-profit-extract-log&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
            </p>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="e7103a7740"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
            <div class="tablenav top">



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
                    <th scope="col" id="real_name" class="manage-column column-real_name column-primary">姓名/主体</th>
                    <th scope="col" id="extract_user_type" class="manage-column column-extract_user_type">提现类型</th>
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
                    if($row['extract_user_type'] == 1){
                        $user_real_name = get_user_meta($row['extract_id'], 'user_real_name', true);
                        if($user_real_name) {
                            $real_name = $user_real_name['real_name'];
                        }else{
                            $real_name = get_user_by('ID',$row['extract_id'])->user_login;
                        }

                        $extract_type_name = '用户提现';
                    }else{
                        $extract_type_name = '主体提现';
                        $real_name = $wpdb->get_var("SELECT zone_name FROM {$wpdb->prefix}zone_meta WHERE id={$row['extract_id']}");
                    }
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
                        <td class="extract_user_type column-extract_user_type" data-colname="提现金额"><?=$extract_type_name?> </td>
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
                                        echo '银行卡';
                                        break;
                                }
                            ?>
                        </td>
                        <td class="extract_account column-extract_account" data-colname="收款账号"><?=$row['extract_account']?> </td>
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
                                    echo '<a href="javascript:;" class="option-a" data-status="1">改为审核中</a> | <a href="javascript:;" class="option-a" data-status="3">改为未通过</a>';
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
                    <th scope="col" class="manage-column column-real_name column-primary">姓名/主体</th>
                    <th scope="col" class="manage-column column-extract_user_type">提现类型</th>
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
            case 'fission':
                wp_register_script('layui-js',match_js_url.'layui/layui.js');
                wp_enqueue_script( 'layui-js' );
                break;
        }
    }
}
new Spread();