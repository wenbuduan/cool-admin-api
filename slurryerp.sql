/*
 Navicat Premium Data Transfer

 Source Server         : slurryerp
 Source Server Type    : MySQL
 Source Server Version : 80012
 Source Host           : localhost:3306
 Source Schema         : slurryerp

 Target Server Type    : MySQL
 Target Server Version : 80012
 File Encoding         : 65001

 Date: 08/07/2025 09:20:44
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for sys_configs
-- ----------------------------
DROP TABLE IF EXISTS `sys_configs`;
CREATE TABLE `sys_configs`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '参数主键',
  `config_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置名称',
  `config_key` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置键名',
  `config_options` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '可选的选项',
  `config_value` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置值',
  `is_allow_change` tinyint(1) NOT NULL COMMENT '是否允许修改',
  `remark` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` bit(1) NOT NULL DEFAULT b'0' COMMENT '逻辑删除',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `config_key_uniq_idx`(`config_key` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 100 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '参数配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_configs
-- ----------------------------
INSERT INTO `sys_configs` VALUES (1, '主框架页-默认皮肤样式名称', 'sys.index.skinName', '[\"skin-blue\",\"skin-green\",\"skin-purple\",\"skin-red\",\"skin-yellow\"]', 'skin-blue', 1, '蓝色 skin-blue、绿色 skin-green、紫色 skin-purple、红色 skin-red、黄色 skin-yellow', '2022-08-28 22:12:19', '2022-05-21 08:30:55', b'0');
INSERT INTO `sys_configs` VALUES (2, '用户管理-账号初始密码', 'sys.user.initPassword', '', '1234567', 1, '初始化密码 123456', '2022-08-28 21:54:19', '2022-05-21 08:30:55', b'0');
INSERT INTO `sys_configs` VALUES (3, '主框架页-侧边栏主题', 'sys.index.sideTheme', '[\"theme-dark\",\"theme-light\"]', 'theme-dark', 1, '深色主题theme-dark，浅色主题theme-light', '2022-08-28 22:12:15', '2022-08-20 08:30:55', b'0');
INSERT INTO `sys_configs` VALUES (4, '账号自助-验证码开关', 'sys.account.captchaOnOff', '[\"true\",\"false\"]', 'false', 0, '是否开启验证码功能（true开启，false关闭）', '2022-08-28 22:03:37', '2022-05-21 08:30:55', b'0');
INSERT INTO `sys_configs` VALUES (5, '账号自助-是否开启用户注册功能', 'sys.account.registerUser', '[\"true\",\"false\"]', 'true', 0, '是否开启注册用户功能（true开启，false关闭）', '2022-10-05 22:18:57', '2022-05-21 08:30:55', b'0');

-- ----------------------------
-- Table structure for sys_dictionary
-- ----------------------------
DROP TABLE IF EXISTS `sys_dictionary`;
CREATE TABLE `sys_dictionary`  (
  `dict_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '字典id',
  `dict_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '字典标识',
  `dict_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '字典名称',
  `sort_number` int(11) NOT NULL DEFAULT 0 COMMENT '排序号',
  `comments` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '备注',
  `deleted` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否删除,0否,1是',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`dict_id`) USING BTREE,
  UNIQUE INDEX `uk_dict_code`(`dict_code` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '系统字典表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_dictionary
-- ----------------------------
INSERT INTO `sys_dictionary` VALUES (1, 'sex', '性别', 0, '用户性别字典', 0, '2025-07-02 12:58:59', '2025-07-02 12:58:59');
INSERT INTO `sys_dictionary` VALUES (2, 'user_status', '用户状态', 0, '用户账户状态', 0, '2025-07-02 12:58:59', '2025-07-02 12:58:59');

-- ----------------------------
-- Table structure for sys_dictionary_data
-- ----------------------------
DROP TABLE IF EXISTS `sys_dictionary_data`;
CREATE TABLE `sys_dictionary_data`  (
  `dict_data_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '字典项id',
  `dict_id` int(11) NOT NULL COMMENT '字典id',
  `dict_data_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '字典项标识',
  `dict_data_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '字典项名称',
  `sort_number` int(11) NOT NULL DEFAULT 0 COMMENT '排序号',
  `comments` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '备注',
  `deleted` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否删除,0否,1是',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`dict_data_id`) USING BTREE,
  UNIQUE INDEX `uk_dict_item`(`dict_id` ASC, `dict_data_code` ASC) USING BTREE,
  INDEX `idx_dict_id`(`dict_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '字典项表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_dictionary_data
-- ----------------------------
INSERT INTO `sys_dictionary_data` VALUES (1, 1, 'male', '男', 1, NULL, 0, '2025-07-02 12:59:40', '2025-07-02 13:54:29');
INSERT INTO `sys_dictionary_data` VALUES (2, 1, 'female', '女', 2, NULL, 0, '2025-07-02 12:59:40', '2025-07-02 13:54:30');
INSERT INTO `sys_dictionary_data` VALUES (3, 1, 'unknown', '未知', 3, NULL, 0, '2025-07-02 12:59:40', '2025-07-02 13:54:32');
INSERT INTO `sys_dictionary_data` VALUES (4, 2, 'active', '启用', 1, NULL, 0, '2025-07-03 10:04:18', '2025-07-03 10:04:18');
INSERT INTO `sys_dictionary_data` VALUES (5, 2, 'disabled', '禁用', 2, NULL, 0, '2025-07-03 10:04:50', '2025-07-03 10:04:50');

-- ----------------------------
-- Table structure for sys_notices
-- ----------------------------
DROP TABLE IF EXISTS `sys_notices`;
CREATE TABLE `sys_notices`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公告ID',
  `title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '公告标题',
  `type` smallint(6) NOT NULL COMMENT '公告类型（1通知 2公告）',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '公告内容',
  `status` smallint(6) NOT NULL DEFAULT 0 COMMENT '公告状态（1正常 0关闭）',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` bit(1) NOT NULL DEFAULT b'0' COMMENT '逻辑删除',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 31 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '通知公告表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_notices
-- ----------------------------
INSERT INTO `sys_notices` VALUES (1, '维护通知', 1, '维护内容', 1, '管理员', '2022-05-21 08:30:55', '2024-04-26 09:51:50', b'0');

-- ----------------------------
-- Table structure for sys_organization
-- ----------------------------
DROP TABLE IF EXISTS `sys_organization`;
CREATE TABLE `sys_organization`  (
  `organization_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '机构id',
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '上级id,0是顶级',
  `organization_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '机构名称',
  `organization_full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '机构全称',
  `organization_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '机构代码',
  `organization_type` int(11) NOT NULL COMMENT '机构类型',
  `leader_id` int(11) NOT NULL COMMENT '负责人id',
  `sort_number` int(11) NOT NULL DEFAULT 0 COMMENT '排序号',
  `comments` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '备注',
  `deleted` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否删除,0否,1是',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`organization_id`) USING BTREE,
  UNIQUE INDEX `idx_org_code`(`organization_code` ASC) USING BTREE,
  INDEX `idx_parent_id`(`parent_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '组织机构表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_organization
-- ----------------------------
INSERT INTO `sys_organization` VALUES (1, 0, '研发一组', '研发一组', '', 4, 0, 0, NULL, 0, '2025-06-30 15:21:06', '2025-06-30 15:21:06');

-- ----------------------------
-- Table structure for sys_permissions
-- ----------------------------
DROP TABLE IF EXISTS `sys_permissions`;
CREATE TABLE `sys_permissions`  (
  `menuId` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '名称',
  `type` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '类型',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '路由名称（需保持和前端对应的vue文件中的name保持一致defineOptions方法中设置的name）',
  `parentId` int(11) NOT NULL DEFAULT 0 COMMENT '父菜单ID',
  `component` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '组件路径（对应前端项目view文件夹中的路径）',
  `menuType` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否按钮',
  `authority` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '权限标识',
  `meta` json NOT NULL COMMENT '路由元信息（前端根据这个信息进行逻辑处理）',
  `status` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '菜单状态',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` bit(1) NOT NULL DEFAULT b'0' COMMENT '逻辑删除',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '图标',
  `sortNumber` int(11) NULL DEFAULT NULL COMMENT '排序数字',
  `hide` int(2) NULL DEFAULT NULL COMMENT '是否隐藏',
  PRIMARY KEY (`menuId`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 88 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '菜单权限表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_permissions
-- ----------------------------
INSERT INTO `sys_permissions` VALUES (1, '系统管理', 'CATALOG', '', 0, '/system', b'0', '', '{\"icon\": \"ep:management\", \"rank\": 1, \"title\": \"系统管理\", \"showParent\": true}', 'ENABLED', '2022-05-21 08:30:54', '2025-06-27 11:58:33', b'0', 'IconProSettingOutlined', NULL, 0);
INSERT INTO `sys_permissions` VALUES (5, '管理员列表', 'PAGE', '/system/user', 1, '/system/user', b'0', 'system:user:list', '\"{\\\"lang\\\": {\\\"zh_TW\\\": \\\"用戶管理\\\", \\\"en\\\": \\\"User\\\"}}\"', 'ENABLED', '2022-05-21 08:30:54', '2025-06-27 13:58:10', b'0', 'IconProUserOutlined', 1, 0);
INSERT INTO `sys_permissions` VALUES (6, '角色管理', 'PAGE', '/system/role', 1, '/system/role', b'0', 'system:role:list', '\"{\\\"lang\\\": {\\\"zh_TW\\\": \\\"角色管理\\\", \\\"en\\\": \\\"Role\\\"}}\"', 'ENABLED', '2022-05-21 08:30:54', '2025-06-27 14:05:17', b'0', 'IconProIdcardOutlined', 2, 0);
INSERT INTO `sys_permissions` VALUES (7, '菜单管理', 'PAGE', '/system/menu', 1, '/system/menu', b'0', 'system:menu:list', '\"{\\\"lang\\\": {\\\"zh_TW\\\": \\\"選單管理\\\", \\\"en\\\": \\\"Menu\\\"}}\"', 'ENABLED', '2022-05-21 08:30:54', '2025-06-27 14:00:57', b'0', 'IconProAppstoreOutlined', 3, 0);
INSERT INTO `sys_permissions` VALUES (20, '用户查询', 'ACTION', ' ', 5, '', b'1', 'system:user:query', '{\"title\": \"用户查询\"}', 'ENABLED', '2022-05-21 08:30:54', '2025-06-26 16:06:37', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (21, '用户新增', 'ACTION', ' ', 5, '', b'1', 'system:user:add', '{\"title\": \"用户新增\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (22, '用户修改', 'ACTION', ' ', 5, '', b'1', 'system:user:edit', '{\"title\": \"用户修改\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (23, '用户删除', 'ACTION', ' ', 5, '', b'1', 'system:user:remove', '{\"title\": \"用户删除\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (24, '用户导出', 'ACTION', ' ', 5, '', b'1', 'system:user:export', '{\"title\": \"用户导出\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (25, '用户导入', 'ACTION', ' ', 5, '', b'1', 'system:user:import', '{\"title\": \"用户导入\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (26, '重置密码', 'ACTION', ' ', 5, '', b'1', 'system:user:resetPwd', '{\"title\": \"重置密码\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (27, '角色查询', 'CATALOG', '', 6, NULL, b'1', 'system:role:query', 'null', 'ENABLED', '2022-05-21 08:30:54', '2025-06-27 15:09:47', b'0', '', 2, 0);
INSERT INTO `sys_permissions` VALUES (28, '角色新增', 'ACTION', ' ', 6, '', b'1', 'system:role:add', '{\"title\": \"角色新增\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (29, '角色修改', 'ACTION', ' ', 6, '', b'1', 'system:role:edit', '{\"title\": \"角色修改\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (30, '角色删除', 'ACTION', ' ', 6, '', b'1', 'system:role:remove', '{\"title\": \"角色删除\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (31, '角色导出', 'ACTION', ' ', 6, '', b'1', 'system:role:export', '{\"title\": \"角色导出\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (32, '菜单查询', 'ACTION', ' ', 7, '', b'1', 'system:menu:query', '{\"title\": \"菜单查询\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (33, '菜单新增', 'ACTION', ' ', 7, '', b'1', 'system:menu:add', '{\"title\": \"菜单新增\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (34, '菜单修改', 'ACTION', ' ', 7, '', b'1', 'system:menu:edit', '{\"title\": \"菜单修改\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (35, '菜单删除', 'ACTION', ' ', 7, '', b'1', 'system:menu:remove', '{\"title\": \"菜单删除\"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', b'0', NULL, NULL, NULL);
INSERT INTO `sys_permissions` VALUES (83, '文档管理', 'PAGE', '/document/index', 0, '/document/index', b'0', '', 'null', 'ENABLED', '2025-05-19 15:24:25', '2025-06-27 14:53:32', b'0', 'IconElDocumentCopy', 11, 0);
INSERT INTO `sys_permissions` VALUES (84, '机构管理', 'PAGE', '/system/organization', 1, '/system/organization', b'0', '', '\"{\\\"lang\\\": {\\\"zh_TW\\\": \\\"機构管理\\\", \\\"en\\\": \\\"Organization\\\"}}\"', 'ENABLED', '2025-06-27 10:45:17', '2025-06-27 10:45:17', b'0', 'IconProCityOutlined', 4, 0);
INSERT INTO `sys_permissions` VALUES (86, 'test', 'PAGE', '/test/test', 0, '/test/test', b'0', '', 'null', 'ENABLED', '2025-06-27 15:58:57', '2025-06-27 16:16:47', b'1', 'IconProAnalysisOutlined', 222, 0);
INSERT INTO `sys_permissions` VALUES (87, '字典管理', 'PAGE', '/system/dictionary', 1, '/system/dictionary', b'0', '', '\"{\\\"hideFooter\\\":true, \\\"lang\\\": {\\\"zh_TW\\\": \\\"字典管理\\\", \\\"en\\\": \\\"Dictionary\\\"}}\"', 'ENABLED', '2025-07-02 10:26:22', '2025-07-02 10:26:22', b'0', 'IconProBookOutlined', 5, 0);

-- ----------------------------
-- Table structure for sys_role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `sys_role_permissions`;
CREATE TABLE `sys_role_permissions`  (
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `menu_id` int(11) NOT NULL COMMENT '菜单ID',
  PRIMARY KEY (`role_id`, `menu_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色和菜单关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_role_permissions
-- ----------------------------
INSERT INTO `sys_role_permissions` VALUES (2, 20);
INSERT INTO `sys_role_permissions` VALUES (2, 83);
INSERT INTO `sys_role_permissions` VALUES (3, 22);
INSERT INTO `sys_role_permissions` VALUES (112, 83);
INSERT INTO `sys_role_permissions` VALUES (114, 83);

-- ----------------------------
-- Table structure for sys_roles
-- ----------------------------
DROP TABLE IF EXISTS `sys_roles`;
CREATE TABLE `sys_roles`  (
  `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `role_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色名称',
  `role_code` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色权限字符串',
  `sort_num` int(11) NOT NULL COMMENT '显示顺序',
  `data_scope` smallint(6) NULL DEFAULT 1 COMMENT '数据范围（1：全部数据权限 2：自定数据权限 3: 本部门数据权限 4: 本部门及以下数据权限 5: 本人权限）',
  `dept_id_set` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '角色所拥有的部门数据权限',
  `status` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色状态',
  `comments` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` bit(1) NOT NULL DEFAULT b'0' COMMENT '删除标志（0代表存在 1代表删除）',
  PRIMARY KEY (`role_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 115 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_roles
-- ----------------------------
INSERT INTO `sys_roles` VALUES (1, '超级管理员', 'admin', 1, 1, '', 'ENABLED', '超级管理员', '2022-05-21 08:30:54', '2024-04-26 22:28:16', b'0');
INSERT INTO `sys_roles` VALUES (2, '运营', 'operating', 3, 2, '', 'ENABLED', NULL, '2022-05-21 08:30:54', '2025-05-21 11:22:11', b'0');
INSERT INTO `sys_roles` VALUES (3, '闲置角色', 'unused', 4, 2, '', 'ENABLED', '未使用的角色', '2022-05-21 08:30:54', '2025-07-02 14:05:26', b'0');
INSERT INTO `sys_roles` VALUES (112, '文档管理员', 'docManger', 2, 1, '', 'ENABLED', '文档管理员', '2025-06-30 13:49:23', '2025-06-30 14:44:53', b'1');
INSERT INTO `sys_roles` VALUES (113, '文档管理1', 'docmange1', 1, 1, '', 'ENABLED', NULL, '2025-06-30 14:45:15', '2025-06-30 14:45:21', b'1');
INSERT INTO `sys_roles` VALUES (114, '文档管理员', 'docManger', 3, 1, '', 'ENABLED', NULL, '2025-07-02 14:13:41', '2025-07-03 10:19:13', b'0');

-- ----------------------------
-- Table structure for sys_user_roles
-- ----------------------------
DROP TABLE IF EXISTS `sys_user_roles`;
CREATE TABLE `sys_user_roles`  (
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  PRIMARY KEY (`user_id`, `role_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户和角色关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_user_roles
-- ----------------------------
INSERT INTO `sys_user_roles` VALUES (1, 1);
INSERT INTO `sys_user_roles` VALUES (2, 2);
INSERT INTO `sys_user_roles` VALUES (3, 1);
INSERT INTO `sys_user_roles` VALUES (3, 2);
INSERT INTO `sys_user_roles` VALUES (110, 2);
INSERT INTO `sys_user_roles` VALUES (111, 2);
INSERT INTO `sys_user_roles` VALUES (112, 2);
INSERT INTO `sys_user_roles` VALUES (113, 2);
INSERT INTO `sys_user_roles` VALUES (114, 2);
INSERT INTO `sys_user_roles` VALUES (115, 2);
INSERT INTO `sys_user_roles` VALUES (116, 2);
INSERT INTO `sys_user_roles` VALUES (117, 2);
INSERT INTO `sys_user_roles` VALUES (118, 2);
INSERT INTO `sys_user_roles` VALUES (119, 2);
INSERT INTO `sys_user_roles` VALUES (120, 2);
INSERT INTO `sys_user_roles` VALUES (121, 2);
INSERT INTO `sys_user_roles` VALUES (122, 2);
INSERT INTO `sys_user_roles` VALUES (129, 114);
INSERT INTO `sys_user_roles` VALUES (130, 114);
INSERT INTO `sys_user_roles` VALUES (132, 114);

-- ----------------------------
-- Table structure for sys_users
-- ----------------------------
DROP TABLE IF EXISTS `sys_users`;
CREATE TABLE `sys_users`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户账号',
  `nickname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户昵称',
  `email` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户邮箱',
  `phone` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '手机号码',
  `gender` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'UNKNOWN' COMMENT '用户性别',
  `avatar` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '头像地址',
  `password` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `login_ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '最后登录IP',
  `login_date` datetime NULL DEFAULT NULL COMMENT '最后登录时间',
  `is_admin` tinyint(1) NOT NULL DEFAULT 0 COMMENT '超级管理员标志（1是，0否）',
  `remark` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '帐号状态（0正常，1冻结）',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` bit(1) NOT NULL DEFAULT b'0' COMMENT '删除标志（0代表存在 1代表删除）',
  `introduction` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '个人简介',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `udx_username`(`username` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 133 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_users
-- ----------------------------
INSERT INTO `sys_users` VALUES (1, 'admin', 'admin', NULL, NULL, 'male', '', '$2y$10$mv8EfQItnMhOH2.0qIbFo.hs7fzMHKtCY0AOM.pFY934BXiSLa472', '127.0.0.1', '2022-10-06 17:00:06', 1, '管理员', 0, '2022-05-21 08:30:54', '2025-07-02 15:04:09', b'0', NULL);
INSERT INTO `sys_users` VALUES (122, 'xiaowang', '小王', NULL, NULL, 'unknown', NULL, '$2y$10$IGsnjar.Um0s743jLB8atu3uJCf48bKuimdbSXuaMXZjiC8Rpl2Qe', NULL, NULL, 0, '文档管理员', 0, '2025-05-19 14:02:05', '2025-07-02 15:04:21', b'0', NULL);
INSERT INTO `sys_users` VALUES (130, 'zhangsan', '张三', NULL, NULL, 'female', NULL, '$2y$10$osRTmfmk8zSjMxDnG/jZWezOXGgnPap8Rawvs3T5WfwMIe4Wpac7W', NULL, NULL, 0, NULL, 0, '2025-07-02 14:57:04', '2025-07-02 15:02:10', b'0', NULL);
INSERT INTO `sys_users` VALUES (132, 'lisi', '李四', NULL, '12232121212', 'female', NULL, '$2y$10$Yz7tPjk96tz7Nh7srtbOUOenGNfsXoMx/mFJy811HAXVmOmRn7WgG', NULL, NULL, 0, NULL, 0, '2025-07-02 16:46:54', '2025-07-03 10:18:54', b'0', '1');

SET FOREIGN_KEY_CHECKS = 1;
