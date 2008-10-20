<?php /* $Id: print_compta.php 2321 2007-07-19 08:14:45Z alexis_granger $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 2321 $
* @author Thomas Despoix 
*/

global $can;

$can->needsEdit();

$fse = new CLmFSE();
$ds =& $fse->_spec->ds;

$query = "
	SELECT 
		COUNT( * ) AS nb_fse, 
		S_CPS_NUMERO AS numero , 
		S_CPS_ID_NAT_NUMERO AS adeli,
		S_CPS_NOM AS nom,
		S_CPS_PRENOM  AS prenom,
		MIN( S_FSE_DATE_FSE ) AS date_min, 
		MAX( S_FSE_DATE_FSE ) AS date_max
	FROM `S_F_FSE`
	LEFT JOIN S_F_CPS ON S_F_CPS.S_CPS_NUMERO = S_F_FSE.S_FSE_CPS
	GROUP BY `S_FSE_CPS`
	ORDER BY date_min";
$cpss = $ds->loadList($query);

$functions = array();
foreach ($cpss as &$cps) {
  $cps["jours"] = mbDaysRelative($cps["date_min"], $cps["date_max"]) + 1;
  $cps["actif"] = mbDaysRelative($cps["date_max"], mbDate()) <= 60;
  // Praticien associé
  $cps["prat"] = null;  
  $prat = new CMediusers();
  $prat->loadFromIdCPS($cps["numero"]);
  if (!$prat->_id) {
    break;
  }

  // Chargement de la fonction est classement
  $cps["prat"] = $prat;  
  if (!array_key_exists($prat->function_id, $functions)) {
    $function = new CFunctions();
    $function->load($prat->function_id);
    $functions[$function->_id] = $function;
    $function->_count_total = 0;
    $function->_count_active = 0;
  }
  
  // Totaux activité
  $func = $functions[$function->_id];
  $func->_count_total++;
  
  if ($cps["actif"]) {
    $func->_count_active++;
  }
  
  $prat->_ref_function = $func;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("cpss", $cpss);
$smarty->assign("functions", $functions);

$smarty->display("print_bilan_cps.tpl");

?>