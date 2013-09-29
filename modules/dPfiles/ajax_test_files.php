<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$dir = CFile::$directory . "/test";

CAppUI::stepAjax("Test de création de répertoire et d'un fichier dans ce répertoire", UI_MSG_WARNING);

// Création d'un répertoire

$directory_create = CMbPath::forceDir($dir);

if (!$directory_create) {
  CAppUI::stepAjax("Création de répertoire échoué", UI_MSG_ERROR); CApp::rip();
}

CAppUI::stepAjax("Création de répertoire Ok", UI_MSG_OK);

// Création d'un fichier

$file_create = file_put_contents($dir . "/test_file", "a");

if (!$file_create) {
  CAppUI::stepAjax("Création de fichier échoué", UI_MSG_ERROR);
  @unlink($dir);
  CApp::rip();
}

CAppUI::stepAjax("Création de fichier Ok", UI_MSG_OK);

// Suppression du fichier et du dossier
@unlink($dir . "/test_file");
@unlink($dir);

CAppUI::stepAjax("Fin de test de création de fichier", UI_MSG_WARNING);


