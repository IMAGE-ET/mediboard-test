<?php

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Yohann  
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
}

?>