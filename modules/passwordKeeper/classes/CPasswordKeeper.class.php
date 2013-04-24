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
  const SAMPLE = "Ceci est une chaîne témoin servant à vérifier la phrase de passe saisie.";

  /** @var int Password keeper ID */
  public $password_keeper_id;

  /** @var string Keeper name */
  public $keeper_name;

  /** @var bool Is the password keeper public? */
  public $is_public;

  /** @var string Random initialisation vector */
  public $iv;

  /** @var string Sample string for testing passphrase */
  public $sample;

  /** @var int CUser reference */
  public $user_id;

  /** @var CPasswordCategory[] references */
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
    $backProps["categories"] = "CPasswordCategory password_keeper_id";

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
    $this->generateIV();

    /*
     * Lorsque le trousseau existe et que la phrase est modifiée
     * Charge tous les mots de passe et les re-chiffre
     */
    $this->loadRefsBack();
    foreach ($this->_ref_categories as $_category) {
      $_category->loadRefsBack();
      foreach ($_category->_ref_passwords as $_password) {
        $_password->renew(CValue::sessionAbs("passphrase"), $this->_passphrase);
      }
    }

    $this->sample = $this->encrypt();

    if ($msg = parent::store()) {
      return $msg;
    }
  }

  /**
   * Génération d'un vecteur d'initialisation
   */
  function generateIV() {
    CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/Random");
    $this->iv = bin2hex(crypt_random_string(16));
  }

  /**
   * Chiffrement de la chaîne témoin
   *
   * @return string
   */
  function encrypt() {
    CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/AES");

    $cipher = new Crypt_AES(CRYPT_AES_MODE_CTR);
    $cipher->setKey($this->_passphrase);
    $cipher->setIV($this->iv);

    $crypted = rtrim(base64_encode($cipher->encrypt(self::SAMPLE)), "\0\3");

    return $crypted;
  }

  /**
   * Teste la validité de la phrase de passe via la chaîne témoin
   *
   * @param string $passphrase Phrase de passe saisie par l'utilisateur
   *
   * @return bool
   */
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