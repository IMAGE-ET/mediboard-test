<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsRead();

CAppUI::requireModuleClass("sip", "hprimsoaphandler");

$wsdl = CValue::get('wsdl');

// première étape : désactiver le cache lors de la phase de test
ini_set("soap.wsdl_cache_enabled", "0");

// Génération du fichier WSDL
if(isset($wsdl)) {
  header('Content-Type: application/xml; charset=UTF-8');

  $username = CValue::get('username');
  $password = CValue::get('password');
  
  $functions = CHprimSoapHandler::$paramSpecs;
  
  $wsdlFile = new CWsdlDocument();
  $wsdlFile->addTypes();
  $wsdlFile->addMessage($functions);
  $wsdlFile->addPortType($functions);
  $wsdlFile->addBinding($functions);
  $documentation = "Ceci est une documentation du WebService";
  $wsdlFile->addService($documentation, $username, $password);
  
  echo $wsdlFile->saveXML();
} else {
	$username = CValue::get('username');
	$password = CValue::get('password');

	// on indique au serveur à quel fichier de description il est lié
	try {
	  $serverSOAP = new SoapServer("?login=1&username=$username&password=$password&m=sip&a=mbSip&suppressHeaders=1&wsdl");
	} catch (Exception $e) {
	  echo $e;
	}
	  
	$serverSOAP->setClass("CHprimSoapHandler"); 
	
	// lancer le serveur
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	  $serverSOAP->handle();
	} else {
	   echo '<strong>This SOAP server can handle following functions : </strong>';    
	   echo '<ul>';
	   foreach($serverSOAP -> getFunctions() as $func)        
	        echo '<li>' , $func , '</li>';
	   echo '</ul>';
	}

}

?>