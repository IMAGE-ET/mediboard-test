<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/


/**
 * Dossier Médical liés aux notions d'antécédents, traitements et diagnostics
 */
class CDossierMedical extends CMbMetaObject {
  // DB Fields
  var $dossier_medical_id = null;
  var $codes_cim          = null;
  
  // Form Fields
  var $_added_code_cim   = null;
  var $_deleted_code_cim = null;
  var $_codes_cim        = null;
  var $_ext_codes_cim    = null;

  // Back references
  var $_ref_antecedents = null;
  var $_ref_traitements = null;
  var $_ref_etats_dents = null;
  var $_ref_prescription = null;
  
  // Derived back references
  var $_count_antecedents = null;
  var $_count_cancelled_antecedents = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'dossier_medical';
    $spec->key   = 'dossier_medical_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["object_class"] = "enum list|CPatient|CSejour";
    $specs["codes_cim"] = "text";
    return $specs;
  }  

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["antecedents"] = "CAntecedent dossier_medical_id";
    $backProps["traitements"] = "CTraitement dossier_medical_id";
    $backProps["etats_dent"]  = "CEtatDent dossier_medical_id";
    $backProps["prescription"] = "CPrescription object_id";
    return $backProps;
  }

  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsAntecedents();
    $this->loadRefsTraitements();
  }
  
  function loadRefPrescription(){
    $this->_ref_prescription = $this->loadUniqueBackRef("prescription");  
    if($this->_ref_prescription && $this->_ref_prescription->_id){
      $this->_ref_prescription->loadRefsLinesMed();
    }
  }
  
  function loadRefObject(){  
    $this->_ref_object = new $this->object_class;
    $this->_ref_object->load($this->object_id);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    // Tokens CIM
    $this->codes_cim = strtoupper($this->codes_cim);
    $this->_codes_cim = $this->codes_cim ? explode("|", $this->codes_cim) : array();
  
    // Objets CIM
    $this->_ext_codes_cim = array();
    foreach ($this->_codes_cim as $code_cim) {
      $this->_ext_codes_cim[$code_cim] = new CCodeCIM10($code_cim, 1);
    }
  }

  function updateDBFields() {
    global $AppUI;
    
    parent::updateDBFields();
    if(!$listCodesCim = $this->codes_cim) {
      $oldDossier = new CDossierMedical();
      $oldDossier->load($this->_id);
      $listCodesCim = $oldDossier->codes_cim;
    }
    if($this->_added_code_cim) {
      $da = new CCodeCIM10($this->_added_code_cim, 1);
      if(!$da->exist){
        $AppUI->setMsg("Le code CIM saisi n'est pas valide", UI_MSG_WARNING);
      }
      if($listCodesCim && $da->exist) {
        $this->codes_cim = "$listCodesCim|$this->_added_code_cim";
      } elseif($da->exist) {
        $this->codes_cim = $this->_added_code_cim;
      }
    }
    if($this->_deleted_code_cim) {
      $arrayCodesCim = explode("|", $listCodesCim);
      CMbArray::removeValue($this->_deleted_code_cim, $arrayCodesCim);
      $this->codes_cim = implode("|", $arrayCodesCim);
    }
  }
  
  function mergeDBFields ($objects = array()/*<CMbObject>*/) {
    $codes_cim_array = CMbArray::pluck($objects, 'codes_cim');
    $codes_cim_array[] = $this->codes_cim;
    $codes_cim = implode('|', $codes_cim_array);
    $codes_cim_array = array_unique(explode('|', $codes_cim));
    CMbArray::removeValue('', $codes_cim_array);
    
    if ($msg = parent::mergeDBFields($objects)) return $msg;
    
    $this->codes_cim = implode('|', $codes_cim_array);
  }
  
  function loadView() {
    $this->loadComplete();
  }
    
  function loadRefsAntecedents($cancelled = false) {
    // Initialisation du classement
    $ant = new CAntecedent();
    $list_types = explode("|", $ant->_specs["type"]->list);
    foreach ($list_types as $type) {
      $this->_ref_antecedents[$type] = array();
    }

    $order = "type ASC";
    if (null == $antecedents = $this->loadBackRefs("antecedents", $order)) {
      return;
    }

    // Classements des antécédents
    foreach ($antecedents as $_antecedent) {
    	if ($_antecedent->annule == 0 || $cancelled)
        $this->_ref_antecedents[$_antecedent->type][$_antecedent->_id] = $_antecedent;
    }
  }
  
  function loadRefsAntecedentsByAppareil($cancelled = false){
    // Initialisation du classement
    $ant = new CAntecedent();
    $list_types = explode("|", $ant->_specs["type"]->list);
    foreach ($list_types as $appareil) {
      $this->_ref_antecedents_by_appareil[$appareil] = array();
    }
    $order = "type ASC";
    if (null == $antecedents = $this->loadBackRefs("antecedents", $order)) {
      return;
    }
    // Classements des antécédents
    foreach ($antecedents as $_antecedent) {
    	if ($_antecedent->annule == 0 || $cancelled)
        $this->_ref_antecedents_by_appareil[$_antecedent->appareil][$_antecedent->_id] = $_antecedent;
    }
  }
  
  function loadRefsEtatsDents() {
    $etat_dent = new CEtatDent();
    if ($this->_id) {
      $etat_dent->dossier_medical_id = $this->_id;
      $this->_ref_etats_dents = $etat_dent->loadMatchingList();
    }
  }

  /**
   * Compte les antécédents annulés et non-annulés
   */
  function countAntecedents(){
    
  	$antedecent = new CAntecedent();
  	$where = array();
    $where["dossier_medical_id"] = " = '$this->_id'";
  	// [tom] ??    $where["type"] = " != 'alle'";

	  $where["annule"] = " != '1'";
  	$this->_count_antecedents = $antedecent->countList($where);

	  $where["annule"] = " = '1'";
  	$this->_count_cancelled_antecedents = $antedecent->countList($where);
  }
  
  function loadRefsTraitements() {
    $order = "fin DESC, debut DESC";
    if (CAppUI::conf("dPpatients CTraitement enabled")) {
      $this->_ref_traitements = $this->loadBackRefs("traitements", $order);
    }
  }
  
  /**
   * Identifiant de dossier médical lié à l'objet fourni. 
   * Crée le dossier médical si nécessaire
   * @param $object_id ref Identifiant de l'objet
   * @param $object_class str Classe de l'objet
   * @return ref|CDossierMedical
   */
  static function dossierMedicalId($object_id, $object_class) {
    $dossier = new CDossierMedical();
    $dossier->object_id    = $object_id;
    $dossier->object_class = $object_class;
    $dossier->loadMatchingObject();
    if(!$dossier->_id) {
      $dossier->store();
    }
    return $dossier->_id;
  }

  function fillTemplate(&$template, $champ = "Patient") {
    // Antécédents
    $this->loadRefsAntecedents();
    if (is_array($this->_ref_antecedents)){
      $sAntecedents = "";

      foreach ($this->_ref_antecedents as $keyAnt => $currTypeAnt) {
        $aAntecedentsParType = array();
        $sType =  CAppUI::tr("CAntecedent.type.".$keyAnt);
        foreach ($currTypeAnt as $currAnt) {
          $sAntecedent = "&bull; ";
          if ($currAnt->date) { 
            $sAntecedent .= mbDateToLocale($currAnt->date) . " : ";
          }
          $sAntecedent .= $currAnt->rques;
          $aAntecedentsParType[] = $sAntecedent;
        }
        $sAntecedentsParType = join("<br />", $aAntecedentsParType);

        $template->addProperty("$champ - Antécédents - $sType", $sAntecedentsParType);
        
        if (count($currTypeAnt)) {
	        $sAntecedents .="<br />{$sType}<br />{$sAntecedentsParType}";
        }
      }
      $template->addProperty("$champ - Antécédents -- tous", $sAntecedents !== "" ? $sAntecedents : null);
    }
    
    $this->loadRefsAntecedentsByAppareil();
      if (is_array($this->_ref_antecedents_by_appareil)){
      $sAntecedentsApp = "";
  
      foreach ($this->_ref_antecedents_by_appareil as $keyAppAnt => $currAppAnt) {
        $aAntecedentsParApp = array();
        $sApp =  CAppUI::tr("CAntecedent.appareil.".$keyAppAnt);
        foreach ($currAppAnt as $currAppAnt) {
          $sAntecedentApp = "&bull; ";
          if ($currAppAnt->date) { 
            $sAntecedentApp .= mbDateToLocale($currAppAnt->date) . " : ";
          }
          $sAntecedentApp .= $currAppAnt->rques;
          $aAntecedentsParApp[] = $sAntecedentApp;
        }
        $sAntecedentsParApp = join("<br />", $aAntecedentsParApp);

        $template->addProperty("$champ - Antécédents - $sApp", $sAntecedentsParApp);
        
        if (count($currAppAnt)) {
	        $sAntecedentsApp .="<br />{$sType}{$sAntecedentsParApp}";
        }
      }
      //$template->addProperty("$champ - Antécédents -- tous", $sAntecedents !== "" ? $sAntecedents : null);
    }
    
    // Traitements
    $this->loadRefsTraitements();
    if (is_array($this->_ref_traitements)) {
      $sTraitements = "";
      foreach($this->_ref_traitements as $curr_trmt){
        $sTraitements.="<br /> &bull; ";
        if ($curr_trmt->fin){
          $sTraitements .= "Du ";
          $sTraitements .= mbDateToLocale($curr_trmt->debut) ;
          $sTraitements .= " au ";
          $sTraitements .= mbDateToLocale($curr_trmt->fin);
          $sTraitements .= " : ";
        }
        elseif($curr_trmt->debut){
          $sTraitements .= "Depuis le ";
          $sTraitements .= mbDateToLocale($curr_trmt->debut);
          $sTraitements .= " : ";
        }
        
        $sTraitements .= $curr_trmt->traitement;
      }
      $template->addProperty("$champ - Traitements", $sTraitements !== "" ? $sTraitements : null);
    }
    
    // Etat dentaire
    $this->loadRefsEtatsDents();
    $etats = array();
    if (is_array($this->_ref_etats_dents)) {
      foreach($this->_ref_etats_dents as $etat) {
        if ($etat->etat != null) {
          switch ($etat->dent) {
            case 10: 
            case 30: $position = 'Central haut'; break;
            case 50: 
            case 70: $position = 'Central bas'; break;
            default: $position = $etat->dent;
          }
          if (!isset ($etats[$etat->etat])) {
            $etats[$etat->etat] = array();
          }
          $etats[$etat->etat][] = $position;
        }
      }
    }
    $sEtatsDents = '';
    foreach ($etats as $key => $list) {
      sort($list);
      $sEtatsDents .= '&bull; '.ucfirst($key).' : '.implode(', ', $list).'<br />';
    }
    $template->addProperty("$champ - Etat dentaire", $sEtatsDents);
    
    
    // Codes CIM10
    $aCim10 = array();
    if ($this->_ext_codes_cim){
      foreach ($this->_ext_codes_cim as $curr_code){
        $aCim10[] = "<br />&bull; $curr_code->code : $curr_code->libelle";
      }
    }
    
    $template->addProperty("$champ - Diagnostics", join("", $aCim10));
  }
}

?>