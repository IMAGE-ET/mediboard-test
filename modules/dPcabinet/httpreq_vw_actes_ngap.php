<?php 

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 23 $
* @author Alexis Granger
*/

$object_id = mbGetValueFromGetOrSession("object_id");
$object_class = mbGetValueFromGetOrSession("object_class");
// Chargement de la consultation
$object = new $object_class;
$object->load($object_id);
$object->loadRefsActesNGAP();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("acte_ngap", new CActeNGAP);
$smarty->assign("object"  , $object  );

$smarty->display("inc_acte_ngap.tpl");
?>