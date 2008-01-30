<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbProduit extends CBcbObject {

  // G�n�rale
  var $distObj               = null;
 
  // Sp�ciale Produit
  var $code_cip              = null;
  var $code_ucd              = null;
  var $libelle_produit       = null;
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
  
  // Objects references
  var $_ref_DCI              = null;
  var $_ref_UCD              = null;
  var $_ref_monographie      = null;
  var $_ref_composition      = null;
  var $_ref_economique       = null;
  var $_ref_classes_ATC      = null;
  var $_ref_classes_thera    = null;
  
  // Constructeur
  function CBcbProduit(){
    $this->initBCBConnection();
    // Creation de la connexion
    $this->distObj = new BCBProduit();
    $result = $this->distObj->InitConnexion(CBcbObject::$objDatabase->LinkDB, CBcbObject::$TypeDatabase);
  }
 
  // Chargement d'un produit
  function load($code_cip){
    $this->distObj->SearchInfo($code_cip);
    $infoProduit = $this->distObj->DataInfo;  
    if($infoProduit->Charge == 1){
      $this->code_cip        = $infoProduit->Code_CIP;
      $this->code_ucd        = $infoProduit->Code_Ucd;
      $this->libelle         = $infoProduit->Libelle;
      $this->nom_commercial  = $infoProduit->NomCommercial;
      $this->forme           = $infoProduit->Forme;
      $this->formes          = $infoProduit->Formes;
      $this->nb_ucd          = $infoProduit->Nb_UCD;
      $this->hospitalier     = $infoProduit->Hospitalier;
      $this->nom_laboratoire = $infoProduit->Laboratoire;
    }
    // Chargement du statut du produit
    $this->getStatut();
    
    // Chargement de l'agrement
    $this->getAgrement();
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
 
  
  
  // Recherche d'un produit
  // $text: texte a rechercher
  // $lexico: 0: recherche sur le debut, lexico = 256: n'importe ou dans la chaine
  function searchProduit($text, $supprime = 1, $position_text = "debut", $specialite = 1){
    // Parametres supplementaires pour la recherche
    
    // Affichage des produits supprimes
    if($supprime == "" || $supprime == 0){
      $supprime = 1;
    } else {
      $supprime = 0;
    }
    
    // Position de la recherche
    if($position_text == "partout"){
      $position_text = 256;
    } else {
      $position_text = 0;
    }

    $this->distObj->Specialite = $specialite;
    $this->distObj->Supprime = $supprime;  
    
    $this->distObj->Search($text, 0, 50, $position_text);
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
    $this->_ref_classes_ATC = $classeATC->searchATCProduit($this->code_cip);  
  }
  
  // Recherche des classes Therapeutique d'un produit
  function loadClasseTherapeutique(){
    $classeThera = new CBcbClasseTherapeutique();
    $this->_ref_classes_thera = $classeThera->searchTheraProduit($this->code_cip); 
  }
  
}
