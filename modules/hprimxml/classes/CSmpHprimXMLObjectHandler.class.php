<?php

/**
 * SMP H'XML Object handler
 *
 * @category SMP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CSmpHprimXMLObjectHandler
 * SMP H'XML Object handler
 */

class CSmpHprimXMLObjectHandler extends CHprimXMLObjectHandler {
  /** @var array $handled */
  static $handled = array ("CSejour", "CAffectation");

  /**
   * If object is handled ?
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  /**
   * Trigger after event store
   *
   * @param CMbObject $mbObject Object
   *
   * @throws CMbException
   *
   * @return void
   */
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
    
    $receiver = $mbObject->_receiver;  
    
    // Traitement Sejour
    if ($mbObject instanceof CSejour) {
      $sejour = $mbObject;

      $sejour->loadRefPraticien();
      $sejour->loadNDA();
      $sejour->loadLastLog();

      $sejour->loadRefPatient();
      $sejour->loadRefAdresseParPraticien();

      // Si Serveur
      if (CAppUI::conf('smp server')) {
        
        $echange_hprim = new CEchangeHprim();
        if (isset($sejour->_eai_exchange_initiator_id)) {
          $echange_hprim->load($sejour->_eai_exchange_initiator_id);
        }

        $initiateur = ($receiver->_id == $echange_hprim->sender_id) ? $echange_hprim->_id : null;
        
        $group = new CGroups();
        $group->load($receiver->group_id);
        $group->loadConfigValues();
        
        $mbObject->_id400 = null;
        $idexPatient = new CIdSante400();
        $idexPatient->loadLatestFor($sejour, $receiver->_tag_sejour);
        $mbObject->_id400 = $idexPatient->id400;

        $this->generateTypeEvenement("CHPrimXMLVenuePatient", $sejour, true, $initiateur);
      }
      // Si Client
      else {
        if ($sejour->_eai_initiateur_group_id || !$receiver->isMessageSupported("CHPrimXMLVenuePatient")) {
          return;
        }
          
        if (CGroups::loadCurrent()->_id != $receiver->group_id) {
          return;
        }
        
        if (!$sejour->_NDA) {
          $nda = new CIdSante400();
          //Paramétrage de l'id 400
          $nda->loadLatestFor($sejour, $receiver->_tag_sejour);

          $sejour->_NDA = $nda->id400;
        }
        
        if (!$sejour->_ref_patient->_IPP) {
          $IPP = new CIdSante400();
          //Paramétrage de l'id 400
          $IPP->loadLatestFor($sejour->_ref_patient, $receiver->_tag_patient);

          $sejour->_ref_patient->_IPP = $IPP->id400;
        }
        
        $this->sendEvenementPatient("CHPrimXMLVenuePatient", $sejour);
        
        if ($receiver->isMessageSupported("CHPrimXMLDebiteursVenue") && $sejour->_ref_patient->code_regime) {
          $this->sendEvenementPatient("CHPrimXMLDebiteursVenue", $sejour);
        }
        
        if ($receiver->isMessageSupported("CHPrimXMLMouvementPatient") && ($sejour->_ref_last_log->type == "create")) {
          $affectation = $sejour->loadRefFirstAffectation();

          // $this->sendEvenementPatient("CHPrimXMLMouvementPatient", $affectation);
        }

        $sejour->_NDA = null;
      }
    }

    // Traitement Affectation
    elseif ($mbObject instanceof CAffectation) {
      $affectation = $mbObject;

      // Si Client
      if (!CAppUI::conf('smp server')) {
        if (!$receiver->isMessageSupported("CHPrimXMLMouvementPatient")) {
          return;
        }
        
        //$this->sendEvenementPatient("CHPrimXMLMouvementPatient", $affectation);
      }
    }
  }

  /**
   * Trigger before event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @throws CMbException
   *
   * @return void
   */
  function onBeforeMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
  }

  /**
   * Trigger after event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @throws CMbException
   *
   * @return void
   */
  function onAfterMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    // Traitement Séjour
    if ($mbObject instanceof CSejour) {
      $sejour = $mbObject;
      $sejour->check();
      $sejour->updateFormFields();
      $sejour->loadRefPatient();
      $sejour->loadRefPraticien();
      $sejour->loadLastLog();
      $sejour->loadRefAdresseParPraticien();
      
      $receiver = $mbObject->_receiver;
      
      // Si Client
      if (!CAppUI::conf('smp server')) {
        foreach ($mbObject->_fusion as $group_id => $infos_fus) {
          if ($receiver->group_id != $group_id) {
            continue;
          } 

          if ($mbObject->_eai_initiateur_group_id == $receiver->group_id) {
            continue;
          }
          
          $sejour1_nda = $sejour->_NDA = $infos_fus["sejour1_nda"];
          
          $sejour_eliminee = $infos_fus["sejourElimine"];
          $sejour2_nda = $sejour_eliminee->_NDA = $infos_fus["sejour2_nda"];

          // Cas 0 NDA : Aucune notification envoyée
          if (!$sejour1_nda && !$sejour2_nda) {
            continue;
          }
         
          // Cas 1 NDA : Pas de message de fusion mais d'une modification de la venue
          if ((!$sejour1_nda && $sejour2_nda) || ($sejour1_nda && !$sejour2_nda)) {
            if ($sejour2_nda) {
              $sejour->_NDA = $sejour2_nda;
            }
            
            $this->sendEvenementPatient("CHPrimXMLVenuePatient", $sejour);
            continue;
          }
          
          // Cas 2 NDA : Message de fusion
          if ($sejour1_nda && $sejour2_nda) {
            $sejour->_sejour_eliminee = $sejour_eliminee;
            
            $this->sendEvenementPatient("CHPrimXMLFusionVenue", $sejour);
            continue;
          }
        }        
      }
    }
  }

  /**
   * Trigger before event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onBeforeDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
  }

  /**
   * Trigger after event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
  }
}