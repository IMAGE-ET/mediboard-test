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
class CSpMalade extends CSpObject {  
  // DB Table key
  var $malnum = null;

  // DB Fields : see getSpecs();
  
	function CSpMalade() {
	  $this->CSpObject("t_malade", "malnum");    
	}
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = "CPatient";
    return $spec;
  }
 	
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["malfla"] = "str length|1"    ; /* Flag                         */
    $specs["malnum"] = "numchar length|6"; /* No de client                 */
    $specs["malnom"] = "str maxLength|50"; /* Nom                          */
    $specs["malpre"] = "str maxLength|30"; /* Prenom                       */
    $specs["malpat"] = "str maxLength|50"; /* Nom de jeune fille           */
    $specs["datnai"] = "str maxLength|10"; /* Date de naissance jj/mm/aaaa */
    $specs["vilnai"] = "str maxLength|30"; /* Lieu de naissance            */
    $specs["depnai"] = "str maxLength|02"; /* departement de naissance     */
    $specs["nation"] = "str maxLength|03"; /* Nationalite                  */
    $specs["sexe"  ] = "str length|1"    ; /* Sexe                         */
    $specs["rannai"] = "numchar length|1"; /* Rang de Naissance            */
    $specs["relign"] = "str maxLength|02"; /* Religion                     */
    $specs["malru1"] = "str maxLength|25"; /* Adresse 1                    */
    $specs["malru2"] = "str maxLength|25"; /* Adresse 2                    */
    $specs["malcom"] = "str maxLength|25"; /* Commune                      */
    $specs["malpos"] = "str maxLength|05"; /* Code postal                  */
    $specs["malvil"] = "str maxLength|25"; /* Ville                        */
    $specs["maltel"] = "str maxLength|14"; /* No telephone                 */
    $specs["malpro"] = "str maxLength|30"; /* Profession                   */
    /*   PERSONNE A PREVENIR  No 1  */
    $specs["perso1"] = "str maxLength|30"; /* Identite                     */
    $specs["prvad1"] = "str maxLength|25"; /* Adresse                      */
    $specs["prvil1"] = "str maxLength|30"; /* Code postal et ville         */
    $specs["prtel1"] = "str maxLength|14"; /* No telephone                 */
    $specs["malie1"] = "str maxLength|20"; /* Lien avec le malade          */
    /*   PERSONNE A PREVENIR  No 2  */
    $specs["perso2"] = "str maxLength|30"; /* Identite                     */
    $specs["prvad2"] = "str maxLength|25"; /* Adresse                      */
    $specs["prvil2"] = "str maxLength|30"; /* Code postal et ville         */
    $specs["prtel2"] = "str maxLength|14"; /* No telephone                 */
    $specs["malie2"] = "str maxLength|20"; /* Lien avec le malade          */
    /*                              */
    $specs["malnss"] = "str maxLength|13"; /* No matricule du malade       */
    $specs["clenss"] = "str maxLength|02"; /* Cle matricule                */
    $specs["parent"] = "str maxLength|02"; /* Rang beneficiaire            */
    /*            ASSURE :          */
    $specs["assnss"] = "str maxLength|13"; /* No matricule                 */
    $specs["nsscle"] = "str maxLength|02"; /* Cle matricule                */
    $specs["assnom"] = "str maxLength|50"; /* Nom                          */
    $specs["asspre"] = "str maxLength|30"; /* Prenom                       */
    $specs["asspat"] = "str maxLength|50"; /* Nom de jeune fille           */
    $specs["assru1"] = "str maxLength|25"; /* Adresse 1                    */
    $specs["assru2"] = "str maxLength|25"; /* Adresse 2                    */
    $specs["asscom"] = "str maxLength|25"; /* Commune                      */
    $specs["asspos"] = "str maxLength|05"; /* Code postal                  */
    $specs["assvil"] = "str maxLength|25"; /* Ville                        */
    $specs["datmaj"] = "str length|18"   ; /* Date de derniere mise a jour */
    
    return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->malnum - $this->malnom, $this->malpre - $this->datnai";
  }
  
  function mapTo() {
    $patient = new CPatient();
    $patient->nom    = $this->malnom;
    $patient->prenom = $this->malpre;
    $patient->naissance = mbDateFromLocale($this->datnai);
    return $patient;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!is_a($mbObject, $mbClass)) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $patient = $mbObject;
        
    $this->malnum = str_pad($this->loadLatestId()+1, 6, "0", STR_PAD_LEFT);
    $this->malnom = $this->makeString($patient->nom, 20);
    $this->malpre = $this->makeString($patient->prenom, 10);
    $this->datnai = mbDateToLocale($patient->naissance);
  }
  
  function loadLatestId() {
    $ds =& $this->_spec->ds;
    $query = "SELECT MAX(`$this->_tbl_key`) FROM `$this->_tbl`";
    return $ds->loadResult($query);
  }  
}

?>