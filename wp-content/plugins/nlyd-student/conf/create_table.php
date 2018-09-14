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
          `fullname` varchar(255) NOT NULL,
          `telephone` varchar(50) NOT NULL COMMENT '联系电话',
          `address` text NOT NULL COMMENT '收货地址',
          `order_type` tinyint(2) NOT NULL COMMENT '订单类型 1:报名订单',
          `express_number` varchar(30) DEFAULT NULL COMMENT '快递单号',
          `express_company` text COMMENT '快递公司',
          `pay_type` varchar(255) DEFAULT NULL COMMENT '支付类型 支付宝:zfb 微信:wx 银联卡:ylk 其他:线下',
          `cost` decimal(10,2) DEFAULT NULL COMMENT '总价',
          `pay_status` tinyint(2) DEFAULT NULL COMMENT '支付状态 -2:已退款 -1:待退款 1:待支付 2:支付完成',
          `pay_lowdown` text COMMENT '支付反馈信息',
          `created_time` datetime DEFAULT NULL COMMENT '创建时间',
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
          `created_time` datetime DEFAULT NULL COMMENT '创建时间',
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


}


the_table_install();