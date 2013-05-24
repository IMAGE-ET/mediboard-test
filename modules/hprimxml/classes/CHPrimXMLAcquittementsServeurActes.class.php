<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 8208 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLAcquittementsServeurActes extends CHPrimXMLAcquittementsServeurActivitePmsi {
  function __construct() {
    $this->evenement    = "evt_serveuractes";
    $this->acquittement = "acquittementsServeurActes";
    
    $version = str_replace(".", "", CAppUI::conf("hprimxml $this->evenement version"));
     
    parent::__construct("serveurActivitePmsi_v$version", "msgAcquittementsServeurActes$version");
  }
}

