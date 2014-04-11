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
 * Class for manipulate the certificate SSL
 */
class CSSLCertificate {

  public $certificate_path;
  public $certificate;
  public $chain;
  public $pivate_key;
  public $passphrase;
  public $private_key_handle;


  /**
   * Construct
   *
   * @param String $path_certificate P12 file
   * @param String $passphrase       Passphrase for the certificate
   */
  function __construct($path_certificate, $passphrase = null) {
    $this->passphrase = $passphrase;
    $this->certificate_path = $path_certificate;
    openssl_pkcs12_read(file_get_contents($path_certificate), $array_cert, $passphrase);
    $this->certificate = $array_cert["cert"];
    $this->pivate_key = $array_cert["pkey"];
    $this->chain = $array_cert["extracerts"];
  }

  /**
   * Return the certificate with or without the header
   *
   * @param boolean $withHeader 'Begin certificate' present
   *
   * @return String
   */
  function getCertificate($withHeader = true) {
    if ($withHeader) {
      return $this->certificate;
    }

    return self::deleteHeader($this->certificate);
  }

  /**
   * Delete the header of the certificate
   *
   * @param String $certificate Certificate
   *
   * @return String
   */
  static function deleteHeader($certificate) {
    preg_match_all("#(?<=-{5})[^-]+(?=-{5}\\w)#", $certificate, $matches);
    return $matches[0][0];
  }

  /**
   * Return a resource of the private key
   *
   * @return resource
   */
  function getPrivateKey() {
    $this->private_key_handle = openssl_pkey_get_private($this->pivate_key, $this->passphrase);
    return $this->private_key_handle;
  }

  /**
   * Sign the data with the certificate
   *
   * @param String  $data      Data to sign
   * @param boolean $base64    Encode the resut ot base 64
   * @param int     $algorithm Algorithm to use
   *
   * @return string
   */
  function sign($data, $base64 = true, $algorithm = OPENSSL_ALGO_SHA1) {
    if (!$this->private_key_handle) {
      $this->getPrivateKey();
    }

    openssl_sign($data, $sign_openssl, $this->private_key_handle, $algorithm);

    if ($base64) {
      $sign_openssl = base64_encode($sign_openssl);
    }

    return $sign_openssl;
  }

  /**
   * Return the issuer of the certificate
   *
   * @param boolean $rfc representation RFC of the dn
   *
   * @return String
   */
  function getIssuerDn($rfc = false) {
    $dn = CMbSecurity::getDNString($this->certificate);
    if (!$rfc) {
      return $dn;
    }

    preg_match_all("#[^,]+#", $dn, $match);
    $rdn = "";
    $separator = "+";
    $match = array_reverse($match[0]);
    foreach ($match as $_dn) {
      if (strpos(current($match), "OU=") !== false) {
        $separator = ",";
      }
      $rdn .= trim($_dn).$separator;
    }

    $rdn = substr($rdn, 0, -1);
    return $rdn;
  }

  /**
   * Return the certificate to array format
   *
   * @return String[]
   */
  function getCertificateToArray() {
    return CMbSecurity::getInformationCertificate($this->certificate);
  }

  /**
   * Return the fingerPrint of the certificate
   *
   * @param String $certificate PEM Certificate
   *
   * @return string
   */
  static function getFingerPrint($certificate) {

    $pem = preg_replace('/\-+BEGIN CERTIFICATE\-+/', '', $certificate);
    $pem = preg_replace('/\-+END CERTIFICATE\-+/', '', $pem);
    $pem = str_replace(array("\n","\r"), '', trim($pem));

    $result = sha1(base64_decode($pem), true);

    return $result;
  }
}