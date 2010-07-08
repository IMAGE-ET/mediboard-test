<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPplanningOp
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;

$operation_id= CValue::get("operation_id");

$operation = new COperation();
$operation->load($operation_id);
$operation->loadRefAffectation();
$operation->loadRefsFwd(1);
$operation->loadRefsConsultAnesth();
$operation->_ref_sejour->loadRefsFwd();
$operation->_ref_sejour->loadRefsConsultAnesth();

// Récupération de l'utilisateur courant
$currUser = new CMediusers();
$currUser->load($AppUI->user_id);
$currUser->isAnesth();

// Chargement des anesthésistes
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("currUser"               , $currUser);
$smarty->assign("user_id"                , $AppUI->user_id);
$smarty->assign("listAnesths"            , $listAnesths);
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("isImedsInstalled"       , CModule::getActive("dPImeds"));
$smarty->assign("operation"              , $operation);
$smarty->assign("anesth_perop"           , new CAnesthPerop());
$smarty->display("edit_visite_anesth.tpl");

?>