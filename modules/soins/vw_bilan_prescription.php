<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $m, $AppUI;

$can->needsRead();

$praticien_id = CValue::getOrSession("praticien_id" , $AppUI->user_id);
$signee       = CValue::getOrSession("signee"       , 0);  // par default les non signees
$date_min     = CValue::getOrSession("_date_min"     , mbDateTime("00:00:00"));  // par default, date du jour
$date_max     = CValue::getOrSession("_date_max"     , mbDateTime("23:59:59"));
$type         = CValue::getOrSession("type"         , "sejour");  // sejour - externe - sortie_manquante

// Chargement de la liste des praticiens
$mediuser = new CMediusers();
$praticiens = $mediuser->loadPraticiens();

if(!$praticien_id){
	$user_courant = new CMediusers();
	$user_courant->load($AppUI->user_id);
	if($user_courant->isPraticien()){
		$praticien_id = $user_courant->_id;
	}
}

$sejour = new CSejour();
$sejour->_date_min = $date_min;
$sejour->_date_max = $date_max;

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("praticiens", $praticiens);
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("signee", $signee);
$smarty->assign("sejour", $sejour);
$smarty->assign("type", $type);
$smarty->display('vw_bilan_prescription.tpl');

?>