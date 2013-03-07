<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();
$user = CMediusers::get();

$praticien_id      = CValue::getOrSession("prat_bilan_id"      , $user->_id);
$signee            = CValue::getOrSession("signee"             , 0);         // par default les non signees
$date_min          = CValue::getOrSession("_date_entree_prevue", CMbDT::date());  // par default, date du jour
$date_max          = CValue::getOrSession("_date_sortie_prevue", CMbDT::date());
$type_prescription = CValue::getOrSession("type_prescription"  , "sejour");  // sejour - externe - sortie_manquante

// Chargement de la liste des praticiens
$mediuser = new CMediusers();
$praticiens = $mediuser->loadPraticiens();

if(!$praticien_id && $user->isPraticien()){
  $praticien_id = $user->_id;
}

$sejour = new CSejour();
$sejour->_date_entree_prevue = $date_min;
$sejour->_date_sortie_prevue = $date_max;

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("praticiens", $praticiens);
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("signee", $signee);
$smarty->assign("sejour", $sejour);
$smarty->assign("type_prescription", $type_prescription);
$smarty->display('vw_bilan_prescription.tpl');

?>