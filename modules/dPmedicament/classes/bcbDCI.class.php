<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
  
  // Fonction qui retourne la liste des DCI qui commence par $search
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
	
	
	/*
	 * Recherche de produits à partir du nom partiel de DCI
	 */
	function searchProduits($NomClasse, $limit = 100, $search_by_cis = 1) {
		$tokens = explode(" ", $NomClasse);
    $NomClasse = $tokens[0];
    unset($tokens[0]);
		
		$this->distObj->TabProduit = array();
		
		$NomClasse = strtoupper($NomClasse);
    $NomClasse = str_replace("'", "''", $NomClasse);
    if ($NomClasse){
		  $Sql = "SELECT PRODUITS_IFP.Code_CIP, PRODUITS_IFP.Libelle_Produit, PRODUITS_IFP.LIBELLELONG";
	    if($this->distObj->LivretTherapeutique > 0){
	      $Sql .= ", LivretTherapeutique.Commentaire";
	    } else {
	      $Sql .= ", ''";
	    }
	    $Sql .= ", PRODUITS_IFP.Produit_supprime, PRODUITS_IFP.Hospitalier, 
	                     IDENT_PRODUITS.Code_UCD, IDENT_PRODUITS.LIBELLE_ABREGE, IDENT_PRODUITS.DOSAGE, IDENT_FORMES_GALENIQUES.Libelle_Forme_Galenique, IDENT_PRODUITS.CODECIS, CLASSES_THERAPEUTIQUES_DCI.Libelle_Classe ";
	    $Sql .= " FROM (PRODUITS_IFP, ";
      $Sql .= "CLASSES_THERAPEUTIQUES_PRODUITS, ";  
      $Sql .= "CLASSES_THERAPEUTIQUES_DCI ";
      if ($this->distObj->LivretTherapeutique > 0) {
      	$Sql .= ", LivretTherapeutique) ";
			} else {
        $Sql .= ") ";
      }
		  $Sql .= " LEFT JOIN IDENT_PRODUITS ON IDENT_PRODUITS.Code_CIP = PRODUITS_IFP.Code_CIP ";
			$Sql .= " LEFT JOIN IDENT_FORMES_GALENIQUES ON IDENT_FORMES_GALENIQUES.Code_Forme_Galenique =  IDENT_PRODUITS.Code_Forme_Galenique ";
				
	    if ($this->distObj->LivretTherapeutique > 0){
	      $Sql .= "WHERE IDENT_PRODUITS.Code_CIP=LivretTherapeutique.CodeCIP ";
	      $Sql .= "AND LivretTherapeutique.CodeEtablissement=" . $this->distObj->LivretTherapeutique . " ";
			  $Sql .= "AND IDENT_PRODUITS.Code_CIP = ";
	    } else {
	    	 $Sql .= "WHERE IDENT_PRODUITS.Code_CIP = ";
	    }                    
			$Sql .= "CLASSES_THERAPEUTIQUES_PRODUITS.Code_CIP AND CLASSES_THERAPEUTIQUES_PRODUITS.Code_Classe = ";
	    $Sql .= "CLASSES_THERAPEUTIQUES_DCI.Code_Classe ";
	    foreach($tokens as $_token){
	      $Sql .= "AND ((PRODUITS_IFP.LIBELLELONG Like '%$_token%') OR (IDENT_PRODUITS.DOSAGE LIKE '%$_token%')) ";
	    }
		  $Sql .= "AND PRODUITS_IFP.Code_CIP = IDENT_PRODUITS.Code_CIP ";
	    $Sql .= "AND CLASSES_THERAPEUTIQUES_DCI.Libelle_Classe_MAJ Like '%$NomClasse%'";
	    $Sql .= " ORDER BY PRODUITS_IFP.Libelle_Produit";
			$Sql .= " LIMIT $limit";      
	  
	    //modif oracle!
	    $Sql = strtoupper($Sql);
	    if($this->distObj->ClasseSQL->TypeDB==3){ 
	      $Sql=str_replace("CLASSES_THERAPEUTIQUES_PRODUITS","CLASSES_THERAPEUTIQUES_PRODUIT",$Sql);
	    }
	   
	    $result = $this->distObj->ClasseSQL->sql_query($Sql,$this->distObj->LinkDBProd) or die("<BR>erreur DB BCBDci/SearchMedicamentType 1: " . $this->distObj->ClasseSQL->sql_error($this->distObj->LinkDBProd));
	    while($row=$this->distObj->ClasseSQL->sql_fetch_array($result)) {
	      $Temp=new Type_Produit();
	      $Temp->CodeCIP=$row[0];
	      $Temp->Libelle=$row[1];
	      $Temp->LibelleLong=$row[2];
	      $Temp->Commentaire=strtoupper($row[3]);
	      $Temp->DateSupp=$row[4];
	      $Temp->Hospitalier=$row[5];
	      $Temp->CodeUCD=$row[6];
	      if($row[7]){
	        $Temp->ucd_view = "$row[7] $row[8]";
	      } else {
	        $Temp->ucd_view = $Temp->Libelle;
	      }
	      $Temp->forme_galenique = $row[9];
	      $Temp->code_cis = $row[10];
	      $Temp->dci = $row[11];
	      $key = (($search_by_cis == 1) && ($Temp->code_cis || $Temp->CodeUCD)) ? ($Temp->code_cis ? $Temp->code_cis : "_$Temp->CodeUCD" ) : $Temp->CodeCIP;
	      $this->distObj->TabProduit[$key] = $Temp;
	    }
    }
    return $this->distObj->TabProduit; 
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