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
class CSpUrgDro extends CSpObject {  
  // DB Table key
  var $numdos = null;

  // DB Fields : see getSpecs();
  
	function CSpOuvDro() {
	  $this->CSpObject("t_urgdro", "numdos");    
	}
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = "CSejour";
    return $spec;
  }
 	
  function getSpecs() {
    $specs = parent::getSpecs();
        
    $specs["urgfla"]  = "str length|1"    ; /* Flag                             */
    $specs["referan"] = "str length|9"    ; /* Annee de Reference (AAA Annnnn)  */
    $specs["numdos"]  = "numchar length|6"; /* No de dossier (Annnnn)           */
    $specs["malnum"]  = "numchar length|6"; /* No de malade                     */
    $specs["datarr"]  = "str length|19"   ; /* Date et Heure Arrivee            */
    $specs["datdep"]  = "str length|19"   ; /* Date et Heure Départ             */
    $specs["urprov"]  = "str length|2"    ; /* Provenance                       */
    $specs["urmtra"]  = "str length|1"    ; /* Mode de transport (arrivee)      */

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

//    $specs["exoner"]  = "bool"            ; /* Justification exoneration        */
//    $specs["mutuel"]  = "str maxLength|12"; /* Code mutuelle                    */
//    $specs["opsion"]  = "str maxLength|2" ; /* Code Option                      */
//    $specs["dnaiss"]  = "str length|10"   ; /* Date Naiss. Selon Secu jj/mm/aaaa*/

//    $specs["nomemp"]  = "str maxLength|30"; /* Nom employeur                    */
//    $specs["adremp"]  = "str maxLength|30"; /* Adresse employeur                */
//    $specs["vilemp"]  = "str maxLength|30"; /* Code postal et ville employeur   */
//    $specs["matemp"]  = "str maxLength|9" ; /* No matricule employeur           */
//    $specs["telemp"]  = "str maxLength|14"; /* Forfait gouvernemental  O/N      */

    $specs["urdest"]  = "str length|1"    ; /* Destination                      */
    $specs["urmuta"]  = "str length|1"    ; /* Cause Transfert   (Depart)       */
    $specs["urtype"]  = "str length|1"    ; /* Type d'Urgence                   */
    $specs["urtrau"]  = "str length|1"    ; /* Urgence Traumato                 */
    $specs["urgems"]  = "str length|1"    ; /* Code GEMSA                       */
    $specs["urccmu"]  = "str length|1"    ; /* Code CCMU                        */
//    $specs["exoatu"]  = "str length|1"    ; /* Exoneration Forfait ATU          */
    
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
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $sejour = $mbObject;
    $sejour->loadRefsFwd();
    
    $this->urgfla = "1";
    
    // Malade
    $idMalde = CSpObjectHandler::getId400For($sejour->_ref_patient);
    $this->malnum = $idMalde->id400;
        
    // Mise à jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>