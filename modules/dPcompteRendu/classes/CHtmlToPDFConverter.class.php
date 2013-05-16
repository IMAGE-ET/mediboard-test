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
  /** @var CHtmlToPDFConverter */
  static $instance;

  public $html;
  public $result;

  /**
   * Fonction d'initialisation
   *
   * @param string $class frontend à utiliser
   *
   * @throws CMbException
   * @return void
   */
  static function init($class) {
    self::$instance = new $class;
    
    //  Vérifier l'existance de la sous-classe
    if (!is_subclass_of(self::$instance, "CHtmlToPDFConverter")) {
      throw new CMbException("$class not a subclass of CHtmlToPDFConverter");
    }
  }

  /**
   * Conversion d'une source html en pdf
   *
   * @param string $html        source html
   * @param string $format      format de la page
   * @param string $orientation orientation de la page
   *
   * @throws CMbException
   * @return string|null
   */
  static function convert($html, $format, $orientation) {
    $instance = self::$instance;
    if (!$instance) {
      return null;
    }
    
    $instance->html = $html;
    $instance->prepare($format, $orientation);
    $instance->render();

    if (!$instance->result) {
      throw new CMbException("Error while generating the PDF");
    }

    return $instance->result;
  }
  
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
