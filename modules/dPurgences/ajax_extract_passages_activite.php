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

$now = CValue::get("datetime", CMbDT::dateTime());
$group_id = CGroups::loadCurrent()->_id;

$datas = array(
  "extract"     => CMbDT::dateToLocale($now),
  "csite"       => '',  //todo
  "nsite"       => '',  //todo
  "presents"    => 0,
  "attente"     => 0,
  "aval"        => 0,
  "box"         => 0,
  "dechoc"      => 0,
  "porte"       => 0,
  "radio"       => 0,
  "maxpatients" => 0,   //todo
  "totbox"      => 0,
  "totdechoc"   => 0,
  "totporte"    => 0
);

// Chargement des rpu de la main courante
$sejour = new CSejour;
$where = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
$where[] = "sejour.entree <= '$now' AND sejour.sortie_reelle IS NULL";
// RPUs
$where[] = "rpu.rpu_id IS NOT NULL";
$where["sejour.group_id"] = "= '$group_id'";
$order = "sejour.entree ASC";
/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

//work
foreach ($sejours as $_sejour) {
  $affectation = $_sejour->getCurrAffectation($now);

  //total
  $datas['presents']++;

  //placé
  if ($affectation->_id) {
    $lit = $affectation->loadRefLit();
    $chambre = $lit->loadRefChambre();
    if ($chambre->_id) {
      // salle d'attente
      if ($chambre->is_waiting_room) {
        $datas["attente"]++;
      }

      //salle d'examen
      if ($chambre->is_examination_room) {
        $datas["box"]++;
      }

      if ($chambre->is_sas_dechoc) {
        $datas["dechoc"]++;
      }
    }
  }
  $rpu = $_sejour->loadRefRPU();

  //mutation
  if ($rpu->mutation_sejour_id) {
    $datas["aval"]++;
  }

  //porte
  if ($_sejour->UHCD) {
    $datas["porte"]++;
  }

  //radio
  if ($rpu->radio_debut && !$rpu->radio_fin) {
    $datas['radio']++;
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
$where["service.group_id"] = " = '$group_id'";
$where["lit.annule"] = " = '0'";

//uhcd
$datas["totporte"] = $lit->countList($where, null, $ljoin);

//urgences
$where["service.uhcd"] = " IS NOT NULL";
$where["service.urgence"] = "= '1'";

$where["chambre.is_examination_room"] = " = '1'";
$datas["totbox"] = $lit->countList($where, null, $ljoin);

$where["chambre.is_examination_room"] = " IS NOT NULL";
$where["chambre.is_sas_dechoc"] = " = '1'";
$datas["totdechoc"] = $lit->countList($where, null, $ljoin);

//
return $datas;