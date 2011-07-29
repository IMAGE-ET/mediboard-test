<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSipObjectHandler extends CEAIObjectHandler {
  static $handled = array ("CPatient");
  
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }
  
  function onAfterStore(CMbObject $mbObject) {
    if (!parent::onAfterStore($mbObject)) {
      return;
    }
    
    // Si pas de tag patient
    if (!CAppUI::conf("dPpatients CPatient tag_ipp")) {
      throw new CMbException("no_tag_defined");
    }

    // Si serveur et pas d'IPP sur le patient ou de numéro de dossier sur le séjour
    if ((isset($mbObject->_no_ipp) && ($mbObject->_no_ipp == 1)) &&
        CAppUI::conf('sip server')) {
      return;
    }
    
    $this->sendFormatAction("onAfterStore", $mbObject);
  }

  function onBeforeMerge(CMbObject $mbObject) {
    if (!parent::onBeforeMerge($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onBeforeMerge", $mbObject);
  }
  
  function onAfterMerge(CMbObject $mbObject) {
    if (!parent::onAfterMerge($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterMerge", $mbObject);
  }
}
?>