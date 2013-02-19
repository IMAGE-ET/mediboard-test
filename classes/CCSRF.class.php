<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CCSRF {
  /**
   * Check anti-CSRF protection
   */
  static function checkProtection() {
    if (!CAppUI::conf("csrf_protection") || strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {
      return;
    }

    if (!isset($_POST["csrf"])) {
      CAppUI::setMsg("CCSRF-no_token", UI_MSG_ERROR);
      return;
    }

    if (array_key_exists($_POST['csrf'], $_SESSION["tokens"])) {
      $token = $_SESSION['tokens'][$_POST['csrf']];

      if ($token["lifetime"] >= time()) {
        foreach ($token["fields"] as $_field => $_value) {
          if (CValue::read($_POST, $_field) != $_value) {
            CAppUI::setMsg("CCSRF-form_corrupted", UI_MSG_ERROR);
            unset($_SESSION['tokens'][$_POST['csrf']]);
            return;
          }
        }
        //mbTrace("Le jeton est accepté !");
        unset($_SESSION['tokens'][$_POST['csrf']]);
      }
      else {
        CAppUI::setMsg("CCSRF-token_outdated", UI_MSG_ERROR);
        unset($_SESSION['tokens'][$_POST['csrf']]);
      }

      return;
    }

    CAppUI::setMsg("CCSRF-token_does_not_exist", UI_MSG_ERROR);
    return;
  }

  /**
   * Generate a 32-bytes random token
   *
   * @return string
   */
  static function generateToken() {
    // Mcrypt has a better CSPRNG method
    if (function_exists('mcrypt_create_iv')) {
      // CSPRNG initialisation
      srand();
      return bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    }
    else {
      // Instead of Mcrypt, we use mt_rand() method
      return hash("SHA256", mt_rand());
    }
  }
}