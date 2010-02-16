<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("bcbObject.class.php");

class CBcbProduit extends CBcbObject {
  
  var $code_cip              = null;
  var $code_ucd              = null;
  var $code_cis              = null;
  var $libelle               = null;
  var $nom_commercial        = null;
  var $forme                 = null;
  var $formes                = null;
  var $nb_ucd                = null;
  var $hospitalier           = null;
  var $nom_laboratoire       = null;
  
  var $code_statut           = null;
  var $libelle_statut        = null;
  var $numero_AMM            = null;
  var $date_AMM              = null;
  var $agrement              = null;

  // Others Fields
  var $_referent             = null;
  var $_generique            = null;
  var $_supprime             = null;
  
  var $voies                      = null;
  var $libelle_presentation       = null;
  var $nb_presentation            = null;
  var $libelle_unite_presentation = null;
  var $libelle_unite_presentation_pluriel = null;
  var $libelle_conditionnement    = null;
  var $rapport_unite_prise        = null;
  var $dosages                    = null;
  var $dosage = null;
  var $inLivret = null;
  var $inT2A = true;
  var $ucd_view = null;
  var $libelle_abrege = null;
	
  // Objects references
  var $_ref_DCI              = null;
  var $_ref_UCD              = null;
  var $_ref_monographie      = null;
  var $_ref_composition      = null;
  var $_ref_economique       = null;
  var $_ref_classes_ATC      = null;
  var $_ref_classes_thera    = null;
  var $_ref_equivalents      = null;
  var $_ref_posologies       = null;
  var $_unite_dispensation   = null;
  var $_unite_administration = null;
  var $_ref_fiches_ATC       = null;
  var $_ref_ATC_2_libelle = null;
  var $_ref_ATC_2_code    = null;

  var $_prises = null;
  var $_libelle_dci = null;
	
	static $loaded = array();
	static $useCount = 0;
    
	/**
	 * Chargement avec mise en cache
	 */
	static function get($code_cip, $full_mode = true) {
		self::$useCount++;
		
    if (!CAppUI::conf("dPmedicament CBcbProduit use_cache") || !$full_mode) {
		  $produit = new CBcbProduit();
		  $produit->load($code_cip, $full_mode);
		  return $produit;
		}

		// On instancie si ca n'existe pas
		if (!isset(self::$loaded[$code_cip])) {
			self::$loaded[$code_cip] = new CBcbProduit();
 	    self::$loaded[$code_cip]->load($code_cip, $full_mode);
		} 
		
  	$produit =& self::$loaded[$code_cip];
   	return $produit->copy();
	}
	
	// Constructeur
  function CBcbProduit(){
    $this->distClass = "BCBProduit";
    parent::__construct();
  }
  
	/**
	 * Should use clone with appropriate behaviour
	 * But a bit complicated to implement
	 */
	function copy() {
	  $obj = unserialize(serialize($this));
	  return $obj;
	  
	}
  
