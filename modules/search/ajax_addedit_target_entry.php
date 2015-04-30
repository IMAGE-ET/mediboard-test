<?php 

/**
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkRead();

$thesaurus_entry_id = CValue::get("thesaurus_entry_id", null);
$target = new CSearchTargetEntry();
$thesaurus_entry = new CSearchThesaurusEntry();
$thesaurus_entry->load($thesaurus_entry_id);
$thesaurus_entry->loadRefsTargets();

foreach ($thesaurus_entry->_atc_targets as $_target) {
  foreach ($_target->_ref_target as $_atc) {
    $object = new CMedicamentClasseATC();
    $_target->_libelle = $object->getLibelle($_target->object_id);
  }
}

$smarty = new CSmartyDP();
$smarty->assign("thesaurus_entry_id", $thesaurus_entry_id);
$smarty->assign("thesaurus_entry", $thesaurus_entry);
$smarty->assign("target", $target);
$smarty->display("vw_addedit_target.tpl");