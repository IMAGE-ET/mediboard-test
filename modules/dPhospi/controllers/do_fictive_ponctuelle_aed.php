<?php /* $Id: do_affect_ponctuelle_aed.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id      = CValue::post("sejour_id");
$prestation_id  = CValue::post("prestation_id");

// Création d'une liaison pour la première affectation du séjour
$sejour = new CSejour;
$sejour->load($sejour_id);

$prestation = new CPrestationPonctuelle;
$prestation->load($prestation_id);

$first_item = reset($prestation->loadBackRefs("items"));

if (!$first_item) {
  CAppUI::setMsg("Aucun niveau dans la prestation", UI_MSG_WARNING);
  echo CAppUI::getMsg();
  CApp::rip();
}

$sejour->loadRefsAffectations();

$first_affectation = $sejour->_ref_first_affectation;

$item_liaison = new CItemLiaison;
$item_liaison->item_prestation_id = $first_item->_id;
$item_liaison->affectation_id = $first_affectation->_id;
$item_liaison->date = mbDate($first_affectation->entree);

if ($msg = $item_liaison->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
else {
  CAppUI::setMsg(CAppUI::tr("CPrestationPonctuelle-msg-create"), UI_MSG_OK);
}

echo CAppUI::getMsg();

CApp::rip();

?>