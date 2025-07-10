-- BEGIN TABLE admin_configs
DROP TABLE IF EXISTS admin_configs;
CREATE TABLE `admin_configs` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '参数主键',
  `config_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置名称',
  `config_key` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置键名',
  `config_options` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '可选的选项',
  `config_value` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置值',
  `is_allow_change` tinyint(1) NOT NULL COMMENT '是否允许修改',
  `remark` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '备注',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` bit(1) NOT NULL DEFAULT b'0' COMMENT '逻辑删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_key_uniq_idx` (`config_key`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='参数配置表';

-- Inserting 5 rows into admin_configs
-- Insert batch #1
INSERT INTO admin_configs (id, config_name, config_key, config_options, config_value, is_allow_change, remark, update_time, create_time, deleted) VALUES
(1, '主框架页-默认皮肤样式名称', 'sys.index.skinName', '["skin-blue","skin-green","skin-purple","skin-red","skin-yellow"]', 'skin-blue', 1, '蓝色 skin-blue、绿色 skin-green、紫色 skin-purple、红色 skin-red、黄色 skin-yellow', '2022-08-28 22:12:19', '2022-05-21 08:30:55', 0),
(2, '用户管理-账号初始密码', 'sys.user.initPassword', '', '1234567', 1, '初始化密码 123456', '2022-08-28 21:54:19', '2022-05-21 08:30:55', 0),
(3, '主框架页-侧边栏主题', 'sys.index.sideTheme', '["theme-dark","theme-light"]', 'theme-dark', 1, '深色主题theme-dark，浅色主题theme-light', '2022-08-28 22:12:15', '2022-08-20 08:30:55', 0),
(4, '账号自助-验证码开关', 'sys.account.captchaOnOff', '["true","false"]', 'false', 0, '是否开启验证码功能（true开启，false关闭）', '2022-08-28 22:03:37', '2022-05-21 08:30:55', 0),
(5, '账号自助-是否开启用户注册功能', 'sys.account.registerUser', '["true","false"]', 'true', 0, '是否开启注册用户功能（true开启，false关闭）', '2022-10-05 22:18:57', '2022-05-21 08:30:55', 0);

-- END TABLE admin_configs

