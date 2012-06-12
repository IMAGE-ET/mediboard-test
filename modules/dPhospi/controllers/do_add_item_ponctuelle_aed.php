<?php /* $Id: do_affect_ponctuelle_aed.php $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id          = CValue::post("sejour_id");
$item_prestation_id = CValue::post("item_prestation_id");
$date               = CValue::post("date");

$item_liaison = new CItemLiaison;
$item_liaison->item_souhait_id = $item_prestation_id;
$item_liaison->sejour_id = $sejour_id;
$item_liaison->date = $date;
$item_liaison->quantite = 1;

if ($msg = $item_liaison->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
else {
  CAppUI::setMsg(CAppUI::tr("CPrestationPonctuelle-msg-create"), UI_MSG_OK);
}

echo CAppUI::getMsg();

CApp::rip();

?>