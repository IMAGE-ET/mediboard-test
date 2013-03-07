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

$xml = $hl7_message->toXML(null ,false);

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

$names = array(
  "nom"             => "",
  "nom_jeune_fille" => ""
);
  
$PID5 = $xml->query("PID.5", $PID);
foreach ($PID5 as $_PID5) {
  // Nom(s)
  getNames($xml, $_PID5, $PID5, $names);
  
  // Prenom(s)
  $prenom = getFirstNames($xml, $_PID5);
}      

$queries = array(
  "CPatient" => array(
    "nom"       => $names["nom"] . " (".$names["nom_jeune_fille"].")",
    "prenom"    => $prenom,
    "naissance" => CMbDT::dateToLocale($xml->queryTextNode("PID.7", $PID)),
    "_IPP"      => $IPP,
  ),
  "CSejour" => array(
    "type"          => $xml->queryTextNode("PV1.2", $PV1),
    "entree_prevue" => CMbDT::dateToLocale($xml->queryTextNode("PV2.8/TS.1", $PV2)),
    "entree_reelle" => CMbDT::dateToLocale($xml->queryTextNode("PV1.44/TS.1", $PV1)),
    "sortie_prevue" => CMbDT::dateToLocale($xml->queryTextNode("PV2.9/TS.1", $PV2)),
    "sortie_reelle" => CMbDT::dateToLocale($xml->queryTextNode("PV1.45/TS.1", $PV1)),
    "_NDA"          => $NDA,
  )
);

function getNames(CHL7v2MessageXML $xml, DOMNode $node, DOMNodeList $PID5, &$names = array()) {
  $fn1 = $xml->queryTextNode("XPN.1/FN.1", $node);
  
  switch($xml->queryTextNode("XPN.7", $node)) {
    case "D" :
      $names["nom"] = $fn1;
      break;
    case "L" :
      // Dans le cas où l'on a pas de nom de nom de naissance le legal name
      // est le nom du patient
      if ($PID5->length > 1) {
        $names["nom_jeune_fille"] = $fn1;
      }
      else {
        $names["nom"] = $fn1;
      }
      break;
  }  
}

function getFirstNames(CHL7v2MessageXML $xml, DOMNode $node) {
  return $xml->queryTextNode("XPN.2", $node);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("queries", $queries);
$smarty->display("inc_show_fast_queries.tpl");
