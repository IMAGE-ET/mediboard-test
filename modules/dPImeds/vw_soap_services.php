<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Romain Ollivier
*/

global $can;
$can->needsRead();

$soap_url = CImeds::getSoapUrl();
if (!url_exists($soap_url)) {
  CAppUI::stepMessage(UI_MSG_ERROR, "Serveur IMeds inatteignable à l'addresse : $serviceAdresse");
  return;
}

$client = new SoapClient($soap_url."?WSDL", array('exceptions' => 0));
$functions = $client->__getFunctions();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("adresse", $serviceAdresse);
$smarty->assign("functions", $functions);

$smarty->display("vw_soap_services.tpl");

?>