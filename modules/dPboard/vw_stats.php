<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPboard
* @version $Revision: 2260 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

// Chargement de l'utilisateur courant
$userCourant = new CMediusers;
$userCourant->load($AppUI->user_id);

$can->needsRead();

$filterSejour    = new CSejour();
$filterOperation = new COperation();

$filterSejour->_date_min_stat = mbGetValueFromGetOrSession("_date_min_stat", mbDate("-1 YEAR"));
$rectif = mbTransformTime("+0 DAY", $filterSejour->_date_min_stat, "%d") - 1;
$filterSejour->_date_min_stat = mbDate("-$rectif DAYS", $filterSejour->_date_min_stat);

$filterSejour->_date_max_stat =  mbGetValueFromGetOrSession("_date_max_stat",  mbDate());
$rectif = mbTransformTime("+0 DAY", $filterSejour->_date_max_stat, "%d") - 1;
$filterSejour->_date_max_stat = mbDate("-$rectif DAYS", $filterSejour->_date_max_stat);
$filterSejour->_date_max_stat = mbDate("+ 1 MONTH", $filterSejour->_date_max_stat);
$filterSejour->_date_max_stat = mbDate("-1 DAY", $filterSejour->_date_max_stat);

$filterSejour->praticien_id = $userCourant->user_id;
$filterSejour->type = mbGetValueFromGetOrSession("type", 1);
$filterOperation->codes_ccam = strtoupper(mbGetValueFromGetOrSession("codes_ccam", ""));

$sejour = new CSejour;
$listHospis = array();
$listHospis = array_merge($listHospis,$sejour->_enumsTrans["type"]);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user_id"                , $userCourant->user_id);
$smarty->assign("filterSejour"           , $filterSejour);
$smarty->assign("filterOperation"        , $filterOperation);
$smarty->assign("listHospis"             , $listHospis);

$smarty->display("vw_stats.tpl");

?>