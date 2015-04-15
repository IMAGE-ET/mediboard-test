<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// Praticien courant pour les prises de rendez-vous suivantes
if ($chir_id = CValue::post("chir_id")) {
  CValue::setSession("chir_id", $chir_id);
}

// Consultation courante dans edit_consulation
if (CValue::post("del")) {
  CValue::setSession("selConsult");
}

// before basic job, do the multiple consultations
CAppUI::requireModuleFile("dPcabinet", "controllers/do_consultation_multiple");

// Cas de l'annulation / rétablissement / supression multiple
if ($consultation_ids = CValue::post("consultation_ids")) {
  $_POST = array(
    "consultation_ids" => CValue::post("consultation_ids"),
    "motif_annulation" => CValue::post("motif_annulation"),
    "annule"           => CValue::post("annule", 0),
    "del"              => CValue::post("del"),
    "sejour_id"        => CValue::post("sejour_id"),
    "postRedirect"     => CValue::post("postRedirect")
  );
}

//consult n°1, classic use
$do = new CDoObjectAddEdit("CConsultation");
$do->doIt();