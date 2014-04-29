<?php /* $Id: cache_tester.php 22953 2014-04-28 05:19:23Z mytto $ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision: 22953 $
 * @author Thomas Despoix
 */

CCanDo::checkRead();


$chrono = new Chronometer();
$chrono->start();

$classes = CApp::getChildClasses("CModelObject");
//$classes = array_keys(CModelObject::$spec);
foreach ($classes as $_class) {
  /** @var CModelObject $object */
  $object = new $_class;
  $object->makeAllBackSpecs();
  $chrono->step("make");
}

foreach ($classes as $_class) {
  $ballot = array(
    "spec"      => CModelObject::$spec[$_class],
    "props"     => CModelObject::$props[$_class],
    "specs"     => CModelObject::$specs[$_class],
    "backProps" => CModelObject::$backProps[$_class],
    "backSpecs" => CModelObject::$backSpecs[$_class],
  );
  SHM::put("ballot-$_class", $ballot, true);
  $chrono->step("put");
}

foreach ($classes as $_class) {
  SHM::get("ballot-$_class");
  $chrono->step("get");
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("chrono", $chrono);
$smarty->display("cache_tester_metamodel.tpl");

