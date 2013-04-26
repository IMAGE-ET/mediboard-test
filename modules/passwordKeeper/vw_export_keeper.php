<?php
/**
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

CPasswordKeeper::checkHTTPS();

CCanDo::checkAdmin();

$password_keeper_id = CValue::postOrSession("password_keeper_id");
$oldPassphrase      = CValue::post("oldPassphrase");
$newPassphrase      = CValue::post("newPassphrase");

$passphrase         = CValue::sessionAbs("passphrase");

if ($passphrase != $oldPassphrase) {
  $msg = "Phrase de passe incorrecte.";
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}

$user   = CMediusers::get();
$keeper = new CPasswordKeeper();
$keeper->load($password_keeper_id);

if ($keeper->_id && $keeper->user_id == $user->_id) {
  $dom = new CMbXMLDocument("ISO-8859-1");
  $keeperNode = $dom->addElement($dom, "keeper");
  $dom->addAttribute($keeperNode, "name", $keeper->keeper_name);

  $categoriesNode = $dom->addElement($keeperNode, "categories");

  $categories = $keeper->loadRefsBack();
  foreach ($categories as $_category) {
    $categoryNode = $dom->addElement($categoriesNode, "category");
    $dom->addAttribute($categoryNode, "name", $_category->category_name);

    $passwordsNode = $dom->addElement($categoryNode, "passwords");

    $passwords = $_category->loadRefsBack();
    foreach ($passwords as $_password) {
      // Déchiffrement du mot de passe
      $_password->password = $_password->getPassword();
      // Génération d'un nouveau vecteur d'inistalisation
      $_password->generateIV();
      // Chiffrement avec la nouvelle phrase de passe saisie
      $newPass = $_password->encrypt($newPassphrase);

      $passwordNode = $dom->addElement($passwordsNode, "password");
      $dom->insertTextElement($passwordNode, "description", $_password->password_description);
      $dom->insertTextElement($passwordNode, "crypted"    , $newPass);
      $dom->insertTextElement($passwordNode, "last_change", $_password->password_last_change);
      $dom->insertTextElement($passwordNode, "iv"         , $_password->iv);
      $dom->insertTextElement($passwordNode, "comments"   , $_password->password_comments);
    }
  }

  // Préparation des en-têtes
  $name = $keeper->keeper_name.time().".xml";
  header("Content-Type: application/xml");
  header("Content-Disposition: attachment; filename=\"$name\"");
  echo $dom->saveXML();
}

CApp::rip();