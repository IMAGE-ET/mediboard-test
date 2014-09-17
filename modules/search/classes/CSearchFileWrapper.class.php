<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

/**
 * Class CSearchFileWrapper
 * Manage in order to index CFile
 */
class CSearchFileWrapper {
  public $_fichier;
  public $_file_id;

  /**
   * Constructor
   *
   * @param string  $fichier  Le fichier dont on doit extraire le contenu
   * @param integer $file_id  L'id du fichier dont on doit extraire le contenu
   *
   */
  function __construct ($fichier, $file_id) {
    $this->_fichier = $fichier;
    $this->_file_id = $file_id;
  }

  /**
   * Méthode permettant de démarrer le jar d'apache tika afin d'en extraire le contenu souhaité
   *
   * @param string $option l'option spécifiée pour l'extraction.
   *
   * @return string
   */
  function run ($option) {
    $path = realpath(__DIR__.'/../../../lib/tika-app-1.6.jar');
    $shellCommand = "java -jar $path $option $this->_fichier ";

    $processorInstance = proc_open($shellCommand, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
    $processorResult = stream_get_contents($pipes[1]);
    $processorErrors = stream_get_contents($pipes[2]);
    $error = explode("\n", $processorErrors);
    trigger_error("le CFile n°$this->_file_id suivant n'a pas été correctement indexé raison : $error[0]", E_USER_WARNING);

    proc_close($processorInstance);

    return trim($processorResult);

  }

  /**
   * Méthode permettant la récupération du contenu principal du document. (body)
   *
   * @return string
   */
  function getPlainText () {
    $option = "--text-main --encoding=utf-8";
    return $this->run($option);
  }

  /**
   * Méthode permettant la récupération du contenu global du document. (en-tête, corp, pied-de-page)
   *
   * @return string
   */
  function getText () {
    $option = "--text --encoding=utf-8";
    return $this->run($option);
  }

  /** Méthode permettant de récupérer le contenu du document sous format xml valide.
   *
   * @return string
   */
  function getXHTML () {
    $option = "--xml --encoding=utf-8";
    return $this->run($option);
  }

  /**
   * Méthode permettant de récupérer le contenu du document sous format HTML.
   *
   * @return string
   */
  function getHTML () {
    $option = "--html --encoding=utf-8";
    return $this->run($option);
  }

  /**
   * Méthode permettant de récupérer les métadonnées du document.
   *
   * @return string
   */
  function getMetadata () {
    $option = "--metadata --encoding=utf-8";
    return $this->run($option);
  }

  /**
   * Méthode permettant de récupérer les métadonnées du document au format JSON.
   *
   * @return string
   */
  function getMetadataJson () {
    $option = "--json --encoding=utf-8";
    return $this->run($option);
  }

  /**
   * Méthode permettant de récuperer les métadonnées du document au format Xmp
   *
   * @return string
   */
  function getMetadataXmp () {
    $option = "--xmp --encoding=utf-8";
    return $this->run($option);
  }

  /**
   * Méthode permettant de récuperer le langage du document.
   *
   * @return string
   */
  function getLanguage () {
    $option = "--language --encoding=utf-8";
    return $this->run($option);
  }

}