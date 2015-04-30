<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkRead();
$thesaurus_entry_id = CValue::get("thesaurus_entry", null);
$search_agregation  = CValue::get("search_agregation", null);
$search_body        = CValue::get("search_body", null);
$search_user_id     = CValue::get("search_user_id", null);
$search_types       = CValue::get("search_types", null);
$search_contexte    = CValue::get("search_contexte", null);

$thesaurus_entry = new CSearchThesaurusEntry();
if ($thesaurus_entry_id) {
  $thesaurus_entry->load($thesaurus_entry_id);
  $search_types = explode("|", $thesaurus_entry->types);
  $thesaurus_entry->loadRefsTargets();
  foreach ($thesaurus_entry->_atc_targets as $_target) {
    foreach ($_target->_ref_target as $_atc) {
      $object = new CMedicamentClasseATC();
      $_target->_libelle = $object->getLibelle($_target->object_id);
    }
  }
}
else {
  $thesaurus_entry->agregation = $search_agregation;
  $thesaurus_entry->entry      = $search_body;
  $thesaurus_entry->user_id    = $search_user_id;
  $thesaurus_entry->types      = is_array($search_types) ? implode(" ", $search_types) : explode(" ", $thesaurus_entry->types);
  $thesaurus_entry->contextes  = $search_contexte;
}

$types = array();
$group = CGroups::loadCurrent();
if (CAppUI::conf("search active_handler active_handler_search_types", $group)) {
  $types = explode("|", CAppUI::conf("search active_handler active_handler_search_types", $group));
}

$user = new CMediusers();
$user->load($thesaurus_entry->user_id);
$user->loadRefFunction()->loadRefGroup();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("thesaurus_entry", $thesaurus_entry);
$smarty->assign("search_types", $search_types);
$smarty->assign("types", $types);
$smarty->assign("user_thesaurus", $user);
$smarty->display("vw_addedit_entry.tpl");