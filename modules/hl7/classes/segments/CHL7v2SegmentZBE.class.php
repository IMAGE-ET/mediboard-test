<?php

/**
 * Represents an HL7 ZBE message segment (Movement) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentZBE
 * ZBE - Represents an HL7 ZBE message segment (Movement)
 */

class CHL7v2SegmentZBE extends CHL7v2Segment {
  static $actions = array(
    "INSERT" => array(
      "A05", "A01", "A14", "A04", "A06", "A07", "A54", "A02", "A15", 
      "A03", "A16", "A21", "A22", "Z80", "Z83", "Z84", "Z86", "Z88"
    ),
    "UPDATE" => array(
      "Z99"
    ),
    "CANCEL" => array(
      "A38", "A11", "A27", /* "A06", "A07", */ "A55", "A12", "A26", "A13", 
      "A25", "A52", "A53", "Z81", "Z83", "Z85", "Z87", "Z89"
    ),
  );
  
  var $name   = "ZBE";
  
  /**
   * @var CSejour
   */
  var $sejour = null;
  
  /**
   * @var CMovement
   */
  var $movement = null;
  
  /**
   * @var CAffectation
   */
  var $curr_affectation = null;
  
  /**
   * @var CAffectation
   */
  var $other_affectation = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $sejour      = $this->sejour;
    $movement    = $this->movement;
    $affectation = $this->curr_affectation;
    if ($this->other_affectation) {
      $affectation = $this->other_affectation;
    }

    // ZBE-1: Movement ID (EI) (optional)
    $data[] = array (
      array (
        // Entity identifier
        $movement->_view,
        // Autorité assignement
        "Mediboard",
        CAppUI::conf("hl7 assigningAuthorityUniversalID"),
        "OX"
      )
    );
    
    // ZBE-2: Start of Movement Date/Time (TS)
    // $data[] = $movement->last_update;
    $code = $event->code;
    if ($event->code == "Z99") {
      $code = $movement->original_trigger_code;
    }
    
    switch ($code) {
      case 'A01' : case 'A04' :
        $data[] = $sejour->entree_reelle;
        break;
      case 'A05':
        $data[] = $sejour->entree_prevue;
        break;
      case 'A03':
        $data[] = $sejour->sortie_reelle;
        break;  
      default:
        $data[] = $movement->last_update;
        break;
    }
    
    // ZBE-3: End of Movement Date/Time (TS) (optional)
    // Forbidden (IHE France)
    $data[] = null;
    
    // ZBE-4: Action on the Movement (ID)
    $action_movement = null;
    foreach (self::$actions as $action => $events) {
      if (in_array($event->code, $events)) {
        $action_movement = $action;
      }
    };
    $data[] = $action_movement;
    
    // ZBE-5: Indicator "Historical Movement" (ID) 
    $data[] = $movement->_current ? "Y" : "N";
    
    // ZBE-6: Original trigger event code (ID) (optional)
    $data[] = ($action_movement == "UPDATE" || $action_movement == "CANCEL") ? $movement->original_trigger_code : null;
    
    // ZBE-7: Ward of medical responsibility in the period starting with this movement (XON) (optional)
    $affectation->loadRefUFMedicale();
    $uf_medicale = $affectation->_ref_uf_medicale;
    if (isset($uf_medicale->_id)) {
      $data[] = array(
        // ZBE-7.1 : Libellé de l'UF
        $uf_medicale->libelle,
        null,
        null,
        null,
        null,
        // ZBE-7.6 : Identifiant de l'autorité d'affectation  qui a attribué l'identifiant de l'UF de responsabilité médicale
        $this->getAssigningAuthority("mediboard"),
        // ZBE-7.7 : La seule valeur utilisable de la table 203 est "UF"
        "UF",
        null,
        null,
        // ZBE-7.10 : Identifiant de l'UF de responsabilité médicale
        $uf_medicale->code
      );
    } 
    else {
      $data[] = null;
    }
    
    // ZBE-8: Ward of care responsibility in the period starting with this movement (XON) (optional)
    $affectation->loadRefUFSoins();
    $uf_soins = $affectation->_ref_uf_soins;
    if (isset($uf_soins->_id)) {
      $data[] = array(
        // ZBE-7.1 : Libellé de l'UF
        $uf_soins->libelle,
        null,
        null,
        null,
        null,
        // ZBE-7.6 : Identifiant de l'autorité d'affectation  qui a attribué l'identifiant de l'UF de responsabilité médicale
        $this->getAssigningAuthority("mediboard"),
        // ZBE-7.7 : La seule valeur utilisable de la table 203 est "UF"
        "UF",
        null,
        null,
        // ZBE-7.10 : Identifiant de l'UF de responsabilité médicale
        $uf_soins->code
      );
    } 
    else {
      $data[] = null;
    }
    
    // ZBE-9: Nature of this movement (CWE)
    // S - Changement de responsabilité de soins uniquement
    // H - Changement de  responsabilité  d'hébergement  soins uniquement
    // M - Changement de responsabilité médicale uniquement
    // L - Changement de lit uniquement
    // D - Changement de prise en charge médico-administrative laissant les responsabilités et la localisation du patient inchangées 
    //     (ex : changement de tarif du séjour en unité de soins)
    // SM - Changement de responsabilité soins + médicale
    // SH - Changement de responsabilité soins + hébergement
    // MH - Changement de responsabilité hébergement + médicale
    // LD - Changement de prise en charge médico-administrative et de lit, laissant les responsabilités inchangées
    // HMS - Changement conjoint des trois responsabilités.
    /* @todo Voir comment gérer ceci... */
    $data[] = "HMS";
    
    $this->fill($data);
  }
}

?>