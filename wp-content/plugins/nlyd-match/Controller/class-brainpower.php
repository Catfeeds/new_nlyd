<?php
class Brainpower
{
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_order_menu_page') );
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));

    }

    public function register_order_menu_page(){

        add_menu_page('脑力健将', '意见反馈', 'administrator', 'brainpower',array($this,'index'),'dashicons-businessman',99);
        add_submenu_page('brainpower','查看详情','查看详情','administrator','brainpower-intro',array($this,'intro'));
//        add_submenu_page('order','申请退款','申请退款','administrator','order-refund',array($this,'refund'));
//        add_submenu_page('order','我的学员','我的学员','administrator','teacher-student',array($this,'student'));
//        add_submenu_page('order','我的课程','我的课程','administrator','teacher-course',array($this,'course'));
    }
    public function index(){
        global $wpdb;
        $page = ($page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1) < 1 ? 1 : $page;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS id,content,contact FROM '.$wpdb->prefix.'feedback ORDER BY created_time DESC LIMIT '.$start.','.$pageSize ,ARRAY_A);
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
            <h1 class="wp-heading-inline">意见反馈</h1>

                <input type="hidden" id="_wpnonce" name="_wpnonce" value="23f0952b96"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">	<div class="tablenav top">

<!--                    <div class="alignleft actions bulkactions">-->
<!--                        <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label><select name="action" id="bulk-action-selector-top">-->
<!--                            <option value="-1">批量操作</option>-->
<!--                            <option value="delete">删除</option>-->
<!--                        </select>-->
<!--                        <input type="submit" id="doaction" class="button action" value="应用">-->
<!--                    </div>-->
<!--                    <div class="alignleft actions">-->
<!--                        <label class="screen-reader-text" for="new_role">将角色变更为…</label>-->
<!--                        <select name="new_role" id="new_role">-->
<!--                            <option value="">将角色变更为…</option>-->
<!---->
<!--                            <option value="subscriber">学生</option>-->
<!--                            <option value="contributor">投稿者</option>-->
<!--                            <option value="author">作者</option>-->
<!--                            <option value="editor">教练</option>-->
<!--                            <option value="administrator">管理员</option>		</select>-->
<!--                        <input type="submit" name="changeit" id="changeit" class="button" value="更改">		</div>-->
                    <div class="tablenav-pages one-page">
                        <?=$pageHtml?>
                    </div>
                    <br class="clear">
                </div>
                <h2 class="screen-reader-text">用户列表</h2><table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="" class="manage-column column-username column-primary sortable desc">
                            <a href="javascript:;"><span>联系方式</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="" class="manage-column column-name">内容</th>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">
                        <?php foreach ($rows as $row){ ?>
                            <tr id="user-5" data-id="<?=$row['id']?>">
                                <th scope="row" class="check-column">
                                    <label class="screen-reader-text" for="user_5"></label>
                                    <input type="checkbox" name="" id="" class="subscriber" value="">
                                </th>
                                <td class="username column-username has-row-actions column-primary" data-colname="联系方式">
                                    <strong><a href="<?php if(is_mobile()){ ?>tel:<?=$row['contact']?><?php }else{ ?> javascript:; <?php } ?>"><?=$row['contact']?></a></strong><br>
                                    <div class="row-actions">
                                        <span class="delete"><a class="submitdelete rem" href="javascript:;">删除</a> | </span>
                                        <span class="view"><a href="?page=feedback-intro&id=<?=$row['id']?>" aria-label="阅读魏, 海东的文章">查看</a></span>
                                    </div>
                                    <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                                </td>
                                <td class="name column-name" data-colname="内容"><span aria-hidden="true"><?=$row['content']?></span><span class="screen-reader-text"></span></td>

                            </tr>

                        <?php } ?>

                    </tbody>
                    <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-username column-primary sortable desc">
                            <a href="javascript:;"><span>联系方式</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" class="manage-column column-name">内容</th>
                    </tr>
                    </tfoot>

                </table>
                <div class="tablenav bottom">
<!---->
<!--                    <div class="alignleft actions bulkactions">-->
<!--                        <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label><select name="action2" id="bulk-action-selector-bottom">-->
<!--                            <option value="-1">批量操作</option>-->
<!--                            <option value="delete">删除</option>-->
<!--                        </select>-->
<!--                        <input type="submit" id="doaction2" class="button action" value="应用">-->
<!--                    </div>-->
<!--                    <div class="alignleft actions">-->
<!--                        <label class="screen-reader-text" for="new_role2">将角色变更为…</label>-->
<!--                        <select name="new_role2" id="new_role2">-->
<!--                            <option value="">将角色变更为…</option>-->
<!---->
<!--                            <option value="subscriber">学生</option>-->
<!--                            <option value="contributor">投稿者</option>-->
<!--                            <option value="author">作者</option>-->
<!--                            <option value="editor">教练</option>-->
<!--                            <option value="administrator">管理员</option>		</select>-->
<!--                        <input type="submit" name="changeit2" id="changeit2" class="button" value="更改">		</div>-->
<!--                    <br class="clear">-->
<!--                </div>-->
                    <div class="tablenav-pages one-page">
                        <?=$pageHtml?>
                    </div>
            <br class="clear">
        </div>
        <?php
    }

    /**
     * 详情
     */
    public function intro(){
        global $wpdb;
        $id = intval($_GET['id']);
        $row = $wpdb->get_row('SELECT contact,content,images FROM '.$wpdb->prefix.'feedback WHERE id='.$id, ARRAY_A);
//        var_dump($row);
        ?>
            <div id="wpbody-content" aria-label="主内容" tabindex="0">
                <div class="wrap" id="profile-page">

                    <h1 class="wp-heading-inline">反馈详情</h1>



                    <p>
                    <input type="hidden" name="from" value="profile">
                    <input type="hidden" name="checkuser_id" value="1">
                    </p>

                    <table class="form-table">
                    </table>

                    <table class="form-table">
                    <tbody>
                    <tr class="user-description-wrap">
                        <th><label for="">联系方式</label></th>
                        <td>

                               <?=$row['contact']?>
                        </td>
                    </tr>
                    <tr class="user-description-wrap">
                        <th><label for="description">内容</label></th>
                        <td>
                            <div name="description" id="description">

                               <?=$row['content']?>
                            </div>
                            <p class="description"></p>
                        </td>
                    </tr>

                    <tr class="user-profile-picture">
                        <th>资料图片</th>
                        <td>
                        <?php foreach (unserialize($row['images']) as $img){ ?>
                            <img alt="" src="<?=$img?>" srcset="" class="avatar avatar-96 photo avatar-default" width="<?php if (is_mobile()){ ?><?php echo '90%'; }else{ ?><?php echo '60%'; } ?>">		<p class="description"></p>

                        <?php } ?>
                        </td>
                    </tr>

                    </tbody>
                    </table>





                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="user_id" id="user_id" value="3">
                </div>
            <div class="clear"></div></div>
        <?php
    }



    /**
     * 引入当前页面css/js
     */
    public function register_scripts(){
        switch ($_GET['page']){
            case 'feedback':
                wp_register_script('feedback-js',match_js_url.'feedback.js');
                wp_enqueue_script( 'feedback-js' );
//                wp_register_style('list-css',match_css_url.'order-lists.css');
//                wp_enqueue_style( 'list-css' );
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
new Brainpower();