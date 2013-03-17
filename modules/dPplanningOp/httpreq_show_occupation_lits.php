<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: 6171 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$type   = CValue::get("type");
$entree = CValue::get("entree");

$group = CGroups::loadCurrent();
$group->loadConfigValues();

$sejour = new CSejour();
$nb_sejour = 0;
if ($type && $entree) {
	$where = array(
	  "type"     => "= '$type'",
	  "annule"   => "= '0'",
	  "group_id" => "= '$group->_id'"
	);
	$min = $entree;
  $max = CMbDT::date("+1 DAY", $min);
	if ($type == "ambu") {
	  $where[] = "entree BETWEEN '$min' AND '$max'";
	} elseif($type == "comp") {
    $where[] = "'$max' BETWEEN entree AND sortie";
	}
  $nb_sejour = $sejour->countList($where);
}

$occupation = 0;
if ($type == "ambu" && $group->_configs["max_ambu"]) {
  $occupation = $nb_sejour / $group->_configs["max_ambu"] * 100;
} else if ($type == "comp" && $group->_configs["max_comp"]) {
	$occupation = $nb_sejour / $group->_configs["max_comp"] * 100;
}
$pct = min($occupation, 100);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("occupation", $occupation);
$smarty->assign("pct", $pct);

$smarty->display("inc_show_occupation_lits.tpl");

?>