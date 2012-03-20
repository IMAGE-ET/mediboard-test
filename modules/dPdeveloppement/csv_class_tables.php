<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Romain Ollivier
*/

$csv = new CCSVFile();

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
