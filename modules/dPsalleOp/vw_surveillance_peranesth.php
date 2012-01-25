<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$operation_id = CValue::get("operation_id");

$interv = new COperation;
$interv->load($operation_id);
$interv->loadRefSejour()->loadRefPatient()->loadRefConstantesMedicales();
$interv->loadRefPlageOp();

$consult_anesth = $interv->loadRefsConsultAnesth();

list($results, $times) = CObservationResultSet::getResultsFor($interv);

$time_min = $interv->entree_salle;
$time_max = mbTime("+".mbMinutesRelative("00:00:00", $interv->temp_operation)." MINUTES", $interv->entree_salle);

$date = mbDate($interv->_datetime);
$time_debut_op = CMbDate::toUTCTimestamp("$date $time_min");
$time_fin_op   = CMbDate::toUTCTimestamp("$date $time_max");

$round_minutes = 10;
$round = $round_minutes * 60000;

$time_min = floor(CMbDate::toUTCTimestamp("$date $time_min") / $round) * $round;
$time_max =  ceil(CMbDate::toUTCTimestamp("$date $time_max") / $round) * $round;

$graph_object = new CSupervisionGraph;
$graph_objects = $graph_object->loadList(array(
  "disabled" => "= '0'",
));

$graphs = array();
foreach($graph_objects as $_go) {
  $graphs[] = $_go->buildGraph($results, $time_min, $time_max);;
}

$yaxes_count = 0;
foreach($graphs as $_graph) {
  $yaxes_count = max($yaxes_count, count($_graph["yaxes"]));
}

foreach($graphs as &$_graph) {
  if (count($_graph["yaxes"]) < $yaxes_count) {
    $_graph["yaxes"] = array_pad($_graph["yaxes"], $yaxes_count, CSupervisionGraphAxis::$default_yaxis);
  }
}

// ---------------------------------------------------
// Gestes, Medicaments, Perfusions peranesth
$gestes = array(
  "CAnesthPerop" => array(),
);

$interv->loadRefsAnesthPerops();

foreach($interv->_ref_anesth_perops as $_perop) {
  $_ts = CMbDate::toUTCTimestamp($_perop->datetime);
  
  $gestes["CAnesthPerop"][$_perop->_id] = array(
    "icon" => null,
    "label" => $_perop->libelle,
    "unit"  => null,
    "alert" => $_perop->incident,
    "datetime" => $_perop->datetime,
    "position" => 100 * ($_ts - $time_min) / ($time_max - $time_min),
    "object" => $_perop,
  );
}

$sejour = $interv->loadRefSejour();

$prescription = $sejour->loadRefPrescriptionSejour();

if($prescription->_id){
  $lines = $prescription->loadPeropLines(false);
  
  foreach($lines as $_guid => $_line_array) {
    $_line = $_line_array["object"];
    
    $_view = "";
    
    if ($_line instanceof CPrescriptionLineElement) {
      $_view = $_line->_view;
    }
    elseif ($_line instanceof CPrescriptionLineMix) {
      foreach ($_line->_ref_lines as $_mix_item) {
        $_view .= "$_mix_item->_ucd_view<br />";
      }
    }
    else {
      $_view = $_line->_ucd_view;
    }
    
    $key = "CPrescription._chapitres.$_line->_chapitre";
    if (!isset($gestes[$key])) {
      $gestes[$key] = array();
    }
    
    /*
    foreach($_line_array["planifications"] as $_planifs) {
      foreach($_planifs as $_planif) {
        if ($_planif->_ref_object instanceof CPrescriptionLineMixItem) {
          $quantite = $_planif->_ref_object->_quantite_administration;
        }
        else {
          $quantite = $_planif->_ref_prise->_quantite_administrable;
        }
        
        if ($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineMix) {
          $unite = $_planif->_ref_object->_ref_produit->libelle_unite_presentation;
        }
        else {
          $unite = $_line->_unite_prise;
        }

        $gestes[$_line->_class][] = array(
          "label" => "$quantite $unite",
          "alert" => false,
          "datetime" => $_planif->dateTime,
          "position" => 100 * (CMbDate::toUTCTimestamp($_planif->dateTime) - $time_min) / ($time_max - $time_min),
        );
      }
    }*/
   
    foreach ($_line_array["administrations"] as $_adms) {
      $_adms = CStoredObject::naturalSort($_adms, array("dateTime"));
      
      foreach ($_adms as $_adm) {
        $unite = "";
        if ($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineMix) {
          $unite = $_adm->_ref_object->_ref_produit->libelle_unite_presentation;
        }
        
        $gestes[$key][] = array(
          "icon"  => $_line->_chapitre,
          "label" => $_view,
          "unit"  => "$_adm->quantite $unite",
          "alert" => false,
          "datetime" => $_adm->dateTime,
          "position" => 100 * (CMbDate::toUTCTimestamp($_adm->dateTime) - $time_min) / ($time_max - $time_min),
          "object"   => $_line,
        );
      }
    }
  }
}

$now = 100 * (CMbDate::toUTCTimestamp(mbDateTime()) - $time_min) / ($time_max - $time_min);

CJSLoader::$files = array(
  "lib/flot/jquery.min.js",
  "lib/flot/jquery.flot.min.js",
  "lib/flot/jquery.flot.symbol.min.js",
  "lib/flot/jquery.flot.crosshair.min.js",
  "lib/flot/jquery.flot.resize.min.js",
);
echo CJSLoader::loadFiles();
CAppUI::JS('$.noConflict()');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("interv",      $interv);
$smarty->assign("graphs",      $graphs);
$smarty->assign("gestes",      $gestes);
$smarty->assign("time_debut_op", $time_debut_op);
$smarty->assign("time_fin_op",   $time_fin_op);
$smarty->assign("yaxes_count", $yaxes_count);
$smarty->assign("consult_anesth", $consult_anesth);
$smarty->assign("now", $now);

$smarty->display("vw_surveillance_peranesth.tpl");
