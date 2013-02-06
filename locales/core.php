<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage locales
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$root_dir = CAppUI::conf("root_dir");
$locale = CAppUI::pref("LOCALE", "fr");
$shared_name = "locales-$locale";

// Load from shared memory if possible
if (null == $locales = SHM::get($shared_name)) {
  foreach (CAppUI::getLocaleFilesPaths($locale) as $_path) {
    include_once $_path;
  }
  $locales = array_filter($locales, "stringNotEmpty");
  foreach ($locales as &$_locale) {
    $_locale = CMbString::unslash($_locale);
  }

  //overwrite locales by Database
  $locale_db = new CTranslationOverwrite();
  $ds = CSQLDataSource::get("std");

  $where = array();
  $where["language"] = "= '$locale'";

  $query = new CRequest();
  $query->addSelect("source, translation");
  $query->addTable("translation");
  $query->addWhere($where);
  $locales2 = $ds->loadList($query->getRequest());

  foreach ($locales2 as &$_locales2) {
    $locales[$_locales2["source"]] = $_locales2["translation"];
  }

  SHM::put($shared_name, $locales);
}




// Encoding definition
require "$root_dir/locales/$locale/meta.php";
