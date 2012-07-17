<?php
/**
 * $Id: CHtmlToPDFConverter.class.php $
 * 
 * @package    Mediboard
 * @subpackage dPcompteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

/**
 * Factory pour la conversion html vers pdf
 */
abstract class CHtmlToPDFConverter {
  static $instance = null;
  
  /**
   * Fonction d'initialisation
   * 
   * @param object $class frontend à utiliser
   * 
   * @return void
   */
  static function init($class) {
    self::$instance = new $class;
    
    //  Vérifier l'existance de la sous-classe
    if (!is_subclass_of(self::$instance, "CHtmlToPDFConverter")) {
      throw new CMbException("$class not a subclass of " . get_class($this));
    }
  }
  
  /**
   * Conversion d'une source html en pdf
   * 
   * @param string $html        source html
   * @param string $format      format de la page
   * @param string $orientation orientation de la page
   * 
   * @return string
   */
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
  
  /**
   * Préparation de la conversion
   * 
   * @param string $format      format de la page
   * @param string $orientation orientation de la page
   * 
   * @return void
   */
  function prepare($format, $orientation) {
  }
  
  /**
   * Création du pdf à partir de la source html
   * 
   * @return void 
   */
  function render() {
  }
}

?>