<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage todo
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 */

class CSetuptasking extends CSetup {
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "tasking";
    $this->makeRevision("all");

    $this->makeRevision("0.01");

    $this->addDependency("monitorServer", "0.50");

    $query = "CREATE TABLE `tasking_contact` (
              `tasking_contact_id` INT(11)      NOT NULL AUTO_INCREMENT,
              `site_id`            INT(11)      NOT NULL,
              `last_name`          VARCHAR(255) NOT NULL,
              `first_name`         VARCHAR(255),
              `email`              VARCHAR(255),
              `mobile_phone`       VARCHAR(255),
              `desk_phone`         VARCHAR(255),
              PRIMARY KEY (`tasking_contact_id`),
              KEY `site_id` (`site_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "INSERT INTO `tasking_contact` (
                `site_id`,
                `last_name`
              )
              SELECT `monitor_server`.`site_id`, `monitor_customer_events`.`customer`
              FROM `monitor_customer_events`, `monitor_server`
              WHERE `monitor_customer_events`.`server_id` = `monitor_server`.`server_id`
              GROUP BY `monitor_customer_events`.`customer`;";
    $this->addQuery($query);

    $this->makeRevision("0.02");
    $query = "CREATE TABLE `tasking_contact_event` (
              `tasking_contact_event_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `site_id`                  INT(11) UNSIGNED NOT NULL,
              `tasking_contact_id`       INT(11) UNSIGNED NOT NULL,
              `interlocutor_user_id`     INT(11) UNSIGNED NOT NULL,
              `subject_id`               INT(11) UNSIGNED,
              `subject_class`            VARCHAR(255),
              `date_start`               DATETIME,
              `date_end`                 DATETIME,
              `event_description`        TEXT,
              `event_comment`            TEXT,
              PRIMARY KEY (`tasking_contact_event_id`),
              KEY `site_id`              (`site_id`),
              KEY `tasking_contact_id`   (`tasking_contact_id`),
              KEY `interlocutor_user_id` (`interlocutor_user_id`),
              KEY `subject`              (`subject_id`, `subject_class`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.03");
    function importInterlocutors() {
      $ds = CSQLDataSource::get("std");
      CApp::setTimeLimit(1800);

      $query = "SELECT DISTINCT `interlocutor`
          FROM `monitor_customer_events`;";

      $users = array();
      $interlocutors = $ds->loadList($query);
      foreach ($interlocutors as $_interlocutor) {
        $_user = explode(' ', $_interlocutor['interlocutor'], 2);

        $query = "SELECT `user_id`
                  FROM  `users`
                  WHERE `user_first_name` = '" . CMbString::upper($_user[0]) . "'
                  AND   `user_last_name`  = '". CMbString::upper($_user[1]) . "'";

        $users[$_interlocutor['interlocutor']] = $ds->loadResult($query);
      }

      foreach ($users as $_interlocutor => $_user_id) {
        if ($_user_id) {
          $query = "INSERT INTO `tasking_contact_event` (
                      `site_id`,
                      `tasking_contact_id`,
                      `interlocutor_user_id`,
                      `date_start`,
                      `date_end`,
                      `event_description`
                    )
                    SELECT `monitor_server`.`site_id`,
                           `tasking_contact`.`tasking_contact_id`,
                           $_user_id,
                           `monitor_customer_events`.`date_start`,
                           `monitor_customer_events`.`date_end`,
                           `monitor_customer_events`.`event_description`
                    FROM `monitor_customer_events`,
                         `monitor_server`,
                         `tasking_contact`
                    WHERE `monitor_customer_events`.`server_id` = `monitor_server`.`server_id`
                      AND `tasking_contact`.`last_name` = `monitor_customer_events`.`customer`
                      AND `monitor_customer_events`.`interlocutor` = ?;";

          $query = $ds->prepare($query, $_interlocutor);
          $ds->exec($query);
        }
      }

      return true;
    }

    $this->addFunction('importInterlocutors');

    $this->makeRevision("0.04");
    $query = "CREATE TABLE `tasking_ticket` (
              `tasking_ticket_id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `supervisor_id`          INT(11) UNSIGNED,
              `assigned_to_id`         INT(11) UNSIGNED,
              `duplicate_of_id`        INT(11) UNSIGNED,
              `ticket_name`            VARCHAR(255)     NOT NULL,
              `creation_date`          DATETIME         NOT NULL,
              `last_modification_date` DATETIME,
              `due_date`               DATETIME,
              `closing_date`           DATETIME,
              `priority`               INT(11) UNSIGNED DEFAULT '0' NOT NULL,
              `estimate`               INT(11) UNSIGNED,
              `type`                   ENUM ('ref','bug','erg','fnc','action') DEFAULT 'ref' NOT NULL,
              `status`                 ENUM ('new','accepted','inprogress','invalid','duplicate','cancelled','closed','refused') NOT NULL,
              `funding`                ENUM ('fund-cus','fund-50','fund-ox'),
              `nb_postponements`       INT(11) UNSIGNED DEFAULT '0',
              PRIMARY KEY (`tasking_ticket_id`),
              KEY `supervisor_id`   (`supervisor_id`),
              KEY `assigned_to_id`  (`assigned_to_id`),
              KEY `duplicate_of_id` (`duplicate_of_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "CREATE TABLE `tasking_ticket_message` (
              `tasking_ticket_message_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `title`                     VARCHAR(255),
              `text`                      TEXT NOT NULL,
              `creation_date`             DATETIME NOT NULL,
              `task_id`                   INT(11) UNSIGNED NOT NULL,
              `user_id`                   INT(11) UNSIGNED,
              PRIMARY KEY   (`tasking_ticket_message_id`),
              KEY `task_id` (`task_id`),
              KEY `user_id` (`user_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "CREATE TABLE `ticket_request` (
              `ticket_request_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `object_id`         INT(11) UNSIGNED NOT NULL,
              `object_class`      VARCHAR(255) NOT NULL,
              `label`             VARCHAR(255) NOT NULL,
              `description`       TEXT,
              `priority`          INT(11) UNSIGNED,
              `due_date`          DATETIME,
              PRIMARY KEY  ( `ticket_request_id`),
              KEY `object` (`object_id`, `object_class`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.05");
    $query = "CREATE TABLE `tasking_bill` (
              `tasking_bill_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `bill_name`       VARCHAR(255) NOT NULL,
              PRIMARY KEY  ( `tasking_bill_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.06");
    $query = "ALTER TABLE `tasking_ticket`
              ADD COLUMN `bill_id` INT(11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `tasking_ticket`
              ADD INDEX `bill_id` (`bill_id`);";
    $this->addQuery($query);
    
    $this->mod_version = "0.07";
  }
}
