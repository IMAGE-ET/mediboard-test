<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Actes NGAP concrets pouvant être associé à n'importe quel codable
 */
class CActeNGAP extends CActe {
  // DB key
  public $acte_ngap_id;

  // DB fields
  public $quantite;
  public $code;
  public $coefficient;
  public $demi;
  public $complement;
  public $lettre_cle;
  public $lieu;
  public $exoneration;
  public $ald;
  public $numero_dent;
  public $comment;
  public $major_pct;
  public $major_coef;
  public $minor_pct;
  public $minor_coef;
  public $numero_forfait_technique;
  public $numero_agrement;
  public $rapport_exoneration;
  public $prescripteur_id;

  // Distant fields
  public $_libelle;

  // Tarif final
  public $_tarif;

  public $_ref_prescripteur;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = 'acte_ngap';
    $spec->key    = 'acte_ngap_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["code"]                     = "str notNull maxLength|5";
    $props["quantite"]                 = "num notNull maxLength|2";
    $props["coefficient"]              = "float notNull";
    $props["demi"]                     = "enum list|0|1 default|0";
    $props["complement"]               = "enum list|N|F|U";
    $props["lettre_cle"]               = "enum list|0|1 default|0";
    $props["lieu"]                     = "enum list|C|D default|C";
    $props["exoneration"]              = "enum list|N|13|17 default|N";
    $props["ald"]                      = "enum list|0|1 default|0";
    $props["numero_dent"]              = "num min|11 max|85";
    $props["comment"]                  = "str";
    $props["major_pct"]                = "num";
    $props["major_coef"]               = "float";
    $props["minor_pct"]                = "num";
    $props["minor_coef"]               = "float";
    $props["numero_forfait_technique"] = "num min|1 max|99999";
    $props["numero_agrement"]          = "num min|1 max|99999999999999";
    $props["rapport_exoneration"]      = "enum list|4|7|C|R";
    $props['prescripteur_id']          = 'ref class|CMediusers';
    $props['_tarif']                   = 'currency';

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    // Vue codée
    $this->_shortview = $this->quantite > 1 ? "{$this->quantite}x" : "";
    $this->_shortview.= $this->code;
    if ($this->coefficient != 1) {
      $this->_shortview.= $this->coefficient;
    }
    if ($this->demi) {
      $this->_shortview.= "/2";
    }

    $this->_view = "Acte NGAP $this->_shortview";
    if ($this->object_class && $this->object_id) {
      $this->_view .= " de $this->object_class-$this->object_id";
    }

    $this->_tarif = round($this->montant_base + $this->montant_depassement, 2);
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();

