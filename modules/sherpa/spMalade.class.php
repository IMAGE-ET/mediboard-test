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
class CSpMalade extends CSpObject {  
  // DB Table key
  var $malnum = null;

  // DB Fields : see getProps();
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CPatient';
    $spec->table   = 't_malade';
    $spec->key     = 'malnum';
    return $spec;
  }
 	
  function getProps() {
    $specs = parent::getProps();
    $specs["malfla"] = "str length|1"    ; /* Flag                         */
    $specs["malnum"] = "numchar length|6"; /* No de client                 */
    $specs["malnom"] = "str maxLength|50"; /* Nom                          */
    $specs["malpre"] = "str maxLength|30"; /* Prenom                       */
    $specs["malpat"] = "str maxLength|50"; /* Nom de jeune fille           */
    $specs["datnai"] = "str maxLength|10"; /* Date de naissance jj/mm/aaaa */
    $specs["vilnai"] = "str maxLength|30"; /* Lieu de naissance            */
//    $specs["depnai"] = "str maxLength|02"; /* departement de naissance     */
    $specs["nation"] = "str maxLength|03"; /* Nationalite                  */
    $specs["sexe"  ] = "enum list|F|M"   ; /* Sexe                         */
    $specs["malnss"] = "str maxLength|13"; /* No matricule du malade       */
    $specs["clenss"] = "str maxLength|02"; /* Cle matricule                */
    $specs["parent"] = "str maxLength|02"; /* Rang beneficiaire            */
    $specs["rannai"] = "enum list|1|2|3|4|5|6"  ; /* Rang de Naissance            */
//    $specs["relign"] = "str maxLength|02"; /* Religion                     */
    $specs["malru1"] = "str maxLength|25"; /* Adresse 1                    */
    $specs["malru2"] = "str maxLength|25"; /* Adresse 2                    */
    $specs["malcom"] = "str maxLength|25"; /* Commune                      */
    $specs["malpos"] = "str maxLength|05"; /* Code postal                  */
    $specs["malvil"] = "str maxLength|25"; /* Ville                        */
    $specs["maltel"] = "str maxLength|14"; /* No telephone                 */
    $specs["malpro"] = "str maxLength|30"; /* Profession                   */
//    /*   PERSONNE A PREVENIR  No 1  */
    $specs["perso1"] = "str maxLength|30"; /* Identite                     */
    $specs["prvad1"] = "str maxLength|25"; /* Adresse                      */
    $specs["prvil1"] = "str maxLength|30"; /* Code postal et ville         */
    $specs["prtel1"] = "str maxLength|14"; /* No telephone                 */
    $specs["malie1"] = "str maxLength|20"; /* Lien avec le malade          */
//    /*   PERSONNE A PREVENIR  No 2  */
    $specs["perso2"] = "str maxLength|30"; /* Identite                     */
    $specs["prvad2"] = "str maxLength|25"; /* Adresse                      */
    $specs["prvil2"] = "str maxLength|30"; /* Code postal et ville         */
    $specs["prtel2"] = "str maxLength|14"; /* No telephone                 */
    $specs["malie2"] = "str maxLength|20"; /* Lien avec le malade          */
//    /*            ASSURE :          */
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
    $specs["datmaj"] = "str length|19"   ; /* Date de derniere mise a jour */
    
    return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->malnum - $this->malnom, $this->malpre - $this->datnai";
  }
  
  function mapTo() {
    static $liensMatrix = array(
      "FILLE" => "enfant",
      "FILS" => "enfant",
      "ENFANT" => "enfant",
      "SA FILLE" => "enfant",
      "MARI" => "conjoint",
      "FEMME" => "conjoint",
      "EPOUX" => "conjoint",
      "EPOUSE" => "conjoint",
      "PERE" => "ascendant",
      "MERE" => "ascendant",
      "PAPA" => "ascendant",
      "MAMAN" => "ascendant",
      "PARENT" => "ascendant",
      "PARENTS" => "ascendant",
      "GRANSPARENTS" => "ascendant",
      "PARENTS" => "ascendant",
      "FRERE" => "colateral",
      "SOEUR" => "colateral",
      "ONCLE" => "colateral",
      "TANTE" => "colateral",
      "NEVEU" => "colateral",
      "NIECE" => "colateral",
      );
    
    static $sexesMatrix = array(
      "M" => "m",
      "F" => "f",
    );
    
    $patient = new CPatient();
    $patient->nom    = $this->malnom;
    $patient->prenom = mbGetValue($this->malpre, "inconnu");
    $patient->nom_jeune_fille = $this->malpat;
    $patient->naissance = mbDateFromLocale($this->datnai);
    $patient->lieu_naissance = $this->vilnai; 

    $patient->nationalite = in_array($this->nation, array("FR", "FRA")) ? "local" : "etranger";
    
    $patient->sexe      = @$sexesMatrix[$this->sexe];
    $patient->matricule = $this->importMatricule($this->malnss, $this->clenss);
    $patient->rang_beneficiaire = $this->parent;
    $patient->adresse   = "$this->malru1\n$this->malru2\n$this->malcom";
    $patient->cp        = $this->importCodePostal($this->malpos);
    $patient->ville     = $this->malvil;
    $patient->tel       = $this->ImportPhone($this->maltel);
    $patient->profession= $this->malpro;

    $patient->prevenir_nom     = $this->perso1;
    $patient->prevenir_adresse = $this->prvad1;
    $patient->prevenir_ville   = $this->prvil1;
    $patient->prevenir_tel     = $this->ImportPhone($this->prtel1);
    // VERIFIER LA MATRICE
    $patient->prevenir_parente = mbGetValue(@$liensMatrix[$this->malie1], "divers");

//    $this->perso2 = $this->makeString($patient->employeur_nom, 30);
//    $this->prvad2 = $this->makeString($patient->employeur_adresse, 25);
//    $this->prvil2 = $this->makeString($patient->employeur_ville, 30);
//    $this->prtel2 = $this->makePhone($patient->employeur_tel);
//    $this->malie2 = $this->makeString("Employ. $patient->employeur_urssaf", 20);

    $patient->assure_matricule       = $this->importMatricule($this->assnss, $this->nsscle);
    $patient->assure_nom             = $this->assnom;
    $patient->assure_prenom          = $this->asspre;
    $patient->assure_nom_jeune_fille = $this->asspat;
    $patient->assure_adresse         = "$this->assru1\n$this->assru2\n$this->asscom";
    $patient->assure_cp              = $this->importCodePostal($this->asspos);
    $patient->assure_ville           = $this->assvil;

    return $patient;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    global $AppUI;
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    static $sexesMatrix = array(
      "m" => "M",
      "f" => "F",
      "j" => "F"
    );
    
    $patient = $mbObject;
            
    $this->malfla = "A";
    $this->malnom = $this->makeString($patient->nom, 50);
    $this->malpre = $this->makeString($patient->prenom, 30);
    $this->malpat = $this->makeString($patient->nom_jeune_fille, 50);
    $this->datnai = mbDateToLocale($patient->naissance);
    $this->rannai = $patient->rang_naissance;
    $this->vilnai = $this->makeString($patient->lieu_naissance, 30);
    
    $this->nation = $patient->nationalite == "local" ? "FRA" : "";
    
    $this->sexe   = $sexesMatrix[$patient->sexe];
    
    $this->malnss = $patient->matricule ? substr($patient->matricule, 0, 13) : "";
    $this->clenss = $patient->matricule ? substr($patient->matricule, 13, 2) : "";
    $this->parent = $patient->rang_beneficiaire;
    
    $parts = split("\r\n", $patient->adresse);
    $this->malru1 = $this->makeString(@$parts[0], 25);
    $this->malru2 = $this->makeString(@$parts[1], 25);
    $this->malcom = $this->makeString(@$parts[2], 25);
    
    $this->malpos = $patient->cp;
    $this->malvil = $this->makeString($patient->ville, 25);
    $this->maltel = $this->makePhone(mbGetValue($patient->tel, $patient->tel2));
    $this->malpro = $this->makeString($patient->profession, 30);
    
    $this->perso1 = $this->makeString("$patient->prevenir_nom $patient->prevenir_prenom", 30);
    $this->prvad1 = $this->makeString($patient->prevenir_adresse, 25);
    $this->prvil1 = $this->makeString($patient->prevenir_ville, 30);
    $this->prtel1 = $this->makePhone($patient->prevenir_tel);
    $this->malie1 = $this->makeString(CAppUI::tr("CPatient.prevenir_parente.$patient->prevenir_parente"), 20);
    
    $this->perso2 = $this->makeString($patient->employeur_nom, 30);
    $this->prvad2 = $this->makeString($patient->employeur_adresse, 25);
    $this->prvil2 = $this->makeString($patient->employeur_ville, 30);
    $this->prtel2 = $this->makePhone($patient->employeur_tel);
    $this->malie2 = $patient->employeur_urssaf ? $this->makeString("Employ. $patient->employeur_urssaf", 20) : "";
    
    $this->assnss = $patient->assure_matricule ? substr($patient->assure_matricule, 0, 13) : "";
    $this->nsscle = $patient->assure_matricule ? substr($patient->assure_matricule, 13, 2) : "";
    $this->assnom = $this->makeString($patient->assure_nom, 50);
    $this->asspre = $this->makeString($patient->assure_prenom, 30);
    $this->asspat = $this->makeString($patient->assure_nom_jeune_fille, 30);

    $parts = split("\r\n", $patient->assure_adresse);
    $this->assru1 = $this->makeString(@$parts[0], 25);
    $this->assru2 = $this->makeString(@$parts[1], 25);
    $this->asscom = $this->makeString(@$parts[2], 25);
    $this->asspos = $patient->assure_cp;
    $this->assvil = $this->makeString($patient->assure_ville, 25);
    
    // Information sur doublons
    $patient->_IPP = null;
    $patient->loadIPP($this->_current_group_id);
    if ($patient->_IPP != $this->_id) {
      $this->maltel = "DOUBLON-$patient->_IPP";  
      $this->malfla = "D";  
    }
    
    $this->datmaj = mbDateToLocale(mbDateTime());    
  }
}

?>