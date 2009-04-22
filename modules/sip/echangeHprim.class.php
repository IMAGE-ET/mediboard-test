<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
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
  var $message_valide       = null;
  var $acquittement_valide  = null;
  
  var $_ref_notifications   = null;
  
  // Form fields
  var $_self_emetteur       = null;
  var $_self_destinataire   = null;
  
  // Filter fields
  var $_date_min            = null;
  var $_date_max            = null;
  
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
    $specs["initiateur_id"]         = "ref class|CEchangeHprim";
    $specs["statut_acquittement"]   = "str";
    $specs["message_valide"]        = "bool";
    $specs["acquittement_valide"]   = "bool";
    $specs["_self_emetteur"]        = "bool";
    $specs["_self_destinataire"]    = "bool notNull";
    $specs["_date_min"]             = "dateTime";
    $specs["_date_max"]             = "dateTime";
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