<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g;
$can->needsRead();

$date_suivi = CValue::getOrSession("date");
$bloc_id	= CValue::getOrSession("vue");

// Chargement des AnesthÈsistes
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);

// Chargement des Chirurgiens
$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_READ);

$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ);

$bloc = new CBlocOperatoire();
if (!$bloc->load($bloc_id)) {
		$bloc = reset($listBlocs);
}
$bloc->loadRefs();

$salle = new CSalle;
$where = array("bloc_id" => "='$bloc->_id'");
$bloc->_ref_salles = $salle->loadListWithPerms(PERM_READ, $where, "nom");

foreach ($bloc->_ref_salles as &$salle) {
		$salle->loadRefsForDay($date_suivi);
}

// CrÈation du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"	 , true);
$smarty->assign("listAnesths"    , $listAnesths);
$smarty->assign("listBlocs"      , $listBlocs);
$smarty->assign("bloc"           , $bloc);
$smarty->assign("date_suivi"     , $date_suivi);
$smarty->assign("operation_id"   , 0);

$smarty->display("print_suivi.tpl");

?>