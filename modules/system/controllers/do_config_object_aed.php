<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$class_name = CValue::post("_class_name");

$object = new $class_name;

$fields = $object->getDBFields();
unset($fields[$object->_spec->key]);
unset($fields["object_id"]);
foreach($fields as $_name => $_value) {
  if (!array_key_exists($_name, $_POST)) {
    $_POST[$_name] = "";
  }
}

$do = new CDoObjectAddEdit($class_name);
$do->doIt();

?>