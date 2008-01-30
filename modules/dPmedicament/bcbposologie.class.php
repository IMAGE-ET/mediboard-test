<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbPosologie extends CBcbObject {

  // Générale
  //var $distObj            = null;
 
  var $quantite1          = null;
  var $quantite2          = null;
  var $unite_prise1       = null;
  var $unite_prise2       = null;
  var $p_kg               = null;
  var $adequation_up1_up2 = null;
  var $combien1           = null;
  var $combien2           = null;
  var $tous_les           = null;
  var $code_duree1        = null;
  var $code_moment        = null;
  var $pendant1           = null;
  var $pendant2           = null;
  var $code_duree2        = null;
  var $maximum            = null; 
  var $maximum_pds        = null;
  var $code_duree3        = null;
  var $code_prise1        = null;
  var $code_prise2        = null;
  var $nombre_unites      = null;
  var $code_posologie     = null;
  var $code_cip           = null;
  var $terrain            = null;
  var $code_par           = null;
  var $commentaire        = null;
  
  // Others fields
  var $_code_prise1       = null;
  var $_code_prise2       = null;
  var $_code_indication   = null;
  
  // champs rajouté
  var $code_indication = null;
  var $code_profil = null;
  var $code_voie = null;
  var $code_nature = null;
  
  
  // Constructeur
  function CBcbPosologie(){
    //$this->initBCBConnection();
    // Creation de la connexion
    //$this->distObj = new BCBPosologie();
    //mbTrace($this->distObj);
    //$result = $this->distObj->InitConnexion(CBcbObject::$objDatabase->LinkDB, CBcbObject::$TypeDatabase); 
  }
 
  // Chargement d'une posologie a partir d'un code CIP
  function load($cip, $numPoso = null){
    // Chargement des posologies du produit
    $ds = CSQLDataSource::get("bcb");
    if($numPoso){
      $query = "SELECT * FROM `poso_produits` WHERE `CODE_CIP` = '$cip' AND `NO_POSO` = '$numPoso';";  
    } else {
      $query = "SELECT * FROM `poso_produits` WHERE `CODE_CIP` = '$cip' ORDER BY `NO_POSO` ASC;";
    }
    
    $posologie = reset($ds->loadList($query));
    
    if(!$posologie){
      return;
    }
    
    if($posologie){
      $this->quantite1          = $posologie["QUANTITE1"];
      $this->quantite2          = $posologie["QUANTITE2"];
     
      $this->code_prise1 = $posologie["CODE_PRISE1"];	  
      $this->getValeur($this->code_prise1, "_code_prise1", "LIBELLE_SPECIF", "CODE_SPECIF", "CODE_PRISE1", "poso_specif_prise");
  
      $this->code_prise2 = $posologie["CODE_PRISE2"];	  
      $this->getValeur($this->code_prise2, "_code_prise2", "LIBELLE_SPECIF", "CODE_SPECIF", "CODE_PRISE2", "poso_specif_prise");
 
      $this->code_indication = $posologie["CODE_INDICATION"];
      $this->getValeur($this->code_indication, "_code_indication", "LIBELLE_INDICATION", "CODE_INDICATION", "CODE_INDICATION", "poso_indications");
 
      /*
      $query = "SELECT * FROM `poso_indications` WHERE `CODE_INDICATION` = '$this->code_indication';";
      $ds->loadObject($query, $indication);
      if($indication){
        $this->_code_indication = $indication->LIBELLE_INDICATION;
      }
      */
      // Code Nature
      $this->code_nature     = $posologie["CODE_NATURE"];
      $query = "SELECT * FROM `poso_natures` WHERE `CODE_NATURE` = '$this->code_nature';";
      $ds->loadObject($query, $nature);
      if($nature){
        $this->_code_nature = $indication->LIBELLE_NATURE;
      }
      
      // Code Voie
      $this->code_voie       = $posologie["CODE_VOIE"];
      $query = "SELECT * FROM `poso_voies` WHERE `CODE_VOIE` = '$this->code_nature';";
      $ds->loadObject($query, $nature);
      if($nature){
        $this->_code_nature = $indication->LIBELLE_NATURE;
      }
      
      
      $this->code_profil     = $posologie["CODE_PROFIL"];

      /*
      $this->unite_prise1       = $posologie[""];
      $this->unite_prise2       = $posologie[""];
      */
      $this->unite_prise        = $posologie["CODE_UNITE_DE_PRISE"];
      
      $this->p_kg               = $posologie["P_KG"];
		  $this->adequation_up1_up2 = $posologie["ADEQUATION_UP1_UP2"];
		  $this->combien1           = $posologie["COMBIEN1"];
		  $this->combien2           = $posologie["COMBIEN2"];
		  $this->tous_les           = $posologie["TOUS_LES"];
		  $this->code_duree1        = $posologie["CODE_DUREE1"];
		  $this->code_moment        = $posologie["CODE_MOMENT"];
		  $this->pendant1           = $posologie["PENDANT1"];
		  $this->pendant2           = $posologie["PENDANT2"];
		  $this->code_duree2        = $posologie["CODE_DUREE2"];
		  $this->maximum            = $posologie["MAXIMUM"];
		  $this->maximum_pds        = $posologie["MAXIMUM_PDS"];
		  $this->code_duree3        = $posologie["CODE_DUREE3"];
		 
		  

	 
 		  
      
		  $this->nombre_unites      = $posologie["NOMBRE_UNITES"];
		  $this->code_posologie     = $posologie["NO_POSO"];
		  $this->code_cip           = $posologie["CODE_CIP"];
		  $this->terrain            = $posologie["CODE_TERRAIN"];
		  $this->code_par           = $posologie["CODE_PAR"];
		  $this->commentaire        = $posologie["COMMENTAIRE"];
    }
    mbTrace($this);
  }
  
  
  function getValeur($champ, $_champ, $nom_champ_base1, $nom_champ_base2, $champ_base, $table){
    $ds = CSQLDataSource::get("bcb");
    $query = "SELECT * FROM `$table` WHERE `$nom_champ_base2` = '$champ';";
    $object = null;
    $ds->loadObject($query, $object);
    if($object){
      $this->$_champ = $object->$nom_champ_base1;
    }  
  }
  
      
      
}
