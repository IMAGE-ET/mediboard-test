<?php

/**
 * Acquittements pour le serveur d'intervention
 *
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20171 $
 */

/**
 * Class CHPrimXMLAcquittementsServeurIntervention
 */
class CHPrimXMLAcquittementsServeurIntervention extends CHPrimXMLAcquittementsServeurActivitePmsi {
  /**
   * @see parent::__construct
   */
  function __construct() {
    $this->evenement    = "evt_serveurintervention";
    $this->acquittement = "acquittementsServeurActes";

    $version = str_replace(".", "", CAppUI::conf("hprimxml $this->evenement version"));

    parent::__construct("serveurActivitePmsi_v$version", "msgAcquittementsServeurActes$version");
  }
}

