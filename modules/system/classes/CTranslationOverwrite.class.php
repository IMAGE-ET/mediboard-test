<?php

/**
 * Overwrite a locale for an instance
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CTranslationOverwrite extends CMbObject {

  /**
   * Table Key
   *
   * @var integer
   */
  public $translation_id;

  var $source;
  var $translation;
  var $language;
  var $_old_translation;


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
    $props["source"]        = "str notNull";
    $props["language"]      = "enum notNull list|".implode('|', CAppUI::getAvailableLanguages())." default|fr";
    $props["translation"]   = "text notNull";
    return $props;
  }

}