<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $can;
$can->needsAdmin();

$marked = CValue::get("marked");

$type = CValue::get("type");
$types = $type == "all" ? CMouvFactory::getTypes() : array($type);

$marked = CValue::get("marked");
$marked = $marked == "all" ? array("0", "1") : array($marked);

foreach ($types as $_type) {
  foreach ($marked as $_marked) {
	  $mouv = CMouvFactory::create($_type);
	   
		switch (CValue::get("action")) {
			case "count":
			$count = $mouv->count($_marked);
			CAppUI::stepAjax("%s - %s : %s disponibles ", UI_MSG_OK, 
			  CAppUI::tr("CMouvement400-type-$_type"),
			  CAppUI::tr("CMouvement400-marked-$_marked"), 
			  $count);
			break;
			
			default:
		  CAppUI::stepAjax("Action '$action' non prise en charge", UI_MSG_ERROR);
			break;
		}
  }
}

?>