<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

$codeCCAM = strtoupper(mbGetValueFromGetOrSession("codeCCAM", ""));
$prat_id  = mbGetValueFromGetOrSession("prat_id", 0);
$type     = mbGetValueFromGetOrSession("type", "ambu");

$total["nbSejours"] = 0;
$total["duree_moy"] = 0;
$total["duree_somme"] = 0;


$listTemps = new CTempsHospi;

$where = array();
$ds = CSQLDataSource::get("std");
if($type) {
  $where["type"] = $ds->prepare("= %", $type);
}
$where["praticien_id"] = $ds->prepareIn(array_keys($listPrats), $prat_id);

if($codeCCAM) {
  $codeCCAM     = trim($codeCCAM);
  $listCodeCCAM = explode(" ",$codeCCAM);
  $listCodeCCAM = array_filter($listCodeCCAM);
  foreach($listCodeCCAM as $keyccam => $code){
    $where[] = "ccam LIKE '%$code%'";
  }
}

$ljoin = array();
$ljoin["users"] = "users.user_id = temps_hospi.praticien_id";

$order = "users.user_last_name ASC, users.user_first_name ASC, ccam";

$listTemps = $listTemps->loadList($where, $order, null, null, $ljoin);


if($codeCCAM) {
  // Groupement des données par chirurgien
  $old_praticien = 0;
  $TempsHospitalisation = array();
  
  foreach($listTemps as $keyTemps => $temps) {    
    if($old_praticien != $temps->praticien_id) {
      // Si on change de chirurgien, alors on initialise la variable
      $TempsHospitalisation[$temps->praticien_id] = new CTempsHospi();
      $TempsHospitalisation[$temps->praticien_id]->praticien_id = $temps->praticien_id;
      $TempsHospitalisation[$temps->praticien_id]->nb_sejour = 0;
      $TempsHospitalisation[$temps->praticien_id]->duree_moy = 0;
      $TempsHospitalisation[$temps->praticien_id]->duree_ecart = 0;
      $TempsHospitalisation[$temps->praticien_id]->ccam= $codeCCAM;
    }
    
    $TempsHospitalisation[$temps->praticien_id]->nb_sejour += $temps->nb_sejour;
    $TempsHospitalisation[$temps->praticien_id]->duree_moy += $temps->nb_sejour * $temps->duree_moy;

    $old_praticien = $temps->praticien_id;    
  }
  $listTemps = $TempsHospitalisation;
}


foreach($listTemps as $keyTemps => $temps) {
  if($codeCCAM) {
    $temps->duree_moy = $temps->duree_moy / $temps->nb_sejour; 
  }
  $listTemps[$keyTemps]->loadRefsFwd();
  $total["nbSejours"]   += $temps->nb_sejour;
  $total["duree_somme"] += $temps->nb_sejour * $temps->duree_moy;
}
if($total["nbSejours"] != 0) {
  $total["duree_moy"] = $total["duree_somme"] / $total["nbSejours"];
}

?>
