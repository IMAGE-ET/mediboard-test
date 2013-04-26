<?php
/**
 * $Id$
 *  
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CApp::setTimeLimit(360);

// Récupération du fichier
$file       = CValue::files('datafile');
$passphrase = CValue::post('passphrase');

if (!$passphrase || !$file) {
  $msg = "Le fichier et la phrase de passe doivent être saisis.";
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}

$user = CMediusers::get();

$dom = new CMbXMLDocument();

if (!$dom->load($file['tmp_name'])) {
  CAppUI::redirect('m=passwordKeeper&a=vw_import_keeper&dialog=1');
}

$xpath = new CMbXPath($dom);

$keeperNode = $xpath->queryUniqueNode("/keeper");
$keeperName = $keeperNode->getAttribute("name");
if ($keeperNode->nodeName != "keeper") {
  CAppUI::redirect('m=passwordKeeper&a=vw_import_keeper&dialog=1');
}

$keeper = new CPasswordKeeper();
$keeper->keeper_name = $keeperName;
$keeper->_passphrase = $passphrase;
$keeper->user_id     = $user->_id;
$keeper->store();

$categoryNodes = $xpath->query("//category");
foreach ($categoryNodes as $_categoryNode) {
  $category = new CPasswordCategory();
  $category->category_name = $_categoryNode->getAttribute("name");
  $category->password_keeper_id = $keeper->_id;
  $category->store();

  $passwordNodes = $xpath->query(".//password", $_categoryNode);
  foreach ($passwordNodes as $_passwordNode) {
    $password = new CPasswordEntry();

    $desc = $xpath->queryUniqueNode("description", $_passwordNode);
    $password->password_description = $desc->nodeValue;

    $crypted = $xpath->queryUniqueNode("crypted", $_passwordNode);
    $password->password = $crypted->nodeValue;

    $last_change = $xpath->queryUniqueNode("last_change", $_passwordNode);
    $password->password_last_change = $last_change->nodeValue;

    $iv = $xpath->queryUniqueNode("iv", $_passwordNode);
    $password->iv = $iv->nodeValue;

    $comments = $xpath->queryUniqueNode("comments", $_passwordNode);
    $password->password_comments = $comments->nodeValue;

    $password->category_id = $category->_id;
    $password->store(null, true);
  }
}

CAppUI::redirect('m=passwordKeeper&a=vw_import_keeper&dialog=1');