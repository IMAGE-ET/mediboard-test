<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 8208 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "acquittementsserveuractivitepmsi");

class CHPrimXMLAcquittementsServeurActes extends CHPrimXMLAcquittementsServeurActivitePmsi {
  function __construct() {
    $this->evenement = "evt_serveuractes";
    $this->acquittement = "acquittementsServeurActes";
     
    parent::__construct("msgAcquittementsServeurActes105");
  }
}

?>