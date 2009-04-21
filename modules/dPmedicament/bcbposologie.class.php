<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("bcbObject.class.php");

class CBcbPosologie extends CBcbObject {

  var $quantite1          = null;
  var $quantite2          = null;
  var $code_prise1        = null;
  var $code_prise2        = null;
  var $code_indication    = null;
  var $code_nature        = null;
  var $code_voie          = null;
  var $code_profil        = null;
  var $code_unite_prise   = null;
  var $code_duree1        = null;
  var $code_duree2        = null;
  var $code_duree3        = null;
  var $code_moment        = null;
  var $code_terrain       = null;
  var $code_age1          = null;
  var $code_age2          = null;    
	var $p_kg               = null;        
  var $adequation_up1_up2 = null;
	var $combien1           = null;           
	var $combien2           = null;
  var $tous_les           = null; 
 	var $pendant1           = null;
	var $pendant2           = null;          
	var $maximum            = null;          
	var $maximum_pds        = null;       
	var $nombre_unites      = null;     
	var $code_posologie     = null;   
	var $code_cip           = null;        
	var $code_par           = null;        
	var $commentaire        = null; 
	
  // Others fields
  var $_code_prise1       = null;
  var $_code_prise2       = null;
  var $_code_indication   = null;
  var $_code_terrain      = null;
  var $_code_nature       = null;
  var $_code_voie         = null;
  var $_code_profil       = null;
  var $_code_unite_prise  = null;
  var $_code_duree1       = null;
  var $_code_duree2       = null;
  var $_code_duree3       = null;
  var $_code_moment       = null;
  var $_code_age1         = null;
  var $_code_age2         = null;
  
  var $_view              = null;
  
  // Constructeur
  function CBcbPosologie(){
    $this->distClass = "BCBPosologie";
    parent::__construct();
  }
 
