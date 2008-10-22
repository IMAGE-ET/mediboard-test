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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("pass", mbGetValueFromGet("pass"));
$smarty->assign("types_antecedents", CAntecedent::$types);
$smarty->assign("types_antecedents_active", $types_antecedents_active);

$smarty->display("configure.tpl");

?>