
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cms_admin
-- ----------------------------
DROP TABLE IF EXISTS `cms_admin`;
CREATE TABLE `cms_admin`  (
                              `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                              `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
                              `pwd` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
                              `salt` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
                              `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '',
                              `thumb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
                              `theme` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'black' COMMENT '系统主题',
                              `mobile` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '',
                              `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '',
                              `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '备注',
                              `did` int NOT NULL DEFAULT 0 COMMENT '部门id',
                              `position_id` int NOT NULL DEFAULT 0 COMMENT '职位id',
                              `create_time` int NOT NULL DEFAULT 0,
                              `update_time` int NOT NULL DEFAULT 0,
                              `last_login_time` int NOT NULL DEFAULT 0,
                              `login_num` int NOT NULL DEFAULT 0,
                              `last_login_ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
                              `status` int NOT NULL DEFAULT 1 COMMENT '1正常,0禁止登录,-1删除',
                              PRIMARY KEY (`id`) USING BTREE,
                              UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '管理员表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_admin
-- ----------------------------
INSERT INTO `cms_admin` VALUES (1, 'admin', 'c9e86cd68c30456ab1fa4d847bfa7d45', 'LFjUm8V32sBINDGkA7Ru', '超级管理员', '/static/admin/images/icon.png', 'white', '', '', '', 1, 1, 1667440174, 1667440174, 1669115753, 18, '172.18.0.1', 1);
-- ----------------------------
-- Table structure for cms_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `cms_admin_group`;
CREATE TABLE `cms_admin_group`  (
                                    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
                                    `status` int NOT NULL DEFAULT 1,
                                    `rules` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '用户组拥有的规则id， 多个规则\",\"隔开',
                                    `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '备注',
                                    `create_time` int NOT NULL DEFAULT 0,
                                    `update_time` int NOT NULL DEFAULT 0,
                                    PRIMARY KEY (`id`) USING BTREE,
                                    UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '权限分组表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_admin_group
-- ----------------------------
INSERT INTO `cms_admin_group` VALUES (1, 'Administrator', 1, '1,4,9,10,11,12,17,18,19,20,21,22,29,30,31,32,75,76,77,78,80,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175', 'Super administrator, the system automatically assigns all operable permissions and menus', 0, 1669093661);

-- ----------------------------
-- Table structure for cms_admin_group_access
-- ----------------------------
DROP TABLE IF EXISTS `cms_admin_group_access`;
CREATE TABLE `cms_admin_group_access`  (
                                           `uid` int UNSIGNED NULL DEFAULT NULL,
                                           `group_id` int NULL DEFAULT NULL,
                                           `create_time` int NOT NULL DEFAULT 0,
                                           `update_time` int NOT NULL DEFAULT 0,
                                           UNIQUE INDEX `uid_group_id`(`uid`, `group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '权限分组和管理员的关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_admin_group_access
-- ----------------------------
INSERT INTO `cms_admin_group_access` VALUES (2, 1, 0, 0);
INSERT INTO `cms_admin_group_access` VALUES (1, 1, 0, 0);

-- ----------------------------
-- Table structure for cms_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `cms_admin_log`;
CREATE TABLE `cms_admin_log`  (
                                  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                  `uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
                                  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '昵称',
                                  `type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '操作类型',
                                  `action` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '操作动作',
                                  `subject` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '操作主体',
                                  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '操作标题',
                                  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '操作描述',
                                  `module` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '模块',
                                  `controller` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '控制器',
                                  `function` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '方法',
                                  `rule_menu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '节点权限路径',
                                  `ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '登录ip',
                                  `param_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作数据id',
                                  `param` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '参数json格式',
                                  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0删除 1正常',
                                  `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '后台操作日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_admin_module
-- ----------------------------
DROP TABLE IF EXISTS `cms_admin_module`;
CREATE TABLE `cms_admin_module`  (
                                     `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                     `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '模块名称',
                                     `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '模块所在目录，小写字母',
                                     `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '图标',
                                     `status` int NOT NULL DEFAULT 1 COMMENT '状态,0禁用,1正常',
                                     `type` int NOT NULL DEFAULT 2 COMMENT '模块类型,2普通模块,1系统模块',
                                     `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                     `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
                                     PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '功能模块表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `cms_admin_rule`;
CREATE TABLE `cms_admin_rule`  (
                                   `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                   `pid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '父id',
                                   `src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT 'url链接',
                                   `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '名称',
                                   `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '日志操作名称',
                                   `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '图标',
                                   `menu` int NOT NULL DEFAULT 0 COMMENT '是否是菜单,1是,2不是',
                                   `sort` int NOT NULL DEFAULT 1 COMMENT '越小越靠前',
                                   `status` int NOT NULL DEFAULT 1 COMMENT '状态,0禁用,1正常',
                                   `module` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '所属模块',
                                   `crud` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT 'crud标识',
                                   `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                   `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
                                   PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '菜单及权限表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_admin_rule
-- ----------------------------
INSERT INTO `cms_admin_rule` VALUES (1, 0, '', 'System Manager', 'System Manager', 'bi-gear', 1, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (4, 0, '', 'Platform user', 'Platform user', 'bi-people', 1, 4, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (9, 1, 'admin/conf/index', 'System configuration', 'System configuration', '', 1, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (10, 9, 'admin/conf/add', 'add/edit', 'Configuration Item', '', 2, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (11, 9, 'admin/conf/delete', 'delete', 'Configuration Item', '', 2, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (12, 9, 'admin/conf/edit', 'edit', 'Configuration Detail', '', 2, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (17, 1, 'admin/rule/index', 'Function node', 'Function node', '', 1, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (18, 17, 'admin/rule/add', 'add/edit', 'Function node', '', 2, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (19, 17, 'admin/rule/delete', 'delete', 'Function node', '', 2, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (20, 1, 'admin/role/index', 'Role Permissions', 'Role Permissions', '', 1, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (21, 20, 'admin/role/add', 'add/edit', 'Role Permissions', '', 2, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (22, 20, 'admin/role/delete', 'delete', 'Role Permissions', '', 2, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (29, 1, 'admin/admin/index', 'System user', 'System user', '', 1, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (30, 29, 'admin/admin/add', 'add/edit', 'System user', '', 2, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (31, 29, 'admin/admin/view', 'read', 'System user', '', 2, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (32, 29, 'admin/admin/delete', 'delete', 'System user', '', 2, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (75, 4, 'admin/user/index', 'Platform user', 'Platform user', '', 1, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (76, 75, 'admin/user/edit', 'edit', 'Platform user', '', 2, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (77, 75, 'admin/user/view', 'read', 'Platform user', '', 2, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (78, 75, 'admin/user/disable', 'disable/enable', 'Platform user', '', 2, 1, 1, '', '', 0, 0);
INSERT INTO `cms_admin_rule` VALUES (151, 0, '', 'Coupon', 'Coupon', ' bi-ticket-perforated', 1, 11, 1, '', 'voucher', 1668821876, 0);
INSERT INTO `cms_admin_rule` VALUES (152, 151, 'admin/voucher/datalist', 'Coupon list', 'Coupon list', '', 1, 0, 1, '', 'voucher', 1668821876, 0);
INSERT INTO `cms_admin_rule` VALUES (153, 152, 'admin/voucher/add', 'add', 'Coupon', '', 2, 0, 1, '', 'voucher', 1668821876, 0);
INSERT INTO `cms_admin_rule` VALUES (154, 152, 'admin/voucher/edit', 'edit', 'Coupon', '', 2, 0, 1, '', 'voucher', 1668821876, 0);
INSERT INTO `cms_admin_rule` VALUES (155, 152, 'admin/voucher/read', 'read', 'Coupon', '', 2, 0, 1, '', 'voucher', 1668821876, 0);
INSERT INTO `cms_admin_rule` VALUES (156, 152, 'admin/voucher/del', 'delete', 'Coupon', '', 2, 0, 1, '', 'voucher', 1668821876, 0);
INSERT INTO `cms_admin_rule` VALUES (157, 0, '', 'Recycling orders', 'Recycling orders', ' bi-card-checklist', 1, 12, 1, '', 'recycle_order', 1668840188, 0);
INSERT INTO `cms_admin_rule` VALUES (158, 157, 'admin/recycle_order/datalist', 'Recycling orders list', 'Recycling orders list', '', 1, 0, 1, '', 'recycle_order', 1668840188, 0);
INSERT INTO `cms_admin_rule` VALUES (159, 158, 'admin/recycle_order/add', 'add', 'Recycling orders', '', 2, 0, 1, '', 'recycle_order', 1668840188, 0);
INSERT INTO `cms_admin_rule` VALUES (160, 158, 'admin/recycle_order/edit', 'edit', 'Recycling orders', '', 2, 0, 1, '', 'recycle_order', 1668840188, 0);
INSERT INTO `cms_admin_rule` VALUES (161, 158, 'admin/recycle_order/read', 'read', 'Recycling orders', '', 2, 0, 1, '', 'recycle_order', 1668840188, 0);
INSERT INTO `cms_admin_rule` VALUES (162, 158, 'admin/recycle_order/del', 'delete', 'Recycling orders', '', 2, 0, 1, '', 'recycle_order', 1668840188, 0);
INSERT INTO `cms_admin_rule` VALUES (163, 0, '', 'Record of points', 'Record of points', ' bi-bookmark-star', 1, 13, 1, '', 'points_record', 1668912137, 0);
INSERT INTO `cms_admin_rule` VALUES (164, 163, 'admin/points_record/datalist', 'Record of points list', 'Record of points list', '', 1, 0, 1, '', 'points_record', 1668912137, 0);
INSERT INTO `cms_admin_rule` VALUES (165, 164, 'admin/points_record/add', 'add', 'Record of points', '', 2, 0, 1, '', 'points_record', 1668912137, 0);
INSERT INTO `cms_admin_rule` VALUES (166, 164, 'admin/points_record/approved', 'approval', 'Record of points', '', 2, 0, 1, '', 'points_record', 1668912137, 0);
INSERT INTO `cms_admin_rule` VALUES (167, 164, 'admin/points_record/read', 'read', 'Record of points', '', 2, 0, 1, '', 'points_record', 1668912137, 0);
INSERT INTO `cms_admin_rule` VALUES (168, 164, 'admin/points_record/del', 'delete', 'Record of points', '', 2, 0, 1, '', 'points_record', 1668912137, 0);
INSERT INTO `cms_admin_rule` VALUES (169, 75, 'admin/user/approved', 'approval', 'Platform user', '', 2, 1, 1, '', '', 1668935138, 0);
INSERT INTO `cms_admin_rule` VALUES (170, 0, '', 'Record of donation', 'Record of donation', ' bi-currency-pound', 1, 14, 1, '', 'donate_record', 1669093660, 0);
INSERT INTO `cms_admin_rule` VALUES (171, 170, 'admin/donate_record/datalist', 'Record of donation list', 'Record of donation list', '', 1, 0, 1, '', 'donate_record', 1669093660, 0);
INSERT INTO `cms_admin_rule` VALUES (172, 171, 'admin/donate_record/add', 'add', 'Record of donation', '', 2, 0, 1, '', 'donate_record', 1669093660, 0);
INSERT INTO `cms_admin_rule` VALUES (173, 171, 'admin/donate_record/edit', 'edit', 'Record of donation', '', 2, 0, 1, '', 'donate_record', 1669093660, 0);
INSERT INTO `cms_admin_rule` VALUES (174, 171, 'admin/donate_record/read', 'read', 'Record of donation', '', 2, 0, 1, '', 'donate_record', 1669093661, 0);
INSERT INTO `cms_admin_rule` VALUES (175, 171, 'admin/donate_record/del', 'delete', 'Record of donation', '', 2, 0, 1, '', 'donate_record', 1669093661, 0);

-- ----------------------------
-- Table structure for cms_article
-- ----------------------------
DROP TABLE IF EXISTS `cms_article`;
CREATE TABLE `cms_article`  (
                                `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                `cate_id` int NOT NULL DEFAULT 0 COMMENT '所属分类',
                                `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '标题',
                                `desc` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '摘要',
                                `thumb` int NOT NULL DEFAULT 0 COMMENT '缩略图:附件id',
                                `original` int NOT NULL DEFAULT 0 COMMENT '是否原创:1是,0否',
                                `origin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '来源或作者',
                                `origin_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '来源地址',
                                `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '内容',
                                `md_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'markdown内容',
                                `read` int NOT NULL DEFAULT 0 COMMENT '阅读量',
                                `type` tinyint NOT NULL DEFAULT 0 COMMENT '属性:1精华,2热门,3推荐',
                                `is_home` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否首页显示:0否,1是',
                                `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
                                `status` int NOT NULL DEFAULT 1 COMMENT '状态:1正常,0下架',
                                `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
                                `create_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
                                `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
                                `delete_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '文章::crud' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_article_cate
-- ----------------------------
DROP TABLE IF EXISTS `cms_article_cate`;
CREATE TABLE `cms_article_cate`  (
                                     `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                     `pid` int NOT NULL DEFAULT 0 COMMENT '父类ID',
                                     `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '分类名称',
                                     `keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '关键字',
                                     `desc` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '描述',
                                     `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
                                     `create_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
                                     `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
                                     `delete_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
                                     PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '文章分类::crud' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_article_keywords
-- ----------------------------
DROP TABLE IF EXISTS `cms_article_keywords`;
CREATE TABLE `cms_article_keywords`  (
                                         `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                         `aid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章ID',
                                         `keywords_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联关键字id',
                                         `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
                                         `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                         PRIMARY KEY (`id`) USING BTREE,
                                         INDEX `aid`(`aid`) USING BTREE,
                                         INDEX `inid`(`keywords_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '文章关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_config
-- ----------------------------
DROP TABLE IF EXISTS `cms_config`;
CREATE TABLE `cms_config`  (
                               `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                               `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '配置名称',
                               `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '配置标识',
                               `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '配置内容',
                               `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
                               `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                               `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '系统配置表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_department
-- ----------------------------
DROP TABLE IF EXISTS `cms_department`;
CREATE TABLE `cms_department`  (
                                   `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                   `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '部门名称',
                                   `pid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级部门id',
                                   `leader_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '部门负责人ID',
                                   `phone` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '部门联系电话',
                                   `remark` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '备注',
                                   `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
                                   `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                   `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
                                   PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '部门组织' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_donate_record
-- ----------------------------
DROP TABLE IF EXISTS `cms_donate_record`;
CREATE TABLE `cms_donate_record`  (
                                      `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                      `type` tinyint NOT NULL DEFAULT 1 COMMENT '捐款类型 1 stripe',
                                      `third_payment_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '第三方支付订单ID',
                                      `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '用户名称',
                                      `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '用户名称',
                                      `amount` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '捐赠金额',
                                      `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '手机',
                                      `email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '邮箱',
                                      `country` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '国家',
                                      `province` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '省',
                                      `city` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '城市',
                                      `address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '地址',
                                      `payment_status` tinyint NOT NULL DEFAULT 0 COMMENT '支付状态 -1支付失败 0待支付 1支付成功',
                                      `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态  -1删除 0禁用 1正常',
                                      `donate_ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '登录IP',
                                      `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                      `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
                                      PRIMARY KEY (`id`) USING BTREE,
                                      UNIQUE INDEX `udx_type_payment`(`third_payment_id`, `type`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '捐款记录::crud' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_file
-- ----------------------------
DROP TABLE IF EXISTS `cms_file`;
CREATE TABLE `cms_file`  (
                             `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                             `module` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '所属模块',
                             `sha1` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'sha1',
                             `md5` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'md5',
                             `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '原始文件名',
                             `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '文件名',
                             `filepath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '文件路径+文件名',
                             `filesize` int NOT NULL DEFAULT 0 COMMENT '文件大小',
                             `fileext` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '文件后缀',
                             `mimetype` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '文件类型',
                             `user_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '上传会员ID',
                             `uploadip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '上传IP',
                             `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0未审核1已审核-1不通过',
                             `create_time` int NOT NULL DEFAULT 0,
                             `admin_id` int NOT NULL COMMENT '审核者id',
                             `audit_time` int NOT NULL DEFAULT 0 COMMENT '审核时间',
                             `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '来源模块功能',
                             `use` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '用处',
                             `download` int NOT NULL DEFAULT 0 COMMENT '下载量',
                             PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '文件表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_gallery
-- ----------------------------
DROP TABLE IF EXISTS `cms_gallery`;
CREATE TABLE `cms_gallery`  (
                                `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                `cate_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类ID',
                                `type` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '属性:1精华,2热门,3推荐',
                                `is_home` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否首页显示:0否,1是',
                                `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态:0下架 1正常',
                                `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '图集名称',
                                `thumb` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '缩略图',
                                `desc` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '图集摘要',
                                `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '内容',
                                `user_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
                                `origin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '来源或作者',
                                `origin_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '来源地址',
                                `read` int NOT NULL DEFAULT 0 COMMENT '阅读量',
                                `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
                                `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
                                `create_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
                                `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
                                `delete_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '图集::crud' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_gallery_cate
-- ----------------------------
DROP TABLE IF EXISTS `cms_gallery_cate`;
CREATE TABLE `cms_gallery_cate`  (
                                     `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                     `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '图集分类名称',
                                     `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
                                     `pid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级id',
                                     `keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '关键字',
                                     `desc` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '描述',
                                     `create_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
                                     `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
                                     `delete_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
                                     PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '图集分类::crud' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_gallery_file
-- ----------------------------
DROP TABLE IF EXISTS `cms_gallery_file`;
CREATE TABLE `cms_gallery_file`  (
                                     `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                     `aid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '图集ID',
                                     `file_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件id',
                                     `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '图片名称',
                                     `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '标题',
                                     `desc` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '摘要',
                                     `filepath` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '图片路径',
                                     `link` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '链接地址',
                                     `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
                                     `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                     PRIMARY KEY (`id`) USING BTREE,
                                     INDEX `aid`(`aid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '图集关联表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_gallery_keywords
-- ----------------------------
DROP TABLE IF EXISTS `cms_gallery_keywords`;
CREATE TABLE `cms_gallery_keywords`  (
                                         `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                         `aid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '图集ID',
                                         `keywords_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联关键字id',
                                         `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                         PRIMARY KEY (`id`) USING BTREE,
                                         INDEX `aid`(`aid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '图集关联表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_goods
-- ----------------------------
DROP TABLE IF EXISTS `cms_goods`;
CREATE TABLE `cms_goods`  (
                              `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                              `cate_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类ID',
                              `type` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '属性:1精华,2热门,3推荐',
                              `is_home` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否首页显示:0否,1是',
                              `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '商品状态:0下架,1正常',
                              `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '商品名称',
                              `thumb` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '缩略图',
                              `banner` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '商品轮播图',
                              `tips` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '商品卖点，一句话推销',
                              `desc` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '商品摘要',
                              `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '内容',
                              `base_price` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '市场价格',
                              `price` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '实际价格',
                              `stocks` int NOT NULL DEFAULT 0 COMMENT '商品库存',
                              `sales` int NOT NULL DEFAULT 0 COMMENT '商品销量',
                              `address` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '商品发货地址',
                              `start_time` int UNSIGNED NULL DEFAULT 0 COMMENT '开始抢购时间',
                              `end_time` int UNSIGNED NULL DEFAULT 0 COMMENT '结束抢购时间',
                              `read` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '阅读量',
                              `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
                              `is_mail` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否包邮:0否,1是',
                              `tag_values` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '商品标签:1正品保证,2一年保修,3七天退换,4赠运费险,5闪电发货,6售后无忧',
                              `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
                              `create_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
                              `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '编辑时间',
                              `delete_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
                              PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '商品::crud' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_goods_cate
-- ----------------------------
DROP TABLE IF EXISTS `cms_goods_cate`;
CREATE TABLE `cms_goods_cate`  (
                                   `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                   `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '分类名称',
                                   `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
                                   `pid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级id',
                                   `keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '关键字',
                                   `desc` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '描述',
                                   `create_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
                                   `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
                                   `delete_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
                                   PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '商品分类::crud' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_goods_keywords
-- ----------------------------
DROP TABLE IF EXISTS `cms_goods_keywords`;
CREATE TABLE `cms_goods_keywords`  (
                                       `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                       `aid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品ID',
                                       `keywords_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联关键字id',
                                       `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态:-1删除 0禁用 1启用',
                                       `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                       PRIMARY KEY (`id`) USING BTREE,
                                       INDEX `aid`(`aid`) USING BTREE,
                                       INDEX `inid`(`keywords_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '商品关联表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_keywords
-- ----------------------------
DROP TABLE IF EXISTS `cms_keywords`;
CREATE TABLE `cms_keywords`  (
                                 `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                 `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '关键字名称',
                                 `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
                                 `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
                                 `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                 `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
                                 PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '关键字表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_links
-- ----------------------------
DROP TABLE IF EXISTS `cms_links`;
CREATE TABLE `cms_links`  (
                              `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                              `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '网站标题',
                              `logo` int NOT NULL DEFAULT 0 COMMENT '网站logo',
                              `src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '链接',
                              `target` int NOT NULL DEFAULT 1 COMMENT '是否新窗口打开，1是,0否',
                              `status` int NOT NULL DEFAULT 1 COMMENT '状态:1可用-1禁用',
                              `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
                              `create_time` int NOT NULL DEFAULT 0,
                              `update_time` int NOT NULL DEFAULT 0,
                              PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '友情链接' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_nav
-- ----------------------------
DROP TABLE IF EXISTS `cms_nav`;
CREATE TABLE `cms_nav`  (
                            `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                            `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
                            `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '标识',
                            `status` int NOT NULL DEFAULT 1 COMMENT '1可用-1禁用',
                            `desc` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
                            `create_time` int NOT NULL DEFAULT 0,
                            `update_time` int NOT NULL DEFAULT 0,
                            PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '导航' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_nav_info
-- ----------------------------
DROP TABLE IF EXISTS `cms_nav_info`;
CREATE TABLE `cms_nav_info`  (
                                 `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                 `pid` int NOT NULL DEFAULT 0,
                                 `nav_id` int UNSIGNED NOT NULL DEFAULT 0,
                                 `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '',
                                 `src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
                                 `param` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
                                 `target` int NOT NULL DEFAULT 0 COMMENT '是否新窗口打开,默认0,1新窗口打开',
                                 `status` int NOT NULL DEFAULT 1 COMMENT '1可用,-1禁用',
                                 `sort` int NOT NULL DEFAULT 0,
                                 `create_time` int NOT NULL DEFAULT 0,
                                 `update_time` int NOT NULL DEFAULT 0,
                                 PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '导航详情表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_pages
-- ----------------------------
DROP TABLE IF EXISTS `cms_pages`;
CREATE TABLE `cms_pages`  (
                              `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                              `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '页面名称',
                              `thumb` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '缩略图',
                              `banner` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '图集相册',
                              `desc` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '页面摘要',
                              `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '内容',
                              `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '页面状态:0下架,1正常',
                              `read` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '阅读量',
                              `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
                              `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT 'url文件名',
                              `template` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '前端模板',
                              `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
                              `create_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
                              `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '编辑时间',
                              `delete_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
                              PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '单页面::crud' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_pages_keywords
-- ----------------------------
DROP TABLE IF EXISTS `cms_pages_keywords`;
CREATE TABLE `cms_pages_keywords`  (
                                       `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                       `aid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '页面ID',
                                       `keywords_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联关键字id',
                                       `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
                                       `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                       PRIMARY KEY (`id`) USING BTREE,
                                       INDEX `aid`(`aid`) USING BTREE,
                                       INDEX `inid`(`keywords_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '单页面关联表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_points_record
-- ----------------------------
DROP TABLE IF EXISTS `cms_points_record`;
CREATE TABLE `cms_points_record`  (
                                      `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                      `user_id` int NOT NULL DEFAULT 0 COMMENT '用户ID',
                                      `order_id` int NOT NULL DEFAULT 0 COMMENT '订单ID',
                                      `type` tinyint NOT NULL DEFAULT 1 COMMENT '类型 1回收积分 2兑换代金券 3兑换现金',
                                      `voucher_id` int NOT NULL DEFAULT 0 COMMENT '代金券ID',
                                      `money_amount` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '兑换现金数额',
                                      `quantity` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '数量（负数为扣除积分）',
                                      `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '备注',
                                      `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态 -1删除 0待审核 1已审核',
                                      `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                      `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
                                      PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '用户积分记录::crud' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_position
-- ----------------------------
DROP TABLE IF EXISTS `cms_position`;
CREATE TABLE `cms_position`  (
                                 `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                 `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '岗位名称',
                                 `work_price` int NOT NULL DEFAULT 0 COMMENT '工时单价',
                                 `remark` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '备注',
                                 `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
                                 `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                 `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
                                 PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '岗位职称' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_recycle_order
-- ----------------------------
DROP TABLE IF EXISTS `cms_recycle_order`;
CREATE TABLE `cms_recycle_order`  (
                                      `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                      `user_id` int NOT NULL COMMENT '用户ID',
                                      `order_no` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '订单编号',
                                      `express_no` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '快递跟踪号',
                                      `shipment_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '运输单ID',
                                      `weight` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '核准重量',
                                      `quantity` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '核准数量',
                                      `points` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '核发积分',
                                      `pics` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '图片',
                                      `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '备注',
                                      `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态 -1删除 0已取消 1运输中 2已完成',
                                      `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                      `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
                                      PRIMARY KEY (`id`) USING BTREE,
                                      UNIQUE INDEX `udx_no`(`order_no`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '回收订单表::crud' ROW_FORMAT = Dynamic;


DROP TABLE IF EXISTS `cms_payout_status`;
CREATE TABLE `cms_payout_status`  (
                                      `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                      `record_id` int NOT NULL DEFAULT 0 COMMENT '提现记录ID',
                                      `batch_status` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '批量支出状态',
                                      `payout_batch_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '批量支出ID',
                                      `status` tinyint NOT NULL DEFAULT 1 COMMENT '',
                                      `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                      `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
                                      PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'paypal支出状态' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_search_keywords
-- ----------------------------
DROP TABLE IF EXISTS `cms_search_keywords`;
CREATE TABLE `cms_search_keywords`  (
                                        `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                        `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '关键字',
                                        `times` int NOT NULL DEFAULT 1 COMMENT '搜索次数',
                                        `type` tinyint NOT NULL DEFAULT 1 COMMENT '1,2',
                                        PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '搜索关键字表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_sitemap
-- ----------------------------
DROP TABLE IF EXISTS `cms_sitemap`;
CREATE TABLE `cms_sitemap`  (
                                `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                `sitemap_cate_id` int NOT NULL DEFAULT 0 COMMENT '分类id',
                                `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '标题',
                                `pc_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'pc端图片',
                                `pc_src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'pc端链接',
                                `mobile_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '移动端图片',
                                `mobile_src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '移动端链接',
                                `status` int NOT NULL DEFAULT 1 COMMENT '1可用-1禁用',
                                `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
                                `create_time` int NOT NULL DEFAULT 0,
                                `update_time` int NOT NULL DEFAULT 0,
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '网站地图内容表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_sitemap
-- ----------------------------

-- ----------------------------
-- Table structure for cms_sitemap_cate
-- ----------------------------
DROP TABLE IF EXISTS `cms_sitemap_cate`;
CREATE TABLE `cms_sitemap_cate`  (
                                     `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                     `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '分类名称',
                                     `status` int NOT NULL DEFAULT 1 COMMENT '1可用-1禁用',
                                     `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
                                     `create_time` int NOT NULL DEFAULT 0,
                                     `update_time` int NOT NULL DEFAULT 0,
                                     PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '网站地图分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_sitemap_cate
-- ----------------------------

-- ----------------------------
-- Table structure for cms_slide
-- ----------------------------
DROP TABLE IF EXISTS `cms_slide`;
CREATE TABLE `cms_slide`  (
                              `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                              `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
                              `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '标识',
                              `status` int NOT NULL DEFAULT 1 COMMENT '1可用-1禁用',
                              `desc` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
                              `create_time` int NOT NULL DEFAULT 0,
                              `update_time` int NOT NULL DEFAULT 0,
                              PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '幻灯片表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_slide_info
-- ----------------------------
DROP TABLE IF EXISTS `cms_slide_info`;
CREATE TABLE `cms_slide_info`  (
                                   `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                   `slide_id` int UNSIGNED NOT NULL DEFAULT 0,
                                   `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
                                   `desc` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
                                   `img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
                                   `src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
                                   `status` int NOT NULL DEFAULT 1 COMMENT '1可用-1禁用',
                                   `sort` int NOT NULL DEFAULT 0,
                                   `create_time` int NOT NULL DEFAULT 0,
                                   `update_time` int NOT NULL DEFAULT 0,
                                   PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '幻灯片详情表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_slide_info
-- ----------------------------

-- ----------------------------
-- Table structure for cms_user
-- ----------------------------
DROP TABLE IF EXISTS `cms_user`;
CREATE TABLE `cms_user`  (
                             `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
                             `user_no` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '用户编号',
                             `type` tinyint NOT NULL DEFAULT 1 COMMENT '用户类型 1普通用户，2商户',
                             `points` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '总积分',
                             `lock_points` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '锁定积分（扣除积分为负数）',
                             `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '用户名称',
                             `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '用户名称',
                             `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '账号',
                             `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '密码',
                             `salt` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '密码盐',
                             `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
                             `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '手机',
                             `mobile_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '手机绑定状态： 0未绑定 1已绑定',
                             `email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '邮箱',
                             `headimgurl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '头像',
                             `sex` tinyint(1) NOT NULL DEFAULT 0 COMMENT '性别 0:未知 1:女 2:男 ',
                             `desc` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '个人简介',
                             `country` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '国家',
                             `province` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '省',
                             `city` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '城市',
                             `company` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '公司',
                             `company_tax_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '公司税码',
                             `address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '公司地址',
                             `detail_address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '公司地址',
                             `postcode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '邮编',
                             `longitude` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '经度',
                             `latitude` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '纬度',
                             `depament` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '部门',
                             `position` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '职位',
                             `paypal_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT 'PayPal名称',
                             `paypal_account` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT 'PayPal账号',
                             `level` tinyint(1) NOT NULL DEFAULT 1 COMMENT '等级  默认是普通会员',
                             `approval_status` tinyint NOT NULL DEFAULT 0 COMMENT '用户审核状态 0未审核 1已审核',
                             `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态  -1删除 0禁用 1正常',
                             `last_login_time` int NOT NULL DEFAULT 0 COMMENT '最后登录时间',
                             `last_login_ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '最后登录IP',
                             `login_num` int NOT NULL DEFAULT 0,
                             `register_time` int NOT NULL DEFAULT 0 COMMENT '注册时间',
                             `register_ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '注册IP',
                             `update_time` int NOT NULL DEFAULT 0 COMMENT '信息更新时间',
                             PRIMARY KEY (`id`) USING BTREE,
                             UNIQUE INDEX `udx_email`(`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '用户表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_user_level
-- ----------------------------
DROP TABLE IF EXISTS `cms_user_level`;
CREATE TABLE `cms_user_level`  (
                                   `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                   `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '等级名称',
                                   `desc` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
                                   `status` int NOT NULL DEFAULT 1 COMMENT '状态:0禁用,1正常',
                                   `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                   `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
                                   PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '会员等级表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_user_log
-- ----------------------------
DROP TABLE IF EXISTS `cms_user_log`;
CREATE TABLE `cms_user_log`  (
                                 `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                 `uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
                                 `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '昵称',
                                 `type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '操作类型',
                                 `title` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '操作标题',
                                 `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '操作描述',
                                 `module` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '模块',
                                 `controller` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '控制器',
                                 `function` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '方法',
                                 `ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '登录ip',
                                 `param_id` int UNSIGNED NOT NULL COMMENT '操作ID',
                                 `param` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '参数json格式',
                                 `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0删除 1正常',
                                 `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                 PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '用户操作日志表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for cms_voucher
-- ----------------------------
DROP TABLE IF EXISTS `cms_voucher`;
CREATE TABLE `cms_voucher`  (
                                `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                `code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '券码',
                                `passwd` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '密码',
                                `value` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '面额',
                                `deduct_points` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '扣除积分',
                                `pics` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '图片',
                                `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '备注',
                                `status` tinyint NOT NULL DEFAULT 0 COMMENT '状态 -1作废 0待兑换 1已兑换',
                                `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
                                `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
                                PRIMARY KEY (`id`) USING BTREE,
                                UNIQUE INDEX `udx_code`(`code`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '代金券表::crud' ROW_FORMAT = Dynamic;


SET FOREIGN_KEY_CHECKS = 1;