  // Chargement d'une posologie a partir d'un code CIP
  function load($cip, $numPoso = null){
    //mbTrace($numPoso, $cip);
    /*
    // Test des fonctions intégrées BCB
    $posologie &= $this->distObj;
    $result = $posologie->chargementDetail($cip, $numPoso);
    if($result <= 0) {
      return;
    }
    $this->quantite1 = $posologie->ChargementGetData(1);
    $this->quantite2 = $posologie->ChargementGetData(2);
    $this->code_prise1 = $posologie->ChargementGetData(3);
    $this->getValeur($this->code_prise1, "_code_prise1", "LIBELLE_SPECIF", "CODE_SPECIF",  "POSO_SPECIF_PRISE");
    $this->code_prise2 = $posologie->ChargementGetData(4);
    $this->getValeur($this->code_prise2, "_code_prise2", "LIBELLE_SPECIF", "CODE_SPECIF", "POSO_SPECIF_PRISE");
    $this->code_indication = $posologie["CODE_INDICATION"];
    $this->getValeur($this->code_indication, "_code_indication", "LIBELLE_INDICATION", "CODE_INDICATION", "POSO_INDICATIONS");
    $this->code_indication = $posologie["CODE_NATURE"];
    $this->getValeur($this->code_nature, "_code_nature", "LIBELLE_NATURE", "CODE_NATURE", "POSO_NATURES");
    $this->code_voie = $posologie["CODE_VOIE"];
    $this->getValeur($this->code_voie, "_code_voie", "LIBELLE_VOIE", "CODE_VOIE", "POSO_VOIES");
    $this->code_profil = $posologie["CODE_PROFIL"];
    $this->getValeur($this->code_profil, "_code_profil", "LIBELLE_PROFIL", "CODE_PROFIL", "POSO_PROFILS");
    $this->code_unite_prise = $posologie["CODE_UNITE_DE_PRISE"];
    $this->getValeur($this->code_unite_prise, "_code_unite_prise", "LIBELLE_UNITE_DE_PRISE,LIBELLE_UNITE_DE_PRISE_PLURIEL", "CODE_UNITE_DE_PRISE", "POSO_UNITES_PRISE");
    $this->code_duree1 = $posologie["CODE_DUREE1"];
    $this->getValeur($this->code_duree1, "_code_duree1", "LIBELLE_DUREE", "CODE_DUREE", "POSO_DUREES");
    $this->code_duree2 = $posologie["CODE_DUREE2"];
    $this->getValeur($this->code_duree2, "_code_duree2", "LIBELLE_DUREE", "CODE_DUREE", "POSO_DUREES");
    $this->code_duree3 = $posologie["CODE_DUREE3"];
    $this->getValeur($this->code_duree3, "_code_duree3", "LIBELLE_DUREE", "CODE_DUREE", "POSO_DUREES");
    $this->code_moment = $posologie["CODE_MOMENT"];
    $this->getValeur($this->code_moment, "_code_moment", "LIBELLE_MOMENT", "CODE_MOMENT", "POSO_MOMENTS");
    $this->code_terrain = $posologie["CODE_TERRAIN"];
    $this->getValeur($this->code_terrain, "_code_terrain", "TERRAIN,AGE1,AGE2,CAGE1,CAGE2,POIDS1,POIDS2", "CODE_TERRAIN", "POSO_PRODUITS_TERRAIN");
    $this->code_age1 = $this->_code_terrain["CAGE1"].$this->_code_terrain["AGE1"];
    $this->getValeur($this->code_age1, "_code_age1", "POIDS_G,POIDS_F,TAILLE_G,TAILLE_F,SURFACE_G,SURFACE_F", "CODE_AGE", "POSO_TABLEAU");
    $this->code_age2 = $this->_code_terrain["CAGE2"].$this->_code_terrain["AGE2"];
    $this->getValeur($this->code_age2, "_code_age2", "POIDS_G,POIDS_F,TAILLE_G,TAILLE_F,SURFACE_G,SURFACE_F", "CODE_AGE", "POSO_TABLEAU");
    $this->p_kg               = $posologie["P_KG"];
    $this->adequation_up1_up2 = $posologie["ADEQUATION_UP1_UP2"];
    $this->combien1           = $posologie["COMBIEN1"];
    $this->combien2           = $posologie["COMBIEN2"];
    $this->tous_les           = $posologie["TOUS_LES"];
    $this->pendant1           = $posologie["PENDANT1"];
    $this->pendant2           = $posologie["PENDANT2"];
    $this->maximum            = $posologie["MAXIMUM"];
    $this->maximum_pds        = $posologie["MAXIMUM_PDS"];
  
    $this->nombre_unites      = $posologie["NOMBRE_UNITES"];
    $this->code_posologie     = $posologie["NO_POSO"];
    $this->code_cip           = $posologie["CODE_CIP"];
  
    $this->code_par           = $posologie["CODE_PAR"];
    $this->commentaire        = $posologie["COMMENTAIRE"];*/
    /*$result = $this->distObj->DecodageDetail(1, 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 6, 0, 40, 2004, 0, 0, 1, 3419544, 1, "P", "Boire immédiatement après dissolution complète dans un grand verre d'eau, de préférence au cours des repas.
*1 comprimé effervescent (200 mg), à renouveler si besoin au bout de 6 heures. En cas de douleurs ou de fièvre plus intense, 2 comprimés effervescents à 200 mg, à renouveler si besoin au bout de 6 heures. Dans tous les cas ne pas dépasser 6 comprimés effervescents par jour (1200 mg par jour).
- Les prises systématiques permettent d'éviter les oscillations de fièvre ou de douleur. Elles doivent être espacées d'au moins 6 heures.
- Boire immédiatement après dissolution complète du comprimé effervescent dans un grand verre d'eau, de préférence au cours des repas.
- Au cours des traitements prolongés, contrôler la formule sanguin, les fonctions hépatiques et rénales.");*/
    
    // Chargement des posologies du produit
    $ds = CBcbObject::getDataSource();
    if($numPoso){
      $query = "SELECT * FROM `POSO_PRODUITS` WHERE `CODE_CIP` = '$cip' AND `NO_POSO` = '$numPoso';";
    } else {
      $query = "SELECT * FROM `POSO_PRODUITS` WHERE `CODE_CIP` = '$cip' ORDER BY `NO_POSO` ASC;";
    }
    
    $posologies = $ds->loadList($query);
    $posologie = reset($posologies);
    
    if (!$posologie){
      return;
    }
    
    if ($posologie){
      $this->quantite1 = $posologie["QUANTITE1"];
      $this->quantite2 = $posologie["QUANTITE2"];
     
      $this->code_prise1 = $posologie["CODE_PRISE1"];
      $this->getValeur($this->code_prise1, "_code_prise1", "LIBELLE_SPECIF", "CODE_SPECIF",  "POSO_SPECIF_PRISE");
  
      $this->code_prise2 = $posologie["CODE_PRISE2"];
      $this->getValeur($this->code_prise2, "_code_prise2", "LIBELLE_SPECIF", "CODE_SPECIF", "POSO_SPECIF_PRISE");
 
      $this->code_indication = $posologie["CODE_INDICATION"];
      $this->getValeur($this->code_indication, "_code_indication", "LIBELLE_INDICATION", "CODE_INDICATION", "POSO_INDICATIONS");

      $this->code_indication = $posologie["CODE_NATURE"];
      $this->getValeur($this->code_nature, "_code_nature", "LIBELLE_NATURE", "CODE_NATURE", "POSO_NATURES");
      
      $this->code_voie = $posologie["CODE_VOIE"];
      $this->getValeur($this->code_voie, "_code_voie", "LIBELLE_VOIE", "CODE_VOIE", "POSO_VOIES");
      
      $this->code_profil = $posologie["CODE_PROFIL"];
      $this->getValeur($this->code_profil, "_code_profil", "LIBELLE_PROFIL", "CODE_PROFIL", "POSO_PROFILS");
      
      $this->code_unite_prise = $posologie["CODE_UNITE_DE_PRISE"];
      $this->getValeur($this->code_unite_prise, "_code_unite_prise", "LIBELLE_UNITE_DE_PRISE,LIBELLE_UNITE_DE_PRISE_PLURIEL", "CODE_UNITE_DE_PRISE", "POSO_UNITES_PRISE");
      
      $this->code_duree1 = $posologie["CODE_DUREE1"];
      $this->getValeur($this->code_duree1, "_code_duree1", "LIBELLE_DUREE", "CODE_DUREE", "POSO_DUREES");
      
      $this->code_duree2 = $posologie["CODE_DUREE2"];
      $this->getValeur($this->code_duree2, "_code_duree2", "LIBELLE_DUREE", "CODE_DUREE", "POSO_DUREES");
      
      $this->code_duree3 = $posologie["CODE_DUREE3"];
      $this->getValeur($this->code_duree3, "_code_duree3", "LIBELLE_DUREE", "CODE_DUREE", "POSO_DUREES");
      
      $this->code_moment = $posologie["CODE_MOMENT"];
		  $this->getValeur($this->code_moment, "_code_moment", "LIBELLE_MOMENT", "CODE_MOMENT", "POSO_MOMENTS");
     
		  $this->code_terrain = $posologie["CODE_TERRAIN"];
		  $this->getValeur($this->code_terrain, "_code_terrain", "TERRAIN,AGE1,AGE2,CAGE1,CAGE2,POIDS1,POIDS2", "CODE_TERRAIN", "POSO_PRODUITS_TERRAIN");
		  
		  //$this->code_age1 = $this->_code_terrain["CAGE1"].$this->_code_terrain["AGE1"];
		  //$this->getValeur($this->code_age1, "_code_age1", "POIDS_G,POIDS_F,TAILLE_G,TAILLE_F,SURFACE_G,SURFACE_F", "CODE_AGE", "POSO_TABLEAU");
      
		  //$this->code_age2 = $this->_code_terrain["CAGE2"].$this->_code_terrain["AGE2"];
		  //$this->getValeur($this->code_age2, "_code_age2", "POIDS_G,POIDS_F,TAILLE_G,TAILLE_F,SURFACE_G,SURFACE_F", "CODE_AGE", "POSO_TABLEAU");
      
      $this->p_kg               = $posologie["P_KG"];
		  $this->adequation_up1_up2 = $posologie["ADEQUATION_UP1_UP2"];
		  $this->combien1           = $posologie["COMBIEN1"];
		  $this->combien2           = $posologie["COMBIEN2"];
		  $this->tous_les           = $posologie["TOUS_LES"];
		  
		  $this->pendant1           = $posologie["PENDANT1"];
		  $this->pendant2           = $posologie["PENDANT2"];
		  $this->maximum            = $posologie["MAXIMUM"];
		  $this->maximum_pds        = $posologie["MAXIMUM_PDS"];
		  
		  $this->nombre_unites      = $posologie["NOMBRE_UNITES"];
		  $this->code_posologie     = $posologie["NO_POSO"];
		  $this->code_cip           = $posologie["CODE_CIP"];
		  
		  $this->code_par           = $posologie["CODE_PAR"];
		  $this->commentaire        = $posologie["COMMENTAIRE"];
    }
    $this->updateFormFields();
  }
  
 
  // Recupere la valeur d'un code
  function getValeur($champ, $_champ, $champs, $nom_champ_base2, $table){
    $ds = CBcbObject::getDataSource();
    $query = "SELECT * FROM `$table` WHERE `$nom_champ_base2` = '$champ';";
    $object = null;
 
    $ds->loadObject($query, $object);
    // Si un objet a ete trouvé
    if($object){  
      $listChamps = explode(",", $champs);
      // Si un seul champ est présent
      if(count($listChamps) == 1){
        $this->$_champ = $object->$listChamps[0];
      }
      // Si plusieus champs
      if(count($listChamps) > 1){
        $tab = array();
        foreach($listChamps as $key => $_champ_temp){
          $tab[$_champ_temp] = $object->$listChamps[$key];
        }
        $this->$_champ = $tab;
      }
    }  
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "";
    $this->_view .= $this->quantite1;
    if($this->quantite2) {
      $this->_view .= " à $this->quantite2";
    }
    $this->_view .= " ".$this->_code_unite_prise["LIBELLE_UNITE_DE_PRISE"];
    if($this->p_kg) {
      $this->_view .= "/kg";
    }
    if($this->code_moment) {
      $this->_view .= " $this->_code_moment";
    }
    if($this->code_duree1) {
      if($this->combien1) {
        $this->_view .= " $this->combien1";
        if($this->combien2) {
          $this->_view .= " à $this->combien2";
        }
        $this->_view .= " fois";
      }
      if($this->tous_les <= 1) {
        $this->_view .= " par";
      } elseif($this->tous_les > 1) {
        $this->_view .= " tous les $this->tous_les";
      }
      $this->_view .= " $this->_code_duree1";
    }
    if($this->code_prise1) {
      $this->_view .= " $this->_code_prise1";
    }
    if($this->code_duree2) {
      if($this->pendant1) {
        $this->_view .= " pendant $this->pendant1";
      }
      if($this->pendant2) {
        $this->_view .= " à $this->pendant2";
      }
      $this->_view .= " $this->_code_duree2";
    }
  }
  
