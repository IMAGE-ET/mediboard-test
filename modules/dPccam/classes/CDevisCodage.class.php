<?php

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */
 
/**
 * Description
 */
class CDevisCodage extends CCodable {
  /**
   * @var integer Primary key
   */
  public $devis_codage_id;

  /**
   * @var string The class of the codable object linked to the devis
   */
  public $codable_class;

  /**
   * @var integer The id of the codable object linked to the devis
   */
  public $codable_id;

  /**
   * @var integer The id of the patient
   */
  public $patient_id;

  /**
   * @var integer The id of the responsible practitioner
   */
  public $praticien_id;

  /**
   * @var string The date of the creation of the devis
   */
  public $creation_date;

  /**
   * @var string The date of the event
   */
  public $date;

  /**
   * @var string The type of event, Consultation or Operation
   */
  public $event_type;

  /**
   * @var string A libelle for the devis
   */
  public $libelle;

  /**
   * @var string a comment on the devis
   */
  public $comment;

  /**
   * @var float The amount of the total price equal to the sum of the base fare of the acts
   */
  public $base;

  /**
   * @var float The amount of the total price above the base fare
   */
  public $dh;

  /**
   * @var float The amount of the total price on which the tax rate is applied
   */
  public $ht;

  /**
   * @var float The tax rate applied to the ht
   */
  public $tax_rate;

  /**
   * @var float
   */
  public $_ttc;

  /**
   * @var float The total price
   */
  public $_total;

  /**
   * @var CCodable The codable object
   */
  public $_ref_codable;

