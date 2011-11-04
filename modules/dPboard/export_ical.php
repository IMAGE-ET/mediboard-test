<?php /* $Id:export_ical.php  $ */

/**
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision: 13328 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */ 

CCanDo::checkRead();

// Récupération des paramètres
$prat_id      = CValue::get("prat_id");
$details      = CValue::get("details");
$export       = CValue::get("export");

$weeks_before = CValue::get("weeks_before");
$weeks_after  = CValue::get("weeks_after");

$date         = CValue::get("date", mbDate());
$debut        = mbDate("-$weeks_before week", $date);
$debut        = mbDate("last sunday", $debut);
$fin          = mbDate("+$weeks_after week", $date);
$fin          = mbDate("next sunday", $fin);

//Création du calendrier
$v = new CMbCalendar();

//Chargement de toutes les plages de consultations et interventions pour un praticien donné

// Liste des Salles
$listSalles = new CSalle();
$listSalles = $listSalles->loadGroupList();

// Plages de Consultations
$plageConsult   = new CPlageconsult();
$plageOp        = new CPlageOp();
$listDays       = array();
$plagesConsult  = array();
$plagesOp       = array();
$plagesPerDayOp = array();

for($i = 0; mbDate("+$i day", $debut)!=$fin ; $i++) {
  $where = array();
  $where["chir_id"] = "= '$prat_id'";
  $date             = mbDate("+$i day", $debut);
  $where["date"]    = "= '$date'";
  
  $plagesPerDayConsult = $plageConsult->loadList($where);
  $nb_oper        = 0;
  $where          = array();
  $where[]        = "chir_id = '$prat_id' OR anesth_id = '$prat_id'";
  $where["date"]  = "= '$date'";
  foreach($listSalles as $keySalle=>$salle){
    $where["salle_id"] = "= '$keySalle'";
    $plagesPerDayOp[$keySalle] = $plageOp->loadList($where);
    $nb_oper = $nb_oper + count($plagesPerDayOp[$keySalle]);
  }
  foreach($plagesPerDayConsult as $value) {
      $value->countPatients();
    }
  
  if(($export[0]=="consult" || $export[1]=="consult") && count($plagesPerDayConsult)){
    foreach($plagesPerDayConsult as $value) {
      $value->loadFillRate();
      if($details==1){$value->loadRefsConsultations();}      
    }
    $plagesConsult[$date] = $plagesPerDayConsult;
  }
  if (($export[0]=="interv" || $export[1]=="interv") && $nb_oper){
    foreach($plagesPerDayOp as $key => $valuePlages) {
      if(!count($plagesPerDayOp[$key])){
        unset($plagesPerDayOp[$key]);
      }else{
        foreach($valuePlages as $keyPlage=>$value){
          $value->loadRefSalle();
          $value->_ref_salle->loadRefBloc();
          $value->_ref_salle->_ref_bloc->loadRefGroup();
          if($details==1){$value->loadRefsOperations();}
          $value->getNbOperations();
        }
        $plagesOp[$key][$date] = $plagesPerDayOp[$key];
      }
    }
  }
}

$re = "/(\d+)\-(\d+)\-(\d+)/i";
// Création des évènements plages de consultation
if($export[0]=="consult" || $export[1]=="consult"){
	foreach($plagesConsult as $curr_day => $plagesPerDay){
	  foreach($plagesPerDay as $rdv){
	  	preg_match($re, $rdv->date, $matches);
      $noms="";
      //évènement détaillé
     	if($details==1 && $rdv->_nb_patients>0){
     	  $noms.=" :\n";
	     	foreach($rdv->_ref_consultations as $consult){
	     	  $consult->loadRefPatient();
	     		$noms.=substr($consult->heure, 0, 2)."h".substr($consult->heure, 3, 3)." ".$consult->_ref_patient."\n";
     		}
     	}
      $v->addevent("", "Consultation - ".$rdv->libelle, $rdv->_nb_patients." patient(s)".$noms, "no comment",$rdv->_guid,
                  $matches[1],$matches[2],$matches[3], $rdv->_hour_deb, $rdv->_min_deb,
                  $matches[1],$matches[2],$matches[3], $rdv->_hour_fin, $rdv->_min_fin);
	  }
	}
}
// Création des évènements plages d'interventions
if ($export[0]=="interv" || $export[1]=="interv"){
	foreach($plagesOp as $keySalle=>$salle){
	  foreach($plagesOp[$keySalle] as $curr_day => $plagesPerDay){
	    foreach($plagesPerDay as $rdv){
        preg_match($re, $rdv->date, $matches);
	      $noms="";
	      //évènement détaillé
        if($details==1 && $rdv->_nb_operations>0){
        	$noms.=" : \n";
          foreach($rdv->_ref_operations as $op){
            $op->loadComplete();
            $noms.=$op->_ref_patient." Duree: ".substr($op->temp_operation, 0, 2)."h".substr($op->temp_operation, 3, 2)." Horaire :".$op->horaire_voulu."\n";
          }
        }
        $v->addevent($rdv->_ref_salle->_ref_bloc->_ref_group->_view, $rdv->_ref_salle->_view, "$rdv->_nb_operations intervention(s)".$noms, "no comment",$rdv->_guid,
                    $matches[1],$matches[2],$matches[3], $rdv->_heuredeb, $rdv->_minutedeb,
                    $matches[1],$matches[2],$matches[3], $rdv->_heurefin, $rdv->_minutefin);          
	    }
	  }
	}
}
//Conversion du calendrier en champ texte
$str = $v->createCalendar();

header("Content-disposition: attachment; filename=agenda.ics"); 
header("Content-Type: text/calendar; charset=".CApp::$encoding);
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header("Cache-Control: post-check=0, pre-check=0", false );
header("Content-Length: ".strlen($str));
echo $str;
?>