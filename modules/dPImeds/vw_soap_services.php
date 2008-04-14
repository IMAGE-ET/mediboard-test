<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Romain Ollivier
*/

global $dPconfig;

$urlImeds = parse_url($dPconfig["dPImeds"]["url"]);

$serviceAdresse = $urlImeds["scheme"]."://".$urlImeds["host"]."/dllimeds/webimeddll.asmx";

$client = new SoapClient($serviceAdresse."?WSDL", array('exceptions' => 0));
$functions = $client->__getFunctions();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("adresse", $serviceAdresse);
$smarty->assign("functions", $functions);

$smarty->display("vw_soap_services.tpl");

?>