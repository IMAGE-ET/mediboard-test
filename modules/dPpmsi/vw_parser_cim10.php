<?php 

/**
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

$path = CAppUI::getTmpPath("pmsi");
$path .= "/cim10/LIBCIM10.TXT";
$result = array();
if (file_exists($path)) {
  if (!$fp = fopen("$path", "r+")) {
    CAppUI::displayAjaxMsg("Echec de l'ouverture du fichier LIBCIM10.txt", UI_MSG_WARNING);
  }
  else {
    while (!feof($fp)) {

      // On récupère une ligne
      $ligne = fgets($fp);

      if ($ligne) {
        $ligne = utf8_encode($ligne);
        $_ligne = explode('|', $ligne);
        $_ligne = array_map("trim", $_ligne);
        $result [] = implode(";", $_ligne)."\n";
      }
    }
    fclose($fp); // On ferme le fichier
  }

$path = CAppUI::getTmpPath("pmsi");
    $fic =  $path."/cim10/MCCIM10.csv";
    $fichier = fopen($fic, "w+");
    fwrite($fichier, implode("", $result));
    fclose($fichier);

CApp::rip();















}