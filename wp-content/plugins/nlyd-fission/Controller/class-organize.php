<?php
//组织主体控制器
class Organize{
    public function __construct($is_list = false)
    {
        if($is_list === false){
            add_action( 'admin_menu', array($this,'register_organize_menu_page') );
//        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
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
        }
        add_submenu_page('fission','主体详情','主体详情','organize_detail','fission-organize-detail',array($this,'organizeDetails'));
        add_submenu_page('fission','新增主体','新增主体','add_organize','fission-add-organize',array($this,'addOrganize'));
        add_submenu_page('fission','主体类型','主体类型','organize_type','fission-organize-type',array($this,'organizeType'));
        add_submenu_page('fission','新增主体类型','新增主体类型','add_organize_type','fission-add-organize-type',array($this,'addOrganizeType'));
    }

    /**
     *主体列表
     */
    public function organizeList(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $type = isset($_GET['ctype']) ? intval($_GET['ctype']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';

        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $where = "WHERE 1=1";
        $leftJoin = '';
        $joinWhere = '';
        if($type>0){
            $where .= " AND zm.type_id='{$type}'";
        }
        if($searchStr != ''){
            $leftJoin = " LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=zm.user_id AND um.meta_key='user_real_name'";
            $joinWhere = " AND (um.meta_value LIKE '%{$searchStr}%' OR u.user_mobile LIKE '%{$searchStr}%')";
        }
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS u.user_login,u.user_mobile,zm.user_id,zm.type_id,zm.referee_id,zm.created_time,zm.audit_time,zm.user_status,
                CASE zm.user_status 
                WHEN 1 THEN '正常' 
                WHEN -1 THEN '正在审核' 
                WHEN -2 THEN '未通过' 
                END AS user_status_name 
                FROM {$wpdb->prefix}zone_meta AS zm 
                LEFT JOIN `{$wpdb->users}` AS u ON u.ID=zm.user_id 
                {$leftJoin} 
                {$where} 
                {$joinWhere} 
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
        //各种数量
        $numSql = "SELECT count(id) FROM {$wpdb->prefix}zone_meta";
        $all_num = $wpdb->get_var($numSql);
        $fzx_num = $wpdb->get_var($numSql." WHERE type_id=1");
        $sq_num = $wpdb->get_var($numSql." WHERE type_id=2");
        $kjzx_num = $wpdb->get_var($numSql." WHERE type_id=3");
        $jg_num = $wpdb->get_var($numSql." WHERE type_id=4");

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">主体列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize')?>" class="page-title-action">添加主体</a>

            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤主体列表</h2>
            <ul class="subsubsub">
                <li class="all"><a href="<?=admin_url('admin.php?page=fission&ctype=0')?>" <?=$type===0?'class="current"':''?> aria-current="page">全部<span class="count">（<?=$all_num?>）</span></a> |</li>
                <li class="all"><a href="<?=admin_url('admin.php?page=fission&ctype=1')?>" <?=$type===1?'class="current"':''?> aria-current="page">分中心<span class="count">（<?=$fzx_num?>）</span></a> |</li>
                <li class="administrator"><a href="<?=admin_url('admin.php?page=fission&ctype=2')?>" <?=$type===2?'class="current"':''?>>赛区<span class="count">（<?=$sq_num?>）</span></a> |</li>
                <li class="editor"><a href="<?=admin_url('admin.php?page=fission&ctype=3')?>" <?=$type===3?'class="current"':''?>>考级中心<span class="count">（<?=$kjzx_num?>）</span></a> |</li>
                <li class="subscriber"><a href="<?=admin_url('admin.php?page=fission&ctype=4')?>" <?=$type===4?'class="current"':''?>>机构<span class="count">（<?=$jg_num?>）</span></a></li>
            </ul>

            <p class="search-box">
                <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                <input type="search" id="search_val" name="search_val" placeholder="负责人姓名/手机" value="<?=$searchStr?>">
                <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=fission&ctype='.$type.'&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
            </p>
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
                        <th scope="col" id="real_name" class="manage-column column-real_name column-primary">负责人姓名</th>
                        <th scope="col" id="referee_id" class="manage-column column-referee_id">推荐人</th>
                        <th scope="col" id="zone_type" class="manage-column column-zone_type">主体类型</th>
                        <th scope="col" id="zone_status" class="manage-column column-zone_status">状态</th>
                        <th scope="col" id="created_time" class="manage-column column-created_time">提交时间</th>
                        <th scope="col" id="audit_time" class="manage-column column-audit_time">审核时间</th>
                    </tr>
                 </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                   <?php
                   foreach ($rows as $row){
                        $usermeta = get_user_meta($row['user_id']);
                        $user_real_name = isset($usermeta['user_real_name']) ? $usermeta['user_real_name'][0] : [];

                        $referee_real_name = get_user_meta($row['referee_id'],'user_real_name',true);
                   ?>
                   <tr>
                       <th scope="row" class="check-column">
                           <label class="screen-reader-text" for="cb-select-407">选择<?=isset($user_real_name['real_name'])?$user_real_name['real_name']:$row['user_login']?></label>
                           <input id="cb-select-<?=$row['user_id']?>" type="checkbox" name="post[]" value="<?=$row['user_id']?>">
                           <div class="locked-indicator">
                               <span class="locked-indicator-icon" aria-hidden="true"></span>
                               <span class="screen-reader-text">“<?=isset($user_real_name['real_name'])?$user_real_name['real_name']:$row['user_login']?>”已被锁定</span>
                           </div>
                       </th>
                       <td class="real_name column-real_name has-row-actions column-primary" data-colname="负责人姓名">
                            <?=isset($user_real_name['real_name'])?$user_real_name['real_name']:$row['user_login']?>
                           <br>
                           <div class="row-actions">
<!--                               <span class="edit"><a href="">编辑</a> | </span>-->
<!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
<!--                               <span class="view"><a href="">资料</a></span>-->
                           </div>
                           <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                       </td>
                       <td class="referee_id column-referee_id" data-colname="推荐人"><?=isset($referee_real_name['real_name'])?$referee_real_name['real_name']:($row['referee_id']>0?get_user_by('ID',$row['referee_id'])->user_login:'')?></td>
                       <td class="zone_type column-zone_type" data-colname="主体类型"><?=$this->getZoneTypeNameByType($row['type_id'])?></td>
                       <td class="zone_status column-zone_status" data-colname="状态"><?=$row['user_status_name']?></td>
                       <td class="created_time column-created_time" data-colname="提交时间"><?=$row['created_time']?></td>
                       <td class="audit_time column-audit_time" data-colname="审核时间"><?=$row['audit_time']?></td>
                   </tr>
                   <?php
                   }
                   ?>
                <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td>
                        <th scope="col" class="manage-column column-real_name column-primary">负责人姓名</th>
                        <th scope="col" class="manage-column column-referee_id">推荐人</th>
                        <th scope="col" class="manage-column column-zone_type">主体类型</th>
                        <th scope="col" class="manage-column column-zone_status">状态</th>
                        <th scope="col" class="manage-column column-created_time">提交时间</th>
                        <th scope="col" class="manage-column column-audit_time">审核时间</th>
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
     * 新增主体类型
     */
    public function addOrganizeType(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        global $wpdb;
        if(is_post()){
            $success_msg = '';
            $error_msg = '';
            $zone_type_name = isset($_POST['zone_type_name']) ? trim($_POST['zone_type_name']) : '';
            $zone_type_status = isset($_POST['zone_type_status']) ? intval($_POST['zone_type_status']) : 0;

            if($zone_type_name == '') $error_msg = '请填写类型名称';
            if($zone_type_status != 1 && $zone_type_status != 2) $error_msg = $error_msg==''?'请选择类型状态':$error_msg.'<br >请选择类型状态';

            if($error_msg == ''){
                $insertData = [
                    'zone_type_name' => $zone_type_name,
                    'zone_type_status' => $zone_type_status,
                ];
                if($id > 0){
                    $bool = $wpdb->update($wpdb->prefix.'zone_type',$insertData,['id'=>$id]);
                }else{
                    $bool = $wpdb->insert($wpdb->prefix.'zone_type',$insertData);
                }
                if($bool) $success_msg = '操作成功!';
                else $error_msg = '操作失败!';
            }
        }
        if($id > 0){
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}zone_type WHERE id='{$id}'", ARRAY_A);
        }
        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加主体类型</h1>

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

                    <tr class="">
                        <th scope="row"><label for="zone_type_status">状态 </label></th>
                        <td>
                            <input type="radio" <?=isset($row['zone_type_status']) && $row['zone_type_status'] == '1'?'checked="checked"':''?> name="zone_type_status" id="zone_type_status_1" value="1"><label for="zone_type_status_1">正常</label>  &ensp;
                            <input type="radio" <?=isset($row['zone_type_status']) && $row['zone_type_status'] == '2'?'checked="checked"':''?> name="zone_type_status" id="zone_type_status_2" value="2"><label for="zone_type_status_2">关闭</label>


                        </td>
                    </tr>

                    </tbody>
                </table>

                <p class="submit"><input type="submit" class="button button-primary" value="添加主体类型"></p>
            </form>
        </div>
        <?php
    }
    /**
     * 主体类型
     */
    public function getZoneTypeNameByType($type){
        switch ($type){
            case 1:
                $name = '分中心';
                break;
            case 2:
                $name = '赛区';
                break;
            case 3:
                $name = '考级中心';
                break;
            case 4:
                $name = '机构';
                break;
            default :
                $name = '';
        }
        return $name;
    }

    /**
     * 主体类型列表
     */
    public function organizeType(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $type = isset($_GET['ctype']) ? intval($_GET['ctype']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';

        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS id,zone_type_name,
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

            <p class="search-box">
                <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                <input type="search" id="search_val" name="search_val" placeholder="负责人姓名/手机" value="<?=$searchStr?>">
                <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=fission&ctype='.$type.'&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
            </p>
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
                                <span class="edit"><a href="<?=admin_url('admin.php?page=fission-add-organize-type&id='.$row['id'])?>">编辑</a> | </span>
                                <!--                               <span class="delete"><a class="submitdelete" href="">删除</a> | </span>-->
                                <!--                               <span class="view"><a href="">资料</a></span>-->
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
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
     * 新增主体
     */
    public function addOrganize(){
        if(is_post()){
            $success_msg = '';
            $error_msg = '';
            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $zone_type = isset($_POST['zone_type']) ? intval($_POST['zone_type']) : 0;
            $referee_id = isset($_POST['referee_id']) ? intval($_POST['referee_id']) : 0;
            $user_status = isset($_POST['user_status']) ? intval($_POST['user_status']) : 0;

            if($user_id < 0) $error_msg = '请选择升级账号';
            if($zone_type === 0) $error_msg = $error_msg==''?'请选择主体类型':$error_msg.'<br >请选择主体类型';
            if($user_id == $referee_id) $error_msg = $error_msg==''?'推荐人不能为主体账号':$error_msg.'<br >推荐人不能为主体账号';
            global $wpdb;
            if($error_msg == ''){
                $insertData = [
                    'type_id' => $zone_type,
                    'user_id' => $user_id,
                    'referee_id' => $referee_id,
                    'user_status' => $user_status,
                    'created_time' => get_time('mysql'),
                    'audit_time' => get_time('mysql'),
                ];
                $bool = $wpdb->insert($wpdb->prefix.'zone_meta',$insertData);
                if($bool) $success_msg = '添加成功!';
                else $error_msg = '添加失败!';
            }
        }
        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加主体</h1>

            <div id="ajax-response">
                <span style="color: #2bc422"><?=$success_msg?></span>
                <span style="color: #c44e00"><?=$error_msg?></span>
            </div>

            <form method="post" action="" class="validate" novalidate="novalidate">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="5f6ea9ff44"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php"><table class="form-table">
                    <tbody>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="user_id">升级账号 </label></th>
                        <td>
                            <select class="js-data-select-ajax" name="user_id" style="width: 50%" data-action="get_base_user_list" data-type="base">

                            </select>

                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="zone_type">主体类型</label></th>
                        <td>
                            <select name="zone_type" id="zone_type">
                                <option value="1">分中心</option>
                                <option value="2">赛区</option>
                                <option value="3">考级中心</option>
                                <option value="4">机构</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="referee_id">推荐人 </label></th>
                        <td>
                            <select class="js-data-select-ajax" name="referee_id" style="width: 50%" data-action="get_base_user_list" data-type="all">

                            </select>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="user_status">状态 </label></th>
                        <td>
                            <select name="user_status" id="user_status">
                                <option value="1">正常</option>
                                <option value="-1">正在审核</option>
                                <option value="-2">未通过</option>
                            </select>
                        </td>
                    </tr>

                    </tbody>
                </table>


                <p class="submit"><input type="submit" name="createuser" id="createusersub" class="button button-primary" value="添加主体"></p>
            </form>
        </div>
        <?php
    }
}
new Organize();