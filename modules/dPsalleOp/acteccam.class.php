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

class CActeCCAM extends CActe {
  static $coef_associations = array (
    "1" => 100,
    "2" => 50,
    "3" => 75,
    "4" => 100,
    "5" => 100,
  );
  
  // DB Table key
	var $acte_id = null;

  // DB Fields
  var $code_acte           = null;
  var $code_activite       = null;
  var $code_phase          = null;
  var $execution           = null;
  var $modificateurs       = null;
  var $commentaire         = null;
  var $code_association    = null;
  var $rembourse           = null;
  var $regle               = null;
  var $signe               = null;
  
  // Form fields
  var $_modificateurs     = array();
  var $_rembex            = null;
  var $_anesth            = null;
  var $_anesth_associe    = null;
  var $_linked_actes      = null;
  var $_guess_association = null;
  var $_guess_regle_asso  = null;
  
  // Behaviour fields
  var $_adapt_object = false;
  var $_calcul_montant_base = false;
  
  // Object references
  var $_ref_code_ccam = null;
  
  var $_activite = null;
  var $_phase = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'acte_ccam';
    $spec->key   = 'acte_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["code_acte"]           = "code notNull ccam seekable";
    $specs["code_activite"]       = "num notNull min|0 max|99";
    $specs["code_phase"]          = "num notNull min|0 max|99";
    $specs["execution"]           = "dateTime notNull";
    $specs["modificateurs"]       = "str maxLength|4";
    $specs["commentaire"]         = "text";
    $specs["code_association"]    = "enum list|1|2|3|4|5";
    $specs["rembourse"]           = "bool default|1";
    $specs["regle"]               = "bool";
    $specs["signe"]               = "bool default|0";

    $specs["_montant_facture"]    = "currency";
    $specs["_rembex"]             = "bool";
    
