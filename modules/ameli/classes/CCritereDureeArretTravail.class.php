<?php

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ameli
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */
 
/**
 * Description
 */
class CCritereDureeArretTravail {
  public $critere_id;
  public $text;
  public $duree;
  public $unite_duree;
  public $parent_id;
  public $parent_class;

  public $_depth;
  public $_nb_duration;
  public $_rowspan;

  /** @var CCritereDureeArretTravail[] */
  public $_ref_children;

  /**
   * The constructor
   *
   * @param array $fields The fields
   */
  public function __construct($fields) {
    $this->critere_id = $fields['critere_id'];
    $this->text = $fields['text'];
    $this->duree = $fields['duree'];
    $this->unite_duree = $fields['unite_duree'];
    $this->parent_id = $fields['parent_id'];
    $this->parent_class = $fields['parent_class'];
  }

  /**
   * Load the children of the criteria
   *
   * @return void
   */
  public function loadChildren() {
    $children = CCritereDureeArretTravail::loadCriteresByParent($this->critere_id, 'Critere');
    foreach ($children as $_child) {
      $_child->loadChildren();
    }

    $this->_ref_children = $children;
  }

  /**
   * Compute the depth of the criteria
   *
   * @return void
   */
  public function getDepth() {
    if (!$this->unite_duree) {
      $this->_depth = 1;
      $this->_nb_duration = 0;
      $max_depth = 0;
      $max_nb_duration = 0;
      $max_rowspan = 0;
      $this->_rowspan = count($this->_ref_children);
      foreach ($this->_ref_children as $_child) {
        $_child->getDepth();
        if ($_child->_depth > $max_depth) {
          $max_depth = $_child->_depth;
        }
        if ($_child->unite_duree) {
          $this->_nb_duration++;
        }
        else {
          $max_nb_duration += $_child->_nb_duration;
        }
        if ($_child->_rowspan) {
          $max_rowspan += $_child->_rowspan;
        }
      }
      $this->_depth += $max_depth;
      if ($max_nb_duration > $this->_nb_duration) {
        $this->_nb_duration = $max_nb_duration;
      }
      if ($max_rowspan > $this->_rowspan) {
        $this->_rowspan = $max_rowspan;
      }
    }
    else {
      $this->_depth = 0;
      $this->_nb_duration = 1;
      $this->_rowspan = 1;
    }
  }

  /**
   * Load the Criterias by using direct ancestors
   *
   * @param integer $parent_id    The parent id
   * @param string  $parent_class The parent class
   *
   * @return array
   */
  static function loadCriteresByParent($parent_id, $parent_class) {
    $ds = CSQLDataSource::get('ameli', true);
    $criteres = array();

    if (!$ds) {
      return $criteres;
    }

    $query = "SELECT * FROM `critere_duree_aati` WHERE `parent_id` = $parent_id AND `parent_class` = '$parent_class';";
    $result = $ds->exec($query);
    if (!$result) {
      return array();
    }
    while ($row = $ds->fetchAssoc($result)) {
      $criteres[] = new CCritereDureeArretTravail($row);
    }

    return $criteres;
  }
}
