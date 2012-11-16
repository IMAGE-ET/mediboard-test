<?php


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

    $this->mod_version = "0.1";


    }
}