<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPplanningOp
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$user = CUser::get();

$operation_id= CValue::get("operation_id");

$operation = new COperation();
$operation->load($operation_id);
$operation->loadRefAffectation();
$operation->loadRefsFwd(1);
$operation->loadRefsConsultAnesth();
$operation->_ref_sejour->loadRefsFwd();
$operation->_ref_sejour->loadRefsConsultAnesth();

// Récupération de l'utilisateur courant
$currUser = CMediusers::get();
$currUser->isAnesth();

// Chargement des anesthésistes
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("currUser"               , $currUser);
$smarty->assign("user_id"                , $user->_id);
$smarty->assign("listAnesths"            , $listAnesths);
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("isImedsInstalled"       , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("operation"              , $operation);
$smarty->assign("anesth_perop"           , new CAnesthPerop());
$smarty->assign("create_dossier_anesth"  , 0);
$smarty->display("edit_visite_anesth.tpl");

?>