  function checkTerrain($patient) {
    $patient = new CPatient();
    // Age
    $listAges = array(
			"T"  => array("nom" => "Tout Age"           , "min" => 0,  "max" => 0  , "sexe" => "" ),
			"AE" => array("nom" => "Adulte - Enfant"    , "min" => 2,  "max" => 70 , "sexe" => "" ),
			"EN" => array("nom" => "Enfant - Nourrisson", "min" => 0,  "max" => 13 , "sexe" => "" ),
			"AD" => array("nom" => "Adolescent"         , "min" => 13, "max" => 18 , "sexe" => "" ),
			"A"  => array("nom" => "Adulte"             , "min" => 18, "max" => 70 , "sexe" => "" ),
			"AH" => array("nom" => "Adulte Homme"       , "min" => 18, "max" => 70 , "sexe" => "H"),
			"AF" => array("nom" => "Adulte Femme"       , "min" => 18, "max" => 70 , "sexe" => "F"),
			"E"  => array("nom" => "Enfant"             , "min" => 2 , "max" => 13 , "sexe" => "" ),
			"N"  => array("nom" => "Nourrisson"         , "min" => 0 , "max" => 2  , "sexe" => "" ),
			"S"  => array("nom" => "Sujet âgé"          , "min" => 70, "max" => 70 , "sexe" => "" ),
    );
    $listDurees = array(
      "J" => array("nom" => "jour(s)"   , "multiple" => 1),
      "S" => array("nom" => "semaine(s)", "multiple" => 7),
      "M" => array("nom" => "mois"      , "multiple" => 30),
      "A" => array("nom" => "année(s)"  , "multiple" => 365),
    );
    $terrain = $listAges[$this->_code_terrain["TERRAIN"]];
    if(!$this->_code_terrain["AGE1"] && !$this->_code_terrain["AGE2"]) {
      // On prend directement le terrain
      $this->_code_terrain["AGE1"] = $terrain["min"];
      $this->_code_terrain["CAGE1"] = "A";
      $this->_code_terrain["AGE2"] = $terrain["max"];
      $this->_code_terrain["CAGE2"] = "A";
    }
    // Age de comparaison à AGE1 et AGE2
    switch($this->_code_terrain["CAGE1"]) {
      case "J":
        $age1 = $patient->evalAgeJours();
        break;
      case "S":
        $age1 = $patient->evalAgeSemaines();
        break;
      case "M":
        $age1 = $patient->evalAgeMois();
        break;
      default:
        $age1 = $patient->evalAge();
    }
    if($this->_code_terrain["CAGE1"] == $this->_code_terrain["CAGE2"]) {
      $age2 = $age1;
    } else {
      switch($this->_code_terrain["CAGE2"]) {
        case "J":
          $age2 = $patient->evalAgeJours();
          break;
        case "S":
          $age2 = $patient->evalAgeSemaines();
          break;
        case "M":
          $age2 = $patient->evalAgeMois();
          break;
        default:
          $age2 = $patient->evalAge();
      }
    }
    if($this->_code_terrain["AGE1"]) {
      if($this->_code_terrain["AGE2"]) {
        if($this->_code_terrain["AGE1"] == $this->_code_terrain["AGE2"]) {
          // Patient de plus de Age1
          if($this->_code_terrain["AGE1"] > $age1) {
            return false;
          }
        } else {
          // Patient de Age1 à Age2
          if($this->_code_terrain["AGE1"] < $age1 || $this->_code_terrain["AGE2"] > $age2) {
            return false;
          }
        }
      } else {
        // Patient de Age1
          if($this->_code_terrain["AGE1"] != $age1) {
            return false;
          }
      }
    } elseif($this->_code_terrain["AGE2"]) {
      // Patient de moins de Age2
          if($this->_code_terrain["AGE2"] < $age2) {
            return false;
          }
    }
    
    // Sexe
    $sexe = false;
    if($terrain["sexe"] == "M") {
      if($patient->sexe != "m") {
        return false;
      }
    } elseif($terrain["sexe"] == "F") {
      if($patient->sexe == "m") {
        return false;
      }
    }

    // Poids
    if($this->_code_terrain["POIDS1"] || $this->_code_terrain["POIDS2"]) {
      $patient->loadRefConstantesMedicales();
      if($poids = $patient->_ref_constantes_medicales->poids) {
        if($this->_code_terrain["POIDS1"]) {
          if($this->_code_terrain["POIDS2"]) {
            if($this->_code_terrain["POIDS1"] == $this->_code_terrain["POIDS2"]) {
              // Patient de plus de Poids1
              if($this->_code_terrain["POIDS1"] > $poids) {
                return false;
              }
            } else {
              // Patient de Poids1 à Poids2
              if($this->_code_terrain["POIDS1"] > $poids || $this->_code_terrain["POIDS2"] < $poids) {
                return false;
              }
            }
          } else {
            // Patient de Poids1
              if($this->_code_terrain["POIDS1"] != $poids) {
                return false;
              }
          }
        } elseif($this->_code_terrain["POIDS2"]) {
          // Patient de moins de Poids2
              if($this->_code_terrain["POIDS2"] < $poids) {
                return false;
              }
        }
      }
    }
    
    return true;
  }

}

?>
