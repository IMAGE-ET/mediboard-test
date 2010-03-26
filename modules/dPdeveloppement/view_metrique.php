<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $can;
$can->needsRead();

$ds = CSQLDataSource::get("std");

$listeClasses = getInstalledClasses();

$result = array();
foreach ($listeClasses as $class){
  $object = new $class;
  if ($object->_spec->measureable) {
	  $sql = "SHOW TABLE STATUS LIKE '{$object->_spec->table}'";
	  $statusTable = $ds->loadList($sql);
	  if ($statusTable) {
	    $result[$class] = $statusTable[0];
	    $result[$class]["Update_relative"] = CMbDate::relative($result[$class]["Update_time"]);
	  }
	}
}

// Pour l'établissement courant
$etab = new CGroups;
$nb_etabs = $etab->countList();

if($nb_etabs > 1) {
	$etab = CGroups::loadCurrent();
	$current_group = $etab->_id;
	$res_current_etab = array();
	$where = array();
	$ljoin= array();
	
	// - Nombre de séjours
	$sejour = new CSejour;
	$tag_dossier = CAppUI::conf("dPplanningOp CSejour tag_dossier");
	$tag_dossier = str_replace('$g', $current_group, $tag_dossier);
	$where["tag"] = " = '$tag_dossier'";
	$where["object_class"] = " = 'CSejour'";
	$id400 = new CIdSante400;
	$res_current_etab["CSejour-_num_dossier"] = $id400->countList($where);
	
	// - Patients IPP
	$where = array();
	$tag_ipp = CAppUI::conf("dPpatients CPatient tag_ipp");
	$tag_ipp = str_replace('$g', $current_group, $tag_ipp);
	$where["tag"] = " = '$tag_ipp'";
	$where["object_class"] = " = 'CPatient'";
	$id400 = new CIdSante400;
	$res_current_etab["CPatient-_IPP"] = $id400->countList($where);
	
	// - Nombre de consultations
	$where = array();
	$consultation = new CConsultation;
	$ljoin["plageconsult"]        = "consultation.plageconsult_id = plageconsult.plageconsult_id";
	$ljoin["users_mediboard"]     = "plageconsult.chir_id = users_mediboard.user_id";
	$ljoin["functions_mediboard"] = "users_mediboard.function_id = functions_mediboard.function_id";
	$where["functions_mediboard.group_id"] = " = $current_group";
	$res_current_etab["CConsultation"] = $consultation->countList($where, null, null, null, $ljoin);
	
	// - Lits
	$ljoin = array();
	$where = array();
	$lit = new CLit;
	$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
	$ljoin["service"] = "chambre.service_id = service.service_id";
	$where["service.group_id"] = " = $current_group";
	$res_current_etab["CLit"] = $lit->countList($where, null, null, null, $ljoin);
	
	// - Chambres
	$ljoin = array();
	$where = array();
	$chambre = new CChambre;
	$ljoin["service"] = "chambre.service_id = service.service_id";
	$where["service.group_id"] = " = $current_group";
	$res_current_etab["CChambre"] = $chambre->countList($where, null, null, null, $ljoin);
	
	// - Utilisateurs
	$ljoin = array();
	$where = array();
	$mediuser = new CMediusers;
	$ljoin["functions_mediboard"]   = "users_mediboard.function_id = functions_mediboard.function_id";
	$where["functions_mediboard.group_id"] = " = $current_group";
	$res_current_etab["CMediusers"] = $mediuser->countList($where, null, null, null, $ljoin);
	 
	// - Entrées de journal
	$ljoin = array();
	$where = array();
	$user_log = new CUserLog;
	$ljoin["users_mediboard"] = "user_log.user_id = users_mediboard.user_id";
	$ljoin["functions_mediboard"] = "users_mediboard.function_id = functions_mediboard.function_id";
	$where["functions_mediboard.group_id"] = " = $current_group";
	$res_current_etab["CUserLog"] = $user_log->countList($where, null, null, null, $ljoin);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("result" , $result);
$smarty->assign("etab", $etab);
if ($nb_etabs > 1) {
  $smarty->assign("res_current_etab", $res_current_etab);
}
$smarty->assign("nb_etabs", $nb_etabs);
$smarty->display("view_metrique.tpl");

?>