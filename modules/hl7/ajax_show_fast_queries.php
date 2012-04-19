<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 15199 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$er7         = CValue::post("er7");
$exchange_id = CValue::post("exchange_id");

$hl7_message = new CHL7v2Message;
$hl7_message->parse($er7);

$xml = $hl7_message->toXML();

$PID = $xml->queryNode("PID");
$IPP = $NDA = null;
foreach ($xml->query("PID.3", $PID) as $_node) {
  // PI - Patient internal identifier
  if ($xml->queryTextNode("CX.5", $_node) == "PI") {
    $IPP = $xml->queryTextNode("CX.1", $_node);
  }   
}

// AN - Patient Account Number (NDA)
$PID_18 = $xml->queryNode("PID.18", $PID);
if ($xml->queryTextNode("CX.5", $PID_18) == "AN") {
  $NDA = $xml->queryTextNode("CX.1", $PID_18);
} 

$PV1 = $xml->queryNode("PV1");
$PV2 = $xml->queryNode("PV2");

$queries = array(
  "CPatient" => array(
    "nom"       => $xml->queryTextNode("PID.5/XPN.1/FN.1", $PID),
    "prenom"    => $xml->queryTextNode("PID.5/XPN.2", $PID),
    "naissance" => mbDate($xml->queryTextNode("PID.7", $PID)),
    "_IPP"      => $IPP,
  ),
  "CSejour" => array(
    "type"          => $xml->queryTextNode("PV1.2", $PV1),
    "entree_prevue" => mbDateTime($xml->queryTextNode("PV2.8/TS.1", $PV2)),
    "entree_reelle" => mbDateTime($xml->queryTextNode("PV1.44/TS.1", $PV1)),
    "sortie_prevue" => mbDateTime($xml->queryTextNode("PV2.9/TS.1", $PV2)),
    "sortie_reelle" => mbDateTime($xml->queryTextNode("PV1.45/TS.1", $PV1)),
    "_NDA"          => $NDA,
  )
);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("queries", $queries);
$smarty->display("inc_show_fast_queries.tpl");
