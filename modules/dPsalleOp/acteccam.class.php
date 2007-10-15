<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage mediusers
 *	@version $Revision$
 *  @author Thomas Despoix
 */

/**
 * Classe servant � g�rer les enregistrements des actes CCAM pendant les
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

    // datetime_execution: attention � rester dans la plage de l'op�ration
  }
   
  function updateFormFields() {
    parent::updateFormFields();
    $this->_modificateurs = str_split($this->modificateurs);
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
    arsort($orderedActes);
    $position = array_search($this->_id, array_keys($orderedActes));
    
    // Nombre d'actes du chap. 18
    $numChap18 = 0;
    if($this->_ref_code_ccam->chapitres[0] == "18") {
      $numChap18++;
    }
    foreach($this->_linked_actes as $linkedActe) {
      if($this->_ref_code_ccam->chapitres[0] == "18") {
        $numChap18++;
      }
    }
    
    // Nombre d'actes du chap. 19.01
    $numChap1901 = 0;
    if($this->_ref_code_ccam->chapitres[0] == "19" && $this->_ref_code_ccam->chapitres[1] == "01") {
      $numChap1901++;
    }
    foreach($this->_linked_actes as $linkedActe) {
      if($linkedActe->_ref_code_ccam->chapitres[0] == "19" && $linkedActe->_ref_code_ccam->chapitres[1] == "01") {
        $numChap1901++;
      }
    }
    
    // Nombre d'actes du chap. 19.02
    $numChap1902 = 0;
    if($this->_ref_code_ccam->chapitres[0] == "19" && $this->_ref_code_ccam->chapitres[1] == "02") {
      $numChap1902++;
    }
    foreach($this->_linked_actes as $linkedActe) {
      if($linkedActe->_ref_code_ccam->chapitres[0] == "19" && $linkedActe->_ref_code_ccam->chapitres[1] == "02") {
        $numChap1902++;
      }
    }
     
    // Nombre d'actes des chap. 02, 03, 05 � 10, 16, 17
    $numChap02 = 0;
    $listChaps = array("02", "03", "05", "06", "07", "08", "09", "10", "16", "17");
    if(in_array($this->_ref_code_ccam->chapitres[0], $listChaps)) {
      $numChap02++;
    }
    foreach($this->_linked_actes as $linkedActe) {
      if(in_array($linkedActe->_ref_code_ccam->chapitres[0], $listChaps)) {
        $numChap02++;
      }
    }
     
    // Nombre d'actes des chap. 01, 04, 11, 15
    $numChap01 = 0;
    $listChaps = array("01", "04", "11", "15");
    if(in_array($this->_ref_code_ccam->chapitres[0], $listChaps)) {
      $numChap01++;
    }
    foreach($this->_linked_actes as $linkedActe) {
      if(in_array($linkedActe->_ref_code_ccam->chapitres[0], $listChaps)) {
        $numChap01++;
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
    
    // Diagnostic principal en S ou T avec l�sions multiples
    // Diagnostic principal en C (carcinologie)
    $DPST = false;
    $DPC  = false;
    if($this->object_class == "COperation") {
      $this->loadRefObject();
      $this->_ref_object->loadRefSejour();
      if(substr(0, 1, $this->_ref_object->_ref_sejour->DP) == "S" || substr(0, 1, $this->_ref_object->_ref_sejour->DP) == "T") {
        $DPST = true;
      }
      if(substr(0, 1, $this->_ref_object->_ref_sejour->DP) == "C") {
        $DPC = true;
      }
    }
    
    // Association d'1 ex�r�se, d'1 curage et d'1 reconstruction
    $assoEx  = false;
    $assoCur = false;
    $assoRec = false;
    if($numActes == 3) {
      if(stripos($this->_ref_code_ccam->libelleLong, "ex�r�se")) {
        $assoEx = true;
      }
      if(stripos($this->_ref_code_ccam->libelleLong, "curage")) {
        $assoCu = true;
      }
      if(stripos($this->_ref_code_ccam->libelleLong, "reconstruction")) {
        $assoRec = true;
      }
      foreach($this->_linked_actes as $linkedActe) {
        if(stripos($linkedActe->_ref_code_ccam->libelleLong, "ex�r�se")) {
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
     * Application des r�gles
     */
    
    // Cas d'un seul actes (r�gle A)
    if($numActes == 1) {
      $this->_guess_association = "A";
      return $this->_guess_association;
    }
    
    // 1 actes + 1 acte du chap. 18 ou du chap. 19.02 (r�gles B et C)
    if($numActes == 2) {
      // 1 acte + 1 geste compl�mentaire chap. 18 (r�gle B)
      if($numChap18 == 1) {
        $this->_guess_association = "B";
        return $this->_guess_association;
      }
      // 1 acte + 1 suppl�ment des chap. 19.02 (r�gle C)
      if($numChap1902 == 1) {
        $this->_guess_association = "C1";
        return $this->_guess_association;
      }
    }
    
    // 1 acte + 1 ou pls geste compl�mentaire chap. 18 + 1 ou pls suppl�ment des chap. 19.02 (r�gle D)
    if($numActes >= 3 && $numActes - ($numChap18 + $numChap1902) == 1 && $numChap18 && $numChap1902) {
      $this->_guess_association = "D1";
      return $this->_guess_association;
    }
    
    // 1 acte + 1 acte des chap. 02, 03, 05 � 10, 16, 17 ou 19.01 (r�gle E)
    if($numActes == 2 && ($numChap02 == 1 || $numChap1901 == 1)) {
      switch($position) {
        case 0 :
          $this->_guess_association = "E1";
          break;
        case 1 :
          $this->_guess_association = "E2";
          break;
      }
      return $this->_guess_association;
    }
    
    // 1 acte + 1 acte des chap. 02, 03, 05 � 10, 16, 17 ou 19.01 + 1 acte des chap. 18 ou 19.02 (r�gle F)
    if($numActes == 3 && ($numChap02 == 1 || $numChap1901 == 1) && ($numChap18 == 1 || $numChap1902 == 1)) {
      switch($position) {
        case 0 :
          $this->_guess_association = "F1";
          break;
        case 1 :
          if($this->_ref_code_ccam->chapitres[0] == "18" || $this->_ref_code_ccam->chapitres[0] == "19") {
            $this->_guess_association = "F1";
          } else {
            $this->_guess_association = "F2";
          }
          break;
        case 2 :
          if($this->_ref_code_ccam->chapitres[0] == "18" || $this->_ref_code_ccam->chapitres[0] == "19") {
            $this->_guess_association = "F1";
          } else {
            $this->_guess_association = "F2";
          }
          break;
      }
      return $this->_guess_association;
    }
    
    // 2 actes des chap. 01, 04, 11 ou 15 (r�gle G)
    if($numActes == 2 && $numChap01 == 2 && $membresDiff) {
      switch($position) {
        case 0 :
          $this->_guess_association = "G1";
          break;
        case 1 :
          $this->_guess_association = "G3";
          break;
      }
      return $this->_guess_association;
    }
    
    // 3 actes des chap. 01, 04, 11 ou 15 avec DP en S ou T (l�sions traumatiques multiples) (r�gle H)
    if($numActes == 3 && $numChap01 == 3 && $DPST) {
      switch($position) {
        case 0 :
          $this->_guess_association = "H1";
          break;
        case 1 :
          $this->_guess_association = "H3";
          break;
        case 2 :
          $this->_guess_association = "H2";
          break;
      }
    }
    
    // 3 actes, chirurgien ORL, DP en C (carcinologie) et association d'1 ex�r�se, d'1 curage et d'1 reconstruction (r�gle I)
    if($numActes == 3 && $pratORL && $DPC && $assoExCurRec) {
      switch($position) {
        case 0 :
          $this->_guess_association = "I1";
          break;
        case 1 :
          $this->_guess_association = "I2";
          break;
        case 2 :
          $this->_guess_association = "I2";
          break;
      }
    }
    
    // Cas g�n�ral pour plusieurs actes (r�gle Z)
    switch($position) {
      case 0 :
        $this->_guess_association = "Z1";
        break;
      case 1 :
        $this->_guess_association = "Z2";
        break;
      default :
        $this->_guess_association = "X";
    }
    
    return $this->_guess_association;
  }
}

?>