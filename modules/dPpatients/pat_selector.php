<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$name      = mbGetValueFromGet("name"     , "");
$firstName = mbGetValueFromGet("firstName", "");

$where        = array();
$whereSoundex = array();
$soundexObj   = new soundex2();

if($name != "" || $firstName != "") {
  $where["nom"]                    = "LIKE '$name%'";
  $where["prenom"]                 = "LIKE '$firstName%'";
  $whereSoundex["nom_soundex2"]    = "LIKE '".$soundexObj->build($name)."%'";
  $whereSoundex["prenom_soundex2"] = "LIKE '".$soundexObj->build($firstName)."%'";
} else {
  $where[]        = "0";
  $whereSoundex[] = "0";
}
$limit = "0, 100";
$order = "patients.nom, patients.prenom";

if (!function_exists('array_diff_key')) {
  function array_diff_key() {
    $argCount  = func_num_args();
    $argValues  = func_get_args();
    $valuesDiff = array();
    if ($argCount < 2) return false;
    foreach ($argValues as $argParam) {
      if (!is_array($argParam)) return false;
    }
    foreach ($argValues[0] as $valueKey => $valueData) {
      for ($i = 1; $i < $argCount; $i++) {
        if (isset($argValues[$i][$valueKey])) continue 2;
      }
      $valuesDiff[$valueKey] = $valueData;
    }
    return $valuesDiff;
  }
}

$pat             = new CPatient();
$patients        = array();
$patientsSoundex = array();

$patients = $pat->loadList($where, $order, $limit);
if($nbExact = (100 - count($patients))) {
  $limit = "0, $nbExact";
  $patientsSoundex = $pat->loadList($whereSoundex, $order, $limit);
  $patientsSoundex = array_diff_key($patientsSoundex, $patients);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("name"            , $name            );
$smarty->assign("firstName"       , $firstName       );
$smarty->assign("patients"        , $patients        );
$smarty->assign("patientsSoundex" , $patientsSoundex );

$smarty->display("pat_selector.tpl");

?>