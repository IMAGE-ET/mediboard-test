<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();
$group = CGroups::loadCurrent();

$date = CValue::getOrSession("date", mbDate());
$next = mbDate("+1 day", $date);

$sejour = new CSejour;
$where = array();
$where["entree"] = "< '$next'";
$where["sortie"] = "> '$date'";
$where["group_id"]      = "= '$group->_id'";
$where["annule"]        = "= '0'";
$order = array();
$order[] = "sortie";
$order[] = "entree";

$listSejours = $sejour->loadList($where, $order);

foreach ($listSejours as $keySejour => $valueSejour) {
  $sejour =& $listSejours[$keySejour];
  $sejour->loadRefsFwd();
  $sejour->loadNDA();
  $sejour->_ref_patient->loadIPP();
  $sejour->loadRefGHM();
  $sejour->countExchanges();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"       , $date       );
$smarty->assign("listSejours", $listSejours);

$smarty->display("vw_list_hospi.tpl");

?>
