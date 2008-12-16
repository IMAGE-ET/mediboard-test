<?php /* $Id: $*/

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: $
 * @author Thomas Despoix
 */

global $can;
$can->needsEdit();

CMedicap::makeURLs();
$serviceURL = CMedicap::$urls["soap"]["documents"];

$requestParams = array (
  "aLoginApplicatif"       => CAppUI::conf("ecap soap user"),
  "aPasswordApplicatif"    => CAppUI::conf("ecap soap pass"),
  "aTypeIdentifiantActeur" => 1,
  "aIdentifiantActeur"     => "pr1",
  "aIdClinique"            => CAppUI::conf("dPsante400 group_id"),
  "aTypeObjet"             => "SJ",
);

mbExport($requestParams, "Paramètres de la requête, 'ListerTypeDocument'");

if (!url_exists($serviceURL)) {
  CAppUI::stepMessage(UI_MSG_ERROR, "Serveur wep inatteignable à l'adresse : $serviceURL");
  return;
}

$client = new SoapClient("$serviceURL?WSDL", array('exceptions' => 0));
$results = $client->ListerTypeDocument($requestParams);
$typesEcap = simplexml_load_string($results->ListerTypeDocumentResult->any);
mbExport($typesEcap, "Retour requête");

$typesEcap = array(
  "CPatient" => array (
    "PA" => array(
      "1.1.0" => "Libéllé long pour 1.1.0",
      "1.1.1" => "Libéllé long pour 1.1.1",
		),
   ),
  "CSejour" => array(
    "SJ" => array(
      "1.2.0" => "Libéllé long pour 1.2.0",
      "1.2.1" => "Libéllé long pour 1.2.1",
		),
  ),
  "COperation" => array(
    "AT" => array(
      "2.1.0" => "Libéllé long pour 2.1.0",
      "2.1.1" => "Libéllé long pour 2.1.1",
      "2.1.2" => "Libéllé long pour 2.1.2",
    ),
    "IN" => array(
      "2.2.0" => "Libéllé long pour 2.2.0",
    ),
  ),
);

$categories = CFilesCategory::loadListByClass();
foreach ($categories as &$_catsByClass) {
  foreach ($_catsByClass as &$_category) {
    $idEcap = new CIdSante400();

    $idEcap->loadLatestFor($_category, "ecap type");
    $idsEcap[$_category->_id] = $idEcap;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("typesEcap", $typesEcap);
$smarty->assign("idsEcap"  , $idsEcap);
$smarty->assign("categories", $categories);

$smarty->display("manage_categories.tpl");

?>