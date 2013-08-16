<?php

/**
 * Acquittements pour le serveur d'actes
 *
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20171 $
 */

/**
 * Class CHPrimXMLAcquittementsServeurActes
 */
class CHPrimXMLAcquittementsServeurActes extends CHPrimXMLAcquittementsServeurActivitePmsi {
  /**
   * @see parent::__construct
   */
  function __construct() {
    $this->evenement    = "evt_serveuractes";
    $this->acquittement = "acquittementsServeurActes";
    
    $version = str_replace(".", "", CAppUI::conf("hprimxml $this->evenement version"));
     
    parent::__construct("serveurActivitePmsi_v$version", "msgAcquittementsServeurActes$version");
  }
}