    if ($this->code) {
      $this->code = strtoupper($this->code);
    }
  }

  /**
   * Prepare un acte NGAP vierge en vue d'être associé à un codable
   *
   * @param CCodable $codable Codable ciblé
   *
   * @return CActeNGAP
   */
  static function createEmptyFor(CCodable $codable) {
    $acte = new self;
    $acte->setObject($codable);
    $acte->quantite    = 1;
    $acte->coefficient = 1;
    $acte->loadListExecutants();
    $acte->loadExecution();

    if ($acte->object_class == 'CConsultation' && $acte->_ref_object->sejour_id) {
      $sejour = $acte->_ref_object->loadRefSejour();
      $acte->prescripteur_id = $sejour->praticien_id;
    }

    return $acte;
  }

  /**
   * @see parent::makeFullCode()
   */
  function makeFullCode() {
    return $this->_full_code =
      $this->quantite.
      "-". $this->code.
      "-". $this->coefficient.
      "-". $this->montant_base.
      "-". str_replace("-", "*", $this->montant_depassement).
      "-". $this->demi.
      "-". $this->complement.
      "-" . $this->gratuit;
  }

  /**
   * @see parent::setFullCode()
   */
  function setFullCode($code) {
    $details = explode("-", $code);

    $this->quantite    = $details[0];
    $this->code        = $details[1];
    $this->coefficient = $details[2];

    if (count($details) >= 4) {
      $this->montant_base = $details[3];
    }

    if (count($details) >= 5) {
      $this->montant_depassement = str_replace("*", "-", $details[4]);
    }

    if (count($details) >= 6) {
      $this->demi = $details[5];
    }

    if (count($details) >= 7) {
      $this->complement = $details[6];
    }

    if (count($details) >= 8) {
      $this->gratuit = $details[7];
    }

    $this->getLibelle();
    if (!$this->lettre_cle) {
      $this->lettre_cle = 0;
    }

    $this->updateFormFields();
  }

  /**
   * @see parent::getPrecodeReady()
   */
  function getPrecodeReady() {
    return $this->quantite && $this->code && $this->coefficient;
  }

  /**
   * @see parent::check()
   */
  function check() {
    if ($msg = $this->checkCoded()) {
      return $msg;
    }

    if ($this->code) {
      /* Check if the act exists */
      $ds = CSQLDataSource::get("ccamV2");
      $query = "SELECT *
      FROM `codes_ngap`
      WHERE `code` = ?;";
      $query = $ds->prepare($query, $this->code);
      $res = $ds->loadResult($query);
      if (empty($res)) {
        return 'CActeNGAP-unknown';
      }

      /* Check if the act is deprecated */
      $query = "SELECT COUNT(t.`code`)
      FROM `codes_ngap` as c, `tarif_ngap` as t
      WHERE c.`code` = ? AND t.`code` = c.`code`;";
      $query = $ds->prepare($query, $this->code);
      $res = $ds->loadResult($query);
      if ($res == 0) {
        CAppUI::setMsg('CActeNGAP-deprecated', UI_MSG_WARNING, $this->code);
      }
    }

    return parent::check();
  }

  /**
   * @see parent::canDeleteEx()
   */
  function canDeleteEx() {
    if ($msg = $this->checkCoded()) {
      return $msg;
    }

    return parent::canDeleteEx();
  }

  /**
   * Calcule le montant de base de l'acte
   *
   * @return float
   */
  function updateMontantBase() {
    $this->loadRefExecutant();
    $this->_ref_executant->loadRefFunction();
    $zone = self::getZone($this->_ref_executant->_ref_function);

    if ($this->gratuit) {
      return $this->montant_base = 0;
    }
    else {
      $this->_ref_executant->spec_cpam_id ? $spe = $this->_ref_executant->spec_cpam_id : $spe = 1;

      $ds = CSQLDataSource::get("ccamV2");
      $query = "SELECT t.`tarif`, t.`maj_nuit`, t.`maj_ferie`
      FROM `tarif_ngap` AS t, `specialite_to_tarif_ngap` AS s
      WHERE t.`code` = ?1 AND t.`zone` = ?2 AND s.`specialite` = ?3 AND t.`tarif_ngap_id` = s.`tarif_id`;";
      $query = $ds->prepare($query, $this->code, $zone, $spe);

      $res = $ds->loadList($query);
      if (empty($res)) {
        $res[0] = array('tarif' => 0, 'maj_ferie' => 0, 'maj_nuit' => 0);
      }

      $this->montant_base = $res[0]['tarif'];
      $this->montant_base *= $this->coefficient;
      $this->montant_base *= $this->quantite;

      if ($this->demi) {
        $this->montant_base /= 2;
      }

      if ($this->complement == "F") {
        $this->montant_base += $res[0]['maj_ferie'];
      }

      if ($this->complement == "N") {
        $this->montant_base += $res[0]['maj_nuit'];
      }
    }

    return $this->montant_base;
  }

  /**
   * Produit le libellé NGAP complet de l'acte
   *
   * @return string
   */
  function getLibelle() {
    $ds = CSQLDataSource::get("ccamV2");
    $query = "SELECT `libelle`, `lettre_cle`
      FROM codes_ngap 
      WHERE CODE = ? ";
    $query = $ds->prepare($query, $this->code);

    $this->_libelle = "Acte inconnu ou supprimé";

    $hash = $ds->loadHash($query);
    if ($hash) {
      $this->_libelle = $hash['libelle'];
      /* on récupère au passage la lettre cle pour l'utiliser
       * dans le remplissage automatique de ce champ dans la cotation
       */
      $this->lettre_cle = $hash['lettre_cle'];
    }
    return $this->_libelle;
  }

  /**
   * Return the zone (metropole, antilles, guyane-reunion, mayotte) by using the zip code of the CFunctions or of the CGroups
   *
   * @param CFunctions $function The function
   *
   * @return string
   */
  static function getZone(CFunctions $function) {
    if ($function->cp) {
      $cp = $function->cp;
    }
    else {
      $cp = CGroups::loadCurrent()->cp;
    }
    $cp = ceil($cp / 10);
    if ($cp == 971 || $cp == 972) {
      $zone = 'antilles';
    }
    elseif ($cp == 973 || $cp == 974) {
      $zone = 'guyane-reunion';
    }
    elseif ($cp == 976) {
      $zone = 'mayotte';
    }
    else {
      $zone = 'metro';
    }

    return $zone;
  }

  /**
   * Création d'un item de facture pour un code ngap
   *
   * @param CFacture $facture la facture
   * @param string   $date    date à défaut
   *
   * @return string|null
   */
  function creationItemsFacture($facture, $date) {
    $ligne = new CFactureItem();
    $ligne->libelle       = $this->_libelle;
    $ligne->code          = $this->code;
    $ligne->type          = $this->_class;
    $ligne->object_id     = $facture->_id;
    $ligne->object_class  = $facture->_class;
    $ligne->date          = $date;
    $ligne->montant_base  = $this->montant_base;
    $ligne->montant_depassement = $this->montant_depassement;
    $ligne->quantite      = $this->quantite;
    $ligne->coeff         = $this->coefficient;
    return $ligne->store();
  }

  /**
   * Load the prescriptor
   *
   * @return CMediusers
   */
  public function loadRefPrescripteur() {
    /** @var CMediusers $prescripteur */
    $prescripteur = $this->loadFwdRef('prescripteur_id', true);
    $prescripteur->loadRefFunction();
    return $this->_ref_prescripteur = $prescripteur;
  }
}
