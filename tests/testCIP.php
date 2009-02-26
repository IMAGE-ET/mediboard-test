<?php

// première étape : désactiver le cache lors de la phase de test
ini_set("soap.wsdl_cache_enabled", "0");

// lier le client au fichier WSDL
$clientSOAP = new SoapClient("http://192.168.0.101/~yohann/mediboard_yohann/tests/medinetWebServices.xml?WSDL");



/*
// executer la methode getHello
echo "Methode getHello() <br />";
echo $clientSOAP->getHello('Yohann','Poiron');
echo "<br /><br />";

$chaineXML = '<patient><nom>Dubois</nom><prenom>Robert</prenom></patient>';

$dom = new DomDocument();
//$dom->load('fichier.xml');
$dom->loadXML($chaineXML);
$dataXML = $dom->saveXML();

// executer la methode enregistrementPatient
echo "Methode enregistrementPatient() <br />";
echo $clientSOAP->enregistrementPatient('openxtrem','openxtrem', $dom);*/

?>