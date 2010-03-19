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
$current_group = CGroups::loadCurrent()->_id;
$res_current_etab = array();
$where = array();
$ljoin= array();

// - Nombre de séjours
$sejour = new CSejour;
$where["sejour.group_id"] = " = $current_group";
$res_current_etab["CSejour"] = $sejour->countList($where);


// - Nombre de consultations
$where = array();
$consultation = new CConsultation;
$ljoin["plageconsult"]        = "consultation.plageconsult_id = plageconsult.plageconsult_id";
$ljoin["users_mediboard"]     = "plageconsult.chir_id = users_mediboard.user_id";
$ljoin["functions_mediboard"] = "users_mediboard.function_id = functions_mediboard.function_id";
$where["functions_mediboard.group_id"] = " = $current_group";
$res_current_etab["CConsultation"] = $consultation->countList($where, null, null, null, $ljoin);

// - Entrées de journal
$ljoin = array();
$where = array();
$user_log = new CUserLog;
$ljoin["users_mediboard"] = "user_log.user_id = users_mediboard.user_id";
$ljoin["functions_mediboard"] = "users_mediboard.function_id = functions_mediboard.function_id";
$where["functions_mediboard.group_id"] = " = $current_group";
$res_current_etab["CUserLog"] = $user_log->countList($where, null, null, null, $ljoin);

// - Utilisateurs
$ljoin = array();
$where = array();
$mediuser = new CMediusers;
$ljoin["functions_mediboard"]   = "users_mediboard.function_id = functions_mediboard.function_id";
$where["functions_mediboard.group_id"] = " = $current_group";
$res_current_etab["CMediusers"] = $mediuser->countList($where, null, null, null, $ljoin);
 
// - Patients IPP
$where = array();
$tag_ipp = CAppUI::conf("dPpatients CPatient tag_ipp");
str_replace('$g', $current_group, $tag_ipp);
$where["tag"] = " = '$tag_ipp'";
$id400 = new CIdSante400;
$res_current_etab["CIdSante400"] = $id400->countList($where);

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

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("result" , $result);
$smarty->assign("res_current_etab", $res_current_etab);
$smarty->display("view_metrique.tpl");

?>