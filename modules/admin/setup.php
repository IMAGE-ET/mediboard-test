<?php

/**
 * $Id$
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

class CSetupadmin extends CSetup {
  function __construct() {
    parent::__construct();

    $this->mod_type = "core";
    $this->mod_name = "admin";

    $this->makeRevision("all");

    $this->makeRevision("1.0.14");
    $query = "ALTER TABLE `user_preferences`
      DROP PRIMARY KEY;";
    $this->addQuery($query);
    $query = "ALTER TABLE `user_preferences`
      ADD `pref_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      CHANGE `pref_user` `user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
      CHANGE `pref_name` `key` VARCHAR (255) NOT NULL,
      CHANGE `pref_value` `value` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision("1.0.15");
    $query = "ALTER TABLE `users`
      ADD INDEX (`user_birthday`),
      ADD INDEX (`user_last_login`),
      ADD INDEX (`profile_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.0.16");
    $query = "ALTER TABLE `users`
      CHANGE `user_address1` `user_address1` VARCHAR( 255 );";
    $this->addQuery($query);

    $this->makeRevision("1.0.17");
    $query = "ALTER TABLE `users`
      ADD `dont_log_connection` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.0.18");
    $query = "CREATE TABLE `source_ldap` (
      `source_ldap_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `name` VARCHAR (255) NOT NULL,
      `host` TEXT NOT NULL,
      `port` INT (11) DEFAULT '389',
      `rootdn` VARCHAR (255) NOT NULL,
      `ldap_opt_protocol_version` INT (11) DEFAULT '3',
      `ldap_opt_referrals` ENUM ('0','1') DEFAULT '0'
     ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("1.0.19");
    $query = "ALTER TABLE `source_ldap`
                ADD `bind_rdn_suffix` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision("1.0.20");
    $query = "ALTER TABLE `source_ldap`
              ADD `priority` INT (11);";
    $this->addQuery($query);

    $this->makeRevision("1.0.21");
    $query = "ALTER TABLE `source_ldap`
              ADD `secured` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.0.22");
    $query = "ALTER TABLE `users`
      CHANGE `user_phone`  `user_phone`  VARCHAR (20),
      CHANGE `user_mobile` `user_mobile` VARCHAR (20)";
    $this->addQuery($query);

    $this->makeRevision("1.0.23");
    $query = "ALTER TABLE `users`
      DROP `user_pic`,
      DROP `user_signature`,
      CHANGE `user_password`     `user_password`     VARCHAR(255),
      CHANGE `user_login_errors` `user_login_errors` TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT '0',
      CHANGE `user_type`         `user_type`         TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.0.24");
    $query = "ALTER TABLE `users`
      ADD `user_salt` CHAR(64) AFTER `user_password`,
      MODIFY `user_password` CHAR(64);";
    $this->addQuery($query);

    $this->makeRevision("1.0.25");
    $this->addDependency("system", "1.1.12");

    $query = "CREATE TABLE `view_access_token` (
      `view_access_token_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `user_id` INT (11) UNSIGNED NOT NULL,
      `datetime_start` DATETIME NOT NULL,
      `ttl_hours` INT (11) UNSIGNED NOT NULL,
      `first_use` DATETIME,
      `params` VARCHAR (255) NOT NULL,
      `hash` CHAR (40) NOT NULL
     ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `view_access_token`
      ADD INDEX (`user_id`),
      ADD INDEX (`datetime_start`),
      ADD INDEX (`first_use`),
      ADD INDEX (`hash`);";
    $this->addQuery($query);

    $this->makeRevision("1.0.26");
    $query = "ALTER TABLE
      `user_preferences` CHANGE `user_id` `user_id` INT( 11 ) UNSIGNED NULL";
    $this->addQuery($query);
    $query = "UPDATE `user_preferences`
      SET `user_id` = NULL
      WHERE `user_id` = '0'";
    $this->addQuery($query);

    $this->makeRevision("1.0.27");

    $query = "ALTER TABLE `users` ADD `user_astreinte` VARCHAR (20)";
    $this->addQuery($query);

    $this->makeRevision("1.0.28");
    $query = "ALTER TABLE `user_preferences`
      CHANGE `value` `value` TEXT;";
    $this->addQuery($query);

    $this->makeRevision("1.0.29");
    $date = CMbDT::dateTime();
    $query = "ALTER TABLE `users`
      ADD `user_password_last_change` DATETIME NOT NULL DEFAULT '$date' AFTER `user_password`;";
    $this->addQuery($query);

    $this->makeRevision("1.0.30");
    $query = "ALTER TABLE `users`
                CHANGE `user_birthday` `user_birthday` CHAR (10)";
    $this->addQuery($query);

    $this->makeRevision("1.0.31");
    $query = "UPDATE `users`
                SET `user_birthday` = NULL
                WHERE `user_birthday` = '0000-00-00'";
    $this->addQuery($query);

    $this->makeRevision("1.0.32");
    $this->addPrefQuery("notes_anonymous", "0");

    $this->makeRevision("1.0.33");
    $query = "ALTER TABLE
                `view_access_token` ADD `restricted` ENUM('0', '1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.0.34");
    $query = "ALTER TABLE `user_preferences`
              ADD `restricted` ENUM('0', '1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.0.35");
    $query = "CREATE TABLE `log_access_medical_object` (
                `access_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `datetime` DATETIME NOT NULL,
                `object_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `object_class` VARCHAR (80) NOT NULL,
                `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0'
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("1.0.36");
    $query = "ALTER TABLE `log_access_medical_object`
                ADD INDEX (`user_id`),
                ADD INDEX (`datetime`),
                ADD INDEX (`object_id`),
                ADD INDEX (`object_class`),
                ADD INDEX (`group_id`),
                ADD UNIQUE unique_line (`user_id`, `datetime`, `object_id`, `object_class`);";
    $this->addQuery($query);

    $this->makeRevision("1.0.37");
    $query = "CREATE TABLE `bris_de_glace` (
                `bris_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `date` DATETIME NOT NULL,
                `user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `object_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `object_class` VARCHAR (80) NOT NULL
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("1.0.38");
    $query ="ALTER TABLE `bris_de_glace`
                ADD INDEX (`date`),
                ADD INDEX (`user_id`),
                ADD INDEX (`object_id`),
                ADD INDEX (`object_class`);";
    $this->addQuery($query);

    $this->makeRevision("1.0.39");
    $query = "ALTER TABLE `bris_de_glace`
                ADD `comment` TEXT NOT NULL,
                ADD `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $query = "ALTER TABLE `bris_de_glace`
                ADD INDEX (`group_id`)";
    $this->addQuery($query);

    $this->makeRevision("1.0.40");
    $query = "ALTER TABLE `users`
      ADD `force_change_password` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.0.41");
    $query = "ALTER TABLE  `users` ADD INDEX (`user_type`)";
    $this->addQuery($query);

    $this->mod_version = "1.0.42";
  }
}
