<?php
if(!class_exists('Team')){
    class Team{


        public function __construct()
        {
            $this->main();
        }

        public function main(){

            add_action('admin_menu',array($this,'add_submenu'));

            add_action('admin_enqueue_scripts', array($this,'scripts_default'));

        }

        public function add_submenu(){

            add_submenu_page( 'edit.php?post_type=team', '战队成员', '战队成员', 'manage_options', 'team-student', array($this,'student') );

        }

        /**
         * 战队成员
         */
        public function student(){
            $id = intval($_GET['id']);
            $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
            //状态
            $type = isset($_GET['team_type']) ? intval($_GET['team_type']) : 1;
            $user_type = isset($_GET['user_type']) ? intval($_GET['user_type']) : 1; //成员类型 1=学生 2=教练
            $search_str = isset($_GET['search']) ? trim($_GET['search']) : '';
            $search_where = $search_str != '' ? ' AND (u.user_login LIKE "%'.$search_str.'%" OR u.user_mobile LIKE "%'.$search_str.'%" OR u.user_email LIKE "%'.$search_str.'%")' : '';
            global $wpdb;
            //每个状态的数据数量
            $member_num = 0; // 所有战队成员数量
            $into_apply_num = 0; //申请入队数量
            $out_apply_num = 0; //申请退队数量
            $refuse_num = 0; //已拒绝数量
            $out_num = 0; //已退出数量
            $match_num = 0; //教练成员数量
            $student_num = 0; //学生成员数量
            $num_sql = "SELECT COUNT(mt.id) FROM {$wpdb->prefix}match_team AS mt right JOIN {$wpdb->users} AS u ON u.ID=mt.user_id WHERE team_id={$id} AND ";

            $member_num_val = $wpdb->get_var($num_sql."mt.status=2 AND u.ID!=''");
            $into_apply_num_val = $wpdb->get_var($num_sql."mt.status=1 AND u.ID != ''");
            $out_apply_num_val = $wpdb->get_var($num_sql."mt.status=-1 AND u.ID != ''");
            $refuse_num_val = $wpdb->get_var($num_sql."mt.status=-2 AND u.ID != ''");
            $out_num_val = $wpdb->get_var($num_sql."mt.status=-3 AND u.ID != ''");
            $member_num_val && $member_num = $member_num_val;
            $into_apply_num_val && $into_apply_num = $into_apply_num_val;
            $out_apply_num_val && $out_apply_num = $out_apply_num_val;
            $refuse_num_val && $refuse_num = $refuse_num_val;
            $out_num_val && $out_num = $out_num_val;
            $user_type_where = '';
            switch ($type){
                case 1://战队成员
                    $status = 2;
                    $user_type_where = ' AND user_type='.$user_type;
                    $match_num_val = $wpdb->get_var($num_sql."mt.status=2 AND user_type=2 AND u.ID != ''");
                    $student_num_val = $wpdb->get_var($num_sql."mt.status=2 AND user_type=1 AND u.ID != ''");
                    $match_num_val && $match_num = $match_num_val;
                    $student_num_val && $student_num = $student_num_val;
                    break;
                case 2://入队申请
                    $status = 1;
                    break;
                case 3://退队申请
                    $status = -1;
                    break;
                case 4://已拒绝
                    $status = -2;
                    break;
                case 5://已退出
                    $status = -3;
                    break;
                default:
                    exit('状态参数错误');
            }

            $page < 1 && $page = 1;
            $pageSize = 20;
            $start = ($page-1)*$pageSize;
            $sql = 'SELECT SQL_CALC_FOUND_ROWS 
            m.id,u.user_login,u.display_name,u.user_email,u.user_mobile,m.status,m.user_id,
            CASE m.status WHEN -3 THEN "'. "<span style='color:#6a1c25'>已退出</span>" .'" 
            WHEN -2 THEN "'. "<span style='color:rgba(191,34,49,0.91)'>已拒绝</span>" .'" 
            WHEN -1 THEN "'. "<span style='color:#61655b'>退队申请</span>" .'" 
            WHEN 1 THEN "'. "<span style='color:#bf0000'>入队申请</span>" .'" 
            WHEN 2 THEN "'."<span style='color:#0073aa'>战队成员</span>".'" 
            END AS status_title 
            FROM '.$wpdb->prefix.'match_team AS m 
            LEFT JOIN '.$wpdb->users.' AS u ON u.ID=m.user_id 
            WHERE m.team_id='.$id.' AND m.status='.$status.$user_type_where.' AND u.ID !="" '.$search_where.'  
            ORDER BY m.status ASC 
            LIMIT '.$start.','.$pageSize;
            $rows = $wpdb->get_results($sql, ARRAY_A);
            $count = $wpdb->get_row('SELECT FOUND_ROWS() AS count',ARRAY_A)['count'];
            $pageAll = ceil($count/$pageSize);
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
                <h1 class="wp-heading-inline"><?=get_post($id)->post_title?>-战队成员</h1>


                <form method="get" onsubmit="return false;">


                    <input type="hidden" id="_wpnonce" name="_wpnonce" value="31db78f456"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/users.php">	<div class="tablenav top">

                        <br class="clear">
                        <ul class="subsubsub">
                            <li class="member"><a href="<?=admin_url('edit.php?post_type=team&page=team-student&id='.$id.'&team_type=1')?>" class="<?=$type === 1 ? 'current' : ''?>" aria-current="page">战队成员<span class="count">（<?=$member_num?>）</span></a> |</li>
                            <li class="into-apply"><a href="<?=admin_url('edit.php?post_type=team&page=team-student&id='.$id.'&team_type=2')?>" class="<?=$type === 2 ? 'current' : ''?>">入队申请<span class="count">（<?=$into_apply_num?>）</span></a> |</li>
                            <li class="out-apply"><a href="<?=admin_url('edit.php?post_type=team&page=team-student&id='.$id.'&team_type=3')?>" class="<?=$type === 3 ? 'current' : ''?>">退队申请<span class="count">（<?=$out_apply_num?>）</span></a> |</li>
                            <li class="already_refuse"><a href="<?=admin_url('edit.php?post_type=team&page=team-student&id='.$id.'&team_type=4')?>" class="<?=$type === 4 ? 'current' : ''?>">已拒绝<span class="count">（<?=$refuse_num?>）</span></a>|</li>
                            <li class="already_out"><a href="<?=admin_url('edit.php?post_type=team&page=team-student&id='.$id.'&team_type=5')?>" class="<?=$type === 5 ? 'current' : ''?>">已退出<span class="count">（<?=$out_num?>）</span></a></li>
                        </ul>
                        <br class="clear">
                        <?php if($type === 1){ ?>
                        <ul class="subsubsub">
                            <li class="into-apply"><a href="<?=admin_url('edit.php?post_type=team&page=team-student&id='.$id.'&team_type=1&user_type=1')?>" class="<?=$type === 1 && $user_type === 1 ? 'current' : ''?>">学生<span class="count">（<?=$student_num?>）</span></a> |</li>
                            <li class="member"><a href="<?=admin_url('edit.php?post_type=team&page=team-student&id='.$id.'&team_type=1&user_type=2')?>"  class="<?=$type === 1 && $user_type === 2 ? 'current' : ''?>" aria-current="page">教练<span class="count">（<?=$match_num?>）</span></a> |</li>
                        </ul>
                        <br class="clear">
                        <?php } ?>

                        <div class="alignleft actions bulkactions">
                            <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label><select name="action" id="bulk-action-selector-top">
                                <option value="">批量操作</option>
                                <option value="1">同意入队</option>
                                <option value="2">拒绝入队</option>
                                <option value="3">同意退队</option>
                                <option value="4">驳回退队</option>
                            </select>
                            <input type="submit" id="doaction" class="button action  all-btn" value="应用">
                        </div>
                        <p class="search-box">
                            <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                            <input type="text" id="s" placeholder="用户名/手机/邮箱" name="s" value="<?=$search_str?>">
                            <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('edit.php?post_type=team&page=team-student&id='.$id.'&team_type='.$type.'&user_type='.$user_type.'&search=')?>'+document.getElementById('s').value" value="搜索用户">
                        </p>

                        <div class="tablenav-pages one-page">
                            <?=$pageHtml?>
                        </div>
                    <h2 class="screen-reader-text">成员列表</h2><table class="wp-list-table widefat fixed striped users">
                        <thead>
                        <tr>
                            <td id="cb" class="manage-column column-cb check-column">
                                <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                                <input id="cb-select-all-1" type="checkbox">
                            </td>
                            <th scope="col" id="username" class="manage-column column-username column-primary sortable desc">
                                <a href="javascript:;">
                                    <span>用户名</span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th scope="col" id="name" class="manage-column column-name">姓名</th>
                            <th scope="col" id="status" class="manage-column column-status status">状态</th>
                            <th scope="col" id="email" class="manage-column column-email sortable desc">
                                <a href="javascript:;"><span>电子邮件</span><span class="sorting-indicator"></span></a>
                            </th>
                            <th scope="col" id="mobile" class="manage-column column-mobile">手机</th>
                            <th scope="col" id="age" class="manage-column column-age">年龄</th>
                            <th scope="col" id="sex" class="manage-column column-age">性别</th>
                        </tr>
                        </thead>

                        <tbody id="the-list" data-wp-lists="list:user">
                            <?php
                                foreach ($rows as $row){
                                    $usermeta = get_user_meta($row['user_id']);
                                    $user_real_name = isset($usermeta['user_real_name'][0]) ? unserialize($usermeta['user_real_name'][0]) : [];
                                ?>

                                <tr id="user-5" data-id="<?=$row['id']?>">
                                    <th scope="row" class="check-column">
                                        <label class="screen-reader-text" for="user_5"></label>
                                        <input type="checkbox" name="" id="" class="subscriber check" value="<?=$row['id']?>">
                                    </th>
                                    <td class="username column-username has-row-actions column-primary" data-colname="用户名">
                                        <strong><a href="javascript:;"><?=$row['user_login']?></a></strong><br>
                                        <div class="row-actions" data-status="<?=$row['status']?>">
                                            <?php if($row['status'] == -1){ ?>
                                                <span class="edit"><a href="javascript:;" class="agree">同意退队</a> | </span>
                                                <span class="delete"><a class="submitdelete refuse" href="javascript:;"">驳回退队</a> </span>
                                            <?php }else if($row['status'] == 1){ ?>
                                                <span class="edit"><a href="javascript:;" class="agree">同意入队</a> | </span>
                                                <span class="delete"><a class="submitdelete refuse" href="javascript:;"">拒绝入队</a> </span>
                                            <?php }else if($row['status'] == 2){ ?>
<!--                                                 <span class="delete"><a class="submitdelete expel" href="javascript:;"">踢出战队</a> </span>-->
                                            <?php } ?>

                                            <span class=""><a class="submitdelete " href="javascript:;" style="height: 1em;display: inline-block"></a> </span>
                                        </div>
                                        <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                                    </td>
                                    <td class="name column-name" data-colname="姓名"><span aria-hidden="true"><?=isset($user_real_name['real_name']) ? $user_real_name['real_name'] : '-'?></span><span class="screen-reader-text">未知</span></td>

                                    <td class="status column-status status" data-colname="状态"><?=$row['status_title']?></td>
                                    <td class="email column-email" data-colname="电子邮件"><a href="mailto:<?=$row['user_email']?>"><?=$row['user_email']?></a></td>
                                    <td class="mobile column-mobile" data-colname="手机"><?=$row['user_mobile']?></td>
                                    <td class="mobile column-age" data-colname="年龄"><?=isset($user_real_name['real_age']) ? $user_real_name['real_age'] : '-'?></td>
                                    <td class="mobile column-sex" data-colname="性别"><?=isset($usermeta['user_gender'][0]) ? $usermeta['user_gender'][0] : '-'?></td>

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
                                <a href="javascript:;"><span>用户名</span><span class="sorting-indicator"></span></a>
                            </th>
                            <th scope="col" class="manage-column column-name">姓名</th>
                            <th scope="col" class="manage-column column-status status">状态</th>
                            <th scope="col" class="manage-column column-email sortable desc">
                                <a href="javascript:;"><span>电子邮件</span><span class="sorting-indicator"></span></a>
                            </th>
                            <th scope="col" class="manage-column column-mobile">手机</th>
                            <th scope="col" class="manage-column column-age">年龄</th>
                            <th scope="col" class="manage-column column-sex">性别</th>

                        </tr>
                        </tfoot>

                    </table>
                    <div class="tablenav bottom">

                        <div class="alignleft actions bulkactions">
                            <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label><select name="action2" id="bulk-action-selector-bottom">
                                <option value="">批量操作</option>
                                <option value="1">同意入队</option>
                                <option value="2">拒绝入队</option>
                                <option value="3">同意退队</option>
                                <option value="4">驳回退队</option>
                            </select>
                            <input type="submit" id="doaction2" class="button action all-btn" value="应用">
                        </div>
                        <div class="tablenav-pages one-page">
                            <?=$pageHtml?>
                        </div>
                        <br class="clear">
                    </div>
                </form>

                <br class="clear">
            </div>
        <?php }



        /**
         * 引入js/css
         */
        public function scripts_default(){

            $screen       = get_current_screen();
            $screen_id    = $screen ? $screen->id : '';

            if ( in_array( $screen_id, array('settings_page_interface') ) ) {

                //js
                wp_register_script( 'interface-js',plugins_url('/js/interface.js', __FILE__),array(), leo_user_interface_version  );
                wp_enqueue_script( 'interface-js' );

                //css
                wp_register_style( 'interface-css',plugins_url('/css/interface.css', __FILE__),array(), leo_user_interface_version  );
                wp_enqueue_style( 'interface-css' );

            }

            if($_GET['page'] == 'team-student'){
                wp_register_script( 'student-js',match_js_url.'team-student.js',array(), leo_user_interface_version  );
                wp_enqueue_script( 'student-js' );
            }
            echo "<script>var ajax_url='".admin_url('admin-ajax.php' )."';</script>";

        }

        /**
         * Ajax提交
         */
        public function saveInterface(){

            if(empty($_POST['action'])) wp_send_json_error('请求方法错误');

            if(update_option( 'interface_config', $_POST['interface'] )){

                wp_send_json_success('保存成功');

            }else{

                wp_send_json_error('保存失败');
            }
        }

        public function saveLogo(){
            if(empty($_POST['action'])) wp_send_json_error('请求方法错误');
            if(update_option( 'logo_url', $_POST['logo_url'] ) || update_option( 'match_project_default', $_POST['match_project_default'] )){

                wp_send_json_success('保存成功');

            }else{

                wp_send_json_error('保存失败');
            }
        }
    }


}
//define( 'leo_user_interface_path', plugin_dir_path( __FILE__ ) );
//define( 'leo_user_interface_version','1.0' );//样式版本

new Team();