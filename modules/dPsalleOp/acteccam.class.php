<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage mediusers
 *	@version $Revision$
 *  @author Thomas Despoix
 */

/**
 * Classe servant à gérer les enregistrements des actes CCAM pendant les
 * interventions
 */
class CActeCCAM extends CMbMetaObject {
  // DB Table key
	var $acte_id = null;

  // DB References
  var $executant_id        = null;

  // DB Fields
  var $code_acte           = null;
  var $code_activite       = null;
  var $code_phase          = null;
  var $execution           = null;
  var $modificateurs       = null;
  var $montant_depassement = null;
  var $commentaire         = null;
  var $code_association    = null;

  // Form fields
  var $_modificateurs     = array();
  var $_anesth            = null;
  var $_linked_actes      = null;
  var $_guess_association = null;
  var $_guess_regle_asso  = null;
  
  // Object references
  var $_ref_executant = null;
  var $_ref_code_ccam = null;

	function CActeCCAM() {
		$this->CMbObject( "acte_ccam", "acte_id" );
    
    $this->loadRefModule(basename(dirname(__FILE__)));
	}
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["object_class"]        = "notNull enum list|COperation|CSejour|CConsultation";
    $specs["code_acte"]           = "notNull code ccam";
    $specs["code_activite"]       = "notNull num minMax|0|99";
    $specs["code_phase"]          = "notNull num minMax|0|99";
    $specs["execution"]           = "notNull dateTime";
    $specs["modificateurs"]       = "str maxLength|4";
    $specs["montant_depassement"] = "currency min|0";
    $specs["commentaire"]         = "text";
    $specs["executant_id"]        = "notNull ref class|CMediusers";
    $specs["code_association"]    = "num minMax|1|5";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "code_acte" => "equal"
    );
  }
  
  function check() {
    return parent::check(); 

    // datetime_execution: attention à rester dans la plage de l'opération
  }
   
  function updateFormFields() {
    parent::updateFormFields();
    $this->_modificateurs = str_split($this->modificateurs);
    mbRemoveValuesInArray("", $this->_modificateurs);
    $this->_view   = "$this->code_acte-$this->code_activite-$this->code_phase-$this->modificateurs";
    $this->_anesth = ($this->code_activite == 4) ? true : false;
  }
  
  function loadRefObject(){
    $this->_ref_object = new $this->object_class;
    $this->_ref_object->load($this->object_id); 
  }
 
  function loadRefExecutant() {
    $this->_ref_executant = new CMediusers;
    $this->_ref_executant->load($this->executant_id);
  }
  
  function loadRefCodeCCAM() {
    $this->_ref_code_ccam = new CCodeCCAM($this->code_acte);
    $this->_ref_code_ccam->load();
  }
   
  function loadRefsFwd() {
    parent::loadRefsFwd();

    $this->loadRefExecutant();
    $this->loadRefCodeCCAM();
  }
  
  function getFavoris($chir,$class,$view) {
  	$condition = ( $class == "" ) ? "executant_id = '$chir'" : "executant_id = '$chir' AND object_class = '$class'";
  	$sql = "select code_acte, object_class, count(code_acte) as nb_acte
            from acte_ccam
            where $condition
            group by code_acte
            order by nb_acte DESC
            limit 10";
  	$codes = $this->_spec->ds->loadlist($sql);
  	return $codes;
  }
  
  function getPerm($permType) {
    if(!$this->_ref_object) {
    	$this->loadRefObject();
    }
    return $this->_ref_object->getPerm($permType);
  }
  
  function getLinkedActes() {
    $acte = new CActeCCAM();
    
    $where = array();
    $where["acte_id"]       = "<> '$this->_id'";
    $where["object_class"]  = "= '$this->object_class'";
    $where["object_id"]     = "= '$this->object_id'";
    $where["code_activite"] = "= '$this->code_activite'";
    
    $this->_linked_actes = $acte->loadList($where);
  }
  
  function guessAssociation() {
    /*
     * Calculs initiaux
     */
    
    // Chargements initiaux
    $this->loadRefCodeCCAM();
    $this->getLinkedActes();
    foreach($this->_linked_actes as &$acte) {
      $acte->loadRefCodeCCAM();
    }
    
    // Nombre d'actes
    $numActes = count($this->_linked_actes) + 1;
    
    // Calcul de la position tarifaire de l'acte
    $tarif = $this->_ref_code_ccam->activites[$this->code_activite]->phases[$this->code_phase]->tarif;
    $orderedActes = array();
    $orderedActes[$this->_id] = $tarif;
    foreach($this->_linked_actes as &$acte) {
      $tarif = $acte->_ref_code_ccam->activites[$acte->code_activite]->phases[$acte->code_phase]->tarif;
      $orderedActes[$acte->_id] = $tarif;
    }
    ksort($orderedActes);
    arsort($orderedActes);
    $position = array_search($this->_id, array_keys($orderedActes));
    
    // Nombre d'actes du chap. 18
    $numChap18 = 0;
    if($this->_ref_code_ccam->chapitres[0]["db"] == "000018") {
      $numChap18++;
    }
    foreach($this->_linked_actes as $linkedActe) {
      if($linkedActe->_ref_code_ccam->chapitres[0]["db"] == "000018") {
        $numChap18++;
      }
    }
    
    // Nombre d'actes du chap. 19.01
    $numChap1901 = 0;
    if($this->_ref_code_ccam->chapitres[0]["db"] == "000019" && $this->_ref_code_ccam->chapitres[1]["db"] == "000001") {
      $numChap1901++;
    }
    foreach($this->_linked_actes as $linkedActe) {
      if($linkedActe->_ref_code_ccam->chapitres[0]["db"] == "000019" && $linkedActe->_ref_code_ccam->chapitres[1]["db"] == "000001") {
        $numChap1901++;
      }
    }
    
    // Nombre d'actes du chap. 19.02
    $numChap1902 = 0;
    if($this->_ref_code_ccam->chapitres[0]["db"] == "000019" && $this->_ref_code_ccam->chapitres[1]["db"] == "000002") {
      $numChap1902++;
    }
    foreach($this->_linked_actes as $linkedActe) {
      if($linkedActe->_ref_code_ccam->chapitres[0]["db"] == "000019" && $linkedActe->_ref_code_ccam->chapitres[1]["db"] == "000002") {
        $numChap1902++;
      }
    }
     
    // Nombre d'actes des chap. 02, 03, 05 à 10, 16, 17
    $numChap02 = 0;
    $listChaps = array("000002", "000003", "000005", "000006", "000007", "000008", "000009", "000010", "000016", "000017");
    if(in_array($this->_ref_code_ccam->chapitres[0]["db"], $listChaps)) {
      $numChap02++;
    }
    foreach($this->_linked_actes as $linkedActe) {
      if(in_array($linkedActe->_ref_code_ccam->chapitres[0]["db"], $listChaps)) {
        $numChap02++;
      }
    }
     
    // Nombre d'actes des chap. 01, 04, 11, 15
    $numChap0115 = 0;
    $listChaps = array("000001", "000004", "000011", "000015");
    if(in_array($this->_ref_code_ccam->chapitres[0]["db"], $listChaps)) {
      $numChap0115++;
    }
    foreach($this->_linked_actes as $linkedActe) {
      if(in_array($linkedActe->_ref_code_ccam->chapitres[0]["db"], $listChaps)) {
        $numChap0115++;
      }
    }
     
    // Nombre d'actes des chap. 01, 04, 11, 12, 13, 14, 15, 16
    $numChap0116 = 0;
    $listChaps = array("000001", "000004", "000011", "000012", "000013", "000014", "000015", "000016");
    if(in_array($this->_ref_code_ccam->chapitres[0]["db"], $listChaps)) {
      $numChap0116++;
    }
    foreach($this->_linked_actes as $linkedActe) {
      if(in_array($linkedActe->_ref_code_ccam->chapitres[0]["db"], $listChaps)) {
        $numChap0116++;
      }
    }
    
    // Le praticien est-il un ORL
    $pratORL = false;
    if($this->object_class == "COperation") {
      $this->loadRefExecutant();
      $this->_ref_executant->loadRefDiscipline();
      if($this->_ref_executant->_ref_discipline->_compat == "ORL") {
        $pratORL = true;
      }
    }
    
    // Diagnostic principal en S ou T avec lésions multiples
    // Diagnostic principal en C (carcinologie)
    $DPST = false;
    $DPC  = false;
    if($this->object_class == "COperation") {
      $this->loadRefObject();
      $this->_ref_object->loadRefSejour();
      if(substr(0, 1, $this->_ref_object->_ref_sejour->DP) == "S" || substr(0, 1, $this->_ref_object->_ref_sejour->DP) == "T") {
        $DPST = true;
        $membresDiff = true;
      }
      if(substr(0, 1, $this->_ref_object->_ref_sejour->DP) == "C") {
        $DPC = true;
      }
    }
    
    // Association d'1 exérèse, d'1 curage et d'1 reconstruction
    $assoEx  = false;
    $assoCur = false;
    $assoRec = false;
    if($numActes == 3) {
      if(stripos($this->_ref_code_ccam->libelleLong, "exérèse")) {
        $assoEx = true;
      }
      if(stripos($this->_ref_code_ccam->libelleLong, "curage")) {
        $assoCu = true;
      }
      if(stripos($this->_ref_code_ccam->libelleLong, "reconstruction")) {
        $assoRec = true;
      }
      foreach($this->_linked_actes as $linkedActe) {
        if(stripos($linkedActe->_ref_code_ccam->libelleLong, "exérèse")) {
          $assoEx = true;
        }
        if(stripos($linkedActe->_ref_code_ccam->libelleLong, "curage")) {
          $assoCu = true;
        }
        if(stripos($linkedActe->_ref_code_ccam->libelleLong, "reconstruction")) {
          $assoRec = true;
        }
      }
    }
    $assoExCurRec = $assoEx && $assoCur && $assoRec;
    
    
    /*
     * Application des règles
     */
    
    // Cas d'un seul actes (règle A)
    if($numActes == 1) {
      $this->_guess_association = "";
      $this->_guess_regle_asso  = "A";
      return $this->_guess_association;
    }
    
    // 1 actes + 1 acte du chap. 18 ou du chap. 19.02 (règles B et C)
    if($numActes == 2) {
      // 1 acte + 1 geste complémentaire chap. 18 (règle B)
      if($numChap18 == 1) {
        $this->_guess_association = "";
      $this->_guess_regle_asso    = "B";
        return $this->_guess_association;
      }
      // 1 acte + 1 supplément des chap. 19.02 (règle C)
      if($numChap1902 == 1) {
        $this->_guess_association = "1";
      $this->_guess_regle_asso    = "C";
        return $this->_guess_association;
      }
    }
    
    // 1 acte + 1 ou pls geste complémentaire chap. 18 + 1 ou pls supplément des chap. 19.02 (règle D)
    if($numActes >= 3 && $numActes - ($numChap18 + $numChap1902) == 1 && $numChap18 && $numChap1902) {
      $this->_guess_association = "1";
      $this->_guess_regle_asso  = "D";
      return $this->_guess_association;
    }
    
    // 1 acte + 1 acte des chap. 02, 03, 05 à 10, 16, 17 ou 19.01 (règle E)
    if($numActes == 2 && ($numChap02 == 1 || $numChap1901 == 1)) {
      switch($position) {
        case 0 :
          $this->_guess_association = "1";
          $this->_guess_regle_asso  = "E";
          break;
        case 1 :
          $this->_guess_association = "2";
          $this->_guess_regle_asso  = "E";
          break;
      }
      return $this->_guess_association;
    }
    
    // 1 acte + 1 acte des chap. 02, 03, 05 à 10, 16, 17 ou 19.01 + 1 acte des chap. 18 ou 19.02 (règle F)
    if($numActes == 3 && ($numChap02 == 1 || $numChap1901 == 1) && ($numChap18 == 1 || $numChap1902 == 1)) {
      switch($position) {
        case 0 :
          $this->_guess_association = "1";
          $this->_guess_regle_asso  = "F";
          break;
        case 1 :
          if($this->_ref_code_ccam->chapitres[0] == "18" || $this->_ref_code_ccam->chapitres[0] == "19") {
            $this->_guess_association = "1";
            $this->_guess_regle_asso  = "F";
          } else {
            $this->_guess_association = "2";
            $this->_guess_regle_asso  = "F";
          }
          break;
        case 2 :
          if($this->_ref_code_ccam->chapitres[0] == "18" || $this->_ref_code_ccam->chapitres[0] == "19") {
            $this->_guess_association = "1";
            $this->_guess_regle_asso  = "F";
          } else {
            $this->_guess_association = "2";
            $this->_guess_regle_asso  = "F";
          }
          break;
      }
      return $this->_guess_association;
    }
    
    // 2 actes des chap. 01, 04, 11 ou 15 (règle G)
    if($numActes == 2 && $numChap0115 == 2 && $membresDiff) {
      switch($position) {
        case 0 :
          $this->_guess_association = "1";
          $this->_guess_regle_asso  = "G";
          break;
        case 1 :
          $this->_guess_association = "3";
          $this->_guess_regle_asso  = "G";
          break;
      }
      return $this->_guess_association;
    }
    
    // 3 actes des chap. 01, 04 ou 11 à 16 avec DP en S ou T (lésions traumatiques multiples) (règle H)
    if($numActes == 3 && $numChap0116 == 3 && $DPST) {
      switch($position) {
        case 0 :
          $this->_guess_association = "1";
          $this->_guess_regle_asso  = "H";
          break;
        case 1 :
          $this->_guess_association = "3";
          $this->_guess_regle_asso  = "H";
          break;
        case 2 :
          $this->_guess_association = "2";
          $this->_guess_regle_asso  = "H";
          break;
      }
    }
    
    // 3 actes, chirurgien ORL, DP en C (carcinologie) et association d'1 exérèse, d'1 curage et d'1 reconstruction (règle I)
    if($numActes == 3 && $pratORL && $DPC && $assoExCurRec) {
      switch($position) {
        case 0 :
          $this->_guess_association = "1";
          $this->_guess_regle_asso  = "I";
          break;
        case 1 :
          $this->_guess_association = "2";
          $this->_guess_regle_asso  = "I";
          break;
        case 2 :
          $this->_guess_association = "2";
          $this->_guess_regle_asso  = "I";
          break;
      }
    }
    
    // Cas général pour plusieurs actes (règle Z)
    switch($position) {
      case 0 :
        $this->_guess_association = "1";
        $this->_guess_regle_asso  = "Z";
        break;
      case 1 :
        $this->_guess_association = "2";
        $this->_guess_regle_asso  = "Z";
        break;
      default :
        $this->_guess_association = "X";
        $this->_guess_regle_asso  = "Z";
    }
    
    return $this->_guess_association;
  }
  
  function getTarif() {
    $this->guessAssociation();
    if($this->code_association !== null) {
      $code_asso = $this->code_association;
    } else {
      $code_asso = $this->_guess_association;
    }
    $this->_tarif = $this->_ref_code_ccam->activites[$this->code_activite]->phases[$this->code_phase]->tarif;
    $coeffAsso    = $this->_ref_code_ccam->getCoeffAsso($code_asso);
    $forfait     = 0;
    $coefficient = 100;
    foreach($this->_modificateurs as $modif) {
      $result = $this->_ref_code_ccam->getForfait($modif);
      $forfait     += $result["forfait"];
      $coefficient += ($result["coefficient"]) - 100;
    }
    $this->_tarif = ($this->_tarif * ($coefficient / 100) + $forfait) * ($coeffAsso / 100);
    return $this->_tarif;
  }
}

?>