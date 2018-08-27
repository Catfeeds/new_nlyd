<?php
class Match_student {
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_order_menu_page') );
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));

    }

    public function register_order_menu_page(){
        add_menu_page('报名学员', '报名学员', 'administrator', 'match_student',array($this,'studentLists'),'dashicons-businessman',99);
        add_submenu_page('order','退款单','退款单','administrator','order-refundOrder',array($this,'refundOrder'));
//        add_submenu_page('order','申请退款','申请退款','administrator','order-refund',array($this,'refund'));
//        add_submenu_page('order','发货','发货','administrator','order-send',array($this,'sendGoods'));
//        add_submenu_page('order','我的课程','我的课程','administrator','teacher-course',array($this,'course'));
    }

    public function studentLists(){
        $match = get_post($_GET['match_id']);
        global $wpdb;

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?=$match->post_title?>-报名学员</h1>

            <a href="http://127.0.0.1/nlyd/wp-admin/user-new.php" class="page-title-action">添加报名学员</a>

            <hr class="wp-header-end">

            <h2 class="screen-reader-text">过滤用户列表</h2><ul class="subsubsub">
                <li class="all"><a href="users.php" class="current" aria-current="page">全部<span class="count">（28）</span></a> |</li>
                <li class="administrator"><a href="users.php?role=administrator">管理员<span class="count">（1）</span></a> |</li>
                <li class="editor"><a href="users.php?role=editor">教练<span class="count">（20）</span></a> |</li>
                <li class="subscriber"><a href="users.php?role=subscriber">学生<span class="count">（7）</span></a></li>
            </ul>
            <form method="get">

                <p class="search-box">
                    <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                    <input type="search" id="user-search-input" name="s" value="">
                    <input type="submit" id="search-submit" class="button" value="搜索用户"></p>

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="9783a8b758"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">	<div class="tablenav top">

                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label><select name="action" id="bulk-action-selector-top">
                            <option value="-1">批量操作</option>
                            <option value="delete">删除</option>
                        </select>
                        <input type="submit" id="doaction" class="button action" value="应用">
                    </div>
                    <div class="alignleft actions">
                        <label class="screen-reader-text" for="new_role">将角色变更为…</label>
                        <select name="new_role" id="new_role">
                            <option value="">将角色变更为…</option>

                            <option value="subscriber">学生</option>
                            <option value="contributor">投稿者</option>
                            <option value="author">作者</option>
                            <option value="editor">教练</option>
                            <option value="administrator">管理员</option>		</select>
                        <input type="submit" name="changeit" id="changeit" class="button" value="更改">		</div>
                    <h2 class="screen-reader-text">用户列表导航</h2><div class="tablenav-pages"><span class="displaying-num">29个项目</span>
                        <span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
<span class="paging-input">第<label for="current-page-selector" class="screen-reader-text">当前页</label><input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging"><span class="tablenav-paging-text">页，共<span class="total-pages">2</span>页</span></span>
<a class="next-page" href="http://127.0.0.1/nlyd/wp-admin/users.php?paged=2"><span class="screen-reader-text">下一页</span><span aria-hidden="true">›</span></a>
<span class="tablenav-pages-navspan" aria-hidden="true">»</span></span></div>
                    <br class="clear">
                </div>
                <h2 class="screen-reader-text">用户列表</h2><table class="wp-list-table widefat fixed striped users">
                    <thead>

                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="username" class="manage-column column-username column-primary sortable desc">
                            <a href="http://127.0.0.1/nlyd/wp-admin/users.php?orderby=login&amp;order=asc"><span>用户名</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="ID" class="manage-column column-ID">ID</th>
                        <th scope="col" id="name" class="manage-column column-name">姓名</th>
                        <th scope="col" id="sex" class="manage-column column-sex">性别</th>
                        <th scope="col" id="birthday" class="manage-column column-birthday">出⽣⽇期</th>
                        <th scope="col" id="age_group" class="manage-column column-age_group">年龄组别</th>
                        <th scope="col" id="address" class="manage-column column-address">所在地区</th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">电话</th>
                        <th scope="col" id="email" class="manage-column column-email">电子邮件</th>
                        <th scope="col" id="entry_time" class="manage-column column-entry_time">报名时间</th>
                        <th scope="col" id="score" class="manage-column column-score">个⼈⽐赛成绩</th>
                        <th scope="col" id="record" class="manage-column column-record">答题记录</th>

                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">

                        <tr id="user-13">
                            <th scope="row" class="check-column">
                                <label class="screen-reader-text" for="">选择</label>
                                <input type="checkbox" name="users[]" id="" class="subscriber" value="">
                            </th>
                            <td class="username column-username has-row-actions column-primary" data-colname="用户名">
                                <img alt="" src="http://1.gravatar.com/avatar/451d4615203f690faf31abe4f5c5118c?s=32&amp;d=mm&amp;r=g" srcset="http://1.gravatar.com/avatar/451d4615203f690faf31abe4f5c5118c?s=64&amp;d=mm&amp;r=g 2x" class="avatar avatar-32 photo" height="32" width="32">
                                <strong><a href="http://127.0.0.1/nlyd/wp-admin/user-edit.php?user_id=13&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">1111111111</a></strong>
                                <br>
                                <div class="row-actions">
                                    <span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/user-edit.php?user_id=13&amp;wp_http_referer=%2Fnlyd%2Fwp-admin%2Fusers.php">编辑</a> | </span>
                                    <span class="delete"><a class="submitdelete" href="users.php?action=delete&amp;user=13&amp;_wpnonce=9783a8b758">删除</a> | </span>
                                    <span class="view"><a href="http://127.0.0.1/nlyd/author/1111111111/" aria-label="阅读1111111111的文章">查看</a></span>
                                </div>
                                <button type="button" class="toggle-row">
                                    <span class="screen-reader-text">显示详情</span>
                                </button>
                            </td>
                            <td class="role column-ID" data-colname="ID">ID</td>

                            <td class="name column-name" data-colname="姓名"><span aria-hidden="true">—</span><span class="screen-reader-text">未知</span></td>

                            <td class="name column-sex" data-colname="性别"><span aria-hidden="true">—</span><span class="screen-reader-text">男</span></td>
                            <td class="role column-birthday" data-colname="出生日期">出生日期</td>
                            <td class="role column-age_group" data-colname="年龄组别">年龄组别</td>
                            <td class="role column-address" data-colname="所在地区">所在地区</td>
                            <td class="email column-mobile" data-colname="手机"><a href="tel:dddddddddddddd@aa.aa">手机</a></td>
                            <td class="email column-email" data-colname="电子邮件"><a href="mailto:dddddddddddddd@aa.aa">邮件</a></td>
                            <td class="role column-entry_time" data-colname="报名时间">报名时间</td>
                            <td class="role column-score" data-colname="个人比赛成绩">个人比赛成绩</td>
                            <td class="role column-record" data-colname="答题记录">答题记录</td>
                        </tr>

                    </tbody>

                    <tfoot>
                    <tr>


                        <td class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-username column-primary sortable desc">
                            <a href="http://127.0.0.1/nlyd/wp-admin/users.php?orderby=login&amp;order=asc"><span>用户名</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" class="manage-column column-ID">ID</th>
                        <th scope="col" class="manage-column column-name">姓名</th>
                        <th scope="col" class="manage-column column-sex">性别</th>
                        <th scope="col" class="manage-column column-birthday">出⽣⽇期</th>
                        <th scope="col" class="manage-column column-age_group">年龄组别</th>
                        <th scope="col" class="manage-column column-address">所在地区</th>
                        <th scope="col" class="manage-column column-mobile">电话</th>
                        <th scope="col" class="manage-column column-email">电子邮件</th>
                        <th scope="col" class="manage-column column-entry_time">报名时间</th>
                        <th scope="col" class="manage-column column-score">个人比赛成绩</th>
                        <th scope="col" class="manage-column column-record">答题记录</th>
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">

                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label><select name="action2" id="bulk-action-selector-bottom">
                            <option value="-1">批量操作</option>
                            <option value="delete">删除</option>
                        </select>
                        <input type="submit" id="doaction2" class="button action" value="应用">
                    </div>
                    <div class="alignleft actions">
                        <label class="screen-reader-text" for="new_role2">将角色变更为…</label>
                        <select name="new_role2" id="new_role2">
                            <option value="">将角色变更为…</option>

                            <option value="subscriber">学生</option>
                            <option value="contributor">投稿者</option>
                            <option value="author">作者</option>
                            <option value="editor">教练</option>
                            <option value="administrator">管理员</option>		</select>
                        <input type="submit" name="changeit2" id="changeit2" class="button" value="更改">		</div>
                    <div class="tablenav-pages"><span class="displaying-num">29个项目</span>
                        <span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
<span class="screen-reader-text">当前页</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">第1页，共<span class="total-pages">2</span>页</span></span>
<a class="next-page" href="http://127.0.0.1/nlyd/wp-admin/users.php?paged=2"><span class="screen-reader-text">下一页</span><span aria-hidden="true">›</span></a>
<span class="tablenav-pages-navspan" aria-hidden="true">»</span></span></div>
                    <br class="clear">
                </div>
            </form>

            <br class="clear">
        </div>
        <?php
    }


    /**
     * 引入当前页面css/js
     */
    public function register_scripts(){
        switch ($_GET['page']){
            case 'order':
                wp_register_script('list-js',match_js_url.'order-lists.js');
                wp_enqueue_script( 'list-js' );
                wp_register_style('list-css',match_css_url.'order-lists.css');
                wp_enqueue_style( 'list-css' );
                break;
            case 'order-refund':
                wp_register_style('datum-css',match_css_url.'teacher-datum.css');
                wp_enqueue_style( 'datum-css' );
                wp_register_script('list-js',match_js_url.'order-lists.js');
                wp_enqueue_script( 'list-js' );
                break;
        }
        echo "<script>var ajax_url='".admin_url('admin-ajax.php' )."';</script>";
    }
}

new Match_student();