<?php /* $Id: httpreq_get_op_time.php 7210 2009-11-03 12:18:57Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 7210 $
* @author Romain Ollivier
*/


$type   = CValue::get("type");
$entree = CValue::get("entree");

$group = CGroups::loadCurrent();
$group->loadConfigValues();

$sejour = new CSejour();
$nb_sejour = 0;
if($type && $entree) {
	$where = array(
	  "type"     => "= '$type'",
	  "annule"   => "= '0'",
	  "group_id" => "= '$group->_id'"
	);
	if($type == "ambu") {
		$where[] = "DATE_FORMAT(entree, '%Y-%m-%d') = '$entree'";
	} elseif($type == "comp") {
    $where[] = "'$entree 23:59:59' BETWEEN entree AND sortie";
	}
  $nb_sejour = $sejour->countList($where);
}

$occupation = 0;
if($type == "ambu" && $group->_configs["max_ambu"]) {
  $occupation = $nb_sejour / $group->_configs["max_ambu"] * 100;
} else if($type == "comp" && $group->_configs["max_comp"]) {
	$occupation = $nb_sejour / $group->_configs["max_comp"] * 100;
}
$pct = min($occupation, 100);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("occupation", $occupation);
$smarty->assign("pct", $pct);

$smarty->display("inc_show_occupation_lits.tpl");

?>