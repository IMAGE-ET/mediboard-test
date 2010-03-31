<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$date = CValue::getOrSession("date", mbDate());
$next = mbDate("+1 day", $date);

$sejour = new CSejour;
$where = array();
$where["entree_prevue"] = "< '$next'";
$where["sortie_prevue"] = "> '$date'";
$where["group_id"]      = "= '$g'";
$where["annule"]        = "= '0'";
$order = array();
$order[] = "sortie_prevue";
$order[] = "entree_prevue";

$listSejours = $sejour->loadList($where, $order);

foreach ($listSejours as $keySejour => $valueSejour) {
  $sejour =& $listSejours[$keySejour];
  $sejour->loadRefsFwd();
  $sejour->loadNumDossier();
  $sejour->_ref_patient->loadIPP();
  $sejour->loadRefGHM();
  $sejour->countEchangeHprim();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"       , $date       );
$smarty->assign("listSejours", $listSejours);

$smarty->display("vw_list_hospi.tpl");

?>
