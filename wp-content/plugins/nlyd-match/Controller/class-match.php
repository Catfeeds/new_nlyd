<?php

/**
 * 页面自定义box控制
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 21:44
 */
class Match
{
    private $meta;
    private $project;
    private $team_meta;
    private $temp_key;
    private $post_array;
    private $problem;
    private $alias;
    private $child_count_down;
    private $default_str_length;
    

    public function __construct()
    {
        if(isset($_GET['post']) && $_GET['action'] == 'edit'){

            global $wpdb;
            if(isset($_GET['post'])){
                $row = get_post($_GET['post']);
                if(!empty($row)){
                    $post_type = $row->post_type;
                }
            }

            if($post_type == 'match'){
                //获取比赛meta
                $sql = "select * from {$wpdb->prefix}match_meta_new where match_id = {$_GET['post']}";
                $post_meta = $wpdb->get_row($sql,ARRAY_A);
                $this->meta = $post_meta;
            }elseif ($post_type == 'team'){
                //获取战队基本资料
                $sql = " select * from {$wpdb->prefix}team_meta where team_id = {$_GET['post']} ";
                $this->team_meta = $wpdb->get_row($sql,ARRAY_A);
                //print_r($this->team_meta);
            }elseif ($post_type == 'problem'){
                //获取题目选项以及答案
                $sql = " select * from {$wpdb->prefix}problem_meta where problem_id = {$_GET['post']} order by id asc ";
                $this->problem = $wpdb->get_results($sql,ARRAY_A);
            }
            elseif ($post_type == 'grading'){
                //获取题目选项以及答案
                $sql = " select * from {$wpdb->prefix}grading_meta where grading_id = {$_GET['post']} order by id asc ";
                $this->grading = $wpdb->get_row($sql,ARRAY_A);
                //print_r($this->grading );
            }

        }

        add_action('admin_menu',array($this,'add_submenu'));

    }

    public function add_submenu(){

        if ( current_user_can( 'administrator' ) && !current_user_can( 'match' ) ) {
            global $wp_roles;

            $role = 'match_more_list';//权限名
            $wp_roles->add_cap('administrator', $role);

            $role = 'match_bonus_list';//权限名
            $wp_roles->add_cap('administrator', $role);
        }

        add_submenu_page( 'edit.php?post_type=match', '轮数设置', '轮数设置', 'match_more_list', 'match_more', array($this,'match_more_list'),45);
        add_submenu_page( 'edit.php?post_type=match', '监赛列表', '监赛列表', 'match_more_list', 'match_prison', array($this,'match_prison_list'),45);
        add_submenu_page( 'edit.php?post_type=match', '奖金设置', '奖金设置', 'match_bonus_list', 'match_bonus', array($this,'match_bonus_list'),45);

    }

    /**
     * 监赛列表
     */