  /**
   * @var string The formfield, for compability with other codable
   */
  public $_date;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "devis_codage";
    $spec->key    = "devis_codage_id";
    return $spec;  
  }

  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();

    $props['codable_class'] = 'str notNull class';
    $props['codable_id'] = 'ref notNull class|CCodable meta|codable_class';
    $props['patient_id']    = 'ref notNull class|CPatient';
    $props['praticien_id']  = 'ref notNull class|CMediusers';
    $props['creation_date'] = 'dateTime notNull';
    $props['date']          = 'date';
    $props['event_type']    = 'enum list|CConsultation|COperation';
    $props['libelle']       = 'str';
    $props['comment']       = 'text helped';
    $props['base']          = 'currency min|0 show|0';
    $props['dh']            = 'currency min|0 show|0';
    $props['ht']            = 'currency min|0 show|0';
    $props['tax_rate']      = 'float';
    $props['_ttc']          = 'currency min|0 show|0';
    $props['_total']        = 'currency min|0 show|0';

    return $props;
  }

  public function delete() {
    $this->loadRefsCodagesCCAM();

    foreach ($this->_ref_codages_ccam as $_codage_by_prat) {
      foreach ($_codage_by_prat as $_codage) {
        $_codage->delete();
      }
    }

    return parent::delete();
  }

  public function updateFormFields() {
    parent::updateFormFields();

    $this->_ttc = round($this->ht * $this->tax_rate / 100, 2);
    $this->_total = round($this->base + $this->dh + $this->_ttc, 2);
    $this->_praticien_id = $this->praticien_id;
    $this->getActeExecution();
  }

  /**
   * Register all the template properties (distant and genuine)
   *
   * @param CTemplateManager $template
   *
   * @return void
   *
   * @see parent::fillTemplate()
   */
  public function fillTemplate(&$template) {
    $this->updateFormFields();
    $this->loadRefPatient();
    $this->loadRefPraticien();

    $this->_ref_patient->fillTemplate($template);
    $this->_ref_praticien->fillTemplate($template);
    $this->fillLimitedTemplate($template);
  }

  /**
   * Register the object's template properties
   *
   * @param CTemplateManager $template
   *
   * @return void
   *
   * @see parent::fillTemplate()
   */
  public function fillLimitedTemplate(&$template) {
    $this->notify("BeforeFillLimitedTemplate", $template);

    $template->addDateProperty('Devis - Date', $this->date);
    $template->addProperty('Devis - Type d\'événement', $this->event_type);
    $template->addProperty('Devis - Libellé', $this->libelle);
    $template->addProperty('Devis - Actes NGAP', $this->printActesNGAP(), '', false);
    $template->addProperty('Devis - Actes CCAM', $this->printActesCCAM(), '', false);
    $template->addProperty('Devis - Frais divers', $this->printFraisDivers(), '', false);
    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed")) {
      $this->loadRefsActes();
      $template->addProperty("Devis - TARMED - codes"            , CActeTarmed::actesHtml($this), '', false);
      $template->addProperty("Devis - Caisse - codes"            , CActeCaisse::actesHtml($this), '', false);
    }
    $template->addProperty('Devis - Commentaire', $this->comment);
    $template->addProperty('Devis - Base', $this->base);
    $template->addProperty('Devis - Dépassements d\'honoraire', $this->dh);
    $template->addProperty('Devis - Hors taxe', $this->ht);
    $template->addProperty('Devis - TTC', $this->_ttc);
    $template->addProperty('Devis - Total', $this->_total);

    $this->notify("AfterFillLimitedTemplate", $template);
  }

  /**
   * Update the different amounts (base, dh) from the linked acts
   *
   * @return null|string
   */
  public function doUpdateMontants() {
    $this->loadRefsActes();
    $this->loadRefsFraisDivers();

    $this->base = 0;
    $this->dh = 0;
    $this->ht = 0;

    foreach ($this->_ref_actes as $_acte) {
      $this->base += $_acte->montant_base;
      $this->dh += $_acte->montant_depassement;
    }

    foreach ($this->_ref_frais_divers as $_frais) {
      $this->ht += $_frais->montant_base + $_frais->montant_depassement;
    }

    return $this->store();
  }

  /**
   * Calcul de la date d'execution de l'acte
   *
   * @return void
   */
  function getActeExecution() {
    $this->_acte_execution = $this->date . ' ' . CMbDT::time();

    return $this->_acte_execution;
  }

  /**
   * Load the linked codable object
   *
   * @param bool $cache
   *
   * @return CCodable|null
   */
  public function loadRefCodable($cache = true) {
    if (! $this->_ref_codable) {
      $this->_ref_codable = $this->loadFwdRef('codable_id', $cache);
    }

    return $this->_ref_codable;
  }
  
  /**
   * Load the responsible practitioner
   *
   * @param bool $cache Utilisation du cache
   *
   * @return CMediusers
   */
  function loadRefPraticien($cache = true) {
    if (!$this->_ref_praticien) {
      $this->_ref_praticien = $this->loadFwdRef('praticien_id', $cache);
      $this->_ref_executant = $this->_ref_praticien;
    }
    
    return $this->_ref_praticien;
  }

  /**
   * Load the linked patient
   *
   * @param bool $cache
   *
   * @return CPatient|null
   */
  public function loadRefPatient($cache = true) {
    if (!$this->_ref_patient) {
      $this->_ref_patient = $this->loadFwdRef('patient_id', $cache);
    }

    return $this->_ref_patient;
  }

  /**
   * @see parent::getExecutantId()
   */
  function getExecutantId($code_activite = null) {
    return $this->praticien_id;
  }

  /**
   * Format an html output of the NGAP acts for the documents fields
   *
   * @return string
   */
  public function printActesNGAP() {
    $this->loadRefsActesNGAP();

    $html = '<table>';

    foreach ($this->_ref_actes_ngap as $_acte) {
      $html .= "<tr><td>$_acte->quantite x<strong> $_acte->code</strong></td>";
      $html .= "<td>$_acte->coefficient</td><td>" . ($_acte->montant_base + $_acte->montant_depassement) . "</td></tr>";
    }


    return $html . '</table>';
  }

  /**
   * Format an html output of the CCAM acts for the documents fields
   *
   * @return string
   */
  public function printActesCCAM() {
    $this->loadRefsActesCCAM();

    $html = '<table>';

    foreach ($this->_ref_actes_ccam as $_acte) {
      $html .= "<tr><td><strong>$_acte->code_acte</strong></td><td><span class=\"circled\">$_acte->code_activite - $_acte->code_phase</span></td>";
      $html .= '<td>Asso : ' . CAppUI::tr("CActeCCAM.code_association.$_acte->code_association") . '</td>';
      $html .= "<td>$_acte->modificateurs</td><td>$_acte->_tarif</td></tr>";
    }

    return $html . '</table>';
  }

  /**
   * Format an html output of the diverse costs for the documents fields
   *
   * @return string
   */
  public function printFraisDivers() {
    $this->loadRefsFraisDivers();

    $html = '<table>';

    foreach ($this->_ref_frais_divers as $_frais) {
      $_frais->loadRefType();
      $html .= "<tr><td>$_frais->quantite x <strong>" . $_frais->_ref_type->libelle . '(' . $_frais->_ref_type->code . ")</strong></td>";
      $html .= "<td>$_frais->coefficient</td><td>$_frais->_montant</td></tr>";
    }

    return $html . '</table>';
  }
}