  // Chargement d'un produit
  function load($code_cip, $full_mode = true){
    //$this->distObj->SearchInfo($code_cip);
    $this->SearchInfoEx($code_cip);
    
    $infoProduit = $this->distObj->DataInfo;

    if ($infoProduit->Charge == 1) {
      $this->code_cip        = $infoProduit->Code_CIP;
      if (isset($infoProduit->Code_Ucd)) {
        $this->code_ucd        = $infoProduit->Code_Ucd;
      }
      $this->libelle         = $infoProduit->Libelle;
      $this->libelle_long    = $infoProduit->LibelleLong;
      $this->nom_commercial  = $infoProduit->NomCommercial;
      $this->forme           = $infoProduit->Forme;
      $this->formes          = $infoProduit->Formes;
      $this->nb_ucd          = $infoProduit->Nb_UCD;
      $this->hospitalier     = $infoProduit->Hospitalier;
      $this->nom_laboratoire = $infoProduit->Laboratoire;
    }
    
   if ($full_mode){
	    $this->isInT2A();
	    $this->getGenerique();
	    $this->getReferent();
    }
    $this->isInLivret();
  }
  
  
	function SearchEx($Chaine, $Posit, $nbr, $TypeRecherche, $search_by_cis = 1, $hors_specialite = 0) {
	  
	  $tokens = explode(" ", $Chaine);
	  $Chaine = $tokens[0];
	  unset($tokens[0]);

		$lngLexico = $TypeRecherche & 256;
		$TypeRecherche = $TypeRecherche & 255;
		
		$query = "SELECT PRODUITS_IFP.Code_CIP, PRODUITS_IFP.Libelle_Produit, PRODUITS_IFP.LIBELLELONG";
	  if($this->distObj->LivretTherapeutique > 0){
		  $query .= ", LivretTherapeutique.Commentaire";
		} else {
		  $query .= ", ''";
		}
		$query .= ", PRODUITS_IFP.Produit_supprime, PRODUITS_IFP.Hospitalier, 
										 IDENT_PRODUITS.Code_UCD, IDENT_PRODUITS.LIBELLE_ABREGE, IDENT_PRODUITS.DOSAGE, IDENT_FORMES_GALENIQUES.LIBELLE_FORME_GALENIQUE, IDENT_PRODUITS.CODECIS, produits_codes_acl.CODE_FICHE ";
		$query .= " FROM (PRODUITS_IFP, IDENT_FORMES_GALENIQUES ";

		if ($this->distObj->LivretTherapeutique > 0){
		  $query .= ", LivretTherapeutique) ";
		} else {
		  $query .= ") ";
    }
		
		$query .= " LEFT JOIN IDENT_PRODUITS ON IDENT_PRODUITS.Code_CIP = PRODUITS_IFP.Code_CIP ";
		$query .= " LEFT JOIN PRODUITS_CODES_ACL ON PRODUITS_CODES_ACL.CODE_ACL = PRODUITS_IFP.CODE_CIP ";
		
		$query .= "WHERE PRODUITS_IFP.Code_Marge <> '40' ";
		$query .= "AND (IDENT_FORMES_GALENIQUES.CODE_FORME_GALENIQUE = IDENT_PRODUITS.CODE_FORME_GALENIQUE OR IDENT_PRODUITS.Code_CIP IS NULL)";
		
		if ($this->distObj->LivretTherapeutique > 0){
			$query .= " AND PRODUITS_IFP.Code_CIP=LivretTherapeutique.CodeCIP ";
			$query .= " AND LivretTherapeutique.CodeEtablissement=".$this->distObj->LivretTherapeutique." ";
		}

		if (($this->distObj->LivretTherapeutique > 0) && (strlen($Chaine) > 1) && $TypeRecherche == 0){
		  $query .= " AND (PRODUITS_IFP.LIBELLELONG Like '$Chaine%' OR LivretTherapeutique.Commentaire LIKE '$Chaine%')";	  
		} else {
			switch ($TypeRecherche)	{
				case 0:
					$query .= " AND PRODUITS_IFP.LIBELLELONG Like '";
					break;
				case 1:
					$query .= " AND PRODUITS_IFP.Code_CIP Like '";
					break;
				case 2:
					$query .= " AND IDENT_PRODUITS.Code_UCD Like '";
					break;
			}
			if ($lngLexico == 256){ 
			  $query .= "%";
		  }
			$query .= $Chaine."%'";
		}

		foreach($tokens as $_token){
		  $query .= "AND ((PRODUITS_IFP.LIBELLELONG Like '%$_token%') OR (IDENT_PRODUITS.DOSAGE LIKE '%$_token%')) ";
		}
		
		if ($hors_specialite == 0){ 
		  // Medicaments
      $query .= " AND (PRODUITS_IFP.Code_CIP Like '0%' OR PRODUITS_IFP.Code_CIP Like '1%' OR PRODUITS_IFP.Code_CIP Like '3%' OR PRODUITS_IFP.Code_CIP Like '5%')  ";
		}
		if ($hors_specialite == 1){ 
		  // Autres produits
			$query .= " AND (PRODUITS_IFP.Code_CIP Like '4%' OR PRODUITS_IFP.Code_CIP Like '6%' OR PRODUITS_IFP.Code_CIP Like '7%')";
		}
		if ($this->distObj->Supprime == 1){ 
		  $query .= " AND PRODUITS_IFP.Produit_supprime is NULL ";
	  }
		$query .= " GROUP BY PRODUITS_IFP.Code_CIP, PRODUITS_IFP.Libelle_Produit, PRODUITS_IFP.Produit_supprime, PRODUITS_IFP.Hospitalier";
		$query .= " ORDER BY PRODUITS_IFP.Libelle_Produit";
		$query = strtoupper($query);

		$result = $this->distObj->ClasseSQL->sql_query($query,$this->distObj->LinkDBProd) or die( "Erreur DB: ".$this->distObj->ClasseSQL->sql_error($this->distObj->LinkDBProd));
		$cpt=0;
		
		while($Posit>0){
			$row = $this->distObj->ClasseSQL->sql_fetch_row($result);
			$Posit--;
		}
		while($row = $this->distObj->ClasseSQL->sql_fetch_row($result)){
			if($nbr >= 0) {
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
				$Temp->code_fiche = $row[11];
				$Temp->dci = "";
				$key = (($search_by_cis == 1) && ($Temp->code_cis || $Temp->CodeUCD)) ? ($Temp->code_cis ? $Temp->code_cis : "_$Temp->CodeUCD" ) : $Temp->CodeCIP;
				$this->distObj->TabProduit[$key] = $Temp;
				$nbr--;
				$cpt++;
			}
		}
		
		$this->distObj->NbTotalLigne=count($this->distObj->TabProduit);
		return count($this->distObj->TabProduit);
	}

