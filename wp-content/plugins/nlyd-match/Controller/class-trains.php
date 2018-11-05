<?php
if(!class_exists('Trains')){
    class Trains{
        public function __construct()
        {
            add_action( 'admin_menu', array($this,'register_order_menu_page') );
//            add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
        }

        public function register_order_menu_page(){

            if ( current_user_can( 'administrator' ) && !current_user_can( 'match_trains' ) ) {
                global $wp_roles;

                $role = 'match_match_trains';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'match_trains_question';//权限名
                $wp_roles->add_cap('administrator', $role);
            }

            add_submenu_page( 'edit.php?post_type=match', '训练记录', '训练记录', 'match_match_trains', 'match_trains', array($this,'matchTrains'),45);
            add_submenu_page( 'edit.php?post_type=match', '训练记录答题记录', '训练记录答题记录', 'match_trains_question', 'match_trains_question', array($this,'matchTrainsQuestion'),46);
        }

        public function matchTrains(){
            global $wpdb;
            $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
            $searchStr = isset($_GET['s']) ? trim($_GET['s']) : '';
            $searchWhere = '';
            if($searchStr != ''){
                $searchWhere = " WHERE um2.meta_value LIKE '%{$searchStr}%' OR um.meta_value LIKE '%{$searchStr}%'";
            }
            $page < 1 && $page = 1;
            $pageSize = 20;
            $start = ($page-1)*$pageSize;
            $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS utl.user_id,utl.id,utl.genre_id,utl.project_type,utl.surplus_time,utl.my_score,utl.created_time,um.meta_value AS user_real_name,um2.meta_value AS userID 
                           FROM `{$wpdb->prefix}user_train_logs` AS utl 
                           LEFT JOIN `{$wpdb->usermeta}` AS um2 ON um2.user_id=utl.user_id AND um2.meta_key='user_ID' 
                           LEFT JOIN `{$wpdb->usermeta}` AS um ON um.user_id=utl.user_id AND um.meta_key='user_real_name'{$searchWhere} ORDER BY utl.created_time DESC LIMIT {$start},{$pageSize}",ARRAY_A);

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

//            leo_dump($rows);
            ?>
            <div class="wrap">
                <h1 class="wp-heading-inline">训练记录</h1>


                <hr class="wp-header-end">

                <form method="get">


                    <p class="search-box">
                        <label class="screen-reader-text" for="user-search-input">搜索用户:</label>
                        <input type="search" id="search_val" name="s" placeholder=" 姓名/ID" value="<?=$searchStr?>">
                        <input type="button" id="" class="button" onclick="window.location.href='<?=admin_url('edit.php?post_type=match&page=match_trains&s=')?>'+document.getElementById('search_val').value" value="搜索用户">
                    </p>
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
                            <th scope="col" id="real_name" class="manage-column column-real_name column-primary">
                                姓名
                            </th>
                            <th scope="col" id="ID" class="manage-column column-ID">ID</th>
                            <th scope="col" id="project" class="manage-column column-project">训练项目</th>
                            <th scope="col" id="surplus_time" class="manage-column column-surplus_time">剩余时间</th>
                            <th scope="col" id="my_score" class="manage-column column-my_score">分数</th>
                            <th scope="col" id="created_time" class="manage-column column-created_time">创建时间</th>
                        </tr>
                        </thead>

                        <tbody id="the-list" data-wp-lists="list:user">
                        <?php foreach ($rows as $row){
                                $row['post_title'] = '';
                                switch ($row['project_type']){
                                    case 'szzb':
                                        $row['post_title'] = '数字争霸';
                                        break;
                                    case 'zxss':
                                        $row['post_title'] = '正向速算';
                                        break;
                                    case 'nxss':
                                        $row['post_title'] = '逆向速算';
                                        break;
                                    case 'wzsd':
                                        $row['post_title'] = '文章速读';
                                        break;
                                    case 'pkjl':
                                        $row['post_title'] = '扑克接力';
                                        break;
                                    case 'kysm':
                                        $row['post_title'] = '快眼扫描';
                                        break;
                                }
                            ?>
                            <tr data-id="<?=$row['id']?>">
                                <td class="real_name column-real_name has-row-actions column-primary" data-colname="姓名">
                                    <strong><?=!empty($row['user_real_name']) ? unserialize($row['user_real_name'])['real_name'] : '' ?></strong>
                                    <br>
                                    <div class="row-actions">
                                        <span class="edit"><a href="<?=admin_url('edit.php?post_type=match&page=match_trains_question&id='.$row['id'])?>">答题记录</a> </span>
                                    </div>
                                    <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                                </td>
                                <td class="ID column-ID" data-colname="ID"><?=$row['userID']?></td>
                                <td class="project column-project" data-colname="训练项目"><?=$row['post_title']?></td>
                                <td class="surplus_time column-surplus_time" data-colname="剩余时间"><?=$row['surplus_time']?></td>
                                <td class="my_score column-my_score" data-colname="分数"><?=$row['my_score']?></td>
                                <td class="created_time column-created_time" data-colname="创建时间"><?=$row['created_time']?></td>
                            </tr>
                        <?php } ?>

                        <tfoot>
                        <tr>
                            <th scope="col" class="manage-column column-real_name column-primary">
                                姓名
                            </th>
                            <th scope="col" class="manage-column column-ID">ID</th>
                            <th scope="col" class="manage-column column-project">训练项目</th>
                            <th scope="col" class="manage-column column-surplus_time">剩余时间</th>
                            <th scope="col" class="manage-column column-my_score">分数</th>
                            <th scope="col" class="manage-column column-created_time">创建时间</th>
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
         * 训练答题记录
         */
        public function matchTrainsQuestion(){
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $id < 1 && exit('参数错误');
            global $wpdb;
            $row = $wpdb->get_row("SELECT utl.*,um.meta_value AS user_real_name,um2.meta_value AS userID 
                           FROM `{$wpdb->prefix}user_train_logs` AS utl 
                           LEFT JOIN `{$wpdb->usermeta}` AS um2 ON um2.user_id=utl.user_id AND um2.meta_key='user_ID'
                           LEFT JOIN `{$wpdb->usermeta}` AS um ON um.user_id=utl.user_id AND um.meta_key='user_real_name' 
                           WHERE utl.id={$id}",ARRAY_A);
            if($row){
                $row['train_questions'] = json_decode($row['train_questions'], true);
                $row['train_answer'] = json_decode($row['train_answer'], true);
                $row['my_answer'] = json_decode($row['my_answer'], true);
            }

            $row['post_title'] = '';
            switch ($row['project_type']){
                case 'szzb':
                    $row['post_title'] = '数字争霸';
                    break;
                case 'zxss':
                    $row['post_title'] = '正向速算';
                    break;
                case 'nxss':
                    $row['post_title'] = '逆向速算';
                    break;
                case 'wzsd':
                    $row['post_title'] = '文章速读';
                    break;
                case 'pkjl':
                    $row['post_title'] = '扑克接力';
                    break;
                case 'kysm':
                    $row['post_title'] = '快眼扫描';
                    break;
            }
//            leo_dump($row);

            ?>
            <div class="wrap">
                <h1 class="wp-heading-inline"><?!empty($row['user_real_name']) ? unserialize($row['user_real_name'])['real_name'].'-' : ''?><?=$row['post_title']?>-答题记录</h1>


                <hr class="wp-header-end">
                <form method="get">

                    <h2 class="screen-reader-text">用户列表</h2><table class="wp-list-table widefat fixed striped users">
                        <thead>
                        <tr>

                            <th scope="col" id="train_questions" class="manage-column column-train_questions column-primary">训练题目</th>
                            <th scope="col" id="train_answer" class="manage-column column-train_answer">标准答案</th>
                            <th scope="col" id="my_answer" class="manage-column column-my_answer">我的答案</th>
                        </tr>
                        </thead>

                        <tbody id="the-list" data-wp-lists="list:user">
                            <?php foreach ($row['train_questions'] as $k => $t_ques){ ?>
                                <tr>
                                    <?php
                                    $color = '#bf0000';
                                    switch ($row['project_type']){
                                        case 'kysm':
                                            $t_ques = join('  |  ', $t_ques);
                                            $color = $row['train_answer'][$k] == $row['my_answer'][$k] ? '#0a8406' : '#bf0000';
                                            break;
                                        case 'pkjl':
                                            $color = $row['train_answer'][$k] == $row['my_answer'][$k] ? '#0a8406' : '#bf0000';
                                            break;
                                        case 'szzb':
                                            $color = $row['train_answer'][$k] == $row['my_answer'][$k] ? '#0a8406' : '#bf0000';
                                            break;
                                        case 'zxss':
                                            $color = $row['train_answer'][$k] == $row['my_answer'][$k] ? '#0a8406' : '#bf0000';
                                            break;
                                        case 'wzsd':

                                            $str = join('<br />',$row['train_answer'][$k]['problem_select']);
                                            if($row['my_answer'][$k][0] == '-1'){
                                                $row['my_answer'][$k] = '未作答';
                                                $color = '#bf0000';
                                            }elseif($row['train_answer'][$k]['problem_answer'][$row['my_answer'][$k][0]] == '1'){
                                                $row['my_answer'][$k] = $row['train_answer'][$k]['problem_select'][$row['my_answer'][$k][0]];
                                                $color = '#0a8406';
                                            }else{
                                                $row['my_answer'][$k] = $row['train_answer'][$k]['problem_select'][$row['my_answer'][$k][0]];
                                                $color = '#bf0000';
                                            }
                                            $row['train_answer'][$k] = $str;
                                            break;
                                        case 'nxss':
                                            $t_ques = join(' , ', $t_ques);
                                            $row['train_answer'][$k] = $row['train_answer']['examples'][$k];
                                            $color = $row['train_answer']['result'][$k] == 'true' ? '#0a8406' : '#bf0000';
                                            break;
                                    }
                                    ?>
                                    <td class="train_questions column-train_questions" data-colname="训练题目"><?=$t_ques?></td>
                                    <td class="train_answer column-train_answer" data-colname="标准答案"><?=$row['train_answer'][$k]?></td>
                                    <td class="my_answer column-my_answer" data-colname="我的答案">
                                        <span style="color: <?=$color?>"><?=$row['my_answer'][$k]?></span>

                                    </td>

                                </tr>
                            <?php } ?>

                        <tfoot>
                        <tr>

                            <th scope="col" class="manage-column column-train_questions column-primary">训练题目</th>
                            <th scope="col" class="manage-column column-train_answer">标准答案</th>
                            <th scope="col" class="manage-column column-my_answer">我的答案</th>
                        </tr>
                        </tfoot>

                    </table>

                        </div>
                        <br class="clear">
                    </div>
                </form>

                <br class="clear">
            </div>
            <?php
        }

    }

}
//define( 'leo_user_interface_path', plugin_dir_path( __FILE__ ) );
//define( 'leo_user_interface_version','1.0' );//样式版本

new Trains();