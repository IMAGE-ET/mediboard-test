<?php

/**
 * SMP Object handler
 *
 * @category SMP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CSmpObjectHandler
 * SMP Object handler
 */

class CSmpObjectHandler extends CEAIObjectHandler {
  /** @var array $handled */
  static $handled = array ("CSejour", "CAffectation", "CNaissance");

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
    if (!parent::onAfterStore($mbObject)) {
      return;
    }
    
    // Si pas de tag séjour
    if (!CAppUI::conf("dPplanningOp CSejour tag_dossier")) {
      throw new CMbException("no_tag_defined");
    }

    // Si serveur et pas de NDA sur le séjour
    if ((isset($mbObject->_no_num_dos) && ($mbObject->_no_num_dos == 1)) && CAppUI::conf('smp server')) {
      return;
    }
    
    $this->sendFormatAction("onAfterStore", $mbObject);
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
    if (!parent::onBeforeMerge($mbObject)) {
      return;
    }
    
    // Si pas en mode alternatif
    if (!CAppUI::conf("alternative_mode")) {
      throw new CMbException("no_alternative_mode");
    }
    
     $sejour = $mbObject;

    $sejour_elimine = new CSejour();
    $sejour_elimine->load(reset($mbObject->_merging));

    // Si Client
    if (!CAppUI::conf('smp server')) {
      $mbObject->_fusion = array();
      foreach (CGroups::loadGroups() as $_group) {
        if ($mbObject->_eai_initiateur_group_id == $_group->_id) {
          continue;
        }
        
        $sejour->_NDA = null;
        $sejour->loadNDA($_group->_id);
        $sejour1_nda = $sejour->_NDA;

        $sejour_elimine->_NDA = null;
        $sejour_elimine->loadNDA($_group->_id);
        $sejour2_nda = $sejour_elimine->_NDA;

        // Passage en trash des NDA des séjours
        $tag_NDA = CSejour::getTagNDA($_group->_id);
        
        $idexSejour = new CIdSante400();
        $idexSejour->tag          = $tag_NDA;
        $idexSejour->object_class = "CSejour";
        $idexSejour->object_id    = $sejour->_id;
        $idexsSejour = $idexSejour->loadMatchingList();

        $idexSejourElimine = new CIdSante400();
        $idexSejourElimine->tag          = $tag_NDA;
        $idexSejourElimine->object_class = "CSejour";
        $idexSejourElimine->object_id    = $sejour_elimine->_id;
        $idexsSejourElimine = $idexSejourElimine->loadMatchingList();

        $idexs = array_merge($idexsSejour, $idexsSejourElimine);
        if (count($idexs) > 1) {
          foreach ($idexs as $_idex) {
            // On continue pour ne pas mettre en trash le NDA du séjour que l'on garde
            if ($_idex->id400 == $sejour1_nda) {
              continue;
            }

            $_idex->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_trash').$tag_NDA;
            $_idex->last_update = CMbDT::dateTime();
            $_idex->store();
          }
        }
        
        if (!$sejour1_nda && !$sejour2_nda) {
          continue;  
        }
        
        $mbObject->_fusion[$_group->_id] = array (
          "sejourElimine" => $sejour_elimine,
          "sejour1_nda"   => $sejour1_nda,
          "sejour2_nda"   => $sejour2_nda,
        );
      }        
    }
    
    $this->sendFormatAction("onBeforeMerge", $mbObject);
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
    if (!parent::onAfterMerge($mbObject)) {
      return;
    }
    
     // Si pas en mode alternatif
    if (!CAppUI::conf("alternative_mode")) {
      throw new CMbException("no_alternative_mode");
    }

    $this->sendFormatAction("onAfterMerge", $mbObject);
  }

  /**
   * Trigger before event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onBeforeDelete(CMbObject $mbObject) {
    if (!parent::onBeforeDelete($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onBeforeDelete", $mbObject);
  }

  /**
   * Trigger after event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterDelete(CMbObject $mbObject) {
    if (!parent::onAfterDelete($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterDelete", $mbObject);
  }  
}