<?php

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author
 */

abstract class CHtmlToPDFConverter {
  static $instance = null;
  
  static function init($class) {
    self::$instance = new $class;
    
    //  Vérifier l'existance de la sous-classe
    if (!is_subclass_of(self::$instance, "CHtmlToPDFConverter")) {
      throw new CMbException("$class not a subclass of " . get_class($this));
    }
  }
  
  static function convert($html, $format, $orientation) {
    $instance = self::$instance;
    if (!$instance) {
      return;
    }
    
    $instance->html = $html;
    $instance->prepare($format, $orientation);
    $instance->render();

    if (!$instance->result) {
      throw new CMbException("Error while generating the PDF");
    }
    return $instance->result;
  }
  
  var $html = null;
  var $result = null;
  
  function prepare($format, $orientation) {}
  
  function render() {}
}

?>