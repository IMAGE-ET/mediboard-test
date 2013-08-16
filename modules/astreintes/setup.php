<?php

/**
 * Class CSetupastreintes
 */
class CSetupastreintes extends CSetup {
  function __construct() {
    parent::__construct();

    $this->mod_name = "astreintes";
    $this->makeRevision("all");

    $query = "CREATE TABLE `plage_astreinte` (
      `plage_id` INT (11) UNSIGNED NOT NULL auto_increment,
      `date_debut` DATE NOT NULL,
      `date_fin` DATE NOT NULL,
      `libelle` VARCHAR (255) NOT NULL,
      `user_id` INT (11) UNSIGNED,
      PRIMARY KEY (`plage_id`)
      ) /*! ENGINE=MyISAM */ COMMENT='table des astreintes'";
    $this->addQuery($query);


    $this->makeRevision("0.1");
    $query = "RENAME TABLE `plage_astreinte` TO `astreinte_plage`";
    $this->addQuery($query);

    $query = "ALTER TABLE `astreinte_plage`
      ADD `type` VARCHAR (255) NOT NULL,
      CHANGE `date_debut` `start` DATETIME NOT NULL,
      CHANGE `date_fin` `end` DATETIME NOT NULL,
      ADD `phone_astreinte` INT (11) UNSIGNED";
    $this->addQuery($query);


    $this->makeRevision("0.2");
    $query = "ALTER TABLE `astreinte_plage`
      CHANGE `libelle` `libelle` VARCHAR (255),
      CHANGE `user_id` `user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
      CHANGE `type` `type` ENUM ('medical','admin','personnelsoignant') NOT NULL,
      CHANGE `phone_astreinte` `phone_astreinte` VARCHAR (20) NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.3");
    $query = "ALTER TABLE `astreinte_plage`
                ADD INDEX (`user_id`),
                ADD INDEX (`start`),
                ADD INDEX (`end`);";
    $this->addQuery($query);

    $this->mod_version = "0.4";
  }
}