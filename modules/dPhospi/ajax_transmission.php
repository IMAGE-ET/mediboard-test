<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$transmission_id = CValue::get("transmission_id");
$sejour_id       = CValue::get("sejour_id");
$user_id         = CValue::get("user_id");
$object_id       = CValue::get("object_id");
$object_class    = CValue::get("object_class");
$libelle_ATC     = CValue::get("libelle_ATC");
$refreshTrans    = CValue::get("refreshTrans", 0);

$transmission =  new CTransmissionMedicale;
if ($transmission_id) {
  $transmission->load($transmission_id);
}
else {
  $transmission->sejour_id = $sejour_id;
  $transmission->user_id = $user_id;
  if ($object_id && $object_class) {
    $transmission->object_id = $object_id;
    $transmission->object_class = $object_class;
  }
  if ($libelle_ATC) {
    $transmission->libelle_ATC = stripslashes($libelle_ATC);
  }
}

$transmission->loadTargetObject();

$smarty = new CSmartyDP;

$smarty->assign("transmission", $transmission);
$smarty->assign("refreshTrans", $refreshTrans);
$smarty->assign("date", mbDate());
$smarty->assign("hour", mbTransformTime(null, mbTime(), "%H"));

$smarty->display("inc_transmission.tpl");
?>