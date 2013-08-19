<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$affectation_id = CValue::get("affectation_id");
$lit_id         = CValue::get("lit_id");
$sejour_id      = CValue::get("sejour_id");

$affectation = new CAffectation;
$affectation->load($affectation_id);

$smarty = new CSmartyDP;

$smarty->assign("affectation"   , $affectation);
$smarty->assign("affectation_id", $affectation_id);
$smarty->assign("lit_id"        , $lit_id);
$smarty->assign("sejour_id"     , $sejour_id);
$smarty->assign("affectations"  , array());
$smarty->display("inc_select_action_affectation.tpl");

