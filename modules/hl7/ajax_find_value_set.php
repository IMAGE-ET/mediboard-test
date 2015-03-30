<?php

/**
 * Find value set
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$value_set_type = CValue::get("value_set_type", "RetrieveValueSet");

$OID      = CValue::get("OID");
$version  = CValue::get("version");
$language = CValue::get("language");

if (!$OID) {
  return;
}

$value_set = null;
$error     = null;
try {
  $value_set = CSVS::sendRetrieveValueSet($OID, $version, $language);
}
catch (SoapFault $s) {
  $error = $s->getMessage();
}

$smarty = new CSmartyDP();
$smarty->assign("error"    , $error);
$smarty->assign("value_set", $value_set);
$smarty->display("inc_result_find_value_set.tpl");