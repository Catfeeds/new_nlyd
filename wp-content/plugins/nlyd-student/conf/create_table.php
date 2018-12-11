<?php

function the_table_install () {

    global $wpdb;
    require_once(ABSPATH . "wp-admin/includes/upgrade.php");  //引用wordpress的内置方法库

    /*
    **************示例*****************
    $table_name = $wpdb->prefix . "theTable";  //获取表前缀，并设置新表的名称

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (

          id mediumint(9) NOT NULL AUTO_INCREMENT,

          time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,

          name tinytext NOT NULL,

          text text NOT NULL,

          url VARCHAR(55) DEFAULT '' NOT NULL,

          UNIQUE KEY id (id)

          );";

        dbDelta($sql);

    }*/

    $table_name = $wpdb->prefix . "income_logs";  //收益领取记录表   存储收益分配记录

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
           `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `order_type` tinyint(2) DEFAULT NULL COMMENT '同order表order_type',
          `match_id` int(20) DEFAULT NULL COMMENT '同order表match_id',
          `user_id` int(20) DEFAULT NULL COMMENT '付款人id',
          `referee_id` int(20) DEFAULT NULL COMMENT '直接推广人',
          `referee_income` decimal(10,2) DEFAULT NULL COMMENT '直接收益',
          `indirect_referee_id` int(20) DEFAULT NULL COMMENT '间接推广人',
          `indirect_referee_income` decimal(10,2) DEFAULT NULL COMMENT '间接收益',
          `person_liable_id` int(20) DEFAULT NULL COMMENT '负责人',
          `person_liable_income` decimal(10,2) DEFAULT NULL COMMENT '负责人收益',
          `sponsor_id` int(20) DEFAULT NULL COMMENT '主办方',
          `sponsor_income` decimal(10,2) DEFAULT NULL COMMENT '主办方收益',
          `manager_id` int(20) DEFAULT NULL COMMENT '事业员(主办方上级)',
          `manager_income` int(20) DEFAULT NULL COMMENT '事业员收益',
          `created_time` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "zone_join_coach";  //机构与员工的关系表   储存机构拥有的员工

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
           `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `zone_id` int(20) DEFAULT NULL COMMENT '机构id 即user_id',
          `coach_id` int(20) DEFAULT NULL COMMENT '教练id 即(user_id)',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "zone_join_role";  //机构类型与权限关联表   储存机构拥有的权限

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
           `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(20) DEFAULT NULL COMMENT '主体用户id',
          `role_id` varchar(255) DEFAULT NULL COMMENT '权限id合集 以,为分割符',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "zone_type_role";  //机构权限表   储存权限

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
           `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `role_name` varchar(255) DEFAULT NULL COMMENT '权限名',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "zone_type";  //机构类型表   储存机构所有类型

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
           `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `zone_type_name` varchar(255) DEFAULT NULL COMMENT '机构名字',
          `zone_type_status` tinyint(2) DEFAULT NULL COMMENT '状态 1正常 2关闭',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "zone_meta";  //机构外键表   储存机构信息

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(20) NOT NULL,
          `type_id` int(20) DEFAULT NULL COMMENT '升级后用户类型',
          `zone_name` text DEFAULT NULL COMMENT '机构名字',
          `zone_address` varchar(255) DEFAULT NULL COMMENT '机构地址',
          `business_licence` varchar(255) DEFAULT NULL COMMENT '营业执照',
          `business_licence_url` varchar(255) DEFAULT NULL COMMENT '营业执照照片路径',
          `legal_person` varchar(255) DEFAULT NULL COMMENT '法人',
          `opening_bank` varchar(255) DEFAULT NULL COMMENT '开户行',
          `opening_bank_address` varchar(255) DEFAULT NULL COMMENT '开户行地址',
          `bank_card_num` varchar(255) DEFAULT NULL COMMENT '银行卡号',
          `referee_id` int(20) DEFAULT NULL COMMENT '推荐人id',
          `is_able` tinyint(1) DEFAULT NULL COMMENT '1正常,2冻结',
          `user_status` tinyint(2) DEFAULT NULL COMMENT '审核状态 1正常 -1正在审核 -2未通过',
          `created_time` datetime DEFAULT NULL COMMENT '提交时间',
          `audit_time` datetime DEFAULT NULL COMMENT '审核时间',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "user_grade_log_history";  //考級训练次数记录表   储存考级训练次数

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(20) DEFAULT NULL,
          `genre_id` int(20) DEFAULT NULL,
          `grade_type` varchar(20) DEFAULT NULL COMMENT '考级类型 memory 速记 reading 速读 arithmetic 速算',
          `grade_lv` tinyint(3) DEFAULT NULL COMMENT '考级等级',
          `grade_result` tinyint(2) DEFAULT NULL COMMENT '考级结果',
          `created_time` datetime DEFAULT NULL COMMENT '训练时间',
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }


    $table_name = $wpdb->prefix . "user_grade_logs";  //考級训练记录表   储存考级训练记录

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(20) NOT NULL,
          `grade_log_id` int(20) NOT NULL COMMENT '训练记录id',
          `grading_type` varchar(50) NOT NULL COMMENT '考级类型 memory 速记 reading 速读 arithmetic 速算',
          `questions_type` varchar(50) NOT NULL COMMENT '考题类型',
          `grading_questions` longtext COMMENT '考试题',
          `questions_answer` longtext COMMENT '考题答案',
          `my_answer` longtext COMMENT '我的答案',
          `correct_rate` float(5,3) DEFAULT NULL COMMENT '准确率',
          `my_score` mediumint(10) DEFAULT NULL COMMENT '分数',
          `use_time` smallint(20) DEFAULT NULL COMMENT '记忆耗时',
          `created_time` datetime DEFAULT NULL,
          `post_id` int(20) DEFAULT NULL COMMENT '文章id',
          `post_str_length` int(20) DEFAULT NULL COMMENT '阅读文章长度',
           PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "grading_logs";  //考級记录表   储存考级是否通过

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(20) DEFAULT NULL COMMENT '用户id',
          `grading_id` int(20) DEFAULT NULL COMMENT '考级id',
          `grading_result` tinyint(2) DEFAULT NULL COMMENT '考级结果 1 过级 2 失败',
          `grading_lv` tinyint(3) DEFAULT NULL COMMENT '本次考级等级',
          `created_time` datetime DEFAULT NULL COMMENT '创建时间',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "grading_questions";  //考級成绩表  储存考级成绩记录

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(20) DEFAULT NULL,
          `grading_id` int(20) DEFAULT NULL COMMENT '考级id',
          `grading_type` varchar(50) DEFAULT NULL COMMENT '考级类型 memory 速记 reading 速读 arithmetic 速算',
          `questions_type` varchar(50) DEFAULT NULL COMMENT '考题类型',
          `grading_questions` longtext COMMENT '考试题',
          `questions_answer` longtext COMMENT '考题答案',
          `my_answer` longtext COMMENT '我的答案',
          `correct_rate` float(3,3) COMMENT '准确率',
          `my_score` mediumint(10) COMMENT '成绩',
          `use_time` smallint(20) DEFAULT NULL COMMENT '记忆使用时间',
          `submit_type` tinyint(2) DEFAULT NULL COMMENT '提交方式 1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切换,系统提交',
          `leave_page_time` text COMMENT '记录每次离开页面的时间',
          `created_time` datetime DEFAULT NULL,
          `is_true` tinyint(2) DEFAULT '1' COMMENT '成绩真实性 1 真实 2 虚假',
          `post_id` int(20) DEFAULT NULL COMMENT '文章id',
          `post_str_length` int(20) DEFAULT NULL COMMENT '阅读文章长度',
          `post_more` tinyint(3) DEFAULT NULL COMMENT '速读考级轮数',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "grading_meta";  //考級   储存考级meta信息

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `grading_id` int(20) DEFAULT NULL COMMENT '考级id',
          `scene` tinyint(2) NOT NULL COMMENT '考级场景 1 正式考级 2 模拟考级',
          `category_id` int(20) NOT NULL COMMENT '考级类别',
          `entry_end_time` datetime NOT NULL COMMENT '报名截止时间',
          `start_time` datetime NOT NULL COMMENT '开始时间',
          `end_time` datetime DEFAULT NULL COMMENT '结束时间',
          `address` varchar(255) DEFAULT NULL COMMENT '考级地址',
          `cost` decimal(10,2) DEFAULT NULL COMMENT '考级费用',
          `status` tinyint(2) DEFAULT NULL COMMENT '考级状态 -3:已结束 -2等待开赛 1:报名中 2:进行中',
          `grading_notice_url` varchar(255) DEFAULT NULL COMMENT '考级须知',
          `person_liable` int(20) DEFAULT NULL COMMENT '考级须知',
          `created_person` int(20) DEFAULT NULL COMMENT '责任人',
          `created_time` datetime DEFAULT NULL COMMENT '创建时间',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "prison_match_log";  //比赛meta

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
            `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
              `supervisor_id` int(20) NOT NULL COMMENT '监赛官id',
              `match_id` int(20) NOT NULL COMMENT '比赛id',
              `project_id` int(20) NOT NULL COMMENT '比赛项目id',
              `match_more` tinyint(5) DEFAULT NULL COMMENT '比赛轮数',
              `student_name` varchar(255) DEFAULT NULL COMMENT '学生姓名',
              `seat_number` smallint(10) NOT NULL COMMENT '座位号',
              `evidence` varchar(255) DEFAULT NULL COMMENT '证据照片',
              `describe` varchar(255) DEFAULT NULL COMMENT '证据描述',
              `created_time` datetime DEFAULT NULL COMMENT '提交时间',
              PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "match_meta_new";  //比赛meta

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
            `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
            `match_id` int(20) NOT NULL COMMENT '比赛id(posts主键ID)',
            `match_slogan` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '比赛口号',
            `match_scene` tinyint(2) DEFAULT NULL COMMENT '比赛口号',
            `match_genre` int(20) DEFAULT NULL COMMENT '比赛类型',
            `match_start_time` datetime DEFAULT NULL COMMENT '比赛时间',
            `match_end_time` datetime DEFAULT NULL COMMENT '比赛结束时间',
            `entry_end_time` datetime DEFAULT NULL COMMENT '报名结束时间',
            `match_status` tinyint(2) DEFAULT '1' COMMENT '比赛状态 -3:已结束 -2等待开赛 1:报名中 2:进行中',
            `match_address` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '比赛地点',
            `match_cost` decimal(10,2) DEFAULT NULL COMMENT '比赛费用',
            `match_max_number` smallint(10) DEFAULT NULL COMMENT '最大参与人数 值大于0才有限制',
            `match_project_id` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '比赛项目id合集',
            `created_id` int(20) DEFAULT NULL COMMENT '发布人',
            `created_time` datetime DEFAULT NULL COMMENT '创建时间',
            `match_notice_url` text DEFAULT NULL COMMENT '参赛须知链接',
              PRIMARY KEY (`id`,`match_id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "match_project_more";  //比赛项目轮数记录表

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
            `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
              `match_id` int(20) NOT NULL COMMENT '比赛id',
              `project_id` int(20) NOT NULL COMMENT '比赛项目id',
              `more` tinyint(5) DEFAULT NULL COMMENT '轮数',
              `start_time` datetime DEFAULT NULL COMMENT '开始时间',
              `end_time` datetime DEFAULT NULL COMMENT '结束时间',
              `use_time` smallint(10) DEFAULT NULL COMMENT '比赛时长',
              `status` tinyint(1) DEFAULT NULL COMMENT '状态 已结束-1 未开始1 进行中2',
              `created_id` int(20) DEFAULT NULL COMMENT '创建人id',
              `revise_id` int(20) DEFAULT NULL COMMENT '修改人id',
              `created_time` datetime DEFAULT NULL COMMENT '创建时间',
              `revise_time` datetime DEFAULT NULL COMMENT '编辑时间',
              PRIMARY KEY (`id`,`match_id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }


    $table_name = $wpdb->prefix . "user_post_use";  //用户文章速读使用记录  储存文章速读

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
            `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` int(20) NOT NULL,
            `type` tinyint(2) DEFAULT NULL COMMENT '题库类型 1 比赛题库 2 训练题库',
            `post_id` text COMMENT '已使用的文章id',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "user_train_logs";  //训练记录表  储存用户训练

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(20) NOT NULL,
          `genre_id` int(20) NOT NULL,
          `project_type` varchar(50) NOT NULL COMMENT '训练类型:szzb pkjl kysm等',
          `train_questions` longtext COMMENT '训练题目',
          `train_answer` longtext COMMENT '标准答案',
          `my_answer` longtext COMMENT '我的答案',
          `surplus_time` int(8) DEFAULT NULL COMMENT '剩余时间',
          `my_score` int(10) DEFAULT NULL COMMENT '成绩',
          `created_time` datetime DEFAULT NULL,
          `post_id` int(20) DEFAULT NULL COMMENT '文章id',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }


    $table_name = $wpdb->prefix . "match_sign";  //签到表  储存用户签到

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) NOT NULL AUTO_INCREMENT,
          `match_id` int(20) DEFAULT NULL,
          `user_id` int(20) DEFAULT NULL,
          `created_time` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }


    $table_name = $wpdb->prefix . "messages";  //消息表  储存用户消息

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (

          id int(20) unsigned NOT NULL AUTO_INCREMENT,
          user_id int(20) NOT NULL,
          type tinyint(2) DEFAULT NULL COMMENT '消息类型 1:平台 2:战队 3:个人 4:其他',
          title varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT '标题',
          content text CHARACTER SET utf8mb4 NOT NULL COMMENT '内容',
          status tinyint(2) NOT NULL DEFAULT '1' COMMENT '消息状态 1:正常 2:回收站',
          read_status tinyint(2) NOT NULL DEFAULT '1' COMMENT '消息状态 1:未读 2:已读',
          message_time datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "match_meta";  //比赛外键  储存比赛信息

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (

          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `match_id` int(20) NOT NULL COMMENT '比赛id(posts主键ID)',
          `match_slogan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '比赛口号',
          `match_genre` int(20) DEFAULT NULL COMMENT '比赛类型',
          `match_start_time` datetime DEFAULT NULL COMMENT '比赛时间',
          `entry_start_time` datetime DEFAULT NULL COMMENT '报名开始时间',
          `entry_end_time` datetime DEFAULT NULL COMMENT '报名结束时间',
          `match_status` tinyint(2) DEFAULT '-1' COMMENT '比赛状态 -3:已结束 -2:等待开赛 -1:未开始 1:报名中 2:进行中',
          `match_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '比赛地点',
          `match_cost` decimal(10,2) DEFAULT NULL COMMENT '比赛费用',
          `match_max_number` int(10) DEFAULT NULL COMMENT '最大参与人数 值大于0才有限制',
          `match_more` tinyint(5) DEFAULT '1' COMMENT '比赛轮数',
          `match_use_time` tinyint(5) DEFAULT NULL,
          `match_project_interval` tinyint(5) DEFAULT NULL COMMENT '项目间隔',
          `match_subject_interval` tinyint(5) DEFAULT NULL COMMENT '每轮题间隔',
          `match_category_order` varchar(255) DEFAULT NULL,
          `str_bit` tinyint(10) DEFAULT NULL COMMENT '初始字符长度',
          `child_count_down` tinyint(10) DEFAULT NULL COMMENT '子项倒计时',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "match_project";  //比赛项目外键  储存比赛项目信息

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (

          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `post_id` int(20) NOT NULL COMMENT '比赛id(posts表主键id)',
          `match_project_id` int(20) DEFAULT NULL COMMENT '比赛项目id',
          `project_use_time` tinyint(5) DEFAULT NULL COMMENT '比赛用时',
          `match_more` tinyint(3) DEFAULT NULL COMMENT '比赛轮数',
          `project_start_time` datetime DEFAULT NULL COMMENT '开始时间',
          `project_washing_out` tinyint(10) DEFAULT NULL COMMENT '淘汰率或淘汰人数',
          `project_time_interval` tinyint(5) DEFAULT NULL COMMENT '时间间隔',
          `str_bit` tinyint(5) DEFAULT NULL COMMENT '初始字符长度',
          `child_count_down` tinyint(5) DEFAULT NULL COMMENT '子项倒计时',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "order";  //订单表  存储订单信息

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (

          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `serialnumber` varchar(50) DEFAULT NULL,
          `user_id` int(20) NOT NULL COMMENT '学生id(user主键id)',
          `match_id` int(20) DEFAULT NULL COMMENT '参与的比赛id(posts表主键)',
          `sub_centres_id` int(20) DEFAULT NULL COMMENT '分中心',
          `fullname` varchar(255) NOT NULL,
          `telephone` varchar(50) NOT NULL COMMENT '联系电话',
          `address` text NOT NULL COMMENT '收货地址',
          `order_type` tinyint(2) NOT NULL COMMENT '订单类型 1:报名订单,2考级订单,3商品订单',
          `express_number` varchar(30) DEFAULT NULL COMMENT '快递单号',
          `express_company` text COMMENT '快递公司',
          `pay_type` varchar(255) DEFAULT NULL COMMENT '支付类型 支付宝:zfb 微信:wx 银联卡:ylk 其他:线下',
          `cost` decimal(10,2) DEFAULT NULL COMMENT '总价',
          `pay_status` tinyint(2) DEFAULT NULL COMMENT '支付状态 -2:已退款 -1:待退款 1:待支付 2:待发货 3:待收货 4:完成 5:订单失效',
          `pay_lowdown` text COMMENT '支付反馈信息',
          `created_time` datetime DEFAULT NULL COMMENT '创建时间',
          `memory_lv` tinyint(2) DEFAULT NULL COMMENT '记忆考级等级',
          `seat_number` smallint(10) DEFAULT NULL COMMENT '座位号',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "world";  //订单表  存储订单信息

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (

          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `title` varchar(255) DEFAULT NULL,
          `code` varchar(255) DEFAULT NULL,
          PRIMARY KEY (`id`)
          )ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "match_team";  //我的战队   储存我的战队信息

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `team_id` int(20) NOT NULL,
          `user_id` int(20) NOT NULL,
          `user_type` varchar(2) NOT NULL COMMENT '用户类型',
          `status` tinyint(2) DEFAULT NULL COMMENT '战队状态 -3:已退出;-2:已拒绝;-1:退队申请;1:入队申请;2:我的战队',
          `created_time` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }


    $table_name = $wpdb->prefix . "my_coach";  //我的教练   储存我的教练信息

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(20) NOT NULL,
          `category_id` int(20) NOT NULL COMMENT '比赛类别id',
          `coach_id` int(20) NOT NULL COMMENT '教练id',
          `apply_status` tinyint(2) DEFAULT NULL COMMENT '申请状态 -1:拒绝;1申请中;2通过',
          `major` tinyint(2) DEFAULT NULL COMMENT '是否设为主训教练',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }


    $table_name = $wpdb->prefix . "coach_skill";  //教练技能    储存教练技能信息

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `coach_id` int(20) NOT NULL COMMENT '教练id(user表主键id)',
          `read` int(20) DEFAULT NULL COMMENT '速读类',
          `memory` int(20) DEFAULT NULL COMMENT '速记类',
          `compute` int(20) DEFAULT NULL COMMENT '速算类',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }


    $table_name = $wpdb->prefix . "my_address";  //我的地址    储存用户收货地址

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(20) NOT NULL,
          `fullname` varchar(255) NOT NULL COMMENT '收件人',
          `telephone` varchar(50) NOT NULL COMMENT '联系电话',
          `country` varchar(255) DEFAULT NULL COMMENT '国家',
          `province` varchar(255) NOT NULL COMMENT '省份',
          `city` varchar(255) NOT NULL COMMENT '城市',
          `area` varchar(255) NOT NULL COMMENT '地区',
          `address` varchar(255) NOT NULL COMMENT '详细地址',
          `is_default` tinyint(2) DEFAULT NULL COMMENT '默认地址',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "team_meta";  //战队外键表    储存战队外键信息

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `team_id` int(20) NOT NULL COMMENT '战队id',
          `team_world` varchar(255) DEFAULT NULL COMMENT '战队国籍',
          `team_slogan` text COMMENT '战队口号',
          `team_director` int(20) DEFAULT NULL COMMENT '战队负责人',
          `max_number` int(10) DEFAULT NULL COMMENT '最大人数',
          `team_leader` int(20) DEFAULT NULL,
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "user_skill_rank";  //战队外键表    储存战队外键信息

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(20) NOT NULL COMMENT '用户id 学员/教练id',
          `read` varchar(50) DEFAULT NULL COMMENT '速读段位',
          `memory` varchar(50) DEFAULT NULL COMMENT '速记段位',
          `compute` varchar(50) DEFAULT NULL COMMENT '速算段位',
          `nationality` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '国籍',
          `mental_lv` tinyint(3) DEFAULT NULL COMMENT '脑力级别',
          `mental_type` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '脑力健将',
          `skill_type` tinyint(2) DEFAULT '1' COMMENT '考级类别 1 专业比赛  2 考级训练',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "match_questions";  //比赛考题提交记录表    储存用户的技能段位

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
          `user_id` int(20) DEFAULT NULL,
          `match_id` int(20) DEFAULT NULL COMMENT '比赛id',
          `project_id` int(20) DEFAULT NULL COMMENT '比赛项目id',
          `match_more` tinyint(20) DEFAULT NULL COMMENT '比赛的轮数',
          `match_questions` longtext CHARACTER SET utf8mb4 COMMENT '比赛试题',
          `questions_answer` longtext CHARACTER SET utf8mb4 COMMENT '考题答案',
          `my_answer` longtext CHARACTER SET utf8mb4 COMMENT '我的答案',
          `surplus_time` varchar(50) DEFAULT NULL COMMENT '消耗时间',
          `my_score` mediumint(10) DEFAULT NULL COMMENT '成绩',
          `answer_status` tinyint(2) DEFAULT NULL COMMENT '答题状态 -1：记忆完成 1：提交',
          `submit_type` tinyint(2) DEFAULT '1' COMMENT '提交方式 1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切换,系统提交',
          `leave_page_time` text DEFAULT NULL COMMENT '记录每次离开页面的时间',
          `created_time` datetime DEFAULT NULL COMMENT '创建时间',
          `created_microtime` float(8,5) DEFAULT NULL COMMENT '提交时间毫秒',
          `is_true` tinyint(2) DEFAULT '1' COMMENT '成绩真实性 1 真实 2 虚假',
          `post_id` int(20) DEFAULT NULL COMMENT '文章id',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }


    $table_name = $wpdb->prefix . "order_refund";  //订单申请退款表    储存退款信息

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `order_id` int(10) unsigned NOT NULL,
          `refund_no` varchar(64) NOT NULL,
          `refund_cost` decimal(14,2) unsigned DEFAULT NULL,
          `refund_lowdown` text COMMENT '第三方返回信息',
          `created_time` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `unquire` (`refund_no`) USING BTREE,
          KEY `indexs` (`order_id`) USING BTREE
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }


    $table_name = $wpdb->prefix . "problem_meta";  //题目问题外键表    储存问题选项以及答案

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `problem_id` int(20) NOT NULL COMMENT '题目问题id post表主键ID',
          `problem_select` text CHARACTER SET utf8mb4 NOT NULL COMMENT '问题选项',
          `problem_answer` varchar(5) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '问题答案',
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }


    $table_name = $wpdb->prefix . "feedback";  //意见反馈表

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在

        $sql = "CREATE TABLE " . $table_name . " (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `content` text NOT NULL,
          `contact` varchar(50) NOT NULL COMMENT '联系方式',
          `images` text,
          `created_time` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
          )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        //print_r($sql);
        dbDelta($sql);

    }

    $table_name = $wpdb->prefix . "match_bonus";  //奖金明细表

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在
        $sql = "CREATE TABLE `{$table_name}` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `match_id` int(10) unsigned NOT NULL,
              `user_id` int(10) unsigned NOT NULL,
              `all_bonus` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '奖金总额',
              `tax_send_bonus` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '税后发放额',
              `tax_all` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣税总额',
              `bonus_list` text NOT NULL COMMENT '奖项奖金列表',
              `is_send` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1未发放,2=发放',
              `real_name` varchar(64) NOT NULL,
              `userID` varchar(32) NOT NULL,
              `collect_name` varchar(255) DEFAULT NULL COMMENT '收款类型名称',
              `card_num` varchar(255) NOT NULL COMMENT '证件号码',
              `cart_type` varchar(32) NOT NULL COMMENT '证件类型',
              `mobile` varchar(32) DEFAULT NULL COMMENT '手机号码',
              `team` varchar(255) DEFAULT NULL COMMENT '战队名称',
              `is_user_view` tinyint(1) unsigned DEFAULT '2' COMMENT '1允许前台显示,2禁止前台显示',
              PRIMARY KEY (`id`),
              KEY `index` (`match_id`,`user_id`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "directories";  //名录表

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在
        $sql = "CREATE TABLE `{$table_name}` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(10) unsigned NOT NULL,
          `category_name` varchar(66) DEFAULT NULL COMMENT '类别名称',
          `level` varchar(33) NOT NULL COMMENT '等级',
          `match` text COMMENT '比赛id记录(1)(2)(3)',
          `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1显示,0显示',
          `range` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1中国,2国际',
          `type_name` varchar(255) NOT NULL COMMENT '名录类型名称',
          `certificate` varchar(64) DEFAULT NULL COMMENT '证书编号',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "match_bonus_tmp";  //比赛奖金模板表

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在
        $sql = "CREATE TABLE `{$table_name}` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `project1` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单项冠军奖金金额',
          `project2` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单项亚军奖金金额',
          `project3` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单项季军奖金金额',
          `category1` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '大类冠军奖金金额',
          `category2` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '大类亚军奖金金额',
          `category3` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '大季军奖金金额',
          `category_excellent` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '大类优秀选手奖金金额',
          `category1_age` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '大类年龄组冠军奖金金额',
          `category2_age` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '大类年龄组亚军奖金金额',
          `category3_age` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '大类年龄组季军奖金金额',
          `bonus_tmp_name` varchar(255) NOT NULL COMMENT '奖金模板名称',
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "spread_money_set";  //推广奖金项设置

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在
        $sql = "CREATE TABLE `{$table_name}` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `money_name` varchar(255) DEFAULT NULL COMMENT '名称',
          `project_type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '1考级报名费,2战队赛报名费,3城市赛报名费,4购买商品',
          `user_type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '1分中心,2教练,3用户,4战队',
          `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '获取金额',
          `is_enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1启用,2禁用',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "course";  //课程

    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在
        $sql = "CREATE TABLE `{$table_name}` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `course_title` varchar(255) NOT NULL,
          `course_details` mediumtext,
          `course_img` varchar(255) DEFAULT NULL,
          `const` decimal(10,2) unsigned NOT NULL,
          `is_enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1启用,2禁用',
          `teacher_id` int(10) unsigned NOT NULL,
          `course_start_time` datetime DEFAULT NULL COMMENT '开课时间',
          `course_end_time` datetime DEFAULT NULL,
          `created_time` datetime DEFAULT NULL COMMENT '课程结束时间',
          `province` varchar(180) NOT NULL COMMENT '省',
          `city` varchar(180) NOT NULL COMMENT '城市',
          `area` varchar(180) NOT NULL,
          `address` varchar(255) DEFAULT NULL COMMENT '详细地址',
          `open_quota` varchar(32) NOT NULL DEFAULT '0' COMMENT '开放名额',
          `seize_quota` varchar(32) NOT NULL DEFAULT '0' COMMENT '已抢占名额',
          `course_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1乐学乐',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4;";
        dbDelta($sql);
    }
}


the_table_install();