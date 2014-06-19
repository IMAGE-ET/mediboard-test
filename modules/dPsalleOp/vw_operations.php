<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Ne pas supprimer, utilisé pour mettre le praticien en session
$praticien_id    = CValue::getOrSession("praticien_id");
$hide_finished   = CValue::getOrSession("hide_finished", 0);
$salle_id        = CValue::getOrSession("salle");
$operation_id    = CValue::getOrSession("operation_id");
$date            = CValue::getOrSession("date", CMbDT::date());

// Récupération de l'utilisateur courant
$currUser = CMediusers::get();
$currUser->isAnesth();
$currUser->isPraticien();

// Sauvegarde en session du bloc (pour preselectionner dans la salle de reveil)
$salle = new CSalle();
$salle->load($salle_id);
CValue::setSession("bloc_id", $salle->bloc_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("operation_id" , $operation_id);
$smarty->assign("salle"        , $salle_id);
$smarty->assign("currUser"     , $currUser);
$smarty->assign("date"         , $date);
$smarty->assign("hide_finished", $hide_finished);

$smarty->display("vw_operations.tpl");
