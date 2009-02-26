<?php 

$domEvenement = new DomDocument();
$domEvenement->load("document.xml");

$xpath = new DOMXPath($domEvenement);
$xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );

$evenementsPatients = $xpath->query("/hprim:evenementsPatients/");


//foreach ($evenementsPatients as $evenement) {
//  $patient = $xpath->query("/hprim:enteteMessage", $evenement); 
//}
/*

$elements = $evenementsPatients->query("hprim:enteteMessage/hprim:emetteur/hprim:agents/hprim:agent[@categorie='".utf8_encode('système')."']/hprim:code");
if (!is_null($elements)) 
  foreach ($elements as $element) {
    $nodes = $element->childNodes;
    foreach ($nodes as $node) 
      $idClient = utf8_decode($node->nodeValue);
  }
echo $idClient;*/
?>