	function SearchInfoEx($CodeCIP)	{
		$code_labo = "";
		$code_forme = "";
		$this->distObj->DataInfo = new Type_Info();
		$this->distObj->DataInfo->Charge = 0;
	
		$query = "SELECT PRODUITS_IFP.Libelle_Produit, PRODUITS_IFP.LIBELLELONG, PRODUITS_IFP.Code_Labo_RESIP, PRODUITS_IFP.Hospitalier, PRODUITS_CODES_ACL.CODE_FICHE ";
		
		$query .= "FROM PRODUITS_IFP ";
		$query .= " LEFT JOIN PRODUITS_CODES_ACL ON PRODUITS_CODES_ACL.CODE_ACL = PRODUITS_IFP.CODE_CIP ";
		$query .= "WHERE PRODUITS_IFP.Code_CIP ='".$CodeCIP."'";
		$query = strtoupper($query);
		$result = $this->distObj->ClasseSQL->sql_query($query,$this->distObj->LinkDBProd) or die( "Erreur DB : ".$this->distObj->ClasseSQL->sql_error($this->distObj->LinkDBProd));
	
	  $this->distObj->DataInfo->Code_FICHE = "";
	
		if ($row = $this->distObj->ClasseSQL->sql_fetch_row($result)){
			$this->distObj->DataInfo->Charge = 1;
			$this->distObj->DataInfo->Code_CIP = $CodeCIP;
			$this->libelle_abrege = $this->distObj->DataInfo->Libelle = $row[0];
			$this->distObj->DataInfo->LibelleLong = $row[1];
			$code_labo = $row[2];
			$this->distObj->DataInfo->Hospitalier = $row[3];
			$this->distObj->DataInfo->Code_FICHE = $row[4];
		}
		
		if(!$this->distObj->DataInfo->Code_FICHE){
			$query = "SELECT IDENT_PRODUITS.Code_UCD, IDENT_PRODUITS.Libelle_Abrege, ";
			$query .= "IDENT_PRODUITS.Code_Forme_Galenique, IDENT_PRODUITS.Nb_UP2, IDENT_PRODUITS.LIBELLE_ABREGE, IDENT_PRODUITS.DOSAGE, IDENT_PRODUITS.CODECIS ";
			$query .= "FROM IDENT_PRODUITS ";
			$query .= "WHERE IDENT_PRODUITS.Code_CIP='".$CodeCIP."'"; 
			$query = strtoupper($query);
			$result = $this->distObj->ClasseSQL->sql_query($query,$this->distObj->LinkDBProd) or die( "Erreur DB : ".$this->distObj->ClasseSQL->sql_error($this->distObj->LinkDBProd));
		
			if ($row = $this->distObj->ClasseSQL->sql_fetch_row($result)){
				$this->distObj->DataInfo->Code_Ucd = $row[0];
				$this->distObj->DataInfo->NomCommercial = $row[1];
				$code_forme = $row[2];
				$this->distObj->DataInfo->Nb_UCD = $row[3];
				$this->libelle_abrege = $row[4];
				$this->dosage = $row[5];
				$this->code_cis = $row[6];
			}
			
			$query = "SELECT IDENT_FORMES_GALENIQUES.Libelle_Forme_Galenique,  IDENT_FORMES_GALENIQUES.Libelle_Forme_Galenique_Pluriel ";
			$query .= "FROM IDENT_FORMES_GALENIQUES WHERE IDENT_FORMES_GALENIQUES.Code_Forme_Galenique='".$code_forme."'";
			
			$query = strtoupper($query);
			$result = $this->distObj->ClasseSQL->sql_query($query,$this->distObj->LinkDBProd) or die( "Erreur DB : ".$this->distObj->ClasseSQL->sql_error($this->distObj->LinkDBProd));
			if ($row = $this->distObj->ClasseSQL->sql_fetch_row($result)){
				$this->distObj->DataInfo->Forme = $row[0];
				$this->distObj->DataInfo->Formes = $row[1];
			}
			
			$query = "SELECT LABORATOIRES.Nom_du_Laboratoire FROM LABORATOIRES WHERE LABORATOIRES.Code_Laboratoire='".$code_labo."'";
			$query = strtoupper($query);
			$result = $this->distObj->ClasseSQL->sql_query($query,$this->distObj->LinkDBProd) or die( "Erreur DB : ".$this->distObj->ClasseSQL->sql_error($this->distObj->LinkDBProd));
			if ($row = $this->distObj->ClasseSQL->sql_fetch_row($result)){
				$this->distObj->DataInfo->Laboratoire = $row[0];
			}
		}
		return 1;
	}

