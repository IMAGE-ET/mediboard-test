<?php
/**
 * Overwrite a locale for an instance
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CTranslationOverwrite extends CMbObject {
  public $translation_id;

  public $source;
  public $translation;
  public $language;
  public $_old_translation;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "translation";
    $spec->key = "translation_id";
    $spec->uniques['trad'] = array('source');
    return $spec;
  }

  /**
   * Get backward reference specifications
   *
   * @return array
   */
  function getBackProps() {
    return $backProps = parent::getBackProps();
  }

  /**
   * load the activated translation from mediboard (used to compare with the sql one)
   *
   * @return string
   */
  function loadOldTranslation() {
    return $this->_old_translation = CAppUI::tr($this->source);
  }

  /**
   * Update DB fields before storing
   */
  function updatePlainFields() {
    parent::updatePlainFields();

    // HTML clenaning library
    CAppUI::requireLibraryFile("htmlpurifier/library/HTMLPurifier.auto");
    $config   = HTMLPurifier_Config::createDefault();
    // App encoding (in order to prevent from removing diacritics)
    $config->set('Core.Encoding', CApp::$encoding);
    $purifier = new HTMLPurifier($config);

    $this->translation = $purifier->purify($this->translation);
  }

  /**
   * the source must be found in locales
   *
   * @return string
   */
  function check() {
    global $locales;
    if (!isset($locales[$this->source])) {
      return "CTranslationOverwrite-failed-locale-doesnot-exist";
    }
    return parent::check();
  }

  /**
   * Get the properties of our class as string
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["source"]      = "str notNull";
    $props["language"]    = "enum notNull list|".implode('|', CAppUI::getAvailableLanguages())." default|fr";
    $props["translation"] = "text notNull";
    return $props;
  }
}