<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Romain Ollivier
*/

$urlImeds = parse_url(CAppUI::conf("dPImeds url"));
$urlImeds['path'] = "/dllimeds/webimeddll.asmx";
$serviceAdresse = make_url($urlImeds);

if (!url_exists($serviceAdresse)) {
  CAppUI::stepMessage(UI_MSG_ERROR, "Serveur IMeds inatteignable à l'addresse : $serviceAdresse");
  return;
}

$client = new SoapClient($serviceAdresse."?WSDL", array('exceptions' => 0));
$functions = $client->__getFunctions();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("adresse", $serviceAdresse);
$smarty->assign("functions", $functions);

$smarty->display("vw_soap_services.tpl");

?>