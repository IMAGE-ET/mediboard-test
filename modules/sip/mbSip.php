<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can, $m, $a;

$can->needsRead();

CAppUI::requireModuleClass("sip", "hprimsoaphandler");

$wsdl = CValue::get('wsdl');

// première étape : désactiver le cache lors de la phase de test
ini_set("soap.wsdl_cache_enabled", "0");

$username = CValue::get('username');
$password = CValue::get('password');
  
// Génération du fichier WSDL
if(isset($wsdl)) {
  header('Content-Type: application/xml; charset=UTF-8');
  
  $functions = CHprimSoapHandler::$paramSpecs;
  
  $wsdlFile = new CWsdlDocument();
  $wsdlFile->addTypes();
  $wsdlFile->addMessage($functions);
  $wsdlFile->addPortType($functions);
  $wsdlFile->addBinding($functions);
  $wsdlFile->addService($username, $password, $m, $a);
  
  echo $wsdlFile->saveXML();
} else {
	// on indique au serveur à quel fichier de description il est lié
	try {
	  $serverSOAP = new SoapServer(CAppui::conf("base_url")."/index.php?login=1&username=$username&password=$password&m=$m&a=$a&wsdl");
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