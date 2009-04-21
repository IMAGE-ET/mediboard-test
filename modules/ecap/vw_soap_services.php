<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("serviceURL", $serviceURL);
$smarty->assign("functions", $functions);

$smarty->display("vw_soap_services.tpl");

?>