<?php

/**
 * $Id$
 *  
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Create a client for manipulate the request HTTP to a site
 */
class CHTTPClient {

  private $handle;
  public $url;
  public $option = array();
  public $header = array();

  /**
   * Construct the HTTP client
   *
   * @param String $url Site URL
   *
   * @throws Exception
   */
  function __construct($url) {
    $this->url = $url;
    $init      = curl_init($url);
    if ($init === false) {
      throw new Exception("Initialisation impossible");
    }
    $this->handle = $init;

  }

  /**
   * Execute a request GET
   *
   * @param bool $close Close the connection
   *
   * @return String
   */
  function get($close = true) {
    $this->setOption(CURLOPT_HTTPGET, true);
    return $this->executeRequest($close);
  }

  /**
   * Execute a request POST
   *
   * @param String $content uri of post data (http_build_query)
   * @param bool   $close   Close the connection
   *
   * @return String
   */
  function post($content, $close = true) {
    $this->setOption(CURLOPT_POST, true);
    $this->setOption(CURLOPT_POSTFIELDS, $content);
    return $this->executeRequest($close);
  }

  /**
   * Execute a request PUT
   *
   * @param String $content uri of post data (http_build_query)
   * @param bool   $close   Close the connection
   *
   * @return String
   */
  function put($content, $close = true) {
    $this->setOption(CURLOPT_CUSTOMREQUEST, "PUT");
    $this->setOption(CURLOPT_POSTFIELDS, $content);
    return $this->executeRequest($close);
  }

  /**
   * Execute a request DELETE
   *
   * @param bool $close Close the connection
   *
   * @return String
   */
  function delete($close = true) {
    $this->setOption(CURLOPT_CUSTOMREQUEST, "DELETE");
    return $this->executeRequest($close);
  }

  /**
   * Execute a request HEAD
   *
   * @param bool $close Close the connection
   *
   * @return String
   */
  function head($close = true) {
    $this->setOption(CURLOPT_NOBODY, true);
    return $this->executeRequest($close);
  }

  /**
   * Assign a HTTP authentification
   *
   * @param String $username Username for the site
   * @param String $password Password for the site
   *
   * @return void
   */
  function setHTTPAuthentification($username, $password) {
    $this->setOption(CURLOPT_USERPWD, "$username:$password");
  }

  /**
   * Assign a SSL authentification
   *
   * @param String $local_cert Certificate path
   * @param String $passphrase Certificate passphrase
   *
   * @return void
   */
  function setSSLAuthentification($local_cert, $passphrase = null) {
    $this->setOption(CURLOPT_SSL_VERIFYHOST, 2);
    $this->setOption(CURLOPT_SSLCERT, $local_cert);
    if ($passphrase) {
      $this->setOption(CURLOPT_SSLCERTPASSWD, $passphrase);
    }
  }

  /**
   * Verify the peer certificate
   *
   * @param String $ca_cert Certificate authority path
   *
   * @return void
   */
  function setSSLPeer($ca_cert) {
    $this->setOption(CURLOPT_SSL_VERIFYPEER, 1);
    $this->setOption(CURLOPT_CAINFO, $ca_cert);
  }

  /**
   * Assign the option to use
   *
   * @param String $name  Option name
   * @param String $value Option value
   *
   * @return void
   */
  function setOption($name, $value) {
    $this->option[$name] = $value;
  }

  /**
   * Create the CURL option
   *
   * @return void
   * @throws Exception
   */
  private function createOption() {
    if (count($this->header) !== 0) {
      $this->option[CURLOPT_HTTPHEADER] = $this->header;
    }
    $result = curl_setopt_array($this->handle, $this->option);
    if (!$result) {
      throw new Exception("Impossible d'ajouter une option");
    }
  }

  /**
   * Execute the request to the site
   *
   * @param bool $close Close the connexion
   *
   * @return String
   * @throws Exception
   */
  function executeRequest($close = true) {
    $handle = $this->handle;
    $this->setOption(CURLOPT_RETURNTRANSFER, true);
    $this->createOption();

    $result = curl_exec($handle);
    if (curl_errno($handle)) {
      throw new Exception(curl_error($handle));
    }

    if ($close) {
      $this->closeConnection();
    }

    return $result;
  }

  /**
   * Close the connection
   *
   * @return void
   */
  function closeConnection() {
    curl_close($this->handle);
  }

  /**
   * Check the URL disponibility
   *
   * @param String   $url         URL site
   * @param String[] $option      Option array
   * @param Boolean  $return_body Return the content of the page
   *
   * @return bool|int
   */
  static function checkUrl($url, $option = null, $return_body = false) {
    try {
      $http_client = new CHTTPClient($url);
      if ($option) {
        if (CMbArray::get($option, "ca_cert")) {
          $http_client->setSSLPeer($option["ca_cert"]);
        }
        if (CMbArray::get($option, "username")||CMbArray::get($option, "password")) {
          $http_client->setHTTPAuthentification($option["username"], $option["password"]);
        }
        if (CMbArray::get($option, "local_cert")) {
          $http_client->setSSLAuthentification($option["local_cert"], $option["passphrase"]);
        }
      }
      $http_client->setOption(CURLOPT_HEADER, true);
      $result = $http_client->get();
    }
    catch (Exception $e) {
      return false;
    }

    if ($return_body) {
      return $result;
    }

    return (preg_match("|200|", $result));
  }
}