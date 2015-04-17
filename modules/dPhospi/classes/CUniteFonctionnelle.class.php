<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Unité fonctionnelle
 */
class CUniteFonctionnelle extends CMbObject {
  // DB Table key
  public $uf_id;
  
  // DB Fields
  public $group_id;
  public $code;
  public $libelle;
  public $description;
  public $type;
  public $type_sejour;
  public $date_debut;
  public $date_fin;
  public $type_autorisation_um;

  /** @var CGroups */
  public $_ref_group;

  /** @var CAffectationUniteFonctionnelle[] */
  public $_ref_affectations_uf;
  
  /** @var CMediusers[] */
  public $_ref_praticiens;

  /** @var CLit[] */
  public $_ref_lits;

  /** @var CChambre */
  public $_ref_chambre;

  /** @var CService */
  public $_ref_service;

  /** @var CUniteMedicale */
  public $_ref_um;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'uf';
    $spec->key   = 'uf_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    static $atih_active = null;
    if ($atih_active === null) {
      $atih_active = CModule::getActive("atih") != null;
    }
    $props = parent::getProps();
    $props["group_id"]              = "ref class|CGroups notNull";
    $props["code"]                  = "str notNull seekable";
    $props["libelle"]               = "str notNull seekable";
    $props["description"]           = "text";
    $props["type"]                  = "enum list|hebergement|soins|medicale default|hebergement";
    $props["type_sejour"]           = "enum list|comp|ambu|exte|seances|ssr|psy|urg|consult";
    $props["date_debut"]            = "date";
    $props["date_fin"]              = "date";
    if ($atih_active) {
      $props["type_autorisation_um"]  = "ref class|CUniteMedicaleInfos";
    }

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["affectations_uf"         ] = "CAffectationUniteFonctionnelle uf_id";

    $backProps["affectations_hebergement"] = "CAffectation uf_hebergement_id";
    $backProps["affectations_medical"    ] = "CAffectation uf_medicale_id";
    $backProps["affectations_soin"       ] = "CAffectation uf_soins_id";
    
    $backProps["sejours_hebergement"     ] = "CSejour uf_hebergement_id";
    $backProps["sejours_medical"         ] = "CSejour uf_medicale_id";
    $backProps["sejours_soin"            ] = "CSejour uf_soins_id";
    
    $backProps["protocoles_hebergement"  ] = "CProtocole uf_hebergement_id";
    $backProps["protocoles_medical"      ] = "CProtocole uf_medicale_id";
    $backProps["protocoles_soin"         ] = "CProtocole uf_soins_id";
    $backProps["rum"                     ] = "CRUM um_id";

    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
  }

  /**
   * @return CUniteMedicaleInfos
   */
  function loadRefUm() {
    if (!CModule::getActive("atih")) {
      return null;
    }
    return $this->_ref_um = $this->loadFwdRef("type_autorisation_um", true);
  }

  /**
   * Récupération de l'uf
   *
   * @param string $code_uf  code de l'uf
   * @param string $type     type de l'uf
   * @param int    $group_id group
   * @param string $date_deb date de début
   * @param string $date_fin date de fin
   *
   * @return CUniteFonctionnelle
   */
  static function getUF($code_uf, $type = null, $group_id = null, $date_deb = null, $date_fin = null) {
    $uf = new self;

    if (!$code_uf) {
      return $uf;
    }

    $group_id = $group_id ? $group_id : CGroups::loadCurrent()->_id;

    $where["code"]     = " = '$code_uf'";
    $where["type"]     = " = '$type'";
    $where["group_id"] = " = '$group_id'";

    if ($date_fin) {
      $where[] = "uf.date_debut IS NULL OR uf.date_debut < '".CMbDT::date($date_fin)."'";
    }
    if ($date_deb) {
      $where[] = "uf.date_fin IS NULL OR uf.date_fin > '".CMbDT::date($date_deb)."'";
    }

    $uf->loadObject($where);

    return $uf;
  }

  /**
   * Chargement des types d'ufs
   *
   * @param string $object_class classe concernée
   *
   * @return array()
   */
  static function getUFs($object_class = null) {
    $uf = new self;
    $group_id = CGroups::loadCurrent()->_id;

    $tab_ufs = array(
      "hebergement" => $uf->loadList(array("type" => "= 'hebergement'", "group_id" => "= '$group_id'"), "libelle"),
      "medicale"    => $uf->loadList(array("type" => "= 'medicale'", "group_id" => "= '$group_id'"), "libelle"),
      "soins"       => $uf->loadList(array("type" => "= 'soins'", "group_id" => "= '$group_id'"), "libelle"),
    );

    if ($object_class == "CChambre" || $object_class == "CLit") {
      unset($tab_ufs["medicale"]);
      unset($tab_ufs["soins"]);
    }
    elseif ($object_class == "CService") {
      unset($tab_ufs["medicale"]);
    }
    elseif ($object_class == "CMediusers" || $object_class == "CFunctions") {
      unset($tab_ufs["hebergement"]);
      unset($tab_ufs["soins"]);
    }

    return $tab_ufs;
  }
}

