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

  static function generateXMLToJv() {
    $files = glob("modules/xds/resources/jeux_de_valeurs/*.jv");

    foreach ($files as $_file) {
      $name = self::deleteDate(basename($_file));
      $csv = new CXDSFileJv($_file);
      $csv->readLine();
      $csv->readLine();
      $csv->readLine();
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

  static function deleteDate($name) {
    return substr($name, 0, strrpos($name, "_"));
  }
}