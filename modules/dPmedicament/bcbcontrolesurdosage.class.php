<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("bcbObject.class.php");

class CBcbControleSurdosage {
  
  var $terrain                = null;
  var $qte_mini               = null;
  var $qte_maxi               = null;
  var $qte_maxi_toxique       = null;
  var $duree_mini             = null;
  var $duree_maxi             = null;
  var $duree_maxi_toxique     = null;
  
  var $alertes                = null;
  
  var $_ref_prescription      = null;
  var $_ref_patient           = null;
  
  function setPrescription($prescription) {
    if($prescription->_class_name == "CPrescription") {
      $this->_ref_prescription  = $prescription;
      $this->_ref_patient       = $prescription->_ref_patient;
      $this->terrain            = null;
      $this->qte_mini           = null;
      $this->qte_maxi           = null;
      $this->qte_maxi_toxique   = null;
      $this->duree_mini         = null;
      $this->duree_maxi         = null;
      $this->duree_maxi_toxique = null;
      return true;
    }
    return false;
  }
  
  function getTerrain() {
    if(!$this->_ref_patient) {
      return false;
    }
    if($this->terrain) {
      return $this->terrain;
    }
    // Test par rapport à l'age du patient
    // T : Tous
    if(!$this->_ref_patient->_age) {
      $this->terrain = "T";
    // N : Nourisson (< 3 ans)
    } elseif($this->_ref_patient->_age < 3) {
      $this->terrain = "N";
    // E : Enfant (3 à 18 ans)
    } elseif($this->_ref_patient->_age < 18) {
      $this->terrain = "E";
    // A : Adulte (18 ans - 60 ans)
    } elseif($this->_ref_patient->_age < 60) {
      $this->terrain = "A";
    // S : Sujet agé (>= 60 ans)
    } else {
      $this->terrain = "S";
    }
    return $this->terrain;
  }
  
  function getPosoMax($line) {
    $this->getTerrain();
    $ds = CBcbObject::getDataSource();
    $result = null;
    if($this->terrain != "T") {
      $query = "SELECT POSO_MAXIMUM.*
                FROM POSO_MAXIMUM
                WHERE POSO_MAXIMUM.Code_CIP='".$line->code_cip."'
                AND POSO_MAXIMUM.Terrain='".$this->terrain."'";
    }
    if(!$result) {
      $query = "SELECT POSO_MAXIMUM.*
                FROM POSO_MAXIMUM
                WHERE POSO_MAXIMUM.Code_CIP='".$line->code_cip."'
                AND POSO_MAXIMUM.Terrain='T'";
      $result = reset($ds->loadList($query));
    }
    if(!$result && $this->terrain == "S") {
      $query = "SELECT POSO_MAXIMUM.*
                FROM POSO_MAXIMUM
                WHERE POSO_MAXIMUM.Code_CIP='".$line->code_cip."'
                AND POSO_MAXIMUM.Terrain='A'";
      $result = reset($ds->loadList($query));
    }
    $conditionnement = $line->_ref_produit->loadUnitePresentation();
    $uadministration = $line->_ref_produit->libelle_unite_presentation;
    $unite = $conditionnement["CODE_UNITE_DE_PRISE1"] == $uadministration ? "UP1" : "UP2";

    // UP1QTEMINI, UP1QTEMAXI, UP1QTEMINIPKG, UP1QTEMAXIPKG, UP1PKG, UP1QTEMINIPM2, UP1QTEMAXIPM2, UP1PM2, UP1QTEMAXITOXIQUE, UP1QTEMAXITOXIQUEPKG, UP1QTEMAXITOXIQUEPM2,
    // UP2QTEMINI, UP2QTEMAXI, UP2QTEMINIPKG, UP2QTEMAXIPKG, UP2PKG, UP2QTEMINIPM2, UP2QTEMAXIPM2, UP2PM2, UP2QTEMAXITOXIQUE, UP2QTEMAXITOXIQUEPKG, UP2QTEMAXITOXIQUEPM2,
    // DUREEMINI, DUREEMAXI, DUREEMAXITOXIQUE
    if($result){
      $this->qte_mini           = $result[$unite."QTEMINI"];
      $this->qte_maxi           = $result[$unite."QTEMAXI"];
      $this->qte_maxi_toxique   = $result[$unite."QTEMAXITOXIQUE"];
      $this->duree_mini         = $result["DUREEMINI"];
      $this->duree_maxi         = $result["DUREEMAXI"];
      $this->duree_maxi_toxique = $result["DUREEMAXITOXIQUE"];
      $msg = "qte toxique : $this->qte_maxi_toxique $uadministration, qte max : $this->qte_maxi $uadministration, qte min : $this->qte_mini $uadministration";
      //mbTrace($msg, $line->code_cip." - ".$line->_view);
      $msg = "duree toxique : $this->duree_maxi_toxique jours, duree max : $this->duree_maxi jours, duree min : $this->duree_mini jours";
      //mbTrace($msg, $line->code_cip." - ".$line->_view);
    }
  }
  
