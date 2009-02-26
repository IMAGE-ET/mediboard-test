<?php

// première étape : désactiver le cache lors de la phase de test
ini_set("soap.wsdl_cache_enabled", "0");

function saveNewDocument_withStringFile($parameters) {
  return 1;
}

function getStatus($parameters) {
  return 10;
}

function cancelDocument($parameters) {
  return 3;
}

// on indique au serveur à quel fichier de description il est lié
$serveurSOAP = new SoapServer("http://192.168.0.101/~yohann/mediboard_yohann/tests/medinetWebServices.xml?WSDL");

// ajouter la fonction getHello au serveur
$serveurSOAP->addFunction('saveNewDocument_withStringFile');

// ajouter la fonction enregistrementPatient au serveur
$serveurSOAP->addFunction('getStatus');

$serveurSOAP->addFunction('cancelDocument');

// lancer le serveur
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serveurSOAP->handle();
  } else {
     echo '<strong>This SOAP server can handle following functions : </strong>';    
     echo '<ul>';
     foreach($serveurSOAP -> getFunctions() as $func)        
          echo '<li>' , $func , '</li>';
     echo '</ul>';
  }


?>