<?php

/**
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

/**
 * Manage a password keeper
 */
class CPasswordKeeper extends CMbObject {
  /** Sample string */
  const SAMPLE = "toto";

  /** @var  Password keeper ID */
  public $password_keeper_id;

  public $keeper_name;

  /** @var  Is the password keeper public? */
  public $is_public;

  /** @var  Random initialisation vector */
  public $iv;

  /** @var  Sample string for testing passphrase */
  public $sample;

  /** @var  CUser reference */
  public $user_id;

  /** @var  CPasswordCategory references */
  public $_ref_categories;

  /** @var  Passphrase, needed for testing sample string */
  public $_passphrase;

  function getSpec() {
    $spec = parent::getSpec();

    $spec->table = 'password_keeper';
    $spec->key   = 'password_keeper_id';

    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["categories"] = "CPasswordCategory category_id";

    return $backProps;
  }

  function getProps() {
    $props = parent::getProps();

    $props["keeper_name"] = "str notNull maxLength|50";
    $props["is_public"]   = "enum list|0|1 notNull default|0";
    $props["iv"]          = "str notNull show|0 loggable|0";
    $props["sample"]      = "password notNull show|0 loggable|0";
    $props["_passphrase"] = "password notNull show|0 loggable|0";
    $props["user_id"]     = "ref notNull class|CUser";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->keeper_name;
  }

  function loadRefsBack() {
    return $this->_ref_categories = $this->loadBackRefs("categories");
  }

  function store() {
    // Si création : génération du vecteur d'initialisation
    if (!$this->_id || $this->fieldModified("_passphrase")) {
      CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/Random");
      $this->iv = bin2hex(crypt_random_string(16));
    }

    $this->sample = $this->encrypt();

    if ($msg = parent::store()) {
      return $msg;
    }
  }

  function encrypt() {
    CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/AES");

    $cipher = new Crypt_AES(CRYPT_AES_MODE_CTR);
    $cipher->setKey($this->_passphrase);
    $cipher->setIV($this->iv);

    $crypted = rtrim(base64_encode($cipher->encrypt(self::SAMPLE)), "\0\3");

    return $crypted;
  }

  function testSample($passphrase) {
    CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/AES");

    $cipher = new Crypt_AES(CRYPT_AES_MODE_CTR);
    $cipher->setKey($passphrase);
    $cipher->setIV($this->iv);

    $decrypted = rtrim(base64_decode($this->sample), "\0\3");
    $decrypted = $cipher->decrypt($decrypted);

    return ($decrypted === self::SAMPLE);
  }
}