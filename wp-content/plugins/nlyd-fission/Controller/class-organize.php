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
        }
        add_submenu_page('fission','主体详情','主体详情','organize_detail','fission-organize-detail',array($this,'organizeDetails'));
        add_submenu_page('fission','新增主体','新增主体','add_organize','fission-add-organize',array($this,'addOrganize'));
    }

    public function organizeList(){
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">主体列表</h1>

            <a href="<?=admin_url('admin.php?page=fission-add-organize')?>" class="page-title-action">添加主体</a>

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

    /**
     * 主体详情
     */
    public function organizeDetails(){

    }

    /**
     * 新增主体
     */
    public function addOrganize(){

        ?>
        <div class="wrap">
            <h1 id="add-new-user">添加主体</h1>

            <div id="ajax-response"></div>

            <form method="post" name="createuser" id="createuser" class="validate" novalidate="novalidate">
                <input name="action" type="hidden" value="createuser">
                <input type="hidden" id="_wpnonce_create-user" name="_wpnonce_create-user" value="5f6ea9ff44"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/user-new.php"><table class="form-table">
                    <tbody><tr class="form-field form-required">
                        <th scope="row"><label for="user_login">用户名 <span class="description">（必填）</span></label></th>
                        <td><input name="user_login" type="text" id="user_login" value="" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="60"></td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label for="email">电子邮件 <span class="description">（必填）</span></label></th>
                        <td><input name="email" type="email" id="email" value=""></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="first_name">名字 </label></th>
                        <td><input name="first_name" type="text" id="first_name" value=""></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="last_name">姓氏 </label></th>
                        <td><input name="last_name" type="text" id="last_name" value=""></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="url">站点</label></th>
                        <td><input name="url" type="url" id="url" class="code" value=""></td>
                    </tr>
                    <tr class="form-field form-required user-pass1-wrap">
                        <th scope="row">
                            <label for="pass1-text">
                                密码				<span class="description hide-if-js">（必填）</span>
                            </label>
                        </th>
                        <td>
                            <input class="hidden" value=" "><!-- #24364 workaround -->
                            <button type="button" class="button wp-generate-pw hide-if-no-js">显示密码</button>
                            <div class="wp-pwd hide-if-js" style="display: none;">
								<span class="password-input-wrapper show-password">
					<input type="password" name="pass1" id="pass1" class="regular-text strong" autocomplete="off" data-reveal="1" data-pw="stU#zJAWMAtWqN16yiPZzJu4" aria-describedby="pass-strength-result" disabled=""><input type="text" id="pass1-text" name="pass1-text" autocomplete="off" class="regular-text strong" disabled="">
				</span>
                                <button type="button" class="button wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="隐藏密码">
                                    <span class="dashicons dashicons-hidden"></span>
                                    <span class="text">隐藏</span>
                                </button>
                                <button type="button" class="button wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="取消密码修改">
                                    <span class="text">取消</span>
                                </button>
                                <div style="" id="pass-strength-result" aria-live="polite" class="strong">强</div>
                            </div>
                        </td>
                    </tr>
                    <tr class="form-field form-required user-pass2-wrap hide-if-js" style="display: none;">
                        <th scope="row"><label for="pass2">重复密码 <span class="description">（必填）</span></label></th>
                        <td>
                            <input name="pass2" type="password" id="pass2" autocomplete="off" disabled="">
                        </td>
                    </tr>
                    <tr class="pw-weak" style="display: none;">
                        <th>确认密码</th>
                        <td>
                            <label>
                                <input type="checkbox" name="pw_weak" class="pw-checkbox">
                                确认使用弱密码			</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">发送用户通知</th>
                        <td>
                            <input type="checkbox" name="send_user_notification" id="send_user_notification" value="1" checked="checked">
                            <label for="send_user_notification">向新用户发送有关账户详情的电子邮件。</label>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="role">角色</label></th>
                        <td><select name="role" id="role">

                                <option value="supervisor">监赛官</option>
                                <option selected="selected" value="subscriber">学生</option>
                                <option value="contributor">投稿者</option>
                                <option value="author">作者</option>
                                <option value="editor">教练</option>
                                <option value="administrator">管理员</option>			</select>
                        </td>
                    </tr>
                    </tbody></table>


                <p class="submit"><input type="submit" name="createuser" id="createusersub" class="button button-primary" value="添加用户"></p>
            </form>
        </div>
        <?php
    }
}
new Organize();