<?php
/**
 * Download exchange
 *
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

$echange_soap_id = CValue::get("echange_soap_id");
$echange_soap = new CEchangeSOAP();
$echange_soap->load($echange_soap_id);
$echange_soap->loadRefs();

$input  = print_r(unserialize($echange_soap->input), true);

if ($echange_soap->soapfault == 1) {
  $output = print_r($echange_soap->output, true);
}
else {
  $output = print_r(unserialize($echange_soap->output), true);
}

$function_name = $echange_soap->function_name;
$content = "Date d'echange :
{$echange_soap->date_echange}\n
Temps de reponse :
{$echange_soap->response_time} ms \n
Parametres :\n
$input
Resultat :\n
$output\n";

if (CAppUI::conf("webservices trace")) {
  $content .= "Entete requete :\n
  {$echange_soap->last_request_headers}\n
  Requete :\n
  {$echange_soap->last_request}\n
  Entete reponse :\n
  {$echange_soap->last_response_headers}\n
  Reponse :\n
  {$echange_soap->last_response}\n";
}

$echange = utf8_decode($content);

header("Content-Disposition: attachment; filename={$function_name}-{$echange_soap_id}.txt");
header("Content-Type: text/plain; charset=".CApp::$encoding);
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header("Cache-Control: post-check=0, pre-check=0", false );
header("Content-Length: ".strlen($echange));
echo $echange;