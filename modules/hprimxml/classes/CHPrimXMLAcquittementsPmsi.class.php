<?php

/**
 * Acquittements pour le PMSI
 *
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20171 $
 */

/**
 * Class CHPrimXMLAcquittementsPmsi
 */
class CHPrimXMLAcquittementsPmsi extends CHPrimXMLAcquittementsServeurActivitePmsi {
  /**
   * @see parent::__construct
   */
  function __construct() {
    $this->evenement = "evt_pmsi";
    $this->acquittement = "acquittementsPmsi";
    
    parent::__construct("msgAcquittementsPmsi105");
  }
}