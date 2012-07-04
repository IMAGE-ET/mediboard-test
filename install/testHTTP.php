<?php /** $Id:$ **/

/**
 * @category Install
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Test returned HTTP codes from a requested URL
 * 
 * @param array  $urls [optional] List of URLs to test
 * @param string $type [optional] To know what is espected return value
 * 
 * @return array
 */
function testHTTPCode( $urls = array(), $type = "Autorisé" ) {
  switch ( $type ) {
    case "Autorisé":
      $startOfCode = "4";
      break;
    
    default:
      $startOfCode = "2";
  }
  
  $result = array();
  foreach ( $urls as $url ) {
    $lastHTTPCode = getLastHTTPCode($url);
    
    if ($lastHTTPCode) {
      if ( substr($lastHTTPCode, 0, 1) == $startOfCode ) {
        $result[$url] = array("type" => $type, "result" => false);
      }
      else {
        $result[$url] = array("type" => $type, "result" => true);
      }
    }    
  }
  
  return $result;
}

/**
 * Get the last HTTP code from a requested URL (follow redirection)
 * 
 * @param string $url URL to request
 * 
 * @return string
 */
function getLastHTTPCode( $url ) {
  ini_set("default_socket_timeout", 2);
  @file_get_contents($url);
  $headers = $http_response_header;

  $response = false;
  if ( is_array($headers) ) {
    foreach ( $headers as $key => $header ) {
      if ( substr($header, 0, 4) == "HTTP" ) {
        $response = split(" ", $header);
        $response = $response[1];
      }
    }
  }
  
  return $response;
}

?>
