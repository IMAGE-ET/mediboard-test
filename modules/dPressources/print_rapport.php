<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPressource
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$prat_id = mbGetValueFromGet("prat_id", 0);
if(!$prat_id) {
  echo "Vous devez choisir un praticien valide";
  exit(0);
}
$deb  = mbGetValueFromGet("deb", mbDate());
$fin  = mbGetValueFromGet("fin", mbDate());
if($fin > mbDate())
  $fin = mbDate();
$type = mbGetValueFromGet("type", 0);
$total = 0;

// Chargement du praticien
$prat = new CMediusers;
$prat->load($prat_id);

// Chargement des plages de ressource
$plages = new CPlageressource;

$where["date"] = "BETWEEN '$deb' AND '$fin'";
$where["prat_id"] = "= $prat->user_id";
$where["paye"] = "= '$type'";
$order = "date, debut";

$plages = $plages->loadList($where, $order);

foreach($plages as $key => $value) {
  $total += $value->tarif;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->debugging = false;

$smarty->assign("deb"   , $deb   );
$smarty->assign("fin"   , $fin   );
$smarty->assign("type"  , $type  );
$smarty->assign("prat"  , $prat  );
$smarty->assign("plages", $plages);
$smarty->assign("total" , $total );

$smarty->display("print_rapport.tpl");