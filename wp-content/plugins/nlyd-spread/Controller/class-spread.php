<?php
class Spread{

    public function __construct()
    {
        //add_action( 'init', array($this,'add_wp_roles'));

        add_action( 'admin_menu', array($this,'register_teacher_menu_page') );
//        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));

    }

    public function register_teacher_menu_page(){

        if ( current_user_can( 'administrator' )) {
            global $wp_roles;

            $role = 'spread_user';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'spread_user_set';//权限名
            $wp_roles->add_cap('administrator', $role);

        }
        add_submenu_page('spread','关系流水','关系流水','spread_user','spread-spread_user',array($this,'spreadUser'));
        add_submenu_page('spread','单用户设置','单用户设置','spread_user_set','spread-spread_user_set',array($this,'spreadUserSet'));
    }



    /**
     * 关系流水
     */
    public function spreadUser(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        $likeWhere = '';
        if($searchStr != ''){
            $likeWhere = " AND (um2.meta_value LIKE '%{$searchStr}%' OR u.user_mobile LIKE '%{$searchStr}%')";
        }
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS u.user_login,u.user_mobile,um.meta_value AS userID,um2.meta_value AS user_real_name,
                SUM(sl1.commission) AS commission1,SUM(sl2.commission) AS commission2,SUM(cl.money) AS cash 
                FROM `{$wpdb->prefix}spread` AS s  
                LEFT JOIN `{$wpdb->users}` AS u ON u.ID=s.superior 
                LEFT JOIN `{$wpdb->prefix}spread_log` AS sl1 ON sl1.user_id=s.superior AND sl1.under_level=1 
                LEFT JOIN `{$wpdb->prefix}spread_log` AS sl2 ON sl2.user_id=s.superior AND sl2.under_level=2 
                LEFT JOIN `{$wpdb->prefix}cash_log` AS cl ON cl.user_id=s.superior AND cl.status=2 AND cl.type=1
                LEFT JOIN `{$wpdb->usermeta}` AS um ON um.user_id=s.superior AND um.meta_key='user_ID'  
                LEFT JOIN `{$wpdb->usermeta}` AS um2 ON um2.user_id=s.superior AND um2.meta_key='user_real_name'  
                WHERE u.ID!='' {$likeWhere} GROUP BY s.superior ORDER BY s.id DESC LIMIT {$start},{$pageSize}", ARRAY_A);
        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page,
//            'add_fragment' => '&searchCode='.$searchCode,
        ));
