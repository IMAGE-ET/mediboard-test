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

$sejour_id            = CValue::post("sejour_id");
$equipement_id        = CValue::post("equipement_id");
$therapeute_id        = CValue::post("therapeute_id");
$line_id              = CValue::post("line_id");
$cdarrs               = CValue::post("cdarrs");
$_cdarrs              = CValue::post("_cdarrs");
$csarrs               = CValue::post("csarrs");
$_csarrs              = CValue::post("_csarrs");
$remarque             = CValue::post("remarque");
$seance_collective    = CValue::post("seance_collective");    // Checkbox de la seance collective
$seance_collective_id = CValue::post("seance_collective_id"); // Id de la seance collective

// Codes CdARR
$codes_cdarrs = array();
if (is_array($cdarrs)) {
  foreach ($cdarrs as $_code) {
    $codes_cdarrs[] = $_code;
  }
}

if (is_array($_cdarrs)) {
  foreach ($_cdarrs as $_code) {
    $codes_cdarrs[] = $_code;
  }
}

// Codes CdARR
$codes_csarrs = array();
if (is_array($csarrs)) {
  foreach ($csarrs as $_code) {
    $codes_csarrs[] = $_code;
  }
}

if (is_array($_csarrs)) {
  foreach ($_csarrs as $_code) {
    $codes_csarrs[] = $_code;
  }
}


$_days = CValue::post("_days");
$_heure_deb = CValue::post("_heure_deb");
$duree = CValue::post("duree");

$kine = new CMediusers();
$kine->load($therapeute_id);

$sejour = new CSejour;
$sejour->load($sejour_id);

// Ajout d'un evenement dans la seance choisie
if ($seance_collective_id) {
  $evenement = new CEvenementSSR();
  $evenement->sejour_id = $sejour_id;
  $evenement->prescription_line_element_id = $line_id;
  $evenement->seance_collective_id = $seance_collective_id;
  
  $evenement->loadMatchingObject();
  if ($evenement->_id) {
    CAppUI::displayMsg("Patient déjà présent dans la séance", "CEvenementSSR-title-create");
  }
  else {
    $msg = $evenement->store();
    CAppUI::displayMsg($msg, "CEvenementSSR-msg-create");
    
    // Actes CdARR
    foreach ($codes_cdarrs as $_code) {
      $acte = new CActeCdARR();
      $acte->code = $_code;
      $acte->evenement_ssr_id = $evenement->_id;
      $msg = $acte->store();
      CAppUI::displayMsg($msg, "$acte->_class-msg-create");
    }

    // Actes CsARR
    foreach ($codes_csarrs as $_code) {
      $acte = new CActeCsARR();
      $acte->code = $_code;
      $acte->evenement_ssr_id = $evenement->_id;
      $msg = $acte->store();
      CAppUI::displayMsg($msg, "$acte->_class-msg-create");
    }
  }
}
// Creation des evenements et eventuellement des seances si la checkbox est cochée
else {
  if (count($_days)) {
    $entree = CMbDT::date($sejour->entree);
    $sortie = CMbDT::date($sejour->sortie);
    $bilan = $sejour->loadRefBilanSSR();
    $referent = $bilan->loadRefKineReferent();
    
    // Ugly hack du m_post
    global $m;
    $m = $m_post;
    
    $date = CValue::getOrSession("date", CMbDT::date());

    $monday = CMbDT::date("last monday", CMbDT::date("+1 day", $date));
    foreach ($_days as $_number) {
      $_day = CMbDT::date("+$_number DAYS", $monday);
      if (!CMbRange::in($_day, $entree, $sortie)) {
        CAppUI::setMsg("CEvenementSSR-msg-failed-bounds", UI_MSG_WARNING);
        continue; 
      }
    
      if (!$_heure_deb || !$duree) {
        continue;
      }  
      
      $evenement = new CEvenementSSR();
      $evenement->equipement_id = $equipement_id;
      $evenement->debut         = "$_day $_heure_deb";
      $evenement->duree         = $duree;
      $evenement->remarque      = $remarque;
      $evenement->therapeute_id = $therapeute_id;
                
      // Transfert kiné référent => kiné remplaçant si disponible
      if ($therapeute_id == $referent->_id) {
        $conge = new CPlageConge();
        $conge->loadFor($therapeute_id, $_day);
        // Référent en congés
        if ($conge->_id) {
          $replacement = new CReplacement();
          $replacement->conge_id = $conge->_id;
          $replacement->sejour_id = $sejour->_id;
          $replacement->loadMatchingObject();
          if ($replacement->_id) {
            $evenement->therapeute_id = $replacement->replacer_id;
          }
        }
      }

      // Transfert kiné remplacant => kiné référant si présent
      if ($sejour->isReplacer($therapeute_id)) {
        $conge = new CPlageConge();
        $conge->loadFor($referent->_id, $_day);
        // Référent présent
        if (!$conge->_id) {
          $evenement->therapeute_id = $referent->_id;
        }
      }

      // Si l'evenement n'est pas une seance collective
      if (!$seance_collective) {
        $evenement->prescription_line_element_id = $line_id;
        $evenement->sejour_id = $sejour_id;
      }
  
      // Store de l'evenement ou de la nouvelle seance
      $msg = $evenement->store();
      CAppUI::displayMsg($msg, "CEvenementSSR-msg-create");
      
      $evenement_actes_id = $evenement->_id;
            
      // Si une seance a ete créée, on crée l'evenement lié a la seance, et on crée les code cdarr sur l'evenement
      if ($seance_collective) {
        $evt_ssr = new CEvenementSSR();
        $evt_ssr->sejour_id = $sejour_id;
        $evt_ssr->prescription_line_element_id = $line_id;
        $evt_ssr->seance_collective_id = $evenement->_id;
        $msg = $evt_ssr->store();
        CAppUI::displayMsg($msg, "CEvenementSSR-msg-create");
        
        // Si une seance a ete créée, les codes cdarrs seront créés sur l'evenement de la seance
        $evenement_actes_id = $evt_ssr->_id;    
      } 
      
      // Actes CdARR
      foreach ($codes_cdarrs as $_code) {
        $acte = new CActeCdARR();
        $acte->code = $_code;
        $acte->evenement_ssr_id = $evenement_actes_id;
        $msg = $acte->store();
        CAppUI::displayMsg($msg, "$acte->_class-msg-create");
      }
  
      // Actes CsARR
      foreach ($codes_csarrs as $_code) {
        $acte = new CActeCsARR();
        $acte->code = $_code;
        $acte->evenement_ssr_id = $evenement_actes_id;
        $msg = $acte->store();
        CAppUI::displayMsg($msg, "$acte->_class-msg-create");
      }
    }
  }
}
echo CAppUI::getMsg();
CApp::rip();
