<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPsante400
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = new CModule;
$module->mod_name = "dPadmissions";
$module->loadMatchingObject();
$admin_admission = $module->canAdmin();

$module = new CModule;
$module->mod_name = "sip";
$module->loadMatchingObject();
$sip_active = $module->mod_active;

$id400 = CValue::get("id400");
$object_id = CValue::get("object_id");
$listIdSante400 = array();
$idSante400_id  = null;
$order = "last_update DESC";

$idSante400 = new CIdSante400;
$idSante400->id400 = $id400;
$idSante400->object_id = $object_id;
$idSante400->loadMatchingObject();

$group_id = CGroups::loadCurrent()->_id;

if ($idSante400->_id) {
  $filter = new CIdSante400;
  $filter->object_class = $idSante400->object_class;
  $filter->object_id    = $idSante400->object_id;
  
  $filter->tag = CSejour::getTagNumDossier($group_id);
  $listIdSante400  = $filter->loadMatchingList($order);
  
  $filter->tag = CSejour::getTagNumDossier($group_id, "tag_dossier_trash");
  $listIdSante400 += $filter->loadMatchingList($order);
  
  $filter->tag = CSejour::getTagNumDossier($group_id, "tag_dossier_cancel");
  $listIdSante400 += $filter->loadMatchingList($order);
  
  $filter->tag = CSejour::getTagNumDossier($group_id, "tag_dossier_pa");
  $listIdSante400 += $filter->loadMatchingList($order);
  
  // Chargement de l'objet afin de récupérer l'id400 associé (le latest)
  $object = new $filter->object_class;
  $object->load($filter->object_id);
  $object->loadNumDossier($group_id);
  
  foreach ($listIdSante400 as $key=>$_idSante400) {
    $_idSante400->loadRefs();
    if (!$idSante400_id && $_idSante400->id400 == $object->_num_dossier) {
      $idSante400_id = $_idSante400->_id;
    }
  }
  
  ksort($listIdSante400);
}

$smarty = new CSmartyDP;
$smarty->assign("admin_admission", $admin_admission);
$smarty->assign("listIdSante400" , $listIdSante400);
$smarty->assign("idSante400_id"  , $idSante400_id);
$smarty->assign("object_id"      , $object_id);
$smarty->assign("sip_active"     , $sip_active);
if ($idSante400->_id) {
  $smarty->assign("patient_id"    , $object->patient_id);
}

$smarty->display("inc_list_show_id400.tpl");
?>