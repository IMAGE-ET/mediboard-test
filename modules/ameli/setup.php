<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage ameli
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 */

/**
 * Ameli Setup class
 */
class CSetupameli extends CSetup {
  /**
   * @see parent::__construct()
   */
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "ameli";
    $this->makeRevision("all");

    $query = "CREATE TABLE `avis_arret_travail` (
                `avis_arret_travail_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `motif_id` VARCHAR(8),
                `libelle_motif` VARCHAR(100) NOT NULL,
                `type` ENUM('initial', 'prolongation') NOT NULL DEFAULT 'initial',
                `accident_tiers` ENUM('0', '1') NOT NULL DEFAULT '0',
                `date_accident` DATE,
                `debut` DATE NOT NULL,
                `fin` DATE NOT NULL,
                `consult_id` INT (11) UNSIGNED NOT NULL,
                `patient_id` INT (11) UNSIGNED NOT NULL
    )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->mod_version = "0.01";

    $query = "SHOW TABLES LIKE 'motif_aati';";
    $this->addDatasource("ameli", $query);
  }
}
