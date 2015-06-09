<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 */
CCanDo::checkRead();

$code_ccam     = CValue::get("code_ccam");
$date_version  = CValue::get("date_version");
$date_demandee = CValue::get("date_demandee");

$date_version_to = null;
if ($date_demandee) {
  $date_version_to = CDatedCodeCCAM::mapDateToDash($date_demandee);
}
if ($date_version) {
  $date_version_to = CDatedCodeCCAM::mapDateToSlash($date_version);
}
$date_demandee = CDatedCodeCCAM::mapDateFrom($date_version_to);

$date_versions = array();
$code_complet = CDatedCodeCCAM::get($code_ccam, $date_version_to);
foreach ($code_complet->_ref_code_ccam->_ref_infotarif as $_infotarif) {
  $date_versions[] = $code_complet->mapDateFrom($_infotarif->date_effet);
}
foreach ($code_complet->activites as $_activite) {
  $code_complet->_count_activite += count($_activite->assos);
}
$acte_voisins = $code_complet->loadActesVoisins();

$smarty = new CSmartyDP();
if (!in_array($date_demandee, $date_versions) && $date_demandee) {
  $smarty->assign("no_date_found", "CDatedCodeCCAM-msg-No date found for date searched");

}
$smarty->assign("code_complet"      , $code_complet);
$smarty->assign("numberAssociations", $code_complet->_count_activite);
$smarty->assign("date_versions"     , $date_versions);
$smarty->assign("date_version"      , $date_version);
$smarty->assign("date_demandee"     , $date_demandee);
$smarty->assign("code_ccam"         , $code_ccam);
$smarty->assign("acte_voisins"      , $acte_voisins);
$smarty->display("inc_show_code.tpl");