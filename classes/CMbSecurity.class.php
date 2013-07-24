<?php

/**
 * $Id$
 *  
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CAppUI::requireLibraryFile("phpseclib/phpseclib/Math/BigInteger");
CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/Hash");
CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/Random");
CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/RSA");
CAppUI::requireLibraryFile("phpseclib/phpseclib/File/ASN1");
CAppUI::requireLibraryFile("phpseclib/phpseclib/File/X509");
CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/AES");
CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/DES");
CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/Rijndael");
CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/TripleDES");

/**
 * Generic security class, uses pure-PHP library phpseclib
 */
class CMbSecurity {
  // Ciphers
  const AES      = 1;
  const DES      = 2;
  const TDES     = 3;
  const RIJNDAEL = 4;

  // Encryption modes
  const CTR = CRYPT_AES_MODE_CTR;
  const ECB = CRYPT_AES_MODE_ECB;
  const CBC = CRYPT_AES_MODE_CBC;
  const CFB = CRYPT_AES_MODE_CFB;
  const OFB = CRYPT_AES_MODE_OFB;

  // Hash algorithms
  const MD2     = 1;
  const MD5     = 2;
  const MD5_96  = 3;
  const SHA1    = 4;
  const SHA1_96 = 5;
  const SHA256  = 6;
  const SHA384  = 7;
  const SHA512  = 8;

  /**
   * Generate a pseudo random string
   *
   * @param int $length String length
   *
   * @return string
   */
  static function getRandomString($length) {
    return bin2hex(crypt_random_string($length));
  }

  /**
   * Generate a pseudo random binary string
   *
   * @param int $length Binary string length
   *
   * @return string
   */
  static function getRandomBinaryString($length) {
    return crypt_random_string($length);
  }

  /**
   * Generate an initialisation vector
   *
   * @param int $length IV length
   *
   * @return string
   */
  static function generateIV($length = 16) {
    return self::getRandomString($length);
  }

