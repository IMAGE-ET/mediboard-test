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

class CExamIgs extends CMbObject {
  public $examigs_id;

  // DB References
  public $sejour_id;

  // DB fields
  public $date;
  public $age;
  public $FC;
  public $TA;
  public $temperature;
  public $PAO2_FIO2;
  public $diurese;
  public $uree;
  public $globules_blancs;
  public $kaliemie;
  public $natremie;
  public $HCO3;
  public $billirubine;
  public $glasgow;
  public $maladies_chroniques;
  public $admission;
  public $scoreIGS;
  
  /** @var CConsultation */
  public $_ref_consult;
  
  static $fields = array("age", "FC", "TA", "temperature", "PAO2_FIO2", "diurese", "uree", "globules_blancs", 
                            "kaliemie", "natremie", "HCO3" , "billirubine", "glasgow", "maladies_chroniques", "admission");

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'examigs';
    $spec->key   = 'examigs_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["date"]                = "dateTime notNull";
    $props["sejour_id"]           = "ref notNull class|CSejour";
    $props["age"]                 = "enum list|0|7|12|15|16|18";
    $props["FC"]                  = "enum list|11|2|0|4|7";
    $props["TA"]                  = "enum list|13|5|0|2";
    $props["temperature"]         = "enum list|0|3";
    $props["PAO2_FIO2"]           = "enum list|11|9|6";
    $props["diurese"]             = "enum list|11|4|0";
    $props["uree"]                = "enum list|0|6|10";
    $props["globules_blancs"]     = "enum list|12|0|3";
    $props["kaliemie"]            = "enum list|3a|0|3b";
    $props["natremie"]            = "enum list|5|0|1";
    $props["HCO3"]                = "enum list|6|3|0";
    $props["billirubine"]         = "enum list|0|4|9";
    $props["glasgow"]             = "enum list|26|13|7|5|0";
    $props["maladies_chroniques"] = "enum list|9|10|17";
    $props["admission"]           = "enum list|0|6|8";
    $props["scoreIGS"]            = "num";
    return $props;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = "Score IGS: $this->scoreIGS";  
  }
}
