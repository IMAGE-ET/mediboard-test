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

if (!url_exists($serviceURL)) {
  CAppUI::stepMessage(UI_MSG_ERROR, "Serveur wep inatteignable à l'adresse : $serviceURL");
  return;
}

$client = new SoapClient("$serviceURL?WSDL", array('exceptions' => 0));

$functions = $client->__getFunctions();

$requestParams = array (
  "aLoginApplicatif"       => CAppUI::conf("ecap soap user"),
  "aPasswordApplicatif"    => CAppUI::conf("ecap soap pass"),
  "aTypeIdentifiantActeur" => "loginUser",
  "aIdentifiantActeur"     => "pr1",
  "aIdClinique"            => CAppUI::conf("dPsante400 group_id"),
  "aTypeObjet"             => "SJ",
);

mbDump($requestParams);

$results = $client->ListerTypeDocument($requestParams);
mbTrace($results->ListerTypeDocumentResult);

$types = simplexml_load_string($results->ListerTypeDocumentResult->any);

mbTrace($types);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("serviceURL", $serviceURL);
$smarty->assign("functions", $functions);

$smarty->display("vw_soap_services.tpl");

?>