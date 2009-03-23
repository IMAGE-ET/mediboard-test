<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2249 $
* @author Romain Ollivier
*/

CAppUI::requireModuleClass("sherpa", "spObject");

/**
 * Classe du malade sherpa
 */
class CSpOuvDro extends CSpObject {  
  // DB Table key
  var $numdos = null;

  // DB Fields : see getProps();
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CSejour';
    $spec->table   = 't_ouvdro';
    $spec->key     = 'numdos';
    return $spec;
  }
 	
  function getProps() {
    $specs = parent::getProps();

    $specs["drofla"]  = "str length|1"       ; /* Flag                         */
    $specs["referan"] = "str length|9"      ; /* Annee de Reference (AAA Annnnn)  */
    $specs["numdos"]  = "numchar length|6"    ; /* No de dossier (Annnnn)           */
//    $specs["typmal"]  = "bool"            ; /* Type de malade (Externe/Hospit.) */
    $specs["malnum"]  = "numchar length|6"    ; /* No de malade                     */
//    $specs["admiss"]  = "bool"            ; /* Mode d'Admission                 */
//    $specs["accide"]  = "bool"            ; /* Accident  O/N                    */
//    $specs["acctie"]  = "bool"            ; /*          Cause par 1 Tiers O/N   */
//    $specs["acctra"]  = "bool"            ; /*          Du Travail        O/N   */
//    $specs["datacc"]  = "str length|10"   ; /* Date de l'accident               */
//    $specs["numacc"]  = "str maxLength|9" ; /* No de l'accident                 */
//    $specs["oridro"]  = "bool"            ; /* Origine des Droits               */
//    $specs["datval"]  = "str length|10"   ; /* Date Validite CAS ou etabl. PEC  */
//    $specs["codorg"]  = "str maxLength|3" ; /* Code organ. delivr. CAS ou PEC   */
//    $specs["grdreg"]  = "str maxLength|2" ; /* Grand regime                     */
//    $specs["caisse"]  = "str maxLength|3" ; /* Organisme gestionnaire           */
//    $specs["centre"]  = "str maxLength|3" ; /* centre gestionnaire              */
//    $specs["cleorg"]  = "bool"            ; /* Cle organisme                    */
//    $specs["regime"]  = "str maxLength|3" ; /* Regime                           */
//    $specs["risque"]  = "str maxLength|2" ; /* Nature d'assurance               */
//    $specs["datcho"]  = "str length|10"   ; /* Date cessation activite          */
//    $specs["nomemp"]  = "str maxLength|30"; /* Nom employeur                    */
//    $specs["adremp"]  = "str maxLength|30"; /* Adresse employeur                */
//    $specs["vilemp"]  = "str maxLength|30"; /* Code postal et ville employeur   */
//    $specs["matemp"]  = "str maxLength|9" ; /* No matricule employeur           */
//    $specs["forgou"]  = "bool"            ; /* Forfait gouvernemental  O/N      */
//    $specs["art115"]  = "bool"            ; /* Article 115 (pension guerre) O/N */
//    $specs["num115"]  = "str maxLength|5" ; /* No pensionne de guerre           */
//    $specs["exoner"]  = "bool"            ; /* Justification exoneration        */
//    $specs["pfjamo"]  = "bool"            ; /* FJ Prise en charge AMO           */
//    $specs["exof18"]  = "bool"            ; /* Exo de la PAT (18 Euros)         */
//    $specs["mutuel"]  = "str length|12"   ; /* Code mutuelle                    */
//    $specs["numadh"]  = "str maxLength|9" ; /* No d'adherent                    */
//    $specs["pratra"]  = "str maxLength|5" ; /* Medecin traitant                 */
    $specs["prares"]  = "str maxLength|3" ; /* Responsable                      */
//    $specs["dnaiss"]  = "str length|10"   ; /* Date Naiss. Selon Secu jj/mm/aaaa*/
//    $specs["ghscod"]  = "num"             ; /* Code GHS                         */
    $specs["datmaj"]  = "str length|19"   ; /* DateTime de derniere mise a jour */
    
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
    
    $sejour = $mbObject;
    $sejour->loadRefsFwd();

    $typeMatrix = array(
      "ambu" => "Z",
			"exte" => "E",
			"comp" => "H",
			"seances" => "H",
			"SSR" => "H",
			"psy" => "H",
		);
    
    $this->drofla = $typeMatrix[$sejour->type];
    
    // Numéro de dossier amélioré
    $this->referan = mbTransformTime(null, $sejour->entree_prevue, "%Y") . substr($this->numdos, 1);
    
    // Malade
    $idMalade = CSpObjectHandler::getId400For($sejour->_ref_patient);
    $this->malnum = $idMalade->id400;
    
    // Praticien responsable
    $idPraticien = CSpObjectHandler::getId400For($sejour->_ref_praticien);
    $this->prares = $idPraticien->id400;
    
    // Mise à jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>