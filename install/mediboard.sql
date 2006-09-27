-- --------------------------------------------------------

-- 
-- Table structure for table `message`
-- 

CREATE TABLE `message` (
  `message_id` int(10) unsigned NOT NULL auto_increment,
  `deb` datetime NOT NULL default '0000-00-00 00:00:00',
  `fin` datetime NOT NULL default '0000-00-00 00:00:00',
  `titre` varchar(40) NOT NULL default '',
  `corps` text NOT NULL,
  PRIMARY KEY  (`message_id`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `message`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `modules`
-- 

CREATE TABLE `modules` (
  `mod_id` int(11) NOT NULL auto_increment,
  `mod_name` varchar(64) NOT NULL default '',
  `mod_directory` varchar(64) NOT NULL default '',
  `mod_version` varchar(10) NOT NULL default '',
  `mod_setup_class` varchar(64) NOT NULL default '',
  `mod_type` varchar(64) NOT NULL default '',
  `mod_active` int(1) unsigned NOT NULL default '0',
  `mod_ui_name` varchar(20) NOT NULL default '',
  `mod_ui_icon` varchar(64) NOT NULL default '',
  `mod_ui_order` tinyint(3) NOT NULL default '0',
  `mod_ui_active` int(1) unsigned NOT NULL default '0',
  `mod_description` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`mod_id`,`mod_directory`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `modules`
-- 

INSERT INTO `modules` VALUES (1, 'admin', 'admin', '1.0.0', '', 'core', 1, 'User Admin', 'admin.png', 1, 1, '');
INSERT INTO `modules` VALUES (2, 'system', 'system', '1.0.0', '', 'core', 1, 'System Admin', 'system.png', 2, 1, '');
INSERT INTO `modules` VALUES (3, 'dPetablissement', 'dPetablissement', '0.1', '', 'core', 1, 'Groups admin', 'dPetablissement.png', 3, 1, '');

-- --------------------------------------------------------

-- 
-- Table structure for table `permissions`
-- 

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL auto_increment,
  `permission_user` int(11) NOT NULL default '0',
  `permission_grant_on` varchar(12) NOT NULL default '',
  `permission_item` int(11) NOT NULL default '0',
  `permission_value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`permission_id`),
  UNIQUE KEY `idx_pgrant_on` (`permission_grant_on`,`permission_item`,`permission_user`),
  KEY `idx_puser` (`permission_user`),
  KEY `idx_pvalue` (`permission_value`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `permissions`
-- 

INSERT INTO `permissions` VALUES (1, 1, 'all', -1, -1);

-- --------------------------------------------------------

-- 
-- Table structure for table `syskeys`
-- 

CREATE TABLE `syskeys` (
  `syskey_id` int(10) unsigned NOT NULL auto_increment,
  `syskey_name` varchar(48) NOT NULL default '',
  `syskey_label` varchar(255) NOT NULL default '',
  `syskey_type` int(1) unsigned NOT NULL default '0',
  `syskey_sep1` char(2) default '\n',
  `syskey_sep2` char(2) NOT NULL default '|',
  PRIMARY KEY  (`syskey_id`),
  UNIQUE KEY `idx_syskey_name` (`syskey_id`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `syskeys`
-- 

INSERT INTO `syskeys` VALUES (1, 'SelectList', 'Enter values for list', 0, '\n', '|');

-- --------------------------------------------------------

-- 
-- Table structure for table `sysvals`
-- 

CREATE TABLE `sysvals` (
  `sysval_id` int(10) unsigned NOT NULL auto_increment,
  `sysval_key_id` int(10) unsigned NOT NULL default '0',
  `sysval_title` varchar(48) NOT NULL default '',
  `sysval_value` text NOT NULL,
  PRIMARY KEY  (`sysval_id`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `sysvals`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `user_log`
-- 

CREATE TABLE `user_log` (
  `user_log_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `object_id` int(10) unsigned NOT NULL default '0',
  `object_class` varchar(25) NOT NULL default '',
  `type` enum('store','delete') NOT NULL default 'store',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`user_log_id`),
  KEY `user_id` (`user_id`),
  KEY `object_id` (`object_id`),
  KEY `object_class` (`object_class`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `user_log`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `user_preferences`
-- 

CREATE TABLE `user_preferences` (
  `pref_user` varchar(12) NOT NULL default '',
  `pref_name` varchar(20) NOT NULL default '',
  `pref_value` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`pref_user`,`pref_name`),
  KEY `pref_user` (`pref_user`,`pref_name`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `user_preferences`
-- 

INSERT INTO `user_preferences` VALUES ('0', 'LOCALE', 'fr');
INSERT INTO `user_preferences` VALUES ('0', 'TABVIEW', '1');
INSERT INTO `user_preferences` VALUES ('0', 'SHDATEFORMAT', '%d/%m/%Y');
INSERT INTO `user_preferences` VALUES ('0', 'TIMEFORMAT', '%I:%M %p');
INSERT INTO `user_preferences` VALUES ('0', 'UISTYLE', 'mediboard');
INSERT INTO `user_preferences` VALUES ('0', 'CURRENCYFORMAT', 'es_FR');

-- --------------------------------------------------------

-- 
-- Table structure for table `groups_mediboard`
-- 

CREATE TABLE `groups_mediboard` (
  `group_id` TINYINT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
  `text` VARCHAR(50) NOT NULL,
  PRIMARY KEY  (`group_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_username` varchar(20) NOT NULL default '',
  `user_password` varchar(32) NOT NULL default '',
  `user_parent` int(11) NOT NULL default '0',
  `user_type` tinyint(3) NOT NULL default '0',
  `user_first_name` varchar(50) default '',
  `user_last_name` varchar(50) default '',
  `user_company` int(11) default '0',
  `user_department` int(11) default '0',
  `user_email` varchar(255) default '',
  `user_phone` varchar(30) default '',
  `user_home_phone` varchar(30) default '',
  `user_mobile` varchar(30) default '',
  `user_address1` varchar(30) default '',
  `user_address2` varchar(30) default '',
  `user_city` varchar(30) default '',
  `user_state` varchar(30) default '',
  `user_zip` varchar(11) default '',
  `user_country` varchar(30) default '',
  `user_icq` varchar(20) default '',
  `user_aol` varchar(20) default '',
  `user_birthday` datetime default NULL,
  `user_pic` text,
  `user_owner` int(11) NOT NULL default '0',
  `user_signature` text,
  PRIMARY KEY  (`user_id`),
  KEY `idx_uid` (`user_username`),
  KEY `idx_pwd` (`user_password`),
  KEY `idx_user_parent` (`user_parent`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `users`
-- 

INSERT INTO `users` VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 0, 1, 'Admin', 'Person', 1, 0, 'contact@mediboard.org', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', NULL, 0, '');

-- 
-- Contenu de la table `users` et `permissions`
-- pour les modèles d'utilisateurs
--

INSERT INTO `users` VALUES ('', '>> Accueil', '5678708573db51f90da2095407abc45e', 0, 2, 'accueil', 'accueil', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, '');
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'mediusers', '-1', '3' FROM users WHERE `user_username` = '>> Accueil';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPccam', '-1', '-1' FROM users WHERE `user_username` = '>> Accueil';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcim10', '-1', '-1' FROM users WHERE `user_username` = '>> Accueil';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPadmissions', '-1', '-1' FROM users WHERE `user_username` = '>> Accueil';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPpatients', '-1', '-1' FROM users WHERE `user_username` = '>> Accueil';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPhospi', '-1', '1' FROM users WHERE `user_username` = '>> Accueil';

INSERT INTO `users` VALUES ('', '>> Anesthesie', '5678708573db51f90da2095407abc45e', 0, 4, 'anesthesie', 'anesthesie', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, '');
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPccam', '-1', '-1' FROM users WHERE `user_username` = '>> Anesthesie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcim10', '-1', '-1' FROM users WHERE `user_username` = '>> Anesthesie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPpatients', '-1', '-1' FROM users WHERE `user_username` = '>> Anesthesie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPhospi', '-1', '1' FROM users WHERE `user_username` = '>> Anesthesie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPbloc', '-1', '1' FROM users WHERE `user_username` = '>> Anesthesie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPplanningOp', '-1', '-1' FROM users WHERE `user_username` = '>> Anesthesie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcabinet', '-1', '-1' FROM users WHERE `user_username` = '>> Anesthesie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcompteRendu', '-1', '-1' FROM users WHERE `user_username` = '>> Anesthesie';

INSERT INTO `users` VALUES ('', '>> Chirurgie', '5678708573db51f90da2095407abc45e', 0, 3, 'chirurgie', 'chirurgie', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, '');
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPccam', '-1', '-1' FROM users WHERE `user_username` = '>> Chirurgie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcim10', '-1', '-1' FROM users WHERE `user_username` = '>> Chirurgie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPpatients', '-1', '-1' FROM users WHERE `user_username` = '>> Chirurgie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPhospi', '-1', '1' FROM users WHERE `user_username` = '>> Chirurgie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPbloc', '-1', '1' FROM users WHERE `user_username` = '>> Chirurgie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPplanningOp', '-1', '-1' FROM users WHERE `user_username` = '>> Chirurgie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcabinet', '-1', '-1' FROM users WHERE `user_username` = '>> Chirurgie';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcompteRendu', '-1', '-1' FROM users WHERE `user_username` = '>> Chirurgie';

INSERT INTO `users` VALUES ('', '>> Direction', '5678708573db51f90da2095407abc45e', 0, 5, 'direction', 'direction', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, '');
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'all', '-1', '-1' FROM users WHERE `user_username` = '>> Direction';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcabinet', '-1', '0' FROM users WHERE `user_username` = '>> Direction';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPgestionCab', '-1', '0' FROM users WHERE `user_username` = '>> Direction';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPinterop', '-1', '0' FROM users WHERE `user_username` = '>> Direction';

INSERT INTO `users` VALUES ('', '>> Facturation', '5678708573db51f90da2095407abc45e', 0, 6, 'facturation', 'facturation', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, '');
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'mediusers', '-1', '3' FROM users WHERE `user_username` = '>> Facturation';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPccam', '-1', '-1' FROM users WHERE `user_username` = '>> Facturation';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcim10', '-1', '-1' FROM users WHERE `user_username` = '>> Facturation';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPadmissions', '-1', '1' FROM users WHERE `user_username` = '>> Facturation';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPpatients', '-1', '-1' FROM users WHERE `user_username` = '>> Facturation';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPhospi', '-1', '1' FROM users WHERE `user_username` = '>> Facturation';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPbloc', '-1', '1' FROM users WHERE `user_username` = '>> Facturation';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPpmsi', '-1', '1' FROM users WHERE `user_username` = '>> Facturation';

INSERT INTO `users` VALUES ('', '>> Hospitalisation', '5678708573db51f90da2095407abc45e', 0, 7, 'Hospitalisation', 'Hospitalisation', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', NULL, 1, '');
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'mediusers', '-1', '3' FROM users WHERE `user_username` = '>> Hospitalisation';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPccam', '-1', '-1' FROM users WHERE `user_username` = '>> Hospitalisation';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcim10', '-1', '-1' FROM users WHERE `user_username` = '>> Hospitalisation';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPadmissions', '-1', '-1' FROM users WHERE `user_username` = '>> Hospitalisation';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPpatients', '-1', '-1' FROM users WHERE `user_username` = '>> Hospitalisation';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPhospi', '-1', '-1' FROM users WHERE `user_username` = '>> Hospitalisation';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPbloc', '-1', '1' FROM users WHERE `user_username` = '>> Hospitalisation';

INSERT INTO `users` VALUES ('', '>> PMSI', '5678708573db51f90da2095407abc45e', 0, 8, 'PMSI', 'PMSI', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, '');
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'mediusers', '-1', '3' FROM users WHERE `user_username` = '>> PMSI';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPccam', '-1', '-1' FROM users WHERE `user_username` = '>> PMSI';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcim10', '-1', '-1' FROM users WHERE `user_username` = '>> PMSI';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPadmissions', '-1', '1' FROM users WHERE `user_username` = '>> PMSI';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPpatients', '-1', '-1' FROM users WHERE `user_username` = '>> PMSI';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPhospi', '-1', '1' FROM users WHERE `user_username` = '>> PMSI';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPbloc', '-1', '1' FROM users WHERE `user_username` = '>> PMSI';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPpmsi', '-1', '-1' FROM users WHERE `user_username` = '>> PMSI';

INSERT INTO `users` VALUES ('', '>> Qualite', '5678708573db51f90da2095407abc45e', 0, 9, 'qualite', 'qualite', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, '');
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'mediusers', '-1', '3' FROM users WHERE `user_username` = '>> Qualite';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPccam', '-1', '-1' FROM users WHERE `user_username` = '>> Qualite';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcim10', '-1', '-1' FROM users WHERE `user_username` = '>> Qualite';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPadmissions', '-1', '1' FROM users WHERE `user_username` = '>> Qualite';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPpatients', '-1', '-1' FROM users WHERE `user_username` = '>> Qualite';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPhospi', '-1', '1' FROM users WHERE `user_username` = '>> Qualite';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPbloc', '-1', '1' FROM users WHERE `user_username` = '>> Qualite';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPstats', '-1', '-1' FROM users WHERE `user_username` = '>> Qualite';

INSERT INTO `users` VALUES ('', '>> Secretariat', '5678708573db51f90da2095407abc45e', 0, 10, 'secretariat', 'secretariat', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, '');
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPccam', '-1', '-1' FROM users WHERE `user_username` = '>> Secretariat';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcim10', '-1', '-1' FROM users WHERE `user_username` = '>> Secretariat';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPpatients', '-1', '-1' FROM users WHERE `user_username` = '>> Secretariat';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPhospi', '-1', '1' FROM users WHERE `user_username` = '>> Secretariat';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPbloc', '-1', '1' FROM users WHERE `user_username` = '>> Secretariat';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPplanningOp', '-1', '-1' FROM users WHERE `user_username` = '>> Secretariat';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcabinet', '-1', '-1' FROM users WHERE `user_username` = '>> Secretariat';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcompteRendu', '-1', '-1' FROM users WHERE `user_username` = '>> Secretariat';

INSERT INTO `users` VALUES ('', '>> SI', '5678708573db51f90da2095407abc45e', 0, 1, 'SI', 'SI', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, '');
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'all', '-1', '-1' FROM users WHERE `user_username` = '>> SI';

INSERT INTO `users` VALUES ('', '>> Survbloc', '5678708573db51f90da2095407abc45e', 0, 12, 'survbloc', 'survbloc', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, '');
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'mediusers', '-1', '3' FROM users WHERE `user_username` = '>> Survbloc';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPccam', '-1', '-1' FROM users WHERE `user_username` = '>> Survbloc';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcim10', '-1', '-1' FROM users WHERE `user_username` = '>> Survbloc';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPadmissions', '-1', '1' FROM users WHERE `user_username` = '>> Survbloc';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPpatients', '-1', '-1' FROM users WHERE `user_username` = '>> Survbloc';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPhospi', '-1', '1' FROM users WHERE `user_username` = '>> Survbloc';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPbloc', '-1', '-1' FROM users WHERE `user_username` = '>> Survbloc';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPsalleOp', '-1', '-1' FROM users WHERE `user_username` = '>> Survbloc';

INSERT INTO `users` VALUES ('', '>> Bloc', '5678708573db51f90da2095407abc45e', 0, 12, 'bloc', 'bloc', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, '');
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'mediusers', '-1', '3' FROM users WHERE `user_username` = '>> Bloc';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPccam', '-1', '-1' FROM users WHERE `user_username` = '>> Bloc';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPcim10', '-1', '-1' FROM users WHERE `user_username` = '>> Bloc';
INSERT INTO `permissions` (`permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) SELECT users.user_id, 'dPsalleOp', '-1', '1' FROM users WHERE `user_username` = '>> Bloc';