<?php
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The MessageHprim class
 */
class CMessageHprim extends CMbObject {
  // DB Table key
  var $message_hprim_id   = null;
  
  // DB Fields
  var $date_production    = null;
  var $emetteur           = null;
  var $destinataire       = null;
  var $type               = null;
  var $sous_type          = null;
  var $date_echange       = null;
  var $message            = null;
  var $acquittement       = null;
  var $initiateur_id      = null;
  
  var $_ref_notifications = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'message_hprim';
    $spec->key   = 'message_hprim_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["date_production"]     = "dateTime notNull";
    $specs["emetteur"]            = "str notNull";
    $specs["destinataire"]        = "str notNull";
    $specs["type"]                = "str notNull";
    $specs["sous_type"]           = "str";
    $specs["date_echange"]        = "dateTime";
    $specs["message"]             = "xml notNull";
    $specs["acquittement"]        = "xml";
    $specs["initiateur_id"]       = "ref class|CMessageHprim";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['notifications'] = "CMessageHprim initiateur_id";
    
    return $backProps;
  }
  
  function loadRefNotifications(){
    $this->_ref_notifications = $this->loadBackRefs("notifications");
  }

}
?>