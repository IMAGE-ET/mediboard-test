<?php

// Creation de la connexion au serveur SOAP
$soapAddress = "https://127.0.0.1/medilab/medilab.asmx?WSDL";
$client = new SoapClient($soapAddress, array('exceptions' => 0)); 

// Liste des fonctions disponibles
var_dump($client->__getFunctions());

$NumMedi = "12345";
$Pwd     = "67890";

$result = $client->NDOSLAB(array("NumMedi" => $NumMedi, "pwd" => $Pwd));

if (is_soap_fault($result)) {
trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring})", E_USER_ERROR);
}

// Affichage de la requete
var_dump($result);

?>