  function loadVoies(){
    $ds = CBcbObject::getDataSource();
    $query = "SELECT IDENT_VOIES.LIBELLEVOIE FROM `IDENT_VOIES`
							LEFT JOIN `IDENT_PRODUITS_VOIES` ON `IDENT_PRODUITS_VOIES`.`CODEVOIE` = `IDENT_VOIES`.`CODEVOIE`
							WHERE `IDENT_PRODUITS_VOIES`.`CODECIP` = '$this->code_cip';";
    $this->voies = $ds->loadColumn($query);
  }
  
  function loadLibellePresentation(){
  	// Chargement du nombre de produit dans la presentation
  	$ds = CBcbObject::getDataSource();
    $query = "SELECT * FROM `IDENT_PRODUITS` WHERE `CODE_CIP` = '$this->code_cip';";
    $_presentation = $ds->loadHash($query);
    $code_presentation_id = $_presentation['CODE_PRESENTATION'];

    $query = "SELECT * FROM `IDENT_PRESENTATIONS` WHERE `CODE_PRESENTATION` = '$code_presentation_id';";
    $libelle_presentation = $ds->loadHash($query);
    $this->libelle_presentation = $libelle_presentation['LIBELLE_PRESENTATION'];
  }
  
  function loadUnitePresentation(){
  	// Chargement du nombre de produit dans la presentation
  	$ds = CBcbObject::getDataSource();
    $query = "SELECT * FROM `IDENT_PRODUITS` WHERE `CODE_CIP` = '$this->code_cip';";
  	$conditionnement = $ds->loadHash($query);
   	$this->nb_unite_presentation = $conditionnement["NB_UNITE_DE_PRESENTATION"];
    $this->nb_presentation = ($conditionnement["NB_PRESENTATION"]) ? $conditionnement["NB_PRESENTATION"] : "1";  	
  
  	// Libelle de la presentation
  	$code_unite_presentation = $conditionnement['CODE_UNITE_DE_PRESENTATION'];
  	$query = "SELECT * FROM `IDENT_UNITES_DE_PRESENTATION` WHERE `CODE_UNITE_DE_PRESENTATION` = '$code_unite_presentation';";
  	$presentation = $ds->loadHash($query);
  	$this->libelle_unite_presentation = $presentation["LIBELLE_UNITE_DE_PRESENTATION"];
  	$this->libelle_unite_presentation_pluriel = $presentation["LIBELLE_UNITE_DE_PRESENTATION_PLURIEL"];
  	
  	return $conditionnement;
  }
  
