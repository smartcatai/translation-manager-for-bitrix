SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `b_smartcat_connector_profile`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `b_smartcat_connector_profile` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `ACTIVE` enum('Y','N') NOT NULL DEFAULT 'N',
  `PUBLISH` enum('Y','N') NOT NULL DEFAULT 'N',
  `AUTO_ORDER` enum('Y','N') NOT NULL DEFAULT 'N',
  `IBLOCK_ID` int(11) NOT NULL,
  `LANG` varchar(5) NOT NULL,
  `FIELDS` text,
  `WORKFLOW` varchar(100) NOT NULL,
  `VENDOR` text,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `b_smartcat_connector_profile_iblock`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `b_smartcat_connector_profile_iblock` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PROFILE_ID` int(11) NOT NULL,
  `IBLOCK_ID` int(11) NOT NULL,
  `LANG` varchar(10) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `b_smartcat_connector_task`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `b_smartcat_connector_task` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PROFILE_ID` int(11) NOT NULL,
  `ELEMENT_ID` int(11) NOT NULL,
  `STATUS` varchar(1) NOT NULL DEFAULT 'N',
  `PROJECT_ID` varchar(100) DEFAULT NULL,
  `PROJECT_NAME` varchar(100) DEFAULT NULL,
  `DATE_CREATE` datetime DEFAULT NULL,
  `DATE_UPDATE` datetime DEFAULT NULL,
  `CONTENT` mediumtext,
  `COMMENT` varchar(255) DEFAULT NULL,
  `AMOUNT` float(11,2) DEFAULT NULL,
  `CURRENCY` varchar(5) DEFAULT NULL,
  `VENDOR` varchar(100) NOT NULL,
  `DEADLINE` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `b_smartcat_connector_task_file`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `b_smartcat_connector_task_file` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TASK_ID` int(11) NOT NULL,
  `ELEMENT_ID` int(11) DEFAULT NULL,
  `LANG_FROM` varchar(10) NOT NULL,
  `LANG_TO` varchar(10) NOT NULL,
  `DOCUMENT_ID` varchar(255) DEFAULT NULL,
  `EXPORT_TASK_ID` varchar(255) DEFAULT NULL,
  `TRANSLATION` mediumtext,
  `STATUS` varchar(1) NOT NULL DEFAULT 'N',
  `DATE_CREATE` datetime DEFAULT NULL,
  `DATE_UPDATE` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
