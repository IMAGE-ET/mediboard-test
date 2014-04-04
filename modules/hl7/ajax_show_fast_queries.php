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

$exchange = new CExchangeHL7v2();
$exchange->load($exchange_id);
$exchange->loadRefsInteropActor();

if ($exchange->receiver_id) {
  /** @var CInteropReceiver $actor */
  $actor = $exchange->_ref_receiver;
  $actor->loadConfigValues();
}
else {
  /** @var CInteropSender $actor */
  $actor = $exchange->_ref_sender;
  $actor->getConfigs($exchange);
}

$hl7_message = new CHL7v2Message;
$hl7_message->parse($er7);

/** @var CHL7v2MessageXML $xml */
$xml = $hl7_message->toXML(null ,false);

$PID = $xml->queryNode("PID");
$PV1 = $xml->queryNode("PV1");
$PV2 = $xml->queryNode("PV2");

$IPP = $NDA = null;

$data = array();
$data["personIdentifiers"] = $xml->getPersonIdentifiers("PID.3", $PID, $actor);
$data["admitIdentifiers"]  = $xml->getAdmitIdentifiers($PV1, $actor);

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
    "nom"             => $names["nom"],
    "nom_jeune_fille" => $names["nom_jeune_fille"],
    "prenom"          => $prenom,
    "naissance"       => CMbDT::dateToLocale($xml->queryTextNode("PID.7", $PID)),
    "_IPP"            => CValue::read($data["personIdentifiers"], "PI"),
  ),
  "CSejour" => array(
    "type"          => $xml->queryTextNode("PV1.2", $PV1),
    "entree_prevue" => CMbDT::dateToLocale($xml->queryTextNode("PV2.8/TS.1", $PV2)),
    "entree_reelle" => CMbDT::dateToLocale($xml->queryTextNode("PV1.44/TS.1", $PV1)),
    "sortie_prevue" => CMbDT::dateToLocale($xml->queryTextNode("PV2.9/TS.1", $PV2)),
    "sortie_reelle" => CMbDT::dateToLocale($xml->queryTextNode("PV1.45/TS.1", $PV1)),
    "_NDA"          => CValue::read($data["personIdentifiers"], "AN")
  )
);

function getNames(CHL7v2MessageXML $xml, DOMNode $node, DOMNodeList $PID5, &$names = array()) {
  $fn1 = $xml->queryTextNode("XPN.1/FN.1", $node);
  
  switch ($xml->queryTextNode("XPN.7", $node)) {
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
