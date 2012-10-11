<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 */

class CSetupdicom extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = "dicom";
    $this->makeRevision("all");
    
    $query = "CREATE TABLE `dicom_sender` (
                `dicom_sender_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `nom` VARCHAR (255) NOT NULL,
                `libelle` VARCHAR (255),
                `group_id` INT (11) UNSIGNED NOT NULL,
                `actif` ENUM ('0','1') NOT NULL DEFAULT '0'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `source_dicom` (
                `source_dicom_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `name` VARCHAR (255) NOT NULL,
                `role` ENUM ('prod', 'qualif') NOT NULL DEFAULT 'qualif',
                `host` TEXT NOT NULL,
                `type_echange` VARCHAR (255),
                `active` ENUM ('0','1') NOT NULL DEFAULT '1',
                `loggable` ENUM ('0','1') NOT NULL DEFAULT '1',
                `port` INT (11),
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->mod_version = "0.1";
  }
}
?>