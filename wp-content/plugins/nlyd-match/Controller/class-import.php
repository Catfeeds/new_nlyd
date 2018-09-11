<?php
class Import {
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'register_order_menu_page') );
//        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
    }

    public function register_order_menu_page(){

        if ( current_user_can( 'administrator' ) && !current_user_can( 'imports' ) ) {
            global $wp_roles;

            $role = 'imports';//权限名
            $wp_roles->add_cap('administrator', $role);

        }

        // sckm_searches
        add_menu_page('导入user', '导入user', 'administrator', 'imports',array($this,'users'),'dashicons-businessman',99);
        add_submenu_page('imports','导入战队','导入战队','administrator','imports-teams',array($this,'import_teams'));
        add_submenu_page('imports','usermeta','usermeta','administrator','imports-usermeta',array($this,'usermeta'));
//        add_submenu_page('match_student','新增报名学员','新增报名学员','administrator','match_student-add_student',array($this,'addStudent'));
//        add_submenu_page('match_student','脑力健将','脑力健将','administrator','match_student-brainpower',array($this,'brainpower'));
    }
    /**
     * usermeta
     */
    public function usermeta(){
        set_time_limit(0);//0表示不限时
        global $wpdb;
        $result = $wpdb->get_results('SELECT * FROM sckm_members');
        foreach ($result as $res){
            if($res->birthday){
                $age = get_time('mysql') - explode('-',$res->birthday)[0];
            }else{
                $age = 0;
            };
            $display_name = mb_substr($res->truename, 0, 1).','.mb_substr($res->truename, 1);
            $display_name = $display_name == ',' ? '' : addslashes($display_name);
            $user_real_name = ['real_type' => '','real_name' => addslashes($res->truename), 'real_ID' => '', 'real_age' => $age];
            $user_address = ['country' => $res->country,'province' => $res->province, 'city' => $res->city, 'area' => $res->dist];
            update_user_meta($res->id,'user_address',$user_address);
            update_user_meta($res->id,'$user_real_name',$user_real_name);
            update_user_meta($res->id,'user_birthday',$res->birthday);
            update_user_meta($res->id,'user_head',$res->headimgurl);
            update_user_meta($res->id,'first_name',explode(',',$display_name)[1]);
            update_user_meta($res->id,'last_name',explode(',',$display_name)[0]);
        }
    }

    /**
     * 导入用户
     */
    public function users(){

        require_once LIBRARY_PATH.'Vendor/PHPExcel/Classes/PHPExcel.php';
        $excelClass = new PHPExcel();
        global $wpdb;

//        $wpdb->delete($wpdb->users,['1' => '1']);
//        exit;
        $fileName = 'user.sql';
//        file_put_contents('user.sql', '');
//        $tmp = fopen($fileName, 'w+');

//        $res = $wpdb->get_results('SELECT * FROM '.$wpdb->usermeta, ARRAY_A);
//        echo '<pre />';
//        print_r($res);
//        die;
        set_time_limit(0);//0表示不限时



        $errStr = '';
            $result = $wpdb->get_results('SELECT * FROM sckm_members');

            foreach ($result as $res){

                $regirestTime = date('Y-m-d H:i:s', $res->register_time);
                $display_name = mb_substr($res->truename, 0, 1).','.mb_substr($res->truename, 1);
                $display_name = $display_name == ',' ? '' : addslashes($display_name);
                $res->nickname = addslashes($res->nickname);
                $user_login = $res->mem_mobile ? $res->mem_mobile : $res->openid;
                $userCreatBool = wp_create_user($user_login,get_time(),$res->email,$res->mem_mobile);
                $userUpdateArr = [
                    'user_nicename' => $res->nickname,
                    'weChat_openid' => $res->openid,
                    'display_name' => $display_name,
                ];
                if(is_object($userCreatBool)){
                    $user_login = $res->openid ? $res->openid : $res->email;
                    $userCreatBool = wp_create_user($user_login,get_time(),$res->email,$res->mem_mobile);
                    if(is_object($userCreatBool)){
                        $user_login = 'nlyd_'.get_time().$res->id;
                        $userCreatBool = wp_create_user($user_login,get_time(),$res->email,$res->mem_mobile);
                        var_dump($userCreatBool);
                    }
                }

                $userUpdateData = "user_nicename='$res->nickname',weChat_openid='$res->openid',display_name='$display_name',ID='$res->id'";
                $userSql = 'UPDATE wp_users SET '.$userUpdateData.' WHERE ID='.$userCreatBool;
                $usersBool = $wpdb->query($userSql);
                //usermeta
                if($res->birthday){
                    $age = get_time('mysql') - explode('-',$res->birthday)[0];
                }else{
                    $age = 0;
                }

//                $meatBool = $wpdb->query($metaSql);

                //脑力认证
                $searchsRes = $wpdb->get_results('SELECT * FROM sckm_searches WHERE member_id='.$res->id);
                $searchsSql = 'INSERT INTO '.$wpdb->prefix.'directories(`user_id`,`level`,`range`,`type`) VALUES ';
                $searchsSql2 = '';
                foreach ($searchsRes as $sv){
                    $level = $sv->level;
                    $range = 1;
                    if(preg_match('/一/',$level)) $level = 1;
                    if(preg_match('/国际/',$level)) $range = 2;
                    $type = 0;
                    switch ($sv->search_type_id){
                        case 3:
                            $type = 4;
                            break;
                        case 4:
                            $type = 5;
                            break;
                        case 5:
                            $type = 2;
                            break;
                        case 6:
                            $type = 3;
                            break;
                        case 7:
                            $type = 1;
                            break;
                    }
                    $searchsSql2 = "('$res->id','$level','$range','$type'),";

                }
                if(!$searchsSql2 == ''){
                    $searchsSql2 = substr($searchsSql2,0,strlen($searchsSql2)-1);
                    $searchsBool = $wpdb->query($searchsSql.$searchsSql2);
                    if(!$searchsBool){
                        $errStr .= $res->id.'->认证: 插入失败<br /> sql: '.$searchsSql.$searchsSql2.'<br />';
                    }
                }


                if(!$userCreatBool){
                    $errStr .= $res->id.':插入失败<br /> sql: <br />';
                }
                if(!$usersBool){
                    $errStr .= $res->id.':更新数据失败<br /> sql: '.$userSql.'<br />';
                }
//                if(!$meatBool){
//                    $errStr .= $res->id.'->usermeta: 插入失败<br /> sql: '.$metaSql.'<br />';
//                }
                if(!$usersBool){
                    $errStr .= '<hr>';
                }
            }
        echo $errStr;
//        fwrite($tmp,)


//        ini_set('memory_limit','3072M');    // 临时设置最大内存占用为3G
//        set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期
//
//        global $wpdb;
//
//
//        $inputFileType = PHPExcel_IOFactory::identify($_FILES['import']['tmp_name']);
//        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
//        $objReader->setReadDataOnly(true);
//
//        //接收存在缓存中的excel表格
//        $reader = $objReader->load($_FILES['import']['tmp_name']);
//        $sheet = $reader->getSheet(0);
//        $highestRow = $sheet->getHighestRow(); // 取得总行数;
//        if (is_uploaded_file($_FILES['import']['tmp_name'])) {
//            /*先确定是否改excel格式*/
//            $a = $reader->getActiveSheet()->getCell("A1")->getValue();//获取A列的值
//            $b = $reader->getActiveSheet()->getCell("B1")->getValue();//获取B列的值
//            $c = $reader->getActiveSheet()->getCell("C1")->getValue();//获取C列的值
//            $d = $reader->getActiveSheet()->getCell("D1")->getValue();//获取D列的值
//            $e = $reader->getActiveSheet()->getCell("E1")->getValue();//获取E列的值
//            $f = $reader->getActiveSheet()->getCell("F1")->getValue();//获取F列的值
//            if($a!='文章序号' || $b != '文章标题' || $c != '文章内容' || $d != '问题列表' || $e != '选项列表' || $f != '答案'){
//                echo '文件格式错误，请核对是否为最新题库模板';
//                return false;
//            }
//            $ci = 0;
//            $dataArr = [];
//            $index = 0;
//            $errStr = '';
//            $errNum = 0;
//            for($page = 1; $page <= $highestRow;$page++){
//                $a = $reader->getActiveSheet()->getCell("A".$page)->getValue();//获取A列的值
//                $b = $reader->getActiveSheet()->getCell("B".$page)->getValue();//获取B列的值
//                $c = $reader->getActiveSheet()->getCell("C".$page)->getValue();//获取C列的值
//                $d = $reader->getActiveSheet()->getCell("D".$page)->getValue();//获取D列的值
//                $e = $reader->getActiveSheet()->getCell("E".$page)->getValue();//获取E列的值
//                $f = $reader->getActiveSheet()->getCell("F".$page)->getValue();//获取F列的值
//                if(intval($a) < 1 && $a != false) continue;//过滤文本标题
//                if(!$d || !$e || !$f || empty($problemArr = explode('<br>', $e)) || ($correct = intval($f)-1) < 0){
//                    $errNum++;
//                    $errStr .= "第{$page}行发 生错误<br />";
//                    continue;
//                }
//                if($a != false){ //新题目
//                    $ci = 0;
//                    $index = $a;
//                    $dataArr[$a]['title'] = $b;
//                    $dataArr[$a]['content'] = $c;
//                }
//
//                $dataArr[$index]['problem'][$ci]['title'] = $d;  //问题
//                $dataArr[$index]['problem'][$ci]['answer'] = $problemArr; //答案选项
//                $dataArr[$index]['problem'][$ci]['correct'] = $correct; //正确答案
//                $ci++;
//
//            }
//            if($errNum > 0){
//                echo '导入失败, 导入文件发生了'.$errNum.'个错误<hr />'.$errStr;
//                return false;
//            }
//            $errStr = '';
//            $errNum = 0;
//            $successNum = 0;
//            $wpdb->startTrans();
//            foreach ($dataArr as $k => $data){
//                $id = wp_insert_post([
//                    'post_title' => $data['title'],
//                    'post_content' => $data['content'],
//                    'post_status' => 'publish',
//                    'post_type' => 'question',
//                ]);
//
//                if(!$id){
//                    //题目插入失败
//                    $wpdb->rollback();
//                    echo '致命错误: '.$data['title'].'存入数据库失败';
//                    return false;
//                }
//
//                foreach ($data['problem'] as $problem){
//                    if(empty($problem['answer'])){
//                        //问题无答案选项
//                        $wpdb->rollback();
//                        echo '致命错误: '.$data['title'].'无选项';
//                        return false;
//                    }
//                    $problemId = wp_insert_post([
//                        'post_title' => $problem['title'],
//                        'post_content' => '',
//                        'post_status' => 'publish',
//                        'post_type' => 'problem',
//                        'post_parent' => $id
//                    ]);
//
//                    if(!$problemId){
//                        //问题插入数据库失败
//                        $wpdb->rollback();
//                        echo '致命错误: '.$data['title'].'-> '.$problem['title'].'存入数据库失败';
//                        return false;
//                    }
//
//                    foreach ($problem['answer'] as $ansK => $answer){
//
//                        $problem_answer = $ansK == $problem['correct']-1 ? 1 : 0;
//                        $bool = $wpdb->insert($wpdb->prefix.'problem_meta',['problem_id' => $problemId, 'problem_select' => $answer, 'problem_answer' => $problem_answer]);
//                        if(!$bool){
//                            //选项插入数据库失败
//                            $wpdb->rollback();
//                            echo '致命错误: '.$data['title'].'-> '.$problem['title'].' -> '.$answer.'存入数据库失败';
//                            return false;
//                        }
//                    }
//
//                }
//            }
//            $wpdb->commit();
//            echo '<script>alert("导入成功")</script>';
//        }

    }

    /**
     * 导入战队
     */
    public function import_teams(){
        global $wpdb;
        $teamErrStr = '';
        $errStr = '';
        $result = $wpdb->get_results('SELECT * FROM sckm_teams WHERE is_verify=1');
//        $result->g
        foreach ($result as $res){
            $bool = wp_insert_post([
//                'ID' => $res->id,
                'post_title' => $res->title,
                'post_content' => $res->content,
                'post_status' => 'publish',
                'post_type' => 'team',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'ping_date' => date('Y-m-d H:i:s',$res->created),
            ]);
            $wpdb->update($wpdb->posts, ['post_date' => date('Y-m-d H:i:s',$res->created)], ['ID' => $bool]);
            $teamId = $bool;
            $word = $res->country_id == 3 ? '中国' : '';
            $teamMetaData = [
                'team_slogan' => $res->summary,
                'team_leader' => $res->member_id,
                'team_director' => $res->member_id,
                'team_id' => $teamId,
                'team_world' => $word,
            ];
            $metaBool = insert($wpdb->prefix.'team_meta',$teamMetaData);

            $users = $wpdb->get_results('SELECT * FROM sckm_team_members WHERE team_id='.$res->id);
            foreach ($users as $usv){
                $data = [
                    'team_id' => $teamId,
                    'user_id' => $usv->member_id,
                    'user_type' => 1,
                    'status' => 2,
                    'created_time' => date('Y-m-d H:i:s',$usv->created),
                ];
                $bool = $wpdb->insert($wpdb->prefix.'match_team',$data);
                if(!$bool){
                    $errStr .= $usv->id.'插入失败, sql: '.$wpdb->last_query.'<br />';
                }
            }

            if(!$bool){
                $teamErrStr .= $res->id.'插入失败 <br />';
            }
            if(!$metaBool){
                $teamErrStr .= $res->id.':team: 插入失败 <br />';
            }
        }
//        $users = $wpdb->get_results('SELECT * FROM sckm_team_members');
//        foreach ($users as $usv){
//            $data = [
//                'team_id' => $usv->team_id*100,
//                'user_id' => $usv->member_id,
//                'user_type' => 1,
//                'status' => 2,
//                'created_time' => date('Y-m-d H:i:s',$usv->created),
//            ];
//            $bool = $wpdb->insert($wpdb->prefix.'match_team',$data);
//            if(!$bool){
//                $errStr .= $usv->id.'插入失败, sql: '.$wpdb->last_query.'<br />';
//            }
//        }
        echo $teamErrStr.'<hr />'.$errStr;
    }

}

new Import();