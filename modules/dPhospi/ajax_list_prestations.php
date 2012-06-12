<?php /* $Id: ajax_list_prestations.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prestation_guid = CValue::getOrSession("prestation_guid");

$where = array();
$where["group_id"] = " = '" . CGroups::loadCurrent()->_id . "'";

$prestation_journaliere = new CPrestationJournaliere;
$prestation_ponctuelle = new CPrestationPonctuelle;

$prestations[$prestation_journaliere->_class] = $prestation_journaliere->loadList($where);
$prestations[$prestation_ponctuelle->_class ] = $prestation_ponctuelle->loadList($where);

foreach ($prestations as $_prestations_by_class) {
  foreach ($_prestations_by_class as $_prestation) {
    $_prestation->_count_items = $_prestation->countBackRefs("items");
  }
}

$smarty = new CSmartyDP;

$smarty->assign("prestations", $prestations);
$smarty->assign("prestation_guid", $prestation_guid);

$smarty->display("inc_list_prestations.tpl");

?>