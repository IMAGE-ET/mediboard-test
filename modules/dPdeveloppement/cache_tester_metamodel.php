<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
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

