<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$_validation   = CValue::post("_validation", null);

class CDoFicheEiAddEdit extends CDoObjectAddEdit {
  function CDoFicheEiAddEdit() {
    $this->CDoObjectAddEdit("CFicheEi", "fiche_ei_id");
  }

  function doStore() {
    global $_validation, $m;

    
    if(!$_validation){
      // Pas de $_validation ==> pas d'edition de la fiche
      
      if(!$this->_old->fiche_ei_id){
        // Nouvelle fiche
        $this->_obj->date_fiche = CMbDT::dateTime();
        
      }elseif($this->_old->qualite_date_validation){      
        // NE RIEN FAIRE !! -> Attente de verif et de controle
        
      }elseif(!$this->_old->qualite_date_validation && $this->_old->service_date_validation){
        $this->_obj->qualite_date_validation = CMbDT::dateTime();
      
      }elseif(!$this->_old->service_date_validation && $this->_old->date_validation){  
        $this->_obj->service_date_validation = CMbDT::dateTime();
        
      }elseif(!$this->_old->date_validation){
        $this->_obj->date_validation = CMbDT::dateTime();
      }
      
    }
    $this->redirectStore = "m=$m&tab=vw_incidentvalid";
    parent::doStore();
  }
}

$do = new CDoFicheEiAddEdit;
$do->doIt();
?>
