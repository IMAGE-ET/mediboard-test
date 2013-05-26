<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$token_field_evts = CValue::getOrSession("token_field_evts");

/** @var CSejour[] $sejours */
$sejours = array();
/** @var CEvenementSSR[] $events */
$events = array();

$_evenements = $token_field_evts ? explode("|", $token_field_evts) : array();
foreach ($_evenements as $_evenement_id) {
  $evenement = new CEvenementSSR();
  $evenement->load($_evenement_id);
  
  if ($evenement->sejour_id) {
    $events[$evenement->_id] = $evenement;
  }
  else {
    $evenement->loadRefsEvenementsSeance();
    foreach ($evenement->_ref_evenements_seance as $_evt_seance) {
      $_evt_seance->debut = $evenement->debut;
      $_evt_seance->duree = $evenement->duree;
      
      $events[$_evt_seance->_id] = $_evt_seance;
    }
  }
}

$count_zero_actes = 0;
$evenements = array();
foreach ($events as $_event) {
  $_event->loadRefEquipement();

  $actes_cdarr = $_event->loadRefsActesCdarr();
  $actes_csarr = $_event->loadRefsActesCsarr();
  foreach ($actes_csarr as $_acte_csarr) {
    $_acte_csarr->loadRefActiviteCsARR();
  }
  
  $_event->_count_actes = count($actes_cdarr) + count($actes_csarr);
  if (!$_event->_count_actes) {
    $count_zero_actes++;
  }
  
  $sejour = $_event->loadRefSejour();
  $sejour->loadRefPatient();
  $sejours[$sejour->_id] = $sejour;
  $line = $_event->loadRefPrescriptionLineElement();
  $element_id = $line ? $line->element_prescription_id : null;
  $date_debut = CMbDT::date($_event->debut);
  $evenements[$_event->sejour_id][$element_id.$date_debut][$_event->_id] = $_event;
}

/** @var array $_evenements_by_sejour */
foreach ($evenements as &$_evenements_by_sejour) {
  ksort($_evenements_by_sejour);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("evenements", $evenements);
$smarty->assign("sejours", $sejours);
$smarty->assign("count_zero_actes", $count_zero_actes);
$smarty->display("inc_vw_modal_evenements.tpl");