-- BEGIN TABLE admin_menus
DROP TABLE IF EXISTS admin_menus;
CREATE TABLE `admin_menus` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(64) NOT NULL COMMENT '名称',
  `type` varchar(16) NOT NULL COMMENT '类型',
  `router_name` varchar(255) NOT NULL DEFAULT '' COMMENT '路由名称（需保持和前端对应的vue文件中的name保持一致defineOptions方法中设置的name）',
  `parent_id` int NOT NULL DEFAULT '0' COMMENT '父菜单ID',
  `path` varchar(255) DEFAULT NULL COMMENT '组件路径（对应前端项目view文件夹中的路径）',
  `is_button` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否按钮',
  `permission` varchar(128) DEFAULT NULL COMMENT '权限标识',
  `meta_info` json NOT NULL COMMENT '路由元信息（前端根据这个信息进行逻辑处理）',
  `status` varchar(16) NOT NULL COMMENT '菜单状态',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` bit(1) NOT NULL DEFAULT b'0' COMMENT '逻辑删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='菜单权限表';

-- Inserting 61 rows into admin_menus
-- Insert batch #1
INSERT INTO admin_menus (id, name, type, router_name, parent_id, `path`, is_button, permission, meta_info, status, create_time, update_time, deleted) VALUES
(1, '系统管理', 'CATALOG', '', 0, '/system', 0, '', '{"icon": "ep:management", "rank": 1, "title": "系统管理", "showParent": true}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(5, '管理员列表', 'PAGE', 'SystemUser', 1, '/system/user/index', 0, 'system:user:list', '{"icon": "ep:user-filled", "title": "管理员列表", "showParent": true}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-27 10:48:17', 0),
(6, '角色管理', 'PAGE', 'SystemRole', 1, '/system/role/index', 0, 'system:role:list', '{"icon": "ep:user", "title": "角色管理", "showParent": true}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(7, '菜单管理', 'PAGE', 'MenuManagement', 1, '/system/menu/index', 0, 'system:menu:list', '{"icon": "ep:menu", "title": "菜单管理", "showParent": true}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(20, '用户查询', 'ACTION', ' ', 5, '', 1, 'system:user:query', '{"title": "用户查询"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(21, '用户新增', 'ACTION', ' ', 5, '', 1, 'system:user:add', '{"title": "用户新增"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(22, '用户修改', 'ACTION', ' ', 5, '', 1, 'system:user:edit', '{"title": "用户修改"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(23, '用户删除', 'ACTION', ' ', 5, '', 1, 'system:user:remove', '{"title": "用户删除"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(24, '用户导出', 'ACTION', ' ', 5, '', 1, 'system:user:export', '{"title": "用户导出"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(25, '用户导入', 'ACTION', ' ', 5, '', 1, 'system:user:import', '{"title": "用户导入"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(26, '重置密码', 'ACTION', ' ', 5, '', 1, 'system:user:resetPwd', '{"title": "重置密码"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(27, '角色查询', 'ACTION', ' ', 6, '', 1, 'system:role:query', '{"title": "角色查询"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(28, '角色新增', 'ACTION', ' ', 6, '', 1, 'system:role:add', '{"title": "角色新增"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(29, '角色修改', 'ACTION', ' ', 6, '', 1, 'system:role:edit', '{"title": "角色修改"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(30, '角色删除', 'ACTION', ' ', 6, '', 1, 'system:role:remove', '{"title": "角色删除"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(31, '角色导出', 'ACTION', ' ', 6, '', 1, 'system:role:export', '{"title": "角色导出"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(32, '菜单查询', 'ACTION', ' ', 7, '', 1, 'system:menu:query', '{"title": "菜单查询"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(33, '菜单新增', 'ACTION', ' ', 7, '', 1, 'system:menu:add', '{"title": "菜单新增"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(34, '菜单修改', 'ACTION', ' ', 7, '', 1, 'system:menu:edit', '{"title": "菜单修改"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0),
(35, '菜单删除', 'ACTION', ' ', 7, '', 1, 'system:menu:remove', '{"title": "菜单删除"}', 'ENABLED', '2022-05-21 08:30:54', '2024-04-26 15:15:19', 0);

-- END TABLE admin_menus

-- BEGIN TABLE admin_notices
DROP TABLE IF EXISTS admin_notices;
CREATE TABLE `admin_notices` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '公告ID',
  `title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '公告标题',
  `type` smallint NOT NULL COMMENT '公告类型（1通知 2公告）',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '公告内容',
  `status` smallint NOT NULL DEFAULT '0' COMMENT '公告状态（1正常 0关闭）',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` bit(1) NOT NULL DEFAULT b'0' COMMENT '逻辑删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='通知公告表';

-- Inserting 1 row into admin_notices
-- Insert batch #1
INSERT INTO admin_notices (id, title, type, content, status, remark, create_time, update_time, deleted) VALUES
(1, '维护通知', 1, '维护内容', 1, '管理员', '2022-05-21 08:30:55', '2024-04-26 09:51:50', 0);

-- END TABLE admin_notices

-- BEGIN TABLE admin_role_menu
DROP TABLE IF EXISTS admin_role_menu;
CREATE TABLE `admin_role_menu` (
  `role_id` int NOT NULL COMMENT '角色ID',
  `menu_id` int NOT NULL COMMENT '菜单ID',
  PRIMARY KEY (`role_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='角色和菜单关联表';

