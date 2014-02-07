<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CMbObjectHandler
 *
 * @abstract Event handler class for CMbObject
 */
abstract class CMbObjectHandler {
  /**
   * Trigger before event store
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onBeforeStore(CMbObject $mbObject) {
  }

  /**
   * Trigger after event store
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onAfterStore(CMbObject $mbObject) {
  }

  /**
   * Trigger before event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onBeforeMerge(CMbObject $mbObject) {
  }

  /**
   * Trigger when merge failed
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onMergeFailure(CMbObject $mbObject) {
  }

  /**
   * Trigger after event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onAfterMerge(CMbObject $mbObject) {
  }

  /**
   * Trigger before event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onBeforeDelete(CMbObject $mbObject) {
  }

  /**
   * Trigger after event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onAfterDelete(CMbObject $mbObject) {
  }

  /**
   * Trigger before fill limited template call
   *
   * @param CMbObject        $mbObject Object
   * @param CTemplateManager $template Template
   *
   * @return bool
   */
  function onBeforeFillLimitedTemplate(CMbObject $mbObject, CTemplateManager $template) {
  }

  /**
   * Trigger after fill limited template call
   *
   * @param CMbObject        $mbObject Object
   * @param CTemplateManager $template Template
   *
   * @return bool
   */
  function onAfterFillLimitedTemplate(CMbObject $mbObject, CTemplateManager $template) {
  }
}
