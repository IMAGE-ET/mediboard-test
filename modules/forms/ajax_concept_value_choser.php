<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage forms
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$concept_id = CValue::get("concept_id");

$concept = new CExConcept;
$concept->load($concept_id);
$concept->loadView();

$list_owner = $concept->getRealListOwner();
$list_owner->loadView();
$list_owner->loadRefItems();

$spec = CExConcept::getConceptSpec($concept->prop);
if ($spec instanceof CEnumSpec) {
  $list_owner->updateEnumSpec($spec);
}

$smarty = new CSmartyDP();
$smarty->assign("concept", $concept);
$smarty->assign("spec", $spec);
$smarty->display("inc_concept_value_choser.tpl");

