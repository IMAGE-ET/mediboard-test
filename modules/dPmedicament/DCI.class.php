<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

require_once("bcbObject.class.php");


class CDCI extends CBcbObject {

  // Générale
  var $distObj        = null;
 
  // Spéciale DCI
  var $code_classe        = null;
  var $libelle_classe     = null;
  var $libelle_classe_maj = null;
  var $flag               = null;
  var $code_maj           = null;
  
  // Objects references
  var $_refs_produits = null;
  var $_refs_DCI      = null;
  
  
  // Constructeur
  function CDCI(){
    $this->initBCBConnection();
    // Creation de la connexion
    $this->distObj = new BCBDci();
    $result = $this->distObj->InitConnexion(CBcbObject::$objDatabase->LinkDB, CBcbObject::$TypeDatabase); 
  }
 
  // Chargement d'un DCI a partir de son code
  function load($code){
    $ds = CSQLDataSource::get("bcb");
    $query = "SELECT * FROM `classes_therapeutiques_dci` WHERE `CODE_CLASSE` = '$code';";
    $result = reset($ds->loadList($query));
    if($result){
      $this->code_classe        = $result["CODE_CLASSE"];
      $this->libelle_classe     = $result["LIBELLE_CLASSE"];
      $this->libelle_classe_maj = $result["LIBELLE_CLASSE_MAJ"];
      $this->flag               = $result["FLAG"];
      $this->code_maj           = $result["CODE_MAJ"];
    }
  }
  
  
  // Chargement des produits de la DCI
  function loadRefsProduits(){
    $ds = CSQLDataSource::get("bcb");
    $query = "SELECT * FROM `classes_therapeutiques_produits` WHERE `CODE_CLASSE` = '$this->code_classe';";
    $result = $ds->loadList($query);
    foreach($result as $key => $produit){
      // A faire: chargement du produit (des que la classe )
      $prod = new CProduit();
      $prod->load($produit["CODE_CIP"]);
      $this->_refs_produits[$produit["CODE_CIP"]] = $prod;
    }
  }
  
}
