<?php /* $Id:affectation.class.php 8146 2010-02-25 14:38:16Z rhum1 $ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision:8146 $
 *  @author Thomas Despoix
 */

/**
 * Classe CAffectation.
 * @abstract Gère les affectation des séjours dans des lits
 */
class CAffectation extends CMbObject {
  // DB Table key
  var $affectation_id = null;

  // DB References
  var $lit_id    = null;
  var $sejour_id = null;

  // DB Fields
  var $entree   = null;
  var $sortie   = null;
  var $confirme = null;
  var $effectue = null;
  var $rques    = null;

  // Form Fields
  var $_entree_relative = null;
  var $_sortie_relative = null;
  var $_mode_sortie     = null;
  var $_duree           = null;
  
  // Order fields
  var $_patient   = null;
  var $_praticien = null;
  var $_chambre   = null;

  // Object references
  var $_ref_lit    = null;
  var $_ref_sejour = null;
  var $_ref_prev   = null;
  var $_ref_next   = null;
  var $_no_synchro = null;
  var $_list_repas = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'affectation';
    $spec->key   = 'affectation_id';
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["repas"] = "CRepas affectation_id";
    return $backProps;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["lit_id"]       = "ref notNull class|CLit";
    $specs["sejour_id"]    = "ref class|CSejour cascade";
    $specs["entree"]       = "dateTime notNull";
    $specs["sortie"]       = "dateTime notNull";
    $specs["confirme"]     = "bool";
    $specs["effectue"]     = "bool";
    $specs["rques"]        = "text";

    $specs["_duree"]       = "num";
    $specs["_mode_sortie"] = "enum list|normal|transfert|deces default|normal";
    
    $specs["_patient"]     = "str";
    $specs["_praticien"]   = "str";
    $specs["_chambre"]     = "str";
    
