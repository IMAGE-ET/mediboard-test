<?php

/**
 * Source HTTP
 *  
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CSourceHTTP extends CExchangeSource {
  // DB Table key
  var $source_http_id = null;
  
  var $_filename = null;
  var $_fieldname = null;
  var $_mimetype = null;
  var $_user = null;
  var $_password = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_http';
    $spec->key   = 'source_http_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();    
    return $specs;
  }
  
  function send($evenement_name = null) {
    $boundary = "-------MB-BOUNDARY-".uniqid();
    $content =  "--$boundary\r\n".
                "Content-Disposition: form-data; name=\"txtDocument\"; filename=\"$this->_filename\"\r\n".
                "Content-Type: $this->_mimetype\r\n\r\n".
                $this->_data."\r\n";
    $content .= "--$boundary\r\n".
            "Content-Disposition: form-data; name=\"chkDoubleOK\"\r\n\r\n".
            "\r\n";
    $content .= "--$boundary\r\n".
            "Content-Disposition: form-data; name=\"docs\"\r\n\r\n".
            "\r\n";
    $content .= "--$boundary\r\n".
            "Content-Disposition: form-data; name=\"user\"\r\n\r\n".
            "$this->_user\r\n";
    $content .= "--$boundary\r\n".
            "Content-Disposition: form-data; name=\"pwd\"\r\n\r\n".
            "$this->_password\r\n";
    $content .= "--$boundary\r\n".
            "Content-Disposition: form-data; name=\"lang\"\r\n\r\n".
            "\r\n";
    $content .= "--$boundary\r\n".
            "Content-Disposition: form-data; name=\"btnTransfert\"\r\n\r\n".
            "TransfÈrer\r\n";
    $content .= "--$boundary--\r\n";
    $context = stream_context_create(
      array(
        'http' => array(
              'method' => 'POST',
              'header' => "Content-Type: multipart/form-data; boundary=$boundary\r\n".
                          "Content-Length: ".strlen($content)."\r\n",
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
?>