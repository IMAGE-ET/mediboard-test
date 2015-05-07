<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();
$token_evts = CValue::getOrSession("token_evts");

$_evenements = array();
if ($token_evts) {
  $_evenements = explode("|", $token_evts);
}

$count_actes = $actes = array(
  "cdarr" => array(),
  "csarr" => array(),
);

$evenements = array();
foreach ($_evenements as $_evenement_id) {
  $evenement = new CEvenementSSR();
  $evenement->load($_evenement_id);
  
  if ($evenement->seance_collective_id) {
    // Recuperation des informations de la seance collective
    $evenement->loadRefSeanceCollective();
    $evenement->debut = $evenement->_ref_seance_collective->debut;
    $evenement->duree = $evenement->_ref_seance_collective->duree;
  }
  
  $evenement->loadRefSejour()->loadRefPatient();

  // Chargement et comptage des codes de tous les actes
  foreach ($evenement->loadRefsActes() as $_type => $_actes) {
    foreach ($_actes as $_acte) {
      $actes[$_type][$_acte->code] = $_acte->code;
      if (!isset($count_actes[$_type][$_acte->code])) {
        $count_actes[$_type][$_acte->code] = 0;
      }
      $count_actes[$_type][$_acte->code]++;
    }
  }

  // Chargement des codes possibles pour l'evenement
  $line = $evenement->loadRefPrescriptionLineElement();
  $element  = $line->_ref_element_prescription;
  foreach ($element->loadRefsCodesSSR() as $_type => $_links) {
    foreach ($_links as $_link_cdarr) {
      $actes[$_type][$_link_cdarr->code] = $_link_cdarr->code;
    }
  }

  $evenements[$evenement->_id] = $evenement;
}

// Sorting
foreach ($actes as $_type => &$_actes) {
  ksort($_actes);
}

if (!count($count_actes["cdarr"])) {
  unset($actes["cdarr"]);
  unset($count_actes["cdarr"]);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("token_evts"  , $token_evts);
$smarty->assign("evenements"  , $evenements);
$smarty->assign("actes"       , $actes);
$smarty->assign("count_actes" , $count_actes);
$smarty->display("inc_vw_modal_evts_modif.tpl");
