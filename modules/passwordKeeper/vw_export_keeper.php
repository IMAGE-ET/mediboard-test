<?php
/**
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

if (empty($_SERVER["HTTPS"])) {
  $msg = "Vous devez utiliser le protocole HTTPS pour utiliser ce module.";
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}

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
  $dom = new CMbXMLDocument("UTF-8");
  $keeperNode = $dom->addElement($dom, "keeper");
  $dom->addAttribute($keeperNode, "name", utf8_encode($keeper->keeper_name));

  $categoriesNode = $dom->addElement($keeperNode, "categories");

  $categories = $keeper->loadRefsBack();
  foreach ($categories as $_category) {
    $categoryNode = $dom->addElement($categoriesNode, "category");
    $dom->addAttribute($categoryNode, "name", utf8_encode($_category->category_name));

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
      $dom->insertTextElement($passwordNode, "description", utf8_encode($_password->password_description));
      $dom->insertTextElement($passwordNode, "crypted"    , utf8_encode($newPass));
      $dom->insertTextElement($passwordNode, "last_change", utf8_encode($_password->password_last_change));
      $dom->insertTextElement($passwordNode, "iv"         , utf8_encode($_password->iv));
      $dom->insertTextElement($passwordNode, "comments"   , utf8_encode($_password->password_comments));
    }
  }

  // Préparation des en-têtes
  $name = $keeper->keeper_name.time().".xml";
  header("Content-Type: application/xml");
  header("Content-Disposition: attachment; filename=\"$name\"");
  echo $dom->saveXML();
}

CApp::rip();