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
        }
        add_submenu_page('fission','主体详情','主体详情','organize_detail','fission-organize-detail',array($this,'organizeDetails'));
        add_submenu_page('fission','新增主体','新增主体','add_organize','fission-organize-detail',array($this,'organizeDetails'));
    }

    public function organizeList(){
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">主体列表</h1>

            <a href="javascript:;" class="page-title-action">添加主体</a>

            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤主体列表</h2>
            <ul class="subsubsub">
                <li class="all"><a href="users.php" class="current" aria-current="page">全部<span class="count">（11）</span></a> |</li>
                <li class="administrator"><a href="users.php?role=administrator">管理员<span class="count">（1）</span></a> |</li>
                <li class="editor"><a href="users.php?role=editor">教练<span class="count">（4）</span></a> |</li>
                <li class="subscriber"><a href="users.php?role=subscriber">学生<span class="count">（4）</span></a> |</li>
                <li class="supervisor"><a href="users.php?role=supervisor">监赛官<span class="count">（2）</span></a></li>
            </ul>

            <p class="search-box">
                <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                <input type="search" id="user-search-input" name="s" value="">
                <input type="submit" id="search-submit" class="button" value="搜索用户">
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
                <div class="alignleft actions">
                    <label class="screen-reader-text" for="new_role">将角色变更为…</label>
                    <select name="new_role" id="new_role">
                        <option value="">将角色变更为…</option>

                        <option value="supervisor">监赛官</option>
                        <option value="subscriber">学生</option>
                        <option value="contributor">投稿者</option>
                        <option value="author">作者</option>
                        <option value="editor">教练</option>
                        <option value="administrator">管理员</option>		</select>
                    <input type="submit" name="changeit" id="changeit" class="button" value="更改">
                </div>
                <div class="tablenav-pages one-page">
                    <span class="displaying-num">13个项目</span>

                </div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">主体列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                    <tr>
                        <th scope="col" id="username" class="manage-column column-username column-primary sortable desc">
                            <a href="http://127.0.0.1/nlyd/wp-admin/users.php?orderby=login&amp;order=asc"><span>用户名</span><span class="sorting-indicator"></span></a>
                        </th><th scope="col" id="real_name" class="manage-column column-real_name">姓名</th>
                    </tr>
                 </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                    <tr>
                        <td class="username column-username has-row-actions column-primary" data-colname="用户名">
                            <strong><a href="http://127.0.0.1/nlyd/wp-admin/user-edit.php?user_id=5&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">13982242710</a></strong>
                            <br>
                            <div class="row-actions">
                                <span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/user-edit.php?user_id=5&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">编辑</a> | </span>
                                <span class="delete"><a class="submitdelete" href="users.php?action=delete&amp;user=5&amp;_wpnonce=e7103a7740">删除</a> | </span>
                                <span class="view"><a href="http://127.0.0.1/nlyd/wp-admin/users.php?page=users-info&amp;ID=5">资料</a></span>
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="real_name column-real_name" data-colname="姓名"></td>
                    </tr>
                <tfoot>
                    <tr>
                        <th scope="col" class="manage-column column-username column-primary sortable desc">
                            <a href="http://127.0.0.1/nlyd/wp-admin/users.php?orderby=login&amp;order=asc"><span>用户名</span><span class="sorting-indicator"></span></a>
                        </th>
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
                <div class="alignleft actions">
                    <label class="screen-reader-text" for="new_role2">将角色变更为…</label>
                    <select name="new_role2" id="new_role2">
                        <option value="">将角色变更为…</option>

                        <option value="supervisor">监赛官</option>
                        <option value="subscriber">学生</option>
                        <option value="contributor">投稿者</option>
                        <option value="author">作者</option>
                        <option value="editor">教练</option>
                        <option value="administrator">管理员</option>		</select>
                    <input type="submit" name="changeit2" id="changeit2" class="button" value="更改">		</div>
                <div class="tablenav-pages one-page">
                    <span class="displaying-num">13个项目</span>
                </div>
                <br class="clear">
            </div>

            <br class="clear">
        </div>
        <?php
    }

    public function organizeDetails(){

    }
}
new Organize();