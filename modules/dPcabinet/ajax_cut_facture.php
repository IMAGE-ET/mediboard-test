<?php /* $Id ajax_cut_facture.php $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$factureconsult_id  = CValue::getOrSession("factureconsult_id");
$caisse_id             = CValue::get("caisse", 0);
$refresh             = CValue::get("refresh");

$facture = new CFactureConsult();
$facture->load($factureconsult_id);
$facture->loadRefs();

$nb_factures = array();
$proposition_tarifs = array();

foreach($facture->_montant_factures_caisse as $caisse => $montant){
	$nb_factures[$caisse]= CValue::get("nb_factures_$caisse", 1);
  for($i=1; $i<=$nb_factures[$caisse]; $i++){
    $proposition_tarifs[$caisse][] = $montant/$nb_factures[$caisse];
  }
}

$caisses = array();
$caisse_maladie = new CCaisseMaladie();
$caisses_maladie = $caisse_maladie->loadList(null, "caisse_maladie_id");

foreach($caisses_maladie as $ca){
	$caisses[$ca->_id] = $ca->nom;
}
$caisses[0] = "Codes Tarmed";

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("facture"             , $facture);
$smarty->assign("proposition_tarifs"  , $proposition_tarifs);
$smarty->assign("nb_factures"         , $nb_factures);
$smarty->assign("caisses_maladie"     , $caisses);

if(!$refresh){
	$smarty->display("inc_cut_facture.tpl");
}
else{
	$smarty->assign("caisse"  , $caisse_id);
	$smarty->assign("tarifs"  , $proposition_tarifs[$caisse_id]);
  $smarty->display("inc_vw_cut_facture.tpl");
}
?>