    return $specs;
  }

  function loadView() {
    $this->loadRefLit();
    $this->_ref_lit->loadCompleteView();
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadView();
    $this->_view = $this->_ref_lit->_view;
    $this->_duree = mbDaysRelative($this->entree, $this->sortie);
  }

  function check() {
    if($msg = parent::check()) {
      return $msg;
    }
    
    //TODO: utiliser moreThan
    $this->completeField("entree");
    $this->completeField("sortie");
    if ($this->sortie < $this->entree) {
      return "La date de sortie doit être supérieure ou égale à la date d'entrée";
    }
    
    if ($msg = $this->checkCollisions()) {
      return $msg;
    }
    
    return null;
  }
  
  /**
   * @return string Store-like message
   */
  function checkCollisions(){
    $this->completeField("sejour_id");
    if (!$this->sejour_id) return;
    
    $affectation = new CAffectation;
    $affectation->sejour_id = $this->sejour_id;
    $affectations = $this->loadMatchingList();
    unset($affectations[$this->_id]);
    
    //TODO: corriger l'orthographe de colide > collide
    foreach($affectations as $_aff) {
      if($this->colide($_aff)) {
        return "Placement déjà effectué";
      }
    }
  }

  function deleteOne() {
    return parent::delete();
  }

  function delete() {
    $this->completeField("sejour_id");
    if($this->sejour_id){
      $this->loadRefSejour();
      return $this->_ref_sejour->delAffectations();
    }
    return $this->deleteOne();
  }

  function store() {
    $oldAff = new CAffectation();
    if($this->_id) {
      $oldAff->load($this->_id);
      $oldAff->loadRefsAffectations();
    }
    
    if ($msg = parent::store()) {
      return $msg;
    }
    
    // Modification de la date d'admission et de la durée de l'hospi
    $this->load($this->affectation_id);

    $this->loadRefSejour();
    if($oldAff->_id) {
      $this->_ref_prev = $oldAff->_ref_prev;
      $this->_ref_next = $oldAff->_ref_next;
    } else {
      $this->loadRefsAffectations();
    }
     
    $changeSejour = 0;
    $changePrev   = 0;
    $changeNext   = 0;

    if($this->_no_synchro) {
      return $msg;
    }
    if(!$this->_ref_prev->_id && $this->sejour_id) {
      if($this->entree != $this->_ref_sejour->_entree) {
        if($this->_ref_sejour->entree_reelle) {
          $this->_ref_sejour->entree_reelle = $this->entree;
        } else {
          $this->_ref_sejour->entree_prevue = $this->entree;
        }
        $changeSejour = 1;
      }
    } elseif($this->sejour_id) {
      if($this->entree != $this->_ref_prev->sortie) {
        $this->_ref_prev->sortie = $this->entree;
        $changePrev = 1;
      }
    }
    if(!$this->_ref_next->_id  && $this->sejour_id) {
      if($this->sortie != $this->_ref_sejour->_sortie) {
        if($this->_ref_sejour->sortie_reelle) {
          $this->_ref_sejour->sortie_reelle = $this->sortie;
        } else {
          $this->_ref_sejour->sortie_prevue = $this->sortie;
        }
        $changeSejour = 1;
      }
    } elseif($this->sejour_id) {
      if($this->sortie != $this->_ref_next->entree) {
        $this->_ref_next->entree = $this->sortie;
        $changeNext = 1;
      }
    }
    if($changePrev) {
      $this->_ref_prev->store();
    }
    if($changeNext) {
      $this->_ref_next->store();
    }
    if($changeSejour) {
      $this->_ref_sejour->_no_synchro = 1;
      $this->_ref_sejour->updateFormFields();
      $this->_ref_sejour->store();
    }
    return $msg;
  }

  function loadRefLit($cache = 1) {
    $this->_ref_lit = $this->loadFwdRef("lit_id", $cache);
  }

  function loadRefSejour($cache = 1) {
    $this->_ref_sejour =  $this->loadFwdRef("sejour_id", $cache);
  }

  function loadRefsFwd($cache = 1) {
    $this->loadRefLit($cache);
    $this->loadRefSejour($cache);
    $this->loadRefsAffectations();
  }

  function loadRefsAffectations() {
    $where = array (
      "affectation_id" => "!= '$this->affectation_id'",
      "sejour_id" => "= '$this->sejour_id'",
      "sortie" => "= '$this->entree'"
    );

    $this->_ref_prev = new CAffectation;
    $this->_ref_prev->loadObject($where);

    $where = array (
      "affectation_id" => "!= '$this->affectation_id'",
      "sejour_id" => "= '$this->sejour_id'",
      "entree" => "= '$this->sortie'"
    );

    $this->_ref_next = new CAffectation;
    $this->_ref_next->loadObject($where);
  }

  function getPerm($permType) {
    if(!$this->_ref_lit) {
      $this->loadRefLit();
    }
    if(!$this->_ref_sejour) {
      $this->loadRefSejour();
    }
    return ($this->_ref_lit->getPerm($permType) && $this->_ref_sejour->getPerm($permType));
  }

  function checkDaysRelative($date) {
    if ($this->entree and $this->sortie) {
      $this->_entree_relative = mbDaysRelative("$date 10:00:00", $this->entree);
      $this->_sortie_relative = mbDaysRelative("$date 10:00:00", $this->sortie);
    }
  }

  function colide($aff) {
    return 
      ($aff->entree < $this->sortie and $aff->sortie > $this->sortie) || 
      ($aff->entree < $this->entree and $aff->sortie > $this->entree) || 
      ($aff->entree >= $this->entree and $aff->sortie <= $this->sortie);
  }

  function loadMenu($date, $listTypeRepas = null){
    $this->_list_repas[$date] = array();
    $repas =& $this->_list_repas[$date];
    if(!$listTypeRepas){
      $listTypeRepas = new CTypeRepas;
      $order = "debut, fin, nom";
      $listTypeRepas = $listTypeRepas->loadList(null,$order);
    }

    $where                   = array();
    $where["date"]           = $this->_spec->ds->prepare(" = %", $date);
    $where["affectation_id"] = $this->_spec->ds->prepare(" = %", $this->affectation_id);
    foreach($listTypeRepas as $keyType=>$typeRepas){
      $where["typerepas_id"] = $this->_spec->ds->prepare("= %",$keyType);
      $repasDuJour = new CRepas;
      $repasDuJour->loadObject($where);
      $repas[$keyType] = $repasDuJour;
    }
  }
}
?>