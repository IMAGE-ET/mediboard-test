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

$idex_value = CValue::get("id400");
$object_id = CValue::get("object_id");
$order = "last_update DESC";

$idex = new CIdSante400;
$idex->id400     = $idex_value;
$idex->object_id = $object_id;
$idex->loadMatchingObject();

$group_id = CGroups::loadCurrent()->_id;

$idexs   = array();
$idex_id = null;
if ($idex->_id) {
  $filter = new CIdSante400;
  $filter->object_class = $idex->object_class;
  $filter->object_id    = $idex->object_id;
  
  $filter->tag = CSejour::getTagNDA($group_id);
  $idexs = $filter->loadMatchingList($order);
  
  $filter->tag = CSejour::getTagNDA($group_id, "tag_dossier_trash");
  $idexs += $filter->loadMatchingList($order);
  
  $filter->tag = CSejour::getTagNDA($group_id, "tag_dossier_cancel");
  $idexs += $filter->loadMatchingList($order);
  
  $filter->tag = CSejour::getTagNDA($group_id, "tag_dossier_pa");
  $idexs += $filter->loadMatchingList($order);
  
  // Chargement de l'objet afin de récupérer l'id400 associé (le latest)
  $object = new $filter->object_class;
  $object->load($filter->object_id);
  $object->loadNDA($group_id);
  
  foreach ($idexs as $key => $_idex) {
    $_idex->loadRefs();
    $_idex->getSpecialType();
    if (!$idex_id && $_idex->id400 == $object->_NDA) {
      $idex_id = $_idex->_id;
    }
  }
  
  ksort($idexs);
}

$smarty = new CSmartyDP;
$smarty->assign("admin_admission", $admin_admission);
$smarty->assign("idexs"          , $idexs);
$smarty->assign("idex_id"        , $idex_id);
$smarty->assign("object_id"      , $object_id);
$smarty->assign("sip_active"     , $sip_active);
if ($idex->_id) {
  $smarty->assign("patient_id"   , $object->patient_id);
}

$smarty->display("inc_list_show_id400.tpl");