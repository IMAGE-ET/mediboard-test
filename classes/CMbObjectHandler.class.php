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
 * @abstract Event handler class for CMbObject
 */
abstract class CMbObjectHandler {
  function onBeforeStore(CMbObject $mbObject) {}
  function onAfterStore(CMbObject $mbObject) {}
  
  function onBeforeMerge(CMbObject $mbObject) {}
  function onAfterMerge(CMbObject $mbObject) {}
  
  function onBeforeDelete(CMbObject $mbObject) {}
  function onAfterDelete(CMbObject $mbObject) {}
  
  function onBeforeFillLimitedTemplate(CMbObject $mbObject, CTemplateManager $template) {}
  function onAfterFillLimitedTemplate(CMbObject $mbObject, CTemplateManager $template) {}
}
