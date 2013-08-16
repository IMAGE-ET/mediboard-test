<?php

/**
 * Acquittements pour les frais divers
 *
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20171 $
 */

/**
 * Class CHPrimXMLAcquittementsFraisDivers
 */
class CHPrimXMLAcquittementsFraisDivers extends CHPrimXMLAcquittementsServeurActivitePmsi {
  /**
   * @see parent::__construct
   */
  function __construct() {
    $this->evenement = "evt_frais_divers";
    $this->acquittement = "acquittementsFraisDivers";
    
    parent::__construct("msgAcquittementsPmsi105");
  }
}

