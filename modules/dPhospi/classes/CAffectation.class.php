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
  var $service_id = null;
  var $lit_id     = null;
  var $sejour_id  = null;
  var $parent_affectation_id = null;
  var $function_id = null;
  var $praticien_id = null;
  
  // DB Fields
  var $entree   = null;
  var $sortie   = null;
  var $effectue = null;
  var $rques    = null;
  
  var $uf_hebergement_id = null; // UF de responsabilité d'hébergement
  var $uf_medicale_id    = null; // UF de responsabilité médicale
  var $uf_soins_id       = null; // UF de responsabilité de soins

  // Form Fields
  var $_entree_relative = null;
  var $_sortie_relative = null;
  var $_mode_sortie     = null;
  var $_duree           = null;
  var $_is_prolong      = null;
  var $_entree          = null;
  var $_sortie          = null;
  var $_start_prolongation = null;
  var $_stop_prolongation = null;
  var $_width_prolongation = null;

  // Order fields
  var $_patient   = null;
  var $_praticien = null;
  var $_chambre   = null;

  // Object references
  var $_ref_lit     = null;
  var $_ref_service = null;
  var $_ref_sejour  = null;
  var $_ref_prev    = null;
  var $_ref_next    = null;
  var $_no_synchro  = null;
  var $_list_repas  = null;
  var $_ref_uf_hebergement = null; 
  var $_ref_uf_medicale    = null; 
  var $_ref_uf_soins       = null; 
  var $_ref_parent_affectation = null;
  var $_ref_praticien      = null;
  
  // EAI Fields
  var $_eai_initiateur_group_id  = null; // group initiateur du message EAI
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'affectation';
    $spec->key   = 'affectation_id';
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["echanges_hprim"]      = "CEchangeHprim object_id cascade";
    $backProps["echanges_ihe"]        = "CExchangeIHE object_id cascade";
    $backProps["repas"]               = "CRepas affectation_id";
    $backProps["affectations_enfant"] = "CAffectation parent_affectation_id";
    $backProps["movements"]           = "CMovement affectation_id";
    $backProps["meal"]                = "CMeal affectation_id";
    return $backProps;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["service_id"]            = "ref notNull class|CService";
    $specs["lit_id"]                = "ref class|CLit";
    $specs["sejour_id"]             = "ref class|CSejour cascade";
    $specs["parent_affectation_id"] = "ref class|CAffectation";
    $specs["function_id"]           = "ref class|CFunctions";
    $specs["praticien_id"]          = "ref class|CMediusers";
    $specs["entree"]                = "dateTime notNull";
    $specs["sortie"]                = "dateTime notNull";
    $specs["effectue"]              = "bool";
    $specs["rques"]                 = "text";
    
    $specs["uf_hebergement_id"] = "ref class|CUniteFonctionnelle seekable";
    $specs["uf_medicale_id"]    = "ref class|CUniteFonctionnelle seekable";
    $specs["uf_soins_id"]       = "ref class|CUniteFonctionnelle seekable";

    $specs["_duree"]       = "num";
    $specs["_mode_sortie"] = "enum list|normal|mutation|transfert|deces default|normal";
    
    $specs["_patient"]     = "str";
    $specs["_praticien"]   = "str";
    $specs["_chambre"]     = "str";
    
    return $specs;
  }

  function loadView() {
    parent::loadView();
    $sejour = $this->loadRefSejour();
    $sejour->loadRefPraticien();
    $sejour->loadRefPatient()->loadRefPhotoIdentite();
    $affectations = $sejour->loadRefsAffectations();
    
    if (is_array($affectations) && count($affectations)) {
      foreach ($affectations as $_affectation) {
        $_affectation->loadRefLit()->loadCompleteView();
        $_affectation->_view = $_affectation->_ref_lit->_view;
        $_affectation->loadRefParentAffectation();
      }
    }
    
    $this->loadRefLit()->loadCompleteView();
    $this->_view = $this->_ref_lit->_view;
    $this->loadRefParentAffectation();
    
    foreach ($sejour->loadRefsOperations() as $_operation) {
      $_operation->loadRefChir();
      $_operation->loadRefPlageOp();
    }
    
    $sejour->getDroitsCMU();
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_duree = CMbDT::daysRelative($this->entree, $this->sortie);
  }

  function check() {
    if ($msg = parent::check()) {
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
    $affectations = $affectation->loadMatchingList();
    unset($affectations[$this->_id]);
    
    foreach ($affectations as $_aff) {
      if ($this->collide($_aff)) {
        return "Placement déjà effectué";
      }
    }
  }

  function deleteOne() {
    return parent::delete();
  }

  function delete() {
    $this->completeField("sejour_id", "entree", "sortie");
    if (!$this->sejour_id) {
      return $this->deleteOne();
    }
    
    if ($this->loadRefSejour()->type == "seances") {
      return $this->deleteOne(); 
    }
    
    $this->loadRefsAffectations();
    
    $entree = $this->entree;
    $sortie = $this->sortie;
    
    if ($msg = $this->deleteOne()) {
      return $msg;
    }
    
    // On positionne la sortie de la précédente affectation à l'affectation que l'on supprime 
    $prev = $this->_ref_prev;
    if (isset($prev->_id)) {
      $prev->sortie = $sortie;
      
      if ($msg = $prev->store()) {
        return $msg;
      }
      
      return null;
    }
    
    // On positionne l'entrée de la suivante affectation à l'affectation que l'on supprime 
    $next = $this->_ref_next;
    if (isset($next->_id)) {
      $next->entree = $entree;
      
      if ($msg = $next->store()) {
        return $msg;
      }
      
      return null;
    } 
  }

  function store() {
    $this->completeField("sejour_id");
    $create_affectations = false;
    $sejour = $this->loadRefSejour();
    $sejour->loadRefPatient();
    
    // Conserver l'ancien objet avant d'enregistrer
    $old = new CAffectation();
    if ($this->_id) {
      $old->load($this->_id);
      $old->loadRefsAffectations();
    }
    
    // Gestion du service_id
    if ($this->lit_id) {
      $this->service_id = $this->loadRefLit()->loadRefChambre()->service_id;
    }
    
    // Gestion des UFs
    $this->makeUF();

    // Si c'est une création d'affectation, avec ni une précédente ni une suivante,
    // que le séjour est relié à une grossesse, et que le module maternité est actif,
    // alors il faut créer les affectations des bébés.
    if (CModule::getActive("maternite")  &&
        !is_numeric($this->_ref_sejour->_ref_patient->nom) &&
        $this->_ref_sejour->grossesse_id &&
        !$this->_id && !$this->_ref_prev->_id &&
        !$this->_ref_next->_id) {
      $create_affectations = true;
    }
    
    // Enregistrement standard
    if ($msg = parent::store()) {
      return $msg;
    }

    if ($create_affectations) {

      $grossesse = $this->_ref_sejour->loadRefGrossesse();
      $naissances = $grossesse->loadRefsNaissances();

      $sejours = CMbObject::massLoadFwdRef($naissances, "sejour_enfant_id");
      
      foreach ($sejours as $_sejour) {
        $_affectation = new CAffectation;
        $_affectation->lit_id = $this->lit_id;
        $_affectation->sejour_id = $_sejour->_id;
        $_affectation->parent_affectation_id = $this->_id;
        $_affectation->entree = CMbDT::dateTime();
        $_affectation->sortie = $this->sortie;
        if ($msg = $_affectation->store()) {
          return $msg;
        }
      }
    }
    
    // Pas de problème de synchro pour les blocages de lits
    if (!$this->sejour_id || $this->_no_synchro) {
      return $msg;
    }
    
    // Modification de la date d'admission et de la durée de l'hospi
    $this->load($this->affectation_id);

    if ($old->_id) {
      $this->_ref_prev = $old->_ref_prev;
      $this->_ref_next = $old->_ref_next;
    }
    else {
      $this->loadRefsAffectations();
    }
     
    $changeSejour = 0;
    $changePrev   = 0;
    $changeNext   = 0;
    
    $prev = $this->_ref_prev;
    $next = $this->_ref_next;

    // Mise à jour vs l'entrée
    if (!$prev->_id) {
      if ($this->entree != $sejour->_entree) {
        $field = $sejour->entree_reelle ? "entree_reelle" : "entree_prevue";
        $sejour->$field = $this->entree;
        $changeSejour = 1;
      }
    }
    elseif ($this->entree != $prev->sortie) {
      $prev->sortie = $this->entree;
      $changePrev = 1;
    }
    
    // Mise à jour vs la sortie
    if (!$next->_id) {
      if ($this->sortie != $sejour->_sortie) {
        $field = $sejour->sortie_reelle ? "sortie_reelle" : "sortie_prevue";
        $sejour->$field = $this->sortie;
        $changeSejour = 1;
      }
    }
    elseif ($this->sortie != $next->entree) {
      $next->entree = $this->sortie;
      $changeNext = 1;
    }

    if ($changePrev) {
      $prev->store();
    }
    
    if ($changeNext) {
      $next->store();
    }

    if ($changeSejour) {
      $sejour->_no_synchro = 1;
      $sejour->updateFormFields();
      if ($msg = $sejour->store()) {
        return $msg;
      }
    }
    
    return $msg;
  }

  function loadRefLit($cache = 1) {
    return $this->_ref_lit = $this->loadFwdRef("lit_id", $cache);
  }

  function loadRefSejour($cache = 1) {
    return $this->_ref_sejour =  $this->loadFwdRef("sejour_id", $cache);
  }
  
  function loadRefService($cache = 1) {
    return $this->_ref_service = $this->loadFwdRef("service_id", $cache);
  }

  function loadRefsFwd($cache = 1) {
    $this->loadRefLit($cache);
    $this->loadView();
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
  
  function loadRefsAffectationsEnfant() {
    return $this->_ref_affectations_enfant = $this->loadBackRefs("affectations_enfant");
  }
  
  function getPerm($permType) {
    $lit = $this->loadRefLit();
    $sejour = $this->loadRefSejour();

    return $lit->getPerm($permType) && $sejour->getPerm($permType);
  }

  function checkDaysRelative($date) {
    if ($this->entree and $this->sortie) {
      $this->_entree_relative = CMbDT::daysRelative("$date 10:00:00", $this->entree);
      $this->_sortie_relative = CMbDT::daysRelative("$date 10:00:00", $this->sortie);
    }
  }

  function collide($aff) {
    return CMbRange::collides($this->entree, $this->sortie, $aff->entree, $aff->sortie);
  }

  function loadMenu($date, $listTypeRepas = null){
    $this->_list_repas[$date] = array();
    $repas =& $this->_list_repas[$date];
    if (!$listTypeRepas) {
      $listTypeRepas = new CTypeRepas;
      $order = "debut, fin, nom";
      $listTypeRepas = $listTypeRepas->loadList(null,$order);
    }

    $where                   = array();
    $where["date"]           = $this->_spec->ds->prepare(" = %", $date);
    $where["affectation_id"] = $this->_spec->ds->prepare(" = %", $this->affectation_id);
    foreach ($listTypeRepas as $keyType=>$typeRepas) {
      $where["typerepas_id"] = $this->_spec->ds->prepare("= %",$keyType);
      $repasDuJour = new CRepas;
      $repasDuJour->loadObject($where);
      $repas[$keyType] = $repasDuJour;
    }
  }
  
  function loadRefParentAffectation() {
    return $this->_ref_parent_affectation = $this->loadFwdRef("parent_affectation_id", true);
  }
  
  function loadRefPraticien($cache = true) {
    return $this->_ref_praticien = $this->loadFwdRef("praticien_id", $cache);
  }
    
  function makeUF() {
    $this->completeField("lit_id", "uf_hebergement_id", "uf_soins_id", "uf_medicale_id");
    $this->loadRefsAffectations();
    $this->loadRefLit()->loadRefChambre()->loadRefService();
    
    $lit       = $this->_ref_lit;
    $chambre   = $lit->_ref_chambre;
    $service   = $chambre->_ref_service;
    $sejour    = $this->loadRefSejour();
    $prev_aff  = $this->_ref_prev;
    $modified  = false;
    $ljoin = array("uf" => "uf.uf_id = affectation_uf.uf_id");

    if (!$this->uf_hebergement_id) {
      $affectation_uf = new CAffectationUniteFonctionnelle();
      $where = array("uf.type" => "= 'hebergement'");
 
      if (!$affectation_uf->uf_id) {
        $where["object_id"]    = "= '$lit->_id'";
        $where["object_class"] = "= 'CLit'";
        $affectation_uf->loadObject($where, null, null, $ljoin);
        
        if (!$affectation_uf->_id) {
          $where["object_id"]    = "= '$chambre->_id'";
          $where["object_class"] = "= 'CChambre'";
          $affectation_uf->loadObject($where, null, null, $ljoin);

          if (!$affectation_uf->_id) {
            $where["object_id"]    = "= '$service->_id'";
            $where["object_class"] = "= 'CService'";
            $affectation_uf->loadObject($where, null, null, $ljoin);
          }
        }
      }

      $this->uf_hebergement_id = $affectation_uf->uf_id;      
    }
    
    if (!$this->uf_soins_id) {
      $affectation_uf = new CAffectationUniteFonctionnelle();
      $where = array("uf.type" => "= 'soins'");
      
      if (!$prev_aff->_id) {
        $affectation_uf->uf_id = $sejour->uf_soins_id;
      }
      
      if (!$affectation_uf->uf_id) {
        $where["object_id"]    = "= '$service->_id'";
        $where["object_class"] = "= 'CService'";
        $affectation_uf->loadObject($where, null, null, $ljoin);
      }
      
      $this->uf_soins_id = $affectation_uf->uf_id;
    }
    
    if (!$this->uf_medicale_id) {
      $affectation_uf = new CAffectationUniteFonctionnelle();
      $where = array("uf.type" => "= 'medicale'");
      
      if (!$prev_aff->_id) {
        $affectation_uf->uf_id = $sejour->uf_medicale_id;
      }
      
      if (!$affectation_uf->uf_id) {
        $praticien = new CMediusers();
        if (!$this->praticien_id) {
          $praticien = $this->loadRefSejour()->loadRefPraticien();
        }
        else {
          $praticien = $this->loadRefPraticien();
          $praticien->loadRefFunction();
        }
        $where["object_id"]    = "= '$praticien->_id'";
        $where["object_class"] = "= 'CMediusers'";
        $affectation_uf->loadObject($where, null, null, $ljoin);
        
        if (!$affectation_uf->_id) {
          $function = $praticien->_ref_function;
          $where["object_id"]    = "= '$function->_id'";
          $where["object_class"] = "= 'CFunctions'";
          $affectation_uf->loadObject($where, null, null, $ljoin);
        }
      }
      
      $this->uf_medicale_id = $affectation_uf->uf_id;
    }
  }
  
  function loadRefUfs($cache = 1) {
    $this->loadRefUFHebergement($cache);
    $this->loadRefUFMedicale($cache);
    $this->loadRefUFSoins($cache);
  }
  
  function loadRefUFHebergement($cache = true) {
    return $this->_ref_uf_hebergement = $this->loadFwdRef("uf_hebergement_id", $cache);
  }
  
  function loadRefUFMedicale($cache = true) {
    return $this->_ref_uf_medicale = $this->loadFwdRef("uf_medicale_id", $cache);
  }
  
  function loadRefUFSoins($cache = true) {
    return $this->_ref_uf_soins = $this->loadFwdRef("uf_soins_id", $cache);
  }
  
  function getUFs(){
    $this->loadRefUfs();

    return array(
      "hebergement" => $this->_ref_uf_hebergement,
      "medicale"    => $this->_ref_uf_medicale,
      "soins"       => $this->_ref_uf_soins,
    );
  }
  
  function getMovementType() {
    $sejour = $this->loadRefSejour();
    
    return $sejour->getMovementType();
  }
  
  function isLast() {
    $this->loadRefsAffectations();
    
    return !$this->_ref_next->_id;
  }
}
?>
