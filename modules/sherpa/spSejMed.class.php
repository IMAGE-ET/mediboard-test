<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

CAppUI::requireModuleClass("sherpa", "spObject");

/**
 * Classe du malade sherpa
 */
class CSpSejMed extends CSpObject {  
  // DB Table key
  var $numdos = null;

  // DB Fields : see getProps();

  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CSejour';
    $spec->table   = 't_sejmed';
    $spec->key     = 'numdos';
    return $spec;
  }
 	
  function getProps() {
    $specs = parent::getProps();

    $specs["sejfla"]  = "str length|1"    ; /* Flag                        */
    $specs["numdos"] = "numchar length|6" ; /* Numero de dossier           */
    $specs["malnum"] = "numchar length|6" ; /* Numero de malade            */
    $specs["datent"] = "str length|19"; /* Date et heure d'entree      */
    $specs["litcod"] = "str maxLength|4" ; /* Lit                         */
    $specs["sercod"] = "str length|2" ; /* Service                     */
    $specs["pracod"] = "str length|3" ; /* Code interne du praticien   */
    $specs["datsor"] = "str length|19"; /* Date et heure de sortie     */

    /* Nature Interruption Sejour  */
    $specs["depart"] = "enum list|D|T|S|E|R|P";
//              /* D(eces) , T(ransfert > 48)  */
//              /* S(ortie), E(xterieur < 48)  */
//              /* R(etour Trf > 48), P(sy)    */
//    $specs["etapro"] = "str length|2" ; /* Code Etabl. Ext. Prov.      */
    $specs["etades"] = "str length|2" ; /* Code Etabl. Ext. Dest.      */

    $specs["datmaj"]  = "str length|19"   ; /* DateTime de derniere mise a jour */
    return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->numdos ($this->malnum)";
  }
  
  function mapTo() {
    global $g;

    // Etablissement
    $sejour = new CSejour();
    $sejour->group_id = $g;
    
    // Patient
    $malade = new CSpMalade();
    $malade->load($this->malnum);
    if ($patient = $malade->loadMbObject()) {
      $sejour->patient_id = $patient->_id;
    }
    
    // Praticien
    $praticien = CSpObjectHandler::getMbObjectFor("CMediusers", $this->pracod);
    $sejour->praticien_id = $praticien->_id;
    
    
    // Entrée et sortie
    $this->datent = trim(preg_replace("/(\d{2})\/(\d{2})\/(\d{2})/", "$1/$2/20$3", $this->datent));
    $sejour->entree_prevue = mbDateFromLocale($this->datent);
    $this->datsor = trim(preg_replace("/(\d{2})\/(\d{2})\/(\d{2})/", "$1/$2/20$3", $this->datsor));
    $sejour->sortie_prevue = mbDateFromLocale($this->datsor);
    
    
    if ($this->datsor) {
      $sejour->entree_reelle = $sejour->entree_prevue;
      $sejour->sortie_reelle = $sejour->sortie_prevue;
    }
    
    // Sercod
    $duree_prevue = 2;
    switch ($this->sercod) {
      case "ZT":
      $sejour->type = "ambu";
      $sejour->zt = "1";
      $duree_prevue = 0;
      break;

      case "SR":
      $sejour->type = "comp";
      $sejour->reanimation = 1;
      $duree_prevue = 3;
      break;

      case "PB":
      $sejour->type = "ambu";
      $sejour->facturable = 1;
      $duree_prevue = 0;
      break;
      
      case "EX":
      $sejour->type = "ambu";
      $sejour->facturable = 0;
      $duree_prevue = 0;
      break;
      
      default:
	    switch (substr($this->sercod, 0, 1)) {
	      case "A" : 
	      $sejour->type = "ambu"; 
        $duree_prevue = 1;
	      break;
	      
	      case "2" : 
	      $sejour->type = "comp"; 
	      $duree_prevue = 3;
	      break;
	    }

	    $presta = substr($this->sercod, 1, 1);
	    if ($presta == "2") {
	      $sejour->chambre_seule = 0;
	    }
	    else {
	      $sejour->chambre_seule = 1;
        $mbpresta = CSpObjectHandler::getMbObjectFor("CPrestation", $presta);
        
        $sejour->prestation_id = $mbpresta->_id;
	    }
    }
    
    if (!$sejour->sortie_prevue) {
      $sejour->sortie_prevue = mbDateTime("+$duree_prevue DAYS", $sejour->entree_prevue);
    }
    
    switch ($this->depart) {
      case "S" : $sejour->mode_sortie = "normal"; break;
      case "D" : $sejour->mode_sortie = "deces" ; break;
      case "T" : 
      case "E" : $sejour->mode_sortie = "transfert"; break;
    }
    
    return $sejour;
  }
  
  function isConcernedBy(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
      return false;
    }
    
    return $mbObject->type != "urg" || $mbObject->zt;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $this->sejfla = "A";
    
    $sejour = $mbObject;
    $sejour->loadRefsFwd();
    $idMalade = CSpObjectHandler::getId400For($sejour->_ref_patient);
    
    $this->malnum = $idMalade->id400;
    
    // Date d'entrée
    $entree = mbGetValue($sejour->entree_reelle, $sejour->entree_prevue); 
    $this->datent = $this->importDateTime($entree);
    
    // Date de sortie, on ne passe pas les sorties prévues
    $sortie_prevue = in_array($sejour->type, array("ambu", "exte")) ? $sejour->sortie_prevue : null;
    $sortie = mbGetValue($sejour->sortie_reelle ,$sortie_prevue);
    $this->datsor = $this->importDateTime($sortie);
    
    // Code du praticien
    $idPraticien = CSpObjectHandler::getId400For($sejour->_ref_praticien);
    $this->pracod = $idPraticien->id400;
    
    // Codes d'admission/prestation    
    $sercod = "";
    if ($sejour->type == "exte") {
      $sercod = $sejour->facturable ? "PB" : "EX";
    } 
    else {
      $sercod = $sejour->type == "comp" ? "2" : "A";

      if (!$sejour->chambre_seule) {
        $sercod .= "2";
      } 
      else {
	      $presta = new CPrestation();
	      $presta->load($sejour->prestation_id);
        $idPresta = CSpObjectHandler::getId400For($presta);
        $sercod .= mbGetValue($idPresta->id400, "1");
      }
      
      if ($sejour->reanimation) {
        $sercod = "SR";
      }
      
      if ($sejour->zt) {
        $sercod = "ZT";
      }
    }

    $this->sercod = $sercod;
        
    // Codes du lit 
    $sejour->loadRefsAffectations();
    $affectation = $sejour->_ref_first_affectation;
    $affectation->loadRefLit();
    $idLit = CSpObjectHandler::getId400For($affectation->_ref_lit);
    $this->litcod = $idLit->id400;

    // Si externe, valeur prioritaire
    if ($sejour->type == "exte") {
      $this->litcod = $sejour->facturable ? "BLOC" : "AMEX";
    }
    
    // Mode de sortie 
    $this->depart = "";
    $this->etades = "";
    if ($sejour->sortie_reelle) {
      if ($sejour->mode_sortie == "normal"   ) $this->depart = "S";
      if ($sejour->mode_sortie == "deces"    ) $this->depart = "D";

      if ($sejour->mode_sortie == "transfert") {
        $this->depart = "T";
        $idEtab = CSpObjectHandler::getId400For($sejour->_ref_etabExterne);
        $this->etades = $idEtab->id400;
      }
    }
    
    // Mise à jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>