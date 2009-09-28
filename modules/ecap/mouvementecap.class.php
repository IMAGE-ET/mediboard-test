<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass("dPsante400", "mouvement400");

class CMouvementEcap extends CMouvement400 {

  function __construct() {
    $this->base = "ECAPFILE";
    $this->markField = "ETAT";
    $this->idField = "INDEX";
    $this->typeField = "TRACTION";
  }
  
  function getFilterClause() {
    if (null == $group_ids = CAppUI::conf("dPsante400 group_id")) {
    	return "";
    }

		$group_ids = explode("|", $group_ids);
		$ors = array();
		foreach($group_ids as $group_id) {
			$ors[] = "CIDC = '$group_id'";
		}
		$ors = implode(" OR ", $ors);
		
    return "\n AND ($ors)";
  }
}
?>
