<?php

/**
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Classe outils pour le module XDS
 */
class CXDSTools {

  /**
   * Génération des jeux de valeurs en xml
   *
   * @return bool
   */
  static function generateXMLToJv() {
    $files = glob("modules/xds/resources/jeux_de_valeurs/*.jv");

    foreach ($files as $_file) {
      $name = self::deleteDate(basename($_file));
      $csv = new CCSVFile($_file);
      $csv->jumpLine(3);
      $xml = new CXDSXmlJvDocument();
      while ($line = $csv->readLine()) {
        list(
          $oid,
          $code,
          $code_xds,
          ) = $line;
        $xml->appendLine($oid, $code, $code_xds);
      }
      $xml->save("modules/xds/resources/jeux_de_valeurs/$name.xml");
    }
    return true;
  }

  /**
   * Retourne une entrée pour un document
   *
   * @param String $name Nom du jeux de valeur
   * @param String $code Identifiant de la valeur voulut
   *
   * @return array
   */
  static function loadEntryDocument($name, $code) {
    $path = "modules/xds/resources/$name";
    $dom = new CMbXMLDocument();
    $dom->load($path);
    $xpath = new CMbXPath($dom);
    $node = $xpath->queryUniqueNode("//line[@id='$code']");
    $valeur = array("contenu" => $xpath->queryAttributNode("./mediaType", $node, "contenu"),
                    "formatCode" => $xpath->queryAttributNode("./xds", $node, "formatCode"),
                    "codingScheme" => $xpath->queryAttributNode("./xds", $node, "codingScheme"));

    return $valeur;
  }

  /**
   * Retourne une entrée dans un jeux de valeur
   *
   * @param String $name Nom du jeux de valeur
   * @param String $code Identifiant de la valeur voulut
   *
   * @return array
   */
  static function loadEntryJV($name, $code) {
    $path = "modules/xds/resources/jeux_de_valeurs/$name";
    $dom = new CMbXMLDocument();
    $dom->load($path);
    $xpath = new CMbXPath($dom);
    $node = $xpath->queryUniqueNode("//line[@id='$code']");
    $valeur = array("id" => $xpath->queryAttributNode(".", $node, "id"),
                    "oid" => $xpath->queryAttributNode(".", $node, "oid"),
                    "name" => $xpath->queryAttributNode(".", $node, "name"));

    return $valeur;
  }

  /**
   * Supprime la date du nom des fichiers des jeux de valeurs
   *
   * @param String $name Nom du fichier
   *
   * @return string
   */
  static function deleteDate($name) {
    return substr($name, 0, strrpos($name, "_"));
  }

  /**
   * Retourne le datetime actuelle au format UTC
   *
   * @param String $date now
   *
   * @return string
   */
  static function getTimeUtc($date = "now") {
    $timezone_local = new DateTimeZone(CAppUI::conf("timezone"));
    $timezone_utc = new DateTimeZone("UTC");
    $date = new DateTime($date, $timezone_local);
    $date->setTimezone($timezone_utc);
    return $date->format("YmdHis");
  }

  /**
   * Retourne les informations de l'etablissement sous la forme HL7v2 XON
   *
   * @return string
   */
  static function getXONetablissement() {
    $etablissement = CGroups::loadCurrent();
    $comp1  = $etablissement->text;
    $comp6  = "&1.2.250.1.71.4.2.2&ISO";
    $comp7  = "IDNST";
    $comp10 = self::getIdEtablissement(false, $etablissement);
    $xon = "$comp1^^^^^$comp6^$comp7^^^$comp10";
    return $xon;
  }

  /**
   * Retourne l'identifiant de l'établissement courant
   *
   * @param boolean $forPerson Identifiant concernant une personne
   *
   * @return null|string
   */
  static function getIdEtablissement($forPerson = false) {
    $siret = "3";
    $finess = "1";

    if ($forPerson) {
      $siret = "5";
      $finess = "3";
    }

    $etablissement = CGroups::loadCurrent();

    if ($etablissement->siret) {
      return $siret.$etablissement->siret;
    }

    if ($etablissement->finess) {
      return $finess.$etablissement->finess;
    }

    return null;
  }

  /**
   * Retourne les informations du Mediuser sous la forme HL7v2 XCN
   *
   * @return string
   */
  static function getXCNMediuser() {
    $mediuser = CMediusers::get();
    $comp1  = self::getIdEtablissement(true)."/$mediuser->_id";
    $comp2  = $mediuser->_p_last_name;
    $comp3  = $mediuser->_p_first_name;
    $comp9  = "&1.2.250.1.71.4.2.1&ISO";
    $comp10 = "D";
    $comp13 = "EI";

    return "$comp1^$comp2^$comp3^^^^^^$comp9^$comp10^^^$comp13";
  }
}