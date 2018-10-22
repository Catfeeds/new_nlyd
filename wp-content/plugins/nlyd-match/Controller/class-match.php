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





            /*if(in_array($post_type,array('match','genre','project','match-category'))){

                //获取比赛meta
                $sql = "select * from {$wpdb->prefix}match_meta_new where match_id = {$_GET['post']}";
                $post_meta = $wpdb->get_row($sql,ARRAY_A);
                $this->meta = $post_meta;


                switch ($post_type){
                    case 'match':
                        //获取比赛选项
                        $sql = " select a.id,a.post_id,a.match_project_id ID,
                                  if(a.project_use_time < 1,'',a.project_use_time) project_use_time, 
                                  if(a.match_more < 1,'',a.match_more) match_more,
                                  if(unix_timestamp(a.project_start_time) > 1,a.project_start_time,'') project_start_time, 
                                  if(a.project_washing_out < 1,'',a.project_washing_out) project_washing_out, 
                                  if(a.project_time_interval < 1,'',a.project_time_interval) project_time_interval,
                                  if(a.str_bit < 1,'',a.str_bit) str_bit,
                                  if(child_count_down < 1,'',child_count_down) child_count_down,
                                  b.post_title
                                  from {$wpdb->prefix}match_project a
                                  left join {$wpdb->prefix}posts b on a.match_project_id = b.ID
                                  where a.post_id = {$_GET['post']} order by a.project_start_time ASC ,a.id asc
                               ";
                        //print_r($sql);
                        $rows = $wpdb->get_results($sql,ARRAY_A);
                        //$this->temp_key = array_column($rows,'match_project_id');
                        $this->temp_key = array_column($rows,'ID');
                        $match_project = array_combine($this->temp_key,$rows) ;
                        //print_r($this->temp_key );
                        $this->project = $match_project;
                        break;
                    case 'project':

                        //获取当前项目别名
                        $alias = get_post_meta($_GET['post'],'project_alias');
                        $this->alias = $alias[0];
                        if(in_array($this->alias,array('zxss','kysm'))){
                            $child_count_down = get_post_meta($_GET['post'],'child_count_down');
                            $this->child_count_down = $child_count_down[0];

                            $this->default_str_lenth = get_post_meta($_GET['post'],'default_str_length')[0];
                        }
                        break;
                    default:
                        break;
                }
            }
            elseif ($post_type == 'team'){
                //获取战队基本资料
                $sql = " select * from {$wpdb->prefix}team_meta where team_id = {$_GET['post']} ";
                $this->team_meta = $wpdb->get_row($sql,ARRAY_A);
                //print_r($this->team_meta);
            }elseif ($post_type == 'problem'){
                //获取题目选项以及答案
                $sql = " select * from {$wpdb->prefix}problem_meta where problem_id = {$_GET['post']} order by id asc ";
                $this->problem = $wpdb->get_results($sql,ARRAY_A);
            }*/
        }

        add_action('admin_menu',array($this,'add_submenu'));

    }

    public function add_submenu(){

        if ( current_user_can( 'administrator' ) && !current_user_can( 'match' ) ) {
            global $wp_roles;

            $role = 'match_more_list';//权限名
            $wp_roles->add_cap('administrator', $role);


        }

        add_submenu_page( 'edit.php?post_type=match', '轮数设置', '轮数设置', 'match_more_list', 'match_more', array($this,'match_more_list'),45);

    }

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
                <select class="js-data-example-ajax" name="parent_id" style="width: 100%" data-action="get_question_list">
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
        $args = array(
            'post_type' => array('genre'),
            'post_status' => array('publish'),
            'order' => 'DESC',
        );
        $the_query = new WP_Query( $args );
    ?>
        <div class="layui-form-item">
            <label class="layui-form-label">比赛口号</label>
            <div class="layui-input-block">
            <input placeholder="比赛口号" class="layui-input" value="<?=$this->meta['match_slogan'];?>" type="text" name="match[match_slogan]">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">比赛类型</label>
            <div class="layui-input-block">
            <?php if(!empty($the_query->post)){ ?>
                <select name="match[match_genre]" lay-search="">
                    <option value="">请选择</option>
                    <?php
                    foreach ($the_query->posts as $v){
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
    <?php }


    /**
     * 比赛时间box
     */
    public function time_review_meta_box($post){?>
        <div class="layui-form-item">
            <label class="layui-form-label">报名结束时间</label>
            <div class="layui-input-block">
                <input type="text" value="<?=$this->meta['entry_end_time']?>" name="match[entry_end_time]" class="layui-input date-picker" readonly  id="entry_end_time" placeholder="报名结束时间">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">比赛时间</label>
            <div class="layui-input-block">
                <input type="text" value="<?=$this->meta['match_start_time']?>" name="match[match_start_time]" class="layui-input date-picker" readonly  id="match_start_time" placeholder="比赛时间">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">结束时间</label>
            <div class="layui-input-block">
                <input type="text" value="<?=$this->meta['match_end_time']?>" name="match[match_end_time]" class="layui-input date-picker" readonly  id="match_end_time" placeholder="比赛结束时间">
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

        //获取所有项目
        $args = array(
            'post_type' => array('project'),
            'post_status' => array('publish'),
            'order' => 'asc',
            'orderby' => 'menu_order',
        );
        $the_query = new WP_Query($args);
        $match_project = $the_query->posts;

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

        if (!empty($default_project)) {
            global $wpdb;
            foreach ($default_project as $k => $val){

                //获取每个项目信息
                $sql = "select *,date_format(start_time, '%Y-%m-%d %H:%i') start_time_format,date_format(end_time, '%Y-%m-%d %H:%i') end_time_format from {$wpdb->prefix}match_project_more where match_id = {$posts->ID} and project_id = {$k}";
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
                        <a href="<?=admin_url('edit.php?post_type=match&page=match_more&post_id='.$posts->ID.'&project_id='.$k);?>">新增轮数</a>
                        <div>
                            <?php if($total >0): ?>
                                <ul>
                                    <?php foreach ($rows as $k => $row){ ?>
                                    <li>
                                        第<?=$k+1?>轮
                                        开始时间:<?=$row['start_time_format']?>
                                        结束时间:<?=$row['end_time_format']?>
                                    </li>
                                    <?php } ?>
                                </ul>
                            <?php endif;?>
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


}