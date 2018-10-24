<?php
/*
Plugin Name: Application Match 比赛管理
Plugin URI: http://localhost/wordpress/
Description: 后台比赛管理
Version: 1.0
Author: leo
Author URI: --
Text Domain: nlyd-match
*/
load_plugin_textdomain( 'nlyd-match', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
if(!class_exists('MatchController')){

    class MatchController{

        public $post_type;
        public $match;

        public function __construct($post_type)
        {
            define( 'leo_match_path', plugin_dir_path( __FILE__ ) );
            define( 'leo_match_url', plugins_url('',__FILE__ ) );
            define( 'leo_match_version','v2.1.0.2' );//样式版本

            define( 'match_css_url', leo_match_url.'/Public/css/' );
            define( 'match_js_url', leo_match_url.'/Public/js/' );
            define( 'match_view_path', leo_match_path.'View/' );
            define( 'match_controller_path', leo_match_path.'Controller/' );
            $this->post_type = $post_type;
            $this->main();
        }


        public function Yct_Row_actions( $actions, $post )
        {
//            unset($actions['inline hide-if-no-js']);
            if(in_array($this->post_type,array('match','genre','match-category','project','match','question','problem')) ){
                if($this->post_type == 'match') {
                    unset($actions['trash']);
                    unset($actions['delete']);
                }
                unset($actions['view']);

            }elseif ($this->post_type == 'question'){
                if($post->post_status == 'trash'){
                    $actions['delete'] = '<span class="del_question" style="color: #a00; cursor: pointer;">永久删除</span>';
                }
            }elseif ($this->post_type == 'problem'){
                if($post->post_status == 'trash'){
                    $actions['delete'] = '<span class="del_answer" style="color: #a00; cursor: pointer;">永久删除</span>';
                }
            }
//            $actions['student'] = '<span class="inline hide-if-no-js"></span>';
            return $actions;
        }
        public function main(){

            //屏蔽文章类型的不必要操作按键
            add_action('post_row_actions', array($this,'Yct_Row_actions'), 10, 2);

            //用户新增时添加html
            add_filter('user_contactmethods',array($this,'add_input_html'));


            //自定义文章页
            add_action( 'init', array($this,'create_match_view'));

            //自定义文章分类
            add_action('init',array($this,'create_question_category'));

            //添加meta保存方法
            add_action( 'save_post', array($this,'add_movie_review_fields'), 10, 2 );

            //引入当前页面css/js
            add_action('admin_enqueue_scripts', array($this,'scripts_default'));

            //为文章页删除box
            add_action( 'admin_init', array($this,'remove_meta_boxes') );

            //为文章页添加box
            add_action( 'admin_init', array($this,'add_view_box') );

            //添加子菜单
            add_action('admin_menu',array($this,'add_submenu'));


            if( in_array( $this->post_type,array( 'match','genre','project','match-category','team','student' ) ) ){

                //为文章管理页面添加列
                add_filter("manage_{$this->post_type}_posts_columns", array($this,'add_new_match_columns'));
                //给自定义列赋值
                add_action("manage_{$this->post_type}_posts_custom_column", array($this,'manage_match_columns'), 10, 2);

                //为文章列表标题排序
                add_filter( "manage_edit-{$this->post_type}_sortable_columns",array($this, 'sort_postviews_column' ));

            }

            if(isset($_GET['page']) && $_GET['page'] == 'download'){
                include_once(match_controller_path.'class-download.php');
            }

            include_once(match_controller_path.'class-match.php');
            $this->match = new Match();

            //添加教练菜单
            include_once(match_controller_path.'class-teacher.php');

            //添加战队成员菜单
            include_once(match_controller_path.'class-team.php');

            //添加订单菜单
            include_once(match_controller_path.'class-order.php');

            //添加意见反馈菜单
            include_once(match_controller_path.'class-feedback.php');

            //添加参赛学员菜单
            include_once(match_controller_path.'class-match_student.php');

            //添加脑力健将菜单
            include_once(match_controller_path.'class-brainpower.php');

            //添加导入数据菜单
            include_once(match_controller_path.'class-import.php');


            //引入ajax操作文件
            include_once(leo_match_path.'Controller/class-match-ajax.php');
        }


        public function add_input_html($user_contactmethods){
            $user_contactmethods['user_mobile'] = '用户手机';
            return $user_contactmethods;
        }

        /**
         * 添加子菜单
         */
        public function add_submenu(){
            if ( current_user_can( 'administrator' ) && !current_user_can( 'question_import' ) ) {
                global $wp_roles;

                $role = 'question_import';//权限名
                $wp_roles->add_cap('administrator', $role);

            }
            add_submenu_page( 'edit.php?post_type=question', '题库导入', '题库导入', 'question_import', 'question_import', array($this,'questionImport') );

        }

        /**
         * @return bool
         * @throws PHPExcel_Exception
         * @throws PHPExcel_Reader_Exception
         * 下载图库excel模板
         * 导入题库
         */
        public function questionImport(){
            global $wpdb;
            if(is_post()){

                if(empty($_FILES['import']['tmp_name'])){
                    echo "<script type='text/javascript'>
                            alert('请选择上传文件');
                            setTimeout(function() {
                              history.go(0);
                            },1300)
                          </script>";
                    return false;

                }

                $questionTypeId = $_POST['question_type'];
                require_once LIBRARY_PATH.'Vendor/PHPExcel/Classes/PHPExcel.php';
                $excelClass = new PHPExcel();


                $inputFileType = PHPExcel_IOFactory::identify($_FILES['import']['tmp_name']);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objReader->setReadDataOnly(true);

                //接收存在缓存中的excel表格
                $reader = $objReader->load($_FILES['import']['tmp_name']);
                $sheet = $reader->getSheet(0);
                $highestRow = $sheet->getHighestRow(); // 取得总行数;
                if (is_uploaded_file($_FILES['import']['tmp_name'])) {
                    /*先确定是否改excel格式*/
                    $a = $reader->getActiveSheet()->getCell("A1")->getValue();//获取A列的值
                    $b = $reader->getActiveSheet()->getCell("B1")->getValue();//获取B列的值
                    $c = $reader->getActiveSheet()->getCell("C1")->getValue();//获取C列的值
                    $d = $reader->getActiveSheet()->getCell("D1")->getValue();//获取D列的值
                    $e = $reader->getActiveSheet()->getCell("E1")->getValue();//获取E列的值
                    $f = $reader->getActiveSheet()->getCell("F1")->getValue();//获取F列的值
                    if($a!='文章序号' || $b != '文章标题' || $c != '文章内容' || $d != '问题列表' || $e != '答案选项列表' || $f != '正确答案序号'){
                        echo '文件格式错误，请核对是否为最新题库模板';
                        return false;
                    }
                    $ci = 0;
                    $dataArr = [];
                    $index = 0;
                    $errStr = '';
                    $errNum = 0;
                    $titleB = '';
                    $titleRepeat = '';//重复题目

                    for($page = 1; $page <= $highestRow;$page++){
                        $a = $reader->getActiveSheet()->getCell("A".$page)->getValue();//获取A列的值
                        $b = $reader->getActiveSheet()->getCell("B".$page)->getValue();//获取B列的值
                        $c = $reader->getActiveSheet()->getCell("C".$page)->getValue();//获取C列的值
                        $d = $reader->getActiveSheet()->getCell("D".$page)->getValue();//获取D列的值
                        $e = $reader->getActiveSheet()->getCell("E".$page)->getValue();//获取E列的值
                        $f = $reader->getActiveSheet()->getCell("F".$page)->getValue();//获取F列的值
                        if($b != false) {
                            $token = 0;
                            $titleB = $b;
                        }else{
                            $b = $titleB;
                        }
                        if($token == 1) continue;//已存在题目
                        if(intval($a) < 1 && $a != false) continue;//过滤文本标题

                        //去除已存在的题目
                        if($wpdb->get_var('SELECT ID FROM '.$wpdb->posts.' WHERE post_title="'.$b.'" AND post_type="question"')){
                            $token = 1;
                            $titleRepeat = "\\n{$b}: 已存在";
                            $titleRepeat .= "\\n{$b}: 已存在";
                            continue;
                        }


                        if(!$d || !$e || !$f || empty($problemArr = explode("\n", $e))){
                            $errNum++;
                            $errStr .= "第{$page}行发 生错误<br />";
                            continue;
                        }



                        if($a != false){ //新题目
                            $ci = 0;
                            $index = $a;
                            $dataArr[$a]['title'] = $b;
                            $dataArr[$a]['content'] = $c;
                        }

                        $dataArr[$index]['problem'][$ci]['title'] = $d;  //问题
                        $dataArr[$index]['problem'][$ci]['answer'] = $problemArr; //答案选项
                        $dataArr[$index]['problem'][$ci]['correct'] = $f-1; //正确答案
                        $ci++;

                    }


                    if($errNum > 0){
                        echo '导入失败, 导入文件发生了'.$errNum.'个错误<hr />'.$errStr;
                        return false;
                    }



                    $errStr = '';
                    $errNum = 0;
                    $successNum = 0;
                    $wpdb->startTrans();
                    foreach ($dataArr as $k => $data){
                        $content = '';
                        foreach (explode("\n",$data['content']) as $contentChild){
                            $content .= '<p style="text-indent: 2em;">'.$contentChild.'</p>';
                        }

//                        echo '<pre />';
//                        echo htmlspecialchars($content);
//                        die;
                        $id = wp_insert_post([
                            'post_title' => $data['title'],
                            'post_content' => $content,
                            'post_status' => 'publish',
                            'post_type' => 'question',
                        ]);

                        if(!$id){
                            //题目插入失败
                            $wpdb->rollback();
                            echo '致命错误: '.$data['title'].'存入数据库失败';
                            return false;
                        }
                        //插入比赛类型
                        $termRes = $wpdb->insert($wpdb->prefix.'term_relationships', ['object_id' => $id,'term_taxonomy_id' => $questionTypeId]);
                        if(!$termRes){
                            //问题插入数据库失败
                            $wpdb->rollback();
                            echo '致命错误: '.$data['title'].'-> 存入题目类型失败';
                            return false;
                        }
                        foreach ($data['problem'] as $problem){
                            if(empty($problem['answer'])){
                                //问题无答案选项
                                $wpdb->rollback();
                                echo '致命错误: '.$data['title'].'无选项';
                                return false;
                            }
                            $problemId = wp_insert_post([
                                'post_title' => $problem['title'],
                                'post_content' => '',
                                'post_status' => 'publish',
                                'post_type' => 'problem',
                                'post_parent' => $id
                            ]);

                            if(!$problemId){
                                //问题插入数据库失败
                                $wpdb->rollback();
                                echo '致命错误: '.$data['title'].'-> '.$problem['title'].'存入数据库失败';
                                return false;
                            }
//                            echo '<pre />';
//                            print_r($problem);
//                            die;
                            foreach ($problem['answer'] as $ansK => $answer){
                                if(empty($answer)) continue;
//                                foreach ($problem['correct'] as $correctV){
//                                    $problem_answer = 0;
//                                    if($ansK == $correctV){
//                                        $problem_answer = 1;
//                                        break;
//                                    }
//
//                                }

                                $problem_answer = $ansK == $problem['correct'] ? 1 : 0;
                                $bool = $wpdb->insert($wpdb->prefix.'problem_meta',['problem_id' => $problemId, 'problem_select' => $answer, 'problem_answer' => $problem_answer]);
                                if(!$bool){
                                    //选项插入数据库失败
                                    $wpdb->rollback();
                                    echo '致命错误: '.$data['title'].'-> '.$problem['title'].' -> '.$answer.'存入数据库失败';
                                    return false;
                                }
                            }

                        }
                    }
                    $wpdb->commit();
                    echo "<script type='text/javascript'>alert('导入成功 {$titleRepeat}')</script>";
                }
            }

            //查询题目类型
            $questionType = $wpdb->get_results('SELECT term_id,`name`,slug FROM '.$wpdb->prefix.'terms', ARRAY_A);
            $questionTypeArr = [];
            foreach ($questionType as $qtv){
                foreach (explode('-', $qtv['slug']) as $slug){
                    if($slug == 'question'){
                        $questionTypeArr[] = $qtv;
                        continue 2;
                    }
                }
            }
            ?>

            <div id="wpbody-content" aria-label="主内容" tabindex="0">
                <div id="screen-meta" class="metabox-prefs">

                    <div id="contextual-help-wrap" class="hidden no-sidebar" tabindex="-1" aria-label="“上下文帮助”选项卡">
                        <div id="contextual-help-back"></div>
                        <div id="contextual-help-columns">
                            <div class="contextual-help-tabs">
                                <ul>
                                </ul>
                            </div>


                            <div class="contextual-help-tabs-wrap">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wrap">
                    <h1>题库导入 </h1>
                        <form enctype="multipart/form-data" id="import-upload-form" method="post" action="">
                        <table class="form-table">
                            <tbody>
                            <tr>
                                <th>
                                    <a class="button" type="button" href="?page=download&action=question">下载模板</a>
                                </th>
                            </tr>
                            <tr>
                                <th>
                                    <label for="upload">选择题目类型：</label>
                                </th>
                                <td>
                                    <select name="question_type" id="">
                                        <?php foreach ($questionTypeArr as $question){ ?>
                                            <option value="<?=$question['term_id']?>"><?=$question['name']?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="upload">从您的电脑中选择一个文件：</label>
                                </th>
                                <td>
                                    <input type="file" id="upload" name="import" size="25">
                                    <input type="hidden" name="action" value="save">
                                    <input type="hidden" name="max_file_size" value="2097152">
<!--                                    <small>最大尺寸：2 MB</small>-->

                                    <a class="button" type="button" href="?page=download&action=question">下载模板</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <p class="submit">
                            <input type="submit" class="button button-primary" value="汇入">
                        </p>
                    </form>
                </div>
                <div class="clear"></div>
            </div>
            <?php
        }

        /**
         * 标题列排序
         */
        function sort_postviews_column($columns) {
            $columns['students'] = 'students';
            $columns['match_status'] = 'match_status';
//            $columns['cost'] = '报名费用';

//            $columns['data'] = 'post_data';

            return $columns;
        }

        /**
         * 添加标题列
         */
        public function add_new_match_columns($columns){
            if($this->post_type == 'team'){
                $columns['team_student'] = '战队成员';
                $columns['into_apply_num'] = '入队申请';
                $columns['out_apply_num'] = '退队申请';
                return $columns;
            }elseif ($this->post_type == 'match'){
                unset( $columns['date'] );
                $columns['match_status'] = '状态';
                $columns['author'] = '发布人';
                $columns['students'] = '报名人数';
                $columns['match_ranking'] = '比赛排名';
                $columns['match_brainpower'] = '脑力健将';
                $columns['slogan'] = '口号';
                $columns['times'] = '比赛时间';
                $columns['time_slot'] = '报名时间段';
                $columns['match_address'] = '比赛地点';
                $columns['cost'] = '报名费用';
                $columns['match_type'] = '比赛类型';
                $columns['date'] = '创建日期';
                $columns['options'] = '操作';
            }

            return $columns;

        }
        /*
         *给标题列赋值
         */
        public function manage_match_columns($column_name, $id){
            global $wpdb;
            $sql = "select 
                            match_slogan,match_genre,match_start_time,entry_start_time,entry_end_time,match_address,match_cost,match_status,match_use_time,match_more,match_subject_interval,match_project_interval,
                            case match_status 
                            when -3 then '已结束' 
                            when -2 then '等待开赛' 
                            when -1 then '未开始' 
                            when 1 then '报名中' 
                            when 2 then '比赛中' 
                            end match_status_cn  
                            from {$wpdb->prefix}match_meta   where match_id = {$id} 
                            ";
            $row = $wpdb->get_row($sql,ARRAY_A);
            //print_r($sql);die;

            switch ($column_name){
                case 'match_status':
                    echo $row['match_status_cn'];
                    break;
                case 'slogan':
                    echo $row['match_slogan'];
                    break;
                case 'times':
                    //计算比赛结束时间
                    //对比赛项目进行排序
                    $sql1 = "SELECT b.post_title,c.meta_value as project_alias,a.post_id match_id,a.match_project_id,a.project_use_time,a.match_more,a.project_start_time,a.project_time_interval,a.str_bit,a.child_count_down
                         FROM {$wpdb->prefix}match_project a
                         LEFT JOIN {$wpdb->prefix}posts b ON a.match_project_id = b.ID
                         LEFT JOIN {$wpdb->prefix}postmeta c ON a.match_project_id = c.post_id AND meta_key = 'project_alias'
                         WHERE a.post_id = {$id} ORDER BY a.project_start_time ASC , a.id ASC 
                         ";
                    //print_r($sql1);
                    $rows = $wpdb->get_results($sql1,ARRAY_A);
                    if(!empty($rows)){

                        foreach ($rows as $k => $val){

                            if($val['project_alias'] == 'zxss'){

                                $child_count_down = get_post_meta($val['match_project_id'],'child_count_down')[0];
                                if($val['child_count_down'] > 0){
                                    $child_count_down['even_add'] = $val['child_count_down'];
                                    $child_count_down['add_and_subtract'] = $val['child_count_down'];
                                    $child_count_down['wax_and_wane'] = $val['child_count_down'];
                                }elseif (!empty($child_count_down) && !empty($child_count_down['even_add']) && !empty($child_count_down['add_and_subtract']) && !empty($child_count_down['wax_and_wane'])){
                                    //var_dump($child_count_down);
                                    $child_count_down['even_add'] *= 1;
                                    $child_count_down['add_and_subtract'] *= 1;
                                    $child_count_down['wax_and_wane'] *= 1;
                                }else{

                                    $child_count_down['even_add'] = 3;
                                    $child_count_down['add_and_subtract'] = 3;
                                    $child_count_down['wax_and_wane'] = 3;
                                }
                                $project_use_time = $child_count_down['even_add']+$child_count_down['add_and_subtract']+$child_count_down['wax_and_wane'];
                                //print_r($project_use_time);die;
                            }else{
                                $project_use_time = $val['project_use_time'] > 0 ? $val['project_use_time'] : $row['match_use_time'];
                            }
                            $match_more = $val['match_more'] > 0 ? $val['match_more'] : $row['match_more'];
                            $project_time_interval = $val['project_time_interval'] > 0 ? $val['project_time_interval'] : $row['match_subject_interval'];
                            //项目间隔时间
                            $project_interval = count($rows) - 1 == $k ? 0 :$row['match_project_interval'];


                            if(strtotime($val['project_start_time']) > 0){
                                $end_time = strtotime($val['project_start_time']) + ($project_use_time*$match_more + ($match_more-1)*$project_time_interval+$project_interval)*60;
                                $rows[$k]['project_end_time'] = $val['project_end_time'] = date_i18n('Y-m-d H:i:s',$end_time);

                            }else{

                                $project_end_time = !empty($rows[$k-1]['project_end_time']) ? strtotime($rows[$k-1]['project_end_time']) + $row['match_project_interval']*60 : strtotime($row['match_start_time']);
                                $end_time = $project_end_time + ($project_use_time*$match_more + ($match_more-1)*$project_time_interval)*60;
                                $rows[$k]['project_end_time'] = $val['project_end_time'] = date_i18n('Y-m-d H:i:s',$end_time);
                                $rows[$k]['project_start_time'] = $val['project_start_time'] = date_i18n('Y-m-d H:i:s',$project_end_time);
                            }

                            //leo_dump($rows[$k]['project_start_time'].'-----'.$rows[$k]['project_end_time']);

                        }
                        $end = end($rows);
                        $match_end_time = $end['project_end_time'];
                    }

                    echo $row['match_start_time'].'<br/>'.$match_end_time;
                    break;
                case 'time_slot':
                    echo $row['entry_start_time'].'<br />'.$row['entry_end_time'];
                    break;
                case 'match_address':
                    echo $row['match_address'];
                    break;
                case 'cost':
                    echo $row['match_cost'];
                    break;
                case 'students':
                    $student_num = $wpdb->get_var('SELECT count(o.id) AS num FROM '.$wpdb->prefix.'order AS o 
                    RIGHT JOIN '.$wpdb->users.' AS u ON u.ID=o.user_id WHERE o.match_id='.$id.' AND o.pay_status IN(2,3,4) AND o.order_type=1');
                    echo '<a href="?page=match_student&match_id='.$id.'" class="">'.$student_num.'人</a>';
//                    if($row['match_status'] == -3){
//                        echo '<a href="?page=match_student&match_id='.$id.'" class="">报名学员</a>';
//                    }else{
//                        echo '比赛未结束';
//                    }
                    break;
                case 'match_type':
                    $args = array(
                        'post_type' => array('genre'),
                        'post_status' => array('publish'),
                        'order' => 'DESC',
                    );
                    $the_query = new WP_Query( $args );
                    $str = '-';
                    foreach ($the_query->posts as $v){
                        if($row['match_genre'] == $v->ID) $str = $v->post_title;
                    }
                    echo $str;
                    break;
                case 'match_ranking':
                    echo '<a href="admin.php?page=match_student-ranking&match_id='.$id.'">查看排名</a>';
//                    if($row['match_status'] == -3){
//                        echo '<a href="admin.php?page=match_student-ranking&match_id='.$id.'">查看排名</a>';
//                    }else{
//                        echo '比赛未结束';
//                    }

                    break;
                case 'match_brainpower':
                    if($row['match_status'] == -3){
                        echo '<a href="admin.php?page=brainpower-join_directory&match_id='.$id.'">查看名录</a>';
                    }else{
                        echo '比赛未结束';
                    }

                    break;
                case 'options':
                    //删除比赛必须要先关闭比赛
                    $str = '<a href="post.php?post='.$id.'&action=edit">查看详情</a>';
                    $post = get_post($id);
                    if($post->post_status == 'trash'){
                        $str .= ' | <a href="javascript:;" data-id="'.$id.'" data-status="'.$row['match_status'].'" class="delMatch">删除比赛</a>';
                    }else{
                        $str .= ' | <a href="javascript:;" class="closeMatch" data-status="'.$row['match_status'].'" data-id="'.$id.'">关闭比赛</a>';
                    }

                    echo $str;
                    break;
                case 'team_student':
                    //每个战队的成员数量
                    $student_num = $wpdb->get_var("SELECT COUNT(mt.id) FROM `{$wpdb->prefix}match_team` AS mt 
                    LEFT JOIN {$wpdb->users} AS u ON mt.user_id=u.ID WHERE mt.status=2 AND mt.team_id={$id} AND u.ID!=''");
                    $student_num = $student_num ? $student_num : 0;
                    echo '<a href="'.admin_url('edit.php?post_type=team&page=team-student&id='.$id.'&team_type=1').'" class="">'.$student_num.'人</a>';
                    break;
                case 'into_apply_num':
                    //入队申请
                    $student_num = $wpdb->get_var("SELECT COUNT(mt.id) FROM `{$wpdb->prefix}match_team` AS mt 
                    LEFT JOIN {$wpdb->users} AS u  ON mt.user_id=u.ID WHERE mt.status=1 AND mt.team_id={$id} AND u.ID!=''");
                    $student_num = $student_num ? $student_num : 0;
                    $color = 'style="color:#424242"';
                    if($student_num>0) $color = 'style="color:#bf0000"';
                    echo '<a '.$color.' href="'.admin_url('edit.php?post_type=team&page=team-student&id='.$id.'&team_type=2').'" class="">'.$student_num.'个</a>';
                    break;
                case 'out_apply_num':
                    //退队申请
                    $student_num = $wpdb->get_var("SELECT COUNT(mt.id) FROM `{$wpdb->prefix}match_team` AS mt 
                    LEFT JOIN {$wpdb->users} AS u  ON mt.user_id=u.ID WHERE status=-1 AND team_id={$id} AND u.ID!=''");
                    $student_num = $student_num ? $student_num : 0;
                    $color = 'style="color:#424242"';
                    if($student_num>0) $color = 'style="color:#bf0000"';
                    echo '<a '.$color. ' href="' .admin_url('edit.php?post_type=team&page=team-student&id='.$id.'&team_type=3').'" class="">'.$student_num.'个</a>';
                    break;
                default:
                    break;
            }
        }

        /**
         * 保存meta信息
         * @param $post_ID 比赛id
         * @param $post_data 比赛内容
         */

        public function add_movie_review_fields( $post_ID, $post_data ) {

            // Check post type for movie reviews
            global $wpdb,$current_user;
            if(in_array($post_data->post_type,array('match','genre','project','match-category'))){

                if(!empty($_POST['project_alias'])){
                    update_post_meta($post_ID,'project_alias',$_POST['project_alias']);
                }
                if(!empty($_POST['child_count_down'])){

                    update_post_meta($post_ID,'child_count_down',$_POST['child_count_down']);
                }
                if(!empty($_POST['default_str_length'])){

                    update_post_meta($post_ID,'default_str_length',$_POST['default_str_length']);
                }
                if(!empty($_POST['match_switch']) && $_POST['match_switch'] == 'ON'){

                    update_post_meta($post_ID,'default_match_switch',$_POST['match_switch']);
                }else{
                    update_post_meta($post_ID,'default_match_switch','OFF');
                }

                if(isset($_POST['match']) && !empty($_POST['match'])){

                    $match_meta = $_POST['match'];

                    if(empty($match_meta['match_project'])){
                        //获取所有比赛项目
                        $args = array(
                            'post_type' => array('project'),
                            'post_status' => array('publish'),
                            'orderby' => 'menu_order',
                            'order' => 'asc',
                        );
                        $the_query = new WP_Query($args);
                        $project_array = array_column($the_query->posts,'ID');

                    }else{
                        $project_array = array_column($match_meta['match_project'],'match_project_id');
                    }

                    $project_id = arr2str($project_array);


                    $match_meta['match_id'] = $post_ID;
                    $match_meta['match_project_id'] = $project_id;
                    $match_meta['created_id'] = $current_user->ID;
                    $match_meta['created_time'] = get_time('mysql');

                    unset($match_meta['match_project']);

                    //查询是否发布了比赛信息
                    //var_dump($match_meta);die;
                    $wpdb->delete($wpdb->prefix.'match_meta_new',array('match_id'=>$post_ID));
                    $a = $wpdb->insert($wpdb->prefix.'match_meta_new',$match_meta);

                    //var_dump($a);die;
                }
            }elseif ($post_data->post_type == 'team'){
                if(isset($_POST['team']) && !empty($_POST['team'])){

                    $data = $_POST['team'];
                    $data['team_id'] = $post_ID;
                    //var_dump($_POST['team']);die;
                    $wpdb->delete($wpdb->prefix.'team_meta',array('team_id'=>$post_ID));
                    //print_r($data);
                    $a = $wpdb->insert($wpdb->prefix.'team_meta',$data);
                    //var_dump($a);die;
                }
            }elseif ($post_data->post_type == 'problem'){
                if(!empty($_POST['problem'])){

                    $this->save_problem($post_ID,$_POST['problem']);
                }
            }

        }

        /**
         * 比赛项目信息保存
         * @param problem_id 问题id
         * @param $data 题目选项
         */
        public function save_problem($problem_id,$data){
            global $wpdb;
            $table = $wpdb->prefix.'problem_meta';
            $sql = "INSERT INTO {$table} (problem_id,problem_select,problem_answer) VALUES ";
            $count = 0;

            foreach ($data as $k => $v){
                $select = !empty($v['select']) ? $v['select'] : '';
                $answer = !empty($v['answer']) ? $v['answer'] : '';
                $sql .= "( {$problem_id},'{$select}','{$answer}' ),";
                ++$count;
            }
            if($count > 0){
                $a = $wpdb->delete($table,array('problem_id'=>$problem_id));
                /*var_dump($a);
                var_dump($sql);
                die;*/
                $result = $wpdb->query(rtrim($sql, ','));
                //var_dump($result);die;
            }
        }


        /**
         * 比赛项目信息保存
         * @param $post_ID 比赛id
         * @param $data 项目内容
         */
        public function save_match_project($post_ID,$data){
            //var_dump($data);die;
            global $wpdb;
            $table = $wpdb->prefix.'match_project';
            $sql = "INSERT INTO {$table} (post_id,match_project_id,project_use_time,project_start_time,project_washing_out,project_time_interval,str_bit,match_more,child_count_down) VALUES ";
            $count = 0;

            foreach ($data as $k => $v){
                if(!empty($v['match_project_id'])){

                    $sql .= "( $post_ID,{$v['match_project_id']},'{$v['project_use_time']}','{$v['project_start_time']}','{$v['project_washing_out']}','{$v['project_time_interval']}','{$v['str_bit']}','{$v['match_more']}','{$v['child_count_down']}' ),";
                    ++$count;
                }
            }
            if($count > 0){
                //var_dump($sql);die;
                $a = $wpdb->delete($table,array('post_id'=>$post_ID));
                $result = $wpdb->query(rtrim($sql, ','));
                //var_dump($result);die;
            }
        }

        /**
         * 删除box
         */
        public function remove_meta_boxes() {

           // remove_meta_box( 'submitdiv', 'team', 'side' );

        }

        /**
         * 添加自定义box
         */
        public function add_view_box() {

            switch ($this->post_type){
                case 'match':
                case 'genre':
                case 'project':
                case 'match-category':

                    if($this->post_type == 'match'){

                        add_meta_box( 'switch_meta_box',
                            '自定义开赛开关',
                            array($this->match,'match_switch_meta_box'),
                            $this->post_type, 'side'
                        );

                        add_meta_box( 'slogan_meta_box',
                            '口号类型设置',
                            array($this->match,'slogan_review_meta_box'),
                            $this->post_type, 'normal', 'high'
                        );

                        add_meta_box( 'time_meta_box',
                            '比赛时间设置',
                            array($this->match,'time_review_meta_box'),
                            $this->post_type, 'normal', 'high'
                        );

                        add_meta_box( 'address_meta_box',
                            '地点费用设置',
                            array($this->match,'address_review_meta_box'),
                            $this->post_type, 'normal'
                        );

                        /*add_meta_box( 'order_meta_box',
                            '比赛顺序设置',
                            array($this->match,'order_review_meta_box'),
                            $this->post_type, 'normal'
                        );*/

                        add_meta_box( 'project_meta_box',
                            '比赛项目设置',
                            array($this->match,'project_review_meta_box'),
                            $this->post_type, 'normal'
                        );


                    }

                    /*add_meta_box( 'interval_meta_box',
                        '比赛间隔设置',
                        array($this->match,'interval_review_meta_box'),
                        $this->post_type, 'normal','high'
                    );*/

                    if( in_array($this->post_type,array('project')) ){

                        add_meta_box( 'str_bit_meta_box',
                            '初始字符位数',
                            array($this->match,'str_bit_set_meta_box'),
                            $this->post_type, 'normal', 'low'
                        );
                        add_meta_box( 'child_count_down_meta_box',
                            '子项倒计时',
                            array($this->match,'child_count_down_meta_box'),
                            $this->post_type, 'normal', 'low'
                        );
                    }

                    if( in_array($this->post_type,array('project','match-category','genre')) ){
                        add_meta_box( 'parent_meta_box',
                            '选择父类',
                            array($this->match,'page_attributes_meta_box'),
                            $this->post_type, 'side', 'low'
                        );

                        add_meta_box( 'alias_meta_box',
                            '别名设置',
                            array($this->match,'alias_meta_box'),
                            $this->post_type, 'side', 'low'
                        );
                    }
                    break;
                case 'team';

                    add_meta_box( 'nationality_meta_box',
                        '国籍口号设置',
                        array($this->match,'team_nationality_meta_box'),
                        $this->post_type, 'normal', 'low'
                    );

                    add_meta_box( 'team_number_meta_box',
                        '人数设置',
                        array($this->match,'team_number_meta_box'),
                        $this->post_type, 'normal', 'low'
                    );
                    add_meta_box( 'team_leader_meta_box',
                        '队长设置',
                        array($this->match,'team_leader_meta_box'),
                        $this->post_type, 'normal', 'low'
                    );

                    break;
                case 'problem':
                    add_meta_box( 'question_meta_box',
                        '题目设置',
                        array($this->match,'question_meta_box'),
                        $this->post_type, 'normal', 'low'
                    );
                    add_meta_box( 'problem_meta_box',
                        '问题设置',
                        array($this->match,'problem_meta_box'),
                        $this->post_type, 'normal', 'low'
                    );
                    break;
                case 'question':
                    add_meta_box( 'go_problem_meta_box',
                        '设置问题',
                        array($this->match,'go_problem_meta_box'),
                        $this->post_type, 'normal', 'low'
                    );
                    break;
            }



        }


        /**
         * 自定义页面
         */
        public function create_match_view(){
            //我的战队
            $args = array(
                'labels' => array(
                    'name'               => _x( '战队', 'post type 名称' ),
                    'singular_name'      => _x( '战队', 'post type 单个 item 时的名称，因为英文有复数' ),
                    'add_new'            => _x( '新建战队', '添加新内容的链接名称' ),
                    'add_new_item'       => __( '新建 战队' ),
                    'edit_item'          => __( '编辑战队' ),
                    'new_item'           => __( '新战队' ),
                    'all_items'          => __( '所有战队' ),
                    'view_item'          => __( '查看' ),
                    'search_items'       => __( '搜索' ),
                    'not_found'          => __( '没有找到有关战队' ),
                    'not_found_in_trash' => __( '回收站里面没有相关战队' ),
                    'menu_name'          => '战队',
                ),
                'capability_type'=>array('team','teams'),
                'public'        => true,
                'menu_icon'  => 'dashicons-groups',
                'menu_position' => 99,
                'supports'      => array( 'title', 'editor','thumbnail' ),
                'has_archive'   => true
            );

            register_post_type( 'team', $args );
            if ( current_user_can( 'administrator' ) && !current_user_can( 'team' ) ) {
                global $wp_roles;

                $role = 'edit_team';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_teams';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_others_teams';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'publish_teams';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_team';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_private_teams';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'delete_team';//权限名
                $wp_roles->add_cap('administrator', $role);

            }


            //比赛历史页
            $args = array(
                'labels' => array(
                            'name'               => _x( '比赛 记录', 'post type 名称' ),
                            'singular_name'      => _x( 'Movie', 'post type 单个 item 时的名称，因为英文有复数' ),
                            'add_new'            => _x( '新建比赛', '添加新内容的链接名称' ),
                            'add_new_item'       => __( '新建 比赛' ),
                            'edit_item'          => __( '编辑比赛' ),
                            'new_item'           => __( '新比赛' ),
                            'all_items'          => __( '所有比赛' ),
                            'view_item'          => __( '查看' ),
                            'search_items'       => __( '搜索' ),
                            'not_found'          => __( '没有找到有关比赛' ),
                            'not_found_in_trash' => __( '回收站里面没有相关比赛' ),
                            'menu_name'          => '比赛',
                        ),
                'capability_type'=>array('match','matchs'),
                'public'        => true,
                'menu_icon'  => 'dashicons-welcome-write-blog',
                'menu_position' => 99,
                'supports'      => array( 'title', 'editor','thumbnail' ),
                'has_archive'   => true
            );

            register_post_type( 'match', $args );
            if ( !current_user_can( 'match' ) ) {
                global $wp_roles;

                $role = 'edit_match';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_matchs';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_others_matchs';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'publish_matchs';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_match';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_private_matchs';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'delete_match';//权限名
                $wp_roles->add_cap('administrator', $role);

            }

            //比赛类型页
            $args = array(
                'labels' => array(
                    'name'               => _x( '比赛 类型', 'post type 名称' ),
                    'add_new'            => _x( '新建类型', '添加新内容的链接名称' ),
                    'add_new_item'       => __( '新建 类型' ),
                    'edit_item'          => __( '编辑类型' ),
                    'new_item'           => __( '新类型' ),
                    'all_items'          => __( '比赛类型' ),
                    'view_item'          => __( '查看' ),
                    'search_items'       => __( '搜索' ),
                    'not_found'          => __( '没有找到有关比赛类型' ),
                    'not_found_in_trash' => __( '回收站里面没有相关比赛类型' ),
                    'menu_name'          => '比赛类型',
                ),
                'capability_type'=>array('genre','genres'),
                'public'        => true,
                'supports'      => array( 'title', 'editor', 'thumbnail' ),
                'has_archive'   => true,
                'show_in_menu'  =>'edit.php?post_type=match'
            );

            register_post_type( 'genre', $args );

            if ( !current_user_can( 'genre' ) ) {
                global $wp_roles;

                $role = 'edit_genre';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_genres';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_others_genres';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'publish_genres';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_genre';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_private_genres';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'delete_genre';//权限名
                $wp_roles->add_cap('administrator', $role);
            }

            //比赛类型页
            $args = array(
                'labels' => array(
                    'name'               => _x( '比赛 类别', 'post type 名称' ),
                    'add_new'            => _x( '新建类别', '添加新内容的链接名称' ),
                    'add_new_item'       => __( '新建 类别' ),
                    'edit_item'          => __( '编辑类别' ),
                    'new_item'           => __( '新类别' ),
                    'all_items'          => __( '比赛类别' ),
                    'view_item'          => __( '查看' ),
                    'search_items'       => __( '搜索' ),
                    'not_found'          => __( '没有找到有关比赛类型' ),
                    'not_found_in_trash' => __( '回收站里面没有相关比赛类型' ),
                    'menu_name'          => '比赛类别',
                ),
                'capability_type'=>array('match-category','match-categorys'),
                'public'        => true,
                'supports'      => array( 'title', 'editor', 'thumbnail' ),
                'has_archive'   => true,
                'show_in_menu'  =>'edit.php?post_type=match',
            );

            register_post_type( 'match-category', $args );

            if ( !current_user_can( 'match-category' ) ) {
                global $wp_roles;

                $role = 'edit_match-category';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_match-categorys';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_others_match-categorys';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'publish_match-categorys';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_match-category';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_private_match-categorys';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'delete_match-category';//权限名
                $wp_roles->add_cap('administrator', $role);
            }

            //比赛项目页
            $args = array(
                'labels' => array(
                    'name'               => _x( '比赛 项目', 'post type 名称' ),
                    'singular_name'      => _x( 'Movie', 'post type 单个 item 时的名称，因为英文有复数' ),
                    'add_new'            => _x( '新建项目', '添加新内容的链接名称' ),
                    'add_new_item'       => __( '新建 项目' ),
                    'edit_item'          => __( '编辑项目' ),
                    'new_item'           => __( '新项目' ),
                    'all_items'          => __( '比赛项目' ),
                    'view_item'          => __( '查看' ),
                    'search_items'       => __( '搜索' ),
                    'not_found'          => __( '没有找到有关比赛项目' ),
                    'not_found_in_trash' => __( '回收站里面没有相关比赛项目' ),
                    'menu_name'          => '比赛项目',
                ),
                'capability_type'=>array('project','projects'),
                'public'        => true,
                'supports'      => array( 'title', 'editor', 'thumbnail' ),
                'has_archive'   => true,
                'show_in_menu'  =>'edit.php?post_type=match'
            );

            register_post_type( 'project', $args );


            if ( !current_user_can( 'project' ) ) {
                global $wp_roles;

                $role = 'edit_project';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_projects';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_others_projects';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'publish_projects';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_project';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_private_projects';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'delete_project';//权限名
                $wp_roles->add_cap('administrator', $role);
            }


            //题库
            $args = array(
                'labels' => array(
                    'name'               => _x( '题库', 'post type 名称' ),
                    'singular_name'      => _x( '题库', 'post type 单个 item 时的名称，因为英文有复数' ),
                    'add_new'            => _x( '新建题目', '添加新内容的链接名称' ),
                    'add_new_item'       => __( '新建 题目' ),
                    'edit_item'          => __( '编辑题目' ),
                    'new_item'           => __( '新题目' ),
                    'all_items'          => __( '所有题目' ),
                    'view_item'          => __( '查看' ),
                    'search_items'       => __( '搜索' ),
                    'not_found'          => __( '没有找到有关题目' ),
                    'not_found_in_trash' => __( '回收站里面没有相关题目' ),
                    'menu_name'          => '题库',
                ),
                'capability_type'=>array('question','questions'),
                'public'        => true,
                'menu_icon'  => 'dashicons-book',
                'menu_position' => 99,
                'supports'      => array( 'title', 'editor'  ),
                'has_archive'   => true
            );
            register_post_type( 'question', $args );
            //创建题库分类box
           // register_taxonomy('question_genre', 'question', array('labels' => array('name' => '题库类型', 'add_new_item' => '添加新的题库类型', 'new_item_name' => "新的题库类型"), 'show_ui' => true, 'show_tagcloud' => true, 'hierarchical' => true));

            if ( !current_user_can( 'question' ) ) {
                global $wp_roles;

                $role = 'edit_question';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_questions';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_others_questions';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'publish_questions';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_question';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_private_questions';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'delete_question';//权限名
                $wp_roles->add_cap('administrator', $role);

            }

            //比赛问题页
            $args = array(
                'labels' => array(
                    'name'               => _x( '题目 问题', 'post type 名称' ),
                    'singular_name'      => _x( 'Problem', 'post type 单个 item 时的名称，因为英文有复数' ),
                    'add_new'            => _x( '新建问题', '添加新内容的链接名称' ),
                    'add_new_item'       => __( '新建 问题' ),
                    'edit_item'          => __( '编辑问题' ),
                    'new_item'           => __( '新问题' ),
                    'all_items'          => __( '问题' ),
                    'view_item'          => __( '查看' ),
                    'search_items'       => __( '搜索' ),
                    'not_found'          => __( '没有找到有关比赛项目' ),
                    'not_found_in_trash' => __( '回收站里面没有相关比赛项目' ),
                    'menu_name'          => '问题',
                ),
                'capability_type'=>array('problem','problems'),
                'public'        => true,
                'supports'      => array( 'title', 'editor' ),
                'has_archive'   => true,
                'show_in_menu'  =>'edit.php?post_type=question'
            );

            register_post_type( 'problem', $args );


            if ( !current_user_can( 'problem' ) ) {
                global $wp_roles;

                $role = 'edit_problem';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_problems';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'edit_others_problems';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'publish_problems';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_problem';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'read_private_problems';//权限名
                $wp_roles->add_cap('administrator', $role);

                $role = 'delete_problem';//权限名
                $wp_roles->add_cap('administrator', $role);

            }
        }

        /**
         * 自定义分类
         */
        public function create_question_category(){

            if ( !current_user_can( 'problem' ) ) {
                global $wp_roles;

                $role = 'edit_problem';//权限名
                $wp_roles->add_cap('administrator', $role);

            }

            register_taxonomy('question_genre', 'question', array('labels' => array('name' => '题库类型', 'add_new_item' => '添加新的题库类型', 'new_item_name' => "新的题库类型"), 'show_ui' => true, 'show_tagcloud' => true, 'hierarchical' => true));
        }

        /**
         * 默认公用js/css引入
         */
        public function scripts_default(){
            // var_dump($this->post_type);
            wp_register_style( 'admin_index_css',match_css_url.'index.css','', leo_match_version  );
            wp_enqueue_style( 'admin_index_css' );
            //in_array($this->post_type,array('team','match','genre'));
            //if(!in_array($this->post_type,array('page','post','question','project','match-category','problem' ))){
            if(in_array($this->post_type,array('team','match'))){
                wp_register_script( 'admin_layui_js',match_js_url.'layui/layui.js',array('jquery'), leo_match_version  );
                wp_enqueue_script( 'admin_layui_js' );
                wp_register_style( 'admin_layui_css',match_css_url.'layui.css','', leo_match_version  );
                wp_enqueue_style( 'admin_layui_css' );
            }
            wp_register_script( 'drag',match_js_url.'drag/drag.js',array('jquery'), leo_match_version  );
            wp_enqueue_script( 'drag' );
            wp_register_script( 'admin_select2_js',match_js_url.'select2/dist/js/select2.js',array('jquery'), leo_match_version  );
            wp_enqueue_script( 'admin_select2_js' );
            wp_register_script( 'admin_select2_i18n_js',match_js_url.'select2/dist/js/i18n/zh-CN.js',array('jquery'), leo_match_version  );
            wp_enqueue_script( 'admin_select2_i18n_js' );
            wp_register_style( 'admin_select2_css',match_js_url.'select2/dist/css/select2.css','', leo_match_version  );
            wp_enqueue_style( 'admin_select2_css' );

            wp_register_script( 'admin_match_question',match_js_url.'question.js',array('jquery'), leo_match_version  );
            wp_enqueue_script( 'admin_match_question' );
            if($this->post_type == 'match'){
                wp_register_script( 'team_leader',match_js_url.'team_leader.js',array('jquery'), leo_match_version  );
                wp_enqueue_script( 'team_leader' );
                wp_register_style( 'match_css',match_css_url.'match/match.css','', leo_match_version  );
                wp_enqueue_style( 'match_css' );
                wp_register_script( 'match-lists',match_js_url.'match-lists.js',array('jquery'), leo_match_version  );
                wp_enqueue_script( 'match-lists' );
            }
        }



        public function my_submenu_page_student(){

        }

    }

    if(isset($_GET['post'])){
        $row = get_post($_GET['post']);
        if(!empty($row)){
            $post_type = $row->post_type;
        }
    }elseif (isset($_GET['post_type'])){
        $post_type = $_GET['post_type'];
    }else{
        $post_type = '';
    }

    new MatchController($post_type);
}
