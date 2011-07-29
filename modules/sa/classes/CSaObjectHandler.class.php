<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage smp
 * @version $Revision: 12577 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSaObjectHandler extends CEAIObjectHandler {
  static $handled = array ("CSejour", "COperation");

  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  function onAfterStore(CMbObject $mbObject) {
    if (!parent::onAfterStore($mbObject)) {
      return;
    }
    
    if ($mbObject->fieldModified('facture', 1)) {
      // Cas du séjour / opération facturé
      $this->sendFormatAction("onAfterStore", $mbObject);
    }
  }
}
?>