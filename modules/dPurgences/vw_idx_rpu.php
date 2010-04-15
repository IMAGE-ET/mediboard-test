<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

if ($sip_barcode = CValue::get("sip_barcode")) {
	$values = array();
  if (preg_match("/SID([\d]+)/i", $sip_barcode, $values)) {
  	$sejour = new CSejour;
		$sejour->load($values[1]);
    if ($sejour->_id) {
			$sejour->loadRefPatient();
			if ($sejour->type == "urg") {
	      CAppUI::redirect("m=dPurgences&tab=vw_aed_rpu&sejour_id=$sejour->_id");
			}
			else {
        CAppUI::stepAjax("Le séjour trouvé '%s' n'est pas un séjour d'urgences", UI_MSG_WARNING, $sejour->_view);
			}
    }
		else {
      CAppUI::redirect("Le séjour dont l'idenfitiant est '%s' n'existe pas", UI_MSG_WARNING, $sejour->_id);
		}
  }
	else {
		CAppUI::stepAjax("Le numéro saisi '%s' ne correspond pas à un idenfitiant de séjour", UI_MSG_WARNING, $sip_barcode);
	}
}

// Type d'affichage
$selAffichage = CValue::postOrSession("selAffichage", CAppUI::conf("dPurgences default_view"));

// Parametre de tri
$order_way = CValue::getOrSession("order_way", "DESC");
$order_col = CValue::getOrSession("order_col", "ccmu");

// Selection de la date
$date = CValue::getOrSession("date", mbDate());
$today = mbDate();


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("group"       , CGroups::loadCurrent());
$smarty->assign("selAffichage", $selAffichage);
$smarty->assign("date"        , $date);
$smarty->assign("isImedsInstalled"  , CModule::getActive("dPImeds"));

$smarty->display("vw_idx_rpu.tpl");
?>