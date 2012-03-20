<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Romain Ollivier
*/

$file = tempnam(sys_get_temp_dir(), "mb_");
$csv = new CCSVFile($file);

CApp::getMbClasses(null, $instances);

foreach($instances as $_class => $_instance) {
  if (!$_instance->_spec->table || !$_instance->_ref_module) {
    continue;
  }
  
  $csv->writeLine(array(
    $_class,
    $_instance->_spec->table,
    $_instance->_spec->key,
  ));
}

$csv->stream("Class to table");

unlink($file);
