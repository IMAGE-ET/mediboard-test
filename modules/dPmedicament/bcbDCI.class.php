<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbDCI extends CBcbObject {

  // Spéciale DCI
  var $code_classe        = null;
  var $libelle_classe     = null;
  var $libelle_classe_maj = null;
  var $flag               = null;
  var $code_maj           = null;
  
  // Objects references
  var $_refs_produits = null;
  
  
  // Constructeur
  function CBcbDCI(){
    $this->distClass = "BCBDci";
    parent::__construct();
  }
 
  // Chargement d'un DCI a partir de son code
  function load($code){
    $ds = CBcbObject::getDataSource();
    $query = "SELECT * FROM `CLASSES_THERAPEUTIQUES_DCI` WHERE `CODE_CLASSE` = '$code';";
    $result = reset($ds->loadList($query));
    if($result){
      $this->code_classe        = $result["CODE_CLASSE"];
      $this->libelle_classe     = $result["LIBELLE_CLASSE"];
      $this->libelle_classe_maj = $result["LIBELLE_CLASSE_MAJ"];
      $this->flag               = $result["FLAG"];
      $this->code_maj           = $result["CODE_MAJ"];
    }
  }
  
  // Fonction qui retourne la liste des DCI qui comment par $search
  function searchDCI($search, $limit = 100){
    $this->distObj->Search($search, 0, $limit, 1);
    return $this->distObj->gtabDCI;
  }
  
  
  function searchProduitsByType($livretTherapeutique){
    global $g;
    if($livretTherapeutique){
      $this->distObj->LivretTherapeutique = $g;
    }
    $this->distObj->SearchMedicamentType($this->libelle_classe, 0);
    $this->_ref_produits = $this->distObj->gTabPdtType;
  }
  
  // Chargement des produits de la DCI
  function loadRefsProduits(){
    $ds = CBcbObject::getDataSource();
    $query = "SELECT * FROM `CLASSES_THERAPEUTIQUES_PRODUITS` WHERE `CODE_CLASSE` = '$this->code_classe';";
    $result = $ds->loadList($query);
    foreach($result as $key => $produit){
      $prod = new CProduit();
      $prod->load($produit["CODE_CIP"]);
      $this->_refs_produits[$produit["CODE_CIP"]] = $prod;
    }
  }
  
}

?>