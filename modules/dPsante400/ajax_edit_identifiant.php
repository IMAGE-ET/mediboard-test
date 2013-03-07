<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$canSante400 = CModule::getCanDo("dPsante400");

$idSante400 = new CIdSante400;
$idSante400->load(CValue::get("idSante400_id"));
$idSante400->loadRefs();

// Chargement du filtre
$filter = new CIdSante400;
$filter->object_id    = CValue::get("object_id"   );
$filter->object_class = CValue::get("object_class");
$filter->tag          = CValue::get("tag"         );
$filter->id400        = CValue::get("id400");
$filter->nullifyEmptyFields();

$filter->last_update = CValue::first($idSante400->last_update, CMbDT::dateTime());

// Rester sur le même filtre en mode dialogue
$dialog = CValue::get("dialog");
if ($dialog && $idSante400->_id) {
  $filter->object_class = $idSante400->object_class;
  $filter->object_id    = $idSante400->object_id   ;
}

// Récupération de la liste des classes disponibles
if ($filter->object_class && $filter->object_id) {
  $listClasses = array($filter->object_class);
}
else {
  $listClasses = CApp::getInstalledClasses();
}

// Chargement de la cible si oBjet unique
$target = null;
if ($filter->object_id && $filter->object_class) {
  $target = new $filter->object_class;
  $target->load($filter->object_id);
}

if (!$idSante400->_id) {
  $idSante400 = $filter;
}

$smarty = new CSmartyDP;
$smarty->assign("idSante400" , $idSante400);
$smarty->assign("canSante400", $canSante400);
$smarty->assign("filter"     , $filter);
$smarty->assign("target"     , $target);
$smarty->assign("listClasses", $listClasses);
$smarty->display("inc_edit_identifiant.tpl");

?>