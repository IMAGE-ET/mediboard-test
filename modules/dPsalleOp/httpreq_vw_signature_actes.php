<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsaleOp
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $a, $can, $m, $g;

$object_id = mbGetValueFromGetOrSession("object_id");
$object_class = mbGetValueFromGetOrSession("object_class");

$actes_ccam = array();

// Creation de la liste des praticiens
$praticiens = array();

// Tableau qui stocke le nombre d'acte non signé par praticien
$nonSigne = array();

// Chargement de l'objet
$object = new $object_class;
$object->load($object_id);

// Chargement des actes CCAM de l'objet
$object->loadRefsActesCCAM();

// Classement des actes par executant
foreach($object->_ref_actes_ccam as $key => $acte_ccam){
  // Mise a jour de la liste des praticiens
  if(!array_key_exists($acte_ccam->executant_id, $praticiens)){
    $praticien = new CMediusers();
    $praticien->load($acte_ccam->executant_id);
    $praticien->loadRefFunction();
    $praticiens[$acte_ccam->executant_id] = $praticien;
    // initialisation du tableau d'actes non signés
    $nonSigne[$acte_ccam->executant_id] = 0;
  }  
  $acte_ccam->loadRefExecutant();
  $acte_ccam->getTarif();
  $actes_ccam[$acte_ccam->executant_id][] = $acte_ccam;
  if(!$acte_ccam->signe){
    $nonSigne[$acte_ccam->executant_id]++;
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("nonSigne", $nonSigne);
$smarty->assign("redirectUrl", $a);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("object", $object);
$smarty->assign("actes_ccam", $actes_ccam);

$smarty->display("inc_vw_signature_actes.tpl");
?>