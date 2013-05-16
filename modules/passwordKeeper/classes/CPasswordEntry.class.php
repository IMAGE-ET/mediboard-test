<?php

/**
 * $Id$
 *
 * @category Password_Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

/**
 * Manage a password entry
 */
class CPasswordEntry extends CMbObject {
  /** @var int Password ID */
  public $password_id;

  /** @var string Password description */
  public $password_description;

  /** @var string Password */
  public $password;

  /** @var datetime Datetime when password last changed */
  public $password_last_change;

  /** @var string Random initialisation vector */
  public $iv;

  /** @var string Password comments */
  public $password_comments;

  /** @var CPasswordCategory[] reference */
  public $category_id;

  /**
   * @see parent::getSpec()
   *
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();

    $spec->table = 'password_entry';
    $spec->key   = 'password_id';

    return $spec;
  }

  /**
   * @see parent::getBackProps()
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }

  /**
   * @see parent::getProps()
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();

    $props["password_description"] = "str notNull maxLength|50";
    $props["password"]             = "password notNull show|0 loggable|0";
    $props["password_last_change"] = "dateTime notNull";
    $props["iv"]                   = "str notNull show|0 loggable|0";
    $props["password_comments"]    = "text";
    $props["category_id"]          = "ref notNull class|CPasswordCategory";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->password_description;
  }

  /**
   * @see parent::store()
   *
   * @param string $passphrase Passphrase to use
   * @param bool   $import     Is import
   *
   * @return null|string
   */
  function store($passphrase = null, $import = null) {
    // Si importation en cours, on enregistre simplement en base
    if ($import) {
      if ($msg = parent::store()) {
        return $msg;
      }

      return;
    }

    // Si création : génération du vecteur d'initialisation
    if (!$this->_id || $this->fieldModified("password")) {
      $this->generateIV();

      // Si la passphrase est donnée, c'est un renouvellement de chiffrement, pas de mot de passe
      if (!$passphrase) {
        // Date de création du mot de passe
        $this->password_last_change = CMbDT::dateTime();
      }
    }

    $this->password = $this->encrypt($passphrase);

    if ($msg = parent::store()) {
      return $msg;
    }
  }

  /**
   * Génération d'un vecteur d'initialisation
   *
   * @return void
   */
  function generateIV() {
    CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/Random");
    $this->iv = bin2hex(crypt_random_string(16));
  }

  /**
   * Chiffrement d'un mot de passe
   *
   * @param string $passphrase Phrase de passe à appliquer
   *
   * @return string
   */
  function encrypt($passphrase = null) {
    if (!$passphrase) {
      $passphrase = CValue::sessionAbs("passphrase");
    }

    CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/AES");

    $cipher = new Crypt_AES(CRYPT_AES_MODE_CTR);
    $cipher->setKey($passphrase);
    $cipher->setIV($this->iv);

    $crypted = rtrim(base64_encode($cipher->encrypt($this->password)), "\0\3");

    return $crypted;
  }

  /**
   * Déchiffrement d'un mot de passe
   *
   * @param string $passphrase Phrase de passe à appliquer
   *
   * @return string
   */
  function getPassword($passphrase = null) {
    if (!$passphrase) {
      $passphrase = CValue::sessionAbs("passphrase");
    }

    CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/AES");

    $cipher = new Crypt_AES(CRYPT_AES_MODE_CTR);
    $cipher->setKey($passphrase);
    $cipher->setIV($this->iv);

    $decrypted = rtrim(base64_decode($this->password), "\0\3");
    $decrypted = $cipher->decrypt($decrypted);

    return $decrypted;
  }

  /**
   * Renouvellement du chiffrement d'un mot de passe
   *
   * @param string $oldPassphrase Old passphrase to use for uncrypting
   * @param string $newPassphrase New passphrase to use for crypting
   *
   * @return void
   */
  function renew($oldPassphrase, $newPassphrase) {
    // Déchiffrement pour mot de passe avec l'ancienne phrase de passe
    $this->password = $this->getPassword($oldPassphrase);
    // Pour le rechiffrer ensuite
    $this->store($newPassphrase);
  }
}
