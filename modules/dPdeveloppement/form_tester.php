<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Fabien Ménager
*/

global $can;
$can->needsRead();

class CTestClass extends CMbObject {
  function __construct() {
    foreach (CMbFieldSpecFact::$classes as $spec => $class) {
      $this->$spec = null;
    }

    parent::__construct();
    
    foreach ($this->_specs as $key => &$spec) {
      $spec->sample($this);
    }
  }
  
  function getSpec(){
    $spec = parent::getSpec();
    $spec->key = 'test_class_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    foreach (CMbFieldSpecFact::$classes as $spec => $class) {
      $specs[$spec] = $spec;
    }
    $specs['enum'] = 'enum list|1|2|3|4';
    $specs['ref'] += 'enum class|CMbObject';
    return $specs;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('object', new CTestClass());
$smarty->assign('specs', CMbFieldSpecFact::$classes);
$smarty->display('form_tester.tpl');

?>