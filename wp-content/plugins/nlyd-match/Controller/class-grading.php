<?php
class Grading
{
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_order_menu_page') );
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
    }

    public function register_order_menu_page(){

        if ( current_user_can( 'administrator' ) && !current_user_can( 'edit.php?post_type=grading' ) ) {
            global $wp_roles;

            $role = 'grading_students';//权限名
            $wp_roles->add_cap('administrator', $role);
        }

        add_submenu_page('edit.php?post_type=grading','考级选手','考级选手','grading_students','grading-students',array($this,'gradingStudents'));
    }

    /**
     * 考级选手页面
     */
    public function gradingStudents(){
        $gradingId = isset($_GET['grading_id']) ? intval($_GET['grading_id']) : 0;
        $gradingId < 1 && exit('参数错误');
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $searchJoin = '';
        $searchWhere = '';
        global $wpdb;
        if($searchStr!=''){
            $searchJoin = "LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id=o.user_id AND um.meta_key='user_real_name' 
                           LEFT JOIN {$wpdb->usermeta} AS um2 on um2.user_id=o.user_id AND um2.meta_key='user_ID'";
            $searchWhere = "AND (um.meta_value LIKE '%{$searchStr}%' OR u.user_mobile LIKE '%{$searchStr}%' OR um2.meta_value LIKE '%{$searchStr}%')";
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS u.user_login,u.user_mobile,u.user_email,o.user_id,o.created_time FROM `{$wpdb->prefix}order` AS o 
        LEFT JOIN `{$wpdb->users}` AS u ON u.ID=o.user_id AND u.ID!='' 
        {$searchJoin} 
        WHERE o.match_id='{$gradingId}' AND o.order_type=2 AND o.pay_status IN(2,3,4) 
        {$searchWhere} 
        LIMIT {$start},{$pageSize}";
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

        $rows  = $wpdb->get_results($sql, ARRAY_A);
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">考级选手</h1>

            <a href="<?='javascript:;'?>" class="page-title-action">添加选手</a>

            <hr class="wp-header-end">

                <p class="search-box">
                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                    <input type="search" id="search_val" name="s" placeholder="姓名/手机/ID" value="<?=$searchStr?>">
                    <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('edit.php?post_type=grading&page=grading-students&grading_id='.$gradingId.'&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
                </p>

                <div class="tablenav top">



                    <div class="tablenav-pages">
                        <span class="displaying-num"><?=$count['count']?>个项目</span>
                        <?=$pageHtml?>
                    </div>
                    <br class="clear">
                </div>
                <h2 class="screen-reader-text">用户列表</h2><table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <th scope="col" id="real_name" class="manage-column column-real_name column-primary sortable">
                            <a href="javascript:;">
                                <span>姓名</span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" id="ID" class="manage-column column-ID">ID</th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                        <th scope="col" id="email" class="manage-column column-email">邮箱</th>
                        <th scope="col" id="is_adopt" class="manage-column column-is_adopt">是否通过</th>
                        <th scope="col" id="adopt_level" class="manage-column column-adopt_level">通过级别</th>
                        <th scope="col" id="created_time" class="manage-column column-created_time">报名时间</th>
                    </tr>
                     </thead>

                    <tbody id="the-list" data-wp-lists="list:user">

                    <?php foreach ($rows as $row){
                        $usermeta = get_user_meta($row['user_id']);
                        $use_real_name = isset($usermeta['user_real_name']) ? unserialize($usermeta['user_real_name'][0]) : [];
                        ?>
                    <tr id="user-<?=$row['user_id']?>"><td class="real_name column-real_name has-row-actions column-primary" data-colname="姓名">
                            <img alt="" src="<?=isset($usermeta['user_head']) ? $usermeta['user_head'][0] : ''?>" class="avatar avatar-32 photo" height="32" width="32">
                            <strong><?=isset($use_real_name['real_name']) ? $use_real_name['real_name'] : ''?></strong>
                            <br>
<!--                            <div class="row-actions">-->
<!--                                <span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/user-edit.php?user_id=16&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">编辑</a> | </span>-->
<!--                            </div>-->
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="ID column-ID" data-colname="ID"><?=isset($usermeta['user_ID']) ? $usermeta['user_ID'][0] : ''?></td>
                        <td class="mobile column-mobile" data-colname="手机"><?=$row['user_mobile']?></td>
                        <td class="email column-email" data-colname="邮箱"><?=$row['user_email']?></td>
                        <td class="is_adopt column-is_adopt" data-colname="是否通过"><?='是否通过'?></td>
                        <td class="adopt_level column-adopt_level" data-colname="通过级别"><?='2'?></td>
                        <td class="created_time column-created_time" data-colname="报名时间"><?=$row['created_time']?></td>
                    </tr>
                    <?php } ?>
                    <tfoot>
                    <tr>
                        <th scope="col" class="manage-column column-real_name column-primary sortable">
                            <a href="javascript:;">
                                <span>姓名</span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" class="manage-column column-ID">ID</th>
                        <th scope="col" class="manage-column column-mobile">手机</th>
                        <th scope="col" class="manage-column column-email">邮箱</th>
                        <th scope="col" class="manage-column column-is_adopt">是否通过</th>
                        <th scope="col" class="manage-column column-adopt_level">通过级别</th>
                        <th scope="col" class="manage-column column-created_time">报名时间</th>
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
     * 引入当前页面css/js
     */
    public function register_scripts(){

    }
}

new Grading();