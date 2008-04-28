<?php /* */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:  $
* @author Alexis Granger
* @author Fabien Menager
**/

global $AppUI;

$object_id    = mbGetValueFromGet("object_id");
$object_class = mbGetValueFromGet("object_class");
$praticien_id = mbGetValueFromGetOrSession("praticien_id");

$user_id      = $AppUI->user_id;

// Chargement de l'objet
$object = new $object_class;
$object->load($object_id);

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($praticien_id);

// Chargement des objets relatifs a l'objet chargé
$templateClasses = $object->getTemplateClasses();

// Chargement des modeles de consultations du praticien
$order = "nom";

$wherePrat = array();
$whereFunc = array();

// Chargement des modeles pour chaque classe, pour les praticiens et leur fonction
foreach($templateClasses as $class => $id) {
  if ($id) {
    // Pour le praticien
    $modelesCompat[$class]['prat'] = CCompteRendu::loadModelesForPrat($class, $praticien->_id);
  
    // Pour la fonction du praticien
    $modelesCompat[$class]['func'] = CCompteRendu::loadModelesForFunc($class, $praticien->function_id);
  } else {
    // Pour le praticien
    $modelesNonCompat[$class]['prat'] = CCompteRendu::loadModelesForPrat($class, $praticien->_id);
  
    // Pour la fonction du praticien
    $modelesNonCompat[$class]['func'] = CCompteRendu::loadModelesForFunc($class, $praticien->function_id);
  }
}


  // Création du template
$smarty = new CSmartyDP();

$smarty->assign("target_id", $object->_id);
$smarty->assign("target_class", $object->_class_name);

$smarty->assign("modelesCompat", $modelesCompat);
$smarty->assign("modelesNonCompat", $modelesNonCompat);
$smarty->assign("modelesId", $templateClasses);

$smarty->display("modele_selector.tpl");

?>