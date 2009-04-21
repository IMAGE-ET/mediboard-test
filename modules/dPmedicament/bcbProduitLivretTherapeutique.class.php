<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision$
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbProduitLivretTherapeutique extends CBcbObject {

  var $group_id = null;
  var $code_cip = null;
  var $prix_hopital = null;
  var $prix_ville = null;
  var $date_prix_ville = null;
  var $date_prix_hopital = null;
  var $code_interne = null;
  var $commentaire = null;
  
  var $_ref_produit = null;
  
  
  // Constructeur
  function CBcbProduitLivretTherapeutique(){
    $this->distClass = "BCBLivretTherapeutique";
    parent::__construct();
    
    // Connexion a la base de Gestion
    $result = $this->distObj->InitConnexionGestion(CBcbObject::$objDatabaseGestion->LinkDB, CBcbObject::$TypeDatabaseGestion);
  }
  
  /**
   * Compte le nombre de produit du livret courant dans la base médicament
   * @return int Le nombre de produit
   */
  static function countProduitsMed() {
    global $g;
    $query = "SELECT COUNT(*) FROM `LIVRETTHERAPEUTIQUE` WHERE `CODEETABLISSEMENT` = '$g'";
    $ds = CBcbObject::getDataSource();
    return $ds->loadResult($query);
  }
  
  /**
   * Compte le nombre de produit du livret courant dans la base de gestion
   * @return int Le nombre de produit
   */
  static function countProduitsGes(){
    global $g;
    $query = "SELECT COUNT(*) FROM `LIVRETTHERAPEUTIQUE` WHERE `CODEETABLISSEMENT` = '$g'";
    $ds = CSQLDataSource::get("bcbges");
    return $ds->loadResult($query);
  }
  
  static function purgeProduits() {
    global $g;
    $query = "DELETE FROM `LIVRETTHERAPEUTIQUE` WHERE `CODEETABLISSEMENT` = '$g'";
    $ds = CSQLDataSource::get("bcbges");
    $ds->exec($query);
    $ds = CBcbObject::getDataSource();
    $ds->exec($query);
    return $ds->affectedRows();
  }
  
  static function getProduits($order = 'CODECIP', $limit = null, $full_mode = true) {
  	global $g;
    $ds = CBcbObject::getDataSource();
    $query = "SELECT `CODECIP` FROM `LIVRETTHERAPEUTIQUE` WHERE `CODEETABLISSEMENT` = '$g'";
    if ($order) $query .= " ORDER BY $order";
    if ($limit) $query .= " LIMIT $limit";
    $results = $ds->loadList($query);
    $list = array();
    foreach ($results as $plt) {
    	$produitLivretTherapeutique = new CBcbProduitLivretTherapeutique();
      $produitLivretTherapeutique->load($plt['CODECIP']);
      $produitLivretTherapeutique->loadRefProduit($full_mode);
      $list[] = $produitLivretTherapeutique;
    }
    return $list;
  }
  
  function load($code_cip) {
    global $g;
    $ds = CBcbObject::getDataSource();
    $query = "SELECT * FROM `LIVRETTHERAPEUTIQUE` WHERE `CODEETABLISSEMENT` = '$g' AND `CODECIP` = '$code_cip';";
    if($result = $ds->loadHash($query)){
      $this->group_id          = $result["CODEETABLISSEMENT"];
      $this->code_cip          = $result["CODECIP"];
      $this->prix_hopital      = $result["PRIXHOPITAL"];
      $this->prix_ville        = $result["PRIXVILLE"];
      $this->date_prix_hopital = $result["DATEPRIXHOPITAL"];
      $this->date_prix_ville   = $result["DATEPRIXVILLE"];
      $this->code_interne      = $result["CODEINTERNE"];
      $this->commentaire       = $result["COMMENTAIRE"];
      // return true si le produit existe => chargement
      return true;
    }
    // return false si le produit n'existe pas
    return false;
  }
  
  function updateFormFields(){
    $this->date_prix_hopital = mbDateFromLocale("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$1-$2-$3", $this->date_prix_hopital);
    $this->date_prix_ville = mbDateFromLocale("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$1-$2-$3", $this->date_prix_ville);
  }
  
  function updateDBFields(){
    $this->distObj->DatePrixHopital = mbDateToLocale("/(\d{4})-(\d{2})-(\d{2})/", "$3/$2/$1", $this->distObj->DatePrixHopital);
	  $this->distObj->DatePrixVille = mbDateToLocale("/(\d{4})-(\d{2})-(\d{2})/", "$3/$2/$1", $this->distObj->DatePrixVille);
  }
    
  function loadRefProduit($full_mode = true){
    $this->_ref_produit = new CBcbProduit();
    if($this->code_cip){
      $this->_ref_produit->load($this->code_cip, $full_mode);
    }
  }
  
  function synchronize(){
    $this->distObj->synchronize();
  }
} 

?>