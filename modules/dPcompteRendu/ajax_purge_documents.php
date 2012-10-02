<?php /* $ */

/**
 *  @package Mediboard
 *  @subpackage dPcompteRendu
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$modele = new CCompteRendu;
$limit = CValue::get("limit", 100);

$where = array();
$where["object_id"] = "IS NULL";
$where["purgeable"] = "= '1'";

$modeles = $modele->loadList($where);

foreach ($modeles as $_modele) {
  $documents = $_modele->loadBackRefs("documents", null, $limit);
  foreach ($documents as $_doc) {
    $_doc->delete();
  }
}

?>