  function loadConditionnement(){
    $conditionnement = $this->loadUnitePresentation();
    
  	// Dosages
  	$this->dosages = array();
  	$this->loadDosage($conditionnement["DOSAGEUNITE1"],$conditionnement["DOSAGEQTE1"]);
  	$this->loadDosage($conditionnement["DOSAGEUNITE2"],$conditionnement["DOSAGEQTE2"]);
  	$this->loadDosage($conditionnement["DOSAGEUNITE3"],$conditionnement["DOSAGEQTE3"]);	
  	
  	$this->loadRapportUnitePrise($conditionnement["CODE_UNITE_DE_PRISE1"], $conditionnement["CODE_UNITE_DE_CONTENANCE1"], $conditionnement["NB_UP1"]);
  	$this->loadRapportUnitePrise($conditionnement["CODE_UNITE_DE_PRISE2"], $conditionnement["CODE_UNITE_DE_CONTENANCE2"], $conditionnement["NB_UP2"]);
  	
    $this->loadLibelleConditionnement($conditionnement["CODE_CONDITIONNEMENT"]);    
  }
  
  function loadLibelleConditionnement($code_conditionnement){
  	$ds = CBcbObject::getDataSource();
  	$query = "SELECT LIBELLE_CONDITIONNEMENT_PLURIEL FROM `IDENT_CONDITIONNEMENTS` WHERE `CODE_CONDITIONNEMENT` = '$code_conditionnement';";
    $this->libelle_conditionnement = $ds->loadResult($query);
  }
  
  function loadRapportUnitePrise($code_unite_prise, $code_unite_contenance, $nb_up){
  	if(!$this->libelle_presentation){
  		$this->loadLibellePresentation();
  	}
		if(!$this->libelle_unite_presentation){
			$this->loadUnitePresentation();
		}
  	$ds = CBcbObject::getDataSource();
    $query = "SELECT LIBELLE_UNITE_DE_PRISE_PLURIEL FROM `POSO_UNITES_PRISE` WHERE `CODE_UNITE_DE_PRISE` = '$code_unite_prise';";
    $unite_prise = $ds->loadResult($query);
    $query = "SELECT LIBELLE_UNITE_DE_CONTENANCE FROM `IDENT_UNITES_DE_CONTENANCE` WHERE `CODE_UNITE_DE_CONTENANCE` = '$code_unite_contenance';";
    $unite_contenance = $ds->loadResult($query);
    $this->rapport_unite_prise[$unite_prise][$unite_contenance] = $nb_up;
    
    // Ajout de la presentation
    if($this->libelle_presentation){
      $this->rapport_unite_prise[$this->libelle_presentation][$this->libelle_unite_presentation] = $this->nb_unite_presentation;
    }
  }
  
  
  function loadRapportUnitePriseByCIS(){
    $ds = CBcbObject::getDataSource();
    $query = "SELECT * FROM `IDENT_PRODUITS` WHERE `CODECIS` = '$this->code_cis';";
  	$_produits = $ds->loadList($query);
  	
  	$this->rapport_unite_prise = array();
  	$produits = array();
  	foreach($_produits as $_produit){
  	  $produit = CBcbProduit::get($_produit["CODE_CIP"]);
  	  
  	  $produit->loadConditionnement();
      $produits[] = $produit;
  	  // Fusion des tableaux d'unite de prises
  	  foreach($produit->rapport_unite_prise as $_presentation_produit => $_unites_produit){
  	    foreach($_unites_produit as $_unite_produit => $valeur_produit){
  	      $this->rapport_unite_prise[$_presentation_produit][$_unite_produit] = $valeur_produit;
  	      $this->rapport_unite_prise["$_presentation_produit ($valeur_produit $_unite_produit)"][$_unite_produit] = $valeur_produit;
  	    }
  	  }
  	}
  	return $produits;
  }
  
  
  function loadUnitesPrise(){
    $ds = CBcbObject::getDataSource();
    $query = "SELECT CODE_UNITE_DE_PRISE1, CODE_UNITE_DE_PRISE2 
							FROM `IDENT_PRODUITS` 
							WHERE `CODE_CIP` = '$this->code_cip';";
    $this->_codes_prises = $ds->loadhash($query);

	  if(is_array($this->_codes_prises)){
	    foreach($this->_codes_prises as $_code_prise){
	      if($_code_prise){
	        $query = "SELECT LIBELLE_UNITE_DE_PRISE_PLURIEL FROM `POSO_UNITES_PRISE` WHERE `CODE_UNITE_DE_PRISE` = '$_code_prise';";
	        $this->_prises[] = $ds->loadResult($query);
	      }
	    }
		}
  }
  
	
	
