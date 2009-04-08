<?php
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CEchangeHprim class
 */
class CEchangeHprim extends CMbObject {
  // DB Table key
  var $echange_hprim_id     = null;
  
  // DB Fields
  var $date_production      = null;
  var $emetteur             = null;
  var $identifiant_emetteur = null;
  var $destinataire         = null;
  var $type                 = null;
  var $sous_type            = null;
  var $date_echange         = null;
  var $message              = null;
  var $acquittement         = null;
  var $statut_acquittement  = null;
  var $initiateur_id        = null;
  
  var $_ref_notifications   = null;
  
  // Form fields
  var $_self_emetteur             = null;
  var $_self_destinataire         = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'echange_hprim';
    $spec->key   = 'echange_hprim_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["date_production"]       = "dateTime notNull";
    $specs["emetteur"]              = "str";
    $specs["identifiant_emetteur"]  = "str";
    $specs["destinataire"]          = "str notNull";
    $specs["type"]                  = "str";
    $specs["sous_type"]             = "str";
    $specs["date_echange"]          = "dateTime";
    $specs["message"]               = "xml notNull";
    $specs["acquittement"]          = "xml";
    $specs["statut_acquittement"]   = "str";
    $specs["initiateur_id"]         = "ref class|CEchangeHprim";
    $specs["_self_emetteur"]        = "bool";
    $specs["_self_destinataire"]    = "bool notNull";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['notifications'] = "CEchangeHprim initiateur_id";
    
    return $backProps;
  }
  
  function loadRefNotifications(){
    $this->_ref_notifications = $this->loadBackRefs("notifications");
  }
  
  function updateFormFields() {
  	parent::updateFormFields();
  	
  	$this->_self_emetteur = $this->emetteur == CAppUI::conf('mb_id');
    $this->_self_destinataire = $this->destinataire == CAppUI::conf('mb_id');
    
  }

}
?>