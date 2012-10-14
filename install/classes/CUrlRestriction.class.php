<?php
/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage Intaller
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

/**
 * URL restriction
 */
class CUrlRestriction extends CCheckable {
  var $url = "";
  var $description = "";
  var $allowed = false;

  /**
   * Get the last HTTP code from a requested URL (follow redirection)
   * 
   * @param string $url URL to request
   * 
   * @return string
   */
  private function getHTTPResponseCode($url) {
    ini_set("default_socket_timeout", 2);
    @file_get_contents($url);
    $headers = $http_response_header;
  
    $response = false;
    if (is_array($headers)) {
      foreach ($headers as $header) {
        if (substr($header, 0, 4) == "HTTP") {
          $response = explode(" ", $header);
          $response = $response[1];
        }
      }
    }
    
    return $response;
  }
  
  function check($strict = true){
    $code = substr($this->getHTTPResponseCode($this->url), 0, 1);
    
    if ( $this->allowed && $code == 2 ||
        !$this->allowed && $code == 4) {
      return true;
    }
    
    return false;
  }
  
  function getAll(){
    $http = "http://";
    if (array_key_exists("HTTPS", $_SERVER)) {
      $http = "https://";
    }
    
    $url = $http.dirname(dirname($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']));
    
    $restrictions = array();
    
    $restriction = new self;
    $restriction->url = "$url/tmp/locales-fr.js";
    $restriction->description = "Fichier de traductions";
    $restriction->allowed = true;
    $restrictions[] = $restriction;
    
    $restriction = new self;
    $restriction->url = "$url/files";
    $restriction->description = "Répertoire des fichiers utilisateur";
    $restrictions[] = $restriction;
    
    $restriction = new self;
    $restriction->url = "$url/tmp";
    $restriction->description = "Répertoire des fichiers temporaires";
    $restrictions[] = $restriction;
    
    $restriction = new self;
    $restriction->url = "$url/tmp/mb-log.html";
    $restriction->description = "Journal d'erreurs système";
    $restrictions[] = $restriction;
    
    return $restrictions;
  }
}
