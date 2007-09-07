<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2249 $
* @author Romain Ollivier
*/

global $AppUI;
require_once($AppUI->getModuleClass("sherpa", "spObject"));

/**
 * Classe du malade sherpa
 */
class CSpSejMed extends CSpObject {  
  // DB Table key
  var $numdos = null;

  // DB Fields : see getSpecs();
  
	function CSpSejMed() {
	  $this->CSpObject("t_sejmed", "numdos");    
	}
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = "CSejour";
    return $spec;
  }
 	
  function getSpecs() {
    $specs = parent::getSpecs();

//    $specs["sejfla"] = "bool"         ; /* Flag                        */
    $specs["numdos"] = "numchar length|6" ; /* Numero de dossier           */
    $specs["malnum"] = "numchar length|6" ; /* Numero de malade            */
    $specs["datent"] = "str length|10"; /* Date et heure d'entree      */
    $specs["litcod"] = "str length|4" ; /* Lit                         */
    $specs["sercod"] = "str length|2" ; /* Service                     */
    $specs["pracod"] = "str length|3" ; /* Code interne du praticien   */
    $specs["datsor"] = "str length|10"; /* Date et heure de sortie     */

    /* Nature Interruption Sejour  */
    $specs["depart"] = "enum list|D|T|S|E|R|P";
//              /* D(eces) , T(ransfert > 48)  */
//              /* S(ortie), E(xterieur < 48)  */
//              /* R(etour Trf > 48), P(sy)    */
//    $specs["etapro"] = "str length|2" ; /* Code Etabl. Ext. Prov.      */
//    $specs["etades"] = "str length|2" ; /* Code Etabl. Ext. Dest.      */

    return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->numdos ($this->malnum)";
  }
  
  function mapTo() {
    // Load patient
    $malade = new CSpMalade();
    $malade->load($this->malnum); 
    if (!$patient = $malade->loadMbObject()) {
      throw new Exception("Malade '$this->malnum' is not linked to a Mb Patient");
    }
    
    $sejour = new CSejour();
    $sejour->patient_id = $patient->_id;
    return $sejour;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!is_a($mbObject, $mbClass)) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $sejour = $mbObject;
    $sejour->loadRefsFwd();
    $idMalde = CSpObjectHandler::getId400For($sejour->_ref_patient);
    
    $this->malnum = $idMalde->id400;
    
    // Date d'entr�e
    $entree = mbGetValue($sejour->entree_reelle, $sejour->entree_prevue); 
    $this->datent = mbDateToLocale(mbDate($entree));
    
    // Date de sortie, on ne passe pas les sorties pr�vues
    $sortie = $sejour->sortie_reelle;
    if ($sortie) {
      $this->datsor = mbDateToLocale(mbDate($sortie));
    }
    
    // Code du praticien
    $idPraticien = CSpObjectHandler::getId400For($sejour->_ref_praticien);
    $this->pracod = $idPraticien->id400;

    // Codes du lit et services
    $sejour->loadRefsAffectations();
    $affectation = $sejour->_ref_last_affectation;
    $affectation->loadRefLit();
    $lit = $affectation->_ref_lit;
    $lit->loadCompleteView();
    
    $idChambre = CSpObjectHandler::getId400For($lit->_ref_chambre);
    $this->litcod = $idChambre->id400;

    $idService = CSpObjectHandler::getId400For($lit->_ref_chambre->_ref_service);
    $this->sercod = $idService->id400;
    
    // Mode de sortie 
    $this->depart = "";
    if ($sejour->sortie_reelle) {
      if ($sejour->mode_sortie == "normal"   ) $this->depart = "S";
      if ($sejour->mode_sortie == "deces"    ) $this->depart = "D";
      if ($sejour->mode_sortie == "transfert") $this->depart = $sejour->_duree_reelle >= 2 ? "T" : "E";
    }
  }
}

?>