  /**
   * Generate a UUID
   * Based on: http://www.php.net/manual/fr/function.uniqid.php#87992
   *
   * @return string
   */
  static function generateUUID() {
    $pr_bits = null;
    $pr_bits = self::getRandomBinaryString(25);

    $time_low = bin2hex(substr($pr_bits, 0, 4));
    $time_mid = bin2hex(substr($pr_bits, 4, 2));

    $time_hi_and_version       = bin2hex(substr($pr_bits, 6, 2));
    $clock_seq_hi_and_reserved = bin2hex(substr($pr_bits, 8, 2));

    $node = bin2hex(substr($pr_bits, 10, 6));

    /**
     * Set the four most significant bits (bits 12 through 15) of the
     * time_hi_and_version field to the 4-bit version number from
     * Section 4.1.3.
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
     */
    $time_hi_and_version = hexdec($time_hi_and_version);
    $time_hi_and_version = $time_hi_and_version >> 4;
    $time_hi_and_version = $time_hi_and_version | 0x4000;

    /**
     * Set the two most significant bits (bits 6 and 7) of the
     * clock_seq_hi_and_reserved to zero and one, respectively.
     */
    $clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
    $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
    $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

    return sprintf('%08s-%04s-%04x-%04x-%012s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node);
  }

  /**
   * Create a Crypt object
   *
   * @param int $encryption Cipher to use (AES, DES, TDES or RIJNDAEL)
   * @param int $mode       Encryption mode to use (CTR, ECB, CBC, CFB or OFB)
   *
   * @return Crypt_AES|Crypt_DES|Crypt_TripleDES
   */
  static function getCipher($encryption = self::AES, $mode = self::CTR) {
    switch ($encryption) {
      case self::AES:
        return new Crypt_AES($mode);

      case self::DES:
        return new Crypt_DES($mode);

      case self::TDES:
        return new Crypt_TripleDES($mode);

      case self::RIJNDAEL:
        return new Crypt_Rijndael($mode);
    }

    return false;
  }

  /**
   * Encrypt a text
   *
   * @param int    $encryption Cipher to use (AES, DES, TDES or RIJNDAEL)
   * @param int    $mode       Encryption mode to use (CTR, ECB, CBC, CFB or OFB)
   * @param string $key        Key to use
   * @param string $clear      Clear text to encrypt
   * @param string $iv         Initialisation vector to use
   *
   * @return bool|string
   */
  static function encrypt($encryption, $mode, $key, $clear, $iv = null) {
    $cipher = self::getCipher($encryption, $mode);

    if (!$cipher) {
      return false;
    }

    $cipher->setKey($key);

    switch ($mode) {
      case self::CBC:
      case self::CFB:
      case self::CTR:
      case self::OFB:
        $cipher->setIV($iv);
    }

    return rtrim(base64_encode($cipher->encrypt($clear)), "\0\3");
  }

  /**
   * Decrypt a text
   *
   * @param int    $encryption Cipher to use (AES, DES, TDES or RIJNDAEL)
   * @param int    $mode       Encryption mode to use (CTR, ECB, CBC, CFB or OFB)
   * @param string $key        Key to use
   * @param string $crypted    Cipher text to decrypt
   * @param string $iv         Initialisation vector to use
   *
   * @return bool|string
   */
  static function decrypt($encryption, $mode, $key, $crypted, $iv = null) {
    $cipher = self::getCipher($encryption, $mode);

    if (!$cipher) {
      return false;
    }

    $cipher->setKey($key);

    switch ($mode) {
      case self::CBC:
      case self::CFB:
      case self::CTR:
      case self::OFB:
        $cipher->setIV($iv);
    }

    return $cipher->decrypt(rtrim(base64_decode($crypted), "\0\3"));
  }

  /**
   * Global hashing function
   *
   * @param int    $algo   Hash algorithm to use
   * @param string $text   Text to hash
   * @param bool   $binary Binary or hexa output
   *
   * @return bool|string
   */
  static function hash($algo, $text, $binary = false) {
    $algos = array(
      self::MD2     => 'md2',
      self::MD5     => 'md5',
      self::MD5_96  => 'md5-96',
      self::SHA1    => 'sha1',
      self::SHA1_96 => 'sha1-96',
      self::SHA256  => 'sha256',
      self::SHA384  => 'sha384',
      self::SHA512  => 'sha512'
    );

    if (array_key_exists($algo, $algos)) {
      $hash = new Crypt_Hash($algos[$algo]);
      $fingerprint = $hash->hash($text);

      if (!$binary) {
        $fingerprint = bin2hex($fingerprint);
      }

      return $fingerprint;
    }

    return false;
  }

  /**
   * Filtering input data
   *
   * @param string $params Array to filter
   *
   * @return array
   */
  static function filterInput($params) {
    if (!is_array($params)) {
      return $params;
    }

    $patterns = array(
      "/password|passphrase/i",
      "/login/i"
    );

    $replacements = array(
      array("/.*/", "***"),
      array("/([^:]*):(.*)/i", "$1:***")
    );

    // We replace passwords and passphrases with a mask
    foreach ($params as $_key => $_value) {
      foreach ($patterns as $_k => $_pattern) {
        if (!empty($_value) && preg_match($_pattern, $_key)) {
          $params[$_key] = preg_replace($replacements[$_k][0], $replacements[$_k][1], $_value);
        }
      }
    }

    return $params;
  }

  /**
   * Validate the client certificate with the authority certificate
   *
   * @param String $certificate_client Client certificate
   * @param String $certificate_ca     Authority certificate
   *
   * @return bool
   */
  static function validateCertificate($certificate_client, $certificate_ca) {
    $x509 = new File_X509();

    $x509->loadX509($certificate_client);
    $x509->loadCA($certificate_ca);

    return $x509->validateSignature(FILE_X509_VALIDATE_SIGNATURE_BY_CA);
  }

  /**
   * Return the DN of the certificate
   *
   * @param String $certificate_client Client certificate
   *
   * @return String
   */
  static function getDNString($certificate_client) {
    $x509 = new File_X509();

    $x509->loadX509($certificate_client);

    return $x509->getDN(true);
  }

  /**
   * Return the Issuer DN of the certificate
   *
   * @param String $certificate_client Client certificate
   *
   * @return String
   */
  static function getIssuerDnString($certificate_client) {
    $x509 = new File_X509();

    $x509->loadX509($certificate_client);

    return $x509->getIssuerDN(true);
  }

  /**
   * Validate the client certificate with the current date
   *
   * @param String $certificate_client Client certificate
   *
   * @return bool
   */
  static function validateCertificateDate($certificate_client) {
    $x509 = new File_X509();

    $x509->loadX509($certificate_client);

    return $x509->validateDate();
  }

  /**
   * Return the information of certificate
   *
   * @param String $certificate_client Client certificate
   *
   * @return bool
   */
  static function getInformationCertificate($certificate_client) {
    $x509 = new File_X509();

    return $x509->loadX509($certificate_client);
  }

  /**
   * Verify that certificate is not revoked
   *
   * @param String $certificate_client String
   * @param String $list_revoked       String
   *
   * @return bool
   */
  static function isRevoked($certificate_client, $list_revoked) {
    $certificate = self::getInformationCertificate($certificate_client);

    if (!$certificate) {
      return false;
    }

    $serial = $certificate['tbsCertificate']['serialNumber']->value;

    $x509 = new File_X509();
    $crl = $x509->loadCRL($list_revoked);

    foreach ($crl["tbsCertList"]["revokedCertificates"] as $_cert) {
      if ($_cert["userCertificate"]->value === $serial) {
        return false;
      }
    }

    return true;
  }
}
