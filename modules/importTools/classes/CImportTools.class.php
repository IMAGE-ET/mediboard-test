<?php

/**
 * $Id$
 *  
 * @category ImportTools
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Import tools class
 */
class CImportTools  {
  static function getColumnsInfo(CSQLDataSource $ds, $table) {
    $columns = $ds->loadHashAssoc("SHOW COLUMNS FROM `{$table}`");
    $xpath = null;

    $description = self::getDescription($ds->dsn);
    if ($description) {
      $xpath = new DOMXPath($description);
    }

    foreach ($columns as $_name => &$_column) {
      $_column["datatype"] = $_column["Type"]." ".($_column["Null"] == "YES" ? "NULL" : "NOT NULL");
      $_column["is_text"] = preg_match('/(char|text)/', $_column["Type"]);
      $_column["foreign_key"] = null;

      if ($xpath) {
        /** @var DOMElement $_column_element */
        $_column_element = $xpath->query("//tables/table[@name='$table']/column[@name='$_name']")->item(0);

        if ($_column_element) {
          $_column["foreign_key"] = $_column_element->getAttribute("foreign_key");
        }
      }
    }

    return $columns;
  }

  static function getTableInfo(CSQLDataSource $ds, $table) {
    $xpath = null;
    mbTrace("table");

    $description = self::getDescription($ds->dsn);
    if ($description) {
      $xpath = new DOMXPath($description);
    }

    /** @var DOMElement $element */
    $element = $xpath->query("//tables/table[@name='$table']")->item(0);

    $info = array(
      "name"    => $table,
      "title"   => ($element ? utf8_decode($element->getAttribute("title")) : null),
      "display" => ($element ? ($element->getAttribute("display") != "no") : true),
      "columns" => self::getColumnsInfo($ds, $table, $description),
    );

    return $info;
  }

  static function getDescription($dsn) {
    $databases = self::getAllDatabaseInfo();
    $info = null;

    foreach ($databases as $_dsn => $_info) {
      if ($_dsn == $dsn) {
        $info = $_info;
        break;
      }
    }

    $description = null;
    if ($info) {
      $description = new DOMDocument();
      $description->load($info["description_file"]);
    }

    return $description;
  }

  static function getAllDatabaseInfo() {
    static $databases = null;

    if ($databases !== null) {
      return $databases;
    }

    $db_meta_files = glob(__DIR__."/../../*/db_meta.php");

    $databases = array();
    foreach ($db_meta_files as $_file) {
      $dbs = include_once $_file;

      foreach ($dbs as $_dsn => $_info) {
        $databases[$_dsn] = $_info;
      }
    }

    return $databases;
  }
}
