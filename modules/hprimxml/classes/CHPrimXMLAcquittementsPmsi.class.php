<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 8208 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLAcquittementsPmsi extends CHPrimXMLAcquittementsServeurActivitePmsi {
  function __construct() {
    $this->evenement = "evt_pmsi";
    $this->acquittement = "acquittementsPmsi";
    
    parent::__construct("msgAcquittementsPmsi105");
  }

}

