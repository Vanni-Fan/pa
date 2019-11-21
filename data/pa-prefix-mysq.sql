/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 80017
 Source Host           : 127.0.0.1:3306
 Source Schema         : pa

 Target Server Type    : MySQL
 Target Server Version : 80017
 File Encoding         : 65001

 Date: 04/11/2019 10:47:44
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for configs
-- ----------------------------
DROP TABLE IF EXISTS `pa_configs`;
CREATE TABLE `pa_configs`  (
  `config_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `type` enum('rule','attribute') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '配置类型',
  `menu_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '所属菜单',
  `is_action_name` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否有同名的Action',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '名称',
  `var_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '变量名',
  `var_default` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '变量默认值',
  `var_type` enum('list','hash','text') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '变量类型',
  `options` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '可选项',
  `options_type` enum('Input:text','Input:mail','Input:url','Input:tel','Input:mobile','Input:currency','Input:number','Input:password','Input:time','Input:date','Input:color','Select:single','Select:multiple','Select:tags','TextArea:simple','TextArea:ckeditor','TextArea:wyihtml5','File:image','File:file','File:multipeImages','File:multipeFiles','Check:checkbox','Check:radio') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '可选项类型',
  `is_enabled` tinyint(255) UNSIGNED NULL DEFAULT 1 COMMENT '是否可用',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '备注',
  `created_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `created_user` int(255) UNSIGNED NULL DEFAULT NULL COMMENT '创建者',
  `updated_user` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '更新者',
  PRIMARY KEY (`config_id`) USING BTREE,
  UNIQUE INDEX `type`(`type`, `menu_id`, `var_name`) USING BTREE,
  INDEX `menu_id`(`menu_id`) USING BTREE,
  CONSTRAINT `configs_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `pa_menus` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of configs
-- ----------------------------
INSERT INTO `pa_configs` VALUES (1, 'attribute', NULL, 1, '网站标题', 'site_name', '权限管理', 'text', '{"minLength":0,"maxLength":255}', 'Input:text', 1, '', NULL, 1572399079, NULL, 1);
INSERT INTO `pa_configs` VALUES (2, 'attribute', NULL, 0, '网站子标题', 'site_subname', '通用后台系统', 'text', '{"minLength":0,"maxLength":255}', 'Input:text', 1, '', NULL, 1572399099, NULL, 1);
INSERT INTO `pa_configs` VALUES (3, 'attribute', NULL, 0, '网站收藏夹图标', 'site_icon', 'https://permissionadmin.com/logo.png', 'text', NULL, 'Input:url', 1, '', NULL, 1572399191, NULL, 1);
INSERT INTO `pa_configs` VALUES (4, 'attribute', NULL, 0, '网站域名', 'site_domain', 'permissionadmin.com', 'text', '{"minLength":0,"maxLength":255}', 'Input:text', 1, '', NULL, 1572399210, NULL, 1);
INSERT INTO `pa_configs` VALUES (5, 'attribute', NULL, 0, '公司名称', 'site_compan_name', 'Permission Admin', 'text', '{"minLength":0,"maxLength":255}', 'Input:text', 1, '', NULL, 1572399224, NULL, 1);
INSERT INTO `pa_configs` VALUES (6, 'attribute', NULL, 0, '公司URL', 'site_compan_url', '', 'text', NULL, 'Input:url', 1, '', NULL, 1572399232, NULL, 1);
INSERT INTO `pa_configs` VALUES (7, 'attribute', NULL, 0, '域名简写', 'site_logogram', 'PA', 'text', '{"minLength":0,"maxLength":255}', 'Input:text', 1, '', NULL, 1572399238, NULL, 1);
INSERT INTO `pa_configs` VALUES (8, 'attribute', NULL, 0, '版本', 'site_version', '1.0.0', 'text', '{"minLength":0,"maxLength":255}', 'Input:text', 1, '', NULL, 1572330934, NULL, 1);

-- ----------------------------
-- Table structure for logs
-- ----------------------------
DROP TABLE IF EXISTS `pa_logs`;
CREATE TABLE `pa_logs`  (
  `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `user_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `request` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `server` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `created_time` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`log_id`) USING BTREE,
  INDEX `menu_id`(`menu_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `pa_menus` (`menu_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `pa_users` (`user_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE = InnoDB AUTO_INCREMENT = 164 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for menus
-- ----------------------------
DROP TABLE IF EXISTS `pa_menus`;
CREATE TABLE `pa_menus`  (
  `menu_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '菜单名称',
  `is_displayable` tinyint(255) UNSIGNED NULL DEFAULT NULL COMMENT '是否需要显示',
  `url_suffix` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'URL后缀',
  `router` json NULL COMMENT '路由信息',
  `params` json NULL COMMENT '额外路由参数',
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '图标',
  `parent_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '父级菜单ID',
  `index` tinyint(255) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `is_enabled` tinyint(255) UNSIGNED NULL DEFAULT 1 COMMENT '是否可用',
  `corner_mark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '角标回调函数',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '备注',
  `created_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
  `created_user` int(255) UNSIGNED NULL DEFAULT NULL COMMENT '创建者',
  `updated_user` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '修改者',
  PRIMARY KEY (`menu_id`) USING BTREE,
  INDEX `parent_id`(`parent_id`) USING BTREE,
  CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `pa_menus` (`menu_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '菜单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of menus
-- ----------------------------
INSERT INTO `pa_menus` VALUES (1, '欢迎页', NULL, NULL, '{"priority": 10, "namespace": "Power\\\\Controllers", "controller": "Index"}', '[]', 'fa fa-child', NULL, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `pa_menus` VALUES (2, '系统管理', NULL, NULL, NULL, '[]', 'fa fa-wrench', NULL, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `pa_menus` VALUES (3, '用户管理', NULL, NULL, '{"priority": 10, "namespace": "Power\\\\Controllers", "controller": "Users"}', '[]', 'fa fa-user', 2, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `pa_menus` VALUES (4, '角色管理', NULL, NULL, '{"priority": 10, "namespace": "Power\\\\Controllers", "controller": "Roles"}', '[]', 'fa fa-users', 2, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `pa_menus` VALUES (5, '菜单管理', NULL, NULL, '{"namespace": "Power\\\\Controllers", "controller": "Menus"}', '[]', 'fa fa-legal', 2, 6, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `pa_menus` VALUES (6, '配置管理', NULL, NULL, '{"namespace": "Power\\\\Controllers", "controller": "Configs"}', '[]', 'fa fa-cubes', 2, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `pa_menus` VALUES (8, '插件管理', NULL, '/admin/plugins', '{"namespace": "Power\\\\Controllers", "controller": "plugins"}', '[]', 'fa fa-gears', 2, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `pa_menus` VALUES (9, '系统日志', NULL, '/admin/logs', '{"namespace": "Power\\\\Controllers", "controller": "logs"}', '[]', 'fa fa-wpforms', 2, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `pa_menus` VALUES (26, 'API测试', NULL, '/GraphApi', '{"namespace": "plugins\\\\GraphQL\\\\Controllers", "controller": "graph-ql"}', '[]', '', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `pa_permissions`;
CREATE TABLE `pa_permissions`  (
  `permission_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '权限ID',
  `role_id` int(255) UNSIGNED NULL DEFAULT NULL COMMENT '角色ID',
  `type` enum('menu','config') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '是配置还是菜单',
  `menu_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '菜单ID',
  `config_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '配置ID',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '权限值',
  `created_time` int(11) UNSIGNED NULL DEFAULT NULL,
  `updated_time` int(11) UNSIGNED NULL DEFAULT NULL,
  `created_user` int(11) UNSIGNED NULL DEFAULT NULL,
  `updated_user` int(11) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`permission_id`) USING BTREE,
  INDEX `role_id`(`role_id`) USING BTREE,
  INDEX `menu_id`(`menu_id`) USING BTREE,
  INDEX `config_id`(`config_id`) USING BTREE,
  CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `pa_roles` (`role_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `permissions_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `pa_menus` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permissions_ibfk_3` FOREIGN KEY (`config_id`) REFERENCES `pa_configs` (`config_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 87 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '权限表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `pa_permissions` VALUES (1, 1, 'menu', 1, NULL, '255', NULL, NULL, NULL, NULL);
INSERT INTO `pa_permissions` VALUES (2, 1, 'menu', 3, NULL, '255', NULL, NULL, NULL, NULL);
INSERT INTO `pa_permissions` VALUES (3, 1, 'menu', 4, NULL, '255', NULL, NULL, NULL, NULL);
INSERT INTO `pa_permissions` VALUES (4, 1, 'menu', 5, NULL, '255', NULL, NULL, NULL, NULL);
INSERT INTO `pa_permissions` VALUES (5, 1, 'menu', 6, NULL, '255', NULL, NULL, NULL, NULL);
INSERT INTO `pa_permissions` VALUES (7, 1, 'menu', 8, NULL, '255', NULL, NULL, NULL, NULL);
INSERT INTO `pa_permissions` VALUES (8, 1, 'menu', 9, NULL, '255', NULL, NULL, NULL, NULL);
INSERT INTO `pa_permissions` VALUES (39, NULL, 'menu', 1, NULL, '224', 1572534132, 1572534132, 1, 1);
INSERT INTO `pa_permissions` VALUES (40, NULL, 'menu', 3, NULL, '28', 1572534132, 1572534132, 1, 1);
INSERT INTO `pa_permissions` VALUES (41, NULL, 'menu', 4, NULL, '224', 1572534132, 1572534132, 1, 1);
INSERT INTO `pa_permissions` VALUES (42, NULL, 'menu', 6, NULL, '16', 1572534132, 1572534132, 1, 1);
INSERT INTO `pa_permissions` VALUES (43, NULL, 'menu', 8, NULL, '16', 1572534132, 1572534132, 1, 1);
INSERT INTO `pa_permissions` VALUES (44, NULL, 'menu', 9, NULL, '16', 1572534132, 1572534132, 1, 1);
INSERT INTO `pa_permissions` VALUES (45, NULL, 'menu', 5, NULL, '16', 1572534132, 1572534132, 1, 1);
INSERT INTO `pa_permissions` VALUES (46, NULL, 'menu', 26, NULL, '16', 1572534132, 1572534132, 1, 1);
INSERT INTO `pa_permissions` VALUES (51, NULL, 'menu', 1, NULL, '32', 1572534308, 1572534308, 1, 1);
INSERT INTO `pa_permissions` VALUES (52, NULL, 'menu', 3, NULL, '32', 1572534308, 1572534308, 1, 1);
INSERT INTO `pa_permissions` VALUES (53, NULL, 'menu', 4, NULL, '32', 1572534308, 1572534308, 1, 1);
INSERT INTO `pa_permissions` VALUES (54, NULL, 'menu', 6, NULL, '64', 1572534308, 1572534308, 1, 1);
INSERT INTO `pa_permissions` VALUES (55, NULL, 'menu', 8, NULL, '128', 1572534308, 1572534308, 1, 1);
INSERT INTO `pa_permissions` VALUES (56, NULL, 'menu', 9, NULL, '64', 1572534308, 1572534308, 1, 1);
INSERT INTO `pa_permissions` VALUES (57, NULL, 'menu', 5, NULL, '32', 1572534308, 1572534308, 1, 1);
INSERT INTO `pa_permissions` VALUES (58, NULL, 'menu', 26, NULL, '32', 1572534308, 1572534308, 1, 1);
INSERT INTO `pa_permissions` VALUES (69, NULL, 'menu', 1, NULL, '128', 1572534524, 1572534524, 1, 1);
INSERT INTO `pa_permissions` VALUES (70, NULL, 'menu', 3, NULL, '128', 1572534524, 1572534524, 1, 1);
INSERT INTO `pa_permissions` VALUES (81, NULL, 'menu', 1, NULL, '128', 1572535100, 1572535100, 1, 1);
INSERT INTO `pa_permissions` VALUES (82, NULL, 'menu', 3, NULL, '128', 1572535100, 1572535100, 1, 1);
INSERT INTO `pa_permissions` VALUES (83, NULL, 'menu', 4, NULL, '32', 1572535100, 1572535100, 1, 1);
INSERT INTO `pa_permissions` VALUES (84, NULL, 'menu', 6, NULL, '32', 1572535100, 1572535100, 1, 1);
INSERT INTO `pa_permissions` VALUES (87, NULL, 'menu', 3, NULL, '32', 1572794901, 1572794901, 1, 1);
INSERT INTO `pa_permissions` VALUES (88, NULL, 'menu', 4, NULL, '32', 1572794901, 1572794901, 1, 1);

-- ----------------------------
-- Table structure for plugins
-- ----------------------------
DROP TABLE IF EXISTS `pa_plugins`;
CREATE TABLE `pa_plugins`  (
  `plugin_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '插件ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '名称',
  `class_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '类名',
  `is_enabled` tinyint(255) UNSIGNED NULL DEFAULT 1 COMMENT '是否可用',
  `icon_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '图标URL',
  `images` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '图片列表',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '详情',
  `permission` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '权限',
  `official_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '官网',
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '作者',
  `author_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '作者网站',
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '当前版本',
  `match_version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '匹配的版本',
  `license` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '版权',
  `publish_date` datetime(0) NULL DEFAULT NULL COMMENT '发布时间',
  `created_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `created_user` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '创建者',
  `updated_user` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '更新者',
  PRIMARY KEY (`plugin_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '插件表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of plugins
-- ----------------------------
INSERT INTO `pa_plugins` VALUES (2, 'Proxy', NULL, 1, 'https://ps.w.org/wp-mail-smtp/assets/icon-256x256.png?rev=1755440', NULL, '让用户能使用PA自动登录第三方管理后台，限制第三方提供的功能或者提供额外的功能', NULL, 'http://pa.com', 'Vanni Fan', 'http://vanni.fan', '1.0', '~1.0', 'BSD', '2019-08-14 09:46:48', NULL, NULL, NULL, NULL);
INSERT INTO `pa_plugins` VALUES (9, 'Tables', NULL, 1, NULL, NULL, '对MySQL表进行增删改查的简易操作', NULL, 'http://pa.com', 'Vanni Fan', 'http://vanni.fan', '1.0', '^1.0', 'BSD', '2019-10-26 09:06:31', NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `pa_roles`;
CREATE TABLE `pa_roles`  (
  `role_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '角色名称',
  `is_enabled` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否可用',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '备注',
  `created_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `created_user` int(255) UNSIGNED NULL DEFAULT NULL COMMENT '创建者',
  `updated_user` int(255) UNSIGNED NULL DEFAULT NULL COMMENT '修改者',
  PRIMARY KEY (`role_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `pa_roles` VALUES (1, '系统管理员', 1, NULL, NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for user_configs
-- ----------------------------
DROP TABLE IF EXISTS `pa_user_configs`;
CREATE TABLE `pa_user_configs`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `config_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `user_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `menu_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `is_enabled` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_time` int(11) UNSIGNED NULL DEFAULT NULL,
  `updated_time` int(11) UNSIGNED NULL DEFAULT NULL,
  `created_user` int(11) UNSIGNED NULL DEFAULT NULL,
  `updated_user` int(11) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `config_id`(`config_id`, `user_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `menu_id`(`menu_id`) USING BTREE,
  CONSTRAINT `user_configs_ibfk_1` FOREIGN KEY (`config_id`) REFERENCES `pa_configs` (`config_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_configs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `pa_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_configs_ibfk_3` FOREIGN KEY (`menu_id`) REFERENCES `pa_menus` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `pa_users`;
CREATE TABLE `pa_users`  (
  `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `role_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '角色',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '登录名',
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '用户名',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '密码',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '头像',
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '手机号',
  `is_enabled` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否可用',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '备注',
  `created_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `created_user` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '创建者',
  `updated_user` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '修改者',
  PRIMARY KEY (`user_id`) USING BTREE,
  INDEX `role_id`(`role_id`) USING BTREE,
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `pa_roles` (`role_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `pa_users` VALUES (1, 1, 'admin', '管理员', '$2y$10$TKQCJlKzwLsOcvlW9cEso.ImT8c4E2OsYy5pMZu.a2xQ75gS/ygDi', NULL, '', 1, NULL, NULL, 1572797595, NULL, 1);

SET FOREIGN_KEY_CHECKS = 1;
