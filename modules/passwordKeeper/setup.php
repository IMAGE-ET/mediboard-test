<?php
/**
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

class CSetuppasswordKeeper extends CSetup {
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "passwordKeeper";
    $this->makeRevision("all");

    $query = "CREATE TABLE `password_keeper` (
      `password_keeper_id` INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
      `keeper_name`        VARCHAR(50)  NOT NULL,
      `is_public`          BOOLEAN      DEFAULT 0,
      `iv`                 VARCHAR(255) NOT NULL,
      `sample`             VARCHAR(255) NOT NULL,
      `user_id`            INT(11)      UNSIGNED NOT NULL,
      PRIMARY KEY (`password_keeper_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "CREATE TABLE `password_category` (
      `category_id`        INT(11)     UNSIGNED NOT NULL AUTO_INCREMENT,
      `category_name`      VARCHAR(50) NOT NULL,
      `password_keeper_id` INT(11)     UNSIGNED NOT NULL,
      PRIMARY KEY (`category_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "CREATE TABLE `password_entry` (
      `password_id`          INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
      `password_description` VARCHAR(50)  NOT NULL,
      `password`             VARCHAR(255) NOT NULL,
      `password_last_change` DATETIME     NOT NULL,
      `iv`                   VARCHAR(255) NOT NULL,
      `password_comments`    TEXT,
      `category_id`          INT(11)      UNSIGNED NOT NULL,
      PRIMARY KEY (`password_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->mod_version = "0.01";
  }
}
