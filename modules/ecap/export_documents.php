<?php

/**
* @package Mediboard
* @subpackage ecap
* @version $Revision: 2165 $
* @author Thomas Despoix
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
  CEcDocsExporter::exportSejour($sejour);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("do", $do);
$smarty->assign("filter", $filter);
$smarty->assign("sejours", $sejours);

$smarty->display("export_documents.tpl");
?>