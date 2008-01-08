<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;
$ds = CSQLDataSource::get("std");

// Récupération du sejour_id
$sejour_id = mbGetValueFromGetOrSession("sejour_id");

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);

// Chargement du praticien du séjour
$sejour->loadRefPraticien();

$whereCommon = array();
$whereCommon["object_id"] = "IS NULL";
$whereCommon["object_class"] = " = 'CSejour'";
$order = "nom";

// Chargement des modeeles de l'utilisateur
$listModelePrat = array();
$where = $whereCommon;
$where["chir_id"] = $ds->prepare("= %", $sejour->praticien_id);
$listModelePrat = new CCompteRendu;
$listModelePrat = $listModelePrat->loadlist($where, $order);

// Chargement des modèles de la fonction
$listModeleFunc = array();
$where = $whereCommon;
$where["function_id"] = $ds->prepare("= %", $sejour->_ref_praticien->function_id);
$listModeleFunc = new CCompteRendu;
$listModeleFunc = $listModeleFunc->loadlist($where, $order);

// Chargement des packs disponibles
$packList         = array();
$where            = array();
$where["chir_id"] = "= '$sejour->praticien_id'";
$where["object_class"] = " = 'CSejour'";
$pack             = new CPack;
$packList         = $pack->loadlist($where, $order);

// Chargement des documents du sejour
$sejour->loadRefsDocs();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour"         , $sejour         );
$smarty->assign("listModelePrat" , $listModelePrat );
$smarty->assign("listModeleFunc" , $listModeleFunc );
$smarty->assign("packList"       , $packList       );

$smarty->display("inc_vw_documents.tpl");

?>