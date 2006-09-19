<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $g;

$_validation   = mbGetValueFromPost("_validation", null);

class CDoFicheEiAddEdit extends CDoObjectAddEdit {
  function CDoFicheEiAddEdit() {
    $this->CDoObjectAddEdit("CFicheEi", "fiche_ei_id");
    
    $this->createMsg = "Votre fiche d'incident à bien été prise en compte";
    $this->modifyMsg = "Fiche d'EI modifiée";
    $this->deleteMsg = "Fiche d'EI supprimée";
  }

  function doStore() {
    global $AppUI, $_validation;
    
    if($this->_obj->fiche_ei_id){
      // Fiche Existante --> Date de validation
      $this->_obj->date_validation = mbDateTime();
    }else{
      // Fiche non existante -> Date de memorisation
      $this->_obj->date_fiche = mbDateTime();
    }
    parent::doStore();
  }
}

$do = new CDoFicheEiAddEdit;
$do->doIt();
?>