-- Inserting 12 rows into admin_role_menu
-- Insert batch #1
INSERT INTO admin_role_menu (role_id, menu_id) VALUES
(2, 70),
(2, 71),
(2, 72),
(2, 73),
(2, 74),
(2, 75),
(2, 76),
(2, 77),
(2, 78),
(2, 79),
(2, 80),
(3, 1);

-- END TABLE admin_role_menu

-- BEGIN TABLE admin_roles
DROP TABLE IF EXISTS admin_roles;
CREATE TABLE `admin_roles` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色名称',
  `key` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色权限字符串',
  `sort_num` int NOT NULL COMMENT '显示顺序',
  `data_scope` smallint DEFAULT '1' COMMENT '数据范围（1：全部数据权限 2：自定数据权限 3: 本部门数据权限 4: 本部门及以下数据权限 5: 本人权限）',
  `dept_id_set` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '角色所拥有的部门数据权限',
  `status` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色状态',
  `remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '备注',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` bit(1) NOT NULL DEFAULT b'0' COMMENT '删除标志（0代表存在 1代表删除）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='角色信息表';

-- Inserting 4 rows into admin_roles
-- Insert batch #1
INSERT INTO admin_roles (id, name, `key`, sort_num, data_scope, dept_id_set, status, remark, create_time, update_time, deleted) VALUES
(1, '超级管理员', 'admin', 1, 1, '', 'ENABLED', '超级管理员', '2022-05-21 08:30:54', '2024-04-26 22:28:16', 0),
(2, '运营', 'operating', 3, 2, '', 'ENABLED', NULL, '2022-05-21 08:30:54', '2024-04-28 13:01:58', 0),
(3, '闲置角色', 'unused', 4, 2, '', 'DISABLED', '未使用的角色', '2022-05-21 08:30:54', '2024-04-27 10:42:56', 0);

-- END TABLE admin_roles

-- BEGIN TABLE admin_user_role
DROP TABLE IF EXISTS admin_user_role;
CREATE TABLE `admin_user_role` (
  `user_id` int NOT NULL COMMENT '用户ID',
  `role_id` int NOT NULL COMMENT '角色ID',
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户和角色关联表';

-- Inserting 16 rows into admin_user_role
-- Insert batch #1
INSERT INTO admin_user_role (user_id, role_id) VALUES
(1, 1),
(2, 2),
(3, 1),
(3, 2),
(110, 2),
(111, 2),
(112, 2),
(113, 2),
(114, 2),
(115, 2),
(116, 2),
(117, 2),
(118, 2),
(119, 2),
(120, 2),
(121, 2);

-- END TABLE admin_user_role

-- BEGIN TABLE admin_users
DROP TABLE IF EXISTS admin_users;
CREATE TABLE `admin_users` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户账号',
  `nickname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户昵称',
  `email` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '用户邮箱',
  `phone_number` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '手机号码',
  `gender` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'UNKNOWN' COMMENT '用户性别',
  `avatar` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '头像地址',
  `password` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `login_ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '最后登录IP',
  `login_date` datetime DEFAULT NULL COMMENT '最后登录时间',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '超级管理员标志（1是，0否）',
  `remark` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '备注',
  `status` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '帐号状态',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` bit(1) NOT NULL DEFAULT b'0' COMMENT '删除标志（0代表存在 1代表删除）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户信息表';

-- Inserting 1 row into admin_users
-- Insert batch #1
INSERT INTO admin_users (id, username, nickname, email, phone_number, gender, avatar, password, login_ip, login_date, is_admin, remark, status, create_time, update_time, deleted) VALUES
(1, 'admin', 'admin', NULL, NULL, 'MALE', '', '$2y$10$mv8EfQItnMhOH2.0qIbFo.hs7fzMHKtCY0AOM.pFY934BXiSLa472', '127.0.0.1', '2022-10-06 17:00:06', 1, '管理员', 'ENABLED', '2022-05-21 08:30:54', '2024-11-30 22:55:08', 0);

-- END TABLE admin_users