  function controleSurdosage($line) {
    // On parcours les dates sur la durée de la prescription
    $debut_reel = mbDate(null, $line->_debut_reel);
    $fin_reelle = mbDate(null, $line->_fin_reelle);
    $unite = $line->_ref_produit->libelle_unite_presentation;
    for($date = $debut_reel; $fin_reelle && $date <= $fin_reelle; $date = mbDate("+1 DAY", $date)) {
      $qte = $line->calculPrises($this->_ref_prescription, $date);
      if($qte) {
        $date_formated = mbTransformTime(null, $date, "%d/%m/%Y");
        if($this->qte_maxi_toxique && $qte > $this->qte_maxi_toxique) {
          $msg = "$qte $unite est superieure à la quantité toxique de ";
          $msg .= "$this->qte_maxi_toxique $unite pour le $date_formated";
          $alerte = new CObject();
          $alerte->Type = "Qte";
          $alerte->Niveau = 12;
          $alerte->CIP = $line->code_cip;
          $alerte->Produit = $line->_ref_produit->libelle;
          $alerte->LibellePb = $msg;
          $this->alertes[] = $alerte;
        } elseif($this->qte_maxi && $qte > $this->qte_maxi) {
          $msg = "$qte $unite est superieure à la quantité maximale usuelle de ";
          $msg .= "$this->qte_maxi $unite pour le $date_formated";
          $alerte = new CObject();
          $alerte->Type = "Qte";
          $alerte->Niveau = 11;
          $alerte->CIP = $line->code_cip;
          $alerte->Produit = $line->_ref_produit->libelle;
          $alerte->LibellePb = $msg;
          $this->alertes[] = $alerte;
        } elseif($this->qte_mini && $qte < $this->qte_mini) {
          $msg = "$qte $unite est inférieur à la quantité minimal usuelle de ";
          $msg .= "$this->qte_mini $unite pour le $date_formated";
          $alerte = new CObject();
          $alerte->Type = "Qte";
          $alerte->Niveau = 10;
          $alerte->CIP = $line->code_cip;
          $alerte->Produit = $line->_ref_produit->libelle;
          $alerte->LibellePb = $msg;
          $this->alertes[] = $alerte;
        }
      }
    }
    return true;
  }
  
  function controleDureeMax($line) {
    $debut_reel = mbDate(null, $line->_debut_reel);
    $fin_reelle = mbGetValue(mbDate(null, $line->_fin_reelle));
    if($fin_reelle) {
      $duree = mbDaysRelative($debut_reel, $fin_reelle) + 1;
      if($this->duree_maxi_toxique && $duree >= $this->duree_maxi_toxique) {
          $msg = "$duree jours de prescription est superieur à la durée toxique de $this->duree_maxi_toxique jours";
          $alerte = new CObject();
          $alerte->Type = "Duree";
          $alerte->Niveau = 22;
          $alerte->CIP = $line->code_cip;
          $alerte->Produit = $line->_ref_produit->libelle;
          $alerte->LibellePb = $msg;
          $this->alertes[] = $alerte;
      } elseif($this->duree_maxi && $duree > $this->duree_maxi) {
          $msg = "$duree jours de prescription est superieur à la durée maximale usuelle de $this->duree_maxi jours";
          $alerte = new CObject();
          $alerte->Type = "Duree";
          $alerte->Niveau = 21;
          $alerte->CIP = $line->code_cip;
          $alerte->Produit = $line->_ref_produit->libelle;
          $alerte->LibellePb = $msg;
          $this->alertes[] = $alerte;
      } elseif($this->duree_mini && $duree < $this->duree_mini) {
          $msg = "$duree jours de prescription est inferieur à la durée minimale usuelle de $this->duree_mini jours";
          $alerte = new CObject();
          $alerte->Type = "Duree";
          $alerte->Niveau = 20;
          $alerte->CIP = $line->code_cip;
          $alerte->Produit = $line->_ref_produit->libelle;
          $alerte->LibellePb = $msg;
          $this->alertes[] = $alerte;
      }
    }
  }
  
  /*
   * Fonction de récupération des alertes
   * de surdosage temporel ou journalier
   * -Types d'erreur :
   * Qté trop faible   : 10
   * Qté trop forte    : 11
   * Qté toxique       : 12
   * Durée trop courte : 20
   * Durée trop longue : 21
   * Durée toxique     : 22
   */
  
  function getSurdosage() {
    $this->alertes = array();
    foreach($this->_ref_prescription->_ref_prescription_lines as $_line) {
      $this->getPosoMax($_line);
      $this->controleSurdosage($_line);
      $this->controleDureeMax($_line);
    }
    return $this->alertes;
  }
  
}

?>