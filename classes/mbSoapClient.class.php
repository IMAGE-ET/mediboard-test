<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CMbSOAPClient class
 */
class CMbSOAPClient extends SoapClient {
  function __construct($rooturl) {
  	
    if (!$html = file_get_contents($rooturl)) {
    	trigger_error("Impossible d'analyser l'url : ".$rooturl, E_USER_ERROR);
    	return;
    }

    if (strpos($html, "<?xml") === false) {
      trigger_error("Erreur de connexion sur le service web. WSDL non accessible ou au mauvais format.", E_USER_ERROR);
      return;
    }
    
    parent::__construct($rooturl);
  }
  
  static public function make($rooturl, $login, $password) {
    if (preg_match('#\%u#', $rooturl)) 
      $rooturl = str_replace('%u', $login, $rooturl);
    
    if (preg_match('#\%p#', $rooturl)) 
      $rooturl = str_replace('%p', $password, $rooturl);

    if (!$client = new CMbSOAPClient($rooturl)) {
      trigger_error("Instanciation du SoapClient impossible.");
    }
    return $client;
  }
}

?>