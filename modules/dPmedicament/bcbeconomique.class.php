<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbEconomique extends CBcbObject {

  // Générale
  var $distObj               = null;
 
  var $prix_vente            = null;
  var $prix_achat_ht         = null;
  var $base_remboursement_ss = null;
  var $tips                  = null;
  var $taux_tva              = null;
  var $taux_ss               = null;
  var $laboratoire           = null;
  var $liste                 = null;
  var $libelle_acte          = null;
  var $code_ucd              = null;
  var $labo_exploitant       = null;
  
  
  // Constructeur
  function CBcbEconomique(){
    $this->initBCBConnection();
    // Creation de la connexion
    $this->distObj = new BCBEconomique();
    $result = $this->distObj->InitConnexion(CBcbObject::$objDatabase->LinkDB, CBcbObject::$TypeDatabase); 
  }
 
  // Chargement
  function load($CIP){
    $this->distObj->Search($CIP);
    $this->prix_vente = $this->distObj->DataEco->Prix_Vente;
    $this->prix_achat_ht = $this->distObj->DataEco->Prix_AchatHT;
    $this->base_remboursement_ss = $this->distObj->DataEco->Base_RemboursementSS;
    $this->tips = $this->distObj->DataEco->Tips;
    $this->taux_tva = $this->distObj->DataEco->Taux_TVA;
    $this->taux_ss = $this->distObj->DataEco->Taux_SS;
    $this->laboratoire = $this->distObj->DataEco->Laboratoire;
    $this->liste = $this->distObj->DataEco->Liste;
    $this->libelle_acte = $this->distObj->DataEco->Libelle_Acte;
    $this->code_ucd = $this->distObj->DataEco->Code_Ucd;
    $this->labo_exploitant = $this->distObj->DataEco->Labo_Exploitant;
  }
}
