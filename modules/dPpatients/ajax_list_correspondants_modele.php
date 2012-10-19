<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$correspondant_id = CValue::getOrSession("correspondant_id", 0);

$correspondant = new CCorrespondantPatient();

$where = array();
$where["patient_id"] = "IS NULL";

$correspondants = $correspondant->loadList($where);
//$correspondants = $correspondant->loadMatchingList("nom");

$smarty = new CSmartyDP();

$smarty->assign("correspondants"  , $correspondants);
$smarty->assign("correspondant_id", $correspondant_id);

$smarty->display("inc_list_correspondants_modele.tpl");
