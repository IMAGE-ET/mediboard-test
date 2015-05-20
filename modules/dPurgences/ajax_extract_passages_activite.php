<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

CApp::setMemoryLimit("512M");

$now = CValue::get("debut_selection", CMbDT::dateTime());

$date_tolerance = CAppUI::conf("dPurgences date_tolerance");
$date_before    = CMbDT::date("-$date_tolerance DAY", $now);
$date_after     = CMbDT::date("+1 DAY", $now);
$group          = CGroups::loadCurrent();

$extractPassages = new CExtractPassages();
$extractPassages->debut_selection = $now;
$extractPassages->fin_selection   = $now;
$extractPassages->type            = "activite";
$extractPassages->group_id        = $group->_id;
$extractPassages->date_extract    = $now;

if (!$max_patient = CAppUI::conf("dPurgences send_RPU max_patient", $group)) {
  $max_patient = CAppUI::conf("cerveau max_patient");
}

$datas = array(
  "PRESENTS"    => 0,
  "ATTENTE"     => 0,
  "AVAL"        => 0,
  "BOX"         => 0,
  "DECHOC"      => 0,
  "PORTE"       => 0,
  "RADIO"       => 0,
  "MAXPATIENTS" => $max_patient ? $max_patient : 0,
  "TOTBOX"      => 0,
  "TOTDECHOC"   => 0,
  "TOTPORTE"    => 0
);

// Chargement des rpu de la main courante
$sejour = new CSejour;
$where = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";

$where[] = "sejour.entree BETWEEN '$now' AND '$date_after'
  OR (sejour.sortie_reelle IS NULL AND sejour.entree BETWEEN '$date_before' AND '$date_after' AND sejour.annule = '0')";

// RPUs
$where["rpu.rpu_id"]      = "IS NOT NULL";
$where["sejour.group_id"] = "= '$group->_id'";
//$where["sejour.type"]     = "= 'urg'";
$order                    = "sejour.entree ASC";

/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, $order, null, "sejour.sejour_id", $ljoin);

$count_sejour = 0;
//work
foreach ($sejours as $_sejour) {
  // Dans le cas où le patient est déjà sorti
  if ($_sejour->sortie_reelle) {
    continue;
  }

  $comptabilise = false;

  $affectation = $_sejour->getCurrAffectation($now);
  $service     = $affectation->loadRefService();

  $rpu = $_sejour->loadRefRPU();

  // Dans le cas d'une mutation utation
  if ($rpu->mutation_sejour_id) {
    // Il est déjà muté dans un service
    if ($service->_id && (!$service->urgence || !$service->uhcd)) {
      continue;
    }

    $datas["AVAL"]++;
    $comptabilise = true;
  }

  $count_sejour++;

  // Présents
  $datas['PRESENTS']++;

  // Placé : salle d'attente, box d'examen ou salle dechoc
  if ($affectation->_id) {
    $lit         = $affectation->loadRefLit();
    $chambre     = $lit->loadRefChambre();

    if ($chambre->_id) {
      // Salle d'attente
      if ($chambre->is_waiting_room) {
        $datas["ATTENTE"]++;
        $comptabilise = true;
      }

      // Salle d'examen
      if ($chambre->is_examination_room) {
        $datas["BOX"]++;
        $comptabilise = true;
      }

      if ($chambre->is_sas_dechoc) {
        $datas["DECHOC"]++;
        $comptabilise = true;
      }
    }
  }

  // Service lit-porte
  if ($_sejour->UHCD) {
    $datas["PORTE"]++;
    $comptabilise = true;
  }

  // Plateaux techniques
  if ($rpu->radio_debut && !$rpu->radio_fin) {
    $datas['RADIO']++;
    $comptabilise = true;
  }

  if (!$comptabilise) {
    $consult = $rpu->loadRefConsult();

    // Si on a pas de consult on considère qu'il est en salle d'attente
    if (!$consult || !$consult->_id) {
      $datas["ATTENTE"]++;
    }

    // S'il y a une consult et pas placé, il est au moins dans un box ?
    // $datas["BOX"]++;
  }
}

//totaux

$lit = new CLit();
$where = array();
$ljoin = array();
$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
$ljoin["service"] = "service.service_id = chambre.service_id";
$where["service.externe"] = "= '0'";
$where["service.uhcd"] = "= '1'";
$where["service.group_id"] = " = '$group->_id'";
$where["lit.annule"] = " = '0'";

//uhcd
$totporte = CAppUI::conf("dPurgences send_RPU totporte", $group);
$datas["TOTPORTE"] = $totporte ? $totporte : $lit->countList($where, null, $ljoin);

//urgences
$where["service.uhcd"] = " IS NOT NULL";
$where["service.urgence"] = "= '1'";
$where["chambre.is_examination_room"] = " = '1'";

$totbox = CAppUI::conf("dPurgences send_RPU totbox", $group);
$datas["TOTBOX"] = $totbox ? $totbox : $lit->countList($where, null, $ljoin);

$where["chambre.is_examination_room"] = " IS NOT NULL";
$where["chambre.is_sas_dechoc"] = " = '1'";

$totdechoc = CAppUI::conf("dPurgences send_RPU totdechoc", $group);
$datas["TOTDECHOC"] = $totdechoc ? $totdechoc : $lit->countList($where, null, $ljoin);

// Appel de la fonction d'extraction du RPUSender
$rpuSender = $extractPassages->getRPUSender();
if (!$rpuSender) {
  CAppUI::stepAjax("Aucun sender définit dans le module dPurgences.", UI_MSG_ERROR);
}
$extractPassages = $rpuSender->extractActivite($extractPassages, $datas);

CAppUI::stepAjax("Extraction de $count_sejour séjours du $now terminée.", UI_MSG_OK);
if (!$extractPassages->message_valide) {
  CAppUI::stepAjax("Le document produit n'est pas valide.", UI_MSG_WARNING);
}
else {
  CAppUI::stepAjax("Le document produit est valide.", UI_MSG_OK);
}

echo "<script>RPU_Sender.extract_passages_id = $extractPassages->_id;</script>";

