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
  /**
   * Get columns information from a table
   *
   * @param CSQLDataSource $ds    Datasource object
   * @param string         $table Table name
   *
   * @return array
   */
  static function getColumnsInfo(CSQLDataSource $ds, $table) {
    $columns = $ds->loadHashAssoc("SHOW COLUMNS FROM `{$table}`");
    $xpath = null;

    $description = self::getDescription($ds->dsn);
    $xpath = $description->_xpath;

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

  /**
   * Get table information
   *
   * @param CSQLDataSource $ds    Datasource object
   * @param string         $table Table name
   *
   * @return array
   */
  static function getTableInfo(CSQLDataSource $ds, $table) {
    $xpath = null;

    $description = self::getDescription($ds->dsn);
    $xpath = $description->_xpath;

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

  /**
   * Get full database structure
   *
   * @param string $dsn   Datasource name
   * @param bool   $count Count each table entries
   *
   * @return mixed
   * @throws Exception
   */
  static function getDatabaseStructure($dsn, $count = false) {
    $databases = CImportTools::getAllDatabaseInfo();

    if (!isset($databases[$dsn])) {
      throw new Exception("DSN not found : $dsn");
    }

    $db_info = $databases[$dsn];

    $ds = CSQLDataSource::get($dsn);

    // Description file
    $description = new DOMDocument();
    $description->load($db_info["description_file"]);
    $description->_xpath = new DOMXPath($description);

    $db_info["description"] = $description;

    // Tables
    $table_names = $ds->loadTables();
    $tables = array();
    foreach ($table_names as $_table_name) {
      $_table_info = CImportTools::getTableInfo($ds, $_table_name);

      if ($count) {
        $_table_info["count"] = $ds->loadResult("SELECT COUNT(*) FROM $_table_name");
      }

      $tables[$_table_name] = $_table_info;
    }

    $db_info["tables"] = $tables;

    return $db_info;
  }

  /**
   * Get a database description DOM document
   *
   * @param string $dsn Datasource name
   *
   * @return DOMDocument|null
   */
  static function getDescription($dsn) {
    static $cache = array();

    if (isset($cache[$dsn])) {
      return $cache[$dsn];
    }

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
      $description->_xpath = new DOMXPath($description);
    }

    return $cache[$dsn] = $description;
  }

  /**
   * Load all databases basic information
   *
   * @return array
   */
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
