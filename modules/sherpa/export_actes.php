<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsRead();

// Filter sur les dossiers
$filter = new CSejour();
$filter->_num_dossier = mbGetValueFromGet("_num_dossier");
$filter->_date_sortie = !$filter->_num_dossier ? mbGetValueFromGet("_date_sortie", mbDate()) : null;

// Chargement des séjours concernés
$sejour = new CSejour();
$sejours = array();
if ($do = mbGetValueFromGet("do")) {
	if ($filter->_num_dossier) {
	  $sejour->loadFromNumDossier($filter->_num_dossier);
	  if ($sejour->_id) {
	    $sejours[$sejour->_id] = $sejour;
	  }
	}
	else {
		$where = array();
		$where["type"] = "NOT IN ('exte')";
		$where["sortie_reelle"] = "LIKE '$filter->_date_sortie%'";
	  $order = "entree_reelle, sortie_reelle";
	  $sejours = $sejour->loadGroupList($where, $order);
	}
}

foreach ($sejours as &$sejour) {
  CSpActesExporter::exportSejour($sejour);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("do", $do);
$smarty->assign("filter", $filter);
$smarty->assign("acte_ccam", new CActeCCAM());
$smarty->assign("sejours", $sejours);
$smarty->assign("delDetCIM" , CSpActesExporter::$delDetCIM );
$smarty->assign("delActNGAP", CSpActesExporter::$delActNGAP);
$smarty->assign("delDetCCAM", CSpActesExporter::$delDetCCAM);
$smarty->assign("delEntCCAM", CSpActesExporter::$delEntCCAM);
$smarty->assign("detCIM" , CSpActesExporter::$detCIM);
$smarty->assign("detCCAM", CSpActesExporter::$detCCAM);
$smarty->assign("actNGAP", CSpActesExporter::$actNGAP);
$smarty->assign("entCCAM", CSpActesExporter::$entCCAM);

$smarty->display("export_actes.tpl");
?>