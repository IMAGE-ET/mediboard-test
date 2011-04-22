<?php

$facture_id = CValue::getOrSession("facture_id");


$order = "date DESC";

$facture = new CFacture();
$list_facture = $facture->loadList(null,$order);
foreach($list_facture as &$curr_facture) {
  $curr_facture->loadRefs();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list_facture", $list_facture);

$smarty->display("inc_list_facture.tpl");


?>