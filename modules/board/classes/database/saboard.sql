SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `sa_board` (
  `board_index` int(15) NOT NULL AUTO_INCREMENT,
  `board_id` varchar(200) DEFAULT NULL,
  `board_depth` int(5) DEFAULT NULL,
  `board_order` int(5) DEFAULT NULL,
  `board_parent` int(5) DEFAULT NULL,
  `board_grp` int(5) DEFAULT NULL,
  `board_user_id` varchar(20) DEFAULT NULL,
  `board_user_nm` varchar(200) DEFAULT NULL,
  `board_password` varchar(255) DEFAULT NULL,
  `board_title` varchar(200) DEFAULT NULL,
  `board_content` longtext,
  `board_has_file` varchar(5) DEFAULT NULL,
  `board_read_cnt` int(200) DEFAULT NULL,
  `board_reg_date` varchar(40) DEFAULT NULL,
  `board_reg_ip` varchar(100) DEFAULT NULL,
  `board_expansion` longtext,
  `board_secret` varchar(5) DEFAULT NULL,
  `board_attach_image` longtext,
  PRIMARY KEY (`board_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sa_board_file` (
  `board_file_index` int(15) NOT NULL AUTO_INCREMENT,
  `board_file_id` int(15) NOT NULL,
  `board_file_name` varchar(120) NOT NULL,
  `board_file_size` int(10) NOT NULL,
  `board_file_reg_date` datetime NOT NULL,
  `board_file_oriname` varchar(120) NOT NULL,
  `board_file_seq` int(15) NOT NULL,
  PRIMARY KEY (`board_file_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sa_board_reply` (
  `board_reply_index` int(15) NOT NULL AUTO_INCREMENT,
  `board_reply_id` int(15) NOT NULL,
  `board_reply_depth` int(200) DEFAULT NULL,
  `board_reply_order` int(200) DEFAULT NULL,
  `board_reply_parent` int(200) DEFAULT NULL,
  `board_reply_grp` int(200) DEFAULT NULL,
  `board_reply_user_id` varchar(20) DEFAULT NULL,
  `board_reply_user_nm` varchar(20) DEFAULT NULL,
  `board_reply_user_ip` varchar(200) DEFAULT NULL,
  `board_reply_password` varchar(20) DEFAULT NULL,
  `board_reply_email` varchar(20) DEFAULT NULL,
  `board_reply_title` varchar(200) DEFAULT NULL,
  `board_reply_content` longtext,
  `board_reply_reg_date` varchar(40) NOT NULL,
  `board_reply_use_yn` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`board_reply_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sa_board_tables` (
  `board_table_index` int(15) NOT NULL AUTO_INCREMENT,
  `board_table_id` varchar(200) NOT NULL,
  `board_table_nm` varchar(200) DEFAULT NULL,
  `board_table_desc` varchar(200) DEFAULT NULL,
  `board_table_theme` varchar(200) DEFAULT NULL,
  `board_table_theme_pagination` varchar(200) DEFAULT NULL,
  `board_table_theme_reply` varchar(200) DEFAULT NULL,
  `board_table_theme_search` varchar(100) DEFAULT NULL,
  `board_table_write_role` varchar(100) DEFAULT NULL,
  `board_table_read_role` varchar(100) DEFAULT NULL,
  `board_table_list_cnt` int(50) DEFAULT NULL,
  `board_table_file_cnt` int(50) DEFAULT NULL,
  `board_table_file_max_size` int(200) DEFAULT NULL,
  `board_table_title_cut` int(50) DEFAULT NULL,
  `board_table_reply_useyn` varchar(5) DEFAULT NULL,
  `board_table_secret_useyn` varchar(5) DEFAULT NULL,
  `board_table_comment_useyn` varchar(5) DEFAULT NULL,
  `board_table_search_useyn` varchar(5) DEFAULT NULL,
  `board_table_default_content` longtext,
  `board_table_seo_useyn` varchar(5) DEFAULT NULL,
  `board_table_show_columns` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`board_table_index`),
  UNIQUE KEY `index_board_table_id` (`board_table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sa_board_group` (
  `board_group_index` int(11) NOT NULL AUTO_INCREMENT,
  `board_group_id` varchar(200) NOT NULL DEFAULT '',
  `board_group_nm` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`board_group_index`),
  UNIQUE KEY `index_board_group_id` (`board_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;