<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;

$_validation   = mbGetValueFromPost("_validation", null);

class CDoFicheEiAddEdit extends CDoObjectAddEdit {
  function CDoFicheEiAddEdit() {
    $this->CDoObjectAddEdit("CFicheEi", "fiche_ei_id");
  }

  function doStore() {
    global $AppUI, $_validation, $m;

    
    if(!$_validation){
      // Pas de $_validation ==> pas d'edition de la fiche
      
      if(!$this->_objBefore->fiche_ei_id){
        // Nouvelle fiche
        $this->_obj->date_fiche = mbDateTime();
        
      }elseif($this->_objBefore->qualite_date_validation){      
        // NE RIEN FAIRE !! -> Attente de verif et de controle
        
      }elseif(!$this->_objBefore->qualite_date_validation && $this->_objBefore->service_date_validation){
        $this->_obj->qualite_date_validation = mbDateTime();
      
      }elseif(!$this->_objBefore->service_date_validation && $this->_objBefore->date_validation){  
        $this->_obj->service_date_validation = mbDateTime();
        
      }elseif(!$this->_objBefore->date_validation){
        $this->_obj->date_validation = mbDateTime();
      }
      
    }
    $this->redirectStore = "m=$m&tab=vw_incidentvalid";
    parent::doStore();
  }
}

$do = new CDoFicheEiAddEdit;
$do->doIt();
?>
