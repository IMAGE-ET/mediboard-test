<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

// !! Attention, régression importante si ajout de type de paiement

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPcabinet', 'plageconsult') );
require_once( $AppUI->getModuleClass('dPcabinet', 'consultation') );

// Récupération des paramètres
$deb = mbGetValueFromGetOrSession("deb", mbDate());
$fin = mbGetValueFromGetOrSession("fin", mbDate());
$chir = mbGetValueFromGetOrSession("chir");
$chirSel = new CMediusers;
$chirSel->load($chir);
//$etat = mbGetValueFromGetOrSession("etat", 0);
$etat = 1;
$type = mbGetValueFromGetOrSession("type", 0);
$aff = mbGetValueFromGetOrSession("aff", 1);

// Récupération des plages de dates de paiement
$sql = "SELECT consultation.date_paiement AS date," .
		"\n plageconsult.chir_id AS chir_id" .
		"\n FROM consultation" .
		"\n LEFT JOIN plageconsult" .
		"\n ON consultation.plageconsult_id = plageconsult.plageconsult_id";
if ($chir)
  $sql .= "\n WHERE chir_id = '$chir'";
else {
  $listPrat = new CMediusers();
  $listPrat = $listPrat->loadPraticiens(PERM_READ);
  $in = array();
  foreach($listPrat as $key => $value) {
    $in[] = "'$key'";
  }
  $in = implode(", ", $in);
  $sql .= "\n WHERE chir_id IN ($in)";
}
$sql .= "\n AND date_paiement >= '$deb'";
$sql .= "\n AND date_paiement <= '$fin'";
$sql .= "\n GROUP BY date_paiement";
$sql .= "\n ORDER BY date_paiement";

$listPlage = db_loadlist($sql);

// On charge les références des consultations qui nous interessent
$total["cheque"]["valeur"] = 0;
$total["CB"]["valeur"] = 0;
$total["especes"]["valeur"] = 0;
$total["tiers"]["valeur"] = 0;
$total["autre"]["valeur"] = 0;
$total["cheque"]["nombre"] = 0;
$total["CB"]["nombre"] = 0;
$total["especes"]["nombre"] = 0;
$total["tiers"]["nombre"] = 0;
$total["autre"]["nombre"] = 0;
$total["secteur1"] = 0;
$total["secteur2"] = 0;
$total["tarif"] = 0;
$total["nombre"] = 0;
foreach($listPlage as $key => $value) {
  $curr_chir = new CMediusers;
  $curr_chir->load($listPlage[$key]["chir_id"]);
  $listPlage[$key]["_ref_chir"] = $curr_chir;
  $where = array();
  $where["chir_id"] = "= '$curr_chir->user_id'";
  $where["date_paiement"] = "= '".$value["date"]."'";
  $where["chrono"] = ">= '".CC_TERMINE."'";
  $where["annule"] = "= 0";
  if($etat != -1)
    $where["paye"] = "= '$etat'";
  if($etat == 0)
    $where[] = "(secteur1 + secteur2) != 0";
  $where["secteur1"] = "IS NOT NULL";
  if($type)
    $where["type_tarif"] = "= '$type'";
  $ljoin = array();
  $ljoin["plageconsult"] = "plageconsult.plageconsult_id = consultation.plageconsult_id";
  $listConsult = new CConsultation;
  $listConsult = $listConsult->loadList($where, "heure", null, null, $ljoin);
  $listPlage[$key]["_ref_consultations"] = $listConsult;
  $listPlage[$key]["total1"] = 0;
  $listPlage[$key]["total2"] = 0;
  foreach($listPlage[$key]["_ref_consultations"] as $key2 => $value2) {
    $listPlage[$key]["_ref_consultations"][$key2]->loadRefs();
    if($etat == -1 && $listPlage[$key]["_ref_consultations"][$key2]->paye){
      $listPlage[$key]["total1"] += $value2->secteur1;
      $listPlage[$key]["total2"] += $value2->secteur2;
      $total[$value2->type_tarif]["valeur"] += $value2->secteur1 + $value2->secteur2;
      $total[$value2->type_tarif]["nombre"]++;
    }
    elseif($etat != -1){
      $listPlage[$key]["total1"] += $value2->secteur1;
      $listPlage[$key]["total2"] += $value2->secteur2;
      if($value2->type_tarif) {
        $total[$value2->type_tarif]["valeur"] += $value2->secteur1 + $value2->secteur2;
        $total[$value2->type_tarif]["nombre"]++;
      }
    }
  }
  $total["secteur1"] += $listPlage[$key]["total1"];
  $total["secteur2"] += $listPlage[$key]["total2"];
  $total["tarif"] += $listPlage[$key]["total1"] + $listPlage[$key]["total2"];
  $total["nombre"] += count($listPlage[$key]["_ref_consultations"]);
  if(!count($listPlage[$key]["_ref_consultations"]))
    unset($listPlage[$key]);
}

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP(1);

$smarty->debugging = false;
$smarty->assign('deb', $deb);
$smarty->assign('fin', $fin);
$smarty->assign('aff', $aff);
$smarty->assign('etat', $etat);
$smarty->assign('type', $type);
$smarty->assign('chirSel', $chirSel);
$smarty->assign('listPlage', $listPlage);
$smarty->assign('total', $total);

$smarty->display('print_compta.tpl');

?>