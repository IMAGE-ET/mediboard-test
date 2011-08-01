{{*
 * View Interop actor Exchange Sources EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{if count($actor->_ref_exchanges_sources) > 0}}  
  {{mb_include module=eai template="`$actor->_parent_class`_exchanges_sources_inc"}}
{{/if}}