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

CCanDo::checkAdmin();

$db_meta_files = glob(__DIR__."/../*/db_meta.php");

$databases = array();
foreach ($db_meta_files as $_file) {
  $dbs = include_once $_file;

  foreach ($dbs as $_dsn => $_info) {
    $ds = CSQLDataSource::get($_dsn);

    // DS
    $_info["ds"] = $ds;

    // Description file
    $description = new DOMDocument();
    $description->load($_info["description_file"]);
    $xpath = new DOMXPath($description);
    $_info["description"] = $description;

    // Tables
    $table_names = $ds->loadTables();
    $tables = array();
    foreach ($table_names as $_table_name) {
      /** @var DOMElement $element */
      $element = $xpath->query("//tables/table[@name='$_table_name']")->item(0);

      $tables[] = array(
        "name"    => $_table_name,
        "title"   => ($element ? utf8_decode($element->getAttribute("title")) : null),
        "display" => ($element ? ($element->getAttribute("display") != "no") : true),
        "count"   => $ds->loadResult("SELECT COUNT(*) FROM $_table_name"),
      );
    }
    $_info["tables"] = $tables;

    $databases[$_dsn] = $_info;
  }
}

$smarty = new CSmartyDP();
$smarty->assign("databases", $databases);
$smarty->display("vw_database_explorer.tpl");