	static function getLibellePrise($code_prise){
    $ds = CBcbObject::getDataSource();
    $query = "SELECT LIBELLE_UNITE_DE_PRISE 
		          FROM `POSO_UNITES_PRISE`
							WHERE `CODE_UNITE_DE_PRISE` = '$code_prise';";
		return $ds->loadResult($query);
	}
	
	
	
  function loadDosage($dosage_unite, $dosage_nb){
   $ds = CBcbObject::getDataSource();
   if($dosage_unite){
	  	$query = "SELECT UNITE FROM `IDENT_UNITES_DE_DOSAGE` WHERE `CODE_UNITE` = '$dosage_unite';";
	  	$unite = $ds->loadResult($query);
	  	$this->dosages[] = array("nb_dosage" => $dosage_nb, "unite_dosage" => $unite);
  	}
  }
  
  
  // Permet de savoir si le produit est un générique 
  function getGenerique(){
    $this->_generique = $this->distObj->IsGenerique($this->code_cip);
  }
  
  function getSuppression(){
  	/*
    if($this->_ref_monographie->date_suppression){
      $this->_supprime = 1;
    }
    */
  }
  
  // Permet de savoir si le produit est un referent
  function getReferent(){
    $this->_referent = $this->distObj->IsReferent($this->code_cip);
  }
  
  function getStatut(){
    $this->distObj->SearchStatut($this->code_cip);
    $this->code_statut = $this->distObj->GetStatut(2);
    $this->libelle_statut = $this->distObj->GetStatut(3);
    $this->numero_AMM = $this->distObj->GetStatut(4);
    $this->date_AMM = $this->distObj->GetStatut(5);
  }
  
  function getAgrement(){
    $this->agrement = $this->distObj->GetStatut(15);
  }
  
  // Fonction qui retourne les equivalents d'un produit
  function loadRefsEquivalents(){
    $produitEquivalent = new CBcbEquivalent();
    $this->_ref_equivalents = $produitEquivalent->searchEquivalents($this->code_cip);
  }
  
  function loadRefsEquivalentsInLivret(){
  	$this->loadRefsEquivalents();
  	foreach($this->_ref_equivalents as $key => $produit_equivalent){
      if(!$produit_equivalent->inLivret){
        unset($this->_ref_equivalents[$key]);
      }
    }
  }

  function isInLivret(){
    $ds = CBcbObject::getDataSource();
	  $query = "SELECT count(*) 
							FROM LIVRETTHERAPEUTIQUE
							LEFT JOIN IDENT_PRODUITS ON IDENT_PRODUITS.CODE_CIP = LIVRETTHERAPEUTIQUE.CODECIP
							WHERE ((IDENT_PRODUITS.CODE_UCD = '$this->code_ucd'	OR IDENT_PRODUITS.CODECIS = '$this->code_cis')
							       OR(LIVRETTHERAPEUTIQUE.CODECIP = '$this->code_cip'));";
	  $countInLivret = $ds->loadResult($query);
    $this->inLivret = ($countInLivret > 0) ? 1 : 0;
  }

  function isInT2A(){
    $ds = CBcbObject::getDataSource();
    $query = "SELECT `MEDICAMENT_T2A` FROM `PHARMACIE_ADD_ON` WHERE `CODE_CIP` = '$this->code_cip'";
    $t2a = $ds->loadResult($query);
    $this->inT2A = ($t2a == "-1") ? false : true; 
  }
  
