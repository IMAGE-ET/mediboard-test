<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/
 
global $AppUI, $can, $g;
$can->needsRead();

$now = mbDate();

$filter = new COperation;
$filter->_date_min     = mbGetValueFromGet("_date_min", $now);
$filter->_date_max     = mbGetValueFromGet("_date_max", $now);
$filter->_prat_id      = mbGetValueFromGetOrSession("_prat_id");
$filter->salle_id      = mbGetValueFromGetOrSession("salle_id");
$filter->_plage        = mbGetValueFromGetOrSession("_plage");
$filter->_intervention = mbGetValueFromGetOrSession("_intervention");
$filter->_specialite   = mbGetValueFromGetOrSession("_specialite");
$filter->_codes_ccam   = mbGetValueFromGetOrSession("_codes_ccam");
$filter->_ccam_libelle = mbGetValueFromGetOrSession("_ccam_libelle");

$filterSejour = new CSejour;
$filterSejour->type = mbGetValueFromGetOrSession("type");

$yesterday  = mbDate("-1 day", $now);

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites(PERM_READ);

// Récupération des salles
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_EDIT);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("chir"         , $AppUI->user_id);
$smarty->assign("filter"       , $filter);
$smarty->assign("filterSejour" , $filterSejour);
$smarty->assign("now"          , $now);
$smarty->assign("yesterday"    , $yesterday);
$smarty->assign("listPrat"     , $listPrat);
$smarty->assign("listSpec"     , $listSpec);
$smarty->assign("listBlocs"    , $listBlocs);

$smarty->display("form_print_planning.tpl");

?>