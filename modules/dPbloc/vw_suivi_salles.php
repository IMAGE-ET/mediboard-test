<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date_suivi = CAppUI::pref("suivisalleAutonome") ? CValue::get("date", mbDate()) : CValue::getOrSession("date", mbDate());
$listBlocs  = CGroups::loadCurrent()->loadBlocs(PERM_READ, null, "nom");
$bloc_id    = CValue::getOrSession("bloc_id", reset($listBlocs)->_id);
if(!key_exists($bloc_id, $listBlocs)) {
  $bloc_id = reset($listBlocs)->_id;
}
$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
$bloc->loadRefs();

// Chargement de la liste des salles de chaque bloc
foreach($listBlocs as $_bloc) {
  $_bloc->loadRefsSalles();
}

// Chargement des Anesthésistes
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);

// Chargement des Chirurgiens
$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_READ);

$salle = new CSalle;
$where = array("bloc_id" => "='$bloc->_id'");
$bloc->_ref_salles = $salle->loadListWithPerms(PERM_READ, $where, "nom");

foreach ($bloc->_ref_salles as &$salle) {
  $salle->loadRefsForDay($date_suivi);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"     , true);
$smarty->assign("listAnesths"    , $listAnesths);
$smarty->assign("listBlocs"      , $listBlocs);
$smarty->assign("bloc"           , $bloc);
$smarty->assign("date_suivi"     , $date_suivi);
$smarty->assign("operation_id"   , 0);

$smarty->display("vw_suivi_salles.tpl");
?>