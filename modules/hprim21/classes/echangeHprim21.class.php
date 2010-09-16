<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision: 10062 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireSystemClass('mbMetaObject');

class CEchangeHprim21 extends CMbMetaObject {
  // DB Table key
  var $echange_hprim21_id = null;
  
  // DB Fields
  var $group_id          = null;
  var $date_production   = null;
  var $version           = null;
  var $type              = null;
  var $nom_fichier       = null;
  var $id_emetteur       = null;
  var $emetteur_desc     = null;
  var $adresse_emetteur  = null;
  var $id_destinataire   = null;
  var $destinataire_desc = null;
  var $type_message      = null;
  var $date_echange      = null;
  var $message           = null;
  var $message_valide    = null;
  var $id_permanent      = null;
  
  // Filter fields
  var $_date_min         = null;
  var $_date_max        =  null;
  
  // Forward references
  var $_ref_group         = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'echange_hprim21';
    $spec->key   = 'echange_hprim21_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
        
    $specs["group_id"]          = "ref notNull class|CGroups";
    $specs["date_production"]   = "dateTime notNull";
    $specs["version"]           = "str";
    $specs["type"]              = "str";
    $specs["nom_fichier"]       = "str";
    $specs["id_emetteur"]       = "str";
    $specs["emetteur_desc"]     = "str";
    $specs["adresse_emetteur"]  = "str";
    $specs["id_destinataire"]   = "str";
    $specs["destinataire_desc"] = "str";
    $specs["type_message"]      = "str";
    $specs["date_echange"]      = "dateTime";
    $specs["message"]           = "text";
    $specs["message_valide"]    = "bool show|0";
    $specs["id_permanent"]      = "str";
    $specs["object_id"]         = "ref class|CMbObject meta|object_class unlink";
    $specs["object_class"]      = "enum list|CPatient|CSejour|CMedecin show|0";
    
    $specs["_date_min"]         = "dateTime";
    $specs["_date_max"]         = "dateTime";
    
    return $specs;
  }
  
  function loadRefGroups() {
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
  }
}
?>