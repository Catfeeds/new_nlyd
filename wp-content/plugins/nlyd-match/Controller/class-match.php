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

            if(in_array($post_type,array('match','genre','project','match-category'))){

                //获取比赛meta
                $sql = "select 
                          id,match_id,match_genre,match_genre,match_status,match_category_order,match_slogan,match_address,match_max_number,
                          if(unix_timestamp(match_start_time) > 1,match_start_time,'') match_start_time, 
                          if(unix_timestamp(entry_start_time) > 1,entry_start_time,'') entry_start_time, 
                          if(unix_timestamp(entry_end_time) > 1,entry_end_time,'') entry_end_time, 
                          if(match_cost < 0,'',match_cost) match_cost, 
                          if(match_more < 1,'',match_more) match_more,
                          if(match_use_time < 1,'',match_use_time) match_use_time, 
                          if(match_project_interval < 1,'',match_project_interval) match_project_interval, 
                          if(match_subject_interval < 1,'',match_subject_interval) match_subject_interval,
                          if(str_bit < 1,'',str_bit) str_bit,
                          if(child_count_down < 1,'',child_count_down) child_count_down
                from {$wpdb->prefix}match_meta where match_id = {$_GET['post']} ";
                $post_meta = $wpdb->get_row($sql,ARRAY_A);
                //var_dump($sql);
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
                                  where a.post_id = {$_GET['post']} order by a.id asc
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
        }

    }

    /**
     * 关闭和删除比赛
     */
    public function closeOrDelMatch(){
        var_dump(111);
    }


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
            <li class="select-li"><input class="select-checkBox" type="checkbox" name="problem[<?=$k?>][answer]" value="1" <?=$val['problem_answer'] == 1 ? 'checked' : '';?> ><input class="select-input" type="text" name="problem[<?=$k?>][select]" value="<?=$val['problem_select']?>"><span class="delSelect">删除</span></li>
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
    public function go_problem_meta_box(){
        $url = admin_url('post-new.php?post_type=problem&question_id='.$_GET['post']);
        ?>

        <p><a href="<?=$url?>">问题设置 Go</a></p>

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

        <p>绑定题目
            <select class="js-data-example-ajax" name="parent_id" style="width: 90%" data-action="get_question_list">
                <?php if(!empty($question)):?><option value="<?=$question->ID?>" selected="selected"><?=$question->post_title?></option><?php endif;?>
            </select>
        </p>

    <?php }


    /**
     * 别名设置box
     */
    public function alias_meta_box(){
        $project_alias = get_post_meta($_GET['post'],'project_alias');
        ?>

        <p>项目别名<input  value="<?=!empty($project_alias[0])?$project_alias[0]:'';?>" type="text" name="project_alias"/></p>

    <?php }

    /**
     * 子项倒计时设置box
     */
    public function child_count_down_meta_box(){

        if($this->alias =='zxss'){ ?>
        <p>连加运算<input  value="<?=$this->child_count_down['even_add']?>" type="text" name="child_count_down[even_add]"/>默认单位为分</p>
        <p>加减运算<input  value="<?=$this->child_count_down['add_and_subtract']?>" type="text" name="child_count_down[add_and_subtract]"/>默认单位为分</p>
        <p>乘除运算<input  value="<?=$this->child_count_down['wax_and_wane']?>" type="text" name="child_count_down[wax_and_wane]"/>默认单位为分</p>
        <?php }else{ ?>
        <p>子项比赛用时<input  value="<?=$this->child_count_down?>" type="text" name="child_count_down"/>默认单位为秒</p>
        <?php }?>
    <?php }

    /**
     * 初始字符位数设置box
     */
    public function str_bit_set_meta_box(){ 
        
    ?>

        <p>初始长度<input  value="<?=$this->default_str_length ?>" type="text" name="default_str_length"/></p>

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
        <p>队长设置
            <select class="js-data-example-ajax" name="team[team_leader]" style="width: 90%" data-action="getMemberByWhere" data-type="team_leader" team-id="<?=$post->ID?>">
                <?php if(!empty($team_leader)):?><option value="<?=$this->team_meta['team_leader']?>" selected="selected"><?=$team_leader[0]['real_name']?></option><?php endif;?>
            </select>
        </p>
    <?php }


    /**
     * 战队最大人数设置box
     */
    public function team_number_meta_box(){ ?>

        <p>最大人数<input  value="<?=$this->team_meta['max_number']?>" type="text" name="team[max_number]"/></p>

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
            <label class="post-attributes-label" for="parent_id">国籍</label>
            <select name="team[team_world]">
                <option value="">(选择国籍)</option>
                <?php
                foreach ($rows as $v){
                    $selected = $this->team_meta['team_world'] == $v->title ? "selected":" ";
                    echo '<option value="'.$v->title.'" '.$selected.'>'.$v->title.'</option>';
                }
                ?>
            </select>
        <?php
        endif;
        $team_director = get_user_meta($this->team_meta['team_director'],'user_real_name');
        ?>

        <p><label class="post-attributes-label" for="parent_id">战队口号</label><input  value="<?=$this->team_meta['team_slogan'];?>" type="text" name="team[team_slogan]"/></p>
        <p>战队负责人
            <select class="js-data-example-ajax" name="team[team_director]" style="width: 90%" data-action="getMemberByWhere" data-type="team_director" team-id="<?=$post->ID?>">
                <?php if(!empty($team_director)):?><option value="<?=$this->team_meta['team_slogan']?>" selected="selected"><?=$team_director[0]['real_name']?></option><?php endif;?>
            </select>
        </p>

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
        if ( ! empty($the_query->posts) ) :
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
   
        <div class="layui-block">
            <label class="layui-form-label">比赛口号</label>
            <div class="layui-input-block">
                <input  class="layui-input" value="<?=$this->meta['match_slogan'];?>" type="text" name="match[match_slogan]" placeholder="比赛口号"/>
            </div>
        </div>
        <form>
            <div class="layui-block">
                <label class="layui-form-label">比赛类型</label>
                <div class="layui-input-inline">
                <?php if(!empty($the_query->post)){ ?>
                    <select name="match[match_genre]">
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
        </form>
    <?php }


    /**
     * 比赛时间box
     */
    public function time_review_meta_box($post){?>
        <div class="layui-inline">
            <label class="layui-form-label">比赛时间</label>
            <div class="layui-input-inline">
                <input type="text" value="<?=$this->meta['match_start_time']?>" name="match[match_start_time]" class="layui-input date-picker" id="match_start_time" placeholder="比赛时间">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">报名开始时间</label>
            <div class="layui-input-inline">
                <input type="text" value="<?=$this->meta['entry_start_time']?>" name="match[entry_start_time]" class="layui-input date-picker" id="entry_start_time" placeholder="报名开始时间">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">报名结束时间</label>
            <div class="layui-input-inline">
                <input type="text" value="<?=$this->meta['entry_end_time']?>" name="match[entry_end_time]" class="layui-input date-picker" id="entry_end_time" placeholder="报名结束时间">
            </div>
        </div>
        <p>比赛状态
            <input type="radio" name="match[match_status]" value="-3" <?=$this->meta['match_status'] == -3?'checked':'';?> >已结束
            <input type="radio" name="match[match_status]" value="-2" <?=$this->meta['match_status'] == -2?'checked':'';?> >等待开赛
            <input type="radio" name="match[match_status]" value="-1" <?=$this->meta['match_status'] == -1 || empty($this->meta['match_status'])?'checked':'';?> >未开始
            <input type="radio" name="match[match_status]" value="1" <?=$this->meta['match_status'] == 1?'checked':'';?> >报名中
            <input type="radio" name="match[match_status]" value="2" <?=$this->meta['match_status'] == 2?'checked':'';?> >进行中
        </p>
    <?php }


    /**
     * 地点费用设置box
     */
    public function address_review_meta_box(){ ?>

        <p>比赛地点<input  value="<?=$this->meta['match_address']?>" type="text" name="match[match_address]"/></p>
        <p>比赛费用<input  value="<?=$this->meta['match_cost']?>" type="text" name="match[match_cost]"/></p>
        <p>最多参与人数<input  value="<?=$this->meta['match_max_number']?>" type="text" name="match[match_max_number]"/></p>

    <?php }

    /**
     * 比赛间隔box
     */
    public function interval_review_meta_box(){ ?>

        <p>比赛用时<input  value="<?=$this->meta['match_use_time']?>" type="text" name="match[match_use_time]"/><span>默认单位为分钟</span></p>
        <p>比赛轮数<input  value="<?=$this->meta['match_more']?>" type="text" name="match[match_more]"/></p>
        <p>项目间隔<input  value="<?=$this->meta['match_project_interval']?>" type="text" name="match[match_project_interval]"/><span>默认单位为分钟</span></p>
        <p>每轮题间隔<input  value="<?=$this->meta['match_subject_interval']?>" type="text" name="match[match_subject_interval]"/><span>默认单位为分钟</span></p>

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
    public function project_review_meta_box()
    {
        //获取所有项目
        $args = array(
            'post_type' => array('project'),
            'post_status' => array('publish'),
            'order' => 'DESC',
        );
        $the_query = new WP_Query($args);
        $this->post_array = array_combine(array_column($the_query->posts,'ID'),$the_query->posts);

        $default_array = array_unique(array_merge($this->temp_key,array_column($the_query->posts,'ID')));
        $match_project = array_map(array($this,'mop_function'),$default_array);
        //var_dump($match_project);

        if (!empty($match_project)) {
            foreach ($match_project as $k => $v){
                ?>
                <div class="layui-inline match_project">
                    <div class="layui-input-inline title">
                        <label class="layui-form-label">拖拽排序</label>
                    </div>
                    <div class="layui-input-inline">
                        <label class="layui-form-label"><input type="checkbox" name="match[match_project][<?=$k?>][match_project_id]" value="<?=$v['ID']?>" <?=!empty($this->project[$v['ID']])?'checked':''; ?> /><?=$v['post_title']?></label>
                    </div>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="match[match_project][<?=$k?>][project_use_time]" value="<?=!empty($v['project_use_time']) ? $v['project_use_time'] : '';?>" placeholder="比赛用时"/>
                    </div>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="match[match_project][<?=$k?>][match_more]" value="<?=!empty($v['match_more']) ? $v['match_more'] : '';?>" placeholder="比赛轮数"/>
                    </div>
                    <div class="layui-input-inline">
                        <input class="layui-input date-picker" type="text" name="match[match_project][<?=$k?>][project_start_time]" value="<?=!empty($v['project_start_time']) ? $v['project_start_time'] : '';?>" id="id<?=$k?>" placeholder="开始时间"/>
                    </div>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="match[match_project][<?=$k?>][project_washing_out]" value="<?=!empty($v['project_washing_out']) ? $v['project_washing_out'] : '';?>" placeholder="淘汰率或淘汰人数"/>
                    </div>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="match[match_project][<?=$k?>][project_time_interval]" value="<?=!empty($v['project_time_interval']) ? $v['project_time_interval'] : '';?>" placeholder="间隔时间"/>
                    </div>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="match[match_project][<?=$k?>][str_bit]" value="<?=!empty($v['str_bit']) ? $v['str_bit'] : '';?>" placeholder="初始位数"/>
                    </div>
                    <?php if(in_array($v['post_title'],array('正向速算','快眼扫描'))): ?>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="match[match_project][<?=$k?>][child_count_down]" value="<?=!empty($v['child_count_down']) ? $v['child_count_down'] : '';?>" placeholder="子项倒计时"/>
                    </div>
                    <?php endif;?>
                </div>
            <?php }
        }else{ ?>
            <b>暂无项目</b>
            <a href="post-new.php?post_type=project">去添加</a>
        <?php }
    }

}