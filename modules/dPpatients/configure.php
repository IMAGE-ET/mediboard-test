<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $can;
$can->needsAdmin();

$types_antecedents_active = explode('|', CAppUI::conf("dPpatients CAntecedent types"));
$appareils_antecedents_active = explode('|', CAppUI::conf("dPpatients CAntecedent appareils"));

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("pass", CValue::get("pass"));
$smarty->assign("types_antecedents", CAntecedent::$types);
$smarty->assign("types_antecedents_active", $types_antecedents_active);
$smarty->assign("appareils_antecedents", CAntecedent::$appareils);
$smarty->assign("appareils_antecedents_active", $appareils_antecedents_active);
$smarty->display("configure.tpl");

?>