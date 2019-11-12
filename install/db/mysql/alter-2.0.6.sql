ALTER TABLE `b_smartcat_connector_profile` ADD COLUMN
    `PROJECT_ID` varchar(250) AFTER `IBLOCK_ID`;

ALTER TABLE `b_smartcat_connector_task` ADD COLUMN
    `STATS_BUILDED` enum('Y','N') NOT NULL DEFAULT 'N';