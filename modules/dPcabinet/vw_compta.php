<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m;
$can->needsEdit();

// Gestion des bouton radio des dates
$now             = mbDate();
$yesterday       = mbDate("-1 DAY"     , $now);
$week_deb        = mbDate("last sunday", $now);
$week_fin        = mbDate("next sunday", $week_deb);
$week_deb        = mbDate("+1 day"     , $week_deb);
$rectif          = mbTransformTime("+0 DAY", $now, "%d")-1;
$month_deb       = mbDate("-$rectif DAYS", $now);
$month_fin       = mbDate("+1 month"     , $month_deb);
$three_month_deb = mbDate("-3 month"     , $month_fin);
$month_fin       = mbDate("-1 day"       , $month_fin);

$filter = new CConsultation;

$filter->_date_min = mbDate();
$filter->_date_max = mbDate("+ 0 day");

$filter->_etat_paiement = mbGetValueFromGetOrSession("_etat_paiement", 0);
$filter->_type_affichage = mbGetValueFromGetOrSession("_type_affichage", 0);

$filter_reglement = new CReglement();
$filter_reglement->mode = mbGetValueFromGetOrSession("mode", 0);

// L'utilisateur est-il praticien ?
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$mediuser->loadRefFunction();

$is_praticien           = $mediuser->isPraticien();
$is_admin = in_array(CUser::$types[$mediuser->_user_type], array("Administrator"));
$is_admin_or_secretaire = in_array(CUser::$types[$mediuser->_user_type], array("Administrator", "Secrétaire"));

// Liste des praticiens du cabinet -> on ne doit pas voir les autres...
if($is_admin_or_secretaire || $mediuser->_ref_function->compta_partagee) {
  if($is_admin) {
    $listPrat = $mediuser->loadPraticiens(PERM_EDIT);
  } else {
    $listPrat = $mediuser->loadPraticiens(PERM_EDIT, $mediuser->function_id);
  }
} else {
  $listPrat = array($mediuser->_id => $mediuser);
}
  
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"                , $filter);
$smarty->assign("filter_reglement"      , $filter_reglement);
$smarty->assign("mediuser"              , $mediuser);
$smarty->assign("is_praticien"          , $is_praticien);
$smarty->assign("is_admin_or_secretaire", $is_admin_or_secretaire);
$smarty->assign("listPrat"              , $listPrat);
$smarty->assign("now"                   , $now);
$smarty->assign("yesterday"                   , $yesterday);
$smarty->assign("week_deb"              , $week_deb);
$smarty->assign("week_fin"              , $week_fin);
$smarty->assign("month_deb"             , $month_deb);
$smarty->assign("three_month_deb"       , $three_month_deb);
$smarty->assign("month_fin"             , $month_fin);

$smarty->display("vw_compta.tpl");

?>