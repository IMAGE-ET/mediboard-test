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

  /**
   * @see parent::getSpec()
   *
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();

    $spec->table = 'password_keeper';
    $spec->key   = 'password_keeper_id';

    return $spec;
  }

  /**
   * @see parent::getBackProps()
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["categories"] = "CPasswordCategory password_keeper_id";

    return $backProps;
  }

  /**
   * @see parent::getProps()
   *
   * @return array
   */
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

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->keeper_name;
  }

  /**
   * Load all the categories of a CPasswordKeeper
   *
   * @return CPasswordCategories[]
   */
  function loadRefsCategories() {
    return $this->_ref_categories = $this->loadBackRefs("categories");
  }

  /**
   * @see parent::store()
   *
   * @return null|string
   */
  function store() {
    $this->generateIV();

    /*
     * Lorsque le trousseau existe et que la phrase est modifiée
     * Charge tous les mots de passe et les re-chiffre
     */

    foreach ($this->loadRefsCategories() as $_category) {
      foreach ($_category->loadRefsPasswords() as $_password) {
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
   *
   * @return void
   */
  function generateIV() {
    $this->iv = CMbSecurity::generateIV();
  }

  /**
   * Chiffrement de la chaîne témoin
   *
   * @return string
   */
  function encrypt() {
    return CMbSecurity::encrypt(CMbSecurity::AES, CMbSecurity::CTR, $this->_passphrase, self::SAMPLE, $this->iv);
  }

  /**
   * Teste la validité de la phrase de passe via la chaîne témoin
   *
   * @param string $passphrase Phrase de passe saisie par l'utilisateur
   *
   * @return bool
   */
  function testSample($passphrase) {
    $decrypted = CMbSecurity::decrypt(CMbSecurity::AES, CMbSecurity::CTR, $passphrase, $this->sample, $this->iv);

    return ($decrypted === self::SAMPLE);
  }

  /**
   * Check if HTTPS in use
   *
   * @return void
   */
  static function checkHTTPS() {
    if (empty($_SERVER["HTTPS"])) {
      $msg = "passwordKeeper-HTTPS-required";
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
  }
}
