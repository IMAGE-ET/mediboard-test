<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$unit_id = CValue::getOrSession("value_unit_id");
$type_id = CValue::getOrSession("value_type_id");

$unit = new CObservationValueUnit;
$unit->load($unit_id);
$unit->loadRefsNotes();
$units = $unit->loadList(null, "coding_system, code");

$type = new CObservationValueType;
$type->load($type_id);
$type->loadRefsNotes();
$types = $type->loadList(null, "coding_system, code");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("unit", $unit);
$smarty->assign("units", $units);
$smarty->assign("type", $type);
$smarty->assign("types", $types);
$smarty->display("vw_config_param_surveillance.tpl");