  static function getHorsT2ALivret(){
    $produits = array();
    $ds = CBcbObject::getDataSource();
    $query = "SELECT `CODE_CIP` 
							FROM `PHARMACIE_ADD_ON`
							LEFT JOIN `LIVRETTHERAPEUTIQUE` ON `PHARMACIE_ADD_ON`.`CODE_CIP` = `LIVRETTHERAPEUTIQUE`.`CODECIP` 
							WHERE `MEDICAMENT_T2A` = '-1' 
							AND `LIVRETTHERAPEUTIQUE`.`CODECIP` IS NOT NULL;";
    $codes_cip = $ds->loadColumn($query);

    foreach($codes_cip as $code_cip){
      $produit = new CBcbProduitLivretTherapeutique();
      $produit->load($code_cip);
      $produit->loadRefProduit();
      $produits[$code_cip] = $produit;
    }
    return $produits;
  }
  
  function searchProduit($text, $supprime = 1, $type_recherche = "debut", $hors_specialite = 0, $max = 50, $livretTherapeutique = 0, $full_mode = true, $search_by_cis = 1){   
    // Type_recherche
    // 0 ou 256 => recherche par nom
    // 1: recherche par CIP
    // 2: recherche par UCD
    
    // Affichage des produits supprimes
    if($supprime == "" || $supprime == 0){
      $supprime = 1;
    } else {
      $supprime = 0;
    }
    
    // Position de la recherche
    if($type_recherche == "partout"){
      $type_recherche = 256;
    } 
    if($type_recherche == "debut"){
      $type_recherche = 0;
    }

    $this->distObj->LivretTherapeutique = $livretTherapeutique;
    $this->distObj->Supprime = $supprime;
        
    // 1ere recherche 
    //$this->distObj->Specialite = $specialite;
    //$this->distObj->Search($text, 0, $max, $type_recherche);
    $this->SearchEx($text, 0, $max, $type_recherche, $search_by_cis, $hors_specialite);
    
    $_produits = array();
    // Parcours des produits
    foreach($this->distObj->TabProduit as $key => $prod){
      $produit = new CBcbProduit();
      $produit->load($prod->CodeCIP, $full_mode); 
      $_produits[$prod->CodeCIP] = $produit; 
    }
    return $_produits;
  }
  
  static function getProduitsFromCIS($code_cis){
    $ds = CBcbObject::getDataSource();
    $query = "SELECT * 
              FROM `IDENT_PRODUITS` 
							LEFT JOIN PRODUITS_IFP ON IDENT_PRODUITS.CODE_CIP = PRODUITS_IFP.CODE_CIP
							WHERE `CODECIS` = '$code_cis'
							AND PRODUITS_IFP.Produit_supprime is NULL;";
    return $ds->loadList($query);
  }
  
  static function getProduitsFromCISInLivret($code_cis){
    global $g;
    
    $ds = CBcbObject::getDataSource();
    $query = "SELECT * 
              FROM `IDENT_PRODUITS`, LIVRETTHERAPEUTIQUE, PRODUITS_IFP
							WHERE `CODECIS` = '$code_cis'
              AND IDENT_PRODUITS.CODE_CIP = PRODUITS_IFP.CODE_CIP
							AND PRODUITS_IFP.Produit_supprime is NULL
              AND IDENT_PRODUITS.CODE_CIP = LIVRETTHERAPEUTIQUE.CODECIP
			        AND LIVRETTHERAPEUTIQUE.CodeEtablissement='$g';";
    return $ds->loadList($query);
  }
  
  static function getProduitsFromUCD($code_ucd){
    $ds = CBcbObject::getDataSource();
    $query = "SELECT * 
              FROM `IDENT_PRODUITS` 
              LEFT JOIN PRODUITS_IFP ON IDENT_PRODUITS.CODE_CIP = PRODUITS_IFP.CODE_CIP
							WHERE `CODE_UCD` = '$code_ucd'
							AND PRODUITS_IFP.Produit_supprime is NULL;";
    return $ds->loadList($query);
  }
  
