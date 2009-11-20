<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDailyCheckItemCategory extends CMbObject {
  var $daily_check_item_category_id  = null;

  // DB Fields
  var $title    = null;
  var $desc     = null;
  var $target_class = null;
  var $type     = null;
	
	// Refs
  var $_ref_item_types = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_item_category';
    $spec->key   = 'daily_check_item_category_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['title'] = 'str notNull';
    $specs['target_class'] = 'enum list|CSalle|CBlocOperatoire|COperation notNull default|CSalle';
    $specs['type']  = 'enum list|preanesth|preop|postop';
    $specs['desc']  = 'text';
    return $specs;
  }
	
	function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['item_types'] = 'CDailyCheckItemType category_id';
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = ($this->target_class == 'CBlocOperatoire' ? 'Salle de réveil' : CAppUI::tr($this->target_class))." - $this->title";
  }
}
?>