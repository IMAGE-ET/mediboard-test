<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision: 10062 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CEchangeHprim21 extends CExchangeTabular {
  static $messages = array(
    "ADM" => "CADM",
    "REG" => "CREG",
  );
  
  // DB Table key
  var $echange_hprim21_id = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'echange_hprim21';
    $spec->key   = 'echange_hprim21_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    
    $backProps["complementaire_hprim21"] = "CHprim21Complementaire echange_hprim21_id";
    $backProps["medecins_hprim21"]       = "CHprim21Medecin echange_hprim21_id";
    $backProps["patients_hprim21"]       = "CHprim21Patient echange_hprim21_id";
    $backProps["sejours_hprim21"]        = "CHprim21Sejour echange_hprim21_id";
    
    return $backProps;
  }
  
  function getProps() {
    $props = parent::getProps();

    $props["receiver_id"]  = "ref class|CDestinataireHprim21";
    $props["object_class"] = "enum list|CPatient|CSejour|CMedecin show|0";
    
    $props["_message"]      = "hpr";
    $props["_acquittement"] = "hpr";
    
    return $props;
  }
  
  function handle() {
    return COperatorIHE::event($this);
  }

  function getFamily() {
    return self::$messages;
  }
  
  function isWellFormed($data, CInteropActor $actor = null) {
    return true; 
  }
  
  function understand($data, CInteropActor $actor = null) {
    if (!$this->isWellFormed($data, $actor)) {
      return false;
    }
  }
}
?>