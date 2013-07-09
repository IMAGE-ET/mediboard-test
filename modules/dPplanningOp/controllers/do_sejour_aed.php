<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

if ($praticien_id = CValue::post("praticien_id")) {
  CValue::setSession("praticien_id", $praticien_id);
}

//Pour un séjour ayant comme mode de sortie urgence:
if (CValue::post("mode_sortie") == "mutation" && CValue::post("type") == "urg" && CValue::post("lit_id")) {
  $sejour_id = CValue::post("sejour_id");
  $lit_id = CValue::post("lit_id");
  $sejour = new CSejour();
  $sejour->load($sejour_id);

  //Création de l'affectation du patient
  $affectation = new CAffectation();
  $affectation->entree = CMbDT::dateTime();
  $affectation->lit_id = $lit_id;
  $affectation->sejour_id = $sejour_id;
  $affectation->sortie = $sejour->sortie_prevue;
  $affectation->store();
}

$do = new CDoObjectAddEdit("CSejour");
$do->doIt();