    return $specs;
  }
  
  /**
   * Check the number of codes compared to the number of actes
   * @return string check-like message
   */
  function checkEnoughCodes() {
    $this->loadTargetObject();
    if (!$this->_ref_object || !$this->_ref_object->_id) {
      return;
    }
    
    $acte = new CActeCCAM();
    $where = array();
    if ($this->_id) {
      // dans le cas de la modification
      $where["acte_id"]     = "<> '$this->_id'";  
    }
    $this->completeField("code_acte");
    $this->completeField("object_class");
    $this->completeField("object_id");
    $this->completeField("code_activite");
    $this->completeField("code_phase");
    $where["code_acte"]     = "= '$this->code_acte'";
    $where["object_class"]  = "= '$this->object_class'";
    $where["object_id"]     = "= '$this->object_id'";
    $where["code_activite"] = "= '$this->code_activite'";
    $where["code_phase"]    = "= '$this->code_phase'";
    $this->_ref_siblings = $acte->loadList($where);

    // retourne le nombre de code semblables
    $siblings = count($this->_ref_siblings);
    
    // compteur d'acte prevue ayant le meme code_acte dans l'intervention
    $nbCode = 0;
    foreach ($this->_ref_object->_codes_ccam as $key => $code) {
      // si le code est sous sa forme complete, garder seulement le code
      $code = substr($code, 0, 7);
      if ($code == $this->code_acte) {
        $nbCode++;
      }
    }
    if ($siblings >= $nbCode) {
      return "$this->_class_name-check-already-coded";
    }
  }

  function canDeleteEx(){
    parent::canDeleteEx();
   
    // Test si la consultation est validée
    if($msg = $this->checkCoded()){
      return $msg;
    }
  }

  function check() {
    
    // Test si la consultation est validée
    if($msg = $this->checkCoded()){
      return $msg;
    }
    
    if ($msg = $this->checkEnoughCodes()) {
      // Ajoute le code si besoins à l'objet
      if ($this->_adapt_object) {
        $this->_ref_object->_codes_ccam[] = $this->code_acte;
        $this->_ref_object->updateDBCodesCCAMField();
        return $this->_ref_object->store();
      }
      return $msg;
    }
    
    if ($this->code_activite !== null){

// 			Temporairement désactivé pour les imports eCap et Sherpa      
//      $this->loadRefExecutant();
//      // si c'est un acte chir et que le prat est un anesth => break
//      if($this->code_activite == 1 && $this->_ref_executant->isFromType(array("Anesthésiste"))){
//          return "$this->code_acte: Impossible de coder cet acte de chirurgie";
//      }
//      // si c'est un acte d'anesth et que le prat est un chir
//      if($this->code_activite == 4 && $this->_ref_executant->isFromType(array("Chirurgien"))){
//          return "$this->code_acte: Impossible de coder cet acte d'anesthésie";
//      }
    }   
 
    return parent::check(); 
    // datetime_execution: attention à rester dans la plage de l'opération
  }
   
  function setCodeComplet($code){
    $details = explode("-", $code);
    if (count($details) >= 3){
      $this->code_acte     = $details[0];
      $this->code_activite = $details[1];
      $this->code_phase    = $details[2];
      
      // Modificateurs
      if (count($details) >= 4){
        $this->modificateurs = $details[3];
      } 
      
      // Dépassement
      if (count($details) >= 5){
        $this->montant_depassement = str_replace("*","-",$details[4]);
      }
    }
  }
  
  function getPrecodeReady() {
    return $this->code_acte && $this->code_activite && $this->code_phase !== null;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_modificateurs = str_split($this->modificateurs);
    CMbArray::removeValue("", $this->_modificateurs);
    $this->_view       = "$this->code_acte-$this->code_activite-$this->code_phase-$this->modificateurs";
    $this->_viewUnique = $this->_id ? $this->_id : $this->_view;
    $this->_anesth = ($this->code_activite == 4) ? true : false;
    
    // Remboursement exceptionnel
    $code = CCodeCCAM::get($this->code_acte, CCodeCCAM::LITE);
    $this->_rembex = $this->rembourse && $code->remboursement == 3 ? '1' : '0';
    
  }
  
  
  function store() {
    // Sauvegarde du montant de base
    if ($this->_calcul_montant_base) {
      $this->updateFormFields();
      $this->montant_base = $this->getTarif();  
    }
   
   
    // En cas d'une modification autre que signe, on met signe à 0
    if(!$this->signe){
      // Chargement du oldObject
      $oldObject = new CActeCCAM();
      $oldObject->load($this->_id);
    
      // Parcours des objets pour detecter les modifications
      $_modif = 0;
      foreach($oldObject->getDBFields() as $propName => $propValue) {
      	if (($this->$propName !== null) && ($propValue != $this->$propName)) {
          $_modif++;
        }
      }
      if($_modif){
        $this->signe = 0;
      }
    }
    
    //mbTrace($)
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
  }
  
  function loadRefObject(){
    $this->_ref_object = new $this->object_class;
    $this->_ref_object->load($this->object_id); 
  }

  function loadRefCodeCCAM() {
    $this->_ref_code_ccam = CCodeCCAM::get($this->code_acte, CCodeCCAM::FULL);
  }
   
  function loadRefsFwd() {
    parent::loadRefsFwd();

    $this->loadRefExecutant();
    $this->loadRefCodeCCAM();
  }
  
  function getAnesthAssocie() {
    if(!$this->_ref_code_ccam) {
      $this->loadRefsFwd();
    }
    if($this->code_activite != 4 && !isset($this->_ref_code_ccam->activites[4])) {
      foreach($this->_ref_code_ccam->assos as $code_anesth) {
        if(substr($code_anesth["code"], 0, 4) == "ZZLP") {
          $this->_anesth_associe = $code_anesth["code"];
          return $this->_anesth_associe;
        }
      }
    }
    return;
  }
  
  function getFavoris($user_id, $class) {
  	$condition = ( $class == "" ) ? "executant_id = '$user_id'" : "executant_id = '$user_id' AND object_class = '$class'";
  	$sql = "select code_acte, object_class, count(code_acte) as nb_acte
            from acte_ccam
            where $condition
            group by code_acte
            order by nb_acte DESC
            limit 20";
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
    //$where["code_activite"] = "= '$this->code_activite'";
    $where["executant_id"]  = "= '$this->executant_id'";
    
    $this->_linked_actes = $acte->loadList($where);
  }
  
  function guessAssociation() {
    /*
     * Calculs initiaux
     */

    // Chargements initiaux
    $this->loadRefCodeCCAM();
    $this->getLinkedActes();
    if(count($this->_linked_actes) > 3) {
      $this->_guess_association = "?";
      $this->_guess_regle_asso  = "?";
      return $this->_guess_association;
    }
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
    $membresDiff = false;
    
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

    if(!$this->_id) {
      $this->_guess_association = "-";
      $this->_guess_regle_asso  = "-";
      return $this->_guess_association;
    }
    
    // Cas d'un seul actes (règle A)
    if($numActes == 1) {
      $this->_guess_association = "";
      $this->_guess_regle_asso  = "A";
      return $this->_guess_association;
    }
    
    // 1 actes + 1 acte du chap. 18 ou du chap. 19.02 (règles B) / Non présent pour le mode Siemens
    if($numActes == 2 && !(CAppUI::conf("dPpmsi systeme_facturation") == "siemens")) {
      // 1 acte + 1 geste complémentaire chap. 18 (règle B)
      if($numChap18 == 1) {
        $this->_guess_association = "";
        $this->_guess_regle_asso  = "B";
        return $this->_guess_association;
      }
      // 1 acte + 1 supplément des chap. 19.02 (règle B)
      if($numChap1902 == 1) {
        $this->_guess_association = "";
        $this->_guess_regle_asso  = "B";
        return $this->_guess_association;
      }
    }
    
    // 1 acte + 1 ou pls geste complémentaire chap. 18 + 1 ou pls supplément des chap. 19.02 (règle C)
    if($numActes >= 3 && $numActes - ($numChap18 + $numChap1902) == 1 && $numChap18 && $numChap1902) {
      $this->_guess_association = "1";
      $this->_guess_regle_asso  = "C";
      return $this->_guess_association;
    }
    
    // 1 acte + pls supplément des chap. 19.02 (règle D)
    if($numActes >= 3 && $numActes - $numChap1902 == 1) {
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
          if($this->_ref_code_ccam->chapitres[0] == "18" || ($this->_ref_code_ccam->chapitres[0] == "19" && $this->_ref_code_ccam->chapitres[1] == "02")) {
            $this->_guess_association = "1";
            $this->_guess_regle_asso  = "F";
          } else {
            $this->_guess_association = "2";
            $this->_guess_regle_asso  = "F";
          }
          break;
        case 2 :
          if($this->_ref_code_ccam->chapitres[0] == "18" || ($this->_ref_code_ccam->chapitres[0] == "19" && $this->_ref_code_ccam->chapitres[1] == "02")) {
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
    
    // 2 actes des chap. 01, 04, 11 ou 15 sur des membres différents (règle G)
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
    $this->loadRefCodeCCAM();
    $this->_tarif = $this->_ref_code_ccam->activites[$this->code_activite]->phases[$this->code_phase]->tarif;
    $coeffAsso    = $this->_ref_code_ccam->getCoeffAsso($this->code_association);
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