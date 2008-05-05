<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m, $utypes;
$can->needsEdit();

// Gestion des bouton radio des dates
$now       = mbDate();
$week_deb  = mbDate("last sunday", $now);
$week_fin  = mbDate("next sunday", $week_deb);
$week_deb  = mbDate("+1 day"     , $week_deb);
$rectif     = mbTranformTime("+0 DAY", $now, "%d")-1;
$month_deb  = mbDate("-$rectif DAYS", $now);
$month_fin  = mbDate("+1 month", $month_deb);
$month_fin  = mbDate("-1 day", $month_fin);

$filter = new CConsultation;

$filter->_date_min = mbDate();
$filter->_date_max = mbDate("+ 0 day");

$filter->_etat_paiement = mbGetValueFromGetOrSession("_etat_paiement", 0);
$filter->patient_mode_reglement = mbGetValueFromGetOrSession("patient_mode_reglement", 0);
$filter->_type_affichage = mbGetValueFromGetOrSession("_type_affichage", 0);

// L'utilisateur est-il praticien ?
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$mediuser->loadRefFunction();

$is_praticien           = $mediuser->isPraticien();
$is_admin_or_secretaire = in_array($utypes[$mediuser->_user_type], array("Administrator", "Secr�taire"));

// Liste des praticiens du cabinet -> on ne doit pas voir les autres...
$listPrat = $is_admin_or_secretaire || $mediuser->_ref_function->compta_partagee ? 
               $mediuser->loadPraticiens(PERM_READ, $mediuser->function_id) : 
               array($mediuser->_id => $mediuser);
  
// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("filter"                , $filter);
$smarty->assign("mediuser"              , $mediuser);
$smarty->assign("is_praticien"          , $is_praticien);
$smarty->assign("is_admin_or_secretaire", $is_admin_or_secretaire);
$smarty->assign("listPrat"              , $listPrat);
$smarty->assign("now"                   , $now);
$smarty->assign("week_deb"              , $week_deb);
$smarty->assign("week_fin"              , $week_fin);
$smarty->assign("month_deb"             , $month_deb);
$smarty->assign("month_fin"             , $month_fin);

$smarty->display("vw_compta.tpl");

?>