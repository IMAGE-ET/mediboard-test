<?php
/**
 * Source HTTP
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CSourceHTTP extends CExchangeSource {
  // DB Table key
  public $source_http_id;
  
  public $_filename;
  public $_fieldname;
  public $_mimetype;
  public $_authorization = "";
  public $_disposition = "";

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_http';
    $spec->key   = 'source_http_id';
    return $spec;
  }
  
  function send($evenement_name = null, $tab_data = null) {
    if ($this->_filename) {
      $this->_disposition = "Content-Disposition: form-data; name=\"txtDocument\"; filename=\"$this->_filename\"\r\n";
    }
    $boundary = "-------MB-BOUNDARY-".uniqid();
    $content =  "--$boundary\r\n".
                $this->_disposition.
                "Content-Type: $this->_mimetype\r\n\r\n".
                $this->_data."\r\n";
    if ($tab_data) {
      foreach ($tab_data as $key => $value) {
        $content .= "--$boundary\r\n" .
          "Content-Disposition: form-data; name=\"$key\"\r\n\r\n" .
          "$value\r\n";
      }
    }
    $content .= "--$boundary--\r\n";

    $context = stream_context_create(
      array(
        'http' => array(
              'method' => 'POST',
              'header' => "Content-Type: $this->_mimetype"."\r\n".
                          "Content-Length: ".strlen($content)."\r\n".
                          $this->_authorization,
              'content' => $content,
        ),
      )
    );
    $this->_acquittement = file_get_contents($this->host, false, $context);
  }
    
  function receive() {
  }
  
  function isReachableSource() {
  }
  
  function isAuthentificate() {
  }
  
  function getResponseTime() {
  }
}
