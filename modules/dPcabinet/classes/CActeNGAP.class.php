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

  // Distant fields
  public $_libelle;

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
    $props["code"]                = "str notNull maxLength|3";
    $props["quantite"]            = "num notNull maxLength|2";
    $props["coefficient"]         = "float notNull";
    $props["demi"]                = "enum list|0|1 default|0";
    $props["complement"]          = "enum list|N|F|U";
    $props["lettre_cle"]          = "enum list|0|1 default|0";
    $props["lieu"]                = "enum list|C|D default|C";
    $props["exoneration"]         = "enum list|N|13|17 default|N";
    $props["ald"]                 = "enum list|0|1 default|0";
    $props["numero_dent"]         = "num min|11 max|85";
    $props["comment"]             = "str";

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
      "-". $this->complement;
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
    $ds = CSQLDataSource::get("ccamV2");
    $query = "SELECT `tarif` 
      FROM `codes_ngap` 
      WHERE `code` = %";
    $query = $ds->prepare($query, $this->code);

    $this->montant_base = $ds->loadResult($query);
    $this->montant_base *= $this->coefficient;
    $this->montant_base *= $this->quantite;

    if ($this->demi) {
      $this->montant_base /= 2;
    }

    if ($this->complement == "F") {
      $this->montant_base += 19.06;
    }

    if ($this->complement == "N") {
      $this->montant_base += 25;
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
      WHERE CODE = % ";
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
}
