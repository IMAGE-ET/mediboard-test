<?php

/**
 * dPboard
 *
 * @category Board
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Setup du module Tableau de bord
 */
class CSetupdPboard extends CSetup {

  /**
   * Constructeur
   */
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPboard";
    
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
  }
}
