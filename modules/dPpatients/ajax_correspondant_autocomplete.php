<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();
$patient_id = CValue::get("patient_id");
$type       = CValue::get("type");
$nom        = @$_POST["$type"];

$corresp = new CCorrespondantPatient();
$where = array();
$where["nom"] = "LIKE '%$nom%'";
$where["patient_id"] = " = '$patient_id'";
$correspondants = $corresp->loadList($where, "nom");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("nom"            , $nom);
$smarty->assign("correspondants" , $correspondants);

$smarty->display("ajax_correspondant_autocomplete.tpl");