  function searchProduitAutocomplete($text, $nb_max, $livretTherapeutique = 0, $search_libelle_long = false, $hors_specialite = 0, $search_by_cis = 1){   
    global $g;
    $this->distObj->Supprime = 1;
    if($livretTherapeutique){
      $this->distObj->LivretTherapeutique = $g;  
    }
    $this->SearchEx($text, 0, $nb_max, 0, $search_by_cis, $hors_specialite);
    return $this->distObj->TabProduit;
  }
  
  
  // Chargement de toutes les posologies d'un produit
  function loadRefPosologies(){
    $ds = CBcbObject::getDataSource();
    $query = "SELECT * FROM `POSO_PRODUITS` WHERE `CODE_CIP` = '$this->code_cip' ORDER BY `NO_POSO` ASC;"; 
    $posologies = $ds->loadList($query);
    
    // Chargement de chaque posologie
    $this->_ref_posologies = array();
    $view_poso = array();
    foreach($posologies as $key => $posologie){
      $mbposologie = new CBcbPosologie();
      $mbposologie->load($posologie["CODE_CIP"], $posologie["NO_POSO"]);
      if(!in_array($mbposologie->_view, $view_poso)){
        $this->_ref_posologies[] = $mbposologie;
      }
      $view_poso[] = $mbposologie->_view;
    }
    return $this->_ref_posologies;
  }
  
  // Chargement de la monographie d'un produit
  function loadRefMonographie(){
    $this->_ref_monographie = new CBcbMonographie();
    $this->_ref_monographie->load($this->code_cip);
  }
  
  
  // Chargement de la composition
  function loadRefComposition(){    
    $this->_ref_composition = new CBcbComposition();
    $this->_ref_composition->load($this->code_cip); 
  }
  
  
  // Chargement des donnees technico-reglementaires
  function loadRefEconomique(){
    $this->_ref_economique = new CBcbEconomique();
    $this->_ref_economique->load($this->code_cip);
  }
  
  // Recherche des classes ATC d'un produit
  function loadClasseATC(){
    $classeATC = new CBcbClasseATC();
    $this->_ref_classes_ATC   = $classeATC->searchATCProduit($this->code_cip);
    if(isset($this->_ref_classes_ATC[0]->classes[3])){
      $this->_ref_ATC_2_code    = $this->_ref_classes_ATC[0]->classes[3]["code"];
      $this->_ref_ATC_2_libelle = strtolower($this->_ref_classes_ATC[0]->classes[3]["libelle"]);
    }
  }
  
  // Chargement des fiches ATC
  function loadRefsFichesATC(){
    $fiche_ATC = new CFicheATC();
    $fiche_ATC->code_ATC = $this->_ref_ATC_2_code;
    $this->_ref_fiches_ATC = $fiche_ATC->loadMatchingList();
  }
  
  // Recherche des classes Therapeutique d'un produit
  function loadClasseTherapeutique(){
    $classeThera = new CBcbClasseTherapeutique();
    $this->_ref_classes_thera = $classeThera->searchTheraProduit($this->code_cip); 
  }
  
  static function getFavoris($praticien_id) {
    $ds = CSQLDataSource::get("std");
    $sql = "SELECT *, COUNT(*) AS total
            FROM prescription_line_medicament, prescription
            WHERE prescription_line_medicament.prescription_id = prescription.prescription_id
            AND prescription.praticien_id = $praticien_id
            AND prescription.object_id IS NOT NULL
            GROUP BY prescription_line_medicament.code_cis
            ORDER BY total DESC
            LIMIT 0, 10";
    return $ds->loadlist($sql);
  }
  
  static function getFavorisInjectable($praticien_id) {
    $ds = CSQLDataSource::get("std");
    $sql = "SELECT *, COUNT(*) AS total
            FROM perfusion, perfusion_line, prescription
            WHERE perfusion_line.perfusion_id = perfusion.perfusion_id
            AND perfusion.prescription_id = prescription.prescription_id
            AND perfusion.praticien_id = $praticien_id
            AND prescription.object_id IS NOT NULL
            GROUP BY perfusion_line.code_cis
            ORDER BY total DESC
            LIMIT 0, 10";
    return $ds->loadlist($sql);
  }
}

?>