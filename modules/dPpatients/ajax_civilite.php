<?php /* $Id: httpreq_do_add_insee.php 2342 2007-07-19 14:24:59Z mytto $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2342 $
* @author SARL OpenXtrem
*/

global $can;
$can->needsAdmin();

$patient = new CPatient;

$fields = array("civilite", "assure_civilite");

foreach ($fields as $_field) {
  switch ($mode = mbGetValueFromGet("mode")) {
    case "check":
    $ds = $patient->_spec->ds;
    $query = "SELECT `$_field`, COUNT( * ) AS  `counts`
      FROM `patients`
      GROUP BY `$_field`";
    foreach ($ds->loadHashList($query) as $value => $count) {
      $msgType = $value ? UI_MSG_OK : UI_MSG_WARNING;
      CAppUI::stepAjax("Nombre d'occurences pour '%s' valant '%s' : '%s'", 
       $msgType,
       CAppUI::tr("CPatient-$_field-desc"),
       CAppUI::tr("CPatient.$_field.$value"),
       $count);
    };
    break;
    
    case "repair" :
	  $where = array();
    $where["$_field"] = "IS NULL";
    $repaired = 0;
    foreach($patient->loadList($where) as $_patient) {
      $_patient->$_field = "guess";
      if ($msg = $_patient->store()) {
        CAppUI::stepAjax("Echec de la correction de %s pour le patient '%s' : %s", 
         UI_MSG_ERROR, 
         CAppUI::tr("CPatient-$_field-desc"),
         $_patient, 
         $msg);
        continue;
      }
      $repaired++;
    } 
    CAppUI::stepAjax("%s patients corrigés", UI_MSG_OK, $repaired);
    break;
      
    default:
    CAppUI::stepAjax("Mode '$mode' non pris en charge", UI_MSG_ERROR);
    break;
  }
}

?>