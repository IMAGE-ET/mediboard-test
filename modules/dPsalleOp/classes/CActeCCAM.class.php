<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
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
  public $acte_id;

  // DB Fields
  public $code_acte;
  public $code_activite;
  public $code_phase;
  public $modificateurs;
  public $motif_depassement;
  public $commentaire;
  public $code_association;
  public $extension_documentaire;
  public $rembourse;
  public $charges_sup;
  public $regle;
  public $regle_dh;
  public $signe;
  public $sent;
  public $exoneration;
  public $lieu;
  public $ald;
  public $position_dentaire;
  public $numero_forfait_technique;
  public $numero_agrement;
  public $rapport_exoneration;
  public $accord_prealable;
  public $date_demande_accord;

  // Derived fields
  public $_modificateurs = array();
  public $_dents         = array();
  public $_rembex;
  public $_anesth;
  public $_anesth_associe;
  public $_tarif_base;
  public $_tarif_sans_asso;
  public $_tarif_base2;
  public $_tarif_sans_asso2;
  public $_tarif;
  public $_activite;
  public $_phase;
  public $_position;
  public $_guess_facturable;
  public $_guess_association;
  public $_guess_regle_asso;
  public $_exclusive_modifiers;

  // Behaviour fields
  public $_adapt_object = false;
  public $_calcul_montant_base = false;
  public $_edit_modificateurs = false;

  // References
  /** @var  CDatedCodeCCAM */
  public $_ref_code_ccam;
  /** @var  CCodable */
  public $_ref_object;
  /** @var CCodageCCAM */
  public $_ref_codage_ccam;

  // Collections
  /** @var  CActeCCAM[] */
  public $_ref_siblings;
  /** @var  CActeCCAM[] */
  public $_linked_actes;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'acte_ccam';
    $spec->key   = 'acte_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    // DB fields
    $props["code_acte"]                = "code notNull ccam seekable";
    $props["code_activite"]            = "num notNull min|0 max|99";
    $props["code_phase"]               = "num notNull min|0 max|99";
    $props["modificateurs"]            = "str maxLength|4";
    $props["motif_depassement"]        = "enum list|d|e|f|n";
    $props["commentaire"]              = "text helped";
    $props["code_association"]         = "enum list|1|2|3|4|5";
    $props["extension_documentaire"]   = "enum list|1|2|3|4|5|6";
    $props["rembourse"]                = "bool default|1";
    $props["charges_sup"]              = "bool";
    $props["regle"]                    = "bool default|0";
    $props["regle_dh"]                 = "bool default|0";
    $props["signe"]                    = "bool default|0";
    $props["sent"]                     = "bool default|0";
    $props["lieu"]                     = "enum list|C|D default|C";
    $props["exoneration"]              = "enum list|N|13|17 default|N";
    $props["ald"]                      = "bool";
    $props["position_dentaire"]        = "str";
    $props["numero_forfait_technique"] = "num min|1 max|99999";
    $props["numero_agrement"]          = "num min|1 max|99999999999999";
    $props["rapport_exoneration"]      = "enum list|4|7|C|R";
    $props['accord_prealable']         = 'bool default|0';
    $props['date_demande_accord']      = 'date';

    // Derived fields
    $props["_rembex"]           = "bool";
    $props["_tarif_base"]       = "currency";
    $props["_tarif_sans_asso"]  = "currency";
    $props["_tarif_base2"]      = "currency";
    $props["_tarif_sans_asso2"] = "currency";
    $props["_tarif"]            = "currency";
    
    return $props;
  }
  
  /**
   * Check the number of codes compared to the number of actes
   *
   * @return string check-like message
   */
  function checkEnoughCodes() {
    $this->loadTargetObject();
    if (!$this->_ref_object || !$this->_ref_object->_id) {
      return null;
    }
    
    $acte = new CActeCCAM();
    $where = array();
    if ($this->_id) {

      // dans le cas de la modification
      $where["acte_id"]     = "<> '$this->_id'";  
    }
    
    $this->completeField("code_acte", "object_class", "object_id", "code_activite", "code_phase");
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
    foreach ($this->_ref_object->_codes_ccam as $code) {
      // si le code est sous sa forme complete, garder seulement le code
      $code = substr($code, 0, 7);
      if ($code == $this->code_acte) {
        $nbCode++;
      }
    }
    if ($siblings >= $nbCode) {
      return "$this->_class-check-already-coded";
    }
    return null;
  }

  /**
   * @see parent::checkCoded()
   */
  function checkCoded() {
    if (CAppUI::conf('dPccam CCodeCCAM use_new_association_rules')) {
      $this->loadRefCodageCCAM();
      if ($this->_ref_codage_ccam->_id && $this->_ref_codage_ccam->locked) {
        return "Codage CCAM verrouillé, impossible de modifier l'acte";
      }
    }
    return parent::checkCoded();
  }

  /**
   * @see parent::canDeleteEx()
   */
  function canDeleteEx(){
    // Test si la consultation est validée
    if ($msg = $this->checkCoded()) {
      return $msg;
    }
    return parent::canDeleteEx();
  }


  /**
   * @see parent::delete()
   */
  function delete() {
    $this->loadRefCodageCCAM();
    if ($msg = parent::delete()) {
      return $msg;
    }
    if (CAppUI::conf('dPccam CCodeCCAM use_new_association_rules')) {
      $this->loadRefCodageCCAM();

      if (isset($this->_ref_codage_ccam)) {
        if ($this->_ref_codage_ccam->_id) {
          $this->_ref_codage_ccam->updateRule(true);
        }
      }
      $this->_ref_codage_ccam->store();
    }

    return null;
  }

  /**
   * @see parent::check()
   */
  function check() {
    // Test si la consultation est validée
    if ($msg = $this->checkCoded()) {
      return $msg;
    }
      
    // Test si on n'a pas d'incompatibilité avec les autres codes
    if ($msg = $this->checkCompat()) {
      return $msg;
    }
    
    if ($msg = $this->checkEnoughCodes()) {
      // Ajoute le code si besoins à l'objet
      if ($this->_adapt_object || $this->_forwardRefMerging) {
        $this->_ref_object->_codes_ccam[] = $this->code_acte;
        $this->_ref_object->updateDBCodesCCAMField();
        
        /*if ($this->_forwardRefMerging) {
          $this->_ref_object->_merging = true;
        }*/
        
        return $this->_ref_object->store();
      }
      return $msg;
    }

    if ($msg = $this->checkExclusiveModifiers()) {
      return $msg;
    }

    if (CAppUI::conf('dPccam CCodeCCAM use_new_association_rules')) {
      $codage_ccam = CCodageCCAM::get($this->_ref_object, $this->executant_id, $this->code_activite, CMbDT::date(null, $this->execution));
      if (!$codage_ccam->_id) {
        $codage_ccam->store();
      }
    }

    return parent::check(); 
    // datetime_execution: attention à rester dans la plage de l'opération
  }
  
  /**
   * @see parent::makeFullCode();
   */
  function makeFullCode() {
    return $this->_full_code = 
      $this->code_acte.
      "-". $this->code_activite.
      "-". $this->code_phase.
      "-". $this->modificateurs.
      "-". str_replace("-", "*", $this->montant_depassement).
      "-". $this->code_association.
      "-". $this->rembourse.
      "-". $this->charges_sup.
      "-". $this->gratuit;
  }

  /**
   * CActe redefinition
   *
   * @param string $code Serialised full code
   *
   * @return void
   */
  function setFullCode($code){
    ml('CActeCCAM::setFullCode()');
    $details = explode("-", $code);
    if (count($details) > 2) {
      $this->code_acte     = $details[0];
      $this->code_activite = $details[1];
      $this->code_phase    = $details[2];
      
      // Modificateurs
      if (count($details) > 3) {
        $modificateurs       = str_split($details[3]);
        $list_modifs_actifs  = str_split(CCodeCCAM::getModificateursActifs());
        $this->modificateurs = implode('', array_intersect($modificateurs, $list_modifs_actifs));
      } 
      
      // Dépassement
      if (count($details) > 4) {
        $this->montant_depassement = str_replace("*", "-", $details[4]);
      }
      
      // Code association
      if (count($details) > 5) {
        $this->code_association = $details[5];
      }
      
      // Remboursement
      if (count($details) > 6) {
        $this->rembourse = $details[6];
      }

      // Charges supplémentaires
      if (count($details) > 7) {
        $this->charges_sup = $details[7];
      }

      // Gratuit
      if (count($details) > 8) {
        $this->gratuit = $details[8];
      }

      $this->updateFormFields();

      if ($this->facturable === null) {
        $this->facturable = 1;
      }
    }
  }

  /**
   * @see parent::getPrecodeReady()
   */
  function getPrecodeReady() {
    return $this->code_acte && $this->code_activite && $this->code_phase !== null;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_modificateurs = str_split($this->modificateurs);
    CMbArray::removeValue("", $this->_modificateurs);
    $this->_dents         = explode("|", $this->position_dentaire);
    $this->_shortview = $this->code_acte;
    $this->_view      = "$this->code_acte-$this->code_activite-$this->code_phase-$this->modificateurs";
    $this->_anesth    = ($this->code_activite == 4);
    
    // Remboursement exceptionnel
    $code = CDatedCodeCCAM::get($this->code_acte, $this->execution);
    $this->_rembex = $this->rembourse && $code->remboursement == 3 ? '1' : '0';
  }

  /**
   * Calcule le montant de base de l'acte
   *
   * @return float
   */
  function updateMontantBase() {
    return $this->montant_base = $this->getTarif();  
  }
  
  /**
   * Check wether acte is compatible with others already coded
   *
   * @return bool
   */
  function checkCompat() {
    if ($this->object_class == "CConsultation" || $this->_permissive) {
      return null;
    }
    $this->loadRefCodeCCAM();
    $this->getLinkedActes(false, false);
    
    /**
    // Cas du nombre d'actes
    // Cas général : 2 actes au plus
    $distinctCodes = array();
    foreach($this->_linked_actes as $_acte) {
      $_acte->loadRefCodeCCAM();
      if(!in_array($_acte->_ref_code_ccam->code, $distinctCodes)) {
        $distinctCodes[] = $_acte->_ref_code_ccam->code;
      }
    }
    if(count($distinctCodes) >= 2) {
      return "Vous ne pouvez pas coder plus de deux actes";
    }
    */
    
    // Cas des incompatibilités
    if (CAppUI::conf("dPsalleOp CActeCCAM check_incompatibility") != 'allow') {
      foreach ($this->_linked_actes as $_acte) {
        $_acte->loadRefCodeCCAM();
        $_acte->_ref_code_ccam->getActesIncomp();
        $incomps = CMbArray::pluck($_acte->_ref_code_ccam->incomps, "code");
        if (in_array($this->code_acte, $incomps)) {
          $msg = "Acte incompatible avec le codage de " . $_acte->_ref_code_ccam->code;
          if (CAppUI::conf("dPsalleOp CActeCCAM check_incompatibility") == 'block') {
            return $msg;
          }
          else {
            CAppUI::setMsg($msg, UI_MSG_WARNING);
            return null;
          }
        }
      }
      
      // Cas des associations d'anesthésie
      if ($this->_ref_code_ccam->chapitres["1"]["rang"] == "18.01.") {
        $asso_possible = false;
        foreach ($this->_linked_actes as $_acte) {
          $_acte->loadRefCodeCCAM();
          $_acte->_ref_code_ccam->getActivites();
          $activites = CMbArray::pluck($_acte->_ref_code_ccam->activites, "numero");
          if (!in_array("4", $activites)) {
            $asso_possible = true;
          }
        }
        if (!$asso_possible) {
          $msg = "Aucun acte codé ne permet actuellement d'associer une Anesthésie Complémentaire";
          if (CAppUI::conf("dPsalleOp CActeCCAM check_incompatibility") == 'block') {
            return $msg;
          }
          else {
            CAppUI::setMsg($msg, UI_MSG_WARNING);
            return null;
          }
        }
      }

      // Cas du chapitre sur la radiologie vasculaire
      if (
          isset($this->_ref_code_ccam->chapitres['3']) &&
          $this->_ref_code_ccam->chapitres['3']['rang'] == '19.01.09.02.' ||
          in_array($this->code_acte, array('YYYY033', 'YYYY300'))
      ) {
        $possible = true;
        foreach ($this->_linked_actes as $_acte) {
          $codes_incompatibles = array('YYYY033', 'YYYY300');
          if (
              in_array($_acte->code_acte, $codes_incompatibles) && isset($this->_ref_code_ccam->chapitres['3']) &&
              $this->_ref_code_ccam->chapitres['3']['rang'] == '19.01.09.02.'
          ) {
            $possible = false;
          }
          elseif (
              in_array($this->code_acte, $codes_incompatibles) && isset($_acte->_ref_code_ccam->chapitres['3']) &&
              $_acte->_ref_code_ccam->chapitres['3']['rang'] == '19.01.09.02.'
          ) {
            $possible = false;
          }
        }
        if (!$possible) {
          $msg = "Un acte du chapitre 19.01.09.02 (Radiologie vasculaire et imagerie interventionnelle) ne peut pas être associé avec les actes YYYY030 et YYYY300";
          if (CAppUI::conf("dPsalleOp CActeCCAM check_incompatibility") == 'block') {
            return $msg;
          }
          else {
            CAppUI::setMsg($msg, UI_MSG_WARNING);
          }
        }
      }
    }
    return null;
  }

  /**
   * Check wether acte is facturable
   *
   * @return bool
   */
  function checkFacturable() {
    $this->completeField("facturable");

    // Si acte non facturable on met le code d'asso à aucun
    if (!$this->facturable) {
      $this->code_association = "";
      $this->modificateurs = "";
    }

    // Si on repasse le facturable à 1 on remet à la montant base à la valeur de l'acte et le dépassement à 0
    if ($this->fieldModified("facturable", 1)) {
      $this->montant_depassement  = 0;
      $this->motif_depassement    = "";
      $this->_calcul_montant_base = true;
    }
    return $this->facturable;
  }

  /**
   * Check if there is only one modifier F, U, P or S coded on the act and it's linked acts
   *
   * @return null|string
   */
  function checkExclusiveModifiers() {
    $this->getLinkedActes(1, 1, 1);

    $exclusive_modifiers = array('F', 'U', 'P', 'S');

    $count_exclusive_modifiers = count(array_intersect($this->_modificateurs, $exclusive_modifiers));
    foreach ($this->_linked_actes as $_linked_acte) {
      $count_exclusive_modifiers += count(array_intersect($_linked_acte->_modificateurs, $exclusive_modifiers));
    }

    if ($count_exclusive_modifiers > 1) {
      return 'Les modificateurs F, P, S et U sont exclusifs les uns des autres, et ne peuvent être facturés qu\'une seule fois par praticien, quel que soit le nombre d \'actes réalisés';
    }

    return null;
  }

  /**
   * @see parent::store()
   */
  function store() {
    // Chargement du oldObject
    $oldObject = new CActeCCAM();
    $oldObject->load($this->_id);
    // On test si l'acte CCAM est facturable
    $this->checkFacturable();

    // Sauvegarde du montant de base
    if ($this->_calcul_montant_base) {
      $this->updateFormFields();
      $this->updateMontantBase();
    }

    // En cas d'une modification autre que signe, on met signe à 0
    if (!$this->signe) {
    
      // Parcours des objets pour detecter les modifications
      $_modif = 0;
      foreach ($oldObject->getPlainFields() as $propName => $propValue) {
        if (($this->$propName !== null) && ($propValue != $this->$propName)) {
          $_modif++;
        }
      }
      if ($_modif) {
        $this->signe = 0;
      }
    }

    if (CAppUI::conf('dPccam CCodeCCAM use_new_association_rules')) {
      // Vérification de l'existence du codage
      $date = null;
      if ($this->object_class == 'CSejour') {
        $date = CMbDT::date(null, $this->execution);
      }
      $codage = CCodageCCAM::get($this->loadRefObject(), $this->executant_id, $this->code_activite, $date);
      if (!$codage->_id) {
        if ($msg = $codage->store()) {
          return $msg;
        }
      }
    }
    
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }

    if (CAppUI::conf('dPccam CCodeCCAM use_new_association_rules')) {
      // Si on crée un nouvel acte, on relance l'analyse du codage
      if (!$oldObject->_id && !$this->code_association) {
        $codage->updateRule(true);
        if ($msg = $codage->store()) {
          return $msg;
        }
      }
    }

    return null;
  }

  /**
   * Charge le codable asssocié
   *
   * @todo Rename as CActe::loadRefCodable()
   * @return CCodable
   */
  function loadRefObject() {
    return $this->loadTargetObject();
  }

  /**
   * Charge le code CCAM complet tel que décrit par la nomenclature
   *
   * @return CDatedCodeCCAM
   */
  function loadRefCodeCCAM() {
    return $this->_ref_code_ccam = CDatedCodeCCAM::get($this->code_acte, $this->execution);
  }

  /**
   * Charge le codage CCAM associé
   *
   * @return CCodageCCAM|null
   */
  function loadRefCodageCCAM() {
    $this->loadRefObject();
    if (isset($this->_ref_object)) {
      return $this->_ref_codage_ccam = CCodageCCAM::get($this->_ref_object, $this->executant_id, $this->code_activite, CMbDT::date(null, $this->execution));
    }
    return null;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();

    $this->loadRefExecutant();
    $this->loadRefCodeCCAM();
    if (CAppUI::conf('dPccam CCodeCCAM use_new_association_rules')) {
      $this->loadRefCodageCCAM();
    }
  }

  /**
   * Trouve le code CCAM d'anesthésie associée
   *
   * @return string|null
   */
  function getAnesthAssocie() {
    if (!$this->_ref_code_ccam) {
      $this->loadRefsFwd();
    }

    if ($this->code_activite != 4 && !isset($this->_ref_code_ccam->activites[4])) {
      foreach ($this->_ref_code_ccam->assos as $code_anesth) {
        if (substr($code_anesth["code"], 0, 4) == "ZZLP") {
          $this->_anesth_associe = $code_anesth["code"];
          return $this->_anesth_associe;
        }
      }
    }
    return null;
  }

  /**
   * Charge les codes favoris d'un utilisateur
   *
   * @param ref    $user_id Idenfiant d'utilisateur
   * @param string $class   Classe de contexte codable
   *
   * @return string[]
   */
  function getFavoris($user_id, $class) {
    $condition = ( $class == "" ) ? "executant_id = '$user_id'" : "executant_id = '$user_id' AND object_class = '$class'";
    $sql = "SELECT code_acte, object_class, COUNT(code_acte) as nb_acte
      FROM acte_ccam
      WHERE $condition
      GROUP BY code_acte
      ORDER BY nb_acte DESC
      LIMIT 20";
    $codes = $this->_spec->ds->loadlist($sql);
    return $codes;
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    return $this->loadRefObject()->getPerm($permType);
  }


  /**
   * Charge les autre actes du même codable
   *
   * @param bool $same_executant  Seulement les actes du même exécutant si vrai
   * @param bool $only_facturable Seulement les actes facturables si vrai
   * @param bool $same_activite   Seulement les actes ayant la même activité (4 ou (1,2,3,5))
   *
   * @return CActeCCAM[]
   */
  function getLinkedActes($same_executant = true, $only_facturable = true, $same_activite = false) {
    $acte = new CActeCCAM();
    
    $where = array();
    $where["acte_id"]       = "<> '$this->_id'";
    $where["object_class"]  = "= '$this->object_class'";
    $where["object_id"]     = "= '$this->object_id'";
    if ($only_facturable) {
      $where["facturable"]    = "= '1'";
    }
    if ($same_executant) {
      $where["executant_id"]  = "= '$this->executant_id'";
    }
    if ($same_activite) {
      if ($this->code_activite == 4) {
        $where['code_activite'] = " = 4";
      }
      else {
        $where['code_activite'] = " IN(1, 2, 3, 5)";
      }
    }

    $this->_linked_actes = $acte->loadList($where);
    return $this->_linked_actes;
  }

  /**
   * Devine les associations de l'acte en fonction des autres actes du même codable
   *
   * @return string
   */
  function guessAssociation() {
    /*
     * Calculs initiaux
     */
    // Chargements initiaux
    if (!$this->facturable) {
      $this->_guess_association = "";
      $this->_guess_regle_asso  = "X";

      return $this->_guess_association;
    }

    $this->loadRefCodeCCAM();
    $this->getLinkedActes();
    foreach ($this->_linked_actes as $_acte) {
      $_acte->loadRefCodeCCAM();
    }
    
    // Nombre d'actes
    $numActes = count($this->_linked_actes) + 1;
    
    // Calcul de la position tarifaire de l'acte
    $tarif = $this->getTarifSansAssociationNiCharge();
    $orderedActes = array();
    $orderedActes[$this->_id] = $tarif;
    foreach ($this->_linked_actes as $_acte) {
      $tarif = $_acte->getTarifSansAssociationNiCharge();
      $orderedActes[$_acte->_id] = $tarif;
    }
    ksort($orderedActes);
    arsort($orderedActes);
    $this->_position = array_search($this->_id, array_keys($orderedActes));

    // Récupération des informations d'application des règles

    $chapitres = $this->_ref_code_ccam->chapitres;
    
    // Nombre d'actes des chap. 12, 13 et 14 (chirurgie membres, tronc et cou)
    $numChap121314 = 0;
    if (in_array($chapitres[0]["db"], array("000012", "000013", "000014"))) {
      $numChap121314++;
    }

    foreach ($this->_linked_actes as $_linked_acte) {
      $linked_chapitres = $_linked_acte->_ref_code_ccam->chapitres;
      if (in_array($linked_chapitres[0]["db"], array("000012", "000013", "000014"))) {
        $numChap121314++;
      }
    }
    
    // Nombre d'actes du chap. 18.01
    $numChap1801 = 0;
    if ($chapitres[0]["db"] == "000018" && $chapitres[1]["db"] == "000001") {
      $numChap1801++;
    }
    foreach ($this->_linked_actes as $_linked_acte) {
      $linked_chapitres = $_linked_acte->_ref_code_ccam->chapitres;
      if ($linked_chapitres[0]["db"] == "000018" && $linked_chapitres[1]["db"] == "000001") {
        $numChap1801++;
      }
    }
    
    // Nombre d'actes du chap. 18.02
    $numChap1802 = 0;
    if ($chapitres[0]["db"] == "000018" && $chapitres[1]["db"] == "000002") {
      $numChap1802++;
    }
    foreach ($this->_linked_actes as $_linked_acte) {
      $linked_chapitres = $_linked_acte->_ref_code_ccam->chapitres;
      if ($linked_chapitres[0]["db"] == "000018" && $linked_chapitres[1]["db"] == "000002") {
        $numChap1802++;
      }
    }
    
    // Nombre d'actes du chap. 19.01
    $numChap1901 = 0;
    if ($chapitres[0]["db"] == "000019" && $chapitres[1]["db"] == "000001") {
      $numChap1901++;
    }
    foreach ($this->_linked_actes as $_linked_acte) {
      $linked_chapitres = $_linked_acte->_ref_code_ccam->chapitres;
      if ($linked_chapitres[0]["db"] == "000019" && $linked_chapitres[1]["db"] == "000001") {
        $numChap1901++;
      }
    }
    
    // Nombre d'actes du chap. 19.02
    $numChap1902 = 0;
    if ($chapitres[0]["db"] == "000019" && $chapitres[1]["db"] == "000002") {
      $numChap1902++;
    }
    foreach ($this->_linked_actes as $_linked_acte) {
      $linked_chapitres = $_linked_acte->_ref_code_ccam->chapitres;
      if ($linked_chapitres[0]["db"] == "000019" && $linked_chapitres[1]["db"] == "000002") {
        $numChap1902++;
      }
    }
     
    // Nombre d'actes des chap. 02, 03, 05 à 10, 16, 17
    $numChap02 = 0;
    $listChaps = array("000002", "000003", "000005", "000006", "000007", "000008", "000009", "000010", "000016", "000017");
    if (in_array($chapitres[0]["db"], $listChaps)) {
      $numChap02++;
    }

    foreach ($this->_linked_actes as $_linked_acte) {
      $linked_chapitres = $_linked_acte->_ref_code_ccam->chapitres;
      if (in_array($linked_chapitres[0]["db"], $listChaps)) {
        $numChap02++;
      }
    }
     
    // Nombre d'actes des chap. 01, 04, 11, 15
    $numChap0115 = 0;
    $listChaps = array("000001", "000004", "000011", "000015");
    if (in_array($chapitres[0]["db"], $listChaps)) {
      $numChap0115++;
    }
    foreach ($this->_linked_actes as $_linked_acte) {
      $linked_chapitres = $_linked_acte->_ref_code_ccam->chapitres;
      if (in_array($linked_chapitres[0]["db"], $listChaps)) {
        $numChap0115++;
      }
    }
     
    // Nombre d'actes des chap. 01, 04, 11, 12, 13, 14, 15, 16
    $numChap0116 = 0;
    $listChaps = array("000001", "000004", "000011", "000012", "000013", "000014", "000015", "000016");
    if (in_array($chapitres[0]["db"], $listChaps)) {
      $numChap0116++;
    }
    foreach ($this->_linked_actes as $_linked_acte) {
      $linked_chapitres = $_linked_acte->_ref_code_ccam->chapitres;
      if (in_array($linked_chapitres[0]["db"], $listChaps)) {
        $numChap0116++;
      }
    }
    
    // Le praticien est-il un ORL
    $pratORL = false;
    if ($this->object_class == "COperation") {
      $discipline = $this->loadRefExecutant()->loadRefDiscipline();
      if ($discipline->_compat == "ORL") {
        $pratORL = true;
      }
    }
    
    // Diagnostic principal en S ou T avec lésions multiples
    // Diagnostic principal en C (carcinologie)
    $DPST = false;
    $DPC  = false;
    $membresDiff = false;
    
    if ($this->object_class == "COperation") {
      /** @var COperation $operation */
      $operation = $this->loadRefObject();
      $sejour = $operation->loadRefSejour();
      if ($sejour->DP) {
        if ($sejour->DP[0] == "S" || $sejour->DP[0] == "T") {
          $DPST = true;
          $membresDiff = true;
        }
        if ($sejour->DP[0] == "C") {
          $DPC = true;
        }
      }
      if ($operation->cote == "bilatéral") {
        $membresDiff = true;
      }
    }
    
    // Association d'1 exérèse, d'1 curage et d'1 reconstruction
    $assoEx  = false;
    $assoCur = false;
    $assoRec = false;
    if ($numActes == 3) {
      $libelle = $this->_ref_code_ccam->libelleLong;
      if (stripos($libelle, "exérèse") !== false) {
        $assoEx = true;
      }
      if (stripos($libelle, "curage") !== false) {
        $assoCur = true;
      }
      if (stripos($libelle, "reconstruction") !== false) {
        $assoRec = true;
      }

      foreach ($this->_linked_actes as $_linked_acte) {
        $linked_libelle = $_linked_acte->_ref_code_ccam->libelleLong;
        if (stripos($linked_libelle, "exérèse") !== false) {
          $assoEx = true;
        }
        if (stripos($linked_libelle, "curage") !== false) {
          $assoCur = true;
        }
        if (stripos($linked_libelle, "reconstruction") !== false) {
          $assoRec = true;
        }
      }
    }
    $assoExCurRec = $assoEx && $assoCur && $assoRec;

    /*
     * Application des règles
     */

    $this->_guess_facturable  = "1";
    if (!$this->_id) {
      $this->_guess_association = "-";
      $this->_guess_regle_asso  = "-";
      return $this->_guess_association;
    }
    
    // Cas d'un seul actes (règle A)
    if ($numActes == 1) {
      $this->_guess_association = "";
      $this->_guess_regle_asso  = "A";
      return $this->_guess_association;
    }
    
    // 1 actes + 1 acte du chap. 18.02 ou du chap. 19.02 (règles B)
    if ($numActes == 2) {
      // 1 acte + 1 geste complémentaire chap. 18.02 (règle B)
      if ($numChap1802 == 1) {
        $this->_guess_association = "";
        $this->_guess_regle_asso  = "B";
        return $this->_guess_association;
      }
      // 1 acte + 1 supplément des chap. 19.02 (règle B)
      if ($numChap1902 == 1) {
        $this->_guess_association = "";
        $this->_guess_regle_asso  = "B";
        return $this->_guess_association;
      }
    }
    
     
    // 1 acte + 1 ou pls geste complémentaire chap. 18.02 et/ou 1 ou pls supplément des chap. 19.02 (règle C)
    if ($numActes >= 3 && $numActes - ($numChap1802 + $numChap1902) == 1 && ($numChap1802 || $numChap1902)) {
      $this->_guess_association = "1";
      $this->_guess_regle_asso  = "C";
      return $this->_guess_association;
    }
    
    // 1 acte + pls supplément des chap. 19.02 (règle D)
    if ($numActes >= 3 && $numActes - $numChap1902 == 1) {
      $this->_guess_association = "1";
      $this->_guess_regle_asso  = "D";
      return $this->_guess_association;
    }
    
    // 1 acte + 1 acte des chap. 02, 03, 05 à 10, 16, 17 ou 19.01 (règle E)
    if ($numActes == 2 && ($numChap02 == 1 || $numChap1901 == 1)) {
      switch ($this->_position) {
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
    
    // 1 acte + 1 acte des chap. 02, 03, 05 à 10, 16, 17 ou 19.01 + 1 acte des chap. 18.02 ou 19.02 (règle F)
    if ($numActes == 3 && ($numChap02 == 1 || $numChap1901 == 1) && ($numChap1802 == 1 || $numChap1902 == 1)) {
      switch ($this->_position) {
        case 0 :
          $this->_guess_association = "1";
          $this->_guess_regle_asso  = "F";
          break;
        case 1 :
          if (($chapitres[0] == "18" || $chapitres[0] == "19") && $chapitres[1] == "02") {
            $this->_guess_association = "1";
            $this->_guess_regle_asso  = "F";
          }
          else {
            $this->_guess_association = "2";
            $this->_guess_regle_asso  = "F";
          }
          break;
        case 2 :
          if (($chapitres[0] == "18" || $chapitres[0] == "19") && $chapitres[1] == "02") {
            $this->_guess_association = "1";
            $this->_guess_regle_asso  = "F";
          }
          else {
            $this->_guess_association = "2";
            $this->_guess_regle_asso  = "F";
          }
          break;
      }
      return $this->_guess_association;
    }
    
    // 2 actes des chap. 01, 04, 11 ou 15 sur des membres différents (règle G)
    if ($numActes == 2 && $numChap0115 == 2 && $membresDiff) {
      switch ($this->_position) {
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
    
    // 2 actes des chap. 12, 13 ou 14 sur des membres différents (règle G2)
    if ($numActes == 2 && $numChap121314 == 2 && $membresDiff) {
      switch ($this->_position) {
        case 0 :
          $this->_guess_association = "1";
          $this->_guess_regle_asso  = "G2";
          break;
        case 1 :
          $this->_guess_association = "3";
          $this->_guess_regle_asso  = "G2";
          break;
      }
      return $this->_guess_association;
    }
    
    // 3 actes des chap. 12, 13 ou 14 sur des membres différents (règle G3)
    if ($numActes == 3 && $numChap121314 == 3 && $membresDiff) {
      switch ($this->_position) {
        case 0 :
          $this->_guess_association = "1";
          $this->_guess_regle_asso  = "G3";
          break;
        case 1 :
          $this->_guess_association = "3";
          $this->_guess_regle_asso  = "G3";
          break;
        case 3 :
          $this->_guess_association = "2";
          $this->_guess_regle_asso  = "G3";
          break;
      }
      return $this->_guess_association;
    }
    
    // 3 actes des chap. 01, 04 ou 11 à 16 avec DP en S ou T (lésions traumatiques multiples) (règle H)
    if ($numActes == 3 && $numChap0116 == 3 && $DPST) {
      switch ($this->_position) {
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
    if ($numActes == 3 && $pratORL && $DPC && $assoExCurRec) {
      switch ($this->_position) {
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
    switch ($this->_position) {
      case 0 :
        $this->_guess_association = "1";
        $this->_guess_regle_asso  = "Z";
        break;
      case 1 :
        $this->_guess_association = "2";
        $this->_guess_regle_asso  = "Z";
        break;
      default :
        $this->_guess_association = "";
        $this->_guess_regle_asso  = "Z";
        //$this->_guess_facturable  = "0";
    }
    
    return $this->_guess_association;
  }

  /**
   * Calcul le tarif de l'acte sans association ni charge
   *
   * @return float
   */
  function getTarifSansAssociationNiCharge() {
    // Tarif de base
    $code = $this->loadRefCodeCCAM();
    $phase = $code->activites[$this->code_activite]->phases[$this->code_phase];
    $this->_tarif_base       = $phase->tarif;
    $this->_tarif_sans_asso  = $phase->tarif;
    $this->_tarif_base2      = $phase->tarif2;
    $this->_tarif_sans_asso2 = $phase->tarif2;

    // Application des modificateurs
    $forfait     = 0;
    $coefficient = 100;
    foreach ($this->_modificateurs as $modif) {
      $result = $code->getForfait($modif, $code->date);
      if ($result['forfait'] > 0) {
        $forfait     += $result["forfait"];
      }
      if ($result['coefficient'] > 100) {
        $coefficient += $result["coefficient"] - 100;
      }
    }

    $this->_tarif_sans_asso  = ($this->_tarif_base  * ($coefficient / 100) + $forfait);
    $this->_tarif_sans_asso2 = ($this->_tarif_base2 * ($coefficient / 100) + $forfait);

    if ($this->executant_id && $this->_tarif_sans_asso != $this->_tarif_sans_asso2) {
      $this->loadRefExecutant();
      if ($this->_ref_executant->secteur == 2 && !$this->_ref_executant->contrat_acces_soins) {
        return $this->_tarif_sans_asso;
      }
    }
    return $this->_tarif_sans_asso2;
  }

  /**
   * Calcule le montant des modificateurs
   *
   * @param array $modificateurs Les modificateurs de l'acte
   *
   * @return void
   */
  function getMontantModificateurs($modificateurs) {
    $code = $this->loadRefCodeCCAM();
    $phase = $code->activites[$this->code_activite]->phases[$this->code_phase];
    $this->_tarif_base = $phase->tarif;

    foreach ($modificateurs as $_modificateur) {
      if ($_modificateur->_double == 1) {
        $tarif_modif = $code->getForfait($_modificateur->code, $code->date);
        $_modificateur->_montant = round($this->_tarif_base * ($tarif_modif['coefficient'] - 100) / 100 + $tarif_modif['forfait'], 2);
      }
      else {
        $_montant = 0;
        for ($i = 0; $i < strlen($_modificateur->code); $i++) {
          $tarif_modif = $code->getForfait($_modificateur->code[$i], $code->date);
          $_montant += round($this->_tarif_base * ($tarif_modif['coefficient'] - 100) / 100 + $tarif_modif['forfait'], 2);
        }
        $_modificateur->_montant = $_montant;
      }
    }
  }

  /**
   * Calcul le tarif final de l'acte
   *
   * @return float
   */
  function getTarif() {
    // Coefficient d'association
    $code = $this->loadRefCodeCCAM();

    if ($this->code_activite && !$this->gratuit && $this->facturable) {
      $this->_tarif = $this->getTarifSansAssociationNiCharge();
      $this->_tarif *= ($code->getCoeffAsso($this->code_association) / 100);
      // Charges supplémentaires
      $phase = $code->activites[$this->code_activite]->phases[$this->code_phase];
      if ($this->charges_sup) {
        $this->_tarif += $phase->charges;
      }
    }
    else {
      $this->_tarif = 0;
    }

    return $this->_tarif;
  }

  /**
   * Récupère les codes NGAP associés à un code CCAM
   * Utile pour les cotations dentaires et stomatologiques
   *
   * @param string $code Code CCAM
   *
   * @return array|null Tableau d'information sur les codes NGAP, null si non trouvé
   */
  static function getNGAP($code) {
    $ds = CSQLDataSource::get("ccamV2");
    $query = $ds->prepare("SELECT * FROM ccam_ngap WHERE code_ccam = ?", $code);
    $result = $ds->exec($query);
    
    if ($ds->numRows($result)) {
      $row = $ds->fetchArray($result);
      return array(
        "fd" => array(
          "montant_enfant" => $row["montant_enfant"],
          "montant_adulte" => $row["montant_adulte"],
        ),
        "ngap" => array(
          array(
            "code_ngap_1"   => $row["code_ngap_1"],
            "coefficient_1" => $row["coefficient_1"],
          ),
          array(
            "code_ngap_2"   => $row["code_ngap_2"],
            "coefficient_2" => $row["coefficient_2"],
          ),
          array(
            "code_ngap_3"   => $row["code_ngap_3"],
            "coefficient_3" => $row["coefficient_3"],
          )
        )
      );
    }

    return null;
  }
  
  /**
   * Création d'un item de facture avec un code ccam
   * 
   * @param CFacture $facture La facture
   * @param string   $date    Date à défaut
   * 
   * @return string
  **/
  function creationItemsFacture($facture, $date){
    $this->loadRefCodeCCAM();
    $ligne = new CFactureItem();
    $ligne->libelle       = $this->_ref_code_ccam->libelleCourt;
    $ligne->code          = $this->code_acte;
    $ligne->type          = $this->_class;
    $ligne->object_id     = $facture->_id;
    $ligne->object_class  = $facture->_class;
    $ligne->date          = CMbDT::date($this->execution);
    $ligne->montant_base  = $this->montant_base;
    $ligne->montant_depassement = $this->montant_depassement;
    $ligne->quantite      = 1;
    $ligne->coeff         = $facture->_coeff;
    if ($msg = $ligne->store()) {
      return $msg;
    }
    return null;
  }
}