//        echo '<pre />';
//        print_r($rows);

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">推广用户</h1>

            <!--            <a href="http://127.0.0.1/nlyd/wp-admin/user-new.php" class="page-title-action">添加用户</a>-->

            <hr class="wp-header-end">

            <form method="get">

                <p class="search-box">
                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                    <input type="search" id="search_val" name="search_val" placeholder="姓名/手机" value="<?=$searchStr?>">
                    <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=spread-spread_user&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
                </p>

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="79918ab357"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
                <div class="tablenav top">

                    <div class="tablenav-pages">
                        <span class="displaying-num"><?=$count['count']?>个项目</span>
                        <span class="pagination-links">
                        <?=$pageHtml?>
                        </span>
                    </div>
                    <br class="clear">
                </div>
                <h2 class="screen-reader-text">用户列表</h2><table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <th scope="col" id="username" class="manage-column column-username column-primary sortable">
                            <a href="javascript:;"><span>用户名</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="real_name" class="manage-column column-real_name">姓名</th>
                        <th scope="col" id="ID" class="manage-column column-ID">ID</th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                        <th scope="col" id="get_money" class="manage-column column-get_money">已获佣金</th>
                        <th scope="col" id="get_money_1" class="manage-column column-get_money_1">一级佣金</th>
                        <th scope="col" id="get_money_2" class="manage-column column-get_money_2">二级佣金</th>
                        <th scope="col" id="cash_money" class="manage-column column-cash_money">提现佣金</th>
                        <th scope="col" id="surplus_money" class="manage-column column-surplus_money">剩余佣金</th>
                        <th scope="col" id="view_member" class="manage-column column-view_member">查看成员</th>
                        <th scope="col" id="get_log" class="manage-column column-get_log">获取记录</th>
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">

                    <?php foreach ($rows as $row){ ?>
                        <tr>
                            <td class="username column-username has-row-actions column-primary" data-colname="用户名">
                                <strong><?=$row['user_login']?></strong>
                                <br>
                                <div class="row-actions">
                                    <span class="edit"><a href="javascript:;">编辑</a> | </span>
                                    <span class="delete"><a class="submitdelete" href="javascript:;">删除</a> | </span>
                                    <span class="view"><a href="javascript:;">查看</a></span>
                                </div>
                                <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                            </td>
                            <td class="real_name column-real_name" data-colname="姓名"><?=$row['user_real_name'] ? unserialize($row['user_real_name'])['real_name'] : ''?></td>
                            <td class="ID column-ID" data-colname="ID"><?=$row['userID']?></td>
                            <td class="mobile column-mobile" data-colname="手机"><?=$row['user_mobile']?></td>
                            <td class="get_money column-get_money" data-colname="已获佣金"><?=$row['commission1']+$row['commission2']?></td>
                            <td class="get_money_1 column-get_money_1" data-colname="一级佣金"><?=$row['commission1']?></td>
                            <td class="get_money_2 column-get_money_2" data-colname="二级佣金"><?=$row['commission2']?></td>
                            <td class="cash_money column-cash_money" data-colname="提现佣金"><?=$row['cash']?></td>
                            <td class="surplus_money column-surplus_money" data-colname="剩余佣金"><?= $row['commission']-$row['cash']?></td>
                            <td class="view_member column-view_member" data-colname="查看成员">查看成员</td>
                            <td class="get_log column-get_log" data-colname="获取记录">获取记录</td>
                        </tr>
                    <?php } ?>
                    <tfoot>
                    <tr>
                        <th scope="col" class="manage-column column-username column-primary sortable">
                            <a href="javascript:;"><span>用户名</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" class="manage-column column-real_name">姓名</th>
                        <th scope="col" class="manage-column column-ID">ID</th>
                        <th scope="col" class="manage-column column-mobile">手机</th>
                        <th scope="col" class="manage-column column-get_money">已获佣金</th>
                        <th scope="col" class="manage-column column-get_money_1">一级佣金</th>
                        <th scope="col" class="manage-column column-get_money_2">二级佣金</th>
                        <th scope="col" class="manage-column column-cash_money">提现佣金</th>
                        <th scope="col" class="manage-column column-surplus_money">剩余佣金</th>
                        <th scope="col" class="manage-column column-view_member">查看成员</th>
                        <th scope="col" class="manage-column column-get_log">获取记录</th>
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">

                    <div class="tablenav-pages">
                        <span class="displaying-num"><?=$count['count']?>个项目</span>
                        <span class="pagination-links">
                        <?=$pageHtml?>
                        </span>
                    </div>
                    <br class="clear">
                </div>
            </form>

            <br class="clear">
        </div>
        <?php
    }

    /**
     * 单用户设置
     */
    public function spreadUserSet(){
        $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
        global $wpdb;
        if($searchStr == ''){
            $rows = [];
            $count['count'] = 0;
            $pageHtml = '';
        }else{
            $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
            $page < 1 && $page = 1;
            $pageSize = 20;
            $start = ($page-1)*$pageSize;
            $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS u.user_login,u.user_mobile,u.ID AS user_id FROM `{$wpdb->users}` AS u 
                          LEFT JOIN `{$wpdb->usermeta}` AS um ON um.user_id=u.ID AND um.meta_key='user_real_name' 
                          WHERE um.meta_value LIKE '%{$searchStr}%' OR u.user_mobile LIKE '%{$searchStr}%' LIMIT {$start},{$pageSize}", ARRAY_A);
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
        }
//        leo_dump($rows);

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">单用户设置</h1>

            <!--            <a href="http://127.0.0.1/nlyd/wp-admin/user-new.php" class="page-title-action">添加用户</a>-->

            <hr class="wp-header-end">

            <form method="get">

                <p class="search-box">
                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                    <input type="search" id="search_val" name="search_val" placeholder="姓名/手机" value="<?=$searchStr?>">
                    <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('admin.php?page=spread-spread_user_set&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
                </p>

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="79918ab357"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">
                <div class="tablenav top">

                    <div class="tablenav-pages">
                        <span class="displaying-num"><?=$count['count']?>个项目</span>
                        <span class="pagination-links">
                        <?=$pageHtml?>
                        </span>
                    </div>
                    <br class="clear">
                </div>
                <h2 class="screen-reader-text">用户列表</h2><table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <th scope="col" id="username" class="manage-column column-username column-primary sortable">
                            <a href="javascript:;"><span>用户名</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="real_name" class="manage-column column-real_name">姓名</th>
                        <th scope="col" id="ID" class="manage-column column-ID">ID</th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                        <th scope="col" id="lxl_level" class="manage-column column-lxl_level">乐学乐下级</th>
                        <th scope="col" id="fx_level" class="manage-column column-fx_level">分销下级</th>
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">

                    <?php foreach ($rows as $row){
                            $usermeta = get_user_meta($row['user_id'],'',true);
//                            var_dump($usermeta['lxl_level']);
                        ?>
                        <tr data-id="<?=$row['user_id']?>">
                            <td class="username column-username has-row-actions column-primary" data-colname="用户名">
                                <strong><?=$row['user_login']?></strong>
                                <br>
                                <div class="row-actions">
                                    <span class="edit"><a href="javascript:;">编辑</a> | </span>
                                    <span class="delete"><a class="submitdelete" href="javascript:;">删除</a> | </span>
                                    <span class="view"><a href="javascript:;">查看</a></span>
                                </div>
                                <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                            </td>
                            <td class="real_name column-real_name" data-colname="姓名"><?=unserialize($usermeta['user_real_name'][0])['real_name']?></td>
                            <td class="ID column-ID" data-colname="ID"><?=$usermeta['user_ID'][0]?></td>
                            <td class="mobile column-mobile" data-colname="手机"><?=$row['user_mobile']?></td>
                            <td class="lxl_level column-lxl_level" data-colname="乐学乐下级">
                                <select name="" id="" disabled="disabled" style="pointer-events: none">
                                    <option value="0">无</option>
                                    <option <?=$usermeta['lxl_level'][0] == '1' ? 'selected="selected"' :'' ?> value="1">一级</option>
                                    <option <?=$usermeta['lxl_level'][0] == '2' ? 'selected="selected"' :'' ?> value="2">二级</option>
                                </select>
                            </td>
                            <td class="fx_level column-fx_level" data-colname="分销下级">
                                <select name="" id="" disabled="disabled" style="pointer-events: none">
                                    <option value="0">无</option>
                                    <option <?=$usermeta['fx_level'][0] == '1' ? 'selected="selected"' :'' ?> value="1">一级</option>
                                    <option <?=$usermeta['fx_level'][0] == '2' ? 'selected="selected"' :'' ?> value="2">二级</option>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>
                    <tfoot>
                    <tr>
                        <th scope="col" class="manage-column column-username column-primary sortable">
                            <a href="javascript:;"><span>用户名</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" class="manage-column column-real_name">姓名</th>
                        <th scope="col" class="manage-column column-ID">ID</th>
                        <th scope="col" class="manage-column column-mobile">手机</th>
                        <th scope="col" class="manage-column column-lxl_level">乐学乐下级</th>
                        <th scope="col" class="manage-column column-fx_level">分销下级</th>
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">

                    <div class="tablenav-pages">
                        <span class="displaying-num"><?=$count['count']?>个项目</span>
                        <span class="pagination-links">
                        <?=$pageHtml?>
                        </span>
                    </div>
                    <br class="clear">
                </div>
            </form>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('.lxl_level').on('dblclick',function () {
                        $(this).find('select').prop('disabled',false);
                        $(this).find('select').css('pointer-events','auto');
                    });
                    $('.lxl_level').find('select').on('blur',function () {
                        $(this).prop('disabled',true);
                        $(this).css('pointer-events','none');
                    });

                    $('.fx_level').on('dblclick',function () {
                        $(this).find('select').prop('disabled',false);
                        $(this).find('select').css('pointer-events','auto');
                    });
                    $('.fx_level').find('select').on('blur',function () {
                        $(this).prop('disabled',true);
                        $(this).css('pointer-events','none');
                    });

                    $('.lxl_level').find('select').on('change',function () {
                        var user_id = $(this).closest('tr').attr('data-id');
                        var level = $(this).val();
                        $.ajax({
                            url : ajaxurl,
                            data : {'action':'changeUserSpreadLevel','user_id':user_id,'level':level,'type':'lxl'},
                            dataType : 'json',
                            type : 'post',
                            success : function (response) {
                                alert(response.data.info);
                            },error : function () {
                                alert('请求失败');
                            }
                        });
                    });

                    $('.fx_level').find('select').on('change',function () {
                        var user_id = $(this).closest('tr').attr('data-id');
                        var level = $(this).val();
                        $.ajax({
                            url : ajaxurl,
                            data : {'action':'changeUserSpreadLevel','user_id':user_id,'level':level,'type':'fx'},
                            dataType : 'json',
                            type : 'post',
                            success : function (response) {
                                alert(response.data.info);
                            },error : function () {
                                alert('请求失败');
                            }
                        });
                    });
                })
            </script>
            <br class="clear">
        </div>
        <?php
    }
}
new Spread();