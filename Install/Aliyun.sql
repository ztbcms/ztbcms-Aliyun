-- ----------------------------
-- 阿里云配置表
-- ----------------------------
DROP TABLE IF EXISTS `cms_aliyun_config`;
CREATE TABLE `cms_aliyun_config`  (
  `id` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
  `access_key_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `access_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `add_time` int(15) UNSIGNED NULL DEFAULT NULL COMMENT '添加时间',
  `edit_time` int(15) UNSIGNED NULL DEFAULT NULL COMMENT '编辑时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;


-- ----------------------------
-- 阿里云号码保护配置表
-- ----------------------------
DROP TABLE IF EXISTS `cms_aliyun_phone_confing`;
CREATE TABLE `cms_aliyun_phone_confing`  (
  `id` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
  `aliyun_id` int(15) UNSIGNED NOT NULL COMMENT '阿里云id',
  `pool_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '号码池Key。请登录号码隐私保护控制台，在号码池管理中查看号码池Key。',
  `expiration` int(15) UNSIGNED NULL DEFAULT NULL COMMENT '默认过期时间',
  `add_time` int(15) UNSIGNED NULL DEFAULT NULL COMMENT '添加时间',
  `edit_time` int(15) UNSIGNED NULL DEFAULT NULL COMMENT '编辑时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------
-- 阿里云号码保护绑定表
-- ----------------------------
DROP TABLE IF EXISTS `cms_aliyun_phone_bind`;
CREATE TABLE `cms_aliyun_phone_bind`  (
  `id` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求状态码。OK代表请求成功',
  `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '状态码的描述。',
  `request_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求ID。',
  `secret_bind_dto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '绑定成功后返回的结构体。',
  `extension` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分机号码。接口BindAxb不涉及分机号码，请忽略该返回参数。',
  `secret_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '隐私号码，即X号码。',
  `subs_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '绑定关系ID。',
  `aliyun_id` int(15) UNSIGNED NULL DEFAULT NULL,
  `add_time` int(15) UNSIGNED NULL DEFAULT NULL COMMENT '绑定时间',
  `edit_time` int(15) UNSIGNED NULL DEFAULT NULL COMMENT '最后编辑时间',
  `bind_status` int(15) UNSIGNED NULL DEFAULT NULL COMMENT '绑定状态 1已绑定  2绑定失败 3已解绑',
  `un_bind_time` int(15) UNSIGNED NULL DEFAULT 0 COMMENT '解除绑定时间',
  `phone_no_a` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '号码A',
  `phone_no_b` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '号码B',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