    public function match_prison_list(){
        global $wpdb;
        $wap = array();
        $where = '';
        if(isset($_GET['match_title'])){
            $wap[] = " b.post_title like '%{$_GET['post_title']}%' ";
        }
        if(isset($_GET['project_title'])){
            $wap[] = " c.post_title like '%{$_GET['post_title']}%' ";
        }
        if(isset($_GET['student_name'])){
            $wap[] = " a.student_name like '%{$_GET['student_name']}%' ";
        }
        if(isset($_GET['student_name'])){
            $wap[] = " a.seat_number = {$_GET['seat_number']} ";
        }

        if(!empty($wap)){
            $where = ' where '.join($wap,' and ');
        }
        $page = ($page = isset($_GET['paged']) ? intval($_GET['paged']) : 1) < 1 ? 1 : $page;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;

        //获取轮数列表
        $sql = "select SQL_CALC_FOUND_ROWS a.id,a.match_more,a.student_name,a.seat_number,b.post_title match_title,c.post_title project_title,a.created_time
                from {$wpdb->prefix}prison_match_log a
                left join {$wpdb->prefix}posts b on a.match_id = b.ID
                left join {$wpdb->prefix}posts c on a.project_id = c.ID
                $where
                order by a.id desc
                limit {$start},{$pageSize}
                ";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($rows);
        $count = $total = $wpdb->get_row('select FOUND_ROWS() count',ARRAY_A);
        $pageAll = ceil($count['count']/$pageSize);
        $pageHtml = paginate_links( array(
            'base' => add_query_arg( 'paged', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $pageAll,
            'current' => $page
        ));
        //print_r($sql);
        include_once match_view_path.'match_prison_list.php';

    }


    /**
     * 特色高亮设置
     */
    public function highlighter_meta_box($post){
        $color = get_post_meta($post->ID,'genre_highlight')[0];
    ?>
        <div id="picker"></div>
        <script>
           $('#picker').colpick({
            color:'<?=$color?>',
            flat:true,
            layout:'hex',
            submit:0,
            });
        </script>
    <?php }

    /**
     * 轮数设置
     */

    public function match_more_list(){
        global $wpdb;
        //获取轮数列表
        $sql = "select id,more,
                DATE_FORMAT(start_time,'%Y-%m-%d %H:%i') start_time,
                DATE_FORMAT(end_time,'%Y-%m-%d %H:%i') end_time,
                case status
                when 1 then '未开始'
                when 2 then '进行中'
                else '已结束'
                end status_cn
                from {$wpdb->prefix}match_project_more 
                where match_id = {$_GET['post_id']} and project_id = {$_GET['project_id']} order by more asc";
        $rows = $wpdb->get_results($sql,ARRAY_A);
        //print_r($sql);
        include_once match_view_path.'match_more_list.php';

    }

    /**
     * 奖金设置
     */
    public function match_bonus_list(){
        global $wpdb;
        $page = isset($_GET['cpage']) ? intval($_GET['cpage']) : 1;
        $page < 1 && $page = 1;
        $pageSize = 20;
        $start = ($page-1)*$pageSize;
        $rows = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}match_bonus_tmp LIMIT {$start},{$pageSize}", ARRAY_A);

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
//        var_dump($rows);
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">奖金设置</h1>
            <hr class="wp-header-end">
            <form method="get">
                <p class="search-box">
                </p>
                <div class="alignleft actions">

                </div>
                <div class="tablenav top">

                    <div class="tablenav-pages"><span class="displaying-num"><?=$count['count']?>个项目</span>
                        <?=$pageHtml?>
                    </div>
                    <a href="javascript:;" class="page-title-action" id="add-tmp">新增</a>
                    <br class="clear">

                </div>
                <br class="clear">
               
                <style type="text/css">
                    #the-list input[type="text"]{
                        width: 100%;
                    }
                    #the-list .btn-none{
                        display: none;
                    }
                </style>
                <h2 class="screen-reader-text">奖金列表</h2><table class="wp-list-table widefat fixed striped users">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="tmp_name" class="manage-column column-tmp_name column-primary">
                            <span>名称</span>
                        </th>
                        <th scope="col" id="project1" class="manage-column column-project1">单项冠军</th>
                        <th scope="col" id="project2" class="manage-column column-project2">单项亚军</th>
                        <th scope="col" id="project3" class="manage-column column-project3">单项季军</th>
                        <th scope="col" id="category1" class="manage-column column-category1">大类冠军</th>
                        <th scope="col" id="category2" class="manage-column column-category2">大类亚军</th>
                        <th scope="col" id="category3" class="manage-column column-category3">大类季军</th>
                        <th scope="col" id="category_excellent" class="manage-column column-category_excellent">大类优秀选手</th>
                        <th scope="col" id="category1_age" class="manage-column column-category1_age">大类年龄冠军</th>
                        <th scope="col" id="category2_age" class="manage-column column-category2_age">大类年龄亚军</th>
                        <th scope="col" id="category3_age" class="manage-column column-category3_age">大类年龄季军</th>
                        <th scope="col" id="operation" class="manage-column column-operation">操作</th>
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:user">
                    <tr data-id="0" style="display: none">
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text"></label>
                            <input type="checkbox" name="users[]" class="subscriber" value="">
                        </th>
                        <td class="tmp_name column-tmp_name has-row-actions column-primary" data-colname="名称">
                            <strong>
                                <input type="text" name="tmp_name" value="">
                            </strong>

                            <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                        </td>
                        <td class="project1 column-project1" data-colname="单项冠军">
                            <input type="text" name="project1" value="">
                        </td>
                        <td class="project2 column-project2" data-colname="单项亚军">
                            <input type="text" name="project2" value="">
                        </td>
                        <td class="project3 column-project3" data-colname="单项季军">
                            <input type="text" name="project3" value="">
                        </td>
                        <td class="category1 column-category1" data-colname="大类冠军">
                            <input type="text" name="category1" value="">
                        </td>
                        <td class="category2 column-category2" data-colname="大类亚军">
                            <input type="text" name="category2" value="">
                        </td>
                        <td class="category3 column-category3" data-colname="大类季军">
                            <input type="text" name="category3" value="">
                        </td>
                        <td class="category_excellent column-category_excellent" data-colname="大类优秀选手">
                            <input type="text" name="category_excellent" value="">
                        </td>
                        <td class="category1_age column-category1_age" data-colname="大类年龄冠军">
                            <input type="text" name="category1_age" value="">
                        </td>
                        <td class="category2_age column-category2_age" data-colname="大类年龄亚军">
                            <input type="text" name="category2_age" value="">
                        </td>
                        <td class="category3_age column-category3_age" data-colname="大类年龄季军">
                            <input type="text" name="category3_age" value="">
                        </td>
                        <td class="category3_age column-category3_age" data-colname="大类年龄季军">
                            <button type="button" class="button sub-btn">提交</button>
                            <button type="button" class="button del-btn">删除</button>
                        </td>
                    </tr>
                    <?php foreach ($rows as $row){ ?>
                        <tr data-id="<?=$row['id']?>">
                            <th scope="row" class="check-column">
                                <label class="screen-reader-text"></label>
                                <input type="checkbox" name="users[]" class="subscriber" value="<?=$row['id']?>">
                            </th>
                            <td class="tmp_name column-tmp_name has-row-actions column-primary" data-colname="名称">
                                <strong>
                                    <input type="text" name="tmp_name" disabled="disabled" value="<?=$row['bonus_tmp_name']?>">
                                </strong>

                                <button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button>
                            </td>
                            <td class="project1 column-project1" data-colname="单项冠军">
                                <input type="text" name="project1" disabled="disabled" value="<?=$row['project1']?>">
                            </td>
                            <td class="project2 column-project2" data-colname="单项亚军">
                                <input type="text" name="project2" disabled="disabled" value="<?=$row['project2']?>">
                            </td>
                            <td class="project3 column-project3" data-colname="单项季军">
                                <input type="text" name="project3" disabled="disabled" value="<?=$row['project3']?>">
                            </td>
                            <td class="category1 column-category1" data-colname="大类冠军">
                                <input type="text" name="category1" disabled="disabled" value="<?=$row['category1']?>">
                            </td>
                            <td class="category2 column-category2" data-colname="大类亚军">
                                <input type="text" name="category2" disabled="disabled" value="<?=$row['category2']?>">
                            </td>
                            <td class="category3 column-category3" data-colname="大类季军">
                                <input type="text" name="category3" disabled="disabled" value="<?=$row['category3']?>">
                            </td>
                            <td class="category_excellent column-category_excellent" data-colname="大类优秀选手">
                                <input type="text" name="category_excellent" disabled="disabled" value="<?=$row['category_excellent']?>">
                            </td>
                            <td class="category1_age column-category1_age" data-colname="大类年龄冠军">
                                <input type="text" name="category1_age" disabled="disabled" value="<?=$row['category1_age']?>">
                            </td>
                            <td class="category2_age column-category2_age" data-colname="大类年龄亚军">
                                <input type="text" name="category2_age" disabled="disabled" value="<?=$row['category2_age']?>">
                            </td>
                            <td class="category3_age column-category3_age" data-colname="大类年龄季军">
                                <input type="text" name="category3_age" disabled="disabled" value="<?=$row['category3_age']?>">
                            </td>
                            <td class="category3_age column-category3_age" data-colname="大类年龄季军">
                                <button type="button" class="button sub-btn btn-none">提交</button>
                                <button type="button" class="button cancel-btn btn-none">取消</button>
                                <button type="button" class="button edit-btn">编辑</button>
                                <button type="button" class="button del-btn">删除</button>
                            </td>
                        </tr>
                    <?php } ?>

                    <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-tmp_name column-primary">
                            <span>名称</span>
                        </th>
                        <th scope="col" class="manage-column column-project1">单项冠军</th>
                        <th scope="col" class="manage-column column-project2">单项亚军</th>
                        <th scope="col" class="manage-column column-project3">单项季军</th>
                        <th scope="col" class="manage-column column-category1">大类冠军</th>
                        <th scope="col" class="manage-column column-category2">大类亚军</th>
                        <th scope="col" class="manage-column column-category3">大类季军</th>
                        <th scope="col" class="manage-column column-category_excellent">大类优秀选手</th>
                        <th scope="col" class="manage-column column-category1_age">大类年龄冠军</th>
                        <th scope="col" class="manage-column column-category2_age">大类年龄亚军</th>
                        <th scope="col" class="manage-column column-category3_age">大类年龄季军</th>
                        <th scope="col" class="manage-column column-operation">操作</th>
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
            </form>
            
            <br class="clear">
            <script type="text/javascript">

                jQuery(document).ready(function($) {
                    $('.sub-btn').on('click', function () {
                        var _tr = $(this).closest('tr');
                        var id = _tr.attr('data-id');
                        var project1 = _tr.find('input[name="project1"]').val();
                        var project2 = _tr.find('input[name="project2"]').val();
                        var project3 = _tr.find('input[name="project3"]').val();
                        var category1 = _tr.find('input[name="category1"]').val();
                        var category2 = _tr.find('input[name="category2"]').val();
                        var category3 = _tr.find('input[name="category3"]').val();
                        var category_excellent = _tr.find('input[name="category_excellent"]').val();
                        var category1_age = _tr.find('input[name="category1_age"]').val();
                        var category2_age = _tr.find('input[name="category2_age"]').val();
                        var category3_age = _tr.find('input[name="category3_age"]').val();
                        var tmp_name = _tr.find('input[name="tmp_name"]').val();

                        var data = {'action':'updateBonusTmp','id':id,'project1':project1,'project2':project2,'project3':project3,'category1':category1,'category2':category2,'category3':category3
                        ,'category_excellent':category_excellent,'category1_age':category1_age,'category2_age':category2_age,'category3_age':category3_age,'tmp_name':tmp_name}
                        if(id != ''){
                            if(!confirm('修改后关联此设置的比赛都将受到影响,是否确定提交?')) return false;
                        }
                        $.ajax({
                            url : ajaxurl,
                            type : 'post',
                            dataType : 'json',
                            data : data,
                            success : function (response) {
                                if(response['success'] == true){
                                    window.location.reload();
                                }else{
                                    alert(response.data.info)
                                }
                            }, error : function () {
                                alert('请求失败')
                            }
                        });
                    });
                    $('.edit-btn').on('click', function () {
                        $(this).closest('td').find('.sub-btn').removeClass('btn-none');
                        $(this).closest('td').find('.cancel-btn').removeClass('btn-none');
                        $(this).closest('td').find('.del-btn').addClass('btn-none');
                        $(this).addClass('btn-none');
                        $(this).closest('tr').find('input[type="text"]').prop('disabled', false)
                    });
                    $('.cancel-btn').on('click', function () {
                        $(this).closest('td').find('.sub-btn').addClass('btn-none');
                        $(this).closest('td').find('.edit-btn').removeClass('btn-none');
                        $(this).closest('td').find('.del-btn').removeClass('btn-none');
                        $(this).addClass('btn-none');
                        $(this).closest('tr').find('input[type="text"]').prop('disabled', true)
                    });
                    $('.del-btn').on('click', function () {
                        var _tr = $(this).closest('tr');
                        var id = _tr.attr('data-id');
                        if(id == '0') {
                            _tr.remove();
                            return false;
                        }
                        if(confirm('是否确定删除此设置?使用此设置的比赛将无法生成奖金明细')){
                            $.ajax({
                                url : ajaxurl,
                                dataType : 'json',
                                data : {'action' : 'delBonusTmp','id':id},
                                type : 'post',
                                success : function (response) {
                                    if(response['success'] == false){
                                        alert(response.data.info);
                                    }else{
                                        _tr.remove();
                                    }
                                }, error : function () {
                                    alert('请求失败')
                                }
                            });
                        }

                    });
                    $('#add-tmp').on('click', function () {
                        var _html = $('#the-list').find('tr').first().clone(true);
                        _html.css('display','table-row')
                        $('#the-list').append(_html);
                    });
                })

            </script>
        </div>
        <?php
    }

    /**
     * 比赛开关控制
     */
    public function match_switch_meta_box($post){
        $match_switch = get_post_meta($post->ID,'default_match_switch')[0];
        //var_dump($post->post_title);
    ?>
      <div class="layui-form-item" pane="">
        <label class="layui-form-label" style="text-align:left">自动开赛</label>
        <div class="layui-input-block">
            <input type="checkbox" <?= $match_switch=='ON' || empty($post->post_title)?'checked':'';?> lay-skin="switch"  name="match_switch" value="ON"  lay-text="是|否">
        </div>
    </div>
        <!-- 自动发布 <input type="checkbox" value="on" name="match_switch" /> -->
    <?php }

    /**
     * 问题设置box
     */
    public function problem_meta_box(){
        //print_r($this->problem);
        ?>
        <p>选项设置 <span style="float:right" id="addSelect">新增</span></p>
        <ul class="select-list">
            <?php if(!empty($this->problem)):?>
            <?php foreach ($this->problem as $k => $val){ ?>
            <li class="select-li">
                <input class="select-checkBox" type="checkbox" name="problem[<?=$k?>][answer]" value="1" <?=$val['problem_answer'] == 1 ? 'checked' : '';?> >
                <input class="select-input" type="text" name="problem[<?=$k?>][select]" value="<?=$val['problem_select']?>">
                <span class="delSelect">删除</span>
            </li>
            <?php } ?>
            <?php endif;?>
            <!--<li><input type="checkbox" name="problem[0][answer]" value="1"><input type="text" name="problem[0][select]" value="大方"></li>
            <li><input type="checkbox" name="problem[1][answer]" value="1"><input type="text" name="problem[1][select]" value="激情"></li>
            <li><input type="checkbox" name="problem[2][answer]" value="1"><input type="text" name="problem[2][select]" value="热忱"></li>
            <li><input type="checkbox" name="problem[3][answer]" value="1"><input type="text" name="problem[3][select]" value="热情"></li>-->
        </ul>
    <?php }


    /**
     * 去绑定题目设置box
     */
    public function go_problem_meta_box($post){

        //获取当前文章的所有题目
        global $wpdb;
        $sql = "select * from {$wpdb->prefix}posts where post_type = 'problem' and post_status = 'publish' and post_parent = {$post->ID}";
        //var_dump($sql);
        $rows = $wpdb->get_results($sql,ARRAY_A);
        $url = admin_url('post-new.php?post_type=problem&question_id='.$_GET['post']);
        ?>
        <?php if(!empty($rows)):?>
        <?php foreach ($rows as $v){
            $href = admin_url('post.php?action=edit&post='.$v['ID']);
        ?>
        <p>
            <a href="<?=$href?>"><?=$v['post_title']?></a>
        </p>
        <?php } ?>
        <?php endif;?>
        <p><a href="<?=$url?>">新增问题 Go</a></p>

    <?php }


    /**
     * 绑定题目类别
     */
    public function set_question_genre($post){

        //tax_input[question_genre][]
        //查询题目类型
        $args = array(
            'taxonomy' => 'question_genre', //自定义分类法
            'pad_counts' => false,
            'hide_empty' => false,
        );
        $category = get_categories($args);
        $questionTypeArr = array();

        if(!empty($category)){
            foreach ($category as $k => $v){
                if ($post->post_type == 'question' && in_array($v->slug,array('cn-match-question','en-match-question','cn-test-question','en-test-question','en-grading-question','cn-grading-question','en-grading-test-question','cn-grading-test-question'))){
                    $questionTypeArr[] = array(
                        'term_id'=>$v->term_id,
                        'name'=>$v->name,
                    );
                }
            }
        }

        //获取文章分类
        global $wpdb;
        $category_id = $wpdb->get_var("select term_taxonomy_id from {$wpdb->prefix}term_relationships where object_id = {$post->ID} ");

        ?>
        <?php if(!empty($questionTypeArr)){
        ?>
        <select name="tax_input[question_genre][]" id="">
            <?php foreach ($questionTypeArr as $question){ ?>
                <option value="<?=$question['term_id']?>" <?=$question['term_id']==$category_id ? 'selected' : ''?> ><?=$question['name']?></option>
            <?php } ?>
        </select>
        <?php }else{?>
        <p><a href="<?=admin_url('/edit-tags.php?taxonomy=question_genre&post_type=question')?>">新增题库类型 Go</a></p>
        <?php }?>
    <?php }


    /**
     * 题目绑定设置box
     */
    public function question_meta_box($post){
        if(isset($_GET['question_id']) || $post->post_parent){
            $post_id = isset($_GET['question_id']) ? $_GET['question_id'] : $post->post_parent;
            $question = get_post($post_id);
           // print_r($question);
        }
        ?>
        <div class="layui-form-item">
            <label class="layui-form-label">绑定题目</label>
            <div class="layui-input-block">
                <select class="js-data-example-ajax" name="parent_id" style="width: 100%" data-action="get_question_list" data-type="problem">
                    <?php if(!empty($question)):?><option value="<?=$question->ID?>" selected="selected"><?=$question->post_title?></option><?php endif;?>
                </select>
            </div>
        </div>

    <?php }


    /**
     * 别名设置box
     */
    public function alias_meta_box(){
        $project_alias = get_post_meta($_GET['post'],'project_alias');
        ?>
        <div class="layui-form-item">
            <label class="layui-form-label">项目别名</label>
            <div class="layui-input-block">
                <input type="text" value="<?=!empty($project_alias[0])?$project_alias[0]:'';?>" name="project_alias" class="layui-input" placeholder="项目别名">
            </div>
        </div>

    <?php }

    /**
     * 子项倒计时设置box
     */
    public function child_count_down_meta_box(){

        if($this->alias =='zxss'){ ?>
        <div class="layui-form-item">
            <label class="layui-form-label">连加运算(分)</label>
            <div class="layui-input-block">
                <input type="text" value="<?=$this->child_count_down['even_add'] ?>" name="child_count_down[even_add]" class="layui-input" placeholder="连加运算">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">加减运算(分)</label>
            <div class="layui-input-block">
                <input type="text" value="<?=$this->child_count_down['add_and_subtract'] ?>" name="child_count_down[add_and_subtract]" class="layui-input" placeholder="加减运算">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">乘除运算(分)</label>
            <div class="layui-input-block">
                <input type="text" value="<?=$this->child_count_down['wax_and_wane'] ?>" name="child_count_down[wax_and_wane]" class="layui-input" placeholder="乘除运算">
            </div>
        </div>
        <!-- <p>连加运算<input  value="<?=$this->child_count_down['even_add']?>" type="text" name="child_count_down[even_add]"/>默认单位为分</p>
        <p>加减运算<input  value="<?=$this->child_count_down['add_and_subtract']?>" type="text" name="child_count_down[add_and_subtract]"/>默认单位为分</p>
        <p>乘除运算<input  value="<?=$this->child_count_down['wax_and_wane']?>" type="text" name="child_count_down[wax_and_wane]"/>默认单位为分</p> -->
        <?php }else{ ?>
        <div class="layui-form-item">
            <label class="layui-form-label">子项比赛用时(秒)</label>
            <div class="layui-input-block">
                <input type="text" value="<?=$this->child_count_down ?>" name="child_count_down" class="layui-input" placeholder="子项比赛用时">
            </div>
        </div>
        <?php }?>
    <?php }

    /**
     * 初始字符位数设置box
     */
    public function str_bit_set_meta_box(){ 
        
    ?>
        <div class="layui-form-item">
            <label class="layui-form-label">初始长度</label>
            <div class="layui-input-block">
                <input type="text" value="<?=$this->default_str_length ?>" name="default_str_length" class="layui-input" placeholder="初始长度">
            </div>
        </div>

    <?php }

    /**
     * 比赛顺序设置box
     */
    public function order_review_meta_box(){

        $order = unserialize($this->meta['match_category_order']);
        //print_r($order);
        $args = array(
            'post_type' => array('match-category'),
            'post_status' => array('publish'),
            'order' => 'DESC',
            'orderby' => 'ID',
        );
        $the_query = new WP_Query( $args );
        if (!empty($the_query->posts)) : ?>
            <ul name="parent_id">
                <?php
                foreach ($the_query->posts as $v){
                    echo '<li class=" ">'.$v->post_title.' 排序<input type="text" name="match[match_category_order]['.$v->ID.']" value="'.$order[$v->ID].'" /></li>';
                }
                ?>
            </ul>
        <?php endif; ?>
    <?php }

    /**
     * 战队队长设置box
     */
    public function team_leader_meta_box($post){
        $team_leader = get_user_meta($this->team_meta['team_leader'],'user_real_name');
    ?>

    <div class="layui-form-item">
        <label class="layui-form-label">队长设置</label>
        <div class="layui-input-block">
            <select style="width: 100%"  class="js-data-example-ajax" name="team[team_leader]" lay-search="" data-action="getMemberByWhere" data-type="team_leader" team-id="<?=$post->ID?>">
                <?php if(!empty($team_leader)):?><option value="<?=$this->team_meta['team_leader']?>" selected="selected"><?=$team_leader[0]['real_name']?></option><?php endif;?>
            </select>
        </div>
    </div>
        <!-- <p>队长设置
            <select class="js-data-example-ajax" name="team[team_leader]" style="width: 90%" data-action="getMemberByWhere" data-type="team_leader" team-id="<?=$post->ID?>">
                <?php if(!empty($team_leader)):?><option value="<?=$this->team_meta['team_leader']?>" selected="selected"><?=$team_leader[0]['real_name']?></option><?php endif;?>
            </select>
        </p> -->
    <?php }


    /**
     * 战队最大人数设置box
     */
    public function team_number_meta_box(){ ?>
    <div class="layui-form-item">
        <label class="layui-form-label">最大人数</label>
        <div class="layui-input-block">
            <input  value="<?=$this->team_meta['max_number']?>" type="text" name="team[max_number]" class="layui-input" placeholder="加减运算"/>
        </div>
    </div>
        <!-- <p>最大人数<input  value="<?=$this->team_meta['max_number']?>" type="text" name="team[max_number]"/></p> -->

    <?php }

    /**
     * 战队口号/国籍设置
     */
    public function team_nationality_meta_box($post){
        global $wpdb;
        $rows = $wpdb->get_results("select id,title,code from {$wpdb->prefix}world");
        //print_r($rows);
        if ( ! empty($rows) ) :
            ?>
            <div class="layui-form-item">
                <label class="layui-form-label post-attributes-label" for="parent_id">国籍</label>
                <div class="layui-input-block">
                    <select class="" name="team[team_world]" style="width:100%;display:block">
                    <option value="">(选择国籍)</option>
                    <?php
                    foreach ($rows as $v){
                        $selected = $this->team_meta['team_world'] == $v->title ? "selected":" ";
                        echo '<option value="'.$v->title.'" '.$selected.'>'.$v->title.'</option>';
                    }
                    ?>
                    </select>
                </div>
            </div>
            <!-- <label class="post-attributes-label" for="parent_id">国籍</label>
                <select name="team[team_world]">
                <option value="">(选择国籍)</option>
                <?php
                foreach ($rows as $v){
                    $selected = $this->team_meta['team_world'] == $v->title ? "selected":" ";
                    echo '<option value="'.$v->title.'" '.$selected.'>'.$v->title.'</option>';
                }
                ?>
            </select> -->
        <?php
        endif;
        $team_director = get_user_meta($this->team_meta['team_director'],'user_real_name');
        ?>
        <div class="layui-form-item">
            <label class="layui-form-label">战队口号</label>
            <div class="layui-input-block">
                <input type="text" value="<?=$this->team_meta['team_slogan'];?>" name="team[team_slogan]" class="layui-input" placeholder="战队口号">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">战队负责人</label>
            <div class="layui-input-block">
                <select class="js-data-example-ajax" name="team[team_director]" style="width: 100%" data-action="getMemberByWhere" data-type="team_director" team-id="<?=$post->ID?>">
                    <?php if(!empty($team_director)):?><option value="<?=$this->team_meta['team_slogan']?>" selected="selected"><?=$team_director[0]['real_name']?></option><?php endif;?>
                </select>
            </div>
        </div>
        <!-- <p>战队负责人
            <select class="js-data-example-ajax" name="team[team_director]" style="width: 90%" data-action="getMemberByWhere" data-type="team_director" team-id="<?=$post->ID?>">
                <?php if(!empty($team_director)):?><option value="<?=$this->team_meta['team_slogan']?>" selected="selected"><?=$team_director[0]['real_name']?></option><?php endif;?>
            </select>
        </p> -->

    <?php }


    /**
     * 父类选择box
     */
    public function page_attributes_meta_box($post) {

        if($post->post_type == 'project') $parent = 'match-category';
        if($post->post_type == 'match-category') $parent = 'genre';

        $args = array(
            'post_type' => array($parent),
            'post_status' => array('publish'),
            'order' => 'DESC',
        );
        $the_query = new WP_Query( $args );
        //print_r($the_query);die;
        if ( !empty($the_query->posts) ) :
            ?>
            <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="parent_id">父级</label></p>
            <select name="parent_id">
                <option value="">(无父级)</option>
                <?php
                foreach ($the_query->posts as $v){
                    $selected = $v->ID == $post->post_parent ? "selected":" ";
                    echo '<option value="'.$v->ID.'" '.$selected.'>'.$v->post_title.'</option>';
                }
                ?>
            </select>
        <?php
        endif; ?>
        <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="menu_order">排序</label></p>
        <input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr( $post->menu_order ); ?>" />
    <?php }


    /**
     * 口号类型设置box
     */
    public function slogan_review_meta_box($post){

        global $wpdb;
        $match_genre = $wpdb->get_results("select a.ID,a.post_title from {$wpdb->prefix}posts a 
                                  left join {$wpdb->prefix}postmeta b on a.ID = b.post_id and b.meta_key='project_alias'
                                  where a.post_type = 'genre' and a.post_status = 'publish' and b.meta_value in('mental_world_cup','digital_brain_king','counting_brain_marathon')");
        //print_r($match_genre);
        /*$args = array(
            'post_type' => array('genre'),
            'post_status' => array('publish'),
            'order' => 'DESC',
        );
        $the_query = new WP_Query( $args );
        global $wpdb;*/
        $lists = $wpdb->get_results("SELECT id,bonus_tmp_name FROM {$wpdb->prefix}match_bonus_tmp", ARRAY_A);

        $rows = $wpdb->get_results("select * from {$wpdb->prefix}zone_match_role where status = 1 and role_type = 'match'");
        //print_r($rows);
    ?>
        <div class="layui-form-item">
            <label class="layui-form-label">比赛场景</label>
            <div class="layui-input-block">
                <select name="match[match_scene]" lay-search="">
                    <option value="">请选择</option>
                    <?php if(!empty($rows)){?>
                    <?php foreach ($rows as $x){ ?>
                    <option value="<?=$x->id?>" <?=$this->meta['match_scene'] == $x->id ? 'selected' : '';?> ><?=$x->role_name?></option>
                    <?php } ?>
                    <?php }else{ ?>
                        <option value="1" <?=$this->meta['match_scene'] == 1 ? 'selected' : '';?> >正式比赛</option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">比赛口号</label>
            <div class="layui-input-block">
            <input placeholder="比赛口号" class="layui-input" value="<?=$this->meta['match_slogan'];?>" type="text" name="match[match_slogan]">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">比赛类型</label>
            <div class="layui-input-block">
            <?php if(!empty($match_genre)){ ?>
                <select name="match[match_genre]" lay-search="">
                    <option value="">请选择</option>
                    <?php
                    foreach ($match_genre as $v){
                        $selected = $v->ID == $this->meta['match_genre'] ? "selected":" ";
                        echo '<option value="'.$v->ID.'" '.$selected.'>'.$v->post_title.'</option>';
                    }
                    ?>
                </select>
            <?php }else{ ?>
                <b>暂无类型</b>
                <a href="post-new.php?post_type=genre">去添加</a>
            <?php }?>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">参赛须知</label>
            <div class="layui-input-block">
                <input placeholder="https://" class="layui-input" value="<?=$this->meta['match_notice_url'];?>" type="text" name="match[match_notice_url]">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">奖金明细模版</label>
            <div class="layui-input-block">
                <select name="match_income_detail">
                <?php if(!empty($lists)): ?>
                <?php foreach ($lists as $x){?>
                    <option <?=get_post_meta($_GET['post'],'match_income_detail',true) == $x['id'] ? 'selected="selected"':''?> value="<?=$x['id']?>"><?=$x['bonus_tmp_name']?></option>
                <?php } ?>
                <?php endif;?>
                </select>
            </div>
        </div>
    <?php }


    /**
     * 比赛时间box
     */
    public function time_review_meta_box($post){?>
        <div class="layui-form-item">
            <label class="layui-form-label">报名结束时间</label>
            <div class="layui-input-block">
                <input type="text" value="<?=date_i18n('Y-m-d H:i',strtotime($this->meta['entry_end_time']))?>" name="match[entry_end_time]" class="layui-input date-picker" readonly  id="entry_end_time" placeholder="报名结束时间">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">比赛时间</label>
            <div class="layui-input-block">
                <input type="text" value="<?=date_i18n('Y-m-d H:i',strtotime($this->meta['match_start_time']))?>" name="match[match_start_time]" class="layui-input " readonly  id="match_start_time" placeholder="比赛时间">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">结束时间</label>
            <div class="layui-input-block">
                <input type="text" value="<?=date_i18n('Y-m-d H:i',strtotime($this->meta['match_end_time']))?>" name="match[match_end_time]" class="layui-input " readonly  id="match_end_time" placeholder="比赛结束时间">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">比赛状态</label>

            <div class="layui-input-block">
                <?php
                /*switch ($this->meta['match_status']) {
                    case -3:
                        $text = '已结束';
                        $className = '';
                        break;
                    case -2:
                        $text = '等待开赛';
                        $className = 'c_blue';
                        break;
                    case -1:
                        $text = '未开始';
                        $className = '';
                        break;
                    case 1:
                        $text = '报名中';
                        $className = 'c_blue';
                        break;
                    case 2:
                        $text = '进行中';
                        $className = 'c_orange';
                        break;
                    default:
                        $text = '未开始';
                        $className = '';
                        break;
                }*/
                ?>
               <!-- <span class="layui-input <?/*=$className*/?>"><?/*=$text*/?></span>-->
                <input title="已结束" type="radio" name="match[match_status]" value="-3" <?=$this->meta['match_status'] == -3?'checked':'';?> >
                <input title="报名中" type="radio" name="match[match_status]" value="1" <?=$this->meta['match_status'] == 1?'checked':'';?> >
                <input title="等待开赛" type="radio" name="match[match_status]" value="-2" <?=$this->meta['match_status'] == -2?'checked':'';?> >
                <input title="进行中" type="radio" name="match[match_status]" value="2" <?=$this->meta['match_status'] == 2?'checked':'';?> >
            </div>
        </div>
    <?php }


    /**
     * 地点费用设置box
     */
    public function address_review_meta_box(){ ?>
    <div class="layui-form-item">
        <label class="layui-form-label">比赛地点</label>
        <div class="layui-input-block">
            <input placeholder="比赛地点" class="layui-input" value="<?=$this->meta['match_address']?>" type="text" name="match[match_address]">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">比赛费用</label>
        <div class="layui-input-block">
        <input placeholder="比赛费用" class="layui-input" value="<?=$this->meta['match_cost']?>" type="text" name="match[match_cost]">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">最多参与人数</label>
        <div class="layui-input-block">
        <input placeholder="最多参与人数" class="layui-input" value="<?=$this->meta['match_max_number']?>" type="text" name="match[match_max_number]">
        </div>
    </div>

    <?php }

    /**
     * 比赛间隔box
     */
    public function interval_review_meta_box(){ ?>
        <div class="layui-form-item">
            <label class="layui-form-label">比赛用时(分)</label>
            <div class="layui-input-block">
                <input  value="<?=$this->meta['match_use_time']?>" type="text" name="match[match_use_time]" placeholder="比赛用时"  class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">比赛轮数</label>
            <div class="layui-input-block">
                <input  value="<?=$this->meta['match_more']?>" type="text" name="match[match_more]" placeholder="比赛轮数"  class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">项目间隔(分)</label>
            <div class="layui-input-block">
                <input  value="<?=$this->meta['match_project_interval']?>" type="text" name="match[match_project_interval]" placeholder="项目间隔"  class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">每轮间隔(分)</label>
            <div class="layui-input-block">
                <input  value="<?=$this->meta['match_subject_interval']?>" type="text" name="match[match_subject_interval]" placeholder="每轮题间隔"  class="layui-input"/>
            </div>
        </div>

    <?php }


    public function mop_function($val){
        if(isset($this->project[$val])){
            $val = $this->project[$val];
        }else{
            $val = (array)$this->post_array[$val];
        }
        return $val;
    }

    /**
 * 比赛项目设置box
 */
    public function project_review_meta_box($posts)
    {
        global $wpdb;
        //获取所有项目
        $sql = "select ID,post_title from {$wpdb->prefix}posts where post_type = 'project' and post_status = 'publish' order by menu_order asc ";
        $match_project = $wpdb->get_results($sql,ARRAY_A);
        if(empty($match_project)){

            $args = array(
                'post_type' => array('project'),
                'post_status' => array('publish'),
                'orderby' => 'menu_order',
                'order' => 'DESC',
            );
            $the_query = new WP_Query($args);
            $match_project = $the_query->posts;

        }
        if(!empty($match_project)){
            $default_project = array_column($match_project,'post_title','ID');
        }

        //获取已经保存的比赛项目
        $match_project_id = $this->meta['match_project_id'];
        if(!empty($match_project_id)){
            $match_project_id = str2arr($match_project_id);
            //print_r($match_project_id);

            $project_array = array_diff(array_column($match_project,'ID'),$match_project_id);
            $project_array = array_merge($match_project_id,$project_array);

            foreach ($project_array as $v){
                $default_array[$v] = $default_project[$v];
            }
            $default_project = $default_array;
        }else{
            $match_project_id = array();
        }
        //print_r($default_project);

        if (!empty($default_project)) { ?>
            <style>
            .show_form{
                /* width:600px; */
                padding:20px;
            }
            .layui-layer.nl-box-skin{
                width:800px;
            }
            .nl-box-skin .layui-layer-title{
                font-size:20px;
                height:50px;
                line-height:50px;
            }
            </style>
        
           <!--轮数新增/修改form-->
           <div class="show_form" style="display: none" >
                    <input type="hidden" name="post_id" class="_post_id" value="<?=$_GET['post']?>"/>
                    <input type="hidden" name="project_id" class="_project_id" value=""/>
                    <input type="hidden" name="_more_id" class="_more_id" value=""/>
                    <div class="layui-form-item">
                        <label class="layui-form-label">开始时间</label>
                        <div class="layui-input-block">
                            <input type="text" value="" id="start_time" name="start_time" class="layui-input date-picker _start_time"/>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">结束时间</label>
                        <div class="layui-input-block">
                            <input type="text" value="" id="end_time" name="end_time" class="layui-input date-picker _end_time"/>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">比赛时长</label>
                        <div class="layui-input-block">
                            <input type="text" value="" name="use_time" class="layui-input _use_time"/>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">比赛状态</label>
                        <div class="layui-input-block">
                            <input type="radio" class="._status" name="status" value="-1" title="已结束">
                            <input type="radio" class="._status" name="status" value="1" title="未开始">
                            <input type="radio" class="._status" name="status" value="2" title="进行中">
                        </div>
                    </div>
            </div>
        <?php
            global $wpdb;
            foreach ($default_project as $k => $val){

                //获取每个项目信息
                $sql = "select *,
                        case status
                        when -1 then '已结束'
                        when 1 then '未开始'
                        when 2 then '进行中'
                        end status_cn,
                        date_format(start_time, '%Y-%m-%d %H:%i') start_time_format,date_format(end_time, '%Y-%m-%d %H:%i') end_time_format 
                        from {$wpdb->prefix}match_project_more where match_id = {$posts->ID} and project_id = {$k}";
                $rows = $wpdb->get_results($sql,ARRAY_A);
                $total = count($rows);
                //print_r($rows);
        ?>
                <div class="layui-block match_project">
                    <div class="layui-input-inline title">
                        <label class="layui-form-label">拖拽排序</label>
                    </div>
                    <div class="layui-input-inline">
                        <input type="checkbox" name="match[match_project][<?=$k?>][match_project_id]" value="<?=$k?>"  <?= in_array($k,$match_project_id) ? 'checked':''; ?> lay-skin="primary" title="<?=$val?>"/>
                        <?php if(in_array($k,$match_project_id)): ?>
                        <span>(<?=$total > 0 ? $total : 0;?>)</span>
                        <a class="add_new" data-project="<?=$k?>" data-name="<?=$val?>" href="<?=admin_url('edit.php?post_type=match&page=match_more&post_id='.$posts->ID.'&project_id='.$k);?>">新增轮数</a>
                        <?php endif;?>
                    </div>
                    <div>
                        <?php if($total >0): ?>
                        <div class="layui-input-inline title">
                            <label class="layui-form-label">轮数</label>
                        </div>
                        <div class="layui-input-inline">
                            <ul>
                                <?php foreach ($rows as $row){ ?>
                                <li>
                                    <i class="match_more">第<?=$row['more']?>轮</i>
                                    <i>时长<i class="use_time"><?=$row['use_time']?></i>分钟</i>
                                    <i class="status"><?=$row['status_cn']?></i>
                                    <i class="start_time"><?=$row['start_time_format']?></i>
                                    <i class="end_time"><?=$row['end_time_format']?></i>
                                    <a style="color:#4394F9" class="update_more" data-project="<?=$row['project_id']?>" data-name="<?=$val?>" data-id="<?=$row['id']?>" href="">编辑</a>
                                    <a style="color:#4394F9" class="remove_more" data-name="<?=$val?>" data-id="<?=$row['id']?>" href="">删除</a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php endif;?>
                    </div>
                </div>
            <?php }
        }else{ ?>
            <b>暂无项目</b>
            <a href="post-new.php?post_type=project">去添加</a>
        <?php }
    }


    /**
     * 比赛时间box
     */
    public function grading_type_box($post){
        global $wpdb;
        $post_id = $wpdb->get_var("select post_id from {$wpdb->prefix}postmeta where meta_key = 'project_alias' and meta_value = 'mental_world_cup'");

        $sql = "select ID,post_title 
                from {$wpdb->prefix}posts 
                where post_parent = {$post_id} and post_status = 'publish'
                " ;
        $rows = $wpdb->get_results($sql);

        $list = $wpdb->get_results("select * from {$wpdb->prefix}zone_match_role where status = 1 and role_type = 'grading' ");

    ?>
        <div class="layui-form-item">
            <label class="layui-form-label">考级场景</label>
            <div class="layui-input-block">
                <select name="grading[scene]" lay-search="">
                    <option value="">请选择</option>
                    <?php if(!empty($list)){?>
                        <?php foreach ($list as $x){ ?>
                            <option value="<?=$x->id?>" <?=$this->grading['scene'] == $x->id ? 'selected' : '';?> ><?=$x->role_name?></option>
                        <?php } ?>
                    <?php }else{ ?>
                        <option value="1" <?=$this->grading['scene'] == 1 ? 'selected' : '';?> >正式比赛</option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">考级类别</label>
            <div class="layui-input-block">
                <?php if(!empty($rows)){ ?>
                    <select name="grading[category_id]" lay-search="">
                        <option value="">请选择</option>
                        <?php
                        foreach ($rows as $v){
                            $selected = $v->ID == $this->grading['category_id'] ? "selected":" ";
                            echo '<option value="'.$v->ID.'" '.$selected.'>'.$v->post_title.'</option>';
                        }
                        ?>
                    </select>
                <?php }else{ ?>
                    <b>暂无类别</b>
                    <a href="post-new.php?post_type=match-category">去添加</a>
                <?php }?>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">考级地点</label>
            <div class="layui-input-block">
                <input placeholder="比赛地点" class="layui-input" value="<?=$this->grading['address']?>" type="text" name="grading[address]">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">考级费用</label>
            <div class="layui-input-block">
                <input placeholder="比赛费用" class="layui-input" value="<?=$this->grading['cost']?>" type="text" name="grading[cost]">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">报名结束时间</label>
            <div class="layui-input-block">
                <input type="text" value="<?=date_i18n('Y-m-d H:i',strtotime($this->grading['entry_end_time']))?>" name="grading[entry_end_time]" class="layui-input date-picker" readonly  id="entry_end_time" placeholder="报名结束时间">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">考级时间</label>
            <div class="layui-input-block">
                <input type="text" value="<?=date_i18n('Y-m-d H:i',strtotime($this->grading['start_time']))?>" name="grading[start_time]" class="layui-input date-picker" readonly  id="match_start_time" placeholder="比赛时间">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">结束时间</label>
            <div class="layui-input-block">
                <input type="text" value="<?=date_i18n('Y-m-d H:i',strtotime($this->grading['end_time']))?>" name="grading[end_time]" class="layui-input date-picker" readonly  id="match_end_time" placeholder="比赛结束时间">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">责任人</label>
            <div class="layui-input-block">
                <?php
                    if(!empty($this->grading['person_liable'])){
                        $user_real_name = get_user_meta($this->grading['person_liable'],'user_real_name')[0];
                    }
                ?>
                <select class="js-data-select-ajax" name="grading[person_liable]" style="width: 100%" data-action="admin_get_user_list" data-type="user" data-placeholder="输入用户名/手机/邮箱/昵称" >
                    <?php if(!empty($user_real_name)):?><option value="<?=$this->grading['person_liable']?>" selected="selected"><?=$user_real_name['real_name']?></option><?php endif;?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">考级状态</label>

            <div class="layui-input-block">
                <!-- <span class="layui-input <?/*=$className*/?>"><?/*=$text*/?></span>-->
                <input title="已结束" type="radio" name="grading[status]" value="-3" <?=$this->grading['status'] == -3?'checked':'';?> >
                <input title="报名中" type="radio" name="grading[status]" value="1" <?=$this->grading['status'] == 1?'checked':'';?> >
                <input title="等待开赛" type="radio" name="grading[status]" value="-2" <?=$this->grading['status'] == -2?'checked':'';?> >
                <input title="进行中" type="radio" name="grading[status]" value="2" <?=$this->grading['status'] == 2?'checked':'';?> >
            </div>
        </div>
    <?php }

}